@if($notifications->isEmpty())
    <li class="dropdown-item text-center">
        <span>{{ __('No new notifications') }}</span>
    </li>
@else
    @foreach($notifications as $notification)
        <li class="dropdown-item">
            <a href="{{ route('notifications.markAsRead', $notification->id) }}">
                {{ $notification->data['message'] ?? 'No message' }}
                <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
            </a>
        </li>
    @endforeach
    <li class="dropdown-divider"></li>
    <li class="dropdown-item text-center">
        <a href="{{ route('notifications.index') }}">{{ __('View all notifications') }}</a>
    </li>
@endif
