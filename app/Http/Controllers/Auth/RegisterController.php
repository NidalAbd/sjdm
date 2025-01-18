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
        ], [
            // Custom messages for 'name'
            'name.required' => 'Your name is required. Please provide it to proceed.',
            'name.string' => 'Your name should be a valid string.',
            'name.max' => 'Your name cannot be longer than 255 characters.',

            // Custom messages for 'email'
            'email.required' => 'An email address is required.',
            'email.string' => 'The email must be a valid string.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Your email address cannot be longer than 255 characters.',
            'email.unique' => 'This email is already registered. If you have an account, please log in.',

            // Custom messages for 'password'
            'password.required' => 'Please provide a password to secure your account.',
            'password.string' => 'The password must be a valid string.',
            'password.min' => 'Your password must be at least 8 characters long.',
            'password.confirmed' => 'The password confirmation does not match.',

            // Custom messages for 'referral_code'
            'referral_code.exists' => 'The referral code you entered is invalid. Please check and try again.',
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

        return $user;
    }

}
