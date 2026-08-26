<?php

namespace App\Services\Hotels;

use App\Models\ActionLog;
use App\Models\HotelPackage;
use App\Models\HotelPrice;
use App\Models\HotelPromo;
use App\Models\Hotels;
use App\Models\Markup;
use App\Models\Tax;
use App\Models\UsdRates;
use App\Models\User;
use App\ViewModels\Hotels\HotelDetailViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class HotelInventoryService
{
    public function __construct(
        private readonly HotelStatusService $statusService,
        private readonly HotelPricingService $pricingService,
    )
    {
    }

    public function detailData(int $hotelId): array
    {
        $today = Carbon::now();
        $now = $today->toDateString();
        $yearStart = $today->copy()->startOfYear()->toDateString();

        $this->statusService->expirePromosForHotel($hotelId, $today);
        $this->statusService->expirePackagesForHotel($hotelId, $today);

        $usdRates = Cache::remember('usd_rates', 3600, function () {
            return UsdRates::select('name', 'rate')->where('name', 'USD')->first();
        });
        $taxes = Cache::remember('hotel_tax_rate', 3600, function () {
            return Tax::where('id', 1)->first();
        });

        $hotel = Hotels::with([
            'rooms',
            'prices' => fn ($query) => $query->notExpired($now)->orderByDesc('end_date'),
            'prices.rooms',
            'promos' => fn ($query) => $query->whereDate('book_periode_end', '>=', $now)->orderByDesc('book_periode_end'),
            'promos.rooms',
            'packages' => fn ($query) => $query->notExpired($now)->orderByDesc('stay_period_end'),
            'packages.room',
            'optionalrates' => fn ($query) => $query->notExpired($now),
            'contracts' => fn ($query) => $query->where('period_end', '>', $now),
            'extrabeds',
            'wedding_venue',
        ])->findOrFail($hotelId);

        $latestPrice = Hotels::withMax([
            'prices as date' => fn ($query) => $query->notExpired($now),
        ], 'end_date')->findOrFail($hotelId);
        $author = $hotel->author_id ? User::find($hotel->author_id) : null;
        $normalPrices = $hotel->prices->values();
        $promos = $hotel->promos->values();
        $packages = $hotel->packages->values();
        $chartNormalPrices = HotelPrice::with('rooms')
            ->where('hotels_id', $hotelId)
            ->whereDate('start_date', '<=', $now)
            ->whereDate('end_date', '>=', $yearStart)
            ->orderBy('start_date')
            ->get();
        $chartPromos = HotelPromo::with('rooms')
            ->where('hotels_id', $hotelId)
            ->whereDate('periode_start', '<=', $now)
            ->whereDate('periode_end', '>=', $yearStart)
            ->orderBy('periode_start')
            ->get();
        $chartPackages = HotelPackage::with('room')
            ->where('hotels_id', $hotelId)
            ->whereDate('stay_period_start', '<=', $now)
            ->whereDate('stay_period_end', '>=', $yearStart)
            ->orderBy('stay_period_start')
            ->get();
        $additionalCharges = $hotel->optionalrates->values();
        $contracts = $hotel->contracts->values();
        $viewModel = new HotelDetailViewModel(
            hotel: $hotel,
            rooms: $hotel->rooms,
            normalPrices: $normalPrices,
            promos: $promos,
            packages: $packages,
            additionalCharges: $additionalCharges,
            contracts: $contracts,
            usdRate: $usdRates,
            tax: $taxes,
            now: $now,
            latestPrice: $latestPrice,
            author: $author,
            pricingService: $this->pricingService,
            chartNormalPrices: $chartNormalPrices,
            chartPromos: $chartPromos,
            chartPackages: $chartPackages,
        );

        return [
            'taxes' => $taxes,
            'additional_charges' => $additionalCharges,
            'extra_bed' => $hotel->extrabeds,
            'usdrates' => $usdRates,
            'tax' => $taxes,
            'markup' => Markup::where('service', 'Hotel')->where('service_id', $hotelId)->first() ?: '',
            'action_log' => ActionLog::where('service', 'Hotel')->get(),
            'packages' => $packages,
            'priceokt' => HotelPrice::where('hotels_id', $hotelId)->where('rooms_id', 1)->notExpired($now)->orderBy('start_date', 'DESC')->get(),
            'moonnow' => date('m', strtotime($now)),
            'hotel' => $hotel,
            'rooms' => $hotel->rooms,
            'latest_price' => $latestPrice,
            'now' => $now,
            'contracts' => $contracts,
            'author' => $author,
            'promos' => $promos,
            'weddingVenues' => $hotel->wedding_venue,
            'normal_prices' => $normalPrices,
            'inventory_summary' => $this->summary($hotel, $normalPrices, $promos),
            'hotelDetail' => $viewModel,
            'viewModel' => $viewModel,
        ];
    }

    public function summary(Hotels $hotel, iterable $normalPrices, iterable $promos): array
    {
        return [
            'rooms' => $hotel->rooms->count(),
            'normal_prices' => collect($normalPrices)->count(),
            'promos' => collect($promos)->count(),
            'packages' => $hotel->packages->count(),
            'additional_charges' => $hotel->optionalrates->count(),
        ];
    }
}
