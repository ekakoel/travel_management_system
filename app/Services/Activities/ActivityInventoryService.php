<?php

namespace App\Services\Activities;

use App\Models\Activities;
use App\Models\ActivityType;
use App\Models\BusinessProfile;
use App\Models\Partners;
use App\ViewModels\Activities\ActivityDetailViewModel;
use App\ViewModels\Activities\ActivityIndexViewModel;
use Carbon\Carbon;

class ActivityInventoryService
{
    public function __construct(
        private readonly ActivityPricingService $pricingService,
        private readonly ActivityValidityService $validityService,
    ) {
    }

    public function indexData(): array
    {
        $this->validityService->draftExpired();

        $activities = Activities::query()
            ->with('partners:id,name')
            ->where('status', '!=', 'Removed')
            ->where('status', '!=', 'Archived')
            ->get();
        $activeActivities = $activities->where('status', 'Active')->values();
        $draftActivities = $activities->where('status', 'Draft')->values();
        $archivedActivities = Activities::query()
            ->where('status', 'Archived')
            ->get(['id', 'status']);
        $viewModel = new ActivityIndexViewModel(
            activities: $activities,
            activeActivities: $activeActivities,
            draftActivities: $draftActivities,
            archivedActivities: $archivedActivities,
            pricingService: $this->pricingService,
        );

        return [
            'cactiveactivities' => $activeActivities,
            'activeactivities' => $activities,
            'archiveactivities' => $archivedActivities,
            'draftactivities' => $draftActivities,
            'partners' => Partners::query()->get(['id', 'name']),
            'activityIndex' => $viewModel,
            'viewModel' => $viewModel,
        ];
    }

    public function detailData(int $activityId): array
    {
        $this->validityService->draftExpired();

        $now = Carbon::now();
        $business = BusinessProfile::where('id', '=', 1)->first();
        $activity = Activities::with(['partners:id,name', 'images'])->findOrFail($activityId);
        $partner = $activity->partners;
        $viewModel = new ActivityDetailViewModel(
            activity: $activity,
            partner: $partner,
            pricingService: $this->pricingService,
        );

        return [
            'now' => $now,
            'business' => $business,
            'partner' => $partner,
            'activity' => $activity,
            'activityDetail' => $viewModel,
            'viewModel' => $viewModel,
        ];
    }

    public function formOptions(): array
    {
        return [
            'type' => ActivityType::query()->get(['id', 'type']),
            'partners' => Partners::query()->get(['id', 'name']),
        ];
    }
}
