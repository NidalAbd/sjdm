<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class UserRestoredNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Welcome Back! Reset Your Password')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Welcome back to ' . config('app.name') . '! Your account has been restored.')
            ->line('Please set a new password to reactivate your account.')
            ->action('Reset Password', url(route('password.reset', $this->token, false)))
            ->line('Thank you for being with us!');
    }
}
