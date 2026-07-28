<?php

namespace App\Console\Commands;

use App\Services\TransportOrderLifecycleService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CompleteEligibleTransportOrders extends Command
{
    protected $signature = 'transport:complete-eligible-orders {--owner= : Limit completion to one sales_agent user id}';

    protected $description = 'Complete paid public Transport orders after service checkout has passed.';

    public function handle(TransportOrderLifecycleService $lifecycle): int
    {
        $owner = $this->option('owner');
        $ownerId = is_numeric($owner) ? (int) $owner : null;
        $completed = $lifecycle->completeEligiblePaidOrders($ownerId, Carbon::now());

        $this->info("Completed {$completed} eligible public Transport order(s).");

        return self::SUCCESS;
    }
}
