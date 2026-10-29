<?php

namespace App\Tracking\Reports;

use App\Tracking\Models\Event;
use Illuminate\Database\Query\Builder;

/**
 * Clicked links and buttons with clicks, distinct clickers and on how many
 * pages. With a page id: the links on that page, with the page's own
 * click-through rate for each.
 */
final class LinkTable implements Report
{
    public function __construct(
        private ?int $pageId = null,
        private ?string $kind = null,
        private ?string $search = null,
        private int $limit = 50,
        private int $offset = 0,
    ) {
        $this->limit = max(1, min(5000, $limit));
        $this->offset = max(0, $offset);
    }

    public function key(): string
    {
        return 'link-table:' . md5(json_encode([$this->pageId, $this->kind, $this->search, $this->limit, $this->offset]));
    }

    /** @return array{total: int, rows: list<array<string, mixed>>} */
    public function run(ReportContext $context): array
    {
        $base = $this->scoped($context);
        $total = (int) (clone $base)->distinct()->count('e.link_id');

        $rows = (clone $base)
            ->groupBy('e.link_id', 'l.href', 'l.kind', 'l.label')
            ->selectRaw(
                'e.link_id, l.href, l.kind, l.label as link_label, MAX(e.label) as label, MAX(e.section) as section, '
                . 'MAX(e.name) as name, COUNT(*) as clicks, COUNT(DISTINCT e.visitor_id) as clickers, COUNT(DISTINCT e.page_id) as pages'
            )
            ->orderByDesc('clicks')
            ->orderBy('e.link_id')
            ->limit($this->limit)
            ->offset($this->offset)
            ->get();

        $pageVisitors = $this->pageId === null ? 0 : (int) $context->pageViews()
            ->where('pv.page_id', $this->pageId)
            ->selectRaw('COUNT(DISTINCT pv.visitor_id) as n')
            ->value('n');

        return [
            'total' => $total,
            'rows' => $rows->map(fn ($row) => [
                'link_id' => (int) $row->link_id,
                'href' => $row->href,
                'kind' => $row->kind,
                'name' => $row->name,
                'label' => $row->label ?? $row->link_label,
                'section' => $row->section,
                'clicks' => (int) $row->clicks,
                'clickers' => (int) $row->clickers,
                'pages' => (int) $row->pages,
                'ctr' => $this->pageId === null ? null : Metrics::rate((int) $row->clickers, $pageVisitors),
            ])->all(),
        ];
    }

    private function scoped(ReportContext $context): Builder
    {
        return $context->events()
            ->join('tracking_links as l', 'l.id', '=', 'e.link_id')
            ->where('e.type', Event::TYPE_CLICK)
            ->when($this->pageId, fn (Builder $q) => $q->where('e.page_id', $this->pageId))
            ->when($this->kind, fn (Builder $q) => $q->where('l.kind', $this->kind))
            ->when($this->search, fn (Builder $q) => $q->where(fn (Builder $w) => $w
                ->where('l.href', 'like', Metrics::like($this->search))
                ->orWhere('l.label', 'like', Metrics::like($this->search))
                ->orWhere('e.label', 'like', Metrics::like($this->search))));
    }
}
