<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
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
            // For web requests, use session
            $locale = Session::get('applocale', config('app.locale'));
        }

        // Set the locale
        App::setLocale($locale);

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
