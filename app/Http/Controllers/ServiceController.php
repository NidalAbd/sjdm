<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\Api;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query();

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Apply platform filter
        if ($request->filled('platform') && $request->platform !== 'all') {
            $query->where('category', 'like', '%' . $request->platform . '%');
        }

        // Get unique categories after platform filter
        $uniqueCategories = $query->distinct()->pluck('category')->toArray();

        // Apply category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $services = $query->paginate(10);

        // Available categories for the platform filter dropdown
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

    public function fetchFromApi()
    {
        $api = new Api();
        $servicesFromApi = $api->services();

        $totalServices = count($servicesFromApi);
        $storedServices = 0;

        foreach ($servicesFromApi as $service) {
            $storedService = Service::updateOrCreate(
                ['service_id' => $service->service], // Use 'service_id' from the API
                [
                    'name' => $service->name,
                    'type' => $service->type,
                    'category' => $service->category,
                    'rate' => $service->rate,
                    'min' => $service->min,
                    'max' => $service->max,
                    'refill' => $service->refill,
                    'cancel' => $service->cancel,
                ]
            );

            if ($storedService->wasRecentlyCreated || $storedService->wasChanged()) {
                $storedServices++;
            }
        }

        $percentageStored = ($totalServices > 0) ? round(($storedServices / $totalServices) * 100, 2) : 0;

        return redirect()->route('services.index')
            ->with('success', "Services have been updated from the API. $storedServices out of $totalServices services were stored. ($percentageStored%)");
    }

    public function show(Service $service)
    {
        return response()->json([
            'description' => $service->name, // or any other relevant fields
            'rate' => $service->rate,
            'min' => $service->min,
            'max' => $service->max
        ]);
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
