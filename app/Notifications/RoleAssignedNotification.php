<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoleAssignedNotification extends Notification
{
    use Queueable;

    protected $roles;

    public function __construct($roles)
    {
        // Ensure roles is a collection
        $this->roles = collect($roles);
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $roleNames = $this->roles->pluck('name')->toArray();
        $roleList = implode(', ', $roleNames);

        return (new MailMessage)
            ->line('New roles have been assigned to you: ' . $roleList . '.')
            ->action('View Profile', url('/profile'))
            ->line('Thank you for using our application!');
    }

}
