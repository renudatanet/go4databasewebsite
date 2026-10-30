<?php

namespace Tests\Feature\Careers;

use App\Careers\JobBoard;
use App\Careers\JobSchema;

class JobSchemaTest extends CareersTestCase
{
    private function nodes(array $graph): array
    {
        // Round-trip through JSON, as the page prints it.
        $decoded = json_decode(json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('https://schema.org', $decoded['@context']);

        return collect($decoded['@graph'])->keyBy('@type')->all();
    }

    public function test_an_open_role_has_a_complete_job_posting(): void
    {
        $team = $this->category('Business Success');
        $job = $this->job('B2B Sales Leader', ['category_id' => $team->id, 'vacancy' => '2', 'deadline' => '2099-03-31']);
        $this->setOption('job_single_page_apply_form', 'on');

        $nodes = $this->nodes((new JobSchema(new JobBoard('en')))->role($job, 'Careers', ['What you’ll do' => '<ul><li>Close deals</li></ul>']));
        $posting = $nodes['JobPosting'];

        $this->assertSame('B2B Sales Leader', $posting['title']);
        $this->assertSame('<h2>What you’ll do</h2><ul><li>Close deals</li></ul>', $posting['description']);
        $this->assertSame('FULL_TIME', $posting['employmentType']);
        $this->assertStringStartsWith('2099-03-31T23:59:59', $posting['validThrough']);
        $this->assertSame(['@type' => 'MonetaryAmount', 'currency' => 'INR', 'value' => ['@type' => 'QuantitativeValue', 'minValue' => 25000, 'maxValue' => 60000, 'unitText' => 'MONTH']], $posting['baseSalary']);
        $this->assertSame(['@type' => 'PostalAddress', 'streetAddress' => 'Sector 2, Noida', 'addressLocality' => 'Noida', 'addressRegion' => 'Uttar Pradesh', 'postalCode' => '201301', 'addressCountry' => 'IN'], $posting['jobLocation']['address']);
        $this->assertSame(2, $posting['totalJobOpenings']);
        $this->assertSame('Business Success', $posting['occupationalCategory']);
        $this->assertTrue($posting['directApply']);
        $this->assertSame(['Home', 'Careers', 'Business Success', 'B2B Sales Leader'], array_column($nodes['BreadcrumbList']['itemListElement'], 'name'));
    }

    public function test_the_admin_address_settings_replace_the_defaults(): void
    {
        $this->setOption('career_schema_locality', 'Gurugram');
        $this->setOption('career_schema_country', 'in');
        $this->setOption('career_salary_currency', 'usd');
        $job = $this->job('Remote Role', ['salary' => 'Good hike from current']);

        $posting = $this->nodes((new JobSchema(new JobBoard('en')))->role($job, 'Careers', []))['JobPosting'];

        $this->assertSame('Gurugram', $posting['jobLocation']['address']['addressLocality']);
        $this->assertSame('IN', $posting['jobLocation']['address']['addressCountry']);
        $this->assertArrayNotHasKey('baseSalary', $posting);
        $this->assertSame('Remote Role', $posting['description']);
    }

    public function test_a_closed_role_keeps_only_its_breadcrumb(): void
    {
        $job = $this->job('Old Role', ['deadline' => '2020-01-01']);

        $nodes = $this->nodes((new JobSchema(new JobBoard('en')))->role($job, 'Careers', []));

        $this->assertSame(['BreadcrumbList'], array_keys($nodes));
    }

    public function test_the_board_lists_its_roles(): void
    {
        $this->job('One');
        $this->job('Two </script><script>alert(1)</script>');
        $board = new JobBoard('en');

        $graph = (new JobSchema($board))->board('Careers', $board->openJobs());
        $nodes = $this->nodes($graph);

        $this->assertSame(2, $nodes['CollectionPage']['mainEntity']['numberOfItems']);
        $this->assertStringNotContainsString('</script>', json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG));
    }
}
