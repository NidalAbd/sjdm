<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $action;

    public function __construct($user, $action)
    {
        $this->user = $user;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('A user-related action has occurred: ' . $this->action)
            ->action('View User', url('/users/'.$this->user->id))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'action' => $this->action,
            'message' => 'User action: ' . $this->action,
        ];
    }
}
