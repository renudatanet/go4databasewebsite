<?php

namespace App\Tracking\Reports;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/** Visits active within the last few minutes, and the page each is on. Never cached. */
final class LiveVisitors
{
    private const LIMIT = 100;

    public function __construct(private int $windowMinutes)
    {
    }

    /** @return array{count: int, pages: list<array>, visitors: list<array>} */
    public function run(?CarbonImmutable $now = null): array
    {
        $since = ($now ?? CarbonImmutable::now('UTC'))->subMinutes($this->windowMinutes);

        $base = DB::table('tracking_sessions as s')->where('s.last_activity_at', '>=', $since);

        $rows = (clone $base)
            ->join('tracking_visitors as v', 'v.id', '=', 's.visitor_id')
            ->leftJoin('tracking_pages as p', 'p.id', '=', 's.exit_page_id')
            ->select([
                's.id as session_id', 's.visitor_id', 's.started_at', 's.last_activity_at', 's.page_views', 's.clicks',
                's.country_code', 's.country', 's.region', 's.city', 's.network_org', 's.network_type',
                's.device_type', 's.browser', 's.os', 's.channel', 's.source',
                'v.name', 'v.email', 'v.company', 'v.is_anonymous',
                'p.path as current_path', 'p.title as current_title',
            ])
            ->orderByDesc('s.last_activity_at')
            ->limit(self::LIMIT)
            ->get();

        $pages = (clone $base)
            ->join('tracking_pages as p', 'p.id', '=', 's.exit_page_id')
            ->groupBy('p.id', 'p.path')
            ->selectRaw('p.id as page_id, p.path, COUNT(*) as visitors')
            ->orderByDesc('visitors')
            ->limit(10)
            ->get();

        return [
            'count' => (int) (clone $base)->distinct()->count('s.visitor_id'),
            'pages' => $pages->map(fn ($r) => ['page_id' => (int) $r->page_id, 'path' => $r->path, 'visitors' => (int) $r->visitors])->all(),
            'visitors' => $rows->map(fn ($r) => (array) $r)->all(),
        ];
    }
}
