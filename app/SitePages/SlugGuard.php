<?php

namespace App\SitePages;

use App\Page;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;

/**
 * Whether a page may move to a new address. It may not take one that is
 * already the address of another of the site's pages (the earlier route
 * would win and the page would vanish), nor one a custom page (a Pages
 * record) already uses, which it would hide.
 */
final class SlugGuard
{
    public function __construct(private Router $router, private PageRegistry $registry, private PageSeo $seo)
    {
    }

    /**
     * Why $page can't have the address $slug, or null when it can.
     * $slug is expected normalised (PageSeo::normalise).
     */
    public function problem(SitePage $page, string $slug): ?string
    {
        if ($slug === '') {
            return __('Enter an address, for example "pricing".');
        }
        if ($slug === $this->seo->slug($page)) {
            return null; // unchanged: saving the page's SEO must never be blocked by its own address
        }

        foreach ($this->registry->all() as $other) {
            if ($other->key !== $page->key && $other->hasSeo() && $this->seo->slug($other) === $slug) {
                return __('/:slug is already the address of the :page page.', ['slug' => $slug, 'page' => $other->title]);
            }
        }

        $answering = $this->routeAnswering('/' . $slug);
        if ($answering !== null && !in_array($answering->getName(), [$page->route, BuiltPages::CMS_ROUTE], true)) {
            return __('/:slug is already used by another part of the site.', ['slug' => $slug]);
        }

        if (Page::where('slug', $slug)->exists()) {
            return __('/:slug is the address of a custom page in Pages; give one of them another address first.', ['slug' => $slug]);
        }

        return null;
    }

    /** The route that serves $path now (named or not), or null when nothing does. */
    private function routeAnswering(string $path): ?Route
    {
        try {
            return $this->router->getRoutes()->match(Request::create($path, 'GET'));
        } catch (\Throwable $e) {
            return null;
        }
    }
}
