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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('adminlte.messages') }}</h3>
                    <div class="chat-controls">
                        <span class="badge badge-info" id="unread-count">
                            {{ $ticket->messages()->whereNull('read_at')->where('user_id', '!=', Auth::id())->count() }} {{ __('unread') }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="messages" class="chat-messages" style="height: 400px; overflow-y: auto;">
                        @foreach($ticket->messages as $message)
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
                        @endforeach
                    </div>
                </div>

                <!-- Message Form -->
                <div class="card-footer">
                    @if($ticket->status->name !== 'Closed')
                        <form id="messageForm" data-ticket-id="{{ $ticket->id }}">
                            @csrf
                            <div class="input-group">
                                <textarea name="message" id="message" class="form-control" rows="2" placeholder="{{ __('adminlte.type_message') }}" maxlength="1000"></textarea>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary" id="sendButton">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-text text-muted">
                                <span id="charCount">0</span>/1000 {{ __('characters') }}
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

@section('css')
<style>
    .chat-messages {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
    }

    .message-item {
        margin-bottom: 15px;
        animation: fadeInUp 0.3s ease-in-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .message-content {
        max-width: 80%;
        padding: 12px 16px;
        border-radius: 18px;
        position: relative;
        word-wrap: break-word;
    }

    .message-own .message-content {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 4px;
    }

    .message-other .message-content {
        background: white;
        color: #333;
        margin-right: auto;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .message-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
        font-size: 0.85rem;
    }

    .message-author {
        font-weight: 600;
    }

    .message-time {
        opacity: 0.8;
        font-size: 0.75rem;
    }

    .message-text {
        line-height: 1.4;
        margin-bottom: 5px;
    }

    .message-status {
        text-align: right;
        font-size: 0.7rem;
    }

    .message-own .message-status {
        text-align: left;
    }

    .badge-sm {
        font-size: 0.65rem;
        padding: 2px 6px;
    }

    .chat-controls {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .input-group {
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .form-control {
        border: none;
        padding: 12px 20px;
        resize: none;
    }

    .form-control:focus {
        box-shadow: none;
        border: none;
    }

    .btn-primary {
        border-radius: 0 25px 25px 0;
        padding: 12px 20px;
        border: none;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        transform: none;
    }

    .form-text {
        margin-top: 5px;
        font-size: 0.8rem;
    }

    /* Dark mode support */
    .dark-mode .chat-messages {
        background: #2d3748;
    }

    .dark-mode .message-other .message-content {
        background: #4a5568;
        color: #e2e8f0;
    }

    .dark-mode .form-control {
        background: #4a5568;
        color: #e2e8f0;
    }

    .dark-mode .form-control::placeholder {
        color: #a0aec0;
    }

    /* Typing indicator */
    .typing-indicator {
        padding: 10px 15px;
        color: #666;
        font-style: italic;
        font-size: 0.9rem;
    }

    /* Message animations */
    .message-item.new-message {
        animation: slideInRight 0.5s ease-out;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .message-content {
            max-width: 90%;
        }
        
        .chat-messages {
            height: 300px;
        }
    }

    /* Loading state */
    .sending {
        opacity: 0.7;
        pointer-events: none;
    }

    .sending .btn-primary {
        background: #6c757d;
    }
</style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            const messagesContainer = $('#messages');
            const messageForm = $('#messageForm');
            const messageInput = $('#message');
            const sendButton = $('#sendButton');
            const charCount = $('#charCount');
            const unreadCount = $('#unread-count');
            
            let isTyping = false;
            let typingTimer;

            // Auto-scroll to bottom
            function scrollToBottom() {
                messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
            }

            // Update character count
            messageInput.on('input', function() {
                const length = $(this).val().length;
                charCount.text(length);
                
                if (length > 900) {
                    charCount.addClass('text-warning');
                } else {
                    charCount.removeClass('text-warning');
                }
                
                if (length > 1000) {
                    charCount.addClass('text-danger');
                } else {
                    charCount.removeClass('text-danger');
                }
            });

            // Handle form submission
            messageForm.submit(function(e) {
                e.preventDefault();

                const message = messageInput.val().trim();
                if (!message) return;

                const ticketId = $(this).data('ticket-id');
                if (!ticketId) {
                    console.error('Ticket ID is missing.');
                    alert('Unable to send the message. Ticket ID is missing.');
                    return;
                }

                // Disable form and show loading state
                sendButton.prop('disabled', true).addClass('sending');
                messageForm.addClass('sending');

                $.ajax({
                    type: "POST",
                    url: "/support/" + ticketId + "/messages",
                    data: {
                        message: message,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Add new message to chat
                            const newMessageHtml = response.html;
                            messagesContainer.append(newMessageHtml);
                            
                            // Clear input and reset character count
                            messageInput.val('');
                            charCount.text('0');
                            
                            // Scroll to bottom
                            scrollToBottom();
                            
                            // Update unread count
                            updateUnreadCount();
                            
                            // Show success feedback
                            showNotification('Message sent successfully!', 'success');
                        } else {
                            showNotification('Failed to send message. Please try again.', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        showNotification('An error occurred while sending the message.', 'error');
                    },
                    complete: function() {
                        // Re-enable form
                        sendButton.prop('disabled', false).removeClass('sending');
                        messageForm.removeClass('sending');
                    }
                });
            });

            // Update unread count
            function updateUnreadCount() {
                $.ajax({
                    url: '/notifications/unread-count',
                    method: 'GET',
                    success: function(response) {
                        if (response.count > 0) {
                            unreadCount.text(response.count + ' {{ __("unread") }}');
                            unreadCount.show();
                        } else {
                            unreadCount.hide();
                        }
                    }
                });
            }

            // Show notification
            function showNotification(message, type) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const alertHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                // Insert at the top of the content
                $('.content').prepend(alertHtml);
                
                // Auto-dismiss after 5 seconds
                setTimeout(() => {
                    $('.alert').fadeOut();
                }, 5000);
            }

            // Mark messages as read when viewed
            function markMessagesAsRead() {
                const unreadMessages = $('.message-item:not(.message-own) .badge-warning');
                if (unreadMessages.length > 0) {
                    const messageIds = [];
                    unreadMessages.each(function() {
                        const messageItem = $(this).closest('.message-item');
                        const messageId = messageItem.data('message-id');
                        if (messageId) {
                            messageIds.push(messageId);
                        }
                    });

                    if (messageIds.length > 0) {
                        $.ajax({
                            url: '/support/{{ $ticket->id }}/mark-read',
                            method: 'POST',
                            data: {
                                message_ids: messageIds,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function() {
                                unreadMessages.removeClass('badge-warning').addClass('badge-success').text('{{ __("Read") }}');
                                updateUnreadCount();
                            }
                        });
                    }
                }
            }

            // Check for new messages periodically
            function checkForNewMessages() {
                $.ajax({
                    url: '/support/{{ $ticket->id }}/messages/latest',
                    method: 'GET',
                    success: function(response) {
                        if (response.messages && response.messages.length > 0) {
                            response.messages.forEach(function(message) {
                                // Check if message already exists
                                if (!$(`[data-message-id="${message.id}"]`).length) {
                                    const messageHtml = createMessageHtml(message);
                                    messagesContainer.append(messageHtml);
                                    scrollToBottom();
                                }
                            });
                            updateUnreadCount();
                        }
                    }
                });
            }

            // Create message HTML
            function createMessageHtml(message) {
                const isOwn = message.user_id == {{ Auth::id() }};
                const messageClass = isOwn ? 'message-own' : 'message-other';
                const author = isOwn ? '{{ __("You") }}' : message.user_name;
                const readStatus = message.read_at ? 'badge-success' : 'badge-warning';
                const readText = message.read_at ? '{{ __("Read") }}' : '{{ __("Unread") }}';

                return `
                    <div class="message-item ${messageClass} new-message" data-message-id="${message.id}">
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-author">${author}</span>
                                <span class="message-time">${message.created_at}</span>
                            </div>
                            <div class="message-text">${message.message}</div>
                            <div class="message-status">
                                <span class="badge ${readStatus} badge-sm">${readText}</span>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Initialize
            scrollToBottom();
            updateUnreadCount();
            markMessagesAsRead();

            // Check for new messages every 10 seconds
            setInterval(checkForNewMessages, 10000);

            // Mark messages as read when user scrolls to bottom
            messagesContainer.on('scroll', function() {
                const isAtBottom = $(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 10;
                if (isAtBottom) {
                    markMessagesAsRead();
                }
            });

            // Close ticket functionality for admins
            $('#closeTicketBtn').click(function() {
                let ticketId = "{{ $ticket->id }}";

                if (confirm('{{ __("Are you sure you want to close this ticket?") }}')) {
                    $.ajax({
                        type: "POST",
                        url: "/support/" + ticketId + "/close",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $('#ticket-status').text('{{ __("Closed") }}');
                            $('#closeTicketBtn').remove();
                            showNotification('{{ __("Ticket has been successfully closed.") }}', 'success');
                            
                            // Disable message form
                            messageForm.html(`
                                <div class="alert alert-warning">
                                    {{ __('adminlte.ticket_closed_cannot_send_messages') }}
                                </div>
                            `);
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            showNotification('{{ __("An error occurred while closing the ticket.") }}', 'error');
                        }
                    });
                }
            });
        });
    </script>
@stop
