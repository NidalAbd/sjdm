<!-- resources/views/widgets/about.blade.php -->
@extends('layouts.welcome')
@section('title', __('adminlte.about_us'))
@section('content')
    <div class="content-section active" id="about-us-section">
        <div class="container my-5">
            <h2 class="text-center mb-4 platform-title">{{ __('adminlte.about_us') }}</h2>

            <!-- About Us Section - Row 1 -->
            <div class="row mb-4" data-aos="fade-right" data-aos-duration="1000">
                <div class="col-md-6 d-flex align-items-center">
                    <div>
                        <h3 class="about-us-title">{{ __('adminlte.about_us_title1') }}</h3>
                        <p class="about-us-text">{{ __('adminlte.about_us_text1') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <img src="{{ asset('images/about1.jpg') }}" alt="{{ __('adminlte.about_us_title1') }}" class="img-fluid rounded">
                </div>
            </div>

            <!-- About Us Section - Row 2 -->
            <div class="row mb-4" data-aos="fade-left" data-aos-duration="1000">
                <div class="col-md-6 order-md-2 d-flex align-items-center">
                    <div>
                        <h3 class="about-us-title">{{ __('adminlte.about_us_title2') }}</h3>
                        <p class="about-us-text">{{ __('adminlte.about_us_text2') }}</p>
                    </div>
                </div>
                <div class="col-md-6 order-md-1">
                    <img src="{{ asset('images/about2.jpg') }}" alt="{{ __('adminlte.about_us_title2') }}" class="img-fluid rounded">
                </div>
            </div>

            <!-- About Us Section - Row 3 -->
            <div class="row mb-4" data-aos="fade-right" data-aos-duration="1000">
                <div class="col-md-6 d-flex align-items-center">
                    <div>
                        <h3 class="about-us-title">{{ __('adminlte.about_us_title3') }}</h3>
                        <p class="about-us-text">{{ __('adminlte.about_us_text3') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <img src="{{ asset('images/about3.jpg') }}" alt="{{ __('adminlte.about_us_title3') }}" class="img-fluid rounded">
                </div>
            </div>

            <!-- About Us Section - Row 4 -->
            <div class="row mb-4" data-aos="fade-left" data-aos-duration="1000">
                <div class="col-md-6 order-md-2 d-flex align-items-center">
                    <div>
                        <h3 class="about-us-title">{{ __('adminlte.about_us_title4') }}</h3>
                        <p class="about-us-text">{{ __('adminlte.about_us_text4') }}</p>
                    </div>
                </div>
                <div class="col-md-6 order-md-1">
                    <img src="{{ asset('images/about4.jpg') }}" alt="{{ __('adminlte.about_us_title4') }}" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Custom styles for light and dark modes -->
    <style>
        /* Light mode styles */

    </style>
@endpush

@push('scripts')
    <!-- Initialize AOS and other necessary scripts -->
    <script>
        AOS.init(); // Initialize AOS for animations
    </script>
@endpush
