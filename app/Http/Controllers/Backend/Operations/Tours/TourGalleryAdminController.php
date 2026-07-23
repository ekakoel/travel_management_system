<?php

namespace App\Http\Controllers\Backend\Operations\Tours;

use App\Http\Controllers\Controller;
use App\Models\ToursImages;
use App\Services\Tours\TourAssetService;
use Illuminate\Http\Request;

class TourGalleryAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'can:isAdmin']);
    }

    public function upload(Request $request, TourAssetService $assets)
    {
        $validated = $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'tour_id' => 'required|integer|exists:tours,id',
        ]);

        $image = $assets->uploadGallery((int) $validated['tour_id'], $request->file('file'));

        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully!',
            'image' => $image,
        ]);
    }

    public function update(Request $request, $id, TourAssetService $assets)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $tourImage = ToursImages::findOrFail($id);

        $assets->replaceGallery($tourImage, $request->file('file'));

        return response()->json([
            'success' => true,
            'url' => asset('storage/tours/tour-gallery/' . $tourImage->image),
        ]);
    }

    public function destroy($id, TourAssetService $assets)
    {
        $tourImage = ToursImages::findOrFail($id);

        $assets->deleteGallery($tourImage->image);
        $tourImage->delete();

        return response()->json(['success' => true]);
    }
}
