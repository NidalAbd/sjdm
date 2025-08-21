<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateOrderStatuses;
use App\Models\Transaction;
use App\Services\Api;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderCowntroller extends Controller
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

    private function getLocalizedField($field)
    {
        $currentLanguage = app()->getLocale();
        return $currentLanguage === 'ar' ? "{$field}_ar" : "{$field}_en";
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Order::query();
        $currentLanguage = app()->getLocale();

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

        return view('orders.index', compact('orders', 'services', 'translatedPlatforms'));
    }

    public function create()
    {
        $currentLanguage = app()->getLocale();
        $translatedPlatforms = array_map(fn($platform) => $platform[$currentLanguage], $this->platforms);

        $categoryField = $this->getLocalizedField('category');
        $uniqueCategories = Service::select($categoryField)->distinct()->pluck($categoryField);
        $services = Service::where($categoryField, 'LIKE', "%all%")->get();
        $selectedService = $services->first();

        return view('orders.create', compact('translatedPlatforms', 'uniqueCategories', 'services', 'selectedService', 'currentLanguage'));
    }

    public function store(Request $request)
    {
        Log::info('Incoming request data:', $request->all());

        $validated = $request->validate([
            'service_id' => 'required|exists:services,service_id',
            'link' => 'required|url',
            'quantity' => 'required|integer|min:1',
        ]);

        $service = Service::find($validated['service_id']);
        $charge = ($validated['quantity'] * $service->rate) / 1000;
        $user = auth()->user();

        if ($user->balance < $charge) {
            return redirect()->back()->with('error', 'Insufficient balance to place the order.');
        }

        $user->balance -= $charge;
        $user->save();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->type = 'debit';
        $transaction->amount = $charge;
        $transaction->currency = 'USD';
        $transaction->status = 'completed';
        $transaction->save();

        $data = [
            'service' => $validated['service_id'],
            'link' => $validated['link'],
            'quantity' => $validated['quantity'],
        ];

        $apiResponse = $this->api->order($data);
        Log::info('API Response:', (array) $apiResponse);

        if (isset($apiResponse->order)) {
            Log::info('Saving order to database:', $data);

            $order = new Order();
            $order->user_id = $user->id;
            $order->service_id = $validated['service_id'];
            $order->link = $validated['link'];
            $order->quantity = $validated['quantity'];
            $order->charge = $charge;
            $order->status = isset($apiResponse->status) ? $apiResponse->status : 'Pending';
            $order->api_order_id = $apiResponse->order;
            $order->save();

            return redirect()->route('orders.index')->with('success', 'Order created successfully. Order ID: ' . $apiResponse->order);
        } else {
            Log::error('Failed to create order. API Response:', (array) $apiResponse);
            return redirect()->back()->with('error', 'Failed to create order. Please try again.');
        }
    }

    public function getCategories(Request $request)
    {
        $currentLanguage = app()->getLocale();
        $categoryField = $this->getLocalizedField('category');
        $platform = $request->query('platform', '');

        Log::info("Step 1: Current locale - $currentLanguage");
        Log::info("Step 2: Category field selected - $categoryField");
        Log::info("Step 3: Platform parameter - $platform");

        if ($platform === 'all' || empty($platform)) {
            Log::info("Step 4: Fetching all categories");
            $categories = Service::select($categoryField)->distinct()->pluck($categoryField);
        } else {
            $platformName = $this->platforms[$platform][$currentLanguage] ?? $platform;
            Log::info("Step 4: Fetching categories for specific platform - $platform");
            Log::info("Step 5: Platform name used for query - $platformName");
            $categories = Service::where($categoryField, 'LIKE', "%$platformName%")->distinct()->pluck($categoryField);
        }

        Log::info("Step 6: Categories fetched: ", $categories->toArray());

        return response()->json($categories);
    }

    public function getServices(Request $request)
    {
        $currentLanguage = app()->getLocale();
        $categoryField = $this->getLocalizedField('category');
        $nameField = $this->getLocalizedField('name');
        $platform = $request->query('platform', '');
        $category = $request->query('category', '');

        Log::info("Step 1: Current locale - $currentLanguage");
        Log::info("Step 2: Category field selected - $categoryField");
        Log::info("Step 3: Name field selected - $nameField");
        Log::info("Step 4: Platform parameter - $platform");
        Log::info("Step 5: Category parameter - $category");

        $services = Service::when($platform !== 'all' && !empty($platform), function ($query) use ($platform, $categoryField, $currentLanguage) {
            $platformName = $this->platforms[$platform][$currentLanguage] ?? $platform;
            Log::info("Step 6: Platform name used for query - $platformName");
            return $query->where($categoryField, 'LIKE', "%$platformName%");
        })
            ->when(!empty($category), function ($query) use ($category, $categoryField) {
                Log::info("Step 7: Applying category filter - $category");
                return $query->where($categoryField, 'LIKE', "%$category%");
            })
            ->get(['service_id', 'rate', 'min', 'max', $nameField]);

        Log::info("Step 8: Services fetched: ", $services->toArray());

        $response = $services->map(function ($service) use ($nameField) {
            return [
                'service_id' => $service->service_id,
                'name' => $service->$nameField,
                'rate' => $service->rate,
                'min' => $service->min,
                'max' => $service->max,
            ];
        });

        Log::info("Step 9: Services response data: ", $response->toArray());

        return response()->json($response);
    }

    public function show(Order $order)
    {
        // Load the associated service model
        $service = $order->service;

        // Pass both the order and service to the view
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

        // Dispatch the job to update order statuses
        UpdateOrderStatuses::dispatch();

        Log::info('UpdateOrderStatuses job dispatched.');

        return redirect()->back()->with('success', 'Order statuses are being updated.');
    }

    /**
     * Search for services dynamically based on user input.
     */
    public function searchServices(Request $request)
    {
        $currentLanguage = app()->getLocale();
        $nameField = $currentLanguage === 'ar' ? 'name_ar' : 'name_en';
        $categoryField = $currentLanguage === 'ar' ? 'category_ar' : 'category_en';
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
        $apiOrderId = $order->api_order_id; // Use the API order ID

        if (!$apiOrderId) {
            return response()->json(['can_refill' => false, 'message' => 'Invalid API order ID.'], 400);
        }

        $apiResponse = $this->api->refillStatus($apiOrderId);

        // Assuming the API response contains a 'can_refill' key
        return response()->json([
            'can_refill' => $apiResponse->can_refill ?? false
        ]);
    }

    public function checkCancel($id)
    {
        $order = Order::findOrFail($id);
        $apiOrderId = $order->api_order_id; // Use the API order ID

        if (!$apiOrderId) {
            return response()->json(['can_cancel' => false, 'message' => 'Invalid API order ID.'], 400);
        }

        $apiResponse = $this->api->cancel([$apiOrderId]);

        // Assuming the API response contains a 'can_cancel' key
        return response()->json([
            'can_cancel' => $apiResponse->can_cancel ?? false
        ]);
    }
}
