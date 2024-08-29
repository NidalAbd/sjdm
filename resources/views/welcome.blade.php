<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to {{ config('app.name') }}</title>
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" rel="stylesheet">
</head>

<body>

<!-- Add jQuery script before any other script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('images/MaxPeak.png') }}" alt="{{ config('app.name') }} Logo" class="img-fluid circular-logo me-2">
            <span class="fw-bold text-uppercase text-white">{{ config('app.name') }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('login') }}">Sign In</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('register') }}">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">FAQ</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownLanguage" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-globe"></i> Language
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownLanguage">
                        <li><a class="dropdown-item" href="?lang=en">English</a></li>
                        <li><a class="dropdown-item" href="?lang=ar">Arabic</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#" id="darkModeToggle">
                        <i class="fas fa-moon"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="hero-sec" class="hero-section d-flex align-items-center justify-content-center text-center text-white" style="background-image: url('{{ asset('images/headerBackground.jpg') }}');">
    <div class="overlay position-absolute w-100 h-100"></div>
    <div class="container position-relative z-index-2">
        <h1 class="display-4 fw-bold mb-4 text-gradient" data-aos="fade-up" data-aos-duration="1500">
            Better <span class="point">Ideas</span> for Fast Growth
        </h1>
        <h2 class="lead mb-4" data-aos="fade-up" data-aos-duration="2000">
            All Social Media Services Just in 1 Place.
        </h2>
        <a href="{{ route('register') }}" class="btn btn-lg btn-primary rounded-pill px-4" data-aos="fade-up" data-aos-duration="2500">
            Sign Up Now
        </a>
    </div>
</section>

<main class="container my-5">
    <div class="mb-4">
        @include('widgets.platforms')
    </div>
    <div class="mb-4">
        @include('widgets.fast-login')
    </div>

    <div class="mb-4">
        @include('widgets.numerical-widgets')
    </div>
    <div class="mb-4">
        @include('widgets.payment-methods')
    </div>
    <div class="mb-4">
        @include('widgets.support-widget')
    </div>
    <div class="row mb-4">
        @include('widgets.discounts')
    </div>
    <div class="mb-4">
        @include('widgets.features')
    </div>

</main>

@include('layouts.footer')

<!-- Include Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Include AOS for animations and Slick for the slider -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script>
    $(document).ready(function(){
        AOS.init(); // Initialize AOS for animations

        // Initialize Slick Carousel
        $('.mobile-slider').slick({
            autoplay: true,
            autoplaySpeed: 2000,
            arrows: false,
            dots: false,
        });

        // Check localStorage for theme
        const savedTheme = localStorage.getItem('theme') || 'light-mode';
        document.body.classList.add(savedTheme);
        updateThemeIcon(savedTheme);

        // Theme Toggle
        $('#darkModeToggle').on('click', function() {
            const body = $('body');
            const isDarkMode = body.hasClass('dark-mode');

            body.toggleClass('dark-mode', !isDarkMode);
            const newTheme = isDarkMode ? 'light-mode' : 'dark-mode';
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });

        // Update theme icon based on the current theme
        function updateThemeIcon(theme) {
            const themeIcon = $('#darkModeToggle i');
            themeIcon.toggleClass('fa-moon', theme !== 'dark-mode');
            themeIcon.toggleClass('fa-sun', theme === 'dark-mode');
        }
    });
</script>

<style>
    body {
        background-color: #f8f9fa; /* Light mode background */
        transition: background-color 0.3s ease, color 0.3s ease;
        color: #212529; /* Light mode text color */
    }

    .navbar {
        background: rgb(15, 16, 17);
        transition: background-color 0.3s ease;
    }

    .navbar .nav-link {
        font-size: 1rem;
        padding: 0.5rem 1rem;
        transition: color 0.3s ease;
    }

    .navbar .nav-link:hover {
        color: #ff6347; /* Custom hover color */
    }

    .navbar-brand .circular-logo {
        border-radius: 50%;
        max-width: 40px;
    }

    .navbar-toggler {
        border: none;
        outline: none;
    }

    .hero-section {
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        height: calc(40vh - 56px); /* Adjust height to account for navbar */
        color: #fff;
        padding-top: 0;
        margin-top: 56px; /* Space for the fixed navbar */
    }

    .text-gradient {
        background: linear-gradient(90deg, #ff6347, #ff4757);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .btn-primary {
        background-image: linear-gradient(90deg, #007bff, #0056b3);
        border: none;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-image: linear-gradient(90deg, #0056b3, #007bff);
    }

    /* Dark mode styles */
    .dark-mode {
        background-color: #121212; /* Dark mode background */
        color: #ffffff; /* Dark mode text color */
    }

    .dark-mode .navbar {
        background-color: #0f1011 !important;
    }

    .dark-mode .hero-section {
        color: #fff;
    }

    .dark-mode .btn-primary {
        background-image: linear-gradient(90deg, #0056b3, #007bff);
    }

    .dark-mode .navbar .nav-link {
        color: #fff;
    }

    .dark-mode .navbar .nav-link:hover {
        color: #ff6347;
    }

    .dark-mode .navbar-brand .text-white {
        color: #fff;
    }
</style>

</body>
</html>
