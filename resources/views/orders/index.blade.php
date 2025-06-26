@extends('layouts.app')

@section('title', __('adminlte.manage_orders'))

@section('content_header')
    @include('partials.breadcrumbs')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-primary mb-0">
            <i class="fas fa-shopping-cart mr-2"></i>{{ __('adminlte.manage_orders') }}
        </h1>
        <div class="d-flex">
            <span class="badge badge-info badge-lg mr-2">
                <i class="fas fa-list mr-1"></i>{{ $orders->total() }} {{ __('adminlte.total_orders') }}
            </span>
            <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>{{ __('adminlte.create_order') }}
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Filters Card -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>{{ __('adminlte.filters') }}
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="filterForm" action="{{ route('orders.index') }}" method="GET">
                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <label class="form-label text-sm font-weight-bold">{{ __('adminlte.search') }}</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" name="search" class="form-control"
                                       placeholder="{{ __('adminlte.search_orders') }}"
                                       value="{{ request()->get('search') }}">
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <label class="form-label text-sm font-weight-bold">{{ __('adminlte.status') }}</label>
                            <select name="status" class="form-control form-control-sm select2" data-placeholder="{{ __('adminlte.select_status') }}">
                                <option value="all">{{ __('adminlte.all_statuses') }}</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request()->get('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <label class="form-label text-sm font-weight-bold">{{ __('adminlte.platform') }}</label>
                            <select name="platform" class="form-control form-control-sm select2" data-placeholder="{{ __('adminlte.select_platform') }}">
                                <option value="all">{{ __('adminlte.all_platforms') }}</option>
                                @foreach($platforms as $platform)
                                    <option value="{{ $platform }}" {{ request()->get('platform') == $platform ? 'selected' : '' }}>
                                        {{ __('adminlte.' . strtolower($platform)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6 col-sm-6 mb-3">
                            <label class="form-label text-sm font-weight-bold">{{ __('adminlte.date_range') }}</label>
                            <input type="text" name="date_range" class="form-control form-control-sm"
                                   id="daterange" placeholder="{{ __('adminlte.select_date_range') }}"
                                   value="{{ request()->get('date_range') }}">
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-6 mb-3 d-flex align-items-end">
                            <div class="btn-group w-100" role="group">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search mr-1"></i>{{ __('adminlte.filter') }}
                                </button>
                                <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-undo mr-1"></i>{{ __('adminlte.reset') }}
                                </a>
                                <button type="button" class="btn btn-info btn-sm" onclick="exportOrders()">
                                    <i class="fas fa-download mr-1"></i>{{ __('adminlte.export') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table Card -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-2"></i>{{ __('adminlte.orders_list') }}
                </h3>
                <div class="card-tools">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshTable()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0" id="ordersTable">
                        <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-center" style="width: 60px;">#</th>
                            <th>
                                <i class="fas fa-user mr-1"></i>{{ __('adminlte.customer') }}
                            </th>
                            <th>
                                <i class="fas fa-cogs mr-1"></i>{{ __('adminlte.service') }}
                            </th>
                            <th class="text-center">
                                <i class="fas fa-sort-numeric-up mr-1"></i>{{ __('adminlte.quantity') }}
                            </th>
                            <th class="text-center">
                                <i class="fas fa-dollar-sign mr-1"></i>{{ __('adminlte.charge') }}
                            </th>
                            <th class="text-center">
                                <i class="fas fa-play mr-1"></i>{{ __('adminlte.start_count') }}
                            </th>
                            <th class="text-center">
                                <i class="fas fa-hourglass-half mr-1"></i>{{ __('adminlte.remains') }}
                            </th>
                            <th class="text-center">
                                <i class="fas fa-calendar mr-1"></i>{{ __('adminlte.date') }}
                            </th>
                            <th class="text-center">
                                <i class="fas fa-info-circle mr-1"></i>{{ __('adminlte.status') }}
                            </th>
                            <th class="text-center" style="width: 200px;">
                                <i class="fas fa-tools mr-1"></i>{{ __('adminlte.actions') }}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="text-center align-middle">
                                    <span class="badge badge-light">#{{ $order->id }}</span>
                                </td>

                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar mr-3">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 32px; height: 32px;">
                                                    <span class="text-white font-weight-bold text-sm">
                                                        {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                                    </span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-sm">{{ $order->user->name }}</div>
                                            <small class="text-muted">{{ $order->user->email }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td class="align-middle">
                                    <div class="service-info">
                                        <div class="font-weight-bold text-sm text-primary">
                                            @if(app()->getLocale() === 'ar')
                                                {{ $order->service->name_ar }}
                                            @else
                                                {{ $order->service->name_en }}
                                            @endif
                                        </div>
                                        <div class="text-muted text-xs">
                                            <i class="fas fa-external-link-alt mr-1"></i>
                                            <a href="{{ $order->link }}" target="_blank" class="text-muted text-decoration-none">
                                                {{ __('adminlte.view_link') }}
                                            </a>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center align-middle">
                                    <span class="badge badge-info badge-lg">{{ number_format($order->quantity) }}</span>
                                </td>

                                <td class="text-center align-middle">
                                    <span class="font-weight-bold text-success">${{ number_format($order->charge, 2) }}</span>
                                </td>

                                <td class="text-center align-middle">
                                    <span class="badge badge-secondary">{{ number_format($order->start_count) }}</span>
                                </td>

                                <td class="text-center align-middle">
                                    <span class="badge badge-warning">{{ number_format($order->remains) }}</span>
                                </td>

                                <td class="text-center align-middle">
                                    <div class="text-sm">{{ $order->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                </td>

                                <td class="text-center align-middle">
                                    @php
                                        $statusClass = match(strtolower($order->status)) {
                                            'completed' => 'success',
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'cancelled' => 'danger',
                                            'refunded' => 'secondary',
                                            default => 'primary'
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }} badge-lg">
                                            {{ __('adminlte.' . strtolower($order->status)) }}
                                        </span>
                                </td>

                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @can('view_order', $order)
                                            <button type="button" class="btn btn-outline-primary"
                                                    data-toggle="modal" data-target="#viewOrderModal{{ $order->id }}"
                                                    title="{{ __('adminlte.view_order') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endcan

                                        @if($order->can_refill)
                                            <button type="button" class="btn btn-outline-info"
                                                    onclick="checkAndRefill({{ $order->id }})"
                                                    title="{{ __('adminlte.refill') }}">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                        @endif

                                        @if($order->can_cancel)
                                            <button type="button" class="btn btn-outline-warning"
                                                    onclick="checkAndCancel({{ $order->id }})"
                                                    title="{{ __('adminlte.cancel') }}">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @endif

                                        @if(!$order->supportTicket)
                                            @can('create_ticket')
                                                <button type="button" class="btn btn-outline-secondary"
                                                        data-toggle="modal" data-target="#createTicketModal{{ $order->id }}"
                                                        title="{{ __('adminlte.create_support_ticket') }}">
                                                    <i class="fas fa-headset"></i>
                                                </button>
                                            @endcan
                                        @else
                                            <a href="{{ route('support.show', $order->supportTicket->id) }}"
                                               class="btn btn-outline-info" title="{{ __('adminlte.view_ticket') }}">
                                                <i class="fas fa-ticket-alt"></i>
                                            </a>
                                        @endif

                                        @can('delete_order', $order)
                                            <button type="button" class="btn btn-outline-danger"
                                                    onclick="deleteOrder({{ $order->id }})"
                                                    title="{{ __('adminlte.delete_order') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>

                            <!-- View Order Modal -->
                            <div class="modal fade" id="viewOrderModal{{ $order->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">
                                                <i class="fas fa-eye mr-2"></i>{{ __('adminlte.order_details') }} #{{ $order->id }}
                                            </h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="info-box">
                                                            <span class="info-box-icon bg-primary">
                                                                <i class="fas fa-user"></i>
                                                            </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">{{ __('adminlte.customer') }}</span>
                                                            <span class="info-box-number">{{ $order->user->name }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-box">
                                                            <span class="info-box-icon bg-info">
                                                                <i class="fas fa-cogs"></i>
                                                            </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">{{ __('adminlte.service') }}</span>
                                                            <span class="info-box-number text-sm">
                                                                    @if(app()->getLocale() === 'ar')
                                                                    {{ $order->service->name_ar }}
                                                                @else
                                                                    {{ $order->service->name_en }}
                                                                @endif
                                                                </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="info-box">
                                                            <span class="info-box-icon bg-success">
                                                                <i class="fas fa-sort-numeric-up"></i>
                                                            </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">{{ __('adminlte.quantity') }}</span>
                                                            <span class="info-box-number">{{ number_format($order->quantity) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-box">
                                                            <span class="info-box-icon bg-warning">
                                                                <i class="fas fa-dollar-sign"></i>
                                                            </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">{{ __('adminlte.charge') }}</span>
                                                            <span class="info-box-number">${{ number_format($order->charge, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-box">
                                                            <span class="info-box-icon bg-secondary">
                                                                <i class="fas fa-calendar"></i>
                                                            </span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">{{ __('adminlte.date') }}</span>
                                                            <span class="info-box-number text-sm">{{ $order->created_at->format('M d, Y') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ __('adminlte.link') }}</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" value="{{ $order->link }}" readonly>
                                                            <div class="input-group-append">
                                                                <a href="{{ $order->link }}" target="_blank" class="btn btn-outline-primary">
                                                                    <i class="fas fa-external-link-alt"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>{{ __('adminlte.start_count') }}</label>
                                                        <input type="text" class="form-control" value="{{ number_format($order->start_count) }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>{{ __('adminlte.remains') }}</label>
                                                        <input type="text" class="form-control" value="{{ number_format($order->remains) }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                {{ __('adminlte.close') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Create Ticket Modal -->
                            <div class="modal fade" id="createTicketModal{{ $order->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-dark">
                                            <h5 class="modal-title">
                                                <i class="fas fa-headset mr-2"></i>{{ __('adminlte.create_support_ticket') }}
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{ route('support.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="ticketable_id" value="{{ $order->id }}">
                                            <input type="hidden" name="ticketable_type" value="{{ \App\Models\Order::class }}">
                                            <input type="hidden" name="type" value="order">

                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="subject{{ $order->id }}">{{ __('adminlte.subject') }}</label>
                                                    <input type="text" class="form-control" id="subject{{ $order->id }}"
                                                           name="subject" required placeholder="{{ __('adminlte.enter_subject') }}">
                                                </div>
                                                <div class="form-group">
                                                    <label for="message{{ $order->id }}">{{ __('adminlte.message') }}</label>
                                                    <textarea class="form-control" id="message{{ $order->id }}"
                                                              name="message" rows="4" required
                                                              placeholder="{{ __('adminlte.enter_message') }}"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    {{ __('adminlte.cancel') }}
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-paper-plane mr-1"></i>{{ __('adminlte.submit_ticket') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">{{ __('adminlte.no_orders_found') }}</h5>
                                        <p class="text-muted">{{ __('adminlte.no_orders_description') }}</p>
                                        <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus mr-1"></i>{{ __('adminlte.create_first_order') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($orders->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-sm text-muted">
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
@stop

@section('css')
    <style>
        .user-avatar {
            flex-shrink: 0;
        }

        .service-info {
            min-width: 200px;
        }

        .empty-state {
            padding: 2rem;
        }

        .info-box {
            margin-bottom: 1rem;
        }

        .badge-lg {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }

        .table td {
            vertical-align: middle;
        }

        .btn-group-sm > .btn {
            margin: 0 1px;
        }

        .form-label {
            margin-bottom: 0.25rem;
        }

        .card-tools .btn-group .btn {
            border: none;
        }

        .table-responsive {
            border-radius: 0;
        }

        .modal-header.bg-primary,
        .modal-header.bg-warning {
            border-bottom: none;
        }

        .select2-container--default .select2-selection--single {
            height: calc(1.8125rem + 2px);
            border: 1px solid #ced4da;
        }

        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                margin-bottom: 2px;
                border-radius: 0.25rem !important;
            }
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Initialize DataTables
            $('#ordersTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "paging": false,
                "searching": false,
                "info": false,
                "ordering": true,
                "columnDefs": [
                    { "orderable": false, "targets": [9] }
                ]
            });

            // Initialize date range picker
            $('#daterange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            });

            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            // Auto-submit form on select change
            $('select[name="status"], select[name="platform"]').on('change', function() {
                $('#filterForm').submit();
            });
        });

        function refreshTable() {
            location.reload();
        }



        function checkAndCancel(orderId) {
            Swal.fire({
                title: '{{ __("adminlte.checking") }}...',
                text: '{{ __("adminlte.please_wait") }}',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("orders.checkCancel", ":id") }}'.replace(':id', orderId),
                method: 'GET',
                success: function(response) {
                    Swal.close();
                    if (response.can_cancel) {
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __("adminlte.cancel_order") }}',
                            text: '{{ __("adminlte.are_you_sure_cancel") }}',
                            showCancelButton: true,
                            confirmButtonText: '{{ __("adminlte.yes_cancel") }}',
                            cancelButtonText: '{{ __("adminlte.no_keep") }}',
                            confirmButtonColor: '#dc3545'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Perform cancel action
                                performCancel(orderId);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: '{{ __("adminlte.cancel_not_available") }}',
                            text: response.message || '{{ __("adminlte.order_cannot_be_cancelled") }}'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("adminlte.error") }}',
                        text: xhr.responseJSON?.message || '{{ __("adminlte.error_checking_cancel") }}'
                    });
                }
            });
        }

        function performRefill(orderId) {
            $.ajax({
                url: '{{ route("orders.refill", ":id") }}'.replace(':id', orderId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("adminlte.refill_successful") }}',
                        text: response.message || '{{ __("adminlte.order_refilled_successfully") }}'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("adminlte.refill_failed") }}',
                        text: xhr.responseJSON?.message || '{{ __("adminlte.error_processing_refill") }}'
                    });
                }
            });
        }

        function performCancel(orderId) {
            $.ajax({
                url: '{{ route("orders.cancel", ":id") }}'.replace(':id', orderId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("adminlte.cancel_successful") }}',
                        text: response.message || '{{ __("adminlte.order_cancelled_successfully") }}'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("adminlte.cancel_failed") }}',
                        text: xhr.responseJSON?.message || '{{ __("adminlte.error_processing_cancel") }}'
                    });
                }
            });
        }

        function deleteOrder(orderId) {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("adminlte.delete_order") }}',
                text: '{{ __("adminlte.are_you_sure_delete") }}',
                showCancelButton: true,
                confirmButtonText: '{{ __("adminlte.yes_delete") }}',
                cancelButtonText: '{{ __("adminlte.cancel") }}',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("orders.destroy", ":id") }}'.replace(':id', orderId),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("adminlte.deleted") }}',
                                text: '{{ __("adminlte.order_deleted_successfully") }}'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("adminlte.error") }}',
                                text: xhr.responseJSON?.message || '{{ __("adminlte.error_deleting_order") }}'
                            });
                        }
                    });
                }
            });
        }

        // Initialize tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

        // Handle responsive table scrolling
        $(window).on('resize', function() {
            $('.table-responsive').css('overflow-x', 'auto');
        });
    </script>
@endsection
