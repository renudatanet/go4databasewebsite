<?php

namespace App\Tracking\Reports;

/**
 * Where a page's visitors came from (the page before it, or, for visits
 * that started there, the outside source) and where they went next (the
 * page after it, or nowhere: they left).
 */
final class PageFlow implements Report
{
    private const LIMIT = 10;

    public function __construct(private int $pageId)
    {
    }

    public function key(): string
    {
        return 'page-flow:' . $this->pageId;
    }

    /** @return array{previous_pages: list<array>, sources: list<array>, next_pages: list<array>, exits: int} */
    public function run(ReportContext $context): array
    {
        $previousPages = $context->pageViews()
            ->join('tracking_pages as p', 'p.id', '=', 'pv.previous_page_id')
            ->where('pv.page_id', $this->pageId)
            ->groupBy('pv.previous_page_id', 'p.path')
            ->selectRaw('pv.previous_page_id as page_id, p.path, COUNT(*) as views')
            ->orderByDesc('views')
            ->limit(self::LIMIT)
            ->get();

        $sources = $context->pageViews()
            ->where('pv.page_id', $this->pageId)
            ->where('pv.is_entrance', true)
            ->groupBy('s.channel', 's.source')
            ->selectRaw('s.channel, s.source, COUNT(*) as views')
            ->orderByDesc('views')
            ->limit(self::LIMIT)
            ->get();

        $nextPages = $context->pageViews()
            ->join('tracking_pages as p', 'p.id', '=', 'pv.page_id')
            ->where('pv.previous_page_id', $this->pageId)
            ->groupBy('pv.page_id', 'p.path')
            ->selectRaw('pv.page_id, p.path, COUNT(*) as views')
            ->orderByDesc('views')
            ->limit(self::LIMIT)
            ->get();

        return [
            'previous_pages' => $previousPages->map(fn ($r) => ['page_id' => (int) $r->page_id, 'path' => $r->path, 'views' => (int) $r->views])->all(),
            'sources' => $sources->map(fn ($r) => ['channel' => $r->channel, 'source' => $r->source, 'views' => (int) $r->views])->all(),
            'next_pages' => $nextPages->map(fn ($r) => ['page_id' => (int) $r->page_id, 'path' => $r->path, 'views' => (int) $r->views])->all(),
            'exits' => (int) $context->sessions()->where('s.exit_page_id', $this->pageId)->count(),
        ];
    }
}
