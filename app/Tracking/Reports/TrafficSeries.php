<?php

namespace App\Tracking\Reports;

use Illuminate\Database\Query\Builder;

/**
 * Visitors, visits and page views for each hour (one-day ranges) or day.
 * Every bucket is present, empty ones as zero, so charts don't skip days.
 * With a page id it counts only views of that page.
 */
final class TrafficSeries implements Report
{
    public function __construct(private ?int $pageId = null)
    {
    }

    public function key(): string
    {
        return 'traffic-series:' . ($this->pageId ?? 'all');
    }

    /** @return list<array{key: string, label: string, visitors: int, sessions: int, page_views: int}> */
    public function run(ReportContext $context): array
    {
        $range = $context->range;
        $offset = $range->offsetMinutes();

        $query = $this->pageId === null ? $this->siteQuery($context, $range->isHourly(), $offset) : $this->pageQuery($context, $range->isHourly(), $offset);
        $rows = $query->get()->keyBy(fn ($row) => (string) $row->bucket);

        $points = [];
        foreach ($this->buckets($range) as $key => $label) {
            $row = $rows->get($key);
            $points[] = [
                'key' => $key,
                'label' => $label,
                'visitors' => (int) ($row->visitors ?? 0),
                'sessions' => (int) ($row->sessions ?? 0),
                'page_views' => (int) ($row->page_views ?? 0),
            ];
        }

        return $points;
    }

    private function siteQuery(ReportContext $context, bool $hourly, int $offset): Builder
    {
        $bucket = $hourly ? Sql::localHour('s.started_at', $offset) : Sql::localDay('s.started_at', $offset);

        return $context->sessions()
            ->selectRaw("{$bucket} as bucket, COUNT(DISTINCT s.visitor_id) as visitors, COUNT(*) as sessions, COALESCE(SUM(s.page_views), 0) as page_views")
            ->groupByRaw($bucket);
    }

    private function pageQuery(ReportContext $context, bool $hourly, int $offset): Builder
    {
        $bucket = $hourly ? Sql::localHour('pv.viewed_at', $offset) : Sql::localDay('pv.viewed_at', $offset);

        return $context->pageViews()
            ->where('pv.page_id', $this->pageId)
            ->selectRaw("{$bucket} as bucket, COUNT(DISTINCT pv.visitor_id) as visitors, COUNT(DISTINCT pv.session_id) as sessions, COUNT(*) as page_views")
            ->groupByRaw($bucket);
    }

    /** @return array<string, string> bucket key as SQL returns it => chart label */
    private function buckets(DateRange $range): array
    {
        if ($range->isHourly()) {
            $buckets = [];
            for ($hour = 0; $hour < 24; $hour++) {
                $buckets[(string) $hour] = sprintf('%02d:00', $hour);
            }

            return $buckets;
        }

        $buckets = [];
        $day = $range->localStart()->startOfDay();
        $last = $range->localLastDay()->startOfDay();
        while ($day->lte($last)) {
            $buckets[$day->format('Y-m-d')] = $day->format('j M');
            $day = $day->addDay();
        }

        return $buckets;
    }
}
