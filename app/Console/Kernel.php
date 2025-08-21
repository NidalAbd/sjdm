<?php

namespace App\Console;

use App\Jobs\ProcessPendingOrders;
use App\Services\Api;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\RefreshSitemaps::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Schedule the update-statuses command every minute
        $schedule->command('orders:update-statuses')->everyMinute();

        // Schedule the sitemap refresh command to run daily at midnight
        $schedule->command('sitemap:refresh')->dailyAt('00:00');

        // Schedule the ProcessPendingOrders job every minute
        $schedule->call(function () {
            ProcessPendingOrders::dispatch(new Api);
        })->everyMinute();

        // Other scheduled commands
        $schedule->command('notify:unverified-users')->daily();
        $schedule->command('transaction:send-reminder')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
