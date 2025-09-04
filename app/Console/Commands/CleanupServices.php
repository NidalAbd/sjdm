<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupServices extends Command
{
    protected $signature = 'services:cleanup {--dry-run : Show what would be cleaned without actually doing it}';
    protected $description = 'Clean up services with empty or problematic content for better SEO';

    public function handle()
    {
        $this->info('Starting services cleanup for better SEO...');
        
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Find services with empty names
        $emptyNameServices = Service::where(function($query) {
            $query->whereNull('name_en')
                  ->orWhere('name_en', '')
                  ->orWhereNull('name_ar')
                  ->orWhere('name_ar', '');
        })->get();

        if ($emptyNameServices->count() > 0) {
            $this->warn("Found {$emptyNameServices->count()} services with empty names:");
            foreach ($emptyNameServices as $service) {
                $this->line("  - Service ID: {$service->service_id}");
            }
            
            if (!$dryRun) {
                $deletedCount = $emptyNameServices->count();
                Service::whereIn('service_id', $emptyNameServices->pluck('service_id'))->delete();
                $this->info("Deleted {$deletedCount} services with empty names");
            }
        } else {
            $this->info('No services with empty names found');
        }

        // Find duplicate services (same name in same language)
        $duplicateServices = Service::select('name_en', 'name_ar')
            ->whereNotNull('name_en')
            ->where('name_en', '!=', '')
            ->groupBy('name_en', 'name_ar')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicateServices->count() > 0) {
            $this->warn("Found potential duplicate services:");
            foreach ($duplicateServices as $duplicate) {
                $services = Service::where('name_en', $duplicate->name_en)
                    ->where('name_ar', $duplicate->name_ar)
                    ->get();
                
                $this->line("  - Name: {$duplicate->name_en} (Count: {$services->count()})");
                
                if (!$dryRun && $services->count() > 1) {
                    // Keep the first one, delete the rest
                    $toDelete = $services->skip(1);
                    $deletedCount = $toDelete->count();
                    Service::whereIn('service_id', $toDelete->pluck('service_id'))->delete();
                    $this->info("  Deleted {$deletedCount} duplicate services");
                }
            }
        } else {
            $this->info('No duplicate services found');
        }

        // Find services with very low rates (potential spam)
        $lowRateServices = Service::where('rate', '<', 0.001)->get();
        
        if ($lowRateServices->count() > 0) {
            $this->warn("Found {$lowRateServices->count()} services with very low rates (< $0.001):");
            foreach ($lowRateServices as $service) {
                $this->line("  - Service ID: {$service->service_id}, Rate: \${$service->rate}");
            }
            
            if (!$dryRun) {
                $deletedCount = $lowRateServices->count();
                Service::whereIn('service_id', $lowRateServices->pluck('service_id'))->delete();
                $this->info("Deleted {$deletedCount} services with very low rates");
            }
        } else {
            $this->info('No services with very low rates found');
        }

        // Update services with missing timestamps
        $servicesWithoutTimestamps = Service::whereNull('created_at')->orWhereNull('updated_at')->get();
        
        if ($servicesWithoutTimestamps->count() > 0) {
            $this->warn("Found {$servicesWithoutTimestamps->count()} services with missing timestamps");
            
            if (!$dryRun) {
                $now = now();
                foreach ($servicesWithoutTimestamps as $service) {
                    $service->update([
                        'created_at' => $service->created_at ?? $now,
                        'updated_at' => $now
                    ]);
                }
                $this->info("Updated timestamps for {$servicesWithoutTimestamps->count()} services");
            }
        } else {
            $this->info('All services have proper timestamps');
        }

        // Log the cleanup
        if (!$dryRun) {
            Log::info('Services cleanup completed', [
                'empty_names_deleted' => $emptyNameServices->count(),
                'duplicates_deleted' => $duplicateServices->count(),
                'low_rate_deleted' => $lowRateServices->count(),
                'timestamps_updated' => $servicesWithoutTimestamps->count()
            ]);
        }

        $this->info('Services cleanup completed!');
        
        if (!$dryRun) {
            $this->info('Consider running: php artisan sitemap:refresh');
        }
        
        return 0;
    }
}
