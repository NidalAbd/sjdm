<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\VerifyEmailReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyUnverifiedUsers extends Command
{
    protected $signature = 'notify:unverified-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to users who have registered but not verified their email';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Fetch users who have not verified their email
        $unverifiedUsers = User::whereNull('email_verified_at')
            ->whereNotNull('created_at') // Ensure they have at least registered
            ->get();

        foreach ($unverifiedUsers as $user) {
            try {
                // Send notification to remind them to verify their email
                $user->notify(new VerifyEmailReminder());
                Log::info('Verification reminder sent to user: ' . $user->email);
            } catch (\Exception $e) {
                Log::error('Error sending notification to ' . $user->email . ': ' . $e->getMessage());
            }
        }

        return 0;
    }
}
