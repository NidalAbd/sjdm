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
        \App\Console\Commands\SyncGoogleSearchConsole::class,
        \App\Console\Commands\SyncGoogleTrends::class,
        \App\Console\Commands\SeoSyncAll::class,
        \App\Console\Commands\SeoTechnicalAudit::class,
        \App\Console\Commands\SeoGenerateReport::class,
        \App\Console\Commands\SeoGenerateContent::class,
        \App\Console\Commands\SeoUpdateSitemap::class,
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

        // SEO Data Sync Commands
        // Sync Google Search Console data daily at 3 AM
        $schedule->command('seo:sync-gsc')->dailyAt('03:00');

        // Sync Google Trends data weekly on Monday at 4 AM
        $schedule->command('seo:sync-trends')->weeklyOn(1, '04:00');

        // Sync Google Trends for multiple regions (optional - monthly)
        $schedule->command('seo:sync-trends --geo=SA')->monthlyOn(1, '04:30'); // Saudi Arabia
        $schedule->command('seo:sync-trends --geo=AE')->monthlyOn(1, '05:00'); // UAE

        // =============================================
        // COMPREHENSIVE SEO AUTOMATION SCHEDULE
        // =============================================

        // DAILY SEO Tasks (run at 2 AM)
        $schedule->command('seo:sync-all')->dailyAt('02:00');
        $schedule->command('seo:generate-report daily')->dailyAt('06:00');

        // WEEKLY SEO Tasks (run on Sundays)
        $schedule->command('seo:technical-audit')->weeklyOn(0, '03:00');
        $schedule->command('seo:generate-content')->weeklyOn(0, '05:00');
        $schedule->command('seo:generate-report weekly')->weeklyOn(0, '07:00');
        $schedule->command('seo:update-sitemap')->weeklyOn(0, '08:00');

        // MONTHLY SEO Tasks (run on 1st of each month)
        $schedule->command('seo:generate-report monthly')->monthlyOn(1, '09:00');

        // Multi-region keyword sync (monthly for SA and AE)
        $schedule->command('seo:sync-all --geo=SA')->monthlyOn(15, '02:00');
        $schedule->command('seo:sync-all --geo=AE')->monthlyOn(15, '02:30');
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
