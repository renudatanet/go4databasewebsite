<?php

namespace App\Tracking\Support;

use App\Tracking\Data\TrafficSource;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use DateTimeZone;

/** How numbers, times and places are written on the Tracking screens. */
final class Format
{
    public static function number(int|float|null $value): string
    {
        return number_format((float) $value);
    }

    /** 0.4231 => "42.3%" */
    public static function percent(?float $rate, int $decimals = 1): string
    {
        $percent = ($rate ?? 0) * 100;
        if ($percent == 0) {
            return '0%';
        }

        return number_format($percent, $percent >= 10 ? 0 : $decimals) . '%';
    }

    /** 108000 ms => "1m 48s" */
    public static function duration(int|float|null $ms): string
    {
        $seconds = (int) round(((float) $ms) / 1000);
        if ($seconds < 60) {
            return $seconds . 's';
        }
        if ($seconds < 3600) {
            return intdiv($seconds, 60) . 'm ' . ($seconds % 60) . 's';
        }

        return intdiv($seconds, 3600) . 'h ' . intdiv($seconds % 3600, 60) . 'm';
    }

    /** A change against the previous period, e.g. "+12%", or null when there is none. */
    public static function change(?float $change): ?string
    {
        if ($change === null) {
            return null;
        }

        $percent = round($change * 100);

        return ($percent > 0 ? '+' : '') . number_format($percent) . '%';
    }

    /** "US" => the flag emoji. Unknown or malformed codes give a neutral globe. */
    public static function flag(?string $countryCode): string
    {
        if ($countryCode === null || !preg_match('/^[A-Z]{2}$/', $countryCode)) {
            return "\u{1F310}";
        }

        return mb_chr(0x1F1E6 + ord($countryCode[0]) - 65) . mb_chr(0x1F1E6 + ord($countryCode[1]) - 65);
    }

    public static function channel(?string $channel): string
    {
        return TrafficSource::LABELS[$channel] ?? ucfirst((string) ($channel ?: 'unknown'));
    }

    /** A stored UTC time in the report timezone, e.g. "29 Oct, 14:32". */
    public static function time(DateTimeInterface|string|null $value, DateTimeZone $tz, string $format = 'j M, H:i'): string
    {
        if ($value === null || $value === '') {
            return '–';
        }

        $time = $value instanceof DateTimeInterface
            ? CarbonImmutable::instance($value)
            : CarbonImmutable::parse($value, 'UTC');

        return $time->setTimezone($tz)->format($format);
    }

    /** "3 minutes ago", for recent activity. */
    public static function ago(DateTimeInterface|string|null $value): string
    {
        if ($value === null || $value === '') {
            return '–';
        }

        $time = $value instanceof DateTimeInterface ? CarbonImmutable::instance($value) : CarbonImmutable::parse($value, 'UTC');

        return $time->diffForHumans();
    }

    /** A path shortened for a narrow column, keeping its end, which is the specific part. */
    public static function path(?string $path, int $max = 60): string
    {
        $path = (string) $path;

        return mb_strlen($path) > $max ? '…' . mb_substr($path, -($max - 1)) : $path;
    }

    /** A link destination for display: no scheme, no www, shortened. */
    public static function href(?string $href, int $max = 70): string
    {
        $display = preg_replace('#^https?://(www\.)?#i', '', (string) $href);

        return mb_strlen($display) > $max ? mb_substr($display, 0, $max - 1) . '…' : $display;
    }
}
