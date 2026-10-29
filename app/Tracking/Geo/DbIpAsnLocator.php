<?php

namespace App\Tracking\Geo;

use App\Tracking\Contracts\GeoLocator;
use App\Tracking\Data\GeoResult;
use Throwable;

/** Network number and owner from DB-IP's ASN Lite database. */
final class DbIpAsnLocator implements GeoLocator
{
    private ?MmdbReader $reader = null;
    private bool $unavailable = false;

    public function __construct(private string $path, private NetworkClassifier $classifier)
    {
    }

    public function locate(string $ip): GeoResult
    {
        $record = $this->lookup($ip);
        if ($record === null) {
            return GeoResult::unknown();
        }

        $asn = $record['autonomous_system_number'] ?? null;
        $org = $record['autonomous_system_organization'] ?? null;
        $org = is_string($org) && $org !== '' ? mb_substr($org, 0, 190) : null;

        return new GeoResult(
            asn: is_int($asn) ? $asn : null,
            networkOrg: $org,
            networkType: $this->classifier->classify($org),
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
            $this->unavailable = true;
            report($e);

            return null;
        }
    }
}
