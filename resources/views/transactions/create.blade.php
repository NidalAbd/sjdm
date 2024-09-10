@extends('layouts.app')
@section('title', __('adminlte.add_balance'))

@section('content_header')
    @include('partials.breadcrumbs')

    <h1>{{ __('adminlte.add_balance') }}</h1>
@stop

@section('content')
    <div class="col-md-12 row justify-content-center">
        <div class="col-md-12 mb-4 p-0 alert-info d-flex justify-content-between align-items-center rounded shadow-sm " role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-shield-alt fa-2x m-2"></i>
                <div>
                    <p class="m-2" style="font-size: 1.0rem;">
                        {{ __('adminlte.payment_crypto_info') }}
                    </p>
                </div>
            </div>

            <!-- WhatsApp Button to Contact Admin -->
            <a href="https://wa.me/971557830054?text=I%20want%20to%20pay%20via%20USDT" target="_blank" class="btn btn-lg btn-success btn-sm px-4 py-2">
                <i class="fab fa-whatsapp me-2"></i> {{ __('adminlte.contact_admin_whatsapp') }}
            </a>
        </div>

        <!-- Payment Form Section -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm rounded">
                <div class="card-body">
                    <div class=" alert-info text-center mb-4 mt-2" role="alert">
                        <i class="fas fa-shield-alt"></i> {{ __('adminlte.redirect_stripe') }}
                    </div>
                    <h2 class="text-center mb-4 text-dark">{{ __('adminlte.add_balance') }}</h2>
                    <p class="text-center text-muted mb-5">{{ __('adminlte.securely_add_funds') }}</p>

                    <!-- Display error messages -->
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>{{ __('adminlte.error') }}:</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('adminlte.close') }}"></button>
                        </div>
                    @endif

                    <!-- Display success message -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>{{ __('adminlte.success') }}:</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('adminlte.close') }}"></button>
                        </div>
                    @endif

                    <!-- Payment Form -->
                    <form action="{{ route('checkout') }}" method="POST" class="needs-validation" novalidate id="paymentForm">
                        @csrf
                        <div class="form-floating mb-4">
                            <input type="number" name="amount" class="form-control" id="amount" required min="10" placeholder="{{ __('adminlte.enter_amount') }}" oninput="validateAmount()">
                            <label for="amount">{{ __('adminlte.amount') }}</label>
                            <div class="invalid-feedback">
                                {{ __('adminlte.enter_valid_amount') }}
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100" id="proceedButton" disabled>{{ __('adminlte.proceed_to_payment') }}</button>

                    </form>
                </div>
            </div>
        </div>

        <!-- Instructions and Policy Section -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4 text-dark">{{ __('adminlte.payment_instructions_policy') }}</h3>

                    <p class="mb-3"><strong>{{ __('adminlte.payment_process') }}:</strong> {{ __('adminlte.payment_process_detail') }}</p>

                    <p class="mb-3"><strong>{{ __('adminlte.security') }}:</strong> {{ __('adminlte.security_detail') }}</p>

                    <p class="mb-3"><strong>{{ __('adminlte.refund_policy') }}:</strong> {{ __('adminlte.refund_policy_detail') }}</p>

                    <p class="mb-3"><strong>{{ __('adminlte.support') }}:</strong> {{ __('adminlte.support_detail') }}</p>

                    <p class="text-center text-muted mt-5"><small>{{ __('adminlte.agree_terms') }}</small></p>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .form-control {
            background-color: #fff;
            color: #000;
            border: 1px solid #ced4da;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .dark-mode .form-control {
            background-color: #343a40;
            color: #ffffff;
            border: 1px solid #454d55;
        }

        .form-control::placeholder {
            color: #6c757d;
        }

        .dark-mode .form-control::placeholder {
            color: #adb5bd;
        }

        .form-control:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .dark-mode .form-control:focus {
            border-color: #5a9bd4;
            box-shadow: 0 0 0 0.2rem rgba(90, 155, 212, 0.25);
        }
    </style>
@endpush

    <script>
        function validateAmount() {
            const amountField = document.getElementById('amount');
            const proceedButton = document.getElementById('proceedButton');
            const amount = parseFloat(amountField.value);

            if (!isNaN(amount) && amount >= 10) {
                proceedButton.disabled = false;
            } else {
                proceedButton.disabled = true;
            }
        }

        (function () {
            'use strict';

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

    <style>
        body.dark-mode .form-control {
            background-color: #2c2c2c;
            color: #f1f1f1;
            border-color: #444;
        }

        body.dark-mode .form-control:focus {
            background-color: #3a3a3a;
            color: #f1f1f1;
            border-color: #555;
        }

        body.dark-mode .form-floating label {
            color: #bbb;
        }
    </style>
