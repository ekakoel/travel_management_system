<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Pricing\TourTaxPolicyActivationService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Throwable;

class ActivateInitialTourTaxPolicy extends Command
{
    protected $signature = 'pricing:activate-tour-tax
        {--effective-from= : Explicit ISO-8601 production activation timestamp}
        {--approved-by= : Existing approving user ID}
        {--name=Tour Package Tax 1.5% : Policy audit name}
        {--acknowledge-production : Required when APP_ENV is production}';

    protected $description = 'Create the initial approved, non-overlapping Tour Package 1.5% tax policy.';

    public function handle(TourTaxPolicyActivationService $activation): int
    {
        if (app()->environment('production') && !$this->option('acknowledge-production')) {
            $this->error('Production execution requires --acknowledge-production.');

            return self::FAILURE;
        }

        $effectiveFromInput = trim((string) $this->option('effective-from'));
        $approvedBy = filter_var($this->option('approved-by'), FILTER_VALIDATE_INT);

        if ($effectiveFromInput === '' || !$approvedBy || !User::query()->whereKey($approvedBy)->exists()) {
            $this->error('--effective-from and an existing --approved-by user are required.');

            return self::INVALID;
        }

        try {
            $effectiveFrom = CarbonImmutable::parse($effectiveFromInput);
            $policy = $activation->activateInitialPolicy(
                $effectiveFrom,
                (int) $approvedBy,
                (string) $this->option('name')
            );
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Tour Package tax policy {$policy->id} created.");

        return self::SUCCESS;
    }
}
