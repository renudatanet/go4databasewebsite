<?php

namespace Tests\Feature\Blog;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * The blog search lives at /blog/search?s=..., and the old address
 * /blog-search?search=... moves there for good. Each test runs in its own
 * process: routes/web.php loads the admin routes with require_once.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class BlogSearchAddressTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
        $GLOBALS['__g4_static_options'] = [];
        $this->artisan('migrate:fresh', ['--path' => [
            'database/migrations/2019_12_07_082524_create_static_options_table.php',
            'database/migrations/2019_12_13_221931_create_languages_table.php',
        ], '--force' => true]);
        DB::table('languages')->insert(['name' => 'English (USA)', 'slug' => 'en_US', 'default' => 1, 'status' => 'publish']);
        // Tables the site-wide frontend middleware reads.
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

    public function test_the_search_address_is_blog_search_with_s(): void
    {
        $this->assertSame(url('/blog/search') . '?s=lead%20generation', route('frontend.blog.search', ['s' => 'lead generation']));
    }

    public function test_the_old_address_moves_for_good_keeping_the_words_and_page(): void
    {
        $this->get('/blog-search?search=sales+lead')->assertStatus(301)->assertRedirect(url('/blog/search') . '?s=sales+lead');
        $this->get('/blog-search?search=crm&page=3')->assertStatus(301)->assertRedirect(url('/blog/search') . '?s=crm&page=3');
        $this->get('/blog-search')->assertStatus(301)->assertRedirect(url('/blog/search'));
    }
}
