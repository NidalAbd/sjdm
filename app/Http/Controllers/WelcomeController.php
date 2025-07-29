<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller
{
    public function index()
    {
        // Define the date for the last 24 hours
        $last24Hours = Carbon::now()->subDay();

        // Fetch real counts from the database for last 24 hours
        $usersCountLast24h = Cache::remember('users_count_last_24h', 6 * 3600, function () use ($last24Hours) {
            return $this->getLast24HoursCount(User::class, $last24Hours);
        });

        $transactionsCountLast24h = Cache::remember('transactions_count_last_24h', 6 * 3600, function () use ($last24Hours) {
            return $this->getLast24HoursCount(Transaction::class, $last24Hours);
        });

        $ordersCountLast24h = Cache::remember('orders_count_last_24h', 6 * 3600, function () use ($last24Hours) {
            return $this->getLast24HoursCount(Order::class, $last24Hours);
        });

        // Fetch real total counts from the database
        $totalUsersCount = User::count();
        $totalTransactionsCount = Transaction::count();
        $totalOrdersCount = Order::count();
        $completedOrdersCount = Order::where('status', 'completed')->count();

        // Pass data to the view
        return view('welcome', compact(
            'usersCountLast24h',
            'transactionsCountLast24h',
            'ordersCountLast24h',
            'totalUsersCount',
            'totalTransactionsCount',
            'totalOrdersCount',
            'completedOrdersCount'
        ));
    }
    
    // Method to get the count of records created in the last 24 hours
    private function getLast24HoursCount($model, $last24Hours)
    {
        return $model::where('created_at', '>=', $last24Hours)->count();
    }

    // Other static content methods
    public function terms()
    {
        return view('widgets.terms');
    }

    public function faq()
    {
        return view('widgets.faq');
    }

    public function about()
    {
        return view('widgets.about');
    }

    public function howItWorks()
    {
        return view('widgets.how_it_work');
    }

    public function support()
    {
        return view('widgets.support');
    }

    public function privacyPolicy()
    {
        return view('widgets.privacy-policy');
    }

    public function contact()
    {
        return view('widgets.contact');
    }
}
