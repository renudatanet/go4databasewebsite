<?php

namespace App\Tracking\Ingestion\Enrichment;

use App\Tracking\Ingestion\SourceClassifier;
use Closure;

/** How the visit arrived: channel, source, campaign tags and referrer. */
final class AddTrafficSource implements EnrichmentStep
{
    public function __construct(private SourceClassifier $classifier)
    {
    }

    public function handle(SessionDraft $draft, Closure $next): SessionDraft
    {
        $source = $this->classifier->classify(
            $draft->payload->url,
            $draft->payload->referrer,
            $draft->context->host
        );

        $draft->attributes += [
            'channel' => $source->channel,
            'source' => self::cut($source->source),
            'medium' => self::cut($source->medium),
            'campaign' => self::cut($source->campaign),
            'term' => self::cut($source->term),
            'content' => self::cut($source->content),
            'referrer_host' => self::cut($source->referrerHost, 191),
            'referrer_path' => self::cut($source->referrerPath, 255),
        ];

        return $next($draft);
    }

    private static function cut(?string $value, int $max = 150): ?string
    {
        return $value === null ? null : mb_substr($value, 0, $max);
    }
}
