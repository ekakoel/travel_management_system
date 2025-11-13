<?php

namespace App\Http\Controllers;

use App\Models\Hotels;
use App\Models\UsdRates;
use App\Models\Attention;
use App\Models\WeddingMenu;
use App\Http\Requests\StoreWeddingMenuRequest;
use App\Http\Requests\UpdateWeddingMenuRequest;

class WeddingMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function view_add_food_and_beverage($id)
    {
        $hotel = Hotels::find($id);
        $usdrates = UsdRates::where('name','USD')->first();
        $attentions = Attention::where('page','vadd-food-and-beverage')->get();
        return view('form.wedding-add-food-and-beverage',[
           'hotel'=>$hotel,
           'usdrates'=>$usdrates,
           'attentions'=>$attentions,
        ]);
    }

   
    public function func_add_food_and_beverage()
    {
        //
    }

    
}
