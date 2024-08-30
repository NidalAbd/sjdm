<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        // Define the date for the last 24 hours
        $last24Hours = Carbon::now()->subDay();

        // Fetch data from the last 24 hours (excluding services)
        $usersCountLast24h = User::where('created_at', '>=', $last24Hours)->count();
        $transactionsCountLast24h = Transaction::where('created_at', '>=', $last24Hours)->count();
        $ordersCountLast24h = Order::where('created_at', '>=', $last24Hours)->count();

        // Fetch total counts from the database
        $totalUsersCount = User::count() + 79778; // Starting from 79778
        $totalTransactionsCount = Transaction::count() + 398890; // Starting from 398,890
        $totalOrdersCount = Order::count() + 254859; // Starting from 254,859
        $completedOrdersCount = Order::where('status', 'completed')->count()+ 254859-52581;

        // Pass data to the view
        return view('welcome', compact(
            'usersCountLast24h',
            'transactionsCountLast24h',
            'ordersCountLast24h',
            'totalUsersCount',
            'totalTransactionsCount',
            'totalOrdersCount',
            'completedOrdersCount',
        ));
    }
}
