<?php

namespace App\Services\Activities;

use App\Exceptions\PricingException;
use App\Models\Activities;
use App\Models\Guests;
use App\Models\OrderLog;
use App\Models\Orders;
use App\Models\UsdRates;
use App\Models\User;
use App\Models\UserLog;
use App\Services\ActivityReservationService;
use App\Support\MoneyFormatter;
use App\Support\Pricing\FixedScale;
use App\ValueObjects\Money;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ActivityBookingService
{
    public function __construct(
        private readonly ActivityGuestListService $guestLists,
        private readonly ActivityPricingService $pricing,
        private readonly ActivityReservationService $reservations,
        private readonly ActivityTimingService $timing,
    ) {
    }

    public function create(string $code, array $validated, ?UploadedFile $guestList, User $user, ?string $ipAddress): Orders
    {
        $activity = Activities::with('partners')
            ->published()
            ->where('code', $code)
            ->firstOrFail();

        $travelDate = Carbon::parse($validated['travel_date']);
        $guestCount = (int) $validated['number_of_guests'];

        $this->validatePax($activity, $guestCount);

        $activityGuests = $guestCount > ActivityGuestListService::MANUAL_THRESHOLD
            ? $this->guestLists->parseUpload($guestList, $guestCount)
            : $this->guestLists->normalizeManualGuests($validated['guests'] ?? [], $guestCount);

        $timing = $this->timing->resolve($activity, $travelDate);
        $cnyrates = UsdRates::where('name', 'CNY')->first();
        $twdrates = UsdRates::where('name', 'TWD')->first();

        try {
            $activityQuote = $this->pricing->quote(
                activity: $activity,
                guestCount: $guestCount,
                activityDate: CarbonImmutable::parse($travelDate->format('Y-m-d H:i:s')),
            );
        } catch (PricingException $exception) {
            report($exception);

            if ($exception->pricingCode === 'ACTIVITY_PRICE_DATE_OUT_OF_VALIDITY') {
                throw ValidationException::withMessages([
                    'travel_date' => __('messages.The selected activity date is outside the current price validity period.'),
                ]);
            }

            throw ValidationException::withMessages([
                'number_of_guests' => __('messages.Activity pricing is not available.'),
            ]);
        }

        $promotionDiscounts = collect($activityQuote->promotions)
            ->map(fn (array $promotion) => app(MoneyFormatter::class)->decimal(
                Money::usdCents((int) $promotion['amount_usd_minor'])
            ))
            ->values();
        $activityContact = collect($activityGuests)->first(fn ($guest) => filled($guest['phone']))
            ?: ($activityGuests[0] ?? null);
        $guestDetail = $this->buildGuestManifestHtml($activityGuests, $guestCount);

        return DB::transaction(function () use (
            $activity,
            $activityGuests,
            $activityQuote,
            $activityContact,
            $cnyrates,
            $guestCount,
            $guestDetail,
            $ipAddress,
            $promotionDiscounts,
            $travelDate,
            $timing,
            $twdrates,
            $user,
            $validated
        ) {
            $order = Orders::create($this->filterOrderPayload([
                'orderno' => $this->generateActivityOrderNumber($travelDate),
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'service' => Orders::PUBLIC_ACTIVITY_SERVICE,
                'service_type' => $activity->type,
                'service_id' => $activity->id,
                'servicename' => optional($activity->partners)->name ?: '-',
                'subservice' => $activity->name,
                'subservice_id' => $activity->id,
                'sales_agent' => $user->id,
                'checkin' => $timing['activity_start']->format('Y-m-d H:i:s'),
                'checkout' => $timing['activity_end']->format('Y-m-d H:i:s'),
                'pickup_date' => $timing['pickup_date']->format('Y-m-d H:i:s'),
                'dropoff_date' => $timing['dropoff_date']->format('Y-m-d H:i:s'),
                'travel_date' => $travelDate->format('Y-m-d H:i:s'),
                'pickup_name' => $activityContact['name'] ?? null,
                'pickup_phone' => $activityContact['phone'] ?? null,
                'pickup_location' => trim((string) $validated['pickup_location']),
                'dropoff_location' => trim((string) $validated['dropoff_location']),
                'location' => $activity->location,
                'capacity' => (int) ($activity->qty ?: $guestCount),
                'number_of_guests' => $guestCount,
                'guest_detail' => $guestDetail,
                'note' => filled($validated['note'] ?? null) ? trim((string) $validated['note']) : null,
                'include' => $activity->include,
                'additional_info' => $activity->additional_info,
                'cancellation_policy' => $activity->cancellation_policy,
                'itinerary' => $activity->itinerary,
                'duration' => $activity->duration,
                'price_total' => $activityQuote->grossTotalUsd(),
                'normal_price' => $activityQuote->grossTotalUsd(),
                'price_pax' => $activityQuote->unitPriceUsd(),
                'final_price' => $activityQuote->finalTotalUsd(),
                'promotion' => $activityQuote->promotions !== []
                    ? json_encode(collect($activityQuote->promotions)->pluck('name')->values()->all())
                    : null,
                'promotion_disc' => $activityQuote->promotions !== [] ? json_encode($promotionDiscounts->all()) : null,
                'usd_rate' => FixedScale::formatDecimal($activityQuote->rate->valueScaled, $activityQuote->rate->scale),
                'cny_rate' => $cnyrates?->sell ?: $cnyrates?->rate,
                'twd_rate' => $twdrates?->sell ?: $twdrates?->rate,
                'status' => 'Pending',
            ]));

            $this->insertGuests($order, $activityGuests);
            $this->reservations->ensurePendingReservationForOrder($order);

            UserLog::create([
                'action' => 'Create Order',
                'service' => Orders::PUBLIC_ACTIVITY_SERVICE,
                'subservice' => $activity->name,
                'subservice_id' => $order->id,
                'page' => 'activity-detail-modern',
                'user_id' => $user->id,
                'user_ip' => $ipAddress,
                'note' => 'Created activity order with order no: '.$order->orderno,
            ]);

            OrderLog::create([
                'order_id' => $order->id,
                'action' => 'Create Order',
                'url' => 'activity-detail-modern',
                'method' => 'POST',
                'agent' => $user->name,
                'admin' => $user->id,
            ]);

            return $order;
        });
    }

    private function validatePax(Activities $activity, int $guestCount): void
    {
        $minimumPax = max((int) ($activity->min_pax ?: 1), 1);
        if ($guestCount < $minimumPax) {
            throw ValidationException::withMessages([
                'number_of_guests' => __('messages.Number of guests is below the activity minimum.'),
            ]);
        }

        $capacity = (int) ($activity->qty ?: 0);
        if ($capacity > 0 && $guestCount > $capacity) {
            throw ValidationException::withMessages([
                'number_of_guests' => __('messages.Number of guests exceeds activity capacity.'),
            ]);
        }

        if ((float) $activity->contract_rate <= 0 && (float) $activity->markup <= 0) {
            throw ValidationException::withMessages([
                'number_of_guests' => __('messages.Activity pricing is not available.'),
            ]);
        }
    }

    private function insertGuests(Orders $order, array $guests): void
    {
        if (! Schema::hasTable('guests')) {
            return;
        }

        $columns = collect(Schema::getColumnListing('guests'))->flip()->all();
        $now = now();
        $rows = collect($guests)
            ->map(fn ($guest) => collect([
                'order_id' => $order->id,
                'name' => $guest['name'] ?: null,
                'phone' => $guest['phone'] ?: null,
                'age' => $guest['age'] ?: null,
                'sex' => $guest['sex'] ?: null,
                'date_of_birth' => $guest['date_of_birth'] ?: null,
                'identification_type' => $guest['identification_type'] ?: null,
                'identification_no' => $guest['identification_no'] ?: null,
                'created_at' => $now,
                'updated_at' => $now,
            ])->filter(fn ($value, $key) => array_key_exists($key, $columns))->all())
            ->values()
            ->all();

        if ($rows !== []) {
            Guests::insert($rows);
        }
    }

    private function buildGuestManifestHtml(array $guests, int $guestCount): string
    {
        if (empty($guests)) {
            return '';
        }

        $items = collect($guests)
            ->values()
            ->map(function ($guest, $index) {
                $parts = array_filter([
                    $guest['name'] ?: __('tour-detail.guest').' '.($index + 1),
                    $guest['age'] ?: null,
                    $guest['sex'] ?: null,
                    $guest['phone'] ? __('messages.Phone').': '.$guest['phone'] : null,
                    $guest['identification_type'] ? __('tour-detail.guest_id_type').': '.$guest['identification_type'] : null,
                    $guest['identification_no'] ? __('tour-detail.guest_id_number').': '.$guest['identification_no'] : null,
                ]);

                return '<li>'.nl2br(e(implode(' | ', $parts))).'</li>';
            })
            ->implode('');

        return '<p>'.e(__('activities.detail.order.guest_manifest_summary', [
            'count' => count($guests),
            'total' => $guestCount,
        ])).'</p><ol>'.$items.'</ol>';
    }

    private function filterOrderPayload(array $payload): array
    {
        static $columns = null;

        if ($columns === null) {
            $columns = collect(Schema::getColumnListing('orders'))->flip()->all();
        }

        return collect($payload)
            ->filter(fn ($value, $key) => array_key_exists($key, $columns))
            ->all();
    }

    private function generateActivityOrderNumber(Carbon $travelDate): string
    {
        $base = 'ACT-'.$travelDate->format('ymd').'-';
        $lastSuffix = Orders::where('service', Orders::PUBLIC_ACTIVITY_SERVICE)
            ->where('orderno', 'like', $base.'%')
            ->pluck('orderno')
            ->map(function ($orderNumber) use ($base) {
                $suffix = str_replace($base, '', $orderNumber);

                return ctype_digit($suffix) ? (int) $suffix : 0;
            })
            ->max() ?? 0;

        do {
            $lastSuffix++;
            $orderNumber = $base.str_pad((string) $lastSuffix, 3, '0', STR_PAD_LEFT);
        } while (Orders::where('orderno', $orderNumber)->exists());

        return $orderNumber;
    }

}
