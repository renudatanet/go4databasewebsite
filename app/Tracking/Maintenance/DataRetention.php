<?php

namespace App\Tracking\Maintenance;

use App\Tracking\Support\TrackingSettings;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Keeps the store within the retention settings: masks full IP addresses
 * after N days, and deletes visits (with their page views and clicks) and
 * long-gone visitors after M days. Works in batches so a large backlog
 * never locks the tables for long.
 */
final class DataRetention
{
    private const BATCH = 2000;

    public function __construct(private TrackingSettings $settings)
    {
    }

    /** @return array{ips_masked: int, sessions_deleted: int, visitors_deleted: int} */
    public function run(?CarbonImmutable $now = null): array
    {
        $now ??= CarbonImmutable::now('UTC');
        $ipCutoff = $now->subDays($this->settings->ipRetentionDays());
        $dataCutoff = $now->subDays($this->settings->dataRetentionDays());

        // Deletions first, so no time is spent masking rows about to go.
        $sessionsDeleted = $this->deleteInBatches('tracking_sessions', 'started_at', $dataCutoff);
        $visitorsDeleted = $this->deleteInBatches('tracking_visitors', 'last_seen_at', $dataCutoff);

        $result = [
            'ips_masked' => $this->maskIps($ipCutoff),
            'sessions_deleted' => $sessionsDeleted,
            'visitors_deleted' => $visitorsDeleted,
        ];

        $this->settings->markPruned($now->toDateTimeString());

        return $result;
    }

    private function maskIps(CarbonImmutable $cutoff): int
    {
        // ip_masked was written alongside ip, so clearing ip is the whole job.
        $total = 0;
        do {
            $ids = DB::table('tracking_sessions')
                ->whereNotNull('ip')
                ->where('started_at', '<', $cutoff)
                ->limit(self::BATCH)
                ->pluck('id');
            if ($ids->isEmpty()) {
                break;
            }
            $total += DB::table('tracking_sessions')->whereIn('id', $ids)->update(['ip' => null]);
        } while ($ids->count() === self::BATCH);

        return $total;
    }

    /** Page views, clicks and identities go with their visit or visitor (cascading keys). */
    private function deleteInBatches(string $table, string $column, CarbonImmutable $cutoff): int
    {
        $total = 0;
        do {
            $ids = DB::table($table)->where($column, '<', $cutoff)->orderBy('id')->limit(self::BATCH)->pluck('id');
            if ($ids->isEmpty()) {
                break;
            }
            if ($table === 'tracking_sessions') {
                // Done explicitly as well as by the foreign keys, so the job is
                // correct even on a database created without them.
                DB::table('tracking_events')->whereIn('session_id', $ids)->delete();
                DB::table('tracking_page_views')->whereIn('session_id', $ids)->delete();
            }
            if ($table === 'tracking_visitors') {
                DB::table('tracking_identities')->whereIn('visitor_id', $ids)->delete();
                DB::table('tracking_sessions')->whereIn('visitor_id', $ids)->delete();
            }
            $total += DB::table($table)->whereIn('id', $ids)->delete();
        } while ($ids->count() === self::BATCH);

        return $total;
    }
}
