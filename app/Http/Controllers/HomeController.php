<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Stripe\Price;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function settings()
    {
        $user = auth()->user();
        $orders = Order::where('user_id', $user->id)->get();
        $transactions = Transaction::where('user_id', $user->id)->get();

        // Fetch all referrals
        $totalReferrals = User::where('referred_by', $user->id)->get();

        // Fetch only active and verified referrals
        $verifiedActiveReferrals = User::where('referred_by', $user->id)
            ->where('status', 'active')
            ->whereNotNull('email_verified_at')
            ->get();

        return view('profile.settings', compact('user', 'orders', 'transactions', 'totalReferrals', 'verifiedActiveReferrals'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        $user = auth()->user();
        $user->update($request->only('name', 'email'));

        return redirect()->route('profile.settings')->with('success', 'Profile settings updated successfully.');
    }

}
