@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <!-- Payment Form Section -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body">
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
                        <form action="{{ route('checkout') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <div class="form-floating mb-4">
                                <input type="number" name="amount" class="form-control" id="amount" required min="1" placeholder="{{ __('adminlte.enter_amount') }}">
                                <label for="amount">{{ __('adminlte.amount') }}</label>
                                <div class="invalid-feedback">
                                    {{ __('adminlte.enter_valid_amount') }}
                                </div>
                            </div>

                            <div class="alert alert-info text-center mb-4" role="alert">
                                <i class="fas fa-shield-alt"></i> {{ __('adminlte.redirect_stripe') }}
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">{{ __('adminlte.proceed_to_payment') }}</button>
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
    </div>
@endsection

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

    <style>
        /* Adjust input styles for dark mode */
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
@endsection
