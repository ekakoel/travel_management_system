<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\UserLog;
use App\Models\UsdRates;
use App\Models\Attention;
use App\Models\Transports;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TransportType;
use App\Models\TransportBrand;
use App\Models\TransportPrice;
use App\Models\BusinessProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoretransportsRequest;
use App\Http\Requests\UpdatetransportsRequest;

class TransportsAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified','type:admin']);
    }
    public function index()
    {   
        $transports = Transports::all();
        $activetransports=Transports::where('status', '!=','Archived')
        ->where('status', '!=','Removed')->get();
        $cactivetransports=Transports::where('status', '=','Active')->get();
        $archivetransports=Transports::where('status', '=','Archived')->get();
        $drafttransports=Transports::where('status', '=','Draft')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        return view('admin.transportsadmin', compact('activetransports'),[
            'usdrates'=>$usdrates,
            "cactivetransports" => $cactivetransports,
            "activetransports" => $activetransports,
            "archivetransports" => $archivetransports,
            "drafttransports" => $drafttransports,
            
        ]);
    }
// View Add Transports =========================================================================================>
    public function view_add_transport()
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $transports = Transports::all();
            $type = TransportType::all();
            $brand = TransportBrand::all();
            $attentions = Attention::where('page','add-transport')->get();
            return view('form.transportadd',[
                'attentions'=>$attentions,
                'type'=>$type,
                'brand'=>$brand,
            ])->with('transports',$transports);
        }else{
            return redirect("/transports-admin")->with('error','Akses ditolak');
        }
    }

// View Edit Transport =============================================================================================================>
    public function view_edit_transport($id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $transport=Transports::findOrFail($id);
            $type = TransportType::all();
            $brand = TransportBrand::all();
            $attentions = Attention::where('page','edit-transport')->get();
            $usdrates=UsdRates::where('name','USD')->first();
            return view('form.transportedit',[
                'usdrates'=>$usdrates,
                'type'=>$type,
                'brand'=>$brand,
                'attentions'=>$attentions,
            ])->with('transport',$transport);
        }else{
            return redirect("/transports-admin")->with('error','Akses ditolak');
        }
    }

// View Detail Transport =========================================================================================>
    public function view_detail_transport($id)
    {
        $taxes = Tax::where('id',1)->first();
        $now = Carbon::now();
        $business = BusinessProfile::where('id','=',1)->first();
        $transport = Transports::find($id);
        $usdrates = UsdRates::where('name','USD')->first();
        $attentions = Attention::where('page','detail-transport')->get();
        $prices = TransportPrice::where('transports_id',$id)->orderBy('created_at', 'desc')->get();
        return view('admin.transportsadmindetail',[
            'taxes'=>$taxes,
            'prices'=>$prices,
            'attentions'=>$attentions,
            'usdrates'=>$usdrates,
            'now' => $now,
            'business'=>$business,
        ])->with('transport',$transport);
    }

// Function Add Transports =========================================================================================>
    public function func_add_transport(Request $request)
        {
            if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
                if($request->hasFile("cover")){
                    $file=$request->file("cover");
                    $coverName=time().'_'.$file->getClientOriginalName();
                    $file->move("storage/transports/transports-cover/",$coverName);
                    $status="Draft";
                    $code=Str::random(26);
        
                    $transport =new Transports([
                        "name"=>$request->name,
                        "code"=>$code,
                        "type" =>$request->type, 
                        "brand" =>$request->brand, 
                        "description"=>$request->description,
                        "include"=>$request->include,
                        "additional_info"=>$request->additional_info,
                        "cancellation_policy"=>$request->cancellation_policy,
                        "capacity"=>$request->capacity,
                        "cover" =>$coverName,
                        "status"=>$status,
                        "author_id"=>$request->author,
                    ]);
                $transport->save();
                }
                // USER LOG
                $action = "Add";
                $service = "Transportation";
                $subservice = "Transportation";
                $page = "add-transport";
                $note = "Add Transportation: ".$transport->id;
                $user_log =new UserLog([
                    "action"=>$action,
                    "service"=>$service,
                    "subservice"=>$subservice,
                    "subservice_id"=>$transport->id,
                    "page"=>$page,
                    "user_id"=>$request->author,
                    "user_ip"=>$request->getClientIp(),
                    "note" =>$note, 
                ]);
                $user_log->save();
                return redirect("/detail-transport-$transport->id")->with('success','The Transportation has been Added!');
            }else{
                return redirect("/transports-admin")->with('error','Akses ditolak');
            }
        }
