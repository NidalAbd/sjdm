<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Ticket; // Import the Ticket model if you have it for support tickets
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    public function index()
    {
        $user = auth()->user();

        // Time ranges for total cost and profit
        $timeRanges = [
            '24h' => Carbon::now()->subDay(),
            '7d' => Carbon::now()->subDays(7),
            '30d' => Carbon::now()->subDays(30),
            'lifetime' => null,
        ];

        // Fetch transactions and calculate total cost and profit
        $totals = [];
        foreach ($timeRanges as $key => $startDate) {
            $transactions = Transaction::where('user_id', $user->id);
            if ($startDate) {
                $transactions->where('created_at', '>=', $startDate);
            }
            $totals[$key] = [
                'cost' => $transactions->sum('api_cost'),
                'profit' => $transactions->sum('profit'),
            ];
        }

        // Other widgets data
        $userCount = User::count();
        $serviceCount = Service::count();
        $orderCount = Order::count();
        $startingPrice = Service::min('rate');

        // Referrals for current user
        $verifiedActiveReferrals = User::where('referred_by', $user->id)
            ->where('status', 'active')
            ->whereNotNull('email_verified_at')
            ->get();
        $totalUserBalance = User::where('status', 'active')
            ->sum('balance');

        // Support tickets for current user
        $ticketsCount = SupportTicket::where('user_id', $user->id)->count();

        // Unique orders by status for current user
        $ordersByStatus = Order::where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Define colors for statuses
        $statusColors = [
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'canceled' => 'danger',
        ];

        return view('dashboard', compact(
            'totals', 'userCount', 'serviceCount',
            'orderCount', 'startingPrice', 'verifiedActiveReferrals',
            'ticketsCount', 'ordersByStatus', 'statusColors','totalUserBalance'
        ));
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
