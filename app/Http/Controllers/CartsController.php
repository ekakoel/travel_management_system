<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCartsRequest;
use App\Http\Requests\UpdateCartsRequest;

class CartsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $dataprices = json_decode($request->prices);
        $total = array_sum(array_column($dataprices, 'normal_price'));
        $prices = array_column($dataprices, 'normal_price');
        Carts::create([
            'users_id' => Auth::user()->id,
            'hotels_id' => $request->hotel_id,
            'hotel_rooms_id' => $request->room_id,
            'checkin' => $request->checkin,
            'checkout' => $request->checkout,
            'guests' => $request->guests,
            'price' => $request->prices,
            'total' => $total,
            'quantity' => $request->quantity,
        ]);

         return redirect()->back()->with('success', 'Room added to cart!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Carts $carts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Carts $carts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartsRequest $request, Carts $carts)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carts $carts)
    {
        //
    }
}
