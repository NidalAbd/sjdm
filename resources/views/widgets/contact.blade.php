<!-- resources/views/widgets/contact.blade.php -->
@extends('layouts.welcome')
@section('title', __('adminlte.contact_us'))
@section('content')
    <div class="content-section active" id="contact-us-section">
        <div class="container my-5">
            <h2 class="text-center mb-4 platform-title">{{ __('adminlte.contact_us') }}</h2>

            <!-- Contact Information Section -->
            <div class="row mb-5 align-items-center">
                <!-- Contact Details -->
                <div class="col-md-6 mb-4 mb-md-0" data-aos="fade-right" data-aos-duration="1000">
                    <div class="contact-info-box p-4 rounded">
                        <h3 class="contact-title">{{ __('adminlte.get_in_touch') }}</h3>
                        <p class="contact-text">
                            {{ __('adminlte.contact_us_text') }}
                        </p>
                        <div class="contact-details">
                            <p class="contact-text">
                                <i class="fas fa-envelope fa-lg me-2 contact-icon"></i><strong>Email:</strong> <a href="mailto:info@sjdm.store" class="contact-link">info@sjdm.store</a>
                            </p>
                            <p class="contact-text">
                                <i class="fas fa-id-badge fa-lg me-2 contact-icon"></i><strong>License Company Number:</strong> 1241225
                            </p>
                            <p class="contact-text">
                                <i class="fab fa-whatsapp fa-lg me-2 contact-icon"></i><strong>WhatsApp:</strong> <a href="https://wa.me/971557830054" target="_blank" class="contact-link">+971 55 783 0054</a>
                            </p>
                            <p class="contact-text">
                                <i class="fas fa-phone-alt fa-lg me-2 contact-icon"></i><strong>Phone:</strong> <a href="tel:+971557830054" class="contact-link">+971 55 783 0054</a>
                            </p>
                            <p class="contact-text">
                                <i class="fas fa-map-marker-alt fa-lg me-2 contact-icon"></i><strong>Address:</strong> Al-Maktoom St, Deira, Dubai, UAE
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Contact Image -->
                <div class="col-md-6" data-aos="fade-left" data-aos-duration="1000">
                    <img src="{{ asset('images/contact.png') }}" alt="{{ __('adminlte.contact_us') }}" class="img-fluid rounded shadow-lg">
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
