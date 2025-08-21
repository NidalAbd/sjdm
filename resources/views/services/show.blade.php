@extends('layouts.app')

@section('title', __('adminlte.service_details'))

@section('content_header')
    <h1 class="display-4">{{ __('adminlte.service_details') }}</h1>
@stop

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg">
                    <!-- Category prominently displayed -->
                    <div class="card-header bg-gradient-primary text-white text-center">
                        <h4 class="mb-0">{{ $service->category }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- Service Name (acting as a description) -->
                        <div class="mb-4 text-center">
                            <h5 class="text-muted">{{ $service->name }}</h5>
                        </div>

                        <!-- Service Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-tags"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ __('adminlte.type') }}</span>
                                        <span class="info-box-number">{{ $service->type }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-dollar-sign"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ __('adminlte.rate') }}</span>
                                        <span class="info-box-number">${{ number_format($service->rate, 2) }} {{ __('adminlte.per_1000') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-sort-numeric-up"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ __('adminlte.min') }}</span>
                                        <span class="info-box-number">{{ $service->min }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-sort-numeric-down"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ __('adminlte.max') }}</span>
                                        <span class="info-box-number">{{ $service->max }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-redo-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ __('adminlte.refill') }}</span>
                                        <span class="info-box-number">{{ $service->refill ? __('adminlte.yes') : __('adminlte.no') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary"><i class="fas fa-times-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ __('adminlte.cancel') }}</span>
                                        <span class="info-box-number">{{ $service->cancel ? __('adminlte.yes') : __('adminlte.no') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('services.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> {{ __('adminlte.back_to_services') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
