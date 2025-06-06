<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

class OrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail']; // Using both database and email
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject('Order Created Successfully')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your order has been created successfully.')
            ->line('Order ID: ' . $this->order->api_order_id)
            ->line('Service: ' . $this->order->service->name)
            ->line('Quantity: ' . $this->order->quantity)
            ->line('Charge: $' . number_format($this->order->charge, 2))
            ->line('Status: ' . $this->order->status)
            ->action('View Order', url('/orders/' . $this->order->id))
            ->line('Thank you for using our service!');

        // Modify email headers via the Mail facade
        Mail::mailer('smtp')->send($mailMessage, [], function ($message) {
            $message->getHeaders()->addTextHeader('X-Priority', '3');
            $message->getHeaders()->addTextHeader('X-MSMail-Priority', 'Normal');
            $message->getHeaders()->addTextHeader('X-Mailer', 'SJDM');
            $message->getHeaders()->addTextHeader('List-Unsubscribe', '<mailto:info@sjdm.store?subject=unsubscribe>');
            $message->getHeaders()->addTextHeader('X-Complaints-To', 'support@sjdm.store');
        });

        return $mailMessage;
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
            'message' => 'Your order with ID ' . $this->order->api_order_id . ' has been created successfully.',
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'service_name' => $this->order->service->name,
            'quantity' => $this->order->quantity,
            'charge' => $this->order->charge,
        ];
    }
}
