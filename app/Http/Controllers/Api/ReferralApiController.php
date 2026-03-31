<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReferralApiController extends Controller
{
    public function index(Request $request)
    {
        Log::info('API: Referrals index requested', ['user_id' => $request->user()?->id]);
        $user = $request->user();

        $referrals = User::where('referred_by', $user->id)
            ->select('id', 'name', 'email', 'created_at')
            ->withCount('orders')
            ->withSum('orders', 'charge')
            ->latest()
            ->paginate($request->get('per_page', 15));

        $stats = [
            'total_referrals' => User::where('referred_by', $user->id)->count(),
            'active_referrals' => User::where('referred_by', $user->id)
                ->whereHas('orders')
                ->count(),
            'total_earnings' => $user->referral_earnings ?? 0,
            'referral_code' => $user->referral_code,
            'referral_link' => url('/register?ref=' . $user->referral_code),
        ];

        return response()->json([
            'referrals' => $referrals->items(),
            'stats' => $stats,
            'pagination' => [
                'total' => $referrals->total(),
                'per_page' => $referrals->perPage(),
                'current_page' => $referrals->currentPage(),
                'last_page' => $referrals->lastPage(),
            ],
        ]);
    }
}
