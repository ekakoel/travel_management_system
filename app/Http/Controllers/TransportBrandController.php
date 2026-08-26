<?php

namespace App\Http\Controllers;

use App\Models\TransportBrand;
use App\Http\Requests\StoreTransportBrandRequest;
use App\Http\Requests\UpdateTransportBrandRequest;
use App\Services\Transports\TransportMasterDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TransportBrandController extends Controller
{
    private const RESOURCE = TransportMasterDataService::BRAND;

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

    public function store(StoreTransportBrandRequest $request, TransportMasterDataService $masterData)
    {
        $masterData->store(self::RESOURCE, $request->validated('brand'));

        return redirect()->route('admin.transport-brands.index')->with('success', 'Transport Brand created successfully.');
    }

    public function update(UpdateTransportBrandRequest $request, TransportBrand $transportBrand, TransportMasterDataService $masterData)
    {
        try {
            $masterData->update(self::RESOURCE, $transportBrand, $request->validated('brand'));
        } catch (\LogicException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.transport-brands.index')->with('success', 'Transport Brand updated successfully.');
    }

    public function destroy(TransportBrand $transportBrand, TransportMasterDataService $masterData)
    {
        try {
            $masterData->delete(self::RESOURCE, $transportBrand);
        } catch (\LogicException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.transport-brands.index')->with('success', 'Transport Brand deleted successfully.');
    }
}
