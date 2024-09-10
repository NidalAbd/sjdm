<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PointsRedeemedNotification extends Notification
{
    use Queueable;

    protected $points;
    protected $amount;

    /**
     * Create a new notification instance.
     *
     * @param int $points
     * @param float $amount
     */
    public function __construct($points, $amount)
    {
        $this->points = $points;
        $this->amount = $amount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // Sends notification via both mail and database
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Points Redeemed Successfully')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have successfully redeemed ' . $this->points . ' points.')
            ->line('This equals $' . number_format($this->amount, 2) . ' added to your balance.')
            ->action('View Balance', url('/balance'))
            ->line('Thank you for using our application!');
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
            'points' => $this->points,
            'amount' => $this->amount,
            'message' => 'You have successfully redeemed ' . $this->points . ' points for $' . number_format($this->amount, 2),
        ];
    }
}
