<?php

namespace App\Tracking\Ingestion\Guards;

use App\Tracking\Contracts\HitGuard;
use App\Tracking\Ingestion\CollectPayload;
use App\Tracking\Ingestion\RequestContext;
use App\Tracking\Support\TrackingSettings;

/** Tracking is switched on in Settings. */
final class TrackingEnabledGuard implements HitGuard
{
    public function __construct(private TrackingSettings $settings)
    {
    }

    public function allows(CollectPayload $payload, RequestContext $context): bool
    {
        return $this->settings->enabled();
    }
}
