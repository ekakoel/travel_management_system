<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Orders;
use App\Models\UserLog;
use App\Models\Partners;
use App\Models\UsdRates;
use App\Models\Attention;
use App\Models\Promotion;
use App\Models\Activities;
use App\Models\BookingCode;
use Illuminate\Support\Str;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use App\Models\ActivitiesImages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Requests\StoreactivitiesRequest;
use App\Http\Requests\UpdateactivitiesRequest;

class ActivitiesController extends Controller
{   
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index()
    {   
        $activities=Activities::where('status', '=','active')
        ->paginate(12)->withQueryString();
        $type = ActivityType::all();
        $promotions = Promotion::where('status',"Active")->get();
        return view('main.activities', compact('activities'),[
            'type' => $type,
            'promotions' => $promotions,
        ]);
    }
// Search Activities =========================================================================================>
    public function search_activities(Request $request){
        if (isset($promotions)){
            $pr = count($promotions);
            $promotion_price = 0;
            for ($i=0; $i < $pr; $i++) { 
                $promotion_price = $promotion_price + $promotions[$i]->discounts;
            }
        }else{
            $promotion_price = 0;
        }
        $now = Carbon::now();
        $taxes = Tax::where('id',1)->first();
        $usdrates = UsdRates::where('id',1)->first();
        $type = ActivityType::all();
        $activities_type = $request->activities_type;
        $location = $request->get('location');
        $promotions = Promotion::where('status',"Active")->get();
        $activities = Activities::where('status', '=','Active')
            ->where('type','LIKE','%'.$activities_type.'%')
            ->where('location','LIKE','%'.$location.'%')
            ->get();
        $user_id = Auth::user()->id;
        $orders = Orders::where('user_id', $user_id)->get();
        if (isset($request->bookingcode)) {
            $bk_code = BookingCode::where('code', $request->bookingcode)->where('status', 'Active')->first();
            if (isset($bk_code)) {
                if ($bk_code->used < $bk_code->amount) {
                    if (isset($orders)) {
                        $usedcode = $orders->where('bookingcode', $bk_code->code)->first();
                        if (isset($usedcode)) {
                            $bookingcode_status = "Used";
                            $bookingcode = null;
                        }else{
                            if ($bk_code->expired_date >= $now) {
                                $bookingcode_status = "Valid";
                                $bookingcode = $bk_code;
                            }else{
                                $bookingcode_status = "Expired";
                                $bookingcode = null ;
                            }
                        }
                    }else{
                        if ($bk_code->expired_date >= $now) {
                            $bookingcode_status = "Valid";
                            $bookingcode = $bk_code;
                        }else{
                            $bookingcode_status = "Expired";
                            $bookingcode = null ;
                        }
                    }
                }else{
                    $bookingcode_status = "Expired";
                    $bookingcode = null ;
                }
            }else{
                $bookingcode_status = 'Invalid';
                $bookingcode = null;
            }
        }else{
            $bookingcode_status = null;
            $bookingcode = null;
        }
        return view('main.activitiessearch', compact('activities'),[
            'type'=>$type,
            'bookingcode'=>$bookingcode,
            'bookingcode_status'=>$bookingcode_status,
            'usdrates'=>$usdrates,
            'taxes'=>$taxes,
            'orders'=>$orders,
            'promotions'=>$promotions,
            'promotion_price'=>$promotion_price,
        ]);
    }
// View Activities Detail =========================================================================================>
    public function activitydetail($code){
        $now = Carbon::now();
        $taxes = Tax::where('id',1)->first();
        $usdrates = UsdRates::where('id',1)->first();
        $business = BusinessProfile::where('id',1)->first();

        $orders = Orders::all();
        $orderno = count($orders) + 1;

        $type = ActivityType::all();
        $activities = Activities::all();
        $activity = Activities::where('code',$code)->first();
        $partner = Partners::where('id',$activity->partners_id)->first();
        $nearactivities = Activities::where('location','=',$activity->location)
        ->where('id','!=',$activity->id)
        ->where('status','Active')
        ->get();

        $agents = Auth::user()->where('status',"Active")->get();
        $bookingcode_status = null;
        $bookingcode = null;

        $price_non_tax = (ceil($activity->contract_rate / $usdrates->rate))+$activity->markup;
        $tax = ceil(($taxes->tax/100) * $price_non_tax);
        $normal_price = $price_non_tax + $tax;

        $promotions = Promotion::where('status',"Active")->get();
        if (isset($promotions)){
            $pr = count($promotions);
            $promotion_price = 0;
            for ($i=0; $i < $pr; $i++) { 
                $promotion_price = $promotion_price + $promotions[$i]->discounts;
            }
            $final_price = $normal_price - $promotion_price;
        }else{
            $promotion_price = 0;
            $final_price = $normal_price;
        }

        return view('main.activitydetail', compact('activity'),[
            'taxes'=> $taxes,
            'tax'=> $tax,
            'agents'=> $agents,
            'price_non_tax'=>$price_non_tax,
            'usdrates'=>$usdrates,
            'orderno'=>$orderno,
            'type' => $type,
            'activities' => $activities,
            'now' => $now,
            'nearactivities' => $nearactivities,
            'business'=>$business,
            'partner'=>$partner,
            'bookingcode'=>$bookingcode,
            'bookingcode_status'=>$bookingcode_status,
            'promotions'=>$promotions,
            'promotion_price'=>$promotion_price,
            'normal_price'=>$normal_price,
            'final_price'=>$final_price,
        ]);
    }
    public function activity_check_code(Request $request){
        $activity = Activities::where('id',$request->activity_id)->first();
        return redirect("/activity-$activity->code-$request->bookingcode");
    }
// View Activities Detail with code =========================================================================================>
    public function activitydetail_bookingcode($code,$bcode)
        {
            $now = Carbon::now();
            $taxes = Tax::where('id',1)->first();
            $usdrates = UsdRates::where('id',1)->first();
            $business = BusinessProfile::where('id',1)->first();
            
            $orders = Orders::all();
            $orderno = count($orders) + 1;

            $promotions = Promotion::where('status',"Active")->get();
            $activities = Activities::all();
            $activity = Activities::where('code',$code)->first();
            $agents = Auth::user()->where('status',"Active")->get();
            $partner = Partners::where('id',$activity->partners_id)->first();
            $nearactivities = Activities::where('location','=',$activity->location)
            ->where('id','!=',$activity->id)
            ->where('status','Active')
            ->get();
            $type = ActivityType::all();

            $user_id = Auth::user()->id;
            $order = Orders::where('user_id', $user_id)->get();

            if (isset($bcode)) {
                $bk_code = BookingCode::where('code', $bcode)->where('status', 'Active')->first();
                if (isset($bk_code)) {
                    if ($bk_code->used < $bk_code->amount) {
                        if (isset($order)) {
                            $usedcode = $order->where('bookingcode', $bk_code->code)->first();
                            if (isset($usedcode)) {
                                $bookingcode_status = "Used";
                                $bookingcode = null;
                            }else{
                                if ($bk_code->expired_date >= $now) {
                                    $bookingcode_status = "Valid";
                                    $bookingcode = $bk_code;
                                }else{
                                    $bookingcode_status = "Expired";
                                    $bookingcode = null ;
                                }
                            }
                        }else{
                            if ($bk_code->expired_date >= $now) {
                                $bookingcode_status = "Valid";
                                $bookingcode = $bk_code;
                            }else{
                                $bookingcode_status = "Expired";
                                $bookingcode = null ;
                            }
                        }
                    }else{
                        $bookingcode_status = "Expired";
                        $bookingcode = null ;
                    }
                }else{
                    $bookingcode_status = 'Invalid';
                    $bookingcode = null;
                }
            }else{
                $bookingcode_status = null;
                $bookingcode = null;
            }

            if (isset($promotions)){
                $pr = count($promotions);
                $promotion_price = 0;
                for ($i=0; $i < $pr; $i++) { 
                    $promotion_price = $promotion_price + $promotions[$i]->discounts;
                }
            }else{
                $promotion_price = 0;
            }

            $price_non_tax = (ceil($activity->contract_rate / $usdrates->rate))+$activity->markup;
            $tax = ceil(($taxes->tax/100) * $price_non_tax);
            $normal_price = $price_non_tax + $tax;

            if (isset($bookingcode->code) or isset($promotions)) {
                if (isset($bookingcode->code)) {
                    $price_per_pax = $normal_price;
                    
                    if (isset($promotions)) {
                        $final_price = $normal_price - $bookingcode->discounts - $promotion_price;
                    }else{
                        $final_price = $normal_price - $bookingcode->discounts;
                    }
                }else{
                    $price_per_pax = $normal_price ;
                    $final_price = $normal_price  - $promotion_price;
                }
            }else {
                $price_per_pax = $normal_price;
                $final_price = $normal_price;
            }
            return view('main.activitydetail', compact('activity'),[
                    'taxes'=> $taxes,
                    'agents'=> $agents,
                    'usdrates'=>$usdrates,
                    'orderno'=>$orderno,
                    'type' => $type,
                    'activities' => $activities,
                    'now' => $now,
                    'nearactivities' => $nearactivities,
                    'business'=>$business,
                    'partner'=>$partner,
                    'bookingcode'=>$bookingcode,
                    'bookingcode_status'=>$bookingcode_status,
                    'tax'=>$tax,
                    'order'=>$order,
                    'promotions'=>$promotions,
                    'normal_price'=>$normal_price,
                    'final_price'=>$final_price,
                    'price_non_tax'=>$price_non_tax,
                    'promotion_price'=>$promotion_price,
                ]);
        }



// View Admin Activities =========================================================================================>
    public function view_activities()
    {   
        $activities = Activities::all();
        $activeactivities=Activities::where('status', '=','active')->get();
        $nonactiveactivities=Activities::where('status', '=','nonactive')->get();
        $draftactivities=Activities::where('status', '=','draft')->get();
        return view('admin.activitiesadmin', compact('activeactivities'),[
            "nonactivactivities" => Activities::where('status', '=',"nonactive"),
            "activeactivities" => Activities::where('status', '=',"active"),
            "draftactivities" => Activities::where('status', '=',"draft"),
            "activeactivities" => $activeactivities,
            "nonactiveactivities" => $nonactiveactivities,
            "draftactivities" => $draftactivities,
            
        ]);
    }



// View Galery Edit =============================================================================================================>
    public function view_edit_galery_activity($id)
    {
        $activities=Activities::findOrFail($id);
        return view('form.activitygaleryedit')->with('activities',$activities);
    }


// Function Activity delete =============================================================================================================>
    public function destroy_activity(Request $request,$id)
    {
        $activity=Activities::findOrFail($id);
        $status = "Removed";
        $author = Auth::user()->id;
        $activity->update([
            "status"=>$status,
        ]);
        // USER LOG
        $action = "Remove Activity";
        $service = "Activity";
        $subservice = "Activity";
        $page = "detail-partner";
        $note = "Remove Activity: ".$id;
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
        return back()->with('success','The Activity has been successfully deleted!');
    }
    // public function destroy_activity($id)
    // {
    //      $activities=Activities::findOrFail($id);

    //      if (File::exists("images/cover/".$activities->cover)) {
    //          File::delete("images/cover/".$activities->cover);
    //      }
    //      $images=ActivitiesImages::where("activities_id",$activities->id)->get();
    //      foreach($images as $image){
    //      if (File::exists("images/activities".$image->image)) {
    //         File::delete("images/activities".$image->image);
    //     }
    //      }
    //      $activities->delete();
    //      return back();


    // }

// Function Activity image delete =============================================================================================================>
    public function delete_image_activity($id){
        $images=ActivitiesImages::findOrFail($id);
        if (File::exists("images/activities/".$images->image)) 
        {
           File::delete("images/activities/".$images->image);
        }

       ActivitiesImages::find($id)->delete();
       return back();
    }

// Function Cover Activity delete =============================================================================================================>
    public function delete_cover_activity($id){
        $cover=Activities::findOrFail($id)->cover;
        if (File::exists("images/cover/".$cover)) 
        {
            File::delete("images/cover/".$cover);
        }
        return back();
    }
    

}
