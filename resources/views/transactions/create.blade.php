@extends('layouts.app')
@section('title', __('adminlte.add_balance'))

@section('content_header')
    @include('partials.breadcrumbs')
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <!-- Progress Bar -->
            <div class="progress-section mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0 me-3">{{ __('adminlte.add_balance') }}</h4>
                        <div class="progress" style="width: 200px; height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 50%"></div>
                        </div>
                        <span class="ms-2 text-muted">2 of 4</span>
                    </div>
                    <a href="#" class="text-decoration-none text-muted">I'll do it later</a>
                </div>
            </div>

            <div class="row">
                <!-- Left Column - Payment Form -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h5 class="mb-4">{{ __('adminlte.select_payment_option') }}</h5>

                            <!-- Alerts -->
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>{{ __('adminlte.error') }}:</strong> {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>{{ __('adminlte.success') }}:</strong> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form id="addBalanceForm" method="POST" action="{{ route('transactions.store') }}" class="needs-validation" novalidate>
                                @csrf
                                
                                <!-- Amount Section -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold mb-3">{{ __('adminlte.deposit_amount') }}</label>
                                    
                                    <!-- Currency Dropdown -->
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <select class="form-select" id="currency">
                                                <option value="USD">USD</option>
                                                <option value="EUR">EUR</option>
                                                <option value="GBP">GBP</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Preset Amounts -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-outline-primary w-100 preset-amount" data-amount="50">
                                                $50
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-outline-primary w-100 preset-amount" data-amount="100">
                                                $100
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-outline-primary w-100 preset-amount active" data-amount="200">
                                                $200
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Custom Amount Input -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="number" 
                                                   name="amount" 
                                                   id="amount" 
                                                   class="form-control form-control-lg" 
                                                   placeholder="Enter amount" 
                                                   min="10" 
                                                   step="0.01" 
                                                   value="200"
                                                   required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Methods Section -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold mb-3">{{ __('adminlte.select_payment_method') }}</label>
                                    
                                    @if($availablePaymentTypes->isNotEmpty())
                                        <div class="row g-3">
                                            @foreach($availablePaymentTypes as $type)
                                                <div class="col-md-6">
                                                    <div class="payment-method-card" data-type="{{ $type['id'] }}">
                                                        <div class="card h-100 border-2 payment-option">
                                                            <div class="card-body text-center p-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input payment-type-radio" 
                                                                           type="radio" 
                                                                           name="payment_type" 
                                                                           value="{{ $type['id'] }}" 
                                                                           id="type_{{ $type['id'] }}"
                                                                           {{ $loop->first ? 'checked' : '' }}>
                                                                    <label class="form-check-label w-100" for="type_{{ $type['id'] }}">
                                                                        <div class="payment-icon mb-2">
                                                                            <i class="{{ $type['icon'] }} fa-2x text-primary"></i>
                                                                        </div>
                                                                        <h6 class="card-title mb-1">{{ $type['name'] }}</h6>
                                                                        <p class="card-text small text-muted mb-0">{{ $type['description'] }}</p>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            {{ __('adminlte.no_payment_methods_available') }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Save as Default Toggle -->
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="saveAsDefault">
                                        <label class="form-check-label" for="saveAsDefault">
                                            {{ __('adminlte.save_as_default_payment') }}
                                        </label>
                                    </div>
                                </div>

                                <!-- Security Info -->
                                <div class="mb-4">
                                    <p class="text-muted small mb-2">
                                        {{ __('adminlte.we_accept_all_payments') }} 
                                        <a href="#" class="text-decoration-none">{{ __('adminlte.see_how_secure') }}</a>
                                    </p>
                                    <div class="security-badges">
                                        <span class="badge bg-success me-2">Visa Secure</span>
                                        <span class="badge bg-primary me-2">MasterCard Secure</span>
                                        <span class="badge bg-info">SSL Secure</span>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg" id="proceedButton" disabled>
                                        <i class="fas fa-arrow-right me-2"></i>
                                        {{ __('adminlte.select_payment_and_continue') }}
                                    </button>
                                </div>

                                <!-- Hidden inputs for selected method -->
                                <input type="hidden" name="payment_method_id" id="selectedMethodId">
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Promotional Section -->
                <div class="col-lg-4">
                    <div class="promotional-section">
                        <div class="card border-0 bg-light">
                            <div class="card-body text-center p-4">
                                <h5 class="mb-3">{{ __('adminlte.add_funds_unlock_positions') }}</h5>
                                
                                <!-- Circular Progress -->
                                <div class="circular-progress mb-4">
                                    <div class="progress-circle">
                                        <div class="progress-circle-inner">
                                            <span class="question-mark">?</span>
                                        </div>
                                    </div>
                                    <div class="progress mt-3">
                                        <div class="progress-bar bg-primary" style="width: 25%"></div>
                                    </div>
                                </div>

                                <!-- Free Positions Info -->
                                <div class="free-positions-info">
                                    <div class="d-flex align-items-center justify-content-between p-3 bg-primary text-white rounded">
                                        <div class="d-flex align-items-center">
                                            <div class="shield-icon me-3">
                                                <i class="fas fa-shield-alt text-white"></i>
                                            </div>
                                            <div class="text-start">
                                                <h6 class="mb-1">{{ __('adminlte.free_trade_positions') }}</h6>
                                                <p class="mb-0 small">{{ __('adminlte.unlock_positions_description') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <div class="position-count">1</div>
                                            <small>{{ __('adminlte.total') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
    .progress-section {
        background: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .preset-amount {
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .preset-amount.active {
        background-color: var(--bs-primary);
        color: white;
        border-color: var(--bs-primary);
    }

    .payment-method-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .payment-option {
        transition: all 0.3s ease;
        border-color: #dee2e6;
        border-radius: 0.75rem;
    }

    .payment-method-card:hover .payment-option {
        border-color: var(--bs-primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.15);
    }

    .payment-method-card .payment-type-radio:checked ~ .form-check-label .payment-option {
        border-color: var(--bs-primary);
        background: linear-gradient(145deg, #f8f9ff, #ffffff);
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
    }

    .payment-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 123, 255, 0.1);
        border-radius: 50%;
    }

    .circular-progress {
        position: relative;
    }

    .progress-circle {
        width: 120px;
        height: 120px;
        margin: 0 auto;
        position: relative;
    }

    .progress-circle-inner {
        width: 100%;
        height: 100%;
        border: 3px dashed #ff6b35;
        border-radius: 50%;
        background: #ff6b35;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        font-weight: bold;
    }

    .question-mark {
        font-size: 3rem;
        font-weight: bold;
    }

    .free-positions-info {
        margin-top: 2rem;
    }

    .shield-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .position-count {
        font-size: 2rem;
        font-weight: bold;
        line-height: 1;
    }

    .security-badges img {
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }

    .security-badges img:hover {
        opacity: 1;
    }

    .btn-lg {
        padding: 1rem 2rem;
        font-weight: 600;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-lg:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(var(--bs-primary-rgb), 0.3);
    }

    /* Dark mode support */
    [data-bs-theme="dark"] .payment-option {
        background-color: var(--bs-gray-800);
        border-color: var(--bs-gray-600);
    }

    [data-bs-theme="dark"] .payment-method-card:hover .payment-option {
        background-color: var(--bs-gray-700);
    }

    [data-bs-theme="dark"] .promotional-section .card {
        background-color: var(--bs-gray-800) !important;
    }
</style>
@endpush

<script>
// Simple test to see if JavaScript is working
console.log('Script loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Script is running');
    
    const amountInput = document.getElementById('amount');
    const proceedButton = document.getElementById('proceedButton');
    const paymentTypeRadios = document.querySelectorAll('.payment-type-radio');
    const paymentForm = document.getElementById('addBalanceForm');
    const selectedMethodIdInput = document.getElementById('selectedMethodId');
    const presetButtons = document.querySelectorAll('.preset-amount');

    console.log('Elements found:', {
        amountInput: amountInput,
        proceedButton: proceedButton,
        paymentTypeRadios: paymentTypeRadios.length,
        paymentForm: paymentForm,
        selectedMethodIdInput: selectedMethodIdInput,
        presetButtons: presetButtons.length
    });

    let selectedPaymentType = null;
    let selectedMethodId = null;

    // Initialize with first type if available
    if (paymentTypeRadios.length > 0) {
        selectedPaymentType = paymentTypeRadios[0].value;
        paymentTypeRadios[0].checked = true;
        console.log('Initializing with payment type:', selectedPaymentType);
    } else {
        console.log('No payment type radios found');
    }

    // Force validation on page load
    setTimeout(() => {
        validateForm();
    }, 100);

    // Preset amount buttons
    presetButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Preset button clicked:', this.getAttribute('data-amount'));
            // Remove active class from all buttons
            presetButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            // Set amount value
            const amount = this.getAttribute('data-amount');
            amountInput.value = amount;
            console.log('Amount set to:', amount);
            validateForm();
        });
    });

    // Payment type selection
    paymentTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            selectedPaymentType = this.value;
            selectedMethodIdInput.value = this.value; // Set the payment method ID
            console.log('Payment type changed to:', selectedPaymentType);
            validateForm();
        });
    });

    // Amount validation
    amountInput.addEventListener('input', function() {
        console.log('Amount changed to:', this.value);
        validateForm();
    });

    // Validation function
    function validateForm() {
        const amount = parseFloat(amountInput.value);
        const isAmountValid = !isNaN(amount) && amount >= 10;
        const isPaymentTypeSelected = selectedPaymentType !== null;

        console.log('Validating form:', {
            amount: amount,
            isAmountValid: isAmountValid,
            selectedPaymentType: selectedPaymentType,
            isPaymentTypeSelected: isPaymentTypeSelected
        });

        if (isAmountValid && isPaymentTypeSelected) {
            proceedButton.disabled = false;
            proceedButton.classList.remove('btn-secondary');
            proceedButton.classList.add('btn-primary');
            amountInput.setCustomValidity('');
            console.log('Button enabled - form is valid');
        } else {
            proceedButton.disabled = true;
            proceedButton.classList.remove('btn-primary');
            proceedButton.classList.add('btn-secondary');
            if (!isAmountValid && amountInput.value) {
                amountInput.setCustomValidity('{{ __("adminlte.minimum_amount_10") }}');
            }
            console.log('Button disabled - form is invalid');
        }
    }

    // Bootstrap form validation
    (function() {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
});
</script>
