<?php

namespace App\Tracking\Http\Controllers\Admin;

use App\Tracking\Models\Visitor;
use App\Tracking\Reports\DeviceTable;
use App\Tracking\Reports\GeoTable;
use App\Tracking\Reports\LiveVisitors;
use App\Tracking\Reports\OverviewTotals;
use App\Tracking\Reports\PageTable;
use App\Tracking\Reports\ReportRunner;
use App\Tracking\Reports\SourceTable;
use App\Tracking\Reports\TrafficSeries;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class OverviewController
{
    public function __construct(private ScreenContext $screen, private ReportRunner $reports)
    {
    }

    public function index(Request $request, LiveVisitors $live): View
    {
        $context = $this->screen->fromRequest($request);
        $series = new TrafficSeries();

        return view('tracking::admin.overview', [
            'context' => $context,
            'totals' => $this->reports->runWithPrevious(new OverviewTotals(), $context),
            'series' => $this->reports->run($series, $context),
            'previousSeries' => $this->reports->run($series, $context->withRange($context->range->previous())),
            'pages' => $this->reports->run(new PageTable(limit: 8), $context)['rows'],
            'channels' => $this->reports->run(new SourceTable(SourceTable::CHANNEL), $context),
            'countries' => array_slice($this->reports->run(new GeoTable(), $context), 0, 8),
            'devices' => $this->reports->run(new DeviceTable('device_type'), $context),
            'browsers' => $this->reports->run(new DeviceTable('browser', 6), $context),
            'liveCount' => $live->run()['count'],
            'recentlyIdentified' => Visitor::whereNotNull('identified_at')->orderByDesc('identified_at')->limit(6)->get(),
        ]);
    }
}
