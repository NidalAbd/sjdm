<?php

namespace App\Console\Commands;

use App\Services\GoogleSearchConsoleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncGoogleSearchConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:sync-gsc {--days=28 : Number of days to fetch data for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync keyword data from Google Search Console';

    /**
     * Execute the console command.
     */
    public function handle(GoogleSearchConsoleService $gscService)
    {
        $this->info('Starting Google Search Console sync...');

        try {
            $data = $gscService->fetchSearchAnalytics();

            $this->info('Successfully synced ' . count($data) . ' keywords from GSC');

            Log::info('GSC Sync completed', [
                'keywords_synced' => count($data),
                'date' => now()->toDateTimeString(),
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to sync GSC data: ' . $e->getMessage());

            Log::error('GSC Sync failed', [
                'error' => $e->getMessage(),
                'date' => now()->toDateTimeString(),
            ]);

            return Command::FAILURE;
        }
    }
}
