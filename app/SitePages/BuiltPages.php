<?php

namespace App\SitePages;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/**
 * The site's pages that have their own design and their own admin screens,
 * rather than being a record in Pages (which /{slug} serves). Pages > All
 * Pages lists them so every page on the site can be found, and edited, from
 * one place; nothing here makes a page or an address, it only points at what
 * already exists.
 *
 * Each page is named by its public route and lists the admin screens that
 * edit it, each with the sidebar permission that gates it.
 */
final class BuiltPages
{
    /** The route that serves Pages records; anything else answering a slug is a built page. */
    public const CMS_ROUTE = 'frontend.dynamic.page';

    private const SEO = ['Page name, address & SEO', 'admin.general.page.settings', 'General Settings'];

    private const PAGES = [
        ['homepage', 'Home', 'Most of the homepage is fixed design; these parts come from the admin.', [
            ['Sections on or off', 'admin.homeone.section.manage', 'Home Page Manage'],
            ['Latest news', 'admin.homeone.latest.news', 'Home Page Manage'],
            ['Contact area', 'admin.homeone.contact.area', 'Home Page Manage'],
            ['Site title & SEO', 'admin.general.seo.settings', 'General Settings'],
        ]],
        ['frontend.about', 'About', '', [
            ['About section', 'admin.about.page.about', 'about_page_manage'],
            ['Global network', 'admin.about.global.network', 'about_page_manage'],
            ['Experience', 'admin.about.experience', 'about_page_manage'],
            ['Team members', 'admin.about.team.member', 'about_page_manage'],
            ['Sections on or off', 'admin.about.page.section.manage', 'about_page_manage'],
            self::SEO,
        ]],
        ['frontend.contact', 'Contact', '', [
            ['Contact info', 'admin.contact.info', 'Contact Page Manage'],
            ['Form area', 'admin.contact.page.form.area', 'Contact Page Manage'],
            ['Form fields', 'admin.form.builder.contact', 'Form Builder'],
            ['Map', 'admin.contact.page.map', 'Contact Page Manage'],
            self::SEO,
        ]],
        ['frontend.price.plan', 'Pricing', '', [
            ['Price plans', 'admin.price.plan', 'Price Plan'],
            ['Plan categories', 'admin.price.plan.category', 'Price Plan'],
            self::SEO,
        ]],
        ['frontend.faq', 'FAQ', '', [
            ['Questions', 'admin.faq', 'Faq'],
            ['Topics', 'admin.faq.category', 'Faq'],
            self::SEO,
        ]],
        ['frontend.blog', 'Blog', 'The listing, each post and the topic pages.', [
            ['Posts', 'admin.blog', 'Blogs Manage'],
            ['Page wording', 'admin.blog.page.settings', 'Blogs Manage'],
            ['Post page wording', 'admin.blog.single.settings', 'Blogs Manage'],
            ['Topics', 'admin.blog.category', 'Blogs Manage'],
            self::SEO,
        ]],
        ['frontend.author', 'Authors', '', [
            ['Authors', 'admin.author', 'Blogs Manage'],
            self::SEO,
        ]],
        ['frontend.b2c', 'Lists', 'The lists index and every list page.', [
            ['Lists', 'admin.list', 'Services'],
            ['Page wording', 'admin.list.page.settings', 'Services'],
            ['Industries', 'admin.list.category', 'Services'],
            self::SEO,
        ]],
        ['frontend.work', 'B2B', '', [
            ['B2B entries', 'admin.work', 'Case Study'],
            ['Categories', 'admin.work.category', 'Case Study'],
            ['Single page', 'admin.work.single.page.settings', 'Case Study'],
            self::SEO,
        ]],
        ['frontend.case-study', 'Case Studies', '', [
            ['Case studies', 'admin.case-study', 'Case Study'],
            ['Categories', 'admin.case-study.category', 'Case Study'],
            ['Single page', 'admin.case-study.single.page.settings', 'Case Study'],
            self::SEO,
        ]],
        ['frontend.jobs', 'Career', 'The career page, each team, each role and the application form.', [
            ['Roles', 'admin.jobs.all', 'Job Post Manage'],
            ['Page wording', 'admin.jobs.page.settings', 'Job Post Manage'],
            ['Role page labels', 'admin.jobs.single.page.settings', 'Job Post Manage'],
            ['Teams', 'admin.jobs.category.all', 'Job Post Manage'],
            ['Applicants', 'admin.jobs.applicant', 'Job Post Manage'],
            ['Form fields', 'admin.form.builder.apply.job.form', 'Form Builder'],
            self::SEO,
        ]],
        ['frontend.jobs.working', 'Working at Go4Database', 'About the company: what we do, how we work, quotes from the team, offices and teams.', [
            ['Page content, quotes & offices', 'admin.jobs.working.page', 'Job Post Manage'],
            ['Roles', 'admin.jobs.all', 'Job Post Manage'],
            ['Teams', 'admin.jobs.category.all', 'Job Post Manage'],
        ]],
        ['frontend.email.verifier', 'Email Verifier', '', [
            ['Page content', 'admin.email.verifier.content', 'General Settings'],
            ['Checker settings', 'admin.email.verifier.settings', 'General Settings'],
        ]],
        ['frontend.email.finder', 'Email Finder', '', [
            ['Page content', 'admin.email.finder.content', 'General Settings'],
            ['Finder settings', 'admin.email.finder.settings', 'General Settings'],
        ]],
        ['frontend.affiliate', 'Affiliate Program', '', [
            ['Page content', 'admin.affiliate.content', 'General Settings'],
            ['Programme terms', 'admin.affiliate.settings', 'General Settings'],
            ['Applications', 'admin.affiliate.applications', 'General Settings'],
        ]],
    ];

    /** @var callable(string): bool */
    private $can;

    /** @param callable(string): bool|null $can whether the signed-in admin holds a permission */
    public function __construct(private Router $router, ?callable $can = null)
    {
        $this->can = $can ?? fn (string $permission) => check_page_permission_by_string($permission);
    }

    /**
     * The built pages this site serves, each with its address and the admin
     * screens this admin may open (a page they can't edit shows no buttons).
     *
     * @return list<array{route: string, title: string, note: string, url: string, path: string, links: list<array{label: string, url: string}>}>
     */
    public function forAdmin(): array
    {
        $pages = [];
        foreach (self::PAGES as [$route, $title, $note, $links]) {
            if (!Route::has($route)) {
                continue;
            }
            $url = route($route);
            $pages[] = [
                'route' => $route,
                'title' => $title,
                'note' => $note,
                'url' => $url,
                'path' => '/' . ltrim((string) parse_url($url, PHP_URL_PATH), '/'),
                'links' => $this->linksFor($links),
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
        foreach (self::PAGES as [$route, $title, , $links]) {
            if ($route === $name) {
                return ['title' => $title, 'url' => url('/' . ltrim($slug, '/')), 'links' => $this->linksFor($links)];
            }
        }

        return ['title' => __('another page of the site'), 'url' => url('/' . ltrim($slug, '/')), 'links' => []];
    }

    /** @return list<array{label: string, url: string}> */
    private function linksFor(array $links): array
    {
        $out = [];
        foreach ($links as [$label, $route, $permission]) {
            if (Route::has($route) && ($this->can)($permission)) {
                $out[] = ['label' => __($label), 'url' => route($route)];
            }
        }

        return $out;
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
