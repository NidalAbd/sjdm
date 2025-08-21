<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\PaymentMethod;
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

        $transactions = $query->orderBy('id', 'desc')->paginate(5);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $this->authorize('create', Transaction::class);
        
        // Get active payment methods and group by type
        $paymentMethods = PaymentMethod::active()->ordered()->get();
        
        // Debug: Log payment methods
        \Log::info('Available payment methods:', $paymentMethods->toArray());
        
        // Create payment type categories showing what's available
        $paymentTypes = [
            [
                'id' => 'cards',
                'name' => 'Credit & Debit Cards',
                'description' => 'Visa, Mastercard, American Express and other cards',
                'icon' => 'fas fa-credit-card',
                'methods' => $paymentMethods->where('type', 'credit_card')->values(),
                'available' => $paymentMethods->where('type', 'credit_card')->isNotEmpty(),
                'gateway_info' => 'Secure payment processing through our certified payment gateway'
            ],
            [
                'id' => 'crypto',
                'name' => 'Cryptocurrency',
                'description' => 'Bitcoin, USDT, Ethereum and other digital currencies',
                'icon' => 'fab fa-bitcoin',
                'methods' => $paymentMethods->where('type', 'cryptocurrency')->values(),
                'available' => $paymentMethods->where('type', 'cryptocurrency')->isNotEmpty(),
                'gateway_info' => 'Direct crypto wallet payments with instant confirmation'
            ],
            [
                'id' => 'wallets',
                'name' => 'Digital Wallets',
                'description' => 'PayPal, Apple Pay, Google Pay and other e-wallets',
                'icon' => 'fas fa-wallet',
                'methods' => $paymentMethods->where('type', 'digital_wallet')->values(),
                'available' => $paymentMethods->where('type', 'digital_wallet')->isNotEmpty(),
                'gateway_info' => 'Quick payments through your preferred digital wallet'
            ]
        ];
        
        // Filter out unavailable payment types and properly format for JavaScript
        $availablePaymentTypes = collect($paymentTypes)->filter(function ($type) {
            return $type['available'];
        })->values();
        
        return view('transactions.create', compact('availablePaymentTypes', 'paymentMethods'));
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

        // Debug: Log the request data
        \Log::info('Transaction store request:', $request->all());

        // Validate the request input
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        // Retrieve authenticated user and amount
        $user = Auth::user();
        $amount = $request->amount;
        $paymentMethodId = $request->payment_method_id;

        // Create a new transaction record
        $transaction = new Transaction([
            'type' => 'credit',
            'amount' => $amount,
            'status' => 'pending', // Start as pending
            'payment_method_id' => $paymentMethodId,
        ]);

        // Save the transaction
        $user->transactions()->save($transaction);

        // Process payment based on payment method
        return $this->processPayment($transaction, $paymentMethodId);
    }

    /**
     * Process payment based on payment method
     */
    private function processPayment($transaction, $paymentMethodId)
    {
        // Get the payment method
        $paymentMethod = PaymentMethod::find($paymentMethodId);
        
        if (!$paymentMethod) {
            $transaction->update(['status' => 'failed']);
            return redirect()->route('transactions.index')
                ->with('error', 'Payment method not found.');
        }
        
        // Process based on payment method type
        switch ($paymentMethod->type) {
            case 'credit_card':
                return $this->processCardPayment($transaction);
            case 'cryptocurrency':
                return $this->processCryptoPayment($transaction);
            case 'bank_transfer':
                return $this->processBankTransfer($transaction);
            default:
                return $this->processManualPayment($transaction);
        }
    }

    /**
     * Process credit card payment
     */
    private function processCardPayment($transaction)
    {
        // Get the payment method to check if it's Stripe
        $paymentMethod = PaymentMethod::find($transaction->payment_method_id);
        
        if (!$paymentMethod) {
            $transaction->update(['status' => 'failed']);
            return redirect()->route('transactions.index')
                ->with('error', 'Payment method not found.');
        }
        
        // If it's Stripe, redirect to Stripe checkout
        if ($paymentMethod->slug === 'stripe') {
            // Store transaction info in session for Stripe controller
            session([
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'payment_method_id' => $paymentMethod->id
            ]);
            
            // Redirect to Stripe checkout with amount parameter
            return redirect()->route('checkout', ['amount' => $transaction->amount]);
        }
        
        // For other payment methods, simulate processing
        $outcomes = ['success', 'failed', 'suspected'];
        $outcome = $outcomes[array_rand($outcomes)];
        
        switch ($outcome) {
            case 'success':
                $transaction->update([
                    'status' => 'completed',
                    'processed_at' => now(),
                ]);
                
                // Update user balance
                $transaction->user->increment('balance', $transaction->amount);
                
                return redirect()->route('transactions.show', $transaction)
                    ->with('success', 'Payment successful! Your balance has been updated.');
                
            case 'failed':
                $transaction->update([
                    'status' => 'failed',
                    'processed_at' => now(),
                ]);
                
                return redirect()->route('transactions.show', $transaction)
                    ->with('error', 'Payment failed. Please try again.');
                
            case 'suspected':
                $transaction->update([
                    'status' => 'suspected',
                    'processed_at' => now(),
                ]);
                
                return redirect()->route('transactions.show', $transaction)
                    ->with('warning', 'Payment is under review. You will be notified once processed.');
        }
    }

    /**
     * Process cryptocurrency payment
     */
    private function processCryptoPayment($transaction)
    {
        // Simulate crypto payment processing
        $transaction->update([
            'status' => 'pending',
            'processed_at' => now(),
        ]);
        
        // For crypto, usually takes longer to confirm
        return redirect()->route('transactions.show', $transaction)
            ->with('info', 'Cryptocurrency payment submitted. Please wait for blockchain confirmation.');
    }

    /**
     * Process bank transfer payment
     */
    private function processBankTransfer($transaction)
    {
        // Simulate bank transfer processing
        $transaction->update([
            'status' => 'pending',
            'processed_at' => now(),
        ]);
        
        return redirect()->route('transactions.show', $transaction)
            ->with('info', 'Bank transfer initiated. Please allow 1-3 business days for processing.');
    }

    /**
     * Process manual payment
     */
    private function processManualPayment($transaction)
    {
        // For manual payments, keep as pending for admin review
        $transaction->update([
            'status' => 'pending',
        ]);
        
        return redirect()->route('transactions.show', $transaction)
            ->with('info', 'Payment submitted for manual review. You will be notified once processed.');
    }


    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        $transaction->delete();  // Permanently delete the transaction

        return back()->with('success', 'Transaction deleted successfully.');
    }

}
