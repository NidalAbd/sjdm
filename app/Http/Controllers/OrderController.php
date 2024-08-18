<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Services\Api;
use Illuminate\Http\Request;

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
        $platforms = [
            'all', 'facebook', 'instagram', 'tiktok', 'google', 'twitter',
            'youtube', 'spotify', 'snapchat', 'linkedin', 'telegram',
            'discord', 'reviews', 'twitch', 'traffic'
        ];

        $query = Service::query();

        // Apply platform filter
        if ($request->filled('platform') && $request->platform !== 'all') {
            $query->where('category', 'like', '%' . $request->platform . '%');
        }

        // Apply category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $services = $query->get();
        $uniqueCategories = $query->distinct()->pluck('category');

        if ($request->ajax()) {
            return response()->json([
                'categories' => view('partials.category_options', compact('uniqueCategories'))->render(),
                'services' => view('partials.service_options', compact('services'))->render(),
            ]);
        }

        $selectedService = $services->first();
        $charge = null;

        if ($selectedService && $request->filled('quantity')) {
            $charge = ($request->quantity * $selectedService->rate) / 1000;
        }

        return view('orders.create', compact('platforms', 'uniqueCategories', 'services', 'selectedService', 'charge'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'link' => 'required|url',
            'quantity' => 'required|integer|min:1',
        ]);

        $service = Service::find($validated['service_id']);
        $charge = ($validated['quantity'] * $service->rate) / 1000;

        $data = [
            'service' => $validated['service_id'],
            'link' => $validated['link'],
            'quantity' => $validated['quantity'],
        ];

        $apiResponse = $this->api->order($data);

        if (isset($apiResponse->order)) {
            $order = new Order();
            $order->service_id = $validated['service_id'];
            $order->link = $validated['link'];
            $order->quantity = $validated['quantity'];
            $order->charge = $charge;
            $order->status = 'Pending'; // Default status when order is created
            $order->save();

            return redirect()->route('orders.index')->with('success', 'Order created successfully. Order ID: ' . $apiResponse->order);
        } else {
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
