<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class HandleLanguageRedirects
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();
        $languages = ['ar', 'es', 'fr', 'de', 'ru', 'zh', 'hi', 'pt'];

        // Check if the path starts with a language prefix that should redirect to canonical
        foreach ($languages as $lang) {
            if (strpos($path, $lang . '/') === 0) {
                // Extract the path without language prefix
                $cleanPath = substr($path, strlen($lang) + 1);

                // Build the canonical URL
                $canonicalUrl = '/' . $cleanPath;

                // Preserve query parameters
                if ($request->getQueryString()) {
                    $canonicalUrl .= '?' . $request->getQueryString();
                }

                // Check if this is a service, category, or platform URL that should redirect
                if (preg_match('/^(service\/\d+|category\/|platform\/|all-services|terms-and-conditions|faq|about|how-it-works|support_take|privacy-policy|contact-us)/', $cleanPath)) {
                    return redirect($canonicalUrl, 301);
                }
            }
        }

        return $next($request);
    }
}
