<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Notifications\TransactionNotification;

class StripeController extends Controller
{
    /**
     * Handle the checkout process using Stripe.
     */
    public function checkout(Request $request)
    {
        // Get Stripe payment method configuration
        $stripeMethod = PaymentMethod::where('slug', 'stripe')->where('is_active', true)->first();
        
        if (!$stripeMethod) {
            return redirect()->route('transactions.index')->with('error', 'Stripe payment method is not available.');
        }

        // Set Stripe API key from payment method configuration
        $apiCredentials = $stripeMethod->api_credentials;
        $stripeSecretKey = $apiCredentials['api_secret'] ?? env('STRIPE_SECRET');
        Stripe::setApiKey($stripeSecretKey);

        $user = Auth::user();
        $amount = $request->input('amount');

        // Check if we have an existing transaction from the TransactionController
        $transactionId = session('transaction_id');
        $transaction = null;
        
        if ($transactionId) {
            $transaction = Transaction::find($transactionId);
        }

        // If no existing transaction, create a new one
        if (!$transaction) {
            // Validate the amount against payment method limits (minimum $10)
            $minAmount = max(10, $stripeMethod->min_amount);
            $request->validate([
                'amount' => [
                    'required',
                    'numeric',
                    "min:{$minAmount}",
                    $stripeMethod->max_amount ? "max:{$stripeMethod->max_amount}" : 'numeric',
                ],
            ], [
                'amount.min' => __('adminlte.minimum_amount_10'),
            ]);

            // Create a new transaction
            $transaction = $user->transactions()->create([
                'payment_method_id' => $stripeMethod->id,
                'type' => 'credit',
                'amount' => $amount,
                'currency' => $stripeMethod->currency,
                'status' => 'created', // Initial status
            ]);
        }

        // Calculate processing fees
        $processingFee = $stripeMethod->calculateFee($amount);
        $totalAmount = $amount + $processingFee;

        // Store the amount in the session for use in the success method
        session(['amount' => $amount, 'payment_method_id' => $stripeMethod->id]);

        // Create Stripe Checkout session
        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($stripeMethod->currency),
                        'product_data' => [
                            'name' => 'Add Balance to ' . $user->name . ($processingFee > 0 ? " (includes $" . number_format($processingFee, 2) . " processing fee)" : ''),
                        ],
                        'unit_amount' => $totalAmount * 100, // Stripe expects the amount in cents (including fees)
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('checkout.success', ['transaction_id' => $transaction->id]),
                'cancel_url' => route('checkout.cancel', ['transaction_id' => $transaction->id]),
            ]);

            // Update the transaction status to 'started'
            $transaction->update(['status' => 'started']);

            // Clear the session transaction_id since we're using this transaction
            session()->forget('transaction_id');

            // Redirect to Stripe Checkout
            return redirect($session->url, 303);

        } catch (\Exception $e) {
            // If the session creation fails, log and notify the user
            $transaction->update(['status' => 'failed']);
            $user->notify(new TransactionNotification($transaction));
            return redirect()->route('transactions.index')->with('error', 'Could not create Stripe session. Please try again.');
        }
    }

    /**
     * Handle successful payment.
     */
    public function success(Request $request, $transaction_id)
    {
        $user = Auth::user();
        $amount = session('amount');
        $paymentMethodId = session('payment_method_id');

        // Check if the amount and payment method exist in the session
        if (!$amount || !$paymentMethodId) {
            return redirect()->route('transactions.index')->with('error', 'Session data not found.');
        }

        // Find the transaction and payment method
        $transaction = Transaction::findOrFail($transaction_id);
        $paymentMethod = PaymentMethod::findOrFail($paymentMethodId);

        // Calculate processing fee and profit
        $processingFee = $paymentMethod->calculateFee($amount);
        $profit = $amount - $processingFee; // User gets the amount, we pay the processing fee

        // Update user's balance (only the requested amount, not including fees)
        $user->balance += $amount;
        $user->save();

        // Update transaction status to 'completed'
        $transaction->update([
            'status' => 'completed',
            'api_cost' => $processingFee, // Store actual processing fee
            'profit' => $profit, // Calculate net profit after fees
        ]);

        // Notify user about the completed transaction
        $user->notify(new TransactionNotification($transaction));

        // Clear session data
        session()->forget(['amount', 'payment_method_id']);

        return redirect()->route('transactions.index')->with('success', 'Payment successful! Balance added.');
    }

    /**
     * Handle canceled payment.
     */
    public function cancel($transaction_id)
    {
        // Find the transaction
        $transaction = Transaction::findOrFail($transaction_id);

        // Update the transaction status to 'canceled'
        $transaction->update(['status' => 'canceled']);

        // Notify user about the canceled transaction
        $transaction->user->notify(new TransactionNotification($transaction));

        // Clear session data
        session()->forget(['amount', 'payment_method_id']);

        // Redirect back to a page with a message to complete the transaction
        return redirect()->route('transactions.complete', ['transaction_id' => $transaction->id])
            ->with('error', 'Your payment was canceled. You can complete your transaction here.');
    }

    /**
     * Handle failed payment (optional, depending on how Stripe failure is handled).
     */
    public function fail($transaction_id)
    {
        // Find the transaction
        $transaction = Transaction::findOrFail($transaction_id);

        // Update the transaction status to 'failed'
        $transaction->update(['status' => 'failed']);

        // Notify the user about the failed transaction
        $transaction->user->notify(new TransactionNotification($transaction));

        return redirect()->route('transactions.index')->with('error', 'Payment failed. Please try again.');
    }


    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $event = null;

        try {
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid payload', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // contains a StripePaymentIntent
                $transaction = Transaction::where('transaction_id', $paymentIntent->id)->first();
                if ($transaction) {
                    $transaction->update(['status' => 'completed']);
                }
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object; // contains a StripePaymentIntent
                $transaction = Transaction::where('transaction_id', $paymentIntent->id)->first();
                if ($transaction) {
                    $transaction->update(['status' => 'failed']);
                    $transaction->user->notify(new TransactionNotification($transaction));
                }
                break;
            // Handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        return response('Webhook received', 200);
    }


    public function completeTransaction($transaction_id)
    {
        // Find the transaction
        $transaction = Transaction::findOrFail($transaction_id);

        // Pass the transaction data to the view to pre-fill the amount
        return view('transactions.complete', compact('transaction'));
    }



}
