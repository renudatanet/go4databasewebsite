<?php

namespace App\Tracking\Geo;

use RuntimeException;

/**
 * Reader for MaxMind DB (.mmdb) files, the format DB-IP's free databases
 * ship in. Written here rather than added as a Composer package so the live
 * server needs no dependency install; it implements the published spec:
 * https://maxmind.github.io/MaxMind-DB/
 *
 * The file is read with seeks, never loaded whole, so opening the 130 MB
 * city database costs one small read of its metadata.
 */
final class MmdbReader
{
    private const METADATA_MARKER = "\xAB\xCD\xEFMaxMind.com";
    private const METADATA_SEARCH_BYTES = 128 * 1024;
    private const DATA_SEPARATOR_BYTES = 16;

    private const TYPE_POINTER = 1;
    private const TYPE_STRING = 2;
    private const TYPE_DOUBLE = 3;
    private const TYPE_BYTES = 4;
    private const TYPE_UINT16 = 5;
    private const TYPE_UINT32 = 6;
    private const TYPE_MAP = 7;
    private const TYPE_INT32 = 8;
    private const TYPE_UINT64 = 9;
    private const TYPE_UINT128 = 10;
    private const TYPE_ARRAY = 11;
    private const TYPE_BOOLEAN = 14;
    private const TYPE_FLOAT = 15;

    /** @var resource */
    private $handle;
    private int $nodeCount;
    private int $recordSize;
    private int $ipVersion;
    private int $nodeBytes;
    private int $searchTreeSize;
    private int $dataStart;
    private ?int $ipv4Start = null;
    /** @var array<string, mixed> */
    private array $metadata;

    public function __construct(string $path)
    {
        if (!is_readable($path)) {
            throw new RuntimeException("MMDB file is not readable: {$path}");
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new RuntimeException("MMDB file could not be opened: {$path}");
        }
        $this->handle = $handle;

        $this->metadata = $this->readMetadata();
        $this->nodeCount = (int) ($this->metadata['node_count'] ?? 0);
        $this->recordSize = (int) ($this->metadata['record_size'] ?? 0);
        $this->ipVersion = (int) ($this->metadata['ip_version'] ?? 0);

        if (!in_array($this->recordSize, [24, 28, 32], true) || $this->nodeCount <= 0) {
            throw new RuntimeException('Unsupported MMDB layout');
        }

        $this->nodeBytes = intdiv($this->recordSize * 2, 8);
        $this->searchTreeSize = $this->nodeCount * $this->nodeBytes;
        $this->dataStart = $this->searchTreeSize + self::DATA_SEPARATOR_BYTES;
    }

    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    /** @return array<string, mixed> */
    public function metadata(): array
    {
        return $this->metadata;
    }

    /**
     * The record for an IP address, or null when the database has none.
     *
     * @return array<mixed>|null
     */
    public function get(string $ip): ?array
    {
        $packed = @inet_pton($ip);
        if ($packed === false) {
            return null;
        }

        $bitCount = strlen($packed) * 8;
        if ($bitCount === 128 && $this->ipVersion === 4) {
            return null;
        }

        $node = ($bitCount === 32 && $this->ipVersion === 6) ? $this->ipv4StartNode() : 0;

        for ($i = 0; $i < $bitCount && $node < $this->nodeCount; $i++) {
            $bit = (ord($packed[$i >> 3]) >> (7 - ($i & 7))) & 1;
            $node = $this->readRecord($node, $bit);
        }

        if ($node <= $this->nodeCount) {
            return null; // == nodeCount: no data; < nodeCount: ran out of bits
        }

        $offset = $node - $this->nodeCount - self::DATA_SEPARATOR_BYTES;
        [$value] = $this->decode($this->dataStart, $offset);

        return is_array($value) ? $value : null;
    }

    /** IPv4 addresses live under ::/96 in an IPv6 tree; that node is found once. */
    private function ipv4StartNode(): int
    {
        if ($this->ipv4Start === null) {
            $node = 0;
            for ($i = 0; $i < 96 && $node < $this->nodeCount; $i++) {
                $node = $this->readRecord($node, 0);
            }
            $this->ipv4Start = $node;
        }

        return $this->ipv4Start;
    }

    private function readRecord(int $node, int $side): int
    {
        $bytes = $this->read($node * $this->nodeBytes, $this->nodeBytes);

        switch ($this->recordSize) {
            case 24:
                return $this->uint(substr($bytes, $side * 3, 3));
            case 28:
                if ($side === 0) {
                    return ((ord($bytes[3]) & 0xF0) << 20) | $this->uint(substr($bytes, 0, 3));
                }

                return ((ord($bytes[3]) & 0x0F) << 24) | $this->uint(substr($bytes, 4, 3));
            default:
                return $this->uint(substr($bytes, $side * 4, 4));
        }
    }

