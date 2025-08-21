<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class TransactionReminderNotification extends Notification
{
    use Queueable;

    protected $transaction;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Create the MailMessage instance
        $mailMessage = (new MailMessage)
            ->subject('Reminder: Complete Your Transaction')
            ->line('We noticed that you haven’t completed your transaction of $' . number_format($this->transaction->amount, 2) . '.')
            ->action('Complete Transaction', url('/transactions/complete/' . $this->transaction->id))
            ->line('Please complete your transaction as soon as possible.');

        // Send the email and modify headers using the 'message' method
        Mail::mailer('smtp')->send($mailMessage, [], function ($message) {
            $message->getHeaders()->addTextHeader('X-Priority', '3');
            $message->getHeaders()->addTextHeader('X-MSMail-Priority', 'Normal');
            $message->getHeaders()->addTextHeader('X-Mailer', 'SJDM');
            $message->getHeaders()->addTextHeader('List-Unsubscribe', '<mailto:info@sjdm.store?subject=unsubscribe>');
            $message->getHeaders()->addTextHeader('X-Complaints-To', 'support@sjdm.store');
        });

        return $mailMessage;
    }

    public function toArray($notifiable)
    {
        return [
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->amount,
        ];
    }
}
