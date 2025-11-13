<?php

namespace App\Http\Controllers;

use App\Models\Hotels;
use Illuminate\Http\Request;
use App\Models\WeddingVenues;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreWeddingVenuesRequest;
use App\Http\Requests\UpdateWeddingVenuesRequest;

class WeddingVenuesController extends Controller
{
    // FUNCTION ACTIVATE RECEPTION VENUE ===============================================================================================================>
    public function func_activate_ceremony_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $ceremonyVenue=WeddingVenues::find($id);
            $ceremonyVenue->update([
                "status"=>"Active",
            ]);
            return redirect("/weddings-hotel-admin-$ceremonyVenue->hotels_id#ceremonyVenue")->with('success','Ceremony venue has been activated!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
    // FUNCTION DEACTIVATE RECEPTION VENUE ===============================================================================================================>
    public function func_deactivate_ceremony_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $ceremonyVenue=WeddingVenues::find($id);
            $ceremonyVenue->update([
                "status"=>"Draft",
            ]);
            return redirect("/weddings-hotel-admin-$ceremonyVenue->hotels_id#ceremonyVenue")->with('success','Ceremony venue has been save to draft!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
}
