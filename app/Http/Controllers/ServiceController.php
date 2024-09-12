<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    // Define the platforms with translations
    private $platforms = [
        'all' => ['en' => 'all', 'ar' => 'الكل'],
        'facebook' => ['en' => 'facebook', 'ar' => 'فيسبوك'],
        'instagram' => ['en' => 'instagram', 'ar' => 'انستقرام'],
        'tiktok' => ['en' => 'tiktok', 'ar' => 'تيك توك'],
        'google' => ['en' => 'google', 'ar' => 'جوجل'],
        'twitter' => ['en' => 'twitter', 'ar' => 'تويتر'],
        'youtube' => ['en' => 'youtube', 'ar' => 'يوتيوب'],
        'spotify' => ['en' => 'spotify', 'ar' => 'سبوتيفاي'],
        'snapchat' => ['en' => 'snapchat', 'ar' => 'سناب شات'],
        'linkedin' => ['en' => 'linkedin', 'ar' => 'لينكد ان'],
        'telegram' => ['en' => 'telegram', 'ar' => 'تيليجرام'],
        'discord' => ['en' => 'discord', 'ar' => 'ديسكورد'],
        'reviews' => ['en' => 'reviews', 'ar' => 'تقييمات'],
        'twitch' => ['en' => 'twitch', 'ar' => 'تويتش'],
        'traffic' => ['en' => 'traffic', 'ar' => 'مرور']
    ];

    public function index(Request $request)
    {
        $query = Service::query();
        $currentLanguage = app()->getLocale();

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            // Define the fields to be searched
            $fields = ['service_id', 'name_en', 'name_ar', 'category_en', 'category_ar', 'type', 'rate', 'cost'];

            $query->where(function ($q) use ($fields, $searchTerm) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'like', '%' . $searchTerm . '%');
                }
            });
        }

        // Apply platform filter
        if ($request->filled('platform') && $request->platform !== 'all') {
            $categoryField = $currentLanguage === 'ar' ? 'category_ar' : 'category_en';

            // Get the platform name based on the current language
            $platformName = $this->platforms[$request->platform][$currentLanguage] ?? $request->platform;

            $query->where($categoryField, 'like', '%' . $platformName . '%');
        }

        // Get unique categories after platform filter
        $categoryField = $currentLanguage === 'ar' ? 'category_ar' : 'category_en';
        $uniqueCategories = $query->distinct()->pluck($categoryField)->toArray();

        // Apply category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where($categoryField, $request->category);
        }

        $services = $query->paginate(10);

        // Available platforms translated based on the current language
        $translatedPlatforms = array_map(fn($platform) => $platform[$currentLanguage], $this->platforms);

        return view('services.index', compact('services', 'translatedPlatforms', 'uniqueCategories'));
    }

    public function getCategories(Request $request)
    {
        $currentLanguage = app()->getLocale();
        $categoryField = $currentLanguage === 'ar' ? 'category_ar' : 'category_en';

        if ($request->platform && $request->platform !== 'all') {
            $platformName = $this->platforms[$request->platform][$currentLanguage] ?? $request->platform;
            $categories = Service::where($categoryField, 'like', '%' . $platformName . '%')
                ->select($categoryField)
                ->distinct()
                ->pluck($categoryField);
        } else {
            $categories = Service::select($categoryField)->distinct()->pluck($categoryField);
        }

        $html = '<option value="all">' . __('adminlte.select_category') . '</option>';
        foreach ($categories as $category) {
            $html .= '<option value="' . $category . '">' . ucfirst($category) . '</option>';
        }

        return response()->json(['categories' => $html]);
    }

    public function filter(Request $request)
    {
        $query = Service::query();
        $currentLanguage = app()->getLocale();
        $categoryField = $currentLanguage === 'ar' ? 'category_ar' : 'category_en';

        if ($request->filled('platform') && $request->platform !== 'all') {
            $platformName = $this->platforms[$request->platform][$currentLanguage] ?? $request->platform;
            $query->where($categoryField, 'like', '%' . $platformName . '%');
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where($categoryField, $request->category);
        }

        $services = $query->get();

        $html = '<option value="">' . __('adminlte.select_service') . '</option>';
        foreach ($services as $service) {
            $nameField = $currentLanguage === 'ar' ? $service->name_ar : $service->name_en;
            $html .= '<option value="' . $service->id . '" data-rate="' . $service->rate . '">' . $nameField . '</option>';
        }

        return response()->json(['html' => $html]);
    }

    public function create()
    {
        return view('services.create');
    }

    public function fetchFromApiEn()
    {
        $api = new Api();

        // Fetch services in English
        $servicesFromApi = $api->services();

        // Store services with the language set to English
        $this->storeServices($servicesFromApi, 'en');

        return redirect()->route('services.index')
            ->with('success', "Services have been updated from the API in English.");
    }

    public function fetchFromApiAr()
    {
        $api = new Api();

        // Fetch services in Arabic
        $servicesFromApi = $api->services();

        // Store services with the language set to Arabic
        $this->storeServices($servicesFromApi, 'ar');

        return redirect()->route('services.index')
            ->with('success', "Services have been updated from the API in Arabic.");
    }

    private function storeServices($servicesFromApi, $language)
    {
        $totalServices = count($servicesFromApi);
        $storedServices = 0;

        foreach ($servicesFromApi as $service) {
            // Check if service is an object
            if (is_object($service)) {
                // Only process services where type is 'Default'
                if ($service->type === 'Default') {
                    // Store the original rate as the cost
                    $originalRate = $service->rate;

                    // Adjust the rate based on the criteria
                    $adjustedRate = $originalRate;

                    if ($adjustedRate < 0.0001) {
                        $adjustedRate *= 5; // Multiply by 5
                    } elseif ($adjustedRate >= 0.0001 && $adjustedRate < 0.001) {
                        $adjustedRate *= 4; // Multiply by 4
                    } elseif ($adjustedRate >= 0.001 && $adjustedRate < 0.01) {
                        $adjustedRate *= 3; // Multiply by 3
                    } elseif ($adjustedRate >= 0.01 && $adjustedRate < 0.1) {
                        $adjustedRate *= 2; // Multiply by 2
                    } elseif ($adjustedRate >= 0.1 && $adjustedRate < 0.9) {
                        $adjustedRate *= 1.70; // Increase by 70%
                    } elseif ($adjustedRate >= 0.9 && $adjustedRate < 2) {
                        $adjustedRate *= 1.50; // Increase by 50%
                    } elseif ($adjustedRate >= 2 && $adjustedRate < 10) {
                        $adjustedRate *= 1.30; // Increase by 30%
                    } elseif ($adjustedRate >= 10) {
                        $adjustedRate *= 1.20; // Increase by 20%
                    }

                    // Prepare the data to update based on the language
                    $data = [
                        'type' => $service->type,
                        'rate' => $adjustedRate, // Use the adjusted rate
                        'cost' => $originalRate, // Store the original rate as the cost
                        'min' => $service->min,
                        'max' => $service->max,
                        'refill' => $service->refill,
                        'cancel' => $service->cancel,
                    ];

                    // Update only the relevant language fields
                    if ($language === 'en') {
                        $data['name_en'] = $service->name ?? null;
                        $data['category_en'] = $service->category ?? null;
                    } elseif ($language === 'ar') {
                        $data['name_ar'] = $service->name ?? null;
                        $data['category_ar'] = $service->category ?? null;
                    }

                    // Debug logging
                    Log::debug('Data prepared for insertion:', ['data' => $data]);

                    // Update or create the service with the provided data
                    $storedService = Service::updateOrCreate(
                        ['service_id' => $service->service], // Use 'service_id' from the API
                        $data
                    );

                    if ($storedService->wasRecentlyCreated || $storedService->wasChanged()) {
                        $storedServices++;
                    }
                }
            } else {
                Log::warning("Invalid service structure in API response (not an object):", ['service' => $service]);
            }
        }

        $percentageStored = ($totalServices > 0) ? round(($storedServices / $totalServices) * 100, 2) : 0;

        // Log or output the result for this language
        Log::info("Services have been updated from the API in $language. $storedServices out of $totalServices services were stored. ($percentageStored%)");
    }

    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $validatedData = $request->validate([
            'name_en' => 'required|string|max:500',
            'name_ar' => 'required|string|max:500',
            'type' => 'required|string',
            'category_en' => 'required|string|max:255',
            'category_ar' => 'required|string|max:255',
            'cost' => 'required|numeric',
            'rate' => 'required|numeric',
            'min' => 'required|integer',
            'max' => 'required|integer',
            'refill' => 'boolean',
            'cancel' => 'boolean',
        ]);

        $service->update($validatedData);

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }


    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }
}
