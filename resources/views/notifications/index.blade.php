@extends('layouts.app')

@section('title', __('Notifications'))

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">{{ __('Notifications') }}</h1>

        @if($notifications->isEmpty())
            <div class="alert alert-info text-center">
                {{ __('No new notifications') }}
            </div>
        @else
            <div class="row">
                @foreach($notifications as $notification)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar mr-3">
                                        <img src="{{ $notification->data['user_avatar'] ?? 'path/to/default-avatar.jpg' }}" alt="Avatar" class="rounded-circle" width="50" height="50">
                                    </div>
                                    <div class="notification-content flex-grow-1">
                                        <h5 class="mb-1">{{ $notification->data['user_name'] }}</h5>
                                        <p class="mb-0 text-muted">{{ $notification->data['message_content'] }}</p>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex justify-content-between">
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    <a href="{{ route('notifications.markAsRead', $notification->id) }}" class="btn btn-sm btn-outline-primary">{{ __('Mark as read') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection

<style>
    .avatar img {
        object-fit: cover;
    }

    .notification-content {
        margin-left: 10px;
    }

    .card {
        border: none;
        border-radius: 8px;
    }

    .card-body {
        padding: 1.25rem;
    }
</style>
