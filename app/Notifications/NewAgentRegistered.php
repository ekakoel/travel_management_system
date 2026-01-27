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

    protected $agent_id;

    public function __construct($agent_id)
    {
        $this->agent_id = $agent_id;
    }

    public function via($notifiable)
    {
        return ['database']; // Simpan di DB
    }

    public function toDatabase($notifiable)
    {
        $agent = Agent::find($this->agent_id);
        $agent_id = $this->agent_id;
        return [
            'title' => 'New agent registration',
            'message' => 'Agent "' . $agent->company_name . '" has submitted registration.',
            'agent_id' => $agent->id,
            'url' => route('admin.agents.show', $agent->id), // sesuaikan URL
        ];
    }
}