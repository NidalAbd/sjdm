<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Services\TranslationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslateServices extends Command
{
    protected $signature = 'services:translate {--force : Re-translate all} {--limit=0 : Limit services} {--batch=20 : Services per OpenAI batch}';
    protected $description = 'Auto-translate service names/categories to all languages using OpenAI';

    public function handle()
    {
        $force = $this->option('force');
        $limit = (int) $this->option('limit');
        $batchSize = (int) $this->option('batch');
        $apiKey = config('services.openai.key');

        if (empty($apiKey)) {
            $this->error('No OpenAI API key found. Add OPENAI_API_KEY to .env');
            return 1;
        }

        $query = Service::query();
        if (!$force) {
            $query->where(function ($q) {
                $q->whereNull('translations')->orWhere('translations', '');
            });
        }

        $total = $query->count();
        if ($total === 0) {
            $this->info('All services already translated. Use --force to redo.');
            return 0;
        }

        $count = $limit > 0 ? min($limit, $total) : $total;
        $this->info("Translating {$count} services via OpenAI (batch size: {$batchSize})...");

        $languages = array_diff(array_keys(TranslationService::SUPPORTED_LANGUAGES), ['en', 'ar']);
        $langNames = TranslationService::SUPPORTED_LANGUAGES;
        $processed = 0;
        $failed = 0;
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->orderBy('service_id')->chunkById($batchSize, function ($services) use ($apiKey, $languages, $langNames, &$processed, &$failed, $bar, $count) {
            if ($processed >= $count) return false;

            // Build batch: collect all names and categories
            $nameBatch = [];
            $catBatch = [];
            foreach ($services as $svc) {
                $name = $svc->name_en ?: $svc->name_ar ?: '';
                $cat = $svc->category_en ?: $svc->category_ar ?: '';
                if ($name) $nameBatch[$svc->service_id] = $name;
                if ($cat) $catBatch[$svc->service_id] = $cat;
            }

            // Translate names batch
            $nameTranslations = $this->translateBatch($nameBatch, $languages, $apiKey);
            // Translate categories batch
            $catTranslations = $this->translateBatch($catBatch, $languages, $apiKey);

            // Save results
            foreach ($services as $svc) {
                $translations = ['name' => ['en' => $svc->name_en, 'ar' => $svc->name_ar], 'category' => ['en' => $svc->category_en, 'ar' => $svc->category_ar]];

                foreach ($languages as $lang) {
                    $translations['name'][$lang] = $nameTranslations[$svc->service_id][$lang] ?? $svc->name_en;
                    $translations['category'][$lang] = $catTranslations[$svc->service_id][$lang] ?? $svc->category_en;
                }

                try {
                    $svc->translations = $translations;
                    $svc->save();
                    $processed++;
                } catch (\Exception $e) {
                    $failed++;
                }
                $bar->advance();

                if ($processed >= $count) break;
            }
        }, 'service_id');

        $bar->finish();
        $this->newLine(2);
        $this->info("Done! Processed: {$processed}, Failed: {$failed}");
        return 0;
    }

    private function translateBatch(array $items, array $languages, string $apiKey): array
    {
        if (empty($items)) return [];

        $results = [];
        foreach ($items as $id => $text) {
            $results[$id] = [];
        }

        // Send one OpenAI request per language (batch all service texts together)
        foreach ($languages as $lang) {
            $langName = TranslationService::SUPPORTED_LANGUAGES[$lang] ?? $lang;

            // Build JSON to translate
            $toTranslate = [];
            foreach ($items as $id => $text) {
                $toTranslate[(string)$id] = $text;
            }

            try {
                $json = json_encode($toTranslate, JSON_UNESCAPED_UNICODE);
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => "Translate JSON values from English to {$langName}. Keep keys same. Return only valid JSON."],
                        ['role' => 'user', 'content' => $json],
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 8192,
                ]);

                if ($response->successful()) {
                    $content = $response->json('choices.0.message.content', '');
                    $content = preg_replace('/^```(?:json)?\s*\n?/m', '', $content);
                    $content = preg_replace('/\n?```\s*$/m', '', $content);
                    $translated = json_decode(trim($content), true);

                    if (is_array($translated)) {
                        foreach ($translated as $id => $value) {
                            if (isset($results[(int)$id])) {
                                $results[(int)$id][$lang] = $value;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Service translate batch failed for {$lang}: " . $e->getMessage());
            }
        }

        return $results;
    }
}
