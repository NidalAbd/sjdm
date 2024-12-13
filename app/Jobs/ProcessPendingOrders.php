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

class ProcessPendingOrders implements ShouldQueue
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
        Log::info('Processing pending orders...');

        try {
            // Fetch the current API balance
            $apiBalanceResponse = $this->api->balance();
            $apiBalance = $apiBalanceResponse->balance ?? 0;

            // Retrieve all orders that are "Pending API"
            $pendingOrders = Order::where('status', 'waiting')->get();

            foreach ($pendingOrders as $order) {
                // Calculate the cost of the order based on the service cost
                $service = $order->service;
                $apiCost = ($order->quantity * $service->cost) / 1000;

                if ($apiBalance >= $apiCost) {
                    // Prepare data for the API request
                    $data = [
                        'service' => $order->service_id,
                        'link' => $order->link,
                        'quantity' => $order->quantity,
                    ];

                    // Call the API to create the order
                    $apiResponse = $this->api->order($data);

                    if (isset($apiResponse->order)) {
                        // Update the order status and save the API order ID
                        $order->status = 'Processing';
                        $order->api_order_id = $apiResponse->order;
                        $order->save();

                        // Deduct the cost from the API balance
                        $apiBalance -= $apiCost;

                        // Update the corresponding transaction to 'completed'
                        $transaction = $order->transaction;  // Assuming you have a relationship between Order and Transaction
                        $transaction->status = 'completed';
                        $transaction->save();

                        // Deduct the user charge from the user's balance
                        $user = $order->user;
                        $user->balance -= $transaction->amount;  // Deduct the amount from the user
                        $user->save();

                        Log::info("Order {$order->id} has been successfully processed in the API.");
                    } else {
                        Log::error("Failed to process order {$order->id} in the API.");
                    }
                } else {
                    Log::info('Insufficient API balance to process further orders.');
                    break;  // Stop processing if there isn't enough balance
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing pending orders: ' . $e->getMessage());
        }

        Log::info('Finished processing pending orders.');
    }
}
