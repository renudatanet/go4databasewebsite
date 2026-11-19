<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Language;
use App\SitePages\PageManager;
use App\SitePages\PageRegistry;
use App\SitePages\PageSeo;
use App\SitePages\SitePage;
use App\SitePages\SlugGuard;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Page Manage: every page of the site in one place. The overview lists the
 * pages; opening one goes to its first admin screen, where a tab bar
 * (partials/page-manage/tabs, drawn by the admin layout) leads to the rest.
 * The screens themselves are the existing ones, saving where they always
 * have. The only screen of its own here is a page's SEO tab: Address & SEO,
 * or for pages with fixed addresses, Meta title & description.
 */
class PageManageController extends Controller
{
    public function __construct(private PageManager $manager)
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $pages = array_map(fn (SitePage $page) => [
            'page' => $page,
            'url' => $this->manager->publicUrl($page),
            'edit_url' => $this->manager->firstTabUrl($page),
            'tabs' => $this->manager->tabs($page),
        ], $this->manager->pages());

        return view('backend.page-manage.index', [
            'pages' => $pages,
            'can_custom_pages' => $this->manager->can('Pages Manage'),
        ]);
    }

    /** Open a page: its first screen this admin may use. */
    public function show(string $page)
    {
        $site_page = $this->findOr404($page);
        $url = $this->manager->firstTabUrl($site_page);
        if ($url === null) {
            return redirect()->route('admin.page.manage')
                ->with(['msg' => __('Your role has no access to the screens for this page.'), 'type' => 'danger']);
        }

        return redirect()->to($url);
    }

    public function seo(string $page, PageSeo $seo)
    {
        $site_page = $this->seoPageOr404($page);
        $languages = Language::all();

        return view('backend.page-manage.seo', [
            'site_page' => $site_page,
            'public_url' => $this->manager->publicUrl($site_page),
            'slug' => $seo->slug($site_page),
            'values' => $seo->values($site_page, $languages->pluck('slug')),
            'languages' => $languages,
            'fields' => $seo->fields($site_page),
        ]);
    }

    public function update_seo(Request $request, string $page, PageSeo $seo, SlugGuard $guard)
    {
        $site_page = $this->seoPageOr404($page);
        $languages = Language::all()->pluck('slug');

        $limits = ['name' => 'max:191', 'meta_tags' => 'max:191', 'meta_description' => 'max:500', 'schema_code' => 'max:65000', 'meta_image' => 'max:191'];
        $rules = ['slug' => $site_page->slugEditable ? 'required|string|max:191' : 'nullable'];
        foreach ($languages as $lang) {
            foreach ($seo->fields($site_page) as $field) {
                $rules["seo.$lang.$field"] = 'nullable|string|' . $limits[$field];
            }
        }
        $this->validate($request, $rules);

        $slug = null;
        if ($site_page->slugEditable) {
            $slug = PageSeo::normalise((string) $request->input('slug'));
            $problem = $guard->problem($site_page, $slug);
            if ($problem !== null) {
                throw ValidationException::withMessages(['slug' => $problem]);
            }
        }

        $seo->save($site_page, $slug, (array) $request->input('seo', []), $languages);

        return redirect()->back()->with(['msg' => __(':page: address & SEO saved.', ['page' => $site_page->title]), 'type' => 'success']);
    }

    private function findOr404(string $key): SitePage
    {
        $page = $this->manager->registry()->find($key);
        abort_if($page === null, 404);

        return $page;
    }

    /** A page with an Address & SEO tab, for an admin allowed to use it. */
    private function seoPageOr404(string $key): SitePage
    {
        $page = $this->findOr404($key);
        abort_unless($page->hasSeo(), 404);
        abort_unless($this->manager->can(PageRegistry::SEO_PERMISSION), 403);

        return $page;
    }
}
