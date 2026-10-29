<?php

namespace App\Tracking\Http\Controllers\Admin;

use App\Tracking\Ingestion\LinkClassifier;
use App\Tracking\Reports\LinkTable;
use App\Tracking\Reports\NamedEvents;
use App\Tracking\Reports\ReportRunner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ClicksController
{
    public const EVENTS_TAB = 'events';

    public function __construct(private ScreenContext $screen, private ReportRunner $reports)
    {
    }

    public function index(Request $request): View|Response
    {
        $context = $this->screen->fromRequest($request);
        $tab = (string) $request->query('tab', '');
        $kind = array_key_exists($tab, LinkClassifier::KIND_LABELS) ? $tab : null;
        $search = trim((string) $request->query('q')) ?: null;

        if ($tab === self::EVENTS_TAB) {
            return view('tracking::admin.clicks', [
                'context' => $context,
                'tab' => $tab,
                'events' => $this->reports->run(new NamedEvents(), $context),
                'rows' => [],
                'paginator' => null,
                'search' => null,
            ]);
        }

        if ($request->query('export') === 'csv') {
            $all = $this->reports->run(new LinkTable(kind: $kind, search: $search, limit: Paging::EXPORT_LIMIT), $context);

            return CsvExport::download('tracking-clicks', [
                'label' => 'Link', 'href' => 'Goes to', 'kind' => 'Kind', 'clicks' => 'Clicks',
                'clickers' => 'Visitors who clicked', 'pages' => 'Pages it was clicked on',
            ], $all['rows']);
        }

        $paging = new Paging($request);
        $table = $this->reports->run(new LinkTable(kind: $kind, search: $search, limit: $paging->perPage, offset: $paging->offset()), $context);

        return view('tracking::admin.clicks', [
            'context' => $context,
            'tab' => $kind ?? '',
            'events' => [],
            'rows' => $table['rows'],
            'paginator' => $paging->paginator($table['total'], $table['rows']),
            'search' => $search,
        ]);
    }
}
