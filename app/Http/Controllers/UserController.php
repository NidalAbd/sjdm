<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Media;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Notifications\ProfileUpdatedNotification;
use App\Notifications\UserStatusChangedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request)
    {
        $query = User::with(['roles', 'permissions']);

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'LIKE', '%' . $request->search . '%')
                ->orWhere('email', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('role') && $request->role != '') {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('created_at', 'asc')->paginate(5);
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        // Attach roles and permissions
        if ($request->has('roles')) {
            $user->roles()->attach($request->roles);
            foreach ($request->roles as $roleId) {
                $role = Role::find($roleId);
                if ($role) {
                    $permissions = $role->permissions;
                    $user->permissions()->syncWithoutDetaching($permissions);
                }
            }
        }

        // Handle media upload
        if ($request->hasFile('media')) {
            $this->handleMediaUpload($request, $user);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'orders', 'transactions', 'media']);

        // Fetch only active and verified referrals
        $referrals = User::where('referred_by', $user->id)
            ->where('status', 'active')
            ->whereNotNull('email_verified_at')
            ->latest()
            ->get();

        $orders = $user->orders()->latest()->get();
        $transactions = $user->transactions()->latest()->get();

        return view('users.show', compact('user', 'orders', 'transactions', 'referrals'));
    }



    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        $user->notify(new ProfileUpdatedNotification($user));

        if ($request->has('status')) {
            $user->notify(new UserStatusChangedNotification($user->status));
        }

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
            foreach ($request->roles as $roleId) {
                $role = Role::find($roleId);
                if ($role) {
                    $permissions = $role->permissions;
                    $user->permissions()->syncWithoutDetaching($permissions);
                }
            }
        }

        // Handle media upload
        if ($request->hasFile('media')) {
            $this->handleMediaUpload($request, $user);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User has been soft deleted successfully.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.index')->with('success', 'User restored successfully.');
    }

    public function assignRole(User $user)
    {
        $this->authorize('assignRole', $user);

        $roles = Role::all();
        return view('users.roles.assign', compact('user', 'roles'));
    }

    public function storeAssignRole(Request $request, $userId)
    {
        $this->authorize('assignRole', User::find($userId));

        $request->validate([
            'roles' => 'array',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $user = User::find($userId);
        if ($user) {
            // Assign the roles to the user
            $user->roles()->sync($request->roles);

            // Get the permissions for the assigned roles
            $assignedRoles = Role::whereIn('id', $request->roles)->get();
            $permissions = $assignedRoles->map->permissions->flatten()->pluck('id')->unique();

            // Assign the permissions to the user
            $user->permissions()->sync($permissions);

            // Notify the user about the assigned roles
            $user->notify(new \App\Notifications\RoleAssignedNotification($assignedRoles));

            // Clear permission cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return redirect()->route('users.assignRole', $user)->with('success', 'Roles and default permissions assigned successfully.');
        }

        return redirect()->route('users.index')->with('error', 'User not found.');
    }

    public function assignPermission(User $user)
    {
        $this->authorize('assignPermission', $user);

        $permissions = Permission::all()->groupBy(function ($item) {
            if (str_contains($item->name, 'view_any')) {
                return 'view_any';
            } elseif (str_contains($item->name, 'create')) {
                return 'create';
            } elseif (str_contains($item->name, 'edit') !== false) {
                return 'edit';
            } elseif (str_contains($item->name, 'delete') !== false) {
                return 'delete';
            } elseif (str_contains($item->name, 'view') !== false) {
                return 'view';
            } else {
                return 'other';
            }
        });
        return view('users.permissions.assign', compact('user', 'permissions'));
    }

    public function storeAssignPermission(Request $request, $userId)
    {
        $this->authorize('assignPermission', User::find($userId));

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $user = User::find($userId);
        if ($user) {
            $user->permissions()->sync($request->permissions);
            $assignedPermissions = Permission::whereIn('id', $request->permissions)->get();
            $user->notify(new \App\Notifications\PermissionAssignedNotification($assignedPermissions));

            // Clear permission cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return redirect()->route('users.assignPermission', $user)->with('success', 'Permissions assigned successfully.');
        }

        return redirect()->route('users.index')->with('error', 'User not found.');
    }

    private function handleMediaUpload(Request $request, User $user)
    {
        // Store each uploaded file
        foreach ($request->file('media') as $file) {
            $path = $file->store('uploads', 'public');

            // Create a new media record
            $media = new Media([
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'path' => $path,
            ]);

            // Associate the media with the user
            $user->media()->save($media);
        }
    }

    public function updateImage(Request $request)
    {
        // Validate the image upload
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        try {
            // Handle file upload
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Store the file in the 'public/profile_images' directory
                // This will save the image in storage/app/public/profile_images
                $path = $file->storeAs('profile_images', $fileName, 'public');

                // Check if the user already has a media record
                $existingMedia = $user->media()->first();

                // If there is an existing image, delete it from storage
                if ($existingMedia) {
                    Storage::disk('public')->delete($existingMedia->path);
                }

                // Save or update the user's media record
                $user->media()->updateOrCreate(
                    ['mediable_id' => $user->id, 'mediable_type' => get_class($user)], // Conditions to check for the existing record
                    [
                        'file_name' => $fileName,
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'path' => 'profile_images/' . $fileName, // Update with the correct path
                    ]
                );

                // If upload and save succeed, redirect with success message
                return redirect()->back()->with('success', 'Profile image updated successfully!');
            }

            // Fallback in case the file wasn't uploaded
            return redirect()->back()->with('error', 'No image file uploaded.');
        } catch (\Exception $e) {
            // Catch any errors and return an error message
            return redirect()->back()->with('error', 'Failed to upload image. ' . $e->getMessage());
        }
    }
    public function addBalance(User $user)
    {
        // Check and log if the policy is being invoked
        Log::info('Checking add_balance policy for user: ', ['user_id' => $user->id]);

        // Perform the actual authorization check
        return $user->isAdmin() || $user->hasPermissionTo('add_balance');
    }

    /**
     * Process adding balance to a user.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processAddBalance(Request $request)
    {
        $this->authorize('add_balance', Transaction::class);

        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'transaction_type' => 'required|in:credit,debit',
        ]);

        $user = User::findOrFail($request->user_id);
        $amount = $request->amount;
        $transactionType = $request->transaction_type;

        // If it's a debit transaction, decrease the user's balance by the amount
        if ($transactionType === 'debit') {
            // Decrease the balance even if it results in a negative balance
            $user->decrement('balance', $amount);
        }

        // If it's a credit transaction, increase the user's balance
        if ($transactionType === 'credit') {
            // Increment user's balance
            $user->increment('balance', $amount);
        }

        // Create the transaction and send notifications using the existing method
        $transactionData = [
            'type' => $transactionType,
            'amount' => $amount,
            'status' => 'completed',
            'currency' => 'USD', // assuming USD as the default currency
        ];

        // Use the createTransactionAndNotify method
        $transaction = $user->createTransactionAndNotify($transactionData);

        return redirect()->route('transactions.index')->with('success', ucfirst($transactionType) . ' transaction processed successfully.');
    }



    public function referalIndex()
    {
        $user = Auth::user(); // Get the currently authenticated user

        // Fetch referrals related to the authenticated user
        $referrals = $user->referrals()->paginate(10);

        return view('referral.index', compact('user', 'referrals'));
    }


    public function toggleBan(User $user)
    {
        // Check current user status and toggle between 'banned' and 'inactive'
        if ($user->status == 'banned') {
            // Unban the user by setting their status to 'inactive'
            $user->status = 'inactive';
            $message = 'User has been unbanned and set to inactive.';
        } else {
            // Ban the user if they are active, inactive, or suspended
            $user->status = 'banned';
            $message = 'User has been banned successfully.';
        }

        // Save the updated user status
        $user->save();

        // Optionally, you can notify the user about the status change or log the action
        // $user->notify(new UserStatusChangedNotification($user));

        return redirect()->route('users.index')->with('success', $message);
    }

}
