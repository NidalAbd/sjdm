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
                                <th>{{ __('adminlte.support_status') }}</th>
                                <th class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($orders->count() > 0)
                                @foreach($orders as $order)
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
                                    <tr class="{{ $hasUnreadMessages ? 'table-warning unread-order' : '' }}" 
                                        data-order-id="{{ $order->id }}">
                                        <td>
                                            {{ $order->id }}
                                            @if($hasUnreadMessages)
                                                <span class="badge badge-danger badge-sm ml-1 notification-badge">
                                                    {{ $unreadCount }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $order->user->name }}</td>
                                        <td>
                                            @if(app()->getLocale() === 'ar')
                                                {{ $order->service->name_ar }}
                                            @else
                                                {{ $order->service->name_en }}
                                            @endif
                                        </td>
                                        <td><a href="{{ $order->link }}" target="_blank">{{ $order->link }}</a></td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>${{ number_format($order->charge, 2) }}</td>
                                        <td>{{ $order->start_count }}</td>
                                        <td>{{ $order->remains }}</td>
                                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                        <td>{{ __('adminlte.' . strtolower($order->status)) }}</td>
                                        <td>
                                            @if($hasSupportTicket)
                                                <div class="support-status">
                                                    @if($hasUnreadMessages)
                                                        <span class="badge badge-danger badge-pill">
                                                            <i class="fas fa-exclamation-circle"></i> {{ __('adminlte.unread_messages') }}
                                                        </span>
                                                        <br>
                                                        <small class="text-danger">{{ $unreadCount }} {{ __('adminlte.new_messages') }}</small>
                                                    @else
                                                        <span class="badge badge-success badge-pill">
                                                            <i class="fas fa-check-circle"></i> {{ __('adminlte.ticket_active') }}
                                                        </span>
                                                        <br>
                                                        <small class="text-success">{{ __('adminlte.all_read') }}</small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="badge badge-secondary badge-pill">
                                                    <i class="fas fa-times-circle"></i> {{ __('adminlte.no_ticket') }}
                                                </span>
                                            @endif
                                        </td>
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
                                                <!-- Support Ticket Button with enhanced styling -->
                                                @if(!$order->supportTicket)
                                                    @can('create_ticket')
                                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                                data-bs-target="#createTicketModal{{ $order->id }}"
                                                                title="{{ __('adminlte.create_support_ticket') }}">
                                                            <i class="fas fa-headset"></i>
                                                        </button>
                                                    @endcan
                                                @else
                                                    <a href="{{ route('support.show', $order->supportTicket->id) }}" 
                                                       class="btn btn-info btn-sm {{ $hasUnreadMessages ? 'btn-warning' : '' }}"
                                                       title="{{ $hasUnreadMessages ? __('adminlte.view_ticket_with_new_messages') : __('adminlte.view_ticket') }}">
                                                        <i class="fas fa-ticket-alt"></i>
                                                        @if($hasUnreadMessages)
                                                            <span class="badge badge-light badge-sm ml-1">{{ $unreadCount }}</span>
                                                        @endif
                                                    </a>
                                                @endif
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
                                <th>{{ __('adminlte.support_status') }}</th>
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

@section('css')
<style>
    .unread-order {
        background-color: #fff3cd !important;
        border-left: 4px solid #ffc107;
        animation: pulse 2s infinite;
    }

    .unread-order:hover {
        background-color: #ffeaa7 !important;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }

    .notification-badge {
        animation: bounce 1s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-5px); }
        60% { transform: translateY(-3px); }
    }

    .support-status {
        text-align: center;
    }

    .badge-pill {
        border-radius: 50rem;
    }

    .table-hover tbody tr:hover {
        transform: scale(1.01);
        transition: transform 0.2s ease;
    }

    .btn-warning {
        animation: pulse 2s infinite;
    }

    .support-status .badge {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
</style>
@stop

@section('js')
<script>
    console.log('Manage Orders page loaded');

    // Check for new notifications every 30 seconds
    setInterval(function() {
        checkForNewOrderNotifications();
    }, 30000);

    function checkForNewOrderNotifications() {
        fetch('/notifications/latest')
            .then(response => response.json())
            .then(notifications => {
                notifications.forEach(notification => {
                    if (notification.data && notification.data.support_ticket_id) {
                        // Check if this notification is related to an order
                        checkOrderForTicket(notification.data.support_ticket_id, notification);
                    }
                });
            })
            .catch(error => console.error('Error checking notifications:', error));
    }

    function checkOrderForTicket(ticketId, notification) {
        // Find the order row that has this support ticket
        const orderRows = document.querySelectorAll('tr[data-order-id]');
        orderRows.forEach(row => {
            const supportTicketLink = row.querySelector('a[href*="/support/"]');
            if (supportTicketLink && supportTicketLink.href.includes(`/support/${ticketId}`)) {
                updateOrderRow(row, notification);
                showOrderNotificationAlert(notification);
            }
        });
    }

    function updateOrderRow(row, notification) {
        row.classList.add('unread-order');
        
        // Update unread count
        const badge = row.querySelector('.notification-badge');
        if (badge) {
            const currentCount = parseInt(badge.textContent) || 0;
            badge.textContent = currentCount + 1;
        } else {
            // Create new badge if it doesn't exist
            const firstCell = row.querySelector('td:first-child');
            const newBadge = document.createElement('span');
            newBadge.className = 'badge badge-danger badge-sm ml-1 notification-badge';
            newBadge.textContent = '1';
            firstCell.appendChild(newBadge);
        }

        // Update support status
        const supportStatusCell = row.querySelector('td:nth-child(11)');
        if (supportStatusCell) {
            const unreadCount = parseInt(badge?.textContent || '1');
            supportStatusCell.innerHTML = `
                <div class="support-status">
                    <span class="badge badge-danger badge-pill">
                        <i class="fas fa-exclamation-circle"></i> {{ __('adminlte.unread_messages') }}
                    </span>
                    <br>
                    <small class="text-danger">${unreadCount} {{ __('adminlte.new_messages') }}</small>
                </div>
            `;
        }

        // Update support ticket button
        const supportButton = row.querySelector('a[href*="/support/"]');
        if (supportButton) {
            supportButton.classList.add('btn-warning');
            supportButton.classList.remove('btn-info');
            
            // Add badge to button if not exists
            if (!supportButton.querySelector('.badge')) {
                const buttonBadge = document.createElement('span');
                buttonBadge.className = 'badge badge-light badge-sm ml-1';
                buttonBadge.textContent = unreadCount || 1;
                supportButton.appendChild(buttonBadge);
            }
        }
    }

    function showOrderNotificationAlert(notification) {
        // Create a toast notification for orders
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(45deg, #ffc107, #ff9800);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            max-width: 300px;
            animation: slideInRight 0.5s ease;
        `;
        
        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-bell" style="font-size: 1.2rem;"></i>
                <div>
                    <strong>{{ __('adminlte.new_message_notification') }}</strong>
                    <br>
                    <small>${notification.data.message_content || 'New message received'}</small>
                </div>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.5s ease';
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    }

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // Auto-refresh when page is focused
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            checkForNewOrderNotifications();
        }
    });
</script>
@stop
