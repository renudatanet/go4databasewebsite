<?php

namespace App\Tracking\Geo;

use App\Tracking\Contracts\GeoLocator;
use App\Tracking\Data\GeoResult;
use Throwable;

/** Country, region, city and coordinates from DB-IP's City Lite database. */
final class DbIpCityLocator implements GeoLocator
{
    private ?MmdbReader $reader = null;
    private bool $unavailable = false;

    public function __construct(private string $path)
    {
    }

    public function locate(string $ip): GeoResult
    {
        $record = $this->lookup($ip);
        if ($record === null) {
            return GeoResult::unknown();
        }

        $latitude = $record['location']['latitude'] ?? null;
        $longitude = $record['location']['longitude'] ?? null;

        return new GeoResult(
            countryCode: self::text($record['country']['iso_code'] ?? null, 2),
            country: self::text($record['country']['names']['en'] ?? null),
            region: self::text($record['subdivisions'][0]['names']['en'] ?? null),
            city: self::text($record['city']['names']['en'] ?? null),
            latitude: is_numeric($latitude) ? round((float) $latitude, 4) : null,
            longitude: is_numeric($longitude) ? round((float) $longitude, 4) : null,
        );
    }

    /** @return array<mixed>|null */
    private function lookup(string $ip): ?array
    {
        if ($this->unavailable) {
            return null;
        }

        try {
            $this->reader ??= new MmdbReader($this->path);

            return $this->reader->get($ip);
        } catch (Throwable $e) {
            // A missing or damaged file must never break collection: report
            // it once and carry on without locations.
            $this->unavailable = true;
            report($e);

            return null;
        }
    }

    private static function text(mixed $value, int $max = 120): ?string
    {
        return is_string($value) && $value !== '' ? mb_substr($value, 0, $max) : null;
    }
}
