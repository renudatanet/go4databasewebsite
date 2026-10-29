<?php

namespace Tests\Feature\Tracking;

use App\Tracking\Data\GeoResult;
use App\Tracking\Reports\DateRange;
use App\Tracking\Reports\GeoTable;
use App\Tracking\Reports\LinkTable;
use App\Tracking\Reports\LiveVisitors;
use App\Tracking\Reports\NetworkTable;
use App\Tracking\Reports\OverviewTotals;
use App\Tracking\Reports\PageFlow;
use App\Tracking\Reports\PageSummary;
use App\Tracking\Reports\PageTable;
use App\Tracking\Reports\ReportContext;
use App\Tracking\Reports\ReportFilters;
use App\Tracking\Reports\SourceTable;
use App\Tracking\Reports\TrafficSeries;
use App\Tracking\Reports\VisitorJourney;
use App\Tracking\Reports\VisitorTable;
use App\Tracking\Models\Page;
use App\Tracking\Models\Visitor;
use Carbon\CarbonImmutable;
use DateTimeZone;
use Illuminate\Support\Str;

/**
 * One small, known day of traffic, and the figures every report should
 * give for it. Three visitors on 28 Oct 2026 (UTC):
 *  - Ann  (Austin, Acme's network, from Google): / -> /pricing -> clicks Sign up, 40s engaged
 *  - Bob  (Mumbai, Jio, from LinkedIn campaign "oct"): / only, 3s, no click (a bounce)
 *  - Cara (Austin, Acme's network, direct, on a phone): /blog -> /pricing, 20s
 */
class ReportsTest extends TrackingTestCase
{
    private ReportContext $context;
    private array $visitors = [];

    protected function setUp(): void
    {
        parent::setUp();

        $austin = new GeoResult('US', 'United States', 'Texas', 'Austin', 30.27, -97.74, 64500, 'Acme Corp', 'business');
        $mumbai = new GeoResult('IN', 'India', 'Maharashtra', 'Mumbai', 19.07, 72.87, 55836, 'Reliance Jio Infocomm Limited', 'isp');
        $this->places = ['198.51.100.1' => $austin, '198.51.100.2' => $mumbai, '198.51.100.3' => $austin];

        $this->travelToTime('2026-10-28 10:00:00');
        $this->visit('ann', '198.51.100.1', self::UA, [['/', 'https://www.google.com/', 10000], ['/pricing', null, 30000]], 'https://app.go4database.com/register');
        $this->travelToTime('2026-10-28 11:00:00');
        $this->visit('bob', '198.51.100.2', self::UA, [['/?utm_source=linkedin&utm_medium=social&utm_campaign=oct', 'https://www.linkedin.com/', 3000]]);
        $this->travelToTime('2026-10-28 12:00:00');
        $phone = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Mobile/15E148 Safari/604.1';
        $this->visit('cara', '198.51.100.3', $phone, [['/blog', null, 12000], ['/pricing', null, 8000]]);

        $this->travelToTime('2026-10-29 09:00:00');
        $range = DateRange::fromPreset('custom', new DateTimeZone('UTC'), '2026-10-28', '2026-10-28');
        $this->context = new ReportContext($range, new ReportFilters(), 10000);
    }

    /** @param list<array{0: string, 1: ?string, 2: int}> $pages path (with query), referrer, engaged ms */
    private function visit(string $who, string $ip, string $ua, array $pages, ?string $clickHref = null): void
    {
        $visitor = (string) Str::uuid();
        $this->visitors[$who] = $visitor;
        $server = ['REMOTE_ADDR' => $ip, 'HTTP_USER_AGENT' => $ua];

        foreach ($pages as [$path, $referrer, $ms]) {
            $pageView = (string) Str::uuid();
            $base = ['visitor' => $visitor, 'page_view' => $pageView, 'url' => self::SITE . $path, 'referrer' => $referrer, 'title' => "Title {$path}"];
            $this->collect([['type' => 'pageview']], $base, $server)->assertStatus(202);
            $this->collect([['type' => 'engagement', 'engaged_ms' => $ms, 'scroll' => 50]], $base, $server)->assertStatus(202);
        }

        if ($clickHref !== null) {
            $this->collect([['type' => 'click', 'href' => $clickHref, 'label' => 'Sign up free', 'section' => 'pricing-hero']], $base, $server)->assertStatus(202);
        }
    }

    public function test_overview_totals(): void
    {
        $totals = (new OverviewTotals())->run($this->context);

        $this->assertSame(3, $totals['visitors']);
        $this->assertSame(3, $totals['sessions']);
        $this->assertSame(5, $totals['page_views']);
        $this->assertSame(1, $totals['clicks']);
        $this->assertSame(1, $totals['signup_visitors']);
        $this->assertSame(0.3333, $totals['signup_rate']);
        $this->assertSame(0.3333, $totals['bounce_rate'], 'only Bob bounced');
        $this->assertSame(21000, $totals['avg_engaged_ms']);
    }

    public function test_filters_narrow_every_report(): void
    {
        $inIndia = new ReportContext($this->context->range, new ReportFilters(country: 'IN'), 10000);
        $this->assertSame(1, (new OverviewTotals())->run($inIndia)['visitors']);

        $onPhones = new ReportContext($this->context->range, new ReportFilters(device: 'mobile'), 10000);
        $this->assertSame(['/blog', '/pricing'], array_column((new PageTable())->run($onPhones)['rows'], 'path'), 'only Cara was on a phone');
    }

    public function test_pages_table(): void
    {
        $table = (new PageTable())->run($this->context);
        $rows = collect($table['rows'])->keyBy('path');

        $this->assertSame(3, $table['total']);
        $this->assertSame(['/', '/pricing', '/blog'], array_column($table['rows'], 'path'), 'most viewed first; ties in path order');
        $this->assertSame(2, $rows['/pricing']['views']);
        $this->assertSame(2, $rows['/pricing']['exits']);
        $this->assertSame(1, $rows['/pricing']['clicks']);
        $this->assertSame(0.5, $rows['/pricing']['ctr']);
        $this->assertSame(2, $rows['/']['entrances']);
        $this->assertSame(6500, $rows['/']['avg_engaged_ms']);
    }

    public function test_one_page_summary_and_flow(): void
    {
        $pricing = Page::where('path', '/pricing')->sole()->id;

        $summary = (new PageSummary($pricing))->run($this->context);
        $this->assertSame([2, 2, 0, 2, 1.0], [$summary['views'], $summary['visitors'], $summary['entrances'], $summary['exits'], $summary['exit_rate']]);

        $flow = (new PageFlow($pricing))->run($this->context);
        $this->assertEqualsCanonicalizing(['/', '/blog'], array_column($flow['previous_pages'], 'path'));
        $this->assertSame([], $flow['next_pages']);
        $this->assertSame(2, $flow['exits']);

        $home = Page::where('path', '/')->sole()->id;
        $homeFlow = (new PageFlow($home))->run($this->context);
        $this->assertSame(['/pricing'], array_column($homeFlow['next_pages'], 'path'));
        $this->assertEqualsCanonicalizing(['Google', 'linkedin'], array_column($homeFlow['sources'], 'source'));
    }

    public function test_links_table_with_click_through_for_one_page(): void
    {
        $pricing = Page::where('path', '/pricing')->sole()->id;
        $links = (new LinkTable(pageId: $pricing))->run($this->context);

        $this->assertSame(1, $links['total']);
        $this->assertSame(['Sign up free', 'signup_click', 1, 0.5], [$links['rows'][0]['label'], $links['rows'][0]['name'], $links['rows'][0]['clicks'], $links['rows'][0]['ctr']]);
    }

    public function test_traffic_series_has_every_hour_of_a_one_day_range(): void
    {
        $series = (new TrafficSeries())->run($this->context);

        $this->assertCount(24, $series);
        $this->assertSame([1, 1, 1], [$series[10]['visitors'], $series[11]['visitors'], $series[12]['visitors']]);
        $this->assertSame(3, array_sum(array_column($series, 'sessions')));
    }

    public function test_traffic_series_uses_the_report_timezone(): void
    {
        $kolkata = new ReportContext(DateRange::fromPreset('custom', new DateTimeZone('Asia/Kolkata'), '2026-10-28', '2026-10-28'), new ReportFilters(), 10000);
        $series = (new TrafficSeries())->run($kolkata);

        // 10:00 UTC is 15:30 in Kolkata.
        $this->assertSame(1, $series[15]['visitors']);
        $this->assertSame(0, $series[10]['visitors']);
    }

    public function test_visitors_locations_networks_and_sources(): void
    {
        $visitors = (new VisitorTable())->run($this->context);
        $this->assertSame(3, $visitors['total']);
        $this->assertSame('Austin', $visitors['rows'][0]['city'], 'Cara was last active');
        $this->assertSame(1, (new VisitorTable(segment: 'signup'))->run($this->context)['total']);
        $this->assertSame(2, (new VisitorTable(segment: 'business'))->run($this->context)['total']);
        $this->assertSame(1, (new VisitorTable(search: 'jio'))->run($this->context)['total']);

        $countries = collect((new GeoTable())->run($this->context))->keyBy('country_code');
        $this->assertSame([2, 1], [$countries['US']['visitors'], $countries['US']['signups']]);
        $this->assertSame('Austin', (new GeoTable('US', 'Texas'))->run($this->context)[0]['name']);

        $networks = (new NetworkTable())->run($this->context);
        $this->assertSame([['Acme Corp', 2]], array_map(fn ($r) => [$r['network_org'], $r['visitors']], $networks));

        $channels = collect((new SourceTable())->run($this->context))->keyBy('channel');
        $this->assertSame(1, $channels['search']['signups']);
        $this->assertSame(1.0, $channels['social']['bounce_rate']);
        $this->assertSame('oct', (new SourceTable(SourceTable::CAMPAIGN))->run($this->context)[0]['campaign']);
    }

    public function test_a_visitor_journey_lists_every_step_in_order(): void
    {
        $ann = Visitor::where('uuid', $this->visitors['ann'])->sole();
        $journey = (new VisitorJourney())->run($ann->id);

        $steps = $journey['sessions'][0]['steps'];
        $this->assertSame(['pageview', 'pageview', 'click'], array_column($steps, 'kind'));
        $this->assertSame('/pricing', $steps[1]['item']->page->path);
        $this->assertSame(['sessions' => 1, 'page_views' => 2, 'engaged_ms' => 40000, 'clicks' => 1], $journey['totals']);
        $this->assertNull((new VisitorJourney())->run(999999));
    }

    public function test_live_visitors(): void
    {
        $this->assertSame(0, (new LiveVisitors(5))->run()['count']);

        $live = (new LiveVisitors(5))->run(CarbonImmutable::parse('2026-10-28 12:03:00', 'UTC'));
        $this->assertSame(1, $live['count']);
        $this->assertSame('/pricing', $live['visitors'][0]['current_path']);
    }
}
