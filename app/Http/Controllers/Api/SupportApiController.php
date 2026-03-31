<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\Message;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupportApiController extends Controller
{
    public function index(Request $request)
    {
        Log::info('API: Support tickets index requested', ['user_id' => $request->user()?->id]);
        $user = $request->user();
        $query = SupportTicket::with(['user', 'status', 'messages' => function ($q) {
            $q->latest()->take(1);
        }]);

        // Admin can see all tickets, users see only their own
        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        } else {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        }

        // Filters - filter by status name through relationship
        if ($request->filled('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('name', $request->status);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Count unread messages
        $query->withCount(['messages as unread_count' => function ($q) use ($user) {
            $q->whereNull('read_at');
            if (!$user->hasRole('admin')) {
                // For users, count unread messages from admin
                $q->where('sender_role', 'admin');
            } else {
                // For admin, count unread messages from users
                $q->where('sender_role', '!=', 'admin');
            }
        }]);

        // Count messages
        $query->withCount('messages');

        $tickets = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'tickets' => $tickets->items(),
            'pagination' => [
                'total' => $tickets->total(),
                'per_page' => $tickets->perPage(),
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'in:low,medium,high',
            'order_id' => 'nullable|exists:orders,id',
            'transaction_id' => 'nullable|exists:transactions,id',
        ]);

        $user = $request->user();

        // Find or create open status
        $openStatus = TicketStatus::firstOrCreate(['name' => 'open']);

        // Determine ticketable type and id
        $ticketableType = 'App\\Models\\Order';
        $ticketableId = $request->order_id;
        $type = 'order';

        if ($request->filled('transaction_id')) {
            $ticketableType = 'App\\Models\\Transaction';
            $ticketableId = $request->transaction_id;
            $type = 'transaction';
        }

        // If no order or transaction, use user's latest order or a placeholder
        if (!$ticketableId) {
            $latestOrder = \App\Models\Order::where('user_id', $user->id)->latest()->first();
            if ($latestOrder) {
                $ticketableId = $latestOrder->id;
            } else {
                // Use user as ticketable if no orders exist
                $ticketableType = 'App\\Models\\User';
                $ticketableId = $user->id;
            }
        }

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $request->subject,
            'status_id' => $openStatus->id,
            'type' => $type,
            'message' => $request->message,
            'ticketable_type' => $ticketableType,
            'ticketable_id' => $ticketableId,
        ]);

        // Add the initial message
        Message::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'sender_role' => 'user',
        ]);

        return response()->json([
            'message' => 'Ticket created successfully',
            'ticket' => $ticket->load(['messages', 'status']),
        ]);
    }

    public function show(Request $request, SupportTicket $ticket)
    {
        $user = $request->user();

        // Check access
        if (!$user->hasRole('admin') && $ticket->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ticket->load(['user', 'status', 'ticketable', 'messages.user']);

        return response()->json(['ticket' => $ticket]);
    }

    public function addMessage(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = $request->user();

        // Check access
        if (!$user->hasRole('admin') && $ticket->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'sender_role' => $user->hasRole('admin') ? 'admin' : 'user',
        ]);

        // Update ticket status if needed - reopen if closed
        $closedStatus = TicketStatus::where('name', 'closed')->first();
        if ($closedStatus && $ticket->status_id === $closedStatus->id) {
            $openStatus = TicketStatus::where('name', 'open')->first();
            if ($openStatus) {
                $ticket->status_id = $openStatus->id;
                $ticket->save();
            }
        }

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message->load('user'),
        ]);
    }

    public function close(Request $request, SupportTicket $ticket)
    {
        $user = $request->user();

        // Check access
        if (!$user->hasRole('admin') && $ticket->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $closedStatus = TicketStatus::firstOrCreate(['name' => 'closed']);
        $ticket->status_id = $closedStatus->id;
        $ticket->save();

        return response()->json([
            'message' => 'Ticket closed successfully',
            'ticket' => $ticket->load('status'),
        ]);
    }

    public function markRead(Request $request, SupportTicket $ticket)
    {
        $user = $request->user();

        // Check access
        if (!$user->hasRole('admin') && $ticket->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Mark messages as read
        $query = $ticket->messages()->whereNull('read_at');
        if ($user->hasRole('admin')) {
            // Admin marks user messages as read
            $query->where('sender_role', '!=', 'admin');
        } else {
            // User marks admin messages as read
            $query->where('sender_role', 'admin');
        }
        $query->update(['read_at' => now()]);

        return response()->json(['message' => 'Messages marked as read']);
    }
}
