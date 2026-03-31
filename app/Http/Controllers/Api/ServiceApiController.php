<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceApiController extends Controller
{
    public function show(Request $request, $id)
    {
        Log::info('API: Service show requested', ['user_id' => $request->user()?->id, 'service_id' => $id]);
        $service = Service::where('id', $id)
            ->orWhere('service_id', $id)
            ->firstOrFail();

        return response()->json(['service' => $service]);
    }

    public function update(Request $request, $id)
    {
        $service = Service::where('id', $id)
            ->orWhere('service_id', $id)
            ->firstOrFail();

        $request->validate([
            'name_en' => 'sometimes|string|max:255',
            'name_ar' => 'sometimes|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'rate' => 'sometimes|numeric|min:0',
            'min' => 'sometimes|integer|min:1',
            'max' => 'sometimes|integer|min:1',
            'is_active' => 'sometimes|boolean',
            'refill' => 'sometimes|boolean',
            'dripfeed' => 'sometimes|boolean',
            'category_en' => 'nullable|string|max:255',
            'category_ar' => 'nullable|string|max:255',
            'platform' => 'nullable|string|max:100',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $service->fill($request->only([
            'name_en', 'name_ar', 'description_en', 'description_ar',
            'rate', 'min', 'max', 'is_active', 'refill', 'dripfeed',
            'category_en', 'category_ar', 'platform', 'sort_order'
        ]));
        $service->save();

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $service = Service::where('id', $id)
            ->orWhere('service_id', $id)
            ->firstOrFail();

        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully',
        ]);
    }

    public function sync(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $service = Service::where('id', $id)
            ->orWhere('service_id', $id)
            ->firstOrFail();

        $api = new Api();
        $services = $api->services();

        $apiService = collect($services)->firstWhere('service', $service->service_id);

        if (!$apiService) {
            return response()->json([
                'message' => 'Service not found in API'
            ], 404);
        }

        // Update from API
        $service->api_rate = $apiService['rate'];
        $service->original_min = $apiService['min'];
        $service->original_max = $apiService['max'];
        $service->save();

        return response()->json([
            'message' => 'Service synced from API',
            'service' => $service,
        ]);
    }

    public function duplicate(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $service = Service::where('id', $id)
            ->orWhere('service_id', $id)
            ->firstOrFail();

        $newService = $service->replicate();
        $newService->name_en = $service->name_en . ' (Copy)';
        $newService->name_ar = $service->name_ar ? $service->name_ar . ' (نسخة)' : null;
        $newService->service_id = $service->service_id . '_copy_' . time();
        $newService->save();

        return response()->json([
            'message' => 'Service duplicated successfully',
            'service' => $newService,
        ]);
    }
}
