<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class NotificationController extends Controller
{
    public function fetchLatest()
    {
        $notifications = Auth::user()->unreadNotifications()->limit(10)->get();
        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead(); // Mark the notification as read
        }

        return response()->json(['status' => 'success']);
    }

    // In app/Http/Controllers/NotificationController.php
    // NotificationController.php
    public function loadMore(Request $request)
    {
        $offset = $request->input('offset', 0); // Get the offset from the request
        $notifications = Auth::user()->unreadNotifications()->skip($offset)->take(5)->get(); // Fetch the next 5 notifications

        $response = [];

        foreach ($notifications as $notification) {
            $url = '#';
            if (isset($notification->data['transaction_id'])) {
                $url = route('transactions.show', $notification->data['transaction_id']);
            } elseif (isset($notification->data['ticket_id'])) {
                $url = route('support.show', $notification->data['ticket_id']);
            } elseif (isset($notification->data['order_id'])) {
                $url = route('orders.show', $notification->data['order_id']);
            }

            $response[] = [
                'id' => $notification->id,
                'message' => $notification->data['message'] ?? 'New Notification',
                'created_at' => $notification->created_at->diffForHumans(),
                'url' => $url
            ];
        }

        return response()->json([
            'notifications' => $response,
            'count' => $notifications->count(),
        ]);
    }



    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(10);
        return view('notifications.index', compact('notifications'));
    }
}

