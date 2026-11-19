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
 *
 * Pages with fixed addresses (Affiliate, Email Verifier...) have only a
 * meta title (meta_tags) and description, under the keys SitePage::$metaKeys
 * names, the same ones their own content screens use.
 */
final class PageSeo
{
    /** Per-language fields, in the order the form shows them. */
    public const FIELDS = ['name', 'meta_tags', 'meta_description', 'schema_code', 'meta_image'];

    public function __construct(private PageRegistry $registry)
    {
    }

    /**
     * What a page's template prints: one of its SEO fields in the visitor's
     * language, '' when none is set (the template then keeps its own wording).
     */
    public function valueFor(string $pageKey, string $field, ?string $lang = null): string
    {
        $page = $this->registry->find($pageKey);
        if ($page === null || !$page->hasSeo() || !in_array($field, $this->fields($page), true)) {
            return '';
        }

        return trim((string) get_static_option($this->key($page, $lang ?? get_user_lang(), $field), ''));
    }

    /** The fields this page's SEO tab edits, in form order. */
    public function fields(SitePage $page): array
    {
        $this->assertSeo($page);

        return $page->metaKeys !== null ? array_keys($page->metaKeys) : self::FIELDS;
    }

    /** The page's current address, without slashes, e.g. "price-plan". */
    public function slug(SitePage $page): string
    {
        $this->assertSeo($page);
        if (!$page->slugEditable || $page->seoKey === null) {
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
            foreach ($this->fields($page) as $field) {
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
        if ($page->slugEditable && $page->seoKey !== null && $slug !== null) {
            update_static_option($page->seoKey . '_page_slug', self::normalise($slug));
        }
        foreach ($languages as $lang) {
            foreach ($this->fields($page) as $field) {
                if (array_key_exists($field, $input[$lang] ?? [])) {
                    update_static_option($this->key($page, $lang, $field), $input[$lang][$field]);
                }
            }
        }
    }

    public function key(SitePage $page, string $lang, string $field): string
    {
        if ($page->metaKeys !== null) {
            return str_replace('{lang}', $lang, $page->metaKeys[$field]);
        }

        return $page->seoKey . '_page_' . $lang . '_' . $field;
    }

    private function assertSeo(SitePage $page): void
    {
        if (!$page->hasSeo()) {
            throw new \InvalidArgumentException("{$page->title} keeps its SEO on its own content screen.");
        }
    }
}
