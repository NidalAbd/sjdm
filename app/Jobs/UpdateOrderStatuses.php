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

    protected $api;

    /**
     * Create a new job instance.
     *
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('UpdateOrderStatuses job started.');

        try {
            // Retrieve all orders with 'Pending' status
            $orders = Order::where('status', 'Pending')->get();
            Log::info('Found ' . $orders->count() . ' pending orders.');

            $orderIds = $orders->pluck('api_order_id')->filter()->toArray(); // Ensure we only get non-null api_order_ids
            Log::info('Order IDs to check: ' . implode(', ', $orderIds));

            if (!empty($orderIds)) {
                // Fetch order statuses from the API
                $apiResponse = $this->fetchOrderStatuses($orderIds);

                Log::info('API Response Type: ' . gettype($apiResponse));
                Log::info('API Response: ' . json_encode($apiResponse));

                // Check if the API response is an object and contains order data
                if (is_object($apiResponse) && property_exists($apiResponse, 'order')) {
                    // Single order response
                    $this->updateOrderFromApiResponse($apiResponse->order, $apiResponse);
                } elseif (is_object($apiResponse) || is_array($apiResponse)) {
                    // Multiple orders response
                    foreach ($apiResponse as $orderId => $orderData) {
                        if (is_object($orderData) || is_array($orderData)) {
                            $this->updateOrderFromApiResponse($orderId, $orderData);
                        } else {
                            Log::warning('Invalid order data structure in API response.');
                        }
                    }
                } else {
                    Log::error('API Response is not an array or object.');
                }
            } else {
                Log::info('No order IDs to check.');
            }
        } catch (\Exception $e) {
            Log::error('Error in UpdateOrderStatuses job: ' . $e->getMessage());
        }

        Log::info('UpdateOrderStatuses job finished.');
    }

    /**
     * Fetch order statuses from the API.
     *
     * @param array $orderIds
     * @return mixed
     */
    protected function fetchOrderStatuses(array $orderIds)
    {
        if (count($orderIds) === 1) {
            // Single order status request
            return $this->api->status($orderIds[0]);
        } else {
            // Multiple orders status request
            return $this->api->multiStatus($orderIds);
        }
    }

    /**
     * Update order in the database from API response.
     *
     * @param int|string $orderId
     * @param object|array $orderData
     * @return void
     */
    protected function updateOrderFromApiResponse($orderId, $orderData)
    {
        $order = Order::where('api_order_id', $orderId)->first();

        if ($order) {
            Log::info('Current order status: ' . $order->status . ', Start Count: ' . $order->start_count . ', Remains: ' . $order->remains);

            // Extract API response data with proper checks
            $status = $orderData->status ?? $order->status;
            $startCount = isset($orderData->start_count) && $orderData->start_count !== null ? (string)$orderData->start_count : $order->start_count;
            $remains = isset($orderData->remains) && $orderData->remains !== null ? (string)$orderData->remains : $order->remains;

            Log::info('API Data for Order ' . $orderId . ': Start Count Type: ' . gettype($startCount) . ', Start Count: ' . $startCount . ', Remains Type: ' . gettype($remains) . ', Remains: ' . $remains);

            // Update order status and other fields
            $order->status = $status;
            $order->start_count = $startCount;
            $order->remains = $remains;
            $order->save();

            Log::info('Order ' . $orderId . ' updated. New Status: ' . $order->status . ', New Start Count: ' . $order->start_count . ', New Remains: ' . $order->remains);

            // Log the order update
            Log::info('Order Update Logged: Order ID: ' . $order->id . ', Status: ' . $order->status . ', Start Count: ' . $order->start_count . ', Remains: ' . $order->remains);
        } else {
            Log::warning('Order not found for ID: ' . $orderId);
        }
    }
}
