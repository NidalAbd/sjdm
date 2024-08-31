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

<body>

<!-- Include Header -->
@include('layouts.header')

<!-- Email Verification Alert -->
@if (auth()->check() && !auth()->user()->hasVerifiedEmail())
    <div class="alert alert-warning alert-dismissible fade show my-4" role="alert">
        {{ __('Your email address is not verified. Please check your email for a verification link.') }}
        <form method="POST" action="{{ route('verification.resend') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('Click here to request another') }}</button>.
        </form>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<main class="container my-5">
    <div class="mb-4">
        @include('widgets.platforms')
    </div>
    @guest
        <!-- Show "fast-login" widget only if the user is not logged in -->
        <div class="mb-4">
            @include('widgets.fast-login')
        </div>
    @endguest

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
</main>

@include('layouts.footer')

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    });
</script>

</body>
</html>
