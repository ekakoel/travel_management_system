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
use App\Models\WeddingLunchVenues;
use App\Http\Requests\StoreWeddingLunchVenuesRequest;
use App\Http\Requests\UpdateWeddingLunchVenuesRequest;

class WeddingLunchVenuesController extends Controller
{
    // VIEW EDIT LUNCH VENUE ===============================================================================================================>
    public function view_edit_lunch_venue($id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $usdrates = UsdRates::where('name','USD')->first();
            $lunch_venue = WeddingLunchVenues::where('id',$id)->first();
            if ($lunch_venue->status == "Draft") {
                
                $hotel = Hotels::where('id',$lunch_venue->hotel_id)->first();
                $attentions = Attention::where('page','update-lunch-venue')->get();
                $weddingVenues = WeddingVenues::where('hotels_id',$hotel->id)->get();
                return view('form.wedding-lunch-venue-edit',[
                    'lunch_venue'=>$lunch_venue,
                    'usdrates'=>$usdrates,
                    'weddingVenues'=>$weddingVenues,
                    'hotel'=>$hotel,
                    'attentions'=>$attentions,
                ]);
            }else {
                return redirect("/weddings-hotel-admin-$lunch_venue->hotel_id#lunchVenue")->with('success',"Lunch Venue has been updated!");
            }
        }else{
            return redirect("/weddings-hotel-admin-$lunch_venue->hotel_id#lunchVenue")->with('success',"Lunch Venue can't be updated!");
        }
    }

    // FUNCTION ADD LUNCH VENUE =============================================================================================================>
    public function func_add_lunch_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $hotel=Hotels::find($id);
            $type = "Lunch Venue";
            if($request->hasFile("cover")){
                $file=$request->file("cover");
                $coverName=time().'_'.$file->getClientOriginalName();
                $file->move("storage/weddings/lunch-venues/",$coverName);
                $status = "Draft";
                $lunchVenue = new WeddingLunchVenues([
                    "hotel_id"=>$hotel->id,
                    "name"=>$request->name,
                    "cover"=>$coverName,
                    "description"=>$request->description,
                    "terms_and_conditions"=>$request->terms_and_conditions,
                    "min_capacity"=>$request->min_capacity,
                    "max_capacity"=>$request->max_capacity,
                    "periode_start"=>$request->periode_start,
                    "periode_end"=>$request->periode_end,
                    "markup"=>$request->markup,
                    "publish_rate"=>$request->publish_rate,
                    "status"=>$status,
                ]);
                // dd($lunchVenue);
                $lunchVenue->save();
                // USER LOG
                $action = "Add Lunch venue";
                $service = "Wedding Venue";
                $subservice = "Lunch Venue";
                $page = "Weddings-hotel-admin";
                $note = "Add lunch venue at: ".$hotel->id;
                $author = Auth::user()->id;
                $user_log =new UserLog([
                    "action"=>$action,
                    "service"=>$service,
                    "subservice"=>$subservice,
                    "subservice_id"=>$hotel->id,
                    "page"=>$page,
                    "user_id"=>$author,
                    "user_ip"=>$request->getClientIp(),
                    "note" =>$note, 
                ]);
                $user_log->save();
            }
            return redirect("/weddings-hotel-admin-$hotel->id#lunchVenue")->with('success','Lunch Venue has been added successfully!');
        }else{
            return redirect("/weddings-hotel-admin-$hotel->id#lunchVenue")->with('error','Access Denied!');
        }
    }
    // FUNCTION EDIT LUNCH VENUE ===============================================================================================================>
    public function func_edit_lunch_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $lunchVenue=WeddingLunchVenues::find($id);
            if ($lunchVenue->status == "Draft") {
                $status = $request->status;
                if($request->hasFile("cover")){
                    if (File::exists("storage/weddings/lunch-venues/".$lunchVenue->cover)) {
                        File::delete("storage/weddings/lunch-venues/".$lunchVenue->cover);
                    }
                    $file=$request->file("cover");
                    $lunchVenue->cover=time()."_".$file->getClientOriginalName();
                    $img = Image::make($file->getRealPath());
                    $img->resize(800, 600, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $img->save(public_path('storage/weddings/lunch-venues/' . $lunchVenue->cover));
                    $request['cover']=$lunchVenue->cover;
                }
                $lunchVenue->update([
                    "name"=>$request->name,
                    "cover"=>$lunchVenue->cover,
                    "min_capacity"=>$request->min_capacity,
                    "max_capacity"=>$request->max_capacity,
                    "terms_and_conditions"=>$request->terms_and_conditions,
                    "description"=>$request->description,
                    "periode_start"=>$request->periode_start,
                    "periode_end"=>$request->periode_end,
                    "publish_rate"=>$request->publish_rate,
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
                return redirect("/weddings-hotel-admin-$lunchVenue->hotel_id#lunchVenue")->with('success','Lunch Venue has been updated!');
            }else {
                return redirect("/weddings-hotel-admin-$lunchVenue->hotel_id#lunchVenue")->with('success',"Lunch Venue can't be updated!");
            }
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
    // FUNCTION ACTIVATE LUNCH VENUE ===============================================================================================================>
    public function func_activate_lunch_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $lunchVenue=WeddingLunchVenues::find($id);
            $lunchVenue->update([
                "status"=>"Active",
            ]);
            return redirect("/weddings-hotel-admin-$lunchVenue->hotel_id#lunchVenue")->with('success','Lunch Venue has been activated!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
    // FUNCTION DEACTIVATE LUNCH VENUE ===============================================================================================================>
    public function func_deactivate_lunch_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $lunchVenue=WeddingLunchVenues::find($id);
            $lunchVenue->update([
                "status"=>"Draft",
            ]);
            return redirect("/weddings-hotel-admin-$lunchVenue->hotel_id#lunchVenue")->with('success','Lunch Venue has been save to draft!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }

    // FUNCTION DESTROY LUNCH VENUE =============================================================================================================>
    public function destroy_wedding_lunch_venue(Request $request,$id)
    {
        $lunchVenue=WeddingLunchVenues::findOrFail($id);
        $hotel_id = $lunchVenue->hotel_id;
        if (File::exists("storage/weddings/lunch-venues/".$lunchVenue->cover)) {
            File::delete("storage/weddings/lunch-venues/".$lunchVenue->cover);
        }
        if (File::exists("storage/weddings/lunch-venues/".$lunchVenue->pdf)) {
            File::delete("storage/weddings/lunch-venues/".$lunchVenue->pdf);
        }
        
        $lunchVenue->delete();
        // USER LOG
        $action = "Remove";
        $service = "Wedding Venue";
        $subservice = "Lunch Venue";
        $page = "weddings-admin";
        $note = "Remove Lunch Venue : ".$id;
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
        return redirect("/weddings-hotel-admin-$hotel_id#lunchVenue")->with('success','The Lunch Venue has been deleted!');
    }
}
