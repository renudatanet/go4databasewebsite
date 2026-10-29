<?php

namespace App\Tracking\Ingestion\Guards;

use App\Tracking\Contracts\HitGuard;
use App\Tracking\Ingestion\CollectPayload;
use App\Tracking\Ingestion\RequestContext;

/** The browser has not opted out (every browser that opens the Tracking screens has). */
final class NotOptedOutGuard implements HitGuard
{
    public function allows(CollectPayload $payload, RequestContext $context): bool
    {
        return !$context->optedOut;
    }
}
