<?php

namespace App\Tracking\Http\Controllers\Admin;

use App\Tracking\Models\Page;
use App\Tracking\Reports\LinkTable;
use App\Tracking\Reports\PageFlow;
use App\Tracking\Reports\PageSummary;
use App\Tracking\Reports\PageTable;
use App\Tracking\Reports\ReportRunner;
use App\Tracking\Reports\TrafficSeries;
use App\Tracking\Support\Format;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class PagesController
{
    public function __construct(private ScreenContext $screen, private ReportRunner $reports)
    {
    }

    public function index(Request $request): View|Response
    {
        $context = $this->screen->fromRequest($request);
        $search = trim((string) $request->query('q')) ?: null;
        $section = $request->query('section') ?: null;
        $sort = (string) $request->query('sort', 'views');

        if ($request->query('export') === 'csv') {
            $all = $this->reports->run(new PageTable($search, $section, $sort, Paging::EXPORT_LIMIT), $context);

            return CsvExport::download('tracking-pages', [
                'path' => 'Page', 'title' => 'Title', 'views' => 'Views', 'visitors' => 'Visitors',
                'avg_engaged_ms' => 'Avg engaged ms', 'avg_scroll' => 'Avg scroll %', 'entrances' => 'Entrances',
                'exits' => 'Exits', 'clicks' => 'Clicks', 'ctr' => 'Click-through rate',
            ], $all['rows']);
        }

        $paging = new Paging($request);
        $table = $this->reports->run(new PageTable($search, $section, $sort, $paging->perPage, $paging->offset()), $context);

        return view('tracking::admin.pages.index', [
            'context' => $context,
            'rows' => $table['rows'],
            'paginator' => $paging->paginator($table['total'], $table['rows']),
            'sections' => Page::query()->distinct()->orderBy('section')->pluck('section'),
            'search' => $search,
            'section' => $section,
            'sort' => $sort,
            'sorts' => PageTable::SORTS,
        ]);
    }

    public function show(Request $request, int $page): View|Response
    {
        $page = Page::findOrFail($page);
        $context = $this->screen->fromRequest($request);
        $links = $this->reports->run(new LinkTable(pageId: $page->id, limit: 100), $context);

        if ($request->query('export') === 'csv') {
            return CsvExport::download('tracking-page-links', [
                'label' => 'Link', 'href' => 'Goes to', 'kind' => 'Kind', 'section' => 'Section',
                'clicks' => 'Clicks', 'clickers' => 'Visitors who clicked', 'ctr' => 'Click-through rate',
            ], $links['rows']);
        }

        return view('tracking::admin.pages.show', [
            'context' => $context,
            'page' => $page,
            'summary' => $this->reports->runWithPrevious(new PageSummary($page->id), $context),
            'series' => $this->reports->run(new TrafficSeries($page->id), $context),
            'previousSeries' => $this->reports->run(new TrafficSeries($page->id), $context->withRange($context->range->previous())),
            'flow' => $this->reports->run(new PageFlow($page->id), $context),
            'links' => $links['rows'],
        ]);
    }
}
