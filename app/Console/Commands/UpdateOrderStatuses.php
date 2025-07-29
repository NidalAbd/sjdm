<?php

namespace App\Console\Commands;

use App\Jobs\UpdateOrderStatuses as UpdateOrderStatusesJob; // Import the job with an alias
use App\Services\Api;
use Illuminate\Console\Command;

class UpdateOrderStatuses extends Command // Rename the command class to avoid conflict
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update order statuses from the API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Resolve the Api service from the container
        $api = app(Api::class);

        $this->info('Starting order status update...');

        // Get current order statistics
        $totalOrders = \App\Models\Order::count();
        $waitingOrders = \App\Models\Order::where('status', 'waiting')->count();
        $processingOrders = \App\Models\Order::where('status', 'processing')->count();
        $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
        $completedOrders = \App\Models\Order::where('status', 'completed')->count();
        $ordersWithApiId = \App\Models\Order::whereNotNull('api_order_id')->count();

        $this->info("Order Statistics:");
        $this->info("- Total Orders: {$totalOrders}");
        $this->info("- Waiting Orders: {$waitingOrders}");
        $this->info("- Processing Orders: {$processingOrders}");
        $this->info("- Pending Orders: {$pendingOrders}");
        $this->info("- Completed Orders: {$completedOrders}");
        $this->info("- Orders with API ID: {$ordersWithApiId}");

        // Dispatch the UpdateOrderStatuses job with the Api service
        UpdateOrderStatusesJob::dispatch($api);

        $this->info('Order statuses update job dispatched.');
        $this->info('Check the logs for detailed information about the update process.');

        return 0;
    }
}
