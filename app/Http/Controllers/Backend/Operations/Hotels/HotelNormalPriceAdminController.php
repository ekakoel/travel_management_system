<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelNormalPriceRequest;
use App\Http\Requests\UpdateHotelNormalPriceRequest;
use App\Models\HotelPrice;
use App\Models\HotelRoom;
use App\Models\Hotels;
use App\Models\UserLog;
use App\Services\Hotels\HotelNormalPriceOverlapValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class HotelNormalPriceAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified','type:admin']);
    }

    public function create($id)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $hotels = Hotels::findOrFail($id);
        $rooms = HotelRoom::where('hotels_id', $id)
            ->orderBy('rooms')
            ->get(['id', 'hotels_id', 'rooms', 'status']);

        return view('backend.operations.hotels.forms.normal-price-create', [
            'hotels' => $hotels,
            'rooms' => $rooms,
            'hotelContext' => Crypt::encryptString((string) $hotels->id),
        ]);
    }

    public function store(StoreHotelNormalPriceRequest $request, HotelNormalPriceOverlapValidator $overlapValidator)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $validated = $request->validated();
        $hotelId = (int) $request->resolvedHotelId();

        DB::transaction(function () use ($request, $validated, $hotelId, $overlapValidator): void {
            foreach ($validated['rooms_id'] as $index => $roomId) {
                $startDate = date('Y-m-d', strtotime($validated['start_date'][$index]));
                $endDate = date('Y-m-d', strtotime($validated['end_date'][$index]));

                $overlapValidator->ensureAvailable($hotelId, (int) $roomId, $startDate, $endDate);

                $price = HotelPrice::create([
                    'hotels_id' => $hotelId,
                    'rooms_id' => $roomId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'contract_rate' => $validated['contract_rate'][$index],
                    'markup' => $validated['markup'][$index],
                    'kick_back' => $validated['kick_back'][$index] ?? 0,
                    'author' => auth()->id(),
                ]);

                UserLog::create([
                    'action' => 'Add Normal Price',
                    'service' => 'Hotel',
                    'subservice' => 'Normal Price',
                    'subservice_id' => $price->id,
                    'page' => 'add-hotel-price',
                    'user_id' => auth()->id(),
                    'user_ip' => $request->getClientIp(),
                    'note' => 'Add normal price to Hotel id : '.$hotelId.', Room id : '.$roomId.', Start date : '.$startDate.', End date : '.$endDate.', Markup : '.$validated['markup'][$index].', Contract rate : '.$validated['contract_rate'][$index],
                ]);
            }
        }, 3);

        return $this->redirectToHotelDetail($hotelId, 'normalPrice')->with('success', 'Price added successfully');
    }

    public function update(UpdateHotelNormalPriceRequest $request, $id, HotelNormalPriceOverlapValidator $overlapValidator)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $validated = $request->validated();
        $price = HotelPrice::findOrFail($id);
        $hotelId = (int) $price->hotels_id;
        $startDate = date('Y-m-d', strtotime($validated['start_date']));
        $endDate = date('Y-m-d', strtotime($validated['end_date']));

        abort_unless((int) $validated['hotels_id'] === $hotelId, 404);

        DB::transaction(function () use ($price, $validated, $hotelId, $startDate, $endDate, $overlapValidator): void {
            $lockedPrice = HotelPrice::query()->lockForUpdate()->findOrFail($price->id);
            $overlapValidator->ensureAvailable(
                $hotelId,
                (int) $validated['rooms_id'],
                $startDate,
                $endDate,
                (int) $lockedPrice->id
            );

            $lockedPrice->update([
                'rooms_id' => $validated['rooms_id'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'markup' => $validated['markup'],
                'kick_back' => $validated['kick_back'] ?? 0,
                'contract_rate' => $validated['contract_rate'],
            ]);
        }, 3);

        UserLog::create([
            'action' => 'Update Normal Price',
            'service' => 'Hotel',
            'subservice' => 'Normal Price',
            'subservice_id' => $id,
            'page' => 'detail-hotel#normal-price',
            'user_id' => auth()->id(),
            'user_ip' => $request->getClientIp(),
            'note' => 'Update normal price to Hotel id : '.$hotelId.', Room id : '.$validated['rooms_id'].', Start date : '.$startDate.', End date : '.$endDate.', Markup : '.$validated['markup'].', Contract rate : '.$validated['contract_rate'],
        ]);

        return $this->redirectToHotelDetail($hotelId, 'normalPrice')->with('success', 'The Price has been updated!');
    }

    public function destroy(\Illuminate\Http\Request $request, $id)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $price = HotelPrice::findOrFail($id);
        $hotel = Hotels::findOrFail($price->hotels_id);
        $room = HotelRoom::findOrFail($price->rooms_id);

        UserLog::create([
            'action' => 'Remove',
            'service' => 'Hotel',
            'subservice' => 'Normal Price',
            'subservice_id' => $price->id,
            'page' => 'detail-hotel#normal-price',
            'user_id' => Auth::user()->id,
            'user_ip' => $request->getClientIp(),
            'note' => 'Remove normal price Hotel id : '.$hotel->id.', Room id : '.$room->id.', Price id : '.$id,
        ]);

        $price->delete();

        return $this->redirectToHotelDetail($hotel->id, 'normalPrice')->with('success', 'The Price has been successfully deleted!');
    }

    private function canManage(): bool
    {
        return Gate::allows('posDev') || Gate::allows('posAuthor');
    }

    private function redirectToHotelsIndexWithError(?string $message = null)
    {
        $message = $message ?? __('messages.You are not authorized to perform this action.');

        return redirect()->route('admin.hotels.index')->with('error', $message);
    }

    private function redirectToHotelDetail($hotelId, ?string $anchor = null)
    {
        $url = route('admin.hotels.show', $hotelId);

        if ($anchor) {
            $url .= '#'.ltrim($anchor, '#');
        }

        return redirect($url);
    }
}
