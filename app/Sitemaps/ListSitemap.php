<?php

namespace App\Sitemaps;

use App\B2BList;
use App\ListCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * The list pages' sitemaps, one per industry (List > Category):
 * /list_sitemap.xml is the index of them and each industry has its own,
 * /list_sitemap_{industry}.xml, holding that industry's list pages.
 *
 * An industry appears once it has a list a visitor can open; an empty
 * sitemap is not valid, so empty industries are left out until their
 * first list is published. Lists whose industry is gone or unpublished go
 * in a sitemap of their own, so no list page drops out of the sitemaps.
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

        // Categories of the same name in several languages share a sitemap.
        $keys = ListCategory::query()
            ->whereRaw("LOWER(status) = 'publish'")
            ->orderBy('name')
            ->get(['id', 'name', 'slug'])
            ->mapWithKeys(fn (ListCategory $category) => [$category->id => [
                'key' => Str::slug($category->slug ?: $category->name) ?: 'industry-' . $category->id,
                'name' => $category->name,
            ]]);

        $groups = collect();
        foreach ($keys as $category) {
            if (!$groups->has($category['key']) && $category['key'] !== self::OTHER) {
                $groups->put($category['key'], ['key' => $category['key'], 'name' => $category['name'], 'lists' => collect()]);
            }
        }
        $other = ['key' => self::OTHER, 'name' => 'Other lists', 'lists' => collect()];
        foreach ($lists as $list) {
            $key = $keys[$list->categories_id]['key'] ?? null;
            if ($key !== null && $groups->has($key)) {
                $groups[$key]['lists']->push($list);
            } else {
                $other['lists']->push($list);
            }
        }
        if ($other['lists']->isNotEmpty()) {
            $groups->put(self::OTHER, $other);
        }

        return $this->groups = $groups->filter(fn (array $group) => $group['lists']->isNotEmpty())->values();
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
