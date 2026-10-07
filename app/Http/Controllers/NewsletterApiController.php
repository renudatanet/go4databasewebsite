<?php

namespace App\Http\Controllers;

use App\Newsletter;
use Illuminate\Http\Request;

/**
 * Lets the Subscriber Agent (go4database.in/subscriber-agent) read the website's
 * newsletter subscribers. Read-only, emails and sign-up dates only.
 *
 * Protected by a shared secret: the agent sends "Authorization: Bearer <key>"
 * matching SUBSCRIBER_AGENT_TOKEN in .env. With no token set, it refuses every request.
 */
class NewsletterApiController extends Controller
{
    public function index(Request $request)
    {
        $token = (string) config('services.subscriber_agent.token');
        $sent = (string) ($request->bearerToken() ?: $request->header('X-Api-Key'));
        if ($token === '' || !hash_equals($token, $sent)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }
        $rows = Newsletter::orderBy('id')->get(['id', 'email', 'created_at']);
        $subscribers = [];
        foreach ($rows as $row) {
            $email = strtolower(trim((string) $row->email));
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $subscribers[] = [
                'id' => 'website-' . $row->id,
                'email' => $email,
                'subscribed_at' => optional($row->created_at)->toIso8601String(),
                'subscribed_tracks' => ['newsletter'],
            ];
        }
        return response()->json(['total' => count($subscribers), 'subscribers' => $subscribers]);
    }
}
