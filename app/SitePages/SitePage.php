<?php

namespace App\SitePages;

/**
 * A page of the site as Page Manage shows it: its public route, and the
 * admin screens (tabs) that edit it.
 *
 * $seoKey is the name the page's address, name and meta tags are stored
 * under ("{seoKey}_page_slug", "{seoKey}_page_{lang}_meta_tags", ...), the
 * keys General Settings > Page Settings has always written. Pages that keep
 * their SEO in their own content screen (Affiliate, Email Verifier...) have
 * none, and so no Address & SEO tab. $slugEditable is false where the
 * site's routes ignore the stored address (Authors is always /author).
 */
final class SitePage
{
    /**
     * @param list<PageTab> $tabs
     */
    public function __construct(
        public readonly string $key,
        public readonly string $title,
        public readonly string $route,
        public readonly string $note,
        public readonly array $tabs,
        public readonly ?string $seoKey = null,
        public readonly string $defaultSlug = '',
        public readonly bool $slugEditable = true,
    ) {
    }

    public function hasSeo(): bool
    {
        return $this->seoKey !== null;
    }

    /** The tab that is the screen at $route with $parameters, or null. */
    public function tabFor(string $route, array $parameters = []): ?PageTab
    {
        foreach ($this->tabs as $tab) {
            if ($tab->is($route, $parameters)) {
                return $tab;
            }
        }

        return null;
    }
}
