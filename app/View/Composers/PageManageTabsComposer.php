<?php

namespace App\View\Composers;

use App\SitePages\PageManager;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The tab bar the admin layout draws above any screen that edits one of the
 * site's pages: the page's name and address, and a tab for each of its
 * screens. On screens that belong to no page it draws nothing.
 */
final class PageManageTabsComposer
{
    public function __construct(private PageManager $manager, private Request $request)
    {
    }

    public function compose(View $view): void
    {
        $page = $this->manager->current($this->request);
        if ($page === null) {
            $view->with('pm_page', null);

            return;
        }
        $this->manager->remember($this->request, $page);

        $route = $this->request->route();
        $name = (string) $route?->getName();
        $parameters = $route?->parameters() ?? [];

        $view->with([
            'pm_page' => $page,
            'pm_url' => $this->manager->publicUrl($page),
            'pm_tabs' => array_map(fn (array $tab) => $tab + ['active' => $tab['tab']->is($name, $parameters)], $this->manager->tabs($page)),
        ]);
    }
}
