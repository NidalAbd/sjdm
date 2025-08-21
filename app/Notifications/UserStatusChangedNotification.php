<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserStatusChangedNotification extends Notification
{
    use Queueable;

    public $status;

    /**
     * Create a new notification instance.
     *
     * @param string $status
     */
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // Ensure this matches your setup (e.g., 'mail', 'database', 'slack')
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Status Has Changed')
            ->line("Your account status has been changed to: {$this->status}.")
            ->action('View Profile', url('/profile'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Status Updated',
            'message' => "Your account status has been changed to: {$this->status}.",
            'action_url' => url('/profile'),
            'icon' => 'fas fa-user-check', // Add an icon for consistency
        ];
    }
}
