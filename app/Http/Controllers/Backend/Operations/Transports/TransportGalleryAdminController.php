<?php

namespace App\Http\Controllers\Backend\Operations\Transports;

use App\Http\Controllers\Controller;
use App\Models\TransportsImages;
use App\Services\Transports\TransportAssetService;
use App\Services\Transports\TransportInventoryService;

class TransportGalleryAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:isAdmin']);
    }

    public function edit($id, TransportInventoryService $inventory)
    {
        return view('backend.operations.transports.forms.gallery-edit', $inventory->galleryData((int) $id));
    }

    public function destroyCover($id, TransportAssetService $assets, TransportInventoryService $inventory)
    {
        $transport = $inventory->galleryData((int) $id)['transports'];
        $assets->deleteCover($transport->cover);
        $transport->update(['cover' => null]);

        return back()->with('success', 'The transport cover has been successfully deleted!');
    }

    public function destroyImage($id, TransportAssetService $assets)
    {
        $image = TransportsImages::findOrFail($id);
        $assets->deleteGallery($image->image);
        $image->delete();

        return back()->with('success', 'The gallery image has been successfully deleted!');
    }
}
