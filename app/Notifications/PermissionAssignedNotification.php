<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PermissionAssignedNotification extends Notification
{
    use Queueable;

    protected $permissions;

    public function __construct($permissions)
    {
        $this->permissions = $permissions;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $permissionNames = $this->permissions->pluck('name')->toArray();
        $permissionList = implode(', ', $permissionNames);

        return (new MailMessage)
            ->line('New permissions have been assigned to you: ' . $permissionList . '.')
            ->action('View Profile', url('/profile'))
            ->line('Thank you for using our application!');
    }


}

