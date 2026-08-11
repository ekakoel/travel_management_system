<?php

namespace App\Console\Commands;

use App\Services\Activities\ActivityValidityService;
use Illuminate\Console\Command;

class DraftExpiredActivities extends Command
{
    protected $signature = 'activities:draft-expired';

    protected $description = 'Move expired active Activities to Draft status.';

    public function handle(ActivityValidityService $validity): int
    {
        $drafted = $validity->draftExpired();

        $this->info("Moved {$drafted} expired Activity record(s) to Draft.");

        return self::SUCCESS;
    }
}
