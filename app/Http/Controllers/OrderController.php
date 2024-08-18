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

        if ($request->filled('platform') && $request->platform !== 'all') {
            $query->where('category', 'like', '%' . $request->platform . '%');
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $services = $query->get();
        $uniqueCategories = $query->distinct()->pluck('category');

        $selectedService = null;
        $charge = null;

        if ($request->filled('service_id')) {
            $selectedService = Service::find($request->service_id);
            if ($selectedService && $request->filled('quantity')) {
                $charge = ($request->quantity * $selectedService->rate) / 1000;
            }
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
}
