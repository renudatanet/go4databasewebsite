<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
@php
    /*
     * <lastmod> is a promise that the page really changed on that date.
     *
     * These used to fall back to now(), so the sitemap told Google that the
     * home page, /email-finder and /affiliate had all been modified at the
     * exact second it was fetched: two fetches four seconds apart returned
     * two different timestamps. Google ignores lastmod across an entire site
     * once it decides the values cannot be trusted, so a made-up date is
     * worse than none at all.
     *
     * lastmod is optional in the sitemap spec, so every entry below prints it
     * only when a genuine date exists and leaves the element out otherwise.
     */
    $about_raw = get_static_option('about_page_updated_at');
    try {
        $about_date = !empty($about_raw) ? \Carbon\Carbon::parse($about_raw) : null;
    } catch (\Throwable $e) {
        // A malformed stored value must not take the whole sitemap down.
        $about_date = null;
    }

    $page_dates = [
        'pricing' => optional(\App\PricePlan::latest('updated_at')->first())->updated_at,
        'about'   => $about_date,
        'contact' => optional(\App\ContactInfoItem::latest('updated_at')->first())->updated_at,
        'faq'     => optional(\App\Faq::latest('updated_at')->first())->updated_at,
        'author'  => optional(\App\Author::latest('updated_at')->first())->updated_at,
    ];
@endphp

    {{-- Homepage.

         The trailing slash is deliberate. url('/') returns the base address
         without one, so this listed https://www.go4database.com while Google
         had indexed https://www.go4database.com/ . Those are the same page,
         but Search Console matches a URL to its sitemap by exact string, so
         the home page reported "No referring sitemaps detected" despite
         sitting at the top of this file. rtrim first, so this still emits
         exactly one slash if url('/') ever starts returning its own.

         No lastmod: the home page is assembled from hardcoded markup and
         several unrelated records, so there is no single honest
         "changed on" date to give. --}}
    <url>
        <loc>{{ rtrim(url('/'), '/') . '/' }}</loc>
        <priority>0.80</priority>
    </url>

    <url>
        <loc>{{ url('/pricing') }}</loc>
@if($page_dates['pricing'])
        <lastmod>{{ $page_dates['pricing']->toAtomString() }}</lastmod>
@endif
        <priority>0.80</priority>
    </url>

    <url>
        <loc>{{ url('/about') }}</loc>
@if($page_dates['about'])
        <lastmod>{{ $page_dates['about']->toAtomString() }}</lastmod>
@endif
        <priority>0.80</priority>
    </url>

    <url>
        <loc>{{ url('/contact') }}</loc>
@if($page_dates['contact'])
        <lastmod>{{ $page_dates['contact']->toAtomString() }}</lastmod>
@endif
        <priority>0.80</priority>
    </url>

    <url>
        <loc>{{ url('/faq') }}</loc>
@if($page_dates['faq'])
        <lastmod>{{ $page_dates['faq']->toAtomString() }}</lastmod>
@endif
        <priority>0.80</priority>
    </url>

    <url>
        <loc>{{ url('/author') }}</loc>
@if($page_dates['author'])
        <lastmod>{{ $page_dates['author']->toAtomString() }}</lastmod>
@endif
        <priority>0.80</priority>
    </url>

    {{-- Email Finder and Affiliate are hardcoded routes rather than Page
         records, so they are listed here directly. No lastmod for the same
         reason as the home page: their content comes from static copy plus
         admin settings, with no single modification date behind it. --}}
    <url>
        <loc>{{ url('/email-finder') }}</loc>
        <priority>0.80</priority>
    </url>

    <url>
        <loc>{{ url('/affiliate') }}</loc>
        <priority>0.80</priority>
    </url>

@foreach ($pages as $post)
    <url>
        <loc>{{ url('/'.$post->slug) }}</loc>
@if($post->updated_at)
        <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
@endif
        <priority>0.80</priority>
    </url>
@endforeach
</urlset>
