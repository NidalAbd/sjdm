<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslateServices extends Command
{
    protected $signature = 'services:translate {--force : Re-translate all} {--limit=0 : Limit services} {--batch=15 : Services per API call} {--concurrency=5 : Concurrent API calls in flight}';
    protected $description = 'Translate service names/categories to all 15 languages in ONE OpenAI call per batch, firing batches concurrently';

    protected array $langs = ['es', 'fr', 'de', 'pt', 'ru', 'zh', 'ja', 'ko', 'hi', 'tr', 'it', 'pl', 'nl', 'vi', 'th'];

    public function handle()
    {
        $force = $this->option('force');
        $limit = (int) $this->option('limit');
        $batchSize = (int) $this->option('batch');
        $concurrency = (int) $this->option('concurrency');
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
        $this->info("Translating {$count} services (batch: {$batchSize}, concurrency: {$concurrency}, all 15 langs per call)...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        // Pull just the fields we need for every service up front, then chunk client-side.
        // (Pagination via chunkById doesn't compose with Http::pool concurrency groups cleanly.)
        $services = $query->orderBy('service_id')
            ->limit($count)
            ->get(['service_id', 'name_en', 'name_ar', 'category_en', 'category_ar']);

        $batches = $services->chunk($batchSize)->values();
        $processed = 0;
        $failed = 0;

        foreach ($batches->chunk($concurrency) as $group) {
            $responses = Http::pool(fn ($pool) => $group->map(
                fn ($batch, $i) => $pool->as("b{$i}")
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(180)
                    ->connectTimeout(20)
                    ->post('https://api.openai.com/v1/chat/completions', $this->buildPayload($batch))
            ));

            foreach ($group as $i => $batch) {
                $response = $responses["b{$i}"] ?? null;
                $result = $this->parseResponse($response);

                // One retry, sequentially, for a batch that failed inside the pool.
                if ($result === null) {
                    sleep(2);
                    try {
                        $retry = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type' => 'application/json',
                        ])->timeout(180)->post('https://api.openai.com/v1/chat/completions', $this->buildPayload($batch));
                        $result = $this->parseResponse($retry);
                    } catch (\Exception $e) {
                        Log::warning('Service translate retry failed: ' . $e->getMessage());
                    }
                }

                foreach ($batch as $svc) {
                    $translations = [
                        'name' => ['en' => $svc->name_en, 'ar' => $svc->name_ar],
                        'category' => ['en' => $svc->category_en, 'ar' => $svc->category_ar],
                    ];

                    $svcResult = is_array($result) ? ($result[(string) $svc->service_id] ?? null) : null;

                    foreach ($this->langs as $lang) {
                        $val = is_array($svcResult) ? ($svcResult[$lang] ?? null) : null;
                        if ($val && str_contains($val, '|||')) {
                            [$tName, $tCat] = array_map('trim', explode('|||', $val, 2));
                            $translations['name'][$lang] = $tName;
                            $translations['category'][$lang] = $tCat;
                        } else {
                            $translations['name'][$lang] = $svc->name_en;
                            $translations['category'][$lang] = $svc->category_en;
                        }
                    }

                    try {
                        $svc->translations = $translations;
                        $svc->save();
                        $processed++;
                        if ($svcResult === null) $failed++;
                    } catch (\Exception $e) {
                        $failed++;
                        Log::warning('Failed saving translation for service ' . $svc->service_id . ': ' . $e->getMessage());
                    }

                    $bar->advance();
                }
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done! Processed: {$processed}, Failed (fell back to English): {$failed}");
        return 0;
    }

    protected function buildPayload($batch): array
    {
        $input = [];
        foreach ($batch as $svc) {
            $name = $svc->name_en ?: $svc->name_ar ?: '';
            $cat = $svc->category_en ?: $svc->category_ar ?: '';
            if ($name || $cat) {
                $input[(string) $svc->service_id] = $name . ' ||| ' . $cat;
            }
        }

        $langList = implode(', ', $this->langs);
        $json = json_encode($input, JSON_UNESCAPED_UNICODE);

        return [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => "You translate SMM service names. Input has format: {id: \"name ||| category\"}. Output must be: {id: {lang: \"translated_name ||| translated_category\"}} for these languages: {$langList}. Return ONLY valid JSON. No markdown."],
                ['role' => 'user', 'content' => $json],
            ],
            'temperature' => 0.2,
            'max_tokens' => 16384,
        ];
    }

    protected function parseResponse($response): ?array
    {
        try {
            if (!$response || $response instanceof \Exception || !$response->successful()) {
                return null;
            }

            $content = $response->json('choices.0.message.content', '');
            $content = preg_replace('/^```(?:json)?\s*\n?/m', '', $content);
            $content = preg_replace('/\n?```\s*$/m', '', $content);
            $result = json_decode(trim($content), true);

            return is_array($result) ? $result : null;
        } catch (\Exception $e) {
            Log::warning('Error parsing translation response: ' . $e->getMessage());
            return null;
        }
    }
}
