<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PaymentMethod::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Order by sort_order and name
        $paymentMethods = $query->ordered()->paginate(15);

        return view('payment-methods.index', compact('paymentMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $paymentTypes = PaymentMethod::PAYMENT_TYPES;
        $currencies = PaymentMethod::CURRENCIES;
        
        return view('payment-methods.create', compact('paymentTypes', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(PaymentMethod::PAYMENT_TYPES)),
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
            'processing_fee_fixed' => 'nullable|numeric|min:0',
            'processing_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'currency' => 'required|string|in:' . implode(',', array_keys(PaymentMethod::CURRENCIES)),
            'processing_time_min' => 'nullable|integer|min:1',
            'processing_time_max' => 'nullable|integer|min:1|gte:processing_time_min',
            'supported_countries' => 'nullable|array',
            'gateway_url' => 'nullable|url',
            'webhook_url' => 'nullable|url',
            'instructions' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'requires_verification' => 'boolean',
            'settings' => 'nullable|array',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'merchant_id' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
        ]);

        $data = $request->except(['logo', 'supported_countries', 'settings', 'api_key', 'api_secret', 'merchant_id', 'webhook_secret']);
        $data['is_active'] = $request->has('is_active');
        $data['requires_verification'] = $request->has('requires_verification');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('payment-methods', 'public');
            $data['logo'] = $logoPath;
        }

        // Handle supported countries
        if ($request->filled('supported_countries')) {
            $data['supported_countries'] = $request->supported_countries;
        }

        // Handle settings
        if ($request->filled('settings')) {
            $data['settings'] = $request->settings;
        }

        // Handle API credentials
        $apiCredentials = [];
        
        if ($request->filled('api_key')) {
            $apiCredentials['api_key'] = $request->api_key;
        }
        
        if ($request->filled('api_secret')) {
            $apiCredentials['api_secret'] = $request->api_secret;
        }
        
        if ($request->filled('merchant_id')) {
            $apiCredentials['merchant_id'] = $request->merchant_id;
        }
        
        if ($request->filled('webhook_secret')) {
            $apiCredentials['webhook_secret'] = $request->webhook_secret;
        }
        
        if (!empty($apiCredentials)) {
            $data['api_credentials'] = $apiCredentials;
        }

        PaymentMethod::create($data);

        return redirect()->route('payment-methods.index')
            ->with('success', __('adminlte.payment_method_created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethod $paymentMethod)
    {
        return view('payment-methods.show', compact('paymentMethod'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        $paymentTypes = PaymentMethod::PAYMENT_TYPES;
        $currencies = PaymentMethod::CURRENCIES;
        
        return view('payment-methods.edit', compact('paymentMethod', 'paymentTypes', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(PaymentMethod::PAYMENT_TYPES)),
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
            'processing_fee_fixed' => 'nullable|numeric|min:0',
            'processing_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'currency' => 'required|string|in:' . implode(',', array_keys(PaymentMethod::CURRENCIES)),
            'processing_time_min' => 'nullable|integer|min:1',
            'processing_time_max' => 'nullable|integer|min:1|gte:processing_time_min',
            'supported_countries' => 'nullable|array',
            'gateway_url' => 'nullable|url',
            'webhook_url' => 'nullable|url',
            'instructions' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'requires_verification' => 'boolean',
            'settings' => 'nullable|array',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'merchant_id' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
        ]);

        $data = $request->except(['logo', 'supported_countries', 'settings', 'api_key', 'api_secret', 'merchant_id', 'webhook_secret']);
        $data['is_active'] = $request->has('is_active');
        $data['requires_verification'] = $request->has('requires_verification');

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($paymentMethod->logo) {
                Storage::disk('public')->delete($paymentMethod->logo);
            }
            
            $logoPath = $request->file('logo')->store('payment-methods', 'public');
            $data['logo'] = $logoPath;
        }

        // Handle supported countries
        if ($request->filled('supported_countries')) {
            $data['supported_countries'] = $request->supported_countries;
        }

        // Handle settings
        if ($request->filled('settings')) {
            $data['settings'] = $request->settings;
        }

        // Handle API credentials
        $apiCredentials = $paymentMethod->api_credentials ?? [];
        
        if ($request->filled('api_key')) {
            $apiCredentials['api_key'] = $request->api_key;
        }
        
        if ($request->filled('api_secret')) {
            $apiCredentials['api_secret'] = $request->api_secret;
        }
        
        if ($request->filled('merchant_id')) {
            $apiCredentials['merchant_id'] = $request->merchant_id;
        }
        
        if ($request->filled('webhook_secret')) {
            $apiCredentials['webhook_secret'] = $request->webhook_secret;
        }
        
        if (!empty($apiCredentials)) {
            $data['api_credentials'] = $apiCredentials;
        }

        $paymentMethod->update($data);

        return redirect()->route('payment-methods.index')
            ->with('success', __('adminlte.payment_method_updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        // Check if payment method is being used in transactions
        if ($paymentMethod->transactions()->exists()) {
            return redirect()->route('payment-methods.index')
                ->with('error', __('adminlte.cannot_delete_payment_method_in_use'));
        }

        // Delete logo if exists
        if ($paymentMethod->logo) {
            Storage::disk('public')->delete($paymentMethod->logo);
        }

        $paymentMethod->delete();

        return redirect()->route('payment-methods.index')
            ->with('success', __('adminlte.payment_method_deleted_successfully'));
    }

    /**
     * Toggle the status of a payment method
     */
    public function toggleStatus(PaymentMethod $paymentMethod)
    {
        $oldStatus = $paymentMethod->is_active;
        $newStatus = !$oldStatus;
        
        $paymentMethod->update([
            'is_active' => $newStatus
        ]);

        $status = $newStatus ? 'activated' : 'deactivated';
        
        return redirect()->route('payment-methods.index')
            ->with('success', __('adminlte.payment_method_' . $status . '_successfully'));
    }

    /**
     * Handle bulk actions for payment methods
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'payment_methods' => 'required|array|min:1',
            'payment_methods.*' => 'exists:payment_methods,id'
        ]);

        $paymentMethods = PaymentMethod::whereIn('id', $request->payment_methods);

        switch ($request->action) {
            case 'activate':
                $paymentMethods->update(['is_active' => true]);
                $message = __('adminlte.payment_methods_activated_successfully');
                break;

            case 'deactivate':
                $paymentMethods->update(['is_active' => false]);
                $message = __('adminlte.payment_methods_deactivated_successfully');
                break;

            case 'delete':
                // Check if any payment method is being used in transactions
                $usedPaymentMethods = $paymentMethods->whereHas('transactions')->pluck('name')->toArray();
                
                if (!empty($usedPaymentMethods)) {
                    return redirect()->route('payment-methods.index')
                        ->with('error', __('adminlte.cannot_delete_payment_methods_in_use') . ': ' . implode(', ', $usedPaymentMethods));
                }

                // Delete logos
                $paymentMethods->whereNotNull('logo')->get()->each(function ($paymentMethod) {
                    Storage::disk('public')->delete($paymentMethod->logo);
                });

                $paymentMethods->delete();
                $message = __('adminlte.payment_methods_deleted_successfully');
                break;
        }

        return redirect()->route('payment-methods.index')->with('success', $message);
    }
} 