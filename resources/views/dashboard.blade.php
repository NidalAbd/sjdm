@extends('layouts.app')

@section('title', __('adminlte.edit_user'))

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>{{ __('adminlte.dashboard') }}</h1>
@stop

@section('content')
    <!-- Dashboard header -->
    <h1>{{ __('adminlte.welcome_dashboard') }}</h1>

    <!-- Metric widgets row -->
    <div class="row mt-4">
        @if(auth()->user()->isAdmin())
            <!-- Admin-specific Widgets -->
            <!-- Users widget -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $userCount ?? 0 }}</h3>
                        <p>{{ __('adminlte.users') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('users.index') }}" class="small-box-footer">{{ __('adminlte.more_info') }} <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Services widget -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $serviceCount ?? 0 }}</h3>
                        <p>{{ __('adminlte.services') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <a href="{{ route('services.index') }}" class="small-box-footer">{{ __('adminlte.more_info') }} <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Orders widget -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $orderCount ?? 0 }}</h3>
                        <p>{{ __('adminlte.orders') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <a href="{{ route('orders.index') }}" class="small-box-footer">{{ __('adminlte.more_info') }} <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Prices Start widget -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $startingPrice ?? 0 }}</h3>
                        <p>{{ __('adminlte.prices_start') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <a href="#" class="small-box-footer">{{ __('adminlte.more_info') }} <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @else
            <!-- User-specific Widgets -->
            <!-- Referrals widget -->
            <!-- Referrals widget -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $verifiedActiveReferrals->count() }}</h3>
                        <p>{{ __('adminlte.referrals') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <a href="{{ route('profile.settings') }}" class="small-box-footer">{{ __('adminlte.more_info') }} <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>


            <!-- Orders widget -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ auth()->user()->orders()->count() }}</h3>
                        <p>{{ __('adminlte.orders') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <a href="{{ route('orders.index') }}" class="small-box-footer">{{ __('adminlte.more_info') }} <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Transactions widget -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ auth()->user()->transactions()->count() }}</h3>
                        <p>{{ __('adminlte.transactions') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <a href="{{ route('transactions.index') }}" class="small-box-footer">{{ __('adminlte.more_info') }} <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @endif
    </div>
@endsection
