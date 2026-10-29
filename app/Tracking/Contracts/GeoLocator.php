<?php

namespace App\Tracking\Contracts;

use App\Tracking\Data\GeoResult;

/** Looks up where an IP address is. Never throws: an unknown IP gives an empty result. */
interface GeoLocator
{
    public function locate(string $ip): GeoResult;
}
