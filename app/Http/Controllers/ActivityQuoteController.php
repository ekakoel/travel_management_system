<?php

namespace App\Http\Controllers;

use App\Exceptions\PricingException;
use App\Http\Requests\Activities\QuoteActivityRequest;
use App\Models\Activities;
use App\Services\Activities\ActivityPricingService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

class ActivityQuoteController extends Controller
{
    public function __invoke(
        QuoteActivityRequest $request,
        string $code,
        ActivityPricingService $pricing,
    ): JsonResponse {
        $activity = Activities::query()
            ->published()
            ->where('code', $code)
            ->firstOrFail();

        try {
            $validated = $request->validated();
            $quote = $pricing->quote(
                activity: $activity,
                guestCount: (int) $validated['number_of_guests'],
                activityDate: CarbonImmutable::parse($validated['travel_date']),
            );

            return response()->json([
                'price_available' => true,
                'quote' => $quote->toArray(),
                'display' => [
                    'unit_price_usd' => $quote->unitPriceUsd(),
                    'gross_total_usd' => $quote->grossTotalUsd(),
                    'discount_total_usd' => $quote->discountTotalUsd(),
                    'final_total_usd' => $quote->finalTotalUsd(),
                ],
            ]);
        } catch (PricingException $exception) {
            report($exception);

            return response()->json([
                'price_available' => false,
                'code' => $exception->pricingCode,
                'message' => $exception->pricingCode === 'ACTIVITY_PRICE_DATE_OUT_OF_VALIDITY'
                    ? __('messages.The selected activity date is outside the current price validity period.')
                    : __('messages.Activity pricing is not available.'),
            ], 422);
        }
    }
}
