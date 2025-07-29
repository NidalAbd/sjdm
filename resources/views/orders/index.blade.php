@extends('layouts.app')

@section('title', __('adminlte.manage_orders'))

@section('content_header')
    @include('partials.breadcrumbs')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-primary mb-0">
            <i class="fas fa-shopping-cart me-2"></i>{{ __('adminlte.manage_orders') }}
        </h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" onclick="exportOrders()">
                <i class="fas fa-download me-1"></i>{{ __('Export') }}
            </button>
            <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>{{ __('adminlte.create_order') }}
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stat-card bg-gradient-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">{{ __('Total Orders') }}</h6>
                                    <h3 class="mb-0">{{ $orders->total() }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-gradient-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">{{ __('Completed') }}</h6>
                                    <h3 class="mb-0">{{ $orders->where('status', 'completed')->count() }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-gradient-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">{{ __('Pending') }}</h6>
                                    <h3 class="mb-0">{{ $orders->where('status', 'pending')->count() }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-gradient-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title mb-0">{{ __('Total Value') }}</h6>
                                    <h3 class="mb-0">${{ number_format($orders->sum('charge'), 2) }}</h3>
                                </div>
                                <div class="stat-icon">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-0 text-dark">
                                <i class="fas fa-list me-2"></i>{{ __('Order List') }}
                            </h5>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleView('table')">
                                    <i class="fas fa-table"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleView('cards')">
                                    <i class="fas fa-th-large"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Modern Search and Filters -->
                    <div class="modern-filters mb-4">
                        <form id="filterForm" action="{{ route('orders.index') }}" method="GET">
                            <div class="filter-row">
                                <div class="filter-group">
                                    <div class="filter-input-wrapper">
                                        <i class="fas fa-search filter-icon"></i>
                                        <input type="text" name="search" class="filter-input"
                                               placeholder="{{ __('adminlte.search_orders') }}"
                                               value="{{ request()->get('search') }}">
                                    </div>
                                </div>

                                <div class="filter-group">
                                    <div class="filter-select-wrapper">
                                        <i class="fas fa-tag filter-icon"></i>
                                        <select name="status" class="filter-select" onchange="this.form.submit()">
                                            <option value="all">{{ __('adminlte.select_status') }}</option>
                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" {{ request()->get('status') == $status ? 'selected' : '' }}>
                                                    {{ ucfirst($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="filter-group">
                                    <div class="filter-select-wrapper">
                                        <i class="fas fa-globe filter-icon"></i>
                                        <select name="platform" class="filter-select" onchange="this.form.submit()">
                                            <option value="all">{{ __('adminlte.select_platform') }}</option>
                                            @foreach($platforms as $platform)
                                                <option value="{{ $platform }}" {{ request()->get('platform') == $platform ? 'selected' : '' }}>
                                                    {{ __('adminlte.' . strtolower($platform)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="filter-actions">
                                    <button type="submit" class="filter-btn filter-btn-primary">
                                        <i class="fas fa-filter"></i>
                                        <span>{{ __('adminlte.search') }}</span>
                                    </button>

                                    <button type="button" class="filter-btn filter-btn-secondary" onclick="clearFilters()">
                                        <i class="fas fa-times"></i>
                                        <span>{{ __('Clear') }}</span>
                                    </button>

                                    <button type="button" class="filter-btn filter-btn-advanced" onclick="toggleAdvancedFilters()">
                                        <i class="fas fa-cog"></i>
                                        <span>{{ __('Advanced') }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Advanced Filters (Hidden by default) -->
                            <div id="advancedFilters" class="advanced-filters" style="display: none;">
                                <div class="advanced-filters-grid">
                                    <div class="advanced-filter-group">
                                        <label class="advanced-filter-label">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ __('Date Range') }}
                                        </label>
                                        <div class="advanced-filter-inputs">
                                            <input type="date" name="date_from" class="advanced-filter-input"
                                                   value="{{ request()->get('date_from') }}">
                                            <span class="advanced-filter-separator">to</span>
                                            <input type="date" name="date_to" class="advanced-filter-input"
                                                   value="{{ request()->get('date_to') }}">
                                        </div>
                                    </div>

                                    <div class="advanced-filter-group">
                                        <label class="advanced-filter-label">
                                            <i class="fas fa-dollar-sign"></i>
                                            {{ __('Price Range') }}
                                        </label>
                                        <div class="advanced-filter-inputs">
                                            <input type="number" name="price_min" class="advanced-filter-input"
                                                   placeholder="Min" value="{{ request()->get('price_min') }}">
                                            <span class="advanced-filter-separator">-</span>
                                            <input type="number" name="price_max" class="advanced-filter-input"
                                                   placeholder="Max" value="{{ request()->get('price_max') }}">
                                        </div>
                                    </div>

                                    <div class="advanced-filter-group">
                                        <label class="advanced-filter-label">
                                            <i class="fas fa-sort-numeric-up"></i>
                                            {{ __('Quantity Range') }}
                                        </label>
                                        <div class="advanced-filter-inputs">
                                            <input type="number" name="qty_min" class="advanced-filter-input"
                                                   placeholder="Min" value="{{ request()->get('qty_min') }}">
                                            <span class="advanced-filter-separator">-</span>
                                            <input type="number" name="qty_max" class="advanced-filter-input"
                                                   placeholder="Max" value="{{ request()->get('qty_max') }}">
                                        </div>
                                    </div>

                                    <div class="advanced-filter-group">
                                        <label class="advanced-filter-label">
                                            <i class="fas fa-sort"></i>
                                            {{ __('Sort By') }}
                                        </label>
                                        <select name="sort" class="advanced-filter-select">
                                            <option value="id_desc" {{ request()->get('sort') == 'id_desc' ? 'selected' : '' }}>Newest First</option>
                                            <option value="id_asc" {{ request()->get('sort') == 'id_asc' ? 'selected' : '' }}>Oldest First</option>
                                            <option value="charge_desc" {{ request()->get('sort') == 'charge_desc' ? 'selected' : '' }}>Price High to Low</option>
                                            <option value="charge_asc" {{ request()->get('sort') == 'charge_asc' ? 'selected' : '' }}>Price Low to High</option>
                                            <option value="date_desc" {{ request()->get('sort') == 'date_desc' ? 'selected' : '' }}>Date Newest</option>
                                            <option value="date_asc" {{ request()->get('sort') == 'date_asc' ? 'selected' : '' }}>Date Oldest</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table View -->
                    <div id="tableView" class="table-responsive">
                        <table class="table table-hover modern-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">
                                        <div class="d-flex align-items-center">
                                            <input type="checkbox" id="selectAll" class="form-check-input me-2">
                                            <span>#</span>
                                        </div>
                                    </th>
                                    <th class="border-0" style="width: 15%;">{{ __('adminlte.name') }}</th>
                                    <th class="border-0" style="width: 20%;">{{ __('adminlte.service_name') }}</th>
                                    <th class="border-0" style="width: 15%;">{{ __('adminlte.link') }}</th>
                                    <th class="border-0" style="width: 8%;">{{ __('adminlte.quantity') }}</th>
                                    <th class="border-0" style="width: 10%;">{{ __('adminlte.charge') }}</th>
                                    <th class="border-0" style="width: 8%;">{{ __('adminlte.start_count') }}</th>
                                    <th class="border-0" style="width: 8%;">{{ __('adminlte.remains') }}</th>
                                    <th class="border-0" style="width: 8%;">{{ __('adminlte.status') }}</th>
                                    <th class="border-0 text-center" style="width: 15%;">{{ __('adminlte.actions') }}</th>
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
                                    <tr class="order-row"
                                        data-order-id="{{ $order->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input order-checkbox me-2"
                                                       value="{{ $order->id }}">
                                                <span class="order-id">{{ $order->id }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $order->user->name }}</div>
                                                    <small class="text-muted">{{ $order->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="service-info-compact">
                                                <div class="service-name-wrapper">
                                                    @if(app()->getLocale() === 'ar')
                                                        <span class="service-name" title="{{ $order->service->name_ar }}">
                                                            {{ Str::limit($order->service->name_ar, 30) }}
                                                        </span>
                                                    @else
                                                        <span class="service-name" title="{{ $order->service->name_en }}">
                                                            {{ Str::limit($order->service->name_en, 30) }}
                                                        </span>
                                                    @endif
                                                    @if(strlen(app()->getLocale() === 'ar' ? $order->service->name_ar : $order->service->name_en) > 30)
                                                        <span class="service-expand" onclick="toggleServiceDetails({{ $order->id }})">
                                                            <i class="fas fa-chevron-down"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="service-details" id="serviceDetails{{ $order->id }}" style="display: none;">
                                                    <div class="service-full-name">
                                                        @if(app()->getLocale() === 'ar')
                                                            {{ $order->service->name_ar }}
                                                        @else
                                                            {{ $order->service->name_en }}
                                                        @endif
                                                    </div>
                                                    <div class="service-meta">
                                                        <small class="text-muted">ID: {{ $order->service->service_id }}</small>
                                                        <a href="{{ route('services.show', $order->service) }}"
                                                           class="btn btn-sm btn-outline-primary service-link"
                                                           target="_blank" title="View Service Details">
                                                            <i class="fas fa-external-link-alt"></i> Details
                                                        </a>
                                                    </div>
                                                </div>
                                                <small class="text-muted service-id">ID: {{ $order->service->service_id }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="link-container">
                                                <a href="{{ $order->link }}" target="_blank" class="text-truncate d-inline-block"
                                                   style="max-width: 150px;" title="{{ $order->link }}">
                                                    {{ $order->link }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ number_format($order->quantity) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">${{ number_format($order->charge, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ number_format($order->start_count) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ number_format($order->remains) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusConfig = [
                                                    'pending' => ['class' => 'bg-warning', 'icon' => 'fas fa-clock'],
                                                    'processing' => ['class' => 'bg-info', 'icon' => 'fas fa-cog fa-spin'],
                                                    'completed' => ['class' => 'bg-success', 'icon' => 'fas fa-check-circle'],
                                                    'cancelled' => ['class' => 'bg-danger', 'icon' => 'fas fa-times-circle'],
                                                    'refunded' => ['class' => 'bg-secondary', 'icon' => 'fas fa-undo'],
                                                    'partial' => ['class' => 'bg-warning', 'icon' => 'fas fa-exclamation-triangle'],
                                                    'waiting' => ['class' => 'bg-primary', 'icon' => 'fas fa-hourglass-half']
                                                ];
                                                $status = $statusConfig[$order->status] ?? ['class' => 'bg-secondary', 'icon' => 'fas fa-question-circle'];
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
                                                            data-bs-toggle="modal" data-bs-target="#viewOrderModal{{ $order->id }}"
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

                                                @if(!$order->supportTicket)
                                                    @can('create_ticket')
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                                data-bs-toggle="modal" data-bs-target="#createTicketModal{{ $order->id }}"
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
                                                            <span class="badge badge-danger badge-sm ms-1">{{ $unreadCount }}</span>
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
                                            <div class="empty-state">
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

                    <!-- Card View (Hidden by default) -->
                    <div id="cardView" class="row g-3" style="display: none;">
                        @foreach($orders as $order)
                            <div class="col-md-6 col-lg-4">
                                <div class="card order-card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span class="badge bg-primary">#{{ $order->id }}</span>
                                        @php
                                            $statusConfig = [
                                                'pending' => ['class' => 'bg-warning', 'icon' => 'fas fa-clock'],
                                                'processing' => ['class' => 'bg-info', 'icon' => 'fas fa-cog fa-spin'],
                                                'completed' => ['class' => 'bg-success', 'icon' => 'fas fa-check-circle'],
                                                'cancelled' => ['class' => 'bg-danger', 'icon' => 'fas fa-times-circle'],
                                                'refunded' => ['class' => 'bg-secondary', 'icon' => 'fas fa-undo'],
                                                'partial' => ['class' => 'bg-warning', 'icon' => 'fas fa-exclamation-triangle'],
                                                'waiting' => ['class' => 'bg-primary', 'icon' => 'fas fa-hourglass-half']
                                            ];
                                            $status = $statusConfig[$order->status] ?? ['class' => 'bg-secondary', 'icon' => 'fas fa-question-circle'];
                                        @endphp
                                        <span class="badge {{ $status['class'] }}">
                                            <i class="{{ $status['icon'] }} me-1"></i>
                                            {{ __('adminlte.' . strtolower($order->status)) }}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div class="order-info">
                                            <div class="user-info mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $order->user->name }}</div>
                                                        <small class="text-muted">{{ $order->user->email }}</small>
                                                    </div>
                                                </div>
                                            </div>

                                                                                         <div class="service-info-compact mb-3">
                                                 <div class="service-name-wrapper">
                                                     <h6 class="card-title">
                                                         @if(app()->getLocale() === 'ar')
                                                             <span class="service-name" title="{{ $order->service->name_ar }}">
                                                                 {{ Str::limit($order->service->name_ar, 25) }}
                                                             </span>
                                                         @else
                                                             <span class="service-name" title="{{ $order->service->name_en }}">
                                                                 {{ Str::limit($order->service->name_en, 25) }}
                                                             </span>
                                                         @endif
                                                         @if(strlen(app()->getLocale() === 'ar' ? $order->service->name_ar : $order->service->name_en) > 25)
                                                             <span class="service-expand" onclick="toggleServiceDetails({{ $order->id }})">
                                                                 <i class="fas fa-chevron-down"></i>
                                                             </span>
                                                         @endif
                                                     </h6>
                                                 </div>
                                                 <div class="service-details" id="serviceDetails{{ $order->id }}" style="display: none;">
                                                     <div class="service-full-name">
                                                         @if(app()->getLocale() === 'ar')
                                                             {{ $order->service->name_ar }}
                                                         @else
                                                             {{ $order->service->name_en }}
                                                         @endif
                                                     </div>
                                                     <div class="service-meta">
                                                         <small class="text-muted">ID: {{ $order->service->service_id }}</small>
                                                         <a href="{{ route('services.show', $order->service) }}"
                                                            class="btn btn-sm btn-outline-primary service-link"
                                                            target="_blank" title="View Service Details">
                                                             <i class="fas fa-external-link-alt"></i> Details
                                                         </a>
                                                     </div>
                                                 </div>
                                                 <small class="text-muted service-id">ID: {{ $order->service->service_id }}</small>
                                             </div>

                                            <div class="order-details">
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">{{ __('adminlte.quantity') }}</small>
                                                        <span class="fw-bold">{{ number_format($order->quantity) }}</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">{{ __('adminlte.charge') }}</small>
                                                        <span class="fw-bold text-success">${{ number_format($order->charge, 2) }}</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">{{ __('adminlte.start_count') }}</small>
                                                        <span class="fw-bold">{{ number_format($order->start_count) }}</span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">{{ __('adminlte.remains') }}</small>
                                                        <span class="fw-bold">{{ number_format($order->remains) }}</span>
                                                    </div>
                                                </div>

                                                <div class="link-info mb-3">
                                                    <small class="text-muted d-block">{{ __('adminlte.link') }}</small>
                                                    <a href="{{ $order->link }}" target="_blank" class="text-truncate d-block">
                                                        {{ $order->link }}
                                                    </a>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="btn-group w-100" role="group">
                                            @can('view_order', $order)
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal" data-bs-target="#viewOrderModal{{ $order->id }}"
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

                                            @if(!$order->supportTicket)
                                                @can('create_ticket')
                                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                                            data-bs-toggle="modal" data-bs-target="#createTicketModal{{ $order->id }}"
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
                                                        <span class="badge badge-danger badge-sm ms-1">{{ $unreadCount }}</span>
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

                <!-- Pagination -->
                <div class="card-footer bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="pagination-info">
                            <small class="text-muted">
                                {{ __('Showing') }} {{ $orders->firstItem() ?? 0 }} {{ __('to') }} {{ $orders->lastItem() ?? 0 }} {{ __('of') }} {{ $orders->total() }} {{ __('results') }}
                            </small>
                        </div>
                        <div class="pagination-wrapper">
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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

@section('css')
<link rel="stylesheet" href="{{ asset('css/orders-modern.css') }}">
@stop

@section('js')
<script>
// View Toggle Functionality
function toggleView(view) {
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');

    if (view === 'table') {
        tableView.style.display = 'block';
        cardView.style.display = 'none';
        localStorage.setItem('orderView', 'table');
    } else {
        tableView.style.display = 'none';
        cardView.style.display = 'block';
        localStorage.setItem('orderView', 'cards');
    }
}

// Advanced Filters Toggle
function toggleAdvancedFilters() {
    const advancedFilters = document.getElementById('advancedFilters');
    const isVisible = advancedFilters.style.display !== 'none';
    advancedFilters.style.display = isVisible ? 'none' : 'block';
}

// Clear Filters
function clearFilters() {
    const form = document.getElementById('filterForm');
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (input.type === 'text' || input.type === 'number' || input.type === 'date') {
            input.value = '';
        } else if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        }
    });
    form.submit();
}

// Bulk Selection
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateSelectedCount();
});

document.querySelectorAll('.order-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const selectedCheckboxes = document.querySelectorAll('.order-checkbox:checked');
    const count = selectedCheckboxes.length;
    document.getElementById('selectedCount').textContent = count;

    if (count > 0) {
        // Show bulk actions button or modal
        showBulkActions();
    }
}

function showBulkActions() {
    const modal = new bootstrap.Modal(document.getElementById('bulkActionsModal'));
    modal.show();
}

// Export Orders
function exportOrders() {
    const selectedOrders = Array.from(document.querySelectorAll('.order-checkbox:checked'))
        .map(checkbox => checkbox.value);

    if (selectedOrders.length === 0) {
        // Export all orders
        window.location.href = '{{ route("orders.index") }}?export=all';
    } else {
        // Export selected orders
        window.location.href = '{{ route("orders.index") }}?export=selected&orders=' + selectedOrders.join(',');
    }
}

function bulkExport() {
    const selectedOrders = Array.from(document.querySelectorAll('.order-checkbox:checked'))
        .map(checkbox => checkbox.value);

    if (selectedOrders.length > 0) {
        window.location.href = '{{ route("orders.index") }}?export=selected&orders=' + selectedOrders.join(',');
    }
}

// Delete Order with Confirmation
function deleteOrder(orderId) {
    if (confirm('{{ __("Are you sure you want to delete this order?") }}')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("orders.destroy", ":id") }}'.replace(':id', orderId);

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

function bulkDelete() {
    const selectedOrders = Array.from(document.querySelectorAll('.order-checkbox:checked'))
        .map(checkbox => checkbox.value);

    if (selectedOrders.length === 0) {
        alert('{{ __("Please select orders to delete") }}');
        return;
    }

    if (confirm('{{ __("Are you sure you want to delete the selected orders?") }}')) {
        // Implement bulk delete functionality
        console.log('Bulk delete:', selectedOrders);
    }
}

// Enhanced Order Actions
function checkAndRefill(orderId) {
    // Add loading state
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;

    fetch(`/orders/${orderId}/check-refill`)
        .then(response => response.json())
        .then(data => {
            if (data.can_refill) {
                if (confirm('{{ __("This order can be refilled. Proceed?") }}')) {
                    // Implement refill functionality
                    console.log('Refilling order:', orderId);
                }
            } else {
                alert('{{ __("This order cannot be refilled") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("An error occurred while checking refill status") }}');
        })
        .finally(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        });
}

function checkAndCancel(orderId) {
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;

    fetch(`/orders/${orderId}/check-cancel`)
        .then(response => response.json())
        .then(data => {
            if (data.can_cancel) {
                if (confirm('{{ __("This order can be cancelled. Proceed?") }}')) {
                    // Implement cancel functionality
                    console.log('Cancelling order:', orderId);
                }
            } else {
                alert('{{ __("This order cannot be cancelled") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __("An error occurred while checking cancel status") }}');
        })
        .finally(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Restore view preference
    const savedView = localStorage.getItem('orderView') || 'table';
    toggleView(savedView);

    // Add fade-in animation to cards
    document.querySelectorAll('.order-card').forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fade-in');
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-refresh functionality (optional)
    // setInterval(() => {
    //     location.reload();
    // }, 300000); // Refresh every 5 minutes
});

// Real-time updates (WebSocket or Server-Sent Events)
function initializeRealTimeUpdates() {
    // Implement real-time updates if needed
    console.log('Real-time updates initialized');
}

// Search with debouncing
let searchTimeout;
document.querySelector('input[name="search"]').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('filterForm').submit();
    }, 500);
});

// Service details toggle function
function toggleServiceDetails(orderId) {
    const serviceDetails = document.getElementById('serviceDetails' + orderId);
    const expandIcon = event.target.closest('.service-expand');

    if (serviceDetails.style.display === 'none' || serviceDetails.style.display === '') {
        // Show details
        serviceDetails.style.display = 'block';
        expandIcon.classList.add('expanded');
        expandIcon.innerHTML = '<i class="fas fa-chevron-up"></i>';

        // Add smooth animation
        serviceDetails.style.opacity = '0';
        serviceDetails.style.transform = 'translateY(-10px)';

        setTimeout(() => {
            serviceDetails.style.opacity = '1';
            serviceDetails.style.transform = 'translateY(0)';
        }, 10);
    } else {
        // Hide details
        serviceDetails.style.opacity = '0';
        serviceDetails.style.transform = 'translateY(-10px)';

        setTimeout(() => {
            serviceDetails.style.display = 'none';
        }, 300);

        expandIcon.classList.remove('expanded');
        expandIcon.innerHTML = '<i class="fas fa-chevron-down"></i>';
    }
}

// Auto-hide service details when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.service-info-compact')) {
        const allServiceDetails = document.querySelectorAll('.service-details');
        const allExpandIcons = document.querySelectorAll('.service-expand');

        allServiceDetails.forEach((details, index) => {
            if (details.style.display !== 'none') {
                details.style.opacity = '0';
                details.style.transform = 'translateY(-10px)';

                setTimeout(() => {
                    details.style.display = 'none';
                }, 300);

                if (allExpandIcons[index]) {
                    allExpandIcons[index].classList.remove('expanded');
                    allExpandIcons[index].innerHTML = '<i class="fas fa-chevron-down"></i>';
                }
            }
        });
    }
});

