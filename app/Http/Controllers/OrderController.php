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
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

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
        $query = Order::with(['user', 'service', 'supportTicket']);
        $currentLanguage = substr(app()->getLocale(), 0, 2);

        // If the user is not an admin, restrict the orders to their own
        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        // Enhanced search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('link', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                              ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('service', function ($query) use ($search) {
                        $query->where('name_en', 'like', '%' . $search . '%')
                              ->orWhere('name_ar', 'like', '%' . $search . '%')
                              ->orWhere('service_id', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter by platform if provided
        if ($request->filled('platform') && $request->platform !== 'all') {
            $categoryField = $this->getLocalizedField('category');
            $platformName = $this->platforms[$request->platform][$currentLanguage] ?? $request->platform;

            $query->whereHas('service', function ($q) use ($platformName, $categoryField) {
                $q->where($categoryField, 'like', '%' . $platformName . '%');
            });
        }

        // Filter by order status if provided
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Advanced filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('price_min')) {
            $query->where('charge', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('charge', '<=', $request->price_max);
        }

        if ($request->filled('qty_min')) {
            $query->where('quantity', '>=', $request->qty_min);
        }

        if ($request->filled('qty_max')) {
            $query->where('quantity', '<=', $request->qty_max);
        }

        // Enhanced sorting
        $sort = $request->get('sort', 'id_desc');
        switch ($sort) {
            case 'id_asc':
                $query->orderBy('id', 'asc');
                break;
            case 'charge_desc':
                $query->orderBy('charge', 'desc');
                break;
            case 'charge_asc':
                $query->orderBy('charge', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'date_asc':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        // Handle export functionality
        if ($request->has('export')) {
            return $this->exportOrders($query, $request);
        }

        // Get the paginated orders
        $orders = $query->paginate(10);

        // Get the unique statuses from orders to populate the filter dropdown
        $statuses = Order::select('status')->distinct()->pluck('status');

        // Get the available services and translated platforms
        $services = Service::all();
        $translatedPlatforms = array_map(fn($platform) => $platform[$currentLanguage], $this->platforms);

        // Calculate statistics
        $statistics = $this->calculateOrderStatistics($query);

        // Pass the data to the view
        return view('orders.index', compact('orders', 'services', 'translatedPlatforms', 'statuses', 'statistics'))
            ->with('platforms', array_keys($this->platforms));
    }

    /**
     * Calculate order statistics
     */
    private function calculateOrderStatistics($query)
    {
        $totalOrders = $query->count();
        $completedOrders = (clone $query)->where('status', 'completed')->count();
        $pendingOrders = (clone $query)->where('status', 'pending')->count();
        $totalValue = (clone $query)->sum('charge');

        return [
            'total' => $totalOrders,
            'completed' => $completedOrders,
            'pending' => $pendingOrders,
            'total_value' => $totalValue,
        ];
    }

    /**
     * Export orders functionality
     */
    private function exportOrders($query, $request)
    {
        $exportType = $request->get('export');
        
        if ($exportType === 'selected' && $request->has('orders')) {
            $orderIds = explode(',', $request->get('orders'));
            $orders = $query->whereIn('id', $orderIds)->get();
        } else {
            $orders = $query->get();
        }

        $filename = 'orders_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Order ID',
                'User Name',
                'User Email',
                'Service Name',
                'Service ID',
                'Link',
                'Quantity',
                'Charge',
                'Start Count',
                'Remains',
                'Status',
                'Created Date',
                'Updated Date'
            ]);

            // CSV Data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->user->name,
                    $order->user->email,
                    app()->getLocale() === 'ar' ? $order->service->name_ar : $order->service->name_en,
                    $order->service->service_id,
                    $order->link,
                    $order->quantity,
                    $order->charge,
                    $order->start_count,
                    $order->remains,
                    $order->status,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
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

        // Fetch the service details
        $service = Service::find($validated['service_id']);

        // Calculate the charge for the user based on the rate per 1k from the service table
        $userCharge = ($validated['quantity'] * $service->rate) / 1000;  // User charge

        // Calculate the actual cost for the service (cost per 1k from the service table)
        $actualCost = ($validated['quantity'] * $service->cost) / 1000;  // Service cost for the requested quantity

        $user = auth()->user();

        // Check if the user has enough balance
        if ($user->balance < $userCharge) {
            return redirect()->back()->with('error', 'Insufficient balance to place this order.');
        }

        // If the user has enough balance, proceed to create the order
        $order = new Order();
        $order->user_id = $user->id;
        $order->service_id = $validated['service_id'];
        $order->link = $validated['link'];
        $order->quantity = $validated['quantity'];
        $order->charge = $userCharge;  // The amount charged to the user
        $order->status = 'waiting';  // New status indicating that the order is waiting for API processing
        $order->save();

        // Deduct the charge from the user's balance
        $user->balance -= $userCharge;
        $user->save();

        // Create a transaction record
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->type = 'debit';
        $transaction->amount = $userCharge;
        $transaction->api_cost = $actualCost;  // Store the actual cost
        $transaction->profit = $userCharge - $actualCost;  // Calculate the profit
        $transaction->currency = 'USD';
        $transaction->status = 'completed';  // Mark as pending since the order hasn't been sent to the API yet
        $transaction->save();

        // Send notification for the transaction
        $user->notify(new TransactionNotification($transaction));

        return redirect()->route('orders.index')->with('success', 'Order placed successfully and will be processed.');
    }

    public function getCategories(Request $request)
    {
        $currentLanguage = app()->getLocale();  // Get the current language ('en' or 'ar')
        $categoryField = $this->getLocalizedField('category');  // Get localized field for categories
        $platform = $request->query('platform', '');  // Get the platform parameter

        if ($platform === 'all' || empty($platform)) {
            // If platform is 'all' or not specified, fetch all categories
            $categories = Service::select($categoryField)->distinct()->pluck($categoryField);
        } else {
            // Get the platform name in the current language
            $platformName = $this->platforms[$platform][$currentLanguage] ?? $platform;

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

        // Fetch services based on platform and category
        $services = Service::when($platform !== 'all' && !empty($platform), function ($query) use ($platform, $categoryField, $currentLanguage) {
            $platformName = $this->platforms[$platform][$currentLanguage] ?? $platform;
            return $query->where($categoryField, 'LIKE', "%$platformName%");
        })
            ->when(!empty($category), function ($query) use ($category, $categoryField) {
                return $query->where($categoryField, 'LIKE', "%$category%");
            })
            ->get(['service_id', 'rate', 'min', 'max', $nameField]);

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
        $user = $order->user;
        // Check if the order has a charge and refund it to the user's balance
        if ($order->charge > 0) {
            // Create transaction data
            $transactionData = [
                'type' => 'credit',
                'amount' => $order->charge,
                'status' => 'refunded',
                'description' => 'Refund for deleted order ID: ' . $order->id,
                'currency' => 'USD', // Adjust currency as needed
            ];
            // Refund and notify the user using createTransactionAndNotify
            $user->createTransactionAndNotify($transactionData);
        }
        // Delete the order
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted and charge refunded successfully.');
    }

    /**
     * Bulk delete orders
     */
    public function bulkDestroy(Request $request)
    {
        $orderIds = $request->input('order_ids', []);
        
        if (empty($orderIds)) {
            return response()->json(['success' => false, 'message' => 'No orders selected']);
        }

        $orders = Order::whereIn('id', $orderIds)->get();
        $totalRefunded = 0;

        foreach ($orders as $order) {
            if ($order->charge > 0) {
                $user = $order->user;
                $transactionData = [
                    'type' => 'credit',
                    'amount' => $order->charge,
                    'status' => 'refunded',
                    'description' => 'Bulk refund for deleted order ID: ' . $order->id,
                    'currency' => 'USD',
                ];
                $user->createTransactionAndNotify($transactionData);
                $totalRefunded += $order->charge;
            }
            $order->delete();
        }

        return response()->json([
            'success' => true, 
            'message' => count($orders) . ' orders deleted successfully. Total refunded: $' . number_format($totalRefunded, 2)
        ]);
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

    /**
     * Process refill for an order
     */
    public function refill($id)
    {
        try {
            $order = Order::findOrFail($id);
            $apiOrderId = $order->api_order_id;

            if (!$apiOrderId) {
                return response()->json(['success' => false, 'message' => 'Invalid API order ID.'], 400);
            }

            // Call API to refill the order
            $apiResponse = $this->api->refill($apiOrderId);
            
            if ($apiResponse && isset($apiResponse->success) && $apiResponse->success) {
                // Update order status if needed
                $order->update(['status' => 'processing']);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Order refilled successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to refill order. Please try again.'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error refilling order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the refill.'
            ], 500);
        }
    }

    /**
     * Cancel an order
     */
    public function cancel($id)
    {
        try {
            $order = Order::findOrFail($id);
            $apiOrderId = $order->api_order_id;

            if (!$apiOrderId) {
                return response()->json(['success' => false, 'message' => 'Invalid API order ID.'], 400);
            }

            // Call API to cancel the order
            $apiResponse = $this->api->cancel([$apiOrderId]);
            
            if ($apiResponse && isset($apiResponse->success) && $apiResponse->success) {
                // Update order status
                $order->update(['status' => 'cancelled']);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Order cancelled successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to cancel order. Please try again.'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error cancelling order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the cancellation.'
            ], 500);
        }
    }

    /**
     * Process refund for partial orders
     */
    public function processPartialRefund($id)
    {
        try {
            $order = Order::findOrFail($id);
            
            // Check if order is partial and has a charge
            if ($order->status !== 'partial' || $order->charge <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order is not eligible for a partial refund.'
                ], 400);
            }

            $user = $order->user;
            
            // Calculate refund amount based on completion percentage
            $startCount = $order->start_count ?? 0;
            $remains = $order->remains ?? 0;
            $completed = $startCount - $remains;
            
            if ($startCount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot calculate refund: invalid start count.'
                ], 400);
            }

            // Calculate completion percentage and refund amount
            $completionPercentage = ($completed / $startCount) * 100;
            $refundAmount = $order->charge * (1 - ($completionPercentage / 100));
            
            // Round to 2 decimal places
            $refundAmount = round($refundAmount, 2);

            if ($refundAmount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No refund amount calculated.'
                ], 400);
            }

            // Create refund transaction
            $transactionData = [
                'type' => 'credit',
                'amount' => $refundAmount,
                'status' => 'refunded',
                'description' => 'Partial refund for order ID: ' . $order->id . ' (Completed: ' . number_format($completionPercentage, 1) . '%)',
                'currency' => 'USD',
            ];

            $user->createTransactionAndNotify($transactionData);

            // Update order status to completed
            $order->update(['status' => 'completed']);

            return response()->json([
                'success' => true,
                'message' => 'Partial refund processed successfully! Refunded: $' . number_format($refundAmount, 2) . ' (Completion: ' . number_format($completionPercentage, 1) . '%)'
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing partial refund: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the refund.'
            ], 500);
        }
    }

    /**
     * Get order statistics for dashboard
     */
    public function getStatistics()
    {
        $user = Auth::user();
        $query = Order::query();

        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        $statistics = [
            'total_orders' => $query->count(),
            'completed_orders' => (clone $query)->where('status', 'completed')->count(),
            'pending_orders' => (clone $query)->where('status', 'pending')->count(),
            'total_value' => (clone $query)->sum('charge'),
            'today_orders' => (clone $query)->whereDate('created_at', Carbon::today())->count(),
            'this_month_orders' => (clone $query)->whereMonth('created_at', Carbon::now()->month)->count(),
        ];

        return response()->json($statistics);
    }
}
