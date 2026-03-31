<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PointsApiController extends Controller
{
    public function index(Request $request)
    {
        Log::info('API: Points index requested', ['user_id' => $request->user()?->id]);
        $user = $request->user();

        // Get point transactions
        $transactions = PointTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate($request->get('per_page', 15));

        $stats = [
            'points' => $user->points ?? 0,
            'points_value' => ($user->points ?? 0) * 0.01, // $0.01 per point
            'total_earned' => PointTransaction::where('user_id', $user->id)
                ->where('type', 'credit')
                ->sum('points'),
            'total_redeemed' => PointTransaction::where('user_id', $user->id)
                ->where('type', 'debit')
                ->sum('points'),
        ];

        return response()->json([
            'stats' => $stats,
            'transactions' => $transactions->items(),
            'pagination' => [
                'total' => $transactions->total(),
                'per_page' => $transactions->perPage(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
            ],
        ]);
    }

    public function redeem(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:100',
        ]);

        $user = $request->user();
        $points = $request->points;

        if ($user->points < $points) {
            return response()->json([
                'message' => 'Insufficient points'
            ], 422);
        }

        // Calculate balance value (1 point = $0.01)
        $balanceValue = $points * 0.01;

        // Deduct points and add balance
        $user->points -= $points;
        $user->balance += $balanceValue;
        $user->save();

        // Record transaction
        PointTransaction::create([
            'user_id' => $user->id,
            'points' => $points,
            'type' => 'debit',
            'description' => "Redeemed {$points} points for \${$balanceValue} balance",
        ]);

        return response()->json([
            'message' => 'Points redeemed successfully',
            'balance_added' => $balanceValue,
            'remaining_points' => $user->points,
        ]);
    }
}
