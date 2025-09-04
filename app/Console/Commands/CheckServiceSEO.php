<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckServiceSEO extends Command
{
    protected $signature = 'services:check-seo {--fix : Automatically fix issues found}';
    protected $description = 'Check services for SEO issues and provide recommendations';

    public function handle()
    {
        $this->info('Checking services for SEO issues...');
        
        $fix = $this->option('fix');
        
        if ($fix) {
            $this->warn('AUTO-FIX MODE - Issues will be automatically fixed');
        }

        $issues = [];
        $totalServices = Service::count();
        $this->info("Total services: {$totalServices}");

        // Check for services with empty names
        $emptyNameServices = Service::where(function($query) {
            $query->whereNull('name_en')
                  ->orWhere('name_en', '')
                  ->orWhereNull('name_ar')
                  ->orWhere('name_ar', '');
        })->count();

        if ($emptyNameServices > 0) {
            $issues[] = "❌ {$emptyNameServices} services have empty names";
            if ($fix) {
                Service::where(function($query) {
                    $query->whereNull('name_en')
                          ->orWhere('name_en', '')
                          ->orWhereNull('name_ar')
                          ->orWhere('name_ar', '');
                })->delete();
                $this->info("✅ Fixed: Deleted {$emptyNameServices} services with empty names");
            }
        } else {
            $this->info("✅ All services have names");
        }

        // Check for services with very short names
        $shortNameServices = Service::where(function($query) {
            $query->whereRaw('LENGTH(name_en) < 3')
                  ->orWhereRaw('LENGTH(name_ar) < 3');
        })->count();

        if ($shortNameServices > 0) {
            $issues[] = "⚠️  {$shortNameServices} services have very short names (< 3 characters)";
        } else {
            $this->info("✅ All services have adequate name length");
        }

        // Check for services with missing timestamps
        $servicesWithoutTimestamps = Service::whereNull('created_at')->orWhereNull('updated_at')->count();
        
        if ($servicesWithoutTimestamps > 0) {
            $issues[] = "❌ {$servicesWithoutTimestamps} services have missing timestamps";
            if ($fix) {
                $now = now();
                Service::whereNull('created_at')->orWhereNull('updated_at')->update([
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                $this->info("✅ Fixed: Updated timestamps for {$servicesWithoutTimestamps} services");
            }
        } else {
            $this->info("✅ All services have proper timestamps");
        }

        // Check for services with extreme rates
        $extremeRateServices = Service::where('rate', '>', 1000)->orWhere('rate', '<', 0.0001)->count();
        
        if ($extremeRateServices > 0) {
            $issues[] = "⚠️  {$extremeRateServices} services have extreme rates (very high or very low)";
        } else {
            $this->info("✅ All services have reasonable rates");
        }

        // Check for services with invalid min/max values
        $invalidRangeServices = Service::where('min', '>', 'max')->count();
        
        if ($invalidRangeServices > 0) {
            $issues[] = "❌ {$invalidRangeServices} services have invalid min/max ranges";
        } else {
            $this->info("✅ All services have valid min/max ranges");
        }

        // Check for duplicate services
        $duplicateCount = Service::select('name_en', 'name_ar')
            ->whereNotNull('name_en')
            ->where('name_en', '!=', '')
            ->groupBy('name_en', 'name_ar')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        if ($duplicateCount > 0) {
            $issues[] = "❌ Found {$duplicateCount} groups of duplicate services";
        } else {
            $this->info("✅ No duplicate services found");
        }

        // Check for services that haven't been updated recently
        $oldServices = Service::where('updated_at', '<', now()->subMonths(3))->count();
        
        if ($oldServices > 0) {
            $issues[] = "⚠️  {$oldServices} services haven't been updated in 3+ months";
        } else {
            $this->info("✅ All services are relatively recent");
        }

        // Summary
        $this->newLine();
        $this->info('=== SEO CHECK SUMMARY ===');
        
        if (empty($issues)) {
            $this->info('🎉 All services look good for SEO!');
        } else {
            $this->warn('Found the following issues:');
            foreach ($issues as $issue) {
                $this->line($issue);
            }
            
            if (!$fix) {
                $this->newLine();
                $this->info('To automatically fix issues, run: php artisan services:check-seo --fix');
            }
        }

        // Recommendations
        $this->newLine();
        $this->info('=== SEO RECOMMENDATIONS ===');
        $this->line('1. Ensure all service pages have unique, descriptive titles');
        $this->line('2. Add meta descriptions for each service');
        $this->line('3. Include structured data (JSON-LD) for services');
        $this->line('4. Optimize images with alt text');
        $this->line('5. Ensure fast loading times');
        $this->line('6. Add internal links between related services');
        $this->line('7. Create service-specific content (descriptions, FAQs)');
        $this->line('8. Monitor Google Search Console for indexing issues');

        // Log the check
        Log::info('Service SEO check completed', [
            'total_services' => $totalServices,
            'issues_found' => count($issues),
            'auto_fix' => $fix
        ]);

        return 0;
    }
}
