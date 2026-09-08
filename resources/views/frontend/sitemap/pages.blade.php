<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">


     {{-- Homepage --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
<priority>0.80</priority>
</url>

    <url>
<loc>{{ url('/pricing') }}</loc>
<lastmod>{{
            optional(\App\PricePlan::latest('updated_at')->first())->updated_at?->toAtomString()
            ?? now()->toAtomString()
        }}</lastmod>
<priority>0.80</priority>
</url>
<url>
<loc>{{ url('/about') }}</loc>
<lastmod>{{ \Carbon\Carbon::parse(get_static_option('about_page_updated_at'))->toAtomString() }}</lastmod>
<priority>0.80</priority>
</url>

<url>
<loc>{{ url('/contact') }}</loc>
<lastmod>{{
            optional(\App\ContactInfoItem::latest('updated_at')->first())->updated_at?->toAtomString()
            ?? now()->toAtomString()
        }}</lastmod>
<priority>0.80</priority>
</url>

<url>
<loc>{{ url('/faq') }}</loc>
<lastmod>{{
            optional(\App\Faq::latest('updated_at')->first())->updated_at?->toAtomString()
            ?? now()->toAtomString()
        }}</lastmod>
<priority>0.80</priority>
</url>


<url>
<loc>{{ url('/author') }}</loc>
<lastmod>{{
            optional(\App\Author::latest('updated_at')->first())->updated_at?->toAtomString()
            ?? now()->toAtomString()
        }}</lastmod>
<priority>0.80</priority>
</url>

 @foreach ($pages as $post)
<url>
<loc>{{ url('/'.$post->slug) }}</loc>
<lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
<priority>0.80</priority>
</url>
 @endforeach
</urlset>