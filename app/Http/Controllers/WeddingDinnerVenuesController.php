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
use App\Models\WeddingDinnerPackages;
use App\Http\Requests\StoreWeddingDinnerVenuesRequest;
use App\Http\Requests\UpdateWeddingDinnerVenuesRequest;

class WeddingDinnerVenuesController extends Controller
{

    // VIEW ADD DINNER VENUE =========================================================================================>
    public function view_add_dinner_venue($id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $hotel = Hotels::find($id);
            $usdrates = UsdRates::where('name','USD')->first();
            $attentions = Attention::where('page','update-dinner-venue')->get();

            return view('form.wedding-dinner-venue-add',[
                'hotel'=>$hotel,
                'usdrates'=>$usdrates,
                'attentions'=>$attentions,
            ]);
        }else{
            return redirect("/dashboard")->with('success','Page not found!');
        }
    }
    // VIEW EDIT DINNER VENUE =========================================================================================>
    public function view_edit_dinner_venue($id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $dinnerVenue = WeddingDinnerVenues::where('id',$id)->first();
            if ($dinnerVenue->status == "Draft") {
                $usdrates = UsdRates::where('name','USD')->first();
                $hotel = Hotels::where('id',$dinnerVenue->hotel_id)->first();
                $attentions = Attention::where('page','update-dinner-venue')->get();
                $weddingVenues = WeddingVenues::where('hotels_id',$hotel->id)->get();
                return view('form.wedding-dinner-venue-edit',[
                    'dinner_venue'=>$dinnerVenue,
                    'usdrates'=>$usdrates,
                    'weddingVenues'=>$weddingVenues,
                    'hotel'=>$hotel,
                    'attentions'=>$attentions,
                ]);
            }else{
                return redirect("/weddings-hotel-admin-$dinnerVenue->hotel_id#dinnerVenue")->with('success',"Dinner venue can't be edited!");
            }
        }else{
            return redirect("/dashboard")->with('success','Page not found!');
        }
    }
    // FUNCTION ADD DINNER VENUE =========================================================================================>
    public function func_add_dinner_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $hotel=Hotels::find($id);
            $type = "Dinner Venue";
            if($request->hasFile("cover")){
                $file=$request->file("cover");
                $coverName=time().'_'.$file->getClientOriginalName();
                $img = Image::make($file->getRealPath());
                $img->resize(800, 600, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $img->save(public_path('storage/weddings/dinner-venues/' . $coverName));
                $request['cover']=$coverName;
                $status = "Draft";
                $dinnerVenue = new WeddingDinnerVenues([
                    "hotel_id"=>$hotel->id,
                    "name"=>$request->name,
                    "cover"=>$coverName,
                    "description"=>$request->description,
                    "terms_and_conditions"=>$request->terms_and_conditions,
                    "capacity"=>$request->capacity,
                    "periode_start"=>$request->periode_start,
                    "periode_end"=>$request->periode_end,
                    "markup"=>$request->markup,
                    "publish_rate"=>$request->publish_rate,
                    "status"=>$status,
                ]);
                // dd($dinnerVenue);
                $dinnerVenue->save();
                // USER LOG
                $action = "Add Dinner venue";
                $service = "Wedding Venue";
                $subservice = "Dinner Venue";
                $page = "Weddings-hotel-admin";
                $note = "Add dinner venue at: ".$hotel->id;
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
            return redirect("/weddings-hotel-admin-$hotel->id#dinnerVenue")->with('success','Dinner venue has been added successfully!');
        }else{
            return redirect("/weddings-hotel-admin-$hotel->id#dinnerVenue")->with('error','Access Denied!');
        }
    }
    // FUNCTION EDIT RECEPTION VENUE ===============================================================================================================>
    public function func_edit_dinner_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $dinnerVenue=WeddingDinnerVenues::find($id);
            if ($dinnerVenue->status == "Draft") {
                $status = $request->status;
                if($request->hasFile("cover")){
                    if (File::exists("storage/weddings/dinner-venues/".$dinnerVenue->cover)) {
                        File::delete("storage/weddings/dinner-venues/".$dinnerVenue->cover);
                    }
                    $file=$request->file("cover");
                    $dinnerVenue->cover=time()."_".$file->getClientOriginalName();
                    $img = Image::make($file->getRealPath());
                    $img->resize(800, 600, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $file->move("storage/weddings/dinner-venues/",$dinnerVenue->cover);
                    $img->save(public_path('storage/weddings/dinner-venues/' . $dinnerVenue->cover));
                    $request['cover']=$dinnerVenue->cover;
                }
                $dinnerVenue->update([
                    "name"=>$request->name,
                    "cover"=>$dinnerVenue->cover,
                    "capacity"=>$request->capacity,
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
                return redirect("/weddings-hotel-admin-$dinnerVenue->hotel_id#dinnerVenue")->with('success','Dinner venue has been updated!');
            }else {
                return redirect("/weddings-hotel-admin-$dinnerVenue->hotel_id#dinnerVenue")->with('success','Dinner venue has been updated!');
            }
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }

    // FUNCTION ACTIVATE DINNER VENUE ===============================================================================================================>
    public function func_activate_dinner_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $dinnerVenue=WeddingDinnerVenues::find($id);
            $dinnerVenue->update([
                "status"=>"Active",
            ]);
            return redirect("/weddings-hotel-admin-$dinnerVenue->hotel_id#dinnerVenue")->with('success','Dinner venue has been activated!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
    // FUNCTION DEACTIVATE DINNER VENUE ===============================================================================================================>
    public function func_deactivate_dinner_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $dinnerVenue=WeddingDinnerVenues::find($id);
            $dinnerVenue->update([
                "status"=>"Draft",
            ]);
            return redirect("/weddings-hotel-admin-$dinnerVenue->hotel_id#dinnerVenue")->with('success','Dinner venue has been save to draft!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
    // FUNCTION DESTROY RECEPTION VENUE =============================================================================================================>
    public function destroy_dinner_venue(Request $request,$id)
    {
        $dinnerVenue=WeddingDinnerVenues::findOrFail($id);
        $hotel_id = $dinnerVenue->hotel_id;
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            if (File::exists("storage/weddings/dinner-venues/".$dinnerVenue->cover)) {
                File::delete("storage/weddings/dinner-venues/".$dinnerVenue->cover);
            }
            if (File::exists("storage/weddings/dinner-venues/".$dinnerVenue->pdf)) {
                File::delete("storage/weddings/dinner-venues/".$dinnerVenue->pdf);
            }
            
            $dinnerVenue->delete();
            // USER LOG
            $action = "Remove";
            $service = "Wedding Venue";
            $subservice = "Dinner Venue";
            $page = "weddings-admin";
            $note = "Remove Dinner Venue : ".$id;
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
            return redirect("/weddings-hotel-admin-$hotel_id#dinnerVenue")->with('success','The Reception venue has been deleted!');
        }else {
            return redirect("/weddings-hotel-admin-$hotel_id#dinnerVenue")->with('success',"The Reception venue can't be deleted!");
        }
    }
    





    public function view_add_dinner_package($id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $usdrates = UsdRates::where('name','USD')->first();
            $hotel = Hotels::where('id',$id)->first();
            $attentions = Attention::where('page','update-dinner-package')->get();
            $weddingVenues = WeddingVenues::where('hotels_id',$id)->get();
            $dinnerVenues = WeddingDinnerVenues::where('hotels_id',$id)->get();
            return view('form.wedding-dinner-package-add',[
                'dinnerVenues'=>$dinnerVenues,
                'usdrates'=>$usdrates,
                'weddingVenues'=>$weddingVenues,
                'hotel'=>$hotel,
                'attentions'=>$attentions,
            ]);
            
        }else{
            return redirect("/dashboard")->with('success','Page not found!');
        }
    }
    public function view_update_dinner_package($id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $usdrates = UsdRates::where('name','USD')->first();
            $dinnerPackage = WeddingDinnerPackages::where('id',$id)->first();
            if ($dinnerPackage) {
                $dinner_venue_cover = WeddingDinnerVenues::where('id',$dinnerPackage->dinner_venues_id)->first();
                $hotel = Hotels::where('id',$dinnerPackage->hotels_id)->first();
                $attentions = Attention::where('page','update-dinner-package')->get();
                $weddingVenues = WeddingVenues::where('hotels_id',$hotel->id)->get();
                $dinnerVenues = WeddingDinnerVenues::where('hotels_id',$hotel->id)->get();
                $dinner_venue = WeddingDinnerVenues::where('id',$dinnerPackage->dinner_venues_id)->first();
                return view('form.wedding-dinner-package-edit',[
                    'dinnerVenues'=>$dinnerVenues,
                    'dinner_venue_cover'=>$dinner_venue_cover,
                    'dinner_venue'=>$dinner_venue,
                    'dinnerPackage'=>$dinnerPackage,
                    'usdrates'=>$usdrates,
                    'weddingVenues'=>$weddingVenues,
                    'hotel'=>$hotel,
                    'attentions'=>$attentions,
                ]);
            }else{
                return redirect("/weddings-admin")->with('success','Dinner Venue not found!');
            }
        }else{
            return redirect("/dashboard")->with('success','Page not found!');
        }
    }
    // Function Edit Dinner Venue =============================================================================================================>
    public function func_edit_reception_venue(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $dinnerVenue=WeddingDinnerVenues::findOrFail($id);
            $status = $request->status;
            if($request->hasFile("cover")){
                if (File::exists("storage/weddings/wedding-dinner/".$dinnerVenue->cover)) {
                    File::delete("storage/weddings/wedding-dinner/".$dinnerVenue->cover);
                }
                $file=$request->file("cover");
                $dinnerVenue->cover=time()."_".$file->getClientOriginalName();
                $file->move("storage/weddings/wedding-dinner/",$dinnerVenue->cover);
                $request['cover']=$dinnerVenue->cover;
            }
            $dinnerVenue->update([
                "name"=>$request->dinner_venue,
                "cover"=>$dinnerVenue->cover,
                "hotels_id"=>$request->hotels_id,
                "capacity"=>$request->capacity,
                "min_invitations"=>$request->min_invitations,
                "additional_info"=>$request->additional_info,
                "public_rate"=>$request->public_rate,
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
            return redirect("/weddings-hotel-admin-$request->hotels_id#dinner-venue")->with('success','Dinner venue has been updated!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
    // Function Edit Dinner Venue =============================================================================================================>
    public function func_update_dinner_package(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $dinnerPackage=WeddingDinnerPackages::find($id);
            $status = $request->status;
            $public_rate = str_replace(',', '', $request->public_rate);
            $additional_guest_rate = str_replace(',', '', $request->additional_guest_rate);
            $dinnerPackage->update([
                "dinner_venues_id"=>$request->dinner_venues_id,
                "name"=>$request->dinner_package_name,
                "number_of_guests"=>$request->number_of_guests,
                "include"=>$request->include,
                "additional_info"=>$request->additional_info,
                "public_rate"=>$public_rate,
                "additional_guest_rate"=>$additional_guest_rate,
                "status"=>$status,
            ]);
            // dd($dinner_venue);
            // USER LOG
            $action = "Update Dinner package";
            $service = "Weddings Admin";
            $subservice = "Dinner package";
            $page = "update-dinner-package";
            $note = "Update Dinner package on Hotel : ".$request->hotels_id;
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
            return redirect("/weddings-hotel-admin-$request->hotels_id#dinner-package")->with('success','Dinner package has been updated!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }

     // Function Add Dinner Package =========================================================================================>
     public function func_add_dinner_package(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            
            $hotels_id = $id;
            $status = "Draft";
            $public_rate = str_replace(',', '', $request->public_rate);
            $additional_guest_rate = str_replace(',', '', $request->additional_guest_rate);
            $dinner_package =new WeddingDinnerPackages([
                "dinner_venues_id"=>$request->dinner_venues_id,
                "hotels_id"=>$hotels_id,
                "name"=>$request->dinner_package_name,
                "number_of_guests"=>$request->number_of_guests,
                "include"=>$request->include,
                "additional_info"=>$request->additional_info,
                "public_rate"=>$public_rate,
                "additional_guest_rate"=>$additional_guest_rate,
                "status"=>$status,
            ]);
            $dinner_package->save();
            // USER LOG
            $action = "Add Dinner Package";
            $service = "Weddings";
            $subservice = "Dinner Package";
            $page = "weddings-hotel-admin";
            $note = "Add new dinner package : ".$hotels_id;
            $author = Auth::user()->id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$hotels_id,
                "page"=>$page,
                "user_id"=>$author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/weddings-hotel-admin-$hotels_id#dinner-package")->with('success', 'Dinner Package added successfully');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
     }
}