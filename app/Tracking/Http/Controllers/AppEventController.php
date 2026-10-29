<?php

namespace App\Tracking\Http\Controllers;

use App\Tracking\Identity\AppSignature;
use App\Tracking\Identity\IdentityDetails;
use App\Tracking\Identity\VisitorIdentified;
use App\Tracking\Models\Event;
use App\Tracking\Models\Identity;
use App\Tracking\Models\Session;
use App\Tracking\Models\Visitor;
use App\Tracking\Support\TrackingSettings;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * POST /t/app-event — a signed message from app.go4database.com saying a
 * visitor signed up, logged in, or did something else worth a line in
 * their journey. See app/Tracking/README.md for the message format.
 */
final class AppEventController
{
    private const IDENTITY_EVENTS = ['signed_up' => Identity::APP_SIGNUP, 'logged_in' => Identity::APP_LOGIN];

    public function __construct(private TrackingSettings $settings)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $body = (string) $request->getContent();
        $signature = $request->header('X-G4-Signature');

        $valid = AppSignature::verify(
            $this->settings->appSecret(),
            $request->header('X-G4-Timestamp'),
            $signature,
            $body,
            time()
        );
        if (!$valid) {
            return response()->json(['error' => 'invalid signature'], 401);
        }

        // Each signature is accepted once while it is still within its time window.
        if (!Cache::add('tracking:app-signature:' . sha1((string) $signature), 1, AppSignature::TOLERANCE_SECONDS * 2)) {
            return response()->json(['error' => 'replayed'], 409);
        }

        $data = json_decode($body, true);
        $validator = Validator::make(is_array($data) ? $data : [], [
            'event' => ['required', 'string', 'regex:/^[a-z0-9_]{1,64}$/'],
            'visitor_id' => ['required', 'string', 'regex:/^[0-9a-f-]{36}$/i'],
            'occurred_at' => ['nullable', 'date'],
            'user' => ['array'],
            'user.id' => ['nullable'],
            'user.email' => ['nullable', 'string', 'max:191'],
            'user.name' => ['nullable', 'string', 'max:150'],
            'user.company' => ['nullable', 'string', 'max:150'],
            'properties' => ['array', 'max:12'],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'invalid message', 'fields' => $validator->errors()->keys()], 422);
        }

        $occurredAt = isset($data['occurred_at'])
            ? CarbonImmutable::parse($data['occurred_at'])->utc()
            : CarbonImmutable::now('UTC');
        $visitorUuid = strtolower($data['visitor_id']);

        $visitor = Visitor::where('uuid', $visitorUuid)->first();
        if ($visitor === null || $visitor->is_anonymous) {
            // Not a visitor this site knows (cookie cleared, or never tracked).
            return response()->json(['recorded' => false, 'reason' => 'unknown visitor'], 202);
        }

        $this->recordJourneyStep($visitor, $data['event'], $data['properties'] ?? [], $occurredAt);

        if (isset(self::IDENTITY_EVENTS[$data['event']])) {
            $user = $data['user'] ?? [];
            event(new VisitorIdentified($visitorUuid, self::IDENTITY_EVENTS[$data['event']], new IdentityDetails(
                name: $user['name'] ?? null,
                email: $user['email'] ?? null,
                company: $user['company'] ?? null,
                reference: isset($user['id']) && is_scalar($user['id']) ? (string) $user['id'] : null,
            ), $occurredAt));
        }

        return response()->json(['recorded' => true], 202);
    }

    /** The step is added to the visitor's latest visit, so it shows in their journey. */
    private function recordJourneyStep(Visitor $visitor, string $event, array $properties, CarbonImmutable $occurredAt): void
    {
        $session = Session::where('visitor_id', $visitor->id)->orderByDesc('last_activity_at')->first();
        if ($session === null) {
            return;
        }

        $clean = [];
        foreach ($properties as $key => $value) {
            if (is_string($key) && preg_match('/^[a-z0-9_]{1,64}$/i', $key) && is_scalar($value)) {
                $clean[$key] = is_string($value) ? mb_substr($value, 0, 150) : $value;
            }
        }

        DB::table('tracking_events')->insert([
            'session_id' => $session->id,
            'visitor_id' => $visitor->id,
            'type' => Event::TYPE_APP,
            'name' => $event,
            'properties' => $clean !== [] ? json_encode($clean) : null,
            'occurred_at' => $occurredAt,
        ]);
    }
}
