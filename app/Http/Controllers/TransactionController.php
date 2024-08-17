<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Charge;
use Stripe\Stripe;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Auth::user()->transactions()->paginate(5);
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        return view('transactions.create');
    }
    public function show($id)
    {
        $transaction = Auth::user()->transactions()->findOrFail($id);

        return view('transactions.show', compact('transaction'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        $amount = $request->amount;

        // Stripe payment
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $charge = Charge::create([
                'amount' => $amount * 100, // in cents
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
            } else {
                return redirect()->back()->with('error', 'Payment failed.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
