@php
    $currentLanguage = app()->getLocale();
@endphp
@extends('layouts.app')

@section('title', __('adminlte.service_details'))

@section('content_header')
    @include('partials.breadcrumbs')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-primary">{{ __('adminlte.service_details') }}</h1>
        <div>
            <a href="{{ route('services.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>{{ __('adminlte.back') }}
            </a>
            @can('assign_role')
                <a href="{{ route('services.edit', $service->service_id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit me-1"></i>{{ __('adminlte.edit') }}
                </a>
            @endcan
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Main Service Information -->
        <div class="col-lg-8">
            <!-- Service Overview Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>{{ __('adminlte.service_overview') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="service-title-section p-3 bg-light rounded">
                                <h4 class="mb-2 text-primary">{{ $currentLanguage === 'ar' ? $service->name_ar : $service->name_en }}</h4>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-hashtag me-1"></i>{{ __('adminlte.service_id') }}: <strong>#{{ $service->service_id }}</strong>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box h-100">
                                <div class="info-box-icon bg-primary">
                                    <i class="fas fa-tags"></i>
                                </div>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ __('adminlte.category') }}</span>
                                    <span class="info-box-number">{{ $currentLanguage === 'ar' ? $service->category_ar : $service->category_en }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box h-100">
                                <div class="info-box-icon bg-success">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ __('adminlte.type') }}</span>
                                    <span class="info-box-number">{{ $service->type }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-dollar-sign me-2"></i>{{ __('adminlte.pricing_information') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="pricing-card text-center p-4 border rounded">
                                <div class="pricing-icon mb-3">
                                    <i class="fas fa-money-bill-wave fa-3x text-success"></i>
                                </div>
                                <h5 class="text-success">{{ __('adminlte.customer_rate') }}</h5>
                                <div class="pricing-amount">
                                    <span class="h2 text-success">${{ number_format($service->rate, 4) }}</span>
                                    <small class="text-muted">{{ __('adminlte.per_1000') }}</small>
                                </div>
                            </div>
                        </div>

                        @can('assign_role')
                            <div class="col-md-6">
                                <div class="pricing-card text-center p-4 border rounded">
                                    <div class="pricing-icon mb-3">
                                        <i class="fas fa-coins fa-3x text-warning"></i>
                                    </div>
                                    <h5 class="text-warning">{{ __('adminlte.provider_cost') }}</h5>
                                    <div class="pricing-amount">
                                        <span class="h2 text-warning">${{ number_format($service->cost, 4) }}</span>
                                        <small class="text-muted">{{ __('adminlte.per_1000') }}</small>
                                    </div>
                                    <div class="mt-2">
                                        @php
                                            $profit = $service->rate - $service->cost;
                                            $profitPercentage = $service->cost > 0 ? (($profit / $service->cost) * 100) : 0;
                                        @endphp
                                        <small class="text-info">
                                            {{ __('adminlte.profit') }}: ${{ number_format($profit, 4) }} ({{ number_format($profitPercentage, 1) }}%)
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Order Limits -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>{{ __('adminlte.order_limits') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-box h-100">
                                <div class="info-box-icon bg-info">
                                    <i class="fas fa-sort-numeric-up"></i>
                                </div>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ __('adminlte.minimum_order') }}</span>
                                    <span class="info-box-number">{{ number_format($service->min) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box h-100">
                                <div class="info-box-icon bg-warning">
                                    <i class="fas fa-sort-numeric-down"></i>
                                </div>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ __('adminlte.maximum_order') }}</span>
                                    <span class="info-box-number">{{ number_format($service->max) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Service Features -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-list-check me-2"></i>{{ __('adminlte.service_features') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="feature-list">
                        <div class="feature-item d-flex align-items-center mb-3 p-2 rounded {{ $service->refill ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10' }}">
                            <div class="feature-icon me-3">
                                <i class="fas fa-redo-alt {{ $service->refill ? 'text-success' : 'text-danger' }}"></i>
                            </div>
                            <div>
                                <span class="fw-bold">{{ __('adminlte.refill') }}</span>
                                <div class="small {{ $service->refill ? 'text-success' : 'text-muted' }}">
                                    {{ $service->refill ? __('adminlte.available') : __('adminlte.not_available') }}
                                </div>
                            </div>
                        </div>

                        <div class="feature-item d-flex align-items-center mb-3 p-2 rounded {{ $service->cancel ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10' }}">
                            <div class="feature-icon me-3">
                                <i class="fas fa-times-circle {{ $service->cancel ? 'text-success' : 'text-danger' }}"></i>
                            </div>
                            <div>
                                <span class="fw-bold">{{ __('adminlte.cancellation') }}</span>
                                <div class="small {{ $service->cancel ? 'text-success' : 'text-muted' }}">
                                    {{ $service->cancel ? __('adminlte.available') : __('adminlte.not_available') }}
                                </div>
                            </div>
                        </div>

                        <div class="feature-item d-flex align-items-center mb-3 p-2 rounded bg-info bg-opacity-10">
                            <div class="feature-icon me-3">
                                <i class="fas fa-bolt text-info"></i>
                            </div>
                            <div>
                                <span class="fw-bold">{{ __('adminlte.service_type') }}</span>
                                <div class="small text-info">{{ ucfirst($service->type) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>{{ __('adminlte.quick_actions') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('orders.create', ['service' => $service->service_id]) }}" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-2"></i>{{ __('adminlte.create_order') }}
                        </a>

                        @can('assign_role')
                            <a href="{{ route('services.edit', $service->service_id) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>{{ __('adminlte.edit_service') }}
                            </a>
                        @endcan

                        <a href="{{ route('services.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list me-2"></i>{{ __('adminlte.all_services') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Service Statistics (if you have them) -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>{{ __('adminlte.statistics') }}
                    </h3>
                </div>
                <div class="card-body">
                    @php
                        // You can add actual statistics here if you track them
                        $totalOrders = $service->orders()->count();
                        $completedOrders = $service->orders()->where('status', 'completed')->count();
                        $pendingOrders = $service->orders()->where('status', 'pending')->count();
                    @endphp

                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <span>{{ __('adminlte.total_orders') }}</span>
                        <span class="badge bg-primary">{{ number_format($totalOrders) }}</span>
                    </div>

                    <div class="stat-item d-flex justify-content-between align-items-center mb-3">
                        <span>{{ __('adminlte.completed_orders') }}</span>
                        <span class="badge bg-success">{{ number_format($completedOrders) }}</span>
                    </div>

                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <span>{{ __('adminlte.pending_orders') }}</span>
                        <span class="badge bg-warning">{{ number_format($pendingOrders) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .pricing-card {
            transition: all 0.3s ease;
            border: 2px solid #e9ecef !important;
        }

        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: #007bff !important;
        }

        .info-box {
            display: block;
            min-height: 90px;
            background: #fff;
            width: 100%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            border-radius: 0.25rem;
            margin-bottom: 15px;
        }

        .info-box-icon {
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0.25rem;
            display: block;
            float: left;
            height: 90px;
            width: 90px;
            text-align: center;
            font-size: 45px;
            line-height: 90px;
            background: rgba(0,0,0,0.2);
        }

        .info-box-icon > i {
            color: rgba(255,255,255,0.7);
        }

        .info-box-content {
            padding: 5px 10px;
            margin-left: 90px;
        }

        .info-box-number {
            display: block;
            font-weight: bold;
            font-size: 18px;
        }

        .info-box-text {
            display: block;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .feature-item {
            transition: all 0.2s ease;
        }

        .feature-item:hover {
            transform: translateX(5px);
        }

        .stat-item {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .service-title-section {
            border-left: 4px solid #007bff;
        }

        @media (max-width: 768px) {
            .info-box-icon {
                width: 70px;
                height: 70px;
                font-size: 35px;
                line-height: 70px;
            }

            .info-box-content {
                margin-left: 70px;
            }
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Add any JavaScript functionality here

            // Example: Copy service ID to clipboard
            $('.service-id-copy').click(function() {
                const serviceId = $(this).data('service-id');
                navigator.clipboard.writeText(serviceId).then(function() {
                    toastr.success('{{ __("adminlte.service_id_copied") }}');
                });
            });
        });
    </script>
@stop
