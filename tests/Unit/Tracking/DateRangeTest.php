<?php

namespace Tests\Unit\Tracking;

use App\Tracking\Reports\DateRange;
use Carbon\CarbonImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

class DateRangeTest extends TestCase
{
    protected function setUp(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-29 10:00', 'Asia/Kolkata'));
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
    }

    public function test_days_start_at_local_midnight_and_are_stored_as_utc(): void
    {
        $today = DateRange::fromPreset('today', new DateTimeZone('Asia/Kolkata'));

        $this->assertSame('2026-10-28 18:30:00', $today->start->toDateTimeString());
        $this->assertSame('2026-10-29 18:30:00', $today->end->toDateTimeString());
        $this->assertTrue($today->isHourly());
        $this->assertSame(330, $today->offsetMinutes());
    }

    public function test_presets_cover_whole_days_including_today(): void
    {
        $week = DateRange::fromPreset('7d', new DateTimeZone('UTC'));

        $this->assertSame(7, $week->days());
        $this->assertSame('2026-10-23', $week->localStart()->format('Y-m-d'));
        $this->assertSame('2026-10-29', $week->localLastDay()->format('Y-m-d'));
    }

    public function test_the_previous_period_has_the_same_length_and_ends_where_this_starts(): void
    {
        $range = DateRange::fromPreset('30d', new DateTimeZone('UTC'));
        $previous = $range->previous();

        $this->assertTrue($previous->end->eq($range->start));
        $this->assertSame($range->days(), $previous->days());
    }

    public function test_custom_ranges_are_ordered_and_capped(): void
    {
        $tz = new DateTimeZone('UTC');

        $swapped = DateRange::fromPreset('custom', $tz, '2026-10-10', '2026-10-01');
        $this->assertSame('2026-10-01', $swapped->localStart()->format('Y-m-d'));
        $this->assertSame('2026-10-10', $swapped->localLastDay()->format('Y-m-d'));

        $capped = DateRange::fromPreset('custom', $tz, '2020-01-01', '2026-10-29', 400);
        $this->assertSame(400, $capped->days());
    }

    public function test_unknown_presets_and_bad_dates_fall_back_to_30_days(): void
    {
        $tz = new DateTimeZone('UTC');

        $this->assertSame('30d', DateRange::fromPreset('forever', $tz)->preset);
        $this->assertSame('30d', DateRange::fromPreset('custom', $tz, 'yesterday', '2026-13-45')->preset);
    }
}
