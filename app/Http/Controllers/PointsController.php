<?php

namespace App\Http\Controllers;

use App\Models\PointsTransaction;
use App\Notifications\PointsRedeemedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $points = $user->points;

        // Fetch redemption transactions for the current user
        $redeemTransactions = PointsTransaction::where('user_id', $user->id)
            ->where('type', 'redeem')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('points.index', compact('points', 'redeemTransactions'));
    }

    public function redeem(Request $request)
    {
        $user = Auth::user();

        // Ensure the user has more than 1000 points
        $pointsToRedeem = $request->input('points');
        if ($pointsToRedeem < 1000) {
            return redirect()->route('points.index')->with('error', 'You need to redeem at least 1000 points.');
        }

        if ($user->points < $pointsToRedeem) {
            return redirect()->route('points.index')->with('error', 'You do not have enough points to redeem.');
        }

        // Calculate the amount to credit based on points (100 points = $1)
        $amountToCredit = ($pointsToRedeem / 100);

        // Deduct points from the user
        $user->decrement('points', $pointsToRedeem);

        // Create a points transaction for redeeming points
        PointsTransaction::create([
            'user_id' => $user->id,
            'points' => $pointsToRedeem,
            'type' => 'redeem',
        ]);

        // Increment the user's balance
        $user->increment('balance', $amountToCredit);

        // Notify the user of successful redemption
        $user->notify(new PointsRedeemedNotification($pointsToRedeem, $amountToCredit));

        // Redirect with success message
        return redirect()->route('points.index')->with('success', "You have successfully redeemed $pointsToRedeem points for $$amountToCredit.");
    }
}
