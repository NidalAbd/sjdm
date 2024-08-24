<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $this->authorize('create', Transaction::class);

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        $amount = $request->amount;

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $charge = Charge::create([
                'amount' => $amount * 100,
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => 'Add Balance',
            ]);

            if ($charge->status === 'succeeded') {
                $transaction = new Transaction([
                    'type' => 'credit',
                    'amount' => $amount,
                    'status' => 'completed',
                ]);

                $user->balance += $amount;
                $user->transactions()->save($transaction);
                $user->save();

                return redirect()->route('transactions.index')->with('success', 'Balance added successfully.');
            }

            return redirect()->back()->with('error', 'Payment failed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


}
