<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OrderApiController extends Controller
{
    public function index(Request $request)
    {
        Log::info('API: Admin Orders index requested', ['admin_id' => $request->user()?->id]);

        $query = Order::with(['user', 'service']);

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

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

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate($request->get('per_page', 15));

        // Stats
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::whereIn('status', ['processing', 'in_progress'])->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'partial' => Order::where('status', 'partial')->count(),
            'refunded' => Order::where('status', 'refunded')->count(),
            'total_charge' => Order::sum('charge'),
            'total_api_charge' => Order::sum('api_charge'),
            'total_profit' => Order::sum('charge') - Order::sum('api_charge'),
        ];

        return response()->json([
            'orders' => $orders->items(),
            'stats' => $stats,
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['user', 'service']);

        return response()->json(['order' => $order]);
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'sometimes|in:pending,processing,in_progress,completed,partial,cancelled,refunded',
            'start_count' => 'sometimes|integer|min:0',
            'remains' => 'sometimes|integer|min:0',
        ]);

        if ($request->has('status')) {
            $order->status = $request->status;
        }
        if ($request->has('start_count')) {
            $order->start_count = $request->start_count;
        }
        if ($request->has('remains')) {
            $order->remains = $request->remains;
        }

        $order->save();

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order->load(['user', 'service']),
        ]);
    }

    public function refund(Request $request, Order $order)
    {
        Log::info('API: Admin Order refund requested', ['admin_id' => $request->user()?->id, 'order_id' => $order->id]);

        if ($order->status === 'refunded') {
            return response()->json(['message' => 'Order already refunded'], 422);
        }

        $request->validate([
            'percentage' => 'sometimes|numeric|min:0|max:100',
        ]);

        $percentage = $request->percentage ?? 100;

        // Calculate refund amount
        $refundAmount = $order->charge * ($percentage / 100);

        if ($order->remains && $order->quantity && $percentage === 100) {
            // If not manually specified, calculate based on remaining quantity
            $refundAmount = ($order->remains / $order->quantity) * $order->charge;
        }

        // Refund to user
        $user = $order->user;
        $user->balance += $refundAmount;
        $user->save();

        // Create transaction record
        Transaction::create([
            'user_id' => $user->id,
            'amount' => $refundAmount,
            'type' => 'credit',
            'status' => 'completed',
            'payment_method' => 'refund',
            'description' => "Refund for order #{$order->id}",
        ]);

        // Update order status
        $order->status = 'refunded';
        $order->refunded_percent = $percentage;
        $order->save();

        return response()->json([
            'message' => 'Order refunded successfully',
            'refund_amount' => $refundAmount,
            'order' => $order,
        ]);
    }

    public function cancel(Order $order)
    {
        if (in_array($order->status, ['completed', 'cancelled', 'refunded'])) {
            return response()->json(['message' => 'Cannot cancel this order'], 422);
        }

        // Full refund
        $user = $order->user;
        $user->balance += $order->charge;
        $user->save();

        // Create transaction record
        Transaction::create([
            'user_id' => $user->id,
            'amount' => $order->charge,
            'type' => 'credit',
            'status' => 'completed',
            'payment_method' => 'refund',
            'description' => "Order #{$order->id} cancelled - full refund",
        ]);

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'message' => 'Order cancelled and refunded',
            'order' => $order,
        ]);
    }

    public function syncStatus(Request $request, Order $order)
    {
        if (!$order->api_order_id) {
            return response()->json(['message' => 'No API order ID found'], 422);
        }

        $api = new Api();
        $status = $api->status($order->api_order_id);

        if (!isset($status['status'])) {
            return response()->json(['message' => 'Failed to get status from API'], 500);
        }

        $order->status = strtolower($status['status']);
        $order->start_count = $status['start_count'] ?? $order->start_count;
        $order->remains = $status['remains'] ?? $order->remains;
        $order->save();

        return response()->json([
            'message' => 'Order status synced',
            'order' => $order->load(['user', 'service']),
        ]);
    }

    public function syncAll(Request $request)
    {
        Log::info('API: Admin Orders sync all requested', ['admin_id' => $request->user()?->id]);

        $orders = Order::whereIn('status', ['pending', 'processing', 'in_progress'])
            ->whereNotNull('api_order_id')
            ->take(100)
            ->get();

        $api = new Api();
        $updated = 0;
        $failed = 0;

        foreach ($orders as $order) {
            try {
                $status = $api->status($order->api_order_id);

                if (isset($status['status'])) {
                    $order->status = strtolower($status['status']);
                    $order->start_count = $status['start_count'] ?? $order->start_count;
                    $order->remains = $status['remains'] ?? $order->remains;
                    $order->save();
                    $updated++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                $failed++;
            }
        }

        return response()->json([
            'message' => "Synced {$updated} orders, {$failed} failed",
            'updated' => $updated,
            'failed' => $failed,
        ]);
    }

    public function resend(Order $order)
    {
        if (!in_array($order->status, ['cancelled', 'partial', 'refunded'])) {
            return response()->json(['message' => 'Order must be cancelled, partial, or refunded to resend'], 422);
        }

        $service = $order->service;
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $api = new Api();
        $apiResponse = $api->order([
            'service' => $service->service_id,
            'link' => $order->link,
            'quantity' => $order->remains ?? $order->quantity,
        ]);

        if (!isset($apiResponse['order'])) {
            return response()->json([
                'message' => $apiResponse['error'] ?? 'Failed to resend order'
            ], 422);
        }

        $order->api_order_id = $apiResponse['order'];
        $order->status = 'pending';
        $order->save();

        return response()->json([
            'message' => 'Order resent successfully',
            'order' => $order->load(['user', 'service']),
        ]);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully',
        ]);
    }
}
