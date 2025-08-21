@extends('layouts.app')

@section('title', __('adminlte.manage_support_tickets'))

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>{{ __('adminlte.manage_support_tickets') }}</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">
                    <!-- Filter form for search and filter functionality -->
                    <form id="filterForm" action="{{ route('support.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('adminlte.search_tickets') }}"
                                           value="{{ request()->get('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="">{{ __('adminlte.select_status') }}</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}" {{ request()->get('status') == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <select name="unread_filter" class="form-control" onchange="this.form.submit()">
                                        <option value="">{{ __('adminlte.all_tickets') }}</option>
                                        <option value="unread" {{ request()->get('unread_filter') == 'unread' ? 'selected' : '' }}>
                                            {{ __('adminlte.unread_messages') }}
                                        </option>
                                        <option value="read" {{ request()->get('unread_filter') == 'read' ? 'selected' : '' }}>
                                            {{ __('adminlte.read_messages') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">{{ __('adminlte.search') }}</button>
                            </div>
                        </div>
                    </form>

                    <!-- Table to display tickets -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover m-0 align-middle">
                            <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('adminlte.subject') }}</th>
                                <th scope="col">{{ __('adminlte.name') }}</th>
                                <th scope="col">{{ __('adminlte.type') }}</th>
                                <th scope="col">{{ __('adminlte.id') }}</th>
                                <th scope="col">{{ __('adminlte.status') }}</th>
                                <th scope="col">{{ __('adminlte.last_message') }}</th>
                                <th scope="col">{{ __('adminlte.sender') }}</th>
                                <th scope="col">{{ __('adminlte.receiver') }}</th>
                                <th scope="col">{{ __('adminlte.unread_status') }}</th>
                                <th scope="col">{{ __('adminlte.created_at') }}</th>
                                <th scope="col" class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($tickets->count() > 0)
                                @foreach($tickets as $ticket)
                                    @php
                                        $latestMessage = $ticket->messages()->latest()->first();
                                        $unreadCount = $ticket->messages()->whereNull('read_at')->where('user_id', '!=', Auth::id())->count();
                                        $hasUnreadMessages = $unreadCount > 0;
                                        $isCurrentUserSender = $latestMessage && $latestMessage->user_id === Auth::id();
                                    @endphp
                                    <tr class="{{ $hasUnreadMessages ? 'table-warning unread-ticket' : '' }}" 
                                        data-ticket-id="{{ $ticket->id }}">
                                        <th scope="row">
                                            {{ $ticket->id }}
                                            @if($hasUnreadMessages)
                                                <span class="badge badge-danger badge-sm ml-1 notification-badge">
                                                    {{ $unreadCount }}
                                                </span>
                                            @endif
                                        </th>
                                        <td>
                                            <strong>{{ $ticket->subject }}</strong>
                                            @if($hasUnreadMessages)
                                                <i class="fas fa-exclamation-circle text-warning ml-1" 
                                                   title="{{ __('adminlte.new_messages_available') }}"></i>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->user->name }}</td>
                                        <td>{{ $ticket->ticketable_type }}</td>
                                        <td>{{ $ticket->ticketable_id }}</td>
                                        <td>
                                            <span class="badge bg-{{ $ticket->status->name == 'Open' ? 'warning' : ($ticket->status->name == 'Closed' ? 'danger' : 'success') }}">
                                                {{ $ticket->status->name }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($latestMessage)
                                                <div class="message-preview">
                                                    <small class="text-muted">
                                                        {{ Str::limit($latestMessage->message, 50) }}
                                                    </small>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $latestMessage->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-muted">{{ __('adminlte.no_messages') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($latestMessage)
                                                <div class="sender-info">
                                                    <span class="badge badge-{{ $latestMessage->sender_role === 'admin' ? 'danger' : 'info' }}">
                                                        {{ $latestMessage->sender_role === 'admin' ? __('adminlte.admin') : __('adminlte.user') }}
                                                    </span>
                                                    <br>
                                                    <small>{{ $latestMessage->user->name }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($latestMessage)
                                                <div class="receiver-info">
                                                    @if($latestMessage->user_id === Auth::id())
                                                        <span class="badge badge-success">{{ __('adminlte.you') }}</span>
                                                    @else
                                                        <span class="badge badge-{{ Auth::user()->hasRole('admin') ? 'danger' : 'info' }}">
                                                            {{ Auth::user()->hasRole('admin') ? __('adminlte.admin') : __('adminlte.user') }}
                                                        </span>
                                                    @endif
                                                    <br>
                                                    <small>{{ Auth::user()->name }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($hasUnreadMessages)
                                                <div class="unread-indicator">
                                                    <span class="badge badge-danger badge-pill">
                                                        <i class="fas fa-envelope"></i> {{ $unreadCount }}
                                                    </span>
                                                    <br>
                                                    <small class="text-danger">{{ __('adminlte.unread') }}</small>
                                                </div>
                                            @else
                                                <div class="read-indicator">
                                                    <span class="badge badge-success badge-pill">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                    <br>
                                                    <small class="text-success">{{ __('adminlte.read') }}</small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group" aria-label="{{ __('adminlte.ticket_actions') }}">
                                                @can('view', $ticket)
                                                    <a href="{{ route('support.show', $ticket->id) }}"
                                                       class="btn btn-secondary btn-sm {{ $hasUnreadMessages ? 'btn-warning' : '' }}"
                                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                                       title="{{ $hasUnreadMessages ? __('adminlte.view_ticket_with_new_messages') : __('adminlte.view_ticket') }}">
                                                        <i class="fas fa-eye"></i>
                                                        @if($hasUnreadMessages)
                                                            <span class="badge badge-light badge-sm ml-1">{{ $unreadCount }}</span>
                                                        @endif
                                                    </a>
                                                @endcan
                                                @can('assign_role', $ticket)
                                                    <a href="{{ route('support.edit', $ticket->id) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('adminlte.edit_ticket') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete', $ticket)
                                                    <form action="{{ route('support.destroy', $ticket->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            class="btn btn-danger btn-sm"
                                                            type="submit" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ __('adminlte.delete_ticket') }}"><i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="12" class="text-center text-muted">{{ __('adminlte.no_tickets_found') }}</td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('adminlte.subject') }}</th>
                                <th scope="col">{{ __('adminlte.name') }}</th>
                                <th scope="col">{{ __('adminlte.type') }}</th>
                                <th scope="col">{{ __('adminlte.id') }}</th>
                                <th scope="col">{{ __('adminlte.status') }}</th>
                                <th scope="col">{{ __('adminlte.last_message') }}</th>
                                <th scope="col">{{ __('adminlte.sender') }}</th>
                                <th scope="col">{{ __('adminlte.receiver') }}</th>
                                <th scope="col">{{ __('adminlte.unread_status') }}</th>
                                <th scope="col">{{ __('adminlte.created_at') }}</th>
                                <th scope="col" class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="pagination justify-content-center">
                        {{ $tickets->appends(request()->except('page'))->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Notification Alert Modal -->
    <div class="modal fade" id="notificationAlertModal" tabindex="-1" aria-labelledby="notificationAlertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="notificationAlertModalLabel">
                        <i class="fas fa-bell"></i> {{ __('adminlte.new_message_notification') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="notificationAlertContent">
                        <!-- Content will be populated by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('adminlte.close') }}</button>
                    <button type="button" class="btn btn-primary" id="viewTicketBtn">{{ __('adminlte.view_ticket') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .unread-ticket {
        background-color: #fff3cd !important;
        border-left: 4px solid #ffc107;
        animation: pulse 2s infinite;
    }

    .unread-ticket:hover {
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

    .message-preview {
        max-width: 200px;
    }

    .sender-info, .receiver-info {
        text-align: center;
    }

    .unread-indicator, .read-indicator {
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
</style>
@stop

@section('js')
<script>
    console.log('Manage Support Tickets page loaded');

    // Check for new notifications every 30 seconds
    setInterval(function() {
        checkForNewNotifications();
    }, 30000);

    function checkForNewNotifications() {
        fetch('/notifications/latest')
            .then(response => response.json())
            .then(notifications => {
                notifications.forEach(notification => {
                    if (notification.data && notification.data.support_ticket_id) {
                        showNotificationAlert(notification);
                        updateTicketRow(notification.data.support_ticket_id);
                    }
                });
            })
            .catch(error => console.error('Error checking notifications:', error));
    }

    function showNotificationAlert(notification) {
        const modal = document.getElementById('notificationAlertModal');
        const content = document.getElementById('notificationAlertContent');
        const viewBtn = document.getElementById('viewTicketBtn');

        content.innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-envelope"></i>
                <strong>${notification.data.message_content || 'New message received'}</strong>
                <br>
                <small class="text-muted">From: ${notification.data.user_name || 'Unknown'}</small>
            </div>
        `;

        viewBtn.onclick = function() {
            window.location.href = `/support/${notification.data.support_ticket_id}`;
        };

        // Show modal with animation
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }

    function updateTicketRow(ticketId) {
        const row = document.querySelector(`tr[data-ticket-id="${ticketId}"]`);
        if (row) {
            row.classList.add('unread-ticket');
            
            // Update unread count
            const badge = row.querySelector('.notification-badge');
            if (badge) {
                const currentCount = parseInt(badge.textContent) || 0;
                badge.textContent = currentCount + 1;
            } else {
                // Create new badge if it doesn't exist
                const firstCell = row.querySelector('th');
                const newBadge = document.createElement('span');
                newBadge.className = 'badge badge-danger badge-sm ml-1 notification-badge';
                newBadge.textContent = '1';
                firstCell.appendChild(newBadge);
            }

            // Add exclamation icon
            const subjectCell = row.querySelector('td:nth-child(2)');
            if (subjectCell && !subjectCell.querySelector('.fa-exclamation-circle')) {
                const icon = document.createElement('i');
                icon.className = 'fas fa-exclamation-circle text-warning ml-1';
                icon.title = 'New messages available';
                subjectCell.appendChild(icon);
            }
        }
    }

    // Auto-refresh unread status when page is focused
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            checkForNewNotifications();
        }
    });
</script>
@stop
