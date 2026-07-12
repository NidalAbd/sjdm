@php
    $seo = $seo ?? [
        'title' => 'Best SMM Panel - Buy Instagram, TikTok, YouTube Followers | Cheapest Prices',
        'description' => 'The cheapest SMM panel for Instagram, TikTok, YouTube, Facebook followers, likes, views and more. Instant delivery, 24/7 support, starting at $0.01.',
        'keywords' => 'SMM panel, social media marketing, buy instagram followers, buy tiktok followers, buy youtube subscribers',
        'locale' => 'en',
        'supportedLangs' => ['en','zh','hi','es','fr','ar','pt','ru','ja','de','ko','tr','it','pl','nl','vi','th'],
        'baseUrl' => config('app.url', 'https://smm-followerss.com'),
        'page' => 'home',
    ];
    $locale = $seo['locale'] ?? app()->getLocale();
    $isRtl = in_array($locale, ['ar']);
    $supportedLangs = $seo['supportedLangs'] ?? array_keys(config('app.available_locales'));
    $baseUrl = rtrim($seo['baseUrl'] ?? config('app.url'), '/');
    $currentPath = request()->path();

    // Clean path for hreflang generation
    $cleanPath = preg_replace('/^(ar|es|fr|de|ru|zh|hi|pt|ja|ko|tr|it|pl|nl|vi|th)\//', '', $currentPath);
    $cleanPath = preg_replace('/^(ar|es|fr|de|ru|zh|hi|pt|ja|ko|tr|it|pl|nl|vi|th)$/', '', $cleanPath);
    if ($cleanPath === '' || $cleanPath === '/') $cleanPath = '';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Primary SEO Meta Tags - Dynamic per page/language -->
    <title>{{ $seo['title'] }}</title>
    <meta name="title" content="{{ $seo['title'] }}">
    <meta name="description" content="{{ $seo['description'] }}">
    @if(!empty($seo['keywords']))
    <meta name="keywords" content="{{ $seo['keywords'] }}">
    @endif
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="SMM Panel">
    <meta name="publisher" content="SMM Panel">
    <meta name="language" content="{{ $locale }}">
    <meta name="revisit-after" content="1 days">
    <meta name="rating" content="general">
    <meta name="distribution" content="global">

    <!-- Geographic Meta Tags -->
    <meta name="geo.region" content="JO">
    <meta name="geo.placename" content="Jordan">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="SMM Panel">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="SMM Panel - Social Media Marketing Services">
    <meta property="og:locale" content="{{ $locale === 'ar' ? 'ar_SA' : ($locale === 'en' ? 'en_US' : $locale) }}">
    @foreach($supportedLangs as $altLang)
    @if($altLang !== $locale)
    <meta property="og:locale:alternate" content="{{ $altLang === 'ar' ? 'ar_SA' : ($altLang === 'en' ? 'en_US' : $altLang) }}">
    @endif
    @endforeach

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image" content="{{ asset('images/logo.png') }}">
    <meta name="twitter:image:alt" content="SMM Panel - Social Media Marketing Services">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Hreflang tags for all 17 languages -->
    <link rel="alternate" hreflang="en" href="{{ $baseUrl }}/{{ $cleanPath }}">
    @foreach($supportedLangs as $altLang)
    @if($altLang !== 'en')
    <link rel="alternate" hreflang="{{ $altLang }}" href="{{ $baseUrl }}/{{ $altLang }}{{ $cleanPath ? '/' . $cleanPath : '' }}">
    @endif
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $baseUrl }}/{{ $cleanPath }}">

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logo.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#6366f1">
    <meta name="msapplication-TileColor" content="#6366f1">

    <!-- Mobile Web App -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SMM Panel">
    <meta name="application-name" content="SMM Panel">
    <meta name="format-detection" content="telephone=no">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet">

    <!-- Schema.org JSON-LD - Organization -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "SMM Panel",
        "alternateName": "SMMJD",
        "url": "{{ $baseUrl }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/logo.png') }}",
            "width": 512,
            "height": 512
        },
        "description": "{{ $seo['description'] }}",
        "foundingDate": "2020",
        "contactPoint": [{
            "@type": "ContactPoint",
            "contactType": "customer service",
            "email": "support@smmjd.com",
            "availableLanguage": ["English", "Arabic", "Spanish", "French", "German", "Portuguese", "Russian", "Chinese", "Japanese", "Korean", "Turkish", "Italian", "Polish", "Dutch", "Vietnamese", "Thai", "Hindi"],
            "areaServed": "Worldwide"
        }],
        "sameAs": [],
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "JO"
        }
    }
    </script>

    <!-- Schema.org JSON-LD - WebSite with Search Action -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "SMM Panel",
        "alternateName": "Best SMM Panel",
        "url": "{{ $baseUrl }}",
        "description": "{{ $seo['description'] }}",
        "inLanguage": {!! json_encode($supportedLangs) !!},
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "{{ $baseUrl }}/all-services?search={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    <!-- Schema.org JSON-LD - Service -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Service",
        "serviceType": "Social Media Marketing",
        "provider": {
            "@type": "Organization",
            "name": "SMM Panel"
        },
        "name": "SMM Panel Services",
        "description": "{{ $seo['description'] }}",
        "areaServed": "Worldwide",
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "SMM Services",
            "itemListElement": [
                {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Instagram Followers"}},
                {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "TikTok Views"}},
                {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "YouTube Subscribers"}},
                {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Facebook Likes"}},
                {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Twitter Followers"}},
                {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Telegram Members"}}
            ]
        }
    }
    </script>

    <!-- Schema.org JSON-LD - FAQ -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            {
                "@type": "Question",
                "name": "How fast is the delivery?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Most orders start within 0-1 hour and complete based on quantity. Instant delivery is available for many services."
                }
            },
            {
                "@type": "Question",
                "name": "Is this safe for my account?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes! We use safe methods that comply with platform guidelines. Your account security is our priority."
                }
            },
            {
                "@type": "Question",
                "name": "What payment methods do you accept?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "We accept credit cards, PayPal, cryptocurrency, and various local payment methods."
                }
            },
            {
                "@type": "Question",
                "name": "Do you offer refills?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes, many of our services include free refills. Check the service description for refill availability."
                }
            }
        ]
    }
    </script>

    <!-- Schema.org JSON-LD - Breadcrumb -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "{{ $baseUrl }}"
            }
        ]
    }
    </script>

    <!-- Google Analytics (GA4) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZWMQW2P5G8"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-ZWMQW2P5G8');
    </script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <noscript>
        <div style="padding: 20px; text-align: center; background: #f44336; color: white;">
            Please enable JavaScript to use this SMM Panel. JavaScript is required for ordering services and account management.
        </div>
    </noscript>
    <div id="app"></div>
</body>
</html>
