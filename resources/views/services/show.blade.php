@extends('layouts.app')

@section('title', __('adminlte.service_details')) <!-- Use translation for the title -->

@section('content_header')
    <h1>{{ __('adminlte.service_details') }}</h1> <!-- Use translation for the header -->
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3>{{ $service->name }}</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ __('adminlte.type') }}:</strong> {{ $service->type }}</p> <!-- Use translation for labels -->
                        <p><strong>{{ __('adminlte.category') }}:</strong> {{ $service->category }}</p>
                        <p><strong>{{ __('adminlte.rate') }}:</strong> ${{ number_format($service->rate, 2) }} {{ __('adminlte.per_1000') }}</p>
                        <p><strong>{{ __('adminlte.min') }}:</strong> {{ $service->min }}</p>
                        <p><strong>{{ __('adminlte.max') }}:</strong> {{ $service->max }}</p>
                        <p><strong>{{ __('adminlte.refill') }}:</strong> {{ $service->refill ? __('adminlte.yes') : __('adminlte.no') }}</p>
                        <p><strong>{{ __('adminlte.cancel') }}:</strong> {{ $service->cancel ? __('adminlte.yes') : __('adminlte.no') }}</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('services.index') }}" class="btn btn-primary">{{ __('adminlte.back_to_services') }}</a> <!-- Use translation for button -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
