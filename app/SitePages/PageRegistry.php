<?php

namespace App\SitePages;

/**
 * Every page of the site that Page Manage edits, and the admin screens that
 * edit each one. This is the one list: the Page Manage sidebar, its overview,
 * the tab bar on each screen and Pages > All Pages all read it.
 *
 * Nothing here makes a page, an address or a screen; it only points at what
 * already exists. To add a page to Page Manage, add an entry to pages().
 */
final class PageRegistry
{
    /** The permission General Settings > Page Settings is guarded by; the Address & SEO tab writes the same keys. */
    public const SEO_PERMISSION = 'General Settings';

    /** The route of a page's Address & SEO tab (PageManageController@seo). */
    public const SEO_ROUTE = 'admin.page.manage.seo';

    /** @var array<string, SitePage>|null */
    private ?array $pages = null;

    /** @return list<SitePage> in sidebar order */
    public function all(): array
    {
        return array_values($this->pages());
    }

    public function find(string $key): ?SitePage
    {
        return $this->pages()[$key] ?? null;
    }

    /**
     * The pages one of whose tabs is the screen at $route.
     *
     * @return list<SitePage>
     */
    public function owning(string $route, array $parameters = []): array
    {
        return array_values(array_filter(
            $this->pages(),
            fn (SitePage $page) => $page->tabFor($route, $parameters) !== null
        ));
    }

    /** @return array<string, SitePage> */
    private function pages(): array
    {
        if ($this->pages !== null) {
            return $this->pages;
        }

        $this->pages = [];
        foreach (self::definitions() as $key => $page) {
            $tabs = [];
            foreach ($page['tabs'] as [$label, $route, $permission]) {
                $tabs[] = new PageTab($label, $route, $permission);
            }
            $seo = $page['seo'] ?? null;
            if ($seo !== null) {
                $tabs[] = new PageTab('Address & SEO', self::SEO_ROUTE, self::SEO_PERMISSION, ['page' => $key]);
            }
            $this->pages[$key] = new SitePage(
                $key,
                $page['title'],
                $page['route'],
                $page['note'] ?? '',
                $tabs,
                $seo,
                $page['slug'] ?? '',
                $page['slug_editable'] ?? true,
            );
        }

        return $this->pages;
    }

