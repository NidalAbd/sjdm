<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function settings()
    {
        $user = auth()->user();
        $orders = Order::where('user_id', $user->id)->get();
        $transactions = Transaction::where('user_id', $user->id)->get();

        // Fetch all referrals
        $totalReferrals = User::where('referred_by', $user->id)->get();

        // Fetch only active and verified referrals
        $verifiedActiveReferrals = $totalReferrals->filter(function ($referral) {
            return $referral->status === 'active' && $referral->email_verified_at !== null;
        });

        return view('profile.settings', compact('user', 'orders', 'transactions', 'totalReferrals', 'verifiedActiveReferrals'));
    }




    public function updateSettings(Request $request)
    {
        // Validate the input, but exclude the 'email' field from being updated
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255', // Still validating the email to display error messages if needed
        ]);

        $user = auth()->user();

        // Update only the 'name' field, excluding the email
        $user->update($request->only('name'));

        return redirect()->route('profile.settings')->with('success', 'Profile settings updated successfully, but email cannot be changed.');
    }


}
