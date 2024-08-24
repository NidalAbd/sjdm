<?php

namespace Database\Factories;

use App\Models\SupportTicket;
use App\Models\Order;
use App\Models\User;
use App\Models\TicketStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    public function definition()
    {
        // Define possible types and subtypes for the support tickets
        $types = ['order', 'payment'];
        $subtypes = [
            'order' => ['refund', 'acceleration', 'cancel'],
            'payment' => ['failed_payment', 'refund_request', 'payment_dispute', 'chargeback', 'invoice_request']
        ];

        // Randomly pick a type
        $type = $this->faker->randomElement($types);
        // Randomly pick a subtype based on the type
        $subtype = $this->faker->randomElement($subtypes[$type]);

        return [
            'user_id' => User::factory(), // Ensure this generates a user
            'order_id' => Order::factory(), // Ensure this generates an order
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'status_id' => TicketStatus::where('name', 'Open')->first()->id, // Default status as 'Open'
            'type' => $type,
            'subtype' => $subtype,
        ];
    }
}

