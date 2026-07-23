<?php

namespace App\Services\Tours;

use App\Models\ActionLog;
use App\Models\Partners;
use App\Models\Tax;
use App\Models\TourType;
use App\Models\Tours;
use App\Models\UsdRates;
use App\ViewModels\Tours\TourDetailViewModel;
use App\ViewModels\Tours\TourIndexViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class TourInventoryService
{
    public function __construct(
        private readonly TourPricingService $pricingService,
    ) {
    }

    public function indexData(): array
    {
        $tax = $this->tax();
        $usdRate = $this->usdRate();
        $activeTours = Tours::with(['images', 'prices', 'type'])->where('status', 'Active')->get();
        $archivedTours = Tours::with(['prices', 'type'])->where('status', 'Archived')->get();
        $draftTours = Tours::with(['prices', 'type'])->where('status', 'Draft')->get();
        $viewModel = new TourIndexViewModel(
            activeTours: $activeTours,
            draftTours: $draftTours,
            archivedTours: $archivedTours,
            usdRate: $usdRate,
            tax: $tax,
            pricingService: $this->pricingService,
        );

        return [
            'tax' => $tax,
            'usdrates' => $usdRate,
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
        $tax = $this->tax();
        $usdRate = $this->usdRate();
        $tour = Tours::with([
            'images',
            'type',
            'prices' => fn ($query) => $query->where('expired_date', '>=', $now),
        ])->findOrFail($tourId);
        $actionLogs = ActionLog::where('service', 'Tour Package')->where('service_id', $tourId)->get();
        $viewModel = new TourDetailViewModel(
            tour: $tour,
            usdRate: $usdRate,
            tax: $tax,
            actionLogs: $actionLogs,
            pricingService: $this->pricingService,
        );

        return [
            'usdrates' => $usdRate,
            'tour' => $tour,
            'action_log' => $actionLogs,
            'user' => auth()->user(),
            'tax' => $tax,
            'tourDetail' => $viewModel,
            'viewModel' => $viewModel,
        ];
    }

    public function formOptions(): array
    {
        return [
            'tours' => Tours::all(),
            'partners' => Partners::all(),
            'types' => TourType::all(),
        ];
    }

    public function editData(int $tourId): array
    {
        return array_merge($this->formOptions(), [
            'tour' => Tours::with(['locations' => fn ($query) => $query->ordered()])->findOrFail($tourId),
            'usdrates' => $this->usdRate(),
        ]);
    }

    private function usdRate(): object|null
    {
        return Cache::remember('usd_rates', 3600, fn () => UsdRates::select('name', 'rate')->where('name', 'USD')->first());
    }

    private function tax(): object|null
    {
        return Cache::remember('tax', 3600, fn () => Tax::select('name', 'tax')->where('name', 'tax')->first());
    }
}
