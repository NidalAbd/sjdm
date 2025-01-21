<!-- Header Blade File: header.blade.php -->

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('images/sjdm_logo.png') }}" alt="{{ config('app.name') }} Logo" class="img-fluid circular-logo me-2">
            <span class="fw-bold text-uppercase text-white">{{ config('app.name') }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="{{ __('adminlte.toggle_navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Frequently Used Links -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('home') }}">{{ __('adminlte.home') }}</a>
                </li>
                <li><a class="nav-link text-white" href="{{ route('services.all') }}">{{ __('adminlte.services') }}</a></li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('about') }}">{{ __('adminlte.about_us') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('contact') }}">{{ __('adminlte.contact_us') }}</a>
                </li>

                <!-- Additional Links -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('faq') }}">{{ __('adminlte.faq') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('privacy-policy') }}">{{ __('adminlte.privacy_policy') }}</a>
                </li>

                <!-- User-Specific Links -->
                @guest
                    <!-- Links for Guests -->
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('login') }}">{{ __('adminlte.sign_in') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('register') }}">{{ __('adminlte.register') }}</a>
                    </li>
                @else
                    <!-- Links for Authenticated Users -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <!-- Profile Image -->
                            <img src="{{ Auth::user()->adminlte_image() }}" alt="Profile Image" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">{{ __('adminlte.dashboard') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.settings') }}">{{ __('adminlte.profile') }}</a></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('adminlte.log_out') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
                <!-- Language Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownLanguage" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-globe"></i> {{ __('adminlte.language') }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownLanguage">
                        @foreach (config('app.available_locales') as $localeCode => $localeName)
                            <li>
                                <a class="dropdown-item" href="{{ route('changeLang', $localeCode) }}">
                                    {{ $localeName }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <!-- Dark Mode Toggle -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="#" id="darkModeToggle">
                        <i class="fas fa-moon"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>



<!-- Hero Section -->
<section id="hero-sec" class="hero-section p-xxl-2 d-flex align-items-center justify-content-center text-center text-white position-relative overflow-hidden">
    <!-- Gradient Background -->
    <div class="hero-bg p-xxl-2"></div>

    <!-- Content Container -->
    <div class="container">
        <h1 class="display-4  fw-bold mb-4 text-gradient" data-aos="fade-up" data-aos-duration="1500">
            {!! __('adminlte.empower_social_influence') !!}
        </h1>
        <p class="lead mb-4" data-aos="fade-up" data-aos-duration="2000">

        </p>
        <div class="container">
            <div class="row justify-content-center g-2">
                <!-- Instagram Card -->
                <div class="col-4 col-md-4" data-aos="zoom-in" data-aos-duration="2000">
                    <div class="card text-center border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.9); border-radius: 15px;">
                        <div class="card-body">
                            <i class="fab fa-instagram text-danger"><br></i><h5 class="card-title mb-3"> Instagram</h5>
                            <p class="card-text fs-6">$2.60 <small>/ 1K Followers</small></p>
                            <a href="{{ route('contact') }}" class="btn btn-primary rounded-pill px-3 py-2">Order</a>
                        </div>
                    </div>
                </div>
                <!-- Facebook Card -->
                <div class="col-4 col-md-4" data-aos="zoom-in" data-aos-duration="2200">
                    <div class="card text-center border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.9); border-radius: 15px;">
                        <div class="card-body">
                            <i class="fab fa-facebook text-primary"></i><h5 class="card-title mb-3"> Facebook</h5>
                            <p class="card-text fs-6">$2.40 <small>/ 1K Followers</small></p>
                            <a href="{{ route('contact') }}" class="btn btn-primary rounded-pill px-3 py-2">Order</a>
                        </div>
                    </div>
                </div>
                <!-- TikTok Card -->
                <div class="col-4 col-md-4" data-aos="zoom-in" data-aos-duration="2400">
                    <div class="card text-center border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.9); border-radius: 15px;">
                        <div class="card-body">
                            <i class="fab fa-tiktok text-dark"></i><h5 class="card-title mb-3"> TikTok</h5>

                            <p class="card-text fs-6">$3.40 <small>/ 1K Followers</small></p>
                            <a href="{{ route('contact') }}" class="btn btn-primary rounded-pill px-3 py-2">Order</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
<!-- Header Blade File: header.blade.php -->

<style>
    /* General Body Styling */
    body {
        background-image: url('{{ asset('images/double-bubble-outline.webp') }}');
        background-repeat: repeat;
        background-size: auto;
        background-position: center;
        transition: background 0.3s ease-in-out;
    }

    /* Mobile Background Optimization */
    @media (max-width: 768px) {
        body {
            background-image: url('{{ asset('images/double-bubble-outline.webp') }}');
        }
    }

    /* Dark Mode Styling */
    .dark-mode {
        background-color: #365352;
        background-image: url('{{ asset('images/double-bubble-dark.webp') }}');
        background-repeat: repeat;
        background-size: auto;
    }

    @media (max-width: 768px) {
        .dark-mode {
            background-image: url('{{ asset('images/double-bubble-dark.webp') }}');
        }
    }

    /* Navbar Styling */
    .navbar {
        background-color: #111315 !important;
        transition: background-color 0.3s ease-in-out;
    }

    .dark-mode .navbar {
        background-color: #111315 !important;
    }

    .navbar .nav-link {
        font-size: 1rem;
        padding: 0.5rem 1rem;
        transition: color 0.3s ease-in-out;
        color: #fff;
    }

    .navbar .nav-link:hover,
    .dark-mode .navbar .nav-link:hover {
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

    /* Dropdown Styling */
    .dropdown-menu {
        background-color: #ffffff;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .dropdown-item {
        color: #000;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #000;
    }

    /* Dark Mode Dropdown */
    .dark-mode .dropdown-menu {
        background-color: #333;
        color: #fff;
    }

    .dark-mode .dropdown-item {
        color: #fff;
    }

    .dark-mode .dropdown-item:hover {
        background-color: #444;
        color: #fff;
    }

    /* Hero Section Optimization */
    .hero-section {
        background-image: url('{{ asset('images/double-bubble-dark.webp') }}');
        background-repeat: repeat;
        background-size: cover;
        background-position: center;
        height: 45vh;
        color: #fff;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    /* Mobile Optimization */
    @media (max-width: 768px) {
        .hero-section {
            height: 60vh;
            background-size: contain;
        }
    }

    /* General Button Styling */
    .btn-primary {
        background-image: linear-gradient(90deg, rgba(0, 123, 255, 1) 0%, rgba(0, 70, 200, 1) 100%);
        border: none;
        border-radius: 30px;
        padding: 10px 25px;
        transition: all 0.3s ease-in-out;
    }

    .btn-primary:hover {
        background-image: linear-gradient(90deg, rgba(0, 70, 200, 1) 0%, rgba(0, 123, 255, 1) 100%);
        transform: scale(1.05);
    }

    /* Utility Classes for Responsive Layout */
    .text-gradient {
        background: linear-gradient(90deg, #ff7eb3 0%, #ff758c 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .rounded-pill {
        border-radius: 50px;
    }

</style>


