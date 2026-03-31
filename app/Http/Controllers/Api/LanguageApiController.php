<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
