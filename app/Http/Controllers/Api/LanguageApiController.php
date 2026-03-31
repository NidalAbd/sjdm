<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use App\Services\AutoTranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class LanguageApiController extends Controller
{
    public function index(): JsonResponse
    {
        $languages = Language::getActiveOrdered();
        $defaultLanguage = Language::getDefault();

        return response()->json([
            'success' => true,
            'default_locale' => $defaultLanguage?->code ?? 'en',
            'languages' => $languages->map(fn($l) => [
                'code' => $l->code,
                'name' => $l->name,
                'native_name' => $l->native_name,
                'direction' => $l->direction,
                'is_default' => $l->is_default,
            ]),
        ]);
    }

    public function translations(string $locale): JsonResponse
    {
        $language = Language::getByCode($locale);

        if (!$language || !$language->is_active) {
            return response()->json(['success' => false, 'message' => 'Language not found'], 404);
        }

        $translations = Translation::getTranslationsForLocale($locale);
        $version = $language->getTranslationsVersionHash();

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'version' => $version,
            'direction' => $language->direction,
            'translations' => $translations,
        ]);
    }

    /**
     * Admin: List all languages with translation counts
     */
    public function adminIndex(): JsonResponse
    {
        $languages = Language::ordered()->get();

        $languagesData = $languages->map(function ($lang) {
            return array_merge($lang->toArray(), [
                'translations_count' => Translation::where('locale', $lang->code)->count(),
            ]);
        });

        $totalTranslations = Translation::count();

        return response()->json([
            'languages' => $languagesData,
            'stats' => ['total' => $totalTranslations],
        ]);
    }

    /**
     * Admin: Toggle language active status
     */
    public function toggle(string $code): JsonResponse
    {
        $language = Language::where('code', $code)->first();
        if (!$language) return response()->json(['error' => 'Language not found'], 404);

        $language->is_active = !$language->is_active;
        $language->save();

        return response()->json(['success' => true, 'is_active' => $language->is_active]);
    }

    /**
     * Admin: Seed base EN/AR translations
     */
    public function seed(): JsonResponse
    {
        Artisan::call('translations:seed');

        $enCount = Translation::where('locale', 'en')->count();
        $arCount = Translation::where('locale', 'ar')->count();

        return response()->json([
            'success' => true,
            'en_count' => $enCount,
            'ar_count' => $arCount,
        ]);
    }

    /**
     * Admin: Auto-translate to a specific language
     */
    public function translate(string $code): JsonResponse
    {
        $language = Language::where('code', $code)->first();
        if (!$language) return response()->json(['error' => 'Language not found'], 404);

        $service = new AutoTranslationService();
        $result = $service->translateTier1($code, $language->name);

        return response()->json($result);
    }

    public function checkUpdates(Request $request, string $locale): JsonResponse
    {
        $clientVersion = $request->query('version', '');
        $language = Language::getByCode($locale);

        if (!$language || !$language->is_active) {
            return response()->json(['success' => false], 404);
        }

        $serverVersion = $language->getTranslationsVersionHash();

        return response()->json([
            'success' => true,
            'has_updates' => $clientVersion !== $serverVersion,
            'server_version' => $serverVersion,
        ]);
    }
}
