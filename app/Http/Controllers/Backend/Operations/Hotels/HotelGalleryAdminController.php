<?php

namespace App\Http\Controllers\Backend\Operations\Hotels;

use App\Http\Controllers\HotelsAdminController;
use App\Http\Requests\StoreHotelGalleryImagesRequest;
use App\Models\Hotels;
use App\Models\HotelsImages;
use App\Models\UserLog;
use App\Services\Hotels\HotelAssetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class HotelGalleryAdminController extends HotelsAdminController
{
    public function edit($id)
    {
        if (! Gate::any(['posDev', 'posAuthor', 'posAdm'])) {
            return redirect()
                ->route('admin.hotels.index')
                ->with('error', __('messages.You are not authorized to perform this action.'));
        }

        $hotel = Hotels::query()
            ->with(['images' => fn ($query) => $query->latest()])
            ->withCount('images')
            ->findOrFail($id);

        return view('backend.operations.hotels.forms.gallery-edit', [
            'hotels' => $hotel,
        ]);
    }

    public function store(StoreHotelGalleryImagesRequest $request, $id, HotelAssetService $assets)
    {
        $hotel = Hotels::findOrFail($id);
        $storedFiles = [];

        try {
            DB::transaction(function () use ($request, $hotel, $assets, &$storedFiles): void {
                foreach ($request->file('images', []) as $file) {
                    $fileName = $assets->uploadGalleryImage($file);
                    $storedFiles[] = $fileName;

                    $hotel->images()->create([
                        'image' => $fileName,
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            foreach ($storedFiles as $fileName) {
                $assets->deleteGalleryImage($fileName);
            }

            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Gallery images could not be uploaded. Please try again.');
        }

        UserLog::create([
            'action' => 'Add Gallery Images',
            'service' => 'Hotel',
            'subservice' => 'Gallery',
            'subservice_id' => $hotel->id,
            'page' => 'edit-galery-hotel',
            'user_id' => auth()->id(),
            'user_ip' => $request->getClientIp(),
            'note' => 'Add '.count($storedFiles).' gallery image(s) to Hotel id : '.$hotel->id,
        ]);

        return redirect()
            ->route('admin.hotels.gallery.edit', $hotel->id)
            ->with('success', 'Gallery images uploaded successfully.');
    }

    public function destroyCover($id, HotelAssetService $assets)
    {
        if (! Gate::any(['posDev', 'posAuthor', 'posAdm'])) {
            return redirect()
                ->route('admin.hotels.index')
                ->with('error', __('messages.You are not authorized to perform this action.'));
        }

        $hotel = Hotels::findOrFail($id);
        $assets->deleteHotelCover($hotel->cover);

        return back();
    }

    public function destroyImage(Request $request, $hotelId, $imageId, HotelAssetService $assets)
    {
        if (! Gate::any(['posDev', 'posAuthor', 'posAdm'])) {
            return redirect()
                ->route('admin.hotels.index')
                ->with('error', __('messages.You are not authorized to perform this action.'));
        }

        $hotel = Hotels::findOrFail($hotelId);
        $image = $hotel->images()->whereKey($imageId)->firstOrFail();

        $assets->deleteGalleryImage($image->image);
        $image->delete();

        UserLog::create([
            'action' => 'Remove Gallery Image',
            'service' => 'Hotel',
            'subservice' => 'Gallery',
            'subservice_id' => $image->id,
            'page' => 'edit-galery-hotel',
            'user_id' => auth()->id(),
            'user_ip' => $request->getClientIp(),
            'note' => 'Remove gallery image from Hotel id : '.$hotel->id.', Image id : '.$image->id,
        ]);

        return redirect()
            ->route('admin.hotels.gallery.edit', $hotel->id)
            ->with('success', 'Gallery image removed successfully.');
    }
}
