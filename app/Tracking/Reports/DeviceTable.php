<?php

namespace App\Tracking\Reports;

/** Visitors by device type, browser or operating system. */
final class DeviceTable implements Report
{
    private const DIMENSIONS = ['device_type', 'browser', 'os'];

    public function __construct(private string $dimension = 'device_type', private int $limit = 8)
    {
        $this->dimension = in_array($dimension, self::DIMENSIONS, true) ? $dimension : 'device_type';
    }

    public function key(): string
    {
        return 'device-table:' . $this->dimension . ':' . $this->limit;
    }

    /** @return list<array{name: string, visitors: int, sessions: int}> */
    public function run(ReportContext $context): array
    {
        $column = 's.' . $this->dimension;

        return $context->sessions()
            ->groupBy($column)
            ->selectRaw("{$column} as name, COUNT(DISTINCT s.visitor_id) as visitors, COUNT(*) as sessions")
            ->orderByDesc('visitors')
            ->limit($this->limit)
            ->get()
            ->map(fn ($r) => ['name' => $r->name ?: 'Unknown', 'visitors' => (int) $r->visitors, 'sessions' => (int) $r->sessions])
            ->all();
    }
}
