<?php

namespace App\Console\Commands;

use App\Models\TicketStatus;
use Illuminate\Console\Command;

class SeedTicketStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:seed-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed default ticket statuses';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Seeding ticket statuses...');

        $defaultStatuses = ['Open', 'In Progress', 'Closed', 'Resolved'];
        
        foreach ($defaultStatuses as $statusName) {
            $status = TicketStatus::firstOrCreate(['name' => $statusName]);
            $this->line("Created/Found status: {$status->name}");
        }

        $this->info('Ticket statuses seeded successfully!');
        
        return Command::SUCCESS;
    }
} 