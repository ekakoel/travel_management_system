<?php

namespace App\Console\Commands;

use App\Services\AccommodationOrderLifecycleService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CompleteEligibleAccommodationOrders extends Command
{
    protected $signature = 'accommodation:complete-eligible-orders {--owner= : Limit completion to one sales_agent user id}';

    protected $description = 'Complete paid Accommodation orders after checkout has passed.';

    public function handle(AccommodationOrderLifecycleService $lifecycle): int
    {
        $owner = $this->option('owner');
        $ownerId = is_numeric($owner) ? (int) $owner : null;
        $completed = $lifecycle->completeEligiblePaidOrders($ownerId, Carbon::now());

        $this->info("Completed {$completed} eligible Accommodation order(s).");

        return self::SUCCESS;
    }
}
