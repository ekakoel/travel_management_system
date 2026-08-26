<?php

namespace App\Http\Controllers;

use App\Models\TransportType;
use App\Http\Requests\StoreTransportTypeRequest;
use App\Http\Requests\UpdateTransportTypeRequest;
use App\Services\Transports\TransportMasterDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TransportTypeController extends Controller
{
    private const RESOURCE = TransportMasterDataService::TYPE;

    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'can:isAdmin']);
        $this->middleware(function ($request, $next) {
            abort_unless(Gate::any(['posDev', 'posAuthor']), 403);

            return $next($request);
        });
    }

    public function index(Request $request, TransportMasterDataService $masterData)
    {
        return view('backend.operations.transport-master-data.index', [
            'definition' => $masterData->definition(self::RESOURCE),
            'items' => $masterData->index(self::RESOURCE, $request->string('search')->trim()->value()),
        ]);
    }

    public function store(StoreTransportTypeRequest $request, TransportMasterDataService $masterData)
    {
        $masterData->store(self::RESOURCE, $request->validated('type'));

        return redirect()->route('admin.transport-types.index')->with('success', 'Transport Type created successfully.');
    }

    public function update(UpdateTransportTypeRequest $request, TransportType $transportType, TransportMasterDataService $masterData)
    {
        try {
            $masterData->update(self::RESOURCE, $transportType, $request->validated('type'));
        } catch (\LogicException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.transport-types.index')->with('success', 'Transport Type updated successfully.');
    }

    public function destroy(TransportType $transportType, TransportMasterDataService $masterData)
    {
        try {
            $masterData->delete(self::RESOURCE, $transportType);
        } catch (\LogicException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.transport-types.index')->with('success', 'Transport Type deleted successfully.');
    }
}
