<?php

namespace App\Tracking\Contracts;

/** Decides whether a request comes from a crawler, monitor or automated browser. */
interface BotDetector
{
    public function isBot(string $userAgent): bool;
}
