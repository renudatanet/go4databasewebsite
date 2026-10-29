<?php

namespace App\Tracking\Ingestion\Guards;

use App\Tracking\Contracts\HitGuard;
use App\Tracking\Ingestion\CollectPayload;
use App\Tracking\Ingestion\RequestContext;

/**
 * The page is on this site. A copy of a page saved elsewhere, or a request
 * forged from another site, reports a different host and is refused.
 */
final class SameSiteGuard implements HitGuard
{
    public function allows(CollectPayload $payload, RequestContext $context): bool
    {
        $pageHost = strtolower((string) parse_url($payload->url, PHP_URL_HOST));
        if ($pageHost !== $context->host) {
            return false;
        }

        return $context->originHost === null || $context->originHost === $context->host;
    }
}
