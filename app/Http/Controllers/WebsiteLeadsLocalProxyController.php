<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Local testing only: forwards the homepage lead search to the app's website
 * leads API from the server.
 *
 * In production the browser calls the API directly, but the API's CORS list
 * only allows https://www.go4database.com, so a local copy of the site can't.
 * Going through this route makes the request same-origin. The route only
 * exists when APP_ENV=local and WEBSITE_LEADS_LOCAL_PROXY=true, see
 * config/services.php.
 */
class WebsiteLeadsLocalProxyController extends Controller
{
    public function search(Request $request)
    {
        abort_unless(self::enabled(), 404);

        $url = config('services.website_leads.url');
        $query = array_filter($request->only(['title', 'industry_business', 'location']), 'strlen');

        try {
            $response = Http::acceptJson()
                ->timeout(60)
                // Local only, and never enabled in production: the app's
                // certificate can lapse, which shouldn't stop local testing.
                ->withOptions(['verify' => false])
                ->get($url, $query);
        } catch (\Throwable $e) {
            Log::warning('Local website leads proxy could not reach ' . $url . ' : ' . $e->getMessage());

            return response()->json(['message' => 'Could not reach the website leads API.'], 502);
        }

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type') ?: 'application/json');
    }

    public static function enabled(): bool
    {
        return app()->environment('local') && config('services.website_leads.local_proxy');
    }
}
