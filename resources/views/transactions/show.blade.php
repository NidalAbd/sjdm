@extends('layouts.app')

@section('title', 'Transaction Details')

@section('content_header')
    <h1 class="text-center display-4">{{ config('app.name') }} - Transaction Details</h1>
    @include('partials.breadcrumbs')
@stop

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow border-0 rounded-lg">
                    <div class="card-header bg-primary text-white py-4 d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Transaction #{{ $transaction->id }}</h3>
                        <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : 'danger' }} text-uppercase">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                    <div class="card-body p-5">
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <img src="{{ asset('images/MaxPeak.png') }}" alt="{{ config('app.name') }} Logo" class="img-fluid mb-3" style="max-width: 50px; border-radius: 50%;">
                                <h5 class="text-secondary">Transaction Type</h5>
                                <p class="lead">{{ ucfirst($transaction->type) }}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h5 class="text-secondary">Amount</h5>
                                <p class="lead fw-bold text-success">${{ number_format($transaction->amount, 2) }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="text-secondary">Date</h5>
                                <p>{{ $transaction->created_at->format('d M Y, H:i A') }}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h5 class="text-secondary">Payment Method</h5>
                                <p>{{ $transaction->payment_method ?? 'Not specified' }}</p>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-secondary">Description</h5>
                                <p>
                                    {{ $transaction->description ?? 'No description provided.' }}
                                    @if($transaction->payment_method == 'admin')
                                        (Added by Admin)
                                    @elseif($transaction->payment_method == 'stripe')
                                        (Processed via Stripe Payment)
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-secondary">Details</h5>
                                <table class="table table-striped table-bordered">
                                    <thead class="table-light">
                                    <tr>
                                        <th scope="col">Detail</th>
                                        <th scope="col">Value</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Transaction ID</td>
                                        <td>{{ $transaction->id }}</td>
                                    </tr>
                                    <tr>
                                        <td>User</td>
                                        <td>{{ $transaction->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>{{ ucfirst($transaction->status) }}</td>
                                    </tr>
                                    <!-- Add more rows as needed -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white py-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Transactions
                            </a>
                            <button class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Print Receipt
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        console.log('Transaction Details page loaded');
    </script>
@stop
