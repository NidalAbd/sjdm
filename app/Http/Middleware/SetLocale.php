<?php
// app/Http/Middleware/SetLocale.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // Get locale from route parameter first
        $locale = $request->route('locale');

        if ($locale) {
            // If locale is in the route, validate it
            if (in_array($locale, ['ar', 'es', 'fr', 'de', 'ru', 'zh', 'hi', 'pt'])) {
                App::setLocale($locale);
                Session::put('applocale', $locale);
                return $next($request);
            }
        }

        // If no locale in route, check URL path
        $path = $request->path();

        if (strpos($path, 'ar/') === 0 || $path === 'ar') {
            App::setLocale('ar');
            Session::put('applocale', 'ar');
            return $next($request);
        }

        // Default to English or user preference
        if (Auth::check()) {
            $locale = Auth::user()->language ?? 'en';
        } else {
            $locale = Session::get('applocale', 'en');
        }

        // Ensure valid locale
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }

        App::setLocale($locale);
        Session::put('applocale', $locale);

        return $next($request);
    }
}
