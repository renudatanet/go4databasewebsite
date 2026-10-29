<?php

namespace App\Tracking\Reports;

use App\Tracking\Data\NetworkType;
use App\Tracking\Ingestion\LinkClassifier;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Visitors active in the period, newest activity first, each with their
 * latest visit's location, network and device.
 */
final class VisitorTable implements Report
{
    public const SEGMENTS = [
        '' => 'Everyone',
        'identified' => 'Known by name',
        'returning' => 'Returning',
        'signup' => 'Clicked Sign up',
        'business' => 'From a business network',
    ];

    public function __construct(
        private ?string $search = null,
        private string $segment = '',
        private int $limit = 50,
        private int $offset = 0,
    ) {
        $this->segment = array_key_exists($segment, self::SEGMENTS) ? $segment : '';
        $this->limit = max(1, min(5000, $limit));
        $this->offset = max(0, $offset);
    }

    public function key(): string
    {
        return 'visitor-table:' . md5(json_encode([$this->search, $this->segment, $this->limit, $this->offset]));
    }

    /** @return array{total: int, rows: list<array<string, mixed>>} */
    public function run(ReportContext $context): array
    {
        $base = $this->scoped($context);
        $total = (int) (clone $base)->distinct()->count('s.visitor_id');

        $aggregates = (clone $base)
            ->groupBy('s.visitor_id')
            ->selectRaw('s.visitor_id, COUNT(*) as sessions, COALESCE(SUM(s.page_views), 0) as page_views, MAX(s.last_activity_at) as last_seen, MAX(s.id) as latest_session_id')
            ->orderByDesc('last_seen')
            ->orderByDesc('s.visitor_id')
            ->limit($this->limit)
            ->offset($this->offset)
            ->get();

        $visitors = DB::table('tracking_visitors')->whereIn('id', $aggregates->pluck('visitor_id'))->get()->keyBy('id');
        $sessions = DB::table('tracking_sessions')->whereIn('id', $aggregates->pluck('latest_session_id'))->get()->keyBy('id');

        return [
            'total' => $total,
            'rows' => $aggregates->map(function ($row) use ($visitors, $sessions) {
                $visitor = $visitors->get($row->visitor_id);
                $session = $sessions->get($row->latest_session_id);

                return [
                    'visitor_id' => (int) $row->visitor_id,
                    'name' => $visitor->name ?? null,
                    'email' => $visitor->email ?? null,
                    'company' => $visitor->company ?? null,
                    'is_anonymous' => (bool) ($visitor->is_anonymous ?? false),
                    'total_sessions' => (int) ($visitor->sessions_count ?? 0),
                    'first_source' => $visitor->first_source ?? null,
                    'sessions' => (int) $row->sessions,
                    'page_views' => (int) $row->page_views,
                    'last_seen' => $row->last_seen,
                    'country_code' => $session->country_code ?? null,
                    'country' => $session->country ?? null,
                    'region' => $session->region ?? null,
                    'city' => $session->city ?? null,
                    'network_org' => $session->network_org ?? null,
                    'network_type' => $session->network_type ?? NetworkType::UNKNOWN,
                    'device_type' => $session->device_type ?? null,
                    'browser' => $session->browser ?? null,
                    'os' => $session->os ?? null,
                    'channel' => $session->channel ?? null,
                    'source' => $session->source ?? null,
                ];
            })->all(),
        ];
    }

    private function scoped(ReportContext $context): Builder
    {
        $query = $context->sessions();

        if ($this->search !== null && $this->search !== '') {
            $like = Metrics::like($this->search);
            $query->where(fn (Builder $w) => $w
                ->where('s.network_org', 'like', $like)
                ->orWhere('s.city', 'like', $like)
                ->orWhere('s.region', 'like', $like)
                ->orWhere('s.country', 'like', $like)
                ->orWhereExists(fn (Builder $sub) => $sub->selectRaw('1')
                    ->from('tracking_visitors as sv')
                    ->whereColumn('sv.id', 's.visitor_id')
                    ->where(fn (Builder $n) => $n
                        ->where('sv.name', 'like', $like)
                        ->orWhere('sv.email', 'like', $like)
                        ->orWhere('sv.company', 'like', $like))));
        }

        switch ($this->segment) {
            case 'identified':
                $query->whereExists(fn (Builder $sub) => $sub->selectRaw('1')->from('tracking_visitors as sv')
                    ->whereColumn('sv.id', 's.visitor_id')->whereNotNull('sv.identified_at'));
                break;
            case 'returning':
                $query->whereExists(fn (Builder $sub) => $sub->selectRaw('1')->from('tracking_visitors as sv')
                    ->whereColumn('sv.id', 's.visitor_id')->where('sv.sessions_count', '>', 1));
                break;
            case 'signup':
                $query->whereExists(fn (Builder $sub) => $sub->selectRaw('1')->from('tracking_events as se')
                    ->whereColumn('se.session_id', 's.id')->where('se.name', LinkClassifier::SIGNUP_CLICK));
                break;
            case 'business':
                $query->where('s.network_type', NetworkType::BUSINESS);
                break;
        }

        return $query;
    }
}
