<?php

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
        // For API requests, use the 'Accept-Language' header
        if ($request->is('api/*')) {
            $locale = $request->header('Accept-Language') ?? config('app.locale');
            $locale = $this->extractPrimaryLocale($locale);
        } else {
            // FIXED: Get locale from route parameter first (this handles {locale} routes)
            $locale = $request->route('locale');

            if ($locale) {
                // Validate the locale from the URL parameter
                $supportedLocales = ['ar', 'es', 'fr', 'de', 'ru', 'zh', 'hi', 'pt'];

                if (in_array($locale, $supportedLocales)) {
                    // Valid locale from route parameter
                    App::setLocale($locale);
                    Session::put('applocale', $locale);
                    return $next($request);
                }
            }

            // Fallback: No locale in route parameter, check path
            $path = $request->path();

            if (strpos($path, 'ar/') === 0 || $path === 'ar') {
                $locale = 'ar';
            } else {
                // Default to English or user preference
                if (Auth::check()) {
                    $locale = Auth::user()->language ?? 'en';
                } else {
                    $locale = Session::get('applocale', 'en');
                }
            }
        }

        // Ensure we have a valid locale
        $allSupportedLocales = ['en', 'ar', 'es', 'fr', 'de', 'ru', 'zh', 'hi', 'pt'];
        if (!in_array($locale, $allSupportedLocales)) {
            $locale = 'en'; // Default to English
        }

        // Set the application locale
        App::setLocale($locale);

        // Update the session locale for future requests
        Session::put('applocale', $locale);

        return $next($request);
    }

    /**
     * Extracts the primary locale from the Accept-Language header.
     */
    private function extractPrimaryLocale($localeHeader)
    {
        // Parse the Accept-Language header, take the first part (e.g., 'en-US' -> 'en')
        $localeParts = explode(',', $localeHeader);
        $primaryLocale = explode('-', $localeParts[0])[0];  // 'en-US' -> 'en'

        return $primaryLocale;
    }
}
