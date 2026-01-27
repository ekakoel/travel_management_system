<?php

namespace App\Http\Controllers;

use App\Models\Countries;
use Illuminate\Http\Request;
use App\Models\WeddingPlanner;
use App\Models\WeddingInvitations;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreWeddingInvitationsRequest;
use App\Http\Requests\UpdateWeddingInvitationsRequest;

class WeddingInvitationsController extends Controller
{
    public function view_update_wedding_invitations($id)
    {
        $countries = Countries::all();
        $wedding_planner = WeddingPlanner::find($id);
        $agent_id = Auth::user()->id;
        $invitations = WeddingInvitations::where('wedding_planner_id',$id)->get();
        if ($wedding_planner->agent_id == $agent_id) {
            return view('main.wedding-invitations-update',[
                "countries"=>$countries,
                "wedding_planner"=>$wedding_planner,
                "invitations"=>$invitations,
            ]);
        }else{
            return redirect('weddings')->with('success','Wedding planner not found');
        }
    }

    public function func_add_wedding_invitations(Request $request,$id)
    {
        $wedding_planner_id = $id;
        $sex = $request->sex;
        $name = $request->name;
        $chinese_name = $request->chinese_name;
        $country = $request->country;
        $passport_no = $request->passport_no;
        $phone = $request->phone;
        $c_name = count($name);
        for ($i=0; $i < $c_name; $i++) { 
            $invitation =new WeddingInvitations([
                "wedding_planner_id"=>$wedding_planner_id,
                "sex"=>$sex[$i],
                "name"=>$name[$i],
                "chinese_name"=>$chinese_name[$i],
                "country"=>$country[$i],
                "passport_no"=>$passport_no[$i],
                "phone"=>$phone[$i],
            ]);
            $invitation->save();
        }

        return redirect('wedding-invitations-update-'.$id)->with('success','New wedding invitations has been added');
    }
    public function func_update_wedding_invitations(Request $request,$id)
    {
        $invitation = WeddingInvitations::find($id);
        $invitation->update([
            "sex"=>$request->sex,
            "name"=>$request->name,
            "chinese_name"=>$request->chinese_name,
            "country"=>$request->country,
            "passport_no"=>$request->passport_no,
            "phone"=>$request->phone,
        ]);
        return redirect('wedding-invitations-update-'.$invitation->wedding_planner_id)->with('success','Wedding invitations has been updated');
    }
    // FUNC DESTROY WEDDING INVITATIONS
    public function func_destroy_wedding_invitations(Request $request,$id){
        $wedding_invitation = WeddingInvitations::find($id);
        $wedding_invitation->delete();
        return redirect()->back()->with('success','Wedding invitation has been deleted');
    }
}
