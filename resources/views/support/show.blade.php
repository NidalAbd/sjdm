@extends('layouts.app')

@section('title', __('adminlte.support_ticket_details', ['id' => $ticket->id]))

@section('content_header')
    <h1>{{ __('adminlte.support_ticket') }} #{{ $ticket->id }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('adminlte.ticket_details') }}</h3>
            <p>Ticket ID: {{ $ticket->id }}</p>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-4">{{ __('adminlte.subject') }}:</dt>
                <dd class="col-sm-8">{{ $ticket->subject }}</dd>

                <dt class="col-sm-4">{{ __('adminlte.message') }}:</dt>
                <dd class="col-sm-8">{{ $ticket->message }}</dd>

                <dt class="col-sm-4">{{ __('adminlte.status') }}:</dt>
                <dd class="col-sm-8">{{ $ticket->status ? $ticket->status->name : __('adminlte.no_status') }}</dd>

                <dt class="col-sm-4">{{ __('adminlte.created_at') }}:</dt>
                <dd class="col-sm-8">{{ $ticket->created_at }}</dd>

                <dt class="col-sm-4">{{ __('adminlte.updated_at') }}:</dt>
                <dd class="col-sm-8">{{ $ticket->updated_at }}</dd>
            </dl>
        </div>
    </div>

    <!-- Messages Section -->
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('adminlte.messages') }}</h3>
        </div>
        <div class="card-body" id="messages">
            @foreach($ticket->messages as $message)
                <div class="d-flex @if($message->user_id === Auth::id()) justify-content-end @else justify-content-start @endif mb-2">
                    <div class="chat-message @if($message->user_id === Auth::id()) bg-primary text-white @else bg-light @endif p-2 rounded">
                        <strong>{{ $message->user->name }}:</strong> {{ $message->message }}
                        <div class="text-muted"><small>{{ $message->created_at->diffForHumans() }}</small></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Message Form -->
    <div class="card mt-3">
        <div class="card-body">
            <form id="messageForm" data-ticket-id="{{ $ticket->id }}">
                @csrf
                <div class="mb-3">
                    <textarea name="message" id="message" class="form-control" rows="3" placeholder="{{ __('adminlte.type_message') }}"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('adminlte.send_message') }}</button>
            </form>
        </div>
    </div>
@stop

@section('css')
    <style>
        .chat-message {
            max-width: 60%;
        }
        .chat-message.bg-primary {
            color: white;
        }
    </style>
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
                    url: "/support/" + ticketId + "/messages",  // Dynamically construct the URL with the ticket ID
                    data: {
                        message: $('#message').val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        // Handle success response
                        $('#messages').append(response.html);
                        $('#message').val('');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText); // Log full error response for debugging
                        let errorMessages = '';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    errorMessages += value + '\n';
                                });
                            } else if (xhr.responseJSON.message) {
                                errorMessages = xhr.responseJSON.message;
                                if (xhr.responseJSON.error_detail) {
                                    errorMessages += '\n' + xhr.responseJSON.error_detail;
                                }
                            } else {
                                errorMessages = 'An unknown error occurred. Please check the console for details.';
                            }
                        } else {
                            errorMessages = 'An unknown error occurred. Please check the console for details.';
                        }

                        alert(errorMessages); // Display error messages to the user
                    }
                });
            });
        });
    </script>
@stop
