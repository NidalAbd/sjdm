<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceApiController extends Controller
{
    public function index(Request $request)
    {
        Log::info('API: Admin Services index requested', ['admin_id' => $request->user()?->id]);

        $query = Service::query();

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('service_id', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('category_en', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_en', $request->category);
        }

        if ($request->filled('platform')) {
            $platform = $request->platform;
            $query->where(function ($q) use ($platform) {
                $q->where('category_en', 'like', "%{$platform}%")
                  ->orWhere('name_en', 'like', "%{$platform}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true' || $request->is_active === '1');
        }

        if ($request->filled('refill')) {
            $query->where('refill', $request->refill === 'true' || $request->refill === '1');
        }

        $services = $query->orderBy('service_id')->paginate($request->get('per_page', 50));

        // Stats
        $stats = [
            'total' => Service::count(),
            'active' => Service::where('is_active', true)->count(),
            'inactive' => Service::where('is_active', false)->count(),
            'with_refill' => Service::where('refill', true)->count(),
            'categories' => Service::distinct('category_en')->count('category_en'),
        ];

        // Get unique categories
        $categories = Service::distinct()
            ->whereNotNull('category_en')
            ->pluck('category_en')
            ->sort()
            ->values();

        // Get platforms from category names (extract platform keywords)
        $platforms = collect(['Instagram', 'TikTok', 'YouTube', 'Facebook', 'Twitter', 'Telegram', 'Spotify', 'Snapchat', 'Discord', 'Twitch', 'LinkedIn', 'Pinterest'])->filter(function ($p) {
            return Service::where('category_en', 'like', "%{$p}%")->orWhere('name_en', 'like', "%{$p}%")->exists();
        })
            ->sort()
            ->values();

        return response()->json([
            'services' => $services->items(),
            'stats' => $stats,
            'categories' => $categories,
            'platforms' => $platforms,
            'pagination' => [
                'total' => $services->total(),
                'per_page' => $services->perPage(),
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
            ],
        ]);
    }

    public function show(Service $service)
    {
        return response()->json(['service' => $service]);
    }

    public function store(Request $request)
    {
        Log::info('API: Admin Service create requested', ['admin_id' => $request->user()?->id]);

        $request->validate([
            'service_id' => 'required|string|unique:services,service_id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'category_en' => 'required|string|max:255',
            'category_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'rate' => 'required|numeric|min:0',
            'api_rate' => 'nullable|numeric|min:0',
            'min' => 'required|integer|min:1',
            'max' => 'required|integer|min:1',
            // platform is derived from category, not a DB column
            'type' => 'nullable|string|max:100',
            'refill' => 'nullable|boolean',
            'dripfeed' => 'nullable|boolean',
            'cancel' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $service = Service::create([
            'service_id' => $request->service_id,
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'category_en' => $request->category_en,
            'category_ar' => $request->category_ar,
            'description' => $request->description,
            'rate' => $request->rate,
            'api_rate' => $request->api_rate ?? $request->rate,
            'min' => $request->min,
            'max' => $request->max,
            'type' => $request->type,
            'refill' => $request->refill ?? false,
            'dripfeed' => $request->dripfeed ?? false,
            'cancel' => $request->cancel ?? false,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service,
        ]);
    }

    public function update(Request $request, Service $service)
    {
        Log::info('API: Admin Service update requested', ['admin_id' => $request->user()?->id, 'service_id' => $service->id]);

        $request->validate([
            'name_en' => 'sometimes|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'category_en' => 'sometimes|string|max:255',
            'category_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'rate' => 'sometimes|numeric|min:0',
            'api_rate' => 'nullable|numeric|min:0',
            'min' => 'sometimes|integer|min:1',
            'max' => 'sometimes|integer|min:1',
            // platform is derived from category, not a DB column
            'type' => 'nullable|string|max:100',
            'refill' => 'nullable|boolean',
            'dripfeed' => 'nullable|boolean',
            'cancel' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $service->fill($request->only([
            'name_en', 'name_ar', 'category_en', 'category_ar',
            'description', 'rate', 'api_rate',
            'min', 'max', 'type', 'refill', 'dripfeed',
            'cancel', 'is_active'
        ]));
        $service->save();

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service,
        ]);
    }

    public function destroy(Service $service)
    {
        Log::info('API: Admin Service delete requested', ['admin_id' => request()->user()?->id, 'service_id' => $service->id]);

        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully',
        ]);
    }

    public function toggle(Service $service)
    {
        $service->is_active = !$service->is_active;
        $service->save();

        return response()->json([
            'message' => $service->is_active ? 'Service activated' : 'Service deactivated',
            'is_active' => $service->is_active,
        ]);
    }

    public function updateRate(Request $request, Service $service)
    {
        Log::info('API: Admin Service rate update requested', ['admin_id' => $request->user()?->id, 'service_id' => $service->id]);
        $request->validate([
            'rate' => 'required|numeric|min:0',
        ]);

        $service->rate = $request->rate;
        $service->save();

        return response()->json([
            'message' => 'Rate updated successfully',
            'service' => $service,
        ]);
    }

    public function updateAllRates(Request $request)
    {
        Log::info('API: Admin Services bulk rate update requested', ['admin_id' => $request->user()?->id]);

        $request->validate([
            'percentage' => 'required|numeric|min:-100|max:1000',
            'platform' => 'nullable|string',
            'category' => 'nullable|string',
        ]);

        $query = Service::query();

        if ($request->filled('platform')) {
            $platform = $request->platform;
            $query->where(function ($q) use ($platform) {
                $q->where('category_en', 'like', "%{$platform}%")
                  ->orWhere('name_en', 'like', "%{$platform}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_en', $request->category);
        }

        $multiplier = 1 + ($request->percentage / 100);

        $updated = $query->update([
            'rate' => DB::raw("api_rate * {$multiplier}")
        ]);

        return response()->json([
            'message' => "Updated {$updated} services",
            'updated' => $updated,
        ]);
    }

    public function fetchFromApi(Request $request)
    {
        Log::info('API: Admin Services fetch from API requested', ['admin_id' => $request->user()?->id]);

        $language = $request->input('language', 'en');

        $api = new Api();
        $servicesFromApi = $api->services();

        if (!is_array($servicesFromApi) && !is_object($servicesFromApi)) {
            return response()->json([
                'message' => 'Failed to fetch services from API'
            ], 500);
        }

        $created = 0;
        $updated = 0;
        $totalServices = count($servicesFromApi);

        foreach ($servicesFromApi as $service) {
            // Check if service is an object
            if (is_object($service)) {
                // Only process services where type is 'Default'
                if ($service->type === 'Default') {
                    // Store the original rate as the cost
                    $originalRate = $service->rate;

                    // Adjust the rate based on the criteria (same as Blade version)
                    $adjustedRate = $originalRate;

                    if ($adjustedRate < 0.0001) {
                        $adjustedRate *= 5;
                    } elseif ($adjustedRate >= 0.0001 && $adjustedRate < 0.001) {
                        $adjustedRate *= 4;
                    } elseif ($adjustedRate >= 0.001 && $adjustedRate < 0.01) {
                        $adjustedRate *= 3;
                    } elseif ($adjustedRate >= 0.01 && $adjustedRate < 0.1) {
                        $adjustedRate *= 2;
                    } elseif ($adjustedRate >= 0.1 && $adjustedRate < 0.9) {
                        $adjustedRate *= 1.70;
                    } elseif ($adjustedRate >= 0.9 && $adjustedRate < 2) {
                        $adjustedRate *= 1.50;
                    } elseif ($adjustedRate >= 2 && $adjustedRate < 10) {
                        $adjustedRate *= 1.30;
                    } elseif ($adjustedRate >= 10) {
                        $adjustedRate *= 1.20;
                    }

                    // Prepare the data to update based on the language
                    $data = [
                        'type' => $service->type,
                        'rate' => $adjustedRate,
                        'cost' => $originalRate,
                        'api_rate' => $originalRate,
                        'min' => $service->min,
                        'max' => $service->max,
                        'original_min' => $service->min,
                        'original_max' => $service->max,
                        'refill' => $service->refill ?? false,
                        'cancel' => $service->cancel ?? false,
                        'is_active' => true,
                    ];

                    // Update only the relevant language fields
                    if ($language === 'en') {
                        $data['name_en'] = $service->name ?? null;
                        $data['category_en'] = $service->category ?? null;
                    } elseif ($language === 'ar') {
                        $data['name_ar'] = $service->name ?? null;
                        $data['category_ar'] = $service->category ?? null;
                    }

                    // Check if exists
                    $existing = Service::where('service_id', $service->service)->first();

                    if ($existing) {
                        $existing->fill($data);
                        $existing->save();
                        $updated++;
                    } else {
                        $data['service_id'] = $service->service;
                        Service::create($data);
                        $created++;
                    }
                }
            }
        }

        $languageLabel = $language === 'ar' ? 'Arabic' : 'English';

        return response()->json([
            'message' => "Services updated from API in {$languageLabel}. Created {$created}, updated {$updated} services.",
            'created' => $created,
            'updated' => $updated,
            'total' => $totalServices,
        ]);
    }

    public function syncService(Request $request, Service $service)
    {
        Log::info('API: Admin Service sync from API requested', ['admin_id' => $request->user()?->id, 'service_id' => $service->id]);

        $api = new Api();
        $services = $api->services();

        if (!is_array($services)) {
            return response()->json(['message' => 'Failed to fetch from API'], 500);
        }

        $apiService = collect($services)->firstWhere('service', $service->service_id);

        if (!$apiService) {
            return response()->json(['message' => 'Service not found in API'], 404);
        }

        $service->api_rate = $apiService['rate'];
        $service->original_min = $apiService['min'];
        $service->original_max = $apiService['max'];
        $service->save();

        return response()->json([
            'message' => 'Service synced from API',
            'service' => $service,
        ]);
    }

    public function duplicate(Service $service)
    {
        Log::info('API: Admin Service duplicate requested', ['admin_id' => request()->user()?->id, 'service_id' => $service->id]);

        $newService = $service->replicate();
        $newService->service_id = $service->service_id . '_copy_' . time();
        $newService->name_en = $service->name_en . ' (Copy)';
        if ($service->name_ar) {
            $newService->name_ar = $service->name_ar . ' (نسخة)';
        }
        $newService->save();

        return response()->json([
            'message' => 'Service duplicated successfully',
            'service' => $newService,
        ]);
    }

    public function bulkToggle(Request $request)
    {
        $request->validate([
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'is_active' => 'required|boolean',
        ]);

        $updated = Service::whereIn('id', $request->service_ids)
            ->update(['is_active' => $request->is_active]);

        return response()->json([
            'message' => "Updated {$updated} services",
            'updated' => $updated,
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
        ]);

        $deleted = Service::whereIn('id', $request->service_ids)->delete();

        return response()->json([
            'message' => "Deleted {$deleted} services",
            'deleted' => $deleted,
        ]);
    }

    /**
     * Wipe the entire services table and re-fetch fresh from the upstream API.
     * Used when upstream pricing/availability may have drifted and a full resync is wanted.
     */
    public function wipeAndReimport(Request $request)
    {
        Log::info('API: Admin Services wipe & reimport requested', ['admin_id' => $request->user()?->id]);

        $language = $request->input('language', 'en');

        $api = new Api();
        $servicesFromApi = $api->services();

        if (!is_array($servicesFromApi) && !is_object($servicesFromApi)) {
            return response()->json([
                'message' => 'Failed to fetch services from API — aborted before wiping existing data.'
            ], 500);
        }

        $wiped = Service::count();
        Service::query()->delete();

        $request->merge(['language' => $language]);
        $response = $this->fetchFromApi($request);
        $data = $response->getData(true);

        return response()->json([
            'message' => "Wiped {$wiped} services and re-imported " . ($data['created'] ?? 0) . " fresh from the API.",
            'wiped' => $wiped,
            'created' => $data['created'] ?? 0,
            'updated' => $data['updated'] ?? 0,
        ]);
    }
}
