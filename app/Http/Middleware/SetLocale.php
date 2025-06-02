<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->route('locale');

        Log::info('SetLocale Middleware', [
            'route_locale' => $locale,
            'current_app_locale' => App::getLocale(),
            'session_locale' => session('applocale'),
            'url' => $request->url()
        ]);

        if ($locale && in_array($locale, ['ar', 'es', 'fr', 'de', 'ru', 'zh', 'hi', 'pt'])) {
            App::setLocale($locale);
            session(['applocale' => $locale]);
            Log::info('SetLocale: Set locale to ' . $locale);
        } else {
            // Check if we have a session locale for non-prefixed routes
            $sessionLocale = session('applocale', 'en');

            // For non-prefixed routes, default to English
            App::setLocale('en');
            session(['applocale' => 'en']);
            Log::info('SetLocale: Default locale set to en');
        }

        return $next($request);
    }
}
