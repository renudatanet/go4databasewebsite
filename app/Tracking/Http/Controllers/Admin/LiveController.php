<?php

namespace App\Tracking\Http\Controllers\Admin;

use App\Tracking\Reports\LiveVisitors;
use App\Tracking\Support\Format;
use App\Tracking\Support\TrackingSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

final class LiveController
{
    public function __construct(private LiveVisitors $live, private TrackingSettings $settings)
    {
    }

    public function index(): View
    {
        return view('tracking::admin.live', ['initial' => $this->payload()]);
    }

    /** Polled by the Live screen every 15 seconds. */
    public function feed(): JsonResponse
    {
        return response()->json($this->payload())->header('Cache-Control', 'no-store');
    }

    /** @return array<string, mixed> plain values only; the page writes them as text */
    private function payload(): array
    {
        $data = $this->live->run();
        $tz = $this->settings->timezone();

        return [
            'count' => $data['count'],
            'pages' => $data['pages'],
            'updated' => Format::time(now(), $tz, 'H:i:s'),
            'visitors' => array_map(fn (array $v) => [
                'url' => route('admin.tracking.visitors.show', $v['visitor_id']),
                'name' => $v['name'] ?: $v['email'],
                'company' => $v['company'],
                'anonymous' => (bool) $v['is_anonymous'],
                'flag' => Format::flag($v['country_code']),
                'place' => implode(', ', array_filter(array_unique([$v['city'], $v['region'], $v['country']]))) ?: 'Unknown location',
                'network' => $v['network_org'],
                'network_type' => $v['network_type'],
                'device' => trim(ucfirst((string) $v['device_type']) . ' · ' . ($v['browser'] ?? '') . ' · ' . ($v['os'] ?? ''), ' ·'),
                'source' => $v['source'] ?: Format::channel($v['channel']),
                'path' => $v['current_path'],
                'title' => $v['current_title'],
                'pages' => (int) $v['page_views'],
                'clicks' => (int) $v['clicks'],
                'on_site' => Format::duration(strtotime($v['last_activity_at'] . ' UTC') * 1000 - strtotime($v['started_at'] . ' UTC') * 1000),
                'last_active' => Format::ago($v['last_activity_at']),
            ], $data['visitors']),
        ];
    }
}
