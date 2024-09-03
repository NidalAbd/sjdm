<!-- resources/views/widgets/how_it_works.blade.php -->
@extends('layouts.welcome')

@section('content')
    <div class="content-section active" id="how-it-works-section">
        <div class="container my-5">
            <h2 class="text-center mb-4 platform-title">{{ __('adminlte.how_it_works') }}</h2>

            <!-- Step 01: Create an Account -->
            <div class="card shadow-lg rounded-lg border-1 mt-4" data-aos="fade-right" data-aos-duration="1500">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-6 d-flex align-items-center">
                            <div>
                                <h3 class="fw-bold">{{ __('adminlte.create_account') }}</h3>
                                <p>{{ __('adminlte.create_account_description') }}</p>
                                <p>{{ __('adminlte.create_account_note') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Placeholder for image -->
                            <img src="{{ asset('images/how_it_work/1th_step.png') }}" alt="{{ __('adminlte.create_account') }}" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 02: Log In -->
            <div class="card shadow-lg rounded-lg border-1 mt-4" data-aos="fade-left" data-aos-duration="1500">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-6 order-md-2 d-flex align-items-center">
                            <div>
                                <h3 class="fw-bold">{{ __('adminlte.log_in') }}</h3>
                                <p>{{ __('adminlte.log_in_description') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 order-md-1">
                            <!-- Placeholder for image -->
                            <img src="{{ asset('images/how_it_work/2th_step.png') }}" alt="{{ __('adminlte.log_in') }}" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 03: Add Balance -->
            <div class="card shadow-lg rounded-lg border-1 mt-4" data-aos="fade-right" data-aos-duration="1500">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-6 d-flex align-items-center">
                            <div>
                                <h3 class="fw-bold">{{ __('adminlte.add_balance') }}</h3>
                                <p>{{ __('adminlte.add_balance_description') }}</p>
                                <p>{{ __('adminlte.add_balance_note') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Placeholder for image -->
                            <img src="{{ asset('images/how_it_work/3th_step.png') }}" alt="{{ __('adminlte.add_balance') }}" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 04: Order Process -->
            <div class="card shadow-lg rounded-lg border-1 mt-4" data-aos="fade-left" data-aos-duration="1500">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-6 order-md-2 d-flex align-items-center">
                            <div>
                                <h3 class="fw-bold">{{ __('adminlte.order_process') }}</h3>
                                <p>{{ __('adminlte.order_process_description') }}</p>
                                <p>{{ __('adminlte.order_process_note') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6 order-md-1">
                            <!-- Placeholder for image -->
                            <img src="{{ asset('images/how_it_work/4th_step.png') }}" alt="{{ __('adminlte.order_process') }}" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 05: Support and Contact Us -->
            <div class="card shadow-lg rounded-lg border-1 mt-4" data-aos="fade-right" data-aos-duration="1500">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-6 d-flex align-items-center">
                            <div>
                                <h3 class="fw-bold">{{ __('adminlte.support_contact') }}</h3>
                                <p>{{ __('adminlte.support_contact_description') }}</p>
                                <p>{{ __('adminlte.support_contact_note') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Placeholder for image -->
                            <img src="{{ asset('images/how_it_work/5th_step.png') }}" alt="{{ __('adminlte.support_contact') }}" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- AOS Animation Library -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        /* Custom styles for dark mode */
        .dark-mode .card-body {
            background-color: #333;
            color: #fff;
        }
        .dark-mode .platform-title,
        .dark-mode .fw-bold {
            color: #fff;
        }
        .dark-mode .form-control {
            background-color: #444;
            color: #fff;
            border-color: #666;
        }
        .dark-mode .form-control::placeholder {
            color: #aaa;
        }
        .dark-mode .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
@endpush

@push('scripts')
    <!-- AOS Animation Library -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init(); // Initialize AOS for animations
    </script>
@endpush
