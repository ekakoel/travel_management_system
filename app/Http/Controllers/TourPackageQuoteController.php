<?php

namespace App\Http\Controllers;

use App\Exceptions\PricingException;
use App\Http\Requests\Tours\QuoteTourPackageRequest;
use App\Http\Resources\Pricing\PricingQuoteResource;
use App\Models\Tours;
use App\Services\Tours\TourPackagePricingService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

class TourPackageQuoteController extends Controller
{
    public function __invoke(
        QuoteTourPackageRequest $request,
        Tours $tour,
        TourPackagePricingService $pricing,
    ): PricingQuoteResource|JsonResponse {
        if ($tour->status !== 'Active') {
            abort(404);
        }

        try {
            $validated = $request->validated();
            $quote = $pricing->quote(
                tour: $tour,
                guestCount: (int) $validated['number_of_guests'],
                serviceDate: CarbonImmutable::parse($validated['travel_date']),
                preferredPriceId: isset($validated['tour_price_id'])
                    ? (int) $validated['tour_price_id']
                    : null,
                promotionId: isset($validated['promotion_id'])
                    ? (int) $validated['promotion_id']
                    : null,
                bookingCode: $validated['booking_code'] ?? null,
                actorId: $request->user()?->id,
            );

            return new PricingQuoteResource($quote);
        } catch (PricingException $exception) {
            report($exception);

            return response()->json([
                'price_available' => false,
                'code' => $exception->pricingCode,
                'message' => __('tour-detail.price_temporarily_unavailable'),
            ], 422);
        }
    }
}
