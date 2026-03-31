<?php

namespace App\Console\Commands;

use App\Services\TechnicalSeoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SeoUpdateSitemap extends Command
{
    protected $signature = 'seo:update-sitemap';
    protected $description = 'Update sitemap with indexed pages';

    public function handle(TechnicalSeoService $technicalService)
    {
        $this->info('Updating sitemap...');

        try {
            $urlCount = $technicalService->updateSitemap();

            $this->info("Sitemap updated with {$urlCount} URLs");

            Log::info('Sitemap updated', ['url_count' => $urlCount]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Sitemap update failed: ' . $e->getMessage());
            Log::error('Sitemap update failed', ['error' => $e->getMessage()]);

            return Command::FAILURE;
        }
    }
}
