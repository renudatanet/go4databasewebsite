<?php

namespace App\Tracking\Ingestion;

use App\Tracking\Models\Page;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Finds or creates the row for a page path. This is what makes every new
 * page appear in the reports on its own, with no setup.
 */
final class PageRegistry
{
    /** A page's title and last-seen time are refreshed at most this often, so the
     *  homepage's row is not rewritten on every single view. */
    private const REFRESH_MINUTES = 60;

    public function touch(string $path, ?string $title, CarbonImmutable $now): Page
    {
        $hash = Page::hashPath($path);

        DB::table('tracking_pages')->insertOrIgnore([
            'path_hash' => $hash,
            'path' => $path,
            'title' => $title,
            'section' => self::section($path),
            'first_seen_at' => $now,
            'last_seen_at' => $now,
        ]);

        /** @var Page $page */
        $page = Page::where('path_hash', $hash)->firstOrFail();

        $stale = $page->last_seen_at === null || $page->last_seen_at->lt($now->subMinutes(self::REFRESH_MINUTES));
        $retitled = $title !== null && $title !== $page->title;
        if ($stale || $retitled) {
            $page->forceFill(['title' => $title ?? $page->title, 'last_seen_at' => $now])->save();
        }

        return $page;
    }

    public function find(string $path): ?Page
    {
        return Page::where('path_hash', Page::hashPath($path))->first();
    }

    /** The first part of the path, used to group pages: "/blog/x" is "blog", "/" is "home". */
    public static function section(string $path): string
    {
        $first = explode('/', trim($path, '/'))[0];

        return $first === '' ? 'home' : mb_substr(strtolower($first), 0, 64);
    }
}
