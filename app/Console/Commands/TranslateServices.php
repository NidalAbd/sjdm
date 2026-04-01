<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Services\TranslationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslateServices extends Command
{
    protected $signature = 'services:translate {--force : Re-translate all} {--limit=0 : Limit services} {--batch=10 : Services per batch}';
    protected $description = 'Translate service names/categories to all 15 languages in ONE OpenAI call per batch';

    public function handle()
    {
        $force = $this->option('force');
        $limit = (int) $this->option('limit');
        $batchSize = (int) $this->option('batch');
        $apiKey = config('services.openai.key');

        if (empty($apiKey)) {
            $this->error('No OPENAI_API_KEY in .env');
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
            $this->info('All services already translated.');
            return 0;
        }

        $count = $limit > 0 ? min($limit, $total) : $total;
        $this->info("Translating {$count} services (batch: {$batchSize}, all 15 langs per call)...");

        $langs = ['es','fr','de','pt','ru','zh','ja','ko','hi','tr','it','pl','nl','vi','th'];
        $processed = 0;
        $failed = 0;
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->orderBy('service_id')->chunkById($batchSize, function ($services) use ($apiKey, $langs, &$processed, &$failed, $bar, $count) {
            if ($processed >= $count) return false;

            // Build input: {service_id: "name | category"}
            $input = [];
            foreach ($services as $svc) {
                $name = $svc->name_en ?: $svc->name_ar ?: '';
                $cat = $svc->category_en ?: $svc->category_ar ?: '';
                if ($name || $cat) {
                    $input[(string)$svc->service_id] = $name . ' ||| ' . $cat;
                }
            }

            if (empty($input)) {
                foreach ($services as $svc) { $bar->advance(); $processed++; }
                return;
            }

            // One API call: translate all services to all 15 languages at once
            $langList = implode(', ', $langs);
            $json = json_encode($input, JSON_UNESCAPED_UNICODE);

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(180)->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => "You translate SMM service names. Input has format: {id: \"name ||| category\"}. Output must be: {id: {lang: \"translated_name ||| translated_category\"}} for these languages: {$langList}. Return ONLY valid JSON. No markdown."],
                        ['role' => 'user', 'content' => $json],
                    ],
                    'temperature' => 0.2,
                    'max_tokens' => 16384,
                ]);

                if ($response->successful()) {
                    $content = $response->json('choices.0.message.content', '');
                    $content = preg_replace('/^```(?:json)?\s*\n?/m', '', $content);
                    $content = preg_replace('/\n?```\s*$/m', '', $content);
                    $result = json_decode(trim($content), true);

                    if (is_array($result)) {
                        foreach ($services as $svc) {
                            $translations = [
                                'name' => ['en' => $svc->name_en, 'ar' => $svc->name_ar],
                                'category' => ['en' => $svc->category_en, 'ar' => $svc->category_ar],
                            ];

                            $svcResult = $result[(string)$svc->service_id] ?? null;
                            if (is_array($svcResult)) {
                                foreach ($langs as $lang) {
                                    $val = $svcResult[$lang] ?? null;
                                    if ($val && str_contains($val, '|||')) {
                                        [$tName, $tCat] = array_map('trim', explode('|||', $val, 2));
                                        $translations['name'][$lang] = $tName;
                                        $translations['category'][$lang] = $tCat;
                                    } else {
                                        $translations['name'][$lang] = $val ?? $svc->name_en;
                                        $translations['category'][$lang] = $svc->category_en;
                                    }
                                }
                            } else {
                                foreach ($langs as $lang) {
                                    $translations['name'][$lang] = $svc->name_en;
                                    $translations['category'][$lang] = $svc->category_en;
                                }
                            }

                            try {
                                $svc->translations = $translations;
                                $svc->save();
                                $processed++;
                            } catch (\Exception $e) { $failed++; }

                            $bar->advance();
                            if ($processed >= $count) return;
                        }
                    } else {
                        // Parse failed
                        foreach ($services as $svc) { $failed++; $bar->advance(); }
                    }
                } else {
                    Log::warning('OpenAI translate failed: ' . $response->status() . ' ' . substr($response->body(), 0, 200));
                    foreach ($services as $svc) { $failed++; $bar->advance(); }
                }
            } catch (\Exception $e) {
                Log::warning('Service translate error: ' . $e->getMessage());
                foreach ($services as $svc) { $failed++; $bar->advance(); }
            }
        }, 'service_id');

        $bar->finish();
        $this->newLine(2);
        $this->info("Done! Processed: {$processed}, Failed: {$failed}");
        return 0;
    }
}
