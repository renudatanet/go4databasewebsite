<?php

namespace App\Tracking\Reports;

use Carbon\CarbonImmutable;
use DateTimeZone;

/**
 * The period a report covers, in the site owner's timezone. Stored times are
 * UTC; $start and $end are the UTC instants of local midnight, end exclusive.
 */
final class DateRange
{
    public const PRESETS = [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        '7d' => 'Last 7 days',
        '30d' => 'Last 30 days',
        '90d' => 'Last 90 days',
        '12m' => 'Last 12 months',
        'custom' => 'Custom',
    ];

    public const DEFAULT_PRESET = '30d';

    private function __construct(
        public CarbonImmutable $start,
        public CarbonImmutable $end,
        public DateTimeZone $timezone,
        public string $preset,
    ) {
    }

    public static function fromPreset(string $preset, DateTimeZone $tz, ?string $from = null, ?string $to = null, int $maxDays = 400): self
    {
        $today = CarbonImmutable::now($tz)->startOfDay();

        switch ($preset) {
            case 'today':
                return self::local($today, $today->addDay(), $tz, $preset);
            case 'yesterday':
                return self::local($today->subDay(), $today, $tz, $preset);
            case '7d':
                return self::local($today->subDays(6), $today->addDay(), $tz, $preset);
            case '90d':
                return self::local($today->subDays(89), $today->addDay(), $tz, $preset);
            case '12m':
                return self::local($today->subYear()->addDay(), $today->addDay(), $tz, $preset);
            case 'custom':
                $start = self::parseDay($from, $tz);
                $end = self::parseDay($to, $tz);
                if ($start !== null && $end !== null) {
                    if ($end->lt($start)) {
                        [$start, $end] = [$end, $start];
                    }
                    $start = $start->max($end->subDays($maxDays - 1));

                    return self::local($start, $end->addDay(), $tz, $preset);
                }
                // fall through to the default when the dates are unusable
            default:
                return self::local($today->subDays(29), $today->addDay(), $tz, self::DEFAULT_PRESET);
        }
    }

    /** The same length of time immediately before this one, for comparison. */
    public function previous(): self
    {
        $length = $this->start->diffInSeconds($this->end);

        return new self($this->start->subSeconds($length), $this->start, $this->timezone, $this->preset);
    }

    public function days(): int
    {
        return max(1, (int) round($this->start->diffInHours($this->end) / 24));
    }

    /** Hourly points for a single day, daily points otherwise. */
    public function isHourly(): bool
    {
        return $this->days() === 1;
    }

    /** The offset of the report's timezone from UTC, in minutes, for SQL bucketing. */
    public function offsetMinutes(): int
    {
        return intdiv($this->timezone->getOffset($this->start->toDateTime()), 60);
    }

    public function localStart(): CarbonImmutable
    {
        return $this->start->setTimezone($this->timezone);
    }

    /** The last day included, in local time. */
    public function localLastDay(): CarbonImmutable
    {
        return $this->end->setTimezone($this->timezone)->subDay();
    }

    public function label(): string
    {
        $first = $this->localStart();
        $last = $this->localLastDay();

        return $first->isSameDay($last)
            ? $first->format('j M Y')
            : $first->format($first->year === $last->year ? 'j M' : 'j M Y') . ' – ' . $last->format('j M Y');
    }

    /** @return array<string, string> */
    public function toQuery(): array
    {
        $query = ['range' => $this->preset];
        if ($this->preset === 'custom') {
            $query['from'] = $this->localStart()->format('Y-m-d');
            $query['to'] = $this->localLastDay()->format('Y-m-d');
        }

        return $query;
    }

    public function cacheKey(): string
    {
        return $this->start->timestamp . '-' . $this->end->timestamp . '-' . $this->timezone->getName();
    }

    private static function local(CarbonImmutable $start, CarbonImmutable $end, DateTimeZone $tz, string $preset): self
    {
        return new self($start->utc(), $end->utc(), $tz, $preset);
    }

    private static function parseDay(?string $value, DateTimeZone $tz): ?CarbonImmutable
    {
        if ($value === null || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            return CarbonImmutable::createFromFormat('!Y-m-d', $value, $tz) ?: null;
        } catch (\Exception) {
            return null;
        }
    }
}
