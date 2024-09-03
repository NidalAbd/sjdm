<!-- resources/views/widgets/support.blade.php -->
@extends('layouts.welcome')

@section('content')
    <div class="content-section active" id="support-section">
        <div class="container my-5">
            <h2 class="text-center mb-4 platform-title">{{ __('adminlte.support') }}</h2>

            <!-- Support Introduction -->
            <div class="row mb-5" data-aos="fade-up" data-aos-duration="1000">
                <div class="col-12 text-center">
                    <p class="lead">{{ __('adminlte.support_intro') }}</p>
                </div>
            </div>

            <!-- Support Options -->
            <div class="row mb-5">
                <!-- Live Chat Support -->
                <div class="col-md-4 mb-4" data-aos="fade-right" data-aos-duration="1000">
                    <div class="support-option text-center p-4">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <h4 class="support-title">{{ __('adminlte.support_live_chat') }}</h4>
                        <p class="support-text">{{ __('adminlte.support_live_chat_desc') }}</p>
                        <a href="#" class="btn btn-primary">{{ __('adminlte.support_start_chat') }}</a>
                    </div>
                </div>

                <!-- Email Support -->
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-duration="1000">
                    <div class="support-option text-center p-4">
                        <i class="fas fa-envelope fa-3x mb-3"></i>
                        <h4 class="support-title">{{ __('adminlte.support_email') }}</h4>
                        <p class="support-text">{{ __('adminlte.support_email_desc') }}</p>
                        <a href="mailto:support@yourdomain.com" class="btn btn-primary">{{ __('adminlte.support_contact_email') }}</a>
                    </div>
                </div>

                <!-- Phone Support -->
                <div class="col-md-4 mb-4" data-aos="fade-left" data-aos-duration="1000">
                    <div class="support-option text-center p-4">
                        <i class="fas fa-phone fa-3x mb-3"></i>
                        <h4 class="support-title">{{ __('adminlte.support_phone') }}</h4>
                        <p class="support-text">{{ __('adminlte.support_phone_desc') }}</p>
                        <a href="tel:+123456789" class="btn btn-primary">{{ __('adminlte.support_call_us') }}</a>
                    </div>
                </div>
            </div>

            <!-- Support FAQ and Ticket Section -->
            <div class="row">
                <div class="col-md-6 mb-4" data-aos="fade-right" data-aos-duration="1000">
                    <div class="support-faq p-4">
                        <h4 class="support-title">{{ __('adminlte.support_faq') }}</h4>
                        <p class="support-text">{{ __('adminlte.support_faq_desc') }}</p>
                        <a href="{{ route('faq') }}" class="btn btn-secondary">{{ __('adminlte.support_view_faq') }}</a>
                    </div>
                </div>

                <div class="col-md-6 mb-4" data-aos="fade-left" data-aos-duration="1000">
                    <div class="support-ticket p-4">
                        <h4 class="support-title">{{ __('adminlte.support_ticket_Submit') }}</h4>
                        <p class="support-text">{{ __('adminlte.support_ticket_desc') }}</p>
                        <a href="#" class="btn btn-secondary">{{ __('adminlte.support_open_ticket') }}</a>
                    </div>
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
