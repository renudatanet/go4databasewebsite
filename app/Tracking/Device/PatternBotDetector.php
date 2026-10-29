<?php

namespace App\Tracking\Device;

use App\Tracking\Contracts\BotDetector;

/**
 * Known crawlers, previewers, monitors, test tools and HTTP libraries. The
 * tracker only runs in a real browser, which already keeps most bots out;
 * this catches the ones that render JavaScript (Googlebot, Lighthouse, ...).
 */
final class PatternBotDetector implements BotDetector
{
    // "(?<!cu)" spares the Cubot phone brand, which appears in real Android UAs.
    // In-app browsers (Pinterest, Quora, Facebook...) are real people and are
    // deliberately not listed; their link-preview fetchers are caught by "bot"
    // or "preview".
    private const PATTERN = '#(?<!cu)bot\b|(?<!cu)bot/|crawl|spider|slurp|facebookexternalhit|embedly|preview|'
        . 'headless|lighthouse|pagespeed|gtmetrix|pingdom|uptime|statuscake|site24x7|'
        . 'monitor|curl/|wget|python|httpclient|okhttp|axios|node-fetch|go-http|java/|'
        . 'phantomjs|selenium|puppeteer|playwright|webdriver|google-inspectiontool|adsbot|'
        . 'mediapartners|apis-google|feedfetcher|yandex(?!browser)|baiduspider|duckduck|'
        . 'petalbot|semrush|ahrefs|mj12|dotbot|bytespider|gptbot|claudebot|claude-web|'
        . 'ccbot|perplexitybot|amazonbot|applebot|ia_archiver|archive\.org|whatsapp|'
        . 'skypeuripreview|validator|scrapy|httrack|nutch|zgrab|masscan|nmap#i';

    public function isBot(string $userAgent): bool
    {
        $userAgent = trim($userAgent);

        return $userAgent === '' || strlen($userAgent) < 20 || preg_match(self::PATTERN, $userAgent) === 1;
    }
}
