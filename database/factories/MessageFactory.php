<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition()
    {
        // Fetch a random support ticket or create one if none exist
        $supportTicket = SupportTicket::inRandomOrder()->first() ?? SupportTicket::factory()->create();

        // Fetch a random user or use the ticket's user
        $user = User::inRandomOrder()->first() ?? $supportTicket->user;

        return [
            'support_ticket_id' => $supportTicket->id, // Associate with a support ticket
            'user_id' => $user->id, // Associate with a user
            'message' => $this->faker->paragraph, // Generate a random message
        ];
    }
}
