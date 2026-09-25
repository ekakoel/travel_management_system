<?php

namespace App\Mail;

use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentConfirmation extends Mailable implements ShouldQueue
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
        return $this->subject(__('agent-registration.mail.receipt_subject'))
        ->view('emails.agents.confirmation',[
            'agent' => $this->agent,
        ]);
    }
}