    /** @return array<string, mixed> */
    private function readMetadata(): array
    {
        $size = fstat($this->handle)['size'] ?? 0;
        $length = min($size, self::METADATA_SEARCH_BYTES);
        $tail = $this->read($size - $length, $length);

        $position = strrpos($tail, self::METADATA_MARKER);
        if ($position === false) {
            throw new RuntimeException('Not an MMDB file: metadata marker missing');
        }

        $metadataStart = $size - $length + $position + strlen(self::METADATA_MARKER);
        [$metadata] = $this->decode($metadataStart, 0);

        if (!is_array($metadata)) {
            throw new RuntimeException('MMDB metadata is not a map');
        }

        return $metadata;
    }

    /**
     * Decodes one value. Pointers are relative to $base, which is the data
     * section for records and the metadata start for metadata.
     *
     * @return array{0: mixed, 1: int} the value and the offset after it
     */
    private function decode(int $base, int $offset): array
    {
        $control = ord($this->read($base + $offset, 1));
        $offset++;
        $type = $control >> 5;

        if ($type === self::TYPE_POINTER) {
            [$pointer, $offset] = $this->decodePointer($base, $control, $offset);
            [$value] = $this->decode($base, $pointer);

            return [$value, $offset];
        }

        if ($type === 0) {
            $type = 7 + ord($this->read($base + $offset, 1));
            $offset++;
        }

        [$size, $offset] = $this->decodeSize($base, $control, $offset);

        switch ($type) {
            case self::TYPE_MAP:
                $map = [];
                for ($i = 0; $i < $size; $i++) {
                    [$key, $offset] = $this->decode($base, $offset);
                    [$map[(string) $key], $offset] = $this->decode($base, $offset);
                }

                return [$map, $offset];

            case self::TYPE_ARRAY:
                $list = [];
                for ($i = 0; $i < $size; $i++) {
                    [$list[], $offset] = $this->decode($base, $offset);
                }

                return [$list, $offset];

            case self::TYPE_BOOLEAN:
                return [$size !== 0, $offset];
        }

        $bytes = $size > 0 ? $this->read($base + $offset, $size) : '';
        $offset += $size;

        switch ($type) {
            case self::TYPE_STRING:
            case self::TYPE_BYTES:
                return [$bytes, $offset];
            case self::TYPE_DOUBLE:
                return [unpack('E', $bytes)[1], $offset];
            case self::TYPE_FLOAT:
                return [unpack('G', $bytes)[1], $offset];
            case self::TYPE_UINT16:
            case self::TYPE_UINT32:
                return [$this->uint($bytes), $offset];
            case self::TYPE_INT32:
                $value = $this->uint($bytes);
                if ($size === 4 && $value >= 0x80000000) {
                    $value -= 0x100000000;
                }

                return [$value, $offset];
            case self::TYPE_UINT64:
            case self::TYPE_UINT128:
                // Larger than PHP's int on the high end; the databases used here
                // never need these, so wide values come back as hex.
                return [$size <= 7 ? $this->uint($bytes) : bin2hex($bytes), $offset];
            default:
                throw new RuntimeException("Unknown MMDB data type {$type}");
        }
    }

    /** @return array{0: int, 1: int} */
    private function decodePointer(int $base, int $control, int $offset): array
    {
        $sizeBits = ($control >> 3) & 0x3;
        $length = $sizeBits + 1;
        $bytes = $this->read($base + $offset, $length);
        $offset += $length;

        switch ($sizeBits) {
            case 0:
                $pointer = (($control & 0x7) << 8) | ord($bytes);
                break;
            case 1:
                $pointer = ((($control & 0x7) << 16) | $this->uint($bytes)) + 2048;
                break;
            case 2:
                $pointer = ((($control & 0x7) << 24) | $this->uint($bytes)) + 526336;
                break;
            default:
                $pointer = $this->uint($bytes);
        }

        return [$pointer, $offset];
    }

    /** @return array{0: int, 1: int} */
    private function decodeSize(int $base, int $control, int $offset): array
    {
        $size = $control & 0x1F;
        if ($size < 29) {
            return [$size, $offset];
        }

        $extra = $size - 28;
        $bytes = $this->uint($this->read($base + $offset, $extra));
        $offset += $extra;

        if ($size === 29) {
            return [29 + $bytes, $offset];
        }
        if ($size === 30) {
            return [285 + $bytes, $offset];
        }

        return [65821 + $bytes, $offset];
    }

    private function uint(string $bytes): int
    {
        $value = 0;
        $length = strlen($bytes);
        for ($i = 0; $i < $length; $i++) {
            $value = ($value << 8) | ord($bytes[$i]);
        }

        return $value;
    }

    private function read(int $offset, int $length): string
    {
        if ($length === 0) {
            return '';
        }
        if (fseek($this->handle, $offset) !== 0) {
            throw new RuntimeException('MMDB seek failed');
        }
        $bytes = fread($this->handle, $length);
        if ($bytes === false || strlen($bytes) !== $length) {
            throw new RuntimeException('MMDB file is truncated');
        }

        return $bytes;
    }
}
