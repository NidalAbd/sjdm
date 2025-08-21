<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;
use App\Models\SupportTicket;

class MessageSeeder extends Seeder
{
    public function run()
    {
        // Fetch all support tickets to associate messages with them
        $supportTickets = SupportTicket::all();

        foreach ($supportTickets as $ticket) {
            // Generate a random number of messages for each ticket
            Message::factory()->count(rand(1, 5))->create([
                'support_ticket_id' => $ticket->id, // Associate messages with the current ticket
                'user_id' => $ticket->user_id, // Use the ticket's user for the message
            ]);
        }
    }
}
