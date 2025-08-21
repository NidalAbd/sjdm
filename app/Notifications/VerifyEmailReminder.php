<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailReminder extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $platformDetails = "Our platform helps you grow your social media presence on various platforms such as Facebook, Instagram, TikTok, and more. Whether you're looking to boost followers, increase engagement, or grow your brand, we have tailored services to meet your needs.";

        return (new MailMessage)
            ->subject('Verify Your Email to Boost Your Followers')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Thank you for joining our platform!')
            ->line('We noticed that you haven\'t verified your email yet. By verifying your email, you can unlock the full potential of our platform and start increasing your followers across popular social media channels.')
            ->line($platformDetails)
            ->action('Verify Your Email Now', url('/email/verify'))
            ->line('Don\'t miss out on enhancing your social media presence. Complete your registration today and start growing your followers!')
            ->line('If you have any questions or need assistance, feel free to reach out to our support team.')
            ->line('Thank you for choosing our platform to boost your followers and take your social media to the next level!');
    }

}
