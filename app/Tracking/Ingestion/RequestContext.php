<?php

namespace App\Tracking\Ingestion;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

/** What the server itself knows about a collect request, independent of its body. */
final class RequestContext
{
    public function __construct(
        public string $ip,
        public string $userAgent,
        public string $host,
        public ?string $originHost,
        public bool $optedOut,
        public CarbonImmutable $receivedAt,
    ) {
    }

    public static function fromRequest(Request $request, string $optOutCookie): self
    {
        $origin = $request->headers->get('Origin');

        return new self(
            ip: (string) $request->ip(),
            userAgent: mb_substr((string) $request->userAgent(), 0, 500),
            host: strtolower($request->getHost()),
            originHost: $origin ? strtolower((string) parse_url($origin, PHP_URL_HOST)) : null,
            optedOut: $request->cookies->has($optOutCookie),
            receivedAt: CarbonImmutable::now('UTC'),
        );
    }
}
