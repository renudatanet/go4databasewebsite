<?php

namespace App\Tracking\Support;

/** IP helpers: validation, masking and CIDR matching, for IPv4 and IPv6. */
final class IpAddress
{
    public static function isValid(?string $ip): bool
    {
        return $ip !== null && filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Drops the part of the address that identifies a household or device:
     * the last octet of IPv4 (a /24), everything after the /48 of IPv6.
     */
    public static function mask(string $ip): ?string
    {
        $packed = @inet_pton($ip);
        if ($packed === false) {
            return null;
        }

        if (strlen($packed) === 4) {
            return inet_ntop(substr($packed, 0, 3) . "\0");
        }

        return inet_ntop(substr($packed, 0, 6) . str_repeat("\0", 10));
    }

    /** True when $ip equals $rule, or falls inside it when $rule is a CIDR range. */
    public static function matches(string $ip, string $rule): bool
    {
        $address = @inet_pton($ip);
        if ($address === false) {
            return false;
        }

        if (!str_contains($rule, '/')) {
            $single = @inet_pton($rule);

            return $single !== false && $single === $address;
        }

        [$network, $bits] = explode('/', $rule, 2);
        $subnet = @inet_pton($network);
        if ($subnet === false || strlen($subnet) !== strlen($address) || !ctype_digit($bits)) {
            return false;
        }

        $bits = (int) $bits;
        if ($bits > strlen($address) * 8) {
            return false;
        }

        $fullBytes = intdiv($bits, 8);
        if (substr($address, 0, $fullBytes) !== substr($subnet, 0, $fullBytes)) {
            return false;
        }

        $remaining = $bits % 8;
        if ($remaining === 0) {
            return true;
        }

        $mask = (0xFF << (8 - $remaining)) & 0xFF;

        return (ord($address[$fullBytes]) & $mask) === (ord($subnet[$fullBytes]) & $mask);
    }

    /** @param list<string> $rules */
    public static function matchesAny(string $ip, array $rules): bool
    {
        foreach ($rules as $rule) {
            if (self::matches($ip, $rule)) {
                return true;
            }
        }

        return false;
    }
}
