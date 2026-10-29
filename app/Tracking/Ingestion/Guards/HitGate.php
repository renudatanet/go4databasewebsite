<?php

namespace App\Tracking\Ingestion\Guards;

use App\Tracking\Contracts\HitGuard;
use App\Tracking\Ingestion\CollectPayload;
use App\Tracking\Ingestion\RequestContext;

/** Every guard must allow a hit for it to be recorded. */
final class HitGate
{
    /** @var list<HitGuard> */
    private array $guards;

    public function __construct(HitGuard ...$guards)
    {
        $this->guards = $guards;
    }

    public function allows(CollectPayload $payload, RequestContext $context): bool
    {
        foreach ($this->guards as $guard) {
            if (!$guard->allows($payload, $context)) {
                return false;
            }
        }

        return true;
    }
}
