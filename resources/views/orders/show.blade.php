@extends('layouts.app')

@section('title', 'Service Details')

@section('content_header')
    <h1>Service Details</h1>
@stop

@section('content')
    <div class="container">
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">{{ $service->name }}</h5>
                <p class="card-text">
                    <strong>Type:</strong> {{ $service->type }}<br>
                    <strong>Category:</strong> {{ $service->category }}<br>
                    <strong>Rate:</strong> ${{ $service->rate }} per 1000<br>
                    <strong>Min:</strong> {{ $service->min }}<br>
                    <strong>Max:</strong> {{ $service->max }}<br>
                    <strong>Refill:</strong> {{ $service->refill ? 'Yes' : 'No' }}<br>
                    <strong>Cancel:</strong> {{ $service->cancel ? 'Yes' : 'No' }}<br>
                </p>
            </div>
        </div>
    </div>
@endsection
