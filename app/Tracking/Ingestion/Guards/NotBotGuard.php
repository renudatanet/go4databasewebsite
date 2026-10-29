<?php

namespace App\Tracking\Ingestion\Guards;

use App\Tracking\Contracts\BotDetector;
use App\Tracking\Contracts\HitGuard;
use App\Tracking\Ingestion\CollectPayload;
use App\Tracking\Ingestion\RequestContext;

/** Not a crawler, monitor or automated browser. */
final class NotBotGuard implements HitGuard
{
    public function __construct(private BotDetector $detector)
    {
    }

    public function allows(CollectPayload $payload, RequestContext $context): bool
    {
        return !$payload->automated && !$this->detector->isBot($context->userAgent);
    }
}
