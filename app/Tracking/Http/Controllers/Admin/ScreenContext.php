<?php

namespace App\Tracking\Http\Controllers\Admin;

use App\Tracking\Reports\DateRange;
use App\Tracking\Reports\ReportContext;
use App\Tracking\Reports\ReportFilters;
use App\Tracking\Support\TrackingSettings;
use Illuminate\Http\Request;

/** Builds the period and filters a Tracking screen shows from its query string. */
final class ScreenContext
{
    public function __construct(private TrackingSettings $settings)
    {
    }

    public function fromRequest(Request $request): ReportContext
    {
        $range = DateRange::fromPreset(
            (string) $request->query('range', DateRange::DEFAULT_PRESET),
            $this->settings->timezone(),
            $request->query('from'),
            $request->query('to'),
            (int) config('tracking.max_report_days')
        );

        return new ReportContext(
            $range,
            ReportFilters::fromRequest($request),
            (int) config('tracking.bounce_engaged_ms')
        );
    }
}
