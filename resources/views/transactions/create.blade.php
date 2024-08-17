@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <!-- Payment Form Section -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body">
                        <h2 class="text-center mb-4 text-dark">Add Balance</h2>
                        <p class="text-center text-muted mb-5">Securely add funds to your account via Stripe.</p>

                        <!-- Display error messages -->
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Display success message -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success:</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Payment Form -->
                        <form action="{{ route('checkout') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <div class="form-floating mb-4">
                                <input type="number" name="amount" class="form-control" id="amount" required min="1" placeholder="Enter amount">
                                <label for="amount">Amount</label>
                                <div class="invalid-feedback">
                                    Please enter a valid amount.
                                </div>
                            </div>

                            <div class="alert alert-info text-center mb-4" role="alert">
                                <i class="fas fa-shield-alt"></i> You will be redirected to Stripe's secure payment gateway.
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">Proceed to Payment</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Instructions and Policy Section -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4 text-dark">Payment Instructions & Policy</h3>

                        <p class="mb-3"><strong>Payment Process:</strong> After entering the amount you wish to add, click "Proceed to Payment". You will be redirected to Stripe’s secure payment gateway where you can complete your transaction using your credit or debit card.</p>

                        <p class="mb-3"><strong>Security:</strong> All transactions are secured and encrypted by Stripe. Your payment details are not stored on our servers, ensuring the highest level of security.</p>

                        <p class="mb-3"><strong>Refund Policy:</strong> Once the transaction is complete, the added balance is non-refundable. Please ensure you enter the correct amount before proceeding.</p>

                        <p class="mb-3"><strong>Support:</strong> If you encounter any issues during the payment process, please contact our support team at <a href="mailto:support@sjdm.store">support@sjdm.store</a>. We are here to assist you.</p>

                        <p class="text-center text-muted mt-5"><small>By proceeding with the payment, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</small></p>
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
