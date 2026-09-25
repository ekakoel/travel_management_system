<?php

namespace App\Mail;

use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Agent $agent,
        public string $email,
        public string $password,
    ) {
    }

    public function build()
    {
        return $this
            ->subject('Your Bali Kami Partner Account Has Been Verified')
            ->view('emails.agents.verified');
    }
}