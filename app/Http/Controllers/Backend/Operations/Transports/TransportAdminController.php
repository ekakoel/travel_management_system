<?php

namespace App\Http\Controllers\Backend\Operations\Transports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Operations\Transports\StoreTransportAdminRequest;
use App\Http\Requests\Backend\Operations\Transports\UpdateTransportAdminRequest;
use App\Models\Transports;
use App\Services\Transports\TransportAssetService;
use App\Services\Transports\TransportAuditService;
use App\Services\Transports\TransportInventoryService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

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
            return redirect()->route('transports-admin.index')->with('error', 'Akses ditolak');
        }

        return view('backend.operations.transports.forms.create', $inventory->formOptions());
    }

    public function edit($id, TransportInventoryService $inventory)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('transports-admin.index')->with('error', 'Akses ditolak');
        }

        return view('backend.operations.transports.forms.edit', $inventory->editData((int) $id));
    }

    public function show($id, TransportInventoryService $inventory)
    {
        return view('backend.operations.transports.detail', $inventory->detailData((int) $id));
    }

    public function store(StoreTransportAdminRequest $request, TransportAssetService $assets, TransportAuditService $audit)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('transports-admin.index')->with('error', 'Akses ditolak');
        }

        $validated = $request->validated();
        $transport = Transports::create([
            'name' => $validated['name'],
            'code' => Str::random(26),
            'type' => $validated['type'],
            'brand' => $validated['brand'],
            'description' => $validated['description'],
            'include' => $validated['include'],
            'additional_info' => $validated['additional_info'] ?? null,
            'cancellation_policy' => $validated['cancellation_policy'] ?? null,
            'capacity' => $validated['capacity'],
            'cover' => $assets->uploadCover($request->file('cover')),
            'status' => 'Draft',
            'author_id' => $validated['author'],
        ]);

        $audit->userLog($request, 'Add', 'Transportation', $transport->id, 'add-transport', 'Add Transportation: ' . $transport->id);

        return redirect()->route('admin.transports.show', $transport->id)->with('success', 'The Transportation has been Added!');
    }

    public function update(UpdateTransportAdminRequest $request, $id, TransportAssetService $assets, TransportAuditService $audit)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('transports-admin.index')->with('error', 'Akses ditolak');
        }

        $validated = $request->validated();
        $transport = Transports::findOrFail($id);
        $cover = $request->hasFile('cover')
            ? $assets->replaceCover($transport->cover, $request->file('cover'))
            : $transport->cover;

        $transport->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'capacity' => $validated['capacity'],
            'description' => $validated['description'],
            'include' => $validated['include'],
            'additional_info' => $validated['additional_info'] ?? null,
            'cancellation_policy' => $validated['cancellation_policy'] ?? null,
            'brand' => $validated['brand'],
            'status' => $validated['status'],
            'author_id' => $validated['author'],
            'cover' => $cover,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $assets->uploadGallery($transport->id, $image);
            }
        }

        $audit->userLog($request, 'Update Transportation', 'Transportation', $transport->id, 'edit-transport', 'Update Transportation: ' . $transport->id);

        return redirect()->route('admin.transports.show', $transport->id)->with('success', 'The Transportation has been successfully updated!');
    }

    public function destroy($id, TransportAuditService $audit)
    {
        if (! Gate::allows('posDev') && ! Gate::allows('posAuthor')) {
            return redirect()->route('transports-admin.index')->with('error', 'Akses ditolak');
        }

        Transports::findOrFail($id)->update([
            'status' => 'Removed',
        ]);
        $audit->userLog(request(), 'Remove Transportation', 'Transportation Package', $id, 'transports-admin', 'Remove Transportation Package: ' . $id);

        return back()->with('success', 'The Transportation Package has been successfully deleted!');
    }
}
