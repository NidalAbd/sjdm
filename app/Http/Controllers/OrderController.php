<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateOrderStatuses;
use App\Models\Transaction;
use App\Notifications\OrderNotification;
use App\Notifications\TransactionNotification;
use App\Services\Api;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $api;

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
        'linkedin' => ['en' => 'linkedin', 'ar' => 'لينكدان'],
        'telegram' => ['en' => 'telegram', 'ar' => 'تيليجرام'],
        'discord' => ['en' => 'discord', 'ar' => 'ديسكورد'],
        'reviews' => ['en' => 'reviews', 'ar' => 'تقييمات'],
        'twitch' => ['en' => 'twitch', 'ar' => 'تويتش'],
        'traffic' => ['en' => 'traffic', 'ar' => 'مرور']
    ];

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * Get the localized field based on the current language.
     *
     * @param string $field
     * @return string
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Order::query();
        $currentLanguage = substr(app()->getLocale(), 0, 2); // Only the language part ('ar' or 'en')

        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('link', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('platform') && $request->platform !== 'all') {
            $categoryField = $this->getLocalizedField('category');
            $platformName = $this->platforms[$request->platform][$currentLanguage] ?? $request->platform;

            $query->whereHas('service', function ($q) use ($platformName, $categoryField) {
                $q->where($categoryField, 'like', '%' . $platformName . '%');
            });
        }

        $orders = $query->paginate(5);
        $services = Service::all();
        $translatedPlatforms = array_map(fn($platform) => $platform[$currentLanguage], $this->platforms);

        // Pass $platforms to the view
        return view('orders.index', compact('orders', 'services', 'translatedPlatforms'))
            ->with('platforms', array_keys($this->platforms));
    }

    public function create()
    {
        // Determine the current language ('ar' or 'en')
        $currentLanguage = substr(app()->getLocale(), 0, 2);

        // Translate platform names based on the current language
        $translatedPlatforms = array_map(fn($platform) => $platform[$currentLanguage], $this->platforms);

        // Get the localized category field (either category_ar or category_en)
        $categoryField = $this->getLocalizedField('category');

        // Fetch unique categories based on the localized category field
        $uniqueCategories = Service::select($categoryField)->distinct()->pluck($categoryField);

        // Fetch services where category matches 'all', using the localized category field
        $services = Service::where($categoryField, 'LIKE', "%all%")->get();
        $selectedService = $services->first();

        return view('orders.create', compact('translatedPlatforms', 'uniqueCategories', 'services', 'selectedService', 'currentLanguage'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'service_id' => 'required|exists:services,service_id',
            'link' => 'required|url',
            'quantity' => 'required|integer|min:1',
        ]);

        // Fetch the service and calculate the charge
        $service = Service::find($validated['service_id']);
        $charge = ($validated['quantity'] * $service->rate) / 1000;
        $user = auth()->user();

        // Prepare data for the API request
        $data = [
            'service' => $validated['service_id'],
            'link' => $validated['link'],
            'quantity' => $validated['quantity'],
        ];

        // Call the API to create the order
        $apiResponse = $this->api->order($data);

        // Check if the API response contains the order ID
        if (isset($apiResponse->order)) {

            // Save the order in the database
            $order = new Order();
            $order->user_id = $user->id;
            $order->service_id = $validated['service_id'];
            $order->link = $validated['link'];
            $order->quantity = $validated['quantity'];
            $order->charge = $charge;
            $order->status = isset($apiResponse->status) ? $apiResponse->status : 'Pending';
            $order->api_order_id = $apiResponse->order;
            $order->save();

            // Send notification for the order
            $user->notify(new OrderNotification($order));

            // Assume API cost is returned in the response
            $apiCost = $apiResponse->charge ?? $charge;  // Use the API cost if available, fallback to user charge

            // Deduct the charge from the user's balance after successful API order creation
            if ($user->balance >= $charge) {
                $user->balance -= $charge;
                $user->save();

                // Calculate profit (user charge - api cost)
                $profit = $charge - $apiCost;

                // Create a transaction record
                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->type = 'debit';
                $transaction->amount = $charge;  // The amount charged to the user
                $transaction->api_cost = $apiCost;  // The cost from the API
                $transaction->profit = $profit;  // The profit (charge - api cost)
                $transaction->currency = 'USD';
                $transaction->status = 'completed';
                $transaction->save();

                // Send notification for the transaction
                $user->notify(new TransactionNotification($transaction));

                return redirect()->route('orders.index')->with('success', 'Order created successfully. Order ID: ' . $apiResponse->order);
            } else {
                return redirect()->back()->with('error', 'Insufficient balance to place the order.');
            }
        } else {
            return redirect()->back()->with('error', 'Failed to create order. Please try again.');
        }
    }


    public function getCategories(Request $request)
    {
        $currentLanguage = app()->getLocale();  // Get the current language ('en' or 'ar')
        $categoryField = $this->getLocalizedField('category');  // Get localized field for categories
        $platform = $request->query('platform', '');  // Get the platform parameter

        Log::info("Current locale: $currentLanguage");
        Log::info("Category field: $categoryField");
        Log::info("Platform parameter: $platform");

        if ($platform === 'all' || empty($platform)) {
            // If platform is 'all' or not specified, fetch all categories
            Log::info("Fetching all categories");
            $categories = Service::select($categoryField)->distinct()->pluck($categoryField);
        } else {
            // Get the platform name in the current language
            $platformName = $this->platforms[$platform][$currentLanguage] ?? $platform;
            Log::info("Fetching categories for platform: $platformName");

            // Fetch categories that match the platform name
            $categories = Service::where($categoryField, 'LIKE', "%$platformName%")
                ->distinct()
                ->pluck($categoryField);
        }

        Log::info("Categories fetched: ", $categories->toArray());

        return response()->json($categories);  // Return categories as JSON
    }

    public function getServices(Request $request)
    {
        $currentLanguage = app()->getLocale();  // Get the current language ('en' or 'ar')
        $categoryField = $this->getLocalizedField('category');  // Get localized field for categories
        $nameField = $this->getLocalizedField('name');  // Get localized field for service names
        $platform = $request->query('platform', '');  // Get the platform parameter
        $category = $request->query('category', '');  // Get the category parameter

        Log::info("Current locale: $currentLanguage");
        Log::info("Category field: $categoryField");
        Log::info("Name field: $nameField");
        Log::info("Platform parameter: $platform");
        Log::info("Category parameter: $category");

        // Fetch services based on platform and category
        $services = Service::when($platform !== 'all' && !empty($platform), function ($query) use ($platform, $categoryField, $currentLanguage) {
            $platformName = $this->platforms[$platform][$currentLanguage] ?? $platform;
            Log::info("Filtering services by platform: $platformName");
            return $query->where($categoryField, 'LIKE', "%$platformName%");
        })
            ->when(!empty($category), function ($query) use ($category, $categoryField) {
                Log::info("Filtering services by category: $category");
                return $query->where($categoryField, 'LIKE', "%$category%");
            })
            ->get(['service_id', 'rate', 'min', 'max', $nameField]);

        Log::info("Services fetched: ", $services->toArray());

        // Prepare the response with the necessary fields
        $response = $services->map(function ($service) use ($nameField) {
            return [
                'service_id' => $service->service_id,
                'name' => $service->{$nameField},  // Dynamically access the localized name field
                'rate' => $service->rate,
                'min' => $service->min,
                'max' => $service->max,
            ];
        });

        Log::info("Services response data: ", $response->toArray());

        return response()->json($response);  // Return services as JSON
    }

    private function getLocalizedField($field)
    {
        // Get the locale from the app instance
        $currentLanguage = substr(app()->getLocale(), 0, 2);
        Log::info("Current Language in getLocalizedField: " . $currentLanguage);

        return $currentLanguage === 'ar' ? "{$field}_ar" : "{$field}_en";
    }

    public function show(Order $order)
    {
        $service = $order->service;
        return view('orders.show', compact('order', 'service'));
    }


    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }

    public function updateOrderStatuses()
    {
        Log::info('updateOrderStatuses method called.');
        UpdateOrderStatuses::dispatch();
        Log::info('UpdateOrderStatuses job dispatched.');
        return redirect()->back()->with('success', 'Order statuses are being updated.');
    }

    public function searchServices(Request $request)
    {
        $currentLanguage = substr(app()->getLocale(), 0, 2);
        $nameField = $this->getLocalizedField('name');
        $categoryField = $this->getLocalizedField('category');
        $query = $request->get('query', '');

        $services = Service::where($nameField, 'LIKE', "%$query%")
            ->orWhere($categoryField, 'LIKE', "%$query%")
            ->orWhere('service_id', 'LIKE', "%$query%")
            ->get();

        return response()->json($services);
    }

    public function checkRefill($id)
    {
        $order = Order::findOrFail($id);
        $apiOrderId = $order->api_order_id;

        if (!$apiOrderId) {
            return response()->json(['can_refill' => false, 'message' => 'Invalid API order ID.'], 400);
        }

        $apiResponse = $this->api->refillStatus($apiOrderId);
        return response()->json([
            'can_refill' => $apiResponse->can_refill ?? false
        ]);
    }

    public function checkCancel($id)
    {
        $order = Order::findOrFail($id);
        $apiOrderId = $order->api_order_id;

        if (!$apiOrderId) {
            return response()->json(['can_cancel' => false, 'message' => 'Invalid API order ID.'], 400);
        }

        $apiResponse = $this->api->cancel([$apiOrderId]);
        return response()->json([
            'can_cancel' => $apiResponse->can_cancel ?? false
        ]);
    }
}
