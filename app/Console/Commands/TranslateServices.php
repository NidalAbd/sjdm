<?php

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslateServices extends Command
{
    protected $signature = 'services:translate {--force : Re-translate all} {--limit=0 : Limit services} {--batch=40 : Services per API call} {--concurrency=10 : Concurrent API calls in flight}';
    protected $description = 'Translate service names/categories to all 15 languages, one language per call, fired at high concurrency';

    protected array $langs = ['es', 'fr', 'de', 'pt', 'ru', 'zh', 'ja', 'ko', 'hi', 'tr', 'it', 'pl', 'nl', 'vi', 'th'];

    protected array $langNames = [
        'es' => 'Spanish', 'fr' => 'French', 'de' => 'German', 'pt' => 'Portuguese',
        'ru' => 'Russian', 'zh' => 'Chinese', 'ja' => 'Japanese', 'ko' => 'Korean',
        'hi' => 'Hindi', 'tr' => 'Turkish', 'it' => 'Italian', 'pl' => 'Polish',
        'nl' => 'Dutch', 'vi' => 'Vietnamese', 'th' => 'Thai',
    ];

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

        $lastLang = end($this->langs);
        $query = Service::query();
        if (!$force) {
            // A service only counts as "fully done" once the last language in the
            // sequence is present — translations is non-null the moment any single
            // language (even just en/ar seeding) is written, so that alone can't be
            // used as the "needs work" signal once a run has been interrupted partway.
            $query->where(function ($q) use ($lastLang) {
                $q->whereNull('translations')
                    ->orWhereRaw("JSON_EXTRACT(translations, '$.name.\"{$lastLang}\"') IS NULL");
            });
        }

        $total = $query->count();
        if ($total === 0) {
            $this->info('All services already translated.');
            return 0;
        }

        $count = $limit > 0 ? min($limit, $total) : $total;
        $services = $query->orderBy('service_id')->limit($count)
            ->get(['service_id', 'name_en', 'name_ar', 'category_en', 'category_ar', 'translations']);

        // Seed every service with its en/ar translations up front so a partial run
        // (killed halfway) still leaves valid data instead of nulls.
        foreach ($services as $svc) {
            if (empty($svc->translations)) {
                $svc->translations = [
                    'name' => ['en' => $svc->name_en, 'ar' => $svc->name_ar],
                    'category' => ['en' => $svc->category_en, 'ar' => $svc->category_ar],
                ];
                $svc->save();
            }
        }

        // Per language, only the services still missing that specific language need a
        // call — this is what actually makes an interrupted run resume correctly instead
        // of re-translating languages a service already has.
        $perLangPending = [];
        $totalCalls = 0;
        foreach ($this->langs as $lang) {
            $pending = $services->filter(fn ($svc) => empty($svc->translations['name'][$lang] ?? null))->values();
            $perLangPending[$lang] = $pending;
            $totalCalls += (int) ceil($pending->count() / $batchSize);
        }

        $this->info("Translating {$count} services across " . count($this->langs) . " languages (batch: {$batchSize}, concurrency: {$concurrency}) — {$totalCalls} API calls needed...");

        $bar = $this->output->createProgressBar($totalCalls);
        $bar->start();

        $failed = 0;

        foreach ($this->langs as $lang) {
            $itemBatches = $perLangPending[$lang]->chunk($batchSize)->values();
            if ($itemBatches->isEmpty()) continue;

            foreach ($itemBatches->chunk($concurrency) as $group) {
                $responses = Http::pool(fn ($pool) => $group->map(
                    fn ($batch, $i) => $pool->as("b{$i}")
                        ->withHeaders([
                            'Authorization' => 'Bearer ' . $apiKey,
                            'Content-Type' => 'application/json',
                        ])
                        ->timeout(90)
                        ->connectTimeout(15)
                        ->post('https://api.openai.com/v1/chat/completions', $this->buildPayload($batch, $lang))
                ));

                foreach ($group as $i => $batch) {
                    $result = $this->parseResponse($responses["b{$i}"] ?? null);

                    if ($result === null) {
                        // One sequential retry for a batch that failed inside the pool.
                        sleep(1);
                        try {
                            $retry = Http::withHeaders([
                                'Authorization' => 'Bearer ' . $apiKey,
                                'Content-Type' => 'application/json',
                            ])->timeout(90)->post('https://api.openai.com/v1/chat/completions', $this->buildPayload($batch, $lang));
                            $result = $this->parseResponse($retry);
                        } catch (\Exception $e) {
                            Log::warning("Service translate retry failed [{$lang}]: " . $e->getMessage());
                        }
                    }

                    foreach ($batch as $svc) {
                        $val = is_array($result) ? ($result[(string) $svc->service_id] ?? null) : null;

                        if ($val && str_contains($val, '|||')) {
                            [$tName, $tCat] = array_map('trim', explode('|||', $val, 2));
                        } else {
                            $tName = $svc->name_en;
                            $tCat = $svc->category_en;
                            if ($val === null) $failed++;
                        }

                        $this->saveLangAtomic($svc->service_id, $lang, $tName, $tCat);
                    }

                    $bar->advance();
                }
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done! {$count} services x " . count($this->langs) . " languages. Calls that fell back to English: {$failed}");
        return 0;
    }

    /**
     * Merge one language's translation into a service's `translations` JSON with a row lock,
     * since different language passes write to the same row at different times.
     */
    protected function saveLangAtomic(int $serviceId, string $lang, string $name, string $category): void
    {
        DB::transaction(function () use ($serviceId, $lang, $name, $category) {
            $row = DB::selectOne('SELECT `translations` FROM `services` WHERE `service_id` = ? FOR UPDATE', [$serviceId]);
            $translations = json_decode($row->translations ?? '{}', true) ?: [];
            $translations['name'][$lang] = $name;
            $translations['category'][$lang] = $category;
            DB::update('UPDATE `services` SET `translations` = ? WHERE `service_id` = ?', [
                json_encode($translations, JSON_UNESCAPED_UNICODE),
                $serviceId,
            ]);
        });
    }

    protected function buildPayload($batch, string $lang): array
    {
        $input = [];
        foreach ($batch as $svc) {
            $name = $svc->name_en ?: $svc->name_ar ?: '';
            $cat = $svc->category_en ?: $svc->category_ar ?: '';
            if ($name || $cat) {
                $input[(string) $svc->service_id] = $name . ' ||| ' . $cat;
            }
        }

        $langName = $this->langNames[$lang] ?? $lang;
        $json = json_encode($input, JSON_UNESCAPED_UNICODE);

        return [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => "You translate SMM service names to {$langName}. Input: {id: \"name ||| category\"}. Output must be: {id: \"translated_name ||| translated_category\"}. Return ONLY valid JSON, no markdown."],
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
