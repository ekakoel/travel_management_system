<?php

namespace App\Http\Controllers\Backend\Operations\Transports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Operations\Transports\StoreTransportPriceAdminRequest;
use App\Http\Requests\Backend\Operations\Transports\UpdateTransportPriceAdminRequest;
use App\Services\Transports\TransportAuditService;
use App\Services\Transports\TransportPricingService;
use Illuminate\Support\Facades\Gate;

class TransportPriceAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:isAdmin']);
    }

    public function store(StoreTransportPriceAdminRequest $request, TransportAuditService $audit, TransportPricingService $pricing)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('admin.transports.index')->with('error', __('messages.You are not authorized to perform this action.'));
        }

        $validated = $request->validated();
        $price = $pricing->createPrice($validated);

        $audit->userLog($request, 'Add', 'Transportation Price', $validated['transports_id'], 'detail-transport', 'Add Price:' . $price->id . ' to Transportation: ' . $validated['transports_id']);

        return redirect()->route('admin.transports.show', $validated['transports_id'])->withFragment('prices')->with('success', 'The Price has been Added!');
    }

    public function update(UpdateTransportPriceAdminRequest $request, $id, TransportAuditService $audit, TransportPricingService $pricing)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('admin.transports.index')->with('error', __('messages.You are not authorized to perform this action.'));
        }

        $validated = $request->validated();
        $price = $pricing->updatePrice((int) $id, $validated);

        $audit->userLog($request, 'Update', 'Transportation Price', $id, 'admin-detail-transport', 'Update Price :' . $price->id . ' on transport: ' . $validated['transports_id']);

        return redirect()->route('admin.transports.show', $validated['transports_id'])->withFragment('prices')->with('success', 'The price has been successfully updated!');
    }

    public function destroy($id, TransportAuditService $audit, TransportPricingService $pricing)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('admin.transports.index')->with('error', __('messages.You are not authorized to perform this action.'));
        }

        $price = $pricing->deletePrice((int) $id);
        $transportId = (int) request('transport_id', $price->transports_id);

        $audit->userLog(request(), 'Remove', 'Transportation Price', $transportId, 'transports-admin', 'Remove Transportation Price: ' . $id);

        return redirect()->route('admin.transports.show', $transportId)->withFragment('prices')->with('success', 'The Price has been successfully deleted!');
    }
}
