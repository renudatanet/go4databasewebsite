<?php

namespace App\Tracking\Reports;

use App\Tracking\Models\Event;
use Illuminate\Database\Query\Builder;

/**
 * Every page viewed in the period with its traffic, attention, exits and
 * clicks. Sorted, searched and paged in SQL; the extra figures are fetched
 * for the visible page of rows only.
 */
final class PageTable implements Report
{
    public const SORTS = [
        'views' => 'Views',
        'visitors' => 'Visitors',
        'avg_engaged_ms' => 'Engaged time',
        'entrances' => 'Entrances',
    ];

    public function __construct(
        private ?string $search = null,
        private ?string $section = null,
        private string $sort = 'views',
        private int $limit = 50,
        private int $offset = 0,
    ) {
        $this->sort = array_key_exists($sort, self::SORTS) ? $sort : 'views';
        $this->limit = max(1, min(5000, $limit));
        $this->offset = max(0, $offset);
    }

    public function key(): string
    {
        return 'page-table:' . md5(json_encode([$this->search, $this->section, $this->sort, $this->limit, $this->offset]));
    }

    /** @return array{total: int, rows: list<array<string, mixed>>} */
    public function run(ReportContext $context): array
    {
        $base = $this->scoped($context);
        $total = (int) (clone $base)->distinct()->count('pv.page_id');

        $rows = (clone $base)
            ->groupBy('pv.page_id', 'p.path', 'p.title')
            ->selectRaw(
                'pv.page_id, p.path, p.title, COUNT(*) as views, COUNT(DISTINCT pv.visitor_id) as visitors, '
                . 'AVG(pv.engaged_ms) as avg_engaged_ms, AVG(pv.max_scroll) as avg_scroll, SUM(pv.is_entrance) as entrances'
            )
            ->orderByDesc($this->sort)
            ->orderBy('p.path')
            ->limit($this->limit)
            ->offset($this->offset)
            ->get();

        $ids = $rows->pluck('page_id')->all();
        $exits = $ids === [] ? collect() : $context->sessions()
            ->whereIn('s.exit_page_id', $ids)
            ->groupBy('s.exit_page_id')
            ->selectRaw('s.exit_page_id as page_id, COUNT(*) as exits')
            ->pluck('exits', 'page_id');
        $clicks = $ids === [] ? collect() : $context->events()
            ->where('e.type', Event::TYPE_CLICK)
            ->whereIn('e.page_id', $ids)
            ->groupBy('e.page_id')
            ->selectRaw('e.page_id, COUNT(*) as clicks, COUNT(DISTINCT e.visitor_id) as clickers')
            ->get()
            ->keyBy('page_id');

        return [
            'total' => $total,
            'rows' => $rows->map(function ($row) use ($exits, $clicks) {
                $click = $clicks->get($row->page_id);
                $exitCount = (int) ($exits[$row->page_id] ?? 0);

                return [
                    'page_id' => (int) $row->page_id,
                    'path' => $row->path,
                    'title' => $row->title,
                    'views' => (int) $row->views,
                    'visitors' => (int) $row->visitors,
                    'avg_engaged_ms' => (int) round((float) $row->avg_engaged_ms),
                    'avg_scroll' => (int) round((float) $row->avg_scroll),
                    'entrances' => (int) $row->entrances,
                    'exits' => $exitCount,
                    'exit_rate' => Metrics::rate($exitCount, (int) $row->views),
                    'clicks' => (int) ($click->clicks ?? 0),
                    'ctr' => Metrics::rate((int) ($click->clickers ?? 0), (int) $row->visitors),
                ];
            })->all(),
        ];
    }

    private function scoped(ReportContext $context): Builder
    {
        return $context->pageViews()
            ->join('tracking_pages as p', 'p.id', '=', 'pv.page_id')
            ->when($this->search, fn (Builder $q) => $q->where(fn (Builder $w) => $w
                ->where('p.path', 'like', Metrics::like($this->search))
                ->orWhere('p.title', 'like', Metrics::like($this->search))))
            ->when($this->section, fn (Builder $q) => $q->where('p.section', $this->section));
    }
}
