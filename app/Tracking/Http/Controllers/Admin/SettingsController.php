<?php

namespace App\Tracking\Http\Controllers\Admin;

use App\Tracking\Http\Requests\UpdateTrackingSettingsRequest;
use App\Tracking\Maintenance\GeoDatabaseUpdater;
use App\Tracking\Support\TrackingSettings;
use DateTimeZone;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class SettingsController
{
    public function __construct(private TrackingSettings $settings)
    {
    }

    public function index(Request $request, GeoDatabaseUpdater $geo): View
    {
        return view('tracking::admin.settings', [
            'settings' => $this->settings,
            'geo' => $geo->status(),
            'timezones' => DateTimeZone::listIdentifiers(),
            'yourIp' => $request->ip(),
            'counts' => [
                'Visitors' => DB::table('tracking_visitors')->count(),
                'Visits' => DB::table('tracking_sessions')->count(),
                'Page views' => DB::table('tracking_page_views')->count(),
                'Clicks and events' => DB::table('tracking_events')->count(),
            ],
            'collectUrl' => route('tracking.collect'),
            'appEventUrl' => route('tracking.app-event'),
        ]);
    }

    public function update(UpdateTrackingSettingsRequest $request): RedirectResponse
    {
        $this->settings->update($request->settings());

        return back()->with(['msg' => __('Tracking settings saved.'), 'type' => 'success']);
    }

    public function regenerateSecret(): RedirectResponse
    {
        $this->settings->setAppSecret(bin2hex(random_bytes(32)));

        return back()->with([
            'msg' => __('A new app secret was made. The app must use it from now on; messages signed with the old one are refused.'),
            'type' => 'warning',
        ]);
    }

    public function updateGeo(GeoDatabaseUpdater $geo): RedirectResponse
    {
        @set_time_limit(600);
        $results = $geo->update();
        $failed = array_filter($results, fn ($r) => str_starts_with($r, 'failed'));

        $lines = [];
        foreach ($results as $kind => $result) {
            $lines[] = e(ucfirst($kind) . ' file: ' . $result);
        }

        return back()->with(['msg' => implode('<br>', $lines), 'type' => $failed ? 'danger' : 'success']);
    }
}
