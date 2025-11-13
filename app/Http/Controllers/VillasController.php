<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
use App\Models\User;
use App\Models\Villas;
use App\Models\UserLog;
use App\Models\Contract;
use App\Models\UsdRates;
use App\Models\Attention;
use App\Models\Promotion;
use App\Models\VillaPrice;
use App\Models\VillaRooms;
use App\Models\OptionalRate;
use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreVillasRequest;
use App\Http\Requests\UpdateVillasRequest;

class VillasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    // User View Villas =====================================================================================================================>
    public function index(Request $request)
    {
        $now = Carbon::now();
        $promotions = Promotion::select('name', 'discounts', 'periode_start', 'periode_end')
            ->where('status', "Active")
            ->where('periode_start', '<=', $now)
            ->where('periode_end', '>=', $now)
            ->get();
        $villas = $this->getVillasQuery($request)->paginate(12);
        return view('villas.index', compact('villas', 'promotions'));
    }

    // Admin View Villas =====================================================================================================================>
    public function admin_index(Request $request)
    {
        $villas = Villas::with('stay_period','rooms')
        ->get();
        $regions = Villas::select('region')->distinct()->pluck('region');
        $cactivevillas= Villas::where('status','Active')->get();
        $draftvillas=Villas::where('status','Draft')->get();
        $archivevillas=Villas::where('status', '=','Archived')->get();
        return view('admin.villas.index', compact(
            'villas', 
            'regions',
            'cactivevillas',
            'draftvillas',
            'archivevillas',
        ));
    }

    // View Villa Detail =====================================================================================================================>
    public function admin_villa_detail($id){
        $now = Carbon::now();
        $tax = Cache::remember('tax', 3600, function () {
            return Tax::select('name', 'tax')->where('name', 'tax')->first();
        });
        $business = Cache::remember('business_profile', 3600, function () {
            return BusinessProfile::select('id', 'name', 'address')->first();
        });
        $usdrates = Cache::remember('usd_rates', 3600, function () {
            return UsdRates::select('name', 'rate')->where('name', 'USD')->first();
        });
        $attentions = Attention::where('page','admin-villa-detail')->get();
        $villa = Villas::with([
            'prices' =>fn($q) => $q->where('end_date', '>', $now)->orderBy('start_date', 'asc'),
            'stay_period',
            'rooms',
            'galleries'
            ])->find($id);
        $prices = $villa->prices->map(function ($price) use ($usdrates, $tax) {
            $price->contractrate = ceil($price->contract_rate / $usdrates->rate);
            $price->usd_price = ceil($price->contract_rate / $usdrates->rate) + $price->markup;
            $price->tax = ceil($price->usd_price * ($tax->tax / 100));
            $price->public_rate = ($price->usd_price + $price->tax) - $price->kick_back;
            return $price;
        });
        $author = Auth::user()->where('id',$villa->author_id)->first();
        $contracts = Contract::where('period_end','>',$now)->where('villa_id',$id)->get();
        $additional_services = OptionalRate::where('service','Villa')
            ->where('villas_id',$id)->get();
        return view('admin.villas.show',[
            'villa'=>$villa,
            'now'=>$now,
            'tax'=>$tax,
            'taxes'=>$tax,
            'business'=>$business,
            'usdrates'=>$usdrates,
            'attentions'=>$attentions,
            'author'=>$author,
            'contracts'=>$contracts,
            'additional_services'=>$additional_services,
            'prices'=>$prices,
        ]);
    }

    // View Villa Edit =======================================================================================================================>
    public function admin_villa_edit($id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $villa=Villas::findOrFail($id);
            $attentions = Attention::where('page','admin-villa-edit')->get();
            $usdrates = Cache::remember('usd_rates', 3600, function () {
                return UsdRates::select('name', 'rate')->where('name', 'USD')->first();
            });
            return view('villas.forms.edit-villa',[
                'usdrates'=>$usdrates,
                'attentions'=>$attentions,
                ])->with('villa',$villa);
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }

    // View Add Villa Room ===================================================================================================================>
    public function view_add_villa_room($id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $villa=Villas::findOrFail($id);
            $attentions=Attention::where('page','admin-hotel-room-add')->get();
            $usdrates=UsdRates::where('name','USD')->first();
            return view('villas.forms.add-villa-room',[
                'attentions'=>$attentions,
                'usdrates'=>$usdrates,
            ])->with('villa',$villa);
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }
    
    // Function Add Villa Room ===============================================================================================================>
    public function func_add_villa_room(Request $request){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            if($request->hasFile("cover")){
                $file=$request->file("cover");
                $coverName=time().'_'.$file->getClientOriginalName();
                $file->move("storage/villas/rooms/",$coverName);
                
                $status=1;
                $service="Private Villa";
                $action="Add Villa Room";

                $villaRoom =new VillaRooms([
                    "villa_id"=>$request->villa_id,
                    "name"=>$request->name,
                    "cover"=>$coverName,
                    "room_type"=>$request->room_type,
                    "bed_type"=>$request->bed_type,
                    "guest_adult"=>$request->guest_adult,
                    "guest_child"=>$request->guest_child,
                    "size"=>$request->size,
                    "description"=>$request->description,
                    "description_traditional"=>$request->description_traditional,
                    "description_simplified"=>$request->description_simplified,
                    "amenities"=>$request->amenities,
                    "amenities_traditional"=>$request->amenities_traditional,
                    "amenities_simplified"=>$request->amenities_simplified,
                    "view"=>$request->view,
                    "status"=> $status,
                    
                ]);
                $villaRoom->save();
            }
            
            // USER LOG
            $action = "Add Villa Rooms";
            $subservice = "Villa Room";
            $page = "admin-villa-room-add";
            $note = "Add new rooms at Villa id : ".$request->hotels_id.", Room id : ".$villaRoom->id;
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
            return redirect("/admin-villa-detail-$request->villa_id")->with('success', 'Rooms added successfully');
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }

    // Function Update Villa =================================================================================================================>
    public function func_update_villa(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $villa=Villas::findOrFail($id);
            if($request->hasFile("cover")){
                if (File::exists("storage/villas/covers/".$villa->cover)) {
                    File::delete("storage/villas/covers/".$villa->cover);
                }
                $file=$request->file("cover");
                $villa->cover=time()."_".$file->getClientOriginalName();
                $file->move("storage/villas/covers/",$villa->cover);
                $request['cover']=$villa->cover;
            }

            $check_in_time = date('H:i:s',strtotime($request->check_in_time));
            $check_out_time = date('H:i:s',strtotime($request->check_out_time));
            $villa->update([
                "name"=>$request->name,
                "region"=>$request->region,
                "address"=>$request->address,
                "airport_distance" =>$request->airport_distance,
                "airport_duration" =>$request->airport_duration,
                "contact_person"=>$request->contact_person,
                "phone"=>$request->phone,
                "cover" =>$villa->cover,
                "web"=>$request->web,
                "min_stay" =>$request->min_stay,
                "check_in_time" =>$check_in_time,
                "check_out_time" =>$check_out_time,
                "map" =>$request->map,
                "benefits" =>$request->benefits,
                "benefits_traditional" =>$request->benefits_traditional,
                "benefits_simplified" =>$request->benefits_simplified,
                "description"=>$request->description,
                "description_traditional"=>$request->description_traditional,
                "description_simplified"=>$request->description_simplified,
                "facility"=>$request->facility,
                "facility_traditional"=>$request->facility_traditional,
                "facility_simplified"=>$request->facility_simplified,
                "additional_info"=>$request->additional_info,
                "additional_info_traditional"=>$request->additional_info_traditional,
                "additional_info_simplified"=>$request->additional_info_simplified,
                "cancellation_policy"=>$request->cancellation_policy,
                "cancellation_policy_traditional"=>$request->cancellation_policy_traditional,
                "cancellation_policy_simplified"=>$request->cancellation_policy_simplified,
                "author_id"=>$request->author,
                "status"=>$request->status,
            ]);

            // USER LOG
            $action = "Update";
            $service = "Private Villa";
            $subservice = "Villa";
            $page = "admin-villa-edit";
            $note = "Update villa : ".$id;
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
            return redirect("/admin-villa-detail-$villa->id")->with('success','The Villa has been updated!');
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }

    // Function Add Contract =================================================================================================================>
    public function func_add_villa_contract(Request $request){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            if ($request->hasFile("file_name")) {
                $request->validate([
                    'file_name' => 'required|file|mimes:pdf,doc,docx',
                    'period_start' => 'required|date',
                    'period_end' => 'required|date|after_or_equal:period_start',
                    'villa_id' => 'required|exists:villas,id',
                    'contract_name' => 'required|string',
                ]);
            
                try {
                    $fileContract = $request->file("file_name");
                    $contractName = time() . "_" . $fileContract->getClientOriginalName();
                    $fileContract->move("storage/villas/villas-contract/", $contractName);
            
                    $periodStart = date('Y-m-d', strtotime($request->period_start));
                    $periodEnd = date('Y-m-d', strtotime($request->period_end));
                    $villaId = $request->villa_id;
            
                    $contract = new Contract([
                        "name" => $request->contract_name,
                        "villa_id" => $villaId,
                        "period_start" => $periodStart,
                        "period_end" => $periodEnd,
                        "file_name" => $contractName,
                    ]);
                    $contract->save();
                    // USER LOG
                    $action = "Add Contract";
                    $service = "Private Villa";
                    $subservice = "Contract";
                    $page = "admin-villa-detail";
                    $note = "Add new contract : ".$villaId;
                    $user_log =new UserLog([
                        "action"=>$action,
                        "service"=>$service,
                        "subservice"=>$subservice,
                        "subservice_id"=>$villaId,
                        "page"=>$page,
                        "user_id"=>$request->author,
                        "user_ip"=>$request->getClientIp(),
                        "note" =>$note, 
                    ]);
                    $user_log->save();
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Failed to save contract: ' . $e->getMessage()], 500);
                }
            }
        
            return redirect("/admin-villa-detail-$villaId")->with('success', 'Hotel contract added successfully');
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }
    
    // Function Edit Contract ================================================================================================================>
    public function func_edit_villa_contract(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $contract=Contract::findOrFail($id);
            $period_start = date('Y-m-d', strtotime($request->period_start));
            $period_end = date('Y-m-d', strtotime($request->period_end));
            if($request->hasFile("file_name")){
                if (File::exists("storage/villas/villas-contract/".$contract->file_name)) {
                    File::delete("storage/villas/villas-contract/".$contract->file_name);
                }
                $file=$request->file("file_name");
                $contract->file_name = time()."_".$file->getClientOriginalName();
                $file->move("storage/villas/villas-contract/",$contract->file_name);
                $file_name = $contract->file_name;
                
            }
            $contract->update([
                "name"=>$request->contract_name,
                "file_name"=>$contract->file_name,
                "period_start"=>$period_start,
                "period_end"=>$period_end,
            ]);

            // USER LOG
            $action = "Update Villa Contract";
            $service = "Private Villa";
            $subservice = "Contract";
            $page = "admin-villa-detail";
            $note = "Update contract to Villa id : ".$request->villa_id;
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
            return redirect("/admin-villa-detail-$request->villa_id")->with('success','Contract has been updated!');
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }

    // Function Delete Hotel Contract ========================================================================================================>
    public function delete_villa_contract(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $contract=Contract::findOrFail($id);
            if (File::exists("storage/villas/villas-contract/".$request->file_name)) 
            {
                File::delete("storage/villas/villas-contract/".$request->file_name);
            }
            $action = "Remove";
            $service = "Private Villa";
            $subservice = "Contract";
            $page = "admin-villa-detail";
            $note = "Remove Contract on villa : ".$request->villa_id;
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
            return redirect("/admin-villa-detail-$request->villa_id")->with('success','The Contract has been successfully deleted!');
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }

    // View Edit Villa Room ==================================================================================================================>
    public function admin_edit_villa_room($id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $room=VillaRooms::findOrFail($id);
            $villa=Villas::where('id','=', $room->villa_id)->first();
            $attentions = Attention::where('page','edit-villa-room')->get();
            return view('villas.forms.edit-villa-room',[
                'villa'=>$villa,
                'attentions'=>$attentions,
            ])->with('room',$room);
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }

    
    // Function Edit Villa Room ==============================================================================================================>
    public function func_update_villa_room(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $room=VillaRooms::findOrFail($id);
            $villa_id=$room->villa_id;
            $action="Update";
            if($request->hasFile("cover")){
                if (File::exists("storage/villas/rooms/".$room->cover)) {
                    File::delete("storage/villas/rooms/".$room->cover);
                }
                $file=$request->file("cover");
                $room->cover=time()."_".$file->getClientOriginalName();
                $file->move("storage/villas/rooms/",$room->cover);
                $request['cover']=$room->cover;
            }

            $room->update([
                "villa_id"=>$request->villa_id,
                "name"=>$request->name,
                "cover"=>$room->cover,
                "room_type"=>$room->room_type,
                "bed_type"=>$room->bed_type,
                "guest_adult" =>$request->guest_adult, 
                "guest_child" =>$request->guest_child, 
                "size" =>$request->size, 
                "description" =>$request->description, 
                "description_traditional" =>$request->description_traditional, 
                "description_simplified" =>$request->description_simplified, 
                "amenities" =>$request->amenities, 
                "amenities_traditional" =>$request->amenities_traditional, 
                "amenities_simplified" =>$request->amenities_simplified, 
                "view" =>$request->view,
                "status"=>$request->status,
            ]);

            // USER LOG
            $action = "Update";
            $service = "Private Villa";
            $subservice = "Room";
            $page = "edit-villa-room";
            $note = "Update room on villa : ".$request->villa_id.", Room id : ".$id;
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
            // return dd($room);
            return redirect("/admin-villa-detail-$villa_id#rooms")->with('success','The room has been updated!');
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }

    // Function Delete Villa Room ============================================================================================================>
    public function func_delete_villa_room(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $room=VillaRooms::findOrFail($id);
            $villa= Villas::where('id','=',$room->villa_id)->first();
            $service_name=$room->name;
            $service=$villa->name;
            $action="Delete Villa Room";
            $author= Auth::user()->id;

            // USER LOG
            $action = "Remove";
            $service = "Private Villa";
            $subservice = "Room";
            $page = "admin-villa-detail";
            $note = "Remove room on villa : ".$request->villas_id.", Room id : ".$id;
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
            if (File::exists("storage/villas/rooms/".$room->cover)) {
                File::delete("storage/villas/rooms/".$room->cover);
            }
            $room->delete();
            return redirect("/admin-villa-detail-$villa->id#rooms")->with('success','The Room has been successfully deleted!');
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }

    // Function Add Villa Additional Service =================================================================================================>
    public function func_add_additional_service(Request $request){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'contract_rate' => 'required|numeric|min:0',
                'markup' => 'required|numeric|min:0',
                'description' => 'nullable|string',
            ]);
            $service = "Villa";
            $additional_service =new OptionalRate([
                "villas_id"=>$request->villa_id,
                "name"=>$request->name,
                "service"=>$service,
                "service_id"=>$request->villa_id,
                "type"=>$request->type,
                "contract_rate"=>$request->contract_rate,
                "markup"=>$request->markup,
                "description"=>$request->description,
                "description_traditional"=>$request->description_traditional,
                "description_simplified"=>$request->description_simplified,
            ]);
            $additional_service->save();
            // USER LOG
            $action = "Add Additional Service";
            $subservice = "Additional Service";
            $page = "admin-villa-detail";
            $note = "Add new additional service at Villa id : ".$request->villa_id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$additional_service->id,
                "page"=>$page,
                "user_id"=>$request->author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/admin-villa-detail-$request->villa_id#additionalServices")->with('success', 'Additional service added successfully');
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }

    // Function Edit Additional Service ======================================================================================================>
    public function func_edit_additional_service(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $optional_rate=OptionalRate::findOrFail($id);
            $service = "Private Villa";
            $action="Update Optional Rate";
            $optional_rate->update([
                "type"=>$request->type,
                "name"=>$request->name,
                "description"=>$request->description,
                "description_traditional"=>$request->description_traditional,
                "description_simplified"=>$request->description_simplified,
                "markup"=>$request->markup,
                "contract_rate"=>$request->contract_rate,
            ]);

            // USER LOG
            $action = "Update Optional Rate";
            $subservice = "Optional Rate";
            $page = "admin-villa-detail";
            $note = "Update optional rate to Villa id : ".$request->service_id;
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
            return redirect("/admin-villa-detail-$request->service_id#additionalServices")->with('success','The Optional Rate has been updated!');
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }

    // Function Delete Additional Service ====================================================================================================>
    public function func_delete_additional_service(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $additional_service=OptionalRate::findOrFail($id);
            $villa= Villas::where('id',$additional_service->villas_id)->first();
            $author= Auth::user()->id;
            // USER LOG
            $action = "Remove";
            $service = "Private Villa";
            $subservice = "Additional Service";
            $page = "admin-villa-detail";
            $note = "Remove additional service from villa : ".$request->villa_id;
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
            $additional_service->delete();
            return redirect("/admin-villa-detail-$villa->id#additionalServices")->with('success','The additional service has been successfully deleted!');
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }

    // View Add Villa Price ==================================================================================================================>
    public function view_admin_add_villa_price($id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $villa=Villas::with(['rooms'])->findOrFail($id);
            $attentions = Attention::where('page','edit-villa-room')->get();
            return view('admin.villas.forms.add-villa-prices',[
                'villa'=>$villa,
                'attentions'=>$attentions,
            ]);
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }

    // Function ADD VILLA PRICES ==============================================================================================================>
    public function func_add_villa_price(Request $request,$id){
        if (!Gate::allows('posDev') && !Gate::allows('posAuthor')) {
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
        $request->validate([
            'start_date' => 'required|array',
            'end_date' => 'required|array',
            'markup' => 'required|array',
            'contract_rate' => 'required|array',
            'start_date.*' => 'required|date',
            'end_date.*' => 'required|date|after_or_equal:start_date.*',
            'markup.*' => 'required|numeric',
            'contract_rate.*' => 'required|numeric',
            'kick_back.*' => 'nullable|numeric',
        ]);

        $villa_id = $id;
        $author = auth()->id();
        $now = now();
        $status = 'Active';

        $insertData = [];
        foreach ($request->start_date as $i => $startDate) {
            if (!isset($request->end_date[$i], $request->markup[$i], $request->contract_rate[$i])) {
                continue;
            }
            $insertData[] = [
                'villa_id' => $villa_id,
                'start_date' => date('Y-m-d', strtotime($startDate)),
                'end_date' => date('Y-m-d', strtotime($request->end_date[$i])),
                'markup' => $request->markup[$i],
                'kick_back' => $request->kick_back[$i] ?? null,
                'contract_rate' => $request->contract_rate[$i],
                'benefits' => $request->benefits[$i] ?? null,
                'benefits_traditional' => $request->benefits_traditional[$i] ?? null,
                'benefits_simplified' => $request->benefits_simplified[$i] ?? null,
                'author' => $author,
                'status' => $status,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($insertData)) {
            VillaPrice::insert($insertData);
        }
        return redirect("/admin-villa-detail-$villa_id#villaPrices")->with('success', 'Prices added successfully');
    }

    // Function Update Villa Price ============================================================================================================>
    public function func_edit_villa_price(Request $request, $id)
    {
        if (!Gate::allows('posDev') && !Gate::allows('posAuthor')) {
            return redirect('/villas-admin')->with('error', 'Akses ditolak');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'markup' => 'required|numeric',
            'contract_rate' => 'required|numeric',
            'kick_back' => 'nullable|numeric',
            'benefits' => 'nullable|string',
            'benefits_traditional' => 'nullable|string',
            'benefits_simplified' => 'nullable|string',
        ]);

        $villaPrice = VillaPrice::findOrFail($id);
        $start_date = date('Y-m-d', strtotime($request->start_date));
        $end_date = date('Y-m-d', strtotime($request->end_date));
        $villaPrice->update([
            'start_date' => $start_date,
            'end_date' => $end_date,
            'markup' => $request->markup,
            'kick_back' => $request->kick_back ?? 0,
            'contract_rate' => $request->contract_rate,
            'benefits' => $request->benefits,
            'benefits_traditional' => $request->benefits_traditional,
            'benefits_simplified' => $request->benefits_simplified,
            'author' => auth()->id(),
        ]);
        UserLog::create([
            'action' => 'Update Villa Price',
            'service' => 'Private Villa',
            'subservice' => 'Villa Price',
            'subservice_id' => $id,
            'page' => 'admin-villa-index',
            'user_id' => auth()->id(),
            'user_ip' => request()->ip(),
            'note' => 'Update villa price for villa ID: ' . $id,
        ]);
        return redirect("/admin-villa-detail-$villaPrice->villa_id#villaPrices")->with('success', 'Villa price updated successfully!');
    }

    // Function Delete Villa Room ============================================================================================================>
    public function func_delete_villa_price(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $price=VillaPrice::findOrFail($id);
            $villa= Villas::where('id','=',$price->villa_id)->first();
            $author= Auth::user()->id;

            // USER LOG
            $action = "Remove Villa Price";
            $service = "Private Villa";
            $subservice = "Villa Price";
            $page = "admin-villa-detail";
            $note = "Remove price on villa : ".$villa->id;
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
            $price->delete();
            return redirect("/admin-villa-detail-$villa->id#villaPrices")->with('success','The Price has been successfully deleted!');
        }else{
            return redirect("/villas-admin")->with('error','Akses ditolak');
        }
    }

    public function checkVillaStatus(Request $request)
    {
        $today = Carbon::now();
        $villas = Villas::with(['stay_period'])->get();

        foreach ($villas as $villa) {
            if (!$villa->stay_period || ($villa->stay_period->max_end_date < $today)) {
                $villa->update(['status' => 'Draft']);
            }elseif(!$villa->stay_period || ($villa->stay_period->max_end_date >= $today)){
                $villa->update(['status' => 'Active']);
            }
        }
        return response()->json(['status' => 'success']);
    }

    // Detail VILLA =============================================================================================================================> OK
    public function show(Request $request, $code)
    {
        $now = Carbon::now();
        $villa = Villas::with([
            'galleries',
            'rooms' => function ($query) {
                $query->where('status', 1);
            },
        ])->where('code', $code)->firstOrFail();
        $guestTotals = VillaRooms::totalGuestsByVilla($villa->id);
        $business = BusinessProfile::findOrFail(1);
        $usdrates = UsdRates::where('name', 'USD')->first();
        $nearvillas = Villas::select('code','name','region','cover','id')
            ->where('status', 'active')
            ->where('region', $villa->region)
            ->where('id', '!=', $villa->id)
            ->take(4)
            ->get();
        $promotions = Promotion::select('name','discounts','periode_start','periode_end')
            ->where('status', "active")
            ->where('periode_start','<=',$now)
            ->where('periode_end','>=',$now)
            ->get();
        $bookingcode = session('bookingcode');
        return view('villas.show', compact('villa', 'business', 'usdrates', 'now', 'nearvillas','promotions','bookingcode','guestTotals'));
    }

    // VILLA PRICE ==============================================================================================================================> OK
    public function villa_price(Request $request, $code)
    {
        session(['previous_url' => url()->previous()]);
        $checkincout = $request->checkincout;
        [$checkin, $checkout] = $this->parseCheckInOut($checkincout);
        $duration = Carbon::parse($checkin)->diffInDays(Carbon::parse($checkout));

        Session::put('booking_dates', [
            'checkin' => $checkin,
            'checkout' => $checkout,
            'duration' => $duration,
        ]);

        $now = now()->format('Y-m-d');

        $tax = Cache::remember('tax', 3600, function () {
            return Tax::select('name', 'tax')->where('name', 'tax')->first();
        });

        $business = Cache::remember('business_profile', 3600, function () {
            return BusinessProfile::select('id', 'name', 'address')->first();
        });

        $usdrates = Cache::remember('usd_rates', 3600, function () {
            return UsdRates::select('name', 'rate')->where('name', 'USD')->first();
        });

        $villa = Villas::with([
            'galleries',
            'rooms' => fn($q) => $q->where('status', 1),
            'prices' => fn($q) => $q->where('start_date', '<=', $checkin)
                                    ->where('end_date', '>=', $checkin)
                                    ->where('status', "Active")
        ])->where('code', $code)->firstOrFail();

        $guestTotals = VillaRooms::totalGuestsByVilla($villa->id);

        $nearvillas = Villas::where('region', $villa->region)
            ->where('id', '!=', $villa->id)
            ->take(8)
            ->get(['id', 'code', 'cover', 'name', 'region']);

        $rooms = $villa->rooms;

        // Harga fallback (untuk listing semua harga jika tidak ada yang cocok di tanggal)
        $prices = VillaPrice::where('villa_id', $villa->id)
            ->where('end_date','>=', $now)
            ->orderBy('start_date')
            ->get();

        // Hitung harga total berdasarkan tiap malam
        $calculatedPrice = 0;
        $found_price = false;
        $price_details = [];
        for ($date = Carbon::parse($checkin); $date->lt(Carbon::parse($checkout)); $date->addDay()) {
            $price = VillaPrice::where('villa_id', $villa->id)
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->first();

            if ($price) {
                $night_price = $price->calculatePrice($usdrates, $tax);
                $calculatedPrice += $night_price;
                $found_price = true;

                $price_details[] = [
                    'date' => $date->toDateString(),
                    'price_per_night_usd' => $night_price,
                    'contract_rate' => $price->contract_rate,
                    'markup' => $price->markup,
                ];
            } else {
                $price_details[] = [
                    'date' => $date->toDateString(),
                    'price_per_night_usd' => 0,
                    'note' => 'No price available'
                ];
                break;
            }
        }

        // Ambil salah satu data harga (untuk ditampilkan: info tambahan dan cancel policy)
        $price = $calculatedPrice ? VillaPrice::where('villa_id', $villa->id)
            ->where('start_date', '<=', $checkin)
            ->where('end_date', '>=', $checkin)
            ->where('status', 'Active')
            ->first() : null;

        return view('villas.villa-prices', compact(
            'duration',
            'guestTotals',
            'rooms',
            'villa',
            'usdrates',
            'checkin',
            'checkout',
            'calculatedPrice',
            'price',
            'prices',
            'nearvillas',
            'business',
            'tax',
            'price_details'
        ));
    }



    private function parseCheckInOut($checkincout)
    {
        [$check_in, $check_out] = explode(' - ', $checkincout);
        return [
            date('Y-m-d', strtotime($check_in)),
            date('Y-m-d', strtotime($check_out))
        ];
    }

    public function autocomplete(Request $request)
    {
        $query = $request->input('query');
        $villas = Villas::where('name', 'LIKE', "%{$query}%")
            ->where('status', 'active')
            ->limit(5)
            ->get(['id', 'name']);
        return response()->json(['villas' => $villas]);
    }

    public function autocompleteRegion(Request $request)
    {
        $query = $request->input('query');
        $regions = Villas::where('region', 'LIKE', "%{$query}%")
            ->where('status', 'active')
            ->select('region')
            ->distinct()
            ->limit(5)
            ->get(['id', 'region']);
        return response()->json(['regions' => $regions]);
    }
    
    public function loadMore(Request $request)
    {
        $villas = $this->getVillasQuery($request)->paginate(12);
        $html = view('villas.partials.villa-list', compact('villas'))->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $villas->hasMorePages()
        ]);
    }

    private function getVillasQuery(Request $request)
    {
        $now = Carbon::now();
        $villasQuery = villas::select('code', 'name', 'region', 'map', 'cover', 'id')
            ->where('status', 'active');

        if ($request->filled('villa_name')) {
            $villasQuery->where('name', 'like', '%' . $request->input('villa_name') . '%');
        }
        if ($request->filled('villa_region')) {
            $villasQuery->where('region', 'like', '%' . $request->input('villa_region') . '%');
        }
        return $villasQuery;
    }

    public function search_villas(Request $request)
    {
        $villas = $this->getVillasQuery($request)->paginate(12);
        return view('villas.partials.villa-list', compact('villas'))->render();
    }

    public function order_villa(Request $request)
    {
        $villas = $this->getVillasQuery($request)->paginate(6); // Sesuai kebutuhan Anda
        return view('villas.partials.villa-list', compact('villas'))->render();
    }
}
