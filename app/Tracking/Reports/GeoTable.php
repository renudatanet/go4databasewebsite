<?php

namespace App\Tracking\Reports;

use App\Tracking\Ingestion\LinkClassifier;

/**
 * Visitors by place, one level at a time: countries; the regions of one
 * country; the cities of one region.
 */
final class GeoTable implements Report
{
    public const COUNTRY = 'country';
    public const REGION = 'region';
    public const CITY = 'city';

    public function __construct(private ?string $countryCode = null, private ?string $region = null)
    {
    }

    public function level(): string
    {
        if ($this->countryCode === null) {
            return self::COUNTRY;
        }

        return $this->region === null ? self::REGION : self::CITY;
    }

    public function key(): string
    {
        return 'geo-table:' . md5(json_encode([$this->countryCode, $this->region]));
    }

    /** @return list<array<string, mixed>> */
    public function run(ReportContext $context): array
    {
        $groupColumns = match ($this->level()) {
            self::COUNTRY => ['s.country_code', 's.country'],
            self::REGION => ['s.region'],
            self::CITY => ['s.city'],
        };
        $select = implode(', ', $groupColumns);

        $scope = function ($query) {
            if ($this->countryCode !== null) {
                $query->where('s.country_code', $this->countryCode);
            }
            if ($this->region !== null) {
                $query->where('s.region', $this->region);
            }

            return $query;
        };

        $rows = $scope($context->sessions())
            ->groupBy($groupColumns)
            ->selectRaw("{$select}, COUNT(DISTINCT s.visitor_id) as visitors, COUNT(*) as sessions, COALESCE(SUM(s.page_views), 0) as page_views")
            ->orderByDesc('visitors')
            ->limit(250)
            ->get();

        $signups = $scope($context->events())
            ->where('e.name', LinkClassifier::SIGNUP_CLICK)
            ->groupBy($groupColumns)
            ->selectRaw("{$select}, COUNT(DISTINCT e.visitor_id) as signups")
            ->get()
            ->keyBy(fn ($r) => $this->rowKey($r));

        return $rows->map(function ($row) use ($signups) {
            $signupCount = (int) ($signups->get($this->rowKey($row))->signups ?? 0);

            return [
                'country_code' => $row->country_code ?? $this->countryCode,
                'country' => $row->country ?? null,
                'region' => $row->region ?? $this->region,
                'city' => $row->city ?? null,
                'name' => $this->displayName($row),
                'visitors' => (int) $row->visitors,
                'sessions' => (int) $row->sessions,
                'pages_per_session' => $row->sessions ? round($row->page_views / $row->sessions, 1) : 0.0,
                'signups' => $signupCount,
                'signup_rate' => Metrics::rate($signupCount, (int) $row->visitors),
            ];
        })->all();
    }

    private function rowKey(object $row): string
    {
        return match ($this->level()) {
            self::COUNTRY => (string) ($row->country_code ?? ''),
            self::REGION => (string) ($row->region ?? ''),
            self::CITY => (string) ($row->city ?? ''),
        };
    }

    private function displayName(object $row): string
    {
        $name = match ($this->level()) {
            self::COUNTRY => $row->country ?? null,
            self::REGION => $row->region ?? null,
            self::CITY => $row->city ?? null,
        };

        return $name ?: 'Unknown';
    }
}
