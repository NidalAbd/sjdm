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

        // Dispatch the UpdateOrderStatuses job with the Api service
        UpdateOrderStatusesJob::dispatch($api);

        $this->info('Order statuses update job dispatched.');

        return 0;
    }
}
