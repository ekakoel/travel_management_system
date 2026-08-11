<?php

namespace App\Jobs;

use App\Services\Pricing\CurrencyRateRefreshService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateCurrencyRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(CurrencyRateRefreshService $rates): void
    {
        $rates->refresh();
    }
}
