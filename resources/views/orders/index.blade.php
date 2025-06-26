@extends('layouts.app')

@section('title', __('adminlte.manage_orders'))

@section('content_header')
    @include('partials.breadcrumbs')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 font-weight-bold text-gray-900">{{ __('adminlte.manage_orders') }}</h1>
        <div class="d-flex gap-2">
            <span class="badge badge-primary fs-6 px-3 py-2">{{ $orders->total() }} {{ __('adminlte.total_orders') }}</span>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Filters Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 font-weight-bold text-primary">
                    <i class="fas fa-filter me-2"></i>{{ __('adminlte.filters') }}
                </h6>
            </div>
            <div class="card-body">
                <form id="filterForm" action="{{ route('orders.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label text-muted small mb-2">{{ __('adminlte.search_orders') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0"
                                       placeholder="{{ __('adminlte.search_placeholder') }}"
                                       value="{{ request()->get('search') }}">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label text-muted small mb-2">{{ __('adminlte.status') }}</label>
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="all">{{ __('adminlte.all_statuses') }}</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request()->get('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label text-muted small mb-2">{{ __('adminlte.platform') }}</label>
                            <select name="platform" class="form-select" onchange="this.form.submit()">
                                <option value="all">{{ __('adminlte.all_platforms') }}</option>
                                @foreach($platforms as $platform)
                                    <option value="{{ $platform }}" {{ request()->get('platform') == $platform ? 'selected' : '' }}>
                                        {{ __('adminlte.' . strtolower($platform)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>{{ __('adminlte.search') }}
                            </button>
                        </div>
                        <div class="col-lg-2 col-md-6 d-flex align-items-end">
                            <a href="{{ route('orders.create') }}" class="btn btn-success w-100">
                                <i class="fas fa-plus me-1"></i>{{ __('adminlte.create_order') }}
                            </a>
                        </div>
                        <div class="col-lg-1 col-md-6 d-flex align-items-end">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100" title="{{ __('adminlte.clear_filters') }}">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-primary">
                        <i class="fas fa-shopping-cart me-2"></i>{{ __('adminlte.orders_list') }}
                    </h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="refreshTable()">
                            <i class="fas fa-sync-alt me-1"></i>{{ __('adminlte.refresh') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-bold">#</th>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-bold">{{ __('adminlte.customer') }}</th>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-bold">{{ __('adminlte.service') }}</th>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-bold">{{ __('adminlte.target') }}</th>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-bold">{{ __('adminlte.quantity') }}</th>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-bold">{{ __('adminlte.amount') }}</th>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-bold">{{ __('adminlte.progress') }}</th>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-bold">{{ __('adminlte.date') }}</th>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-bold">{{ __('adminlte.status') }}</th>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-bold text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3">
                                        <span class="font-weight-bold text-primary">#{{ $order->id }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                                    <span class="text-white small font-weight-bold">
                                                        {{ strtoupper(substr($order->user->name, 0, 2)) }}
                                                    </span>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $order->user->name }}</div>
                                                <small class="text-muted">{{ $order->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-weight-bold">
                                            @if(app()->getLocale() === 'ar')
                                                {{ $order->service->name_ar }}
                                            @else
                                                {{ $order->service->name_en }}
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $order->service->category }}</small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-external-link-alt text-muted me-2"></i>
                                            <a href="{{ $order->link }}" target="_blank" class="text-decoration-none text-primary" title="{{ __('adminlte.visit_link') }}">
                                                {{ __('adminlte.view_target') }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-light text-dark px-3 py-2">{{ number_format($order->quantity) }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="font-weight-bold text-success">${{ number_format($order->charge, 2) }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $progress = $order->quantity > 0 ? (($order->quantity - $order->remains) / $order->quantity) * 100 : 0;
                                            $progressClass = $progress >= 100 ? 'bg-success' : ($progress >= 50 ? 'bg-warning' : 'bg-info');
                                        @endphp
                                        <div class="progress mb-1" style="height: 6px;">
                                            <div class="progress-bar {{ $progressClass }}" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ number_format($progress, 1) }}% ({{ number_format($order->quantity - $order->remains) }}/{{ number_format($order->quantity) }})</small>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-weight-bold">{{ $order->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $statusConfig = [
                                                'pending' => ['class' => 'warning', 'icon' => 'clock'],
                                                'in_progress' => ['class' => 'info', 'icon' => 'spinner'],
                                                'completed' => ['class' => 'success', 'icon' => 'check-circle'],
                                                'cancelled' => ['class' => 'danger', 'icon' => 'times-circle'],
                                                'partial' => ['class' => 'warning', 'icon' => 'exclamation-triangle']
                                            ];
                                            $config = $statusConfig[strtolower($order->status)] ?? ['class' => 'secondary', 'icon' => 'question'];
                                        @endphp
                                        <span class="badge bg-{{ $config['class'] }} px-3 py-2">
                                                <i class="fas fa-{{ $config['icon'] }} me-1"></i>
                                                {{ __('adminlte.' . strtolower($order->status)) }}
                                            </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                @can('view_order', $order)
                                                    <li>
                                                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#viewOrderModal{{ $order->id }}">
                                                            <i class="fas fa-eye me-2 text-info"></i>{{ __('adminlte.view_details') }}
                                                        </button>
                                                    </li>
                                                @endcan

                                                @if($order->can_refill)
                                                    <li>
                                                        <button class="dropdown-item" onclick="handleRefill({{ $order->id }})">
                                                            <i class="fas fa-sync me-2 text-primary"></i>{{ __('adminlte.refill') }}
                                                        </button>
                                                    </li>
                                                @endif

                                                @if($order->can_cancel)
                                                    <li>
                                                        <button class="dropdown-item" onclick="handleCancel({{ $order->id }})">
                                                            <i class="fas fa-ban me-2 text-warning"></i>{{ __('adminlte.cancel') }}
                                                        </button>
                                                    </li>
                                                @endif

                                                @if(!$order->supportTicket)
                                                    @can('create_ticket')
                                                        <li>
                                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#createTicketModal{{ $order->id }}">
                                                                <i class="fas fa-headset me-2 text-info"></i>{{ __('adminlte.create_ticket') }}
                                                            </button>
                                                        </li>
                                                    @endcan
                                                @else
                                                    <li>
                                                        <a href="{{ route('support.show', $order->supportTicket->id) }}" class="dropdown-item">
                                                            <i class="fas fa-ticket-alt me-2 text-success"></i>{{ __('adminlte.view_ticket') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                @can('delete_order', $order)
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item text-danger" onclick="confirmDelete({{ $order->id }})">
                                                            <i class="fas fa-trash me-2"></i>{{ __('adminlte.delete') }}
                                                        </button>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </div>
                                    </td>
                                </tr>

                                <!-- View Order Modal -->
                                <div class="modal fade" id="viewOrderModal{{ $order->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-gradient-primary text-white border-0">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-shopping-cart me-2"></i>{{ __('adminlte.order_details') }} #{{ $order->id }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <div class="card h-100 border-0 bg-light">
                                                            <div class="card-body text-center">
                                                                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                                                    <i class="fas fa-user text-white fa-lg"></i>
                                                                </div>
                                                                <h6 class="card-title">{{ __('adminlte.customer') }}</h6>
                                                                <p class="card-text font-weight-bold">{{ $order->user->name }}</p>
                                                                <small class="text-muted">{{ $order->user->email }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card h-100 border-0 bg-light">
                                                            <div class="card-body text-center">
                                                                <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                                                    <i class="fas fa-cogs text-white fa-lg"></i>
                                                                </div>
                                                                <h6 class="card-title">{{ __('adminlte.service') }}</h6>
                                                                <p class="card-text font-weight-bold">
                                                                    @if(app()->getLocale() === 'ar')
                                                                        {{ $order->service->name_ar }}
                                                                    @else
                                                                        {{ $order->service->name_en }}
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mt-2">
                                                    <div class="col-md-4">
                                                        <div class="text-center p-3 bg-light rounded">
                                                            <i class="fas fa-sort-numeric-up text-primary mb-2"></i>
                                                            <h6 class="mb-1">{{ __('adminlte.quantity') }}</h6>
                                                            <span class="h5 font-weight-bold">{{ number_format($order->quantity) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="text-center p-3 bg-light rounded">
                                                            <i class="fas fa-dollar-sign text-success mb-2"></i>
                                                            <h6 class="mb-1">{{ __('adminlte.amount') }}</h6>
                                                            <span class="h5 font-weight-bold text-success">${{ number_format($order->charge, 2) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="text-center p-3 bg-light rounded">
                                                            <i class="fas fa-calendar text-info mb-2"></i>
                                                            <h6 class="mb-1">{{ __('adminlte.created') }}</h6>
                                                            <span class="h6 font-weight-bold">{{ $order->created_at->format('M d, Y') }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row g-3 mt-2">
                                                    <div class="col-md-6">
                                                        <div class="text-center p-3 bg-light rounded">
                                                            <i class="fas fa-play text-info mb-2"></i>
                                                            <h6 class="mb-1">{{ __('adminlte.start_count') }}</h6>
                                                            <span class="h6 font-weight-bold">{{ number_format($order->start_count) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="text-center p-3 bg-light rounded">
                                                            <i class="fas fa-hourglass-half text-warning mb-2"></i>
                                                            <h6 class="mb-1">{{ __('adminlte.remaining') }}</h6>
                                                            <span class="h6 font-weight-bold">{{ number_format($order->remains) }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-4">
                                                    <h6 class="mb-3">{{ __('adminlte.target_link') }}</h6>
                                                    <div class="p-3 bg-light rounded">
                                                        <a href="{{ $order->link }}" target="_blank" class="text-decoration-none">
                                                            <i class="fas fa-external-link-alt me-2"></i>{{ __('adminlte.visit_target') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    {{ __('adminlte.close') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Create Ticket Modal -->
                                <div class="modal fade" id="createTicketModal{{ $order->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-gradient-info text-white border-0">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-headset me-2"></i>{{ __('adminlte.create_support_ticket') }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('support.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="ticketable_id" value="{{ $order->id }}">
                                                <input type="hidden" name="ticketable_type" value="{{ \App\Models\Order::class }}">
                                                <input type="hidden" name="type" value="order">
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label font-weight-bold">{{ __('adminlte.subject') }}</label>
                                                        <input type="text" class="form-control" name="subject" required
                                                               placeholder="{{ __('adminlte.enter_subject') }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label font-weight-bold">{{ __('adminlte.message') }}</label>
                                                        <textarea class="form-control" name="message" rows="5" required
                                                                  placeholder="{{ __('adminlte.describe_issue') }}"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 bg-light">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        {{ __('adminlte.cancel') }}
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-paper-plane me-1"></i>{{ __('adminlte.submit_ticket') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-shopping-cart fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">{{ __('adminlte.no_orders_found') }}</h5>
                        <p class="text-muted">{{ __('adminlte.no_orders_description') }}</p>
                        <a href="{{ route('orders.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>{{ __('adminlte.create_first_order') }}
                        </a>
                    </div>
                @endif
            </div>

            @if($orders->count() > 0)
                <div class="card-footer bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            {{ __('adminlte.showing') }} {{ $orders->firstItem() }} {{ __('adminlte.to') }} {{ $orders->lastItem() }}
                            {{ __('adminlte.of') }} {{ $orders->total() }} {{ __('adminlte.results') }}
                        </div>
                        <div>
                            {{ $orders->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ __('adminlte.confirm_delete') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-0">{{ __('adminlte.delete_order_confirmation') }}</p>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('adminlte.cancel') }}
                    </button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>{{ __('adminlte.delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        // Enhanced refill function with better UX
        function handleRefill(orderId) {
            Swal.fire({
                title: '{{ __("adminlte.checking_refill") }}',
                text: '{{ __("adminlte.please_wait") }}',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route('orders.checkRefill', ':id') }}'.replace(':id', orderId),
                method: 'GET',
                success: function(response) {
                    Swal.close();
                    if (response.can_refill) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("adminlte.refill_available") }}',
                            text: '{{ __("adminlte.order_can_be_refilled") }}',
                            confirmButtonText: '{{ __("adminlte.proceed_refill") }}'
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __("adminlte.refill_not_available") }}',
                            text: '{{ __("adminlte.order_cannot_be_refilled") }}'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("adminlte.error") }}',
                        text: xhr.responseJSON?.message || '{{ __("adminlte.something_went_wrong") }}'
                    });
                }
            });
        }

        // Enhanced cancel function with better UX
        function handleCancel(orderId) {
            Swal.fire({
                title: '{{ __("adminlte.checking_cancel") }}',
                text: '{{ __("adminlte.please_wait") }}',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route('orders.checkCancel', ':id') }}'.replace(':id', orderId),
                method: 'GET',
                success: function(response) {
                    Swal.close();
                    if (response.can_cancel) {
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __("adminlte.cancel_available") }}',
                            text: '{{ __("adminlte.order_can_be_cancelled") }}',
                            showCancelButton: true,
                            confirmButtonText: '{{ __("adminlte.proceed_cancel") }}',
                            cancelButtonText: '{{ __("adminlte.keep_order") }}',
                            confirmButtonColor: '#dc3545'
                        });
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: '{{ __("adminlte.cancel_not_available") }}',
                            text: '{{ __("adminlte.order_cannot_be_cancelled") }}'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("adminlte.error") }}',
                        text: xhr.responseJSON?.message || '{{ __("adminlte.something_went_wrong") }}'
                    });
                }
            });
        }

        // Delete confirmation function
        function confirmDelete(orderId) {
            $('#deleteForm').attr('action', '{{ route("orders.destroy", ":id") }}'.replace(':id', orderId));
            $('#deleteModal').modal('show');
        }

        // Refresh table function
        function refreshTable() {
            location.reload();
        }

        // Initialize tooltips
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Auto-submit form on filter change with debounce
            let timeout;
            $('input[name="search"]').on('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    $('#filterForm').submit();
                }, 500);
            });
        });

        // Add loading states to buttons
        $('.btn').on('click', function() {
            const btn = $(this);
            if (!btn.hasClass('dropdown-toggle') && !btn.hasClass('btn-close')) {
                btn.prop('disabled', true);
                setTimeout(() => btn.prop('disabled', false), 2000);
            }
        });
    </script>

    <!-- Include SweetAlert2 for better notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .dropdown-item:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }

        .progress {
            background-color: #e9ecef;
        }

        .badge {
            font-size: 0.75em;
        }

        .card {
            transition: box-shadow 0.15s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .modal-content {
            border-radius: 0.5rem;
        }

        .modal-header {
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
        }

        .form-control:focus, .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        .pagination .page-link {
            border-radius: 0.375rem;
            margin: 0 0.125rem;
            border: 1px solid #dee2e6;
        }

        .pagination .page-link:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }

        .text-decoration-none:hover {
            text-decoration: underline !important;
        }

        /* Custom scrollbar for table */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .table-responsive {
                font-size: 0.875rem;
            }

            .modal-dialog {
                margin: 0.5rem;
            }
        }

        /* Animation for loading states */
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .btn:disabled {
            animation: pulse 1.5s infinite;
        }
    </style>
@stop
