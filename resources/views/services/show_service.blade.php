@extends('layouts.welcome')
@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h1>{{ app()->getLocale() === 'ar' ? $service->name_ar : $service->name_en }}</h1>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Service Details</h5>
                        <p><strong>Service ID:</strong> {{ $service->service_id }}</p>
                        <p><strong>Category:</strong> {{ app()->getLocale() === 'ar' ? $service->category_ar : $service->category_en }}</p>
                        <p><strong>Price:</strong> ${{ $service->rate }} per {{ $service->min }}-{{ $service->max }}</p>
                        <p><strong>Minimum:</strong> {{ $service->min }}</p>
                        <p><strong>Maximum:</strong> {{ $service->max }}</p>

                        @auth
                            <a href="{{ route('orders.create', ['service' => $service->id]) }}" class="btn btn-primary">
                                Order Now
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                Login to Order
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
