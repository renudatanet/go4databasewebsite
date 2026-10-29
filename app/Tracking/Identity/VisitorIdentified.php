<?php

namespace App\Tracking\Identity;

use Carbon\CarbonImmutable;

/**
 * Announced when a visitor tells the site who they are. Forms and the app
 * raise it; tracking listens. Nothing that raises it depends on how
 * tracking stores it.
 */
final class VisitorIdentified
{
    public function __construct(
        public string $visitorUuid,
        public string $source,
        public IdentityDetails $details,
        public CarbonImmutable $occurredAt,
    ) {
    }
}
