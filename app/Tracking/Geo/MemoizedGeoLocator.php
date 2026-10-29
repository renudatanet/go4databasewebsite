<?php

namespace App\Tracking\Geo;

use App\Tracking\Contracts\GeoLocator;
use App\Tracking\Data\GeoResult;

/** Remembers answers for the life of the process (one request, or one CLI run). */
final class MemoizedGeoLocator implements GeoLocator
{
    private const MAX_ENTRIES = 1000;

    /** @var array<string, GeoResult> */
    private array $memo = [];

    public function __construct(private GeoLocator $inner)
    {
    }

    public function locate(string $ip): GeoResult
    {
        if (!isset($this->memo[$ip])) {
            if (count($this->memo) >= self::MAX_ENTRIES) {
                $this->memo = [];
            }
            $this->memo[$ip] = $this->inner->locate($ip);
        }

        return $this->memo[$ip];
    }
}
