<?php

namespace Tests\Feature\HomePage;

use App\Admin;
use App\FormLeads\FormLead;
use App\HomePage\HomePageFields;
use App\HomePage\HomePageText;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Page Manage > Home > Homepage content (App\HomePage) and the homepage's
 * "Talk to an Expert" form. Each test runs in its own process: routes/web.php
 * loads the admin routes with require_once, so a second app in one process
 * would have none.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class HomeContentTest extends TestCase
{
    private const MIGRATIONS = [
        'database/migrations/2019_11_06_180949_create_admins_table.php',
        'database/migrations/2019_12_07_082524_create_static_options_table.php',
        'database/migrations/2019_12_13_221931_create_languages_table.php',
        'database/migrations/2020_03_29_075123_create_admin_roles_table.php',
        'database/migrations/2020_04_14_082508_create_media_uploads_table.php',
        'database/migrations/2026_11_19_100000_create_form_leads_table.php',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
        $GLOBALS['__g4_static_options'] = [];
        $this->artisan('migrate:fresh', ['--path' => self::MIGRATIONS, '--force' => true]);
        DB::table('languages')->insert(['name' => 'English (USA)', 'slug' => 'en_US', 'default' => 1, 'status' => 'publish']);
        // Tables the site-wide frontend middleware reads, and the lead's list lookup.
        foreach (['social_icons', 'menus', 'blogs', 'widgets', 'popup_builders', 'b2blist'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('status')->nullable();
                $table->string('lang')->nullable();
                $table->integer('widget_order')->nullable();
                $table->timestamps();
            });
        }
    }

    protected function tearDown(): void
    {
        $GLOBALS['__g4_static_options'] = [];
        parent::tearDown();
    }

    public function test_the_page_keeps_its_built_in_wording_until_a_field_is_saved(): void
    {
        $home = new HomePageText('en_US');

        $this->assertSame('Supercharge Your Sales with', $home->get('hero_title'));
        $this->assertCount(5, $home->lines('hero_words'));
        $this->assertSame(['400Mn', 'Global Business Contacts'], $home->pairs('stats')[0]);
        $this->assertSame(['CEO / C-Level List', 'CEO'], $home->pairs('search_tabs')[0]);
        $this->assertCount(8, $home->pairs('features'));
        $this->assertCount(5, $home->pairs('faqs'));
        $this->assertTrue($home->on('compare'));
        $this->assertSame([], $home->logos('logos'), 'no logos saved: the page keeps its built-in strip');

        $this->option('home_page_text_en_US_hero_title', 'Grow faster with');
        $this->option('home_page_text_en_US_show_compare', '0');
        $this->assertSame('Grow faster with', $home->get('hero_title'));
        $this->assertFalse($home->on('compare'));
    }

    public function test_links_never_run_script_and_highlights_are_escaped(): void
    {
        $home = new HomePageText('en_US');
        $this->option('home_page_text_en_US_cta_button_url', 'javascript:alert(1)');
        $this->option('home_page_text_en_US_hero_button_url', '/pricing');
        $this->option('home_page_text_en_US_promo_title', 'Get *<b>2000</b> credits* now');

        $this->assertSame(HomePageText::DEFAULTS['cta_button_url'], $home->url('cta_button_url'));
        $this->assertSame('/pricing', $home->url('hero_button_url'));
        $this->assertSame('Get <span style="color:#15803d;">&lt;b&gt;2000&lt;/b&gt; credits</span> now', $home->marked('promo_title', 'color:#15803d;'));
    }

    public function test_a_logo_list_takes_images_or_text(): void
    {
        $this->option('home_page_text_en_US_logos', "Google | assets/frontend/images/logos/Google_2015_logo.svg\nAcme\nCdn | https://cdn.example.com/x.png");

        $this->assertSame([
            ['Google', '/assets/frontend/images/logos/Google_2015_logo.svg'],
            ['Acme', null],
            ['Cdn', 'https://cdn.example.com/x.png'],
        ], (new HomePageText('en_US'))->logos('logos'));
    }

    public function test_every_field_of_the_page_is_on_the_admin_screen(): void
    {
        $types = HomePageFields::types();

        $this->assertSame([], array_values(array_diff(array_keys(HomePageText::DEFAULTS), array_keys($types))), 'a default with no field');
        $this->assertSame([], array_values(array_diff(array_keys($types), array_keys(HomePageText::DEFAULTS))), 'a field with no default');
    }

    public function test_the_screen_shows_the_page_and_saves_it(): void
    {
        $admin = $this->admin();
        $html = $this->actingAs($admin, 'admin')->get(route('admin.home.content'))->assertOk()->getContent();
        $this->assertStringContainsString('name="home_page_text_en_US_hero_title"', $html);
        $this->assertStringContainsString('value="Supercharge Your Sales with"', $html);

        $this->actingAs($admin, 'admin')->post(route('admin.home.content'), [
            'home_page_text_en_US_hero_title' => 'Grow faster with',
            'home_page_text_en_US_stats' => "500Mn | Contacts\n140Mn | Mobiles",
            'home_page_text_en_US_show_stats' => '1',
            // show_compare not sent: an unticked box
        ])->assertRedirect()->assertSessionHasNoErrors();

        $home = new HomePageText('en_US');
        $GLOBALS['__g4_static_options'] = [];
        $this->assertSame('Grow faster with', $home->get('hero_title'));
        $this->assertSame([['500Mn', 'Contacts'], ['140Mn', 'Mobiles']], $home->pairs('stats'));
        $this->assertTrue($home->on('stats'));
        $this->assertFalse($home->on('compare'));
    }

    public function test_the_screen_refuses_what_is_not_an_image_or_a_switch(): void
    {
        $this->actingAs($this->admin(), 'admin')->post(route('admin.home.content'), [
            'home_page_text_en_US_promo_image' => 'not-a-media-id',
            'home_page_text_en_US_show_faq' => 'yes',
        ])->assertSessionHasErrors(['home_page_text_en_US_promo_image', 'home_page_text_en_US_show_faq']);

        $this->assertNull(DB::table('static_options')->where('option_name', 'home_page_text_en_US_show_faq')->value('option_value'));
    }

    public function test_the_screen_needs_the_home_page_permission(): void
    {
        $this->actingAs($this->admin(['job_post_manage']), 'admin')->get(route('admin.home.content'))->assertRedirect(route('admin.home'));
        $this->actingAs($this->admin(['job_post_manage']), 'admin')->post(route('admin.home.content'), ['home_page_text_en_US_hero_title' => 'x'])->assertRedirect(route('admin.home'));
        $this->assertNull(DB::table('static_options')->where('option_name', 'home_page_text_en_US_hero_title')->value('option_value'));
    }

    public function test_the_contact_form_is_kept_as_a_form_lead(): void
    {
        Mail::fake();

        $this->postJson(route('home-contact.submit'), ['email' => 'Asha@Clinic.example', 'name' => 'Asha Rao', 'phone' => '+91 98100 00000'])
            ->assertOk()->assertJson(['message' => HomePageText::DEFAULTS['contact_success']]);

        $lead = FormLead::sole();
        $this->assertSame(['home_expert', 'Asha Rao', 'asha@clinic.example', '+91 98100 00000'], [$lead->form, $lead->name, $lead->email, $lead->phone]);
        $this->assertTrue($lead->emailed);
    }

    public function test_without_script_the_form_comes_back_to_itself_with_the_message(): void
    {
        Mail::fake();

        $this->from('http://localhost/')->post(route('home-contact.submit'), ['email' => 'ken@labs.example', 'name' => 'Ken Sato'])
            ->assertRedirect('http://localhost/#contact')
            ->assertSessionHas('home_contact_success', HomePageText::DEFAULTS['contact_success']);
        $this->assertSame(1, FormLead::count());
    }

    public function test_a_bot_or_an_incomplete_form_is_not_kept(): void
    {
        Mail::fake();

        $this->postJson(route('home-contact.submit'), ['email' => 'bot@spam.example', 'name' => 'Bot', 'website_url' => 'http://spam.example'])->assertOk();
        $this->postJson(route('home-contact.submit'), ['email' => 'not-an-email'])->assertStatus(422)->assertJsonValidationErrors(['email', 'name']);

        $this->assertSame(0, FormLead::count());
    }

    private function option(string $key, string $value): void
    {
        DB::table('static_options')->updateOrInsert(['option_name' => $key], ['option_value' => $value]);
        $GLOBALS['__g4_static_options'] = [];
        cache()->forget($key);
    }

    private function admin(array $permissions = ['home_page_manage']): Admin
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
