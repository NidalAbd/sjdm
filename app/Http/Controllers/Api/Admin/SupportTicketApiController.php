<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\Message;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupportTicketApiController extends Controller
{
    public function index(Request $request)
    {
        Log::info('API: Admin Support Tickets index requested', ['admin_id' => $request->user()?->id]);

        $query = SupportTicket::with(['user', 'status', 'messages' => function ($q) {
            $q->latest()->take(1);
        }]);

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Filter by status name (through relationship)
        if ($request->filled('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('name', $request->status);
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Count unread user messages (messages from users that admin hasn't read)
        $query->withCount(['messages as unread_count' => function ($q) {
            $q->whereNull('read_at')->where('sender_role', '!=', 'admin');
        }]);

        // Count messages
        $query->withCount('messages');

        $tickets = $query->latest()->paginate($request->get('per_page', 15));

        // Get statuses for stats - count by status relationship
        $statuses = TicketStatus::all();
        $stats = [
            'total' => SupportTicket::count(),
            'unread' => Message::where('sender_role', '!=', 'admin')->whereNull('read_at')->count(),
        ];

        // Add counts for each status
        foreach ($statuses as $status) {
            $stats[strtolower($status->name)] = SupportTicket::where('status_id', $status->id)->count();
        }

        return response()->json([
            'tickets' => $tickets->items(),
            'stats' => $stats,
            'statuses' => $statuses,
            'pagination' => [
                'total' => $tickets->total(),
                'per_page' => $tickets->perPage(),
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
            ],
        ]);
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'status', 'messages.user', 'ticketable']);

        // Mark user messages as read
        $ticket->messages()
            ->where('sender_role', '!=', 'admin')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['ticket' => $ticket]);
    }

    public function update(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => 'sometimes|string|max:50',
            'status_id' => 'sometimes|exists:ticket_statuses,id',
            'type' => 'sometimes|string|max:50',
        ]);

        // If status name is provided, find the status_id
        if ($request->has('status')) {
            $status = TicketStatus::where('name', $request->status)->first();
            if ($status) {
                $ticket->status_id = $status->id;
            }
        }
        if ($request->has('status_id')) {
            $ticket->status_id = $request->status_id;
        }
        if ($request->has('type')) {
            $ticket->type = $request->type;
        }

        $ticket->save();

        return response()->json([
            'message' => 'Ticket updated successfully',
            'ticket' => $ticket->load(['user', 'status']),
        ]);
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        Log::info('API: Admin Support Ticket reply', ['admin_id' => $request->user()?->id, 'ticket_id' => $ticket->id]);

        $request->validate([
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'message' => $request->message,
            'sender_role' => 'admin',
        ]);

        // Update ticket status to answered
        $answeredStatus = TicketStatus::where('name', 'answered')->first();
        if ($answeredStatus) {
            $ticket->status_id = $answeredStatus->id;
            $ticket->save();
        }

        return response()->json([
            'message' => 'Reply sent successfully',
            'data' => $message->load('user'),
        ]);
    }

    public function close(SupportTicket $ticket)
    {
        $closedStatus = TicketStatus::where('name', 'closed')->first();
        if ($closedStatus) {
            $ticket->status_id = $closedStatus->id;
            $ticket->save();
        }

        return response()->json([
            'message' => 'Ticket closed',
            'ticket' => $ticket->load('status'),
        ]);
    }

    public function reopen(SupportTicket $ticket)
    {
        $openStatus = TicketStatus::where('name', 'open')->first();
        if ($openStatus) {
            $ticket->status_id = $openStatus->id;
            $ticket->save();
        }

        return response()->json([
            'message' => 'Ticket reopened',
            'ticket' => $ticket->load('status'),
        ]);
    }

    public function destroy(SupportTicket $ticket)
    {
        // Delete all messages first
        $ticket->messages()->delete();
        $ticket->delete();

        return response()->json([
            'message' => 'Ticket deleted',
        ]);
    }

    public function markAllRead(Request $request)
    {
        Message::where('sender_role', '!=', 'admin')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'All messages marked as read',
        ]);
    }

    public function bulkClose(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:support_tickets,id',
        ]);

        $closedStatus = TicketStatus::where('name', 'closed')->first();
        if (!$closedStatus) {
            return response()->json(['message' => 'Closed status not found'], 404);
        }

        $updated = SupportTicket::whereIn('id', $request->ticket_ids)
            ->update(['status_id' => $closedStatus->id]);

        return response()->json([
            'message' => "Closed {$updated} tickets",
            'updated' => $updated,
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:support_tickets,id',
        ]);

        // Delete messages first
        Message::whereIn('support_ticket_id', $request->ticket_ids)->delete();

        $deleted = SupportTicket::whereIn('id', $request->ticket_ids)->delete();

        return response()->json([
            'message' => "Deleted {$deleted} tickets",
            'deleted' => $deleted,
        ]);
    }
}
