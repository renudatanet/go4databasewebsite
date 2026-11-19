<?php

namespace Tests\Feature\FormLeads;

use App\Admin;
use App\FormLeads\FormLead;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * The list pages' forms are kept as form leads, and Admin > List > Form Leads
 * shows them. Each test runs in its own process: routes/web.php loads the
 * admin routes with require_once, so a second app in one process would have
 * none.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class FormLeadsTest extends TestCase
{
    private const MIGRATIONS = [
        'database/migrations/2019_11_06_180949_create_admins_table.php',
        'database/migrations/2019_12_07_082524_create_static_options_table.php',
        'database/migrations/2019_12_13_221931_create_languages_table.php',
        'database/migrations/2020_03_29_075123_create_admin_roles_table.php',
        'database/migrations/2020_04_14_082508_create_media_uploads_table.php',
        'database/migrations/2026_11_19_100000_create_form_leads_table.php',
    ];

    private const PAGE = 'http://localhost/list/healthcare/japan';

    private int $listId;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
        $GLOBALS['__g4_static_options'] = [];
        $this->artisan('migrate:fresh', ['--path' => self::MIGRATIONS, '--force' => true]);
        DB::table('languages')->insert(['name' => 'English (USA)', 'slug' => 'en_US', 'default' => 1, 'status' => 'publish']);
        // b2blist predates the migrations: only what the forms read.
        Schema::create('b2blist', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
        $this->listId = DB::table('b2blist')->insertGetId(['title' => 'Healthcare Email List of Japan']);
        // The site-wide frontend middleware (GlobalVariableMiddleware) reads these.
        foreach (['social_icons', 'menus', 'blogs', 'widgets', 'popup_builders'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
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

    public function test_talk_to_an_expert_is_kept_and_still_emailed(): void
    {
        Mail::fake();

        $this->from(self::PAGE)->post(route('service-lead.submit'), [
            'name' => 'Asha Rao', 'phone' => '+91 98100 00000', 'email' => 'Asha@Clinic.example',
            'business_details' => 'clinic.example, 40 doctors', 'list_id' => $this->listId, 'website_url' => '',
        ], ['referer' => self::PAGE])
            ->assertRedirect(self::PAGE . '#lead-form')
            ->assertSessionHas('success');

        $lead = FormLead::sole();
        $this->assertSame('list_expert', $lead->form);
        $this->assertSame(['Asha Rao', 'asha@clinic.example', '+91 98100 00000', 'clinic.example, 40 doctors'], [$lead->name, $lead->email, $lead->phone, $lead->company]);
        $this->assertSame([$this->listId, 'Healthcare Email List of Japan', self::PAGE], [$lead->list_id, $lead->list_title, $lead->page_url]);
        $this->assertSame('new', $lead->status);
        $this->assertTrue($lead->emailed);
    }

    public function test_a_failed_email_never_loses_the_lead_or_shows_an_error(): void
    {
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('SMTP is down'));

        $this->from(self::PAGE)->post(route('sample-lead.submit'), [
            'fname' => 'Ken', 'lname' => 'Sato', 'business-email' => 'ken@labs.example',
            'sample-phone' => '0312345678', 'sample-message' => 'Need 200 lab directors in Tokyo', 'list_id' => $this->listId,
        ])->assertRedirect(self::PAGE . '#sample-data-section')->assertSessionHas('sample-success');

        $lead = FormLead::sole();
        $this->assertSame(['list_sample', 'Ken Sato', 'Need 200 lab directors in Tokyo'], [$lead->form, $lead->name, $lead->message]);
        $this->assertFalse($lead->emailed);
    }

    public function test_the_free_credits_form_keeps_the_email_and_goes_on_to_the_app(): void
    {
        $this->post(route('list-signup.submit'), [
            'utm_source' => 'ProductPage', 'utm_medium' => 'Internal', 'utm_campaign' => 'login',
            'email' => 'buyer@company.example', 'list_id' => $this->listId,
        ])->assertRedirect('https://app.go4database.com/login?utm_source=ProductPage&utm_medium=Internal&utm_campaign=login&email=buyer%40company.example');

        $lead = FormLead::sole();
        $this->assertSame(['list_signup', 'buyer@company.example', null], [$lead->form, $lead->email, $lead->emailed]);
    }

    public function test_an_empty_or_invalid_signup_email_still_goes_on_but_is_not_kept(): void
    {
        $app = 'https://app.go4database.com/login?utm_source=ProductPage&utm_medium=Internal&utm_campaign=login';
        $this->post(route('list-signup.submit'), ['email' => ''])->assertRedirect($app);
        $this->post(route('list-signup.submit'), ['email' => 'not an email'])->assertRedirect($app . '&email=not+an+email');
        // a tampered tag falls back to the page's own
        $this->post(route('list-signup.submit'), ['utm_source' => '<script>', 'email' => ''])->assertRedirect($app);

        $this->assertSame(0, FormLead::count());
    }

    public function test_a_bot_filling_the_hidden_box_is_thanked_and_ignored(): void
    {
        Mail::fake();

        $this->from(self::PAGE)->post(route('service-lead.submit'), [
            'name' => 'Bot', 'phone' => '1', 'email' => 'bot@spam.example', 'website_url' => 'http://spam.example',
        ])->assertSessionHas('success');
        $this->post(route('list-signup.submit'), ['email' => 'bot@spam.example', 'website_url' => 'x']);

        $this->assertSame(0, FormLead::count());
    }

    public function test_a_form_missing_its_details_is_sent_back_and_nothing_is_kept(): void
    {
        $this->from(self::PAGE)->post(route('service-lead.submit'), ['name' => 'Asha', 'email' => 'not-an-email'])
            ->assertRedirect(self::PAGE)->assertSessionHasErrors(['phone', 'email']);

        $this->assertSame(0, FormLead::count());
    }

    public function test_the_admin_screen_lists_filters_and_searches_leads(): void
    {
        $this->lead('list_expert', 'Asha Rao', 'asha@clinic.example');
        $this->lead('list_sample', 'Ken Sato', 'ken@labs.example', 'contacted');
        $this->lead('list_signup', null, 'buyer@company.example');
        $admin = $this->admin();

        $all = $this->actingAs($admin, 'admin')->get(route('admin.form.leads'))->assertOk()->getContent();
        foreach (['asha@clinic.example', 'ken@labs.example', 'buyer@company.example', 'Talk to an expert', 'Sample request', 'Free credits sign-up'] as $text) {
            $this->assertStringContainsString($text, $all);
        }

        $samples = $this->actingAs($admin, 'admin')->get(route('admin.form.leads', ['form' => 'list_sample']))->getContent();
        $this->assertStringContainsString('ken@labs.example', $samples);
        $this->assertStringNotContainsString('asha@clinic.example', $samples);

        $found = $this->actingAs($admin, 'admin')->get(route('admin.form.leads', ['q' => 'clinic']))->getContent();
        $this->assertStringContainsString('asha@clinic.example', $found);
        $this->assertStringNotContainsString('buyer@company.example', $found);

        $contacted = $this->actingAs($admin, 'admin')->get(route('admin.form.leads', ['status' => 'contacted']))->getContent();
        $this->assertStringContainsString('ken@labs.example', $contacted);
        $this->assertStringNotContainsString('asha@clinic.example', $contacted);
    }

    public function test_status_can_be_changed_and_a_lead_deleted(): void
    {
        $lead = $this->lead('list_expert', 'Asha Rao', 'asha@clinic.example');
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')->post(route('admin.form.leads.status', $lead->id), ['status' => 'done'])->assertRedirect();
        $this->assertSame('done', $lead->fresh()->status);

        $this->actingAs($admin, 'admin')->post(route('admin.form.leads.status', $lead->id), ['status' => 'hacked'])->assertSessionHasErrors('status');
        $this->assertSame('done', $lead->fresh()->status);

        $this->actingAs($admin, 'admin')->post(route('admin.form.leads.delete', $lead->id))->assertRedirect();
        $this->assertSame(0, FormLead::count());
    }

    public function test_the_csv_has_the_filtered_leads_and_no_formulas(): void
    {
        $this->lead('list_expert', '=HYPERLINK("http://evil.example")', 'asha@clinic.example', 'new', '+91 98100 00000');
        $this->lead('list_signup', null, 'buyer@company.example');

        $csv = $this->actingAs($this->admin(), 'admin')->get(route('admin.form.leads.export', ['form' => 'list_expert']))
            ->assertOk()->streamedContent();

        $this->assertStringContainsString('asha@clinic.example', $csv);
        $this->assertStringNotContainsString('buyer@company.example', $csv);
        $this->assertStringContainsString("'=HYPERLINK", $csv);
        $this->assertStringContainsString(',"+91 98100 00000",', $csv, 'a phone number is left as it is');
    }

    public function test_the_screen_needs_the_list_permission(): void
    {
        $this->actingAs($this->admin(['job_post_manage']), 'admin')->get(route('admin.form.leads'))->assertRedirect(route('admin.home'));
        $this->get(route('admin.form.leads'))->assertRedirect();
    }

    private function lead(string $form, ?string $name, string $email, string $status = 'new', ?string $phone = null): FormLead
    {
        return FormLead::create(['form' => $form, 'name' => $name, 'email' => $email, 'phone' => $phone, 'status' => $status,
            'list_id' => $this->listId, 'list_title' => 'Healthcare Email List of Japan', 'page_url' => self::PAGE]);
    }

    private function admin(array $permissions = ['services']): Admin
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
