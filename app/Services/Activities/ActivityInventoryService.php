<?php

namespace App\Services\Activities;

use App\Models\Activities;
use App\Models\ActivityType;
use App\Models\BusinessProfile;
use App\Models\Partners;
use App\Models\Tax;
use App\Models\UsdRates;
use App\ViewModels\Activities\ActivityDetailViewModel;
use App\ViewModels\Activities\ActivityIndexViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ActivityInventoryService
{
    public function __construct(
        private readonly ActivityPricingService $pricingService,
    ) {
    }

    public function indexData(): array
    {
        $activities = Activities::with('partners')
            ->where('status', '!=', 'Removed')
            ->where('status', '!=', 'Archived')
            ->get();
        $activeActivities = Activities::where('status', '=', 'Active')->get();
        $archivedActivities = Activities::where('status', '=', 'Archived')->get();
        $draftActivities = Activities::where('status', '=', 'Draft')->get();
        $usdRate = $this->usdRate();
        $tax = $this->tax();
        $viewModel = new ActivityIndexViewModel(
            activities: $activities,
            activeActivities: $activeActivities,
            draftActivities: $draftActivities,
            archivedActivities: $archivedActivities,
            usdRate: $usdRate,
            tax: $tax,
            pricingService: $this->pricingService,
        );

        return [
            'taxes' => $tax,
            'usdrates' => $usdRate,
            'cactiveactivities' => $activeActivities,
            'activeactivities' => $activities,
            'archiveactivities' => $archivedActivities,
            'draftactivities' => $draftActivities,
            'partners' => Partners::all(),
            'activityIndex' => $viewModel,
            'viewModel' => $viewModel,
        ];
    }

    public function detailData(int $activityId): array
    {
        $now = Carbon::now();
        $business = BusinessProfile::where('id', '=', 1)->first();
        $activity = Activities::with(['partners', 'images'])->findOrFail($activityId);
        $usdRate = $this->usdRate();
        $tax = $this->tax();
        $partner = $activity->partners;
        $viewModel = new ActivityDetailViewModel(
            activity: $activity,
            partner: $partner,
            usdRate: $usdRate,
            tax: $tax,
            pricingService: $this->pricingService,
        );

        return [
            'taxes' => $tax,
            'now' => $now,
            'business' => $business,
            'usdrates' => $usdRate,
            'partner' => $partner,
            'activity' => $activity,
            'activityDetail' => $viewModel,
            'viewModel' => $viewModel,
        ];
    }

    public function formOptions(): array
    {
        return [
            'type' => ActivityType::all(),
            'partners' => Partners::all(),
        ];
    }

    private function usdRate(): object|null
    {
        return Cache::remember('activity_usd_rate', 3600, function () {
            return UsdRates::where('name', 'USD')->first();
        });
    }

    private function tax(): object|null
    {
        return Cache::remember('activity_tax_rate', 3600, function () {
            return Tax::where('id', 1)->first();
        });
    }
}
