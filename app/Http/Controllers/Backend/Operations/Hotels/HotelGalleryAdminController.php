<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\HotelsAdminController;
use App\Models\Hotels;
use App\Models\HotelsImages;
use App\Services\Hotels\HotelAssetService;

class HotelGalleryAdminController extends HotelsAdminController
{
    public function edit($id)
    {
        return parent::view_edit_galery_hotel($id);
    }

    public function destroyCover($id, HotelAssetService $assets)
    {
        $hotel = Hotels::findOrFail($id);
        $assets->deleteHotelCover($hotel->cover);

        return back();
    }

    public function destroyImage($id, HotelAssetService $assets)
    {
        $image = HotelsImages::findOrFail($id);
        $assets->deleteGalleryImage($image->image);
        $image->delete();

        return back();
    }
}
