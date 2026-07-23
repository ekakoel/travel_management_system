<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelNormalPriceRequest;
use App\Http\Requests\UpdateHotelNormalPriceRequest;
use App\Models\HotelPrice;
use App\Models\HotelRoom;
use App\Models\Hotels;
use App\Models\UserLog;
use App\Models\UsdRates;
use Illuminate\Support\Facades\Auth;
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
        $usdrates = UsdRates::where('name', 'USD')->first();

        return view('backend.operations.hotels.forms.normal-price-create', [
            'hotels' => $hotels,
            'usdrates' => $usdrates,
        ]);
    }

    public function edit($id)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $price = HotelPrice::with(['rooms'])->findOrFail($id);
        $hotels = Hotels::findOrFail($price->hotels_id);
        $rooms = HotelRoom::where('hotels_id', $hotels->id)->orderBy('created_at', 'desc')->get();
        $usdrates = UsdRates::where('name', 'USD')->first();

        return view('backend.operations.hotels.forms.normal-price-edit', [
            'price' => $price,
            'hotels' => $hotels,
            'rooms' => $rooms,
            'usdrates' => $usdrates,
        ]);
    }

    public function store(StoreHotelNormalPriceRequest $request)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $rowCount = count($request->rooms_id);

        for ($i = 0; $i < $rowCount; $i++) {
            HotelPrice::create([
                'hotels_id' => $request->hotels_id,
                'rooms_id' => $request->rooms_id[$i],
                'start_date' => date('Y-m-d', strtotime($request->start_date[$i])),
                'end_date' => date('Y-m-d', strtotime($request->end_date[$i])),
                'contract_rate' => $request->contract_rate[$i],
                'markup' => $request->markup[$i],
                'kick_back' => $request->kick_back[$i],
                'author' => auth()->id(),
            ]);
        }

        return $this->redirectToHotelDetail($request->hotels_id, 'normalPrice')->with('success', 'Price added successfully');
    }

    public function update(UpdateHotelNormalPriceRequest $request, $id)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $price = HotelPrice::findOrFail($id);
        $hotelId = $request->hotels_id;
        $startDate = date('Y-m-d', strtotime($request->start_date));
        $endDate = date('Y-m-d', strtotime($request->end_date));

        $price->update([
            'hotels_id' => $request->hotels_id,
            'rooms_id' => $request->rooms_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'markup' => $request->markup,
            'kick_back' => $request->kick_back,
            'contract_rate' => $request->contract_rate,
        ]);

        UserLog::create([
            'action' => 'Update Normal Price',
            'service' => 'Hotel',
            'subservice' => 'Normal Price',
            'subservice_id' => $id,
            'page' => 'detail-hotel#normal-price',
            'user_id' => auth()->id(),
            'user_ip' => $request->getClientIp(),
            'note' => 'Update normal price to Hotel id : '.$request->hotels_id.', Room id : '.$request->rooms_id.', Start date : '.$startDate.', End date : '.$endDate.', Markup : '.$request->markup.', Contract rate : '.$request->contract_rate,
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

    private function redirectToHotelsIndexWithError(string $message = 'Akses ditolak')
    {
        return redirect()->route('hotels-admin.index')->with('error', $message);
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
