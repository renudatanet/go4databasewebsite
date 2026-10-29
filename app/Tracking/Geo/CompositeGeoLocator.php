<?php

namespace App\Tracking\Geo;

use App\Tracking\Contracts\GeoLocator;
use App\Tracking\Data\GeoResult;

/** Asks several locators and combines their answers; earlier ones win on overlap. */
final class CompositeGeoLocator implements GeoLocator
{
    /** @var list<GeoLocator> */
    private array $locators;

    public function __construct(GeoLocator ...$locators)
    {
        $this->locators = $locators;
    }

    public function locate(string $ip): GeoResult
    {
        $result = GeoResult::unknown();
        foreach ($this->locators as $locator) {
            $result = $result->mergedWith($locator->locate($ip));
        }

        return $result;
    }
}
