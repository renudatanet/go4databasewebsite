<?php

namespace App\Tracking\Console;

use App\Tracking\Maintenance\DataRetention;
use Illuminate\Console\Command;

final class PruneTrackingData extends Command
{
    protected $signature = 'tracking:prune';
    protected $description = 'Mask old IP addresses and delete tracking data past its retention period';

    public function handle(DataRetention $retention): int
    {
        $result = $retention->run();
        $this->info(sprintf(
            'Masked %d IP addresses; deleted %d visits and %d visitors.',
            $result['ips_masked'],
            $result['sessions_deleted'],
            $result['visitors_deleted']
        ));

        return self::SUCCESS;
    }
}
