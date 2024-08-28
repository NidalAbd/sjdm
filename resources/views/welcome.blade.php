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

<body class="antialiased bg-light">

<!-- Add jQuery script before any other script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="background-icons" id="background-icons"></div>

@include('layouts.header')

<main class="container my-5">
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
    <div class="mb-4">
        @include('widgets.platforms')
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

        // JS for floating icons background
        const icons = ['fa-facebook', 'fa-instagram', 'fa-twitter', 'fa-linkedin', 'fa-youtube', 'fa-tiktok'];
        const backgroundIcons = document.getElementById('background-icons');

        icons.forEach(icon => {
            const el = document.createElement('i');
            el.className = `fab ${icon} icon`;
            el.style.left = Math.random() * 100 + 'vw';
            el.style.animationDuration = Math.random() * 10 + 10 + 's';
            backgroundIcons.appendChild(el);
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

</body>
</html>
