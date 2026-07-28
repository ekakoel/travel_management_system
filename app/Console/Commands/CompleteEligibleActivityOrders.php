<?php

namespace App\Console\Commands;

use App\Services\ActivityOrderLifecycleService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CompleteEligibleActivityOrders extends Command
{
    protected $signature = 'activity:complete-eligible-orders {--owner=}';

    protected $description = 'Complete paid public Activity orders after the activity checkout time has passed.';

    public function handle(ActivityOrderLifecycleService $lifecycle): int
    {
        $ownerId = $this->option('owner') ? (int) $this->option('owner') : null;
        $completed = $lifecycle->completeEligiblePaidOrders($ownerId, Carbon::now());

        $this->info("Completed {$completed} eligible public Activity order(s).");

        return self::SUCCESS;
    }
}
