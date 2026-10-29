<?php

namespace App\Tracking\Reports;

/** Arithmetic shared by the reports. */
final class Metrics
{
    /** $part as a share of $whole, 0..1; 0 when there is no whole. */
    public static function rate(int|float|null $part, int|float|null $whole): float
    {
        return $whole ? round((float) $part / (float) $whole, 4) : 0.0;
    }

    /** Relative change from $previous to $current, or null when there is nothing to compare with. */
    public static function change(int|float|null $current, int|float|null $previous): ?float
    {
        if (!$previous) {
            return null;
        }

        return round(((float) $current - (float) $previous) / (float) $previous, 4);
    }

    /** Escapes a user's search text for a LIKE pattern. */
    public static function like(string $search): string
    {
        return '%' . addcslashes($search, '%_\\') . '%';
    }
}
