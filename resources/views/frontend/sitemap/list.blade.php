<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">

    <!-- Homepage -->
    <url>
        <loc>{{ url('/list') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Posts -->
 <!-- Posts -->
    @foreach ($list as $post)
    @php
        $randomDays = 10 + ($post->id % 6);
        $lastUpdated = now()->subDays($randomDays);
    @endphp
    <url>
        <loc>{{ url('/list'.$post->slug) }}</loc>
        <lastmod>{{ $lastUpdated->toAtomString() }}</lastmod>
        <priority>0.8</priority>
    </url>
    @endforeach
    



</urlset>