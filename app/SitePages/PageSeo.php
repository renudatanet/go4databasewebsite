<?php

namespace App\SitePages;

use Illuminate\Support\Str;

/**
 * A page's address, name and meta tags, read and written under the same
 * static-option keys General Settings > Page Settings uses, so the two
 * screens always agree and the site reads them as it always has:
 *
 *   {seo}_page_slug                       the address
 *   {seo}_page_{lang}_{field}             name, meta_tags (the title),
 *                                         meta_description, schema_code,
 *                                         meta_image (a media id)
 */
final class PageSeo
{
    /** Per-language fields, in the order the form shows them. */
    public const FIELDS = ['name', 'meta_tags', 'meta_description', 'schema_code', 'meta_image'];

    /** The page's current address, without slashes, e.g. "price-plan". */
    public function slug(SitePage $page): string
    {
        $this->assertSeo($page);
        if (!$page->slugEditable) {
            return $page->defaultSlug;
        }
        $saved = (string) get_static_option($page->seoKey . '_page_slug', '');

        return $saved !== '' ? $saved : $page->defaultSlug;
    }

    /** The address as it would be stored: lower case, words joined by hyphens. */
    public static function normalise(string $slug): string
    {
        return Str::slug($slug);
    }

    /**
     * @param iterable<string> $languages language slugs, e.g. ['en_US']
     * @return array<string, array<string, string>> lang => field => value
     */
    public function values(SitePage $page, iterable $languages): array
    {
        $this->assertSeo($page);
        $values = [];
        foreach ($languages as $lang) {
            foreach (self::FIELDS as $field) {
                $values[$lang][$field] = (string) get_static_option($this->key($page, $lang, $field), '');
            }
        }

        return $values;
    }

    /**
     * Save the page's address (when it can change) and the fields given for
     * each language. A field left out of $input is left as it was.
     *
     * @param array<string, array<string, ?string>> $input lang => field => value
     * @param iterable<string> $languages
     */
    public function save(SitePage $page, ?string $slug, array $input, iterable $languages): void
    {
        $this->assertSeo($page);
        if ($page->slugEditable && $slug !== null) {
            update_static_option($page->seoKey . '_page_slug', self::normalise($slug));
        }
        foreach ($languages as $lang) {
            foreach (self::FIELDS as $field) {
                if (array_key_exists($field, $input[$lang] ?? [])) {
                    update_static_option($this->key($page, $lang, $field), $input[$lang][$field]);
                }
            }
        }
    }

    public function key(SitePage $page, string $lang, string $field): string
    {
        return $page->seoKey . '_page_' . $lang . '_' . $field;
    }

    private function assertSeo(SitePage $page): void
    {
        if (!$page->hasSeo()) {
            throw new \InvalidArgumentException("{$page->title} keeps its SEO on its own content screen.");
        }
    }
}