    /**
     * key => title, public route, a note, its tabs as [label, admin route,
     * permission], and for pages whose address and SEO live in Page Settings,
     * the key they are stored under ('seo') with the address the site falls
     * back to ('slug').
     */
    private static function definitions(): array
    {
        return [
            'home' => [
                'title' => 'Home', 'route' => 'homepage',
                'note' => 'Most of the homepage is fixed design; these parts come from the admin.',
                'tabs' => [
                    ['Sections on or off', 'admin.homeone.section.manage', 'Home Page Manage'],
                    ['Latest news', 'admin.homeone.latest.news', 'Home Page Manage'],
                    ['Contact area', 'admin.homeone.contact.area', 'Home Page Manage'],
                    ['Site title & SEO', 'admin.general.seo.settings', 'General Settings'],
                ],
            ],
            'about' => [
                'title' => 'About', 'route' => 'frontend.about', 'seo' => 'about', 'slug' => 'about',
                'tabs' => [
                    ['About section', 'admin.about.page.about', 'about_page_manage'],
                    ['Global network', 'admin.about.global.network', 'about_page_manage'],
                    ['Experience', 'admin.about.experience', 'about_page_manage'],
                    ['Team members', 'admin.about.team.member', 'about_page_manage'],
                    ['Sections on or off', 'admin.about.page.section.manage', 'about_page_manage'],
                ],
            ],
            'contact' => [
                'title' => 'Contact', 'route' => 'frontend.contact', 'seo' => 'contact', 'slug' => 'contact',
                'tabs' => [
                    ['Contact info', 'admin.contact.info', 'Contact Page Manage'],
                    ['Form area', 'admin.contact.page.form.area', 'Contact Page Manage'],
                    ['Form fields', 'admin.form.builder.contact', 'Form Builder'],
                    ['Map', 'admin.contact.page.map', 'Contact Page Manage'],
                ],
            ],
            'pricing' => [
                'title' => 'Pricing', 'route' => 'frontend.price.plan', 'seo' => 'price_plan', 'slug' => 'price-plan',
                'tabs' => [
                    ['Price plans', 'admin.price.plan', 'Price Plan'],
                    ['Plan categories', 'admin.price.plan.category', 'Price Plan'],
                ],
            ],
            'faq' => [
                'title' => 'FAQ', 'route' => 'frontend.faq', 'seo' => 'faq', 'slug' => 'faq',
                'tabs' => [
                    ['Questions', 'admin.faq', 'Faq'],
                    ['Topics', 'admin.faq.category', 'Faq'],
                ],
            ],
            'blog' => [
                'title' => 'Blog', 'route' => 'frontend.blog', 'seo' => 'blog', 'slug' => 'blog',
                'note' => 'The listing, each post and the topic pages.',
                'tabs' => [
                    ['Posts', 'admin.blog', 'Blogs Manage'],
                    ['Page wording', 'admin.blog.page.settings', 'Blogs Manage'],
                    ['Post page wording', 'admin.blog.single.settings', 'Blogs Manage'],
                    ['Topics', 'admin.blog.category', 'Blogs Manage'],
                ],
            ],
            'authors' => [
                'title' => 'Authors', 'route' => 'frontend.author', 'seo' => 'author', 'slug' => 'author', 'slug_editable' => false,
                'tabs' => [
                    ['Authors', 'admin.author', 'Blogs Manage'],
                ],
            ],
            'lists' => [
                'title' => 'Lists', 'route' => 'frontend.b2c', 'seo' => 'list', 'slug' => 'list',
                'note' => 'The lists index and every list page.',
                'tabs' => [
                    ['Lists', 'admin.list', 'Services'],
                    ['Page wording', 'admin.list.page.settings', 'Services'],
                    ['Industries', 'admin.list.category', 'Services'],
                ],
            ],
            'b2b' => [
                'title' => 'B2B', 'route' => 'frontend.work', 'seo' => 'work', 'slug' => 'work',
                'tabs' => [
                    ['B2B entries', 'admin.work', 'Case Study'],
                    ['Categories', 'admin.work.category', 'Case Study'],
                    ['Single page', 'admin.work.single.page.settings', 'Case Study'],
                ],
            ],
            'b2c' => [
                'title' => 'B2C', 'route' => 'frontend.service', 'seo' => 'service', 'slug' => 'service',
                'tabs' => [
                    ['B2C entries', 'admin.services', 'Services'],
                    ['Categories', 'admin.service.category', 'Services'],
                    ['Page settings', 'admin.services.page.settings', 'Services'],
                ],
            ],
            'case-studies' => [
                'title' => 'Case Studies', 'route' => 'frontend.case-study', 'seo' => 'case_study', 'slug' => 'case-study',
                'tabs' => [
                    ['Case studies', 'admin.case-study', 'Case Study'],
                    ['Categories', 'admin.case-study.category', 'Case Study'],
                    ['Single page', 'admin.case-study.single.page.settings', 'Case Study'],
                ],
            ],
            'career' => [
                'title' => 'Career', 'route' => 'frontend.jobs', 'seo' => 'career_with_us', 'slug' => 'jobs',
                'note' => 'The career page, each team, each role and the application form.',
                'tabs' => [
                    ['Roles', 'admin.jobs.all', 'Job Post Manage'],
                    ['Page wording', 'admin.jobs.page.settings', 'Job Post Manage'],
                    ['Role page labels', 'admin.jobs.single.page.settings', 'Job Post Manage'],
                    ['Teams', 'admin.jobs.category.all', 'Job Post Manage'],
                    ['Applicants', 'admin.jobs.applicant', 'Job Post Manage'],
                    ['Form fields', 'admin.form.builder.apply.job.form', 'Form Builder'],
                ],
            ],
            'working' => [
                'title' => 'Working at Go4Database', 'route' => 'frontend.jobs.working',
                'note' => 'About the company: what we do, how we work, quotes from the team, offices and teams.',
                'tabs' => [
                    ['Page content, quotes & offices', 'admin.jobs.working.page', 'Job Post Manage'],
                    ['Roles', 'admin.jobs.all', 'Job Post Manage'],
                    ['Teams', 'admin.jobs.category.all', 'Job Post Manage'],
                ],
            ],
            'email-verifier' => [
                'title' => 'Email Verifier', 'route' => 'frontend.email.verifier',
                'tabs' => [
                    ['Page content & SEO', 'admin.email.verifier.content', 'General Settings'],
                    ['Checker settings', 'admin.email.verifier.settings', 'General Settings'],
                ],
            ],
            'email-finder' => [
                'title' => 'Email Finder', 'route' => 'frontend.email.finder',
                'tabs' => [
                    ['Page content & SEO', 'admin.email.finder.content', 'General Settings'],
                    ['Finder settings', 'admin.email.finder.settings', 'General Settings'],
                ],
            ],
            'affiliate' => [
                'title' => 'Affiliate Program', 'route' => 'frontend.affiliate',
                'tabs' => [
                    ['Page content & SEO', 'admin.affiliate.content', 'General Settings'],
                    ['Terms & sections', 'admin.affiliate.settings', 'General Settings'],
                    ['Applications', 'admin.affiliate.applications', 'General Settings'],
                ],
            ],
        ];
    }
}
