<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Api;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateOrderStatuses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(Api $api)
    {
        Log::info('UpdateOrderStatuses job started.');

        try {
            // Retrieve all orders with 'Pending' status
            $orders = Order::where('status', 'Pending')->get();
            Log::info('Found ' . $orders->count() . ' pending orders.');

            $orderIds = [];
            foreach ($orders as $order) {
                $orderIds[] = $order->api_order_id;
            }

            Log::info('Order IDs to check: ' . implode(', ', $orderIds));

            if (!empty($orderIds)) {
                $apiResponse = $api->multiStatus($orderIds);
                Log::info('API Response: ', (array) $apiResponse);

                if ($apiResponse && is_object($apiResponse)) {
                    foreach ($apiResponse as $orderId => $orderData) {
                        // Check if order exists in the response
                        if (isset($orderData->error)) {
                            Log::warning('Error for order ID ' . $orderId . ': ' . $orderData->error);
                            continue;
                        }

                        // Find the order in the database
                        $order = Order::where('api_order_id', $orderId)->first();
                        if ($order) {
                            // Update order status
                            $order->status = $orderData->status ?? $order->status;

                            // Update start count if available
                            if (isset($orderData->start_count)) {
                                $order->start_count = $orderData->start_count;
                            }

                            // Update remains if available
                            if (isset($orderData->remains)) {
                                $order->remains = $orderData->remains;
                            }

                            $order->save();
                            Log::info('Order ' . $orderId . ' updated. Status: ' . $order->status . ', Start Count: ' . ($orderData->start_count ?? 'N/A') . ', Remains: ' . ($orderData->remains ?? 'N/A'));
                        } else {
                            Log::warning('Order not found for ID: ' . $orderId);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in UpdateOrderStatuses job: ' . $e->getMessage());
        }

        Log::info('UpdateOrderStatuses job finished.');
    }
}
