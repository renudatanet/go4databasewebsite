<?php

namespace Tests\Unit\Tracking;

use App\Tracking\Data\NetworkType;
use App\Tracking\Geo\NetworkClassifier;
use App\Tracking\Identity\AppSignature;
use App\Tracking\Identity\FormIdentityExtractor;
use App\Tracking\Ingestion\AnonymousId;
use App\Tracking\Ingestion\ConsentPolicy;
use App\Tracking\Support\ArrayOptionStore;
use App\Tracking\Support\TrackingSettings;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class PolicyAndIdentityTest extends TestCase
{
    private function policy(string $mode): ConsentPolicy
    {
        return new ConsentPolicy(new TrackingSettings(new ArrayOptionStore(['tracking_consent_mode' => $mode])), ['DE', 'FR', 'GB']);
    }

    public function test_eu_visitors_are_anonymous_until_they_consent(): void
    {
        $policy = $this->policy(TrackingSettings::CONSENT_EU);

        $this->assertSame(ConsentPolicy::ANONYMOUS, $policy->decide('DE', false));
        $this->assertSame(ConsentPolicy::FULL, $policy->decide('DE', true));
        $this->assertSame(ConsentPolicy::FULL, $policy->decide('US', false));
        $this->assertSame(ConsentPolicy::FULL, $policy->decide(null, false));
    }

    public function test_the_other_consent_modes(): void
    {
        $this->assertSame(ConsentPolicy::ANONYMOUS, $this->policy(TrackingSettings::CONSENT_EVERYWHERE)->decide('US', false));
        $this->assertSame(ConsentPolicy::FULL, $this->policy(TrackingSettings::CONSENT_EVERYWHERE)->decide('US', true));
        $this->assertSame(ConsentPolicy::FULL, $this->policy(TrackingSettings::CONSENT_NONE)->decide('DE', false));
    }

    public function test_anonymous_ids_last_one_day_and_look_like_uuids(): void
    {
        $ids = new AnonymousId('base64:test-key');
        $monday = CarbonImmutable::parse('2026-10-26 09:00', 'UTC');

        $first = $ids->for('203.0.113.7', 'Browser A', $monday);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $first);
        $this->assertSame($first, $ids->for('203.0.113.7', 'Browser A', $monday->setTime(23, 59)));
        $this->assertNotSame($first, $ids->for('203.0.113.7', 'Browser A', $monday->addDay()));
        $this->assertNotSame($first, $ids->for('203.0.113.8', 'Browser A', $monday));
        $this->assertNotSame($first, $ids->for('203.0.113.7', 'Browser B', $monday));
    }

    public function test_app_signatures(): void
    {
        $secret = str_repeat('s', 64);
        $body = '{"event":"signed_up"}';
        $now = 1_800_000_000;
        $signature = AppSignature::sign($secret, $now, $body);

        $this->assertTrue(AppSignature::verify($secret, (string) $now, $signature, $body, $now + 10));
        $this->assertTrue(AppSignature::verify($secret, (string) $now, strtoupper($signature), $body, $now));
        $this->assertFalse(AppSignature::verify($secret, (string) $now, $signature, $body . ' ', $now), 'changed body');
        $this->assertFalse(AppSignature::verify($secret, (string) $now, $signature, $body, $now + 301), 'too old');
        $this->assertFalse(AppSignature::verify(str_repeat('x', 64), (string) $now, $signature, $body, $now), 'other secret');
        $this->assertFalse(AppSignature::verify(null, (string) $now, $signature, $body, $now), 'no secret set');
        $this->assertFalse(AppSignature::verify($secret, 'soon', $signature, $body, $now), 'bad timestamp');
    }

    public function test_finds_who_filled_in_a_form_builder_form(): void
    {
        $extractor = new FormIdentityExtractor();

        $details = $extractor->extract([
            '_token' => 'x', 'your-name' => 'Jane Doe', 'your-email' => 'Jane@Acme.com ', 'phone-number' => '+1 555 0100',
            'company-name' => 'Acme Corp', 'message' => 'Hello',
        ]);
        $this->assertSame(['Jane Doe', 'jane@acme.com', '+1 555 0100', 'Acme Corp'], [$details->name, $details->email, $details->phone, $details->company]);

        $split = $extractor->extract(['first_name' => 'Ravi', 'last_name' => 'Kumar', 'email' => 'ravi@example.in']);
        $this->assertSame('Ravi Kumar', $split->name);

        $this->assertTrue($extractor->extract(['message' => 'no contact details here'])->isEmpty());
        $this->assertNull($extractor->extract(['email' => 'not an email'])->email);
    }

    public function test_network_types(): void
    {
        $classifier = new NetworkClassifier();

        $this->assertSame(NetworkType::HOSTING, $classifier->classify('Amazon.com, Inc.'));
        $this->assertSame(NetworkType::HOSTING, $classifier->classify('Hostinger International Limited'));
        $this->assertSame(NetworkType::ISP, $classifier->classify('Reliance Jio Infocomm Limited'));
        $this->assertSame(NetworkType::ISP, $classifier->classify('Comcast Cable Communications, LLC'));
        $this->assertSame(NetworkType::BUSINESS, $classifier->classify('Acme Manufacturing Inc'));
        $this->assertSame(NetworkType::UNKNOWN, $classifier->classify(null));
    }
}
