<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class TransactionApiController extends Controller
{
    public function index(Request $request)
    {
        Log::info('API: Transactions index requested', ['user_id' => $request->user()?->id]);
        $user = $request->user();
        $query = Transaction::query();

        // Admin can see all transactions, users see only their own
        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        } else {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        }

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'transactions' => $transactions->items(),
            'pagination' => [
                'total' => $transactions->total(),
                'per_page' => $transactions->perPage(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
            ],
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:5',
            'payment_method' => 'required|string',
        ]);

        $user = $request->user();
        $amount = $request->amount;

        // Create pending transaction
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'credit',
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'description' => 'Balance deposit',
        ]);

        // Handle different payment methods
        if ($request->payment_method === 'stripe') {
            return $this->createStripeCheckout($transaction, $amount);
        }

        // For other payment methods, return the transaction for manual processing
        return response()->json([
            'transaction' => $transaction,
            'message' => 'Transaction created. Please complete the payment.',
        ]);
    }

    protected function createStripeCheckout(Transaction $transaction, $amount)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Balance Deposit',
                    ],
                    'unit_amount' => $amount * 100, // Stripe uses cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success', $transaction->id),
            'cancel_url' => route('checkout.cancel', $transaction->id),
            'metadata' => [
                'transaction_id' => $transaction->id,
            ],
        ]);

        $transaction->transaction_id = $session->id;
        $transaction->save();

        return response()->json([
            'checkout_url' => $session->url,
            'transaction' => $transaction,
        ]);
    }
}
