@extends('layouts.app')

@section('title', __('adminlte.manage_orders'))

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content_header')
    @include('partials.breadcrumbs')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-primary mb-0">
            <i class="fas fa-shopping-cart me-2"></i>{{ __('adminlte.manage_orders') }}
        </h1>
        <div class="d-flex gap-2 align-items-center">
            <button class="btn btn-outline-primary btn-sm" onclick="exportOrders()">
                <i class="fas fa-download me-1"></i>{{ __('Export') }}
            </button>
            <div class="btn-group" role="group">
                <button type="button" id="tableBtn" class="btn btn-outline-secondary btn-sm active" onclick="toggleViewMode('table')">
                    <i class="fas fa-table me-1"></i>{{ __('Table') }}
                </button>
                <button type="button" id="cardBtn" class="btn btn-outline-secondary btn-sm" onclick="toggleViewMode('card')">
                    <i class="fas fa-th-large me-1"></i>{{ __('Cards') }}
                </button>
            </div>
            <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>{{ __('adminlte.create_order') }}
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <!-- Order Statistics Row -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('Completed') }}</span>
                            <span class="info-box-number">{{ $orders->where('status', 'completed')->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('Pending') }}</span>
                            <span class="info-box-number">{{ $orders->where('status', 'pending')->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-hourglass-half"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('Waiting') }}</span>
                            <span class="info-box-number">{{ $orders->where('status', 'waiting')->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('Total Value') }}</span>
                            <span class="info-box-number">${{ number_format($orders->sum('charge'), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Waiting Orders Alert Section for Admins -->
            @if(auth()->user()->hasRole('admin'))
                @php
                    $waitingOrdersAlert = checkWaitingOrdersAlert();
                @endphp
                
                @if($waitingOrdersAlert)
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="alert alert-warning alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h5><i class="icon fas fa-exclamation-triangle"></i> {{ __('Orders Waiting for API Processing') }}</h5>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-1">{{ __('There are orders waiting to be processed by the API.') }}</p>
                                        @if(isset($waitingOrdersAlert['api_balance']))
                                            <p class="mb-0"><strong>{{ __('API Balance') }}: ${{ number_format($waitingOrdersAlert['api_balance'], 2) }}</strong></p>
                                        @endif
                                    </div>
                                    <a href="{{ route('transactions.create') }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus me-1"></i>{{ __('Add Balance') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Main Content Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list me-2"></i>{{ __('Order List') }}
                    </h3>
                </div>

                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form id="filterForm" action="{{ route('orders.index') }}" method="GET" class="form-inline">
                                <div class="input-group mr-2 mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    </div>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="{{ __('adminlte.search_orders') }}"
                                           value="{{ request()->get('search') }}">
                                </div>
                                
                                <div class="input-group mr-2 mb-2">
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="all">{{ __('adminlte.select_status') }}</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ request()->get('status') == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="input-group mr-2 mb-2">
                                    <select name="platform" class="form-control" onchange="this.form.submit()">
                                        <option value="all">{{ __('adminlte.select_platform') }}</option>
                                        @foreach($platforms as $platform)
                                            <option value="{{ $platform }}" {{ request()->get('platform') == $platform ? 'selected' : '' }}>
                                                {{ __('adminlte.' . strtolower($platform)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-primary mb-2 mr-2">
                                    <i class="fas fa-filter me-1"></i>{{ __('adminlte.search') }}
                                </button>
                                
                                <button type="button" class="btn btn-secondary mb-2" onclick="clearFilters()">
                                    <i class="fas fa-times me-1"></i>{{ __('Clear') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Table View -->
                    <div id="tableView">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="d-flex align-items-center">
                                                <input type="checkbox" id="selectAll" class="form-check-input mr-2">
                                                <span>#</span>
                                            </div>
                                        </th>
                                        <th>{{ __('adminlte.name') }}</th>
                                        <th>{{ __('adminlte.service_name') }}</th>
                                        <th>{{ __('adminlte.link') }}</th>
                                        <th>{{ __('adminlte.quantity') }}</th>
                                        <th>{{ __('adminlte.charge') }}</th>
                                        <th>{{ __('adminlte.start_count') }}</th>
                                        <th>{{ __('adminlte.remains') }}</th>
                                        <th>{{ __('adminlte.status') }}</th>
                                        <th class="text-center">{{ __('adminlte.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                        @php
                                            $hasSupportTicket = $order->supportTicket;
                                            $hasUnreadMessages = $hasSupportTicket && $hasSupportTicket->messages()
                                                ->whereNull('read_at')
                                                ->where('user_id', '!=', Auth::id())
                                                ->count() > 0;
                                            $unreadCount = $hasSupportTicket ? $hasSupportTicket->messages()
                                                ->whereNull('read_at')
                                                ->where('user_id', '!=', Auth::id())
                                                ->count() : 0;
                                        @endphp
                                        <tr class="order-row" data-order-id="{{ $order->id }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input order-checkbox mr-2"
                                                           value="{{ $order->id }}">
                                                    <span class="order-id">{{ $order->id }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <div>
                                                        @if($order->user)
                                                            <div class="font-weight-bold">{{ $order->user->name }}</div>
                                                            <small class="text-muted">{{ $order->user->email }}</small>
                                                        @else
                                                            <div class="font-weight-bold text-muted">{{ __('adminlte.deleted_user') }}</div>
                                                            <small class="text-muted">User ID: {{ $order->user_id }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($order->service)
                                                    <div class="font-weight-bold">
                                                        @if(app()->getLocale() === 'ar')
                                                            {{ Str::limit($order->service->name_ar, 30) }}
                                                        @else
                                                            {{ Str::limit($order->service->name_en, 30) }}
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">ID: {{ $order->service->service_id }}</small>
                                                @else
                                                    <div class="text-muted">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        {{ __('adminlte.deleted_service') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ $order->link }}" target="_blank" class="text-truncate d-inline-block"
                                                   style="max-width: 150px;" title="{{ $order->link }}">
                                                    {{ $order->link }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ number_format($order->quantity) }}</span>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold text-success">${{ number_format($order->charge, 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ number_format($order->start_count) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-warning">{{ number_format($order->remains) }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusConfig = [
                                                        'pending' => ['class' => 'badge-warning', 'icon' => 'fas fa-clock'],
                                                        'processing' => ['class' => 'badge-info', 'icon' => 'fas fa-cog fa-spin'],
                                                        'completed' => ['class' => 'badge-success', 'icon' => 'fas fa-check-circle'],
                                                        'cancelled' => ['class' => 'badge-danger', 'icon' => 'fas fa-times-circle'],
                                                        'refunded' => ['class' => 'badge-secondary', 'icon' => 'fas fa-undo'],
                                                        'partial' => ['class' => 'badge-warning', 'icon' => 'fas fa-exclamation-triangle'],
                                                        'waiting' => ['class' => 'badge-primary', 'icon' => 'fas fa-hourglass-half']
                                                    ];
                                                    $status = $statusConfig[$order->status] ?? ['class' => 'badge-secondary', 'icon' => 'fas fa-question-circle'];
                                                @endphp
                                                <span class="badge {{ $status['class'] }}">
                                                    <i class="{{ $status['icon'] }} me-1"></i>
                                                    {{ __('adminlte.' . strtolower($order->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @can('view_order', $order)
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                                data-toggle="modal" data-target="#viewOrderModal{{ $order->id }}"
                                                                title="{{ __('adminlte.view_order') }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    @endcan

                                                    @if($order->can_refill)
                                                        <button type="button" class="btn btn-sm btn-outline-info"
                                                                onclick="checkAndRefill({{ $order->id }})"
                                                                title="{{ __('adminlte.refill') }}">
                                                            <i class="fas fa-sync"></i>
                                                        </button>
                                                    @endif

                                                    @if($order->can_cancel)
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                onclick="checkAndCancel({{ $order->id }})"
                                                                title="{{ __('adminlte.cancel') }}">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    @endif

                                                    @if($order->status === 'partial' && $order->charge > 0)
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                                onclick="processPartialRefund({{ $order->id }})"
                                                                title="{{ __('Process Refund for Partial Order') }}">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    @endif

                                                    @if(!$order->supportTicket)
                                                        @can('create_ticket')
                                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                                    data-toggle="modal" data-target="#createTicketModal{{ $order->id }}"
                                                                    title="{{ __('adminlte.create_support_ticket') }}">
                                                                <i class="fas fa-headset"></i>
                                                            </button>
                                                        @endcan
                                                    @else
                                                        <a href="{{ route('support.show', $order->supportTicket->id) }}"
                                                           class="btn btn-sm btn-outline-info"
                                                           title="{{ __('adminlte.view_ticket') }}">
                                                            <i class="fas fa-ticket-alt"></i>
                                                            @if($hasUnreadMessages)
                                                                <span class="badge badge-danger badge-sm ml-1">{{ $unreadCount }}</span>
                                                            @endif
                                                        </a>
                                                    @endif

                                                    @can('delete_order', $order)
                                                        <button class="btn btn-sm btn-outline-danger" type="button"
                                                                onclick="deleteOrder({{ $order->id }})"
                                                                title="{{ __('adminlte.delete_order') }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <div class="text-center">
                                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">{{ __('adminlte.no_orders_found') }}</h5>
                                                    <p class="text-muted">{{ __('Try adjusting your search criteria') }}</p>
                                                    <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                                        <i class="fas fa-plus me-1"></i>{{ __('adminlte.create_order') }}
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Card View (Hidden by default) -->
                    <div id="cardView" style="display: none;">
                        <div class="row">
                            @foreach($orders as $order)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <span class="badge badge-primary">#{{ $order->id }}</span>
                                                @php
                                                    $statusConfig = [
                                                        'pending' => ['class' => 'badge-warning', 'icon' => 'fas fa-clock'],
                                                        'processing' => ['class' => 'badge-info', 'icon' => 'fas fa-cog fa-spin'],
                                                        'completed' => ['class' => 'badge-success', 'icon' => 'fas fa-check-circle'],
                                                        'cancelled' => ['class' => 'badge-danger', 'icon' => 'fas fa-times-circle'],
                                                        'refunded' => ['class' => 'badge-secondary', 'icon' => 'fas fa-undo'],
                                                        'partial' => ['class' => 'badge-warning', 'icon' => 'fas fa-exclamation-triangle'],
                                                        'waiting' => ['class' => 'badge-primary', 'icon' => 'fas fa-hourglass-half']
                                                    ];
                                                    $status = $statusConfig[$order->status] ?? ['class' => 'badge-secondary', 'icon' => 'fas fa-question-circle'];
                                                @endphp
                                                <span class="badge {{ $status['class'] }} float-right">
                                                    <i class="{{ $status['icon'] }} me-1"></i>
                                                    {{ __('adminlte.' . strtolower($order->status)) }}
                                                </span>
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <strong>{{ __('adminlte.name') }}:</strong>
                                                @if($order->user)
                                                    {{ $order->user->name }}
                                                @else
                                                    <span class="text-muted">{{ __('adminlte.deleted_user') }}</span>
                                                @endif
                                            </div>
                                            
                                            <div class="mb-3">
                                                <strong>{{ __('adminlte.service_name') }}:</strong>
                                                @if($order->service)
                                                    @if(app()->getLocale() === 'ar')
                                                        {{ Str::limit($order->service->name_ar, 25) }}
                                                    @else
                                                        {{ Str::limit($order->service->name_en, 25) }}
                                                    @endif
                                                @else
                                                    <span class="text-muted">{{ __('adminlte.deleted_service') }}</span>
                                                @endif
                                            </div>
                                            
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <strong>{{ __('adminlte.quantity') }}:</strong>
                                                    <span class="badge badge-info">{{ number_format($order->quantity) }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <strong>{{ __('adminlte.charge') }}:</strong>
                                                    <span class="font-weight-bold text-success">${{ number_format($order->charge, 2) }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <strong>{{ __('adminlte.start_count') }}:</strong>
                                                    <span class="badge badge-secondary">{{ number_format($order->start_count) }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <strong>{{ __('adminlte.remains') }}:</strong>
                                                    <span class="badge badge-warning">{{ number_format($order->remains) }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <strong>{{ __('adminlte.link') }}:</strong>
                                                <a href="{{ $order->link }}" target="_blank" class="text-truncate d-block">
                                                    {{ Str::limit($order->link, 30) }}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="btn-group w-100" role="group">
                                                @can('view_order', $order)
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                            data-toggle="modal" data-target="#viewOrderModal{{ $order->id }}"
                                                            title="{{ __('adminlte.view_order') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endcan

                                                @if($order->can_refill)
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                            onclick="checkAndRefill({{ $order->id }})"
                                                            title="{{ __('adminlte.refill') }}">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                @endif

                                                @if($order->can_cancel)
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="checkAndCancel({{ $order->id }})"
                                                            title="{{ __('adminlte.cancel') }}">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                @endif

                                                @if($order->status === 'partial' && $order->charge > 0)
                                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                                            onclick="processPartialRefund({{ $order->id }})"
                                                            title="{{ __('Process Refund for Partial Order') }}">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                @endif

                                                @if(!$order->supportTicket)
                                                    @can('create_ticket')
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                                data-toggle="modal" data-target="#createTicketModal{{ $order->id }}"
                                                                title="{{ __('adminlte.create_support_ticket') }}">
                                                            <i class="fas fa-headset"></i>
                                                        </button>
                                                    @endcan
                                                @else
                                                    <a href="{{ route('support.show', $order->supportTicket->id) }}"
                                                       class="btn btn-sm btn-outline-info"
                                                       title="{{ __('adminlte.view_ticket') }}">
                                                        <i class="fas fa-ticket-alt"></i>
                                                        @if($hasUnreadMessages)
                                                            <span class="badge badge-danger badge-sm ml-1">{{ $unreadCount }}</span>
                                                        @endif
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                {{ __('Showing') }} {{ $orders->firstItem() ?? 0 }} {{ __('to') }} {{ $orders->lastItem() ?? 0 }} {{ __('of') }} {{ $orders->total() }} {{ __('results') }}
                            </small>
                        </div>
                        <div>
                            {{ $orders->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Modal -->
    <div class="modal fade" id="bulkActionsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Bulk Actions') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Selected orders:') }} <span id="selectedCount">0</span></p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-danger" onclick="bulkDelete()">
                            <i class="fas fa-trash me-1"></i>{{ __('Delete Selected') }}
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="bulkExport()">
                            <i class="fas fa-download me-1"></i>{{ __('Export Selected') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include existing modals -->
    @foreach($orders as $order)
        @include('orders.partials.view_modal', ['order' => $order])
        @include('orders.partials.create_ticket_modal', ['order' => $order])
    @endforeach
@stop

@section('js')
<script>
    function refreshPage() {
        location.reload();
    }

    // Delete order function
    function deleteOrder(orderId) {
        if (confirm('{{ __("Are you sure you want to delete this order? This action will refund the user and cannot be undone.") }}')) {
            // Show loading state
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Processing...") }}';
            button.disabled = true;

            fetch(`/orders/${orderId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    // Show success message and reload
                    showAlert('success', '{{ __("Order deleted successfully and refund processed!") }}');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    throw new Error('{{ __("Failed to delete order") }}');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', '{{ __("Error deleting order. Please try again.") }}');
                // Restore button
                button.innerHTML = originalContent;
                button.disabled = false;
            });
        }
    }

    // Check and refill order
    function checkAndRefill(orderId) {
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Checking...") }}';
        button.disabled = true;

        fetch(`/orders/${orderId}/check-refill`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.can_refill) {
                if (confirm('{{ __("This order can be refilled. Do you want to proceed?") }}')) {
                    processRefill(orderId, button, originalContent);
                } else {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }
            } else {
                showAlert('warning', data.message || '{{ __("This order cannot be refilled.") }}');
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', '{{ __("Error checking refill status.") }}');
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }

    // Process refill
    function processRefill(orderId, button, originalContent) {
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Processing...") }}';
        
        fetch(`/orders/${orderId}/refill`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', '{{ __("Order refilled successfully!") }}');
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(data.message || '{{ __("Refill failed") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', '{{ __("Error processing refill. Please try again.") }}');
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }

    // Check and cancel order
    function checkAndCancel(orderId) {
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Checking...") }}';
        button.disabled = true;

        fetch(`/orders/${orderId}/check-cancel`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.can_cancel) {
                if (confirm('{{ __("This order can be cancelled. Do you want to proceed?") }}')) {
                    processCancel(orderId, button, originalContent);
                } else {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }
            } else {
                showAlert('warning', data.message || '{{ __("This order cannot be cancelled.") }}');
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', '{{ __("Error checking cancel status.") }}');
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }

    // Process cancel
    function processCancel(orderId, button, originalContent) {
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Processing...") }}';
        
        fetch(`/orders/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', '{{ __("Order cancelled successfully!") }}');
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error(data.message || '{{ __("Cancel failed") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', '{{ __("Error processing cancellation. Please try again.") }}');
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }

    // Process refund for partial orders
    function processPartialRefund(orderId) {
        if (confirm('{{ __("Process refund for this partial order? This will calculate the remaining balance and refund the user.") }}')) {
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("Processing...") }}';
            button.disabled = true;

            fetch(`/orders/${orderId}/process-refund`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || '{{ __("Refund processed successfully!") }}');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    throw new Error(data.message || '{{ __("Refund failed") }}');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', '{{ __("Error processing refund. Please try again.") }}');
                button.innerHTML = originalContent;
                button.disabled = false;
            });
        }
    }

    // Show alert function using AdminLTE toast
    function showAlert(type, message) {
        // Use AdminLTE toast if available, otherwise fallback to alert
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            alert(message);
        }
    }

    // Toggle view mode
    function toggleViewMode(mode) {
        const tableView = document.getElementById('tableView');
        const cardView = document.getElementById('cardView');
        const tableBtn = document.getElementById('tableBtn');
        const cardBtn = document.getElementById('cardBtn');

        if (mode === 'table') {
            tableView.style.display = 'block';
            cardView.style.display = 'none';
            tableBtn.classList.add('active');
            cardBtn.classList.remove('active');
        } else {
            tableView.style.display = 'none';
            cardView.style.display = 'block';
            cardBtn.classList.add('active');
            tableBtn.classList.remove('active');
        }
    }

    // Clear filters
    function clearFilters() {
        document.getElementById('filterForm').reset();
        document.getElementById('filterForm').submit();
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize view mode
        toggleViewMode('table');
    });
</script>
@stop
