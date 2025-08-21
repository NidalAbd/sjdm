@extends('layouts.app')

@section('title', __('adminlte.transaction_details'))

@section('content_header')
    @include('partials.breadcrumbs')
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Payment Status Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-5">
                    @if($transaction->status === 'completed')
                        <div class="payment-success mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            <h3 class="text-success mt-3">Payment Successful!</h3>
                            <p class="text-muted">Your balance has been updated successfully.</p>
                        </div>
                    @elseif($transaction->status === 'failed')
                        <div class="payment-failed mb-4">
                            <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                            <h3 class="text-danger mt-3">Payment Failed</h3>
                            <p class="text-muted">The payment could not be processed. Please try again.</p>
                        </div>
                    @elseif($transaction->status === 'suspected')
                        <div class="payment-suspected mb-4">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                            <h3 class="text-warning mt-3">Payment Under Review</h3>
                            <p class="text-muted">Your payment is being reviewed for security purposes.</p>
                        </div>
                    @else
                        <div class="payment-pending mb-4">
                            <i class="fas fa-clock text-info" style="font-size: 4rem;"></i>
                            <h3 class="text-info mt-3">Payment Processing</h3>
                            <p class="text-muted">Your payment is being processed. Please wait.</p>
                        </div>
                    @endif

                    <!-- Amount Display -->
                    <div class="amount-display mb-4">
                        <h2 class="text-primary">${{ number_format($transaction->amount, 2) }}</h2>
                        <p class="text-muted">Transaction Amount</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        @if($transaction->status === 'failed')
                            <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-redo"></i> Try Again
                            </a>
                        @endif
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-list"></i> View All Transactions
                        </a>
                    </div>
                </div>
            </div>

            <!-- Transaction Details Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Transaction Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item mb-3">
                                <label class="text-muted small">Transaction ID</label>
                                <p class="mb-0 fw-bold">#{{ $transaction->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item mb-3">
                                <label class="text-muted small">Status</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'failed' ? 'danger' : ($transaction->status === 'suspected' ? 'warning' : 'info')) }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item mb-3">
                                <label class="text-muted small">Type</label>
                                <p class="mb-0 fw-bold">{{ ucfirst($transaction->type) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item mb-3">
                                <label class="text-muted small">Payment Method</label>
                                <p class="mb-0 fw-bold">{{ $transaction->paymentMethod ? $transaction->paymentMethod->name : 'Not specified' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item mb-3">
                                <label class="text-muted small">Created</label>
                                <p class="mb-0 fw-bold">{{ $transaction->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        @if($transaction->processed_at)
                        <div class="col-md-6">
                            <div class="detail-item mb-3">
                                <label class="text-muted small">Processed</label>
                                <p class="mb-0 fw-bold">{{ $transaction->processed_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .payment-success, .payment-failed, .payment-suspected, .payment-pending {
        animation: fadeInUp 0.6s ease-out;
    }

    .amount-display {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 1rem;
        margin: 2rem 0;
    }

    .detail-item label {
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .action-buttons {
        margin-top: 2rem;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush
