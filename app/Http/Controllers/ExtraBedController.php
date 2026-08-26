<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExtraBedRequest;
use App\Http\Requests\UpdateExtraBedRequest;
use App\Models\ExtraBed;
use App\Models\Hotels;
use App\Services\Hotels\HotelAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ExtraBedController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function func_add_extra_bed(StoreExtraBedRequest $request, HotelAuditService $audit)
    {
        $validated = $request->validated();
        $hotel = Hotels::findOrFail((int) $validated['hotels_id']);

        $extraBed = DB::transaction(function () use ($request, $validated, $hotel, $audit): ExtraBed {
            $extraBed = ExtraBed::create([
                'name' => 'Extra Bed',
                'hotels_id' => $hotel->id,
                'type' => $validated['type'],
                'min_age' => $validated['min_age'] ?? null,
                'max_age' => $validated['max_age'] ?? null,
                'description' => $validated['description'] ?? null,
                'contract_rate' => $validated['contract_rate'],
                'markup' => $validated['markup'],
            ]);

            $audit->userLog(
                $request,
                'Add Extra Bed',
                'Extra Bed',
                $extraBed->id,
                'detail-hotel#extra-bed',
                'Add Extra Bed to Hotel id : '.$hotel->id.', Extra Bed id : '.$extraBed->id
            );

            return $extraBed;
        }, 3);

        return $this->redirectToHotelDetail($extraBed->hotels_id)->with('success', 'Extra bed successfully added');
    }

    public function fedit_extra_bed(UpdateExtraBedRequest $request, $id, HotelAuditService $audit)
    {
        $validated = $request->validated();
        $extraBed = ExtraBed::findOrFail($id);
        $hotelId = (int) $extraBed->hotels_id;

        DB::transaction(function () use ($request, $validated, $extraBed, $hotelId, $audit): void {
            $lockedExtraBed = ExtraBed::query()->lockForUpdate()->findOrFail($extraBed->id);
            $lockedExtraBed->update([
                'type' => $validated['type'],
                'min_age' => $validated['min_age'] ?? null,
                'max_age' => $validated['max_age'] ?? null,
                'description' => $validated['description'] ?? null,
                'contract_rate' => $validated['contract_rate'],
                'markup' => $validated['markup'],
            ]);

            $audit->userLog(
                $request,
                'Update Extra Bed',
                'Extra Bed',
                $lockedExtraBed->id,
                'detail-hotel#extra-bed',
                'Update Extra Bed to Hotel id : '.$hotelId.', Extra Bed id : '.$lockedExtraBed->id
            );
        }, 3);

        return $this->redirectToHotelDetail($hotelId)->with('success', 'Extra bed has been updated!');
    }

    public function fdelete_extra_bed(Request $request, $id, HotelAuditService $audit)
    {
        if (! Gate::any(['posDev', 'posAuthor'])) {
            return redirect()
                ->route('admin.hotels.index')
                ->with('error', __('messages.You are not authorized to perform this action.'));
        }

        $extraBed = ExtraBed::findOrFail($id);
        $hotelId = (int) $extraBed->hotels_id;

        DB::transaction(function () use ($request, $extraBed, $hotelId, $audit): void {
            $lockedExtraBed = ExtraBed::query()->lockForUpdate()->findOrFail($extraBed->id);
            $audit->userLog(
                $request,
                'Remove',
                'Extra Bed',
                $lockedExtraBed->id,
                'detail-hotel#extra-bed',
                'Remove Extra Bed on Hotel id : '.$hotelId.', Extra Bed id : '.$lockedExtraBed->id
            );

            $lockedExtraBed->delete();
        }, 3);

        return $this->redirectToHotelDetail($hotelId)->with('success', 'Extra bed has been successfully deleted!');
    }

    private function redirectToHotelDetail(int $hotelId)
    {
        return redirect(route('admin.hotels.show', $hotelId).'#extra-bed');
    }
}
