<?php

namespace App\Http\Controllers\Backend\Operations\Activities;

use App\Http\Controllers\Controller;
use App\Models\Activities;
use App\Models\ActivitiesImages;
use App\Services\Activities\ActivityAssetService;
use App\Services\Activities\ActivityAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityGalleryAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'type:admin']);
    }

    public function edit($id)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('activities-admin.index')->with('error', 'Akses ditolak');
        }

        $activities = Activities::with('images')->findOrFail($id);

        return view('backend.operations.activities.forms.gallery-edit')->with('activities', $activities);
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
