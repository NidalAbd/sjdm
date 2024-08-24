<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\Order;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    public function __construct()
    {
        // Apply policies to specific actions
        $this->authorizeResource(SupportTicket::class, 'ticket');
    }

    public function index(Request $request)
    {
        // Determine if the user is an admin
        $query = SupportTicket::query();

        if (!Auth::user()->hasRole('admin')) {
            // Regular users only see their own tickets
            $query->where('user_id', Auth::id());
        }

        // Apply search and filters
        if ($request->filled('search')) {
            $query->where('subject', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        // Paginate the results
        $tickets = $query->paginate(5);

        // Fetch all statuses for the filter dropdown
        $statuses = TicketStatus::all();

        return view('support.index', compact('tickets', 'statuses'));
    }


    public function create(Order $order)
    {
        return view('support.create', compact('order'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|in:order,payment', // Validate type against enum values
            'subtype' => 'nullable|string|in:refund,acceleration,cancel,failed_payment,refund_request,payment_dispute,chargeback,invoice_request' // Validate subtype against enum values
        ]);

        SupportTicket::create([
            'user_id' => Auth::id(),
            'order_id' => $request->order_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'status_id' => TicketStatus::where('name', 'Open')->first()->id,
            'type' => $request->type,
            'subtype' => $request->subtype,
        ]);

        return redirect()->route('support.index')->with('success', 'Ticket created successfully!');
    }


    public function show(SupportTicket $ticket)
    {
        return view('support.show', compact('ticket'));
    }

    public function edit(SupportTicket $ticket)
    {
        // Ensure only the owner or authorized users can edit the ticket
        $this->authorize('update', $ticket);

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
            'type' => 'required|string|in:order,payment', // Validate type
            'subtype' => 'nullable|string|in:refund,acceleration,cancel,failed_payment,refund_request,payment_dispute,chargeback,invoice_request' // Validate subtype
        ]);

        $ticket->update([
            'subject' => $request->subject,
            'message' => $request->message,
            'status_id' => $request->status_id,
            'type' => $request->type,
            'subtype' => $request->subtype,
        ]);

        return redirect()->route('support.index')->with('success', 'Ticket updated successfully!');
    }

    public function destroy(SupportTicket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return redirect()->route('support.index')->with('success', 'Ticket deleted successfully!');
    }
}
