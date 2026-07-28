<?php

namespace App\Console\Commands;

use App\Services\TourOrderLifecycleService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CompleteEligibleTourOrders extends Command
{
    protected $signature = 'tour:complete-eligible-orders {--owner=}';

    protected $description = 'Complete paid public Tour Package orders after tour checkout has passed.';

    public function handle(TourOrderLifecycleService $lifecycle): int
    {
        $ownerId = $this->option('owner') ? (int) $this->option('owner') : null;
        $completed = $lifecycle->completeEligiblePaidOrders($ownerId, Carbon::now());

        $this->line("Completed {$completed} eligible public Tour Package order(s).");

        return self::SUCCESS;
    }
}
