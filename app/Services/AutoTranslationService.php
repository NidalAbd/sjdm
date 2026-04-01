<?php

namespace App\Services;

use App\Models\Translation;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AutoTranslationService
{
    protected string $model = 'gpt-4o-mini';
    protected int $batchSize = 50;

    /**
     * Tier 1: Translate UI strings from translations table
     * Tries OpenAI first, falls back to Google Translate free API
     */
    public function translateTier1(string $targetLocale, string $targetLanguageName): array
    {
        $apiKey = config('services.openai.key', '');

        $sourceLocale = 'en';
        $sourceTranslations = Translation::where('locale', $sourceLocale)->get();
        $total = $sourceTranslations->count();
        $completed = 0;
        $errors = 0;

        // Check which already exist
        $existingRows = Translation::where('locale', $targetLocale)->get();
        $existing = [];
        foreach ($existingRows as $row) {
            $existing["{$row->group}.{$row->key}"] = $row->value;
        }

        $toTranslate = [];
        foreach ($sourceTranslations as $trans) {
            $compositeKey = "{$trans->group}.{$trans->key}";
            if (!empty($existing[$compositeKey])) {
                $completed++;
                continue;
            }
            $toTranslate[$compositeKey] = $trans->value;
        }

        if (empty($toTranslate)) {
            return ['success' => true, 'total' => $total, 'completed' => $completed, 'errors' => 0];
        }

        // Try OpenAI first if key exists
        $useOpenAI = !empty($apiKey);
        if ($useOpenAI) {
            // Test if OpenAI works with a small batch first
            $testBatch = array_slice($toTranslate, 0, 1, true);
            $testResult = $this->translateBatchOpenAI($testBatch, $targetLanguageName, $apiKey);
            if ($testResult === null) {
                Log::info("OpenAI failed, falling back to Google Translate for {$targetLocale}");
                $useOpenAI = false;
            }
        }

        if ($useOpenAI) {
            $batches = array_chunk($toTranslate, $this->batchSize, true);
            foreach ($batches as $batch) {
                $translated = $this->translateBatchOpenAI($batch, $targetLanguageName, $apiKey);
                if ($translated) {
                    foreach ($translated as $compositeKey => $translatedText) {
                        if ($translatedText) {
                            [$grp, $key] = explode('.', $compositeKey, 2);
                            Translation::setTranslation($targetLocale, $grp, $key, $translatedText);
                            $completed++;
                        } else {
                            $errors++;
                            $completed++;
                        }
                    }
                } else {
                    $errors += count($batch);
                    $completed += count($batch);
                }
            }
        } else {
            // Use Google Translate free API
            $translationService = new TranslationService();
            foreach ($toTranslate as $compositeKey => $value) {
                $translated = $translationService->translate($value, $targetLocale, 'en');
                if ($translated) {
                    [$grp, $key] = explode('.', $compositeKey, 2);
                    Translation::setTranslation($targetLocale, $grp, $key, $translated);
                    $completed++;
                } else {
                    $errors++;
                    $completed++;
                }
                usleep(100000); // 100ms delay
            }
        }

        Translation::clearCache($targetLocale);
        return ['success' => true, 'total' => $total, 'completed' => $completed, 'errors' => $errors];
    }

    /**
     * Tier 1 fallback: Use Google Translate free API
     */
    public function translateTier1Google(string $targetLocale): array
    {
        $translationService = new TranslationService();
        $sourceTranslations = Translation::where('locale', 'en')->get();
        $total = $sourceTranslations->count();
        $completed = 0;
        $errors = 0;

        $existingRows = Translation::where('locale', $targetLocale)->get();
        $existing = [];
        foreach ($existingRows as $row) {
            $existing["{$row->group}.{$row->key}"] = $row->value;
        }

        foreach ($sourceTranslations as $trans) {
            $compositeKey = "{$trans->group}.{$trans->key}";
            if (!empty($existing[$compositeKey])) {
                $completed++;
                continue;
            }

            $translated = $translationService->translate($trans->value, $targetLocale, 'en');
            if ($translated) {
                Translation::setTranslation($targetLocale, $trans->group, $trans->key, $translated);
                $completed++;
            } else {
                $errors++;
                $completed++;
            }

            usleep(100000); // 100ms delay
        }

        Translation::clearCache($targetLocale);
        return ['success' => true, 'total' => $total, 'completed' => $completed, 'errors' => $errors];
    }

    /**
     * Tier 2: Translate service names/categories
     */
    public function translateServices(string $targetLocale, string $targetLanguageName, ?int $limit = null): array
    {
        $apiKey = config('services.openai.key', '');
        $translationService = new TranslationService();
        $completed = 0;
        $errors = 0;

        $query = Service::query();
        if ($limit) $query->limit($limit);
        $services = $query->get();

        foreach ($services as $service) {
            $translations = $service->translations ?? ['name' => [], 'category' => []];

            // Skip if already translated
            if (!empty($translations['name'][$targetLocale])) {
                $completed++;
                continue;
            }

            $sourceName = $service->name_en ?: $service->name_ar;
            $sourceCategory = $service->category_en ?: $service->category_ar;

            if (!empty($apiKey)) {
                $batch = [];
                if ($sourceName) $batch['name'] = $sourceName;
                if ($sourceCategory) $batch['category'] = $sourceCategory;

                $translated = $this->translateBatchOpenAI($batch, $targetLanguageName, $apiKey);
                if ($translated) {
                    $translations['name'][$targetLocale] = $translated['name'] ?? $sourceName;
                    $translations['category'][$targetLocale] = $translated['category'] ?? $sourceCategory;
                } else {
                    $errors++;
                }
            } else {
                // Fallback to Google free
                $translatedName = $translationService->translate($sourceName, $targetLocale);
                $translatedCategory = $translationService->translate($sourceCategory, $targetLocale);
                $translations['name'][$targetLocale] = $translatedName ?? $sourceName;
                $translations['category'][$targetLocale] = $translatedCategory ?? $sourceCategory;
                usleep(100000);
            }

            // Always include en/ar
            $translations['name']['en'] = $service->name_en;
            $translations['name']['ar'] = $service->name_ar;
            $translations['category']['en'] = $service->category_en;
            $translations['category']['ar'] = $service->category_ar;

            $service->translations = $translations;
            $service->save();
            $completed++;
        }

        return ['success' => true, 'completed' => $completed, 'errors' => $errors];
    }

    protected function translateBatchOpenAI(array $batch, string $targetLanguage, string $apiKey): ?array
    {
        try {
            $jsonInput = json_encode($batch, JSON_UNESCAPED_UNICODE);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are a professional translator for an SMM panel (social media marketing platform). Translate text from English to {$targetLanguage}. Return only valid JSON.",
                    ],
                    [
                        'role' => 'user',
                        'content' => "Translate the JSON values to {$targetLanguage}. Keep keys the same. Only translate values. Return ONLY JSON, no markdown.\n\n{$jsonInput}",
                    ],
                ],
                'temperature' => 0.3,
                'max_tokens' => 8192,
            ]);

            if (!$response->successful()) return null;

            $content = $response->json('choices.0.message.content', '');
            $content = preg_replace('/^```(?:json)?\s*\n?/m', '', $content);
            $content = preg_replace('/\n?```\s*$/m', '', $content);
            $translated = json_decode(trim($content), true);

            return json_last_error() === JSON_ERROR_NONE ? $translated : null;
        } catch (\Exception $e) {
            Log::error("OpenAI translation failed: " . $e->getMessage());
            return null;
        }
    }
}
