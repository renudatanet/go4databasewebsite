<?php

namespace App\Tracking\Contracts;

use App\Tracking\Ingestion\CollectPayload;
use App\Tracking\Ingestion\RequestContext;

/** One reason a hit may be refused (bot, excluded IP, ...). */
interface HitGuard
{
    public function allows(CollectPayload $payload, RequestContext $context): bool;
}
