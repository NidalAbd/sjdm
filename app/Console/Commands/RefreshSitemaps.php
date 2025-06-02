<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RefreshSitemaps extends Command
{
    protected $signature = 'sitemap:refresh {--ping : Ping search engines after refresh}';
    protected $description = 'Refresh sitemap caches and optionally ping search engines';

    public function handle()
    {
        $this->info('Refreshing sitemap caches for bilingual site...');

        // Clear relevant caches for bilingual setup
        $cacheKeys = [
            'sitemap_services',
            'sitemap_categories_en',
            'sitemap_categories_ar',
            'categories_en',
            'categories_ar',
            'platform_stats_en',
            'platform_stats_ar',
            'featured_services_en',
            'featured_services_ar'
        ];

        $clearedCount = 0;
        foreach ($cacheKeys as $key) {
            if (Cache::forget($key)) {
                $clearedCount++;
                $this->info("✓ Cleared cache: {$key}");
            }
        }

        $this->info("Total caches cleared: {$clearedCount}");

        // Generate fresh sitemap data
        $this->info('Generating fresh sitemap data...');

        try {
            // Test each sitemap endpoint to generate fresh cache
            $sitemapUrls = [
                '/sitemap.xml',
                '/sitemap-main.xml',
                '/sitemap-services.xml',
                '/sitemap-categories.xml',
                '/sitemap-platforms.xml'
            ];

            foreach ($sitemapUrls as $endpoint) {
                $response = Http::get(url($endpoint));
                if ($response->successful()) {
                    $this->info("✓ Generated: {$endpoint}");
                } else {
                    $this->warn("⚠ Failed to generate: {$endpoint}");
                }
            }

        } catch (\Exception $e) {
            $this->error("Error generating sitemap data: " . $e->getMessage());
            Log::error("Sitemap generation error: " . $e->getMessage());
        }

        // Ping search engines if requested
        if ($this->option('ping')) {
            $this->pingSearchEngines(); // FIXED: Removed the extra space
        } else {
            $this->info('Use --ping flag to notify search engines of updates');
        }

        $this->info('✅ Sitemap refresh complete!');
        return 0;
    }

    private function pingSearchEngines()
    {
        $sitemapUrl = url('/sitemap.xml');
        $this->info("Pinging search engines with sitemap: {$sitemapUrl}");

        $searchEngines = [
            'Google' => "https://www.google.com/ping?sitemap=" . urlencode($sitemapUrl),
            'Bing' => "https://www.bing.com/ping?sitemap=" . urlencode($sitemapUrl),
            'Yandex' => "https://webmaster.yandex.com/ping?sitemap=" . urlencode($sitemapUrl)
        ];

        foreach ($searchEngines as $engine => $pingUrl) {
            try {
                $response = Http::timeout(10)->get($pingUrl);

                if ($response->successful()) {
                    $this->info("✓ Successfully pinged {$engine}");
                    Log::info("Successfully pinged {$engine} with sitemap: {$sitemapUrl}");
                } else {
                    $this->warn("⚠ Failed to ping {$engine}: HTTP " . $response->status());
                    Log::warning("Failed to ping {$engine}. Status: " . $response->status());
                }
            } catch (\Exception $e) {
                $this->error("✗ Error pinging {$engine}: " . $e->getMessage());
                Log::error("Error pinging {$engine}: " . $e->getMessage());
            }
        }
    }
}
