<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view_any_user');
    }

    public function add_balance(User $authUser, User $user)
    {
        return $authUser->hasRole('admin'); // Only admins can add balance
    }

    public function view(User $user, User $model)
    {
        return $user->hasPermissionTo('view_user');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('create_user');
    }

    public function update(User $user, User $model)
    {
        return $user->hasPermissionTo('update_user');
    }

    public function delete(User $user, User $model)
    {
        return $user->hasPermissionTo('delete_user');
    }

    public function restore(User $user, User $model)
    {
        return $user->hasPermissionTo('restore_user');
    }

    public function forceDelete(User $user, User $model)
    {
        return $user->hasPermissionTo('force_delete_user');
    }

    public function assignRole(User $user)
    {
        return $user->hasPermissionTo('assign_role');
    }

    public function assignPermission(User $user)
    {
        return $user->hasPermissionTo('assign_permission');
    }

}
