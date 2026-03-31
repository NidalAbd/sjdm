<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Services\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderApiController extends Controller
{
    public function index(Request $request)
    {
        Log::info('API: Orders index requested', ['user_id' => $request->user()?->id]);
        $user = $request->user();
        $query = Order::with('service');

        // Admin can see all orders, users see only their own
        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        } else {
            // Admin filters
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        }

        // Common filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('link', 'like', "%{$search}%")
                  ->orWhere('api_order_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('platform')) {
            $query->whereHas('service', function ($q) use ($request) {
                $q->where('platform', $request->platform);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'orders' => $orders->items(),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,service_id',
            'link' => 'required|url',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $service = Service::where('service_id', $request->service_id)->firstOrFail();

        // Validate quantity
        if ($request->quantity < $service->min || $request->quantity > $service->max) {
            return response()->json([
                'message' => "Quantity must be between {$service->min} and {$service->max}"
            ], 422);
        }

        // Calculate charge
        $charge = ($request->quantity / 1000) * $service->rate;

        // Check balance
        if ($user->balance < $charge) {
            return response()->json([
                'message' => 'Insufficient balance'
            ], 422);
        }

        // Place order via API
        $api = new Api();
        $apiResponse = $api->order([
            'service' => $service->service_id,
            'link' => $request->link,
            'quantity' => $request->quantity,
        ]);

        if (!isset($apiResponse['order'])) {
            return response()->json([
                'message' => $apiResponse['error'] ?? 'Failed to place order'
            ], 422);
        }

        // Deduct balance
        $user->balance -= $charge;
        $user->save();

        // Create order record
        $order = Order::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'api_order_id' => $apiResponse['order'],
            'link' => $request->link,
            'quantity' => $request->quantity,
            'charge' => $charge,
            'api_charge' => ($request->quantity / 1000) * $service->api_rate,
            'status' => 'pending',
            'start_count' => 0,
            'remains' => $request->quantity,
        ]);

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order,
        ]);
    }

    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        // Check access
        if (!$user->hasRole('admin') && $order->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->load('service', 'user');

        return response()->json(['order' => $order]);
    }

    public function refund(Request $request, Order $order)
    {
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if already refunded
        if ($order->status === 'refunded') {
            return response()->json(['message' => 'Order already refunded'], 422);
        }

        // Calculate refund amount
        $refundAmount = $order->charge;
        if ($order->remains && $order->quantity) {
            $refundAmount = ($order->remains / $order->quantity) * $order->charge;
        }

        // Refund to user
        $orderUser = $order->user;
        $orderUser->balance += $refundAmount;
        $orderUser->save();

        // Update order status
        $order->status = 'refunded';
        $order->save();

        return response()->json([
            'message' => 'Order refunded successfully',
            'refund_amount' => $refundAmount,
        ]);
    }

    public function sync(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Get pending orders
        $orders = Order::whereIn('status', ['pending', 'processing', 'in_progress'])
            ->whereNotNull('api_order_id')
            ->take(100)
            ->get();

        $api = new Api();
        $updated = 0;

        foreach ($orders as $order) {
            $status = $api->status($order->api_order_id);

            if (isset($status['status'])) {
                $order->status = strtolower($status['status']);
                $order->start_count = $status['start_count'] ?? $order->start_count;
                $order->remains = $status['remains'] ?? $order->remains;
                $order->save();
                $updated++;
            }
        }

        return response()->json([
            'message' => "Updated {$updated} orders",
            'updated' => $updated,
        ]);
    }
}
