<?php

namespace App\Console\Commands;

use App\Services\SeoAutomationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SeoSyncAll extends Command
{
    protected $signature = 'seo:sync-all {--geo=US : Geographic region}';
    protected $description = 'Sync all SEO data from GSC, Trends, and SERP';

    public function handle(SeoAutomationService $seoService)
    {
        $geo = $this->option('geo');

        $this->info("Starting full SEO sync for region: {$geo}...");

        try {
            $results = $seoService->syncAllKeywords($geo);

            $this->info('GSC Keywords synced: ' . ($results['gsc']['synced'] ?? 0));
            $this->info('Trends Keywords synced: ' . ($results['trends']['synced'] ?? 0));
            $this->info('SERP enriched: ' . ($results['serp']['enriched'] ?? 0));

            Log::info('SEO Sync All completed', $results);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('SEO sync failed: ' . $e->getMessage());
            Log::error('SEO Sync All failed', ['error' => $e->getMessage()]);

            return Command::FAILURE;
        }
    }
}
