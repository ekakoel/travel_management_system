<?php

namespace App\Events;

use App\Models\UsdRates;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CurrencyUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $currency;

    public function __construct(UsdRates $currency)
    {
        $this->currency = $currency;
    }

    public function broadcastOn()
    {
        return new Channel("currency-updates");
    }

    public function broadcastAs()
    {
        return "CurrencyUpdated";
    }
}
