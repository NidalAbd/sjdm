@extends('layouts.app')

@section('title', __('adminlte.dashboard'))

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>{{ __('adminlte.dashboard') }}</h1>
@stop

@section('content')
    <style>
        .waiting-orders-alert {
            animation: pulse 2s infinite;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
            50% { box-shadow: 0 4px 25px rgba(255, 193, 7, 0.3); }
            100% { box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        }
        
        .alert-heading {
            font-size: 1.25rem;
        }
        
        .api-balance-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
        }
    </style>

    <!-- Dashboard header -->
    <h1>{{ __('adminlte.welcome_dashboard') }}</h1>

    <!-- Admin-specific widgets and content -->
    @if(auth()->user()->isAdmin())
        
        <!-- Waiting Orders Alert Section -->
        @php
            $waitingOrdersAlert = checkWaitingOrdersAlert();
        @endphp
        
        @if($waitingOrdersAlert)
            <div class="alert alert-{{ $waitingOrdersAlert['type'] }} border-0 shadow-sm mb-4 waiting-orders-alert" role="alert" style="background: linear-gradient(135deg, {{ $waitingOrdersAlert['type'] === 'warning' ? '#fff3cd' : '#f8d7da' }} 0%, {{ $waitingOrdersAlert['type'] === 'warning' ? '#ffeaa7' : '#f5c6cb' }} 100%); border-left: 4px solid {{ $waitingOrdersAlert['type'] === 'warning' ? '#ffc107' : '#dc3545' }} !important;">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <i class="{{ $waitingOrdersAlert['icon'] }} fa-2x text-{{ $waitingOrdersAlert['type'] === 'warning' ? 'warning' : 'danger' }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="alert-heading mb-0 fw-bold text-{{ $waitingOrdersAlert['type'] === 'warning' ? 'warning' : 'danger' }}">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ $waitingOrdersAlert['title'] }}
                            </h5>
                            <span class="badge bg-{{ $waitingOrdersAlert['type'] === 'warning' ? 'warning' : 'danger' }} fs-6">
                                {{ $waitingOrdersAlert['waiting_orders_count'] }} {{ __('adminlte.waiting_orders') }}
                            </span>
                        </div>
                        
                        <p class="mb-3 text-dark">{{ $waitingOrdersAlert['message'] }}</p>
                        
                        @if(isset($waitingOrdersAlert['api_balance']))
                            <div class="mb-3 p-3 api-balance-box rounded">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-dollar-sign text-success me-2"></i>
                                    <strong class="me-2">{{ __('adminlte.current_api_balance') }}:</strong>
                                    <span class="fs-5 fw-bold text-{{ $waitingOrdersAlert['api_balance'] > 0 ? 'success' : 'danger' }}">
                                        ${{ number_format($waitingOrdersAlert['api_balance'], 2) }}
                                    </span>
                                </div>
                            </div>
                        @endif
                        
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('orders.index', ['status' => 'waiting']) }}" 
                               class="btn btn-{{ $waitingOrdersAlert['type'] === 'warning' ? 'warning' : 'danger' }} btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                {{ __('adminlte.view_waiting_orders') }}
                            </a>
                            <a href="{{ route('transactions.create') }}" 
                               class="btn btn-success btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                {{ __('adminlte.add_api_balance') }}
                            </a>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshPage()">
                                <i class="fas fa-sync-alt me-1"></i>
                                {{ __('Refresh') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Total Cost/Profit Widgets Section -->
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

        <!-- Admin Metrics Widgets Section -->
        <div class="row mt-4">
            <!-- User Count -->
            <x-adminlte-widget color="info" title="{{ __('adminlte.users') }}" count="{{ $userCount }}" icon="fas fa-users" link="{{ route('users.index') }}" />

            <!-- Total User Balance -->
            <x-adminlte-widget color="info" title="{{ __('Total User Balance') }}" count="{{ number_format($totalUserBalance, 2) }}" icon="fas fa-dollar-sign" link="{{ route('users.index') }}" />
        </div>

        <!-- User Verification Metrics Section -->
        <div class="row mt-4">
            <!-- Verified Users -->
            <x-adminlte-widget color="success" title="{{ __('adminlte.verified_users') }}" count="{{ $verifiedUsersCount }}" icon="fas fa-user-check" link="{{ route('users.index') }}" />

            <!-- Non-Verified Users -->
            <x-adminlte-widget color="danger" title="{{ __('adminlte.non_verified_users') }}" count="{{ $nonVerifiedUsersCount }}" icon="fas fa-user-times" link="{{ route('users.index') }}" />
        </div>

        <!-- Transaction Metrics Section -->
        <div class="row mt-4">
            <!-- Completed Transactions -->
            <x-adminlte-widget color="success" title="{{ __('adminlte.completed_transactions') }}" count="{{ $completedTransactionsCount }}" icon="fas fa-check-circle" link="{{ route('transactions.index') }}" />

            <!-- Completed Transactions (Last 24 Hours) -->
            <x-adminlte-widget color="success" title="{{ __('adminlte.completed_transactions_24hour') }}" count="{{ $newCreditTransactionsCount }}" icon="fas fa-check-circle" link="{{ route('transactions.index') }}" />

            <!-- Canceled Transactions -->
            <x-adminlte-widget color="danger" title="{{ __('adminlte.canceled_transactions') }}" count="{{ $canceledTransactionsCount }}" icon="fas fa-times-circle" link="{{ route('transactions.index') }}" />
        </div>

        <!-- Services and Orders Metrics Section -->
        <div class="row mt-4">
            <!-- Services Count -->
            <x-adminlte-widget color="success" title="{{ __('adminlte.services') }}" count="{{ $serviceCount }}" icon="fas fa-cogs" link="{{ route('services.index') }}" />

            <!-- Orders Count -->
            <x-adminlte-widget color="warning" title="{{ __('adminlte.orders') }}" count="{{ $orderCount }}" icon="fas fa-shopping-cart" link="{{ route('orders.index') }}" />
            
            <!-- Waiting Orders Count -->
            <x-adminlte-widget color="danger" title="{{ __('adminlte.waiting_orders') }}" count="{{ \App\Models\Order::where('status', 'waiting')->count() }}" icon="fas fa-hourglass-half" link="{{ route('orders.index', ['status' => 'waiting']) }}" />
        </div>

        <!-- Pricing Section -->
        <div class="row mt-4">
            <!-- Starting Price -->
            <x-adminlte-widget color="danger" title="{{ __('adminlte.prices_start') }}" count="{{ $startingPrice }}" icon="fas fa-dollar-sign" link="{{ route('services.index') }}" />
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

<script>
    function refreshPage() {
        location.reload();
    }
</script>
