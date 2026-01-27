<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\LogData;
use App\Models\UserLog;
use App\Models\Partners;
use App\Models\UsdRates;
use App\Models\Attention;
use App\Models\Activities;
use Illuminate\Support\Str;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use App\Models\ActivitiesImages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreactivitiesRequest;
use App\Http\Requests\UpdateactivitiesRequest;

class ActivitiesAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified','type:admin']);
    }

    public function index()
    {
        $activeactivities=Activities::where('status', '!=','Removed')->where('status',"!=","Archived")->get();
        $cactiveactivities=Activities::where('status', '=','Active')->get();
        $archiveactivities=Activities::where('status', '=','Archived')->get();
        $draftactivities=Activities::where('status', '=','Draft')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $taxes = Tax::where('id',1)->first();
        $partners = Partners::all();
        return view('admin.activitiesadmin',[
            'taxes' =>$taxes,
            'usdrates'=>$usdrates,
            "cactiveactivities" => $cactiveactivities,
            "activeactivities" => $activeactivities,
            "archiveactivities" => $archiveactivities,
            "draftactivities" => $draftactivities,
            "partners"=>$partners,
        ]);
    }

// View Detail Activity =========================================================================================>
    public function view_detail_activity($id)
    {
        $now = Carbon::now();
        $business = BusinessProfile::where('id','=',1)->first();
        $activity = Activities::find($id);
        $usdrates = UsdRates::where('name','USD')->first();
        $attentions = Attention::where('page','admin-activity-add')->get();
        $taxes = Tax::where('id',1)->first();
        $partner = Partners::where('id', $activity->partners_id)->first();
        return view('admin.activitiesadmindetail',[
            'taxes'=>$taxes,
            'now' => $now,
            'business'=>$business,
            'usdrates'=>$usdrates,
            "attentions"=>$attentions,
            "partner"=>$partner,
        ])->with('activity',$activity);
    }

// View Activity Edit =============================================================================================================>
    public function view_edit_activity($id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $activities=Activities::findOrFail($id);
            $type = ActivityType::all();
            $usdrates = UsdRates::where('name','USD')->first();
            $attentions = Attention::where('page','admin-activity-add')->get();
            $partner = Partners::where('id',$activities->partners_id)->first();
            $partners = Partners::all();
            return view('form.activityedit',[
                'type' => $type,
                'usdrates'=>$usdrates,
                "attentions"=>$attentions,
                "partner"=>$partner,
                "partners"=>$partners,
            ])->with('activities',$activities);
        }else{
            return redirect("/activities-admin")->with('error','Akses ditolak');
        }
    }

// View Add Activities =========================================================================================>
    public function view_add_activity()
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $attentions = Attention::where('page','admin-activity-add')->get();
            $activities = Activities::all();
            $type = ActivityType::all();
            $partners = Partners::all();
            return view('form.activityadd', [
                "attentions"=>$attentions,
                "type" => $type,
                "partners" => $partners,
                ])->with('activities',$activities);
        }else{
            return redirect("/activities-admin")->with('error','Akses ditolak');
        }
    }


// Function Add Activities =========================================================================================>
    public function func_add_activity(Request $request){
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
                $prtns = Partners::where('id',$request->partners_id)->first();
                $partner = $prtns->name;
                $activity =new Activities([
                    "name"=>$request->name,
                    "code"=>$code,
                    "type" =>$request->type, 
                    "location"=>$request->location,
                    "map"=>$request->map,
                    "partners_id"=>$request->partners_id,
                    "partner"=>$partner,
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
            // @dd($activity);
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
            return redirect("/detail-activity-$activity->id")->with('success','New Activity has been successfully added!');
        }else{
            return redirect("/activities-admin")->with('error','Akses ditolak');
        }
    }
   

// function Update Activity =============================================================================================================>
    public function func_update_activity(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $activity=Activities::findOrFail($id);
            if($request->hasFile("cover")){
                if (File::exists("storage/activities/activities-cover/".$activity->cover)) {
                    File::delete("storage/activities/activities-cover/".$activity->cover);
                }
                $file=$request->file("cover");
                $activity->cover=time()."_".$file->getClientOriginalName();
                $file->move("storage/activities/activities-cover",$activity->cover);
                $request['cover']=$activity->cover;
            }
            if ($request->status == "Active") {
                $partner=Partners::where('id',$activity->partners_id)->first();
                if (isset($partner)) {
                    $partner->update([
                        "status"=>"Active",
                    ]);
                }
            }
            $prtns = Partners::where('id',$request->partners_id)->first();
            $partner = $prtns->name;
            $validity = date('Y-m-d', strtotime($request->validity));
            $activity->update([
                "name"=>$request->name,
                "type" =>$request->type, 
                "location"=>$request->location,
                "map"=>$request->map,
                "partners_id"=>$request->partners_id,
                "partner"=>$partner,
                "address"=>$request->address,
                "description"=>$request->description,
                "itinerary"=>$request->itinerary,
                "duration"=>$request->duration,
                "include"=>$request->include,
                "additional_info"=>$request->additional_info,
                "contract_rate"=>$request->contract_rate,
                "cancellation_policy"=>$request->cancellation_policy,
                "markup"=>$request->markup,
                "qty"=>$request->qty,
                "min_pax"=>$request->min_pax,
                "status"=>$request->status,
                "author_id"=>$request->author,
                "validity"=>$validity,
                "cover" =>$activity->cover,
            ]);
            
            // USER LOG
            $action = "Update Activity";
            $service = "Activity";
            $subservice = "Activity";
            $page = "edit-activity";
            $note = "Edit Activity: ".$id;
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
            return redirect("/detail-activity-$activity->id")->with('success','Activity has been successfully updated!');
        }else{
            return redirect("/activities-admin")->with('error','Akses ditolak');
        }
    }
}