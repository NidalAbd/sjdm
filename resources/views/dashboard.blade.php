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
            border-radius: 0 !important;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3) !important;
            color: white !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 9999 !important;
            padding: 15px 20px !important;
            margin: 0 !important;
            min-height: auto !important;
            height: auto !important;
        }
        
        .waiting-orders-alert .alert-content {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            max-width: 1400px !important;
            margin: 0 auto !important;
            gap: 20px !important;
        }
        
        .waiting-orders-alert .left-section {
            display: flex !important;
            align-items: center !important;
            gap: 15px !important;
            flex: 1 !important;
        }
        
        .waiting-orders-alert .icon-container {
            background: rgba(255,255,255,0.2) !important;
            border-radius: 50% !important;
            width: 45px !important;
            height: 45px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            backdrop-filter: blur(10px) !important;
            flex-shrink: 0 !important;
        }
        
        .waiting-orders-alert .icon-container i {
            font-size: 1.2rem !important;
            color: white !important;
        }
        
        .waiting-orders-alert .text-content {
            display: flex !important;
            flex-direction: column !important;
            gap: 2px !important;
        }
        
        .waiting-orders-alert .alert-title {
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            color: white !important;
            margin: 0 !important;
            line-height: 1.2 !important;
        }
        
        .waiting-orders-alert .alert-description {
            font-size: 0.9rem !important;
            color: rgba(255,255,255,0.9) !important;
            margin: 0 !important;
            line-height: 1.3 !important;
        }
        
        .waiting-orders-alert .right-section {
            display: flex !important;
            align-items: center !important;
            gap: 15px !important;
            flex-shrink: 0 !important;
        }
        
        .waiting-orders-alert .api-balance {
            background: rgba(255,255,255,0.15) !important;
            border: 1px solid rgba(255,255,255,0.2) !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            backdrop-filter: blur(10px) !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }
        
        .waiting-orders-alert .api-balance i {
            color: white !important;
            font-size: 0.9rem !important;
        }
        
        .waiting-orders-alert .api-balance-text {
            font-size: 0.85rem !important;
            color: white !important;
            font-weight: 600 !important;
        }
        
        .waiting-orders-alert .btn {
            border-radius: 20px !important;
            font-weight: 600 !important;
            padding: 8px 16px !important;
            font-size: 0.85rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            transition: all 0.3s ease !important;
            border: 2px solid transparent !important;
            white-space: nowrap !important;
        }
        
        .waiting-orders-alert .btn:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
        }
        
        .waiting-orders-alert .btn-success {
            background: linear-gradient(135deg, #00b894, #00a085) !important;
            border-color: #00b894 !important;
            color: white !important;
        }
        
        .waiting-orders-alert .btn-warning {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24) !important;
            border-color: #ff6b6b !important;
            color: white !important;
        }
        
        .waiting-orders-alert .btn-outline-secondary {
            background: rgba(255,255,255,0.1) !important;
            border-color: rgba(255,255,255,0.3) !important;
            color: white !important;
        }
        
        .waiting-orders-alert .btn-outline-secondary:hover {
            background: rgba(255,255,255,0.2) !important;
            border-color: rgba(255,255,255,0.5) !important;
        }
        
        .waiting-orders-alert .status-indicator {
            position: absolute !important;
            top: 10px !important;
            right: 15px !important;
            width: 8px !important;
            height: 8px !important;
            background: #ff4757 !important;
            border-radius: 50% !important;
            animation: pulse-dot 2s infinite !important;
        }
        
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }
        
        /* Add top margin to body when alert is present */
        body.has-waiting-alert {
            padding-top: 80px !important;
        }
        
        /* Ensure alert stays visible */
        .waiting-orders-alert {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        
        /* Hide any close buttons */
        .waiting-orders-alert .btn-close,
        .waiting-orders-alert [data-bs-dismiss="alert"] {
            display: none !important;
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
            <div class="alert waiting-orders-alert" role="alert">
                <div class="status-indicator"></div>
                <div class="alert-content">
                    <div class="left-section">
                        <div class="icon-container">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="text-content">
                            <h6 class="alert-title">{{ $waitingOrdersAlert['title'] }}</h6>
                            <p class="alert-description">{{ $waitingOrdersAlert['message'] }}</p>
                        </div>
                    </div>
                    <div class="right-section">
                        @if(isset($waitingOrdersAlert['api_balance']))
                            <div class="api-balance">
                                <i class="fas fa-dollar-sign"></i>
                                <span class="api-balance-text">${{ number_format($waitingOrdersAlert['api_balance'], 2) }}</span>
                            </div>
                        @endif
                        <a href="{{ route('orders.index', ['status' => 'waiting']) }}" class="btn btn-warning">
                            <i class="fas fa-eye me-1"></i>View Orders
                        </a>
                        <a href="{{ route('transactions.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i>Add Balance
                        </a>
                        <button type="button" class="btn btn-outline-secondary" onclick="refreshPage()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
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
            // Add body padding to prevent content overlap
            document.body.classList.add('has-waiting-alert');
            
            // Remove any dismissible functionality
            const closeButtons = waitingAlert.querySelectorAll('.btn-close, [data-bs-dismiss="alert"]');
            closeButtons.forEach(btn => btn.remove());
            
            // Ensure the alert stays visible
            waitingAlert.style.display = 'block';
            waitingAlert.style.visibility = 'visible';
            waitingAlert.style.opacity = '1';
            waitingAlert.style.position = 'fixed';
            waitingAlert.style.top = '0';
            waitingAlert.style.left = '0';
            waitingAlert.style.right = '0';
            waitingAlert.style.zIndex = '9999';
            
            // Prevent any fade out animations
            waitingAlert.classList.remove('fade', 'show');
            waitingAlert.classList.add('permanent-alert');
            
            // Force the alert to stay on top
            setInterval(function() {
                if (waitingAlert.style.display === 'none' || waitingAlert.style.visibility === 'hidden') {
                    waitingAlert.style.display = 'block';
                    waitingAlert.style.visibility = 'visible';
                    waitingAlert.style.opacity = '1';
                }
            }, 1000);
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
