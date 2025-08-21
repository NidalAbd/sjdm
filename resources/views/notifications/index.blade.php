@extends('layouts.app')

@section('title', __('Notifications'))
@section('content_header')
    @include('partials.breadcrumbs')
    <h1 class="text-primary">{{ __('adminlte.Notifications') }}</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-bell me-2"></i>
                        {{ __('Notifications') }}
                    </h3>
                    <div class="notification-actions">
                        @if($notifications->count() > 0)
                            <button class="btn btn-outline-primary btn-sm me-2" onclick="markAllAsRead()">
                                <i class="fas fa-check-double me-1"></i>
                                {{ __('Mark all as read') }}
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="clearAllNotifications()">
                                <i class="fas fa-trash me-1"></i>
                                {{ __('Clear all') }}
                            </button>
                        @endif
                    </div>
                </div>
                
                <div class="card-body p-0">
                    @if($notifications->isEmpty())
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">{{ __('No notifications') }}</h4>
                                <p class="text-muted">{{ __('You\'re all caught up! No new notifications to display.') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="notification-list">
                            @foreach($notifications as $notification)
                                @php
                                    // Determine notification type and set variables
                                    $icon = '';
                                    $title = '';
                                    $details = '';
                                    $url = '#';
                                    $type = 'general';
                                    $iconClass = 'fas fa-info-circle';

                                    // Apply different styles based on the read status
                                    $unreadClass = is_null($notification->read_at) ? 'unread-notification' : 'read-notification';

                                    if (isset($notification->data['support_ticket_id'])) {
                                        // Message Notification
                                        $type = 'message';
                                        $iconClass = 'fas fa-envelope';
                                        $title = __('New Message');
                                        $details = $notification->data['message_content'] ?? __('You have a new message in your support ticket');
                                        $url = route('support.show', $notification->data['support_ticket_id']);
                                    } elseif (isset($notification->data['ticket_id'])) {
                                        // Ticket Notification
                                        $type = 'ticket';
                                        $iconClass = 'fas fa-ticket-alt';
                                        $title = __('Support Ticket');
                                        $details = $notification->data['subject'] ?? __('New support ticket created');
                                        $url = route('support.show', $notification->data['ticket_id']);
                                    } elseif (isset($notification->data['transaction_id'])) {
                                        // Transaction Notification
                                        $type = 'transaction';
                                        $iconClass = 'fas fa-dollar-sign';
                                        $title = __('Transaction Update');
                                        $details = $notification->data['message'] ?? __('Transaction status updated');
                                        $url = route('transactions.show', $notification->data['transaction_id']);
                                    } elseif (isset($notification->data['order_id'])) {
                                        // Order Notification
                                        $type = 'order';
                                        $iconClass = 'fas fa-shopping-cart';
                                        $title = __('Order Update');
                                        $details = $notification->data['message'] ?? __('Order status updated');
                                        $url = route('orders.show', $notification->data['order_id']);
                                    } elseif (isset($notification->data['points'])) {
                                        // Points Notification
                                        $type = 'points';
                                        $iconClass = 'fas fa-coins';
                                        $title = __('Points Redeemed');
                                        $details = __('You have successfully redeemed ' . $notification->data['points'] . ' points');
                                        $url = route('points.index');
                                    } else {
                                        // Fallback for general notifications
                                        $title = __('General Notification');
                                        $details = $notification->data['message'] ?? __('New notification received');
                                    }
                                @endphp

                                <div class="notification-item {{ $unreadClass }}" id="notification-{{ $notification->id }}" data-notification-id="{{ $notification->id }}">
                                    <div class="notification-content">
                                        <div class="notification-icon {{ $type }}">
                                            <i class="{{ $iconClass }}"></i>
                                        </div>
                                        <div class="notification-details">
                                            <div class="notification-header">
                                                <h6 class="notification-title">{{ $title }}</h6>
                                                <div class="notification-actions">
                                                    @if(is_null($notification->read_at))
                                                        <button class="btn btn-sm btn-outline-primary mark-read-btn" onclick="markAsRead('{{ $notification->id }}')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-sm btn-outline-danger delete-btn" onclick="deleteNotification('{{ $notification->id }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <p class="notification-message">{{ $details }}</p>
                                            <div class="notification-footer">
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                @if($url !== '#')
                                                    <a href="{{ $url }}" class="btn btn-sm btn-outline-secondary view-details">
                                                        {{ __('View Details') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="pagination-info">
                                    {{ __('Showing') }} {{ $notifications->firstItem() ?? 0 }} {{ __('to') }} {{ $notifications->lastItem() ?? 0 }} {{ __('of') }} {{ $notifications->total() }} {{ __('notifications') }}
                                </div>
                                <div class="pagination-links">
                                    {{ $notifications->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    .notification-list {
        max-height: 600px;
        overflow-y: auto;
    }

    .notification-item {
        padding: 15px 20px;
        border-bottom: 1px solid #e9ecef;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
    }

    .notification-item.unread-notification {
        background: linear-gradient(90deg, #e3f2fd, #f3e5f5);
        border-left: 4px solid #2196f3;
    }

    .notification-item.read-notification {
        background-color: #ffffff;
        opacity: 0.8;
    }

    .notification-content {
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }

    .notification-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.2rem;
        color: white;
    }

    .notification-icon.message {
        background: linear-gradient(45deg, #4caf50, #66bb6a);
    }

    .notification-icon.ticket {
        background: linear-gradient(45deg, #ff9800, #ffb74d);
    }

    .notification-icon.transaction {
        background: linear-gradient(45deg, #2196f3, #42a5f5);
    }

    .notification-icon.order {
        background: linear-gradient(45deg, #9c27b0, #ba68c8);
    }

    .notification-icon.points {
        background: linear-gradient(45deg, #ffc107, #ffdb4d);
    }

    .notification-icon.general {
        background: linear-gradient(45deg, #6c757d, #adb5bd);
    }

    .notification-details {
        flex: 1;
        min-width: 0;
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }

    .notification-title {
        margin: 0;
        font-weight: 600;
        font-size: 1rem;
        color: #333;
    }

    .notification-actions {
        display: flex;
        gap: 5px;
    }

    .notification-actions .btn {
        padding: 2px 6px;
        font-size: 0.75rem;
    }

    .notification-message {
        margin: 0 0 10px 0;
        color: #666;
        line-height: 1.4;
        font-size: 0.9rem;
    }

    .notification-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .view-details {
        font-size: 0.8rem;
        padding: 4px 8px;
    }

    .empty-state {
        padding: 40px 20px;
    }

    .empty-state i {
        opacity: 0.5;
    }

    /* Dark mode support */
    .dark-mode .notification-item {
        border-bottom-color: #4a5568;
        color: #e2e8f0;
    }

    .dark-mode .notification-item:hover {
        background-color: #4a5568;
    }

    .dark-mode .notification-item.unread-notification {
        background: linear-gradient(90deg, #2c5282, #553c9a);
        border-left-color: #3182ce;
    }

    .dark-mode .notification-item.read-notification {
        background-color: #2d3748;
        opacity: 0.8;
    }

    .dark-mode .notification-title {
        color: #e2e8f0;
    }

    .dark-mode .notification-message {
        color: #a0aec0;
    }

    /* Animation for new notifications */
    .notification-item.new-notification {
        animation: slideInRight 0.5s ease-out;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .notification-content {
            flex-direction: column;
            gap: 10px;
        }

        .notification-icon {
            align-self: center;
        }

        .notification-header {
            flex-direction: column;
            gap: 10px;
        }

        .notification-footer {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }
    }

    /* Loading states */
    .notification-item.loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .btn:disabled {
        opacity: 0.6;
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Initialize notification system
        initializeNotifications();
    });

    function initializeNotifications() {
        // Check for new notifications every 30 seconds
        setInterval(checkForNewNotifications, 30000);
    }

    function checkForNewNotifications() {
        fetch('/notifications/latest')
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    // Update notification count in header
                    updateNotificationBadge(data.length);
                }
            })
            .catch(error => console.error('Error checking for new notifications:', error));
    }

    function updateNotificationBadge(count) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            badge.textContent = count;
        }
    }

    function markAsRead(notificationId) {
        const notificationItem = document.getElementById(`notification-${notificationId}`);
        if (!notificationItem) return;

        // Add loading state
        notificationItem.classList.add('loading');

        fetch(`/notifications/${notificationId}/markAsRead`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Remove unread styling
                    notificationItem.classList.remove('unread-notification');
                    notificationItem.classList.add('read-notification');
                    
                    // Hide mark as read button
                    const markReadBtn = notificationItem.querySelector('.mark-read-btn');
                    if (markReadBtn) {
                        markReadBtn.style.display = 'none';
                    }

                    // Update notification count
                    updateNotificationCount();
                    
                    showToast('{{ __("Notification marked as read") }}', 'success');
                } else {
                    showToast('{{ __("Failed to mark notification as read") }}', 'error');
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
                showToast('{{ __("An error occurred") }}', 'error');
            })
            .finally(() => {
                notificationItem.classList.remove('loading');
            });
    }

    function markAllAsRead() {
        if (!confirm('{{ __("Are you sure you want to mark all notifications as read?") }}')) {
            return;
        }

        fetch('/notifications/markAllAsRead', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove unread styling from all notifications
                document.querySelectorAll('.unread-notification').forEach(item => {
                    item.classList.remove('unread-notification');
                    item.classList.add('read-notification');
                    
                    // Hide mark as read buttons
                    const markReadBtn = item.querySelector('.mark-read-btn');
                    if (markReadBtn) {
                        markReadBtn.style.display = 'none';
                    }
                });

                // Update notification count
                updateNotificationCount();
                
                showToast('{{ __("All notifications marked as read") }}', 'success');
            } else {
                showToast('{{ __("Failed to mark notifications as read") }}', 'error');
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
            showToast('{{ __("An error occurred") }}', 'error');
        });
    }

    function deleteNotification(notificationId) {
        if (!confirm('{{ __("Are you sure you want to delete this notification?") }}')) {
            return;
        }

        const notificationItem = document.getElementById(`notification-${notificationId}`);
        if (!notificationItem) return;

        // Add loading state
        notificationItem.classList.add('loading');

        fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Remove notification from DOM with animation
                notificationItem.style.animation = 'slideOutLeft 0.3s ease-out';
                setTimeout(() => {
                    notificationItem.remove();
                    
                    // Check if no notifications left
                    if (document.querySelectorAll('.notification-item').length === 0) {
                        location.reload();
                    }
                }, 300);

                // Update notification count
                updateNotificationCount();
                
                showToast('{{ __("Notification deleted") }}', 'success');
            } else {
                showToast('{{ __("Failed to delete notification") }}', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting notification:', error);
            showToast('{{ __("An error occurred") }}', 'error');
        })
        .finally(() => {
            notificationItem.classList.remove('loading');
        });
    }

    function clearAllNotifications() {
        if (!confirm('{{ __("Are you sure you want to clear all notifications? This action cannot be undone.") }}')) {
            return;
        }

        fetch('/notifications', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('{{ __("All notifications cleared") }}', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showToast('{{ __("Failed to clear notifications") }}', 'error');
            }
        })
        .catch(error => {
            console.error('Error clearing notifications:', error);
            showToast('{{ __("An error occurred") }}', 'error');
        });
    }

    function updateNotificationCount() {
        fetch('/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error updating notification count:', error));
    }

    function showToast(message, type) {
        const toastClass = type === 'success' ? 'bg-success' : 'bg-danger';
        const toastHtml = `
            <div class="toast align-items-center text-white ${toastClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        // Add toast to container
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        // Show the last toast
        const toast = toastContainer.lastElementChild;
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    // Add slideOutLeft animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideOutLeft {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(-30px);
            }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection
