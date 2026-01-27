<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use App\Models\Attention;
use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use App\Models\TermAndCondition;
use App\Http\Requests\StoreTermAndConditionRequest;
use App\Http\Requests\UpdateTermAndConditionRequest;

class TermAndConditionController extends Controller
{

    public function index()
    {
        $attentions = Attention::where('page','term-and-condition')->get();
        $tandcs = TermAndCondition::where('type','User')
        ->get();
        $system_term = TermAndCondition::where('type','System')
        ->get();
        $admin_term = TermAndCondition::where('type','Administrator')
        ->get();
        $price_term = TermAndCondition::where('type','Price')
        ->get();
        $promotion_term = TermAndCondition::where('type','Promotion')
        ->get();
        $currency_term = TermAndCondition::where('type','Currency')
        ->get();
        $business = BusinessProfile::where('id',1)->first();
        return view('privacy-policy.term-and-condition',compact('tandcs'),[
            'attentions'=>$attentions,
            'system_term'=>$system_term,
            'admin_term'=>$admin_term,
            'price_term'=>$price_term,
            'promotion_term'=>$promotion_term,
            'currency_term'=>$currency_term,
            'business'=>$business,
        ]);
    }
    public function v_privacy_policy()
    {
        $business = BusinessProfile::where('id',1)->first();
        $attentions = Attention::where('page','term-and-condition')->get();
        $tandcs = TermAndCondition::where('type','User')
        ->where('status','Active')
        ->get();
        $system_term = TermAndCondition::where('type','System')
        ->where('status','Active')
        ->get();
        $admin_term = TermAndCondition::where('type','Administrator')
        ->where('status','Active')
        ->get();
        $price_term = TermAndCondition::where('type','Price')
        ->get();
        $promotion_term = TermAndCondition::where('type','Promotion')
        ->get();
        return view('privacy-policy.privacy-policy',compact('tandcs'),[
            'attentions'=>$attentions,
            'system_term'=>$system_term,
            'admin_term'=>$admin_term,
            'price_term'=>$price_term,
            'promotion_term'=>$promotion_term,
            'business'=>$business,
        ]);
    }

    // FUNCTION EDIT POLICY =============================================================================================================>
    public function func_edit_policy(Request $request,$id)
    {
        $policy=TermAndCondition::findOrFail($id);
        $policy->update([
            "type"=>$request->type,
            "name_id"=>$request->name_id,
            "name_en"=>$request->name_en,
            "name_zh"=>$request->name_zh,
            "policy_id"=>$request->policy_id,
            "policy_en"=>$request->policy_en,
            "policy_zh"=>$request->policy_zh,
            "status"=>$request->status,
        ]);

        // USER LOG
        $action = "Edit Policy";
        $service = "Term & Condition";
        $subservice = "Policy";
        $page = "term-and-condition";
        $note = "Update Policy: ".$id;
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
        return redirect("/term-and-condition")->with('success','Policy has been Updated!');
    }


    // FUNCTION ADD POLICY =============================================================================================================>
    public function func_add_policy(Request $request)
        {
            $policy =new TermAndCondition([
                "type"=>$request->type,
                "name_id"=>$request->name_id,
                "name_en"=>$request->name_en,
                "name_zh"=>$request->name_zh,
                "policy_en"=>$request->policy_en,
                "policy_id"=>$request->policy_id,
                "policy_zh"=>$request->policy_zh,
                "status"=>$request->status,
            ]);
            $policy->save();
            
            // USER LOG
            $action = "Add new Policy";
            $service = "Term & Condition";
            $subservice = "Policy";
            $page = "term-and-condition";
            $note = "Add New Policy: ".$policy->id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$policy->id,
                "page"=>$page,
                "user_id"=>$request->author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/term-and-condition")->with('success','New Policy added successfully!');
        }
// FUNCTION REMOVE SERVICE =============================================================================================================>
    public function fdestroy_policy(Request $request,$id)
        {
            $policy=TermAndCondition::findOrFail($id);
            $policy->delete();
        
            // USER LOG
            $action = "Destroy Policy";
            $service = "Term & Condition";
            $subservice = "Policy";
            $page = "term-and-condition";
            $note = "Destroy Policy: ".$policy->id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$policy->id,
                "page"=>$page,
                "user_id"=>$request->author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/term-and-condition")->with('success','Policy has been Removed!');
        }

    public function terms_and_conditions()
    {
        $attentions = Attention::where('page','term-and-condition')->get();
        $tandcs = TermAndCondition::where('type','User')
        ->get();
        $system_term = TermAndCondition::where('type','System')
        ->get();
        $admin_term = TermAndCondition::where('type','Administrator')
        ->get();
        $price_term = TermAndCondition::where('type','Price')
        ->get();
        $promotion_term = TermAndCondition::where('type','Promotion')
        ->get();
        $currency_term = TermAndCondition::where('type','Currency')
        ->get();
        $business = BusinessProfile::where('id',1)->first();
        return view('privacy-policy.terms-and-conditions',compact('tandcs'),[
            'attentions'=>$attentions,
            'system_term'=>$system_term,
            'admin_term'=>$admin_term,
            'price_term'=>$price_term,
            'promotion_term'=>$promotion_term,
            'currency_term'=>$currency_term,
            'business'=>$business,
        ]);
    }
    public function privacy_policy()
    {
        $attentions = Attention::where('page','term-and-condition')->get();
        $tandcs = TermAndCondition::where('type','User')
        ->get();
        $system_term = TermAndCondition::where('type','System')
        ->get();
        $business = BusinessProfile::where('id',1)->first();
        return view('privacy-policy.privacy-policy',[
            'attentions'=>$attentions,
            'system_term'=>$system_term,
            'business'=>$business,
        ]);
    }
}
