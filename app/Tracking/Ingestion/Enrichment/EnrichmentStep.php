<?php

namespace App\Tracking\Ingestion\Enrichment;

use Closure;

/** One step of the new-visit pipeline. Fills its part of the draft, then passes it on. */
interface EnrichmentStep
{
    public function handle(SessionDraft $draft, Closure $next): SessionDraft;
}
