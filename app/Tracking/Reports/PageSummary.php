<?php

namespace App\Tracking\Reports;

use App\Tracking\Models\Event;

/** One page's figures for the period. */
final class PageSummary implements Report
{
    public function __construct(private int $pageId)
    {
    }

    public function key(): string
    {
        return 'page-summary:' . $this->pageId;
    }

    /** @return array<string, int|float> */
    public function run(ReportContext $context): array
    {
        $views = $context->pageViews()
            ->where('pv.page_id', $this->pageId)
            ->selectRaw(
                'COUNT(*) as views, COUNT(DISTINCT pv.visitor_id) as visitors, COALESCE(AVG(pv.engaged_ms), 0) as avg_engaged_ms, '
                . 'COALESCE(AVG(pv.max_scroll), 0) as avg_scroll, COALESCE(SUM(pv.is_entrance), 0) as entrances'
            )
            ->first();

        $exits = (int) $context->sessions()->where('s.exit_page_id', $this->pageId)->count();

        $clicks = $context->events()
            ->where('e.type', Event::TYPE_CLICK)
            ->where('e.page_id', $this->pageId)
            ->selectRaw('COUNT(*) as clicks, COUNT(DISTINCT e.visitor_id) as clickers')
            ->first();

        $viewCount = (int) $views->views;
        $visitors = (int) $views->visitors;

        return [
            'views' => $viewCount,
            'visitors' => $visitors,
            'avg_engaged_ms' => (int) round((float) $views->avg_engaged_ms),
            'avg_scroll' => (int) round((float) $views->avg_scroll),
            'entrances' => (int) $views->entrances,
            'exits' => $exits,
            'exit_rate' => Metrics::rate($exits, $viewCount),
            'clicks' => (int) $clicks->clicks,
            'clickers' => (int) $clicks->clickers,
            'ctr' => Metrics::rate((int) $clicks->clickers, $visitors),
        ];
    }
}
