<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * The old blog search address, /blog-search?search=sales+lead, moved for good
 * (301) to /blog/search?s=sales+lead, keeping the words and the page number,
 * so old links, bookmarks and search engines land on the new one.
 */
class BlogSearchRedirectController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = array_filter([
            's' => trim((string) ($request->query('s') ?? $request->query('search', ''))),
            'page' => (int) $request->query('page', 0) > 1 ? (int) $request->query('page') : null,
        ], fn ($value) => $value !== null && $value !== '');

        return redirect()->to(route('frontend.blog.search') . ($query ? '?' . http_build_query($query) : ''), 301);
    }
}
