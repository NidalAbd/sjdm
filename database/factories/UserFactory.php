<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // default password for testing
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP', 'AED', 'SAR']),
            'language' => $this->faker->randomElement(['en', 'ar', 'fr', 'es']),
            'status' => $this->faker->randomElement(['active', 'inactive', 'suspended', 'banned']),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'marital_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed']),
            'date_of_birth' => $this->faker->date('Y-m-d', '2003-12-31'), // Users must be at least 18 years old
            'remember_token' => Str::random(10),
        ];
    }

    public function configure(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            // Assign a random role to the user, excluding 'admin'
            $role = Role::where('name', '!=', 'admin')->inRandomOrder()->first();
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

            $selectedAvatar = $this->faker->randomElement($avatars);

            $media = new Media([
                'file_name' => basename($selectedAvatar),
                'file_type' => 'image/png',
                'file_size' => filesize(public_path($selectedAvatar)),
                'path' => $selectedAvatar,
            ]);

            $user->media()->save($media);
        });
    }
}
