<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function fetchLatest()
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $user = Auth::user();
            
            // Check if user is banned
            if ($user->status === 'banned') {
                return response()->json(['error' => 'Account banned'], 403);
            }

            $notifications = $user->unreadNotifications()
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $this->getNotificationType($notification),
                        'title' => $this->getNotificationTitle($notification),
                        'message' => $this->getNotificationMessage($notification),
                        'url' => $this->getNotificationUrl($notification),
                        'icon' => $this->getNotificationIcon($notification),
                        'created_at' => $notification->created_at->toISOString(),
                        'data' => $notification->data
                    ];
                });

            return response()->json($notifications);
        } catch (\Exception $e) {
            Log::error('Error fetching latest notifications: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch notifications'], 500);
        }
    }

    public function markAsRead($id)
    {
        try {
            $notification = Auth::user()->notifications()->find($id);

            if ($notification) {
                $notification->markAsRead();
                return response()->json(['status' => 'success', 'message' => 'Notification marked as read']);
            }

            return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to mark notification as read'], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            Auth::user()->unreadNotifications->markAsRead();
            return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to mark notifications as read'], 500);
        }
    }

    public function loadMore(Request $request)
    {
        try {
            $offset = $request->input('offset', 0);
            $notifications = Auth::user()->notifications()
                ->skip($offset)
                ->take(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $this->getNotificationType($notification),
                        'title' => $this->getNotificationTitle($notification),
                        'message' => $this->getNotificationMessage($notification),
                        'url' => $this->getNotificationUrl($notification),
                        'icon' => $this->getNotificationIcon($notification),
                        'created_at' => $notification->created_at->toISOString(),
                        'read_at' => $notification->read_at,
                        'data' => $notification->data
                    ];
                });

            return response()->json($notifications);
        } catch (\Exception $e) {
            Log::error('Error loading more notifications: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load notifications'], 500);
        }
    }

    public function getUnreadCount()
    {
        try {
            $count = Auth::user()->unreadNotifications()->count();
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }

    public function deleteNotification($id)
    {
        try {
            $notification = Auth::user()->notifications()->find($id);

            if ($notification) {
                $notification->delete();
                return response()->json(['status' => 'success', 'message' => 'Notification deleted']);
            }

            return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete notification'], 500);
        }
    }

    public function clearAll()
    {
        try {
            Auth::user()->notifications()->delete();
            return response()->json(['success' => true, 'message' => 'All notifications cleared']);
        } catch (\Exception $e) {
            Log::error('Error clearing all notifications: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to clear notifications'], 500);
        }
    }

    /**
     * Get notification type based on data
     */
    private function getNotificationType($notification)
    {
        $data = $notification->data;

        if (isset($data['support_ticket_id'])) {
            return 'message';
        } elseif (isset($data['ticket_id'])) {
            return 'ticket';
        } elseif (isset($data['transaction_id'])) {
            return 'transaction';
        } elseif (isset($data['order_id'])) {
            return 'order';
        } elseif (isset($data['points'])) {
            return 'points';
        } elseif (isset($data['user_id'])) {
            return 'user';
        }

        return 'general';
    }

    /**
     * Get notification title
     */
    private function getNotificationTitle($notification)
    {
        $data = $notification->data;

        if (isset($data['support_ticket_id'])) {
            return __('New Message');
        } elseif (isset($data['ticket_id'])) {
            return __('Support Ticket');
        } elseif (isset($data['transaction_id'])) {
            return __('Transaction Update');
        } elseif (isset($data['order_id'])) {
            return __('Order Update');
        } elseif (isset($data['points'])) {
            return __('Points Redeemed');
        } elseif (isset($data['user_id'])) {
            return __('User Update');
        }

        return __('General Notification');
    }

    /**
     * Get notification message
     */
    private function getNotificationMessage($notification)
    {
        $data = $notification->data;

        if (isset($data['message_content'])) {
            return $data['message_content'];
        } elseif (isset($data['message'])) {
            return $data['message'];
        } elseif (isset($data['subject'])) {
            return $data['subject'];
        }

        return __('New notification received');
    }

    /**
     * Get notification URL
     */
    private function getNotificationUrl($notification)
    {
        $data = $notification->data;

        if (isset($data['support_ticket_id'])) {
            return route('support.show', $data['support_ticket_id']);
        } elseif (isset($data['ticket_id'])) {
            return route('support.show', $data['ticket_id']);
        } elseif (isset($data['transaction_id'])) {
            return route('transactions.show', $data['transaction_id']);
        } elseif (isset($data['order_id'])) {
            return route('orders.show', $data['order_id']);
        } elseif (isset($data['user_id'])) {
            return route('users.show', $data['user_id']);
        }

        return '#';
    }

    /**
     * Get notification icon
     */
    private function getNotificationIcon($notification)
    {
        $data = $notification->data;

        if (isset($data['support_ticket_id'])) {
            return 'fas fa-envelope';
        } elseif (isset($data['ticket_id'])) {
            return 'fas fa-ticket-alt';
        } elseif (isset($data['transaction_id'])) {
            return 'fas fa-dollar-sign';
        } elseif (isset($data['order_id'])) {
            return 'fas fa-shopping-cart';
        } elseif (isset($data['points'])) {
            return 'fas fa-coins';
        } elseif (isset($data['user_id'])) {
            return 'fas fa-user';
        }

        return 'fas fa-info-circle';
    }
}

