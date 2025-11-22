<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Platform mappings for filtering
     */
    private array $platforms = [
        'instagram' => ['en' => 'Instagram', 'ar' => 'انستقرام'],
        'facebook' => ['en' => 'Facebook', 'ar' => 'فيسبوك'],
        'youtube' => ['en' => 'YouTube', 'ar' => 'يوتيوب'],
        'twitter' => ['en' => 'Twitter', 'ar' => 'تويتر'],
        'tiktok' => ['en' => 'TikTok', 'ar' => 'تيك توك'],
        'telegram' => ['en' => 'Telegram', 'ar' => 'تيليجرام'],
        'spotify' => ['en' => 'Spotify', 'ar' => 'سبوتيفاي'],
        'linkedin' => ['en' => 'LinkedIn', 'ar' => 'لينكد إن'],
        'snapchat' => ['en' => 'Snapchat', 'ar' => 'سناب شات'],
        'twitch' => ['en' => 'Twitch', 'ar' => 'تويتش'],
        'discord' => ['en' => 'Discord', 'ar' => 'ديسكورد'],
        'pinterest' => ['en' => 'Pinterest', 'ar' => 'بينتريست'],
        'soundcloud' => ['en' => 'SoundCloud', 'ar' => 'ساوند كلاود'],
        'website' => ['en' => 'Website', 'ar' => 'موقع'],
    ];

    /**
     * Get all services with filtering, sorting, and pagination
     */
    public function services(Request $request): JsonResponse
    {
        $query = Service::query();
        $lang = $request->get('lang', 'en');

        // Define fields based on language
        $nameField = $lang === 'ar' ? 'name_ar' : 'name_en';
        $categoryField = $lang === 'ar' ? 'category_ar' : 'category_en';

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $nameField, $categoryField) {
                $q->orWhere('service_id', 'like', '%' . $search . '%')
                    ->orWhere($nameField, 'like', '%' . $search . '%')
                    ->orWhere($categoryField, 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%');
            });
        }

        // Apply category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where($categoryField, $request->category);
        }

        // Apply platform filter
        if ($request->filled('platform') && $request->platform !== 'all') {
            $platformName = $this->platforms[$request->platform][$lang] ?? $request->platform;
            $query->where($categoryField, 'like', '%' . $platformName . '%');
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'service_id');
        $sortOrder = $request->get('sort_order', 'asc');
        $allowedSortFields = ['service_id', 'rate', 'min', 'max', 'created_at', $nameField, $categoryField];
        $allowedSortOrders = ['asc', 'desc'];

        if (in_array($sortBy, $allowedSortFields) && in_array($sortOrder, $allowedSortOrders)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('service_id', 'asc');
        }

        // Get total count before pagination
        $totalCount = $query->count();

        // Pagination
        $perPage = $request->get('per_page', 50);
        $services = $query->paginate($perPage);

        return response()->json([
            'services' => $services->items(),
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
        $lang = $request->get('lang', 'en');
        $categoryField = $lang === 'ar' ? 'category_ar' : 'category_en';
        $nameField = $lang === 'ar' ? 'name_ar' : 'name_en';

        $service = Service::where('service_id', $id)->first();

        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        // Get related services
        $relatedServices = Service::where($categoryField, $service->$categoryField)
            ->where('service_id', '!=', $service->service_id)
            ->select(['service_id', 'name_en', 'name_ar', 'category_en', 'category_ar', 'rate', 'min', 'max', 'type', 'refill', 'cancel'])
            ->limit(6)
            ->get();

        return response()->json([
            'service' => $service,
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
     * Get categories with counts
     */
    public function categories(Request $request): JsonResponse
    {
        $lang = $request->get('lang', 'en');
        $categoryField = $lang === 'ar' ? 'category_ar' : 'category_en';

        $categories = Cache::remember('categories_' . $lang, 3600, function() use ($categoryField) {
            return DB::table('services')
                ->select($categoryField . ' as name', DB::raw('COUNT(*) as count'))
                ->whereNotNull($categoryField)
                ->where($categoryField, '!=', '')
                ->groupBy($categoryField)
                ->orderBy($categoryField)
                ->get();
        });

        return response()->json([
            'categories' => $categories,
        ]);
    }

    /**
     * Get platform statistics for filtering
     */
    public function platforms(Request $request): JsonResponse
    {
        $lang = $request->get('lang', 'en');
        $categoryField = $lang === 'ar' ? 'category_ar' : 'category_en';

        $stats = Cache::remember('platform_stats_' . $lang, 3600, function() use ($lang, $categoryField) {
            $platformStats = [];
            foreach ($this->platforms as $key => $platform) {
                $platformName = $platform[$lang] ?? $key;
                $count = Service::where($categoryField, 'like', '%' . $platformName . '%')->count();

                if ($count > 0) {
                    $platformStats[] = [
                        'key' => $key,
                        'name' => $platformName,
                        'count' => $count,
                        'icon' => $this->getPlatformIcon($key),
                    ];
                }
            }
            // Sort by count descending
            usort($platformStats, fn($a, $b) => $b['count'] - $a['count']);
            return $platformStats;
        });

        return response()->json([
            'platforms' => $stats,
        ]);
    }

    /**
     * Get featured/popular services
     */
    public function featured(Request $request): JsonResponse
    {
        $lang = $request->get('lang', 'en');

        $featured = Cache::remember('featured_services_' . $lang, 3600, function() {
            return Service::where('rate', '<', 5)
                ->orderBy('rate', 'asc')
                ->limit(6)
                ->get(['service_id', 'name_en', 'name_ar', 'category_en', 'category_ar', 'rate', 'min', 'max', 'type', 'refill', 'cancel']);
        });

        return response()->json([
            'featured' => $featured,
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
            'website' => 'mdi-web',
        ];

        return $icons[$platform] ?? 'mdi-help-circle';
    }
}
