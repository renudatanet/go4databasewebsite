# Tracking module

First-party visit and click tracking for www.go4database.com, shown in
Admin → Tracking. Every page is tracked, including pages added later: the
tracker is included once, from the shared footer.

What it records, per visit: pages viewed, engaged time and scroll depth per
page, every link and button clicked, the site's own GA4-style events, how the
visit arrived (search, AI assistant, social, email, campaign tags), where it
came from (country, region and city, plus the network owner from the IP),
and the device. Names and emails come only from what a visitor submits
(contact form, quote form, newsletter, job application) or from the app when they sign up or
log in. Nothing is guessed from an IP address.

## Layout

```
app/Tracking/
  TrackingServiceProvider.php   composition root: bindings, routes, views, migrations, schedule
  config.php                    fixed behaviour (limits, cookie names, timeouts)
  routes.php                    /t/collect, /t/app-event, admin-home/tracking/*
  Contracts/                    GeoLocator, DeviceParser, BotDetector, HitGuard, OptionStore
  Data/                         value objects: GeoResult, DeviceInfo, TrafficSource, NetworkType
  Geo/                          MmdbReader (DB-IP .mmdb files), city and network locators,
                                CompositeGeoLocator, MemoizedGeoLocator, NetworkClassifier
  Device/                       UserAgentParser, PatternBotDetector
  Ingestion/                    PayloadParser -> Guards/HitGate -> HitRecorder
                                (+ Enrichment/ steps run when a visit starts)
  Identity/                     VisitorIdentified event, AttachIdentity listener, IdentityRecorder,
                                FormIdentityExtractor, AppSignature
  Reports/                      one query class per question (Report interface), ReportRunner
                                (cache), ReportContext (period + filters), DateRange, Sql
  Maintenance/                  DataRetention, GeoDatabaseUpdater, VisitorEraser
  Console/                      tracking:prune, tracking:geo-update
  Http/                         CollectController, AppEventController, Admin/* screens,
                                CaptureIdentity + MarkBrowserAsInternal middleware
  Models/                       Visitor, Session, Page, PageView, Link, Event, Identity
  Database/Migrations/          the tracking_* tables, and the Super Admin permission
  resources/views/              tracker.blade.php (public script), admin/* (screens)
```

Outside this folder, the module touches only: `config/app.php` (provider),
`frontend/partials/footer.blade.php` (includes the tracker),
`routes/web.php` (`tracking.identify:*` on five form routes),
`backend/partials/sidebar.blade.php` (Tracking menu) and
`backend/user-role-manage/admin-role.blade.php` ("Tracking" permission).

## How a hit is stored

1. The tracker (`resources/views/tracker.blade.php`) starts after `load`,
   in idle time. It sends JSON to `POST /t/collect`: a `pageview` with fetch,
   then clicks, site events and `engagement` (running totals of engaged ms and
   max scroll) by beacon, every minute while the tab is in use and when it is
   hidden. It is not printed for logged-in admins.
2. `CollectController` runs outside the `web` group (no session, CSRF or
   cookie encryption). `PayloadParser` validates and caps everything; anything
   malformed gets `204`.
3. `HitGate` refuses: tracking off, opted-out browsers (`g4_notrack`, set on
   any browser that opens the Tracking screens), pages of another host,
   bots and automated browsers, and excluded IPs.
4. `HitRecorder` locates the IP, asks `ConsentPolicy` for the mode, then in
   one transaction locks the visitor row, finds the visit (new after 30 quiet
   minutes; only a page view starts one) and records each event. Page views
   are keyed by the browser's id, so replays are harmless.
5. Full mode: the server sets `g4_vid` (2 years, readable by the tracker).
   Anonymous mode (EU/UK/CH without consent, by default): no cookie, any old
   one is cleared, the full IP is not kept, and the visitor id is a daily
   keyed hash of IP + browser.

## Reports

Each figure on a screen comes from a class in `Reports/` implementing
`Report` (`key()`, `run(ReportContext)`). `ReportContext` supplies three
pre-filtered starting queries (`sessions()`, `pageViews()`, `events()`), so
every report respects the period and the toolbar filters the same way.
`ReportRunner` caches answers for 2 minutes; Live and visitor journeys are
never cached. Reports read the raw tables through composite indexes. There are
no summary tables: at this site's volume (thousands of rows a day) a 90-day
report runs in milliseconds. If volume grows a hundredfold, add daily
summary tables behind the same `Report` classes; the screens won't change.

