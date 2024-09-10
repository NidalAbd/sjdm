@extends('layouts.app')

@section('title', __('adminlte.dashboard'))

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>{{ __('adminlte.dashboard') }}</h1>
@stop

@section('content')
    <!-- Dashboard header -->
    <h1>{{ __('adminlte.welcome_dashboard') }}</h1>

    <!-- Admin-specific widgets and content -->
    @if(auth()->user()->isAdmin())

        <!-- Total Cost and Profit Widgets -->
        <div class="row mt-4">
            <!-- Last 24 Hours -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($totals['24h']['cost'], 2) }}</h3>
                        <p>{{ __('Total Cost (24h)') }}</p>
                        <h4>{{ number_format($totals['24h']['profit'], 2) }}</h4>
                        <p>{{ __('Total Profit (24h)') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <!-- Last 7 Days -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($totals['7d']['cost'], 2) }}</h3>
                        <p>{{ __('Total Cost (7d)') }}</p>
                        <h4>{{ number_format($totals['7d']['profit'], 2) }}</h4>
                        <p>{{ __('Total Profit (7d)') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                </div>
            </div>

            <!-- Last 30 Days -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($totals['30d']['cost'], 2) }}</h3>
                        <p>{{ __('Total Cost (30d)') }}</p>
                        <h4>{{ number_format($totals['30d']['profit'], 2) }}</h4>
                        <p>{{ __('Total Profit (30d)') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>

            <!-- Lifetime -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ number_format($totals['lifetime']['cost'], 2) }}</h3>
                        <p>{{ __('Total Cost (Lifetime)') }}</p>
                        <h4>{{ number_format($totals['lifetime']['profit'], 2) }}</h4>
                        <p>{{ __('Total Profit (Lifetime)') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-infinity"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other Admin Widgets -->
        <div class="row mt-4">
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
        </div>

    @else
        <!-- User-specific widgets -->
        <div class="row mt-4">
            <!-- Unique Orders by Status Widgets -->

            <!-- Referrals widget -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $verifiedActiveReferrals->count() ?? 0 }}</h3>
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

            <!-- Support Tickets widget -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ auth()->user()->supportTickets()->count() }}</h3>
                        <p>{{ __('Support Tickets') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <a href="{{ route('support.index') }}" class="small-box-footer">{{ __('View Tickets') }} <i class="fas fa-arrow-circle-right"></i></a>
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

            @foreach($ordersByStatus as $status => $count)
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-{{ $statusColors[$status] ?? 'danger' }}">
                        <div class="inner">
                            <h3>{{ $count }}</h3>
                            <p>{{ ucfirst($status) }} {{ __('Orders') }}</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
