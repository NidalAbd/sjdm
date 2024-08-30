<!-- Header Blade File: header.blade.php -->

<style>
    body {
        background-color: #f8f9fa; /* Light mode background */
        transition: background-color 0.3s ease, color 0.3s ease;
        color: #212529; /* Light mode text color */
        padding-top: 56px; /* Add padding to the top to accommodate the fixed navbar */
    }

    .navbar {
        background-color: #1f2123 !important;
        transition: background-color 0.3s ease;
    }

    .dark-mode .navbar {
        background-color: #1f2123 !important;
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
        background-image: url('{{ asset('images/payement/4801645.jpg') }}'); /* Background image for hero section */
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        height: 50vh; /* Adjust height to ensure full display */
        color: #fff;
        position: relative;
        padding-top: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        transition: background 0.3s ease, color 0.3s ease; /* Add transition for smooth theme change */
    }

    .hero-bg {
        background: rgba(0, 0, 0, 0.5); /* Darker overlay for better contrast */
        opacity: 0.85;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
        transition: background 0.3s ease; /* Transition for background change */
    }

    .dark-mode .hero-bg {
        background: rgba(0, 0, 0, 0.7); /* Dark mode gradient overlay */
    }

    .hero-section .container {
        position: relative;
        z-index: 2; /* Ensure text container is above the overlay */
    }

    .text-gradient {
        background: linear-gradient(90deg, #ff6347, #ff4757);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    .btn-primary {
        background: linear-gradient(90deg, #007bff, #0056b3);
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #0056b3, #007bff);
        transform: translateY(-3px);
    }

    .hero-decorative-element {
        position: absolute;
        max-width: 50px; /* Adjust size as needed */
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    /* Dark mode styles */
    .dark-mode {
        background-color: #2e2e2e; /* Dark mode background */
        color: #ffffff; /* Dark mode text color */
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
                        <i class="fas fa-globe"></i> {{ __('adminlte.language') }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownLanguage">
                        <li><a class="dropdown-item" href="{{ route('changeLang', 'en') }}">{{ __('adminlte.english') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('changeLang', 'ar') }}">{{ __('adminlte.arabic') }}</a></li>
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
<section id="hero-sec" class="hero-section d-flex align-items-center justify-content-center text-center text-white position-relative overflow-hidden">
    <!-- Gradient Background -->
    <div class="hero-bg"></div>

    <!-- Content Container -->
    <div class="container">
        <h1 class="display-4 fw-bold mb-4 text-gradient" data-aos="fade-up" data-aos-duration="1500">
            {!! __('adminlte.empower_social_influence') !!}
        </h1>
        <p class="lead mb-4" data-aos="fade-up" data-aos-duration="2000">
            {{ __('adminlte.social_media_growth_tools') }}
        </p>
        <a href="{{ route('register') }}" class="btn btn-lg btn-primary rounded-pill px-5 py-3 shadow" data-aos="zoom-in" data-aos-duration="2500">
            {{ __('adminlte.get_started_free') }}
        </a>
    </div>
</section>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check localStorage for theme
        const savedTheme = localStorage.getItem('theme') || 'light-mode';
        document.body.classList.add(savedTheme);
        updateThemeIcon(savedTheme);

        // Theme Toggle
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            const body = document.body;
            const isDarkMode = body.classList.contains('dark-mode');

            body.classList.toggle('dark-mode', !isDarkMode);
            const newTheme = isDarkMode ? 'light-mode' : 'dark-mode';
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });

        // Update theme icon based on the current theme
        function updateThemeIcon(theme) {
            const themeIcon = document.getElementById('darkModeToggle').querySelector('i');
            themeIcon.classList.toggle('fa-moon', theme !== 'dark-mode');
            themeIcon.classList.toggle('fa-sun', theme === 'dark-mode');
        }
    });
</script>

