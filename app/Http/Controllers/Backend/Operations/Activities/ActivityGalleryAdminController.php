<?php

namespace App\Http\Controllers\Backend\Operations\Activities;

use App\Http\Controllers\Controller;
use App\Models\Activities;
use App\Models\ActivitiesImages;
use App\Services\Activities\ActivityAssetService;
use App\Services\Activities\ActivityAuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ActivityGalleryAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'type:admin']);
    }

    public function edit($id)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('admin.activities.index')->with('error', 'Akses ditolak');
        }

        $activities = Activities::with('images')->findOrFail($id);

        return view('backend.operations.activities.forms.gallery-edit')->with('activities', $activities);
    }

    public function update_gallery(Request $request, Activities $activity): RedirectResponse
    {
        $validated = $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ], [
            'images.required' => 'Please select at least one gallery image.',
            'images.array' => 'The gallery images must be submitted as a list.',
            'images.min' => 'Please select at least one gallery image.',
            'images.*.required' => 'Each selected image is required.',
            'images.*.image' => 'Each uploaded file must be a valid image.',
            'images.*.mimes' => 'Images must use JPG, JPEG, PNG, or WEBP format.',
            'images.*.max' => 'Each image must not exceed 5 MB.',
        ]);

        $storedPaths = [];

        try {
            DB::transaction(function () use (
                $validated,
                $activity,
                &$storedPaths
            ): void {
                foreach ($validated['images'] as $image) {
                    $path = $image->store(
                        "activities/{$activity->id}/gallery",
                        'public'
                    );

                    $storedPaths[] = $path;

                    ActivitiesImages::create([
                        'activities_id' => $activity->id,
                        'image' => $path,
                    ]);
                }
            });
        } catch (Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Gallery images could not be uploaded. Please try again.');
        }

        return redirect()
            ->route('admin.activities.show', $activity->id)
            ->with('success', count($storedPaths) . ' gallery image(s) uploaded successfully.');
    }

    public function destroy(Request $request, $id, ActivityAuditService $audit)
    {
        $activity = Activities::findOrFail($id);
        $activity->update([
            'status' => 'Removed',
        ]);

        $audit->userLog(
            $request,
            'Remove Activity',
            $id,
            'activities-admin',
            'Remove Activity: ' . $id
        );

        return back()->with('success', 'The Activity has been successfully deleted!');
    }

    public function destroyImage($id, ActivityAssetService $assets)
    {
        $image = ActivitiesImages::findOrFail($id);
        $assets->deleteGalleryImage($image->image);

        $image->delete();

        return back()->with('success', 'The Activity gallery image has been successfully deleted!');
    }

    public function destroyCover($id, ActivityAssetService $assets)
    {
        $activity = Activities::findOrFail($id);
        $assets->deleteCover($activity->cover);

        return back()->with('success', 'The Activity cover image has been successfully deleted!');
    }
}
