<?php

namespace App\Services\Hotels;

use App\Models\BookingCode;
use App\Models\HotelPackage;
use App\Models\HotelPrice;
use App\Models\HotelPromo;
use App\Models\Orders;
use App\Models\Promotion;
use App\Models\Tax;
use App\Models\UsdRates;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class HotelPricingService
{
    public function contractRateUsd(float|int|null $contractRateIdr, object|null $usdRate): int
    {
        $rate = (float) ($usdRate->rate ?? 0);

        if ($rate <= 0) {
            return 0;
        }

        return (int) ceil(((float) $contractRateIdr) / $rate);
    }

    public function subtotalUsd(float|int|null $contractRateIdr, float|int|null $markup, object|null $usdRate, int $multiplier = 1): int
    {
        $contractRate = ((float) $contractRateIdr) * max($multiplier, 1);

        return $this->contractRateUsd($contractRate, $usdRate) + (int) ceil((float) $markup);
    }

    public function taxAmount(float|int|null $contractRateIdr, float|int|null $markup, object|null $usdRate, object|null $tax, int $multiplier = 1): int
    {
        $subtotal = $this->subtotalUsd($contractRateIdr, $markup, $usdRate, $multiplier);
        $taxPercent = (float) ($tax->tax ?? 0);

        return (int) ceil($subtotal * ($taxPercent / 100));
    }

    public function publishedRate(float|int|null $contractRateIdr, float|int|null $markup, object|null $usdRate, object|null $tax, int $multiplier = 1): int
    {
        $subtotal = $this->subtotalUsd($contractRateIdr, $markup, $usdRate, $multiplier);

        return $subtotal + $this->taxAmount($contractRateIdr, $markup, $usdRate, $tax, $multiplier);
    }

    public function rateComponents(float|int|null $contractRateIdr, float|int|null $markup, object|null $usdRate, object|null $tax, int $multiplier = 1): array
    {
        $breakdown = $this->rateBreakdown($contractRateIdr, $markup, $usdRate, $tax, $multiplier);

        return [
            'contract_rate_usd' => $breakdown['contract_rate_usd'],
            'markup_usd' => $breakdown['markup_usd'],
            'tax_usd' => $breakdown['tax_usd'],
            'published_rate' => $breakdown['published_rate'],
        ];
    }

    public function rateBreakdown(
        float|int|null $contractRateIdr,
        float|int|null $markup,
        object|null $usdRate,
        object|null $tax,
        int $multiplier = 1,
        float|int|null $kickBack = 0
    ): array {
        $effectiveMultiplier = max($multiplier, 1);
        $exchangeRate = (float) ($usdRate->rate ?? 0);
        $effectiveContractRateIdr = (float) $contractRateIdr * $effectiveMultiplier;
        $contractRateUsd = $this->contractRateUsd($effectiveContractRateIdr, $usdRate);
        $markupUsd = (int) ceil((float) $markup);
        $markupIdr = $this->usdToIdr($markupUsd, $exchangeRate);
        $subtotalUsd = $contractRateUsd + $markupUsd;
        $taxPercent = (float) ($tax->tax ?? 0);
        $taxUsd = (int) ceil($subtotalUsd * ($taxPercent / 100));
        $taxIdr = $this->usdToIdr($taxUsd, $exchangeRate);
        $publishedRate = $subtotalUsd + $taxUsd;
        $kickBackUsd = max((int) ceil((float) $kickBack), 0);
        $publishedRateIdr = $this->usdToIdr($publishedRate, $exchangeRate);
        $netRate = max($publishedRate - $kickBackUsd, 0);

        return [
            'contract_rate_idr' => (float) $contractRateIdr,
            'multiplier' => $effectiveMultiplier,
            'effective_contract_rate_idr' => $effectiveContractRateIdr,
            'exchange_rate_idr' => $exchangeRate,
            'exchange_rate_valid' => $exchangeRate > 0,
            'contract_rate_usd' => $contractRateUsd,
            'markup_usd' => $markupUsd,
            'markup_idr' => $markupIdr,
            'subtotal_usd' => $subtotalUsd,
            'tax_percent' => $taxPercent,
            'tax_usd' => $taxUsd,
            'tax_idr' => $taxIdr,
            'published_rate' => $publishedRate,
            'published_rate_idr' => $publishedRateIdr,
            'kick_back_usd' => $kickBackUsd,
            'kick_back_idr' => $this->usdToIdr($kickBackUsd, $exchangeRate),
            'net_rate' => $netRate,
            'net_rate_idr' => $this->usdToIdr($netRate, $exchangeRate),
        ];
    }

    private function usdToIdr(float|int $usdAmount, float $exchangeRate): int
    {
        if ($exchangeRate <= 0) {
            return 0;
        }

        return (int) ceil((float) $usdAmount * $exchangeRate);
    }

    public function normalPricePublishedRate(object $price, object|null $usdRate, object|null $tax): int
    {
        return $this->publishedRate($price->contract_rate, $price->markup, $usdRate, $tax);
    }

    public function normalPriceNetRate(object $price, object|null $usdRate, object|null $tax): int
    {
        return $this->rateBreakdown(
            $price->contract_rate,
            $price->markup,
            $usdRate,
            $tax,
            1,
            $price->kick_back ?? 0
        )['net_rate'];
    }

    public function packagePublishedRate(object $package, object|null $usdRate, object|null $tax): int
    {
        return $this->publishedRate(
            $package->contract_rate,
            $package->markup,
            $usdRate,
            $tax,
            1
        );
    }

    public function calculateNormalRate(array $input): array
    {
        [$checkin, $checkout, $nights] = $this->stayDates($input['checkin'] ?? null, $input['checkout'] ?? null);
        $hotelId = (int) ($input['hotel_id'] ?? 0);
        $roomId = (int) ($input['room_id'] ?? 0);
        $rooms = max((int) ($input['rooms'] ?? 1), 1);
        $usdRate = $input['usd_rate'] ?? UsdRates::where('name', 'USD')->first();
        $tax = $input['tax'] ?? Tax::find(1);
        $this->ensureValidUsdRate($usdRate);

        $nightly = [];
        $stayTotal = 0;
        $kickBackTotal = 0;

        foreach ($this->stayDateRange($checkin, $nights) as $date) {
            $rate = $this->singleNormalRateForDate($hotelId, $roomId, $date);
            $components = $this->rateComponents($rate->contract_rate, $rate->markup, $usdRate, $tax);
            $kickBack = (int) ($rate->kick_back ?? 0);

            $nightly[] = [
                'date' => $date,
                'rate_type' => 'normal',
                'rate_id' => $rate->id,
                'contract_rate' => (int) $rate->contract_rate,
                'contract_rate_usd' => $components['contract_rate_usd'],
                'markup' => (int) $rate->markup,
                'markup_usd' => $components['markup_usd'],
                'tax_usd' => $components['tax_usd'],
                'kick_back' => $kickBack,
                'published_rate' => $components['published_rate'],
            ];

            $stayTotal += $components['published_rate'];
            $kickBackTotal += $kickBack;
        }

        $promotions = $this->activeGlobalPromotions($input['now'] ?? Carbon::now());
        $promotionDiscounts = $promotions->pluck('discounts')->map(fn ($value) => (int) $value)->values();

        return $this->pricingResult([
            'rate_type' => 'normal',
            'rate_id' => $nightly[0]['rate_id'] ?? null,
            'hotel_id' => $hotelId,
            'room_id' => $roomId,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'nights' => $nights,
            'rooms' => $rooms,
            'user_id' => (int) ($input['user_id'] ?? 0),
            'currency' => 'USD',
            'currency_rate' => (float) ($usdRate->rate ?? 0),
            'nightly_breakdown' => $nightly,
            'stay_total' => $stayTotal,
            'kick_back_total' => $kickBackTotal,
            'promotion_names' => $promotions->pluck('name')->values()->all(),
            'promotion_discounts' => $promotionDiscounts->all(),
            'promotion_discount_total' => $promotionDiscounts->sum(),
            'booking_code' => $input['booking_code'] ?? null,
            'extra_bed_total' => (int) ($input['extra_bed_total'] ?? 0),
            'optional_rate_total' => (int) ($input['optional_rate_total'] ?? 0),
            'airport_shuttle_total' => (int) ($input['airport_shuttle_total'] ?? 0),
        ]);
    }

    public function calculatePromoRate(array $input): array
    {
        [$checkin, $checkout, $nights] = $this->stayDates($input['checkin'] ?? null, $input['checkout'] ?? null);
        $hotelId = (int) ($input['hotel_id'] ?? 0);
        $roomId = (int) ($input['room_id'] ?? 0);
        $rooms = max((int) ($input['rooms'] ?? 1), 1);
        $usdRate = $input['usd_rate'] ?? UsdRates::where('name', 'USD')->first();
        $tax = $input['tax'] ?? Tax::find(1);
        $this->ensureValidUsdRate($usdRate);
        $now = $this->dateString($input['now'] ?? Carbon::now());
        $promoIds = collect($input['promo_ids'] ?? [])->filter()->map(fn ($id) => (int) $id)->unique()->values();

        if ($promoIds->isEmpty()) {
            throw ValidationException::withMessages(['promo_id' => 'Selected promotion is not available.']);
        }

        $promos = HotelPromo::whereIn('id', $promoIds)
            ->where('hotels_id', $hotelId)
            ->where('rooms_id', $roomId)
            ->where('status', 'Active')
            ->whereDate('book_periode_start', '<=', $now)
            ->whereDate('book_periode_end', '>=', $now)
            ->get()
            ->keyBy('id');

        if ($promos->count() !== $promoIds->count()) {
            throw ValidationException::withMessages(['promo_id' => 'Selected promotion is not available for this hotel room.']);
        }

        $nightly = [];
        $stayTotal = 0;
        $promoNightCount = 0;

        foreach ($this->stayDateRange($checkin, $nights) as $date) {
            $promo = $promos->first(function ($item) use ($date, $nights) {
                return $item->periode_start <= $date
                    && $item->periode_end >= $date
                    && (int) $item->minimum_stay <= $nights;
            });

            if ($promo) {
                $components = $this->rateComponents($promo->contract_rate, $promo->markup, $usdRate, $tax);
                $promoNightCount++;
                $nightly[] = [
                    'date' => $date,
                    'rate_type' => 'promo',
                    'rate_id' => $promo->id,
                    'contract_rate' => (int) $promo->contract_rate,
                    'contract_rate_usd' => $components['contract_rate_usd'],
                    'markup' => (int) $promo->markup,
                    'markup_usd' => $components['markup_usd'],
                    'tax_usd' => $components['tax_usd'],
                    'kick_back' => 0,
                    'published_rate' => $components['published_rate'],
                ];
                $stayTotal += $components['published_rate'];

                continue;
            }

            $rate = $this->singleNormalRateForDate($hotelId, $roomId, $date);
            $components = $this->rateComponents($rate->contract_rate, $rate->markup, $usdRate, $tax);
            $nightly[] = [
                'date' => $date,
                'rate_type' => 'normal',
                'rate_id' => $rate->id,
                'contract_rate' => (int) $rate->contract_rate,
                'contract_rate_usd' => $components['contract_rate_usd'],
                'markup' => (int) $rate->markup,
                'markup_usd' => $components['markup_usd'],
                'tax_usd' => $components['tax_usd'],
                'kick_back' => (int) ($rate->kick_back ?? 0),
                'published_rate' => $components['published_rate'],
            ];
            $stayTotal += $components['published_rate'];
        }

        if ($promoNightCount === 0) {
            throw ValidationException::withMessages(['promo_id' => 'Selected promotion does not apply to the selected stay dates.']);
        }

        return $this->pricingResult([
            'rate_type' => 'promo',
            'rate_id' => $promoIds->all(),
            'hotel_id' => $hotelId,
            'room_id' => $roomId,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'nights' => $nights,
            'rooms' => $rooms,
            'user_id' => (int) ($input['user_id'] ?? 0),
            'currency' => 'USD',
            'currency_rate' => (float) ($usdRate->rate ?? 0),
            'nightly_breakdown' => $nightly,
            'stay_total' => $stayTotal,
            'kick_back_total' => 0,
            'promotion_names' => $promos->pluck('name')->values()->all(),
            'promotion_discounts' => [],
            'promotion_discount_total' => 0,
            'booking_code' => $input['booking_code'] ?? null,
            'extra_bed_total' => (int) ($input['extra_bed_total'] ?? 0),
            'optional_rate_total' => (int) ($input['optional_rate_total'] ?? 0),
            'airport_shuttle_total' => (int) ($input['airport_shuttle_total'] ?? 0),
        ]);
    }

    public function calculatePackageRate(array $input): array
    {
        [$checkin, $checkout, $nights] = $this->stayDates($input['checkin'] ?? null, $input['checkout'] ?? null);
        $hotelId = (int) ($input['hotel_id'] ?? 0);
        $roomId = (int) ($input['room_id'] ?? 0);
        $packageId = (int) ($input['package_id'] ?? 0);
        $rooms = max((int) ($input['rooms'] ?? 1), 1);
        $usdRate = $input['usd_rate'] ?? UsdRates::where('name', 'USD')->first();
        $tax = $input['tax'] ?? Tax::find(1);
        $this->ensureValidUsdRate($usdRate);

        $package = HotelPackage::where('id', $packageId)
            ->where('hotels_id', $hotelId)
            ->where('rooms_id', $roomId)
            ->where('status', 'Active')
            ->first();

        if (!$package) {
            throw ValidationException::withMessages(['package_id' => 'Selected package is not available for this hotel room.']);
        }

        if ((int) $package->duration !== $nights) {
            throw ValidationException::withMessages(['duration' => 'Selected package duration does not match the selected stay.']);
        }

        if ($package->stay_period_start > $checkin || $package->stay_period_end < Carbon::parse($checkout)->subDay()->format('Y-m-d')) {
            throw ValidationException::withMessages(['package_id' => 'Selected package is not valid for the selected stay dates.']);
        }

        $components = $this->rateComponents($package->contract_rate, $package->markup, $usdRate, $tax, max((int) $package->duration, 1));

        return $this->pricingResult([
            'rate_type' => 'package',
            'rate_id' => $package->id,
            'hotel_id' => $hotelId,
            'room_id' => $roomId,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'nights' => $nights,
            'rooms' => $rooms,
            'user_id' => (int) ($input['user_id'] ?? 0),
            'currency' => 'USD',
            'currency_rate' => (float) ($usdRate->rate ?? 0),
            'nightly_breakdown' => [[
                'date' => $checkin,
                'rate_type' => 'package',
                'rate_id' => $package->id,
                'contract_rate' => (int) $package->contract_rate,
                'contract_rate_usd' => $components['contract_rate_usd'],
                'markup' => (int) $package->markup,
                'markup_usd' => $components['markup_usd'],
                'tax_usd' => $components['tax_usd'],
                'kick_back' => 0,
                'published_rate' => $components['published_rate'],
            ]],
            'stay_total' => $components['published_rate'],
            'kick_back_total' => 0,
            'promotion_names' => [],
            'promotion_discounts' => [],
            'promotion_discount_total' => 0,
            'booking_code' => $input['booking_code'] ?? null,
            'extra_bed_total' => (int) ($input['extra_bed_total'] ?? 0),
            'optional_rate_total' => (int) ($input['optional_rate_total'] ?? 0),
            'airport_shuttle_total' => (int) ($input['airport_shuttle_total'] ?? 0),
            'package_snapshot' => [
                'name' => $package->name,
                'benefits' => $package->benefits,
                'include' => $package->include,
                'additional_info' => $package->additional_info,
            ],
        ]);
    }

    public function resolveBookingCodeForOrder(?string $code, int $userId, int $subtotal): array
    {
        $code = strtoupper(trim((string) $code));

        if ($code === '') {
            return [null, 0];
        }

        $bookingCode = BookingCode::where('code', $code)
            ->where('status', 'Active')
            ->first();

        if (!$bookingCode || Carbon::parse($bookingCode->expired_date)->startOfDay()->lt(Carbon::now()->startOfDay())) {
            return [null, 0];
        }

        if ((int) $bookingCode->amount > 0 && (int) $bookingCode->used >= (int) $bookingCode->amount) {
            return [null, 0];
        }

        $usedByUser = Orders::where('sales_agent', $userId)
            ->where('bookingcode', $bookingCode->code)
            ->where('status', '!=', 'Rejected')
            ->exists();

        if ($usedByUser) {
            return [null, 0];
        }

        return [$bookingCode, min((int) $bookingCode->discounts, max($subtotal, 0))];
    }

    private function ensureValidUsdRate(object|null $usdRate): void
    {
        if ((float) ($usdRate->rate ?? 0) <= 0) {
            throw ValidationException::withMessages([
                'currency' => 'A valid positive USD conversion rate is required before Hotel pricing can be calculated.',
            ]);
        }
    }

    private function pricingResult(array $data): array
    {
        $rooms = max((int) $data['rooms'], 1);
        $stayTotal = (int) $data['stay_total'];
        $normalPrice = $stayTotal * $rooms;
        $contractRateTotal = collect($data['nightly_breakdown'] ?? [])->sum(fn ($item) => (int) ($item['contract_rate_usd'] ?? 0)) * $rooms;
        $markupTotal = collect($data['nightly_breakdown'] ?? [])->sum(fn ($item) => (int) ($item['markup_usd'] ?? 0)) * $rooms;
        $taxTotal = collect($data['nightly_breakdown'] ?? [])->sum(fn ($item) => (int) ($item['tax_usd'] ?? 0)) * $rooms;
        $kickBackTotal = (int) $data['kick_back_total'] * $rooms;
        $promotionDiscountTotal = (int) $data['promotion_discount_total'];
        $extraBedTotal = (int) $data['extra_bed_total'];
        $optionalRateTotal = (int) $data['optional_rate_total'];
        $airportShuttleTotal = (int) $data['airport_shuttle_total'];
        $subtotalBeforeBookingCode = max(($normalPrice - $kickBackTotal - $promotionDiscountTotal) + $extraBedTotal + $optionalRateTotal + $airportShuttleTotal, 0);
        [$bookingCode, $bookingCodeDiscount] = $this->resolveBookingCodeForOrder(
            $data['booking_code'] ?? null,
            (int) ($data['user_id'] ?? auth()->id()),
            $subtotalBeforeBookingCode
        );

        $priceTotal = max(($normalPrice - $kickBackTotal) + $extraBedTotal + $optionalRateTotal, 0);
        $grandTotal = max($priceTotal - $promotionDiscountTotal - $bookingCodeDiscount + $airportShuttleTotal, 0);

        return array_merge($data, [
            'price_pax' => $stayTotal,
            'normal_price' => $normalPrice,
            'contract_rate_total' => $contractRateTotal,
            'markup_total' => $markupTotal,
            'tax_total' => $taxTotal,
            'kick_back_total' => $kickBackTotal,
            'price_total' => $priceTotal,
            'booking_code_model' => $bookingCode,
            'booking_code_value' => $bookingCode?->code,
            'booking_code_discount' => $bookingCodeDiscount,
            'subtotal' => $subtotalBeforeBookingCode,
            'grand_total' => $grandTotal,
        ]);
    }

    private function stayDates($checkin, $checkout): array
    {
        try {
            $checkinDate = Carbon::parse($checkin)->startOfDay();
            $checkoutDate = Carbon::parse($checkout)->startOfDay();
        } catch (\Throwable $exception) {
            throw ValidationException::withMessages(['checkin' => 'Stay dates are invalid.']);
        }

        if (!$checkoutDate->greaterThan($checkinDate)) {
            throw ValidationException::withMessages(['checkout' => 'Checkout must be after checkin.']);
        }

        return [$checkinDate->format('Y-m-d'), $checkoutDate->format('Y-m-d'), $checkinDate->diffInDays($checkoutDate)];
    }

    private function stayDateRange(string $checkin, int $nights): array
    {
        $dates = [];

        for ($index = 0; $index < $nights; $index++) {
            $dates[] = Carbon::parse($checkin)->addDays($index)->format('Y-m-d');
        }

        return $dates;
    }

    private function singleNormalRateForDate(int $hotelId, int $roomId, string $date): HotelPrice
    {
        $rates = HotelPrice::where('hotels_id', $hotelId)
            ->where('rooms_id', $roomId)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->get();

        if ($rates->count() !== 1) {
            throw ValidationException::withMessages(['price' => 'Hotel rate is not available for every selected night.']);
        }

        return $rates->first();
    }

    private function activeGlobalPromotions($now): Collection
    {
        $date = $this->dateString($now);

        return Promotion::where('status', 'Active')
            ->whereDate('periode_start', '<=', $date)
            ->whereDate('periode_end', '>=', $date)
            ->get();
    }

    private function dateString($date): string
    {
        return $date instanceof Carbon ? $date->format('Y-m-d') : Carbon::parse($date)->format('Y-m-d');
    }
}
