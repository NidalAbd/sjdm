<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create 50 users with random roles
        User::factory()->count(50)->create()->each(function ($user) use ($faker) {
            $role = Role::where('name', 'guest')->first();
            if ($role) {
                $user->assignRole($role);
                $user->syncPermissions($role->permissions);
            }

            // Add a random avatar for each user
            $avatars = [
                'images/avatar1.png',
                'images/avatar2.png',
                'images/avatar3.png',
                'images/avatar04.png',
                'images/avatar5.png',
            ];

            $selectedAvatar = $faker->randomElement($avatars);

            $media = new Media([
                'file_name' => basename($selectedAvatar),
                'file_type' => 'image/png',
                'file_size' => filesize(public_path($selectedAvatar)),
                'path' => $selectedAvatar,
            ]);

            $user->media()->save($media);
        });

        // Create specific admin user
        $adminRole = Role::where('name', 'admin')->first();

        // Ensure the 'admin' role has all permissions
        $allPermissions = Permission::all();
        $adminRole->syncPermissions($allPermissions);

        $adminUser = User::create([
            'name' => 'Nidal.Abd',
            'email' => 'kol.eljra7.90@gmail.com',
            'password' => Hash::make('nedal135'), // Hash the password for security
            'email_verified_at' => now(),
            'status' => 'active',
            'balance' => 10000.00, // Initial balance for the admin
            'currency' => 'USD', // Default currency for the admin
            'language' => 'ar', // Default language
            'gender' => 'male', // Gender
            'marital_status' => 'married', // Marital status
            'date_of_birth' => '1990-03-01', // Date of birth
        ]);

        // Assign the 'admin' role to the user and sync all permissions
        $adminUser->assignRole($adminRole);
        $adminUser->syncPermissions($allPermissions);

        // Add a random avatar for the admin user
        $avatars = [
            'images/avatar1.png',
            'images/avatar2.png',
            'images/avatar3.png',
            'images/avatar04.png',
            'images/avatar5.png',
        ];

        $selectedAvatar = $faker->randomElement($avatars);

        $media = new Media([
            'file_name' => basename($selectedAvatar),
            'file_type' => 'image/png',
            'file_size' => filesize(public_path($selectedAvatar)),
            'path' => $selectedAvatar,
        ]);

        $adminUser->media()->save($media);
    }
}
