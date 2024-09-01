<!-- resources/views/support/partials/message.blade.php -->

<div class="alert-secondary">
    <strong>{{ $message->user->name }}:</strong> {{ $message->message }}
    <div class="text-muted"><small>{{ $message->created_at->diffForHumans() }}</small></div>
</div>
