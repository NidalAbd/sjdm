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
            // Log the error details for debugging
            Log::error('Validation Error:', $validator->errors()->toArray());

            // Return a JSON response with detailed error messages
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create the message
            $message = Message::create([
                'support_ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'message' => $request->message,
            ]);

            // Notify the user
            $ticket->user->notify(new NewMessageNotification($message));

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
            // Log the exception details for debugging
            Log::error('Exception in MessageController@store:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            // Return a JSON response with the exception message
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while sending the message. Please try again.',
                'error_detail' => $e->getMessage(), // Include the exception message for debugging
            ], 500);
        }
    }


}
