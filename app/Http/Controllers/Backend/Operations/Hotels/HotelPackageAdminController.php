<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelPackageRequest;
use App\Http\Requests\UpdateHotelPackageRequest;
use App\Models\HotelPackage;
use App\Models\HotelRoom;
use App\Models\Hotels;
use App\Models\UserLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

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

        $hotel = Hotels::findOrFail($id);
        $rooms = HotelRoom::where('hotels_id', $hotel->id)
            ->orderBy('rooms')
            ->get(['id', 'rooms', 'hotels_id']);

        return view('backend.operations.hotels.forms.package-create', [
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

        $package = HotelPackage::with(['hotels', 'room'])->findOrFail($id);
        $hotel = $package->hotels ?: Hotels::findOrFail($package->hotels_id);
        $rooms = HotelRoom::where('hotels_id', $hotel->id)
            ->orderBy('rooms')
            ->get(['id', 'rooms', 'hotels_id']);

        return view('backend.operations.hotels.forms.package-edit', [
            'package' => $package,
            'hotel' => $hotel,
            'rooms' => $rooms,
            'hotelContext' => Crypt::encryptString((string) $hotel->id),
        ]);
    }

    public function store(StoreHotelPackageRequest $request)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $validated = $request->validated();
        $hotelId = (int) $request->resolvedHotelId();

        $package = DB::transaction(function () use ($request, $validated, $hotelId) {
            $package = HotelPackage::create([
                'rooms_id' => $validated['rooms_id'],
                'hotels_id' => $hotelId,
                'name' => $validated['name'],
                'duration' => $validated['duration'],
                'stay_period_start' => date('Y-m-d', strtotime($validated['stay_period_start'])),
                'stay_period_end' => date('Y-m-d', strtotime($validated['stay_period_end'])),
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
                'note' => 'Add Package to Hotel id : '.$hotelId.', Room id : '.$validated['rooms_id'].', Package id : '.$package->id,
            ]);

            return $package;
        }, 3);

        return $this->redirectToHotelDetail($package->hotels_id, 'package')->with('success', 'Package added successfully');
    }

    public function update(UpdateHotelPackageRequest $request, $id)
    {
        if (! $this->canManage()) {
            return $this->redirectToHotelsIndexWithError();
        }

        $validated = $request->validated();
        $package = HotelPackage::findOrFail($id);
        $hotelId = (int) $package->hotels_id;

        abort_unless((int) $request->resolvedHotelId() === $hotelId, 404);

        DB::transaction(function () use ($request, $validated, $package, $hotelId, $id): void {
            $lockedPackage = HotelPackage::query()->lockForUpdate()->findOrFail($package->id);

            $lockedPackage->update([
                'rooms_id' => $validated['rooms_id'],
                'hotels_id' => $hotelId,
                'name' => $validated['name'],
                'duration' => $validated['duration'],
                'stay_period_start' => date('Y-m-d', strtotime($validated['stay_period_start'])),
                'stay_period_end' => date('Y-m-d', strtotime($validated['stay_period_end'])),
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
                'cancellation_policy' => $validated['cancellation_policy'] ?? null,
                'cancellation_policy_traditional' => $validated['cancellation_policy_traditional'] ?? null,
                'cancellation_policy_simplified' => $validated['cancellation_policy_simplified'] ?? null,
                'author' => auth()->id(),
                'status' => $validated['status'],
            ]);

            UserLog::create([
                'action' => 'Update Package',
                'service' => 'Hotel',
                'subservice' => 'Package',
                'subservice_id' => $id,
                'page' => 'detail-hotel#package',
                'user_id' => auth()->id(),
                'user_ip' => $request->getClientIp(),
                'note' => 'Update Package on Hotel id : '.$hotelId.', Room id : '.$validated['rooms_id'].', Package id : '.$id,
            ]);
        }, 3);

        return $this->redirectToHotelDetail($hotelId, 'package')->with('success', 'The Package has been updated!');
    }

    public function updateStatus(Request $request, HotelPackage $package): JsonResponse
    {
        if (! $this->canManage()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['Active', 'Draft'])],
        ]);

        $package = DB::transaction(function () use ($request, $package, $validated) {
            $previousStatus = $package->status;
            $package->forceFill([
                'status' => $validated['status'],
                'author' => auth()->id(),
            ])->save();

            UserLog::create([
                'action' => 'Update Package Status',
                'service' => 'Hotel',
                'subservice' => 'Package',
                'subservice_id' => $package->id,
                'page' => 'detail-hotel#package',
                'user_id' => auth()->id(),
                'user_ip' => $request->getClientIp(),
                'note' => 'Update Package status on Hotel id : '.$package->hotels_id.', Room id : '.$package->rooms_id.', Package id : '.$package->id.', Status : '.$previousStatus.' -> '.$package->status,
            ]);

            return $package->refresh();
        });

        return response()->json([
            'id' => $package->id,
            'status' => $package->status,
            'next_status' => $package->status === 'Active' ? 'Draft' : 'Active',
            'tone' => $this->packageStatusTone($package),
            'message' => 'Package status updated.',
        ]);
    }

    public function destroy(Request $request, $id)
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

    private function packageStatusTone(HotelPackage $package): string
    {
        if ($package->stay_period_end && $package->stay_period_end < now()->toDateString()) {
            return 'expired';
        }

        return strtolower((string) $package->status) === 'active' ? 'active' : 'draft';
    }
}
