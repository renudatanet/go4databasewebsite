<?php

namespace Tests\Feature\B2B;

use App\Admin;
use App\Works;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * B2B > Manage (admin/works): the row's delete button and Bulk Action >
 * Delete. Each test runs in its own process: routes/web.php loads the admin
 * routes with require_once, so a second app in one process would have none.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class WorkDeleteTest extends TestCase
{
    private const MIGRATIONS = [
        'database/migrations/2019_11_06_180949_create_admins_table.php',
        'database/migrations/2019_12_07_082524_create_static_options_table.php',
        'database/migrations/2019_12_13_221931_create_languages_table.php',
        'database/migrations/2020_03_29_075123_create_admin_roles_table.php',
        'database/migrations/2020_01_23_193759_create_works_table.php',
        'database/migrations/2020_01_23_193805_create_works_categories_table.php',
        'database/migrations/2020_04_14_082508_create_media_uploads_table.php',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
        $GLOBALS['__g4_static_options'] = [];
        $this->artisan('migrate:fresh', ['--path' => self::MIGRATIONS, '--force' => true]);
        DB::table('languages')->insert(['name' => 'English', 'slug' => 'en', 'default' => 1, 'status' => 'publish']);
    }

    public function test_the_row_delete_button_deletes_the_item_and_answers_json(): void
    {
        $keep = $this->work('Healthcare Email List');
        $gone = $this->work('Global Email List');

        $this->actingAs($this->admin(), 'admin')
            ->postJson(route('admin.work.delete', $gone->id))
            ->assertOk()->assertJson(['status' => 'ok']);

        $this->assertNull(Works::find($gone->id));
        $this->assertNotNull(Works::find($keep->id));
    }

    public function test_deleting_an_item_already_gone_is_not_an_error(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->postJson(route('admin.work.delete', 999))
            ->assertOk()->assertJson(['status' => 'ok']);
    }

    public function test_the_table_rows_can_be_ticked_for_bulk_delete(): void
    {
        $work = $this->work('Global Email List');

        $rows = $this->actingAs($this->admin(), 'admin')
            ->postJson(route('admin.work.datatable'), [
                'draw' => 1, 'start' => 0, 'length' => 10,
                'order' => [['column' => 0, 'dir' => 'desc']],
            ])->assertOk()->json('data');

        $this->assertStringContainsString('class="bulk-checkbox" value="' . $work->id . '"', $rows[0]['checkbox']);
        $this->assertStringContainsString('class="btn btn-xs btn-danger delete-blog" data-id="' . $work->id . '"', $rows[0]['action']);
    }

    public function test_bulk_delete_removes_the_ticked_items(): void
    {
        $a = $this->work('A');
        $b = $this->work('B');
        $c = $this->work('C');

        $this->actingAs($this->admin(), 'admin')
            ->postJson(route('admin.work.bulk.action'), ['ids' => [$a->id, $c->id]])
            ->assertOk();

        $this->assertSame([$b->id], Works::pluck('id')->all());
    }

    private function work(string $title): Works
    {
        return Works::forceCreate([
            'title' => $title, 'slug' => \Illuminate\Support\Str::slug($title), 'status' => 'publish',
            'categories_id' => serialize([]), 'description' => '<p>' . $title . '</p>', 'lang' => 'en',
        ]);
    }

    private function admin(): Admin
    {
        $role = DB::table('admin_roles')->insertGetId(['name' => 'Tester', 'permission' => json_encode(['case_study'])]);

        return Admin::forceCreate([
            'name' => 'Tester', 'username' => 'tester', 'email' => 'tester@example.com',
            'role' => (string) $role, 'password' => bcrypt('secret-password'),
        ]);
    }
}
