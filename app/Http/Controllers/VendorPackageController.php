<?php

namespace App\Http\Controllers;

use App\Models\VendorPackage;
use App\Http\Requests\StoreVendorPackageRequest;
use App\Http\Requests\UpdateVendorPackageRequest;

class VendorPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreVendorPackageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVendorPackageRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VendorPackage  $vendorPackage
     * @return \Illuminate\Http\Response
     */
    public function show(VendorPackage $vendorPackage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\VendorPackage  $vendorPackage
     * @return \Illuminate\Http\Response
     */
    public function edit(VendorPackage $vendorPackage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVendorPackageRequest  $request
     * @param  \App\Models\VendorPackage  $vendorPackage
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVendorPackageRequest $request, VendorPackage $vendorPackage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VendorPackage  $vendorPackage
     * @return \Illuminate\Http\Response
     */
    public function destroy(VendorPackage $vendorPackage)
    {
        //
    }
}
