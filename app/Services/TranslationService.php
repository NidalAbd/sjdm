<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    /**
     * All 17 supported languages
     */
    public const SUPPORTED_LANGUAGES = [
        'en' => 'English',
        'zh' => '中文',
        'hi' => 'हिन्दी',
        'es' => 'Español',
        'fr' => 'Français',
        'ar' => 'العربية',
        'pt' => 'Português',
        'ru' => 'Русский',
        'ja' => '日本語',
        'de' => 'Deutsch',
        'ko' => '한국어',
        'tr' => 'Türkçe',
        'it' => 'Italiano',
        'pl' => 'Polski',
        'nl' => 'Nederlands',
        'vi' => 'Tiếng Việt',
        'th' => 'ไทย',
    ];

    /**
     * RTL languages
     */
    public const RTL_LANGUAGES = ['ar'];

    /**
     * Language native names for UI display
     */
    public const LANGUAGE_NAMES = [
        'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇺🇸'],
        'zh' => ['name' => 'Chinese', 'native' => '中文', 'flag' => '🇨🇳'],
        'hi' => ['name' => 'Hindi', 'native' => 'हिन्दी', 'flag' => '🇮🇳'],
        'es' => ['name' => 'Spanish', 'native' => 'Español', 'flag' => '🇪🇸'],
        'fr' => ['name' => 'French', 'native' => 'Français', 'flag' => '🇫🇷'],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦'],
        'pt' => ['name' => 'Portuguese', 'native' => 'Português', 'flag' => '🇧🇷'],
        'ru' => ['name' => 'Russian', 'native' => 'Русский', 'flag' => '🇷🇺'],
        'ja' => ['name' => 'Japanese', 'native' => '日本語', 'flag' => '🇯🇵'],
        'de' => ['name' => 'German', 'native' => 'Deutsch', 'flag' => '🇩🇪'],
        'ko' => ['name' => 'Korean', 'native' => '한국어', 'flag' => '🇰🇷'],
        'tr' => ['name' => 'Turkish', 'native' => 'Türkçe', 'flag' => '🇹🇷'],
        'it' => ['name' => 'Italian', 'native' => 'Italiano', 'flag' => '🇮🇹'],
        'pl' => ['name' => 'Polish', 'native' => 'Polski', 'flag' => '🇵🇱'],
        'nl' => ['name' => 'Dutch', 'native' => 'Nederlands', 'flag' => '🇳🇱'],
        'vi' => ['name' => 'Vietnamese', 'native' => 'Tiếng Việt', 'flag' => '🇻🇳'],
        'th' => ['name' => 'Thai', 'native' => 'ไทย', 'flag' => '🇹🇭'],
    ];

    /**
     * Translate text using Google Translate free API
     */
    public function translate(string $text, string $targetLang, string $sourceLang = 'en'): ?string
    {
        if (empty(trim($text))) {
            return $text;
        }

        if ($targetLang === $sourceLang) {
            return $text;
        }

        // Check cache first
        $cacheKey = 'translation_' . md5($text . $targetLang . $sourceLang);
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        try {
            $response = Http::timeout(10)
                ->get('https://translate.googleapis.com/translate_a/single', [
                    'client' => 'gtx',
                    'sl' => $sourceLang,
                    'tl' => $targetLang,
                    'dt' => 't',
                    'q' => $text,
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $translated = '';

                if (is_array($result) && isset($result[0])) {
                    foreach ($result[0] as $segment) {
                        if (isset($segment[0])) {
                            $translated .= $segment[0];
                        }
                    }
                }

                if (!empty($translated)) {
                    // Cache for 30 days
                    Cache::put($cacheKey, $translated, 60 * 60 * 24 * 30);
                    return $translated;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Translation failed for "' . substr($text, 0, 50) . '" to ' . $targetLang . ': ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Translate text to all supported languages
     */
    public function translateToAll(string $text, string $sourceLang = 'en'): array
    {
        $translations = [];

        foreach (array_keys(self::SUPPORTED_LANGUAGES) as $lang) {
            if ($lang === $sourceLang) {
                $translations[$lang] = $text;
                continue;
            }

            $translated = $this->translate($text, $lang, $sourceLang);
            $translations[$lang] = $translated ?? $text; // Fallback to original if translation fails

            // Small delay to avoid rate limiting
            usleep(100000); // 100ms
        }

        return $translations;
    }

    /**
     * Get languages that need translation (excluding en and ar which have dedicated columns)
     */
    public function getTranslationLanguages(): array
    {
        return array_diff(array_keys(self::SUPPORTED_LANGUAGES), ['en', 'ar']);
    }

    /**
     * Check if a language is RTL
     */
    public static function isRtl(string $lang): bool
    {
        return in_array($lang, self::RTL_LANGUAGES);
    }

    /**
     * Get all supported language codes
     */
    public static function getSupportedLanguageCodes(): array
    {
        return array_keys(self::SUPPORTED_LANGUAGES);
    }
}
