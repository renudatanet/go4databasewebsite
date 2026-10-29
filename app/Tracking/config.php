<?php

/*
|--------------------------------------------------------------------------
| Tracking module: fixed behaviour
|--------------------------------------------------------------------------
| Values here are engineering constants. Anything the site owner may want to
| change (on/off, excluded IPs, retention, consent mode, timezone, the app's
| host and secret) is a static option edited in Admin > Tracking > Settings,
| read through App\Tracking\Support\TrackingSettings.
*/

return [

    'routes' => [
        'collect' => 't/collect',
        'app_event' => 't/app-event',
        'admin_prefix' => 'admin-home/tracking',
    ],

    // A visit ends after this long without any activity.
    'session_timeout_minutes' => 30,

    // A one-page visit shorter than this, with no click, is a bounce.
    'bounce_engaged_ms' => 10000,

    // "Live now" means active within this window.
    'live_window_minutes' => 5,

    // Hard limits on what one collect request may carry.
    'limits' => [
        'events_per_batch' => 25,
        'requests_per_minute' => 240,
        'app_events_per_minute' => 120,
        'engaged_ms_max' => 6 * 60 * 60 * 1000,
    ],

    'cookies' => [
        // First-party visitor id. Readable by the tracker (it is handed to the
        // app on Sign up / Log in), so it is not HttpOnly.
        'visitor' => 'g4_vid',
        'visitor_days' => 730,
        // Set on any browser that opens the Tracking screens; that browser is
        // never tracked again, logged in or not.
        'opt_out' => 'g4_notrack',
        // Written by the site's GDPR banner (jquery.ihavecookies) on accept.
        'consent' => 'cookieControl',
    ],

    'geo' => [
        'directory' => storage_path('app/tracking/geo'),
        'files' => [
            'city' => 'dbip-city-lite.mmdb',
            'asn' => 'dbip-asn-lite.mmdb',
        ],
        // %1$s = city|asn, %2$s = YYYY-MM. Free, CC BY 4.0, credit DB-IP.
        'download_url' => 'https://download.db-ip.com/free/dbip-%1$s-lite-%2$s.mmdb.gz',
    ],

    // EU, EEA, UK and Switzerland: visitors here are counted without a cookie
    // until they accept the cookie banner (consent mode "eu_consent").
    'consent_countries' => [
        'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR',
        'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK',
        'SI', 'ES', 'SE', 'IS', 'LI', 'NO', 'GB', 'CH',
    ],

    // Report results are cached this long; the Live screen is never cached.
    'report_cache_seconds' => 120,

    'max_report_days' => 400,
];
