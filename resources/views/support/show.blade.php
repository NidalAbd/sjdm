@extends('layouts.app')

@section('title', __('adminlte.support_ticket_details', ['id' => $ticket->id]))

@section('content_header')
    <h1>{{ __('adminlte.support_ticket') }} #{{ $ticket->id }}</h1>
@stop

@section('content')
    <div class="row">
        <!-- Ticket Details Column -->
        <div class="col-md-4">
            <div class="card {{ config('adminlte.classes_card', 'card-primary') }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('adminlte.ticket_details') }}</h3>
                    @if(auth()->user()->hasRole('admin') && $ticket->status->name !== 'Closed')
                        <!-- Close Ticket Button for Admin -->
                        <button id="closeTicketBtn" class="btn btn-danger btn-sm">{{ __('Close Ticket') }}</button>
                    @endif
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">{{ __('adminlte.subject') }}:</dt>
                        <dd class="col-sm-8">{{ $ticket->subject }}</dd>

                        <dt class="col-sm-4">{{ __('adminlte.message') }}:</dt>
                        <dd class="col-sm-8">{{ $ticket->message }}</dd>

                        <dt class="col-sm-4">{{ __('adminlte.status') }}:</dt>
                        <dd class="col-sm-8"><span id="ticket-status">{{ $ticket->status ? $ticket->status->name : __('adminlte.no_status') }}</span></dd>

                        <dt class="col-sm-4">{{ __('adminlte.created_at') }}:</dt>
                        <dd class="col-sm-8">{{ $ticket->created_at }}</dd>

                        <dt class="col-sm-4">{{ __('adminlte.updated_at') }}:</dt>
                        <dd class="col-sm-8">{{ $ticket->updated_at }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Chat Column -->
        <div class="col-md-8">
            <div class="card {{ config('adminlte.classes_card', 'card-primary') }}">
                <div class="card-header">
                    <h3 class="card-title">{{ __('adminlte.messages') }}</h3>
                </div>
                <div class="card-body">
                    <div id="messages" class="direct-chat-messages" style="height: 400px; overflow-y: scroll;">
                        @foreach($ticket->messages as $message)
                            <div class="direct-chat-msg @if($message->user_id === Auth::id()) right @endif">
                                <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name @if($message->user_id === Auth::id()) float-right @else float-left @endif">
                                        {{ $message->user_id === Auth::id() ? __('You') : $message->user->name }}
                                    </span>
                                    <span class="direct-chat-timestamp @if($message->user_id === Auth::id()) float-left @else float-right @endif">
                                        {{ $message->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="direct-chat-text">
                                    {{ $message->message }}
                                    <!-- Show read/unread status -->
                                    @if($message->isRead())
                                        <span class="badge badge-success">{{ __('Read') }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ __('Unread') }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Message Form -->
                <div class="card-footer">
                    @if($ticket->status->name !== 'Closed')
                        <form id="messageForm" data-ticket-id="{{ $ticket->id }}">
                            @csrf
                            <div class="input-group">
                                <textarea name="message" id="message" class="form-control" rows="1" placeholder="{{ __('adminlte.type_message') }}"></textarea>
                                <span class="input-group-append">
                                    <button type="submit" class="btn btn-primary">{{ __('adminlte.send_message') }}</button>
                                </span>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            {{ __('adminlte.ticket_closed_cannot_send_messages') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Submit form via AJAX
            $('#messageForm').submit(function(e) {
                e.preventDefault();

                // Get the ticket ID from the form's data attribute
                let ticketId = $(this).data('ticket-id');

                if (!ticketId) {
                    console.error('Ticket ID is missing.');
                    alert('Unable to send the message. Ticket ID is missing.');
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "/support/" + ticketId + "/messages",
                    data: {
                        message: $('#message').val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        // Append the new message to the chat
                        $('#messages').append(response.html);
                        $('#message').val(''); // Clear input after sending
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('An error occurred while sending the message.');
                    }
                });
            });

            // Close ticket functionality for admins
            $('#closeTicketBtn').click(function() {
                let ticketId = "{{ $ticket->id }}";

                if (confirm('Are you sure you want to close this ticket?')) {
                    $.ajax({
                        type: "POST",
                        url: "/support/" + ticketId + "/close",  // Route for closing the ticket
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $('#ticket-status').text('Closed');
                            $('#closeTicketBtn').remove();  // Remove the button after closing the ticket
                            alert('Ticket has been successfully closed.');
                            location.reload(); // Reload the page to apply changes
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert('An error occurred while closing the ticket.');
                        }
                    });
                }
            });
        });
    </script>
@stop
