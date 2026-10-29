<?php

namespace App\Tracking\Console;

use App\Tracking\Maintenance\GeoDatabaseUpdater;
use Illuminate\Console\Command;

final class UpdateGeoDatabase extends Command
{
    protected $signature = 'tracking:geo-update';
    protected $description = 'Download the latest DB-IP location and network files';

    public function handle(GeoDatabaseUpdater $updater): int
    {
        $failed = false;
        foreach ($updater->update() as $kind => $result) {
            $this->line("{$kind}: {$result}");
            $failed = $failed || str_starts_with($result, 'failed');
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
