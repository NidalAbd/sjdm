<?php

namespace App\Console\Commands;

use App\Http\Controllers\OrderController;
use App\Services\Api;
use Illuminate\Console\Command;

class UpdateOrderStatuses extends Command
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

        // Create an instance of OrderController with the required dependency
        $controller = new OrderController($api);

        // Call the method that handles updating order statuses
        $controller->updateOrderStatuses();

        $this->info('Order statuses updated.');
    }

}
