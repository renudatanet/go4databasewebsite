<?php

namespace App\Tracking\Contracts;

use App\Tracking\Data\DeviceInfo;

/** Reads the device, browser and OS from a user agent. */
interface DeviceParser
{
    /** @param bool $touch the browser reported a touch screen (tells iPads from Macs) */
    public function parse(string $userAgent, bool $touch = false): DeviceInfo;
}
