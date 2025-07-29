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
            // Retrieve all orders that are NOT in final statuses (completed, canceled, partial, refunded)
            // This includes: waiting, processing, pending, in progress, etc.
            $orders = Order::whereNotIn('status', ['completed', 'canceled', 'cancelled', 'partial', 'refunded'])->get();
            Log::info('Found ' . $orders->count() . ' orders to check for status updates.');

            // Filter orders that have api_order_id (only these can be checked via API)
            $ordersWithApiId = $orders->filter(function($order) {
                return !empty($order->api_order_id);
            });
            
            $ordersWithoutApiId = $orders->filter(function($order) {
                return empty($order->api_order_id);
            });

            Log::info('Orders with API ID: ' . $ordersWithApiId->count());
            Log::info('Orders without API ID: ' . $ordersWithoutApiId->count());

            if ($ordersWithoutApiId->count() > 0) {
                Log::info('Orders without API ID (these need to be processed first):');
                foreach ($ordersWithoutApiId as $order) {
                    Log::info("Order ID: {$order->id}, Status: {$order->status}, Created: {$order->created_at}");
                }
            }

            $orderIds = $ordersWithApiId->pluck('api_order_id')->toArray();
            Log::info('Order IDs to check: ' . implode(', ', $orderIds));

            if (!empty($orderIds)) {
                // Fetch order statuses from the API
                $apiResponse = $this->fetchOrderStatuses($api, $orderIds);

                Log::info('API Response Type: ' . gettype($apiResponse));
                Log::info('API Response: ' . json_encode($apiResponse));

                // Check if the API response is an object and contains order data
                if (is_object($apiResponse) && property_exists($apiResponse, 'order')) {
                    // Single order response
                    $this->updateOrderFromApiResponse($apiResponse->order, $apiResponse);
                } elseif (is_object($apiResponse) || is_array($apiResponse)) {
                    // Multiple orders response - handle different response structures
                    if (is_array($apiResponse)) {
                        // If it's an array, iterate through it
                        foreach ($apiResponse as $orderId => $orderData) {
                            if (is_object($orderData) || is_array($orderData)) {
                                // Check if the response contains an error
                                if (is_object($orderData) && property_exists($orderData, 'error')) {
                                    Log::warning("API Error for order ID {$orderId}: " . $orderData->error);
                                    continue;
                                }
                                if (is_array($orderData) && isset($orderData['error'])) {
                                    Log::warning("API Error for order ID {$orderId}: " . $orderData['error']);
                                    continue;
                                }
                                $this->updateOrderFromApiResponse($orderId, $orderData);
                            } else {
                                Log::warning('Invalid order data structure in API response for order ID: ' . $orderId);
                            }
                        }
                    } else {
                        // If it's an object, check for common response structures
                        if (property_exists($apiResponse, 'orders')) {
                            // Response with 'orders' property
                            foreach ($apiResponse->orders as $orderId => $orderData) {
                                if (is_object($orderData) && property_exists($orderData, 'error')) {
                                    Log::warning("API Error for order ID {$orderId}: " . $orderData->error);
                                    continue;
                                }
                                $this->updateOrderFromApiResponse($orderId, $orderData);
                            }
                        } else {
                            // Try to iterate through object properties
                            foreach ($apiResponse as $orderId => $orderData) {
                                if (is_object($orderData) || is_array($orderData)) {
                                    // Check if the response contains an error
                                    if (is_object($orderData) && property_exists($orderData, 'error')) {
                                        Log::warning("API Error for order ID {$orderId}: " . $orderData->error);
                                        continue;
                                    }
                                    if (is_array($orderData) && isset($orderData['error'])) {
                                        Log::warning("API Error for order ID {$orderId}: " . $orderData['error']);
                                        continue;
                                    }
                                    $this->updateOrderFromApiResponse($orderId, $orderData);
                                } else {
                                    Log::warning('Invalid order data structure in API response for order ID: ' . $orderId);
                                }
                            }
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
     * @param Api $api
     * @param array $orderIds
     * @return mixed
     */
    protected function fetchOrderStatuses(Api $api, array $orderIds)
    {
        if (count($orderIds) === 1) {
            // Single order status request
            return $api->status($orderIds[0]);
        } else {
            // Multiple orders status request
            return $api->multiStatus($orderIds);
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
            // Handle both object and array data structures
            if (is_object($orderData)) {
                $status = $orderData->status ?? $order->status;
                $startCount = isset($orderData->start_count) && $orderData->start_count !== null ? (string)$orderData->start_count : $order->start_count;
                $remains = isset($orderData->remains) && $orderData->remains !== null ? (string)$orderData->remains : $order->remains;
            } elseif (is_array($orderData)) {
                $status = $orderData['status'] ?? $order->status;
                $startCount = isset($orderData['start_count']) && $orderData['start_count'] !== null ? (string)$orderData['start_count'] : $order->start_count;
                $remains = isset($orderData['remains']) && $orderData['remains'] !== null ? (string)$orderData['remains'] : $order->remains;
            } else {
                Log::warning('Invalid order data type for order ID: ' . $orderId);
                return;
            }

            Log::info('API Data for Order ' . $orderId . ': Status: ' . $status . ', Start Count Type: ' . gettype($startCount) . ', Start Count: ' . $startCount . ', Remains Type: ' . gettype($remains) . ', Remains: ' . $remains);

            // Only update if there are actual changes
            $hasChanges = false;
            if ($status !== $order->status) {
                $order->status = $status;
                $hasChanges = true;
            }
            if ($startCount !== $order->start_count) {
                $order->start_count = $startCount;
                $hasChanges = true;
            }
            if ($remains !== $order->remains) {
                $order->remains = $remains;
                $hasChanges = true;
            }

            if ($hasChanges) {
                $order->save();
                Log::info('Order ' . $orderId . ' updated. New Status: ' . $order->status . ', New Start Count: ' . $order->start_count . ', New Remains: ' . $order->remains);
            } else {
                Log::info('Order ' . $orderId . ' - No changes detected.');
            }

            // Log the order update
            Log::info('Order Update Logged: Order ID: ' . $order->id . ', Status: ' . $order->status . ', Start Count: ' . $order->start_count . ', Remains: ' . $order->remains);
        } else {
            Log::warning('Order not found for API ID: ' . $orderId);
        }
    }
}
