<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\HotelsAdminController;
use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use App\Models\Hotels;
use App\Models\User;
use App\Services\Hotels\HotelAuditService;
use App\Services\Hotels\HotelInventoryService;
use App\Services\Hotels\HotelStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class HotelAdminController extends HotelsAdminController
{
    public function index()
    {
        return parent::index();
    }

    public function refreshStatuses(Request $request, HotelStatusService $statusService, HotelAuditService $audit)
    {
        if (! Gate::any(['posDev', 'posAuthor', 'posAdm'])) {
            return redirect()
                ->route('admin.hotels.index')
                ->with('error', __('messages.You are not authorized to perform this action.'));
        }

        $summary = $statusService->auditPriceDrivenStatuses();

        $audit->userLog(
            $request,
            'Refresh Hotel Status',
            'Hotel',
            HotelAuditService::GLOBAL_SUBSERVICE_ID,
            'admin/hotels',
            sprintf(
                'Price status audit checked %d hotels using valid normal, promo, and package prices; activated %d hotels, drafted %d hotels, activated %d rooms, drafted %d rooms.',
                $summary['hotels_checked'],
                $summary['hotels_activated'],
                $summary['hotels_drafted'],
                $summary['rooms_activated'],
                $summary['rooms_drafted']
            )
        );

        return redirect()
            ->route('admin.hotels.index')
            ->with(
                'success',
                sprintf(
                    'Hotel data refreshed from valid normal, promo, and package prices: %d checked, %d activated, %d drafted, %d rooms activated, %d rooms drafted.',
                    $summary['hotels_checked'],
                    $summary['hotels_activated'],
                    $summary['hotels_drafted'],
                    $summary['rooms_activated'],
                    $summary['rooms_drafted']
                )
            );
    }

    public function show($id, HotelInventoryService $inventoryService)
    {
        return view('backend.operations.hotels.detail', $inventoryService->detailData((int) $id));
    }

    public function create()
    {
        if (! Gate::any(['posDev', 'posAuthor', 'posAdm'])) {
            return redirect()
                ->route('admin.hotels.index')
                ->with('error', __('messages.You are not authorized to perform this action.'));
        }

        return view('backend.operations.hotels.forms.create');
    }

    public function edit($id)
    {
        if (! Gate::any(['posDev', 'posAuthor', 'posAdm'])) {
            return redirect()
                ->route('admin.hotels.index')
                ->with('error', __('messages.You are not authorized to perform this action.'));
        }

        $hotel = Hotels::findOrFail($id);
        $author = $hotel->author_id ? User::find($hotel->author_id) : null;

        return view('backend.operations.hotels.forms.edit', [
            'hotels' => $hotel,
            'author' => $author,
        ]);
    }

    public function store(StoreHotelRequest $request)
    {
        return parent::func_add_hotel($request);
    }

    public function update(UpdateHotelRequest $request, $id)
    {
        return parent::func_edit_hotel($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        return parent::remove_hotel($request, $id);
    }
}
