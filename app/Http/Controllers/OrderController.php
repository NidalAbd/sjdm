<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        $user = Auth::user(); // Get the authenticated user
        $query = Order::query(); // Start with a base query for all orders

        // Check if the user is an admin
        if (!$user->hasRole('admin')) {
            // If the user is not an admin, limit to their orders
            $query->where('user_id', $user->id);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('link', 'like', '%' . $request->search . '%');
        }

        // Apply platform filter
        if ($request->filled('platform') && $request->platform !== 'all') {
            $query->whereHas('service', function($q) use ($request) {
                $q->where('category', 'like', '%' . $request->platform . '%');
            });
        }

        $orders = $query->paginate(5); // Paginate the results

        // Retrieve the list of services for the filter dropdown
        $services = Service::all();

        // Available platforms for the platform filter dropdown
        $platforms = [
            'all', 'facebook', 'instagram', 'tiktok', 'google', 'twitter',
            'youtube', 'spotify', 'snapchat', 'linkedin', 'telegram',
            'discord', 'reviews', 'twitch', 'traffic'
        ];

        return view('orders.index', compact('orders', 'services', 'platforms'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        Log::info('getCategories method called'); // Debugging log

        // List of platforms (categories)
        $platforms = [
            'all', 'facebook', 'instagram', 'tiktok', 'google', 'twitter',
            'youtube', 'spotify', 'snapchat', 'linkedin', 'telegram',
            'discord', 'reviews', 'twitch', 'traffic'
        ];

        // Fetch unique categories from the services table
        $uniqueCategories = Service::select('category')->distinct()->pluck('category');

        // Fetch services to prepopulate for the initial platform/category
        $services = Service::where('category', 'LIKE', "%all%")->get();

        // Select the first service as the default selected service, if available
        $selectedService = $services->first();

        return view('orders.create', compact('platforms', 'uniqueCategories', 'services', 'selectedService'));
    }


    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'service_id' => 'required|exists:services,service_id',
            'link' => 'required|url',
            'quantity' => 'required|integer|min:1',
            'charge' => 'required|numeric',
        ]);

        // Create a new order
        $order = new Order();
        $order->user_id = auth()->id(); // Assuming user authentication
        $order->service_id = $request->service_id;
        $order->link = $request->link;
        $order->quantity = $request->quantity;
        $order->charge = $request->charge;
        $order->status = 'pending'; // Default status
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Order created successfully!');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        return view('orders.show', compact('order'));
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }

    /**
     * Get services based on the selected platform/category.
     */

    public function getCategories(Request $request)
    {
        Log::info('getCategories method called'); // Debugging log

        $platform = $request->query('platform', ''); // Using query method to get the parameter

        if ($platform === 'all' || empty($platform)) {
            // Fetch all categories
            $categories = Service::select('category')->distinct()->pluck('category');
        } else {
            // Fetch categories based on the selected platform
            $categories = Service::where('category', 'LIKE', "%$platform%")->distinct()->pluck('category');
        }

        Log::info('Categories fetched', ['categories' => $categories]); // Debugging log

        return response()->json($categories);
    }



    public function getServices(Request $request)
    {
        Log::info('getServices method called'); // Debugging log

        $platform = $request->query('platform', ''); // Using query method to get the parameter
        $category = $request->query('category', ''); // Using query method to get the parameter

        // Fetch services based on platform and category
        $services = Service::when($platform !== 'all' && !empty($platform), function ($query) use ($platform) {
            return $query->where('category', 'LIKE', "%$platform%");
        })
            ->when(!empty($category), function ($query) use ($category) {
                return $query->where('category', 'LIKE', "%$category%");
            })
            ->get();

        Log::info('Services fetched', ['services' => $services]); // Debugging log

        return response()->json($services);
    }






    /**
     * Search for services dynamically based on user input.
     */
    public function searchServices(Request $request)
    {
        $query = $request->get('query', '');

        $services = Service::where('name', 'LIKE', "%$query%")
            ->orWhere('category', 'LIKE', "%$query%")
            ->get();

        return response()->json($services);
    }


    /**
     * Helper function to extract start time from the service name.
     */
    private function extractStartTime($serviceName)
    {
        preg_match('/\[Start time: ([^\]]+)\]/', $serviceName, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Helper function to extract speed from the service name.
     */
    private function extractSpeed($serviceName)
    {
        preg_match('/\[Speed: ([^\]]+)\]/', $serviceName, $matches);
        return $matches[1] ?? null;
    }
}
