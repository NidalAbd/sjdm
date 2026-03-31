<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Services\TranslationService;
use Illuminate\Console\Command;

class TranslateServices extends Command
{
    protected $signature = 'services:translate {--force : Re-translate all services even if already translated} {--limit=0 : Limit number of services to translate (0 = all)}';
    protected $description = 'Auto-translate all service names and categories to all 17 supported languages';

    public function handle(TranslationService $translationService)
    {
        $force = $this->option('force');
        $limit = (int) $this->option('limit');

        $query = Service::query();

        if (!$force) {
            $query->where(function ($q) {
                $q->whereNull('translations')
                  ->orWhere('translations', '');
            });
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('All services are already translated. Use --force to re-translate.');
            return 0;
        }

        if ($limit > 0) {
            $query->limit($limit);
            $this->info("Translating {$limit} of {$total} services...");
        } else {
            $this->info("Translating {$total} services to " . count(TranslationService::SUPPORTED_LANGUAGES) . " languages...");
        }

        $bar = $this->output->createProgressBar($limit > 0 ? $limit : $total);
        $bar->start();

        $languages = $translationService->getTranslationLanguages();
        $processed = 0;
        $failed = 0;

        $query->chunkById(50, function ($services) use ($translationService, $languages, &$processed, &$failed, $bar) {
            foreach ($services as $service) {
                $translations = ['name' => [], 'category' => []];

                // Include en and ar from existing columns
                $translations['name']['en'] = $service->name_en;
                $translations['name']['ar'] = $service->name_ar;
                $translations['category']['en'] = $service->category_en;
                $translations['category']['ar'] = $service->category_ar;

                // Translate name to all other languages
                foreach ($languages as $lang) {
                    $sourceName = !empty($service->name_en) ? $service->name_en : $service->name_ar;
                    $sourceCategory = !empty($service->category_en) ? $service->category_en : $service->category_ar;
                    $sourceLang = !empty($service->name_en) ? 'en' : 'ar';

                    if (!empty($sourceName)) {
                        $translated = $translationService->translate($sourceName, $lang, $sourceLang);
                        $translations['name'][$lang] = $translated ?? $sourceName;
                    } else {
                        $translations['name'][$lang] = '';
                    }

                    if (!empty($sourceCategory)) {
                        $translated = $translationService->translate($sourceCategory, $lang, $sourceLang);
                        $translations['category'][$lang] = $translated ?? $sourceCategory;
                    } else {
                        $translations['category'][$lang] = '';
                    }

                    usleep(50000); // 50ms delay between API calls
                }

                try {
                    $service->translations = $translations;
                    $service->save();
                    $processed++;
                } catch (\Exception $e) {
                    $failed++;
                    $this->newLine();
                    $this->error("Failed to save service {$service->service_id}: " . $e->getMessage());
                }

                $bar->advance();
            }
        }, 'service_id');

        $bar->finish();
        $this->newLine(2);
        $this->info("Done! Processed: {$processed}, Failed: {$failed}");

        return 0;
    }
}
