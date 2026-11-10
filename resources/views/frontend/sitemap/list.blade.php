{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
{{-- One industry's list pages (/list_sitemap_{industry}.xml), from
     App\Sitemaps\ListSitemap. lastmod is when the list really changed. --}}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
@if($url['lastmod'])
        <lastmod>{{ $url['lastmod'] }}</lastmod>
@endif
        <priority>0.8</priority>
    </url>
@endforeach
</urlset>
