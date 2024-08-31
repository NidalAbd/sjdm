<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
        ]);

        $this->call(UserSeeder::class);

        $this->call(OrderSeeder::class);
        $this->call(TicketStatusSeeder::class);

        $this->call(SupportTicketSeeder::class);
    }
}
