@extends('layouts.app')

@section('title', __('adminlte.service_details'))

@section('content_header')
    <h1>{{ __('adminlte.service_details') }}</h1>
@stop

@section('content')
    <div class="container">
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">{{ $service->name }}</h5>
                <p class="card-text">
                    <strong>{{ __('adminlte.type') }}:</strong> {{ $service->type }}<br>
                    <strong>{{ __('adminlte.category') }}:</strong> {{ $service->category }}<br>
                    <strong>{{ __('adminlte.rate') }}:</strong> ${{ $service->rate }} {{ __('adminlte.per_1000') }}<br>
                    <strong>{{ __('adminlte.min') }}:</strong> {{ $service->min }}<br>
                    <strong>{{ __('adminlte.max') }}:</strong> {{ $service->max }}<br>
                    <strong>{{ __('adminlte.refill') }}:</strong> {{ $service->refill ? __('adminlte.yes') : __('adminlte.no') }}<br>
                    <strong>{{ __('adminlte.cancel') }}:</strong> {{ $service->cancel ? __('adminlte.yes') : __('adminlte.no') }}<br>
                </p>
            </div>
        </div>
    </div>
@endsection
