<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Services\Api;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchServices extends Command
{
    protected $signature = 'services:fetch {--language=en : Language to fetch (en/ar)} {--force : Force update existing services}';
    protected $description = 'Fetch services from the SMM API and populate the database';

    public function handle()
    {
        $language = $this->option('language');
        $force = $this->option('force');
        
        if (!in_array($language, ['en', 'ar'])) {
            $this->error('Language must be either "en" or "ar"');
            return 1;
        }

        $this->info("Fetching services from API for language: {$language}");
        
        try {
            $api = new Api();
            $servicesFromApi = $api->services();
            
            if (!$servicesFromApi) {
                $this->error('Failed to fetch services from API. Check your API configuration.');
                return 1;
            }

            $this->info("Found " . count($servicesFromApi) . " services from API");
            
            $storedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;

            foreach ($servicesFromApi as $serviceData) {
                $serviceId = $serviceData->service ?? null;
                
                if (!$serviceId) {
                    $this->warn("Skipping service without ID");
                    $skippedCount++;
                    continue;
                }

                $existingService = Service::where('service_id', $serviceId)->first();
                
                if ($existingService) {
                    if ($force) {
                        // Update existing service
                        $existingService->update([
                            'name_' . $language => $serviceData->name ?? '',
                            'category_' . $language => $serviceData->category ?? '',
                            'type' => $serviceData->type ?? '',
                            'rate' => $serviceData->rate ?? 0,
                            'cost' => $serviceData->cost ?? 0,
                            'min' => $serviceData->min ?? 0,
                            'max' => $serviceData->max ?? 0,
                            'refill' => $serviceData->refill ?? false,
                            'cancel' => $serviceData->cancel ?? false,
                            'updated_at' => now()
                        ]);
                        $updatedCount++;
                        $this->line("Updated service ID: {$serviceId}");
                    } else {
                        $skippedCount++;
                        $this->line("Skipped existing service ID: {$serviceId}");
                    }
                } else {
                    // Create new service
                    Service::create([
                        'service_id' => $serviceId,
                        'name_' . $language => $serviceData->name ?? '',
                        'category_' . $language => $serviceData->category ?? '',
                        'type' => $serviceData->type ?? '',
                        'rate' => $serviceData->rate ?? 0,
                        'cost' => $serviceData->cost ?? 0,
                        'min' => $serviceData->min ?? 0,
                        'max' => $serviceData->max ?? 0,
                        'refill' => $serviceData->refill ?? false,
                        'cancel' => $serviceData->cancel ?? false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $storedCount++;
                    $this->line("Created service ID: {$serviceId}");
                }
            }

            $this->info("=== FETCH SUMMARY ===");
            $this->info("New services created: {$storedCount}");
            $this->info("Existing services updated: {$updatedCount}");
            $this->info("Services skipped: {$skippedCount}");
            $this->info("Total services in database: " . Service::count());

            // Log the fetch
            Log::info('Services fetched from API', [
                'language' => $language,
                'api_services_count' => count($servicesFromApi),
                'new_services' => $storedCount,
                'updated_services' => $updatedCount,
                'skipped_services' => $skippedCount,
                'force_update' => $force
            ]);

            if ($storedCount > 0 || $updatedCount > 0) {
                $this->info("Consider running: php artisan sitemap:refresh");
            }

        } catch (\Exception $e) {
            $this->error("Error fetching services: " . $e->getMessage());
            Log::error('Error fetching services from API', [
                'error' => $e->getMessage(),
                'language' => $language
            ]);
            return 1;
        }

        return 0;
    }
}
