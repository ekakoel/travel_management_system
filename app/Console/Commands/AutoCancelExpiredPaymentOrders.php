<?php

namespace App\Console\Commands;

use App\Services\Orders\OrderPaymentDeadlineService;
use Illuminate\Console\Command;

class AutoCancelExpiredPaymentOrders extends Command
{
    protected $signature = 'orders:auto-cancel-expired-payments';

    protected $description = 'Cancel approved orders whose 48-hour payment deadline expired without a payment submission.';

    public function handle(OrderPaymentDeadlineService $deadlines): int
    {
        $canceled = $deadlines->cancelExpiredOrders();

        $this->info("Canceled {$canceled} expired unpaid order(s).");

        return self::SUCCESS;
    }
}
