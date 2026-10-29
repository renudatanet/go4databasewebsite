<?php

namespace App\Tracking\Http\Controllers\Admin;

use App\Tracking\Maintenance\VisitorEraser;
use App\Tracking\Models\Visitor;
use App\Tracking\Reports\ReportRunner;
use App\Tracking\Reports\VisitorJourney;
use App\Tracking\Reports\VisitorTable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class VisitorsController
{
    public function __construct(private ScreenContext $screen, private ReportRunner $reports)
    {
    }

    public function index(Request $request): View|Response
    {
        $context = $this->screen->fromRequest($request);
        $search = trim((string) $request->query('q')) ?: null;
        $segment = (string) $request->query('segment', '');

        if ($request->query('export') === 'csv') {
            $all = $this->reports->run(new VisitorTable($search, $segment, Paging::EXPORT_LIMIT), $context);

            return CsvExport::download('tracking-visitors', [
                'name' => 'Name', 'email' => 'Email', 'company' => 'Company', 'country' => 'Country',
                'region' => 'Region', 'city' => 'City', 'network_org' => 'Network', 'network_type' => 'Network type',
                'device_type' => 'Device', 'browser' => 'Browser', 'os' => 'OS', 'first_source' => 'First source',
                'sessions' => 'Visits in period', 'page_views' => 'Pages in period', 'last_seen' => 'Last seen (UTC)',
            ], $all['rows']);
        }

        $paging = new Paging($request);
        $table = $this->reports->run(new VisitorTable($search, $segment, $paging->perPage, $paging->offset()), $context);

        return view('tracking::admin.visitors.index', [
            'context' => $context,
            'rows' => $table['rows'],
            'paginator' => $paging->paginator($table['total'], $table['rows']),
            'search' => $search,
            'segment' => $segment,
            'segments' => VisitorTable::SEGMENTS,
        ]);
    }

    public function show(int $visitor, VisitorJourney $journey): View
    {
        $data = $journey->run($visitor);
        abort_if($data === null, 404);

        return view('tracking::admin.visitors.show', $data);
    }

    public function destroy(int $visitor, VisitorEraser $eraser): RedirectResponse
    {
        $eraser->erase(Visitor::findOrFail($visitor));

        return redirect()->route('admin.tracking.visitors')->with([
            'msg' => __('Everything held about that visitor has been deleted.'),
            'type' => 'success',
        ]);
    }
}
