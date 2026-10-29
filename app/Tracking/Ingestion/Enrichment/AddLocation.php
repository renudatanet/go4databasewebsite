<?php

namespace App\Tracking\Ingestion\Enrichment;

use App\Tracking\Support\IpAddress;
use Closure;

/**
 * Location and network from the IP. Anonymous visits never keep the full
 * IP; the rest keep it until the retention job masks it.
 */
final class AddLocation implements EnrichmentStep
{
    public function handle(SessionDraft $draft, Closure $next): SessionDraft
    {
        $geo = $draft->geo;
        $ip = $draft->context->ip;

        $draft->attributes += [
            'ip' => $draft->isAnonymous() ? null : $ip,
            'ip_masked' => IpAddress::mask($ip),
            'country_code' => $geo->countryCode,
            'country' => $geo->country,
            'region' => $geo->region,
            'city' => $geo->city,
            'latitude' => $geo->latitude,
            'longitude' => $geo->longitude,
            'asn' => $geo->asn,
            'network_org' => $geo->networkOrg,
            'network_type' => $geo->networkType,
        ];

        return $next($draft);
    }
}
