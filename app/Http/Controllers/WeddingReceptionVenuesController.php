<?php

namespace App\Http\Controllers;
use App\Models\Hotels;
use App\Models\UserLog;
use App\Models\UsdRates;
use App\Models\Attention;
use Illuminate\Http\Request;
use App\Models\WeddingDinnerVenues;
use App\Models\WeddingVenues;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\Facades\Image;
use App\Models\WeddingReceptionVenues;
use App\Http\Requests\StoreWeddingReceptionVenuesRequest;
use App\Http\Controllers\WeddingReceptionVenuesController;
use App\Http\Requests\UpdateWeddingReceptionVenuesRequest;

class WeddingReceptionVenuesController extends Controller
{
    // VIEW EDIT RECEPTION VENUE ===============================================================================================================>
    public function view_edit_reception_venue($id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $usdrates = UsdRates::where('name','USD')->first();
            $reception_venue = WeddingReceptionVenues::where('id',$id)->first();
            if ($reception_venue->status == "Draft") {
                
                $hotel = Hotels::where('id',$reception_venue->hotel_id)->first();
                $attentions = Attention::where('page','update-reception-venue')->get();
                $weddingVenues = WeddingVenues::where('hotels_id',$hotel->id)->get();
                return view('form.wedding-reception-venue-edit',[
                    'reception_venue'=>$reception_venue,
                    'usdrates'=>$usdrates,
                    'weddingVenues'=>$weddingVenues,
                    'hotel'=>$hotel,
                    'attentions'=>$attentions,
                ]);
            }else {
                return redirect("/weddings-hotel-admin-$reception_venue->hotel_id#receptionVenue")->with('success',"Reception venue has been updated!");
            }
        }else{
            return redirect("/weddings-hotel-admin-$reception_venue->hotel_id#receptionVenue")->with('success',"Reception venue can't be updated!");
        }
    }

    // FUNCTION ADD RECEPTION VENUE =============================================================================================================>
    public function func_add_reception_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $hotel=Hotels::find($id);
            $type = "Reception Venue";
            if($request->hasFile("cover")){
                $file=$request->file("cover");
                $coverName=time().'_'.$file->getClientOriginalName();
                $file->move("storage/weddings/reception-venues/",$coverName);
                $status = "Draft";
                $author_id = Auth::user()->id;
                $periode_start = date('Y-m-d',strtotime($request->periode_start));
                $periode_end = date('Y-m-d',strtotime($request->periode_end));
                $receptionVenue = new WeddingReceptionVenues([
                    "hotel_id"=>$hotel->id,
                    "name"=>$request->name,
                    "cover"=>$coverName,
                    "description"=>$request->description,
                    "terms_and_conditions"=>$request->terms_and_conditions,
                    "capacity"=>$request->capacity,
                    "periode_start"=>$periode_start,
                    "periode_end"=>$periode_end,
                    "markup"=>$request->markup,
                    "price"=>$request->price,
                    "author_id"=>$author_id,
                    "status"=>$status,
                ]);
                // dd($receptionVenue);
                $receptionVenue->save();
                // USER LOG
                $action = "Add Ceremony venue";
                $service = "Wedding Venue";
                $subservice = "Reception Venue";
                $page = "Weddings-hotel-admin";
                $note = "Add reception venue at: ".$hotel->id;
                $user_log =new UserLog([
                    "action"=>$action,
                    "service"=>$service,
                    "subservice"=>$subservice,
                    "subservice_id"=>$hotel->id,
                    "page"=>$page,
                    "user_id"=>$author_id,
                    "user_ip"=>$request->getClientIp(),
                    "note" =>$note, 
                ]);
                $user_log->save();
            }
            return redirect("/weddings-hotel-admin-$hotel->id#receptionVenue")->with('success','Reception venue has been added successfully!');
        }else{
            return redirect("/weddings-hotel-admin-$hotel->id#receptionVenue")->with('error','Access Denied!');
        }
    }
    // FUNCTION EDIT RECEPTION VENUE ===============================================================================================================>
    public function func_edit_reception_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $receptionVenue=WeddingReceptionVenues::find($id);
            if ($receptionVenue->status == "Draft") {
                $status = $request->status;
                if($request->hasFile("cover")){
                    if (File::exists("storage/weddings/reception-venues/".$receptionVenue->cover)) {
                        File::delete("storage/weddings/reception-venues/".$receptionVenue->cover);
                    }
                    $file=$request->file("cover");
                    $receptionVenue->cover=time()."_".$file->getClientOriginalName();
                    $img = Image::make($file->getRealPath());
                    $img->resize(800, 600, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $img->save(public_path('storage/weddings/reception-venues/' . $receptionVenue->cover));
                    $request['cover']=$receptionVenue->cover;
                }
                $receptionVenue->update([
                    "name"=>$request->name,
                    "cover"=>$receptionVenue->cover,
                    "capacity"=>$request->capacity,
                    "terms_and_conditions"=>$request->terms_and_conditions,
                    "description"=>$request->description,
                    "periode_start"=>$request->periode_start,
                    "periode_end"=>$request->periode_end,
                    "price"=>$request->price,
                    "markup"=>$request->markup,
                    "status"=>$status,
                ]);
                // dd($dinner_venue);
                // USER LOG
                $action = "Update Dinner Venue";
                $service = "Weddings Admin";
                $subservice = "Dinner Venue";
                $page = "Wedding-hotel-admin";
                $note = "Update Dinner Venue on Hotel : ".$request->hotels_id;
                $author = Auth::user()->id;
                $user_log =new UserLog([
                    "action"=>$action,
                    "service"=>$service,
                    "subservice"=>$subservice,
                    "subservice_id"=>$id,
                    "page"=>$page,
                    "user_id"=>$author,
                    "user_ip"=>$request->getClientIp(),
                    "note" =>$note, 
                ]);
                $user_log->save();
                return redirect("/weddings-hotel-admin-$receptionVenue->hotel_id#receptionVenue")->with('success','Reception venue has been updated!');
            }else {
                return redirect("/weddings-hotel-admin-$receptionVenue->hotel_id#receptionVenue")->with('success',"Reception venue can't be updated!");
            }
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
    // FUNCTION ACTIVATE RECEPTION VENUE ===============================================================================================================>
    public function func_activate_reception_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $receptionVenue=WeddingReceptionVenues::find($id);
            $receptionVenue->update([
                "status"=>"Active",
            ]);
            return redirect("/weddings-hotel-admin-$receptionVenue->hotel_id#receptionVenue")->with('success','Reception venue has been activated!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
    // FUNCTION DEACTIVATE RECEPTION VENUE ===============================================================================================================>
    public function func_deactivate_reception_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $receptionVenue=WeddingReceptionVenues::find($id);
            $receptionVenue->update([
                "status"=>"Draft",
            ]);
            return redirect("/weddings-hotel-admin-$receptionVenue->hotel_id#receptionVenue")->with('success','Reception venue has been save to draft!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }

    // FUNCTION DESTROY RECEPTION VENUE =============================================================================================================>
    public function destroy_wedding_reception_venue(Request $request,$id)
    {
        $receptionVenue=WeddingReceptionVenues::findOrFail($id);
        $hotel_id = $receptionVenue->hotel_id;
        if (File::exists("storage/weddings/reception-venues/".$receptionVenue->cover)) {
            File::delete("storage/weddings/reception-venues/".$receptionVenue->cover);
        }
        if (File::exists("storage/weddings/reception-venues/".$receptionVenue->pdf)) {
            File::delete("storage/weddings/reception-venues/".$receptionVenue->pdf);
        }
        
        $receptionVenue->delete();
        // USER LOG
        $action = "Remove";
        $service = "Wedding Venue";
        $subservice = "Ceremony Venue";
        $page = "weddings-admin";
        $note = "Remove Ceremony Venue : ".$id;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$id,
            "page"=>$page,
            "user_id"=>$request->author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        return redirect("/weddings-hotel-admin-$hotel_id#receptionVenue")->with('success','The Reception venue has been deleted!');
    }
    
}
