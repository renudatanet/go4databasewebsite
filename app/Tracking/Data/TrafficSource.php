<?php

namespace App\Tracking\Data;

/** How a visit arrived: channel, named source and campaign tags. */
final class TrafficSource
{
    public const DIRECT = 'direct';
    public const SEARCH = 'search';
    public const AI = 'ai';
    public const SOCIAL = 'social';
    public const EMAIL = 'email';
    public const PAID = 'paid';
    public const REFERRAL = 'referral';
    public const CAMPAIGN = 'campaign';
    public const INTERNAL = 'internal';

    public const LABELS = [
        self::SEARCH => 'Search engines',
        self::AI => 'AI assistants',
        self::SOCIAL => 'Social',
        self::EMAIL => 'Email',
        self::PAID => 'Paid ads',
        self::CAMPAIGN => 'Other campaigns',
        self::REFERRAL => 'Other websites',
        self::DIRECT => 'Direct',
        self::INTERNAL => 'Internal',
    ];

    public function __construct(
        public string $channel = self::DIRECT,
        public ?string $source = null,
        public ?string $medium = null,
        public ?string $campaign = null,
        public ?string $term = null,
        public ?string $content = null,
        public ?string $referrerHost = null,
        public ?string $referrerPath = null,
    ) {
    }
}
