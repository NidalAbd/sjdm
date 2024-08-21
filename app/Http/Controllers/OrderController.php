<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use App\Services\Api;
use Illuminate\Http\Request;
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
        $query = Order::query();

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

        $orders = $query->paginate(10);

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

    public function create(Request $request)
    {
        // Define available platforms
        $platforms = [
            'all', 'facebook', 'instagram', 'tiktok', 'google', 'twitter',
            'youtube', 'spotify', 'snapchat', 'linkedin', 'telegram',
            'discord', 'reviews', 'twitch', 'traffic'
        ];

        // Query services based on the selected platform and category
        $query = Service::query();

        if ($request->filled('platform') && $request->platform !== 'all') {
            $query->where('category', 'like', '%' . $request->platform . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $services = $query->get();
        $uniqueCategories = $query->distinct()->pluck('category');

        // If it's an AJAX request, return the categories and services as JSON
        if ($request->ajax()) {
            return response()->json([
                'categories' => view('partials.category_options', compact('uniqueCategories'))->render(),
                'services' => view('partials.service_options', compact('services'))->render(),
            ]);
        }

        // If not an AJAX request, return the regular view
        $selectedService = $services->first();
        $charge = null;

        if ($selectedService && $request->filled('quantity')) {
            $charge = ($request->quantity * $selectedService->rate) / 1000;
        }

        return view('orders.create', compact('platforms', 'uniqueCategories', 'services', 'selectedService', 'charge'));
    }


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
            $order->status = 'Pending'; // Set the default status to Pending
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
        return view('orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('search');

        // Search for services by ID or name
        $services = Service::where('id', 'like', '%' . $query . '%')
            ->orWhere('name', 'like', '%' . $query . '%')
            ->get();

        // Return the services with necessary data in JSON format
        return response()->json([
            'services' => $services->map(function($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'rate' => $service->rate,
                    'min' => $service->min,
                    'max' => $service->max,
                    'average_time' => $service->average_time,
                    'category' => $service->category,
                ];
            }),
        ]);
    }}
