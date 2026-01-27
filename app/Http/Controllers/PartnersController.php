<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Models\Tax;
use App\Models\Tours;
use App\Models\UserLog;
use App\Models\Partners;
use App\Models\UsdRates;
use App\Models\Attention;
use App\Models\Activities;
use Illuminate\Support\Str;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StorePartnersRequest;
use App\Http\Requests\UpdatePartnersRequest;

class PartnersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','can:isAdmin']);
    }
    public function index()
    {
        $activepartners=Partners::where('status', '=','Active')->get();
        $draft_partners=Partners::where('status', '=','Draft')->get();
        $partners = Partners::where('status','!=','Removed')->get();
        $attentions = Attention::where('page','partners')->get();
        return view('admin.partners', compact('activepartners'),[
            // "ctpackage" => $ctpackage,
            "activepartners" => $activepartners,
            "draft_partners" => $draft_partners,
            "partners" => $partners,
            "attentions" => $attentions,
            
        ]);
    }

// View Detail Partner =========================================================================================>
    public function view_partner_detail($id){
        $now = Carbon::now();
        $tax = Tax::where('id',1)->first();
        $partner = Partners::find($id);
        $usdrates = UsdRates::where('name','USD')->first();
        $attentions = Attention::where('page','partners')->get();
        $author = Auth::user()->where('id',$partner->author_id)->first();
        $type = ActivityType::all();
        
        $activitys = Activities::where('partners_id', $partner->id)->where('status','!=','Removed')->get();
        $tours = Tours::where('partners_id', $partner->id)->where('status','!=','Removed')->get();
        $cactivity = count($activitys);
        $ctours = count($tours);
        $cservice = $cactivity + $ctours;
        

        if ($partner->status == "Removed") {
            return redirect("/partners")->with('invalid','Sorry, the partner you are looking for is not available!');
        }else{
            return view('admin.partner-detail',[
                'partner'=>$partner,
                'usdrates'=>$usdrates,
                'attentions'=>$attentions,
                'author'=>$author,
                'type'=>$type,
                'tax'=>$tax,
                'cservice'=>$cservice,
                'tours'=>$tours,
                'activitys'=>$activitys,
            ]);
        }
    }
// View Partner Add Activity=========================================================================================>
    public function view_partner_add_activity($id){
        $attentions = Attention::where('page','partner-add-activity')->get();
        $activities = Activities::all();
        $type = ActivityType::all();
        $partners = Partners::find($id);
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            return view('form.partner-add-activity', [
                "attentions"=>$attentions,
                "type" => $type,
                "partners" => $partners,
            ])->with('activities',$activities);
        }else{
            return redirect("/dashboard")->with('error','Sorry, anda tidak bisa mengakses halaman tersebut');
        }
        
    }
// View Partner Add Tour=========================================================================================>
    public function view_partner_add_tour($id){
        $attentions = Attention::where('page','partner-add-tour')->get();
        $tours = Activities::all();
        $type = ActivityType::all();
        $partners = Partners::find($id);
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            return view('form.partner-add-tour', [
                "attentions"=>$attentions,
                "type" => $type,
                "partners" => $partners,
            ])->with('tours',$tours);
        }else{
            return redirect("/dashboard")->with('error','Sorry, anda tidak bisa mengakses halaman tersebut');
        }
    }
// Function Add PARTNER =========================================================================================>
    public function func_add_partner(Request $request){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'address' => 'required',
                'location' => 'required',
                'map' => 'required',
                'cover' => 'required',
                'type' => 'required',
                'phone' => 'required',
                'contact_person' => 'required',
            ]);
            $status = 'Draft';
            if($request->hasFile("cover")){
                $file=$request->file("cover");
                $coverName=time().'_'.$file->getClientOriginalName();
                $file->move("storage/partners/covers/",$coverName);
                $status="Draft";
                $code=Str::random(26);
                $partner =new Partners([
                    "name"=>$request->name,
                    "address"=>$request->address,
                    "location"=>$request->location,
                    "map"=>$request->map,
                    "type"=>$request->type,
                    "phone"=>$request->phone,
                    "contact_person"=>$request->contact_person,
                    "status"=>$status,
                    "cover" =>$coverName,
                    "author_id" =>$request->author_id,
                    "description" =>$request->description,
                ]);
                $partner->save();
            }
            // USER LOG
            $action = "Add Partner";
            $service = "Partner";
            $subservice = "Partner";
            $page = "partners";
            $note = "Add new Partner id : ".$partner->id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$partner->id,
                "page"=>$page,
                "user_id"=>$request->author_id,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/detail-partner-$partner->id")->with('success', 'Partner added successfully');
        }else{
            return redirect("/dashboard")->with('error','Sorry, anda tidak bisa mengakses halaman tersebut');
        }
    }

