@extends('layouts.app')

@section('title', __('adminlte.transaction_details'))

@section('content_header')
    <h1 class="text-center display-4">{{ config('app.name') }} - {{ __('adminlte.transaction_details') }}</h1>
    @include('partials.breadcrumbs')
@stop

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow border-0 rounded-lg">
                    <div class="card-header bg-primary text-white py-4 d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">{{ __('adminlte.transaction') }} #{{ $transaction->id }}</h3>
                        <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : 'danger' }} text-uppercase">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                    <div class="card-body p-5">
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <img src="{{ asset('images/MaxPeak.png') }}" alt="{{ config('app.name') }} Logo" class="img-fluid mb-3" style="max-width: 50px; border-radius: 50%;">
                                <h5 class="text-secondary">{{ __('adminlte.transaction_type') }}</h5>
                                <p class="lead">{{ ucfirst($transaction->type) }}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h5 class="text-secondary">{{ __('adminlte.amount') }}</h5>
                                <p class="lead fw-bold text-success">${{ number_format($transaction->amount, 2) }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="text-secondary">{{ __('adminlte.date') }}</h5>
                                <p>{{ $transaction->created_at->format('d M Y, H:i A') }}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h5 class="text-secondary">{{ __('adminlte.payment_method') }}</h5>
                                <p>{{ $transaction->payment_method ?? __('adminlte.not_specified') }}</p>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-secondary">{{ __('adminlte.description') }}</h5>
                                <p>
                                    {{ $transaction->description ?? __('adminlte.no_description_provided') }}
                                    @if($transaction->payment_method == 'admin')
                                        ({{ __('adminlte.added_by_admin') }})
                                    @elseif($transaction->payment_method == 'stripe')
                                        ({{ __('adminlte.processed_via_stripe') }})
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-secondary">{{ __('adminlte.details') }}</h5>
                                <table class="table table-striped table-bordered">
                                    <thead class="table-light">
                                    <tr>
                                        <th scope="col">{{ __('adminlte.detail') }}</th>
                                        <th scope="col">{{ __('adminlte.value') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{ __('adminlte.transaction_id') }}</td>
                                        <td>{{ $transaction->id }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('adminlte.user') }}</td>
                                        <td>{{ $transaction->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('adminlte.status') }}</td>
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
                                <i class="fas fa-arrow-left me-2"></i>{{ __('adminlte.back_to_transactions') }}
                            </a>
                            <button class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>{{ __('adminlte.print_receipt') }}
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
        console.log('{{ __('adminlte.transaction_details_page_loaded') }}');
    </script>
@stop
