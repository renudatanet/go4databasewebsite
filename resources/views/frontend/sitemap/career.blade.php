<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">

 <url>
        <loc>{{ url('/career') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <!-- Posts -->
    @foreach ($careers as $post)
    <url>
        <loc>{{ url('/career/'.$post->slug) }}</loc>
        <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
        <priority>0.8</priority>
    </url>
    @endforeach
      @foreach ($careersCategory as $bc)
    <url>
        <loc>{{ url('/career/'.$bc->slug) }}</loc>
        <lastmod>{{ $bc->updated_at->toAtomString() }}</lastmod>
        <priority>0.8</priority>
    </url>
    @endforeach

</urlset>