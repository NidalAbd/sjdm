<?php

namespace App\Http\Middleware;

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIfBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Check if user is banned
        if ($user && $user->status === 'banned') {
            return redirect()->route('home')->with('error', 'Your account has been banned due to a violation of our policies. While banned, you can log in to view your profile and certain pages, but you are restricted from performing any actions like creating orders or accessing premium features. Please contact support if you believe this is an error or for further assistance.');
        }

        return $next($request);
    }
}

