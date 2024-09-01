<?php

namespace App\Notifications;


namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionNotification extends Notification
{
    use Queueable;

    protected $transaction;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Ensure 'mail' and 'database' channels are set
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Transaction Notification')
            ->line('Your transaction has been processed.')
            ->action('View Transaction', url('/transactions/' . $this->transaction->id))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->amount,
            'status' => $this->transaction->status,
        ];
    }
}
