<?php

namespace App\Services\Tours;

use App\Models\ActionLog;
use App\Models\TourType;
use App\Models\Tours;
use App\Support\MoneyFormatter;
use App\ViewModels\Tours\TourDetailViewModel;
use App\ViewModels\Tours\TourIndexViewModel;
use Carbon\Carbon;

class TourInventoryService
{
    public function __construct(
        private readonly TourPackagePricingService $pricingService,
        private readonly MoneyFormatter $formatter,
    ) {
    }

    public function indexData(): array
    {
        $serviceDate = Carbon::now();
        $activeTours = Tours::with(['images', 'prices', 'type'])->where('status', 'Active')->get();
        $archivedTours = Tours::with(['prices', 'type'])->where('status', 'Archived')->get();
        $draftTours = Tours::with(['prices', 'type'])->where('status', 'Draft')->get();
        $viewModel = new TourIndexViewModel(
            activeTours: $activeTours,
            draftTours: $draftTours,
            archivedTours: $archivedTours,
            pricingService: $this->pricingService,
            formatter: $this->formatter,
            serviceDate: $serviceDate,
        );

        return [
            'tax' => null,
            'usdrates' => null,
            'activetours' => $activeTours,
            'archivetours' => $archivedTours,
            'drafttours' => $draftTours,
            'totalTours' => $viewModel->totalTours(),
            'tourIndex' => $viewModel,
            'viewModel' => $viewModel,
        ];
    }

    public function detailData(int $tourId): array
    {
        $now = Carbon::now();
        $tour = Tours::with([
            'images',
            'locations' => fn ($query) => $query->ordered(),
            'type',
            'prices' => fn ($query) => $query->with('verifier')->orderBy('min_qty')->orderBy('valid_from'),
        ])->findOrFail($tourId);
        $actionLogs = ActionLog::where('service', 'Tour Package')->where('service_id', $tourId)->get();
        $viewModel = new TourDetailViewModel(
            tour: $tour,
            actionLogs: $actionLogs,
            pricingService: $this->pricingService,
            formatter: $this->formatter,
            serviceDate: $now,
        );

        return [
            'usdrates' => null,
            'tour' => $tour,
            'action_log' => $actionLogs,
            'user' => auth()->user(),
            'tax' => null,
            'tourDetail' => $viewModel,
            'viewModel' => $viewModel,
        ];
    }

    public function formOptions(): array
    {
        return [
            'types' => TourType::query()->orderBy('type')->get(),
        ];
    }

    public function editData(int $tourId): array
    {
        return array_merge($this->formOptions(), [
            'tour' => Tours::with(['locations' => fn ($query) => $query->ordered()])
                ->withCount(['locations', 'prices', 'images'])
                ->findOrFail($tourId),
            'usdrates' => null,
        ]);
    }
}
