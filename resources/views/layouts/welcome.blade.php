<!-- resources/views/layouts/welcome.blade.php -->
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
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('title') }}</title>
        <meta name="keywords" content="{{ __('keywords') }}">
        <meta name="description" content="{{ __('description') }}">
        <meta property="og:title" content="{{ __('keywords') }}" />
        <meta property="og:description" content="{{ __('description') }}" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:type" content="website" />
        <meta property="og:locale" content="{{ getOgLocale() }}" />
        <meta property="og:site_name" content="SJDM" />
        <meta property="og:image:type" content="image/png" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <link rel="canonical" href="{{ url()->current() }}" />

    <link rel="icon" href="{{ asset('images/favicon-96x96.png') }}" type="image/jpeg">
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-4W3CL6YLBQ');
        </script>
        <!-- Schema Markup (Structured Data) -->
        <script type="application/ld+json">
            {
              "@context": "https://schema.org",
              "@type": "Service",
              "serviceType": "Social Media Marketing",
              "provider": {
                "@type": "Organization",
                "name": "SJDM",
                "url": "https://sjdm.store"
              },
              "description": "SJDM is a leading platform for boosting followers and engagement across various social media platforms.",
              "areaServed": {
                "@type": "Place",
                "name": "Arab World"
              },
              "offers": {
                "@type": "Offer",
                "priceCurrency": "USD",
                "price": "Starting at $10",
                "url": "https://sjdm.store/pricing"
              }
            }
        </script>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" rel="stylesheet">
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
    <style>
        /* Common styles */
        .faq-answer {
            display: none; /* Hide all answers by default */
        }

        .about-us-title {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .about-us-text {
            font-size: 1rem;
            line-height: 1.5;
        }

        /* Light mode styles */
        body.light-mode .list-group-item,
        body.light-mode .about-us-title,
        body.light-mode .about-us-text {
            background-color: #f8f9fa;
            color: #000;
        }

        body.light-mode .list-group-item-action:hover,
        body.light-mode .list-group-item-action:focus {
            background-color: #121111;
            color: #000;
        }

        /* Dark mode styles */
        body.dark-mode .list-group-item,
        body.dark-mode .platform-title,
        body.dark-mode .about-us-title,
        body.dark-mode .about-us-text,
        body.dark-mode .card-body,
        body.dark-mode .fw-bold {
            color: #fff;
        }
        body.dark-mode .list-group-item
        {
            background-color: #333;
            color: #fff;
        }
        body.dark-mode .list-group-item-action:hover,
        body.dark-mode .list-group-item-action:focus {
            background-color: #444;
            color: #fff;
        }

        /* Light mode styles */
        body.light-mode .support-title,
        body.light-mode .support-text {
            color: #000;
        }

        /* Dark mode styles */
        body.dark-mode .support-title,
        body.dark-mode .support-text {
            color: #fff;
        }

        .support-title {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .support-text {
            font-size: 1rem;
            line-height: 1.5;
        }

        .support-option, .support-faq, .support-ticket {
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .support-option:hover, .support-faq:hover, .support-ticket:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .dark-mode .support-option, .dark-mode .support-faq, .dark-mode .support-ticket {
            border-color: #444;
            background-color: #333;
        }

        .dark-mode .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .dark-mode .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        /* Light mode styles */
        body.light-mode .platform-title,
        body.light-mode .privacy-title,
        body.light-mode .privacy-text {
            color: #000;
        }

        /* Dark mode styles */
        body.dark-mode .platform-title,
        body.dark-mode .privacy-title,
        body.dark-mode .privacy-text {
            color: #fff;
        }

        .privacy-title {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .privacy-text {
            font-size: 1rem;
            line-height: 1.5;
        }

        .contact-details a {
            color: inherit;
            text-decoration: underline;
        } /* Light mode styles */
        body.light-mode .platform-title,
        body.light-mode .contact-title,
        body.light-mode .contact-text {
            color: #000;
        }

        /* Dark mode styles */
        body.dark-mode .platform-title,
        body.dark-mode .contact-title,
        body.dark-mode .contact-text {
            color: #fff;
        }

        .contact-title {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .contact-text {
            font-size: 1rem;
            line-height: 1.5;
        }

        .contact-details a {
            color: inherit;
            text-decoration: underline;
        }

        /* Light mode styles */
        body.light-mode .platform-title,
        body.light-mode .contact-title,
        body.light-mode .contact-text,
        body.light-mode .contact-link {
            color: #000;
        }
        body.light-mode .contact-info-box {
            background-color: #f8f9fa;
            color: #000;
        }
        body.light-mode .contact-icon {
            color: #000;
        }

        /* Dark mode styles */
        body.dark-mode .platform-title,
        body.dark-mode .contact-title,
        body.dark-mode .contact-text,
        body.dark-mode .contact-link {
            color: #fff;
        }
        body.dark-mode .contact-info-box {
            background-color: #333;
            color: #fff;
        }
        body.dark-mode .contact-icon {
            color: #fff;
        }

        /* Common styles */
        .contact-title {
            font-size: 1.75rem;
            font-weight: bold;
        }

        .contact-text {
            font-size: 1rem;
            line-height: 1.5;
        }

        .contact-link {
            text-decoration: none;
            color: inherit;
        }

        .contact-info-box {
            border: 1px solid #ddd;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .contact-icon {
            vertical-align: middle;
        }
    </style>

</head>

<body>
<!-- Include Header -->
@include('layouts.header')
<div class="d-flex justify-content-center">
    @include('partials.alertWelcome')

</div>

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
        const faqList = document.getElementById('faq-list');
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
        // Initialize Slick Carousel
        $('.mobile-slider').slick({
            autoplay: true,
            autoplaySpeed: 2000,
            arrows: false,
            dots: false,
        });
    });
</script>
</body>
</html>
