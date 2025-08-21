@extends('layouts.app')

@section('title', __('adminlte.complete_transaction'))

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <div class="card card-primary shadow-lg">
                    <div class="card-header bg-primary text-center">
                        <h2 class="card-title text-white">{{ __('adminlte.complete_transaction') }}</h2>
                    </div>
                    <div class="card-body">
                        <!-- Transaction Details -->
                        <p class="text-center text-muted">
                            {{ __('adminlte.transaction_canceled', ['amount' => number_format($transaction->amount, 2)]) }}
                        </p>

                        <!-- Payment Form -->
                        <form action="{{ route('checkout') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <div class="form-floating mb-4">
                                <input type="number" name="amount" class="form-control" id="amount" value="{{ $transaction->amount }}" readonly>
                                <label for="amount">{{ __('adminlte.transaction_amount') }}</label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    {{ __('adminlte.complete_payment') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Support for both dark and light mode */
        .form-control {
            background-color: #f7f8fa;
            border: 1px solid #ced4da;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .dark-mode .form-control {
            background-color: #2c2c2c;
            border-color: #444;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .dark-mode .form-control:focus {
            background-color: #3a3a3a;
            color: #f1f1f1;
            border-color: #5a9bd4;
            box-shadow: 0 0 0 0.2rem rgba(90, 155, 212, 0.25);
        }

        /* Floating labels style */
        .form-floating label {
            font-size: 1rem;
            padding-left: 12px;
        }

        .card-header {
            border-bottom: none;
        }

        .dark-mode .card-header {
            background-color: #343a40;
            color: #ffffff;
        }

        .dark-mode .card {
            background-color: #2c2c2c;
            border-color: #444;
        }

        /* Button Styling */
        .btn-lg {
            font-size: 1.2rem;
        }
    </style>
@endpush
