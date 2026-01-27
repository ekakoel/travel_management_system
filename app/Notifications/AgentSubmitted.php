<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgentSubmitted extends Notification
{
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Agent Registration',
            'message' => 'A new travel agent has submitted their registration.',
            'agent_id' => $this->agent->id,
            'url' => route('admin.agents.index'), // sesuaikan URL
        ];
    }
}
