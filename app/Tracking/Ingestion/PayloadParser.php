<?php

namespace App\Tracking\Ingestion;

use Illuminate\Support\Facades\Validator;

/**
 * Reads and checks the tracker's JSON. Anything malformed is dropped
 * silently (null): the collector answers every request the same way, so it
 * gives nothing away to someone probing it.
 */
final class PayloadParser
{
    private const MAX_BODY_BYTES = 32 * 1024;
    private const UUID = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
    private const EVENT_NAME = '/^[a-z0-9_.:-]{1,64}$/i';
    private const MAX_PROPERTIES = 12;

    public function __construct(private int $maxEvents, private int $maxEngagedMs)
    {
    }

    public function parse(string $body): ?CollectPayload
    {
        if ($body === '' || strlen($body) > self::MAX_BODY_BYTES) {
            return null;
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            return null;
        }

        $validator = Validator::make($data, [
            'version' => ['required', 'integer', 'in:1'],
            'visitor' => ['nullable', 'string', 'regex:' . self::UUID],
            'page_view' => ['required', 'string', 'regex:' . self::UUID],
            'consent' => ['boolean'],
            'url' => ['required', 'string', 'max:2000', 'regex:#^https?://#i'],
            'title' => ['nullable', 'string', 'max:500'],
            'referrer' => ['nullable', 'string', 'max:2000'],
            'device' => ['array'],
            'device.width' => ['nullable', 'integer', 'between:1,20000'],
            'device.height' => ['nullable', 'integer', 'between:1,20000'],
            'device.language' => ['nullable', 'string', 'max:16'],
            'device.touch' => ['boolean'],
            'device.automated' => ['boolean'],
            'events' => ['required', 'array', 'min:1', 'max:' . $this->maxEvents],
            'events.*.type' => ['required', 'string', 'in:pageview,engagement,click,event'],
        ]);

        if ($validator->fails()) {
            return null;
        }

        $events = [];
        foreach ($data['events'] as $event) {
            $clean = $this->event($event);
            if ($clean !== null) {
                $events[] = $clean;
            }
        }
        if ($events === []) {
            return null;
        }

        $device = $data['device'] ?? [];
        $referrer = $this->text($data['referrer'] ?? null, 2000);

        return new CollectPayload(
            visitorUuid: isset($data['visitor']) ? strtolower($data['visitor']) : null,
            pageViewUuid: strtolower($data['page_view']),
            consent: (bool) ($data['consent'] ?? false),
            url: $data['url'],
            title: $this->text($data['title'] ?? null, 255),
            referrer: $referrer !== null && preg_match('#^https?://#i', $referrer) ? $referrer : null,
            screenWidth: $device['width'] ?? null,
            screenHeight: $device['height'] ?? null,
            language: $this->text($device['language'] ?? null, 16),
            touch: (bool) ($device['touch'] ?? false),
            automated: (bool) ($device['automated'] ?? false),
            events: $events,
        );
    }

    /**
     * @param array<string, mixed> $event
     * @return array<string, mixed>|null
     */
    private function event(array $event): ?array
    {
        switch ($event['type']) {
            case CollectPayload::PAGEVIEW:
                return ['type' => CollectPayload::PAGEVIEW];

            case CollectPayload::ENGAGEMENT:
                return [
                    'type' => CollectPayload::ENGAGEMENT,
                    'engaged_ms' => $this->int($event['engaged_ms'] ?? 0, 0, $this->maxEngagedMs),
                    'scroll' => $this->int($event['scroll'] ?? 0, 0, 100),
                ];

            case CollectPayload::CLICK:
                $href = $this->text($event['href'] ?? null, 2000);
                $label = $this->text($event['label'] ?? null, 255);
                if ($href === null && $label === null) {
                    return null;
                }

                return [
                    'type' => CollectPayload::CLICK,
                    'href' => $href,
                    'label' => $label,
                    'section' => $this->text($event['section'] ?? null, 64),
                ];

            case CollectPayload::EVENT:
                $name = $event['name'] ?? null;
                if (!is_string($name) || !preg_match(self::EVENT_NAME, $name)) {
                    return null;
                }

                return [
                    'type' => CollectPayload::EVENT,
                    'name' => strtolower($name),
                    'properties' => $this->properties($event['properties'] ?? []),
                ];
        }

        return null;
    }

    /**
     * Scalar values only, a dozen at most, and nothing that looks like an
     * email address: these show up in reports for every admin to read.
     *
     * @return array<string, string|int|float|bool>
     */
    private function properties(mixed $properties): array
    {
        if (!is_array($properties)) {
            return [];
        }

        $clean = [];
        foreach ($properties as $key => $value) {
            if (count($clean) >= self::MAX_PROPERTIES) {
                break;
            }
            if (!is_string($key) || !preg_match(self::EVENT_NAME, $key) || !is_scalar($value)) {
                continue;
            }
            if (is_string($value)) {
                if (str_contains($value, '@')) {
                    continue;
                }
                $value = mb_substr($value, 0, 150);
            }
            $clean[strtolower($key)] = $value;
        }

        return $clean;
    }

    private function text(mixed $value, int $max): ?string
    {
        if (!is_string($value)) {
            return null;
        }
        $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');

        return $value === '' ? null : mb_substr($value, 0, $max);
    }

    private function int(mixed $value, int $min, int $max): int
    {
        return is_numeric($value) ? max($min, min($max, (int) $value)) : $min;
    }
}
