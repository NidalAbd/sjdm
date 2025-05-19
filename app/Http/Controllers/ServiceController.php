<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
    public function getAllServices(Request $request)
    {
        $query = Service::query();
        $currentLanguage = app()->getLocale();

        // Define the fields based on language
        $nameField = $currentLanguage === 'ar' ? 'name_ar' : 'name_en';
        $categoryField = $currentLanguage === 'ar' ? 'category_ar' : 'category_en';

        // Get current category if set
        $currentCategory = $request->filled('category') ? $request->category : null;

        // Get current platform if set
        $currentPlatform = $request->filled('platform') ? $request->platform : null;

        // Apply category filter if present
        if ($currentCategory) {
            $query->where($categoryField, $currentCategory);
        }

        // Apply platform filter if present
        if ($currentPlatform && $currentPlatform !== 'all') {
            $platformName = $this->platforms[$currentPlatform][$currentLanguage] ?? $currentPlatform;
            $query->where($categoryField, 'like', '%' . $platformName . '%');
        }

        // Apply search filter if present
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm, $nameField, $categoryField) {
                $q->orWhere('service_id', 'like', '%' . $searchTerm . '%')
                    ->orWhere($nameField, 'like', '%' . $searchTerm . '%')
                    ->orWhere($categoryField, 'like', '%' . $searchTerm . '%')
                    ->orWhere('type', 'like', '%' . $searchTerm . '%')
                    ->orWhere('rate', 'like', '%' . $searchTerm . '%')
                    ->orWhere('cost', 'like', '%' . $searchTerm . '%');
            });
        }

        // Cache key based on query parameters and language
        $cacheKey = 'services_' . $currentLanguage . '_' . md5(serialize($request->all()));

        // Cache results for 15 minutes to improve performance
        $services = Cache::remember($cacheKey, 900, function() use ($query) {
            return $query->paginate(100);
        });

        // Get all categories for filtering
        $categories = Cache::remember('categories_' . $currentLanguage, 3600, function() use ($categoryField) {
            return DB::table('services')
                ->select($categoryField)
                ->whereNotNull($categoryField)
                ->where($categoryField, '!=', '')
                ->distinct()
                ->orderBy($categoryField)
                ->pluck($categoryField);
        });

        // Prepare SEO data
        $seoTitle = $this->generateSeoTitle($currentCategory, $currentPlatform, $currentLanguage);
        $seoDescription = $this->generateSeoDescription($currentCategory, $currentPlatform, $currentLanguage);
        $canonicalUrl = $this->generateCanonicalUrl($request);

        // Prepare structured data for the page
        $structuredData = $this->generateStructuredData($services, $currentCategory, $currentPlatform);

        // Prepare breadcrumbs
        $breadcrumbs = $this->generateBreadcrumbs($currentCategory, $currentPlatform);

        return view('services', compact(
            'services',
            'categories',
            'currentCategory',
            'currentPlatform',
            'seoTitle',
            'seoDescription',
            'canonicalUrl',
            'structuredData',
            'breadcrumbs'
        ));
    }

    /**
     * Generate SEO title based on filters
     */
    private function generateSeoTitle($category = null, $platform = null, $language = 'en')
    {
        $title = '';

        if ($platform && $platform !== 'all') {
            $platformName = $this->platforms[$platform][$language] ?? $platform;
            $title .= ucfirst($platformName) . ' ';
        }

        if ($category) {
            $title .= ucfirst($category) . ' ';
        }

        $title .= $language === 'en' ? 'SMM Services | SMM-Followers' : 'خدمات التسويق عبر وسائل التواصل الاجتماعي | SMM-Followers';

        return $title;
    }

    /**
     * Generate SEO description based on filters
     */
    private function generateSeoDescription($category = null, $platform = null, $language = 'en')
    {
        if ($language === 'en') {
            if ($platform && $platform !== 'all') {
                $platformName = $this->platforms[$platform][$language] ?? $platform;
                if ($category) {
                    return "Buy high-quality $category for $platformName. Fast delivery, affordable prices, 24/7 support. Get the best $platformName $category services at SMM-Followers.";
                } else {
                    return "Boost your $platformName presence with our SMM services. We offer followers, likes, views and more for $platformName at competitive prices. 24/7 support and fast delivery.";
                }
            } elseif ($category) {
                return "Get the best $category services for all social media platforms. Increase your social media presence with our affordable $category packages. 24/7 support and instant delivery.";
            } else {
                return "Affordable social media marketing services for Instagram, TikTok, YouTube and more. Get followers, likes and views at the lowest market rates. 24/7 support and instant delivery.";
            }
        } else {
            // Arabic descriptions
            if ($platform && $platform !== 'all') {
                $platformName = $this->platforms[$platform][$language] ?? $platform;
                if ($category) {
                    return "اشترِ $category عالية الجودة لـ $platformName. توصيل سريع وأسعار معقولة ودعم على مدار الساعة. احصل على أفضل خدمات $platformName $category في SMM-Followers.";
                } else {
                    return "عزز تواجدك على $platformName مع خدمات التسويق عبر وسائل التواصل الاجتماعي. نقدم متابعين وإعجابات ومشاهدات والمزيد لـ $platformName بأسعار تنافسية. دعم على مدار الساعة وتوصيل سريع.";
                }
            } elseif ($category) {
                return "احصل على أفضل خدمات $category لجميع منصات التواصل الاجتماعي. زيادة وجودك على وسائل التواصل الاجتماعي مع باقات $category بأسعار معقولة. دعم على مدار الساعة وتسليم فوري.";
            } else {
                return "خدمات تسويق مواقع التواصل الاجتماعي بأسعار معقولة لـ Instagram و TikTok و YouTube والمزيد. احصل على المتابعين والإعجابات والمشاهدات بأقل أسعار السوق. دعم على مدار الساعة وتسليم فوري.";
            }
        }
    }

    /**
     * Generate canonical URL
     */
    private function generateCanonicalUrl($request)
    {
        $url = url()->current();

        // If there are query parameters that affect content (not pagination)
        $params = [];
        if ($request->filled('category')) {
            $params['category'] = $request->category;
        }

        if ($request->filled('platform')) {
            $params['platform'] = $request->platform;
        }

        if ($request->filled('search')) {
            $params['search'] = $request->search;
        }

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    /**
     * Generate structured data for services page
     */
    private function generateStructuredData($services, $category = null, $platform = null)
    {
        $itemList = [];
        $position = 1;

        foreach ($services as $service) {
            $currentLanguage = app()->getLocale();
            $nameField = $currentLanguage === 'ar' ? 'name_ar' : 'name_en';

            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $service->$nameField,
                'url' => url('/service/' . $service->service_id)
            ];

            if ($position > 30) break; // Limit to 30 items for performance
        }

        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => $itemList,
            'numberOfItems' => count($itemList)
        ];

        return json_encode($structuredData);
    }

    /**
     * Generate breadcrumbs data
     */
    private function generateBreadcrumbs($category = null, $platform = null)
    {
        $currentLanguage = app()->getLocale();
        $breadcrumbs = [
            [
                'title' => $currentLanguage === 'en' ? 'Home' : 'الرئيسية',
                'url' => url('/')
            ],
            [
                'title' => $currentLanguage === 'en' ? 'Services' : 'الخدمات',
                'url' => url('/all-services')
            ]
        ];

        if ($platform && $platform !== 'all') {
            $platformName = $this->platforms[$platform][$currentLanguage] ?? $platform;
            $breadcrumbs[] = [
                'title' => ucfirst($platformName),
                'url' => url('/platform/' . $platform)
            ];
        }

        if ($category) {
            $breadcrumbs[] = [
                'title' => ucfirst($category),
                'url' => $platform && $platform !== 'all'
                    ? url('/platform/' . $platform . '?category=' . urlencode($category))
                    : url('/category/' . $this->slugify($category))
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Convert text to slug
     */
    private function slugify($text)
    {
        // Replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // Transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // Trim
        $text = trim($text, '-');

        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // Lowercase
        $text = strtolower($text);

        return $text ?: 'n-a';
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
