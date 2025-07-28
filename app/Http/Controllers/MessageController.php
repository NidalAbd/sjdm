<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\SupportTicket;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function store(Request $request, SupportTicket $ticket)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            Log::error('Validation Error:', $validator->errors()->toArray());
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Determine the sender's role
            $senderRole = Auth::user()->hasRole('admin') ? 'admin' : 'user';

            // Create the message
            $message = Message::create([
                'support_ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'message' => $request->message,
                'sender_role' => $senderRole,  // Save the sender's role
            ]);

            // Determine the recipient (if the sender is the user, notify the admin; otherwise, notify the user)
            if ($senderRole === 'admin') {
                // If the admin sends the message, notify the user
                $recipient = $ticket->user;
            } else {
                // If the user sends the message, notify the admin (make sure you have an admin relation or field)
                $recipient = $ticket->admin;  // Make sure that $ticket->admin returns the correct admin user
            }

            // Ensure the recipient is not null before sending the notification
            if ($recipient) {
                $recipient->notify(new NewMessageNotification($message));
            } else {
                Log::error('Recipient is null. Unable to send notification.');
            }

            // Mark all notifications related to this support ticket as read
            Auth::user()->unreadNotifications
                ->where('data.support_ticket_id', $ticket->id)
                ->markAsRead();

            // Return success response with the new message HTML
            return response()->json([
                'status' => 'success',
                'html' => view('support.partials.message', compact('message'))->render()
            ]);
        } catch (\Exception $e) {
            Log::error('Exception in MessageController@store:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while sending the message. Please try again.',
                'error_detail' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAsRead(Request $request, SupportTicket $ticket)
    {
        try {
            $messageIds = $request->input('message_ids', []);
            
            if (empty($messageIds)) {
                return response()->json(['status' => 'error', 'message' => 'No message IDs provided'], 400);
            }

            // Mark messages as read
            Message::whereIn('id', $messageIds)
                ->where('support_ticket_id', $ticket->id)
                ->where('user_id', '!=', Auth::id()) // Only mark messages from other users
                ->update(['read_at' => now()]);

            return response()->json(['status' => 'success', 'message' => 'Messages marked as read']);
        } catch (\Exception $e) {
            Log::error('Error marking messages as read: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to mark messages as read'], 500);
        }
    }

    public function getLatestMessages(Request $request, SupportTicket $ticket)
    {
        try {
            $lastMessageId = $request->input('last_message_id', 0);
            
            $messages = Message::where('support_ticket_id', $ticket->id)
                ->where('id', '>', $lastMessageId)
                ->with('user:id,name')
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'user_id' => $message->user_id,
                        'user_name' => $message->user->name,
                        'created_at' => $message->created_at->diffForHumans(),
                        'read_at' => $message->read_at,
                        'sender_role' => $message->sender_role
                    ];
                });

            return response()->json([
                'status' => 'success',
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting latest messages: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to get latest messages'], 500);
        }
    }
}
