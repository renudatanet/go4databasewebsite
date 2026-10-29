<?php

namespace App\Tracking\Ingestion\Guards;

use App\Tracking\Contracts\HitGuard;
use App\Tracking\Ingestion\CollectPayload;
use App\Tracking\Ingestion\RequestContext;
use App\Tracking\Support\IpAddress;
use App\Tracking\Support\TrackingSettings;

/** The IP is not on the excluded list (office, team, monitoring). */
final class ExcludedIpGuard implements HitGuard
{
    public function __construct(private TrackingSettings $settings)
    {
    }

    public function allows(CollectPayload $payload, RequestContext $context): bool
    {
        return !IpAddress::matchesAny($context->ip, $this->settings->excludedIps());
    }
}
