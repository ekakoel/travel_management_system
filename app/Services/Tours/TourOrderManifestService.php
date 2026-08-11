<?php

namespace App\Services\Tours;

use App\Models\Guests;
use App\Models\InvoiceAdmin;
use App\Models\OrderLog;
use App\Models\Orders;
use App\Models\Tours;
use App\Services\Pricing\OrderPricingSnapshotWriter;
use App\Support\MoneyFormatter;
use App\Support\Pricing\FixedScale;
use App\ValueObjects\Money;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class TourOrderManifestService
{
    public function __construct(
        private readonly TourPackagePricingService $pricing,
        private readonly OrderPricingSnapshotWriter $snapshotWriter,
        private readonly MoneyFormatter $formatter,
    ) {
    }

    public function addGuest(Orders $order, array $attributes, int $actorId, ?string $ipAddress): Guests
    {
        $guest = null;

        $this->mutate($order, $actorId, $ipAddress, 'tour_guest_added', function (Orders $lockedOrder) use ($attributes, &$guest) {
            $guest = Guests::create($attributes + [
                'order_id' => $lockedOrder->id,
                'rsv_id' => $lockedOrder->rsv_id,
            ]);
        });

        return $guest->fresh();
    }

    public function updateGuest(Guests $guest, array $attributes, int $actorId, ?string $ipAddress): Guests
    {
        $order = $this->orderForGuest($guest);

        $this->mutate($order, $actorId, $ipAddress, 'tour_guest_updated', function (Orders $lockedOrder) use ($guest, $attributes) {
            $lockedGuest = Guests::query()->lockForUpdate()->findOrFail($guest->id);
            $this->assertGuestBelongsToOrder($lockedGuest, $lockedOrder);
            $lockedGuest->update($attributes);
        });

        return $guest->fresh();
    }

    public function deleteGuest(Guests $guest, int $actorId, ?string $ipAddress): void
    {
        $order = $this->orderForGuest($guest);

        $this->mutate($order, $actorId, $ipAddress, 'tour_guest_removed', function (Orders $lockedOrder) use ($guest) {
            $lockedGuest = Guests::query()->lockForUpdate()->findOrFail($guest->id);
            $this->assertGuestBelongsToOrder($lockedGuest, $lockedOrder);
            $lockedGuest->delete();
        });
    }

    private function mutate(
        Orders $order,
        int $actorId,
        ?string $ipAddress,
        string $reason,
        Closure $mutation,
    ): void {
        DB::transaction(function () use ($order, $actorId, $ipAddress, $reason, $mutation) {
            $lockedOrder = Orders::query()->lockForUpdate()->findOrFail($order->id);
            abort_unless($lockedOrder->service === Orders::PUBLIC_TOUR_SERVICE, 409);
            abort_unless(in_array($lockedOrder->status, ['Draft', 'Pending'], true), 409);
            abort_unless(!$lockedOrder->handled_by || (int) $lockedOrder->handled_by === $actorId, 403);

            if ($lockedOrder->rsv_id
                && InvoiceAdmin::where('rsv_id', $lockedOrder->rsv_id)->lockForUpdate()->exists()) {
                throw ValidationException::withMessages([
                    'guests' => 'The Tour manifest cannot be changed after an invoice has been created.',
                ]);
            }

            $mutation($lockedOrder);
            $guests = $this->guestsForOrder($lockedOrder, true);
            $guestCount = $guests->count();
            if ($guestCount < 2 || $guestCount > 200) {
                throw ValidationException::withMessages([
                    'guests' => "A Tour Package order requires between 2 and 200 guests; {$guestCount} would remain.",
                ]);
            }

            $tour = Tours::query()->lockForUpdate()->findOrFail($lockedOrder->service_id);
            $promotionId = $this->promotionId($lockedOrder);
            $bookingCode = filled($lockedOrder->bookingcode) ? (string) $lockedOrder->bookingcode : null;
            $quote = $this->pricing->quote(
                tour: $tour,
                guestCount: $guestCount,
                serviceDate: Carbon::parse($lockedOrder->travel_date ?: $lockedOrder->checkin),
                preferredPriceId: null,
                promotionId: $promotionId,
                bookingCode: $bookingCode,
                actorId: $lockedOrder->user_id,
                lockForUpdate: true,
                existingOrderId: $lockedOrder->id,
            );
            $pricing = $quote->toArray();
            $unitUsd = $this->formatter->decimal(Money::usdCents($pricing['unit_price_usd_minor']));
            $grossUsd = $this->formatter->decimal(Money::usdCents($pricing['gross_total_usd_minor']));
            $discountUsd = $this->formatter->decimal(Money::usdCents($pricing['discount_total_usd_minor']));
            $finalUsd = $this->formatter->decimal(Money::usdCents($pricing['final_total_usd_minor']));
            $selectedDiscount = $pricing['selected_discount'] ?? null;
            $isBookingCode = ($selectedDiscount['source'] ?? null) === 'booking_code';
            $isPromotion = ($selectedDiscount['source'] ?? null) === 'promotion';
            $primaryGuest = $guests->first();

            $lockedOrder->forceFill([
                'number_of_guests' => $guestCount,
                'guest_detail' => $this->guestManifestHtml($guests),
                'pickup_name' => $primaryGuest?->name,
                'pickup_phone' => $primaryGuest?->phone,
                'price_id' => $pricing['price_id'],
                'price_pax' => $unitUsd,
                'normal_price' => $grossUsd,
                'price_total' => $grossUsd,
                'discounts' => $discountUsd,
                'bookingcode_disc' => $isBookingCode ? $discountUsd : '0.00',
                // Keep the submitted promotion reference so a later manifest change
                // evaluates the same discount candidates, even when another candidate wins.
                'promotion' => $lockedOrder->promotion,
                'promotion_disc' => $isPromotion ? json_encode([$discountUsd]) : null,
                'final_price' => $finalUsd,
                'order_tax' => (string) $pricing['tax_amount_idr'],
                'usd_rate' => FixedScale::formatDecimal(
                    $pricing['rate_value_scaled'],
                    $pricing['rate_value_scale']
                ),
            ])->save();

            $this->snapshotWriter->commit($lockedOrder, $quote, $actorId, $reason);

            OrderLog::create([
                'order_id' => $lockedOrder->id,
                'action' => 'Reprice Tour Guest Manifest',
                'url' => $ipAddress,
                'method' => 'Update',
                'agent' => $lockedOrder->name,
                'admin' => $actorId,
            ]);
        });
    }

    private function guestsForOrder(Orders $order, bool $lockForUpdate = false)
    {
        $query = Guests::query()
            ->where(function ($guestQuery) use ($order) {
                $guestQuery->where('order_id', $order->id);
                if ($order->rsv_id) {
                    $guestQuery->orWhere(function ($legacyQuery) use ($order) {
                        $legacyQuery->whereNull('order_id')->where('rsv_id', $order->rsv_id);
                    });
                }
            })
            ->orderBy('id');

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        return $query->get();
    }

    private function orderForGuest(Guests $guest): Orders
    {
        $order = $guest->order_id
            ? Orders::find($guest->order_id)
            : Orders::where('rsv_id', $guest->rsv_id)
                ->where('service', Orders::PUBLIC_TOUR_SERVICE)
                ->first();

        abort_unless($order && $order->service === Orders::PUBLIC_TOUR_SERVICE, 409);

        return $order;
    }

    private function assertGuestBelongsToOrder(Guests $guest, Orders $order): void
    {
        $belongs = (int) $guest->order_id === (int) $order->id
            || ($guest->order_id === null && (int) $guest->rsv_id === (int) $order->rsv_id);

        abort_unless($belongs, 404);
    }

    private function promotionId(Orders $order): ?int
    {
        $promotionIds = json_decode((string) $order->promotion, true);

        return is_array($promotionIds) && isset($promotionIds[0])
            ? (int) $promotionIds[0]
            : null;
    }

    private function guestManifestHtml($guests): string
    {
        return '<ol>'.$guests->values()->map(function (Guests $guest, int $index) {
            $parts = array_filter([
                $guest->name,
                $guest->age,
                $guest->sex,
                $guest->phone ? 'Phone: '.$guest->phone : null,
            ]);

            return '<li>'.e(($index + 1).'. '.implode(' | ', $parts)).'</li>';
        })->implode('').'</ol>';
    }
}
