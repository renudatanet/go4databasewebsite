<?php

namespace App\SitePages;

/**
 * One thing an admin can edit on a page: an existing admin screen, named by
 * its route, and the sidebar permission that already guards that screen.
 */
final class PageTab
{
    /**
     * @param array<string, scalar> $parameters the route's parameters, if it takes any
     */
    public function __construct(
        public readonly string $label,
        public readonly string $route,
        public readonly string $permission,
        public readonly array $parameters = [],
    ) {
    }

    /** Whether this tab is the screen at $route with $parameters (extra parameters are ignored). */
    public function is(string $route, array $parameters = []): bool
    {
        if ($route !== $this->route) {
            return false;
        }
        foreach ($this->parameters as $name => $value) {
            if ((string) ($parameters[$name] ?? '') !== (string) $value) {
                return false;
            }
        }

        return true;
    }
}
