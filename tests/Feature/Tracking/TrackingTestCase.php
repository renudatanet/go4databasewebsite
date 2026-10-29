<?php

namespace Tests\Feature\Tracking;

use App\Tracking\Contracts\GeoLocator;
use App\Tracking\Contracts\OptionStore;
use App\Tracking\Data\GeoResult;
use App\Tracking\Support\ArrayOptionStore;
use App\Tracking\Support\TrackingSettings;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Feature tests run against an in-memory SQLite database holding only the
 * tracking tables, with settings in memory and a fake location lookup.
 */
abstract class TrackingTestCase extends TestCase
{
    protected const UA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36';
    protected const SITE = 'http://localhost';

    protected ArrayOptionStore $options;
    /** @var array<string, GeoResult> */
    protected array $places = [];

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => self::SITE, 'cache.default' => 'array']);
        $this->artisan('migrate:fresh', ['--path' => 'app/Tracking/Database/Migrations', '--force' => true]);

        $this->options = new ArrayOptionStore();
        $this->app->instance(OptionStore::class, $this->options);
        $this->app->instance(TrackingSettings::class, new TrackingSettings($this->options));

        $places = &$this->places;
        $this->app->instance(GeoLocator::class, new class ($places) implements GeoLocator {
            public function __construct(private array &$places)
            {
            }

            public function locate(string $ip): GeoResult
            {
                return $this->places[$ip] ?? GeoResult::unknown();
            }
        });
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    /** Sends one tracker request, as the browser would. */
    protected function collect(array $events, array $overrides = [], array $server = []): TestResponse
    {
        $body = $overrides + [
            'version' => 1,
            'visitor' => null,
            'page_view' => (string) Str::uuid(),
            'consent' => false,
            'url' => self::SITE . '/',
            'title' => 'Home',
            'referrer' => null,
            'device' => ['width' => 1440, 'height' => 900, 'language' => 'en-US', 'touch' => false, 'automated' => false],
            'events' => $events,
        ];

        return $this->call('POST', '/t/collect', [], $overrides['_cookies'] ?? [], [], $server + [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_USER_AGENT' => self::UA,
            'REMOTE_ADDR' => '203.0.113.7',
        ], json_encode(array_diff_key($body, ['_cookies' => true])));
    }

    /** A page view by $visitor; returns the page view id. */
    protected function viewPage(string $visitor, string $path = '/', array $overrides = []): string
    {
        $pageView = (string) Str::uuid();
        $this->collect([['type' => 'pageview']], $overrides + [
            'visitor' => $visitor,
            'page_view' => $pageView,
            'url' => self::SITE . $path,
            'title' => 'Title of ' . $path,
        ])->assertStatus(202);

        return $pageView;
    }

    protected function travelToTime(string $time): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse($time, 'UTC'));
        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::parse($time, 'UTC'));
    }
}
