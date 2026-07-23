<?php

namespace App\Http\Controllers\Backend\Operations\Tours;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Operations\Tours\StoreTourAdminRequest;
use App\Http\Requests\Backend\Operations\Tours\UpdateTourAdminRequest;
use App\Models\Tours;
use App\Services\Tours\TourAssetService;
use App\Services\Tours\TourAuditService;
use App\Services\Tours\TourInventoryService;
use App\Services\Tours\TourLocationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TourAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:isAdmin']);
    }

    public function index(TourInventoryService $inventory)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('view.admin-panel-main')->with('error', 'Akses ditolak');
        }

        return view('backend.operations.tours.index', $inventory->indexData());
    }

    public function show($id, TourInventoryService $inventory)
    {
        return view('backend.operations.tours.detail', $inventory->detailData((int) $id));
    }

    public function create(TourInventoryService $inventory)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('tours-admin.index')->with('error', 'Akses ditolak');
        }

        return view('backend.operations.tours.forms.create', $inventory->formOptions());
    }

    public function edit($id, TourInventoryService $inventory)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('tours-admin.index')->with('error', 'Akses ditolak');
        }

        return view('backend.operations.tours.forms.edit', $inventory->editData((int) $id));
    }

    public function store(StoreTourAdminRequest $request, TourAssetService $assets, TourLocationService $locationsService)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('tours-admin.index')->with('error', 'Akses ditolak');
        }

        $locations = $locationsService->validateLocations($request);
        $validated = $request->validated();
        $validated['cover'] = $assets->uploadCover($request->file('cover'));

        $tour = DB::transaction(function () use ($validated, $locations, $locationsService) {
            $tour = new Tours();
            $this->fillTourDetails($tour, $validated);
            $tour->save();
            $locationsService->sync($tour, $locations);

            return $tour;
        });

        return redirect()->route('admin.tours.show', $tour->id)->with('success', 'New Tour Package has been successfully created!');
    }

    public function update(UpdateTourAdminRequest $request, $id, TourAssetService $assets, TourLocationService $locationsService)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('tours-admin.index')->with('error', 'Akses ditolak');
        }

        $tour = Tours::findOrFail($id);
        $locations = $locationsService->validateLocations($request);
        $validated = $request->validated();

        if ($request->hasFile('cover')) {
            $validated['cover'] = $assets->replaceCover($tour->cover, $request->file('cover'));
        } else {
            $validated['cover'] = $tour->cover;
        }

        DB::transaction(function () use ($tour, $validated, $locations, $locationsService) {
            $this->fillTourDetails($tour, $validated, true);
            $tour->save();
            $locationsService->sync($tour, $locations);
        });

        return redirect()->route('admin.tours.show', $tour->id)->with('success', 'The Tour Package has been successfully updated!');
    }

    public function destroy($id, TourAuditService $audit)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('tours-admin.index')->with('error', 'Akses ditolak');
        }

        Tours::findOrFail($id)->update([
            'status' => 'Removed',
        ]);
        $audit->userLog(request(), 'Remove Tour', 'Tour Package', $id, 'tours-admin', 'Remove Tour Package: ' . $id);

        return back()->with('success', 'The Tour Package has been successfully deleted!');
    }

    public function resolveTourLocationCoordinates(\Illuminate\Http\Request $request, TourLocationService $locations)
    {
        $request->validate([
            'google_maps_url' => 'required|url|max:2048',
        ]);

        $url = trim((string) $request->input('google_maps_url'));

        if (! $locations->allowedGoogleMapsUrl($url)) {
            return response()->json([
                'success' => false,
                'message' => 'Google Maps link must be a valid Google Maps URL.',
            ], 422);
        }

        $coordinates = $locations->resolveCoordinates($url);

        if (! $coordinates) {
            return response()->json([
                'success' => false,
                'message' => 'Coordinates could not be read from this link. Please use a Google Maps URL containing coordinates, or fill latitude and longitude manually.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'latitude' => round((float) $coordinates['latitude'], 7),
            'longitude' => round((float) $coordinates['longitude'], 7),
        ]);
    }

    public function searchTourLocationReferences(\Illuminate\Http\Request $request, TourLocationService $locations)
    {
        return response()->json($locations->searchReferences(trim((string) $request->query('q', ''))));
    }

    private function fillTourDetails(Tours $tour, array $validated, bool $isUpdate = false): void
    {
        $tour->cover = $validated['cover'];

        if ($isUpdate) {
            $tour->status = $validated['status'];
        }

        $tour->type_id = $validated['type'];

        foreach ($this->tourDetailFields() as $field) {
            $tour->{$field} = $validated[$field] ?? null;
        }
    }

    private function tourDetailFields(): array
    {
        return [
            'code',
            'name',
            'name_traditional',
            'name_simplified',
            'duration_days',
            'duration_nights',
            'short_description',
            'short_description_traditional',
            'short_description_simplified',
            'description',
            'description_traditional',
            'description_simplified',
            'package_highlights',
            'package_highlights_traditional',
            'package_highlights_simplified',
            'itinerary',
            'itinerary_traditional',
            'itinerary_simplified',
            'include',
            'include_traditional',
            'include_simplified',
            'exclude',
            'exclude_traditional',
            'exclude_simplified',
            'additional_info',
            'additional_info_traditional',
            'additional_info_simplified',
            'cancellation_policy',
            'cancellation_policy_traditional',
            'cancellation_policy_simplified',
        ];
    }
}
