<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelAdditionalChargeRequest;
use App\Http\Requests\UpdateHotelAdditionalChargeRequest;
use App\Models\Hotels;
use App\Models\OptionalRate;
use App\Services\Hotels\HotelAuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class HotelAdditionalChargeAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'type:admin']);
    }

    public function store(StoreHotelAdditionalChargeRequest $request, HotelAuditService $audit)
    {
        $validated = $request->validated();
        $hotel = Hotels::findOrFail((int) $validated['hotel_id']);

        $additionalCharge = DB::transaction(function () use ($request, $validated, $hotel, $audit): OptionalRate {
            $mandatoryDates = $this->mandatoryDates($validated);
            $additionalCharge = OptionalRate::create([
                'type' => $validated['type'],
                'hotels_id' => $hotel->id,
                'name' => $validated['name'],
                'service' => 'Hotel',
                'service_id' => $hotel->id,
                'markup' => $validated['markup'],
                'mandatory' => $mandatoryDates['mandatory'],
                'must_buy_start' => $mandatoryDates['start'],
                'must_buy_end' => $mandatoryDates['end'],
                'contract_rate' => $validated['contract_rate'],
                'description' => $validated['description'] ?? null,
                'description_traditional' => $validated['description_traditional'] ?? null,
                'description_simplified' => $validated['description_simplified'] ?? null,
            ]);

            $audit->userLog(
                $request,
                'Add Additional Charge',
                'Additional Charge',
                $additionalCharge->id,
                'detail-hotel#additional-charge',
                'Add additional charge to Hotel id : '.$hotel->id.', Optional rate id : '.$additionalCharge->id
            );

            return $additionalCharge;
        }, 3);

        return $this->redirectToHotelDetail((int) $additionalCharge->hotels_id)
            ->with('success', 'Additional Charge added successfully');
    }

    public function update(UpdateHotelAdditionalChargeRequest $request, $id, HotelAuditService $audit)
    {
        $validated = $request->validated();
        $additionalCharge = OptionalRate::findOrFail($id);
        $hotelId = (int) ($additionalCharge->hotels_id ?: $additionalCharge->service_id);

        DB::transaction(function () use ($request, $validated, $additionalCharge, $hotelId, $audit): void {
            $lockedCharge = OptionalRate::query()->lockForUpdate()->findOrFail($additionalCharge->id);
            $mandatoryDates = $this->mandatoryDates($validated);

            $lockedCharge->update([
                'type' => $validated['type'],
                'name' => $validated['name'],
                'service' => 'Hotel',
                'service_id' => $hotelId,
                'hotels_id' => $hotelId,
                'description' => $validated['description'] ?? null,
                'description_traditional' => $validated['description_traditional'] ?? null,
                'description_simplified' => $validated['description_simplified'] ?? null,
                'mandatory' => $mandatoryDates['mandatory'],
                'must_buy_start' => $mandatoryDates['start'],
                'must_buy_end' => $mandatoryDates['end'],
                'markup' => $validated['markup'],
                'contract_rate' => $validated['contract_rate'],
            ]);

            $audit->userLog(
                $request,
                'Update Additional Charge',
                'Additional Charge',
                $lockedCharge->id,
                'detail-hotel#additional-charge',
                'Update additional charge to Hotel id : '.$hotelId.', Optional rate id : '.$lockedCharge->id
            );
        }, 3);

        return $this->redirectToHotelDetail($hotelId)
            ->with('success', 'The Additional Charge has been updated!');
    }

    public function destroy(Request $request, $id, HotelAuditService $audit)
    {
        if (! Gate::any(['posDev', 'posAuthor', 'posAdm'])) {
            return redirect()
                ->route('admin.hotels.index')
                ->with('error', __('messages.You are not authorized to perform this action.'));
        }

        $additionalCharge = OptionalRate::findOrFail($id);
        $hotelId = (int) ($additionalCharge->hotels_id ?: $additionalCharge->service_id);

        DB::transaction(function () use ($request, $additionalCharge, $hotelId, $audit): void {
            $lockedCharge = OptionalRate::query()->lockForUpdate()->findOrFail($additionalCharge->id);
            $audit->userLog(
                $request,
                'Remove',
                'Additional Charge',
                $lockedCharge->id,
                'detail-hotel#additional-charge',
                'Remove additional charge on Hotel id : '.$hotelId.', Optional rate id : '.$lockedCharge->id
            );

            $lockedCharge->delete();
        }, 3);

        return $this->redirectToHotelDetail($hotelId)
            ->with('success', 'The Additional Charge has been successfully deleted!');
    }

    private function mandatoryDates(array $validated): array
    {
        $mandatory = (int) ($validated['mandatory'] ?? 0) === 1 ? 1 : 0;

        if ($mandatory !== 1) {
            return ['mandatory' => 0, 'start' => null, 'end' => null];
        }

        return [
            'mandatory' => 1,
            'start' => Carbon::parse($validated['mandatory_start'])->toDateString(),
            'end' => Carbon::parse($validated['mandatory_end'])->toDateString(),
        ];
    }

    private function redirectToHotelDetail(int $hotelId)
    {
        return redirect(route('admin.hotels.show', $hotelId).'#additional-charge');
    }
}
