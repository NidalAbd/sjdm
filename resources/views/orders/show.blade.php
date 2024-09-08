@extends('layouts.app')

@section('title', __('adminlte.order_details'))

@section('content_header')
    <h1>{{ __('adminlte.order_details') }} #{{ $order->id }}</h1>
@stop

@section('content')
        <div class="row justify-content-center">
            <div class="col-md-12">

                <!-- Order Details Card -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ __('adminlte.order_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <!-- Service Name and ID -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <i class="fas fa-tag text-dark"></i>
                                <strong>{{ __('adminlte.service') }}:</strong>
                                {{ app()->getLocale() == 'ar' ? $service->name_ar : $service->name_en }} (ID: {{ $service->service_id }})
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-link text-info"></i>
                                <strong>{{ __('adminlte.link') }}:</strong> <a href="{{ $order->link }}" target="_blank">{{ $order->link }}</a>
                            </div>
                        </div>

                        <!-- Quantity and Charge -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <i class="fas fa-sort-amount-up-alt text-success"></i>
                                <strong>{{ __('adminlte.quantity') }}:</strong> {{ $order->quantity }}
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-dollar-sign text-success"></i>
                                <strong>{{ __('adminlte.charge') }}:</strong> ${{ number_format($order->charge, 7) }}
                            </div>
                        </div>

                        <!-- Start Count and Remains -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <i class="fas fa-play text-primary"></i>
                                <strong>{{ __('adminlte.start_count') }}:</strong> {{ $order->start_count ?? __('adminlte.not_available') }}
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-sync-alt text-warning"></i>
                                <strong>{{ __('adminlte.remains') }}:</strong> {{ $order->remains ?? __('adminlte.not_available') }}
                            </div>
                        </div>

                        <!-- Runs and Interval -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <i class="fas fa-hourglass-half text-secondary"></i>
                                <strong>{{ __('adminlte.runs') }}:</strong> {{ $order->runs ?? __('adminlte.not_available') }}
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-clock text-primary"></i>
                                <strong>{{ __('adminlte.interval') }}:</strong> {{ $order->interval ?? __('adminlte.not_available') }}
                            </div>
                        </div>

                        <!-- Status and API Order ID -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <i class="fas fa-info-circle text-info"></i>
                                <strong>{{ __('adminlte.status') }}:</strong>
                                <span class="badge badge-{{ $order->status == 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-file-alt text-danger"></i>
                                <strong>{{ __('adminlte.api_order_id') }}:</strong> {{ $order->api_order_id ?? __('adminlte.not_available') }}
                            </div>
                        </div>

                        <!-- Can Refill and Can Cancel -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <i class="fas fa-sync-alt text-primary"></i>
                                <strong>{{ __('adminlte.can_refill') }}:</strong> {{ $order->can_refill ? __('adminlte.yes') : __('adminlte.no') }}
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-ban text-danger"></i>
                                <strong>{{ __('adminlte.can_cancel') }}:</strong> {{ $order->can_cancel ? __('adminlte.yes') : __('adminlte.no') }}
                            </div>
                        </div>

                        <!-- Service Details (Category and Rate) -->
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-list-alt text-info"></i>
                                <strong>{{ __('adminlte.category') }}:</strong>
                                {{ app()->getLocale() == 'ar' ? $service->category_ar : $service->category_en }}
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-balance-scale text-primary"></i>
                                <strong>{{ __('adminlte.rate') }}:</strong> ${{ number_format($service->rate, 4) }} {{ __('adminlte.per_1000') }}
                            </div>
                        </div>

                        <!-- Min and Max -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <i class="fas fa-chevron-up text-success"></i>
                                <strong>{{ __('adminlte.min') }}:</strong> {{ $service->min }}
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-chevron-down text-danger"></i>
                                <strong>{{ __('adminlte.max') }}:</strong> {{ $service->max }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
@endsection
