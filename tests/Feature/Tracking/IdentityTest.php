<?php

namespace Tests\Feature\Tracking;

use App\Tracking\Identity\AppSignature;
use App\Tracking\Models\Event;
use App\Tracking\Models\Identity;
use App\Tracking\Models\Visitor;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class IdentityTest extends TrackingTestCase
{
    private const SECRET = 'a3f1c0de5b7e49a8b2d6f0e1c4a7b9d2e5f8a1b4c7d0e3f6a9b2c5d8e1f4a7b0';

    protected function setUp(): void
    {
        parent::setUp();

        // Stand-ins for the site's forms, answering the way the real ones do.
        Route::post('/test/contact', fn () => response()->json(['status' => request('fail') ? '400' : '200']))
            ->middleware('tracking.identify:contact_form');
        Route::post('/test/newsletter', fn () => response()->json('Thanks for Subscribe Our Newsletter'))
            ->middleware('tracking.identify:newsletter');
    }

    public function test_a_successful_form_puts_a_name_on_the_visitor(): void
    {
        $visitor = (string) Str::uuid();
        $this->viewPage($visitor, '/contact');

        $this->call('POST', '/test/contact', ['your-name' => 'Jane Doe', 'your-email' => 'jane@acme.com', 'company' => 'Acme'], ['g4_vid' => $visitor])
            ->assertOk();

        $person = Visitor::sole();
        $this->assertSame(['Jane Doe', 'jane@acme.com', 'Acme', 'contact_form'], [$person->name, $person->email, $person->company, $person->identified_via]);
        $this->assertNotNull($person->identified_at);
        $this->assertSame('contact_form', Identity::sole()->source);
    }

    public function test_a_failed_form_attaches_nothing(): void
    {
        $visitor = (string) Str::uuid();
        $this->viewPage($visitor);

        $this->call('POST', '/test/contact', ['your-name' => 'Jane', 'your-email' => 'jane@acme.com', 'fail' => 1], ['g4_vid' => $visitor])->assertOk();

        $this->assertNull(Visitor::sole()->identified_at);
        $this->assertSame(0, Identity::count());
    }

    public function test_later_details_update_the_profile_but_not_when_they_were_first_known(): void
    {
        $visitor = (string) Str::uuid();
        $this->viewPage($visitor);

        $this->travelToTime('2026-10-01 09:00:00');
        $this->call('POST', '/test/newsletter', ['email' => 'jane@acme.com'], ['g4_vid' => $visitor])->assertOk();
        $this->travelToTime('2026-10-05 09:00:00');
        $this->call('POST', '/test/contact', ['name' => 'Jane Doe', 'email' => 'jane.doe@acme.com'], ['g4_vid' => $visitor])->assertOk();

        $person = Visitor::sole();
        $this->assertSame(['Jane Doe', 'jane.doe@acme.com'], [$person->name, $person->email]);
        $this->assertSame('2026-10-01 09:00:00', $person->identified_at->toDateTimeString());
        $this->assertSame(2, Identity::count());
    }

    public function test_a_form_from_a_browser_without_a_tracking_cookie_changes_nothing(): void
    {
        $this->call('POST', '/test/contact', ['name' => 'Jane', 'email' => 'jane@acme.com'])->assertOk();

        $this->assertSame(0, Identity::count());
    }

    private function sendAppEvent(array $message, ?string $secret = self::SECRET, ?int $timestamp = null)
    {
        $body = json_encode($message);
        $timestamp ??= time();

        return $this->call('POST', '/t/app-event', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_G4_TIMESTAMP' => (string) $timestamp,
            'HTTP_X_G4_SIGNATURE' => AppSignature::sign((string) $secret, $timestamp, $body),
        ], $body);
    }

    public function test_a_signed_sign_up_from_the_app_names_the_visitor_and_joins_their_journey(): void
    {
        $this->options->set('tracking_app_secret', self::SECRET);
        $visitor = (string) Str::uuid();
        $this->viewPage($visitor);

        $this->sendAppEvent([
            'event' => 'signed_up',
            'visitor_id' => $visitor,
            'user' => ['id' => 4821, 'email' => 'Jane@Acme.com', 'name' => 'Jane Doe', 'company' => 'Acme'],
            'properties' => ['plan' => 'free'],
        ])->assertStatus(202)->assertJson(['recorded' => true]);

        $person = Visitor::sole();
        $this->assertSame(['Jane Doe', 'jane@acme.com', '4821', 'app_signup'], [$person->name, $person->email, $person->app_user_id, $person->identified_via]);
        $step = Event::where('type', 'app')->sole();
        $this->assertSame(['signed_up', ['plan' => 'free']], [$step->name, $step->properties]);
    }

    public function test_app_messages_must_be_signed_fresh_and_used_once(): void
    {
        $this->options->set('tracking_app_secret', self::SECRET);
        $visitor = (string) Str::uuid();
        $this->viewPage($visitor);
        $message = ['event' => 'logged_in', 'visitor_id' => $visitor, 'user' => ['email' => 'jane@acme.com']];

        $this->sendAppEvent($message, str_repeat('0', 64))->assertStatus(401);
        $this->sendAppEvent($message, self::SECRET, time() - 3600)->assertStatus(401);

        $timestamp = time();
        $this->sendAppEvent($message, self::SECRET, $timestamp)->assertStatus(202);
        $this->sendAppEvent($message, self::SECRET, $timestamp)->assertStatus(409);

        $this->assertSame(1, Identity::count());
    }

    public function test_app_messages_are_refused_until_a_secret_is_made(): void
    {
        $this->sendAppEvent(['event' => 'signed_up', 'visitor_id' => (string) Str::uuid()])->assertStatus(401);
    }

    public function test_app_messages_for_unknown_visitors_are_accepted_but_not_recorded(): void
    {
        $this->options->set('tracking_app_secret', self::SECRET);

        $this->sendAppEvent(['event' => 'signed_up', 'visitor_id' => (string) Str::uuid(), 'user' => ['email' => 'x@y.com']])
            ->assertStatus(202)->assertJson(['recorded' => false]);
        $this->assertSame(0, Identity::count());
    }

    public function test_malformed_app_messages_are_rejected(): void
    {
        $this->options->set('tracking_app_secret', self::SECRET);

        $this->sendAppEvent(['event' => 'Signed Up!', 'visitor_id' => 'nope'])->assertStatus(422);
    }
}
