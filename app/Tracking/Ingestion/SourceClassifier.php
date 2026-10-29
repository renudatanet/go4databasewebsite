<?php

namespace App\Tracking\Ingestion;

use App\Tracking\Data\TrafficSource;
use App\Tracking\Support\UrlSanitizer;

/**
 * Works out how a visit arrived, from the landing page's UTM tags and click
 * ids and the referrer. UTM tags win, because whoever wrote them knows.
 */
final class SourceClassifier
{
    /**
     * Host => [channel, source name]. "x.com" matches x.com and any
     * subdomain; a key ending in "." ("google.") matches any domain ending,
     * e.g. google.co.in. First match wins, so specific hosts come first.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const KNOWN_HOSTS = [
        'mail.google.com' => [TrafficSource::EMAIL, 'Gmail'],
        'outlook.live.com' => [TrafficSource::EMAIL, 'Outlook'],
        'outlook.office.com' => [TrafficSource::EMAIL, 'Outlook'],
        'mail.yahoo.com' => [TrafficSource::EMAIL, 'Yahoo Mail'],
        'chatgpt.com' => [TrafficSource::AI, 'ChatGPT'],
        'chat.openai.com' => [TrafficSource::AI, 'ChatGPT'],
        'openai.com' => [TrafficSource::AI, 'ChatGPT'],
        'perplexity.ai' => [TrafficSource::AI, 'Perplexity'],
        'claude.ai' => [TrafficSource::AI, 'Claude'],
        'gemini.google.com' => [TrafficSource::AI, 'Gemini'],
        'bard.google.com' => [TrafficSource::AI, 'Gemini'],
        'copilot.microsoft.com' => [TrafficSource::AI, 'Copilot'],
        'grok.com' => [TrafficSource::AI, 'Grok'],
        'you.com' => [TrafficSource::AI, 'You.com'],
        'phind.com' => [TrafficSource::AI, 'Phind'],
        'deepseek.com' => [TrafficSource::AI, 'DeepSeek'],
        'meta.ai' => [TrafficSource::AI, 'Meta AI'],
        'google.' => [TrafficSource::SEARCH, 'Google'],
        'bing.com' => [TrafficSource::SEARCH, 'Bing'],
        'duckduckgo.com' => [TrafficSource::SEARCH, 'DuckDuckGo'],
        'search.yahoo.' => [TrafficSource::SEARCH, 'Yahoo'],
        'yahoo.com' => [TrafficSource::SEARCH, 'Yahoo'],
        'yandex.' => [TrafficSource::SEARCH, 'Yandex'],
        'baidu.com' => [TrafficSource::SEARCH, 'Baidu'],
        'ecosia.org' => [TrafficSource::SEARCH, 'Ecosia'],
        'brave.com' => [TrafficSource::SEARCH, 'Brave Search'],
        'naver.com' => [TrafficSource::SEARCH, 'Naver'],
        'linkedin.com' => [TrafficSource::SOCIAL, 'LinkedIn'],
        'lnkd.in' => [TrafficSource::SOCIAL, 'LinkedIn'],
        'facebook.com' => [TrafficSource::SOCIAL, 'Facebook'],
        'fb.com' => [TrafficSource::SOCIAL, 'Facebook'],
        'instagram.com' => [TrafficSource::SOCIAL, 'Instagram'],
        't.co' => [TrafficSource::SOCIAL, 'X (Twitter)'],
        'twitter.com' => [TrafficSource::SOCIAL, 'X (Twitter)'],
        'x.com' => [TrafficSource::SOCIAL, 'X (Twitter)'],
        'youtube.com' => [TrafficSource::SOCIAL, 'YouTube'],
        'reddit.com' => [TrafficSource::SOCIAL, 'Reddit'],
        'quora.com' => [TrafficSource::SOCIAL, 'Quora'],
        'pinterest.' => [TrafficSource::SOCIAL, 'Pinterest'],
        'medium.com' => [TrafficSource::SOCIAL, 'Medium'],
        'wa.me' => [TrafficSource::SOCIAL, 'WhatsApp'],
        'whatsapp.com' => [TrafficSource::SOCIAL, 'WhatsApp'],
    ];

    private const PAID_MEDIUMS = ['cpc', 'ppc', 'paid', 'paidsearch', 'paid_social', 'paidsocial', 'display', 'cpm', 'banner', 'ad', 'ads'];
    private const EMAIL_MEDIUMS = ['email', 'e-mail', 'newsletter', 'mail'];
    private const SOCIAL_MEDIUMS = ['social', 'social-network', 'social_media', 'sm'];
    private const CLICK_IDS = ['gclid' => 'Google Ads', 'msclkid' => 'Microsoft Ads', 'fbclid' => 'Facebook', 'li_fat_id' => 'LinkedIn Ads'];

    public function classify(string $landingUrl, ?string $referrer, string $siteHost): TrafficSource
    {
        $query = UrlSanitizer::query($landingUrl);
        $referrerHost = UrlSanitizer::host($referrer);
        $referrerPath = UrlSanitizer::referrerPath($referrer);
        $siteHost = preg_replace('/^www\./', '', strtolower($siteHost));

        $utm = fn (string $key) => isset($query[$key]) && $query[$key] !== '' ? $query[$key] : null;
        $source = $utm('utm_source');
        $medium = $utm('utm_medium') !== null ? strtolower($utm('utm_medium')) : null;

        if ($source !== null || $medium !== null) {
            return new TrafficSource(
                $this->channelForCampaign($medium, $source, $referrerHost),
                $source ?? $this->knownSourceName($referrerHost) ?? $referrerHost,
                $medium,
                $utm('utm_campaign'),
                $utm('utm_term'),
                $utm('utm_content'),
                $referrerHost,
                $referrerPath,
            );
        }

        foreach (self::CLICK_IDS as $param => $name) {
            if (isset($query[$param])) {
                return new TrafficSource(TrafficSource::PAID, $name, 'cpc', null, null, null, $referrerHost, $referrerPath);
            }
        }

        if ($referrerHost === null) {
            return new TrafficSource(TrafficSource::DIRECT);
        }

        if ($referrerHost === $siteHost) {
            return new TrafficSource(TrafficSource::INTERNAL, null, null, null, null, null, $referrerHost, $referrerPath);
        }

        $known = $this->known($referrerHost);
        if ($known !== null) {
            return new TrafficSource($known[0], $known[1], null, null, null, null, $referrerHost, $referrerPath);
        }

        return new TrafficSource(TrafficSource::REFERRAL, $referrerHost, null, null, null, null, $referrerHost, $referrerPath);
    }

    private function channelForCampaign(?string $medium, ?string $source, ?string $referrerHost): string
    {
        if ($medium !== null) {
            if (in_array($medium, self::PAID_MEDIUMS, true)) {
                return TrafficSource::PAID;
            }
            if (in_array($medium, self::EMAIL_MEDIUMS, true)) {
                return TrafficSource::EMAIL;
            }
            if (in_array($medium, self::SOCIAL_MEDIUMS, true)) {
                return TrafficSource::SOCIAL;
            }
            if ($medium === 'organic') {
                return TrafficSource::SEARCH;
            }
        }

        $known = $this->known(strtolower((string) $source)) ?? $this->known($referrerHost);

        return $known[0] ?? TrafficSource::CAMPAIGN;
    }

    private function knownSourceName(?string $host): ?string
    {
        return $this->known($host)[1] ?? null;
    }

    /** @return array{0: string, 1: string}|null */
    private function known(?string $host): ?array
    {
        if ($host === null || $host === '') {
            return null;
        }

        foreach (self::KNOWN_HOSTS as $key => $match) {
            $matches = str_ends_with($key, '.')
                ? str_starts_with($host, $key) || str_contains($host, '.' . $key)
                : $host === $key || str_ends_with($host, '.' . $key);
            if ($matches) {
                return $match;
            }
        }

        // A bare name in utm_source, e.g. "linkedin" or "google".
        foreach (self::KNOWN_HOSTS as $key => $match) {
            if ($host === explode('.', $key)[0]) {
                return $match;
            }
        }

        return null;
    }
}
