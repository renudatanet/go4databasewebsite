<?php

namespace App\Tracking\Ingestion\Enrichment;

use App\Tracking\Data\GeoResult;
use App\Tracking\Ingestion\CollectPayload;
use App\Tracking\Ingestion\ConsentPolicy;
use App\Tracking\Ingestion\RequestContext;

/**
 * A visit about to be stored. The enrichment steps each fill in their part
 * of $attributes (location, device, source), which become the session row.
 */
final class SessionDraft
{
    /** @var array<string, mixed> */
    public array $attributes = [];

    public function __construct(
        public CollectPayload $payload,
        public RequestContext $context,
        public GeoResult $geo,
        public string $mode,
    ) {
    }

    public function isAnonymous(): bool
    {
        return $this->mode === ConsentPolicy::ANONYMOUS;
    }
}
