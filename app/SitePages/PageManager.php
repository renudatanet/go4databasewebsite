<?php

namespace App\SitePages;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Page Manage as the signed-in admin sees it: the pages they may edit, each
 * page's tabs (only the screens their role can open, and only screens this
 * install has), and which page the screen they are on belongs to.
 *
 * Tab links carry ?pm={page} so a screen shared by two pages (Roles is on
 * both Career and Working at Go4Database) shows the tab bar of the page the
 * admin came from. The choice is remembered for the session, so a screen
 * that redirects after saving keeps its tab bar too.
 */
final class PageManager
{
    /** The query parameter tab links carry. Not "page": list screens paginate with that. */
    public const QUERY = 'pm';

    private const SESSION = 'page_manage.current';

    /** @var callable(string): bool */
    private $can;

    /** @param callable(string): bool|null $can whether the signed-in admin holds a permission */
    public function __construct(private PageRegistry $registry, ?callable $can = null)
    {
        $this->can = $can ?? fn (string $permission) => check_page_permission_by_string($permission);
    }

    public function registry(): PageRegistry
    {
        return $this->registry;
    }

    /** Whether the signed-in admin holds $permission. */
    public function can(string $permission): bool
    {
        return (bool) ($this->can)($permission);
    }

    /**
     * The pages this site serves that the admin can edit at least a part of.
     *
     * @return list<SitePage>
     */
    public function pages(): array
    {
        return array_values(array_filter(
            $this->registry->all(),
            fn (SitePage $page) => Route::has($page->route) && $this->tabs($page) !== []
        ));
    }

    /**
     * A page's tabs the admin may open, with their links.
     *
     * @return list<array{tab: PageTab, label: string, url: string}>
     */
    public function tabs(SitePage $page): array
    {
        $tabs = [];
        foreach ($page->tabs as $tab) {
            if (Route::has($tab->route) && $this->can($tab->permission)) {
                $tabs[] = ['tab' => $tab, 'label' => __($tab->label), 'url' => $this->url($page, $tab)];
            }
        }

        return $tabs;
    }

    /** Where editing a page starts: its first tab the admin may open, or null when there is none. */
    public function firstTabUrl(SitePage $page): ?string
    {
        return $this->tabs($page)[0]['url'] ?? null;
    }

    public function url(SitePage $page, PageTab $tab): string
    {
        $url = route($tab->route, $tab->parameters);

        return $url . (str_contains($url, '?') ? '&' : '?') . self::QUERY . '=' . rawurlencode($page->key);
    }

    /** The page's public address, e.g. https://www.go4database.com/affiliate, or null when the site doesn't serve it. */
    public function publicUrl(SitePage $page): ?string
    {
        return Route::has($page->route) ? route($page->route) : null;
    }

    /**
     * The page the screen being shown belongs to, or null when it is not one
     * of a page's tabs. Asked for by ?pm first, then the page last chosen
     * this session, then the only page that has this screen.
     */
    public function current(Request $request): ?SitePage
    {
        $route = $request->route();
        $name = $route?->getName();
        if ($name === null) {
            return null;
        }
        $parameters = $route->parameters();

        $owners = $this->registry->owning($name, $parameters);
        if ($owners === []) {
            return null;
        }
        foreach ([(string) $request->query(self::QUERY, ''), $this->remembered($request)] as $key) {
            foreach ($owners as $page) {
                if ($page->key === $key) {
                    return $page;
                }
            }
        }

        return count($owners) === 1 ? $owners[0] : null;
    }

    /** Remember $page as the one being edited, for screens reached without ?pm. */
    public function remember(Request $request, SitePage $page): void
    {
        if ($request->hasSession()) {
            $request->session()->put(self::SESSION, $page->key);
        }
    }

    private function remembered(Request $request): string
    {
        return $request->hasSession() ? (string) $request->session()->get(self::SESSION, '') : '';
    }
}
