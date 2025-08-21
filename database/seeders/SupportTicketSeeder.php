<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupportTicket;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\TicketStatus;

class SupportTicketSeeder extends Seeder
{
    public function run()
    {
        // Fetch all users to associate with support tickets
        $users = User::all();
        $statuses = TicketStatus::pluck('id', 'name');

        // Define possible types and subtypes for the support tickets
        $types = ['order', 'payment'];
        $subtypes = [
            'order' => ['refund', 'acceleration', 'cancel'],
            'payment' => ['failed_payment', 'refund_request', 'payment_dispute', 'chargeback', 'invoice_request']
        ];

        // Create support tickets
        foreach ($users as $user) {
            // Randomly select whether to create a support ticket for an order or a payment
            $type = $types[array_rand($types)];

            if ($type === 'order') {
                // Fetch a random order to associate with the support ticket
                $order = Order::inRandomOrder()->first();

                // If an order exists, create a support ticket for it
                if ($order) {
                    SupportTicket::create([
                        'user_id' => $user->id,
                        'ticketable_id' => $order->id,
                        'ticketable_type' => Order::class,
                        'subject' => 'Support Ticket for Order',
                        'message' => 'This is a sample message for order support.',
                        'status_id' => $statuses['Open'], // Default status as 'Open'
                        'type' => $type,
                        'subtype' => $subtypes[$type][array_rand($subtypes[$type])],
                    ]);
                }
            } else {
                // Fetch a random transaction to associate with the support ticket
                $transaction = Transaction::inRandomOrder()->first();

                // If a transaction exists, create a support ticket for it
                if ($transaction) {
                    SupportTicket::create([
                        'user_id' => $user->id,
                        'ticketable_id' => $transaction->id,
                        'ticketable_type' => Transaction::class,
                        'subject' => 'Support Ticket for Payment',
                        'message' => 'This is a sample message for payment support.',
                        'status_id' => $statuses['Open'], // Default status as 'Open'
                        'type' => $type,
                        'subtype' => $subtypes[$type][array_rand($subtypes[$type])],
                    ]);
                }
            }
        }
    }
}
