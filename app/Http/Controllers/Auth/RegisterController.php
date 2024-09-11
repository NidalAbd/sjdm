<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Faker\Factory as Faker;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
        $this->faker = Faker::create();  // Initialize Faker
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'referral_code' => 'nullable|string|exists:users,referral_code',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function create(array $data)
    {
        $referrer = null; // Initialize referrer as null

        if (!empty($data['referral_code'])) {
            $referrer = User::where('referral_code', $data['referral_code'])->first();
        }
        // Check if the user exists but is soft-deleted
        $user = User::withTrashed()->where('email', $data['email'])->first();

        if ($user && $user->trashed()) {
            // Return a custom message for soft-deleted users trying to register
            return back()->withErrors(['email' => 'This email is already registered but has been deleted. Please contact support to restore your account.']);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'referred_by' => $referrer ? $referrer->id : null,
        ]);

        // Assign the 'client' role to the newly registered user
        $role = Role::where('name', 'client')->first();
        if ($role) {
            $user->assignRole($role);
            $user->syncPermissions($role->permissions);
        }

        // Add a profile photo to the user's media
        $avatars = [
            'images/avatar1.png',
            'images/avatar2.png',
            'images/avatar3.png',
            'images/avatar04.png',
            'images/avatar5.png',
        ];

        $selectedAvatar = Arr::random($avatars); // Use Arr::random for better randomness

        $media = new Media([
            'file_name' => basename($selectedAvatar),
            'file_type' => 'image/png',
            'file_size' => filesize(public_path($selectedAvatar)),
            'path' => $selectedAvatar,
        ]);

        $user->media()->save($media);

        event(new Registered($user));

        return $user;
    }

}
