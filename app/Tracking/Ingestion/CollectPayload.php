<?php

namespace App\Tracking\Ingestion;

/**
 * One request from the tracker: the page it came from, the browser's
 * details, and the events that happened there since the last request.
 * Built only by PayloadParser, so every field is already checked.
 */
final class CollectPayload
{
    public const PAGEVIEW = 'pageview';
    public const ENGAGEMENT = 'engagement';
    public const CLICK = 'click';
    public const EVENT = 'event';

    /**
     * @param list<array<string, mixed>> $events each has "type" and that type's fields
     */
    public function __construct(
        public ?string $visitorUuid,
        public string $pageViewUuid,
        public bool $consent,
        public string $url,
        public ?string $title,
        public ?string $referrer,
        public ?int $screenWidth,
        public ?int $screenHeight,
        public ?string $language,
        public bool $touch,
        public bool $automated,
        public array $events,
    ) {
    }

    public function hasPageview(): bool
    {
        foreach ($this->events as $event) {
            if ($event['type'] === self::PAGEVIEW) {
                return true;
            }
        }

        return false;
    }
}
