<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Dashboard Permissions
            'view_dashboard',

            // User Permissions
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
            'restore_user',
            'force_delete_user',
            'assign_role',
            'assign_permission',
            'view_profile',
            'change_password',
            'add_balance',  // Relevant to the Balance menu
            'view_notifications',

            // Role Permissions
            'view_any_role',
            'view_role',
            'create_role',
            'update_role',
            'delete_role',
            'restore_role',
            'force_delete_role',

            // Permission Permissions
            'view_any_permission',
            'view_permission',
            'create_permission',
            'update_permission',
            'delete_permission',
            'restore_permission',
            'force_delete_permission',

            // Orders Permissions
            'create_order',
            'view_orders',

            // Balance Permissions
            'view_balance',
            'view_transactions',

            // Settings Permissions
            'view_settings',
            'update_settings',

            // Language and Currency Permissions
            'change_language',  // Assuming there's a control to change languages
            'change_currency',  // Assuming there's a control to change currency

            // Support Permissions
            'view_support',

            // Service Permissions
            'view_services',
        ];



        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
