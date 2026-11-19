<?php

namespace Tests\Feature\SitePages;

use App\Admin;
use App\SitePages\PageManager;
use App\SitePages\PageRegistry;
use App\SitePages\PageSeo;
use App\SitePages\SlugGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Page Manage: every page of the site in one place, each with a tab bar over
 * its existing admin screens, and a per-page Address & SEO tab. Each test
 * runs in its own process: routes/web.php loads the admin routes with
 * require_once, so a second app in one process would have none.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PageManageTest extends TestCase
{
    private const MIGRATIONS = [
        'database/migrations/2019_11_06_180949_create_admins_table.php',
        'database/migrations/2019_12_07_082524_create_static_options_table.php',
        'database/migrations/2019_12_13_221931_create_languages_table.php',
        'database/migrations/2020_01_25_155448_create_pages_table.php',
        'database/migrations/2020_03_29_075123_create_admin_roles_table.php',
        'database/migrations/2020_04_14_082508_create_media_uploads_table.php',
    ];

    private const ALL = ['general_settings', 'job_post_manage', 'pages_manage', 'blogs_manage', 'faq', 'price_plan',
        'services', 'case_study', 'home_page_manage', 'about_page_manage', 'contact_page_manage', 'form_builder'];

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
        $GLOBALS['__g4_static_options'] = [];
        $this->artisan('migrate:fresh', ['--path' => self::MIGRATIONS, '--force' => true]);
        DB::table('languages')->insert(['name' => 'English (USA)', 'slug' => 'en_US', 'default' => 1, 'status' => 'publish']);
    }

    protected function tearDown(): void
    {
        $GLOBALS['__g4_static_options'] = [];
        parent::tearDown();
    }

    public function test_the_overview_lists_every_page_the_admin_can_edit(): void
    {
        $html = $this->actingAs($this->admin(), 'admin')->get(route('admin.page.manage'))->assertOk()->getContent();

        foreach (['Home', 'About', 'Contact', 'Pricing', 'FAQ', 'Blog', 'Lists', 'B2B', 'B2C', 'Case Studies', 'Career', 'Working at Go4Database', 'Email Verifier', 'Email Finder', 'Affiliate Program', 'Custom pages'] as $title) {
            $this->assertStringContainsString('<h3>' . $title . '</h3>', $html, "$title is missing from the overview");
        }
        $this->assertStringContainsString(route('admin.affiliate.applications') . '?pm=affiliate', $html);
    }

    public function test_a_role_sees_only_the_pages_and_tabs_it_may_open(): void
    {
        $html = $this->actingAs($this->admin(['job_post_manage']), 'admin')->get(route('admin.page.manage'))->assertOk()->getContent();

        $this->assertStringContainsString('<h3>Career</h3>', $html);
        $this->assertStringContainsString('<h3>Working at Go4Database</h3>', $html);
        $this->assertStringNotContainsString('<h3>Affiliate Program</h3>', $html);
        $this->assertStringNotContainsString('Address &amp; SEO', $html);  // a General Settings screen
        $this->assertStringNotContainsString('<h3>Custom pages</h3>', $html);
    }

    public function test_opening_a_page_goes_to_its_first_screen_with_the_page_named(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.page.manage.show', 'affiliate'))
            ->assertRedirect(route('admin.affiliate.content') . '?pm=affiliate');

        $this->actingAs($this->admin(), 'admin')->get(route('admin.page.manage.show', 'no-such-page'))->assertNotFound();
    }

    public function test_a_page_screen_shows_the_page_tab_bar_and_the_sidebar_group(): void
    {
        $html = $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.affiliate.settings') . '?pm=affiliate')->assertOk()->getContent();

        $this->assertStringContainsString('class="g4pm-bar"', $html);
        $this->assertStringContainsString('<h2 class="g4pm-bar__title">Affiliate Program</h2>', $html);
        $this->assertMatchesRegularExpression('#<a href="[^"]*affiliate-settings\?pm=affiliate"\s+class="is-active"#', $html);
        $this->assertStringContainsString('>Applications</a>', $html);
        $this->assertStringContainsString('<span>Page Manage</span>', $html);
    }

    public function test_a_screen_two_pages_share_follows_the_page_the_admin_came_from(): void
    {
        $manager = new PageManager(new PageRegistry(), fn () => true);
        $jobs = fn (string $query = '') => $this->routed(route('admin.jobs.all', [], false) . $query);

        $this->assertSame('working', $manager->current($jobs('?pm=working'))->key);
        $this->assertSame('career', $manager->current($jobs('?pm=career'))->key);
        $this->assertNull($manager->current($jobs()), 'Roles is on two pages: without ?pm there is no telling which');

        $request = $jobs();
        $manager->remember($request, (new PageRegistry())->find('working'));
        $this->assertSame('working', $manager->current($request)->key, 'the page chosen earlier this session');
    }

    public function test_address_and_seo_save_where_page_settings_does(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.page.manage.seo', 'pricing'), [
                'slug' => 'Our Pricing',
                'seo' => ['en_US' => ['name' => 'Pricing', 'meta_tags' => 'Go4Database pricing', 'meta_description' => 'Plans and prices.', 'schema_code' => '', 'meta_image' => '12']],
            ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame('our-pricing', $this->option('price_plan_page_slug'));
        $this->assertSame('Go4Database pricing', $this->option('price_plan_page_en_US_meta_tags'));
        $this->assertSame('Plans and prices.', $this->option('price_plan_page_en_US_meta_description'));
        $this->assertSame('Pricing', $this->option('price_plan_page_en_US_name'));
        $this->assertSame('12', $this->option('price_plan_page_en_US_meta_image'));
    }

    public function test_an_address_already_taken_is_refused(): void
    {
        $guard = app(SlugGuard::class);
        $pricing = (new PageRegistry())->find('pricing');
        DB::table('pages')->insert(['title' => 'Privacy', 'slug' => 'privacy-policy', 'content' => '', 'status' => 'publish', 'lang' => 'en_US']);

        $this->assertNull($guard->problem($pricing, 'price-plan'), 'its own address');
        $this->assertNull($guard->problem($pricing, 'plans'));
        $this->assertStringContainsString('FAQ', (string) $guard->problem($pricing, 'faq'));
        $this->assertNotNull($guard->problem($pricing, 'admin-home'));
        $this->assertNotNull($guard->problem($pricing, 'affiliate'));
        $this->assertStringContainsString('custom page', (string) $guard->problem($pricing, 'privacy-policy'));
        $this->assertNotNull($guard->problem($pricing, ''));
    }

    public function test_a_refused_address_saves_nothing(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->from(route('admin.page.manage.seo', 'pricing'))
            ->post(route('admin.page.manage.seo', 'pricing'), ['slug' => 'faq', 'seo' => ['en_US' => ['meta_tags' => 'Changed']]])
            ->assertSessionHasErrors('slug');

        $this->assertNull($this->option('price_plan_page_slug'));
        $this->assertNull($this->option('price_plan_page_en_US_meta_tags'));
    }

    public function test_a_fixed_address_is_shown_not_offered(): void
    {
        $html = $this->actingAs($this->admin(), 'admin')->get(route('admin.page.manage.seo', 'authors'))->assertOk()->getContent();

        $this->assertStringNotContainsString('name="slug"', $html);
        $this->assertSame('author', app(PageSeo::class)->slug((new PageRegistry())->find('authors')));
    }

    public function test_the_seo_tab_needs_its_permission_and_a_page_that_has_one(): void
    {
        $this->actingAs($this->admin(['job_post_manage']), 'admin')->get(route('admin.page.manage.seo', 'career'))->assertForbidden();
        $this->actingAs($this->admin(), 'admin')->get(route('admin.page.manage.seo', 'home'))->assertNotFound();
    }

    public function test_pages_with_fixed_addresses_get_a_meta_title_and_description_tab(): void
    {
        $html = $this->actingAs($this->admin(), 'admin')->get(route('admin.page.manage.seo', 'affiliate'))->assertOk()->getContent();

        $this->assertStringContainsString('Meta title &amp; description', $html);
        $this->assertStringContainsString('name="seo[en_US][meta_tags]"', $html);
        $this->assertStringContainsString('name="seo[en_US][meta_description]"', $html);
        $this->assertStringNotContainsString('name="slug"', $html);
        $this->assertStringNotContainsString('name="seo[en_US][schema_code]"', $html);

        foreach (['working', 'email-verifier', 'email-finder', 'affiliate'] as $key) {
            $tabs = array_map(fn ($tab) => $tab->label, (new PageRegistry())->find($key)->tabs);
            $this->assertContains('Meta title & description', $tabs, "$key has no meta tab");
        }
    }

    public function test_the_meta_tab_saves_where_the_page_reads_it(): void
    {
        $this->actingAs($this->admin(), 'admin')->post(route('admin.page.manage.seo', 'affiliate'), [
            'seo' => ['en_US' => ['meta_tags' => 'Affiliate Program: 20% recurring commission | Go4Database', 'meta_description' => 'Earn 20% every month.']],
        ])->assertRedirect()->assertSessionHasNoErrors();

        // the keys the Affiliate page itself reads (its content screen uses af_{lang}_meta_description too)
        $this->assertSame('Affiliate Program: 20% recurring commission | Go4Database', $this->option('af_en_US_meta_title'));
        $this->assertSame('Earn 20% every month.', $this->option('af_en_US_meta_description'));
        $seo = app(PageSeo::class);
        $GLOBALS['__g4_static_options'] = [];
        $this->assertSame('Affiliate Program: 20% recurring commission | Go4Database', $seo->valueFor('affiliate', 'meta_tags', 'en_US'));
        $this->assertSame('', $seo->valueFor('email-verifier', 'meta_tags', 'en_US'), 'nothing set: the page keeps its own title');
        $this->assertSame('', $seo->valueFor('home', 'meta_tags', 'en_US'));
    }

    private function routed(string $uri): Request
    {
        $request = Request::create($uri, 'GET');
        $request->setLaravelSession(app('session.store'));
        $request->setRouteResolver(fn () => app('router')->getRoutes()->match($request)->bind($request));

        return $request;
    }

    private function option(string $key): ?string
    {
        $value = DB::table('static_options')->where('option_name', $key)->value('option_value');

        return $value === null ? null : (string) $value;
    }

    private function admin(array $permissions = self::ALL): Admin
    {
        static $n = 0;
        $n++;
        $role = DB::table('admin_roles')->insertGetId(['name' => 'Tester ' . $n, 'permission' => json_encode($permissions)]);

        return Admin::forceCreate([
            'name' => 'Tester', 'username' => 'tester' . $n, 'email' => "tester$n@example.com",
            'role' => (string) $role, 'password' => bcrypt('secret-password'),
        ]);
    }
}
