<?php

namespace App\Tracking\Support;

/**
 * Turns URLs from the browser into what is safe and useful to store: a clean
 * page path, and link destinations without personal data in their query.
 */
final class UrlSanitizer
{
    /** Query parameters that can carry personal data or secrets; never stored. */
    private const PRIVATE_PARAMS = [
        'email', 'e', 'mail', 'phone', 'mobile', 'tel', 'name', 'first_name', 'last_name',
        'token', 'access_token', 'auth', 'key', 'api_key', 'apikey', 'password', 'pass',
        'pwd', 'secret', 'code', 'session', 'sid', 'g4v',
    ];

    private const MAX_PATH = 500;

    /**
     * The page key: path only, no query or fragment, no trailing slash
     * (except the root), repeated slashes collapsed, at most 500 characters.
     */
    public static function pagePath(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            return '/';
        }

        $path = rawurldecode($path);
        $path = preg_replace('#/{2,}#', '/', $path) ?? '/';
        $path = '/' . trim($path, '/');

        return mb_substr($path, 0, self::MAX_PATH);
    }

    /** Lower-cased host of a URL, without "www.", or null. */
    public static function host(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (!is_string($host) || $host === '') {
            return null;
        }

        return preg_replace('/^www\./', '', strtolower($host));
    }

    /** @return array<string, string> the URL's query parameters */
    public static function query(string $url): array
    {
        $query = parse_url($url, PHP_URL_QUERY);
        if (!is_string($query) || $query === '') {
            return [];
        }

        parse_str($query, $params);

        return array_map(
            fn ($value) => is_string($value) ? mb_substr($value, 0, 150) : '',
            array_filter($params, fn ($value, $key) => is_string($key), ARRAY_FILTER_USE_BOTH)
        );
    }

    /**
     * A link destination as stored: scheme, host, path, and its query minus
     * private parameters and any value that looks like an email address.
     * Relative links are resolved against $base.
     */
    public static function linkHref(string $href, string $base): string
    {
        $href = trim($href);
        if (preg_match('/^(mailto|tel):/i', $href, $match)) {
            // The address itself is personal data of whoever is being contacted
            // only rarely, but it is also the link's identity: keep the scheme
            // and address, drop any subject/body.
            return strtolower($match[1]) . ':' . mb_substr(strtok(substr($href, strlen($match[0])), '?') ?: '', 0, 150);
        }

        $absolute = self::resolve($href, $base);
        $parts = parse_url($absolute);
        if ($parts === false || empty($parts['host'])) {
            return mb_substr($href, 0, 1000);
        }

        $clean = strtolower($parts['scheme'] ?? 'https') . '://' . strtolower($parts['host']);
        if (!empty($parts['port'])) {
            $clean .= ':' . $parts['port'];
        }
        $clean .= $parts['path'] ?? '/';

        $query = [];
        parse_str($parts['query'] ?? '', $query);
        $query = array_filter(
            $query,
            fn ($value, $key) => is_string($key)
                && !in_array(strtolower($key), self::PRIVATE_PARAMS, true)
                && is_string($value)
                && !str_contains($value, '@'),
            ARRAY_FILTER_USE_BOTH
        );
        if ($query !== []) {
            ksort($query);
            $clean .= '?' . http_build_query($query);
        }
        if (!empty($parts['fragment']) && strlen($parts['fragment']) <= 60) {
            $clean .= '#' . $parts['fragment'];
        }

        return mb_substr($clean, 0, 1000);
    }

    /** Referrer kept as host + path only; its query often holds search terms or ids. */
    public static function referrerPath(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }
        $path = parse_url($url, PHP_URL_PATH);

        return is_string($path) && $path !== '' ? mb_substr($path, 0, 255) : '/';
    }

    private static function resolve(string $href, string $base): string
    {
        if (preg_match('#^[a-z][a-z0-9+.-]*://#i', $href)) {
            return $href;
        }

        $baseParts = parse_url($base) ?: [];
        $origin = ($baseParts['scheme'] ?? 'https') . '://' . ($baseParts['host'] ?? '');
        if (!empty($baseParts['port'])) {
            $origin .= ':' . $baseParts['port'];
        }

        if (str_starts_with($href, '//')) {
            return ($baseParts['scheme'] ?? 'https') . ':' . $href;
        }
        if (str_starts_with($href, '/')) {
            return $origin . $href;
        }
        if (str_starts_with($href, '#') || str_starts_with($href, '?')) {
            return $origin . ($baseParts['path'] ?? '/') . $href;
        }

        $dir = preg_replace('#/[^/]*$#', '/', $baseParts['path'] ?? '/');

        return $origin . $dir . $href;
    }
}
