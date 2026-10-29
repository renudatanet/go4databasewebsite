<?php

namespace App\Tracking\Reports;

use App\Tracking\Data\NetworkType;

/** Companies seen visiting from their own (business) networks. */
final class NetworkTable implements Report
{
    public function __construct(private int $limit = 50)
    {
    }

    public function key(): string
    {
        return 'network-table:' . $this->limit;
    }

    /** @return list<array<string, mixed>> */
    public function run(ReportContext $context): array
    {
        return $context->sessions()
            ->where('s.network_type', NetworkType::BUSINESS)
            ->whereNotNull('s.network_org')
            ->groupBy('s.network_org')
            ->selectRaw(
                's.network_org, COUNT(DISTINCT s.visitor_id) as visitors, COUNT(*) as sessions, '
                . 'COALESCE(SUM(s.page_views), 0) as page_views, MAX(s.last_activity_at) as last_seen, '
                . 'MAX(s.country) as country, MAX(s.city) as city'
            )
            ->orderByDesc('visitors')
            ->orderByDesc('page_views')
            ->limit($this->limit)
            ->get()
            ->map(fn ($r) => [
                'network_org' => $r->network_org,
                'visitors' => (int) $r->visitors,
                'sessions' => (int) $r->sessions,
                'page_views' => (int) $r->page_views,
                'last_seen' => $r->last_seen,
                'country' => $r->country,
                'city' => $r->city,
            ])
            ->all();
    }
}
