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
            'message' => 'required|string',
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
}
