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
            ->subject($this->getSubject())
            ->line($this->getStatusMessage())
            ->action('View Transaction', url('/transactions/' . $this->transaction->id))
            ->line('Thank you for using our application! If you have any questions, feel free to contact our support team.');
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
            'message' => $this->getStatusMessage(),
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->amount,
            'status' => $this->transaction->status,
        ];
    }

    /**
     * Get the email subject line based on the transaction status.
     *
     * @return string
     */
    private function getSubject(): string
    {
        switch ($this->transaction->status) {
            case 'completed':
                return 'Your Transaction was Completed Successfully';
            case 'refunded':
                return 'Your Transaction was Refunded';
            case 'pending':
                return 'Your Transaction is Pending';
            case 'failed':
                return 'Your Transaction Failed';
            case 'canceled':
                return 'Your Transaction was Canceled';
            case 'processing':
                return 'Your Transaction is Processing';
            default:
                return 'Transaction Update';
        }
    }

    /**
     * Get the appropriate message based on the transaction status.
     *
     * @return string
     */
    private function getStatusMessage(): string
    {
        $amount = number_format($this->transaction->amount, 2);

        switch ($this->transaction->status) {
            case 'completed':
                return "Your transaction of $$amount has been successfully completed. Thank you for your purchase!";
            case 'refunded':
                return "Your transaction of $$amount has been refunded successfully. If you need any further assistance, feel free to contact support.";
            case 'pending':
                return "Your transaction of $$amount is currently pending. We will notify you once it's processed.";
            case 'failed':
                return "Unfortunately, your transaction of $$amount has failed. Please verify your payment method and try again.";
            case 'canceled':
                return "Your transaction of $$amount has been canceled. No funds have been deducted. If this was unintentional, you may reattempt the transaction.";
            case 'processing':
                return "Your transaction of $$amount is currently being processed. We will notify you once it's completed.";
            default:
                return "The status of your transaction is currently unknown. Please contact support for further details.";
        }
    }
}
