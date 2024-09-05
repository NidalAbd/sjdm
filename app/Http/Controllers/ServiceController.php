<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query();
        $currentLanguage = app()->getLocale();

        // Apply search filter
        if ($request->filled('search')) {
            $searchField = $currentLanguage === 'ar' ? 'name_ar' : 'name_en';
            $query->where($searchField, 'like', '%' . $request->search . '%');
        }

        // Apply platform filter (assuming platform matches category in this context)
        if ($request->filled('platform') && $request->platform !== 'all') {
            $categoryField = $currentLanguage === 'ar' ? 'category_ar' : 'category_en';
            $query->where($categoryField, 'like', '%' . $request->platform . '%');
        }

        // Get unique categories after platform filter
        $categoryField = $currentLanguage === 'ar' ? 'category_ar' : 'category_en';
        $uniqueCategories = $query->distinct()->pluck($categoryField)->toArray();

        // Apply category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where($categoryField, $request->category);
        }

        $services = $query->paginate(5);

        // Available platforms for the platform filter dropdown
        $platforms = [
            'all', 'facebook', 'instagram', 'tiktok', 'google', 'twitter',
            'youtube', 'spotify', 'snapchat', 'linkedin', 'telegram',
            'discord', 'reviews', 'twitch', 'traffic'
        ];

        return view('services.index', compact('services', 'platforms', 'uniqueCategories'));
    }


    public function getCategories(Request $request)
    {
        if ($request->platform && $request->platform !== 'all') {
            $categories = Service::where('category', 'like', '%' . $request->platform . '%')
                ->select('category')
                ->distinct()
                ->pluck('category');
        } else {
            $categories = Service::select('category')->distinct()->pluck('category');
        }

        $html = '<option value="all">Select Category</option>';
        foreach ($categories as $category) {
            $html .= '<option value="' . $category . '">' . ucfirst($category) . '</option>';
        }

        return response()->json(['categories' => $html]);
    }

    public function filter(Request $request)
    {
        $query = Service::query();

        if ($request->filled('platform') && $request->platform !== 'all') {
            $query->where('category', 'like', '%' . $request->platform . '%');
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', 'like', '%' . $request->category . '%');
        }

        $services = $query->get();

        $html = '<option value="">Select Service</option>';
        foreach ($services as $service) {
            $html .= '<option value="' . $service->id . '" data-rate="' . $service->rate . '">' . $service->name . '</option>';
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

        // Fetch services - assuming the API returns the correct language data based on some internal configuration
        $servicesFromApi = $api->services();

        // Store services with the language set to English
        $this->storeServices($servicesFromApi, 'en');

        return redirect()->route('services.index')
            ->with('success', "Services have been updated from the API in English.");
    }

    public function fetchFromApiAr()
    {
        $api = new Api();

        // Fetch services - assuming the API returns the correct language data based on some internal configuration
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
                    $adjustedRate = $service->rate;

                    // Adjust the rate based on the criteria
                    if ($adjustedRate < 0.00001) {
                        $adjustedRate *= 6; // Increase by 500%
                    } elseif ($adjustedRate < 0.0001) {
                        $adjustedRate *= 5; // Increase by 400%
                    } elseif ($adjustedRate < 0.001) {
                        $adjustedRate *= 4; // Increase by 300%
                    } elseif ($adjustedRate < 0.01) {
                        $adjustedRate *= 3; // Increase by 200%
                    } elseif ($adjustedRate > 100) {
                        $adjustedRate *= 1.05; // Increase by 5%
                    } elseif ($adjustedRate > 50) {
                        $adjustedRate *= 1.10; // Increase by 10%
                    } elseif ($adjustedRate > 10) {
                        $adjustedRate *= 1.20; // Increase by 20%
                    } elseif ($adjustedRate > 1) {
                        $adjustedRate *= 1.30; // Increase by 30%
                    }

                    // Prepare the data to update based on the language
                    $data = [
                        'type' => $service->type,
                        'rate' => $adjustedRate, // Use the adjusted rate
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
        $service->update($request->all());
        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }
}
