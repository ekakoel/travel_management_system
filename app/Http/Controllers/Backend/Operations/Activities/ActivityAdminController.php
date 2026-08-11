<?php

namespace App\Http\Controllers\Backend\Operations\Activities;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityAdminRequest;
use App\Http\Requests\UpdateActivityAdminRequest;
use App\Models\Activities;
use App\Models\ActivitiesImages;
use App\Models\Partners;
use App\Services\Activities\ActivityAssetService;
use App\Services\Activities\ActivityAuditService;
use App\Services\Activities\ActivityInventoryService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ActivityAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'type:admin']);
    }

    public function index(ActivityInventoryService $inventory)
    {
        return view('backend.operations.activities.index', $inventory->indexData());
    }

    public function show($id, ActivityInventoryService $inventory)
    {
        return view('backend.operations.activities.detail', $inventory->detailData((int) $id));
    }

    public function edit($id, ActivityInventoryService $inventory)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('admin.activities.index')->with('error', 'Akses ditolak');
        }

        $inventoryData = $inventory->indexData();
        $activities = Activities::findOrFail($id);
        $partner = Partners::where('id', $activities->partners_id)->first();

        return view('backend.operations.activities.forms.edit', array_merge(
            $inventory->formOptions(),
            $inventoryData,
            ['partner' => $partner],
        ))->with('activities', $activities);
    }

    public function create(ActivityInventoryService $inventory)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('admin.activities.index')->with('error', 'Akses ditolak');
        }

        $activities = Activities::all();

        return view('backend.operations.activities.forms.create', $inventory->formOptions())->with('activities', $activities);
    }

    public function store(StoreActivityAdminRequest $request, ActivityAssetService $assets, ActivityAuditService $audit)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('admin.activities.index')->with('error', 'Akses ditolak');
        }

        $validated = $request->validated();
        $coverName = $assets->uploadCover($request->file('cover'));

        $activity = Activities::create([
            'name' => $validated['name'],
            'code' => Str::random(26),
            'type' => $validated['type'],
            'location' => $validated['location'],
            'map' => $validated['map'] ?? null,
            'partners_id' => $validated['partners_id'],
            'description' => $validated['description'],
            'description_traditional' => $validated['description_traditional'],
            'description_simplified' => $validated['description_simplified'],
            'itinerary' => $validated['itinerary'] ?? null,
            'itinerary_traditional' => $validated['itinerary_traditional'] ?? null,
            'itinerary_simplified' => $validated['itinerary_simplified'] ?? null,
            'duration' => $validated['duration'],
            'include' => $validated['include'] ?? null,
            'include_traditional' => $validated['include_traditional'] ?? null,
            'include_simplified' => $validated['include_simplified'] ?? null,
            'additional_info' => $validated['additional_info'] ?? null,
            'additional_info_traditional' => $validated['additional_info_traditional'] ?? null,
            'additional_info_simplified' => $validated['additional_info_simplified'] ?? null,
            'contract_rate' => $validated['contract_rate'],
            'cancellation_policy' => $validated['cancellation_policy'] ?? null,
            'cancellation_policy_traditional' => $validated['cancellation_policy_traditional'] ?? null,
            'cancellation_policy_simplified' => $validated['cancellation_policy_simplified'] ?? null,
            'markup' => $validated['markup'] ?? 0,
            'validity' => date('Y-m-d', strtotime($validated['validity'])),
            'min_pax' => $validated['min_pax'],
            'qty' => $validated['qty'],
            'status' => 'Draft',
            'author_id' => $validated['author'],
            'cover' => $coverName,
        ]);

        $audit->userLog(
            $request,
            'Add Activity',
            $activity->id,
            'add-activity',
            'Add Activity: ' . $activity->id
        );

        return redirect()->route('admin.activities.show', $activity->id)->with('success', 'New Activity has been successfully added!');
    }

    public function update(UpdateActivityAdminRequest $request, $id, ActivityAssetService $assets, ActivityAuditService $audit)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('admin.activities.index')->with('error', 'Akses ditolak');
        }

        $validated = $request->validated();
        $activity = Activities::findOrFail($id);

        if ($request->hasFile('cover')) {
            $activity->cover = $assets->replaceCover($activity->cover, $request->file('cover'));
        }

        if (($validated['status'] ?? null) === 'Active') {
            $partner = Partners::where('id', $activity->partners_id)->first();

            if (isset($partner)) {
                $partner->update([
                    'status' => 'Active',
                ]);
            }
        }

        $validity = date('Y-m-d', strtotime($validated['validity']));
        $activity->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'location' => $validated['location'],
            'map' => $validated['map'] ?? null,
            'partners_id' => $validated['partners_id'],
            'description' => $validated['description'],
            'description_traditional' => $validated['description_traditional'],
            'description_simplified' => $validated['description_simplified'],
            'itinerary' => $validated['itinerary'] ?? null,
            'itinerary_traditional' => $validated['itinerary_traditional'] ?? null,
            'itinerary_simplified' => $validated['itinerary_simplified'] ?? null,
            'duration' => $validated['duration'],
            'include' => $validated['include'] ?? null,
            'include_traditional' => $validated['include_traditional'] ?? null,
            'include_simplified' => $validated['include_simplified'] ?? null,
            'additional_info' => $validated['additional_info'] ?? null,
            'additional_info_traditional' => $validated['additional_info_traditional'] ?? null,
            'additional_info_simplified' => $validated['additional_info_simplified'] ?? null,
            'contract_rate' => $validated['contract_rate'],
            'cancellation_policy' => $validated['cancellation_policy'] ?? null,
            'cancellation_policy_traditional' => $validated['cancellation_policy_traditional'] ?? null,
            'cancellation_policy_simplified' => $validated['cancellation_policy_simplified'] ?? null,
            'markup' => $validated['markup'] ?? 0,
            'qty' => $validated['qty'],
            'min_pax' => $validated['min_pax'],
            'status' => $validated['status'],
            'author_id' => $validated['author'],
            'validity' => $validity,
            'cover' => $activity->cover,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = $assets->uploadGalleryImage($image);
                ActivitiesImages::create([
                    'activities_id' => $activity->id,
                    'image' => $imageName,
                ]);
            }
        }

        $audit->userLog(
            $request,
            'Update Activity',
            (int) $id,
            'edit-activity',
            'Edit Activity: ' . $id
        );

        return redirect()->route('admin.activities.show', $activity->id)->with('success', 'Activity has been successfully updated!');
    }
}
