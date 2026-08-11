<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelPackageRequest;
use App\Http\Requests\UpdateHotelPackageRequest;
use App\Models\HotelPackage;
use App\Models\HotelRoom;
use App\Models\Hotels;
use App\Models\UserLog;
use Illuminate\Support\Facades\Gate;

class HotelPackageAdminController extends Controller
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

        $hotel = Hotels::with('rooms')->findOrFail($id);
        $rooms = HotelRoom::where('hotels_id', $hotel->id)->orderBy('created_at', 'desc')->get();

        return view('backend.operations.hotels.forms.package-create', [
            'hotel' => $hotel,
            'rooms' => $rooms,
        ]);
    }

    public function edit($id)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $package = HotelPackage::with(['room'])->findOrFail($id);
        $hotel = Hotels::with('rooms')->findOrFail($package->hotels_id);
        $rooms = HotelRoom::where('hotels_id', $hotel->id)->orderBy('created_at', 'desc')->get();

        return view('backend.operations.hotels.forms.package-edit', [
            'package' => $package,
            'hotel' => $hotel,
            'rooms' => $rooms,
        ]);
    }

    public function store(StoreHotelPackageRequest $request)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $validated = $request->validated();
        $package = HotelPackage::create([
            'rooms_id' => $request->rooms_id,
            'hotels_id' => $request->hotels_id,
            'name' => $request->name,
            'duration' => $request->duration,
            'stay_period_start' => date('Y-m-d', strtotime($request->stay_period_start)),
            'stay_period_end' => date('Y-m-d', strtotime($request->stay_period_end)),
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
            'cancellation_policy' => $validated['cancellation_policy'] ?? null,
            'cancellation_policy_traditional' => $validated['cancellation_policy_traditional'] ?? null,
            'cancellation_policy_simplified' => $validated['cancellation_policy_simplified'] ?? null,
            'author' => auth()->id(),
            'status' => 'Draft',
        ]);

        UserLog::create([
            'action' => 'Add Package',
            'service' => 'Hotel',
            'subservice' => 'Package',
            'subservice_id' => $package->id,
            'page' => 'detail-hotel#package',
            'user_id' => auth()->id(),
            'user_ip' => $request->getClientIp(),
            'note' => 'Add Package to Hotel id : '.$request->hotels_id.', Room id : '.$request->rooms_id,
        ]);

        return $this->redirectToHotelDetail($request->hotels_id, 'package')->with('success', 'Package added successfully');
    }

    public function update(UpdateHotelPackageRequest $request, $id)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $validated = $request->validated();
        $package = HotelPackage::findOrFail($id);
        $hotelId = $request->hotels_id;

        $package->update([
            'rooms_id' => $request->rooms_id,
            'hotels_id' => $request->hotels_id,
            'name' => $request->name,
            'duration' => $request->duration,
            'stay_period_start' => date('Y-m-d', strtotime($request->stay_period_start)),
            'stay_period_end' => date('Y-m-d', strtotime($request->stay_period_end)),
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
            'cancellation_policy' => $validated['cancellation_policy'] ?? null,
            'cancellation_policy_traditional' => $validated['cancellation_policy_traditional'] ?? null,
            'cancellation_policy_simplified' => $validated['cancellation_policy_simplified'] ?? null,
            'author' => auth()->id(),
            'status' => $request->status,
        ]);

        UserLog::create([
            'action' => 'Update Package',
            'service' => 'Hotel',
            'subservice' => 'Package',
            'subservice_id' => $id,
            'page' => 'detail-hotel#package',
            'user_id' => auth()->id(),
            'user_ip' => $request->getClientIp(),
            'note' => 'Update Package on Hotel id : '.$request->hotels_id.', Room id : '.$request->rooms_id.', Package id : '.$id,
        ]);

        return $this->redirectToHotelDetail($hotelId, 'package')->with('success', 'The Package has been updated!');
    }

    public function destroy(\Illuminate\Http\Request $request, $id)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $package = HotelPackage::findOrFail($id);
        $hotelId = $package->hotels_id;

        UserLog::create([
            'action' => 'Remove',
            'service' => 'Hotel',
            'subservice' => 'Package',
            'subservice_id' => $id,
            'page' => 'detail-hotel#package',
            'user_id' => auth()->id(),
            'user_ip' => $request->getClientIp(),
            'note' => 'Remove Package on hotel : '.$hotelId.', Package id : '.$id,
        ]);

        $package->delete();

        return $this->redirectToHotelDetail($hotelId, 'package')->with('success', 'The Package has been successfully deleted!');
    }

    private function canManage(): bool
    {
        return Gate::allows('posDev') || Gate::allows('posAuthor');
    }

    private function redirectToHotelsIndexWithError(string $message = 'Akses ditolak')
    {
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
