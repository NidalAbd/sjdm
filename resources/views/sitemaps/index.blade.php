<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($sitemaps as $sitemap)
        <sitemap>
            <loc>{{ $sitemap['url'] }}</loc>
            @if(isset($sitemap['lastmod']))
                <lastmod>{{ $sitemap['lastmod'] }}</lastmod>
            @endif
        </sitemap>
    @endforeach
</sitemapindex>
