<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderLifecycleNotification extends Notification
{
    use Queueable;

    public function __construct(private array $payload)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return $this->payload;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject($this->payload['title'])
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line($this->payload['message'])
            ->line(__('Order reference: :reference', ['reference' => $this->payload['order_number']]))
            ->action(__('View your orders'), route('view.orders'))
            ->salutation(__('Regards, Bali Kami Tour'));
    }
}
