<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\HotelsAdminController;
use App\Models\Contract;
use App\Services\Hotels\HotelAuditService;
use App\Services\Hotels\HotelContractService;
use App\Http\Requests\StoreHotelContractRequest;
use App\Http\Requests\UpdateHotelContractRequest;
use Illuminate\Http\Request;

class HotelContractAdminController extends HotelsAdminController
{
    public function store(StoreHotelContractRequest $request, HotelContractService $contracts, HotelAuditService $audit)
    {
        $fileName = $contracts->upload($request->file('file_name'));
        $contract = Contract::create([
            'name' => $request->contract_name,
            'hotels_id' => $request->hotels_id,
            'period_start' => date('Y-m-d', strtotime($request->period_start)),
            'period_end' => date('Y-m-d', strtotime($request->period_end)),
            'file_name' => $fileName,
        ]);

        $audit->userLog($request, 'Add Contract', 'Contract', $contract->id, 'hotel_detail', 'Add new contract : '.$request->hotels_id);

        return $this->redirectToHotelDetail($request->hotels_id)->with('success', 'Hotel contract added successfully');
    }

    public function update(UpdateHotelContractRequest $request, $id, HotelContractService $contracts, HotelAuditService $audit)
    {
        $contract = Contract::findOrFail($id);

        if ($request->hasFile('file_name')) {
            $contract->file_name = $contracts->replace($contract, $request->file('file_name'));
        }

        $contract->update([
            'name' => $request->contract_name,
            'file_name' => $contract->file_name,
            'period_start' => date('Y-m-d', strtotime($request->period_start)),
            'period_end' => date('Y-m-d', strtotime($request->period_end)),
        ]);

        $audit->userLog($request, 'Update Hotel Contract', 'Contract', $id, 'detail-hotel', 'Update contract to Hotel id : '.$request->hotels_id);

        return $this->redirectToHotelDetail($request->hotels_id, 'contracts')->with('success', 'Contract has been updated!');
    }

    public function destroy(Request $request, $id, HotelContractService $contracts, HotelAuditService $audit)
    {
        $contract = Contract::findOrFail($id);
        $hotelId = $request->hotels_id ?? $contract->hotels_id;

        $contracts->delete($contract);
        $audit->userLog($request, 'Remove', 'Contract', $id, 'detail-hotel', 'Remove Contract on hotel : '.$hotelId);
        $contract->delete();

        return $this->redirectToHotelDetail($hotelId, 'contracts')->with('success', 'The Contract has been successfully deleted!');
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
