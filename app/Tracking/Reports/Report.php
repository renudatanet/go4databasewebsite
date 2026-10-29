<?php

namespace App\Tracking\Reports;

/**
 * One question a screen asks of the data ("top pages", "visits per day").
 * Each lives in its own class so its SQL can be read and tested alone, and
 * is run through ReportRunner, which caches the answer.
 */
interface Report
{
    /** Identifies this report and its own parameters (not the context) in the cache. */
    public function key(): string;

    public function run(ReportContext $context): mixed;
}
