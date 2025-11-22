<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * Get all services for the SPA
     */
    public function services(): JsonResponse
    {
        $services = Service::select([
            'service_id',
            'name_en',
            'name_ar',
            'category_en',
            'category_ar',
            'type',
            'rate',
            'min',
            'max',
            'refill',
            'cancel',
        ])->get();

        return response()->json([
            'services' => $services,
        ]);
    }

    /**
     * Get a single service
     */
    public function service($id): JsonResponse
    {
        $service = Service::where('service_id', $id)->first();

        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        return response()->json([
            'service' => $service,
        ]);
    }

    /**
     * Get stats for the home page
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'totalServices' => Service::count(),
            'totalOrders' => class_exists(Order::class) ? Order::count() : 0,
            'totalUsers' => class_exists(User::class) ? User::count() : 0,
        ]);
    }

    /**
     * Get categories
     */
    public function categories(): JsonResponse
    {
        $categoriesEn = Service::distinct()->pluck('category_en')->filter()->values();
        $categoriesAr = Service::distinct()->pluck('category_ar')->filter()->values();

        return response()->json([
            'categories_en' => $categoriesEn,
            'categories_ar' => $categoriesAr,
        ]);
    }
}
