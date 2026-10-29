<?php

namespace App\Tracking\Maintenance;

use App\Tracking\Geo\MmdbReader;
use App\Tracking\Support\TrackingSettings;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

/**
 * Downloads DB-IP's free City Lite and ASN Lite files (published monthly),
 * checks each opens and answers a known address, and only then swaps it in.
 * A failed download leaves the current file in place.
 */
final class GeoDatabaseUpdater
{
    private const PROBE_IP = '8.8.8.8';

    /**
     * @param array<string, string> $files kind (city|asn) => file name
     */
    public function __construct(
        private string $directory,
        private array $files,
        private string $urlPattern,
        private TrackingSettings $settings,
    ) {
    }

    /** @return array<string, string> kind => what happened, for the command or screen to show */
    public function update(?CarbonImmutable $now = null): array
    {
        $now ??= CarbonImmutable::now('UTC');
        if (!is_dir($this->directory) && !mkdir($this->directory, 0775, true) && !is_dir($this->directory)) {
            throw new RuntimeException("Cannot create {$this->directory}");
        }

        $results = [];
        foreach ($this->files as $kind => $file) {
            try {
                $month = $this->download($kind, $this->directory . '/' . $file, $now);
                $results[$kind] = "updated to the {$month} edition";
            } catch (Throwable $e) {
                report($e);
                $results[$kind] = 'failed: ' . $e->getMessage();
            }
        }

        if (!array_filter($results, fn ($r) => str_starts_with($r, 'failed'))) {
            $this->settings->markGeoUpdated($now->toDateTimeString());
        }

        return $results;
    }

    /** @return array<string, array{exists: bool, size: int, modified: ?string}> */
    public function status(): array
    {
        $status = [];
        foreach ($this->files as $kind => $file) {
            $path = $this->directory . '/' . $file;
            $exists = is_file($path);
            $status[$kind] = [
                'exists' => $exists,
                'size' => $exists ? (int) filesize($path) : 0,
                'modified' => $exists ? date('Y-m-d H:i', (int) filemtime($path)) : null,
            ];
        }

        return $status;
    }

    /** The newest edition available: this month's, or last month's early in a month. */
    private function download(string $kind, string $target, CarbonImmutable $now): string
    {
        $lastError = null;
        foreach ([$now, $now->subMonthNoOverflow()] as $month) {
            $edition = $month->format('Y-m');
            $gzip = $target . '.download.gz';
            $fresh = $target . '.download';

            try {
                $response = Http::timeout(300)->withOptions(['sink' => $gzip])->get(sprintf($this->urlPattern, $kind, $edition));
                if (!$response->successful()) {
                    throw new RuntimeException("HTTP {$response->status()} for the {$edition} edition");
                }

                $this->gunzip($gzip, $fresh);
                $this->check($fresh);

                if (!rename($fresh, $target)) {
                    throw new RuntimeException('Could not replace the current file');
                }

                return $edition;
            } catch (Throwable $e) {
                $lastError = $e;
            } finally {
                @unlink($gzip);
                @unlink($fresh);
            }
        }

        throw $lastError ?? new RuntimeException('No edition could be downloaded');
    }

    private function gunzip(string $source, string $target): void
    {
        $in = gzopen($source, 'rb');
        $out = fopen($target, 'wb');
        if ($in === false || $out === false) {
            throw new RuntimeException('Could not unpack the download');
        }

        try {
            while (!gzeof($in)) {
                $chunk = gzread($in, 1024 * 1024);
                if ($chunk === false) {
                    throw new RuntimeException('The download is not a valid gzip file');
                }
                fwrite($out, $chunk);
            }
        } finally {
            gzclose($in);
            fclose($out);
        }
    }

    private function check(string $path): void
    {
        $reader = new MmdbReader($path);
        if ($reader->get(self::PROBE_IP) === null) {
            throw new RuntimeException('The new file has no data for a known address');
        }
    }
}
