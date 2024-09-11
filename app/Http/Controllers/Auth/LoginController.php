<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Models\User;
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

    protected function credentials(Request $request)
    {
        // Get the original credentials from the request
        $credentials = $request->only($this->username(), 'password');

        // Check if the user exists and is soft deleted
        $user = User::withTrashed()->where('email', $credentials['email'])->first();

        // If the user is soft deleted, add a custom error message
        if ($user && $user->trashed()) {
            return redirect()->back()->withErrors([
                'email' => 'Your account has been deleted. Please contact support or restore your account.',
            ]);
        }

        // If the user is not soft deleted, return the credentials
        return $credentials;
    }

    /**
     * Override the sendFailedLoginResponse method to handle custom messages.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        // Get the user by email
        $user = User::withTrashed()->where('email', $request->email)->first();

        if ($user && $user->trashed()) {
            // Return a custom message for soft deleted users
            return redirect()->back()->withErrors([
                'email' => 'Your account has been deleted. Please contact support or restore your account.',
            ]);
        }

        // Default failed login response
        return redirect()->back()->withErrors([
            $this->username() => trans('auth.failed'),
        ]);
    }

    /**
     * Handle a successful login for soft-deleted users who have been restored.
     *
     * @param Request $request
     * @param $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->trashed()) {
            // Redirect to a page where the user can restore their account
            return redirect()->route('account.restore')->with('warning', 'Your account was previously deleted. Please restore your account before proceeding.');
        }

        return redirect()->intended($this->redirectTo);
    }
}
