<?php

namespace App\Tracking\Http\ViewComposers;

use App\Tracking\Data\DeviceInfo;
use App\Tracking\Data\TrafficSource;
use App\Tracking\Support\Format;
use App\Tracking\Support\TrackingSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** Gives every Tracking admin view the formatter, the timezone and the filter choices. */
final class TrackingViewComposer
{
    public function __construct(private TrackingSettings $settings)
    {
    }

    public function compose(View $view): void
    {
        $view->with([
            'fmt' => new Format(),
            'tz' => $this->settings->timezone(),
            'filterOptions' => [
                'countries' => $this->countries(),
                'devices' => [DeviceInfo::DESKTOP => 'Desktop', DeviceInfo::MOBILE => 'Mobile', DeviceInfo::TABLET => 'Tablet'],
                'channels' => TrafficSource::LABELS,
            ],
        ]);
    }

    /** @return array<string, string> code => name, for countries seen at least once */
    private function countries(): array
    {
        return Cache::remember('tracking:filter-countries', 600, fn () => DB::table('tracking_sessions')
            ->whereNotNull('country_code')
            ->groupBy('country_code')
            ->selectRaw('country_code, MAX(country) as country')
            ->orderBy('country')
            ->limit(250)
            ->pluck('country', 'country_code')
            ->all());
    }
}
