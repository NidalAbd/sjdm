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

                if ($apiResponse && isset($apiResponse->orders)) {
                    foreach ($apiResponse->orders as $orderId => $status) {
                        $order = Order::where('api_order_id', $orderId)->first();
                        if ($order) {
                            if ($status === 'Completed') {
                                $order->status = 'Completed';
                            } elseif ($status === 'Canceled') {
                                $order->status = 'Canceled';
                            } elseif ($status === 'InProgress') {
                                $order->status = 'In Progress';
                            }
                            $order->save();
                            Log::info('Order ' . $orderId . ' status updated to ' . $order->status);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in UpdateOrderStatuses job: ' . $e->getMessage());
        }
    }

}
