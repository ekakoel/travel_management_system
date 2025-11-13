<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\UserLog;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','can:isAdmin']);
    }
    public function index()
    {
        $now = Carbon::now();
        $promotions = Promotion::where('status','!=',"Archived")->get();
        $activepromotion = Promotion::where('status',"Active")->get();
        $draft_promotions = Promotion::where('status',"Draft")->get();
        $pending_promotions = Promotion::where('status',"Pending")->get();
        $invalid_promotions = Promotion::where('status',"Invalid")->get();
        $expired_promotions = Promotion::where('status',"Expired")->get();
        $rejected_promotions = Promotion::where('status',"Rejected")->get();
        $usedup_promotions = Promotion::where('status',"Usedup")->get();
        $exp_promotions = Promotion::where('periode_end','<',$now)->get();
        // $active_promotions = Promotion::where('periode_start','<=',$now)->where('periode_end','>',$now)->get();
        // $drf_promotions = Promotion::where('periode_start','>',$now)->get();
        if (count($exp_promotions) > 0) {
            foreach ($exp_promotions as $promos) {
                $promos->update([
                    "status"=>"Draft",
                ]);
            }
        }
        // if (count($active_promotions) > 0) {
        //     foreach ($active_promotions as $actprom) {
        //         $actprom->update([
        //             "status"=>"Active",
        //         ]);
        //     }
        // }
        // if (count($drf_promotions) > 0) {
        //     foreach ($drf_promotions as $draftprom) {
        //         $draftprom->update([
        //             "status"=>"Draft",
        //         ]);
        //     }
        // }
        return view('admin.promotion', compact('promotions'),[
            'promotions'=>$promotions,
            'activepromotion'=>$activepromotion,
            'draft_promotions'=>$draft_promotions,
            'pending_promotions'=>$pending_promotions,
            'invalid_promotions'=>$invalid_promotions,
            'expired_promotions'=>$expired_promotions,
            'rejected_promotions'=>$rejected_promotions,
            'usedup_promotions'=>$usedup_promotions,
            'now'=>$now,
        ]);
    }

    public function create(Request $request)
    {
        if($request->hasFile("cover")){
            $file=$request->file("cover");
            $coverName=time().'_'.$file->getClientOriginalName();
            $file->move("storage/promotion/cover/",$coverName);
            $status = "Draft";
            $periode_start = date('Y-m-d', strtotime($request->periode_start));
            $periode_end = date('Y-m-d', strtotime($request->periode_end));
            $promotion =new Promotion([
                "cover"=>$coverName,
                "name"=>$request->name,
                "author_id"=>$request->author_id,
                "editor_id"=>$request->editor_id,
                "description"=>$request->description,
                "discounts"=>$request->discounts,
                "periode_start"=>$periode_start,
                "periode_end"=>$periode_end,
                "status"=>$status,
                "term"=>$request->term,
            ]);
            // @dd($promotion);
            $promotion->save();
        }
        // USER LOG
        $action = "Add Promotion";
        $service = "Promotion";
        $subservice = "Promotion";
        $page = "promotion";
        $note = "Add new Promotion : ".$promotion->id;
        
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$promotion->id,
            "page"=>$page,
            "user_id"=>$request->author_id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        return redirect("/promotion")->with('success', 'The promotion created successfully');
    }
    // FUNCTION UPDATE PROMOTION =============================================================================================================>
    public function update(Request $request, $id){
        $promotion=Promotion::findOrFail($id);
        $periode_start = date('Y-m-d', strtotime($request->periode_start));
        $periode_end = date('Y-m-d', strtotime($request->periode_end));
        $service="Promotion";
        $action="Update";
        if($request->hasFile("cover")){
            if (File::exists("storage/promotion/cover/".$promotion->cover)) {
                File::delete("storage/promotion/cover/".$promotion->cover);
            }
            $file=$request->file("cover");
            $promotion->cover=time()."_".$file->getClientOriginalName();
            $file->move("storage/promotion/cover/",$promotion->cover);
            $request['cover']=$promotion->cover;
            
        }

        $promotion->update([
            "cover"=>$promotion->cover,
            "name"=>$request->name,
            "editor_id"=>$request->editor_id,
            "description"=>$request->description,
            "discounts"=>$request->discounts,
            "periode_start"=>$periode_start,
            "periode_end"=>$periode_end,
            "status"=>$request->status,
            "term"=>$request->term,
        ]);

        // USER LOG
        $action = "Update";
        $service = "Promotion";
        $subservice = "Promotion";
        $page = "promotion";
        $note = "Update promotion : ".$id;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$id,
            "page"=>$page,
            "user_id"=>$request->editor_id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        return redirect("/promotion")->with('success','The promotion has been updated!');
    }
    // Function Remove Booking Code =============================================================================================================>
    public function destroy(Request $request, $id){
        $promotion = Promotion::findOrFail($id);
        $status = "Archived";
        $promotion->update([
            "status"=>$status,
        ]);
        return redirect("/promotion")->with('success','The promotion has been removed!');
    }
}
