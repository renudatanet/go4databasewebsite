<?php

namespace App\Tracking\Http\Controllers\Admin;

use App\Tracking\Reports\ReportRunner;
use App\Tracking\Reports\SourceTable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SourcesController
{
    public const TABS = [
        SourceTable::CHANNEL => 'Channels',
        SourceTable::SOURCE => 'Sources',
        SourceTable::CAMPAIGN => 'Campaigns',
    ];

    public function __construct(private ScreenContext $screen, private ReportRunner $reports)
    {
    }

    public function index(Request $request): View|Response
    {
        $context = $this->screen->fromRequest($request);
        $tab = array_key_exists((string) $request->query('tab'), self::TABS) ? (string) $request->query('tab') : SourceTable::CHANNEL;
        $rows = $this->reports->run(new SourceTable($tab), $context);

        if ($request->query('export') === 'csv') {
            return CsvExport::download('tracking-sources', [
                'channel' => 'Channel', 'source' => 'Source', 'medium' => 'Medium', 'campaign' => 'Campaign',
                'visitors' => 'Visitors', 'sessions' => 'Visits', 'bounce_rate' => 'Bounce rate',
                'avg_engaged_ms' => 'Avg engaged ms', 'signups' => 'Sign-up clicks', 'signup_rate' => 'Sign-up rate',
            ], $rows);
        }

        return view('tracking::admin.sources', [
            'context' => $context,
            'tab' => $tab,
            'tabs' => self::TABS,
            'rows' => $rows,
        ]);
    }
}
