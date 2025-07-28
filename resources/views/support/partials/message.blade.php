<!-- resources/views/support/partials/message.blade.php -->

<div class="message-item @if($message->user_id === Auth::id()) message-own @else message-other @endif" data-message-id="{{ $message->id }}">
    <div class="message-content">
        <div class="message-header">
            <span class="message-author">
                {{ $message->user_id === Auth::id() ? __('You') : $message->user->name }}
            </span>
            <span class="message-time">
                {{ $message->created_at->diffForHumans() }}
            </span>
        </div>
        <div class="message-text">
            {{ $message->message }}
        </div>
        <div class="message-status">
            @if($message->isRead())
                <span class="badge badge-success badge-sm">{{ __('Read') }}</span>
            @else
                <span class="badge badge-warning badge-sm">{{ __('Unread') }}</span>
            @endif
        </div>
    </div>
</div>
