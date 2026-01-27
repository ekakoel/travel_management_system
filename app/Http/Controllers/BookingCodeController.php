<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\UserLog;
use App\Models\Attention;
use App\Models\BookingCode;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBookingCodeRequest;
use App\Http\Requests\UpdateBookingCodeRequest;

class BookingCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified','type:admin']);
    }
    public function index()
    {
        $now = Carbon::now();
        $bookingcodes=BookingCode::where('status','!=',"Archived")->get();
        $activebookingcodes=BookingCode::where('status','Active')->get();
        $draft_bookingcodes=BookingCode::where('status','Draft')->get();
        $usedup_bookingcodes=BookingCode::where('status','Usedup')->get();
        $expired_bookingcodes=BookingCode::where('status','Expired')->get();
        $invalid_bookingcodes=BookingCode::where('status','Invalid')->get();
        $pending_bookingcodes=BookingCode::where('status','Pending')->get();
        $rejected_bookingcodes=BookingCode::where('status','Rejected')->get();
        $bookingcode_exp = BookingCode::where('expired_date','<',$now)->where('status','Active')->get();
        $attentions = Attention::where('page','booking-code')->get();
        if (count($bookingcode_exp) > 0) {
            foreach ($bookingcode_exp as $bcode) {
                $bcode->update([
                    "status"=>"Expired",
                ]);
            }
        }
        return view('admin.booking-code', compact('bookingcodes'),[
            "activebookingcodes" => $activebookingcodes,
            "draft_bookingcodes" => $draft_bookingcodes,
            "usedup_bookingcodes" => $usedup_bookingcodes,
            "expired_bookingcodes" => $expired_bookingcodes,
            "invalid_bookingcodes" => $invalid_bookingcodes,
            "pending_bookingcodes" => $pending_bookingcodes,
            "rejected_bookingcodes" => $rejected_bookingcodes,
            "now" => $now,
            "attentions" => $attentions,
        ]);
       
    }
// Function Create Booking Code =============================================================================================================>
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'code' => 'required|string|max:25|unique:booking_codes',
            'discounts' => 'required',
            'amount' => 'required',
            'author_id' => 'required',
            'expired_date' => 'required',
        ]);
            $code = strtoupper($request->code);
            $used = 0;
            $expired_date = date('Y-m-d', strtotime($request->expired_date));
            $status = "Draft";
            $bookingcode =new BookingCode([
                "name"=>$request->name,
                "code"=>$code,
                "discounts" =>$request->discounts, 
                "amount"=>$request->amount,
                "used"=>$used,
                "author"=>$request->author_id,
                "expired_date"=>$expired_date,
                "status"=>$status,
            ]);
            $bookingcode->save();
        
         // USER LOG
         $action = "Create Booking Code";
         $service = "Booking Code";
         $subservice = "Booking Code";
         $page = "booking-code";
         $note = "Create Booking Code: ".$bookingcode->id;
         $user_log =new UserLog([
             "action"=>$action,
             "service"=>$service,
             "subservice"=>$subservice,
             "subservice_id"=>$bookingcode->id,
             "page"=>$page,
             "user_id"=>$request->author_id,
             "user_ip"=>$request->getClientIp(),
             "note" =>$note, 
         ]);
         $user_log->save();
         return redirect("/booking-code")->with('success','Booking code has been successfully created!');
    }

// Function Update Booking Code =============================================================================================================>
    public function func_update_bookingcode(Request $request, $id){
        $bookingcode = BookingCode::findOrFail($id);
        $code = strtoupper($request->code);
        $expired_date = date('Y-m-d', strtotime($request->expired_date));
        $bookingcode->update([
            "name"=>$request->name,
            "code"=>$code,
            "discounts" =>$request->discounts, 
            "amount"=>$request->amount,
            "author"=>$request->author_id,
            "expired_date"=>$expired_date,
            "status"=>$request->status,
        ]);
        // dd($bookingcode);
        $action = "Update Booking Code";
        $service = "Booking Code";
        $subservice = "Booking Code";
        $page = "booking-code";
        $note = "Update Booking Code: ".$bookingcode->id;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$bookingcode->id,
            "page"=>$page,
            "user_id"=>$request->author_id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        return redirect("/booking-code")->with('success','Booking code has been updated!');
    }
// Function Remove Booking Code =============================================================================================================>
    public function func_remove_bookingcode(Request $request, $id){
        $bookingcode = BookingCode::findOrFail($id);
        $status = "Archived";
        $bookingcode->update([
            "status"=>$status,
        ]);
        return redirect("/booking-code")->with('success','Booking code has been removed!');
    }

}
