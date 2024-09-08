<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\TicketStatus;
use App\Models\User;
use App\Notifications\TicketNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::query();

        // Allow admins to view all tickets
        if (!Auth::user()->hasRole('admin')) {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('search')) {
            $query->where('subject', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        $tickets = $query->paginate(5);
        $statuses = TicketStatus::all();

        return view('support.index', compact('tickets', 'statuses'));
    }

    public function create(Order $order)
    {
        return view('support.create', compact('order'));
    }

    public function store(Request $request)
    {
        // Custom validation messages
        $messages = [
            'ticketable_id.required' => 'The ticketable ID field is required and cannot be empty.',
            'ticketable_type.required' => 'The ticketable type field is required and cannot be empty.',
            'ticketable_type.in' => 'The ticketable type must be either Order or Transaction.',
            'subject.required' => 'The subject field is required and cannot be empty.',
            'message.required' => 'The message field is required and cannot be empty.',
            'type.required' => 'The type field is required and cannot be empty.',
            'type.in' => 'The type must be either order or payment.',
            'subtype.in' => 'The subtype must be one of the following: refund, acceleration, cancel, failed_payment, refund_request, payment_dispute, chargeback, invoice_request.',
        ];

        // Validation logic
        $validator = Validator::make($request->all(), [
            'ticketable_id' => 'required|integer',
            'ticketable_type' => 'required|string|in:App\Models\Order,App\Models\Transaction',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|in:order,transaction',
            'subtype' => 'nullable|string|in:refund,acceleration,cancel,failed_payment,refund_request,payment_dispute,chargeback,invoice_request'
        ], $messages);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create the support ticket
        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'ticketable_id' => $request->ticketable_id,
            'ticketable_type' => $request->ticketable_type,
            'subject' => $request->subject,
            'message' => $request->message,
            'status_id' => TicketStatus::where('name', 'Open')->first()->id,
            'type' => $request->type,
            'subtype' => $request->subtype,
        ]);

        // Notify the user about the new ticket
        auth()->user()->notify(new TicketNotification($ticket));

        // Redirect the user to the support page with success message
        return redirect()->route('support.index')->with('success', 'Ticket created successfully!');
    }

    public function show(SupportTicket $ticket)
    {
        $user = Auth::user();

        // Log the user's roles and permissions
        Log::info('User Roles: ', $user->getRoleNames()->toArray());
        Log::info('User Permissions: ', $user->getAllPermissions()->pluck('name')->toArray());

        // Check if the current user is authorized to view this ticket
        if ($user->cannot('view', $ticket)) {
            abort(403);
        }
        Log::info('Ticket Details:', $ticket->toArray());

        // Mark all notifications as read when the ticket is viewed
        $user->unreadNotifications
            ->where('data.support_ticket_id', $ticket->id)
            ->markAsRead();

        return view('support.show', compact('ticket'));
    }

    public function edit(SupportTicket $ticket)
    {
        // Get all statuses to allow status updates
        $statuses = TicketStatus::all();

        return view('support.edit', compact('ticket', 'statuses'));
    }

    public function update(Request $request, SupportTicket $ticket)
    {
        // Ensure only the owner or authorized users can update the ticket
        $this->authorize('update', $ticket);

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'status_id' => 'required|exists:ticket_statuses,id',
        ]);

        $ticket->update($request->all());

        return redirect()->route('support.index')->with('success', 'Ticket updated successfully!');
    }

    public function destroy(SupportTicket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return redirect()->route('support.index')->with('success', 'Ticket deleted successfully!');
    }
}
