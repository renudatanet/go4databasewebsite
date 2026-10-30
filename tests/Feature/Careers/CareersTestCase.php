<?php

namespace Tests\Feature\Careers;

use App\Admin;
use App\Jobs;
use App\JobsCategory;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * An in-memory SQLite database holding only what the career pages and their
 * admin screens read: jobs, categories, settings, languages and admins.
 */
abstract class CareersTestCase extends TestCase
{
    private const MIGRATIONS = [
        'database/migrations/2019_11_06_180949_create_admins_table.php',
        'database/migrations/2019_12_07_082524_create_static_options_table.php',
        'database/migrations/2019_12_13_221931_create_languages_table.php',
        'database/migrations/2020_03_29_075123_create_admin_roles_table.php',
        'database/migrations/2020_04_22_065550_create_jobs_table.php',
        'database/migrations/2020_04_22_065603_create_jobs_categories_table.php',
        'database/migrations/2026_10_30_000000_add_seo_fields_to_jobs_categories_table.php',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        config(['cache.default' => 'array', 'app.timezone' => 'UTC']);
        $GLOBALS['__g4_static_options'] = [];
        $this->artisan('migrate:fresh', ['--path' => self::MIGRATIONS, '--force' => true]);

        DB::table('languages')->insert(['name' => 'English', 'slug' => 'en', 'default' => 1, 'status' => 'publish']);
        $this->setOption('job_module_status', 'on');
    }

    protected function tearDown(): void
    {
        $GLOBALS['__g4_static_options'] = [];
        parent::tearDown();
    }

    protected function setOption(string $key, ?string $value): void
    {
        DB::table('static_options')->updateOrInsert(['option_name' => $key], ['option_value' => $value]);
        unset($GLOBALS['__g4_static_options'][$key]);
        cache()->forget($key);
    }

    protected function category(string $title, array $attributes = []): JobsCategory
    {
        return JobsCategory::create($attributes + ['title' => $title, 'status' => 'publish', 'lang' => 'en']);
    }

    protected function job(string $title, array $attributes = []): Jobs
    {
        return Jobs::create($attributes + [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'position' => $title,
            'company_name' => 'Go4Database',
            'employment_status' => 'full_time',
            'job_location' => 'Sector 2, Noida',
            'salary' => '25K-60K',
            'deadline' => now()->addMonth()->toDateString(),
            'status' => 'publish',
            'lang' => 'en',
            'job_responsibility' => '<ul><li>Close deals</li></ul>',
        ]);
    }

    protected function admin(array $permissions = ['job_post_manage']): Admin
    {
        $role = DB::table('admin_roles')->insertGetId(['name' => 'Tester', 'permission' => json_encode($permissions)]);

        return Admin::forceCreate([
            'name' => 'Tester', 'username' => 'tester', 'email' => 'tester@example.com',
            'role' => (string) $role, 'password' => bcrypt('secret-password'),
        ]);
    }
}
