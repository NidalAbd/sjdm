<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserStatusChangedNotification extends Notification
{
    use Queueable;

    protected $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Your account status has changed to ' . $this->status . '.')
            ->action('View Profile', url('/profile'))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'status' => $this->status,
            'message' => 'Your account status has changed to ' . $this->status . '.',
        ];
    }
}
