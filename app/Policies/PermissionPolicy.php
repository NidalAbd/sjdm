<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view_any_permission');
    }

    public function view(User $user, Permission $permission)
    {
        return $user->hasPermissionTo('view_permission');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create_permission');
    }

    public function update(User $user, Permission $permission)
    {
        return $user->hasPermissionTo('update_permission');
    }

    public function delete(User $user, Permission $permission)
    {
        return $user->hasPermissionTo('delete_permission');
    }

    public function restore(User $user, Permission $permission)
    {
        return $user->hasPermissionTo('restore_permission');
    }

    public function forceDelete(User $user, Permission $permission)
    {
        return $user->hasPermissionTo('force_delete_permission');
    }
}
