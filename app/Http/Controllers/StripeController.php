<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $user = Auth::user();
        $amount = $request->input('amount');

        // Store the amount in the session
        session(['amount' => $amount]);

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

        return redirect($session->url, 303);
    }


    public function success(Request $request)
    {
        $user = Auth::user();

        // Retrieve the amount from the session
        $amount = session('amount');

        if (!$amount) {
            return redirect()->route('transactions.index')->with('error', 'Amount not found in session.');
        }

        // Update user balance
        $user->balance += $amount;
        $user->save();

        // Save transaction
        $user->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'currency' => 'USD',
            'status' => 'completed',
        ]);

        // Clear the amount from the session
        session()->forget('amount');

        return redirect()->route('transactions.index')->with('success', 'Payment successful! Balance added.');
    }


    public function cancel()
    {
        return redirect()->route('transactions.index')->with('error', 'Payment was canceled.');
    }
}

