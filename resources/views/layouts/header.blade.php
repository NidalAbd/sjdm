<!-- Modern Header -->
<nav class="navbar navbar-expand-lg fixed-top modern-navbar" data-aos="fade-down" data-aos-duration="1000">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <div class="logo-container">
                <img src="{{ asset('images/sjdm_logo.png') }}" alt="{{ config('app.name') }} Logo" class="logo-img">
                <div class="logo-glow"></div>
            </div>
            <span class="brand-text">{{ config('app.name') }}</span>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler modern-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="{{ __('adminlte.toggle_navigation') }}">
            <span class="toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Main Navigation -->
                <li class="nav-item">
                    <a class="nav-link modern-link" href="{{ route('home') }}">
                        <i class="fas fa-home"></i>
                        <span>{{ __('adminlte.home') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link modern-link" href="{{ route('services.all') }}">
                        <i class="fas fa-cogs"></i>
                        <span>{{ __('adminlte.services') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link modern-link" href="{{ route('about') }}">
                        <i class="fas fa-info-circle"></i>
                        <span>{{ __('adminlte.about_us') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link modern-link" href="{{ route('contact') }}">
                        <i class="fas fa-envelope"></i>
                        <span>{{ __('adminlte.contact_us') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link modern-link" href="{{ route('faq') }}">
                        <i class="fas fa-question-circle"></i>
                        <span>{{ __('adminlte.faq') }}</span>
                    </a>
                </li>

                <!-- Guest/Auth Navigation -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link modern-link auth-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>{{ __('adminlte.sign_in') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link modern-link auth-link register" href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i>
                            <span>{{ __('adminlte.register') }}</span>
                        </a>
                    </li>
                @else
                    <!-- Notifications -->
                    <li class="nav-item dropdown">
                        <a class="nav-link modern-link notification-bell" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <span class="notification-badge">{{ Auth::user()->unreadNotifications->count() }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu modern-dropdown notification-dropdown" aria-labelledby="notificationDropdown">
                            <div class="dropdown-header">
                                <h6>{{ __('Notifications') }}</h6>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    <button class="mark-all-read" onclick="markAllAsRead()">
                                        {{ __('Mark all read') }}
                                    </button>
                                @endif
                            </div>
                            
                            <div id="notification-list">
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    @foreach(Auth::user()->unreadNotifications->take(5) as $notification)
                                        @php
                                            $notificationType = '';
                                            $iconClass = '';
                                            $title = '';
                                            $message = '';
                                            $url = '#';
                                            
                                            if (isset($notification->data['support_ticket_id'])) {
                                                $notificationType = 'message';
                                                $iconClass = 'fas fa-envelope';
                                                $title = __('New Message');
                                                $message = $notification->data['message_content'] ?? __('You have a new message');
                                                $url = route('support.show', $notification->data['support_ticket_id']);
                                            } elseif (isset($notification->data['ticket_id'])) {
                                                $notificationType = 'ticket';
                                                $iconClass = 'fas fa-ticket-alt';
                                                $title = __('Support Ticket');
                                                $message = $notification->data['subject'] ?? __('New support ticket created');
                                                $url = route('support.show', $notification->data['ticket_id']);
                                            } elseif (isset($notification->data['transaction_id'])) {
                                                $notificationType = 'transaction';
                                                $iconClass = 'fas fa-dollar-sign';
                                                $title = __('Transaction Update');
                                                $message = $notification->data['message'] ?? __('Transaction status updated');
                                                $url = route('transactions.show', $notification->data['transaction_id']);
                                            } elseif (isset($notification->data['order_id'])) {
                                                $notificationType = 'order';
                                                $iconClass = 'fas fa-shopping-cart';
                                                $title = __('Order Update');
                                                $message = $notification->data['message'] ?? __('Order status updated');
                                                $url = route('orders.show', $notification->data['order_id']);
                                            } else {
                                                $notificationType = 'general';
                                                $iconClass = 'fas fa-info-circle';
                                                $title = __('General Notification');
                                                $message = $notification->data['message'] ?? __('New notification');
                                            }
                                        @endphp
                                        
                                        <div class="dropdown-item notification-item unread" onclick="handleNotificationClick('{{ $notification->id }}', '{{ $url }}')">
                                            <div class="notification-content">
                                                <div class="notification-icon {{ $notificationType }}">
                                                    <i class="{{ $iconClass }}"></i>
                                                </div>
                                                <div class="notification-details">
                                                    <div class="notification-title">{{ $title }}</div>
                                                    <div class="notification-message">{{ $message }}</div>
                                                    <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="dropdown-item notification-empty">
                                        <i class="fas fa-bell-slash"></i>
                                        <p>{{ __('No new notifications') }}</p>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="dropdown-footer">
                                <a href="{{ route('notifications.index') }}" class="view-all-notifications">
                                    {{ __('View all notifications') }}
                                </a>
                            </div>
                        </div>
                    </li>

                    <!-- User Profile -->
                    <li class="nav-item dropdown">
                        <a class="nav-link modern-link user-profile" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="profile-avatar">
                                <img src="{{ Auth::user()->adminlte_image() }}" alt="Profile Image" class="avatar-img">
                                <div class="avatar-status"></div>
                            </div>
                            <span class="user-name">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <ul class="dropdown-menu modern-dropdown user-dropdown" aria-labelledby="navbarDropdownUser">
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>{{ __('adminlte.dashboard') }}</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.settings') }}">
                                    <i class="fas fa-user-cog"></i>
                                    <span>{{ __('adminlte.profile') }}</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item logout-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>{{ __('adminlte.log_out') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endguest

                <!-- Language Switcher -->
                <li class="nav-item dropdown language-dropdown">
                    @php
                        $currentLanguage = app()->getLocale();
                    @endphp

                    <a class="nav-link modern-link language-link" href="#" id="navbarDropdownLanguage" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="language-flag">
                            @if($currentLanguage === 'en')
                                <span class="flag-icon flag-en"></span>
                            @else
                                <span class="flag-icon flag-ar"></span>
                            @endif
                        </div>
                        <span class="language-text">
                            @if($currentLanguage === 'en')
                                English
                            @else
                                العربية
                            @endif
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu modern-dropdown language-dropdown-menu" aria-labelledby="navbarDropdownLanguage">
                        <li>
                            <a class="dropdown-item {{ $currentLanguage === 'en' ? 'active' : '' }}" href="{{ route('changeLang', 'en') }}">
                                <span class="flag-icon flag-en"></span>
                                <span>English</span>
                                @if($currentLanguage === 'en')
                                    <i class="fas fa-check"></i>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ $currentLanguage === 'ar' ? 'active' : '' }}" href="{{ route('changeLang', 'ar') }}">
                                <span class="flag-icon flag-ar"></span>
                                <span>العربية</span>
                                @if($currentLanguage === 'ar')
                                    <i class="fas fa-check"></i>
                                @endif
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Theme Toggle -->
                <li class="nav-item">
                    <button class="nav-link modern-link theme-toggle" id="darkModeToggle" aria-label="Toggle theme">
                        <i class="fas fa-moon"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<style>
    /* Modern Navbar Styles */
    .modern-navbar {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        padding: 1rem 0;
        transition: all 0.3s ease;
    }

    [data-theme="dark"] .modern-navbar {
        background: rgba(15, 23, 42, 0.9);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Logo Styles */
    .logo-container {
        position: relative;
        width: 40px;
        height: 40px;
        margin-right: 0.75rem;
    }

    .logo-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 8px;
        z-index: 2;
        position: relative;
    }

    .logo-glow {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        border-radius: 8px;
        opacity: 0.3;
        filter: blur(8px);
        animation: logoGlow 3s ease-in-out infinite;
    }

    @keyframes logoGlow {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 0.6; }
    }

    .brand-text {
        font-weight: 700;
        font-size: 1.25rem;
        color: var(--text-primary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Navigation Links */
    .modern-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-primary) !important;
        font-weight: 500;
        padding: 0.75rem 1rem !important;
        border-radius: 12px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .modern-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .modern-link:hover::before {
        left: 100%;
    }

    .modern-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--primary-color) !important;
        transform: translateY(-2px);
    }

    .modern-link i {
        font-size: 1rem;
        transition: transform 0.3s ease;
    }

    .modern-link:hover i {
        transform: scale(1.1);
    }

    /* Auth Links */
    .auth-link {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white !important;
        border-radius: 12px;
        margin: 0 0.25rem;
    }

    .auth-link:hover {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        color: white !important;
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .auth-link.register {
        background: linear-gradient(135deg, var(--success-color), #059669);
    }

    .auth-link.register:hover {
        background: linear-gradient(135deg, #059669, var(--success-color));
    }

    /* Mobile Toggle */
    .modern-toggler {
        border: none;
        padding: 0.5rem;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .modern-toggler:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .toggler-icon {
        display: block;
        width: 20px;
        height: 2px;
        background: var(--text-primary);
        position: relative;
        transition: all 0.3s ease;
    }

    .toggler-icon::before,
    .toggler-icon::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 2px;
        background: var(--text-primary);
        transition: all 0.3s ease;
    }

    .toggler-icon::before {
        top: -6px;
    }

    .toggler-icon::after {
        top: 6px;
    }

    /* Notifications */
    .notification-bell {
        position: relative;
        padding: 0.75rem !important;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .notification-bell:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.1);
    }

    .notification-badge {
        position: absolute;
        top: 0;
        right: 0;
        background: linear-gradient(45deg, var(--danger-color), #dc2626);
        color: white;
        border-radius: 50%;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        min-width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulse 2s infinite;
    }

    /* User Profile */
    .user-profile {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1rem !important;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .user-profile:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .profile-avatar {
        position: relative;
        width: 32px;
        height: 32px;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .avatar-status {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 8px;
        height: 8px;
        background: var(--success-color);
        border-radius: 50%;
        border: 2px solid var(--bg-primary);
    }

    .user-name {
        font-weight: 600;
        color: var(--text-primary);
    }

    /* Language Switcher */
    .language-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem !important;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .language-link:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .language-flag {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        overflow: hidden;
    }

    .flag-icon {
        width: 100%;
        height: 100%;
        display: block;
    }

    .flag-en {
        background: linear-gradient(to bottom, #012169 33%, #fff 33%, #fff 66%, #C8102E 66%);
    }

    .flag-ar {
        background: linear-gradient(to right, #000 25%, #fff 25%, #fff 50%, #ce1126 50%, #ce1126 75%, #007a3d 75%);
    }

    /* Theme Toggle */
    .theme-toggle {
        padding: 0.75rem !important;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        border: none;
    }

    .theme-toggle:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.1);
    }

    /* Dropdowns */
    .modern-dropdown {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        box-shadow: var(--shadow-xl);
        padding: 0.5rem;
        min-width: 250px;
    }

    [data-theme="dark"] .modern-dropdown {
        background: rgba(30, 41, 59, 0.95);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .dropdown-header {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dropdown-header h6 {
        margin: 0;
        font-weight: 600;
        color: var(--text-primary);
    }

    .mark-all-read {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0.25rem 0.75rem;
        border-radius: 8px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .mark-all-read:hover {
        background: var(--primary-dark);
        transform: scale(1.05);
    }

    .dropdown-item {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
        color: var(--text-primary);
    }

    .dropdown-item:hover {
        background: rgba(99, 102, 241, 0.1);
        transform: translateX(5px);
    }

    .dropdown-item.active {
        background: var(--primary-color);
        color: white;
    }

    .dropdown-divider {
        border-color: var(--border-color);
        margin: 0.5rem 0;
    }

    .logout-item {
        color: var(--danger-color) !important;
    }

    .logout-item:hover {
        background: rgba(239, 68, 68, 0.1) !important;
    }

    /* Responsive Design */
    @media (max-width: 991px) {
        .navbar-collapse {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            margin-top: 1rem;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        [data-theme="dark"] .navbar-collapse {
            background: rgba(30, 41, 59, 0.95);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .modern-link {
            padding: 1rem !important;
            border-radius: 8px;
            margin: 0.25rem 0;
        }

        .auth-link {
            margin: 0.5rem 0;
        }
    }

    @media (max-width: 576px) {
        .brand-text {
            font-size: 1rem;
        }

        .user-name {
            display: none;
        }

        .language-text {
            display: none;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.modern-navbar');
        const isDarkMode = document.body.getAttribute('data-theme') === 'dark';
        
        if (window.scrollY > 50) {
            if (isDarkMode) {
                navbar.style.background = 'rgba(15, 23, 42, 0.95)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            }
            navbar.style.backdropFilter = 'blur(20px)';
            navbar.style.boxShadow = 'var(--shadow-lg)';
        } else {
            if (isDarkMode) {
                navbar.style.background = 'rgba(15, 23, 42, 0.9)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.1)';
            }
            navbar.style.backdropFilter = 'blur(20px)';
            navbar.style.boxShadow = 'none';
        }
    });

    // Add hover effects to navigation links
    document.querySelectorAll('.modern-link').forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });

        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Notification functions
    function initializeNotifications() {
        setInterval(fetchLatestNotifications, 30000);
    }

    function fetchLatestNotifications() {
        fetch('/notifications/latest')
            .then(response => response.json())
            .then(data => {
                updateNotificationBadge(data.length);
                updateNotificationList(data);
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }

    function updateNotificationBadge(count) {
        const badge = document.querySelector('.notification-badge');
        if (count > 0) {
            if (badge) {
                badge.textContent = count;
            } else {
                const newBadge = document.createElement('span');
                newBadge.className = 'notification-badge';
                newBadge.textContent = count;
                document.querySelector('.notification-bell').appendChild(newBadge);
            }
        } else {
            if (badge) {
                badge.remove();
            }
        }
    }

    function updateNotificationList(notifications) {
        const notificationList = document.getElementById('notification-list');
        if (!notificationList) return;

        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="dropdown-item notification-empty">
                    <i class="fas fa-bell-slash"></i>
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
                <div class="dropdown-item notification-item unread" onclick="handleNotificationClick('${notification.id}', '${url}')">
                    <div class="notification-content">
                        <div class="notification-icon ${notificationType}">
                            <i class="${iconClass}"></i>
                        </div>
                        <div class="notification-details">
                            <div class="notification-title">${title}</div>
                            <div class="notification-message">${message}</div>
                            <div class="notification-time">${formatTime(notification.created_at)}</div>
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

    function handleNotificationClick(notificationId, url) {
        fetch(`/notifications/${notificationId}/markAsRead`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const notificationItem = event.target.closest('.notification-item');
                    if (notificationItem) {
                        notificationItem.classList.remove('unread');
                    }
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
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                });
                updateNotificationBadge(0);
                const markAllButton = document.querySelector('.mark-all-read');
                if (markAllButton) {
                    markAllButton.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error marking all notifications as read:', error));
    }

    // Initialize notifications if user is authenticated
    if (document.querySelector('.notification-bell')) {
        initializeNotifications();
    }
});
</script>
