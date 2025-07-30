<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#6366f1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Welcome')</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon-96x96.png') }}" type="image/png">
    <link rel="manifest" href="{{ asset('manifest-mobile.json') }}">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Mobile-Optimized CSS -->
    <link rel="stylesheet" href="{{ asset('css/mobile-optimized.css') }}">
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-L001CCMV5K"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-L001CCMV5K');
    </script>
    
    <style>
        /* Reset all AdminLTE and default styles */
        * {
            box-sizing: border-box;
        }
        
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            overflow-x: hidden !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        /* Ensure full width layout */
        .mobile-layout {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            min-height: 100vh;
        }
        
        /* Override any potential AdminLTE styles */
        .content-wrapper,
        .main-content,
        .content,
        .wrapper,
        .container-fluid {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            max-width: 100% !important;
        }
        
        /* Hide sidebar only on welcome page */
        .mobile-welcome-container .sidebar,
        .mobile-welcome-container .main-sidebar {
            display: none !important;
        }
        
        /* Ensure header takes full width */
        .mobile-header {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Ensure content takes full width */
        .mobile-content {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Ensure footer takes full width */
        .mobile-footer {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Remove AOS classes */
        .aos-init,
        .aos-animate {
            animation: none !important;
            transform: none !important;
            opacity: 1 !important;
        }
        
        /* Disable any AOS animations */
        [data-aos] {
            animation: none !important;
            transform: none !important;
            opacity: 1 !important;
        }
    </style>
    
    @yield('css')
</head>
<body>
    <div class="mobile-layout">
        <!-- Header -->
        <header class="mobile-header">
            @include('layouts.header')
        </header>
        
        <!-- Main Content -->
        <main class="mobile-content">
            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="mobile-footer">
            @include('layouts.footer')
        </footer>
    </div>
    
    <!-- Mobile-Optimized JavaScript -->
    <script src="{{ asset('js/mobile-optimized.js') }}"></script>
    
    <!-- Disable AOS animations -->
    <script>
        // Prevent AOS from initializing
        if (typeof AOS !== 'undefined') {
            AOS.init = function() {};
        }
        
        // Remove AOS classes from elements
        document.addEventListener('DOMContentLoaded', function() {
            const aosElements = document.querySelectorAll('.aos-init, .aos-animate, [data-aos]');
            aosElements.forEach(function(element) {
                element.classList.remove('aos-init', 'aos-animate');
                element.removeAttribute('data-aos');
                element.removeAttribute('data-aos-duration');
                element.removeAttribute('data-aos-delay');
                element.removeAttribute('data-aos-easing');
            });
        });
    </script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html> 