<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelPromoRequest;
use App\Http\Requests\UpdateHotelPromoRequest;
use App\Models\HotelPromo;
use App\Models\HotelRoom;
use App\Models\Hotels;
use App\Models\UserLog;
use Illuminate\Support\Facades\Gate;

class HotelPromoAdminController extends Controller
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

        $hotel = Hotels::findOrFail($id);

        return view('backend.operations.hotels.forms.promo-create')->with('hotel', $hotel);
    }

    public function edit($id)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $promo = HotelPromo::findOrFail($id);
        $hotel = Hotels::with('rooms')->findOrFail($promo->hotels_id);
        $rooms = HotelRoom::where('hotels_id', $hotel->id)->orderBy('created_at', 'desc')->get();

        return view('backend.operations.hotels.forms.promo-edit', [
            'promo' => $promo,
            'hotel' => $hotel,
            'rooms' => $rooms,
        ]);
    }

    public function store(StoreHotelPromoRequest $request)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        HotelPromo::create([
            'promotion_type' => $request->promotion_type,
            'quotes' => $request->quotes,
            'hotels_id' => $request->hotels_id,
            'rooms_id' => $request->rooms_id,
            'name' => $request->name,
            'book_periode_start' => date('Y-m-d', strtotime($request->book_periode_start)),
            'book_periode_end' => date('Y-m-d', strtotime($request->book_periode_end)),
            'periode_start' => date('Y-m-d', strtotime($request->periode_start)),
            'periode_end' => date('Y-m-d', strtotime($request->periode_end)),
            'contract_rate' => $request->contract_rate,
            'minimum_stay' => $request->minimum_stay,
            'markup' => $request->markup,
            'booking_code' => $request->booking_code,
            'benefits' => $request->benefits,
            'benefits_traditional' => $request->benefits_traditional,
            'benefits_simplified' => $request->benefits_simplified,
            'email_status' => 0,
            'send_to_spesific_email' => 0,
            'spesific_email' => '',
            'status' => 'Draft',
            'author' => auth()->id(),
            'include' => $request->include,
            'include_traditional' => $request->include_traditional,
            'include_simplified' => $request->include_simplified,
            'additional_info' => $request->additional_info,
            'additional_info_traditional' => $request->additional_info_traditional,
            'additional_info_simplified' => $request->additional_info_simplified,
            'cancellation_policy' => $request->cancellation_policy,
            'cancellation_policy_traditional' => $request->cancellation_policy_traditional,
            'cancellation_policy_simplified' => $request->cancellation_policy_simplified,
        ]);

        return $this->redirectToHotelDetail($request->hotels_id, 'promo')->with('success', 'Promo added successfully');
    }

    public function update(UpdateHotelPromoRequest $request, $id)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $promo = HotelPromo::findOrFail($id);
        $hotelId = $request->hotels_id;
        $bookPeriodeStart = date('Y-m-d', strtotime($request->book_periode_start));
        $bookPeriodeEnd = date('Y-m-d', strtotime($request->book_periode_end));
        $periodeStart = date('Y-m-d', strtotime($request->periode_start));
        $periodeEnd = date('Y-m-d', strtotime($request->periode_end));

        $promo->update([
            'hotels_id' => $request->hotels_id,
            'promotion_type' => $request->promotion_type,
            'quotes' => $request->quotes,
            'rooms_id' => $request->rooms_id,
            'name' => $request->name,
            'book_periode_start' => $bookPeriodeStart,
            'book_periode_end' => $bookPeriodeEnd,
            'periode_start' => $periodeStart,
            'periode_end' => $periodeEnd,
            'minimum_stay' => $request->minimum_stay,
            'contract_rate' => $request->contract_rate,
            'markup' => $request->markup,
            'booking_code' => $request->booking_code,
            'benefits' => $request->benefits,
            'benefits_traditional' => $request->benefits_traditional,
            'benefits_simplified' => $request->benefits_simplified,
            'include' => $request->include,
            'include_traditional' => $request->include_traditional,
            'include_simplified' => $request->include_simplified,
            'additional_info' => $request->additional_info,
            'additional_info_traditional' => $request->additional_info_traditional,
            'additional_info_simplified' => $request->additional_info_simplified,
            'status' => $request->status,
            'author' => auth()->id(),
        ]);

        UserLog::create([
            'action' => 'Update Promo',
            'service' => 'Hotel',
            'subservice' => 'Promo',
            'subservice_id' => $id,
            'page' => 'detail-hotel#promos',
            'user_id' => auth()->id(),
            'user_ip' => $request->getClientIp(),
            'note' => 'Update Promo on Hotel id : '.$request->hotels_id.', Room id : '.$request->rooms_id.', Promo id : '.$id,
        ]);

        return $this->redirectToHotelDetail($hotelId, 'promo')->with('success', 'The Promo has been updated!');
    }

    public function destroy(\Illuminate\Http\Request $request, $id)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $promo = HotelPromo::findOrFail($id);
        $hotelId = $promo->hotels_id;

        UserLog::create([
            'action' => 'Remove',
            'service' => 'Hotel',
            'subservice' => 'Promo',
            'subservice_id' => $id,
            'page' => 'detail-hotel#promo',
            'user_id' => auth()->id(),
            'user_ip' => $request->getClientIp(),
            'note' => 'Remove Promo on hotel : '.$hotelId.', Promo id : '.$id,
        ]);

        $promo->delete();

        return $this->redirectToHotelDetail($hotelId, 'promo')->with('success', 'The Promo has been successfully deleted!');
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
