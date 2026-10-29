<?php

namespace App\Tracking\Contracts;

/**
 * Key/value settings storage. The site keeps settings in `static_options`;
 * tests use an in-memory store.
 */
interface OptionStore
{
    public function get(string $key, ?string $default = null): ?string;

    public function set(string $key, ?string $value): void;
}
