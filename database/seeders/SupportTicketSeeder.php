<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupportTicket;
use App\Models\Order;
use App\Models\User;
use App\Models\TicketStatus;

class SupportTicketSeeder extends Seeder
{
    public function run()
    {
        // Fetch all users and orders to associate with support tickets
        $users = User::all();
        $orders = Order::all();
        $statuses = TicketStatus::pluck('id', 'name');

        // Define possible types and subtypes for the support tickets
        $types = ['order', 'payment'];
        $subtypes = [
            'order' => ['refund', 'acceleration', 'cancel'],
            'payment' => ['failed_payment', 'refund_request', 'payment_dispute', 'chargeback', 'invoice_request']
        ];

        // Create support tickets
        foreach ($users as $user) {
            // Assign tickets to random users
            foreach ($orders as $order) {
                // Randomly pick a type
                $type = $types[array_rand($types)];
                // Randomly pick a subtype based on the type
                $subtype = $subtypes[$type][array_rand($subtypes[$type])];

                // Create a support ticket
                SupportTicket::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'subject' => 'Support Ticket for ' . ucfirst($type),
                    'message' => 'This is a sample message for ' . ucfirst($type) . ' support.',
                    'status_id' => $statuses['Open'], // Default status as 'Open'
                    'type' => $type,
                    'subtype' => $subtype,
                ]);
            }
        }
    }
}

