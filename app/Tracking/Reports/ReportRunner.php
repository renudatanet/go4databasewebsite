<?php

namespace App\Tracking\Reports;

use Illuminate\Contracts\Cache\Repository as Cache;

/**
 * Runs reports, caching each answer briefly: the screens are opened often,
 * the numbers move slowly, and the same report is often asked for twice
 * (a tile and its comparison). Live data skips the cache.
 */
final class ReportRunner
{
    private const CACHE_VERSION = 'v1';

    public function __construct(private Cache $cache, private int $ttlSeconds)
    {
    }

    public function run(Report $report, ReportContext $context): mixed
    {
        if ($this->ttlSeconds <= 0) {
            return $report->run($context);
        }

        $key = 'tracking:report:' . self::CACHE_VERSION . ':' . md5($report->key() . '|' . $context->cacheKey());

        return $this->cache->remember($key, $this->ttlSeconds, fn () => $report->run($context));
    }

    /** Runs a report for the context and for the period just before it. */
    public function runWithPrevious(Report $report, ReportContext $context): array
    {
        return [
            'current' => $this->run($report, $context),
            'previous' => $this->run($report, $context->withRange($context->range->previous())),
        ];
    }
}
