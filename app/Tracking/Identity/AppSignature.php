<?php

namespace App\Tracking\Identity;

/**
 * Signs and checks messages from the app: HMAC-SHA256 over
 * "<unix timestamp>.<raw body>" with the shared secret from Settings.
 * The timestamp bounds how old a message may be; a replayed signature is
 * refused by the caller.
 */
final class AppSignature
{
    public const TOLERANCE_SECONDS = 300;

    public static function sign(string $secret, int $timestamp, string $body): string
    {
        return hash_hmac('sha256', $timestamp . '.' . $body, $secret);
    }

    public static function verify(?string $secret, ?string $timestamp, ?string $signature, string $body, int $now): bool
    {
        if ($secret === null || $timestamp === null || $signature === null || !ctype_digit($timestamp)) {
            return false;
        }
        if (abs($now - (int) $timestamp) > self::TOLERANCE_SECONDS) {
            return false;
        }

        return hash_equals(self::sign($secret, (int) $timestamp, $body), strtolower($signature));
    }
}
