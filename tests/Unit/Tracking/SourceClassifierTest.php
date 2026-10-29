<?php

namespace Tests\Unit\Tracking;

use App\Tracking\Data\TrafficSource;
use App\Tracking\Ingestion\SourceClassifier;
use PHPUnit\Framework\TestCase;

class SourceClassifierTest extends TestCase
{
    private const SITE = 'www.go4database.com';

    /** @dataProvider visits */
    public function test_classifies_how_a_visit_arrived(string $landing, ?string $referrer, string $channel, ?string $source): void
    {
        $result = (new SourceClassifier())->classify($landing, $referrer, self::SITE);

        $this->assertSame($channel, $result->channel);
        $this->assertSame($source, $result->source);
    }

    public static function visits(): array
    {
        $home = 'https://www.go4database.com/';

        return [
            'typed in' => [$home, null, TrafficSource::DIRECT, null],
            'from another page of the site' => [$home, 'https://www.go4database.com/blog', TrafficSource::INTERNAL, null],
            'google' => [$home, 'https://www.google.co.in/', TrafficSource::SEARCH, 'Google'],
            'bing' => [$home, 'https://www.bing.com/search?q=x', TrafficSource::SEARCH, 'Bing'],
            'chatgpt' => [$home, 'https://chatgpt.com/', TrafficSource::AI, 'ChatGPT'],
            'perplexity' => [$home, 'https://www.perplexity.ai/search/abc', TrafficSource::AI, 'Perplexity'],
            'gemini is AI, not search' => [$home, 'https://gemini.google.com/app', TrafficSource::AI, 'Gemini'],
            'gmail is email, not search' => [$home, 'https://mail.google.com/mail/u/0/', TrafficSource::EMAIL, 'Gmail'],
            'linkedin' => [$home, 'https://www.linkedin.com/feed/', TrafficSource::SOCIAL, 'LinkedIn'],
            'x short links' => [$home, 'https://t.co/abc', TrafficSource::SOCIAL, 'X (Twitter)'],
            'unknown website' => [$home, 'https://some-blog.example/post', TrafficSource::REFERRAL, 'some-blog.example'],
            'utm email' => [$home . '?utm_source=brevo&utm_medium=email&utm_campaign=oct', null, TrafficSource::EMAIL, 'brevo'],
            'utm paid' => [$home . '?utm_source=google&utm_medium=cpc', null, TrafficSource::PAID, 'google'],
            'utm with a bare known source' => [$home . '?utm_source=linkedin', null, TrafficSource::SOCIAL, 'linkedin'],
            'utm unknown' => [$home . '?utm_source=partner-x&utm_medium=referral', null, TrafficSource::CAMPAIGN, 'partner-x'],
            'google ads click id' => [$home . '?gclid=abc', 'https://www.google.com/', TrafficSource::PAID, 'Google Ads'],
        ];
    }

    public function test_keeps_campaign_tags_and_only_the_referrer_path(): void
    {
        $result = (new SourceClassifier())->classify(
            'https://www.go4database.com/?utm_source=linkedin&utm_medium=social&utm_campaign=oct_launch&utm_term=ceo&utm_content=banner',
            'https://www.linkedin.com/feed/update/123?trk=private',
            self::SITE
        );

        $this->assertSame(['oct_launch', 'ceo', 'banner'], [$result->campaign, $result->term, $result->content]);
        $this->assertSame('linkedin.com', $result->referrerHost);
        $this->assertSame('/feed/update/123', $result->referrerPath);
    }
}
