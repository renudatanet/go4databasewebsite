<?php

namespace Tests\Unit\Tracking\Support;

/**
 * Writes a minimal MaxMind DB file for tests: an IPv6 tree holding a few
 * IPv4 networks. Supports the three record sizes, maps, arrays, strings,
 * doubles, uints, booleans and pointers, which is what the reader decodes.
 */
final class TinyMmdbWriter
{
    /** @var list<array{0: mixed, 1: mixed}> */
    private array $nodes = [[null, null]];
    private string $data = '';

    public function __construct(private int $recordSize = 24)
    {
    }

    /** Adds an IPv4 network ("203.0.113.0/24") with a record, or a pointer to an earlier record. */
    public function add(string $cidr, array $record, ?int $pointTo = null): int
    {
        [$ip, $prefix] = explode('/', $cidr);
        $bits = array_merge(array_fill(0, 96, 0), $this->bits(inet_pton($ip), (int) $prefix));

        $offset = strlen($this->data);
        $this->data .= $pointTo === null ? $this->encode($record) : $this->pointer($pointTo);

        $node = 0;
        foreach ($bits as $i => $bit) {
            if ($i === count($bits) - 1) {
                $this->nodes[$node][$bit] = ['data' => $offset];
                break;
            }
            if (!is_int($this->nodes[$node][$bit])) {
                $this->nodes[] = [null, null];
                $this->nodes[$node][$bit] = count($this->nodes) - 1;
            }
            $node = $this->nodes[$node][$bit];
        }

        return $offset;
    }

    public function write(string $path): void
    {
        $count = count($this->nodes);
        $tree = '';
        foreach ($this->nodes as [$left, $right]) {
            $tree .= $this->recordPair($this->value($left, $count), $this->value($right, $count));
        }

        $metadata = "\xAB\xCD\xEFMaxMind.com" . $this->encode([
            'node_count' => ['uint32', $count],
            'record_size' => ['uint16', $this->recordSize],
            'ip_version' => ['uint16', 6],
            'database_type' => 'Test-City',
            'languages' => ['array', ['en']],
            'binary_format_major_version' => ['uint16', 2],
            'binary_format_minor_version' => ['uint16', 0],
        ]);

        file_put_contents($path, $tree . str_repeat("\0", 16) . $this->data . $metadata);
    }

    private function value(mixed $child, int $count): int
    {
        if (is_int($child)) {
            return $child;
        }
        if (is_array($child)) {
            return $count + 16 + $child['data'];
        }

        return $count; // empty
    }

    private function recordPair(int $left, int $right): string
    {
        switch ($this->recordSize) {
            case 24:
                return substr(pack('N', $left), 1) . substr(pack('N', $right), 1);
            case 28:
                return substr(pack('N', $left), 1)
                    . chr((($left >> 24) & 0x0F) << 4 | (($right >> 24) & 0x0F))
                    . substr(pack('N', $right), 1);
            default:
                return pack('N', $left) . pack('N', $right);
        }
    }

    /** @return list<int> */
    private function bits(string $packed, int $count): array
    {
        $bits = [];
        for ($i = 0; $i < $count; $i++) {
            $bits[] = (ord($packed[$i >> 3]) >> (7 - ($i & 7))) & 1;
        }

        return $bits;
    }

    private function pointer(int $offset): string
    {
        return chr((1 << 5) | (($offset >> 8) & 0x7)) . chr($offset & 0xFF);
    }

    /** Control byte plus extended size bytes, for sizes of 29 and up. */
    private function header(int $type, int $size): string
    {
        if ($size < 29) {
            return chr(($type << 5) | $size);
        }
        if ($size < 285) {
            return chr(($type << 5) | 29) . chr($size - 29);
        }

        return chr(($type << 5) | 30) . pack("n", $size - 285);
    }

    /** Values: string, float, bool, assoc array (map), or [type, value] for uint16/uint32/array. */
    private function encode(mixed $value): string
    {
        if (is_array($value) && isset($value[0]) && is_string($value[0]) && in_array($value[0], ['uint16', 'uint32', 'array'], true) && count($value) === 2) {
            [$type, $inner] = $value;
            if ($type === 'array') {
                return chr(count($inner)) . chr(11 - 7) . implode('', array_map(fn ($v) => $this->encode($v), $inner));
            }
            $bytes = ltrim(pack('N', $inner), "\0");

            return chr((($type === 'uint16' ? 5 : 6) << 5) | strlen($bytes)) . $bytes;
        }
        if (is_string($value)) {
            return $this->header(2, strlen($value)) . $value;
        }
        if (is_float($value)) {
            return chr((3 << 5) | 8) . pack('E', $value);
        }
        if (is_bool($value)) {
            return chr($value ? 1 : 0) . chr(14 - 7);
        }
        if (is_int($value)) {
            return $this->encode(['uint32', $value]);
        }

        $out = $this->header(7, count($value));
        foreach ($value as $key => $item) {
            $out .= $this->encode((string) $key) . $this->encode($item);
        }

        return $out;
    }
}
