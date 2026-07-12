<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'SMM Panel')</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    {{-- Static fallback for Vuetify's --v-theme-* CSS variables, which are normally
         injected by the Vuetify JS runtime. These auth pages are plain Blade/CSS
         (no Vue mount), so the same values from resources/js/plugins/vuetify.js
         are hardcoded here to keep app.css's design tokens working. --}}
    <style>
        :root {
            --v-theme-primary: 99, 102, 241;
            --v-theme-on-primary: 255, 255, 255;
            --v-theme-success: 16, 185, 129;
            --v-theme-error: 239, 68, 68;
            --v-theme-warning: 245, 158, 11;
            --v-theme-info: 59, 130, 246;
            --v-theme-background: 250, 250, 250;
            --v-theme-on-background: 17, 17, 17;
            --v-theme-surface: 255, 255, 255;
            --v-theme-on-surface: 51, 51, 51;
            --v-border-color: 51, 51, 51;
        }
        html.auth-dark {
            --v-theme-primary: 129, 140, 248;
            --v-theme-success: 52, 211, 153;
            --v-theme-error: 248, 113, 113;
            --v-theme-warning: 251, 191, 36;
            --v-theme-info: 96, 165, 250;
            --v-theme-background: 12, 12, 14;
            --v-theme-on-background: 245, 245, 245;
            --v-theme-surface: 22, 22, 24;
            --v-theme-on-surface: 212, 212, 212;
            --v-border-color: 212, 212, 212;
        }
    </style>
    <script>
        if ((localStorage.getItem('theme') || 'dark') === 'dark') {
            document.documentElement.classList.add('auth-dark');
        }
    </script>
</head>
<body class="auth-body">
    <div class="auth-wrap">
        <a href="/" class="auth-logo-row">
            <img src="{{ asset('images/logo.png') }}" alt="SMM Panel" width="28" height="28">
            <span>SMM Panel</span>
        </a>

        <div class="auth-card">
            @yield('content')
        </div>
    </div>
</body>
</html>
