<?php

namespace Database\Seeders;

use App\Models\TicketStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define statuses
        $statuses = ['Open', 'In Progress', 'Closed', 'Resolved'];

        // Create each status
        foreach ($statuses as $status) {
            TicketStatus::create(['name' => $status]);
        }
    }
}
