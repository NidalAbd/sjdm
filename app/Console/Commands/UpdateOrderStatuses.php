<?php

namespace App\Console\Commands;

use App\Http\Controllers\OrderController;
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
        $controller = new OrderController();
        $controller->updateOrderStatuses();
        $this->info('Order statuses have been updated.');
    }
}
