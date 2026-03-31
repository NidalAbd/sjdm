<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionApiController extends Controller
{
    public function index(Request $request)
    {
        Log::info('API: Admin Transactions index requested', ['admin_id' => $request->user()?->id]);

        $query = Transaction::with(['user', 'paymentMethod']);

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
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

        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        $transactions = $query->latest()->paginate($request->get('per_page', 15));

        // Stats
        $stats = [
            'total' => Transaction::count(),
            'pending' => Transaction::where('status', 'pending')->count(),
            'completed' => Transaction::where('status', 'completed')->count(),
            'failed' => Transaction::where('status', 'failed')->count(),
            'cancelled' => Transaction::where('status', 'cancelled')->count(),
            'total_credits' => Transaction::where('type', 'credit')->where('status', 'completed')->sum('amount'),
            'total_debits' => Transaction::where('type', 'debit')->where('status', 'completed')->sum('amount'),
            'pending_amount' => Transaction::where('status', 'pending')->sum('amount'),
        ];

        return response()->json([
            'transactions' => $transactions->items(),
            'stats' => $stats,
            'pagination' => [
                'total' => $transactions->total(),
                'per_page' => $transactions->perPage(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
            ],
        ]);
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'paymentMethod']);

        return response()->json(['transaction' => $transaction]);
    }

    public function store(Request $request)
    {
        Log::info('API: Admin Transaction create requested', ['admin_id' => $request->user()?->id]);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:credit,debit',
            'description' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:50',
        ]);

        $user = User::findOrFail($request->user_id);

        // For debit transactions, check balance
        if ($request->type === 'debit' && $user->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient user balance'], 422);
        }

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'type' => $request->type,
            'status' => 'completed',
            'payment_method' => $request->payment_method ?? 'admin',
            'description' => $request->description ?? ($request->type === 'credit' ? 'Balance added by admin' : 'Balance deducted by admin'),
        ]);

        // Update user balance
        if ($request->type === 'credit') {
            $user->balance += $request->amount;
        } else {
            $user->balance -= $request->amount;
        }
        $user->save();

        return response()->json([
            'message' => 'Transaction created successfully',
            'transaction' => $transaction->load('user'),
            'new_balance' => $user->balance,
        ]);
    }

    public function approve(Request $request, Transaction $transaction)
    {
        Log::info('API: Admin Transaction approve requested', ['admin_id' => $request->user()?->id, 'transaction_id' => $transaction->id]);

        if ($transaction->status !== 'pending') {
            return response()->json(['message' => 'Transaction is not pending'], 422);
        }

        $user = $transaction->user;

        // Update user balance for credit transactions
        if ($transaction->type === 'credit') {
            $user->balance += $transaction->amount;
            $user->save();
        }

        $transaction->status = 'completed';
        $transaction->processed_at = now();
        $transaction->save();

        return response()->json([
            'message' => 'Transaction approved',
            'transaction' => $transaction->load('user'),
            'new_balance' => $user->balance,
        ]);
    }

    public function reject(Request $request, Transaction $transaction)
    {
        Log::info('API: Admin Transaction reject requested', ['admin_id' => $request->user()?->id, 'transaction_id' => $transaction->id]);

        if ($transaction->status !== 'pending') {
            return response()->json(['message' => 'Transaction is not pending'], 422);
        }

        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $transaction->status = 'failed';
        $transaction->description = $transaction->description . ' | Rejected: ' . ($request->reason ?? 'No reason provided');
        $transaction->processed_at = now();
        $transaction->save();

        return response()->json([
            'message' => 'Transaction rejected',
            'transaction' => $transaction->load('user'),
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'sometimes|in:pending,completed,failed,cancelled',
            'description' => 'sometimes|string|max:255',
        ]);

        $oldStatus = $transaction->status;
        $newStatus = $request->status ?? $oldStatus;

        // Handle balance changes when status changes
        if ($oldStatus !== $newStatus) {
            $user = $transaction->user;

            // If going from completed to something else (for credit)
            if ($oldStatus === 'completed' && $transaction->type === 'credit') {
                $user->balance -= $transaction->amount;
                $user->save();
            }

            // If going to completed (for credit)
            if ($newStatus === 'completed' && $oldStatus !== 'completed' && $transaction->type === 'credit') {
                $user->balance += $transaction->amount;
                $user->save();
            }

            $transaction->status = $newStatus;
            if ($newStatus === 'completed') {
                $transaction->processed_at = now();
            }
        }

        if ($request->has('description')) {
            $transaction->description = $request->description;
        }

        $transaction->save();

        return response()->json([
            'message' => 'Transaction updated',
            'transaction' => $transaction->load('user'),
        ]);
    }

    public function destroy(Transaction $transaction)
    {
        // If completed credit transaction, deduct from user balance
        if ($transaction->status === 'completed' && $transaction->type === 'credit') {
            $user = $transaction->user;
            $user->balance -= $transaction->amount;
            $user->save();
        }

        $transaction->delete();

        return response()->json([
            'message' => 'Transaction deleted',
        ]);
    }
}
