<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\HotelsAdminController;
use App\Http\Requests\StoreHotelRoomRequest;
use App\Http\Requests\UpdateHotelRoomRequest;
use App\Models\HotelRoom;
use App\Services\Hotels\HotelAssetService;
use App\Services\Hotels\HotelAuditService;
use App\Services\Hotels\HotelStatusService;
use Illuminate\Http\Request;

class HotelRoomAdminController extends HotelsAdminController
{
    public function create($id)
    {
        return parent::view_add_room($id);
    }

    public function edit($id)
    {
        return parent::view_edit_room($id);
    }

    public function store(StoreHotelRoomRequest $request, HotelAssetService $assets, HotelAuditService $audit, HotelStatusService $status)
    {
        $room = HotelRoom::create([
            'hotels_id' => $request->hotels_id,
            'cover' => $assets->uploadRoomCover($request->file('cover')),
            'rooms' => $request->rooms,
            'view' => $this->resolvedRoomView($request),
            'beds' => $this->resolvedBedType($request),
            'size' => $request->size,
            'capacity_adult' => $request->capacity_adult,
            'capacity_child' => $request->capacity_child,
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

        $audit->userLog($request, 'Add Rooms', 'Room', $request->hotels_id, 'add-room', 'Add new rooms at Hotel id : '.$request->hotels_id.', Room id : '.$room->id);

        return $this->redirectToHotelDetail($request->hotels_id, 'rooms')->with('success', 'Rooms added successfully');
    }

    public function update(UpdateHotelRoomRequest $request, $id, HotelAssetService $assets, HotelAuditService $audit)
    {
        $room = HotelRoom::findOrFail($id);
        $hotelId = $room->hotels_id;

        if ($request->hasFile('cover')) {
            $room->cover = $assets->replaceRoomCover($room->cover, $request->file('cover'));
        }

        $room->update([
            'hotels_id' => $request->hotels_id,
            'cover' => $room->cover,
            'rooms' => $request->rooms,
            'view' => $this->resolvedRoomView($request),
            'beds' => $this->resolvedBedType($request),
            'size' => $request->size,
            'capacity_adult' => $request->capacity_adult,
            'capacity_child' => $request->capacity_child,
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

        $audit->userLog($request, 'Update', 'Room', $id, 'edit-room', 'Update room on hotel : '.$request->hotels_id.', Room id : '.$id);

        return $this->redirectToHotelDetail($hotelId, 'rooms')->with('success', 'The room has been updated!');
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
