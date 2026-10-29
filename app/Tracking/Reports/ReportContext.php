<?php

namespace App\Tracking\Reports;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * The period and filters a screen is showing, plus the three starting
 * queries every report builds on, already narrowed to them:
 *  - visits that started in the period,
 *  - page views in the period (their visit joined as "s"),
 *  - events in the period (their visit joined as "s").
 */
final class ReportContext
{
    public function __construct(
        public DateRange $range,
        public ReportFilters $filters,
        public int $bounceEngagedMs,
    ) {
    }

    public function withRange(DateRange $range): self
    {
        return new self($range, $this->filters, $this->bounceEngagedMs);
    }

    public function sessions(): Builder
    {
        $query = DB::table('tracking_sessions as s')
            ->where('s.started_at', '>=', $this->range->start)
            ->where('s.started_at', '<', $this->range->end);

        return $this->filters->apply($query, 's');
    }

    public function pageViews(): Builder
    {
        $query = DB::table('tracking_page_views as pv')
            ->join('tracking_sessions as s', 's.id', '=', 'pv.session_id')
            ->where('pv.viewed_at', '>=', $this->range->start)
            ->where('pv.viewed_at', '<', $this->range->end);

        return $this->filters->apply($query, 's');
    }

    public function events(): Builder
    {
        $query = DB::table('tracking_events as e')
            ->join('tracking_sessions as s', 's.id', '=', 'e.session_id')
            ->where('e.occurred_at', '>=', $this->range->start)
            ->where('e.occurred_at', '<', $this->range->end);

        return $this->filters->apply($query, 's');
    }

    /** SQL that is 1 for a bounced visit: one page, no click, under the engagement threshold. */
    public function bounceSql(string $alias = 's'): string
    {
        return "CASE WHEN {$alias}.page_views <= 1 AND {$alias}.clicks = 0 AND {$alias}.engaged_ms < {$this->bounceEngagedMs} THEN 1 ELSE 0 END";
    }

    public function cacheKey(): string
    {
        return $this->range->cacheKey() . ':' . $this->filters->cacheKey();
    }

    /** @return array<string, string> the period and filters as query values, for links that keep them */
    public function toQuery(): array
    {
        return $this->range->toQuery() + $this->filters->toQuery();
    }
}
