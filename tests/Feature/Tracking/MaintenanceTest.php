<?php

namespace Tests\Feature\Tracking;

use App\Tracking\Maintenance\DataRetention;
use App\Tracking\Maintenance\VisitorEraser;
use App\Tracking\Models\Visitor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaintenanceTest extends TrackingTestCase
{
    public function test_retention_masks_old_ips_then_deletes_old_visits(): void
    {
        $this->options->set('tracking_ip_retention_days', '30');
        $this->options->set('tracking_data_retention_days', '90');

        $this->travelToTime('2026-06-01 10:00:00');
        $this->collect([['type' => 'pageview']], ['visitor' => (string) Str::uuid()]);
        $this->travelToTime('2026-09-20 10:00:00');
        $this->collect([['type' => 'pageview']], ['visitor' => (string) Str::uuid()]);
        $this->travelToTime('2026-10-28 10:00:00');
        $this->collect([['type' => 'pageview']], ['visitor' => (string) Str::uuid()]);
        $this->travelToTime('2026-10-29 03:30:00');

        $result = app(DataRetention::class)->run();

        $this->assertSame(['ips_masked' => 1, 'sessions_deleted' => 1, 'visitors_deleted' => 1], $result);
        $this->assertSame(2, DB::table('tracking_sessions')->count());
        $this->assertSame(2, DB::table('tracking_page_views')->count());
        $this->assertSame([null, '203.0.113.7'], DB::table('tracking_sessions')->orderBy('started_at')->pluck('ip')->all());
        $this->assertNotNull($this->options->get('tracking_last_prune_at'));
    }

    public function test_forgetting_a_visitor_removes_everything_about_them(): void
    {
        $visitor = (string) Str::uuid();
        $pageView = $this->viewPage($visitor);
        $this->collect([['type' => 'click', 'href' => '/pricing', 'label' => 'Pricing']], ['visitor' => $visitor, 'page_view' => $pageView]);
        $other = (string) Str::uuid();
        $this->viewPage($other);

        app(VisitorEraser::class)->erase(Visitor::where('uuid', $visitor)->sole());

        $this->assertSame([$other], DB::table('tracking_visitors')->pluck('uuid')->all());
        $this->assertSame(1, DB::table('tracking_sessions')->count());
        $this->assertSame(0, DB::table('tracking_events')->count());
    }

    public function test_the_commands_are_registered_and_scheduled(): void
    {
        $this->artisan('tracking:prune')->assertSuccessful();

        $events = collect(app(\Illuminate\Console\Scheduling\Schedule::class)->events())->map->command->implode(' ');
        $this->assertStringContainsString('tracking:prune', $events);
        $this->assertStringContainsString('tracking:geo-update', $events);
    }
}
