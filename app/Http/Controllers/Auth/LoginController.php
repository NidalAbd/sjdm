<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Ensure user is properly authenticated
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Authentication failed.']);
        }

        // Check if user is banned
        if ($user->status === 'banned') {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Your account has been banned.']);
        }

        // Redirect to intended URL or dashboard
        $redirectUrl = $request->get('redirect', '/home');
        
        // Ensure the redirect URL is safe (only internal URLs)
        if (!filter_var($redirectUrl, FILTER_VALIDATE_URL) || parse_url($redirectUrl, PHP_URL_HOST) === request()->getHost()) {
            return redirect($redirectUrl);
        }

        return redirect('/home');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
