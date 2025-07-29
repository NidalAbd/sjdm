@php
    if (!function_exists('getOgLocale')) {
        function getOgLocale()
        {
            $locales = [
                'ar' => 'ar_AR',
                'en' => 'en_US',
            ];
            return $locales[app()->getLocale()] ?? 'en_US';
        }
    }

    $currentLanguage = app()->getLocale();

    // Default SEO values that can be overridden by individual pages
    $pageTitle = $seoTitle ?? __('title');
    $pageDescription = $seoDescription ?? __('description');
    $pageKeywords = $seoKeywords ?? __('keywords');

    // Canonical URL (always English version)
    $pageCanonical = $canonicalUrl ?? url(request()->path());

    // Remove /ar/ from canonical if present
    $pageCanonical = str_replace('/ar/', '/', $pageCanonical);
    $pageCanonical = preg_replace('/\/ar$/', '', $pageCanonical);
    $pageCanonical = str_replace('/ar', '', $pageCanonical);

    // Generate alternate URLs if not provided
    if (!isset($alternateUrls)) {
        $currentPath = request()->path();
        $cleanPath = str_replace('ar/', '', $currentPath);
        $cleanPath = str_replace('ar', '', $cleanPath);
        $cleanPath = ltrim($cleanPath, '/');

        $alternateUrls = [
            'en' => url($cleanPath ?: '/'),
            'ar' => url('ar/' . ($cleanPath ?: ''))
        ];

        // Preserve query parameters
        if (request()->getQueryString()) {
            $alternateUrls['en'] .= '?' . request()->getQueryString();
            $alternateUrls['ar'] .= '?' . request()->getQueryString();
        }
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $currentLanguage) }}" dir="{{ $currentLanguage === 'ar' ? 'rtl' : 'ltr' }}">
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

    <!-- Canonical URL (always English version) -->
    <link rel="canonical" href="{{ $pageCanonical }}">

    <!-- Hreflang Tags -->
    @foreach($alternateUrls as $lang => $url)
        <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $alternateUrls['en'] }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $pageCanonical }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:locale" content="{{ getOgLocale() }}">
    <meta property="og:site_name" content="SMM-Followers">
    @if($currentLanguage === 'ar')
        <meta property="og:locale:alternate" content="en_US">
    @else
        <meta property="og:locale:alternate" content="ar_AR">
    @endif

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

    <!-- Modern Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @if($currentLanguage === 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @endif

    <!-- Font Loading Optimization -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Modern CSS Libraries -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" rel="stylesheet">

    <!-- Font Loading Fix -->
    <style>
        /* Font Loading Optimization */
        .wf-loading {
            visibility: hidden;
        }
        
        .wf-active {
            visibility: visible;
        }
        
        /* Prevent layout shifts during font loading */
        body {
            font-display: swap;
        }
        
        /* Ensure consistent font rendering */
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }
        
        /* Fix for navbar height consistency */
        .navbar {
            min-height: 70px;
            display: flex;
            align-items: center;
        }
        
        /* Ensure consistent spacing */
        .navbar-nav {
            align-items: center;
        }
        
        .navbar-nav .nav-item {
            display: flex;
            align-items: center;
        }
        
        /* Prevent text wrapping in navbar */
        .navbar-nav .nav-link {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Fix for container width issues */
        @media (min-width: 1200px) {
            .container {
                max-width: 1140px;
            }
        }
        
        @media (min-width: 1400px) {
            .container {
                max-width: 1320px;
            }
        }
    </style>

    <!-- Modern Custom Styles -->
    <style>
        :root {
            /* Modern Color Palette */
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --secondary-color: #8b5cf6;
            --accent-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
            
            /* Neutral Colors */
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            
            /* Light Theme */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
        
        /* Dark Theme Variables */
        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: #334155;
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            font-size: 16px;
        }

        body {
            font-family: {{ $currentLanguage === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }};
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            transition: all 0.3s ease;
            overflow-x: hidden;
        }

        /* Modern Typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .display-1 { font-size: 3.5rem; font-weight: 800; }
        .display-2 { font-size: 3rem; font-weight: 800; }
        .display-3 { font-size: 2.5rem; font-weight: 700; }
        .display-4 { font-size: 2rem; font-weight: 700; }

        /* Modern Buttons */
        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        }

        /* Modern Cards */
        .card {
            border: none;
            border-radius: 20px;
            background: var(--bg-primary);
            box-shadow: var(--shadow-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }

        /* Glassmorphism Effect */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
        }

        [data-theme="dark"] .glass {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Modern Forms */
        .form-control {
            border-radius: 12px;
            border: 2px solid var(--border-color);
            padding: 16px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .form-floating > .form-control {
            padding-top: 24px;
            padding-bottom: 8px;
        }

        .form-floating > label {
            padding: 16px 20px;
            color: var(--text-secondary);
        }

        /* Modern Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        /* Utility Classes */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .bg-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .rounded-xl {
            border-radius: 20px;
        }

        .shadow-soft {
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
        }

        /* RTL Support */
        @if($currentLanguage === 'ar')
        body {
            direction: rtl;
            text-align: right;
        }

        .navbar-nav {
            margin-right: auto !important;
            margin-left: 0 !important;
        }

        .dropdown-menu {
            right: 0;
            left: auto;
        }

        .text-start {
            text-align: right !important;
        }

        .text-end {
            text-align: left !important;
        }

        .me-auto {
            margin-left: auto !important;
            margin-right: 0 !important;
        }

        .ms-auto {
            margin-right: auto !important;
            margin-left: 0 !important;
        }
        @endif

        /* Responsive Design */
        @media (max-width: 768px) {
            .display-1 { font-size: 2.5rem; }
            .display-2 { font-size: 2rem; }
            .display-3 { font-size: 1.75rem; }
            .display-4 { font-size: 1.5rem; }
            
            .btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
</head>

<body data-theme="light">
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
                        <li class="breadcrumb-item">
                            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>
@endif

<!-- Main Content -->
@yield('content')

<!-- Include Footer -->
@include('layouts.footer')

<!-- Modern Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script>
    // Modern Theme Management
    class ThemeManager {
        constructor() {
            this.theme = localStorage.getItem('theme') || 'light';
            this.init();
        }

        init() {
            document.body.setAttribute('data-theme', this.theme);
            this.updateThemeIcon();
            this.bindEvents();
        }

        toggle() {
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            document.body.setAttribute('data-theme', this.theme);
            localStorage.setItem('theme', this.theme);
            this.updateThemeIcon();
        }

        updateThemeIcon() {
            const icon = document.querySelector('#darkModeToggle i');
            if (icon) {
                icon.className = this.theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
        }

        bindEvents() {
            const toggleBtn = document.getElementById('darkModeToggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => this.toggle());
            }
        }
    }

    // Modern Animation Manager
    class AnimationManager {
        constructor() {
            this.init();
        }

        init() {
            AOS.init({
                duration: 800,
                easing: 'ease-out-cubic',
                once: true,
                offset: 100
            });
        }

        addScrollAnimation(element, animation = 'fadeInUp') {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        observer.unobserve(entry.target);
                    }
                });
            });

            observer.observe(element);
        }
    }

    // Modern Form Manager
    class FormManager {
        constructor() {
            this.init();
        }

        init() {
            this.setupFloatingLabels();
            this.setupFormValidation();
        }

        setupFloatingLabels() {
            const inputs = document.querySelectorAll('.form-floating input');
            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    input.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', () => {
                    if (!input.value) {
                        input.parentElement.classList.remove('focused');
                    }
                });
            });
        }

        setupFormValidation() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<span class="loading"></span> Loading...';
                        submitBtn.disabled = true;
                    }
                });
            });
        }
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        new ThemeManager();
        new AnimationManager();
        new FormManager();

        // Initialize Slick Carousel
        if ($('.mobile-slider').length) {
            $('.mobile-slider').slick({
                autoplay: true,
                autoplaySpeed: 3000,
                arrows: false,
                dots: true,
                rtl: {{ $currentLanguage === 'ar' ? 'true' : 'false' }},
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
        }

        // Add smooth scrolling to all links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
</script>

<!-- Additional Scripts for specific pages -->
@stack('scripts')
</body>
</html>
