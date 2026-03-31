<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentMethodApiController extends Controller
{
    public function index(Request $request)
    {
        Log::info('API: Payment Methods index requested', ['user_id' => $request->user()?->id]);
        $methods = PaymentMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'type', 'description', 'logo', 'min_amount', 'max_amount', 'processing_fee_fixed', 'processing_fee_percentage', 'instructions']);

        return response()->json([
            'methods' => $methods,
        ]);
    }
}
