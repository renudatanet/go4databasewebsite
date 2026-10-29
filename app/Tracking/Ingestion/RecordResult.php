<?php

namespace App\Tracking\Ingestion;

/** How a hit was recorded: with the visitor cookie ("full") or without ("anonymous"). */
final class RecordResult
{
    public function __construct(public string $mode, public string $visitorUuid)
    {
    }

    public function isFull(): bool
    {
        return $this->mode === ConsentPolicy::FULL;
    }
}
