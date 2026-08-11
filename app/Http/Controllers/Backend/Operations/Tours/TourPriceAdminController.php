<?php

namespace App\Http\Controllers\Backend\Operations\Tours;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Operations\Tours\StoreTourPriceAdminRequest;
use App\Http\Requests\Backend\Operations\Tours\UpdateTourPriceAdminRequest;
use App\Models\TourPrices;
use App\Models\Tours;
use App\Services\Tours\TourAuditService;
use App\Services\Tours\TourPricingService;

class TourPriceAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:isAdmin']);
    }

    public function store(
        StoreTourPriceAdminRequest $request,
        Tours $tour,
        TourAuditService $audit,
        TourPricingService $pricing,
    )
    {
        $tourPrice = $pricing->createPrice($tour, $request->validated(), (int) $request->user()->id);

        $audit->userLog($request, 'Add Tour Price', 'Tour Package', $tour->id, 'detail-tour', 'Add Tour Price: ' . $tourPrice->id);

        return redirect()->route('admin.tours.show', $tour)->withFragment('prices')->with('success', 'New Tour Package Price has been successfully created!');
    }

    public function update(
        UpdateTourPriceAdminRequest $request,
        Tours $tour,
        TourPrices $tourPrice,
        TourAuditService $audit,
        TourPricingService $pricing,
    )
    {
        $tourPrice = $pricing->updatePrice($tour, $tourPrice, $request->validated(), (int) $request->user()->id);

        $audit->userLog($request, 'Update Tour Price', 'Price', $tourPrice->id, 'detail-tour', 'Update Tour Price: ' . $tourPrice->id);

        return redirect()->route('admin.tours.show', $tourPrice->tour_id)->withFragment('prices')->with('success', 'The Tour Price has been successfully updated!');
    }

    public function destroy(
        Tours $tour,
        TourPrices $tourPrice,
        TourAuditService $audit,
        TourPricingService $pricing,
    )
    {
        $this->authorizeMutation();
        $pricing->deletePrice($tour, $tourPrice);
        $audit->userLog(request(), 'Remove', 'Price', $tourPrice->id, 'detail-tour', 'Remove Tour Price on Tour : ' . $tour->id . ', Price id : ' . $tourPrice->id, 'Tour Package');

        return redirect()->route('admin.tours.show', $tour)->withFragment('prices')->with('success', 'The Tour Price has been successfully deleted!');
    }

    public function restore(
        Tours $tour,
        int $tourPrice,
        TourAuditService $audit,
        TourPricingService $pricing,
    ) {
        $this->authorizeMutation();
        $price = $pricing->restorePrice($tour, $tourPrice);
        $audit->userLog(request(), 'Restore', 'Price', $price->id, 'detail-tour', 'Restore Tour Price: ' . $price->id, 'Tour Package');

        return redirect()->route('admin.tours.show', $tour)->withFragment('prices')->with('success', 'The Tour Price has been successfully restored.');
    }

    private function authorizeMutation(): void
    {
        abort_unless(
            auth()->check()
            && auth()->user()->can('isAdmin')
            && (auth()->user()->can('posDev') || auth()->user()->can('posAuthor')),
            403
        );
    }
}
