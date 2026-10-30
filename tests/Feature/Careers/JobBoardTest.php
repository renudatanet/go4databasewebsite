<?php

namespace Tests\Feature\Careers;

use App\Careers\JobBoard;

class JobBoardTest extends CareersTestCase
{
    public function test_only_published_open_roles_in_this_language_are_listed_newest_first(): void
    {
        $older = $this->job('Older Role');
        $newer = $this->job('Newer Role');
        $this->job('Closed Role', ['deadline' => now()->subDay()->toDateString()]);
        $this->job('Draft Role', ['status' => 'draft']);
        $this->job('Hindi Role', ['lang' => 'hi']);

        $this->assertSame([$newer->id, $older->id], (new JobBoard('en'))->openJobs()->pluck('id')->all());
    }

    public function test_categories_count_open_roles_and_hide_empty_ones(): void
    {
        $sales = $this->category('Sales');
        $tech = $this->category('Technology And AI', ['slug' => 'technology-and-ai']);
        $this->category('Finance');
        $this->job('A', ['category_id' => $sales->id]);
        $this->job('B', ['category_id' => $sales->id]);
        $this->job('C', ['category_id' => $tech->id]);
        $this->job('D', ['category_id' => $tech->id, 'deadline' => now()->subWeek()->toDateString()]);

        $categories = (new JobBoard('en'))->categoriesWithRoles();

        $this->assertSame(['Sales' => 2, 'Technology And AI' => 1], $categories->pluck('open_count', 'title')->all());
    }

    public function test_a_category_is_found_by_its_slug_or_by_the_one_made_from_its_name(): void
    {
        $stored = $this->category('Legal - R&D', ['slug' => 'legal-randd']);
        $made = $this->category('People Management');
        $board = new JobBoard('en');

        $this->assertTrue($stored->is($board->findCategory('legal-randd')));
        $this->assertTrue($made->is($board->findCategory('people-management')));
        $this->assertNull($board->findCategory('legal-rd'));
    }

    public function test_a_published_role_wins_over_an_older_draft_with_the_same_slug(): void
    {
        $this->job('Inside Sales Executive', ['status' => 'draft', 'category_id' => '999']);
        $published = $this->job('Inside Sales Executive');
        $board = new JobBoard('en');

        $this->assertTrue($published->is($board->findJob('inside-sales-executive')));
        $this->assertNull($board->findJob('nothing-here'));
    }

    public function test_drafts_are_found_only_for_an_admin_preview(): void
    {
        $draft = $this->job('Draft Role', ['status' => 'draft']);
        $board = new JobBoard('en');

        $this->assertNull($board->findJob('draft-role'));
        $this->assertTrue($draft->is($board->findJob('draft-role', true)));
    }

    public function test_a_role_whose_category_was_deleted_has_none(): void
    {
        $job = $this->job('Orphan', ['category_id' => '999']);

        $this->assertNull((new JobBoard('en'))->categoryOf($job));
    }

    public function test_search_matches_every_word_across_title_location_and_team(): void
    {
        $sales = $this->category('Business Success');
        $this->job('Inside Sales Executive', ['category_id' => $sales->id]);
        $this->job('SEO Specialist', ['job_location' => 'Delhi NCR']);
        $board = new JobBoard('en');

        $this->assertSame(['Inside Sales Executive'], $board->search('sales noida')->pluck('title')->all());
        $this->assertSame(['Inside Sales Executive'], $board->search('business success')->pluck('title')->all());
        $this->assertSame(['SEO Specialist'], $board->search('delhi')->pluck('title')->all());
        $this->assertCount(0, $board->search('designer'));
        $this->assertCount(2, $board->search('  '));
    }

    public function test_related_roles_put_the_same_team_first(): void
    {
        $sales = $this->category('Sales');
        $other = $this->job('Other Team');
        $teammate = $this->job('Teammate', ['category_id' => $sales->id]);
        $job = $this->job('This Role', ['category_id' => $sales->id]);

        $this->assertSame([$teammate->id, $other->id], (new JobBoard('en'))->related($job)->pluck('id')->all());
    }

    public function test_new_category_slugs_avoid_jobs_and_other_categories(): void
    {
        $this->job('Sales');
        $this->category('Sales Team', ['slug' => 'sales-2']);

        $this->assertSame('sales-3', JobBoard::uniqueCategorySlug('Sales'));
        $this->assertSame('marketing', JobBoard::uniqueCategorySlug('Marketing'));
    }
}
