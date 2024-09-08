<?php

namespace App\Http\Controllers;

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

        // Create Stripe Checkout session
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
            'success_url' => route('checkout.success'),
            'cancel_url' => route('checkout.cancel'),
        ]);

        // Redirect to Stripe Checkout
        return redirect($session->url, 303);
    }

    /**
     * Handle successful payment.
     */
    public function success(Request $request)
    {
        $user = Auth::user();

        // Retrieve the amount from the session
        $amount = session('amount');

        // Check if the amount exists in the session
        if (!$amount) {
            return redirect()->route('transactions.index')->with('error', 'Amount not found in session.');
        }

        // Update user's balance
        $user->balance += $amount;
        $user->save();

        // Create a new transaction record
        $transaction = $user->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'currency' => 'USD',
            'status' => 'completed',
        ]);

        // Send a notification to the user about the successful transaction
        $user->notify(new TransactionNotification($transaction));

        // Clear the amount from the session to avoid reuse
        session()->forget('amount');

        // Redirect back to the transactions page with a success message
        return redirect()->route('transactions.index')->with('success', 'Payment successful! Balance added.');
    }

    /**
     * Handle canceled payment.
     */
    public function cancel()
    {
        return redirect()->route('transactions.index')->with('error', 'Payment was canceled.');
    }
}
