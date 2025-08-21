<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view_any_role');
    }

    public function view(User $user, Role $role)
    {
        return $user->hasPermissionTo('view_role');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create_role');
    }

    public function update(User $user, Role $role)
    {
        return $user->hasPermissionTo('update_role');
    }

    public function delete(User $user, Role $role)
    {
        return $user->hasPermissionTo('delete_role');
    }

    public function restore(User $user, Role $role)
    {
        return $user->hasPermissionTo('restore_role');
    }

    public function forceDelete(User $user, Role $role)
    {
        return $user->hasPermissionTo('force_delete_role');
    }
}
