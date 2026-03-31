<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserApiController extends Controller
{
    public function index(Request $request)
    {
        Log::info('API: Admin Users index requested', ['admin_id' => $request->user()?->id]);
        $query = User::with('roles');

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'unverified') {
                $query->whereNull('email_verified_at');
            } elseif ($request->status === 'banned') {
                $query->where('status', 'banned');
            } elseif ($request->status === 'active') {
                $query->where('status', 'active');
            }
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->latest()->paginate($request->get('per_page', 15));

        // Stats
        $stats = [
            'total' => User::count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'banned' => User::where('status', 'banned')->count(),
            'active' => User::where('status', 'active')->count(),
            'total_balance' => User::sum('balance'),
        ];

        return response()->json([
            'users' => $users->items(),
            'stats' => $stats,
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'balance' => 'nullable|numeric|min:0',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'balance' => $request->balance ?? 0,
            'email_verified_at' => now(),
        ]);

        if ($request->role) {
            $user->assignRole($request->role);
        }

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user->load('roles'),
        ]);
    }

    public function show(User $user)
    {
        $user->load('roles', 'permissions');

        $stats = [
            'total_orders' => $user->orders()->count(),
            'total_spent' => $user->orders()->sum('charge'),
            'total_deposits' => $user->transactions()
                ->where('type', 'credit')
                ->where('status', 'completed')
                ->sum('amount'),
            'referrals_count' => User::where('referred_by', $user->id)->count(),
        ];

        return response()->json([
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'balance' => 'nullable|numeric|min:0',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->has('balance')) {
            $user->balance = $request->balance;
        }

        $user->save();

        if ($request->has('role')) {
            $user->syncRoles([$request->role]);
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->load('roles'),
        ]);
    }

    public function destroy(User $user)
    {
        // Prevent deleting self or admin
        if ($user->hasRole('admin') || auth()->id() === $user->id) {
            return response()->json([
                'message' => 'Cannot delete this user'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    public function toggleBan(User $user)
    {
        // Prevent banning admin or self
        if ($user->hasRole('admin') || auth()->id() === $user->id) {
            return response()->json([
                'message' => 'Cannot ban this user'
            ], 403);
        }

        $user->status = $user->status === 'banned' ? 'active' : 'banned';
        $user->save();

        $isBanned = $user->status === 'banned';

        return response()->json([
            'message' => $isBanned ? 'User banned' : 'User unbanned',
            'is_banned' => $isBanned,
            'status' => $user->status,
        ]);
    }

    public function addBalance(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $amount = $request->amount;
        $user->balance += $amount;
        $user->save();

        // Create transaction record
        Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'credit',
            'status' => 'completed',
            'payment_method' => 'admin',
            'description' => $request->description ?? 'Balance added by admin',
        ]);

        return response()->json([
            'message' => 'Balance added successfully',
            'new_balance' => $user->balance,
        ]);
    }
}