// Ensure action buttons are visible
document.addEventListener('DOMContentLoaded', function() {
    // Force visibility of action buttons
    const actionButtons = document.querySelectorAll('.modern-table .btn-group');
    actionButtons.forEach(btnGroup => {
        btnGroup.style.display = 'flex';
        btnGroup.style.visibility = 'visible';
        btnGroup.style.opacity = '1';
        
        const buttons = btnGroup.querySelectorAll('.btn');
        buttons.forEach(btn => {
            btn.style.display = 'inline-flex';
            btn.style.visibility = 'visible';
            btn.style.opacity = '1';
        });
    });
    
    // Add hover effect to make buttons more visible
    actionButtons.forEach(btnGroup => {
        btnGroup.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.zIndex = '100';
        });
        
        btnGroup.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.zIndex = '10';
        });
    });
    
    // Ensure action column header is visible
    const actionHeader = document.querySelector('.modern-table thead th:last-child');
    if (actionHeader) {
        actionHeader.style.visibility = 'visible';
        actionHeader.style.opacity = '1';
        actionHeader.style.display = 'table-cell';
        actionHeader.style.color = '#495057';
        actionHeader.style.fontWeight = '600';
        actionHeader.style.textAlign = 'center';
        actionHeader.style.background = 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)';
        actionHeader.style.position = 'sticky';
        actionHeader.style.right = '0';
        actionHeader.style.zIndex = '15';
    }
});
</script>
@stop
