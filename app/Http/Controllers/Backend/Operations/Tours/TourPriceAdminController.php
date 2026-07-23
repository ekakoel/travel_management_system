<?php

namespace App\Http\Controllers\Backend\Operations\Tours;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Operations\Tours\StoreTourPriceAdminRequest;
use App\Http\Requests\Backend\Operations\Tours\UpdateTourPriceAdminRequest;
use App\Services\Tours\TourAuditService;
use App\Services\Tours\TourPricingService;
use Illuminate\Support\Facades\Gate;

class TourPriceAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:isAdmin']);
    }

    public function store(StoreTourPriceAdminRequest $request, $id, TourAuditService $audit, TourPricingService $pricing)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('tours-admin.index')->with('error', 'Akses ditolak');
        }

        $pricing->createPrice((int) $id, $request->validated());

        $audit->userLog($request, 'Add Tour Price', 'Tour Package', $id, 'detail-tour', 'Add Tour Price: ' . $id);

        return redirect()->route('admin.tours.show', $id)->withFragment('prices')->with('success', 'New Tour Package Price has been successfully created!');
    }

    public function update(UpdateTourPriceAdminRequest $request, $id, TourAuditService $audit, TourPricingService $pricing)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('tours-admin.index')->with('error', 'Akses ditolak');
        }

        $tourPrice = $pricing->updatePrice((int) $id, $request->validated());

        $audit->userLog($request, 'Update Tour Price', 'Price', $id, 'detail-tour', 'Update Tour Price: ' . $id);

        return redirect()->route('admin.tours.show', $tourPrice->tour_id)->withFragment('prices')->with('success', 'The Tour Price has been successfully updated!');
    }

    public function destroy($id, TourAuditService $audit, TourPricingService $pricing)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('tours-admin.index')->with('error', 'Akses ditolak');
        }

        $tourPrice = $pricing->deletePrice((int) $id);
        $tourId = $tourPrice->tour_id;

        $audit->userLog(request(), 'Remove', 'Price', $id, 'detail-tour', 'Remove Tour Price on Tour : ' . $tourId . ', Price id : ' . $id, 'Tour Package');

        return redirect()->route('admin.tours.show', $tourId)->withFragment('prices')->with('success', 'The Tour Price has been successfully deleted!');
    }
}
