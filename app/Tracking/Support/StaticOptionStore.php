<?php

namespace App\Tracking\Support;

use App\Tracking\Contracts\OptionStore;

/** Settings in the site's `static_options` table, through its helpers. */
final class StaticOptionStore implements OptionStore
{
    public function get(string $key, ?string $default = null): ?string
    {
        $value = get_static_option($key, $default);

        return $value === null ? null : (string) $value;
    }

    public function set(string $key, ?string $value): void
    {
        update_static_option($key, $value);
    }
}
