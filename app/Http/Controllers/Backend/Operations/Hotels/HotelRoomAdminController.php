<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\HotelsAdminController;
use App\Http\Requests\StoreHotelRoomRequest;
use App\Http\Requests\UpdateHotelRoomRequest;
use App\Models\Hotels;
use App\Models\HotelRoom;
use App\Services\Hotels\HotelAssetService;
use App\Services\Hotels\HotelAuditService;
use App\Services\Hotels\HotelStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class HotelRoomAdminController extends HotelsAdminController
{
    public function create($id, HotelStatusService $status)
    {
        if (! Gate::any(['posDev', 'posAuthor', 'posAdm'])) {
            return redirect()
                ->route('admin.hotels.index')
                ->with('error', 'You do not have access to manage Hotel Rooms.');
        }

        $hotel = Hotels::findOrFail($id);

        return view('backend.operations.hotels.forms.room-create', [
            'hotels' => $hotel,
            'hotelContext' => Crypt::encryptString((string) $hotel->id),
            'initialStatus' => $status->defaultRoomStatus(),
        ]);
    }

    public function edit($id)
    {
        if (! Gate::any(['posDev', 'posAuthor', 'posAdm'])) {
            return redirect()
                ->route('admin.hotels.index')
                ->with('error', 'You do not have access to manage Hotel Rooms.');
        }

        $room = HotelRoom::with('hotels')->findOrFail($id);

        return view('backend.operations.hotels.forms.room-edit', [
            'room' => $room,
            'hotel' => $room->hotels,
            'statusOptions' => ['Active', 'Draft', 'Archived'],
        ]);
    }

    public function store(StoreHotelRoomRequest $request, HotelAssetService $assets, HotelAuditService $audit, HotelStatusService $status)
    {
        $hotelId = (int) $request->resolvedHotelId();

        DB::transaction(function () use ($request, $assets, $audit, $status, $hotelId) {
            $room = HotelRoom::create([
                'hotels_id' => $hotelId,
                'cover' => $assets->uploadRoomCover($request->file('cover')),
                'rooms' => $request->rooms,
                'view' => $this->resolvedRoomView($request),
                'beds' => $this->resolvedBedType($request),
                'size' => $request->size,
                'capacity_adult' => $request->capacity_adult,
                'capacity_child' => $request->capacity_child,
                'inventory' => $request->inventory,
                'include' => $request->include,
                'include_traditional' => $request->include_traditional,
                'include_simplified' => $request->include_simplified,
                'additional_info' => $request->additional_info,
                'additional_info_traditional' => $request->additional_info_traditional,
                'additional_info_simplified' => $request->additional_info_simplified,
                'amenities' => $request->amenities,
                'amenities_traditional' => $request->amenities_traditional,
                'amenities_simplified' => $request->amenities_simplified,
                'status' => $status->defaultRoomStatus(),
            ]);

            $audit->userLog($request, 'Add Rooms', 'Room', $hotelId, 'add-room', 'Add new rooms at Hotel id : '.$hotelId.', Room id : '.$room->id);

            return $room;
        });

        return $this->redirectToHotelDetail($hotelId, 'rooms')->with('success', 'Rooms added successfully');
    }

    public function update(UpdateHotelRoomRequest $request, $id, HotelAssetService $assets, HotelAuditService $audit)
    {
        $room = HotelRoom::findOrFail($id);
        $hotelId = $room->hotels_id;

        DB::transaction(function () use ($request, $assets, $audit, $room, $hotelId, $id) {
            if ($request->hasFile('cover')) {
                $room->cover = $assets->replaceRoomCover($room->cover, $request->file('cover'));
            }

            $room->update([
                'hotels_id' => $hotelId,
                'cover' => $room->cover,
                'rooms' => $request->rooms,
                'view' => $this->resolvedRoomView($request),
                'beds' => $this->resolvedBedType($request),
                'size' => $request->size,
                'capacity_adult' => $request->capacity_adult,
                'capacity_child' => $request->capacity_child,
                'inventory' => $request->inventory,
                'include' => $request->include,
                'include_traditional' => $request->include_traditional,
                'include_simplified' => $request->include_simplified,
                'amenities' => $request->amenities,
                'amenities_traditional' => $request->amenities_traditional,
                'amenities_simplified' => $request->amenities_simplified,
                'additional_info' => $request->additional_info,
                'additional_info_traditional' => $request->additional_info_traditional,
                'additional_info_simplified' => $request->additional_info_simplified,
                'status' => $request->status,
            ]);

            $audit->userLog($request, 'Update', 'Room', $id, 'edit-room', 'Update room on hotel : '.$hotelId.', Room id : '.$id);
        });

        return $this->redirectToHotelDetail($hotelId, 'rooms')->with('success', 'The room has been updated!');
    }

    public function updateStatus(Request $request, HotelRoom $room, HotelAuditService $audit): JsonResponse
    {
        if (! Gate::any(['posDev', 'posAuthor', 'posAdm'])) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['Active', 'Draft'])],
        ]);

        $room = DB::transaction(function () use ($request, $room, $audit, $validated) {
            $previousStatus = $room->status;

            $room->forceFill([
                'status' => $validated['status'],
            ])->save();

            $audit->userLog(
                $request,
                'Update Room Status',
                'Room',
                $room->id,
                'detail-hotel#rooms',
                'Update room status on Hotel id : '.$room->hotels_id.', Room id : '.$room->id.', Status : '.$previousStatus.' -> '.$room->status
            );

            return $room->refresh();
        });

        return response()->json([
            'id' => $room->id,
            'status' => $room->status,
            'next_status' => $room->status === 'Active' ? 'Draft' : 'Active',
            'tone' => strtolower($room->status) === 'active' ? 'active' : 'draft',
            'message' => 'Room status updated.',
        ]);
    }

    public function destroy(Request $request, $id, HotelAssetService $assets, HotelAuditService $audit)
    {
        $room = HotelRoom::findOrFail($id);
        $hotelId = $request->hotels_id ?? $room->hotels_id;

        $assets->deleteRoomCover($room->cover);
        $audit->userLog($request, 'Remove', 'Room', $id, 'detail-hotel#rooms', 'Remove room on hotel : '.$hotelId.', Room id : '.$id);
        $room->delete();

        return $this->redirectToHotelDetail($hotelId, 'rooms')->with('success', 'The Room has been successfully deleted!');
    }

    private function resolvedRoomView(Request $request): string
    {
        return $request->room_view === 'custom' ? $request->custom_room_view : $request->room_view;
    }

    private function resolvedBedType(Request $request): string
    {
        return $request->beds === 'custom' ? $request->custom_beds : $request->beds;
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
