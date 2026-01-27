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

    protected $agent_id;

    public function __construct($agent_id)
    {
        $this->agent_id = $agent_id;
    }


    /**
     * Build the message.
     */
    public function build()
    {
        $agent = Agent::find($this->agent_id);
        return $this->subject('New Agent Registration - Bali Kami Tour')
        ->view('emails.agents.registered',[
            'agent' => $agent,
        ]);
    }
}
