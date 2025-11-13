<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Brides;
use App\Models\Hotels;
use App\Models\Markup;
use App\Models\Orders;
use App\Models\Vendor;
use App\Models\UserLog;
use App\Models\Contract;
use App\Models\ExtraBed;
use App\Models\Services;
use App\Models\UsdRates;
use App\Models\Weddings;
use App\Models\ActionLog;
use App\Models\Attention;
use App\Models\HotelRoom;
use App\Models\Promotion;
use App\Models\HotelPrice;
use App\Models\HotelPromo;
use App\Models\Transports;
use Illuminate\Support\Str;
use App\Models\HotelPackage;
use App\Models\OptionalRate;
use App\Models\OrderWedding;
use Illuminate\Http\Request;
use App\Models\VendorPackage;
use App\Models\WeddingVenues;
use App\Models\TransportPrice;
use App\Models\WeddingPlanner;
use App\Models\BusinessProfile;
use App\Models\ContractWedding;
use App\Models\WeddingDecorations;
use App\Models\WeddingLunchVenues;
use Illuminate\Support\Facades\DB;
use App\Models\WeddingDinnerVenues;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use App\Models\WeddingDinnerPackages;
use Intervention\Image\Facades\Image;
use App\Models\WeddingReceptionVenues;
use App\Http\Requests\StoreWeddingsRequest;
use App\Http\Requests\UpdateWeddingsRequest;

class WeddingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index()
    {
        $now = Carbon::now();
        $taxes = Tax::where('id',1)->first();
        $weddings=Weddings::all();
        $activeweddings=Weddings::where('status','Active')->get();
        $draftweddings=Weddings::where('status','Draft')->get();
        $archivedweddings=Weddings::where('status','Archived')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $attentions = Attention::where('page','weddings-admin')->get();
        $service = Services::where('name','Weddings')->first();
        $hotels = Hotels::all();
        $ceremony_venues = WeddingVenues::all();
        $reception_venues = WeddingReceptionVenues::all();
        $contract_weddings = ContractWedding::all();
        foreach ($weddings as $wedding) {
            if ($wedding->period_start < $now and $wedding->period_end < $now) {
                $wedding->update([
                    "status"=>"Draft",
                ]);
            }
        }
        return view('admin.weddingsadmin', compact('weddings'),[
            'now'=>$now,
            'activeweddings'=>$activeweddings,
            'draftweddings'=>$draftweddings,
            'archivedweddings'=>$archivedweddings,
            'service'=>$service,
            'taxes'=>$taxes,
            "usdrates" => $usdrates,
            "attentions"=>$attentions,
            "hotels"=>$hotels,
            "ceremony_venues"=>$ceremony_venues,
            "reception_venues"=>$reception_venues,
            "contract_weddings"=>$contract_weddings,
        ]);
    }