// Function Add Activities =========================================================================================>
    public function func_partner_add_activity(Request $request){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $validated = $request->validate([
                'name' => 'required',
                'location' => 'required',
                'type' => 'required',
                'duration' => 'required',
                'description' => 'required',
                'contract_rate' => 'required',
                'qty' => 'required',
                'min_pax' => 'required',
                'validity' => 'required',
                'cover' => 'required',
                'partners_id' => 'required',
            ]);

            if($request->hasFile("cover")){
                $file=$request->file("cover");
                $coverName=time().'_'.$file->getClientOriginalName();
                $file->move("storage/activities/activities-cover/",$coverName);
                $status="Draft";
                $code=Str::random(26);
                $validity = date('Y-m-d',strtotime($request->validity));
                $activity =new Activities([
                    "name"=>$request->name,
                    "code"=>$code,
                    "type" =>$request->type, 
                    "location"=>$request->location,
                    "map"=>$request->map,
                    "partners_id"=>$request->partners_id,
                    "address"=>$request->address,
                    "description"=>$request->description,
                    "itinerary"=>$request->itinerary,
                    "duration"=>$request->duration,
                    "include"=>$request->include,
                    "additional_info"=>$request->additional_info,
                    "contract_rate"=>$request->contract_rate,
                    "cancellation_policy"=>$request->cancellation_policy,
                    "markup"=>$request->markup,
                    "validity"=>$request->validity,
                    "min_pax"=>$request->min_pax,
                    "qty"=>$request->qty,
                    "status"=>$status,
                    "author_id"=>$request->author,
                    "cover" =>$coverName,
                ]);
                $activity->save();
            }
            // USER LOG
            $action = "Add Activity";
            $service = "Activity";
            $subservice = "Activity";
            $page = "add-activity";
            $note = "Add Activity: ".$activity->id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$activity->id,
                "page"=>$page,
                "user_id"=>$request->author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/detail-partner-$request->partners_id")->with('success','New Activity has been successfully added!');
        }else{
            return redirect("/dashboard")->with('error','Sorry, anda tidak bisa mengakses halaman tersebut');
        }
    }
// Function Add Tour =========================================================================================>
    public function func_partner_add_tour(Request $request){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'destinations' => 'required',
                'location' => 'required',
                'type' => 'required',
                'duration' => 'required',
                'description' => 'required',
                'itinerary'=> 'required',
                'include' => 'required',
                'contract_rate' => 'required',
                'markup' => 'required',
                'qty' => 'required',
            ]);
            if($request->hasFile('cover')){
                $file=$request->file('cover');
                $coverName=time().'_'.$file->getClientOriginalName();
                $file->move("storage/tours/tours-cover",$coverName);
                $status="Draft";
                $code=Str::random(26);
                $tour =new Tours([
                    "name"=>$request->name,
                    "partners_id"=>$request->partners_id,
                    "code"=>$code,
                    "destinations"=>$request->destinations,
                    "location"=>$request->location,
                    "type" =>$request->type, 
                    "duration"=>$request->duration,
                    "description"=>$request->description,
                    "include"=>$request->include,
                    "itinerary"=>$request->itinerary,
                    "additional_info"=>$request->additional_info,
                    "cancellation_policy"=>$request->cancellation_policy,
                    "contract_rate"=>$request->contract_rate,
                    "markup"=>$request->markup,
                    "qty"=>$request->qty,
                    "status"=>$status,
                    "author_id"=>$request->author,
                    "cover" =>$coverName,
                ]);
                $tour->save();
            }
            // USER LOG
            $action = "Add Tours";
            $service = "Tours";
            $subservice = "Tours";
            $page = "add-tour";
            $note = "Add Tours: ".$tour->id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$tour->id,
                "page"=>$page,
                "user_id"=>$request->author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/detail-partner-$request->partners_id")->with('success','New Tour package has been successfully added!');
        }else{
            return redirect("/dashboard")->with('error','Sorry, anda tidak bisa mengakses halaman tersebut');
        }
    }

// Function Update Partner =============================================================================================================>
    public function func_update_partner(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $partner=Partners::findOrFail($id);
            $activities = Activities::where('partners_id',$id)->get();
            $tours = Tours::where('partners_id',$id)->get();
            if($request->hasFile("cover")){
                if (File::exists("storage/partners/covers/".$partner->cover)) {
                    File::delete("storage/partners/covers/".$partner->cover);
                }
                $file=$request->file("cover");
                $partner->cover=time()."_".$file->getClientOriginalName();
                $file->move("storage/partners/covers/",$partner->cover);
                $request['cover']=$partner->cover;
            }
            if (isset($activities)) {
                if ($request->status == "Draft") {
                foreach ($activities as $activity) {
                        $activity->update([
                            "status"=>"Draft",
                        ]);
                    }
                foreach ($tours as $tour) {
                        $tour->update([
                            "status"=>"Draft",
                        ]);
                    }
                }
            }
            $partner->update([
                "status"=>$request->status,
                "name"=>$request->name,
                "address"=>$request->address,
                "location"=>$request->location,
                "map"=>$request->map,
                "type"=>$request->type,
                "phone"=>$request->phone,
                "contact_person"=>$request->contact_person,
                "cover" =>$partner->cover,
                "description" =>$request->description,
            ]);
            // USER LOG
            $action = "update Partner";
            $service = "Partner";
            $subservice = "Partner";
            $page = "partners";
            $note = "update Partner id : ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$request->author_id,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/detail-partner-$id")->with('success','Partner has been updated!');
        }else{
            return redirect("/dashboard")->with('error','Sorry, anda tidak bisa mengakses halaman tersebut');
        }
    }
    // Function Remove Partner =============================================================================================================>
    public function func_remove_partner(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $partner=Partners::findOrFail($id);
            $activities = Activities::where('partners_id',$id)->get();
            $status = 'Removed';
            foreach ($activities as $activity) {
            $activity->update([
                    "status"=>"Draft",
                    "partners_id"=>null,
            ]);
            }
            $partner->update([
                "status"=>$status,
            ]);
            // USER LOG
            $action = "Remove Partner";
            $service = "Partner";
            $subservice = "Partner";
            $page = "partners";
            $note = "Remove Partner id : ".$id;
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
            return redirect("/partners")->with('success','Partner has been removed!');
        }else{
            return redirect("/dashboard")->with('error','Sorry, anda tidak bisa mengakses halaman tersebut');
        }
    }

    public function partnerdetail($id){
        $dpartner = Partners::find($id);
        return view('admin.partnerdetail',[
                'dpartners'=>$dpartner,
            ]);

        } 
}
