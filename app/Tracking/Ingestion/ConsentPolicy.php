<?php

namespace App\Tracking\Ingestion;

use App\Tracking\Support\TrackingSettings;

/**
 * Decides, per visit, whether a visitor may get the tracking cookie and have
 * their full IP kept, or is counted anonymously (no cookie, masked IP, a
 * visitor id that changes daily and can't be linked across days).
 */
final class ConsentPolicy
{
    public const FULL = 'full';
    public const ANONYMOUS = 'anonymous';

    /** @param list<string> $consentCountries */
    public function __construct(private TrackingSettings $settings, private array $consentCountries)
    {
    }

    public function decide(?string $countryCode, bool $consentGiven): string
    {
        if ($consentGiven) {
            return self::FULL;
        }

        switch ($this->settings->consentMode()) {
            case TrackingSettings::CONSENT_NONE:
                return self::FULL;
            case TrackingSettings::CONSENT_EVERYWHERE:
                return self::ANONYMOUS;
            default:
                // Without a location there is no way to know; the location files
                // are checked on the Settings screen so this stays rare.
                return $countryCode !== null && in_array($countryCode, $this->consentCountries, true)
                    ? self::ANONYMOUS
                    : self::FULL;
        }
    }
}
