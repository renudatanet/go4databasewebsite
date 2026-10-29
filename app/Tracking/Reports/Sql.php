<?php

namespace App\Tracking\Reports;

use Illuminate\Support\Facades\DB;

/**
 * The few expressions that differ between MySQL/MariaDB (live) and SQLite
 * (tests). Everything else in the reports is plain, portable SQL.
 */
final class Sql
{
    /** The local calendar day of a UTC timestamp column, as Y-m-d. */
    public static function localDay(string $column, int $offsetMinutes): string
    {
        return self::isSqlite()
            ? "date({$column}, '{$offsetMinutes} minutes')"
            : "DATE(DATE_ADD({$column}, INTERVAL {$offsetMinutes} MINUTE))";
    }

    /** The local hour (0-23) of a UTC timestamp column. */
    public static function localHour(string $column, int $offsetMinutes): string
    {
        return self::isSqlite()
            ? "CAST(strftime('%H', {$column}, '{$offsetMinutes} minutes') AS INTEGER)"
            : "HOUR(DATE_ADD({$column}, INTERVAL {$offsetMinutes} MINUTE))";
    }

    private static function isSqlite(): bool
    {
        return DB::connection()->getDriverName() === 'sqlite';
    }
}
