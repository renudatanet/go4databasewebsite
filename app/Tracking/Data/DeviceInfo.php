<?php

namespace App\Tracking\Data;

/** The visitor's device, browser and operating system. */
final class DeviceInfo
{
    public const DESKTOP = 'desktop';
    public const MOBILE = 'mobile';
    public const TABLET = 'tablet';

    public function __construct(
        public string $type = self::DESKTOP,
        public ?string $browser = null,
        public ?string $browserVersion = null,
        public ?string $os = null,
        public ?string $osVersion = null,
    ) {
    }
}
