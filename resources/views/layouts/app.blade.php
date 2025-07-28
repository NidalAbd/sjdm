@extends('adminlte::page')

@section('title', __('adminlte.dashboard')) <!-- Use translation key -->

<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('images/favicon-96x96.png') }}" type="image/jpeg">
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-L001CCMV5K"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-L001CCMV5K');
</script>
<!-- Preload background images only if they exist -->
@if(file_exists(public_path('images/double-bubble-outline.webp')))
<link rel="preload" as="image" href="{{ asset('images/double-bubble-outline.webp') }}" type="image/webp">
@endif
@if(file_exists(public_path('images/double-bubble-dark.webp')))
<link rel="preload" as="image" href="{{ asset('images/double-bubble-dark.webp') }}" type="image/webp">
@endif

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Include the breadcrumbs partial -->
@stop

@section('content')
    <div class="container-fluid">
        <!-- Bonus Offer Banner -->
        <!-- Moving Bonus Offer Banner -->
        <!-- Moving Bonus Offer Banner -->
        <div class="news-ticker bg-info text-white py-2 mb-4" style="border-radius: 5px;">
            <div class="ticker-container">
                <div class="ticker-item">&nbsp;&nbsp;
                    <i class="fas fa-gift"></i> &nbsp; {{ __('adminlte.bonus_offer_message') }}
                </div>
            </div>
        </div>

    </div>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize notification system for AdminLTE
        initializeAdminLTENotifications();
        
        let bgElement = document.querySelector("body");
        let isDarkMode = document.body.classList.contains("dark-mode");

        let lightImage = "{{ asset('images/double-bubble-outline.webp') }}";
        let darkImage = "{{ asset('images/double-bubble-dark.webp') }}";

        if (isDarkMode) {
            bgElement.style.backgroundImage = `url(${darkImage})`;
        } else {
            bgElement.style.backgroundImage = `url(${lightImage})`;
        }
    });

    function initializeAdminLTENotifications() {
        // Find the notification bell in AdminLTE navigation
        const notificationBell = document.querySelector('#notification-dropdown');
        if (!notificationBell) return;

        // Add notification dropdown functionality
        notificationBell.addEventListener('click', function(e) {
            e.preventDefault();
            toggleNotificationDropdown();
        });

        // Poll for new notifications every 30 seconds
        setInterval(fetchLatestNotifications, 30000);
    }

    function toggleNotificationDropdown() {
        const dropdown = document.getElementById('notification-dropdown-content');
        if (dropdown) {
            dropdown.remove();
            return;
        }

        // Create notification dropdown
        const notificationDropdown = document.createElement('div');
        notificationDropdown.id = 'notification-dropdown-content';
        notificationDropdown.className = 'dropdown-menu dropdown-menu-right notification-dropdown';
        notificationDropdown.style.cssText = `
            position: absolute;
            top: 100%;
            right: 0;
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 0;
            z-index: 1000;
            background: white;
        `;

        // Add header
        const header = document.createElement('div');
        header.className = 'notification-header';
        header.style.cssText = `
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        `;
        header.innerHTML = `
            <h6 style="margin: 0; font-weight: 600;">{{ __('Notifications') }}</h6>
            <button class="mark-all-read" onclick="markAllAsRead()" style="
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                padding: 5px 10px;
                border-radius: 20px;
                font-size: 0.8rem;
                cursor: pointer;
                transition: background 0.3s ease;
            ">{{ __('Mark all read') }}</button>
        `;

        // Add content area
        const content = document.createElement('div');
        content.id = 'notification-list';
        content.style.cssText = `
            padding: 0;
            max-height: 300px;
            overflow-y: auto;
        `;

        // Add footer
        const footer = document.createElement('div');
        footer.className = 'notification-footer';
        footer.style.cssText = `
            padding: 15px 20px;
            text-align: center;
            border-top: 1px solid #f0f0f0;
        `;
        footer.innerHTML = `
            <a href="{{ route('notifications.index') }}" class="view-all-notifications" style="
                color: #667eea;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.9rem;
            ">{{ __('View all notifications') }}</a>
        `;

        notificationDropdown.appendChild(header);
        notificationDropdown.appendChild(content);
        notificationDropdown.appendChild(footer);

        // Position the dropdown
        const bell = document.getElementById('notification-dropdown');
        bell.style.position = 'relative';
        bell.appendChild(notificationDropdown);

        // Load notifications
        loadNotifications();
    }

    function loadNotifications() {
        fetch('/notifications/latest')
            .then(response => response.json())
            .then(data => {
                updateNotificationList(data);
                updateNotificationBadge(data.length);
            })
            .catch(error => console.error('Error loading notifications:', error));
    }

    function updateNotificationList(notifications) {
        const notificationList = document.getElementById('notification-list');
        if (!notificationList) return;

        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div style="padding: 40px 20px; text-align: center; color: #999;">
                    <i class="fas fa-bell-slash" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                    <p>{{ __('No new notifications') }}</p>
                </div>
            `;
            return;
        }

        let html = '';
        notifications.forEach(notification => {
            const notificationType = getNotificationType(notification);
            const iconClass = getNotificationIcon(notification);
            const title = getNotificationTitle(notification);
            const message = getNotificationMessage(notification);
            const url = getNotificationUrl(notification);

            html += `
                <div class="notification-item unread" onclick="handleNotificationClick('${notification.id}', '${url}')" style="
                    padding: 15px 20px;
                    border-bottom: 1px solid #f0f0f0;
                    transition: background 0.3s ease;
                    cursor: pointer;
                    background: linear-gradient(90deg, #e3f2fd, #f3e5f5);
                    border-left: 4px solid #2196f3;
                ">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <div class="notification-icon ${notificationType}" style="
                            width: 40px;
                            height: 40px;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-shrink: 0;
                            font-size: 1.2rem;
                            color: white;
                            background: linear-gradient(45deg, #4caf50, #66bb6a);
                        ">
                            <i class="${iconClass}"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                <h6 style="margin: 0; font-weight: 600; font-size: 1rem; color: #333;">${title}</h6>
                            </div>
                            <p style="margin: 0 0 10px 0; color: #666; line-height: 1.4; font-size: 0.9rem;">${message}</p>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <small style="color: #999; font-size: 0.75rem;">${formatTime(notification.created_at)}</small>
                                <a href="${url}" class="btn btn-sm btn-outline-secondary" style="font-size: 0.8rem; padding: 4px 8px;">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        notificationList.innerHTML = html;
    }

    function getNotificationType(notification) {
        if (notification.data.support_ticket_id) return 'message';
        if (notification.data.ticket_id) return 'ticket';
        if (notification.data.transaction_id) return 'transaction';
        if (notification.data.order_id) return 'order';
        return 'general';
    }

    function getNotificationIcon(notification) {
        if (notification.data.support_ticket_id) return 'fas fa-envelope';
        if (notification.data.ticket_id) return 'fas fa-ticket-alt';
        if (notification.data.transaction_id) return 'fas fa-dollar-sign';
        if (notification.data.order_id) return 'fas fa-shopping-cart';
        return 'fas fa-info-circle';
    }

    function getNotificationTitle(notification) {
        if (notification.data.support_ticket_id) return '{{ __("New Message") }}';
        if (notification.data.ticket_id) return '{{ __("Support Ticket") }}';
        if (notification.data.transaction_id) return '{{ __("Transaction Update") }}';
        if (notification.data.order_id) return '{{ __("Order Update") }}';
        return '{{ __("General Notification") }}';
    }

    function getNotificationMessage(notification) {
        return notification.data.message_content || notification.data.message || '{{ __("New notification") }}';
    }

    function getNotificationUrl(notification) {
        if (notification.data.support_ticket_id) return `/support/${notification.data.support_ticket_id}`;
        if (notification.data.ticket_id) return `/support/${notification.data.ticket_id}`;
        if (notification.data.transaction_id) return `/transactions/${notification.data.transaction_id}`;
        if (notification.data.order_id) return `/orders/${notification.data.order_id}`;
        return '#';
    }

    function formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return '{{ __("Just now") }}';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        return `${Math.floor(diffInSeconds / 86400)}d ago`;
    }

    function updateNotificationBadge(count) {
        const badge = document.querySelector('#notification-dropdown .badge');
        if (count > 0) {
            if (badge) {
                badge.textContent = count;
            } else {
                // Create badge if it doesn't exist
                const bell = document.getElementById('notification-dropdown');
                const newBadge = document.createElement('span');
                newBadge.className = 'badge badge-danger';
                newBadge.style.cssText = `
                    position: absolute;
                    top: -8px;
                    right: -8px;
                    font-size: 0.7rem;
                    padding: 2px 6px;
                    border-radius: 50%;
                    min-width: 18px;
                    height: 18px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                `;
                newBadge.textContent = count;
                bell.appendChild(newBadge);
            }
        } else {
            if (badge) {
                badge.remove();
            }
        }
    }

    function handleNotificationClick(notificationId, url) {
        // Mark as read
        fetch(`/notifications/${notificationId}/markAsRead`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Navigate to the URL
                    window.location.href = url;
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
    }

    function markAllAsRead() {
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
                // Update notification count
                updateNotificationBadge(0);
                
                // Show success message
                showToast('{{ __("All notifications marked as read") }}', 'success');
            }
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
    }

    function showToast(message, type) {
        // Create a simple toast notification
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    function fetchLatestNotifications() {
        fetch('/notifications/latest')
            .then(response => response.json())
            .then(data => {
                updateNotificationBadge(data.length);
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }
</script>
@stop

@yield('scripts')
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const body = document.body;
        const navbar = document.querySelector('.main-header');

        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const rtlToggle = document.getElementById('rtl-toggle');

        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', function() {
                const isDarkMode = !body.classList.contains('dark-mode');
                applyDarkMode(isDarkMode);
                localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
            });
        }

        if (rtlToggle) {
            rtlToggle.addEventListener('click', function() {
                const isRtlMode = body.getAttribute('dir') !== 'rtl';
                applyRtlMode(isRtlMode);
                localStorage.setItem('rtlMode', isRtlMode ? 'enabled' : 'disabled');
            });
        }

        // Existing tooltip initialization code
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Function to apply or remove dark mode classes
        function applyDarkMode(isDarkMode) {
            if (isDarkMode) {
                body.classList.add('dark-mode');
                navbar.classList.remove('navbar-light', 'navbar-white');
                navbar.classList.add('navbar-dark', 'bg-dark');
            } else {
                body.classList.remove('dark-mode');
                navbar.classList.remove('navbar-dark', 'bg-dark');
                navbar.classList.add('navbar-light', 'navbar-white');
            }
        }

        // Function to apply or remove RTL mode
        function applyRtlMode(isRtlMode) {
            if (isRtlMode) {
                body.setAttribute('dir', 'rtl');
                body.classList.add('rtl-mode');
            } else {
                body.setAttribute('dir', 'ltr');
                body.classList.remove('rtl-mode');
            }
        }

        // Initialize dark mode based on localStorage
        const darkModeEnabled = localStorage.getItem('darkMode') === 'enabled';
        applyDarkMode(darkModeEnabled);

        // Initialize RTL mode based on localStorage
        const rtlModeEnabled = localStorage.getItem('rtlMode') === 'enabled';
        applyRtlMode(rtlModeEnabled);
    });
</script>

@yield('scripts')
