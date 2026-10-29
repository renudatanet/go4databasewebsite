<?php

namespace Tests\Feature\Tracking;

use App\Tracking\Data\GeoResult;
use App\Tracking\Models\Event;
use App\Tracking\Models\PageView;
use App\Tracking\Models\Session;
use App\Tracking\Models\Visitor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CollectEndpointTest extends TrackingTestCase
{
    public function test_a_first_page_view_creates_the_visitor_their_visit_and_their_cookie(): void
    {
        $this->places['203.0.113.7'] = new GeoResult('US', 'United States', 'Texas', 'Austin', 30.27, -97.74, 64500, 'Acme Corp', 'business');
        $visitor = (string) Str::uuid();

        $response = $this->collect([['type' => 'pageview']], [
            'visitor' => $visitor,
            'url' => self::SITE . '/pricing?utm_source=linkedin&utm_medium=social&utm_campaign=oct',
            'referrer' => 'https://www.linkedin.com/feed/',
        ]);

        $response->assertStatus(202)->assertJson(['mode' => 'full']);
        $cookie = $response->getCookie('g4_vid', false);
        $this->assertSame($visitor, $cookie->getValue());
        $this->assertFalse($cookie->isHttpOnly(), 'the tracker must be able to read it');

        $session = Session::sole();
        $this->assertSame(['social', 'linkedin', 'oct'], [$session->channel, $session->source, $session->campaign]);
        $this->assertSame(['US', 'Texas', 'Austin', 'Acme Corp', 'business'], [$session->country_code, $session->region, $session->city, $session->network_org, $session->network_type]);
        $this->assertSame(['203.0.113.7', '203.0.113.0'], [$session->ip, $session->ip_masked]);
        $this->assertSame(['desktop', 'Chrome', 'macOS'], [$session->device_type, $session->browser, $session->os]);
        $this->assertSame(1, $session->page_views);
        $this->assertSame('/pricing', $session->landingPage->path);

        $person = Visitor::sole();
        $this->assertSame([1, 1, 'social'], [$person->sessions_count, $person->page_views_count, $person->first_channel]);
        $this->assertTrue(PageView::sole()->is_entrance);
    }

    public function test_the_same_page_view_sent_twice_is_counted_once(): void
    {
        $visitor = (string) Str::uuid();
        $pageView = (string) Str::uuid();

        $this->collect([['type' => 'pageview']], ['visitor' => $visitor, 'page_view' => $pageView])->assertStatus(202);
        $this->collect([['type' => 'pageview']], ['visitor' => $visitor, 'page_view' => $pageView])->assertStatus(202);

        $this->assertSame(1, PageView::count());
        $this->assertSame(1, Session::sole()->page_views);
    }

    public function test_pages_in_one_visit_are_linked_in_order(): void
    {
        $visitor = (string) Str::uuid();
        $this->travelToTime('2026-10-29 10:00:00');
        $this->viewPage($visitor, '/');
        $this->travelToTime('2026-10-29 10:02:00');
        $this->viewPage($visitor, '/pricing');

        $session = Session::sole();
        $this->assertSame(2, $session->page_views);
        $this->assertSame('/', $session->landingPage->path);
        $this->assertSame('/pricing', $session->exitPage->path);

        $second = PageView::where('is_entrance', false)->sole();
        $this->assertSame($session->landing_page_id, $second->previous_page_id);
    }

    public function test_a_new_visit_starts_after_30_quiet_minutes(): void
    {
        $visitor = (string) Str::uuid();
        $this->travelToTime('2026-10-29 10:00:00');
        $this->viewPage($visitor, '/');
        $this->travelToTime('2026-10-29 10:29:00');
        $this->viewPage($visitor, '/blog');
        $this->travelToTime('2026-10-29 11:05:00');
        $this->viewPage($visitor, '/pricing');

        $this->assertSame(2, Session::count());
        $this->assertSame(2, Visitor::sole()->sessions_count);
        $this->assertSame(3, Visitor::sole()->page_views_count);
    }

    public function test_engaged_time_only_grows_and_repeats_add_nothing(): void
    {
        $visitor = (string) Str::uuid();
        $pageView = $this->viewPage($visitor);

        foreach ([[12000, 40], [30000, 80], [30000, 80], [20000, 50]] as [$ms, $scroll]) {
            $this->collect([['type' => 'engagement', 'engaged_ms' => $ms, 'scroll' => $scroll]], ['visitor' => $visitor, 'page_view' => $pageView])->assertStatus(202);
        }

        $this->assertSame([30000, 80], [PageView::sole()->engaged_ms, PageView::sole()->max_scroll]);
        $this->assertSame(30000, Session::sole()->engaged_ms);
    }

    public function test_time_reported_after_a_visit_went_quiet_joins_that_visit_instead_of_starting_one(): void
    {
        $visitor = (string) Str::uuid();
        $this->travelToTime('2026-10-29 10:00:00');
        $pageView = $this->viewPage($visitor);
        $this->travelToTime('2026-10-29 11:00:00'); // tab left open, then closed

        $this->collect([['type' => 'engagement', 'engaged_ms' => 45000, 'scroll' => 90]], ['visitor' => $visitor, 'page_view' => $pageView])->assertStatus(202);

        $this->assertSame(1, Session::count());
        $this->assertSame(45000, Session::sole()->engaged_ms);
    }

    public function test_clicks_are_named_by_destination_and_cleaned(): void
    {
        $visitor = (string) Str::uuid();
        $pageView = $this->viewPage($visitor, '/');

        $this->collect([
            ['type' => 'click', 'href' => "https://app.go4database.com/register?utm_campaign=hero&g4v={$visitor}&email=jane@acme.com", 'label' => 'Sign up free', 'section' => 'hero'],
            ['type' => 'click', 'href' => null, 'label' => 'Open FAQ', 'section' => 'faq'],
            ['type' => 'event', 'name' => 'view_email', 'properties' => ['position' => 2]],
        ], ['visitor' => $visitor, 'page_view' => $pageView])->assertStatus(202);

        $signup = Event::where('name', 'signup_click')->sole();
        $this->assertSame('https://app.go4database.com/register?utm_campaign=hero', $signup->link->href);
        $this->assertSame(['app', 'Sign up free', 'hero'], [$signup->link->kind, $signup->label, $signup->section]);
        $this->assertSame('/', $signup->page->path);

        $this->assertSame('button', Event::where('name', 'button_click')->sole()->link->kind);
        $this->assertSame(['position' => 2], Event::where('type', 'custom')->sole()->properties);

        $session = Session::sole();
        $this->assertSame([2, 3], [$session->clicks, $session->events_count]);
    }

    /** @dataProvider refusedRequests */
    public function test_refused_requests_store_nothing(callable $arrange, array $overrides, array $server): void
    {
        $arrange($this);

        $this->collect([['type' => 'pageview']], $overrides, $server)->assertStatus(204);

        $this->assertSame(0, DB::table('tracking_visitors')->count());
        $this->assertSame(0, DB::table('tracking_sessions')->count());
    }

    public static function refusedRequests(): array
    {
        $nothing = fn () => null;

        return [
            'a crawler' => [$nothing, [], ['HTTP_USER_AGENT' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)']],
            'an automated browser' => [$nothing, ['device' => ['automated' => true]], []],
            'a page on another site' => [$nothing, ['url' => 'https://copy.example/'], []],
            'a request from another site' => [$nothing, [], ['HTTP_ORIGIN' => 'https://evil.example']],
            'an opted-out browser' => [$nothing, ['_cookies' => ['g4_notrack' => '1']], []],
            'an excluded IP' => [fn ($t) => $t->options->set('tracking_excluded_ips', "198.51.100.1\n203.0.113.0/24"), [], []],
            'tracking switched off' => [fn ($t) => $t->options->set('tracking_status', '0'), [], []],
            'a malformed payload' => [$nothing, ['version' => 9], []],
        ];
    }

    public function test_eu_visitors_without_consent_are_counted_anonymously(): void
    {
        $this->places['203.0.113.7'] = new GeoResult('DE', 'Germany', 'Berlin', 'Berlin');
        $cookieVisitor = (string) Str::uuid();

        $response = $this->collect([['type' => 'pageview']], ['visitor' => $cookieVisitor]);

        $response->assertStatus(202)->assertJson(['mode' => 'anonymous']);
        $this->assertTrue($response->getCookie('g4_vid', false)->isCleared(), 'any old cookie is removed');

        $visitor = Visitor::sole();
        $this->assertTrue($visitor->is_anonymous);
        $this->assertNotSame($cookieVisitor, $visitor->uuid, 'the cookie id is not used');
        $session = Session::sole();
        $this->assertNull($session->ip, 'the full IP is never kept');
        $this->assertSame('203.0.113.0', $session->ip_masked);
        $this->assertSame('DE', $session->country_code);
    }

    public function test_eu_visitors_who_accepted_cookies_are_tracked_normally(): void
    {
        $this->places['203.0.113.7'] = new GeoResult('DE', 'Germany');
        $visitor = (string) Str::uuid();

        $this->collect([['type' => 'pageview']], ['visitor' => $visitor, 'consent' => true])
            ->assertStatus(202)->assertJson(['mode' => 'full']);

        $this->assertSame($visitor, Visitor::sole()->uuid);
        $this->assertSame('203.0.113.7', Session::sole()->ip);
    }

    public function test_the_tracker_is_rate_limited_per_ip(): void
    {
        config(['tracking.limits.requests_per_minute' => 3]);
        $visitor = (string) Str::uuid();

        foreach (range(1, 3) as $i) {
            $this->collect([['type' => 'pageview']], ['visitor' => $visitor])->assertStatus(202);
        }
        $this->collect([['type' => 'pageview']], ['visitor' => $visitor])->assertStatus(204);
        $this->assertSame(3, PageView::count());
    }
}
