<?php

namespace App\SitePages;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;

/**
 * The site's pages that have their own design and their own admin screens,
 * rather than being a record in Pages (which /{slug} serves), as Pages > All
 * Pages lists them. The pages and their screens come from PageRegistry, the
 * list Page Manage uses too; nothing here makes a page or an address.
 */
final class BuiltPages
{
    /** The route that serves Pages records; anything else answering a slug is a built page. */
    public const CMS_ROUTE = 'frontend.dynamic.page';

    public function __construct(private Router $router, private PageManager $manager)
    {
    }

    /**
     * The built pages this site serves, each with its address and the admin
     * screens this admin may open (a page they can't edit shows no buttons).
     *
     * @return list<array{route: string, key: string, title: string, note: string, url: string, path: string, edit_url: ?string, links: list<array{label: string, url: string}>}>
     */
    public function forAdmin(): array
    {
        $pages = [];
        foreach ($this->manager->registry()->all() as $page) {
            $url = $this->manager->publicUrl($page);
            if ($url === null) {
                continue;
            }
            $pages[] = [
                'route' => $page->route,
                'key' => $page->key,
                'title' => $page->title,
                'note' => $page->note,
                'url' => $url,
                'path' => '/' . ltrim((string) parse_url($url, PHP_URL_PATH), '/'),
                'edit_url' => $this->manager->firstTabUrl($page),
                'links' => $this->linksFor($page),
            ];
        }

        return $pages;
    }

    /**
     * The built page that answers /{$slug} instead of a Pages record with that
     * slug, or null when the record is what the site shows. A record hidden
     * like this can be edited, but no visitor ever sees the change.
     *
     * @return array{title: string, url: string, links: list<array{label: string, url: string}>}|null
     */
    public function hiding(string $slug): ?array
    {
        $name = $this->routeAnswering('/' . ltrim($slug, '/'));
        if ($name === null || $name === self::CMS_ROUTE) {
            return null;
        }
        foreach ($this->manager->registry()->all() as $page) {
            if ($page->route === $name) {
                return ['title' => $page->title, 'url' => url('/' . ltrim($slug, '/')), 'links' => $this->linksFor($page)];
            }
        }

        return ['title' => __('another page of the site'), 'url' => url('/' . ltrim($slug, '/')), 'links' => []];
    }

    /** @return list<array{label: string, url: string}> */
    private function linksFor(SitePage $page): array
    {
        return array_map(
            fn (array $tab) => ['label' => $tab['label'], 'url' => $tab['url']],
            $this->manager->tabs($page)
        );
    }

    private function routeAnswering(string $path): ?string
    {
        try {
            return $this->router->getRoutes()->match(Request::create($path, 'GET'))->getName();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
