<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TicketNotification extends Notification
{
    use Queueable;

    protected $ticket;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\SupportTicket  $ticket
     * @return void
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Support Ticket Created')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your support ticket has been created successfully.')
            ->line('Ticket Subject: ' . $this->ticket->subject)
            ->line('Message: ' . $this->ticket->message)
            ->action('View Ticket', url('/tickets/' . $this->ticket->id))
            ->line('Thank you for reaching out!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'Your ticket "' . $this->ticket->subject . '" has been created successfully.',
            'ticket_id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
            'status' => 'Open',
        ];
    }
}
