<?php

namespace App\Mail;

use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentRegistered extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $agent;

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }


    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject(__('agent-registration.mail.admin_subject'))
        ->view('emails.agents.registered',[
            'agent' => $this->agent,
        ]);
    }
}
