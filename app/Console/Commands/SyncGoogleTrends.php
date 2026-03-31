<?php

namespace App\Console\Commands;

use App\Services\GoogleTrendsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncGoogleTrends extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:sync-trends {--geo=US : Geographic region to fetch trends for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync trending keywords from Google Trends';

    /**
     * Execute the console command.
     */
    public function handle(GoogleTrendsService $trendsService)
    {
        $geo = $this->option('geo');

        $this->info("Starting Google Trends sync for region: {$geo}...");

        try {
            $data = $trendsService->fetchAllTrends($geo);

            $this->info('Successfully synced ' . count($data) . ' trend keywords');

            Log::info('Google Trends Sync completed', [
                'keywords_synced' => count($data),
                'geo' => $geo,
                'date' => now()->toDateTimeString(),
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to sync trends data: ' . $e->getMessage());

            Log::error('Google Trends Sync failed', [
                'error' => $e->getMessage(),
                'geo' => $geo,
                'date' => now()->toDateTimeString(),
            ]);

            return Command::FAILURE;
        }
    }
}
