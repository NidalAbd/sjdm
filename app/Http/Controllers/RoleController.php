<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $roles = Role::with('permissions')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'asc')
            ->paginate(7);

        return view('users.roles.index', compact('roles', 'search'));
    }

    public function create()
    {
        // Group permissions by their action (create, edit, etc.) and also 'view_any'
        $permissions = Permission::all()->groupBy(function($item) {
            if (strpos($item->name, 'view_any') !== false) {
                return 'view_any';
            } elseif (strpos($item->name, 'create') !== false) {
                return 'create';
            } elseif (strpos($item->name, 'edit') !== false) {
                return 'edit';
            } elseif (strpos($item->name, 'delete') !== false) {
                return 'delete';
            } elseif (strpos($item->name, 'view') !== false) {
                return 'view';
            } else {
                return 'other';
            }
        });

        return view('users.roles.create', compact('permissions'));
    }


    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->validated());

        // Get permission names from their IDs
        $permissionNames = Permission::whereIn('id', $request->input('permissions', []))->pluck('name');

        $role->syncPermissions($permissionNames);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        return view('users.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all();
        return view('users.roles.edit', compact('role', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->validated());

        // Get permission names from their IDs
        $permissionNames = Permission::whereIn('id', $request->input('permissions', []))->pluck('name');

        $role->syncPermissions($permissionNames);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        // Detach all permissions from the role
        $role->syncPermissions([]);
        // Delete the role
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted and permissions unsynced successfully.');
    }

}
