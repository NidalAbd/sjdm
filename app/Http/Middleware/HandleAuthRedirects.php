<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleAuthRedirects
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
        // Check if this is a login/register page with a redirect parameter
        if (($request->is('login') || $request->is('register')) && $request->has('redirect')) {
            $redirectUrl = $request->get('redirect');

            // Add no-index meta tag for these pages to prevent indexing
            $response = $next($request);

            if (method_exists($response, 'header')) {
                $response->header('X-Robots-Tag', 'noindex, nofollow');
            }

            return $response;
        }

        // Check if user is already authenticated and trying to access login/register
        if (Auth::check() && ($request->is('login') || $request->is('register'))) {
            $redirectUrl = $request->get('redirect', '/home');
            return redirect($redirectUrl);
        }

        return $next($request);
    }
}
