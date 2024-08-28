<!-- resources/views/welcome.blade.php -->
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
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
        .circular-logo {
            border-radius: 50%;
            max-width: 150px;
        }
        .widget-icon {
            width: 50px;
            height: 50px;
        }
    </style>
</head>

<body class="antialiased bg-light">
@include('layouts.header')

<main class="container my-5">
    <!-- Add space between each widget by using mb-4 -->
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Include AOS for animations and Slick for the slider -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

</body>

</html>
