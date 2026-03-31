<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileApiController extends Controller
{
    public function update(Request $request)
    {
        Log::info('API: Profile update requested', ['user_id' => $request->user()?->id]);
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $file = $request->file('avatar');
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Store the file in the 'public/profile_images' directory
        $path = $file->storeAs('profile_images', $fileName, 'public');

        // Check if the user already has a media record
        $existingMedia = $user->media()->first();

        // If there is an existing image, delete it from storage
        if ($existingMedia) {
            Storage::disk('public')->delete($existingMedia->path);
        }

        // Save or update the user's media record (same as Blade version)
        $user->media()->updateOrCreate(
            ['mediable_id' => $user->id, 'mediable_type' => get_class($user)],
            [
                'file_name' => $fileName,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'path' => 'profile_images/' . $fileName,
            ]
        );

        return response()->json([
            'message' => 'Avatar updated successfully',
            'avatar_url' => Storage::url($path),
        ]);
    }
}
