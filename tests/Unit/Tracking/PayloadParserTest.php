<?php

namespace Tests\Unit\Tracking;

use App\Tracking\Ingestion\CollectPayload;
use App\Tracking\Ingestion\PayloadParser;
use Tests\TestCase;

class PayloadParserTest extends TestCase
{
    private const PV = '0b5c2f6e-1f0a-4c3e-9b7d-2a1e4f6c8d90';

    private function parse(array $data): ?CollectPayload
    {
        return (new PayloadParser(25, 21_600_000))->parse(json_encode($data));
    }

    private function valid(array $override = []): array
    {
        return $override + [
            'version' => 1,
            'visitor' => null,
            'page_view' => self::PV,
            'url' => 'https://www.go4database.com/pricing',
            'events' => [['type' => 'pageview']],
        ];
    }

    public function test_accepts_a_valid_payload(): void
    {
        $payload = $this->parse($this->valid(['title' => "  Pricing \n | Go4Database ", 'device' => ['width' => 390, 'touch' => true]]));

        $this->assertNotNull($payload);
        $this->assertSame('Pricing | Go4Database', $payload->title);
        $this->assertSame(390, $payload->screenWidth);
        $this->assertTrue($payload->touch);
        $this->assertTrue($payload->hasPageview());
    }

    public function test_rejects_malformed_payloads(): void
    {
        $this->assertNull($this->parse($this->valid(['version' => 2])));
        $this->assertNull($this->parse($this->valid(['page_view' => 'not-a-uuid'])));
        $this->assertNull($this->parse($this->valid(['url' => 'javascript:alert(1)'])));
        $this->assertNull($this->parse($this->valid(['events' => []])));
        $this->assertNull($this->parse($this->valid(['events' => array_fill(0, 26, ['type' => 'pageview'])])));
        $this->assertNull($this->parse($this->valid(['events' => [['type' => 'delete_everything']]])));
        $this->assertNull((new PayloadParser(25, 1000))->parse('not json'));
        $this->assertNull((new PayloadParser(25, 1000))->parse(str_repeat('x', 40000)));
    }

    public function test_caps_numbers_and_drops_unusable_events(): void
    {
        $payload = $this->parse($this->valid(['events' => [
            ['type' => 'engagement', 'engaged_ms' => 999_999_999, 'scroll' => 250],
            ['type' => 'click'],
            ['type' => 'event', 'name' => 'bad name with spaces'],
        ]]));

        $this->assertCount(1, $payload->events);
        $this->assertSame(21_600_000, $payload->events[0]['engaged_ms']);
        $this->assertSame(100, $payload->events[0]['scroll']);
    }

    public function test_event_properties_keep_only_safe_scalar_values(): void
    {
        $payload = $this->parse($this->valid(['events' => [[
            'type' => 'event',
            'name' => 'View_Email',
            'properties' => ['position' => 2, 'who' => 'jane@acme.com', 'nested' => ['a' => 1], 'ok' => true, 'bad key!' => 'x'],
        ]]]));

        $this->assertSame('view_email', $payload->events[0]['name']);
        $this->assertSame(['position' => 2, 'ok' => true], $payload->events[0]['properties']);
    }

    public function test_drops_referrers_that_are_not_web_addresses(): void
    {
        $this->assertNull($this->parse($this->valid(['referrer' => 'android-app://com.google.android.gm/']))->referrer);
        $this->assertSame('https://www.google.com/', $this->parse($this->valid(['referrer' => 'https://www.google.com/']))->referrer);
    }
}
