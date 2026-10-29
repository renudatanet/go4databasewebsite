<?php

namespace App\Tracking;

use App\Tracking\Console\PruneTrackingData;
use App\Tracking\Console\UpdateGeoDatabase;
use App\Tracking\Contracts\BotDetector;
use App\Tracking\Contracts\DeviceParser;
use App\Tracking\Contracts\GeoLocator;
use App\Tracking\Contracts\OptionStore;
use App\Tracking\Device\PatternBotDetector;
use App\Tracking\Device\UserAgentParser;
use App\Tracking\Geo\CompositeGeoLocator;
use App\Tracking\Geo\DbIpAsnLocator;
use App\Tracking\Geo\DbIpCityLocator;
use App\Tracking\Geo\MemoizedGeoLocator;
use App\Tracking\Geo\NetworkClassifier;
use App\Tracking\Http\Controllers\CollectController;
use App\Tracking\Http\Middleware\CaptureIdentity;
use App\Tracking\Http\ViewComposers\TrackingViewComposer;
use App\Tracking\Identity\AttachIdentity;
use App\Tracking\Identity\VisitorIdentified;
use App\Tracking\Ingestion\AnonymousId;
use App\Tracking\Ingestion\ConsentPolicy;
use App\Tracking\Ingestion\Enrichment\AddDevice;
use App\Tracking\Ingestion\Enrichment\AddLocation;
use App\Tracking\Ingestion\Enrichment\AddTrafficSource;
use App\Tracking\Ingestion\Guards\ExcludedIpGuard;
use App\Tracking\Ingestion\Guards\HitGate;
use App\Tracking\Ingestion\Guards\NotBotGuard;
use App\Tracking\Ingestion\Guards\NotOptedOutGuard;
use App\Tracking\Ingestion\Guards\SameSiteGuard;
use App\Tracking\Ingestion\Guards\TrackingEnabledGuard;
use App\Tracking\Ingestion\HitRecorder;
use App\Tracking\Ingestion\LinkClassifier;
use App\Tracking\Ingestion\PageRegistry;
use App\Tracking\Ingestion\LinkRegistry;
use App\Tracking\Ingestion\PayloadParser;
use App\Tracking\Maintenance\GeoDatabaseUpdater;
use App\Tracking\Reports\LiveVisitors;
use App\Tracking\Reports\ReportRunner;
use App\Tracking\Support\StaticOptionStore;
use App\Tracking\Support\TrackingSettings;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * The tracking module's composition root: every interface is bound to its
 * implementation here, and the routes, migrations, views, listeners,
 * commands and schedule are registered. Nothing outside app/Tracking
 * needs to know how the module is put together.
 */
final class TrackingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'tracking');

        $this->app->singleton(OptionStore::class, StaticOptionStore::class);
        $this->app->singleton(TrackingSettings::class);
        $this->app->singleton(DeviceParser::class, UserAgentParser::class);
        $this->app->singleton(BotDetector::class, PatternBotDetector::class);

        $this->app->singleton(GeoLocator::class, function (Application $app) {
            $geo = $app['config']['tracking.geo'];
            $classifier = new NetworkClassifier();

            return new MemoizedGeoLocator(new CompositeGeoLocator(
                new DbIpCityLocator($geo['directory'] . '/' . $geo['files']['city']),
                new DbIpAsnLocator($geo['directory'] . '/' . $geo['files']['asn'], $classifier),
            ));
        });

        $this->app->singleton(ConsentPolicy::class, fn (Application $app) => new ConsentPolicy(
            $app->make(TrackingSettings::class),
            $app['config']['tracking.consent_countries']
        ));

        $this->app->bind(LinkClassifier::class, fn (Application $app) => new LinkClassifier(
            (string) parse_url((string) $app['config']['app.url'], PHP_URL_HOST),
            $app->make(TrackingSettings::class)->appHost()
        ));

        $this->app->bind(AnonymousId::class, fn (Application $app) => new AnonymousId((string) $app['config']['app.key']));

        $this->app->bind(PayloadParser::class, fn (Application $app) => new PayloadParser(
            $app['config']['tracking.limits.events_per_batch'],
            $app['config']['tracking.limits.engaged_ms_max']
        ));

        $this->app->bind(HitGate::class, fn (Application $app) => new HitGate(
            $app->make(TrackingEnabledGuard::class),
            $app->make(NotOptedOutGuard::class),
            $app->make(SameSiteGuard::class),
            $app->make(NotBotGuard::class),
            $app->make(ExcludedIpGuard::class),
        ));

        $this->app->bind(HitRecorder::class, fn (Application $app) => new HitRecorder(
            $app->make(GeoLocator::class),
            $app->make(ConsentPolicy::class),
            $app->make(AnonymousId::class),
            $app->make(PageRegistry::class),
            $app->make(LinkRegistry::class),
            $app->make(LinkClassifier::class),
            $app->make(Pipeline::class),
            [AddLocation::class, AddDevice::class, AddTrafficSource::class],
            $app['config']['tracking.session_timeout_minutes'],
        ));

        $this->app->when(CollectController::class)->needs('$cookies')->giveConfig('tracking.cookies');
        $this->app->when(CaptureIdentity::class)->needs('$visitorCookie')->giveConfig('tracking.cookies.visitor');

        $this->app->bind(ReportRunner::class, fn (Application $app) => new ReportRunner(
            $app['cache.store'],
            $app['config']['tracking.report_cache_seconds']
        ));
        $this->app->bind(LiveVisitors::class, fn (Application $app) => new LiveVisitors($app['config']['tracking.live_window_minutes']));

        $this->app->bind(GeoDatabaseUpdater::class, fn (Application $app) => new GeoDatabaseUpdater(
            $app['config']['tracking.geo.directory'],
            $app['config']['tracking.geo.files'],
            $app['config']['tracking.geo.download_url'],
            $app->make(TrackingSettings::class),
        ));
    }

    public function boot(Router $router): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'tracking');
        View::composer('tracking::admin.*', TrackingViewComposer::class);

        $router->aliasMiddleware('tracking.identify', CaptureIdentity::class);
        $this->registerRateLimits();
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        // The tracker reads and writes these cookies as plain values.
        $this->app->resolving(EncryptCookies::class, function (EncryptCookies $middleware) {
            $middleware->disableFor(config('tracking.cookies.visitor'));
            $middleware->disableFor(config('tracking.cookies.opt_out'));
        });

        Event::listen(VisitorIdentified::class, AttachIdentity::class);

        if ($this->app->runningInConsole()) {
            $this->commands([PruneTrackingData::class, UpdateGeoDatabase::class]);
        }

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('tracking:prune')->dailyAt('03:30')->withoutOverlapping()->runInBackground();
            $schedule->command('tracking:geo-update')->monthlyOn(3, '04:15')->withoutOverlapping()->runInBackground();
        });
    }

    private function registerRateLimits(): void
    {
        RateLimiter::for('tracking-collect', fn (Request $request) => Limit::perMinute(config('tracking.limits.requests_per_minute'))
            ->by('tracking:' . $request->ip())
            ->response(fn () => response()->noContent()));

        RateLimiter::for('tracking-app', fn (Request $request) => Limit::perMinute(config('tracking.limits.app_events_per_minute'))
            ->by('tracking-app:' . $request->ip()));
    }
}
