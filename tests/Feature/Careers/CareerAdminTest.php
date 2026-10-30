<?php

namespace Tests\Feature\Careers;

use App\Careers\CareerText;
use App\JobsCategory;
use Illuminate\Support\Facades\DB;

/**
 * Jobs > Job Category and Job Page Settings: what they save. Each test runs
 * in its own process: routes/web.php loads the admin routes with
 * require_once, so a second app in one process would have none.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CareerAdminTest extends CareersTestCase
{
    public function test_a_new_category_gets_an_address_from_its_name_and_keeps_its_seo(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.jobs.category.new'), [
                'title' => 'Legal - R&D', 'lang' => 'en', 'status' => 'publish',
                'meta_description' => 'Legal and research roles', 'meta_tags' => 'Legal jobs at Go4Database',
            ])->assertRedirect();

        $category = JobsCategory::firstWhere('title', 'Legal - R&D');
        $this->assertSame('legal-rd', $category->slug);
        $this->assertSame('Legal and research roles', $category->meta_description);
        $this->assertSame('Legal jobs at Go4Database', $category->meta_tags);
    }

    public function test_editing_keeps_the_address_unless_it_is_changed(): void
    {
        $category = $this->category('Legal - R&D', ['slug' => 'legal-randd']);
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')->post(route('admin.jobs.category.update'), [
            'id' => $category->id, 'title' => 'Legal & Research', 'lang' => 'en', 'status' => 'publish', 'slug' => '',
        ])->assertRedirect();
        $this->assertSame('legal-randd', $category->fresh()->slug);
        $this->assertSame('Legal & Research', $category->fresh()->title);

        $this->actingAs($admin, 'admin')->post(route('admin.jobs.category.update'), [
            'id' => $category->id, 'title' => 'Legal & Research', 'lang' => 'en', 'status' => 'publish', 'slug' => 'Legal Research',
        ])->assertRedirect();
        $this->assertSame('legal-research', $category->fresh()->slug);
    }

    public function test_bulk_delete_keeps_categories_that_still_hold_a_job(): void
    {
        $used = $this->category('Sales');
        $empty = $this->category('Finance');
        $this->job('Inside Sales Executive', ['category_id' => $used->id]);

        $this->actingAs($this->admin(), 'admin')
            ->postJson(route('admin.jobs.category.bulk.action'), ['ids' => [$used->id, $empty->id]])
            ->assertOk()->assertJson(['kept' => [$used->id]]);

        $this->assertNotNull($used->fresh());
        $this->assertNull($empty->fresh());
    }

    public function test_page_settings_save_every_text_and_the_google_address(): void
    {
        $input = ['site_job_post_items' => '8', 'career_schema_country' => 'IN', 'career_salary_currency' => 'INR', 'career_schema_locality' => 'Noida'];
        foreach (array_keys(CareerText::DEFAULTS) as $key) {
            $input['career_page_en_' . $key] = 'Text for ' . $key;
        }

        $this->actingAs($this->admin(), 'admin')->post(route('admin.jobs.page.settings'), $input)
            ->assertRedirect()->assertSessionHasNoErrors();

        $saved = DB::table('static_options')->pluck('option_value', 'option_name');
        $this->assertSame('8', $saved['site_job_post_items']);
        $this->assertSame('Text for perks', $saved['career_page_en_perks']);
        $this->assertSame('Text for apply_button', $saved['career_page_en_apply_button']);
        $this->assertSame('Noida', $saved['career_schema_locality']);
    }

    public function test_page_settings_refuse_a_bad_country_code_or_page_size(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->post(route('admin.jobs.page.settings'), ['site_job_post_items' => '0', 'career_schema_country' => 'India'])
            ->assertSessionHasErrors(['site_job_post_items', 'career_schema_country']);
    }

    public function test_a_job_saved_without_a_slug_gets_one_from_its_title(): void
    {
        $this->setOption('job_module_status', 'on');
        $this->actingAs($this->admin(), 'admin')->post(route('admin.jobs.new'), [
            'title' => 'QA Test Engineer', 'position' => 'QA', 'company_name' => 'Go4Database', 'category_id' => '1',
            'vacancy' => '1', 'job_responsibility' => '<p>Test</p>', 'employment_status' => 'full_time',
            'job_location' => 'Noida', 'salary' => '5K-10K', 'lang' => 'en', 'deadline' => '2099-01-01', 'status' => 'publish',
        ])->assertSessionHasNoErrors();

        // It used to pass the language as Str::slug's separator: "qaentenstengienr".
        $this->assertSame('qa-test-engineer', \App\Jobs::firstWhere('title', 'QA Test Engineer')->slug);
    }

    public function test_a_cloned_job_is_a_draft_with_a_clean_address(): void
    {
        $job = $this->job('QA Test Engineer');

        $this->actingAs($this->admin(), 'admin')->post(route('admin.jobs.clone'), ['item_id' => $job->id])->assertRedirect();

        $copy = \App\Jobs::where('id', '!=', $job->id)->firstOrFail();
        $this->assertMatchesRegularExpression('/^qa-test-engineer-\d{4}$/', $copy->slug);
        $this->assertSame('draft', $copy->status);
    }

    public function test_admins_without_the_job_permission_are_turned_away(): void
    {
        $this->actingAs($this->admin(['blogs']), 'admin')
            ->post(route('admin.jobs.category.new'), ['title' => 'Nope', 'lang' => 'en', 'status' => 'publish'])
            ->assertRedirect(route('admin.home'));

        $this->assertSame(0, JobsCategory::count());
    }
}
