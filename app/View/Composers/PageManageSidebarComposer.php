<?php

namespace App\View\Composers;

use App\SitePages\PageManager;
use App\SitePages\SitePage;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The Page Manage group in the admin sidebar: the overview, then every page
 * the admin can edit, the one being edited marked active.
 */
final class PageManageSidebarComposer
{
    public function __construct(private PageManager $manager, private Request $request)
    {
    }

    public function compose(View $view): void
    {
        $current = $this->manager->current($this->request);
        $on_overview = $this->request->routeIs('admin.page.manage');

        $view->with([
            'pm_overview_active' => $on_overview,
            'pm_group_active' => $on_overview || $current !== null,
            'pm_links' => array_map(fn (SitePage $page) => [
                'title' => __($page->title),
                'url' => route('admin.page.manage.show', $page->key),
                'active' => $current?->key === $page->key,
            ], $this->manager->pages()),
        ]);
    }
}
