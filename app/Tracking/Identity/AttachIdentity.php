<?php

namespace App\Tracking\Identity;

use Throwable;

/**
 * Listener for VisitorIdentified. Runs inside the form's request, so it
 * must never make that request fail: errors are reported and swallowed.
 */
final class AttachIdentity
{
    public function __construct(private IdentityRecorder $recorder)
    {
    }

    public function handle(VisitorIdentified $event): void
    {
        try {
            $this->recorder->record($event);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