Definitions: a visitor is one browser; a visit ends after 30 minutes without
activity; engaged time counts only while the tab is visible and in use; a
bounce is one page, no click, under 10 s engaged; click-through is visitors
who clicked ÷ visitors of that page; sign-up rate is visitors with a
`signup_click` (any link to the app's `/register` or `/sign-up`) ÷ visitors.

## Settings

Admin → Tracking → Settings, stored as static options:
`tracking_status`, `tracking_excluded_ips`, `tracking_consent_mode`
(`eu_consent` | `consent_everywhere` | `everyone`),
`tracking_ip_retention_days` (30), `tracking_data_retention_days` (395),
`tracking_timezone` (Asia/Kolkata), `tracking_app_host`, `tracking_app_secret`,
plus `tracking_last_prune_at` and `tracking_geo_updated_at` written by the jobs.

## Operating it

- Tables: `php artisan migrate --path=app/Tracking/Database/Migrations --force`
  (use the path, so no unrelated pending migration runs).
- Location files: `php artisan tracking:geo-update` downloads DB-IP City Lite
  and ASN Lite (free, CC BY 4.0, about 65 MB) into
  `storage/app/tracking/geo/` (git-ignored). Or Settings → Location files →
  Update now. Without them, visits are still counted, with no location.
- Scheduler: `tracking:prune` runs nightly (IP masking and retention), and
  `tracking:geo-update` runs monthly. Both need Laravel's scheduler in the
  **site user's** crontab, never root's: a root job creates cache folders the
  site can't write to, which broke the app's API on 2026-10-28.
  `* * * * * cd /path/to/@core && php artisan schedule:run >> /dev/null 2>&1`
- Tests: `vendor/bin/phpunit tests/Unit/Tracking` and
  `vendor/bin/phpunit --testsuite Feature --filter Tracking` (SQLite in
  memory; they also pass on MySQL in strict mode).

## Connecting the app (app.go4database.com)

**1. Handover.** When a visitor clicks a link to the app host (Settings →
App address), the tracker adds `g4v=<visitor id>` to it at click time. The
app should keep that value through registration and login (for example in
the session, or a hidden field on the register form).

**2. Message.** After a sign-up or login, the app sends one request,
server to server:

```
POST https://www.go4database.com/t/app-event
Content-Type: application/json
X-G4-Timestamp: <unix seconds>
X-G4-Signature: hex(HMAC-SHA256(secret, "<timestamp>.<raw body>"))

{
  "event": "signed_up",            // or "logged_in"; any [a-z0-9_] name is
                                   // recorded as a journey step
  "visitor_id": "<the g4v value>",
  "occurred_at": "2026-10-29T14:34:00Z",   // optional
  "user": {"id": 4821, "email": "jane@acme.com", "name": "Jane Doe", "company": "Acme"},
  "properties": {"plan": "free"}           // optional, scalar values only
}
```

The secret is made in Settings → Connection to the app. Responses:
`202 {"recorded": true}`; `202 {"recorded": false}` for a visitor id the site
doesn't know; `401` for a bad or stale signature (older than 5 minutes);
`409` for a signature already used; `422` for a malformed message.

Laravel example for the app:

```php
$body = json_encode([
    'event' => 'signed_up',
    'visitor_id' => session('g4v'),
    'user' => ['id' => $user->id, 'email' => $user->email, 'name' => $user->name],
]);
$timestamp = time();
Http::withHeaders([
    'X-G4-Timestamp' => $timestamp,
    'X-G4-Signature' => hash_hmac('sha256', $timestamp . '.' . $body, config('services.g4_tracking.secret')),
])->withBody($body, 'application/json')->timeout(3)->post('https://www.go4database.com/t/app-event');
```

Send it after the response or from a queue, and ignore failures: tracking must
never hold up a sign-up.

## Privacy

IP + cookie is personal data under GDPR and the California laws. In place:
consent mode, IP masking after 30 days, deletion after 395 days, "Forget this
visitor" on each profile, no email-like values in stored URLs or event
properties, and the DB-IP credit on every Tracking screen (required by its
licence). The privacy policy page should describe this; that text is an
admin content change, to be approved by whoever owns the policy.
