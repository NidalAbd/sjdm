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

<!-- Include Bootstrap Bundle and AOS for animations -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        AOS.init(); // Initialize AOS for animations

        // Check localStorage for theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.body.classList.add(savedTheme);
            updateThemeIcon(savedTheme);
        }

        // Theme Toggle
        document.getElementById('darkModeToggle').addEventListener('click', function () {
            if (document.body.classList.contains('dark-mode')) {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('theme', 'light-mode');
                updateThemeIcon('light-mode');
            } else {
                document.body.classList.add('dark-mode');
                localStorage.setItem('theme', 'dark-mode');
                updateThemeIcon('dark-mode');
            }
        });

        // Update theme icon based on the current theme
        function updateThemeIcon(theme) {
            const themeIcon = document.getElementById('darkModeToggle').querySelector('i');
            if (theme === 'dark-mode') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        }
    });
</script>

<style>
    .navbar {
        background:  rgb(15, 16, 17);
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
