<?php

namespace App\Tracking\Ingestion;

use Carbon\CarbonInterface;

/**
 * A visitor id for people counted without a cookie: derived from the IP and
 * browser with a key that changes every day, so one person is one visitor
 * within a day and can't be followed from one day to the next. Neither the
 * IP nor the key is stored.
 */
final class AnonymousId
{
    public function __construct(private string $appKey)
    {
    }

    public function for(string $ip, string $userAgent, CarbonInterface $day): string
    {
        $dailyKey = hash_hmac('sha256', 'tracking-anon:' . $day->format('Y-m-d'), $this->appKey);
        $hex = hash_hmac('sha256', $ip . '|' . $userAgent, $dailyKey);

        // Shaped as a version-4 UUID so it fits the same column as cookie ids.
        return sprintf(
            '%s-%s-4%s-%s%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 13, 3),
            dechex((hexdec($hex[16]) & 0x3) | 0x8),
            substr($hex, 17, 3),
            substr($hex, 20, 12)
        );
    }
}
