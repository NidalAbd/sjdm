<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'admin' => Permission::all(),

            'moderator' => Permission::whereIn('name', [
                'view_any_role', 'view_role', 'create_role', 'update_role', 'delete_role',
                'view_any_permission', 'view_permission', 'create_permission', 'update_permission', 'delete_permission',
                'assign_role',
            ])->pluck('id'),

            'client' => Permission::whereNotIn('name', [
                'view_any_user', 'view_user', 'create_user', 'update_user', 'delete_user',
                'restore_user', 'force_delete_user', 'assign_role', 'assign_permission',
                'view_any_role', 'view_role', 'create_role', 'update_role', 'delete_role',
                'restore_role', 'force_delete_role', 'view_any_permission', 'view_permission',
                'create_permission', 'update_permission', 'delete_permission', 'restore_permission',
                'force_delete_permission'
            ])->pluck('id'),

            'guest' => Permission::whereIn('name', [
                'view_any_user', 'view_user', 'view_profile',
            ])->pluck('id'),
        ];



        // Create roles and attach permissions
        foreach ($roles as $roleName => $permissions) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
        }
    }
}
