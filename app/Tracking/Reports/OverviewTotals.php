<?php

namespace App\Tracking\Reports;

use App\Tracking\Ingestion\LinkClassifier;
use App\Tracking\Models\Event;

/** The headline figures: visitors, visits, page views, time, bounce, clicks, sign-ups. */
final class OverviewTotals implements Report
{
    public function key(): string
    {
        return 'overview-totals';
    }

    /** @return array<string, int|float> */
    public function run(ReportContext $context): array
    {
        $sessions = $context->sessions()->selectRaw(
            'COUNT(*) as sessions, COUNT(DISTINCT s.visitor_id) as visitors, '
            . 'COALESCE(SUM(s.page_views), 0) as page_views, COALESCE(AVG(s.engaged_ms), 0) as avg_engaged_ms, '
            . 'COALESCE(SUM(' . $context->bounceSql() . '), 0) as bounces'
        )->first();

        $clicks = (int) $context->events()->where('e.type', Event::TYPE_CLICK)->count();

        $signupVisitors = (int) $context->events()
            ->where('e.name', LinkClassifier::SIGNUP_CLICK)
            ->selectRaw('COUNT(DISTINCT e.visitor_id) as n')
            ->value('n');

        $identified = (int) $context->sessions()
            ->join('tracking_visitors as v', 'v.id', '=', 's.visitor_id')
            ->whereNotNull('v.identified_at')
            ->selectRaw('COUNT(DISTINCT s.visitor_id) as n')
            ->value('n');

        $visitors = (int) $sessions->visitors;
        $count = (int) $sessions->sessions;

        return [
            'visitors' => $visitors,
            'sessions' => $count,
            'page_views' => (int) $sessions->page_views,
            'pages_per_session' => $count ? round($sessions->page_views / $count, 2) : 0.0,
            'avg_engaged_ms' => (int) round((float) $sessions->avg_engaged_ms),
            'bounce_rate' => Metrics::rate((int) $sessions->bounces, $count),
            'clicks' => $clicks,
            'signup_visitors' => $signupVisitors,
            'signup_rate' => Metrics::rate($signupVisitors, $visitors),
            'identified' => $identified,
        ];
    }
}
