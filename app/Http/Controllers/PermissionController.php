<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Permission::class, 'permission');
    }
    public function index(Request $request)
    {
        // Fetch permissions, with optional search functionality
        $search = $request->input('search');
        $permissions = Permission::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        })
            ->orderBy('created_at', 'asc')
            ->paginate(5);

        return view('users.permissions.index', compact('permissions', 'search'));
    }

    public function create()
    {
        return view('users.permissions.create');
    }

    public function store(Request $request)
    {
        Permission::create($request->validate([
            'name' => 'required|string|max:255',
        ]));

        return redirect()->route('users.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function show(Permission $permission)
    {
        return view('users.permissions.show', compact('permission'));
    }

    public function edit(Permission $permission)
    {
        return view('users.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $permission->update($request->validate([
            'name' => 'required|string|max:255',
        ]));

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
