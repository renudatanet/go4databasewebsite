<?php

namespace App\Tracking\Ingestion\Enrichment;

use App\Tracking\Contracts\DeviceParser;
use Closure;

/** Device, browser, OS, screen and language. */
final class AddDevice implements EnrichmentStep
{
    public function __construct(private DeviceParser $parser)
    {
    }

    public function handle(SessionDraft $draft, Closure $next): SessionDraft
    {
        $payload = $draft->payload;
        $device = $this->parser->parse($draft->context->userAgent, $payload->touch);

        $draft->attributes += [
            'device_type' => $device->type,
            'browser' => $device->browser,
            'browser_version' => $device->browserVersion,
            'os' => $device->os,
            'os_version' => $device->osVersion,
            'screen_width' => $payload->screenWidth,
            'screen_height' => $payload->screenHeight,
            'language' => $payload->language,
        ];

        return $next($draft);
    }
}
