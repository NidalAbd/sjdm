<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
        // Check if the request is from API
        if ($request->is('api/*')) {
            // Look for the 'Accept-Language' header or 'lang' query parameter
            $locale = $request->header('Accept-Language') ?? $request->query('lang', config('app.locale'));
        } else {
            // Web request - use session
            $locale = Session::get('applocale', config('app.locale'));
        }

        // Set the application locale
        App::setLocale($locale);

        return $next($request);
    }
}
