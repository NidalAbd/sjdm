<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ValidateBilingualSetup extends Command
{
    protected $signature = 'seo:validate-bilingual {--check-urls=10 : Number of URLs to test}';
    protected $description = 'Validate bilingual setup for SEO compliance';

    public function handle()
    {
        $this->info('🔍 Validating Bilingual SEO Setup...');
        $this->newLine();

        $issues = [];
        $checkCount = $this->option('check-urls');

        // 1. Check database content
        $this->info('📊 Checking Database Content...');
        $issues = array_merge($issues, $this->checkDatabaseContent());

        // 2. Check URL accessibility
        $this->info('🌐 Checking URL Accessibility...');
        $issues = array_merge($issues, $this->checkUrlAccessibility($checkCount));

        // 3. Check hreflang implementation
        $this->info('🔗 Checking Hreflang Implementation...');
        $issues = array_merge($issues, $this->checkHreflangImplementation());

        // 4. Check sitemap
        $this->info('🗺️ Checking Sitemap...');
        $issues = array_merge($issues, $this->checkSitemap());

        // 5. Summary
        $this->newLine();
        if (empty($issues)) {
            $this->info('✅ All checks passed! Your bilingual setup looks good.');
        } else {
            $this->error('❌ Found ' . count($issues) . ' issues:');
            foreach ($issues as $issue) {
                $this->warn("  • {$issue}");
            }
        }

        return empty($issues) ? 0 : 1;
    }

    private function checkDatabaseContent()
    {
        $issues = [];

        // Check for missing translations
        $missingEnglish = Service::whereNull('name_en')->orWhere('name_en', '')->count();
        $missingArabic = Service::whereNull('name_ar')->orWhere('name_ar', '')->count();
        $missingCategoryEn = Service::whereNull('category_en')->orWhere('category_en', '')->count();
        $missingCategoryAr = Service::whereNull('category_ar')->orWhere('category_ar', '')->count();

        if ($missingEnglish > 0) {
            $issues[] = "{$missingEnglish} services missing English names";
        }
        if ($missingArabic > 0) {
            $issues[] = "{$missingArabic} services missing Arabic names";
        }
        if ($missingCategoryEn > 0) {
            $issues[] = "{$missingCategoryEn} services missing English categories";
        }
        if ($missingCategoryAr > 0) {
            $issues[] = "{$missingCategoryAr} services missing Arabic categories";
        }

        $totalServices = Service::count();
        $this->info("  📈 Total services: {$totalServices}");
        $this->info("  🇺🇸 English complete: " . ($totalServices - $missingEnglish));
        $this->info("  🇸🇦 Arabic complete: " . ($totalServices - $missingArabic));

        return $issues;
    }

    private function checkUrlAccessibility($count)
    {
        $issues = [];

        // Test sample service URLs
        $services = Service::select('service_id')->limit($count)->get();

        $this->info("  Testing {$count} service URLs...");

        $englishSuccess = 0;
        $arabicSuccess = 0;

        foreach ($services as $service) {
            // Test English URL
            try {
                $response = Http::timeout(5)->get(url("/service/{$service->service_id}"));
                if ($response->successful()) {
                    $englishSuccess++;
                } else {
                    $issues[] = "English service URL /service/{$service->service_id} returns {$response->status()}";
                }
            } catch (\Exception $e) {
                $issues[] = "English service URL /service/{$service->service_id} failed: " . $e->getMessage();
            }

            // Test Arabic URL
            try {
                $response = Http::timeout(5)->get(url("/ar/service/{$service->service_id}"));
                if ($response->successful()) {
                    $arabicSuccess++;
                } else {
                    $issues[] = "Arabic service URL /ar/service/{$service->service_id} returns {$response->status()}";
                }
            } catch (\Exception $e) {
                $issues[] = "Arabic service URL /ar/service/{$service->service_id} failed: " . $e->getMessage();
            }
        }

        $this->info("  🇺🇸 English URLs working: {$englishSuccess}/{$count}");
        $this->info("  🇸🇦 Arabic URLs working: {$arabicSuccess}/{$count}");

        return $issues;
    }

    private function checkHreflangImplementation()
    {
        $issues = [];

        // Test a sample service page for hreflang
        $service = Service::first();
        if (!$service) {
            $issues[] = "No services found to test hreflang";
            return $issues;
        }

        try {
            // Check English page
            $response = Http::get(url("/service/{$service->service_id}"));
            if ($response->successful()) {
                $html = $response->body();

                if (!str_contains($html, 'hreflang="en"')) {
                    $issues[] = "Missing hreflang='en' on English pages";
                }
                if (!str_contains($html, 'hreflang="ar"')) {
                    $issues[] = "Missing hreflang='ar' on English pages";
                }
                if (!str_contains($html, 'hreflang="x-default"')) {
                    $issues[] = "Missing hreflang='x-default' on English pages";
                }
                if (!str_contains($html, 'rel="canonical"')) {
                    $issues[] = "Missing canonical tag on English pages";
                }
            }

            // Check Arabic page
            $response = Http::get(url("/ar/service/{$service->service_id}"));
            if ($response->successful()) {
                $html = $response->body();

                if (!str_contains($html, 'hreflang="en"')) {
                    $issues[] = "Missing hreflang='en' on Arabic pages";
                }
                if (!str_contains($html, 'hreflang="ar"')) {
                    $issues[] = "Missing hreflang='ar' on Arabic pages";
                }
            }

        } catch (\Exception $e) {
            $issues[] = "Failed to check hreflang implementation: " . $e->getMessage();
        }

        return $issues;
    }

    private function checkSitemap()
    {
        $issues = [];

        try {
            // Check main sitemap
            $response = Http::get(url('/sitemap.xml'));
            if (!$response->successful()) {
                $issues[] = "Main sitemap /sitemap.xml not accessible";
                return $issues;
            }

            // Check services sitemap
            $response = Http::get(url('/sitemap-services.xml'));
            if ($response->successful()) {
                $sitemapContent = $response->body();

                // Check for hreflang in sitemap
                $hasHreflangEn = str_contains($sitemapContent, 'hreflang="en"');
                $hasHreflangAr = str_contains($sitemapContent, 'hreflang="ar"');

                if (!$hasHreflangEn) {
                    $issues[] = "Sitemap missing hreflang='en' entries";
                }
                if (!$hasHreflangAr) {
                    $issues[] = "Sitemap missing hreflang='ar' entries";
                }

                $this->info("  📄 Sitemap contains hreflang tags: " . ($hasHreflangEn && $hasHreflangAr ? 'Yes' : 'No'));
            } else {
                $issues[] = "Services sitemap /sitemap-services.xml not accessible";
            }

        } catch (\Exception $e) {
            $issues[] = "Failed to check sitemap: " . $e->getMessage();
        }

        return $issues;
    }
}
