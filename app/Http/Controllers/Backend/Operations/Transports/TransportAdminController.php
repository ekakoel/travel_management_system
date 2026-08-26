<?php

namespace App\Http\Controllers\Backend\Operations\Transports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Operations\Transports\StoreTransportAdminRequest;
use App\Http\Requests\Backend\Operations\Transports\UpdateTransportAdminRequest;
use App\Models\Transports;
use App\Services\Transports\TransportAssetService;
use App\Services\Transports\TransportAuditService;
use App\Services\Transports\TransportInventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Throwable;

class TransportAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:isAdmin']);
    }

    public function index(TransportInventoryService $inventory)
    {
        return view('backend.operations.transports.index', $inventory->indexData());
    }

    public function create(TransportInventoryService $inventory)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('admin.transports.index')->with('error', __('messages.You are not authorized to perform this action.'));
        }

        return view('backend.operations.transports.forms.create', $inventory->formOptions());
    }

    public function edit($id, TransportInventoryService $inventory)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('admin.transports.index')->with('error', __('messages.You are not authorized to perform this action.'));
        }

        return view('backend.operations.transports.forms.edit', $inventory->editData((int) $id));
    }

    public function show($id, TransportInventoryService $inventory)
    {
        return view('backend.operations.transports.detail', $inventory->detailData((int) $id));
    }

    public function store(StoreTransportAdminRequest $request, TransportAssetService $assets, TransportAuditService $audit)
    {
        $validated = $request->validated();
        $coverName = $assets->uploadCover($request->file('cover'));

        try {
            $transport = DB::transaction(function () use ($request, $validated, $coverName, $audit): Transports {
                $transport = Transports::create([
                    'name' => $validated['name'],
                    'partner_id' => $validated['partner_id'] ?? null,
                    'code' => Str::random(26),
                    'type' => $validated['type'],
                    'brand' => $validated['brand'],
                    'description' => $validated['description'],
                    'include' => $validated['include'],
                    'additional_info' => $validated['additional_info'] ?? null,
                    'cancellation_policy' => $validated['cancellation_policy'] ?? null,
                    'capacity' => $validated['capacity'],
                    'inventory' => $validated['inventory'] ?? null,
                    'cover' => $coverName,
                    'status' => 'Draft',
                    'author_id' => $request->user()->id,
                ]);

                $audit->userLog($request, 'Add', 'Transportation', $transport->id, 'add-transport', 'Add Transportation: ' . $transport->id);

                return $transport;
            });
        } catch (Throwable $exception) {
            $assets->deleteCover($coverName);
            report($exception);

            return back()->withInput()->with('error', 'Transportation could not be created. Please try again.');
        }

        return redirect()->route('admin.transports.show', $transport->id)->with('success', 'The Transportation has been Added!');
    }

    public function update(UpdateTransportAdminRequest $request, $id, TransportAssetService $assets, TransportAuditService $audit)
    {
        $validated = $request->validated();
        $transport = Transports::findOrFail($id);
        $oldCover = $transport->cover;
        $uploadedGalleryFiles = [];
        $newCover = $request->hasFile('cover')
            ? $assets->uploadCover($request->file('cover'))
            : null;

        try {
            DB::transaction(function () use ($request, $validated, $transport, $audit, $newCover, $assets, &$uploadedGalleryFiles): void {
                $transport->update([
                    'name' => $validated['name'],
                    'partner_id' => $validated['partner_id'] ?? null,
                    'type' => $validated['type'],
                    'capacity' => $validated['capacity'],
                    'inventory' => $validated['inventory'] ?? null,
                    'description' => $validated['description'],
                    'include' => $validated['include'],
                    'additional_info' => $validated['additional_info'] ?? null,
                    'cancellation_policy' => $validated['cancellation_policy'] ?? null,
                    'brand' => $validated['brand'],
                    'status' => $validated['status'],
                    'author_id' => $request->user()->id,
                    'cover' => $newCover ?: $transport->cover,
                ]);

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $galleryImage = $assets->uploadGallery($transport->id, $image);
                        $uploadedGalleryFiles[] = $galleryImage->image;
                    }
                }

                $audit->userLog($request, 'Update Transportation', 'Transportation', $transport->id, 'edit-transport', 'Update Transportation: ' . $transport->id);
            });
        } catch (Throwable $exception) {
            if ($newCover) {
                $assets->deleteCover($newCover);
            }

            foreach ($uploadedGalleryFiles as $galleryFile) {
                $assets->deleteGallery($galleryFile);
            }

            report($exception);

            return back()->withInput()->with('error', 'Transportation could not be updated. Please try again.');
        }

        if ($newCover && $oldCover && $oldCover !== $newCover) {
            $assets->deleteCover($oldCover);
        }

        return redirect()->route('admin.transports.show', $transport->id)->with('success', 'The Transportation has been successfully updated!');
    }

    public function destroy($id, TransportAuditService $audit)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('admin.transports.index')->with('error', __('messages.You are not authorized to perform this action.'));
        }

        Transports::findOrFail($id)->update([
            'status' => 'Removed',
        ]);
        $audit->userLog(request(), 'Remove Transportation', 'Transportation Package', $id, 'transports-admin', 'Remove Transportation Package: ' . $id);

        return back()->with('success', 'The Transportation Package has been successfully deleted!');
    }
}
