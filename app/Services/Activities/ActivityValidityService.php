<?php

namespace App\Services\Activities;

use App\Models\Activities;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class ActivityValidityService
{
    public function draftExpired(?CarbonInterface $asOf = null): int
    {
        $asOf ??= now();

        return DB::transaction(function () use ($asOf): int {
            return Activities::query()
                ->where('status', 'Active')
                ->whereNotNull('validity')
                ->whereDate('validity', '<', $asOf->toDateString())
                ->update([
                    'status' => 'Draft',
                    'updated_at' => now(),
                ]);
        });
    }
}
