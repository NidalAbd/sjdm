<!-- Header Blade File: header.blade.php -->

<style>
    /* Light mode styles */
    body {
        background: linear-gradient(135deg, #f6f8fa, #ffffff); /* Light gradient background */
        transition: background-color 0.3s ease, color 0.3s ease;
        color: #212529; /* Light mode text color */
        padding-top: 56px; /* Add padding to the top to accommodate the fixed navbar */
    }

    .navbar {
        background-color: #ffffff; /* Light mode navbar background */
        transition: background-color 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .navbar .nav-link {
        font-size: 1rem;
        color: #333;
        transition: color 0.3s ease;
    }

    .navbar .nav-link:hover {
        color: #ff6347; /* Custom hover color */
    }

    .hero-section {
        background: linear-gradient(135deg, #eef2f3, #ffffff); /* Light gradient for hero section */
        color: #333;
        height: 35vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        transition: background 0.3s ease, color 0.3s ease;
    }

    /* Dark mode styles */
    .dark-mode {
        background: linear-gradient(135deg, #1e2d2f, #2f4f4f); /* Dark gradient background */
        color: #e4e4e4; /* Dark mode text color */
    }

    .dark-mode .navbar {
        background-color: #111315; /* Dark mode navbar background */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
    }

    .dark-mode .navbar .nav-link {
        color: #e4e4e4;
    }

    .dark-mode .navbar .nav-link:hover {
        color: #ff6347;
    }

    .dark-mode .hero-section {
        background: linear-gradient(135deg, #2f4f4f, #1e2d2f); /* Dark gradient for hero section */
        color: #fff;
    }

    /* Button styling */
    button {
        background-color: #ff6347;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        cursor: pointer;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    button:hover {
        background-color: #e5533f;
    }

    /* Dropdown styles */
    .dropdown-menu {
        background-color: #ffffff; /* Light mode dropdown */
        border: 1px solid #ccc;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .dropdown-item {
        color: #333;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .dark-mode .dropdown-menu {
        background-color: #333; /* Dark mode dropdown */
        color: #e4e4e4;
    }

    .dark-mode .dropdown-item {
        color: #e4e4e4;
    }

    .dark-mode .dropdown-item:hover {
        background-color: #444;
    }

    /* Footer styles */
    footer {
        background-color: #f1f1f1;
        color: #333;
        text-align: center;
        padding: 20px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .dark-mode footer {
        background-color: #222;
        color: #e4e4e4;
    }

</style>
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
<section id="hero-sec" class="hero-section d-flex align-items-center justify-content-center text-center text-white position-relative overflow-hidden">
    <!-- Gradient Background -->
    <div class="hero-bg"></div>

    <!-- Content Container -->
    <div class="container">
        <h1 class="display-4 fw-bold mb-4 text-gradient" data-aos="fade-up" data-aos-duration="1500">
            {!! __('adminlte.empower_social_influence') !!}
        </h1>
        <p class="lead mb-4" data-aos="fade-up" data-aos-duration="2000">

        </p>
            <div class="container">
                <div class="row justify-content-center g-4">
                    <!-- Instagram Card -->
                    <div class="col-md-4" data-aos="zoom-in" data-aos-duration="2000">
                        <div class="card text-center border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.9); border-radius: 15px;">
                            <div class="card-body">
                                <h3 class="card-title mb-3"><i class="fab fa-instagram text-danger"></i> Instagram</h3>
                                <p class="card-text fs-5">$2.60 <small>/ 1K Followers</small></p>
                                <a href="{{ route('contact') }}" class="btn btn-primary rounded-pill px-4 py-2">Order Now</a>
                            </div>
                        </div>
                    </div>
                    <!-- Facebook Card -->
                    <div class="col-md-4" data-aos="zoom-in" data-aos-duration="2200">
                        <div class="card text-center border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.9); border-radius: 15px;">
                            <div class="card-body">
                                <h3 class="card-title mb-3"><i class="fab fa-facebook text-primary"></i> Facebook</h3>
                                <p class="card-text fs-5">$2.40 <small>/ 1K Followers</small></p>
                                <a href="{{ route('contact') }}" class="btn btn-primary rounded-pill px-4 py-2">Order Now</a>
                            </div>
                        </div>
                    </div>
                    <!-- TikTok Card -->
                    <div class="col-md-4" data-aos="zoom-in" data-aos-duration="2400">
                        <div class="card text-center border-0 shadow-lg" style="background: rgba(255, 255, 255, 0.9); border-radius: 15px;">
                            <div class="card-body">
                                <h3 class="card-title mb-3"><i class="fab fa-tiktok text-dark"></i> TikTok</h3>
                                <p class="card-text fs-5">$3.4 <small>/ 1K Followers</small></p>
                                <a href="{{ route('contact') }}" class="btn btn-primary rounded-pill px-4 py-2">Order Now</a>
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
<style>
    /* General Section Title Styling */
    .platform-title, .achievements-title {
        font-size: 2.5rem; /* Increased font size for better visibility */
        font-weight: 700;
        text-transform: uppercase;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        letter-spacing: 1.2px;
        color: var(--bs-body-color); /* Dynamic color based on theme */
    }

    .platform-title::after, .achievements-title::after {
        content: '';
        display: block;
        margin: 0 auto;
        width: 60px;
        height: 3px;
        background-color: var(--bs-primary); /* Primary color */
        margin-top: 10px;
    }

    /* Stats Box Styling */
    .stats-box {
        background-color: var(--bs-body-bg); /* Dynamic background for light/dark mode */
        border-radius: 15px;
        text-align: center;
        padding: 30px;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 20px;
        color: var(--bs-body-color); /* Dynamic text color */
    }

    .stats-box:hover {
        transform: translateY(-10px);
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
    }

    .stats-title {
        font-size: 1.8rem; /* Increased font size for titles */
        font-weight: 700; /* Increased font weight for emphasis */
        color: var(--bs-body-color); /* Dynamic text color */
        margin-bottom: 15px;
    }

    .stats-value {
        font-size: 2.8rem; /* Increased font size for values */
        font-weight: 800;
        color: var(--bs-primary); /* Primary color for values */
        margin-bottom: 15px;
    }

    .metric-description {
        font-size: 1.2rem; /* Increased font size for better readability */
        font-weight: 600;
        color: var(--bs-secondary-color); /* Secondary text color */
        margin-bottom: 10px;
    }

    /* Icon and Image Styling */
    .icon-circle, .stat-pic {
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
    }

    .icon-circle {
        width: 100px;
        height: 100px;
        font-size: 3.5rem;
        color: #ffffff; /* Ensure icons are white for contrast */
    }

    .stat-pic {
        height: 100px; /* Increased icon size */
        font-size: 5rem;
        color: var(--bs-primary); /* Primary color for icons */
        margin-bottom: 10px;
    }

    .widget-icon {
        max-height: 80px; /* Increased icon size */
        color: var(--bs-body-color); /* Dynamic color based on theme */
        transition: transform 0.3s ease;
    }

    .widget-icon:hover {
        transform: rotate(10deg) scale(1.1);
    }

    /* Form and Button Styling */
    .form-floating .form-control:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: none;
    }

    .form-floating label {
        color: var(--bs-secondary-color);
    }

    .position-relative .form-control {
        padding-right: 3rem; /* Ensure there's space for the icon */
    }

    .position-absolute i.material-icons {
        font-size: 1.5rem;
        color: var(--bs-secondary-color);
    }

    .btn-primary {
        transition: background-color 0.3s ease;
        background-image: linear-gradient(90deg, rgba(0, 123, 255, 1) 0%, rgba(0, 70, 178, 1) 100%);
        border: none;
    }

    .btn-primary:hover {
        background-image: linear-gradient(90deg, rgba(0, 70, 178, 1) 0%, rgba(0, 123, 255, 1) 100%);
    }

    /* Card Styling */
    .card {
        border-radius: 1rem;
    }

    .card-body {
        background-color: var(--bs-body-bg); /* Dynamic background for dark/light mode */
        color: var(--bs-body-color); /* Dynamic text color for dark/light mode */
        border-radius: 1rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Dark Mode Styles */
    .dark-mode .stats-box, .dark-mode .card-body {
        background-color: #343a40; /* Dark mode background */
        color: #ffffff; /* Light text in dark mode */
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3); /* Slightly darker shadow for dark mode */
    }

    .dark-mode .achievements-title,
    .dark-mode .platform-title,
    .dark-mode .stats-title,
    .dark-mode .stats-value,
    .dark-mode .stats-box h3,
    .dark-mode .metric-description {
        color: #ffffff; /* White text for contrast in dark mode */
    }

    .dark-mode .stat-pic, .dark-mode .icon-circle {
        color: var(--bs-primary); /* Primary color for icons in dark mode */
    }

    .dark-mode .icon-circle {
        background-color: #495057; /* Dark mode icon circle background */
        color: #ffffff; /* White color for icons */
    }

    .dark-mode .widget-icon {
        filter: brightness(0) invert(1); /* Invert colors to make icons white in dark mode */
    }

    .dark-mode .form-floating .form-control {
        background-color: #495057;
        color: #ffffff;
    }

    .dark-mode .form-floating label,
    .dark-mode .form-check-label {
        color: #ffffff;
    }

    .dark-mode .form-check-input {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .dark-mode .position-absolute i.material-icons {
        color: #ffffff; /* Adjust icon color for dark mode */
    }
</style>

