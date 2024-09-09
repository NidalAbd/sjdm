@extends('layouts.app')
@section('title', __('adminlte.add_balance'))

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>{{ __('adminlte.add_balance') }}</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-lg">
                <div class="card-body">
                    <!-- Payment Method Header -->
                    <div class=" alert-info text-center mb-4 mt-2">
                        <i class="fas fa-shield-alt"></i> {{ __('adminlte.select_payment_method') }}
                    </div>
                    <h2 class="text-center mb-4 text-dark">{{ __('adminlte.add_balance') }}</h2>
                    <p class="text-center text-muted">{{ __('adminlte.securely_add_funds') }}</p>

                    <!-- Display error messages -->
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>{{ __('adminlte.error') }}:</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Display success message -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <strong>{{ __('adminlte.success') }}:</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Tabs for Payment Options -->
                    <ul class="nav nav-tabs" id="paymentTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="stripe-tab" data-bs-toggle="tab" href="#stripe" role="tab">
                                <i class="fas fa-credit-card"></i> {{ __('adminlte.payment_stripe') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="crypto-tab" data-bs-toggle="tab" href="#crypto" role="tab">
                                <i class="fas fa-coins"></i> {{ __('adminlte.payment_crypto') }}
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-4">
                        <!-- Stripe Payment Form -->
                        <div class="tab-pane fade show active" id="stripe" role="tabpanel">
                            <h4 class="text-center mb-4">{{ __('adminlte.add_balance_via_stripe') }}</h4>

                            <!-- Stripe Payment Form -->
                            <form action="{{ route('checkout') }}" method="POST" class="needs-validation" novalidate>
                                @csrf
                                <div class="form-floating mb-4">
                                    <input type="number" name="amount" class="form-control" id="amount-stripe" required min="1" placeholder="{{ __('adminlte.enter_amount') }}">
                                    <label for="amount-stripe">{{ __('adminlte.amount') }}</label>
                                    <div class="invalid-feedback">
                                        {{ __('adminlte.enter_valid_amount') }}
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100">{{ __('adminlte.proceed_to_payment') }}</button>
                            </form>

                            <!-- Stripe Instructions -->
                            <div class="mt-4">
                                <h5 class="text-center">{{ __('adminlte.payment_instructions_policy') }}</h5>
                                <p>{{ __('adminlte.payment_process_detail_stripe') }}</p>
                                <p>{{ __('adminlte.security_detail_stripe') }}</p>
                                <p>{{ __('adminlte.refund_policy_stripe') }}</p>
                            </div>
                        </div>

                        <!-- Crypto Payment Instructions -->
                        <div class="tab-pane fade" id="crypto" role="tabpanel">
                            <h4 class="text-center mb-4">{{ __('Pay via Cryptocurrency') }}</h4>

                            <div class=" alert-warning text-center">
                                <p>{{ __('You can pay using USDT (TRC20). Please contact our admin on WhatsApp for payment instructions and verification.') }}</p>
                                <a href="https://wa.me/971557830054?text=I%20want%20to%20pay%20via%20USDT" target="_blank" class="btn btn-success mt-3">
                                    <i class="fab fa-whatsapp"></i> {{ __('Contact Admin on WhatsApp') }}
                                </a>
                            </div>

                            <!-- Crypto Payment Instructions -->
                            <div class="mt-4">
                                <h5 class="text-center">{{ __('adminlte.payment_instructions_policy') }}</h5>
                                <p>{{ __('After sending the payment via USDT, contact the admin on WhatsApp with your payment details.') }}</p>
                                <p>{{ __('Make sure you send the correct amount to avoid delays in processing.') }}</p>
                                <p>{{ __('Refunds are only available for failed transactions. Contact support for more information.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* General form control styles using AdminLTE */
        .form-control {
            font-size: 1.25rem;
            padding: 0.75rem;
        }

        /* WhatsApp button styles */
        .btn-success {
            font-size: 1.2rem;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .form-control {
                font-size: 1rem;
            }

            .btn-lg {
                font-size: 1rem;
            }

            .btn-success {
                font-size: 1rem;
            }
        }
    </style>
@endpush

@section('scripts')
    <script>
        (function () {
            'use strict';

            // Bootstrap validation script
            var forms = document.querySelectorAll('.needs-validation');

            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }

                        form.classList.add('was-validated');
                    }, false);
                });
        })();
    </script>
@endsection
