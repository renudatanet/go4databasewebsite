<?php

namespace Tests\Feature\Sitemaps;

use App\Sitemaps\ListSitemap;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * The list pages' sitemaps: /list_sitemap.xml indexes one sitemap per
 * industry (List > Category), and sitemap.xml lists those directly.
 */
class ListSitemapTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);

        // These tables predate the migrations, so only what the sitemaps read.
        Schema::create('list_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('status')->nullable();
            $table->string('lang')->nullable();
            $table->timestamps();
        });
        Schema::create('b2blist', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('categories_id')->nullable();
            $table->timestamps();
        });
        // sitemap.xml also dates the other sitemaps from these.
        foreach (['blogs', 'works', 'pages', 'services', 'case_studies', 'jobs'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }
    }

    private function industry(string $name, string $slug, string $status = 'publish', string $lang = 'en'): int
    {
        return DB::table('list_categories')->insertGetId(compact('name', 'slug', 'status', 'lang'));
    }

    private function list(string $slug, ?int $industry, string $status = 'Publish', string $updated = '2026-11-01 10:00:00'): void
    {
        DB::table('b2blist')->insert([
            'title' => $slug, 'slug' => $slug, 'status' => $status, 'categories_id' => $industry,
            'created_at' => $updated, 'updated_at' => $updated,
        ]);
    }

    public function test_the_index_has_one_sitemap_per_industry_with_lists(): void
    {
        $health = $this->industry('Healthcare', 'healthcare');
        $estate = $this->industry('Real Estate', 'real-estate');
        $this->industry('Aviation', 'aviation');                     // no lists yet
        $hidden = $this->industry('Legal', 'legal', 'draft');        // not published
        $this->list('/healthcare/staffing-agencies', $health, 'Publish', '2026-10-01 09:00:00');
        $this->list('/healthcare/lab-network-ceo', $health, 'Publish', '2026-11-05 12:30:00');
        $this->list('/real-estate/brokers', $estate);
        $this->list('/legal/law-firms', $hidden);

        $xml = $this->get('/list_sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml')->getContent();

        $this->assertStringContainsString('<sitemapindex', $xml);
        $this->assertSame([
            url('/list_sitemap_healthcare.xml'),
            url('/list_sitemap_real-estate.xml'),
            url('/list_sitemap_uncategorized.xml'),
        ], $this->locs($xml));
        // An industry's date is when its newest list changed.
        $this->assertStringContainsString('<lastmod>2026-11-05T12:30:00+00:00</lastmod>', $xml);
    }

    public function test_an_industry_sitemap_holds_the_lists_a_visitor_can_open(): void
    {
        $health = $this->industry('Healthcare', 'healthcare');
        $this->list('/healthcare/staffing-agencies', $health, 'Publish', '2026-10-01 09:00:00');
        $this->list('/healthcare/imported', $health, '1');          // the Excel importer's status
        $this->list('/healthcare/unfinished', $health, 'draft');

        $xml = $this->get('/list_sitemap_healthcare.xml')->assertOk()->getContent();

        $this->assertStringContainsString('<urlset', $xml);
        $this->assertSame([url('/list/healthcare/staffing-agencies'), url('/list/healthcare/imported')], $this->locs($xml));
        $this->assertStringContainsString('<lastmod>2026-10-01T09:00:00+00:00</lastmod>', $xml);
    }

    public function test_industries_of_the_same_name_in_two_languages_share_a_sitemap(): void
    {
        $en = $this->industry('Healthcare', 'healthcare');
        $other = $this->industry('Healthcare', 'healthcare', 'publish', 'ar');
        $this->list('/healthcare/a', $en);
        $this->list('/healthcare/b', $other);

        $this->assertCount(1, (new ListSitemap())->industries());
        $this->assertCount(2, (new ListSitemap())->urls('healthcare'));
    }

    public function test_an_unknown_or_empty_industry_is_not_found(): void
    {
        $this->industry('Aviation', 'aviation');

        // Checked as the exception: the site's 404 page needs tables this test leaves out.
        $this->withoutExceptionHandling();
        foreach (['/list_sitemap_aviation.xml', '/list_sitemap_nothing-here.xml'] as $address) {
            try {
                $this->get($address);
                $this->fail("$address should be not found");
            } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function test_the_main_index_lists_the_industry_sitemaps_and_never_an_index(): void
    {
        $health = $this->industry('Healthcare', 'healthcare');
        $this->list('/healthcare/staffing-agencies', $health);

        $locs = $this->locs($this->get('/sitemap.xml')->assertOk()->getContent());

        $this->assertContains(url('/list_sitemap_healthcare.xml'), $locs);
        $this->assertNotContains(url('/list_sitemap.xml'), $locs);
        $this->assertContains(url('/career_sitemap.xml'), $locs);
    }

    /** @return list<string> */
    private function locs(string $xml): array
    {
        preg_match_all('#<loc>([^<]+)</loc>#', $xml, $m);

        return array_map('trim', $m[1]);
    }
}
