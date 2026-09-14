<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Local testing only: forwards the homepage lead search and contact reveal to
 * the app's website leads API from the server.
 *
 * In production the browser calls the API directly, but the API's CORS list
 * only allows https://www.go4database.com, so a local copy of the site can't.
 * Going through these routes makes the requests same-origin. The routes only
 * exist when APP_ENV=local and WEBSITE_LEADS_LOCAL_PROXY=true, see
 * config/services.php.
 */
class WebsiteLeadsLocalProxyController extends Controller
{
    public function search(Request $request)
    {
        return $this->forward(
            config('services.website_leads.url'),
            $request->only(['title', 'industry_business', 'location'])
        );
    }

    public function contact(Request $request)
    {
        return $this->forward(
            config('services.website_leads.url') . '/contact',
            $request->only(['token', 'type'])
        );
    }

    public static function enabled(): bool
    {
        return app()->environment('local') && config('services.website_leads.local_proxy');
    }

    private function forward(string $url, array $query)
    {
        abort_unless(self::enabled(), 404);

        try {
            $response = Http::acceptJson()
                ->timeout(60)
                // Local only, and never enabled in production: the app's
                // certificate can lapse, which shouldn't stop local testing.
                ->withOptions(['verify' => false])
                ->get($url, array_filter($query, 'strlen'));
        } catch (\Throwable $e) {
            Log::warning('Local website leads proxy could not reach ' . $url . ' : ' . $e->getMessage());

            return response()->json(['message' => 'Could not reach the website leads API.'], 502);
        }

        // Pass the status through untouched: the page reacts to 404 (expired
        // results) and 429 (reveal limit) differently.
        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type') ?: 'application/json');
    }
}
