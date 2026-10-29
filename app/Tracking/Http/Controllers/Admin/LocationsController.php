<?php

namespace App\Tracking\Http\Controllers\Admin;

use App\Tracking\Reports\GeoTable;
use App\Tracking\Reports\NetworkTable;
use App\Tracking\Reports\ReportRunner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class LocationsController
{
    public function __construct(private ScreenContext $screen, private ReportRunner $reports)
    {
    }

    public function index(Request $request): View|Response
    {
        $context = $this->screen->fromRequest($request);
        $country = strtoupper((string) $request->query('in_country'));
        $country = preg_match('/^[A-Z]{2}$/', $country) ? $country : null;
        $region = $country !== null ? (trim((string) $request->query('in_region')) ?: null) : null;

        $table = new GeoTable($country, $region);
        $rows = $this->reports->run($table, $context);

        if ($request->query('export') === 'csv') {
            return CsvExport::download('tracking-locations', [
                'name' => ucfirst($table->level()), 'visitors' => 'Visitors', 'sessions' => 'Visits',
                'pages_per_session' => 'Pages per visit', 'signups' => 'Sign-up clicks', 'signup_rate' => 'Sign-up rate',
            ], $rows);
        }

        $countries = $country === null ? $rows : $this->reports->run(new GeoTable(), $context);

        return view('tracking::admin.locations', [
            'context' => $context,
            'level' => $table->level(),
            'country' => $country,
            'countryName' => collect($countries)->firstWhere('country_code', $country)['country'] ?? $country,
            'region' => $region,
            'rows' => $rows,
            'mapData' => collect($countries)->filter(fn ($r) => $r['country_code'])->mapWithKeys(fn ($r) => [$r['country_code'] => $r['visitors']])->all(),
            'networks' => $this->reports->run(new NetworkTable(), $context),
        ]);
    }
}
