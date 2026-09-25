<?php

namespace App\Notifications;

use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewAgentRegistered extends Notification
{
    use Queueable;

    protected $agent;

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    public function via($notifiable)
    {
        return ['database']; // Simpan di DB
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => __('agent-registration.notification.title'),
            'message' => __('agent-registration.notification.message', ['company' => $this->agent->company_name]),
            'agent_id' => $this->agent->id,
            'url' => route('admin.agents.show', $this->agent->id),
        ];
    }
}
