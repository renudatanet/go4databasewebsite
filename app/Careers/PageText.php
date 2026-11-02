<?php

namespace App\Careers;

/**
 * A page's wording in one language: what was saved in its admin screen
 * (static option PREFIX{lang}_{key}), else the default in DEFAULTS. The
 * admin screens show the same defaults under each field, so the two can't
 * drift apart.
 */
abstract class PageText
{
    /** The static option prefix, e.g. "career_page_" for career_page_{lang}_{key}. */
    protected const PREFIX = '';

    /** @var array<string, string> */
    public const DEFAULTS = [];

    public function __construct(protected string $lang)
    {
    }

    /** @param array<string, string|int> $fill placeholders, e.g. ['{roles}' => 9] */
    public function get(string $key, array $fill = []): string
    {
        $value = trim((string) get_static_option(static::PREFIX . $this->lang . '_' . $key));

        return strtr($value !== '' ? $value : (static::DEFAULTS[$key] ?? ''), array_map('strval', $fill));
    }

    /** A many-line field as its non-empty lines; "none" hides what it lists. */
    public function lines(string $key): array
    {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $this->get($key)))));

        return count($lines) === 1 && strcasecmp($lines[0], 'none') === 0 ? [] : $lines;
    }

    /** "Title | text" lines as [title, text] pairs. */
    public function pairs(string $key): array
    {
        return array_map(function (string $line) {
            [$title, $text] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');

            return [$title, $text];
        }, $this->lines($key));
    }

    /** The static option a field is saved as, for the admin screens. */
    public static function optionName(string $lang, string $key): string
    {
        return static::PREFIX . $lang . '_' . $key;
    }
}