// Function Add Transports Price =========================================================================================>
    public function func_add_transport_price(Request $request)
        {
            if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
                $price =new TransportPrice([
                    "transports_id"=>$request->transports_id,
                    "name"=>$request->name,
                    "type"=>$request->type,
                    "src"=>$request->src,
                    "dst"=>$request->dst,
                    "duration"=>$request->duration,
                    "contract_rate"=>$request->contract_rate,
                    "markup"=>$request->markup,
                    "extra_time"=>$request->extra_time,
                    "additional_info"=>$request->additional_info,
                    "author_id"=>$request->author,
                ]);
                $price->save();
                
                // USER LOG
                $action = "Add";
                $service = "Transportation";
                $subservice = "Transportation Price";
                $page = "detail-transport";
                $note = "Add Price:". $price->id." to Transportation: ".$request->transports_id;
                $user_log =new UserLog([
                    "action"=>$action,
                    "service"=>$service,
                    "subservice"=>$subservice,
                    "subservice_id"=>$request->transports_id,
                    "page"=>$page,
                    "user_id"=>$request->author,
                    "user_ip"=>$request->getClientIp(),
                    "note" =>$note, 
                ]);
                $user_log->save();
                return redirect("/detail-transport-$request->transports_id")->with('success','The Price has been Added!');
            }else{
                return redirect("/transports-admin")->with('error','Akses ditolak');
            }
        }

// function Update Transport =============================================================================================================>
    public function func_update_transport(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $transport=Transports::findOrFail($id);
            if($request->hasFile("cover")){
                if (File::exists("storage/transports/transports-cover/".$transport->cover)) {
                    File::delete("storage/transports/transports-cover/".$transport->cover);
                }
                $file=$request->file("cover");
                $transport->cover=time()."_".$file->getClientOriginalName();
                $file->move("storage/transports/transports-cover/",$transport->cover);
                $request['cover']=$transport->cover;
            }

            $transport->update([
                "name"=>$request->name,
                "type" =>$request->type,
                "capacity"=>$request->capacity,
                "description"=>$request->description,
                "include"=>$request->include,
                "additional_info"=>$request->additional_info,
                "cancellation_policy"=>$request->cancellation_policy,
                "brand"=>$request->brand,
                "status"=>$request->status,
                "author_id"=>$request->author,
            ]);
            // USER LOG
            $action = "Update Transportation";
            $service = "Transportation";
            $subservice = "Transportation";
            $page = "edit-transport";
            $note = "Update Transportation: ".$id;
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
            return redirect("/detail-transport-$transport->id")->with('success','The Transportation has been successfully updated!');
        }else{
            return redirect("/transports-admin")->with('error','Akses ditolak');
        }
    }
// function Update Transport Price =============================================================================================================>
    public function func_update_transport_price(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $price=TransportPrice::findOrFail($id);
            $price->update([
                "transports_id"=>$request->transports_id,
                "name"=>$request->name,
                "type" =>$request->type,
                "type"=>$request->type,
                "src"=>$request->src,
                "dst"=>$request->dst,
                "duration"=>$request->duration,
                "additional_info"=>$request->additional_info,
                "contract_rate"=>$request->contract_rate,
                "markup"=>$request->markup,
                "extra_time"=>$request->extra_time,
                "author_id"=>$request->author,
            ]);
            // USER LOG
            $action = "Update";
            $service = "Transportation";
            $subservice = "Transportation Price";
            $page = "admin-detail-transport";
            $note = "Update Price :".$price->id ." on transport: ".$id;
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
            return back()->with('success','The price has been successfully updated!');
        }else{
            return redirect("/transports-admin")->with('error','Akses ditolak');
        }
    }
    // function Remove Transport =============================================================================================================>
    public function remove_transport(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $transport=Transports::findOrFail($id);
            $status = "Removed";
            $transport->update([
                "status"=>$status,
            ]);
            // USER LOG
            $action = "Remove Transportation";
            $service = "Transportation";
            $subservice = "Transportation Package";
            $page = "transports-admin";
            $note = "Remove Transportation Package: ".$id;
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
            return back()->with('success','The Transportation Package has been successfully deleted!');
        }else{
            return redirect("/transports-admin")->with('error','Akses ditolak');
        }
    }
    // function Remove Transport Price =============================================================================================================>
    public function remove_transport_price(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $price=TransportPrice::findOrFail($id);
            $price->delete();
            // USER LOG
            $action = "Remove";
            $service = "Transportation";
            $subservice = "Transportation Price";
            $page = "transports-admin";
            $note = "Remove Transportation Price: ".$id;
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
            return redirect("/detail-transport-$request->transport_id#prices")->with('success','The Price has been successfully deleted!');
        }else{
            return redirect("/transports-admin")->with('error','Akses ditolak');
        }
    }
}
