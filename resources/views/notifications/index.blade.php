@extends('layouts.app')

@section('title', __('Notifications'))
@section('content_header')
    @include('partials.breadcrumbs')
    <h1 class="text-primary">{{ __('adminlte.Notifications') }}</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class=" py-4">
                @if($notifications->isEmpty())
                    <div class="alert alert-info text-center">
                        <i class="fas fa-bell-slash fa-2x mb-3"></i>
                        <p class="mb-0">{{ __('No new notifications') }}</p>
                    </div>
                @else
                    <div class="row">
                        @foreach($notifications as $notification)
                            @php
                                // Determine notification type and set variables
                                $icon = '';
                                $title = '';
                                $details = '';
                                $url = '#';

                                // Apply different styles based on the read status
                                $unreadClass = is_null($notification->read_at) ? 'unread-notification' : 'read-notification';

                                if (isset($notification->data['transaction_id'])) {
                                    // Transaction Notification
                                    $icon = 'fas fa-dollar-sign text-success';
                                    $title = __('Transaction Notification');
                                    $details = "<strong>" . __('Amount') . ":</strong> $" . number_format($notification->data['amount'], 2) . "<br>" .
                                               "<strong>" . __('Status') . ":</strong> " . ucfirst($notification->data['status']);
                                    $url = route('transactions.show', $notification->data['transaction_id']); // Replace with the actual route
                                } elseif (isset($notification->data['ticket_id'])) {
                                    // Ticket Notification
                                    $icon = 'fas fa-ticket-alt text-warning';
                                    $title = __('Ticket Notification');
                                    $details = "<strong>" . __('Ticket ID') . ":</strong> " . $notification->data['ticket_id'] . "<br>" .
                                               "<strong>" . __('Subject') . ":</strong> " . $notification->data['subject'] . "<br>" .
                                               "<strong>" . __('Status') . ":</strong> " . $notification->data['status'];
                                    $url = route('support.show', $notification->data['ticket_id']); // Replace with the actual route
                                } elseif (isset($notification->data['order_id'])) {
                                    // Order Notification
                                    $icon = 'fas fa-shopping-cart text-primary';
                                    $title = __('Order Notification');
                                    $details = "<strong>" . __('Order ID') . ":</strong> " . $notification->data['order_id'] . "<br>" .
                                               "<strong>" . __('Service') . ":</strong> " . $notification->data['service_name'] . "<br>" .
                                               "<strong>" . __('Quantity') . ":</strong> " . $notification->data['quantity'] . "<br>" .
                                               "<strong>" . __('Charge') . ":</strong> $" . number_format($notification->data['charge'], 2);
                                    $url = route('orders.show', $notification->data['order_id']); // Replace with the actual route
                                } else {
                                    // Fallback for general notifications or undefined types
                                    $icon = 'fas fa-info-circle text-muted';
                                    $title = __('General Notification');
                                    $details = $notification->data['message'] ?? __('No details available');
                                    $url = '#'; // Default URL
                                }
                            @endphp

                            <div class="col-md-12 col-sm-6 mb-4 notification-card-wrapper" id="notification-{{ $notification->id }}">
                                <div class="card shadow-sm border-0 h-100 notification-card {{ $unreadClass }}">
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="icon flex-shrink-0 me-3">
                                                <i class="{{ $icon }} fa-2x"></i>
                                            </div>
                                            <div class="notification-content flex-grow-1">
                                                <h5 class="mb-1 fw-bold">{{ $title }}</h5>
                                                <p class="text-muted mb-0">{!! $details !!}</p>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            <div>
                                                <a href="{{ $url }}" class="btn btn-outline-secondary btn-sm me-2 view-details" data-id="{{ $notification->id }}">
                                                    {{ __('View Details') }}
                                                </a>
                                            </div>
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
        </div>
    </div>
@endsection

<script>
    $(document).ready(function () {
        // Handle notification click
        $('.view-details').on('click', function (e) {
            e.preventDefault();

            var notificationId = $(this).data('id');
            var redirectUrl = $(this).attr('href'); // Get the redirect URL from the anchor tag

            // Disable the link to prevent multiple clicks
            $(this).attr('disabled', true);

            // Mark the notification as read via AJAX
            $.ajax({
                url: '/notifications/' + notificationId + '/markAsRead',
                type: 'GET',
                success: function () {
                    // After marking as read, redirect to the action page
                    window.location.href = redirectUrl;
                },
                error: function () {
                    alert('Error marking notification as read.');
                    // Re-enable the link in case of an error
                    $(this).attr('disabled', false);
                }
            });
        });
    });
</script>


<style>
    .card {
        border-radius: 12px;
        transition: transform 0.3s ease-in-out, background-color 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .icon {
        background-color: var(--adminlte-card-bg);
        border-radius: 50%;
        padding: 10px;
        margin: 15px;
    }

    .notification-content h5 {
        color: var(--adminlte-text-color);
    }

    .notification-content p {
        font-size: 0.9rem;
    }

    .notification-card-wrapper {
        transition: opacity 0.3s ease-in-out;
    }

    .btn {
        font-size: 0.85rem;
    }

    /* Dark/Light Mode Support */
    .card {
        background-color: var(--adminlte-card-bg);
        color: var(--adminlte-card-text-color);
    }

    .btn-outline-primary {
        border-color: var(--adminlte-button-border-color);
        color: var(--adminlte-button-text-color);
    }

    .btn-outline-secondary {
        border-color: var(--adminlte-secondary-border-color);
        color: var(--adminlte-secondary-text-color);
    }

    /* Unread notification styling for both light/dark mode */
    .unread-notification {
        background-color: var(--adminlte-info-color);
        border: 2px solid var(--adminlte-info-color);
    }

    /* Read notification styling for both light/dark mode */
    .read-notification {
        background-color: var(--adminlte-muted-bg);
        border: none;
    }

    /* Animation when marking as read */
    .notification-card-wrapper.fade-out {
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }
</style>
