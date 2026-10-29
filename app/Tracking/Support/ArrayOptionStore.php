<?php

namespace App\Tracking\Support;

use App\Tracking\Contracts\OptionStore;

/** In-memory settings, for tests. */
final class ArrayOptionStore implements OptionStore
{
    /** @param array<string, string|null> $values */
    public function __construct(private array $values = [])
    {
    }

    public function get(string $key, ?string $default = null): ?string
    {
        return $this->values[$key] ?? $default;
    }

    public function set(string $key, ?string $value): void
    {
        $this->values[$key] = $value;
    }
}
