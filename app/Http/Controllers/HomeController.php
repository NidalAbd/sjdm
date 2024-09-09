<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
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

        // Define time ranges
        $timeRanges = [
            '24h' => Carbon::now()->subDay(),
            '7d' => Carbon::now()->subDays(7),
            '30d' => Carbon::now()->subDays(30),
            'lifetime' => null, // For lifetime, no date filter
        ];

        // Fetch transactions and calculate total cost and profit for each period
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

        return view('dashboard', compact('totals', 'userCount', 'serviceCount', 'orderCount', 'startingPrice'));
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
