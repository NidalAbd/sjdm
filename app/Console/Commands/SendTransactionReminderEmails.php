<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\TransactionReminderNotification;

class SendTransactionReminderEmails extends Command
{
    protected $signature = 'transaction:send-reminder';
    protected $description = 'Send reminder emails for incomplete transactions';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Find transactions that are not completed
        $transactions = Transaction::where('status', '!=', 'completed')->get();

        foreach ($transactions as $transaction) {
            $user = $transaction->user;

            // Send email notification to user
            $user->notify(new TransactionReminderNotification($transaction));
        }

        $this->info('Reminder emails sent for incomplete transactions.');
    }
}
