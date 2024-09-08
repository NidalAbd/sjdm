<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionNotification extends Notification
{
    use Queueable;

    protected $transaction;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function __construct($transaction)
    {
        $this->transaction = $transaction;
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
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Transaction Notification')
            ->line('Your transaction of $' . number_format($this->transaction->amount, 2) . ' has been processed.')
            ->line('Transaction Status: ' . ucfirst($this->transaction->status))
            ->action('View Transaction', url('/transactions/' . $this->transaction->id))
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
            'message' => $this->transaction->status === 'completed'
                ? 'Your transaction of $' . number_format($this->transaction->amount, 2) . ' has been completed successfully.'
                : 'Your transaction of $' . number_format($this->transaction->amount, 2) . ' has failed.',
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->amount,
            'status' => $this->transaction->status,
        ];
    }

}
