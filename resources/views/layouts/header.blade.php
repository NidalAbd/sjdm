<!-- Header Blade File: header.blade.php -->

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --warning-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        --glass-bg: rgba(255, 255, 255, 0.1);
        --glass-border: rgba(255, 255, 255, 0.2);
        --shadow-light: 0 8px 32px rgba(31, 38, 135, 0.37);
        --shadow-dark: 0 20px 60px rgba(0, 0, 0, 0.1);
        --text-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --navbar-dark: rgba(17, 19, 21, 0.95);
        --navbar-light: rgba(255, 255, 255, 0.95);
    }

    /* Body Background Patterns */
    body {
        background-image: url('{{ asset('images/double-bubble-outline.webp') }}');
        background-repeat: repeat;
        background-size: auto;
        background-position: center;
        background-attachment: fixed;
        transition: all 0.3s ease;
    }

    @media (max-width: 768px) {
        body {
            background-image: url('{{ asset('images/double-bubble-outline-small.webp') }}');
            background-attachment: scroll;
        }
    }

    /* Dark Mode Body Styling */
    .dark-mode {
        background-color: #1a1f2e;
        background-image: url('{{ asset('images/double-bubble-dark.webp') }}');
        background-repeat: repeat;
        background-size: auto;
        background-attachment: fixed;
    }

    @media (max-width: 768px) {
        .dark-mode {
            background-image: url('{{ asset('images/double-bubble-dark-small.webp') }}');
            background-attachment: scroll;
        }
    }

    /* Modern Navbar Styling */
    .navbar {
        background: var(--navbar-dark) !important;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        padding: 1rem 0;
    }

    .navbar.scrolled {
        background: rgba(17, 19, 21, 0.98) !important;
        box-shadow: 0 5px 30px rgba(0, 0, 0, 0.2);
        padding: 0.5rem 0;
    }

    /* Navbar Brand Modern Styling */
    .navbar-brand {
        font-weight: 800;
        font-size: 1.75rem;
        background: var(--text-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .navbar-brand:hover {
        transform: scale(1.05);
        filter: brightness(1.2);
    }

    .navbar-brand .circular-logo {
        border-radius: 50%;
        max-width: 45px;
        height: 45px;
        object-fit: cover;
        transition: all 0.3s ease;
        box-shadow: 0 3px 15px rgba(102, 126, 234, 0.3);
    }

    .navbar-brand:hover .circular-logo {
        transform: rotate(5deg) scale(1.1);
        box-shadow: 0 5px 25px rgba(102, 126, 234, 0.5);
    }

    .navbar-brand span {
        margin-left: 0.5rem;
        font-weight: 800;
        letter-spacing: 1px;
    }

    /* Navigation Links Modern Styling */
    .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-size: 1rem;
        font-weight: 500;
        padding: 0.75rem 1.25rem !important;
        margin: 0 0.25rem;
        border-radius: 25px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .navbar-nav .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--primary-gradient);
        transition: left 0.3s ease;
        z-index: -1;
        border-radius: 25px;
    }

    .navbar-nav .nav-link:hover::before {
        left: 0;
    }

    .navbar-nav .nav-link:hover {
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .navbar-nav .nav-link.active {
        background: var(--primary-gradient);
        color: white !important;
        box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
    }

    /* Navbar Toggler Modern Styling */
    .navbar-toggler {
        border: none;
        outline: none;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.1);
    }

    .navbar-toggler:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.05);
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.95%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }

    /* Dropdown Menu Modern Styling */
    .dropdown-menu {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        padding: 0.75rem 0;
        margin-top: 0.5rem;
        transition: all 0.3s ease;
        transform: translateY(-10px);
        opacity: 0;
        visibility: hidden;
    }

    .dropdown-menu.show {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
    }

    .dropdown-item {
        color: #2d3748;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        margin: 0.25rem 0.5rem;
        transition: all 0.3s ease;
    }

    .dropdown-item:hover {
        background: var(--primary-gradient);
        color: white;
        transform: translateX(5px);
    }

    .dropdown-item:focus {
        background: var(--primary-gradient);
        color: white;
    }

    /* Dark Mode Dropdown Styling */
    .dark-mode .dropdown-menu {
        background: rgba(52, 58, 64, 0.95);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .dark-mode .dropdown-item {
        color: rgba(255, 255, 255, 0.9);
    }

    .dark-mode .dropdown-item:hover,
    .dark-mode .dropdown-item:focus {
        background: var(--primary-gradient);
        color: white;
    }

    /* Profile Image in Dropdown */
    .profile-dropdown img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
    }

    .profile-dropdown:hover img {
        border-color: rgba(102, 126, 234, 0.8);
        box-shadow: 0 3px 15px rgba(102, 126, 234, 0.3);
    }

    /* Language Dropdown Styling */
    .language-dropdown .nav-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .language-dropdown .nav-link i {
        font-size: 1.1rem;
    }

    /* Dark Mode Toggle Button */
    .dark-mode-toggle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        margin-left: 0.5rem;
    }

    .dark-mode-toggle:hover {
        background: var(--primary-gradient);
        border-color: transparent;
        transform: scale(1.1);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }

    .dark-mode-toggle i {
        font-size: 1.2rem;
        color: white;
        transition: all 0.3s ease;
    }

    /* Hero Section Modern Styling */
    .hero-section {
        background: var(--dark-gradient);
        position: relative;
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        overflow: hidden;
        margin-top: 80px;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(118, 75, 162, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 40% 80%, rgba(240, 147, 251, 0.2) 0%, transparent 50%);
        animation: float 20s ease-in-out infinite;
    }

    .hero-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.3);
        z-index: 1;
        transition: all 0.3s ease;
    }

    .dark-mode .hero-bg {
        background: rgba(0, 0, 0, 0.6);
    }

    .hero-section .container {
        position: relative;
        z-index: 2;
    }

    /* Hero Text Styling */
    .hero-section h1 {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 800;
        color: white;
        margin-bottom: 1.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .text-gradient {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: inline-block;
    }

    .hero-section .lead {
        font-size: 1.3rem;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 2rem;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    /* Hero Cards Modern Styling */
    .hero-section .card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }

    .hero-section .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
        transition: left 0.6s ease;
    }

    .hero-section .card:hover::before {
        left: 100%;
    }

    .hero-section .card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .hero-section .card-body {
        padding: 1.5rem;
        position: relative;
        z-index: 2;
    }

    .hero-section .card-body i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .hero-section .card:hover i {
        transform: scale(1.1) rotate(5deg);
    }

    /* Button Modern Styling */
    .btn-primary {
        background: var(--primary-gradient);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.6s ease;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        color: white;
    }

    /* Animations */
    @keyframes float {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        33% {
            transform: translateY(-10px) rotate(1deg);
        }
        66% {
            transform: translateY(5px) rotate(-1deg);
        }
    }

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
            transform: translateX(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Section Styling */
    .platform-title, .achievements-title {
        font-size: 2.5rem;
        font-weight: 800;
        text-transform: uppercase;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        letter-spacing: 1px;
        background: var(--text-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        position: relative;
    }

    .platform-title::after, .achievements-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: var(--primary-gradient);
        border-radius: 2px;
    }

    /* Stats Box Modern Styling */
    .stats-box {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .stats-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
        transition: left 0.6s ease;
    }

    .stats-box:hover::before {
        left: 100%;
    }

    .stats-box:hover {
        transform: translateY(-15px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .stats-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1rem;
    }

    .stats-value {
        font-size: 3rem;
        font-weight: 800;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
    }

    .metric-description {
        font-size: 1rem;
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 0;
    }

    /* Icon Styling */
    .icon-circle, .stat-pic {
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
        margin-bottom: 1rem;
    }

    .icon-circle {
        width: 80px;
        height: 80px;
        background: var(--primary-gradient);
        color: white;
        font-size: 2.5rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
    }

    .icon-circle:hover {
        transform: scale(1.1) rotate(10deg);
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
    }

    .stat-pic {
        font-size: 4rem;
        color: #667eea;
        transition: all 0.3s ease;
    }

    .stat-pic:hover {
        transform: scale(1.1);
        color: #764ba2;
    }

    /* Widget Icon Styling */
    .widget-icon {
        max-height: 60px;
        transition: all 0.3s ease;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
    }

    .widget-icon:hover {
        transform: rotate(5deg) scale(1.1);
        filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.2));
    }

    /* Dark Mode Adjustments */
    .dark-mode .stats-box {
        background: rgba(52, 58, 64, 0.95);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .dark-mode .stats-title,
    .dark-mode .platform-title,
    .dark-mode .achievements-title {
        color: white;
    }

    .dark-mode .metric-description {
        color: #adb5bd;
    }

    .dark-mode .widget-icon {
        filter: brightness(0) invert(1) drop-shadow(0 4px 8px rgba(255, 255, 255, 0.1));
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .navbar-brand {
            font-size: 1.5rem;
        }

        .navbar-brand .circular-logo {
            max-width: 35px;
            height: 35px;
        }

        .navbar-nav .nav-link {
            padding: 0.5rem 1rem !important;
            margin: 0.25rem 0;
        }

        .hero-section {
            min-height: 50vh;
            margin-top: 70px;
        }

        .hero-section h1 {
            font-size: 2rem;
        }

        .hero-section .lead {
            font-size: 1.1rem;
        }

        .platform-title, .achievements-title {
            font-size: 2rem;
        }

        .stats-value {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 576px) {
        .hero-section .col-4 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 1rem;
        }

        .navbar-nav {
            text-align: center;
            margin-top: 1rem;
        }

        .navbar-nav .nav-link {
            border-radius: 15px;
            margin: 0.25rem 1rem;
        }
    }

    /* Scroll Behavior */
    html {
        scroll-behavior: smooth;
    }

    /* Loading Animation */
    .fade-in {
        animation: fadeInUp 0.8s ease forwards;
    }

    .slide-in-left {
        animation: slideInLeft 0.8s ease forwards;
    }

    .slide-in-right {
        animation: slideInRight 0.8s ease forwards;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('images/sjdm_logo.png') }}" alt="{{ config('app.name') }} Logo" class="img-fluid circular-logo me-2">
            <span class="fw-bold text-uppercase">{{ config('app.name') }}</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="{{ __('adminlte.toggle_navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Navigation Links -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">
                        <i class="fas fa-home me-1"></i>{{ __('adminlte.home') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('services.all') }}">
                        <i class="fas fa-th me-1"></i>{{ __('adminlte.services') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about') }}">
                        <i class="fas fa-info-circle me-1"></i>{{ __('adminlte.about_us') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact') }}">
                        <i class="fas fa-envelope me-1"></i>{{ __('adminlte.contact_us') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('faq') }}">
                        <i class="fas fa-question-circle me-1"></i>{{ __('adminlte.faq') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('privacy-policy') }}">
                        <i class="fas fa-shield-alt me-1"></i>{{ __('adminlte.privacy_policy') }}
                    </a>
                </li>

                <!-- User Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>{{ __('adminlte.sign_in') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i>{{ __('adminlte.register') }}
                        </a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle profile-dropdown d-flex align-items-center" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Auth::user()->adminlte_image() }}" alt="Profile Image" class="me-2">
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>{{ __('adminlte.dashboard') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.settings') }}">
                                    <i class="fas fa-user-cog me-2"></i>{{ __('adminlte.profile') }}
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>{{ __('adminlte.log_out') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                @endguest

                <!-- Language Dropdown -->
                <li class="nav-item dropdown language-dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownLanguage" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-globe"></i>
                        <span class="d-none d-md-inline">{{ __('adminlte.language') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownLanguage">
                        @foreach (config('app.available_locales') as $localeCode => $localeName)
                            <li>
                                <a class="dropdown-item {{ app()->getLocale() === $localeCode ? 'active' : '' }}" href="{{ route('changeLang', $localeCode) }}">
                                    <i class="fas fa-flag me-2"></i>{{ $localeName }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>

                <!-- Dark Mode Toggle -->
                <li class="nav-item">
                    <a class="nav-link dark-mode-toggle" href="#" id="darkModeToggle" title="{{ __('adminlte.toggle_theme') }}">
                        <i class="fas fa-moon"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hidden Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
<div class="container">
    <div class="row align-items-center">
        <div class="col-lg-8 col-md-12">
            <h1 class="display-4 fw-bold mb-4 fade-in" data-aos="fade-up" data-aos-duration="1000">
                <span class="text-gradient">{!! __('adminlte.empower_social_influence') !!}</span>
            </h1>
            <p class="lead mb-4 fade-in" data-aos="fade-up" data-aos-duration="1200">
                {{ __('Transform your social media presence with our premium marketing services. Get real followers, engagement, and results that matter for your business growth.') }}
            </p>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="row justify-content-center g-3">
                <!-- Instagram Card -->
                <div class="col-12 col-sm-4 col-lg-12" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="200">
                    <div class="card text-center border-0 shadow-lg">
                        <div class="card-body">
                            <i class="fab fa-instagram text-danger mb-2"></i>
                            <h6 class="card-title fw-bold">{{ __('adminlte.instagram') }}</h6>
                            <p class="card-text mb-3">
                                <span class="h5 fw-bold text-primary">$2.60</span>
                                <small class="text-muted">/ 1K {{ __('adminlte.followers') }}</small>
                            </p>
                            <a href="{{ route('services.all', ['platform' => 'instagram']) }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                <i class="fas fa-shopping-cart me-1"></i>{{ __('adminlte.order_now') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Facebook Card -->
                <div class="col-12 col-sm-4 col-lg-12" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="400">
                    <div class="card text-center border-0 shadow-lg">
                        <div class="card-body">
                            <i class="fab fa-facebook text-primary mb-2"></i>
                            <h6 class="card-title fw-bold">{{ __('adminlte.facebook') }}</h6>
                            <p class="card-text mb-3">
                                <span class="h5 fw-bold text-primary">$2.40</span>
                                <small class="text-muted">/ 1K {{ __('adminlte.followers') }}</small>
                            </p>
                            <a href="{{ route('services.all', ['platform' => 'facebook']) }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                <i class="fas fa-shopping-cart me-1"></i>{{ __('adminlte.order_now') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- TikTok Card -->
                <div class="col-12 col-sm-4 col-lg-12" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="600">
                    <div class="card text-center border-0 shadow-lg">
                        <div class="card-body">
                            <i class="fab fa-tiktok text-dark mb-2"></i>
                            <h6 class="card-title fw-bold">{{ __('adminlte.tiktok') }}</h6>
                            <p class="card-text mb-3">
                                <span class="h5 fw-bold text-primary">$3.40</span>
                                <small class="text-muted">/ 1K {{ __('adminlte.followers') }}</small>
                            </p>
                            <a href="{{ route('services.all', ['platform' => 'tiktok']) }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                <i class="fas fa-shopping-cart me-1"></i>{{ __('adminlte.order_now') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize theme from localStorage
        const savedTheme = localStorage.getItem('theme') || 'light-mode';
        document.body.classList.add(savedTheme);
        updateThemeIcon(savedTheme);

        // Navbar scroll effect
        const navbar = document.querySelector('.navbar');
        let lastScrollTop = 0;

        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            // Hide/show navbar on scroll
            if (scrollTop > lastScrollTop && scrollTop > 200) {
                navbar.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.transform = 'translateY(0)';
            }
            lastScrollTop = scrollTop;
        });

        // Dark mode toggle functionality
        const darkModeToggle = document.getElementById('darkModeToggle');
        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', function(e) {
                e.preventDefault();
                toggleTheme();
            });
        }

        function toggleTheme() {
            const body = document.body;
            const isDarkMode = body.classList.contains('dark-mode');

            // Remove existing theme classes
            body.classList.remove('dark-mode', 'light-mode');

            // Add new theme class
            const newTheme = isDarkMode ? 'light-mode' : 'dark-mode';
            body.classList.add(newTheme);

            // Save to localStorage
            localStorage.setItem('theme', newTheme);

            // Update icon
            updateThemeIcon(newTheme);

            // Add smooth transition effect
            body.style.transition = 'all 0.3s ease';
            setTimeout(() => {
                body.style.transition = '';
            }, 300);
        }

        function updateThemeIcon(theme) {
            const themeIcon = document.querySelector('#darkModeToggle i');
            if (themeIcon) {
                themeIcon.classList.remove('fa-moon', 'fa-sun');
                themeIcon.classList.add(theme === 'dark-mode' ? 'fa-sun' : 'fa-moon');
            }
        }

        // Enhanced dropdown animations
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            const menu = dropdown.querySelector('.dropdown-menu');

            dropdown.addEventListener('show.bs.dropdown', function() {
                menu.style.display = 'block';
                menu.style.opacity = '0';
                menu.style.transform = 'translateY(-10px)';

                setTimeout(() => {
                    menu.style.transition = 'all 0.3s ease';
                    menu.style.opacity = '1';
                    menu.style.transform = 'translateY(0)';
                }, 10);
            });

            dropdown.addEventListener('hide.bs.dropdown', function() {
                menu.style.transition = 'all 0.2s ease';
                menu.style.opacity = '0';
                menu.style.transform = 'translateY(-10px)';

                setTimeout(() => {
                    menu.style.display = 'none';
                }, 200);
            });
        });

        // Active navigation link highlighting
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link:not(.dropdown-toggle)');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && (currentPath === href || currentPath.startsWith(href + '/'))) {
                link.classList.add('active');
            }
        });

        // Smooth scrolling for anchor links
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    e.preventDefault();
                    const offsetTop = targetElement.offsetTop - 80; // Account for fixed navbar

                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Navbar collapse on mobile link click
        const navbarCollapse = document.querySelector('.navbar-collapse');
        const navbarToggler = document.querySelector('.navbar-toggler');

        if (navbarCollapse && navbarToggler) {
            const navLinks = navbarCollapse.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 992) { // Bootstrap lg breakpoint
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                            toggle: false
                        });
                        bsCollapse.hide();
                    }
                });
            });
        }

        // Enhanced button hover effects
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });

            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Parallax effect for hero section
        const heroSection = document.querySelector('.hero-section');
        if (heroSection) {
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const rate = scrolled * -0.3;
                heroSection.style.transform = `translateY(${rate}px)`;
            });
        }

        // Loading animation for cards
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards for animation
        const cards = document.querySelectorAll('.card, .stats-box');
        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });

        // Initialize AOS if available
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                easing: 'ease-in-out-cubic',
                once: true,
                offset: 100,
                delay: 100
            });
        }

        // Performance optimization: Throttle scroll events
        function throttle(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Apply throttling to resource-intensive scroll handlers
        const throttledScrollHandler = throttle(() => {
            // Add any additional scroll-based functionality here
        }, 16); // ~60fps

        window.addEventListener('scroll', throttledScrollHandler);

        // Preload images for better performance
        const imagesToPreload = [
            '{{ asset("images/sjdm_logo.png") }}',
            '{{ asset("images/double-bubble-outline.webp") }}',
            '{{ asset("images/double-bubble-dark.webp") }}'
        ];

        imagesToPreload.forEach(src => {
            const img = new Image();
            img.src = src;
        });

        // Add ripple effect to buttons
        const rippleButtons = document.querySelectorAll('.btn-primary');
        rippleButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple 0.6s linear;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                pointer-events: none;
            `;

                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);

                setTimeout(() => {
                    if (ripple.parentNode) {
                        ripple.remove();
                    }
                }, 600);
            });
        });

        // Add CSS for ripple animation
        if (!document.querySelector('#ripple-styles')) {
            const style = document.createElement('style');
            style.id = 'ripple-styles';
            style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
            document.head.appendChild(style);
        }

        // Accessibility improvements
        const focusableElements = document.querySelectorAll('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])');

        focusableElements.forEach(element => {
            element.addEventListener('focus', function() {
                this.style.outline = '2px solid #667eea';
                this.style.outlineOffset = '2px';
            });

            element.addEventListener('blur', function() {
                this.style.outline = '';
                this.style.outlineOffset = '';
            });
        });

        // Handle reduced motion preference
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            // Disable animations for users who prefer reduced motion
            const style = document.createElement('style');
            style.textContent = `
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        `;
            document.head.appendChild(style);
        }
    });
</script>
