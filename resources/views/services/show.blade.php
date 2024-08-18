@extends('layouts.app')

@section('title', 'Service Details')

@section('content_header')
    <h1>Service Details</h1>
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
                        <p><strong>Type:</strong> {{ $service->type }}</p>
                        <p><strong>Category:</strong> {{ $service->category }}</p>
                        <p><strong>Rate:</strong> ${{ number_format($service->rate, 2) }} per 1000</p>
                        <p><strong>Min:</strong> {{ $service->min }}</p>
                        <p><strong>Max:</strong> {{ $service->max }}</p>
                        <p><strong>Refill:</strong> {{ $service->refill ? 'Yes' : 'No' }}</p>
                        <p><strong>Cancel:</strong> {{ $service->cancel ? 'Yes' : 'No' }}</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('services.index') }}" class="btn btn-primary">Back to Services</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
