<?php

namespace App\Console\Commands;

use App\Http\Controllers\OrderController;
use App\Services\Api;
use Illuminate\Console\Command;

class UpdateOrderStatuses extends Command
{
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

        // Dispatch the UpdateOrderStatuses job with the Api service
        UpdateOrderStatuses::dispatch($api);

        $this->info('Order statuses update job dispatched.');

        return 0;
    }
}
