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
        } else {
            // Check if the user is authenticated
            if (Auth::check()) {
                // If authenticated, use the user's language from the database
                $locale = Auth::user()->language ?? config('app.locale');
            } else {
                // For guests, use session or fallback to default locale
                $locale = Session::get('applocale', config('app.locale'));
            }
        }

        // Set the application locale
        App::setLocale($locale);

        // Update the session locale for future requests
        Session::put('applocale', $locale);

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
