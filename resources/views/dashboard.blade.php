@extends('layouts.app')

@section('title', __('adminlte.dashboard'))

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>{{ __('adminlte.dashboard') }}</h1>
@stop

@section('content')
    <style>
        .waiting-orders-alert {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            border-radius: 15px !important;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3) !important;
            color: white !important;
            position: relative;
            overflow: hidden;
        }
        
        .waiting-orders-alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .waiting-orders-alert .alert-heading {
            font-size: 1.4rem;
            font-weight: 700;
            color: white !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .waiting-orders-alert .badge {
            background: rgba(255,255,255,0.2) !important;
            color: white !important;
            border: 1px solid rgba(255,255,255,0.3);
            font-size: 0.9rem;
            padding: 8px 12px;
            border-radius: 20px;
        }
        
        .waiting-orders-alert p {
            color: rgba(255,255,255,0.9) !important;
            font-size: 1rem;
            line-height: 1.6;
        }
        
        .api-balance-box {
            background: rgba(255,255,255,0.15) !important;
            border: 1px solid rgba(255,255,255,0.2) !important;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        
        .waiting-orders-alert .btn {
            border-radius: 25px;
            font-weight: 600;
            padding: 10px 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .waiting-orders-alert .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .waiting-orders-alert .btn-warning {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border-color: #ff6b6b;
            color: white;
        }
        
        .waiting-orders-alert .btn-success {
            background: linear-gradient(135deg, #00b894, #00a085);
            border-color: #00b894;
            color: white;
        }
        
        .waiting-orders-alert .btn-outline-secondary {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.3);
            color: white;
        }
        
        .waiting-orders-alert .btn-outline-secondary:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
        }
        
        .waiting-orders-alert .icon-container {
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }
        
        .waiting-orders-alert .icon-container i {
            font-size: 1.8rem;
            color: white;
        }
        
        .waiting-orders-alert .content-wrapper {
            position: relative;
            z-index: 2;
        }
        
        .waiting-orders-alert .status-indicator {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 12px;
            height: 12px;
            background: #ff4757;
            border-radius: 50%;
            animation: pulse-dot 2s infinite;
        }
        
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
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
            <div class="alert waiting-orders-alert mb-4" role="alert">
                <div class="status-indicator"></div>
                <div class="content-wrapper">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-4">
                            <div class="icon-container">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="alert-heading mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ $waitingOrdersAlert['title'] }}
                                </h5>
                                <span class="badge">
                                    {{ $waitingOrdersAlert['waiting_orders_count'] }} {{ __('adminlte.waiting_orders') }}
                                </span>
                            </div>
                            
                            <p class="mb-4">{{ $waitingOrdersAlert['message'] }}</p>
                            
                            @if(isset($waitingOrdersAlert['api_balance']))
                                <div class="mb-4 p-3 api-balance-box">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-dollar-sign text-white me-3"></i>
                                        <div>
                                            <strong class="d-block text-white">{{ __('adminlte.current_api_balance') }}</strong>
                                            <span class="fs-4 fw-bold text-{{ $waitingOrdersAlert['api_balance'] > 0 ? 'success' : 'danger' }}">
                                                ${{ number_format($waitingOrdersAlert['api_balance'], 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="{{ route('orders.index', ['status' => 'waiting']) }}" 
                                   class="btn btn-warning">
                                    <i class="fas fa-eye me-2"></i>
                                    {{ __('adminlte.view_waiting_orders') }}
                                </a>
                                <a href="{{ route('transactions.create') }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>
                                    {{ __('adminlte.add_api_balance') }}
                                </a>
                                <button type="button" class="btn btn-outline-secondary" onclick="refreshPage()">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    {{ __('Refresh') }}
                                </button>
                            </div>
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
    
    // Prevent any automatic dismissal of the waiting orders alert
    document.addEventListener('DOMContentLoaded', function() {
        const waitingAlert = document.querySelector('.waiting-orders-alert');
        if (waitingAlert) {
            // Remove any dismissible functionality
            const closeButtons = waitingAlert.querySelectorAll('.btn-close, [data-bs-dismiss="alert"]');
            closeButtons.forEach(btn => btn.remove());
            
            // Ensure the alert stays visible
            waitingAlert.style.display = 'block';
            waitingAlert.style.visibility = 'visible';
            waitingAlert.style.opacity = '1';
            
            // Prevent any fade out animations
            waitingAlert.classList.remove('fade', 'show');
            waitingAlert.classList.add('permanent-alert');
        }
    });
    
    // Auto-refresh the page every 30 seconds to check for updates
    setInterval(function() {
        // Only refresh if there are waiting orders
        const waitingAlert = document.querySelector('.waiting-orders-alert');
        if (waitingAlert) {
            // Check if we should refresh (optional - uncomment if needed)
            // location.reload();
        }
    }, 30000);
</script>
