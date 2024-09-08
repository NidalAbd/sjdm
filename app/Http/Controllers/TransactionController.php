<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Notifications\TransactionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Stripe\Charge;
use Stripe\Stripe;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Transaction::class, 'transaction');
    }

    public function index(Request $request)
    {
        // Using the policy method directly for authorization
        $this->authorize('viewAny', Transaction::class);

        $user = Auth::user();
        $query = Transaction::query();  // Start with a base query for all transactions

        // Check if the user is an admin
        if (!$user->hasRole('admin')) {
            // If the user is not an admin, limit to their transactions
            $query->where('user_id', $user->id);
        }

        // Refactored filtering logic for better readability
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('type', 'like', '%' . $request->search . '%')
                    ->orWhere('status', 'like', '%' . $request->search . '%')
                    ->orWhere('amount', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->paginate(5);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $this->authorize('create', Transaction::class);
        return view('transactions.create');
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        return view('transactions.show', compact('transaction'));
    }

    public function store(Request $request)
    {
        // Authorize the user to create a transaction
        $this->authorize('create', Transaction::class);

        // Validate the request input
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        // Retrieve authenticated user and amount
        $user = Auth::user();
        $amount = $request->amount;

        // Set the Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Attempt to charge the user via Stripe
            $charge = Charge::create([
                'amount' => $amount * 100,  // Convert to cents for Stripe
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => 'Add Balance',
            ]);

            // Default to failed status
            $status = 'failed';
            $message = 'Payment failed.';

            // Create a new transaction record
            $transaction = new Transaction([
                'type' => 'credit',
                'amount' => $amount,
                'status' => $status,
            ]);

            // If the charge succeeded, update the transaction status and user balance
            if ($charge->status === 'succeeded') {
                $transaction->status = 'completed';
                $user->balance += $amount;
                $message = 'Balance added successfully.';
            }

            // Save the transaction and update user balance
            $user->transactions()->save($transaction);
            $user->save();

            // Send notification to the user about the transaction status
            $user->notify(new TransactionNotification($transaction));  // Using notify() method

            // Redirect back to the transactions page with a success message
            return redirect()->route('transactions.index')->with('success', $message);

        } catch (\Exception $e) {
            // Handle exception, create a failed transaction and notify user
            $transaction = new Transaction([
                'type' => 'credit',
                'amount' => $amount,
                'status' => 'failed',
            ]);
            $user->transactions()->save($transaction);

            // Send notification to the user about the failed transaction
            $user->notify(new TransactionNotification($transaction));  // Using notify() method

            // Redirect back with the error message
            return redirect()->back()->with('error', $e->getMessage());
        }
    }



}
