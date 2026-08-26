<?php

namespace App\Http\Controllers\Backend\Operations\Activities;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityAdminRequest;
use App\Http\Requests\StoreActivityTypeRequest;
use App\Http\Requests\UpdateActivityAdminRequest;
use App\Http\Requests\UpdateActivityTypeRequest;
use App\Models\Activities;
use App\Models\ActivitiesImages;
use App\Models\ActivityType;
use App\Models\Tax;
use App\Models\UsdRates;
use App\Services\Activities\ActivityAssetService;
use App\Services\Activities\ActivityAuditService;
use App\Services\Activities\ActivityInventoryService;
use App\Services\Activities\ActivityMasterDataService;
use App\Services\Activities\ActivityPricingService;
use App\ViewModels\Activities\ActivityDetailViewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Throwable;

class ActivityAdminController extends Controller
{
    private const MANAGE_GATES = ['posDev', 'posAuthor', 'posAdm'];
    private const RESOURCE = ActivityMasterDataService::TYPE;

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

    public function edit($id, ActivityInventoryService $inventory, ActivityPricingService $pricing)
    {
        if (! Gate::any(self::MANAGE_GATES)) {
            return redirect()->route('admin.activities.index')->with('error', __('messages.You are not authorized to perform this action.'));
        }

        $activities = Activities::with('partners')->findOrFail($id);
        $usdrates = UsdRates::where('name', 'USD')->first();
        $activityTax = Tax::where('name', 'tax')->first();

        return view('backend.operations.activities.forms.edit', array_merge($inventory->formOptions(), [
            'activities' => $activities,
            'partner' => $activities->partners,
            'usdrates' => $usdrates,
            'activityTax' => $activityTax,
            'activityPricing' => new ActivityDetailViewModel(
                activity: $activities,
                partner: $activities->partners,
                pricingService: $pricing,
            ),
        ]));
    }

    public function create(ActivityInventoryService $inventory)
    {
        if (! Gate::any(self::MANAGE_GATES)) {
            return redirect()->route('admin.activities.index')->with('error', __('messages.You are not authorized to perform this action.'));
        }

        return view('backend.operations.activities.forms.create', $inventory->formOptions());
    }

    public function store(StoreActivityAdminRequest $request, ActivityAssetService $assets, ActivityAuditService $audit)
    {
        $validated = $request->validated();
        $coverName = $assets->uploadCover($request->file('cover'));

        try {
            $activity = DB::transaction(function () use ($request, $validated, $coverName, $audit): Activities {
                $activity = Activities::create(array_merge(
                    $this->activityPayload($validated),
                    [
                        'code' => Str::random(26),
                        'status' => 'Draft',
                        'author_id' => $request->user()->id,
                        'cover' => $coverName,
                    ]
                ));

                $audit->userLog(
                    $request,
                    'Add Activity',
                    $activity->id,
                    'add-activity',
                    'Add Activity: ' . $activity->id
                );

                return $activity;
            });
        } catch (Throwable $exception) {
            $assets->deleteCover($coverName);
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Activity could not be created. Please try again.');
        }

        return redirect()->route('admin.activities.show', $activity->id)->with('success', 'New Activity has been successfully added!');
    }

    public function update(UpdateActivityAdminRequest $request, $id, ActivityAssetService $assets, ActivityAuditService $audit)
    {
        $validated = $request->validated();
        $activity = Activities::findOrFail($id);
        $oldCover = $activity->cover;
        $newCover = $request->hasFile('cover')
            ? $assets->uploadCover($request->file('cover'))
            : null;

        try {
            DB::transaction(function () use ($request, $validated, $activity, $assets, $audit, $id, $newCover): void {
                $activity->update(array_merge(
                    $this->activityPayload($validated),
                    [
                        'status' => $validated['status'],
                        'author_id' => $request->user()->id,
                        'cover' => $newCover ?: $activity->cover,
                    ]
                ));

                if (($validated['status'] ?? null) === 'Active') {
                    $activity->partners()->update([
                        'status' => 'Active',
                    ]);
                }

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        ActivitiesImages::create([
                            'activities_id' => $activity->id,
                            'image' => $assets->uploadGalleryImage($image),
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
            });
        } catch (Throwable $exception) {
            if ($newCover) {
                $assets->deleteCover($newCover);
            }

            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Activity could not be updated. Please try again.');
        }

        if ($newCover && $oldCover && $oldCover !== $newCover) {
            $assets->deleteCover($oldCover);
        }

        return redirect()->route('admin.activities.show', $activity->id)->with('success', 'Activity has been successfully updated!');
    }

    private function activityPayload(array $validated): array
    {
        return [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'location' => $validated['location'],
            'map' => $validated['map'] ?? null,
            'partners_id' => $validated['partners_id'],
            'description' => $validated['description'] ?? null,
            'description_traditional' => $validated['description_traditional'] ?? null,
            'description_simplified' => $validated['description_simplified'] ?? null,
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
            'validity' => date('Y-m-d', strtotime($validated['validity'])),
        ];
    }

    public function type_index(Request $request, ActivityMasterDataService $masterData)
    {
        return view('backend.operations.activities.activity-master-data.index', [
            'definition' => $masterData->definition(self::RESOURCE),
            'items' => $masterData->index(self::RESOURCE, $request->string('search')->trim()->value()),
        ]);
    }
    public function type_store(StoreActivityTypeRequest $request, ActivityMasterDataService $masterData)
    {
        $masterData->store(self::RESOURCE, $request->validated('type'));

        return redirect()->route('admin.activity-types.index')->with('success', 'Activity Type created successfully.');
    }

    public function type_update(UpdateActivityTypeRequest $request, ActivityType $activityType, ActivityMasterDataService $masterData)
    {
        try {
            $masterData->update(self::RESOURCE, $activityType, $request->validated('type'));
        } catch (\LogicException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.activity-types.index')->with('success', 'Activity Type updated successfully.');
    }
    public function type_destroy(ActivityType $activityType, ActivityMasterDataService $masterData)
    {
        try {
            $masterData->delete(self::RESOURCE, $activityType);
        } catch (\LogicException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.activity-types.index')->with('success', 'Activity Type deleted successfully.');
    }
}
