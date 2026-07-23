<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\HotelsAdminController;
use App\Http\Requests\StoreHotelAdditionalChargeRequest;
use App\Http\Requests\UpdateHotelAdditionalChargeRequest;
use Illuminate\Http\Request;

class HotelAdditionalChargeAdminController extends HotelsAdminController
{
    public function create($id)
    {
        return parent::view_add_additional_charge($id);
    }

    public function edit($id)
    {
        return parent::view_edit_additional_charge($id);
    }

    public function store(StoreHotelAdditionalChargeRequest $request)
    {
        return parent::func_add_additional_charge($request);
    }

    public function update(UpdateHotelAdditionalChargeRequest $request, $id)
    {
        return parent::func_edit_additional_charge($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        return parent::delete_additional_charge($request, $id);
    }
}
