<?php

namespace App\Console\Commands;

use App\Exceptions\CurrencyRateRefreshException;
use App\Services\Pricing\CurrencyRateRefreshService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class RefreshCurrencyRates extends Command
{
    protected $signature = 'currency:refresh-rates';

    protected $description = 'Refresh USD, CNY, and TWD reference rates against IDR.';

    public function handle(CurrencyRateRefreshService $rates): int
    {
        try {
            $result = $rates->refresh();
        } catch (CurrencyRateRefreshException $exception) {
            Log::warning('Scheduled currency refresh failed.', [
                'reason' => $exception->getMessage(),
            ]);
            $this->error($exception->getMessage());

            return self::FAILURE;
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Currency refresh failed unexpectedly.');

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Currency rates refreshed at %s (%s).',
            $result['retrieved_at']->format('Y-m-d H:i:s'),
            $result['source'],
        ));

        return self::SUCCESS;
    }
}
