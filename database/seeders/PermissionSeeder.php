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

            // User Permissions (CRUD + Extras)
            'view_any_user',       // Read all users
            'view_user',           // Read a single user
            'create_user',         // Create a new user
            'update_user',         // Update a user
            'delete_user',         // Soft delete a user
            'restore_user',        // Restore a soft-deleted user
            'force_delete_user',   // Permanently delete a user
            'assign_role',         // Assign roles to a user
            'assign_permission',   // Assign permissions to a user
            'view_profile',        // View own profile
            'change_password',     // Change user password
            'add_balance',         // Add balance to user's account
            'view_notifications',  // View notifications

            // Role Permissions (CRUD)
            'view_any_role',       // Read all roles
            'view_role',           // Read a single role
            'create_role',         // Create a new role
            'update_role',         // Update a role
            'delete_role',         // Soft delete a role
            'restore_role',        // Restore a soft-deleted role
            'force_delete_role',   // Permanently delete a role

            // Permission Permissions (CRUD)
            'view_any_permission', // Read all permissions
            'view_permission',     // Read a single permission
            'create_permission',   // Create a new permission
            'update_permission',   // Update a permission
            'delete_permission',   // Soft delete a permission
            'restore_permission',  // Restore a soft-deleted permission
            'force_delete_permission', // Permanently delete a permission

            // Order Permissions (CRUD)
            'create_order',        // Create a new order
            'view_orders',         // View all orders
            'view_order',
            // Balance Permissions (CRUD)
            'view_balance',        // View balance
            'view_transactions',   // View transactions

            // Settings Permissions (CRUD)
            'view_settings',       // View settings
            'update_settings',     // Update settings

            // Language and Currency Permissions
            'change_language',     // Change language setting
            'change_currency',     // Change currency setting

            // Support Permissions (CRUD)
            'view_support',        // View support

            // Service Permissions (CRUD)
            'view_anyServices_services',       // View all services
            'view_service',       // View service

            'fetch_services',      // Fetch services from an external source

            // Support Ticket Permissions (CRUD)
            'view_any_ticket',     // View all support tickets
            'view_ticket',         // View a single support ticket
            'create_ticket',       // Create a new support ticket
            'update_ticket',       // Update a support ticket
            'delete_ticket',       // Delete a support ticket
            'manage_ticket_status',// Manage support ticket statuses

            // Transaction Permissions (CRUD)
            'view_any_transaction',    // View all transactions
            'view_transaction',        // View a single transaction
            'create_transaction',      // Create a new transaction
            'update_transaction',      // Update a transaction
            'delete_transaction',      // Delete a transaction
            'manage_transaction',      // Manage transactions, possibly involving special actions like refunds or manual balance updates
        ];




        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
