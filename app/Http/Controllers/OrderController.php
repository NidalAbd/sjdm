<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateOrderStatuses;
use App\Models\Transaction;
use App\Services\Api;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

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
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('link', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Apply platform filter
        if ($request->filled('platform') && $request->platform !== 'all') {
            $query->whereHas('service', function ($q) use ($request) {
                $q->where('category', 'like', '%' . $request->platform . '%');
            });
        }

        // Paginate the results
        $orders = $query->paginate(5);

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
        Log::info('Incoming request data:', $request->all());

        // Validate the incoming request data
        $validated = $request->validate([
            'service_id' => 'required|exists:services,service_id',
            'link' => 'required|url',
            'quantity' => 'required|integer|min:1',
        ]);

        // Retrieve the service details
        $service = Service::find($validated['service_id']);

        // Calculate the charge based on the rate and quantity
        $charge = ($validated['quantity'] * $service->rate) / 1000;

        // Check if the user has enough balance
        $user = auth()->user(); // Assuming the user is logged in
        if ($user->balance < $charge) {
            return redirect()->back()->with('error', 'Insufficient balance to place the order.');
        }

        // Deduct the charge from the user's balance
        $user->balance -= $charge;
        $user->save();

        // Record the transaction
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->type = 'debit';
        $transaction->amount = $charge;
        $transaction->currency = 'USD'; // or any other currency you use
        $transaction->status = 'completed';
        $transaction->save();

        // Prepare the data for the API request
        $data = [
            'service' => $validated['service_id'],
            'link' => $validated['link'],
            'quantity' => $validated['quantity'],
        ];

        // Send the order to the API and handle the response
        $apiResponse = $this->api->order($data);

        // Debugging: Log API response
        Log::info('API Response:', (array) $apiResponse);

        // Check if the API response contains the order ID
        if (isset($apiResponse->order)) {
            // Debugging: Log before saving
            Log::info('Saving order to database:', $data);

            // Create a new order in the database
            $order = new Order();
            $order->user_id = $user->id; // Link the order to the user
            $order->service_id = $validated['service_id'];
            $order->link = $validated['link'];
            $order->quantity = $validated['quantity'];
            $order->charge = $charge;

            // Set the order status from API response or default to 'Pending' if not provided
            $order->status = isset($apiResponse->status) ? $apiResponse->status : 'Pending';

            $order->api_order_id = $apiResponse->order; // Store the API order ID for reference
            $order->save();

            // Redirect the user to the orders index page with a success message
            return redirect()->route('orders.index')->with('success', 'Order created successfully. Order ID: ' . $apiResponse->order);
        } else {
            // Debugging: Log failure
            Log::error('Failed to create order. API Response:', (array) $apiResponse);

            // Redirect back with an error message if the API request failed
            return redirect()->back()->with('error', 'Failed to create order. Please try again.');
        }
    }


    public function show(Order $order)
    {
        // Load the associated service model
        $service = $order->service;

        // Pass both the order and service to the view
        return view('orders.show', compact('order', 'service'));
    }


    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }

    /**
     * Get services based on the selected platform/category.
     */

    public function getCategories(Request $request)
    {
//        Log::info('getCategories method called'); // Debugging log

        $platform = $request->query('platform', ''); // Using query method to get the parameter

        if ($platform === 'all' || empty($platform)) {
            // Fetch all categories
            $categories = Service::select('category')->distinct()->pluck('category');
        } else {
            // Fetch categories based on the selected platform
            $categories = Service::where('category', 'LIKE', "%$platform%")->distinct()->pluck('category');
        }

//        Log::info('Categories fetched', ['categories' => $categories]); // Debugging log

        return response()->json($categories);
    }



    public function getServices(Request $request)
    {
//        Log::info('getServices method called'); // Debugging log

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

//        Log::info('Services fetched', ['services' => $services]); // Debugging log

        return response()->json($services);
    }


    public function updateOrderStatuses()
    {
        Log::info('updateOrderStatuses method called.');

        // Dispatch the job to update order statuses
        UpdateOrderStatuses::dispatch();

        Log::info('UpdateOrderStatuses job dispatched.');

        return redirect()->back()->with('success', 'Order statuses are being updated.');
    }


    /**
     * Search for services dynamically based on user input.
     */
    public function searchServices(Request $request)
    {
        $query = $request->get('query', '');

        $services = Service::where('name', 'LIKE', "%$query%")
            ->orWhere('category', 'LIKE', "%$query%")
            ->orWhere('service_id', 'LIKE', "%$query%")
            ->get();

        return response()->json($services);
    }


    public function checkRefill($id)
    {
        $order = Order::findOrFail($id);
        $apiOrderId = $order->api_order_id; // Use the API order ID

        if (!$apiOrderId) {
            return response()->json(['can_refill' => false, 'message' => 'Invalid API order ID.'], 400);
        }

        $apiResponse = $this->api->refillStatus($apiOrderId);

        // Assuming the API response contains a 'can_refill' key
        return response()->json([
            'can_refill' => $apiResponse->can_refill ?? false
        ]);
    }

    public function checkCancel($id)
    {
        $order = Order::findOrFail($id);
        $apiOrderId = $order->api_order_id; // Use the API order ID

        if (!$apiOrderId) {
            return response()->json(['can_cancel' => false, 'message' => 'Invalid API order ID.'], 400);
        }

        $apiResponse = $this->api->cancel([$apiOrderId]);

        // Assuming the API response contains a 'can_cancel' key
        return response()->json([
            'can_cancel' => $apiResponse->can_cancel ?? false
        ]);
    }


}
