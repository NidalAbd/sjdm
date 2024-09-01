<?php

namespace Database\Factories;

use App\Models\SupportTicket;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\TicketStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    public function definition()
    {
        // Define possible types and subtypes for the support tickets
        $types = ['order', 'transaction'];
        $subtypes = [
            'order' => ['refund', 'acceleration', 'cancel'],
            'transaction' => ['failed_payment', 'refund_request', 'payment_dispute', 'chargeback', 'invoice_request']
        ];

        // Randomly pick a type
        $type = $this->faker->randomElement($types);
        // Randomly pick a subtype based on the type
        $subtype = $this->faker->randomElement($subtypes[$type]);

        // Determine the model type and ID based on the ticket type
        if ($type === 'order') {
            $ticketable = Order::factory()->create();
        } else {
            $ticketable = Transaction::factory()->create();
        }

        return [
            'user_id' => User::factory(), // Ensure this generates a user
            'ticketable_id' => $ticketable->id, // Polymorphic ID
            'ticketable_type' => get_class($ticketable), // Polymorphic type
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'status_id' => TicketStatus::where('name', 'Open')->first()->id, // Default status as 'Open'
            'type' => $type,
            'subtype' => $subtype,
        ];
    }
}
