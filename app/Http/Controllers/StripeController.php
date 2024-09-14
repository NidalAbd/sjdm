<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
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
        // Set Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $user = Auth::user();
        $amount = $request->input('amount');

        // Validate the amount input to avoid invalid or malicious input
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        // Store the amount in the session for use in the success method
        session(['amount' => $amount]);

        // Create a 'created' transaction in the database
        $transaction = $user->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'currency' => 'USD',
            'status' => 'created', // Initial status
            'api_cost' => 0, // Adjust as needed
            'profit' => 0, // Adjust as needed
        ]);

        // Create Stripe Checkout session
        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Add Balance to ' . $user->name,
                        ],
                        'unit_amount' => $amount * 100, // Stripe expects the amount in cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('checkout.success', ['transaction_id' => $transaction->id]),
                'cancel_url' => route('checkout.cancel', ['transaction_id' => $transaction->id]),
            ]);

            // Update the transaction status to 'started'
            $transaction->update(['status' => 'started']);

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

        // Check if the amount exists in the session
        if (!$amount) {
            return redirect()->route('transactions.index')->with('error', 'Amount not found in session.');
        }

        // Find the transaction
        $transaction = Transaction::findOrFail($transaction_id);

        // Update user's balance
        $user->balance += $amount;
        $user->save();

        // Update transaction status to 'completed'
        $transaction->update([
            'status' => 'completed',
            'api_cost' => 0, // Set actual API cost if needed
            'profit' => $amount, // Set the profit (amount - api_cost)
        ]);

        // Notify user about the completed transaction
        $user->notify(new TransactionNotification($transaction));

        // Clear the amount from the session
        session()->forget('amount');

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
