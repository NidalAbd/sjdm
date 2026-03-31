<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentMethodApiController extends Controller
{
    public function index(Request $request)
    {
        Log::info('API: Admin Payment Methods index requested', ['admin_id' => $request->user()?->id]);
        $methods = PaymentMethod::orderBy('sort_order')->get();

        return response()->json([
            'payment_methods' => $methods,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:payment_methods,slug',
            'type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'logo' => 'nullable|string|max:191',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'processing_fee_fixed' => 'nullable|numeric|min:0',
            'processing_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'instructions' => 'nullable|string',
        ]);

        $method = PaymentMethod::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'type' => $request->type,
            'description' => $request->description,
            'logo' => $request->logo,
            'min_amount' => $request->min_amount ?? 5,
            'max_amount' => $request->max_amount ?? 10000,
            'processing_fee_fixed' => $request->processing_fee_fixed ?? 0,
            'processing_fee_percentage' => $request->processing_fee_percentage ?? 0,
            'sort_order' => $request->sort_order ?? (PaymentMethod::max('sort_order') + 1),
            'is_active' => $request->is_active ?? true,
            'instructions' => $request->instructions,
        ]);

        return response()->json([
            'message' => 'Payment method created successfully',
            'payment_method' => $method,
        ]);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|max:50',
            'description' => 'nullable|string',
            'logo' => 'nullable|string|max:191',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'processing_fee_fixed' => 'nullable|numeric|min:0',
            'processing_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'instructions' => 'nullable|string',
        ]);

        $paymentMethod->fill($request->only([
            'name', 'type', 'description', 'logo',
            'min_amount', 'max_amount', 'processing_fee_fixed', 'processing_fee_percentage',
            'sort_order', 'is_active', 'instructions'
        ]));
        $paymentMethod->save();

        return response()->json([
            'message' => 'Payment method updated successfully',
            'payment_method' => $paymentMethod,
        ]);
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return response()->json([
            'message' => 'Payment method deleted successfully',
        ]);
    }

    public function toggle(PaymentMethod $paymentMethod)
    {
        $paymentMethod->is_active = !$paymentMethod->is_active;
        $paymentMethod->save();

        return response()->json([
            'message' => $paymentMethod->is_active ? 'Payment method activated' : 'Payment method deactivated',
            'is_active' => $paymentMethod->is_active,
        ]);
    }

    public function moveUp(PaymentMethod $paymentMethod)
    {
        $previousMethod = PaymentMethod::where('sort_order', '<', $paymentMethod->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($previousMethod) {
            $tempOrder = $paymentMethod->sort_order;
            $paymentMethod->sort_order = $previousMethod->sort_order;
            $previousMethod->sort_order = $tempOrder;
            $paymentMethod->save();
            $previousMethod->save();
        }

        return response()->json([
            'message' => 'Order updated',
        ]);
    }

    public function moveDown(PaymentMethod $paymentMethod)
    {
        $nextMethod = PaymentMethod::where('sort_order', '>', $paymentMethod->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($nextMethod) {
            $tempOrder = $paymentMethod->sort_order;
            $paymentMethod->sort_order = $nextMethod->sort_order;
            $nextMethod->sort_order = $tempOrder;
            $paymentMethod->save();
            $nextMethod->save();
        }

        return response()->json([
            'message' => 'Order updated',
        ]);
    }
}
