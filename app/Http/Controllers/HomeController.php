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
    public function index()
    {
        $user = auth()->user();

        // Retrieve data for the dashboard metrics
        $userCount = User::count();
        $serviceCount = Service::count();
        $orderCount = Order::count();
        $startingPrice = Service::min('rate'); // Get the minimum price from services
        $transactionCount = Transaction::count();
        $referrals = User::where('referred_by', $user->id)
            ->where('status', 'active')
            ->whereNotNull('email_verified_at')
            ->get();

        // Pass the data to the dashboard view
        return view('layouts.app', compact('userCount', 'serviceCount', 'orderCount', 'startingPrice', 'transactionCount', 'referrals'));
    }

}
