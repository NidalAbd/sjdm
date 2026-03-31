<?php

namespace App\Console\Commands;

use App\Services\TechnicalSeoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SeoTechnicalAudit extends Command
{
    protected $signature = 'seo:technical-audit';
    protected $description = 'Run a full technical SEO audit';

    public function handle(TechnicalSeoService $technicalService)
    {
        $this->info('Starting technical SEO audit...');

        try {
            $results = $technicalService->runFullAudit();

            $this->info('Pages crawled: ' . $results['pages_crawled']);
            $this->info('Index issues found: ' . $results['index_issues']);
            $this->info('Canonical issues: ' . $results['canonical_issues']);
            $this->info('Redirect issues: ' . $results['redirect_issues']);
            $this->info('Structured data - Valid: ' . $results['structured_data']['valid'] . ', Errors: ' . $results['structured_data']['errors']);
            $this->info('Core Web Vitals issues: ' . $results['core_web_vitals']);
            $this->info('Sitemap URLs: ' . $results['sitemap_health']['url_count']);

            if (!empty($results['sitemap_health']['issues'])) {
                $this->warn('Sitemap issues: ' . implode(', ', $results['sitemap_health']['issues']));
            }

            Log::info('Technical SEO Audit completed', $results);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Technical audit failed: ' . $e->getMessage());
            Log::error('Technical SEO Audit failed', ['error' => $e->getMessage()]);

            return Command::FAILURE;
        }
    }
}
