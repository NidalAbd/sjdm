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
        // Check URL for language prefix
        $path = $request->path();

        if (strpos($path, 'ar/') === 0 || strpos($path, 'ar') === 0) {
            // Arabic URL
            $locale = 'ar';
        } else {
            // Check user preference or session for non-prefixed URLs
            if (Auth::check()) {
                $locale = Auth::user()->language ?? 'en';
            } else {
                $locale = Session::get('applocale', 'en');
            }
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
