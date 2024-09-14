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

        <div class="row mt-4">
            @php
                $timePeriods = [
                    ['period' => '24h', 'color' => 'info', 'icon' => 'fas fa-clock'],
                    ['period' => '7d', 'color' => 'success', 'icon' => 'fas fa-calendar-week'],
                    ['period' => '30d', 'color' => 'warning', 'icon' => 'fas fa-calendar-alt'],
                    ['period' => 'lifetime', 'color' => 'danger', 'icon' => 'fas fa-infinity'],
                ];
            @endphp
                <!-- Loop through total cost/profit data -->
            @foreach($timePeriods as $period)
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-{{ $period['color'] }}">
                        <div class="inner">
                            <h3>{{ number_format($totals[$period['period']]['cost'], 2) }}</h3>
                            <p>{{ __('Total Cost (' . $period['period'] . ')') }}</p>
                            <h4>{{ number_format($totals[$period['period']]['profit'], 2) }}</h4>
                            <p>{{ __('Total Profit (' . $period['period'] . ')') }}</p>
                        </div>
                        <div class="icon">
                            <i class="{{ $period['icon'] }}"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Admin-specific metrics -->
        <div class="row mt-4">
            <!-- User Count -->
            <x-adminlte-widget color="info" title="{{ __('adminlte.users') }}" count="{{ $userCount }}" icon="fas fa-users" link="{{ route('users.index') }}" />

            <!-- Total User Balance -->
            <x-adminlte-widget color="info" title="{{ __('Total User Balance') }}" count="{{ number_format($totalUserBalance, 2) }}" icon="fas fa-dollar-sign" link="{{ route('users.index') }}" />

            <!-- Verified Users -->
            <x-adminlte-widget color="success" title="{{ __('adminlte.verified_users') }}" count="{{ $verifiedUsersCount }}" icon="fas fa-user-check" link="{{ route('users.index') }}" />

            <!-- Non-Verified Users -->
            <x-adminlte-widget color="danger" title="{{ __('adminlte.non_verified_users') }}" count="{{ $nonVerifiedUsersCount }}" icon="fas fa-user-times" link="{{ route('users.index') }}" />

            <!-- Completed Transactions -->
            <x-adminlte-widget color="success" title="{{ __('adminlte.completed_transactions') }}" count="{{ $completedTransactionsCount }}" icon="fas fa-check-circle" link="{{ route('transactions.index') }}" />
            <x-adminlte-widget color="success" title="{{ __('adminlte.completed_transactions_24hour') }}" count="{{ $newCreditTransactionsCount }}" icon="fas fa-check-circle" link="{{ route('transactions.index') }}" />

            <!-- Canceled Transactions -->
            <x-adminlte-widget color="danger" title="{{ __('adminlte.canceled_transactions') }}" count="{{ $canceledTransactionsCount }}" icon="fas fa-times-circle" link="{{ route('transactions.index') }}" />

            <!-- Services Count -->
            <x-adminlte-widget color="success" title="{{ __('adminlte.services') }}" count="{{ $serviceCount }}" icon="fas fa-cogs" link="{{ route('services.index') }}" />

            <!-- Orders Count -->
            <x-adminlte-widget color="warning" title="{{ __('adminlte.orders') }}" count="{{ $orderCount }}" icon="fas fa-shopping-cart" link="{{ route('orders.index') }}" />

            <!-- Starting Price -->
            <x-adminlte-widget color="danger" title="{{ __('adminlte.prices_start') }}" count="{{ $startingPrice }}" icon="fas fa-dollar-sign" />
        </div>

    @else
        <!-- User-specific widgets -->
        <div class="row mt-4">
            <!-- Referrals -->
            <x-adminlte-widget color="info" title="{{ __('adminlte.referrals') }}" count="{{ $verifiedActiveReferrals->count() }}" icon="fas fa-user-friends" link="{{ route('profile.settings') }}" />

            <!-- Orders -->
            <x-adminlte-widget color="warning" title="{{ __('adminlte.orders') }}" count="{{ auth()->user()->orders()->count() }}" icon="fas fa-shopping-cart" link="{{ route('orders.index') }}" />

            <!-- Support Tickets -->
            <x-adminlte-widget color="primary" title="{{ __('Support Tickets') }}" count="{{ auth()->user()->supportTickets()->count() }}" icon="fas fa-ticket-alt" link="{{ route('support.index') }}" />

            <!-- Transactions -->
            <x-adminlte-widget color="primary" title="{{ __('adminlte.transactions') }}" count="{{ auth()->user()->transactions()->count() }}" icon="fas fa-exchange-alt" link="{{ route('transactions.index') }}" />

            <!-- Loop through order statuses and create widgets -->
            @foreach($ordersByStatus as $status => $count)
                <x-adminlte-widget color="{{ $statusColors[$status] ?? 'danger' }}" title="{{ ucfirst($status) }} {{ __('Orders') }}" count="{{ $count }}" icon="fas fa-shopping-cart" />
            @endforeach
        </div>
    @endif
@endsection
