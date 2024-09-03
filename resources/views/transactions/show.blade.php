@extends('layouts.app')

@section('title', __('adminlte.transaction_details'))

@section('content_header')
    @include('partials.breadcrumbs')

    <h1>{{ __('adminlte.transaction_details') }}</h1>
@stop
@section('content')
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header  text-white">
                        <h3 class="card-title">{{ __('adminlte.transaction') }} #{{ $transaction->id }}</h3>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>{{ __('adminlte.user') }}:</strong> {{ $transaction->user->name }}
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('adminlte.email') }}:</strong> {{ $transaction->user->email }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>{{ __('adminlte.transaction_id') }}:</strong> {{ $transaction->id }}
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('adminlte.status') }}:</strong>
                                <span class="badge badge-{{ $transaction->status == 'completed' ? 'success' : 'danger' }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>{{ __('adminlte.transaction_type') }}:</strong> {{ ucfirst($transaction->type) }}
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('adminlte.amount') }}:</strong> ${{ number_format($transaction->amount, 2) }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>{{ __('adminlte.payment_method') }}:</strong> {{ $transaction->payment_method ?? __('adminlte.not_specified') }}
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('adminlte.date') }}:</strong> {{ $transaction->created_at->format('d M Y, H:i A') }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>{{ __('adminlte.description') }}:</strong>
                                <p>{{ $transaction->description ?? __('adminlte.no_description_provided') }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Card Footer -->
                    <div class="card-footer text-left">
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('adminlte.back_to_transactions') }}
                        </a>
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
