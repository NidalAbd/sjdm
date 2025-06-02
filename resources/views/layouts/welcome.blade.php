@php
    if (!function_exists('getOgLocale')) {
        function getOgLocale()
        {
            $locales = [
                'ar' => 'ar_AR',
                'es' => 'es_ES',
                'zh' => 'zh_CN',
                'hi' => 'hi_IN',
                'pt' => 'pt_PT',
                'ru' => 'ru_RU',
                'de' => 'de_DE',
                'fr' => 'fr_FR',
                'en' => 'en_US', // Default for English
            ];

            return $locales[app()->getLocale()] ?? 'en_US';
        }
    }

    // Default SEO values that can be overridden by individual pages
    $pageTitle = $seoTitle ?? __('title');
    $pageDescription = $seoDescription ?? __('description');
    $pageKeywords = $seoKeywords ?? __('keywords');

    // FIXED: Always use English canonical URL (no language prefix)
    $currentLanguage = app()->getLocale();
    $currentPath = request()->path();

    // Remove language prefix from canonical URL
    if ($currentLanguage !== 'en') {
        $currentPath = preg_replace('/^(ar|es|fr|de|ru|zh|hi|pt)\//', '', $currentPath);
    }

    $pageCanonical = $canonicalUrl ?? url($currentPath);

    // Ensure canonical doesn't have language prefix
    $pageCanonical = preg_replace('/\/(ar|es|fr|de|ru|zh|hi|pt)\//', '/', $pageCanonical);
    $pageCanonical = preg_replace('/\/(ar|es|fr|de|ru|zh|hi|pt)$/', '', $pageCanonical);
@endphp
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Primary Meta Tags -->
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $pageKeywords }}">

    <!-- Robots Meta Tags -->
    @if(request()->has('redirect') && (request()->is('login') || request()->is('register')))
        <meta name="robots" content="noindex, nofollow">
    @elseif(request()->get('page', 1) > 1)
        <meta name="robots" content="noindex, follow">
    @else
        <meta name="robots" content="index, follow">
    @endif

    <!-- FIXED: Canonical URL always points to English version -->
    <link rel="canonical" href="{{ $pageCanonical }}">

    <!-- FIXED: Hreflang tags with proper URL generation -->
    @foreach(['en', 'ar', 'es', 'fr', 'de', 'ru', 'zh', 'hi', 'pt'] as $lang)
        @php
            // Get current path without language prefix
            $cleanPath = preg_replace('/^(ar|es|fr|de|ru|zh|hi|pt)\//', '', request()->path());
            $cleanPath = $cleanPath === '' ? '' : $cleanPath;

            // Generate proper URL for each language
            if ($lang === 'en') {
                $alternateUrl = url($cleanPath);
            } else {
                $alternateUrl = url($lang . '/' . $cleanPath);
            }

            // Preserve query parameters
            if(request()->getQueryString()) {
                $alternateUrl .= '?' . request()->getQueryString();
            }
        @endphp
        <link rel="alternate" hreflang="{{ $lang }}" href="{{ $alternateUrl }}">
    @endforeach

    <!-- Default hreflang pointing to English canonical -->
    <link rel="alternate" hreflang="x-default" href="{{ $pageCanonical }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $pageCanonical }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:locale" content="{{ getOgLocale() }}">
    <meta property="og:site_name" content="SMM-Followers">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $pageCanonical }}">
    <meta property="twitter:title" content="{{ $pageTitle }}">
    <meta property="twitter:description" content="{{ $pageDescription }}">
    <meta property="twitter:image" content="{{ asset('images/og-image.jpg') }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon-96x96.png') }}" type="image/jpeg">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Structured Data - Default Organization -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "SMM-Followers",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('images/logo.png') }}",
            "sameAs": [
                "https://facebook.com/smmfollowers",
                "https://twitter.com/smmfollowers",
                "https://instagram.com/smmfollowers"
            ],
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+971-55-783-0054",
                "contactType": "customer service",
                "email": "info@smm-followers.com"
            }
        }
    </script>

    <!-- Base Structured Data for SMM Service -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Service",
            "serviceType": "Social Media Marketing",
            "provider": {
                "@type": "Organization",
                "name": "SMM-Followers",
                "url": "{{ url('/') }}"
            },
            "description": "SMM-Followers is a leading platform for boosting followers and engagement across various social media platforms.",
            "areaServed": {
                "@type": "Place",
                "name": "Global"
            },
            "offers": {
                "@type": "AggregateOffer",
                "priceCurrency": "USD",
                "lowPrice": "1",
                "highPrice": "100",
                "offerCount": "1000+"
            }
        }
    </script>

    <!-- Add page-specific structured data if available -->
    @if(isset($structuredData))
        <script type="application/ld+json">
            {!! $structuredData !!}
        </script>
    @endif

    <!-- Add breadcrumbs structured data if available -->
    @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "BreadcrumbList",
                "itemListElement": [
            @foreach($breadcrumbs as $index => $breadcrumb)
                {
                    "@type": "ListItem",
                    "position": {{ $index + 1 }},
                    "name": "{{ $breadcrumb['title'] }}",
                    "item": "{{ $breadcrumb['url'] }}"
                }@if(!$loop->last),@endif
            @endforeach
            ]
        }
        </script>
    @endif

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZWMQW2P5G8"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-ZWMQW2P5G8');
    </script>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" rel="stylesheet">

    <!-- Your existing styles -->
    <style>
        html {
            scroll-behavior: smooth;
        }
        /* Your existing styles here... */
    </style>
</head>

<body class="{{ session('dark_mode', false) ? 'dark-mode' : 'light-mode' }}">
<!-- Include Header -->
@include('layouts.header')

<div class="d-flex justify-content-center">
    @include('partials.alertWelcome')
</div>

<!-- Breadcrumbs (if available) -->
@if(isset($breadcrumbs) && count($breadcrumbs) > 0)
    <div class="container mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach($breadcrumbs as $breadcrumb)
                    @if($loop->last)
                        <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['title'] }}</li>
                    @else
                        <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>
@endif

<!-- Main Content -->
<main class="container my-5">
    @yield('content')
</main>

<!-- Include Footer -->
@include('layouts.footer')

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script>
    $(document).ready(function(){
        AOS.init(); // Initialize AOS for animations

        // Your existing script for FAQs
        const faqList = document.getElementById('faq-list');
        if (faqList) {
            const answers = document.querySelectorAll('.faq-answer');
            faqList.addEventListener('click', function(e) {
                if (e.target && e.target.matches('li.list-group-item')) {
                    const answerId = e.target.getAttribute('data-answer');
                    // Hide all answers
                    answers.forEach(answer => answer.style.display = 'none');
                    // Show the selected answer
                    document.getElementById(answerId).style.display = 'block';
                }
            });
        }

        // Initialize Slick Carousel if it exists
        if ($('.mobile-slider').length) {
            $('.mobile-slider').slick({
                autoplay: true,
                autoplaySpeed: 2000,
                arrows: false,
                dots: false,
            });
        }
    });
</script>
</body>
</html>