// View User Wedding =========================================================================================>
    public function user_index()
    {
        $now = Carbon::now();
        $weddings=Weddings::where('status','Active')
        ->where("period_start",'<',$now)
        ->where("period_end",'>',$now)
        ->paginate(12)->withQueryString();
        $attentions = Attention::where('page','weddings-admin')->get();
        $service = Services::where('name','Weddings')->first();
        $hotels = Hotels::all();
        return view('main.wedding',[
            'service'=>$service,
            "attentions"=>$attentions,
            "hotels"=>$hotels,
        ])->with('weddings',$weddings);
    }

    // View Wedding Hotel =========================================================================================>
    public function view_wedding_hotel_admin_detail($id)
    {
        $taxes = Tax::where('id',1)->first();
        $now = Carbon::now();
        $hotel = Hotels::find($id);
        $usdrates = UsdRates::where('name','USD')->first();
        $wedding_contracts = ContractWedding::where('period_end','>',$now)->where('hotels_id',$id)->get();
        $user = Auth::user()->all();
        $author = Auth::user()->where('id',$hotel->author_id)->first();
        $attentions = Attention::where('page','wedding-hotel-admin')->get();
        $action_log = ActionLog::where('service',"Hotel")->get();
        $ceremonyVenues = WeddingVenues::where('hotels_id',$id)->where('periode_start','<=',$now)->where('periode_end','>=',$now)->get();
        $dinnerVenues = WeddingDinnerVenues::where('hotel_id',$id)->get();
        $dinnerPackages = WeddingDinnerPackages::where('hotels_id',$id)->get();
        $weddingPackages = Weddings::where('hotel_id',$hotel->id)->orderBy('updated_at','DESC')->get();
        $transports = Transports::where('status','Active')->get();
        $additionalServices = VendorPackage::where('type',"Other")->get();
        $receptionVenues = WeddingReceptionVenues::where('hotel_id',$id)->get();
        $receptionVenue = WeddingReceptionVenues::where('hotel_id',$id)->first();
        $lunchVenues = WeddingLunchVenues::where('hotel_id',$id)->get();
        $ceremonyVenueDecorations = WeddingDecorations::where('venue','Ceremony Venue')->get();
        $receptionVenueDecorations = WeddingDecorations::where('venue','Reception Venue')->get();
        return view('admin.wedding-hotel-detail',[
            'wedding_contracts'=>$wedding_contracts,
            'taxes'=>$taxes,
            'usdrates'=>$usdrates,
            'user'=>$user,
            'action_log'=>$action_log,
            'attentions'=>$attentions,
            'hotel'=>$hotel,
            'now'=>$now,
            'author'=>$author,
            'ceremonyVenues'=>$ceremonyVenues,
            'dinnerVenues'=>$dinnerVenues,
            'weddingPackages'=>$weddingPackages,
            'dinnerPackages'=>$dinnerPackages,
            'transports'=>$transports,
            'additionalServices'=>$additionalServices,
            'receptionVenues'=>$receptionVenues,
            'receptionVenue'=>$receptionVenue,
            'lunchVenues'=>$lunchVenues,
            'ceremonyVenueDecorations'=>$ceremonyVenueDecorations,
            'receptionVenueDecorations'=>$receptionVenueDecorations,
        ]);
    }
    // View Wedding Venue Edit =========================================================================================>
    public function view_edit_wedding_venue($id){
        $weddingVenue = WeddingVenues::find($id);
        $hotel = $weddingVenue->hotels;
        $attentions = Attention::where('page','edit-wedding-venue')->get();
        $slots = json_decode($weddingVenue->slot);
        return view('form.wedding-venue-edit',[
            'weddingVenue'=>$weddingVenue,
            'hotel'=>$hotel,
            'attentions'=>$attentions,
            'slots'=>$slots,
        ]);
    }
    // View Wedding Venue Add =========================================================================================>
    public function view_add_wedding_venue($id)
    {
        $now = Carbon::now();
        $attentions = Attention::where('page','weddings-admin')->get();
        $hotel = Hotels::where('id',$id)->first();
        $usdrates = UsdRates::where('name','USD')->first();
        return view('form.wedding-venue-add',[
            'hotel'=>$hotel,
            "attentions"=>$attentions,
            "now"=>$now,
            "usdrates"=>$usdrates,
        ]);
    }
    // VIEW ADD DECORATION CEREMONY VENUE =========================================================================================>
    public function view_add_decoration_ceremony_venue($id)
    {
        $now = Carbon::now();
        $attentions = Attention::where('page','add-decoration-ceremony-venue')->get();
        $hotel = Hotels::where('id',$id)->first();
        return view('admin.wedding.add-ceremony-venue-decoration',[
            'hotel'=>$hotel,
            "attentions"=>$attentions,
            "now"=>$now,
        ]);
    }
    // VIEW EDIT DECORATION CEREMONY VENUE =========================================================================================>
    public function view_edit_decoration_ceremony_venue($id)
    {
        $now = Carbon::now();
        $decoration = WeddingDecorations::find($id);
        $hotel = Hotels::where('id',$decoration->hotel_id)->first();
        $attentions = Attention::where('page','edit-decoration-ceremony-venue')->get();
        return view('admin.wedding.edit-ceremony-venue-decoration',[
            'decoration'=>$decoration,
            'hotel'=>$hotel,
            "attentions"=>$attentions,
            "now"=>$now,
        ]);
    }
    
    // Function Add Wedding Contract =========================================================================================>
    public function func_add_wedding_contract(Request $request){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $request->validate([
                'file_name' => 'required|mimes:pdf',
            ]);
            if($request->hasFile("file_name")){
                $filecontract=$request->file("file_name");
                $contractname=time()."_".$filecontract->getClientOriginalName();
                $filecontract->move("storage/hotels/wedding-contract/",$contractname);
                $period_start = date('Y-m-d',strtotime($request->period_start));
                $period_end = date('Y-m-d',strtotime($request->period_end));
                $hotels_id = $request->hotels_id;
            
                $contract =new ContractWedding([
                    "name"=>$request->name,
                    "hotels_id"=>$request->hotels_id,
                    "name"=>$request->contract_name,
                    "period_start"=>$period_start,
                    "period_end"=>$period_end,
                    "file_name"=>$contractname,
                    
                ]);
                $contract->save();
            }
            // USER LOG
            $action = "Add Wedding Contract";
            $service = "Hotel";
            $subservice = "Wedding Contract";
            $page = "weddings-hotel-detail";
            $note = "Add new wedding contract : ".$hotels_id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$hotels_id,
                "page"=>$page,
                "user_id"=>$request->author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/weddings-hotel-admin-$hotels_id")->with('success', 'Wedding contract added successfully');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
    // Function Edit Wedding Contract =============================================================================================================>
    public function func_edit_wedding_contract(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $contract=ContractWedding::findOrFail($id);
            $period_start = date('Y-m-d', strtotime($request->period_start));
            $period_end = date('Y-m-d', strtotime($request->period_end));
            if($request->hasFile("file_name")){
                if (File::exists("storage/hotels/wedding-contract/".$contract->file_name)) {
                    File::delete("storage/hotels/wedding-contract/".$contract->file_name);
                }
                $file=$request->file("file_name");
                $contract->file_name = time()."_".$file->getClientOriginalName();
                $file->move("storage/hotels/wedding-contract/",$contract->file_name);
                $file_name = $contract->file_name;
                
            }
            $contract->update([
                "name"=>$request->contract_name,
                "file_name"=>$contract->file_name,
                "period_start"=>$period_start,
                "period_end"=>$period_end,
            ]);

            // USER LOG
            $action = "Update Wedding Contract";
            $service = "Wedding";
            $subservice = "Contract";
            $page = "detail-hotel";
            $note = "Update wedding contract to Hotel id : ".$request->hotels_id;
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
            return redirect("/weddings-hotel-admin-$request->hotels_id")->with('success','Wedding Contract has been updated!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
    // Function Edit Wedding Information =============================================================================================================>
    public function func_edit_wedding_info(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $hotel=hotels::findOrFail($id);
            $hotel->update([
                "wedding_info"=>$request->wedding_info,
            ]);

            return redirect("/weddings-hotel-admin-$id#weddingInformation")->with('success','Other Information has been updated!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
    // Function Edit Cancellation Policy =============================================================================================================>
    public function func_update_cancellation_policy(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $hotel=hotels::findOrFail($id);
            $hotel->update([
                "wedding_cancellation_policy"=>$request->wedding_cancellation_policy,
            ]);
            return redirect("/weddings-hotel-admin-$id#cancellationPolicy")->with('success','Cancelation policy has been updated!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
    // Function Edit Entrance Fee =============================================================================================================>
    public function func_edit_entrance_fee(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $hotel=hotels::findOrFail($id);
            $hotel->update([
                "entrance_fee"=>$request->entrance_fee,
            ]);
            return redirect("/weddings-hotel-admin-$id#entranceFee")->with('success','Entrance fee has been updated!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
   
    // Function Delete Wedding Contract =============================================================================================================>
    public function delete_wedding_contract(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $contract=ContractWedding::findOrFail($id);
            if (File::exists("storage/hotels/wedding-contract/".$request->file_name)) 
            {
                File::delete("storage/hotels/wedding-contract/".$request->file_name);
            }
            $action = "Remove";
            $service = "Wedding";
            $subservice = "Contract";
            $page = "detail-hotel-wedding";
            $note = "Remove Wedding Contract on hotel : ".$request->hotels_id;
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
            $contract->delete();
            return redirect("/weddings-hotel-admin-$request->hotels_id")->with('success','The Wedding Contract has been deleted!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }


    //======================================================================================================================================================================
    // CEREMONY VENUE DECORATION
    //======================================================================================================================================================================
    // FUNCTION ADD DECORATION CEREMONY VENUE
    public function func_add_decoration_ceremony_venue(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            if($request->hasFile("cover")){
                $file=$request->file("cover");
                $coverName=time().'_'.$file->getClientOriginalName();
                $file->move("storage/hotels/weddings/decorations/",$coverName);
                $status = "Draft";
                $venue = "Ceremony Venue";
                $duration = 2;
                $author_id = Auth::user()->id;
                $wedding_venue_decoration =new WeddingDecorations([
                    "hotel_id"=>$id,
                    "venue"=>$venue,
                    "cover"=>$coverName,
                    "name"=>$request->name,
                    "duration"=>$duration,
                    "capacity" =>$request->capacity, 
                    "description"=>$request->description,
                    "terms_and_conditions"=>$request->terms_and_conditions,
                    "price"=>$request->price,
                    "status"=>$status,
                    "author_id"=>$author_id,
                ]);
                $wedding_venue_decoration->save();
            }
            return redirect("/weddings-hotel-admin-$id#ceremonyVenueDecorations")->with('success', 'Ceremony Venue Decoration added successfully');
        }else{
            return redirect("/weddings-hotel-admin-$id#ceremonyVenueDecorations")->with('error','Access denied');
        }
    }
    // FUNCTION EDIT DECORATION CEREMONY VENUE
    public function func_edit_decoration_ceremony_venue(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $decoration=WeddingDecorations::findOrFail($id);
            $hotel_id=$decoration->hotel_id;
            if($request->hasFile("cover")){
                if (File::exists("storage/hotels/weddings/decorations/".$decoration->cover)) {
                    File::delete("storage/hotels/weddings/decorations/".$decoration->cover);
                }
                $file=$request->file("cover");
                $decoration->cover=time()."_".$file->getClientOriginalName();
                $file->move("storage/hotels/weddings/decorations/",$decoration->cover);
                $request['cover']=$decoration->cover;
            }
            $decoration->update([
                "cover"=>$decoration->cover,
                "name"=>$request->name,
                "capacity" =>$request->capacity, 
                "description"=>$request->description,
                "terms_and_conditions"=>$request->terms_and_conditions,
                "price"=>$request->price,
                "status"=>$request->status,
            ]);
            return redirect("/weddings-hotel-admin-$hotel_id#ceremonyVenueDecorations")->with('success','Ceremony Venue Decoration has been updated!');
        }else{
            return redirect("/weddings-admin")->with('error','Access Denied');
        }
    }
    // FUNCTION SAVE TO DRAFT DECORATION CEREMONY VENUE
    public function func_save_to_draft_decoration_ceremony_venue(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $decoration=WeddingDecorations::findOrFail($id);
            $hotel_id=$decoration->hotel_id;
            $status = "Draft";
            $decoration->update([
                "status"=>$status,
            ]);
            return redirect("/weddings-hotel-admin-$hotel_id#ceremonyVenueDecorations")->with('success','Ceremony Venue Decoration has been updated!');
        }else{
            return redirect("/weddings-admin")->with('error','Access Denied');
        }
    }
    // FUNCTION SAVE TO ACTIVE DECORATION CEREMONY VENUE
    public function func_save_to_active_decoration_ceremony_venue(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $decoration=WeddingDecorations::findOrFail($id);
            $hotel_id=$decoration->hotel_id;
            $status = "Active";
            $decoration->update([
                "status"=>$status,
            ]);
            return redirect("/weddings-hotel-admin-$hotel_id#ceremonyVenueDecorations")->with('success','Ceremony Venue Decoration has been updated!');
        }else{
            return redirect("/weddings-admin")->with('error','Access Denied');
        }
    }
    // FUNCTION DELETE DECORATION CEREMONY VENUE
    public function destroy_decoration_ceremony_venue(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $ceremony_venue_decoration=WeddingDecorations::findOrFail($id);
            $hotel_id = $ceremony_venue_decoration->hotel_id;
            if (File::exists("storage/hotels/weddings/decorations/".$ceremony_venue_decoration->cover)) {
                File::delete("storage/hotels/weddings/decorations/".$ceremony_venue_decoration->cover);
            }
            $ceremony_venue_decoration->delete();
            return redirect("/weddings-hotel-admin-$hotel_id#ceremonyVenueDecorations")->with('success','Ceremony venue decoration has been successfully deleted!');
        }else{
            return redirect("/weddings-admin")->with('error','Access Denied');
        }
    }


    //======================================================================================================================================================================
    // CEREMONY VENUE
    //======================================================================================================================================================================
    // FUNCTION ADD CEREMONY VENUE
    public function func_add_wedding_venue(Request $request){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            if($request->hasFile("cover")){
                $author = Auth::user()->id;
                $file=$request->file("cover");
                $coverName=time().'_'.$file->getClientOriginalName();
                $file->move("storage/hotels/hotels-wedding-venue/",$coverName);
                $slot = json_encode($request->slot);
                $status="Draft";
                $service="Ceremony Venue";
                $action="Add Ceremony Venue";
                $arrangement_price = json_encode($request->arrangement_price);
                $basic_price = json_encode($request->basic_price);
                $periode_start = date('Y-m-d',strtotime($request->period_start));
                $periode_end = date('Y-m-d',strtotime($request->period_end));
                $wedding_venue =new WeddingVenues([
                    "hotels_id"=>$request->hotels_id,
                    "cover"=>$coverName,
                    "name"=>$request->name,
                    "slot"=>$slot,
                    "periode_start"=>$periode_start,
                    "periode_end"=>$periode_end,
                    "basic_price"=>$basic_price,
                    "arrangement_price"=>$arrangement_price,
                    "capacity" =>$request->capacity, 
                    "description"=>$request->description,
                    "term_and_condition"=>$request->term_and_condition,
                    "status"=>$status,
                    "author"=>$author,
                ]);
                $wedding_venue->save();
            }
            
            // USER LOG
            $action = "Add Ceremony Venue";
            $service = "Ceremony Venue";
            $subservice = "Ceremony Venue";
            $page = "add-wedding-venue";
            $note = "Add new ceremony venue at Hotel id : ".$request->hotels_id.", Ceremony Venue id : ".$wedding_venue->id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$request->hotels_id,
                "page"=>$page,
                "user_id"=>$request->author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/weddings-hotel-admin-$request->hotels_id#ceremonyVenue")->with('success', 'Ceremony Venue added successfully');
        }else{
            return redirect("/weddings-hotel-admin-$request->hotels_id#ceremonyVenue")->with('error','Access denied');
        }
    }
    // FUNCTION UPDATE CEREMONY VENUE
    public function func_edit_wedding_venue(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $wedding_venue=WeddingVenues::findOrFail($id);
            $hotel_id=$wedding_venue->hotels_id;
            $action="Update";
            $slot = json_encode($request->slot);
            $arrangement_price = json_encode($request->arrangement_price);
            $basic_price = json_encode($request->basic_price);
            if($request->hasFile("cover")){
                if (File::exists("storage/hotels/hotels-wedding-venue/".$wedding_venue->cover)) {
                    File::delete("storage/hotels/hotels-wedding-venue/".$wedding_venue->cover);
                }
                $file=$request->file("cover");
                $wedding_venue->cover=time()."_".$file->getClientOriginalName();
                $file->move("storage/hotels/hotels-wedding-venue/",$wedding_venue->cover);
                $request['cover']=$wedding_venue->cover;
            }
            $periode_start = date('Y-m-d',strtotime($request->period_start));
            $periode_end = date('Y-m-d',strtotime($request->period_end));
            $wedding_venue->update([
                "cover"=>$wedding_venue->cover,
                "hotels_id"=>$request->hotels_id,
                "name"=>$request->name,
                "slot"=>$slot,
                "periode_start"=>$periode_start,
                "periode_end"=>$periode_end,
                "basic_price"=>$basic_price,
                "arrangement_price"=>$arrangement_price,
                "capacity" =>$request->capacity, 
                "description"=>$request->description,
                "status"=>$request->status,
                "term_and_condition"=>$request->term_and_condition,
            ]);

            // USER LOG
            $action = "Update";
            $service = "Hotel";
            $subservice = "Wedding Venue";
            $page = "edit-wedding-venue";
            $note = "Update wedding venue on hotel : ".$request->hotels_id.", Wedding Venue id : ".$id;
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
            return redirect("/weddings-hotel-admin-$hotel_id#ceremonyVenue")->with('success','The wedding venue has been updated!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }
    // FUNCTION DELETE CEREMONY VENUE
    public function destroy_wedding_venue(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $wedding_venue=WeddingVenues::findOrFail($id);
            $hotel= Hotels::where('id','=',$wedding_venue->hotels_id)->first();
            $service_name=$wedding_venue->name;
            $service=$hotel->name;
            $action="Delete Wedding Venue";
            $author= Auth::user()->id;

            // USER LOG
            $action = "Remove";
            $service = "Hotel";
            $subservice = "Wedding Venue";
            $page = "detail-hotel#wedding-venues";
            $note = "Remove wedding venue on hotel : ".$request->hotels_id.", Wedding Venue id : ".$id;
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
            $wedding_venue->delete();
            return redirect("/weddings-hotel-admin-$hotel->id#wedding-venues")->with('success','The Wedding Venue has been successfully deleted!');
        }else{
            return redirect("/weddings-admin")->with('error','Akses ditolak');
        }
    }


// Search Wedding =========================================================================================>
    public function wedding_search(Request $request){
        $now = Carbon::now();
        $hotels = Hotels::all();
        $wedding_id = $request->get('hotel_id');
        $hotel_name = $request->hotel_name;
        $wedding_date = date('Y-m-d',strtotime($request->wedding_date));
        $hotel=Hotels::where('name','LIKE','%'.$hotel_name.'%')->first();
        if ($hotel_name) {
            $weddings=Weddings::where('status','Active')
            ->where("period_start",'<',$now)
            ->where("period_end",'>',$now)
            ->where("hotel_id",$hotel->id)
            ->paginate(12)->withQueryString();
        }else{
            $weddings=Weddings::where('status','Active')
            ->where("period_start",'<',$now)
            ->where("period_end",'>',$now)
            ->paginate(12)->withQueryString();
        }
        $vendors = Hotels::where('name','LIKE','%'.$hotel_name.'%')->get();
        $attentions = Attention::where('page','weddings-admin')->get();
        $service = Services::where('name','Weddings')->first();
        
        return view('main.wedding-search',[
            'service'=>$service,
            "attentions"=>$attentions,
            "hotels"=>$hotels,
            "vendors"=>$vendors,
        ])->with('weddings',$weddings);
        
    }

// View User Wedding Hotel =========================================================================================>
    public function view_wedding_hotel_detail($code){
        $now = Carbon::now();
        $attentions = Attention::where('page','wedding-hotel')->get();
        $hotel = Hotels::where('code',$code)->first();
        $rooms = HotelRoom::where('hotels_id',$hotel->id)->get();
        $service = Services::where('name','Weddings')->first();
        $agent_id = Auth::user()->id;
        $agents = Auth::user()->where('status','Active')->get();
        $brochures = ContractWedding::where('hotels_id',$hotel->id)
        ->where('period_end','>=',$now)
        ->get();
        $weddingVenues = WeddingVenues::where('hotels_id',$hotel->id)
        ->where('status',"Active")
        ->get();
        $weddingPackages = Weddings::where('hotel_id',$hotel->id)
        ->where('period_start','<=',$now)
        ->where('period_end','>=',$now)
        ->where('status','Active')
        ->get();
        $dinnerPackages = WeddingReceptionVenues::where('hotel_id',$hotel->id)
        ->where('status',"Active")
        ->where('periode_end','>=',$now)
        ->get();
        $dinnerVenues = WeddingDinnerVenues::where('hotel_id',$hotel->id)
        ->where('status',"Active")
        ->get();
        $hotels = Hotels::all();
        $wedding_planners = WeddingPlanner::where('wedding_venue_id',$hotel->id)
        ->where('wedding_date','>',$now)
        ->where('status','Draft')
        ->where('agent_id',$agent_id)
        ->get();
        $wedding_planner_ceremonial_venue_none = WeddingPlanner::where('wedding_venue_id',$hotel->id)
        ->where('wedding_date','>',$now)
        ->where('status','Draft')
        ->where('agent_id',$agent_id)
        ->whereNull('ceremonial_venue_id')
        ->get();
        $wedding_planner_reception_venue_none = WeddingPlanner::where('wedding_venue_id',$hotel->id)
        ->where('wedding_date','>',$now)
        ->where('status','Draft')
        ->where('agent_id',$agent_id)
        ->whereNull('dinner_venue_id')
        ->get();
        $reception_venues = WeddingReceptionVenues::where('status','Active')->get();
        $vendor_packages = VendorPackage::all();
        $brides = Brides::all();
        $transports = Transports::where('status','Active')->get();

        $carbonDate = Carbon::parse("2024-09-14");
        $weekday = $carbonDate->format('l');
        return view('main.wedding-hotel',[
            'agents'=>$agents,
            'now'=>$now,
            'attentions'=>$attentions,
            'hotel'=>$hotel,
            'service'=>$service,
            'brochures'=>$brochures,
            'weddingVenues'=>$weddingVenues,
            'weddingPackages'=>$weddingPackages,
            'dinnerPackages'=>$dinnerPackages,
            'dinnerVenues'=>$dinnerVenues,
            'hotels'=>$hotels,
            'rooms'=>$rooms,
            'wedding_planners'=>$wedding_planners,
            'vendor_packages'=>$vendor_packages,
            'brides'=>$brides,
            'wedding_planner_ceremonial_venue_none'=>$wedding_planner_ceremonial_venue_none,
            'wedding_planner_reception_venue_none'=>$wedding_planner_reception_venue_none,
            'reception_venues'=>$reception_venues,
            'transports'=>$transports,
            'weekday'=>$weekday,
        ]);
    }
// View Admin Detail Wedding =========================================================================================>
    public function view_wedding_admin_detail($id)
    {
        $wedding = Weddings::find($id);
        $service = Services::where('name','Weddings')->first();
        $taxes = Tax::where('id',1)->first();
        if ($wedding) {
            $usdrates = UsdRates::where('name','USD')->first();
            $attentions = Attention::where('page','weddings-admin-detail')->get();
            $vendors = Vendor::where('status','Active')->get();
            $hotels = Hotels::where('status', 'Active')->get();
            $hotel = Hotels::where('id', $wedding->hotel_id)->first();
            $room_price = HotelPrice::where('hotels_id',$hotel->id)->get();
            $fixed_services = VendorPackage::where('status','Active')->where('type','Fixed Service')->get();
            $suite_and_villas = HotelRoom::where('status', "Active")->where('hotels_id',$wedding->hotel_id)->get();
            
            $transport_prices = TransportPrice::where('type','Airport Shuttle')->get();
            $transports = Transports::where('status', "Active")->get();

            $wedding_venue = WeddingVenues::where('id', $wedding->ceremony_venue_id)->first();
            $wedding_venues = VendorPackage::where('status', "Active")->where('type','Wedding Venue')->where('hotel_id',$wedding->hotel_id)->get();
            $muas = VendorPackage::where('status', "Active")->where('type',"Make-up")->get();
            $dinner_venues = VendorPackage::where('status', "Active")->where('type','Wedding Dinner')->get();
            $decorations = VendorPackage::where('status', "Active")->where('type','Decoration')->get();
            $entertainments = VendorPackage::where('status', "Active")->where('type','Entertainment')->get();
            $documentations = VendorPackage::where('status', "Active")->where('type','Documentation')->get();
            $other_services = VendorPackage::where('status', "Active")->where('type','Other')->get();
            $transport_service = VendorPackage::where('status', "Active")->where('type','Transport')->get();
            $suite_and_villas_price = $room_price->sum('contract_rate');
            
            return view('admin.weddingsadmindetail',[
                "service"=>$service,
                'taxes'=>$taxes,
                "wedding"=>$wedding,
                "usdrates" => $usdrates,
                "attentions"=>$attentions,
                "suite_and_villas"=>$suite_and_villas,
                "wedding_venues"=>$wedding_venues,
                "wedding_venue"=>$wedding_venue,
                "muas"=>$muas,
                "dinner_venues"=>$dinner_venues,
                "decorations"=>$decorations,
                "entertainments"=>$entertainments,
                "documentations"=>$documentations,
                "other_services"=>$other_services,
                "hotels"=>$hotels,
                "hotel"=>$hotel,
                "vendors"=>$vendors,
                "room_price"=>$room_price,
                "transports"=>$transports,
                "transport_prices"=>$transport_prices,
                'suite_and_villas_price'=>$suite_and_villas_price,
                'transport_service'=>$transport_service,
                'fixed_services'=>$fixed_services,
            ]);
        }else{
            return redirect("/weddings-admin")->with('success','Wedding Package not found!');
        }
    }

// View Admin Edit Wedding =============================================================================================================>
    public function view_edit_wedding($id)
    {
        $wedding=Weddings::findOrFail($id);
        $service = Services::where('name','Weddings')->first();
        $attentions = Attention::where('page','wedding-admin-edit')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $hotel = Hotels::where('id',$wedding->hotel_id)->firstOrFail();
        $hotels = Hotels::where('status','Active')->get();
        $durationday = $wedding->duration;
        $duration_day = substr($wedding->duration,0,-4);
        $duration_night = substr($wedding->duration,3,-1);
        $suite_and_villa = HotelRoom::where('id',$wedding->suites_and_villas_id)->first();
        $transports = Transports::where('status',"Active")->get();
        if ($wedding->bride_transport_id) {
            $tw_id = json_decode($wedding->bride_transport_id);
            $transport_wedding_id = $tw_id[0];
            $transport_wedding = $transports->where('id',$transport_wedding_id)->first();
        }else {
            $transport_wedding = NULL;
        }
        if ($wedding->status == "Active") {
            return redirect("/weddings-admin")->with('error',"Wedding package cannot be changed!");
        }else{
            return view('form.weddingedit',[
                "service"=>$service,
                'usdrates'=>$usdrates,
                'attentions'=>$attentions,
                'hotel'=>$hotel,
                'hotels'=>$hotels,
                'duration_day'=>$duration_day,
                'duration_night'=>$duration_night,
                'suite_and_villa'=>$suite_and_villa,
                'transports'=>$transports,
                'transport_wedding'=>$transport_wedding,
            ])->with('wedding',$wedding);
        }
    }

// View Admin Add Wedding =============================================================================================================>
    public function view_add_wedding_package($id) {
        $service = Services::where('name','Weddings')->first();
        $attentions = Attention::where('page','wedding-add')->get();
        $hotel = Hotels::where('id',$id)->first();
        $rooms = HotelRoom::where('hotels_id',$hotel->id)->where('status','Active')->get();
        $weddingVenues = WeddingVenues::where('status','Active')->where('hotels_id',$hotel->id)->get();
        
        $vendorWeddingVenueDecorations = VendorPackage::where('type',"Ceremony Venue Decoration")->where('status','Active')->get();
        $weddingVenueDecorations = WeddingDecorations::where('hotel_id',$hotel->id)->where('status','Active')->get();
        $receptionVenues = WeddingReceptionVenues::where('status',"Active")->where('hotel_id',$hotel->id)->get();
        $lunchVenues = WeddingLunchVenues::where('status',"Active")->where('hotel_id',$hotel->id)->get();
        $dinnerVenues = WeddingDinnerVenues::where('status',"Active")->where('hotel_id',$hotel->id)->get();
        $receptionVenueDecorations = VendorPackage::where('type',"Reception Venue Decoration")->where('status','Active')->get();
        $transports = Transports::where('status',"Active")->get();
        $additionalServices = VendorPackage::where('type','!=',"Reception Venue Decoration")->where('type','!=',"Ceremony Venue Decoration")->where('status','Active')->get();
        // $weddingVenues = $hotel->weddingVenues()->pluck('name', 'id');
        return view('form.weddingadd', [
            "rooms" => $rooms,
            "service" => $service,
            'attentions' => $attentions,
            'hotel' => $hotel,
            'weddingVenues' => $weddingVenues,
            'receptionVenues' => $receptionVenues,
            'dinnerVenues' => $dinnerVenues,
            'lunchVenues' => $lunchVenues,
            'vendorWeddingVenueDecorations' => $vendorWeddingVenueDecorations,
            'weddingVenueDecorations' => $weddingVenueDecorations,
            'receptionVenueDecorations' => $receptionVenueDecorations,
            'transports' => $transports,
            'additionalServices' => $additionalServices,
        ]);
    }
// View Admin Edit Wedding =============================================================================================================>
    public function view_edit_wedding_package($id) {
        $wedding = Weddings::find($id);
        $hotel = Hotels::where('id',$wedding->hotel_id)->first();
        if ($wedding->status == "Draft") {
            $usdrates = UsdRates::where('name','USD')->first();
            $ceremonyVenue = WeddingVenues::where('id',$wedding->ceremony_venue_id)->first();
            $ceremonyVenues = WeddingVenues::where('status','Active')->where('hotels_id',$wedding->hotel_id)->get();
            $ceremonyVenueDecoration = VendorPackage::where('id',$wedding->ceremony_venue_decoration_id)->first();
            $ceremonyVenueDecorations = VendorPackage::where('type',"Ceremony Venue Decoration")->where('status','Active')->get();
            $receptionVenues = WeddingReceptionVenues::where('status',"Active")->where('hotel_id',$hotel->id)->get();
            $receptionVenue = WeddingReceptionVenues::where('id',$wedding->reception_venue_id)->first();
            $receptionVenueDecoration = VendorPackage::where('id',$wedding->reception_venue_decoration_id)->first();
            $receptionVenueDecorations = VendorPackage::where('type',"Reception Venue Decoration")->where('status','Active')->get();
            $lunchVenues = WeddingLunchVenues::where('hotel_id',$hotel->id)->where('status','Active')->get();
            $lunchVenue = WeddingLunchVenues::where('id',$wedding->lunch_venue_id)->first();
            $dinnerVenues = WeddingDinnerVenues::where('hotel_id',$hotel->id)->where('status','Active')->get();
            $dinnerVenue = WeddingDinnerVenues::where('id',$wedding->dinner_venue_id)->first();
            $attentions = Attention::where('page','edit-wedding-package')->get();
            $hotel = Hotels::where('id',$wedding->hotel_id)->first();
            $rooms = HotelRoom::where('hotels_id',$hotel->id)->where('status','Active')->get();
            $suite_and_villa = HotelRoom::where('id',$wedding->suites_and_villas_id)->first();
            $d_day = $wedding->duration;
            $duration_day = substr($d_day,0,1);
            $duration_night = substr($d_day,3,1);
            $transports = Transports::where('status',"Active")->get();
            if ($wedding->bride_transport_id) {
                $tw_id = json_decode($wedding->bride_transport_id);
                $transport_wedding_id = $tw_id[0];
                $transport_wedding = $transports->where('id',$transport_wedding_id)->first();
            }else {
                $transport_wedding = NULL;
            }
            $additionalServices = VendorPackage::all();
            $additionalServiceWeddingId = json_decode($wedding->additional_service_id);
            $slots = json_decode($wedding->slot);
            // $weddingVenues = $hotel->weddingVenues()->pluck('name', 'id');
            return view('form.weddingedit', [
                "suite_and_villa" => $suite_and_villa,
                "rooms" => $rooms,
                "wedding" => $wedding,
                'attentions' => $attentions,
                'hotel' => $hotel,
                'ceremonyVenue' => $ceremonyVenue,
                'ceremonyVenues' => $ceremonyVenues,
                'ceremonyVenueDecoration' => $ceremonyVenueDecoration,
                'ceremonyVenueDecorations' => $ceremonyVenueDecorations,
                'duration_day' => $duration_day,
                'duration_night' => $duration_night,
                'usdrates' => $usdrates,
                'transports' => $transports,
                'receptionVenues' => $receptionVenues,
                'receptionVenue' => $receptionVenue,
                'receptionVenueDecoration' => $receptionVenueDecoration,
                'receptionVenueDecorations' => $receptionVenueDecorations,
                'lunchVenues' => $lunchVenues,
                'lunchVenue' => $lunchVenue,
                'dinnerVenues' => $dinnerVenues,
                'dinnerVenue' => $dinnerVenue,
                'additionalServices' => $additionalServices,
                'additionalServiceWeddingId' => $additionalServiceWeddingId,
                'hotel' => $hotel,
                'slots' => $slots,
                'transport_wedding' => $transport_wedding,
            ]);
        }else{
            return redirect("/weddings-hotel-admin-$hotel->id#weddingPackage")->with('success',"Wedding Package can't be updated!");
        }
    }

    // GET DATA CEREMONY VENUE DECORATION USING AJAX
    public function getCeremonyDecorations(Request $request)
    {
        $ceremonyVenueId = $request->get('venue_id');
        $ceremonyVenueDecorations = VendorPackage::where('type', "Ceremony Venue Decoration")
            ->where('status', 'Active')
            ->get();
        return response()->json($ceremonyVenueDecorations);
    }
    // GET DATA RECEPTION VENUE DECORATION USING AJAX
    public function getReceptionDecorations(Request $request)
    {
        $receptionVenueId = $request->get('venue_id');
        $receptionVenueDecorations = VendorPackage::where('type', "Reception Venue Decoration")
            ->where('status', 'Active')
            ->get();
        return response()->json($receptionVenueDecorations);
    }

// function Update Wedding =============================================================================================================>
    public function func_edit_wedding_package(Request $request,$id)
    {
        $now = Carbon::now();
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $wedding=Weddings::find($id);
        $hotel = Hotels::where('id',$wedding->hotel_id)->first();
        if($request->hasFile("cover")){
            if (File::exists("storage/weddings/wedding-cover/".$wedding->cover)) {
                File::delete("storage/weddings/wedding-cover/".$wedding->cover);
            }
            $file=$request->file("cover");
            $wedding->cover=time()."_".$file->getClientOriginalName();
            $file->move("storage/weddings/wedding-cover/",$wedding->cover);
        }
        $slot = json_encode($request->slot);
        $arrangement_price = json_encode($request->arrangement_price);
        $basic_price = json_encode($request->basic_price);
        $ceremony_venue_price = $request->ceremony_venue_price;
        $period_start = date('Y-m-d',strtotime($request->period_start));
        $period_end = date('Y-m-d',strtotime($request->period_end));
        $duration = $request->duration;
        $contract_rate = str_replace(",", "", $request->contract_rate);
        $markup = str_replace(",", "", $request->markup);
        $prate = str_replace(",", "", $request->publish_rate);
        $s_and_v = HotelRoom::where('id',$request->suites_and_villas_id)->first();
        $suite_and_villas_average_price = $request->suites_and_villas_price;
        $reception_venue_price = $request->reception_venue_price;
        $reception_venue_decoration = VendorPackage::where('id',$request->reception_venue_decoration_id)->first();
        $reception_venue_decoration_price = $request->reception_venue_decoration_price;
        $ceremony_venue_decoration = VendorPackage::where('id',$request->ceremony_venue_decoration_id)->first();
        $ceremony_venue_decoration ? $ceremony_venue_decoration_price = $ceremony_venue_decoration->publish_rate:$ceremony_venue_decoration_price = NULL;
        $transport = Transports::where('id',$request->transport_id)->first();
        if ($transport) {
            $bride_transport_type = json_encode(["In","Out"]);
            $bride_transport_id = json_encode(["$transport->id","$transport->id"]);
            $bride_transport_price= $request->bride_transport_price;
        }else {
            $bride_transport_price= NULL;
            $bride_transport_type= NULL;
            $bride_transport_id= NULL;
        }
        $adserid = json_encode($request->additional_service);
        $additional_service_id = $request->additional_service;
        $gpr_adser = [];
        if ($additional_service_id) {
            foreach ($additional_service_id as $adser_key => $adser_id) {
                $vp_addser = VendorPackage::where('id',$adser_id)->first();
                array_push($gpr_adser,$vp_addser->publish_rate);
            }
        }
        $lunch_venue = WeddingLunchVenues::where('id',$request->lunch_venue_id)->first();
        $lunch_venue ? $lunch_venue_price = $lunch_venue->publish_rate : $lunch_venue_price = NULL;
        $dinner_venue = WeddingDinnerVenues::where('id',$request->dinner_venue_id)->first();
        $dinner_venue ? $dinner_venue_price = $dinner_venue->publish_rate : $dinner_venue_price = NULL;

        $additional_service_price = array_sum($gpr_adser);
        $wedding->update([
            "cover" =>$wedding->cover,
            "name"=>$request->name,
            "duration"=>$duration,
            "capacity"=>$request->capacity,
            "slot"=>$slot,
            "arrangement_price"=>$arrangement_price,
            "basic_price"=>$basic_price,
            "suites_and_villas_id"=>$request->suites_and_villas_id,
            "suites_and_villas_price"=>$suite_and_villas_average_price,
            "ceremony_venue_id"=>$request->ceremony_venue_id,
            "ceremony_venue_price"=>intval($ceremony_venue_price),
            "ceremony_venue_decoration_id"=>$request->ceremony_venue_decoration_id,
            "ceremony_venue_decoration_price"=>$ceremony_venue_decoration_price,
            "reception_venue_id"=>$request->reception_venue_id,
            "reception_venue_price"=>intval($reception_venue_price),
            "reception_venue_decoration_id"=>$request->reception_venue_decoration_id,
            "reception_venue_decoration_price"=>intval($reception_venue_decoration_price),
            "lunch_venue_id"=>$request->lunch_venue_id,
            "lunch_venue_price"=>$lunch_venue_price,
            "dinner_venue_id"=>$request->dinner_venue_id,
            "dinner_venue_price"=>$dinner_venue_price,
            "additional_service_id"=>$adserid,
            "additional_service_price"=>intval($additional_service_price),
            "include"=>$request->include,
            "description"=>$request->description,
            "additional_info"=>$request->additional_info,
            "cancellation_policy"=>$request->cancellation_policy,
            "terms_and_conditions"=>$request->terms_and_conditions,
            "transport_id"=>$request->transport_id,
            "period_start"=>$period_start,
            "period_end"=>$period_end,
            "payment_process"=>$request->payment_process,
            "remark"=>$request->remark,
            "markup"=>$request->markup,
            "publish_rate"=>$request->publish_rate,
            "status" =>$request->status,
        ]);
        // dd($wedding);
        return redirect("/weddings-hotel-admin-$hotel->id#weddingPackages")->with('success','The Wedding has been successfully updated!');
    }
// FUNCTION ADD TO WEDDING PLANNER =============================================================================================================>
    public function func_add_package_to_wedding_planner(Request $request,$id)
    {
        $wedding_planner_id = $request->wedding_planner;
        $wedding_planner = WeddingPlanner::where('id',$wedding_planner_id)->first();
        $wedding_planner->update([
            "wedding_package_id"=>$id,
        ]);
        return redirect("/edit-wedding-planner-$wedding_planner->id")->with('success','The Wedding package has been added to wedding planner!');
    }
// function Activate Wedding Package =============================================================================================================>
    public function func_activate_wedding_package(Request $request,$id)
    {
        $wedding=Weddings::find($id);
        $status = "Active";
        $author = Auth::user()->id;
        $wedding->update([
            "status" =>$status,
        ]);

        // USER LOG
        $action = "Activate Wedding Package";
        $service = "Wedding";
        $subservice = "Wedding";
        $page = "wedding-edit";
        $note = "Update Wedding: ".$id;
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
        return redirect("/weddings-hotel-admin-$wedding->hotel_id#weddingPackage")->with('success','The Wedding has been successfully Activate!');
    }
// function Draft Wedding Package =============================================================================================================>
    public function func_draft_wedding_package(Request $request,$id)
    {
        $wedding=Weddings::find($id);
        $status = "Draft";
        $author = Auth::user()->id;
        $wedding->update([
            "status" =>$status,
        ]);

        // USER LOG
        $action = "Drafted Wedding Package";
        $service = "Wedding";
        $subservice = "Wedding";
        $page = "wedding-edit";
        $note = "Update Wedding: ".$id;
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
        return redirect("/weddings-hotel-admin-$wedding->hotel_id#weddingPackage")->with('success','The Wedding has been successfully save to Draft!');
    }
// function Drafted Wedding Package =============================================================================================================>
    public function func_drafted_wedding_package(Request $request,$id)
    {
        $wedding=Weddings::findOrFail($id);
        $status = "Draft";
        $wedding->update([
            "status" =>$status,
        ]);

        // USER LOG
        $action = "Drafted Wedding Package";
        $service = "Wedding";
        $subservice = "Wedding";
        $page = "wedding-edit";
        $note = "Update Wedding: ".$id;
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
        return redirect("/weddings-admin-$id")->with('success','The Wedding has been successfully Drafted!');
    }
// function Removed Wedding Package =============================================================================================================>
    public function func_removed_wedding_package(Request $request,$id)
    {
        $wedding=Weddings::findOrFail($id);
        $status = "Removed";
        $wedding->update([
            "status" =>$status,
        ]);

        // USER LOG
        $action = "Drafted Wedding Package";
        $service = "Wedding";
        $subservice = "Wedding";
        $page = "wedding-edit";
        $note = "Update Wedding: ".$id;
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
        return redirect("/weddings-admin")->with('success','The Wedding has been successfully Removed!');
    }

// function Add Wedding Fixed Service =============================================================================================================>
    public function func_add_wedding_fixed_service(Request $request,$id)
    {
        $wedding=Weddings::findOrFail($id);
        $fixedServices_id = $request->fixed_services_id;
        $fixed_services_id = json_encode($fixedServices_id);
        $wedding->update([
            "fixed_services_id"=>$fixed_services_id,
        ]);
        return redirect("/weddings-admin-$id")->with('success','The Wedding fixed service has been successfully updated!');
    }
// function Add Wedding Decoration =============================================================================================================>
    public function func_add_wedding_decoration(Request $request,$id)
    {
        $wedding=Weddings::findOrFail($id);
        $venues_id = $request->decorations_id;
        $decorations_id = json_encode($venues_id);
        $wedding->update([
            "decorations_id"=>$decorations_id,
        ]);
        return redirect("/weddings-admin-$id")->with('success','The Wedding decoration has been successfully updated!');
    }
// function Add Wedding Dinner Venue =============================================================================================================>
    public function func_add_wedding_dinner_venue(Request $request,$id)
    {
        $wedding=Weddings::findOrFail($id);
        $dinner_venues_id = $request->dinner_venues_id;
        $dinnervenue_id = json_encode($dinner_venues_id);
        $wedding->update([
            "dinner_venue_id"=>$dinnervenue_id,
        ]);
        // dd($wedding);
        return redirect("/weddings-admin-$id")->with('success','The Dinner Venue has been successfully updated!');
    }
// function Add Wedding Makeup =============================================================================================================>
    public function func_add_wedding_makeup(Request $request,$id)
    {
        $wedding=Weddings::findOrFail($id);
        $makeup_id = $request->makeup_id;
        $makeups_id = json_encode($makeup_id);
        $wedding->update([
            "makeup_id"=>$makeups_id,
        ]);
        // dd($wedding);
        return redirect("/weddings-admin-$id")->with('success','The Makeup has been successfully updated!');
    }
// function Add Wedding Entertainment =============================================================================================================>
    public function func_add_wedding_entertainment(Request $request,$id)
    {
        $wedding=Weddings::findOrFail($id);
        $entertainments_id = $request->entertainments_id;
        $entertainment_id = json_encode($entertainments_id);
        $wedding->update([
            "entertainments_id"=>$entertainment_id,
        ]);
        // dd($wedding);
        return redirect("/weddings-admin-$id")->with('success','The Entertainment has been successfully updated!');
    }
// function Add Wedding Documentation =============================================================================================================>
    public function func_add_wedding_documentation(Request $request,$id)
    {
        $wedding=Weddings::findOrFail($id);
        $documentations_id = $request->documentations_id;
        $documentation_id = json_encode($documentations_id);
        $wedding->update([
            "documentations_id"=>$documentation_id,
        ]);
        // dd($wedding);
        return redirect("/weddings-admin-$id")->with('success','The Documentation has been successfully updated!');
    }
// function Add Wedding Other Servie =============================================================================================================>
    public function func_add_wedding_other(Request $request,$id)
    {
        $wedding=Weddings::findOrFail($id);
        $other_id = $request->other_service_id;
        $other_service_id = json_encode($other_id);
        $wedding->update([
            "other_service_id"=>$other_service_id,
        ]);
        // dd($wedding);
        return redirect("/weddings-admin-$id")->with('success','The Other Service has been successfully updated!');
    }
// function Add Wedding Room =============================================================================================================>
    public function func_add_wedding_room(Request $request,$id)
    {
        $wedding=Weddings::findOrFail($id);
        $rooms_id = $request->rooms_id;
        $suite_and_villas_id = json_encode($rooms_id);
        $wedding->update([
            "suite_and_villas_id"=>$suite_and_villas_id,
        ]);
        // dd($wedding);
        return redirect("/weddings-admin-$id")->with('success','The Other Service has been successfully updated!');
    }
// function Add Wedding Transport =============================================================================================================>
    public function func_add_wedding_transport(Request $request,$id)
    {
        $wedding=Weddings::findOrFail($id);
        $tr_id = $request->transport_id;
        $transport_id = json_encode($tr_id);
        $wedding->update([
            "transport_id"=>$transport_id,
        ]);
        // dd($wedding);
        return redirect("/weddings-admin-$id")->with('success','Transport Service has been successfully updated!');
    }
// function Add Wedding Price =============================================================================================================>
    public function func_add_wedding_price(Request $request,$id)
    {
        $wedding=Weddings::findOrFail($id);
        $publish_rate = $request->total_service + $request->markup;
        $wedding->update([
            "price"=>$publish_rate,
            "markup"=>$request->markup,
        ]);
        // USER LOG
        $action = "Update wedding Markup";
        $service = "Wedding Package";
        $subservice = "Service";
        $page = "Weddings-admin-".$wedding->id;
        $note = "Update markup : ".$wedding->id;
        $author = Auth::user()->id;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$wedding->id,
            "page"=>$page,
            "user_id"=>$author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        // dd($wedding);
        return redirect("/weddings-admin-$id#wedding-price")->with('success','Wedding Price has been successfully updated!');
    }


// function Refresh Wedding Price =============================================================================================================>
    public function func_refresh_wedding_price(Request $request,$id)
    {
        $wedding=Weddings::findOrFail($id);
        $publish_rate = $request->total_service + $request->markup;
        $wedding->update([
            "price"=>$publish_rate,
            "markup"=>$request->markup,
        ]);
        // USER LOG
        $action = "Refresh wedding Markup";
        $service = "Wedding Package";
        $subservice = "Service";
        $page = "Weddings-admin-".$wedding->id;
        $note = "Refresh markup : ".$wedding->id;
        $author = Auth::user()->id;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$wedding->id,
            "page"=>$page,
            "user_id"=>$author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        // dd($wedding);
        return redirect("/weddings-admin-$id#wedding-price")->with('success','Wedding Price has been successfully updated!');
    }

    //======================================================================================================================================================================
    // WEDDING PACKAGE
    //======================================================================================================================================================================
    // FUNCTION ADD WEDDING PACKAGE
    public function func_add_wedding_package(Request $request,$id){
        $code = Str::random(26);
        $now = Carbon::now();
        $attentions = Attention::where('page','add-wedding')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $hotel = Hotels::find($id);
        if($request->hasFile("cover")){
            $file=$request->file("cover");
            $coverName=time().'_'.$file->getClientOriginalName();
            $file->move("storage/weddings/wedding-cover/",$coverName);
            $additional_service_id = json_encode($request->additional_service);
            $period_start = date('Y-m-d',strtotime($request->period_start));
            $period_end = date('Y-m-d',strtotime($request->period_end));
            $status = "Draft";
            $author_id = Auth::user()->id;
            $slot = json_encode($request->slot);
            $wedding =new Weddings([
                "code"=>$code,
                "hotel_id"=>$hotel->id,
                "cover"=>$coverName,
                "name"=>$request->name,
                "duration"=>$request->duration,
                "capacity"=>$request->capacity,
                "suites_and_villas_id"=>$request->suites_and_villas_id,
                "ceremony_venue_id"=>$request->ceremony_venue_id,
                "ceremony_venue_decoration_id"=>$request->ceremony_venue_decoration_id,
                "reception_venue_id"=>$request->reception_venue_id,
                "reception_venue_decoration_id"=>$request->reception_venue_decoration_id,
                // "lunch_venue_id"=>$request->lunch_venue_id,
                // "dinner_venue_id"=>$request->dinner_venue_id,
                "transport_id"=>$request->transport_id,
                "additional_service_id"=>$additional_service_id,
                "include"=>$request->include,
                "cancellation_policy"=>$request->cancellation_policy,
                "period_start"=>$period_start,
                "period_end"=>$period_end,
                "payment_process"=>$request->payment_process,
                "week_day_price"=>$request->week_day_price,
                "holiday_price"=>$request->holiday_price,
                "slot"=>$slot,
                "terms_and_conditions"=>$request->terms_and_conditions,
                "status"=>$status,
                "author_id"=>$author_id,
            ]);
            // dd($wedding);
            $wedding->save();
        }
        // USER LOG
        $action = "Add Wedding";
        $service = "Wedding";
        $subservice = "Wedding";
        $page = "add-wedding-package";
        $note = "Add new Wedding with Wedding id : ".$wedding->id;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$wedding->id,
            "page"=>$page,
            "user_id"=>$request->author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        return redirect("/edit-wedding-package-$wedding->id")->with('success', 'Wedding package added successfully');
    }
// function Remove Weddings =============================================================================================================>
    public function destroy_wedding(Request $request,$id)
    {
        $wedding=Weddings::findOrFail($id);
        $hotel_id = $wedding->hotel_id;
        if (File::exists("storage/weddings/wedding-cover/".$wedding->cover)) {
            File::delete("storage/weddings/wedding-cover/".$wedding->cover);
        }
        if (File::exists("storage/weddings/wedding-pdf/".$wedding->pdf)) {
            File::delete("storage/weddings/wedding-pdf/".$wedding->pdf);
        }
        
        $wedding->delete();
        // USER LOG
        $action = "Remove";
        $service = "Wedding";
        $subservice = "Wedding Price";
        $page = "weddings-admin";
        $note = "Remove Wedding : ".$id;
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
        return redirect("/weddings-hotel-admin-$hotel_id#weddingPackage")->with('success','The Wedding has been successfully deleted!');
    }
// function Remove Weddings =============================================================================================================>
    public function destroy_dinner_venue(Request $request,$id)
    {
        $dinner_venue=WeddingDinnerVenues::findOrFail($id);
        $hotel_id = $dinner_venue->hotels_id;
        if (File::exists("storage/weddings/dinner-venue/".$dinner_venue->cover)) {
            File::delete("storage/weddings/dinner-venue/".$dinner_venue->cover);
        }
        $dinner_venue->delete();
        // USER LOG
        $action = "Remove";
        $service = "Wedding";
        $subservice = "Dinner Venue";
        $page = "weddings-hotel-admin";
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
        return redirect("/weddings-hotel-admin-$hotel_id#dinner-venue")->with('success','Dinner Venue has been successfully deleted!');
    }
// DOWNLOAD WEDDING PDF =============================================================================================================>
    public function download_pdf(Request $request)
    {
    	$myFile = "storage/weddings/wedding-pdf/".$request->name_pdf;
        $headers = ['Content-Type: application/pdf'];
    	$fileName = $request->name_pdf;
    	return response()->download($myFile, $fileName, $headers);
    }


// FUNCTION ORDER WEDDING =============================================================================================================>
    public function func_order_wedding($id)
    {
    }
}
