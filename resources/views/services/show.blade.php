@extends('layouts.app')

@section('title', __('adminlte.service_details')) <!-- Use translation for the title -->

@section('content_header')
    <h1 class="display-4">{{ __('adminlte.service_details') }}</h1> <!-- Use translation for the header -->
@stop

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-gradient-primary text-white text-center">
                        <h3 class="mb-0">{{ $service->name }}</h3>
                    </div>
                    <div class="card-body p-5">
                        <!-- Service Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5><i class="fas fa-tags text-primary"></i> {{ __('adminlte.type') }}</h5>
                                <p class="text-muted">{{ $service->type }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-list-alt text-primary"></i> {{ __('adminlte.category') }}</h5>
                                <p class="text-muted">{{ $service->category }}</p>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <h5><i class="fas fa-dollar-sign text-primary"></i> {{ __('adminlte.rate') }}</h5>
                                <p class="text-muted">${{ number_format($service->rate, 2) }} {{ __('adminlte.per_1000') }}</p>
                            </div>
                            <div class="col-md-4">
                                <h5><i class="fas fa-sort-numeric-up text-primary"></i> {{ __('adminlte.min') }}</h5>
                                <p class="text-muted">{{ $service->min }}</p>
                            </div>
                            <div class="col-md-4">
                                <h5><i class="fas fa-sort-numeric-down text-primary"></i> {{ __('adminlte.max') }}</h5>
                                <p class="text-muted">{{ $service->max }}</p>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5><i class="fas fa-redo-alt text-primary"></i> {{ __('adminlte.refill') }}</h5>
                                <p class="text-muted">{{ $service->refill ? __('adminlte.yes') : __('adminlte.no') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-times-circle text-primary"></i> {{ __('adminlte.cancel') }}</h5>
                                <p class="text-muted">{{ $service->cancel ? __('adminlte.yes') : __('adminlte.no') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light text-right">
                        <a href="{{ route('services.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-arrow-left"></i> {{ __('adminlte.back_to_services') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card-header {
            background: linear-gradient(90deg, #007bff, #0056b3);
            color: #fff;
        }

        .card-body h5 {
            font-weight: bold;
            color: #333;
        }

        .card-body p {
            font-size: 1.1rem;
        }

        .card-footer {
            background-color: #f8f9fa;
        }

        .btn-primary {
            background: linear-gradient(90deg, #007bff, #0056b3);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #0056b3, #007bff);
            transform: translateY(-3px);
        }

        .card {
            border-radius: 15px;
        }

        .card-header {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .card-footer {
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }

        .text-primary {
            color: #0056b3 !important;
        }
    </style>
@stop
