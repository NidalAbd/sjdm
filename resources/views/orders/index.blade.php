@extends('layouts.app')

@section('title', __('adminlte.manage_orders'))

@section('content_header')
    @include('partials.breadcrumbs')
    <h1 class="text-primary">{{ __('adminlte.manage_orders') }}</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Search and Filters Form -->
                    <form id="filterForm" action="{{ route('orders.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('adminlte.search_orders') }}"
                                           value="{{ request()->get('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="input-group input-group-sm">
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="all">{{ __('adminlte.select_status') }}</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ request()->get('status') == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <div class="input-group input-group-sm">
                                    <select name="platform" class="form-control" onchange="this.form.submit()">
                                        <option value="all">{{ __('adminlte.select_platform') }}</option>
                                        @foreach($platforms as $platform)
                                            <option value="{{ $platform }}" {{ request()->get('platform') == $platform ? 'selected' : '' }}>
                                                {{ __('adminlte.' . strtolower($platform)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">{{ __('adminlte.search') }}</button>
                            </div>
                            <div class="col-md-2 mb-2">
                                <a href="{{ route('orders.create') }}" class="btn btn-sm btn-block btn-info">
                                    {{ __('adminlte.create_order') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Orders Table -->
                    <div class="table-responsive mt-4">
                        <table class="table  table-striped table-hover">
                            <thead class="table-dark text-white">
                            <tr>
                                <th>#</th>
                                <th>{{ __('adminlte.name') }}</th>
                                <th>{{ __('adminlte.service_name') }}</th>
                                <th>{{ __('adminlte.link') }}</th>
                                <th>{{ __('adminlte.quantity') }}</th>
                                <th>{{ __('adminlte.charge') }}</th>
                                <th>{{ __('adminlte.start_count') }}</th>
                                <th>{{ __('adminlte.remains') }}</th>
                                <th>{{ __('adminlte.date') }}</th>
                                <th>{{ __('adminlte.status') }}</th>
                                <th class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($orders->count() > 0)
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->user->name }}</td>
                                        <td>{{ $order->service->name }}</td>
                                        <td><a href="{{ $order->link }}" target="_blank">{{ $order->link }}</a></td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>${{ number_format($order->charge, 2) }}</td>
                                        <td>{{ $order->start_count }}</td>
                                        <td>{{ $order->remains }}</td>
                                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                        <td>{{ __('adminlte.' . strtolower($order->status)) }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group" aria-label="{{ __('adminlte.order_actions') }}">
                                                @can('view_order', $order)
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#viewOrderModal{{ $order->id }}"
                                                            title="{{ __('adminlte.view_order') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endcan
                                                @can('delete_order', $order)
                                                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger btn-sm" type="submit"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="{{ __('adminlte.delete_order') }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @if($order->can_refill)
                                                    <button type="button" class="btn btn-info btn-sm" onclick="checkAndRefill({{ $order->id }})"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('adminlte.refill') }}">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                @endif
                                                @if($order->can_cancel)
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="checkAndCancel({{ $order->id }})"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('adminlte.cancel') }}">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                @endif
                                                @can('create_ticket')
                                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#createTicketModal{{ $order->id }}"
                                                            title="{{ __('adminlte.create_support_ticket') }}">
                                                        <i class="fas fa-headset"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- View Order Modal -->
                                    <div class="modal fade" id="viewOrderModal{{ $order->id }}" tabindex="-1" aria-labelledby="viewOrderModalLabel{{ $order->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title" id="viewOrderModalLabel{{ $order->id }}">{{ __('show') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-user text-info" style="margin-right: 10px;"></i>{{ __('adminlte.name') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ $order->user->name }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-tags text-info" style="margin-right: 10px;"></i>{{ __('adminlte.service_name') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ $order->status }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-link text-info" style="margin-right: 10px;"></i>{{ __('adminlte.link') }}
                                                                    </h5>
                                                                    <p class="card-text"><a href="{{ $order->link }}" target="_blank">{{ $order->link }}</a></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-sort-numeric-up text-info" style="margin-right: 10px;"></i>{{ __('adminlte.quantity') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ $order->quantity }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-dollar-sign text-info" style="margin-right: 10px;"></i>{{ __('adminlte.charge') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>${{ number_format($order->charge, 2) }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-sort-numeric-up text-info" style="margin-right: 10px;"></i>{{ __('adminlte.start_count') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ $order->start_count }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-sort-numeric-down text-info" style="margin-right: 10px;"></i>{{ __('adminlte.remains') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ $order->remains }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-calendar-alt text-info" style="margin-right: 10px;"></i>{{ __('adminlte.date') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ $order->created_at->format('Y-m-d') }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-info-circle text-info" style="margin-right: 10px;"></i>{{ __('adminlte.status') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ __('adminlte.' . strtolower($order->service->name)) }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('adminlte.close') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal for creating support tickets for Orders -->
                                    <div class="modal fade" id="createTicketModal{{ $order->id }}" tabindex="-1" role="dialog"
                                         aria-labelledby="createTicketModalLabel{{ $order->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title" id="createTicketModalLabel{{ $order->id }}">{{ __('adminlte.create_support_ticket') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('support.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="ticketable_id" value="{{ $order->id }}">
                                                        <input type="hidden" name="ticketable_type" value="{{ \App\Models\Order::class }}">
                                                        <input type="hidden" name="type" value="order">
                                                        <div class="mb-3">
                                                            <label for="subject" class="form-label">{{ __('adminlte.subject') }}</label>
                                                            <input type="text" class="form-control" id="subject" name="subject" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="message" class="form-label">{{ __('adminlte.message') }}</label>
                                                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('adminlte.close') }}</button>
                                                            <button type="submit" class="btn btn-primary">{{ __('adminlte.submit_ticket') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="11" class="text-center text-muted">{{ __('adminlte.no_orders_found') }}</td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>{{ __('adminlte.name') }}</th>
                                <th>{{ __('adminlte.service_name') }}</th>
                                <th>{{ __('adminlte.link') }}</th>
                                <th>{{ __('adminlte.quantity') }}</th>
                                <th>{{ __('adminlte.charge') }}</th>
                                <th>{{ __('adminlte.start_count') }}</th>
                                <th>{{ __('adminlte.remains') }}</th>
                                <th>{{ __('adminlte.date') }}</th>
                                <th>{{ __('adminlte.status') }}</th>
                                <th class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="pagination justify-content-center">
                        {{ $orders->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function checkAndRefill(orderId) {
            $.ajax({
                url: '{{ route('orders.checkRefill', ':id') }}'.replace(':id', orderId),
                method: 'GET',
                success: function(response) {
                    if (response.can_refill) {
                        alert('Order can be refilled');
                    } else {
                        alert('Order cannot be refilled');
                    }
                },
                error: function(xhr) {
                    alert('Error checking refill status: ' + xhr.responseJSON.message);
                }
            });
        }

        function checkAndCancel(orderId) {
            $.ajax({
                url: '{{ route('orders.checkCancel', ':id') }}'.replace(':id', orderId),
                method: 'GET',
                success: function(response) {
                    if (response.can_cancel) {
                        alert('Order can be canceled');
                    } else {
                        alert('Order cannot be canceled');
                    }
                },
                error: function(xhr) {
                    alert('Error checking cancel status: ' + xhr.responseJSON.message);
                }
            });
        }
    </script>
@stop
