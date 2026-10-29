<?php

namespace Tests\Unit\Tracking;

use App\Tracking\Support\UrlSanitizer;
use PHPUnit\Framework\TestCase;

class UrlSanitizerTest extends TestCase
{
    /** @dataProvider pagePaths */
    public function test_reduces_urls_to_a_clean_page_path(string $url, string $expected): void
    {
        $this->assertSame($expected, UrlSanitizer::pagePath($url));
    }

    public static function pagePaths(): array
    {
        return [
            'root' => ['https://www.go4database.com/', '/'],
            'no path' => ['https://www.go4database.com', '/'],
            'query and fragment dropped' => ['https://www.go4database.com/blog?page=2#top', '/blog'],
            'trailing slash dropped' => ['https://www.go4database.com/list/healthcare/', '/list/healthcare'],
            'double slashes collapsed' => ['https://www.go4database.com//b2b//it', '/b2b/it'],
            'encoded characters decoded' => ['https://www.go4database.com/blog/caf%C3%A9', '/blog/café'],
        ];
    }

    public function test_link_destinations_lose_personal_data_and_the_handover_id(): void
    {
        $href = UrlSanitizer::linkHref(
            'https://app.go4database.com/register?utm_source=Homepage&email=jane@acme.com&g4v=abc&token=secret&ref=x@y.com&plan=pro',
            'https://www.go4database.com/'
        );

        $this->assertSame('https://app.go4database.com/register?plan=pro&utm_source=Homepage', $href);
    }

    public function test_relative_links_are_resolved_against_the_page(): void
    {
        $base = 'https://www.go4database.com/blog/post?x=1';

        $this->assertSame('https://www.go4database.com/pricing', UrlSanitizer::linkHref('/pricing', $base));
        $this->assertSame('https://www.go4database.com/blog/other', UrlSanitizer::linkHref('other', $base));
        $this->assertSame('https://www.go4database.com/blog/post#faq', UrlSanitizer::linkHref('#faq', $base));
        $this->assertSame('https://cdn.example.com/a.pdf', UrlSanitizer::linkHref('//cdn.example.com/a.pdf', $base));
    }

    public function test_mailto_keeps_the_address_and_drops_subject_and_body(): void
    {
        $this->assertSame('mailto:hello@example.com', UrlSanitizer::linkHref('mailto:hello@example.com?subject=Hi&body=Hello', 'https://www.go4database.com/'));
        $this->assertSame('tel:+18005550100', UrlSanitizer::linkHref('tel:+18005550100', 'https://www.go4database.com/'));
    }

    public function test_hosts_and_referrer_paths(): void
    {
        $this->assertSame('linkedin.com', UrlSanitizer::host('https://www.LinkedIn.com/feed/'));
        $this->assertNull(UrlSanitizer::host(''));
        $this->assertSame('/search', UrlSanitizer::referrerPath('https://www.google.com/search?q=private+terms'));
    }
}
