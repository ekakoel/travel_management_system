<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\HotelsAdminController;
use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use App\Services\Hotels\HotelInventoryService;
use Illuminate\Http\Request;

class HotelAdminController extends HotelsAdminController
{
    public function index()
    {
        return parent::index();
    }

    public function show($id, HotelInventoryService $inventoryService)
    {
        return view('backend.operations.hotels.detail', $inventoryService->detailData((int) $id));
    }

    public function create()
    {
        return parent::view_add_hotel();
    }

    public function edit($id)
    {
        return parent::view_edit_hotel($id);
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
