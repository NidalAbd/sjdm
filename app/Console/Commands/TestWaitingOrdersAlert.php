<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestWaitingOrdersAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:waiting-orders-alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the waiting orders alert functionality';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing waiting orders alert functionality...');

        // Check waiting orders count
        $waitingOrdersCount = \App\Models\Order::where('status', 'waiting')->count();
        $this->info("Waiting orders count: {$waitingOrdersCount}");

        if ($waitingOrdersCount > 0) {
            $this->warn("Found {$waitingOrdersCount} waiting orders!");
            
            // Simulate admin user for testing
            $adminUser = \App\Models\User::whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })->first();
            
            if ($adminUser) {
                auth()->login($adminUser);
                $this->info("Logged in as admin user: {$adminUser->name}");
                
                // Test the alert function
                $alert = checkWaitingOrdersAlert();
                
                if ($alert) {
                    $this->info("Alert would be shown:");
                    $this->info("- Type: {$alert['type']}");
                    $this->info("- Title: {$alert['title']}");
                    $this->info("- Message: {$alert['message']}");
                    
                    if (isset($alert['api_balance'])) {
                        $this->info("- API Balance: $" . number_format($alert['api_balance'], 2));
                    }
                } else {
                    $this->error("Alert function still returned null");
                }
            } else {
                $this->error("No admin user found in database");
            }
        } else {
            $this->info("No waiting orders found. The alert would not be shown.");
        }

        return 0;
    }
} 