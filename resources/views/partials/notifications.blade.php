@if (Auth::user()->unreadNotifications->count() > 0)
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <li class="dropdown-header">{{ Auth::user()->unreadNotifications->count() }} Notifications</li>
        <li class="dropdown-divider"></li>
        @foreach (Auth::user()->unreadNotifications as $notification)
            <li>
                <a href="{{ route('support.show', $notification->data['support_ticket_id']) }}" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> {{ $notification->data['message'] }}
                    <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
                </a>
            </li>
        @endforeach
        <li class="dropdown-divider"></li>
        <li>
            <a href="#" class="dropdown-item dropdown-footer">{{ __('View all notifications') }}</a>
        </li>
    </ul>
@else
    <span class="dropdown-header">No notifications</span>
@endif
