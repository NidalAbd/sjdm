<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>Best SMM Panel - Buy Instagram, TikTok, YouTube Followers | Cheapest Prices</title>
    <meta name="description" content="The cheapest SMM panel for Instagram, TikTok, YouTube, Facebook followers, likes, views and more. Instant delivery, 24/7 support, starting at $0.01.">
    <meta name="keywords" content="smm panel, buy instagram followers, buy tiktok followers, buy youtube subscribers, social media marketing, cheap smm, instant delivery">
    <meta name="robots" content="index, follow">
    <meta name="author" content="SMM Panel">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Best SMM Panel - Buy Instagram, TikTok, YouTube Followers">
    <meta property="og:description" content="The cheapest SMM panel for social media marketing. Instant delivery, 24/7 support.">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="Best SMM Panel - Buy Instagram, TikTok, YouTube Followers">
    <meta property="twitter:description" content="The cheapest SMM panel for social media marketing. Instant delivery, 24/7 support.">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url('/') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet">

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "SMM Panel",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/logo.png') }}",
        "description": "The cheapest SMM panel for Instagram, TikTok, YouTube, Facebook followers, likes, views and more.",
        "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "customer service",
            "email": "support@smmjd.com",
            "availableLanguage": ["English", "Arabic"]
        }
    }
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "SMM Panel",
        "url": "{{ url('/') }}",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ url('/all-services') }}?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
</body>
</html>
