<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelPromoRequest;
use App\Http\Requests\UpdateHotelPromoRequest;
use App\Models\HotelPromo;
use App\Models\HotelRoom;
use App\Models\Hotels;
use App\Models\UserLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

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
        $rooms = HotelRoom::where('hotels_id', $hotel->id)
            ->orderBy('rooms')
            ->get(['id', 'rooms', 'hotels_id']);

        return view('backend.operations.hotels.forms.promo-create', [
            'hotel' => $hotel,
            'rooms' => $rooms,
            'hotelContext' => Crypt::encryptString((string) $hotel->id),
            'initialStatus' => 'Draft',
        ]);
    }

    public function edit($id)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $promo = HotelPromo::with(['hotels', 'rooms'])->findOrFail($id);
        $hotel = $promo->hotels ?: Hotels::findOrFail($promo->hotels_id);
        $rooms = HotelRoom::where('hotels_id', $hotel->id)
            ->orderBy('rooms')
            ->get(['id', 'rooms', 'hotels_id']);

        return view('backend.operations.hotels.forms.promo-edit', [
            'promo' => $promo,
            'hotel' => $hotel,
            'rooms' => $rooms,
            'hotelContext' => Crypt::encryptString((string) $hotel->id),
        ]);
    }

    public function store(StoreHotelPromoRequest $request)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }
        $validated = $request->validated();
        $hotelId = (int) $request->resolvedHotelId();

        $promo = DB::transaction(function () use ($request, $validated, $hotelId) {
            $promo = HotelPromo::create([
                'promotion_type' => $validated['promotion_type'] ?? null,
                'quotes' => $validated['quotes'] ?? null,
                'hotels_id' => $hotelId,
                'rooms_id' => $validated['rooms_id'],
                'name' => $validated['name'],
                'book_periode_start' => date('Y-m-d', strtotime($validated['book_periode_start'])),
                'book_periode_end' => date('Y-m-d', strtotime($validated['book_periode_end'])),
                'periode_start' => date('Y-m-d', strtotime($validated['periode_start'])),
                'periode_end' => date('Y-m-d', strtotime($validated['periode_end'])),
                'contract_rate' => $validated['contract_rate'],
                'minimum_stay' => $validated['minimum_stay'],
                'markup' => $validated['markup'],
                'booking_code' => $validated['booking_code'] ?? null,
                'benefits' => $validated['benefits'] ?? null,
                'benefits_traditional' => $validated['benefits_traditional'] ?? null,
                'benefits_simplified' => $validated['benefits_simplified'] ?? null,
                'email_status' => 0,
                'send_to_specific_email' => 1,
                'specific_email' => '',
                'status' => 'Draft',
                'author' => auth()->id(),
                'include' => $validated['include'] ?? null,
                'include_traditional' => $validated['include_traditional'] ?? null,
                'include_simplified' => $validated['include_simplified'] ?? null,
                'additional_info' => $validated['additional_info'] ?? null,
                'additional_info_traditional' => $validated['additional_info_traditional'] ?? null,
                'additional_info_simplified' => $validated['additional_info_simplified'] ?? null,
            ]);

            UserLog::create([
                'action' => 'Add Promo',
                'service' => 'Hotel',
                'subservice' => 'Promo',
                'subservice_id' => $promo->id,
                'page' => 'detail-hotel#promo',
                'user_id' => auth()->id(),
                'user_ip' => $request->getClientIp(),
                'note' => 'Add Promo to Hotel id : '.$hotelId.', Room id : '.$validated['rooms_id'].', Promo id : '.$promo->id,
            ]);

            return $promo;
        }, 3);

        return $this->redirectToHotelDetail($promo->hotels_id, 'promo')->with('success', 'Promo added successfully');
    }

    public function update(UpdateHotelPromoRequest $request, $id)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $validated = $request->validated();
        $promo = HotelPromo::findOrFail($id);
        $hotelId = (int) $promo->hotels_id;

        abort_unless((int) $request->resolvedHotelId() === $hotelId, 404);

        DB::transaction(function () use ($request, $validated, $promo, $hotelId, $id): void {
            $lockedPromo = HotelPromo::query()->lockForUpdate()->findOrFail($promo->id);

            $lockedPromo->update([
                'hotels_id' => $hotelId,
                'promotion_type' => $validated['promotion_type'] ?? null,
                'quotes' => $validated['quotes'] ?? null,
                'rooms_id' => $validated['rooms_id'],
                'name' => $validated['name'],
                'book_periode_start' => date('Y-m-d', strtotime($validated['book_periode_start'])),
                'book_periode_end' => date('Y-m-d', strtotime($validated['book_periode_end'])),
                'periode_start' => date('Y-m-d', strtotime($validated['periode_start'])),
                'periode_end' => date('Y-m-d', strtotime($validated['periode_end'])),
                'minimum_stay' => $validated['minimum_stay'],
                'contract_rate' => $validated['contract_rate'],
                'markup' => $validated['markup'],
                'booking_code' => $validated['booking_code'] ?? null,
                'benefits' => $validated['benefits'] ?? null,
                'benefits_traditional' => $validated['benefits_traditional'] ?? null,
                'benefits_simplified' => $validated['benefits_simplified'] ?? null,
                'include' => $validated['include'] ?? null,
                'include_traditional' => $validated['include_traditional'] ?? null,
                'include_simplified' => $validated['include_simplified'] ?? null,
                'additional_info' => $validated['additional_info'] ?? null,
                'additional_info_traditional' => $validated['additional_info_traditional'] ?? null,
                'additional_info_simplified' => $validated['additional_info_simplified'] ?? null,
                'status' => $validated['status'],
                'author' => auth()->id(),
            ]);

            UserLog::create([
                'action' => 'Update Promo',
                'service' => 'Hotel',
                'subservice' => 'Promo',
                'subservice_id' => $id,
                'page' => 'detail-hotel#promo',
                'user_id' => auth()->id(),
                'user_ip' => $request->getClientIp(),
                'note' => 'Update Promo on Hotel id : '.$hotelId.', Room id : '.$validated['rooms_id'].', Promo id : '.$id,
            ]);
        }, 3);

        return $this->redirectToHotelDetail($hotelId, 'promo')->with('success', 'The Promo has been updated!');
    }

    public function updateStatus(Request $request, HotelPromo $promo): JsonResponse
    {
        if (! $this->canManage()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['Active', 'Draft'])],
        ]);

        $promo = DB::transaction(function () use ($request, $promo, $validated) {
            $previousStatus = $promo->status;
            $promo->forceFill([
                'status' => $validated['status'],
                'author' => auth()->id(),
            ])->save();

            UserLog::create([
                'action' => 'Update Promo Status',
                'service' => 'Hotel',
                'subservice' => 'Promo',
                'subservice_id' => $promo->id,
                'page' => 'detail-hotel#promo',
                'user_id' => auth()->id(),
                'user_ip' => $request->getClientIp(),
                'note' => 'Update Promo status on Hotel id : '.$promo->hotels_id.', Room id : '.$promo->rooms_id.', Promo id : '.$promo->id.', Status : '.$previousStatus.' -> '.$promo->status,
            ]);

            return $promo->refresh();
        });

        return response()->json([
            'id' => $promo->id,
            'status' => $promo->status,
            'next_status' => $promo->status === 'Active' ? 'Draft' : 'Active',
            'tone' => $this->promoStatusTone($promo),
            'message' => 'Promo status updated.',
        ]);
    }

    public function destroy(Request $request, $id)
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

    private function promoStatusTone(HotelPromo $promo): string
    {
        if ($promo->book_periode_end && $promo->book_periode_end < now()->toDateString()) {
            return 'expired';
        }

        return strtolower((string) $promo->status) === 'active' ? 'active' : 'draft';
    }
}
