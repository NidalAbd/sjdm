<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        Log::info('API: Dashboard stats requested', ['user_id' => $request->user()?->id]);
        $user = $request->user();

        // Get closed status ID
        $closedStatus = \App\Models\TicketStatus::where('name', 'closed')->first();
        $closedStatusId = $closedStatus ? $closedStatus->id : 0;

        // User-specific stats
        $stats = [
            'balance' => $user->balance ?? 0,
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'pending_orders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'completed_orders' => Order::where('user_id', $user->id)->where('status', 'completed')->count(),
            'processing_orders' => Order::where('user_id', $user->id)->whereIn('status', ['processing', 'in_progress'])->count(),
            'total_spent' => Order::where('user_id', $user->id)->sum('charge'),
            'open_tickets' => SupportTicket::where('user_id', $user->id)->where('status_id', '!=', $closedStatusId)->count(),
            'referrals_count' => User::where('referred_by', $user->id)->count(),
            'referral_earnings' => $user->referral_earnings ?? 0,
            'points' => $user->points ?? 0,
        ];

        // Recent orders
        $recent_orders = Order::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Recent transactions
        $recent_transactions = Transaction::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_orders' => $recent_orders,
            'recent_transactions' => $recent_transactions,
        ]);
    }

    public function adminStats(Request $request)
    {
        Log::info('API: Admin Dashboard stats requested', ['admin_id' => $request->user()?->id]);
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $now = Carbon::now();
        $last24h = $now->copy()->subDay();
        $last7d = $now->copy()->subDays(7);
        $last30d = $now->copy()->subDays(30);

        // Get ticket status IDs
        $closedStatus = \App\Models\TicketStatus::where('name', 'closed')->first();
        $openStatus = \App\Models\TicketStatus::where('name', 'open')->first();
        $closedStatusId = $closedStatus ? $closedStatus->id : 0;
        $openStatusId = $openStatus ? $openStatus->id : 0;

        // Revenue calculations (no api_charge column exists)
        $stats = [
            // 24 hours
            'revenue_24h' => Order::where('created_at', '>=', $last24h)->sum('charge'),
            'cost_24h' => 0, // api_charge column doesn't exist
            'orders_24h' => Order::where('created_at', '>=', $last24h)->count(),
            'users_24h' => User::where('created_at', '>=', $last24h)->count(),
            'deposits_24h' => Transaction::where('created_at', '>=', $last24h)
                ->where('type', 'credit')
                ->where('status', 'completed')
                ->sum('amount'),

            // 7 days
            'revenue_7d' => Order::where('created_at', '>=', $last7d)->sum('charge'),
            'cost_7d' => 0,
            'orders_7d' => Order::where('created_at', '>=', $last7d)->count(),
            'users_7d' => User::where('created_at', '>=', $last7d)->count(),
            'deposits_7d' => Transaction::where('created_at', '>=', $last7d)
                ->where('type', 'credit')
                ->where('status', 'completed')
                ->sum('amount'),

            // 30 days
            'revenue_30d' => Order::where('created_at', '>=', $last30d)->sum('charge'),
            'cost_30d' => 0,
            'orders_30d' => Order::where('created_at', '>=', $last30d)->count(),
            'users_30d' => User::where('created_at', '>=', $last30d)->count(),
            'deposits_30d' => Transaction::where('created_at', '>=', $last30d)
                ->where('type', 'credit')
                ->where('status', 'completed')
                ->sum('amount'),

            // Lifetime
            'total_revenue' => Order::sum('charge'),
            'total_cost' => 0,
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'total_deposits' => Transaction::where('type', 'credit')
                ->where('status', 'completed')
                ->sum('amount'),

            // User stats
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => User::whereNull('email_verified_at')->count(),
            'banned_users' => User::where('status', 'banned')->count(),
            'total_balance' => User::sum('balance'),

            // Transaction stats
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
            'completed_transactions' => Transaction::where('status', 'completed')->count(),

            // Order stats by status
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::whereIn('status', ['processing', 'in_progress'])->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'partial_orders' => Order::where('status', 'partial')->count(),

            // Support stats (use status_id)
            'open_tickets' => SupportTicket::where('status_id', '!=', $closedStatusId)->count(),
            'pending_tickets' => SupportTicket::where('status_id', $openStatusId)->count(),
        ];

        // Calculate profits (revenue only since no cost data)
        $stats['profit_24h'] = $stats['revenue_24h'];
        $stats['profit_7d'] = $stats['revenue_7d'];
        $stats['profit_30d'] = $stats['revenue_30d'];
        $stats['total_profit'] = $stats['total_revenue'];

        return response()->json(['stats' => $stats]);
    }
}
