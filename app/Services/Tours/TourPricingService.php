<?php

namespace App\Services\Tours;

use App\Models\TourPrices;
use App\Models\Tours;
use App\Support\CanonicalDecimalInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TourPricingService
{
    public function __construct(private readonly TourPriceOverlapValidator $overlapValidator)
    {
    }

    public function createPrice(Tours $tour, array $validated, int $verifiedBy): TourPrices
    {
        return DB::transaction(function () use ($tour, $validated, $verifiedBy) {
            $this->ensureNoReadyOverlap($tour, $validated, null);

            return TourPrices::create($this->attributes($tour, $validated, $verifiedBy));
        });
    }

    public function updatePrice(
        Tours $tour,
        TourPrices $tourPrice,
        array $validated,
        int $verifiedBy,
    ): TourPrices {
        $this->ensureOwnership($tour, $tourPrice);

        return DB::transaction(function () use ($tour, $tourPrice, $validated, $verifiedBy) {
            $lockedPrice = TourPrices::query()->lockForUpdate()->findOrFail($tourPrice->id);
            $this->ensureOwnership($tour, $lockedPrice);
            $this->ensureNoReadyOverlap($tour, $validated, (int) $lockedPrice->id);
            $lockedPrice->update($this->attributes($tour, $validated, $verifiedBy));

            return $lockedPrice->refresh();
        });
    }

    public function deletePrice(Tours $tour, TourPrices $tourPrice): TourPrices
    {
        $this->ensureOwnership($tour, $tourPrice);
        DB::transaction(fn () => $tourPrice->delete());

        return $tourPrice;
    }

    public function restorePrice(Tours $tour, int $tourPriceId): TourPrices
    {
        return DB::transaction(function () use ($tour, $tourPriceId) {
            $tourPrice = TourPrices::withTrashed()->lockForUpdate()->findOrFail($tourPriceId);
            $this->ensureOwnership($tour, $tourPrice);
            $this->ensureNoReadyOverlap($tour, [
                'min_qty' => $tourPrice->min_qty,
                'max_qty' => $tourPrice->max_qty,
                'valid_from' => optional($tourPrice->valid_from)->toDateString(),
                'valid_until' => optional($tourPrice->valid_until)->toDateString(),
            ], (int) $tourPrice->id);
            $tourPrice->restore();

            return $tourPrice->refresh();
        });
    }

    private function attributes(Tours $tour, array $validated, int $verifiedBy): array
    {
        $contractRate = $validated['contract_rate_idr'] ?? null;
        $markup = CanonicalDecimalInput::normalize($validated['markup_amount'] ?? null);
        $markupType = $validated['markup_type'];
        $validUntil = $validated['valid_until'] ?? null;
        $markupCurrency = match ($markupType) {
            TourPrices::MARKUP_TYPE_USD => 'USD',
            TourPrices::MARKUP_TYPE_IDR, TourPrices::MARKUP_TYPE_PERCENTAGE => 'IDR',
        };

        return [
            'tour_id' => $tour->id,
            'min_qty' => $validated['min_qty'],
            'max_qty' => $validated['max_qty'],
            'contract_rate' => $contractRate === null ? '' : (string) $contractRate,
            'contract_rate_idr' => $contractRate,
            'markup' => $markup === null ? '' : (string) $markup,
            'markup_amount' => $markup,
            'markup_type' => $markupType,
            'markup_currency' => $markupCurrency,
            'markup_source' => 'admin-crud:'.$markupType,
            'valid_from' => $validated['valid_from'] ?? null,
            'valid_until' => $validUntil,
            'expired_date' => $validUntil === null ? '' : $validUntil,
            'pricing_data_status' => TourPrices::STATUS_READY,
            'markup_verified_at' => now(),
            'markup_verified_by' => $verifiedBy,
            'status' => 'Active',
        ];
    }

    private function ensureNoReadyOverlap(Tours $tour, array $validated, ?int $exceptPriceId): void
    {
        if (empty($validated['valid_from']) || empty($validated['valid_until'])) {
            return;
        }

        if ($this->overlapValidator->conflicts(
            (int) $tour->id,
            (int) $validated['min_qty'],
            (int) $validated['max_qty'],
            $validated['valid_from'],
            $validated['valid_until'],
            $exceptPriceId,
            true,
        )) {
            throw ValidationException::withMessages([
                'min_qty' => 'The pax tier overlaps another price during the selected validity period.',
            ]);
        }
    }

    private function ensureOwnership(Tours $tour, TourPrices $tourPrice): void
    {
        abort_unless((int) $tourPrice->tour_id === (int) $tour->id, 404);
    }

}
