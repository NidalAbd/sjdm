<!-- resources/views/widgets/privacy_policy.blade.php -->
@extends('layouts.welcome')

@section('content')
    <div class="content-section active" id="privacy-policy-section">
        <div class="container my-5">
            <h2 class="text-center mb-4 platform-title">{{ __('adminlte.privacy_policy') }}</h2>

            <!-- General Terms -->
            <div class="mb-4" data-aos="fade-right" data-aos-duration="1000">
                <h4 class="privacy-title">{{ __('adminlte.general_terms') }}</h4>
                <p class="privacy-text">{{ __('adminlte.general_terms_desc') }}</p>
            </div>

            <!-- Responsibilities -->
            <div class="mb-4" data-aos="fade-left" data-aos-duration="1000">
                <h4 class="privacy-title">{{ __('adminlte.responsibilities') }}</h4>
                <p class="privacy-text">{{ __('adminlte.responsibilities_desc') }}</p>
            </div>

            <!-- Service Use -->
            <div class="mb-4" data-aos="fade-right" data-aos-duration="1000">
                <h4 class="privacy-title">{{ __('adminlte.service_use') }}</h4>
                <p class="privacy-text">{{ __('adminlte.service_use_desc') }}</p>
            </div>

            <!-- Privacy Policy -->
            <div class="mb-4" data-aos="fade-left" data-aos-duration="1000">
                <h4 class="privacy-title">{{ __('adminlte.privacy_policy_section') }}</h4>
                <p class="privacy-text">{{ __('adminlte.privacy_policy_desc') }}</p>
            </div>

            <!-- Contact Information -->
            <div class="mb-4" data-aos="fade-right" data-aos-duration="1000">
                <h4 class="privacy-title">{{ __('adminlte.contact_info') }}</h4>
                <div class="contact-details">
                    <p class="privacy-text"><strong>{{ __('adminlte.email') }}:</strong> <a href="mailto:info@sjdm.store" class="text-decoration-none">info@sjdm.store</a></p>
                    <p class="privacy-text"><strong>{{ __('adminlte.phone') }}:</strong> <a href="tel:+971557830054" class="text-decoration-none">+971 55 783 0054</a></p>
                    <p class="privacy-text"><strong>{{ __('adminlte.address') }}:</strong> Al-Maktoom St, Deira, Dubai, UAE</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>

    </style>
@endpush

@push('scripts')
    <script>
        AOS.init(); // Initialize AOS for animations
    </script>
@endpush
