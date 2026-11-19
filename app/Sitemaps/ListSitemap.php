<?php

namespace App\Sitemaps;

use App\B2BList;
use App\ListCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * The list pages' sitemaps, one per industry (List > Category), listed
 * straight in sitemap.xml: sitemap.xml > /list_sitemap_{industry}.xml >
 * that industry's list pages. Two levels, no index in between.
 *
 * An industry appears once it has a list a visitor can open; an empty
 * sitemap is not valid, so empty industries are left out until their
 * first list is added. A list goes in its category's sitemap (whatever the
 * category's status: the list page itself is public). A list whose
 * category is gone goes by its address instead, /healthcare/japan in
 * Healthcare's, so a category slip in the admin can't move its pages out.
 * Only lists that match no industry at all go in the uncategorized one.
 */
final class ListSitemap
{
    /** The key of the sitemap for lists without a published industry. */
    public const OTHER = 'uncategorized';

    private ?Collection $groups = null;

    /**
     * The industries with lists, in name order, then the uncategorized
     * lists if there are any.
     *
     * @return Collection<int, array{key: string, name: string, loc: string, lastmod: ?string, count: int}>
     */
    public function industries(): Collection
    {
        return $this->groups()->map(fn (array $group) => [
            'key' => $group['key'],
            'name' => $group['name'],
            'loc' => url('/list_sitemap_' . $group['key'] . '.xml'),
            'lastmod' => self::atom($group['lists']->max('updated_at')),
            'count' => $group['lists']->count(),
        ])->values();
    }

    /**
     * The list pages of one industry, or null when it has none.
     *
     * @return Collection<int, array{loc: string, lastmod: ?string}>|null
     */
    public function urls(string $key): ?Collection
    {
        $group = $this->groups()->firstWhere('key', $key);
        if ($group === null) {
            return null;
        }

        return $group['lists']->map(fn (B2BList $list) => [
            'loc' => url('/list/' . ltrim((string) $list->slug, '/')),
            'lastmod' => self::atom($list->updated_at),
        ])->values();
    }

    /** When any visible list last changed (for /list and the index), or null. */
    public function lastModified(): ?string
    {
        return self::atom($this->groups()->flatMap(fn (array $group) => $group['lists'])->max('updated_at'));
    }

    /** @return Collection<int, array{key: string, name: string, lists: Collection<int, B2BList>}> */
    private function groups(): Collection
    {
        if ($this->groups !== null) {
            return $this->groups;
        }

        // The lists a visitor can open: FrontendController::list_single
        // shows every list but drafts (imported rows carry status "1").
        $lists = B2BList::query()
            ->select(['id', 'slug', 'categories_id', 'updated_at'])
            ->whereNotNull('slug')->where('slug', '!=', '')
            ->whereRaw("LOWER(COALESCE(status, '')) <> 'draft'")
            ->orderBy('id')
            ->get();

        // Category id => sitemap key. Categories of the same name in several
        // languages share a key, so they share a sitemap.
        $groups = collect();
        $keyOf = [];
        $categories = ListCategory::query()->orderBy('name')->get(['id', 'name', 'slug']);
        foreach ($categories as $category) {
            $key = Str::slug($category->slug ?: $category->name) ?: 'industry-' . $category->id;
            if ($key === self::OTHER) {
                continue;
            }
            $keyOf[$category->id] = $key;
            if (!$groups->has($key)) {
                $groups->put($key, ['key' => $key, 'name' => $category->name, 'lists' => collect()]);
            }
        }

        $other = ['key' => self::OTHER, 'name' => 'Other lists', 'lists' => collect()];
        foreach ($lists as $list) {
            $key = $keyOf[$list->categories_id] ?? $this->keyFromAddress((string) $list->slug, $groups);
            if ($key === null) {
                $other['lists']->push($list);
                continue;
            }
            if (!$groups->has($key)) {
                $groups->put($key, ['key' => $key, 'name' => Str::headline($key), 'lists' => collect()]);
            }
            $groups[$key]['lists']->push($list);
        }

        $groups = $groups->filter(fn (array $group) => $group['lists']->isNotEmpty())
            ->sortBy(fn (array $group) => Str::lower($group['name']))
            ->values();
        if ($other['lists']->isNotEmpty()) {
            $groups->push($other);
        }

        return $this->groups = $groups;
    }

    /**
     * The industry a list's address names, for a list whose category is
     * gone: its first part, when that is a known industry or the address
     * has more parts (/healthcare/japan). A one-part address that names no
     * industry (/some-list) is left uncategorized.
     */
    private function keyFromAddress(string $slug, Collection $groups): ?string
    {
        $parts = array_values(array_filter(explode('/', $slug), 'strlen'));
        $key = Str::slug($parts[0] ?? '');
        if ($key === '' || $key === self::OTHER) {
            return null;
        }

        return $groups->has($key) || count($parts) > 1 ? $key : null;
    }

    private static function atom($date): ?string
    {
        if (empty($date)) {
            return null;
        }
        try {
            return Carbon::parse($date)->toAtomString();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
