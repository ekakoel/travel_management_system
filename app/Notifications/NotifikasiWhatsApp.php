<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifikasiWhatsApp extends Notification
{
    use Queueable;

    private $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['nexmo'];
    }

    public function toNexmo($notifiable)
    {
        $order = 'Anda memiliki pesanan baru dengan detail berikut:' . PHP_EOL;
        $order .= 'Nomor Pesanan: ' . $this->order->orderno . PHP_EOL;
        $order .= 'Total Pesanan: ' . $this->order->number_of_room . PHP_EOL;

        return (new NexmoMessage())
            ->content($order);
    }
}
