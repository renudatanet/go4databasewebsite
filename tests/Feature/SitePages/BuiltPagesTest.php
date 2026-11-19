<?php

namespace Tests\Feature\SitePages;

use App\SitePages\BuiltPages;
use App\SitePages\PageManager;
use App\SitePages\PageRegistry;
use Tests\TestCase;

/**
 * Pages > All Pages' list of built pages. Each test runs in its own process:
 * routes/web.php loads the admin routes with require_once, so a second app in
 * one process would have none, and these tests check every one of them.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class BuiltPagesTest extends TestCase
{
    private function pages(?callable $can = null): BuiltPages
    {
        return new BuiltPages(app('router'), new PageManager(new PageRegistry(), $can ?? fn () => true));
    }

    public function test_every_page_and_screen_it_names_exists(): void
    {
        $listed = collect($this->pages()->forAdmin())->keyBy('route');

        foreach ((new PageRegistry())->all() as $page) {
            $this->assertTrue($listed->has($page->route), "{$page->title}: route {$page->route} is missing");
            $this->assertCount(count($page->tabs), $listed[$page->route]['links'], "{$page->title}: an admin screen it names is missing");
        }
    }

    public function test_pages_come_with_their_address(): void
    {
        $verifier = collect($this->pages()->forAdmin())->firstWhere('route', 'frontend.email.verifier');

        $this->assertSame('/email-verifier', $verifier['path']);
        $this->assertSame(url('/email-verifier'), $verifier['url']);
        $this->assertSame(['Page content', 'Checker settings', 'Meta title & description'], array_column($verifier['links'], 'label'));
        $this->assertSame(route('admin.email.verifier.content') . '?pm=email-verifier', $verifier['edit_url']);
    }

    public function test_only_screens_the_admin_may_open_are_offered(): void
    {
        $pages = collect($this->pages(fn (string $permission) => $permission === 'Job Post Manage')->forAdmin())->keyBy('route');

        $this->assertSame(['Roles', 'Page wording', 'Role page labels', 'Teams', 'Applicants'], array_column($pages['frontend.jobs']['links'], 'label'));
        $this->assertSame([], $pages['frontend.email.verifier']['links']);
        $this->assertSame([], $pages['frontend.blog']['links']);
    }

    public function test_a_record_hidden_behind_a_built_page_is_spotted(): void
    {
        $hidden = $this->pages()->hiding('email-verifier');

        $this->assertSame('Email Verifier', $hidden['title']);
        $this->assertSame(url('/email-verifier'), $hidden['url']);
        $this->assertSame(route('admin.email.verifier.content') . '?pm=email-verifier', $hidden['links'][0]['url']);
    }

    public function test_records_the_site_shows_are_not_flagged(): void
    {
        foreach (['privacy-policy', 'terms-of-services', 'database-affiliate', 'our-testimonial', 'a-page-nobody-made'] as $slug) {
            $this->assertNull($this->pages()->hiding($slug), "/$slug is a Pages record the site shows");
        }
    }
}
