<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileUpdatedNotification extends Notification
{
    use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Add 'database' to store in database
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Your profile has been updated.')
            ->action('View Profile', url('/profile'))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Profile Updated',
            'message' => 'Your profile has been successfully updated.',
            'action_url' => url('/profile'),
            'icon' => 'fas fa-user-edit', // Add an icon for consistency
        ];
    }
}
