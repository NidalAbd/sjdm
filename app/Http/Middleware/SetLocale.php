<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        // For API requests, use the 'Accept-Language' header
        if ($request->is('api/*')) {
            $locale = $request->header('Accept-Language') ?? config('app.locale');
            $locale = $this->extractPrimaryLocale($locale);
        } else {
            // Check if there's a locale in the URL (from route parameter)
            $routeLocale = $request->route('locale');

            if ($routeLocale) {
                // Validate the locale from the URL
                $supportedLocales = ['ar', 'es', 'fr', 'de', 'ru', 'zh', 'hi', 'pt'];

                if (in_array($routeLocale, $supportedLocales)) {
                    $locale = $routeLocale;
                } else {
                    // Invalid locale in URL, default to English
                    $locale = 'en';
                }
            } else {
                // No locale in URL, determine from user preference or session
                if (Auth::check()) {
                    // If authenticated, use the user's language from the database
                    $locale = Auth::user()->language ?? config('app.locale');
                } else {
                    // For guests, use session or fallback to default locale
                    $locale = Session::get('applocale', config('app.locale'));
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

        // Update the session locale for future requests (only if not from URL)
        if (!$request->route('locale')) {
            Session::put('applocale', $locale);
        }

        return $next($request);
    }

    /**
     * Extracts the primary locale from the Accept-Language header.
     *
     * @param string $localeHeader
     * @return string
     */
    private function extractPrimaryLocale($localeHeader)
    {
        // Parse the Accept-Language header, take the first part (e.g., 'en-US' -> 'en')
        $localeParts = explode(',', $localeHeader);
        $primaryLocale = explode('-', $localeParts[0])[0];  // 'en-US' -> 'en'

        return $primaryLocale;
    }
}
