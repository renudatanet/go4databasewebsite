<?php

namespace Tests\Feature\Careers;

use App\CareerPageItem;
use App\Careers\JobBoard;
use App\Careers\JobSchema;
use App\Careers\WorkingPage;
use App\Careers\WorkingPageText;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** What /career/working-at-go4database shows, from its blocks, the testimonials and the teams. */
class WorkingPageTest extends CareersTestCase
{
    private function page(): WorkingPage
    {
        return new WorkingPage(new JobBoard('en'), 'en');
    }

    public function test_the_migration_seeds_what_we_do_how_we_work_and_the_office_but_no_quotes(): void
    {
        $page = $this->page();

        $this->assertSame(['Verified B2B contact data', 'Email Verifier', 'Email Finder', 'Lead search'], $page->items('offering')->pluck('title')->all());
        $this->assertSame('/email-verifier', $page->items('offering')[1]->link);
        $this->assertCount(4, $page->items('value'));
        $this->assertSame('Noida', $page->items('office')->sole()->filter);
        $this->assertCount(0, $page->items('quote'));
    }

    public function test_hidden_items_and_other_languages_are_left_out_and_order_is_kept(): void
    {
        CareerPageItem::create(['section' => 'quote', 'title' => 'Second', 'body' => 'b', 'lang' => 'en', 'sr_order' => 2]);
        CareerPageItem::create(['section' => 'quote', 'title' => 'First', 'body' => 'a', 'lang' => 'en', 'sr_order' => 1]);
        CareerPageItem::create(['section' => 'quote', 'title' => 'Hidden', 'body' => 'c', 'lang' => 'en', 'status' => 'draft']);
        CareerPageItem::create(['section' => 'quote', 'title' => 'Hindi', 'body' => 'd', 'lang' => 'hi']);

        $this->assertSame(['First', 'Second'], $this->page()->items('quote')->pluck('title')->all());
    }

    public function test_offices_count_the_roles_whose_location_has_their_word(): void
    {
        $this->job('In Noida', ['job_location' => 'Sector 2, NOIDA']);
        $this->job('Also Noida', ['job_location' => 'Sector - 2, Noida']);
        $this->job('In Delhi', ['job_location' => 'Connaught Place, Delhi']);
        $this->job('Closed', ['job_location' => 'Noida', 'deadline' => now()->subDay()->toDateString()]);
        CareerPageItem::create(['section' => 'office', 'title' => 'Delhi, India', 'lang' => 'en', 'sr_order' => 99]);

        $counts = $this->page()->offices()->mapWithKeys(fn ($o) => [$o['item']->title => $o['count']])->all();

        // An office with no filter word uses its title, "Delhi, India", which no location contains.
        $this->assertSame(['Noida, India' => 2, 'Delhi, India' => 0], $counts);
    }

    public function test_customer_testimonials_are_the_newest_published_ones(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('lang')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
        foreach (['Old', 'Middle', 'Newest'] as $name) {
            DB::table('testimonials')->insert(['name' => $name, 'description' => 'Great', 'lang' => 'en', 'status' => 'publish']);
        }
        DB::table('testimonials')->insert(['name' => 'Draft', 'description' => 'x', 'lang' => 'en', 'status' => 'draft']);

        $this->assertSame(['Newest', 'Middle'], $this->page()->testimonials(2)->pluck('name')->all());
        $this->assertCount(0, $this->page()->testimonials(0));
    }

    public function test_teams_carry_their_photo_from_the_admin(): void
    {
        $sales = $this->category('Business Success');
        $this->job('Account Executive', ['category_id' => $sales->id]);
        $this->setOption(WorkingPage::TEAM_IMAGE_OPTION . $sales->id, '450');

        $team = $this->page()->teams()->sole();

        $this->assertSame(1, $team['count']);
        $this->assertSame('450', $team['image']);
    }

    public function test_the_optional_band_is_off_until_it_has_a_title_and_none_hides_the_story(): void
    {
        $text = new WorkingPageText('en');
        $this->assertSame('', $text->get('remote_title'));
        $this->assertCount(2, $text->lines('story_text'));

        $this->setOption('working_page_en_story_text', 'none');
        $this->assertSame([], (new WorkingPageText('en'))->lines('story_text'));
    }

    public function test_the_structured_data_describes_an_about_page_under_careers(): void
    {
        $graph = json_decode(json_encode((new JobSchema(new JobBoard('en')))->working('Careers', 'Working at Go4Database', 'About us')), true);
        $nodes = collect($graph['@graph'])->keyBy('@type');

        $this->assertSame('Working at Go4Database', $nodes['AboutPage']['name']);
        $this->assertSame('Organization', $nodes['AboutPage']['about']['@type']);
        $this->assertSame(['Home', 'Careers', 'Working at Go4Database'], array_column($nodes['BreadcrumbList']['itemListElement'], 'name'));
    }
}
