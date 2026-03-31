<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Order;
use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * All supported language codes
     */
    private array $supportedLangs;

    public function __construct()
    {
        $this->supportedLangs = array_keys(config('app.available_locales', ['en' => 'English']));
    }

    /**
     * Platform mappings for filtering with aliases
     */
    private array $platforms = [
        'instagram' => ['en' => 'Instagram', 'ar' => 'انستقرام', 'aliases' => ['instagram', 'insta', 'ig']],
        'facebook' => ['en' => 'Facebook', 'ar' => 'فيسبوك', 'aliases' => ['facebook', 'fb']],
        'youtube' => ['en' => 'YouTube', 'ar' => 'يوتيوب', 'aliases' => ['youtube', 'yt']],
        'twitter' => ['en' => 'Twitter', 'ar' => 'تويتر', 'aliases' => ['twitter', 'x']],
        'tiktok' => ['en' => 'TikTok', 'ar' => 'تيك توك', 'aliases' => ['tiktok', 'tik tok', 'tt']],
        'telegram' => ['en' => 'Telegram', 'ar' => 'تيليجرام', 'aliases' => ['telegram', 'tg']],
        'spotify' => ['en' => 'Spotify', 'ar' => 'سبوتيفاي', 'aliases' => ['spotify']],
        'linkedin' => ['en' => 'LinkedIn', 'ar' => 'لينكد إن', 'aliases' => ['linkedin']],
        'snapchat' => ['en' => 'Snapchat', 'ar' => 'سناب شات', 'aliases' => ['snapchat', 'snap']],
        'twitch' => ['en' => 'Twitch', 'ar' => 'تويتش', 'aliases' => ['twitch']],
        'discord' => ['en' => 'Discord', 'ar' => 'ديسكورد', 'aliases' => ['discord']],
        'pinterest' => ['en' => 'Pinterest', 'ar' => 'بينتريست', 'aliases' => ['pinterest']],
        'soundcloud' => ['en' => 'SoundCloud', 'ar' => 'ساوند كلاود', 'aliases' => ['soundcloud']],
        'threads' => ['en' => 'Threads', 'ar' => 'ثريدز', 'aliases' => ['threads']],
        'website' => ['en' => 'Website', 'ar' => 'موقع', 'aliases' => ['website', 'traffic', 'web']],
    ];

    /**
     * Resolve language fields - for en/ar use dedicated columns, for others use JSON translations
     */
    private function resolveLanguage(string $lang): array
    {
        if ($lang === 'ar') {
            return ['nameField' => 'name_ar', 'categoryField' => 'category_ar', 'useJson' => false];
        }
        if ($lang === 'en') {
            return ['nameField' => 'name_en', 'categoryField' => 'category_en', 'useJson' => false];
        }
        // For other languages, we still query by en columns but transform output from JSON
        return ['nameField' => 'name_en', 'categoryField' => 'category_en', 'useJson' => true, 'lang' => $lang];
    }

    /**
     * Transform a service to include translated name/category
     */
    private function transformService($service, string $lang): array
    {
        $data = $service instanceof \Illuminate\Database\Eloquent\Model ? $service->toArray() : (array) $service;

        if (!in_array($lang, ['en', 'ar'])) {
            $translations = is_string($data['translations'] ?? null) ? json_decode($data['translations'], true) : ($data['translations'] ?? []);
            $data['name'] = $translations['name'][$lang] ?? $data['name_en'] ?? '';
            $data['category'] = $translations['category'][$lang] ?? $data['category_en'] ?? '';
        } else {
            $data['name'] = $lang === 'ar' ? ($data['name_ar'] ?? '') : ($data['name_en'] ?? '');
            $data['category'] = $lang === 'ar' ? ($data['category_ar'] ?? '') : ($data['category_en'] ?? '');
        }

        return $data;
    }

    /**
     * Get validated language from request
     */
    private function getLang(Request $request): string
    {
        $lang = $request->get('lang', 'en');
        return in_array($lang, $this->supportedLangs) ? $lang : 'en';
    }

    /**
     * Get all services with filtering, sorting, and pagination
     */
    public function services(Request $request): JsonResponse
    {
        $query = Service::query();
        $lang = $this->getLang($request);
        $resolved = $this->resolveLanguage($lang);
        $nameField = $resolved['nameField'];
        $categoryField = $resolved['categoryField'];

        // Apply search filter (always search in en columns for consistency)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->orWhere('service_id', 'like', '%' . $search . '%')
                    ->orWhere('name_en', 'like', '%' . $search . '%')
                    ->orWhere('category_en', 'like', '%' . $search . '%')
                    ->orWhere('name_ar', 'like', '%' . $search . '%')
                    ->orWhere('category_ar', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%');
            });
        }

        // Apply category filter (use English category for DB query)
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where(function ($q) use ($request) {
                $q->where('category_en', $request->category)
                  ->orWhere('category_ar', $request->category);
            });
        }

        // Apply platform filter using aliases
        if ($request->filled('platform') && $request->platform !== 'all') {
            $platformKey = $request->platform;
            if (isset($this->platforms[$platformKey])) {
                $platform = $this->platforms[$platformKey];
                $searchTerms = array_merge(
                    [$platform['en'], $platform['ar']],
                    $platform['aliases'] ?? []
                );
                $query->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $q->orWhere('category_en', 'like', '%' . $term . '%')
                          ->orWhere('name_en', 'like', '%' . $term . '%')
                          ->orWhere('category_ar', 'like', '%' . $term . '%')
                          ->orWhere('name_ar', 'like', '%' . $term . '%');
                    }
                });
            } else {
                $query->where('category_en', 'like', '%' . $platformKey . '%');
            }
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'service_id');
        $sortOrder = $request->get('sort_order', 'asc');
        $allowedSortFields = ['service_id', 'rate', 'min', 'max', 'created_at', 'name_en', 'category_en'];
        $allowedSortOrders = ['asc', 'desc'];

        if (in_array($sortBy, $allowedSortFields) && in_array($sortOrder, $allowedSortOrders)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('service_id', 'asc');
        }

        $totalCount = $query->count();

        $perPage = $request->get('per_page', 50);
        $services = $query->paginate($perPage);

        // Transform services with translated names
        $transformedServices = collect($services->items())->map(fn($s) => $this->transformService($s, $lang))->values();

        return response()->json([
            'services' => $transformedServices,
            'pagination' => [
                'total' => $services->total(),
                'per_page' => $services->perPage(),
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
                'from' => $services->firstItem(),
                'to' => $services->lastItem(),
            ],
            'total_services' => $totalCount,
        ]);
    }

    /**
     * Get a single service with related services
     */
    public function service($id, Request $request): JsonResponse
    {
        $lang = $this->getLang($request);
        $service = Service::where('service_id', $id)->first();

        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        // Get related services using English category for matching
        $relatedServices = Service::where('category_en', $service->category_en)
            ->where('service_id', '!=', $service->service_id)
            ->select(['service_id', 'name_en', 'name_ar', 'category_en', 'category_ar', 'translations', 'rate', 'min', 'max', 'type', 'refill', 'cancel'])
            ->limit(6)
            ->get()
            ->map(fn($s) => $this->transformService($s, $lang));

        return response()->json([
            'service' => $this->transformService($service, $lang),
            'related_services' => $relatedServices,
        ]);
    }

    /**
     * Get stats for the home page
     */
    public function stats(): JsonResponse
    {
        $stats = Cache::remember('home_stats', 300, function () {
            return [
                'totalServices' => Service::count(),
                'totalOrders' => class_exists(Order::class) ? Order::count() + 254859 : 254859,
                'totalUsers' => class_exists(User::class) ? User::count() + 79778 : 79778,
                'completedOrders' => class_exists(Order::class) ? Order::where('status', 'completed')->count() + 202278 : 202278,
            ];
        });

        return response()->json($stats);
    }

    /**
     * Get categories with counts, optionally filtered by platform
     */
    public function categories(Request $request): JsonResponse
    {
        $lang = $this->getLang($request);
        // Always query by English category for consistency, then translate in output
        $platformKey = $request->get('platform');

        if ($platformKey && isset($this->platforms[$platformKey])) {
            $platform = $this->platforms[$platformKey];
            $searchTerms = array_merge(
                [$platform['en'], $platform['ar']],
                $platform['aliases'] ?? []
            );

            $categories = DB::table('services')
                ->select('category_en as name', DB::raw('COUNT(*) as count'))
                ->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $q->orWhere('category_en', 'like', '%' . $term . '%')
                          ->orWhere('category_ar', 'like', '%' . $term . '%');
                    }
                })
                ->whereNotNull('category_en')
                ->where('category_en', '!=', '')
                ->groupBy('category_en')
                ->orderBy('category_en')
                ->get();
        } else {
            $categories = Cache::remember('categories_en_all', 3600, function() {
                return DB::table('services')
                    ->select('category_en as name', DB::raw('COUNT(*) as count'))
                    ->whereNotNull('category_en')
                    ->where('category_en', '!=', '')
                    ->groupBy('category_en')
                    ->orderBy('category_en')
                    ->get();
            });
        }

        // If not English, translate category names from stored translations
        if (!in_array($lang, ['en'])) {
            $categories = $categories->map(function ($cat) use ($lang) {
                $catName = $cat->name;
                // Try to find a service with this category and get its translated category
                $service = Service::where('category_en', $catName)->whereNotNull('translations')->first();
                if ($service) {
                    $cat->name = $service->getTranslatedCategory($lang);
                }
                return $cat;
            });
        }

        return response()->json([
            'categories' => $categories,
        ]);
    }

    /**
     * Get platform statistics for filtering
     */
    public function platforms(Request $request): JsonResponse
    {
        $lang = $this->getLang($request);

        $stats = Cache::remember('platform_stats_all', 3600, function() {
            $platformStats = [];
            foreach ($this->platforms as $key => $platform) {
                $searchTerms = array_merge(
                    [$platform['en'], $platform['ar']],
                    $platform['aliases'] ?? []
                );

                $count = Service::where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $q->orWhere('category_en', 'like', '%' . $term . '%')
                          ->orWhere('name_en', 'like', '%' . $term . '%')
                          ->orWhere('category_ar', 'like', '%' . $term . '%')
                          ->orWhere('name_ar', 'like', '%' . $term . '%');
                    }
                })->count();

                if ($count > 0) {
                    $platformStats[] = [
                        'key' => $key,
                        'name_en' => $platform['en'],
                        'name_ar' => $platform['ar'],
                        'count' => $count,
                        'icon' => $this->getPlatformIcon($key),
                    ];
                }
            }
            usort($platformStats, fn($a, $b) => $b['count'] - $a['count']);
            return $platformStats;
        });

        // Add localized name based on current language
        $localizedStats = collect($stats)->map(function ($p) use ($lang) {
            $p['name'] = in_array($lang, ['ar']) ? $p['name_ar'] : $p['name_en'];
            return $p;
        })->values();

        return response()->json([
            'platforms' => $localizedStats,
        ]);
    }

    /**
     * Get featured/popular services
     */
    public function featured(Request $request): JsonResponse
    {
        $lang = $this->getLang($request);

        $featured = Cache::remember('featured_services_all', 3600, function() {
            return Service::where('rate', '<', 5)
                ->orderBy('rate', 'asc')
                ->limit(6)
                ->get(['service_id', 'name_en', 'name_ar', 'category_en', 'category_ar', 'translations', 'rate', 'min', 'max', 'type', 'refill', 'cancel']);
        });

        $transformed = $featured->map(fn($s) => $this->transformService($s, $lang));

        return response()->json([
            'featured' => $transformed,
        ]);
    }

    /**
     * Get supported languages list for frontend
     */
    public function languages(): JsonResponse
    {
        return response()->json([
            'languages' => TranslationService::LANGUAGE_NAMES,
            'supported' => TranslationService::getSupportedLanguageCodes(),
            'rtl' => TranslationService::RTL_LANGUAGES,
        ]);
    }

    /**
     * Get platform icon class
     */
    private function getPlatformIcon(string $platform): string
    {
        $icons = [
            'instagram' => 'mdi-instagram',
            'facebook' => 'mdi-facebook',
            'youtube' => 'mdi-youtube',
            'twitter' => 'mdi-twitter',
            'tiktok' => 'mdi-music-note',
            'telegram' => 'mdi-telegram',
            'spotify' => 'mdi-spotify',
            'linkedin' => 'mdi-linkedin',
            'snapchat' => 'mdi-snapchat',
            'twitch' => 'mdi-twitch',
            'discord' => 'mdi-discord',
            'pinterest' => 'mdi-pinterest',
            'soundcloud' => 'mdi-soundcloud',
            'threads' => 'mdi-at',
            'website' => 'mdi-web',
        ];

        return $icons[$platform] ?? 'mdi-help-circle';
    }
}
