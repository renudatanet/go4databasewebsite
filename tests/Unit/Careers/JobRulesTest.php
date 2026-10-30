<?php

namespace Tests\Unit\Careers;

use App\Jobs;
use Carbon\Carbon;
use Tests\TestCase;

/** The deadline, type and salary rules on a job, without a database. */
class JobRulesTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_a_role_is_open_through_its_whole_deadline_day(): void
    {
        config(['app.timezone' => 'UTC']);
        Carbon::setTestNow('2026-10-30 23:30:00');

        $this->assertTrue((new Jobs(['deadline' => '2026-10-30']))->isOpen());
        $this->assertFalse((new Jobs(['deadline' => '2026-10-29']))->isOpen());
    }

    public function test_an_unreadable_or_missing_deadline_means_no_deadline(): void
    {
        foreach (['', 'soon', null] as $deadline) {
            $job = new Jobs(['deadline' => $deadline]);
            $this->assertNull($job->closesAt());
            $this->assertTrue($job->isOpen());
        }
    }

    public function test_types_read_well_and_map_to_schema(): void
    {
        $this->assertSame('Full time', (new Jobs(['employment_status' => 'full_time']))->typeLabel());
        $this->assertSame('PART_TIME', (new Jobs(['employment_status' => 'part_time']))->schemaType());
        $this->assertSame('CONTRACTOR', (new Jobs(['employment_status' => 'project_based']))->schemaType());
        $this->assertNull((new Jobs(['employment_status' => 'other']))->schemaType());
    }

    /** @dataProvider salaries */
    public function test_salary_ranges(string $salary, ?array $expected): void
    {
        $this->assertSame($expected, (new Jobs(['salary' => $salary]))->salaryRange());
    }

    public static function salaries(): array
    {
        return [
            ['5k-15k', [5000, 15000]],
            ['25K-60K', [25000, 60000]],
            ['5 - 15K', [5000, 15000]],
            ['₹20,000', null],
            ['3L to 6L', [300000, 600000]],
            ['Good Hike from current + Attractive Incentive + Perk', null],
            ['60K-25K', null],
            ['', null],
        ];
    }

    public function test_the_application_fee_needs_both_switch_and_amount(): void
    {
        $this->assertTrue((new Jobs(['application_fee_status' => 'on', 'application_fee' => 10]))->hasApplicationFee());
        $this->assertFalse((new Jobs(['application_fee_status' => 'on', 'application_fee' => 0]))->hasApplicationFee());
        $this->assertFalse((new Jobs(['application_fee' => 10]))->hasApplicationFee());
    }
}
