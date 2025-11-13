<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Models\ExtraBed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreExtraBedRequest;
use App\Http\Requests\UpdateExtraBedRequest;

class ExtraBedController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
   // Function Add Optional Rate =========================================================================================>
   public function func_add_extra_bed(Request $request){
        $validated = $request->validate([
            'name'=> 'required',
            'hotels_id'=> 'required',
            'type'=> 'required',
            'contract_rate'=> 'required',
            'markup'=> 'required',
        ]);
        $extra_bed =new ExtraBed([
            "name"=>$request->name,
            "hotels_id"=>$request->hotels_id,
            "type"=>$request->type,
            "max_age"=>$request->max_age,
            "min_age" =>$request->min_age, 
            "description" =>$request->description, 
            "contract_rate" =>$request->contract_rate, 
            "markup" =>$request->markup, 
        ]);
        $extra_bed->save();

        // USER LOG
        $action = "Add Extra Bed";
        $service = "Rooms";
        $subservice = "Extra Bed";
        $page = "detail-hotel#extra-bed";
        $note = "Add extra bed to Hotel id : ".$request->hotels_id.", extra bed id: ".$extra_bed->id;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$extra_bed->id,
            "page"=>$page,
            "user_id"=>$request->author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        return redirect("/detail-hotel-$request->hotels_id#extra-bed")->with('success', 'Extra bed successfully added');
    }

    // Function Edit Extra Bed =============================================================================================================>
    public function fedit_extra_bed(Request $request,$id){
        $extra_bed=ExtraBed::findOrFail($id);
        $extra_bed->update([
            "type"=>$request->type,
            "max_age"=>$request->max_age,
            "min_age" =>$request->min_age, 
            "description" =>$request->description, 
            "contract_rate" =>$request->contract_rate, 
            "markup" =>$request->markup, 
        ]);

        // USER LOG
        $service = "Extra Bed";
        $action = "Update Extra Bed";
        $subservice = "Extra Bed";
        $page = "detail-hotel#extra-bed";
        $note = "Update extra bed to Hotel id : ".$request->hotels_id;
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
        return redirect("/detail-hotel-$request->hotels_id#extra-bed")->with('success','extra bed has been updated!');
    }

    // Function Delete Extra Bed =============================================================================================================>
    public function fdelete_extra_bed(Request $request,$id){
        $extra_bed=ExtraBed::findOrFail($id);
        $author= Auth::user()->id;
        // USER LOG
        $action = "Remove";
        $service = "Extra Bed";
        $subservice = "Extra Bed";
        $page = "detail-hotel#extra-bed";
        $note = "Remove extra bed on Hotel id : ".$request->hotels_id.", extra bed id : ".$id;
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
        $extra_bed->delete();
        return back()->with('success','Extra bed has been successfully deleted!');
    }
}
