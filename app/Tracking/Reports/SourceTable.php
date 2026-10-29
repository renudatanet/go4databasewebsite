<?php

namespace App\Tracking\Reports;

use App\Tracking\Ingestion\LinkClassifier;

/**
 * Visits by how they arrived: by channel, by named source, or by UTM
 * campaign, with engagement and sign-up clicks for each.
 */
final class SourceTable implements Report
{
    public const CHANNEL = 'channel';
    public const SOURCE = 'source';
    public const CAMPAIGN = 'campaign';

    private const GROUPS = [
        self::CHANNEL => ['s.channel'],
        self::SOURCE => ['s.channel', 's.source'],
        self::CAMPAIGN => ['s.campaign', 's.source', 's.medium'],
    ];

    public function __construct(private string $dimension = self::CHANNEL)
    {
        $this->dimension = array_key_exists($dimension, self::GROUPS) ? $dimension : self::CHANNEL;
    }

    public function key(): string
    {
        return 'source-table:' . $this->dimension;
    }

    /** @return list<array<string, mixed>> */
    public function run(ReportContext $context): array
    {
        $columns = self::GROUPS[$this->dimension];
        $select = implode(', ', $columns);
        $onlyCampaigns = fn ($query) => $this->dimension === self::CAMPAIGN ? $query->whereNotNull('s.campaign') : $query;

        $rows = $onlyCampaigns($context->sessions())
            ->groupBy($columns)
            ->selectRaw(
                "{$select}, COUNT(DISTINCT s.visitor_id) as visitors, COUNT(*) as sessions, "
                . 'COALESCE(SUM(' . $context->bounceSql() . '), 0) as bounces, COALESCE(AVG(s.engaged_ms), 0) as avg_engaged_ms'
            )
            ->orderByDesc('visitors')
            ->limit(200)
            ->get();

        $signups = $onlyCampaigns($context->events())
            ->where('e.name', LinkClassifier::SIGNUP_CLICK)
            ->groupBy($columns)
            ->selectRaw("{$select}, COUNT(DISTINCT e.visitor_id) as signups")
            ->get()
            ->keyBy(fn ($r) => $this->rowKey($r));

        return $rows->map(function ($row) use ($signups) {
            $signupCount = (int) ($signups->get($this->rowKey($row))->signups ?? 0);

            return [
                'channel' => $row->channel ?? null,
                'source' => $row->source ?? null,
                'medium' => $row->medium ?? null,
                'campaign' => $row->campaign ?? null,
                'visitors' => (int) $row->visitors,
                'sessions' => (int) $row->sessions,
                'bounce_rate' => Metrics::rate((int) $row->bounces, (int) $row->sessions),
                'avg_engaged_ms' => (int) round((float) $row->avg_engaged_ms),
                'signups' => $signupCount,
                'signup_rate' => Metrics::rate($signupCount, (int) $row->visitors),
            ];
        })->all();
    }

    private function rowKey(object $row): string
    {
        return implode('|', array_map(fn ($column) => (string) ($row->{substr($column, 2)} ?? ''), self::GROUPS[$this->dimension]));
    }
}
