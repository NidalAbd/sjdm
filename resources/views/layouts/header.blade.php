<!-- resources/views/layouts/header.blade.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark bg-black">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('images/MaxPeak.png') }}" alt="{{ config('app.name') }} Logo" class="img-fluid circular-logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Sign In</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">FAQ</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownLanguage" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-globe"></i> Language
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownLanguage">
                        <li><a class="dropdown-item" href="?lang=en">English</a></li>
                        <li><a class="dropdown-item" href="?lang=ar">Arabic</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <button class="btn btn-outline-light nav-link" id="themeToggle">
                        <i class="fas fa-sun" id="themeIcon">s</i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<section id="hero-sec" class="hero-section" style="background-image: url('{{ asset('images/headerBackground.jpg') }}');">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h1 data-aos="fade-up" data-aos-duration="3000" class="aos-init aos-animate">
                    Better <i class="point"></i>ideas <br>for Fast Growth
                </h1>
                <h2 data-aos="fade-up" data-aos-duration="3000" class="aos-init aos-animate">
                    All Social Media Services Just in 1 Place.
                </h2>
                <a href="{{ route('register') }}" class="btn btn-primary aos-init aos-animate" data-aos="fade-up" data-aos-duration="3000">
                    Sign Up Now
                </a>
            </div>
            <div class="col-md-5">
                <div class="mobile-slider slick-initialized slick-slider aos-init aos-animate" data-aos="fade-up" data-aos-duration="3000">
                    <div class="slick-list draggable">
                        <div class="slick-track">
                            <!-- Example of social logo slides -->
                            <div class="slick-slide slick-current slick-active" data-slick-index="0" aria-hidden="false" tabindex="0">
                                <div class="social-logos-wrap logo-tw"></div>
                            </div>
                            <div class="slick-slide" data-slick-index="1" aria-hidden="true" tabindex="-1">
                                <div class="social-logos-wrap logo-ig"></div>
                            </div>
                            <div class="slick-slide" data-slick-index="2" aria-hidden="true" tabindex="-1">
                                <div class="social-logos-wrap logo-yt"></div>
                            </div>
                            <!-- Add more slides as needed -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include AOS for animations and Slick for the slider -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<style>
    .navbar-brand .circular-logo {
        border-radius: 50%;
        max-width: 50px;
    }

    .hero-section {
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        height: 600px; /* Increase height as needed */
        position: relative;
        color: #fff; /* White text color for better contrast */
        padding-top: 100px; /* Adjust padding for vertical alignment */
    }

    .point {
        color: #ff6347; /* Custom color for the "point" element */
    }

    .social-logos-wrap {
        width: 50px;
        height: 50px;
        background-size: contain;
        background-repeat: no-repeat;
    }

    /* Add styles for each social logo as background image */
    .logo-tw { background-image: url('{{ asset('images/logo-twitter.png') }}'); }
    .logo-ig { background-image: url('{{ asset('images/logo-instagram.png') }}'); }
    .logo-yt { background-image: url('{{ asset('images/logo-youtube.png') }}'); }
    /* Add more styles for additional logos */
</style>

<script>
    AOS.init(); // Initialize AOS for animations

    $(document).ready(function(){
        $('.mobile-slider').slick({
            autoplay: true,
            autoplaySpeed: 2000,
            arrows: false,
            dots: false,
        });

        // Check localStorage for theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.body.classList.add(savedTheme);
            updateThemeIcon(savedTheme);
        }

        // Theme Toggle
        document.getElementById('themeToggle').addEventListener('click', function() {
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
            const themeIcon = document.getElementById('themeIcon');
            if (theme === 'dark-mode') {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            } else {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            }
        }
    });
</script>
