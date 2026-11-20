<?php

namespace App\Console\Commands;

use App\Services\Payments\DokuPaymentService;
use Illuminate\Console\Command;

class ReconcileDokuPayments extends Command
{
    protected $signature = 'doku:reconcile-pending {--limit=50}';

    protected $description = 'Reconcile pending DOKU payments with the DOKU status API';

    public function handle(DokuPaymentService $payments): int
    {
        $processed = $payments->reconcilePending(max(1, (int) $this->option('limit')));

        $this->info('Reconciled '.$processed.' DOKU payment(s).');

        return self::SUCCESS;
    }
}
