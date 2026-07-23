<?php

namespace App\Services\Transports;

use App\Models\BusinessProfile;
use App\Models\Tax;
use App\Models\TransportBrand;
use App\Models\TransportPrice;
use App\Models\Transports;
use App\Models\TransportType;
use App\Models\UsdRates;
use App\ViewModels\Transports\TransportDetailViewModel;
use App\ViewModels\Transports\TransportIndexViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class TransportInventoryService
{
    public function __construct(
        private readonly TransportPricingService $pricingService,
    ) {
    }

    public function indexData(): array
    {
        $activeTransports = Transports::with(['images', 'prices'])
            ->where('status', 'Active')
            ->get();
        $draftTransports = Transports::with(['images', 'prices'])
            ->where('status', 'Draft')
            ->get();
        $archivedTransports = Transports::with(['images', 'prices'])
            ->where('status', 'Archived')
            ->get();
        $visibleTransports = $activeTransports->concat($draftTransports)->values();
        $viewModel = new TransportIndexViewModel(
            activeTransports: $activeTransports,
            draftTransports: $draftTransports,
            archivedTransports: $archivedTransports,
        );

        return [
            'usdrates' => $this->usdRate(),
            'cactivetransports' => $activeTransports,
            'activetransports' => $visibleTransports,
            'archivetransports' => $archivedTransports,
            'drafttransports' => $draftTransports,
            'transportIndex' => $viewModel,
            'viewModel' => $viewModel,
        ];
    }

    public function detailData(int $transportId): array
    {
        $tax = $this->tax();
        $usdRate = $this->usdRate();
        $transport = Transports::with('images')->findOrFail($transportId);
        $prices = TransportPrice::where('transports_id', $transportId)->orderBy('created_at', 'desc')->get();
        $viewModel = new TransportDetailViewModel(
            transport: $transport,
            prices: $prices,
            usdRate: $usdRate,
            tax: $tax,
            pricingService: $this->pricingService,
        );

        return [
            'taxes' => $tax,
            'prices' => $prices,
            'usdrates' => $usdRate,
            'now' => Carbon::now(),
            'business' => BusinessProfile::where('id', 1)->first(),
            'transport' => $transport,
            'transportDetail' => $viewModel,
            'viewModel' => $viewModel,
        ];
    }

    public function formOptions(): array
    {
        return [
            'transports' => Transports::all(),
            'type' => TransportType::all(),
            'brand' => TransportBrand::all(),
        ];
    }

    public function editData(int $transportId): array
    {
        return array_merge($this->formOptions(), [
            'transport' => Transports::findOrFail($transportId),
            'usdrates' => $this->usdRate(),
        ]);
    }

    public function galleryData(int $transportId): array
    {
        return [
            'transports' => Transports::with('images')->findOrFail($transportId),
        ];
    }

    private function usdRate(): object|null
    {
        return Cache::remember('usd_rates', 3600, fn () => UsdRates::select('name', 'rate')->where('name', 'USD')->first());
    }

    private function tax(): object|null
    {
        return Cache::remember('transport_tax', 3600, fn () => Tax::select('name', 'tax')->where('id', 1)->first());
    }
}
