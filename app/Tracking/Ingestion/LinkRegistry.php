<?php

namespace App\Tracking\Ingestion;

use App\Tracking\Models\Link;
use Illuminate\Support\Facades\DB;

/**
 * Finds or creates the row for a click target. Links are keyed by their
 * destination; buttons (no destination) by their label.
 */
final class LinkRegistry
{
    public function resolve(?string $href, ?string $label, string $kind): Link
    {
        $key = $href !== null ? 'href:' . $href : 'button:' . mb_strtolower((string) $label);
        $hash = sha1($key);

        DB::table('tracking_links')->insertOrIgnore([
            'href_hash' => $hash,
            'href' => $href,
            'host' => $href !== null ? parse_url($href, PHP_URL_HOST) ?: null : null,
            'kind' => $kind,
            'label' => $label,
        ]);

        /** @var Link $link */
        $link = Link::where('href_hash', $hash)->firstOrFail();
        if ($label !== null && $link->label === null) {
            $link->forceFill(['label' => $label])->save();
        }

        return $link;
    }
}
