<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\UsdRates;
use App\Models\Attention;
use App\Models\BankAccount;
use App\Models\Reservation;
use App\Models\InvoiceAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\BusinessProfile;
use App\Models\AdditionalInvoice;
use App\Http\Requests\StoreInvoiceAdminRequest;
use App\Http\Requests\UpdateInvoiceAdminRequest;

class InvoiceAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index()
    {
        $now = Carbon::now();
        $invoices = InvoiceAdmin::where('due_date','>',$now)->orderBy('due_date','ASC')->get();
        $business = BusinessProfile::first();
        $now = Carbon::now();
        $attentions = Attention::where('page',"invoice")->get();
        $rsv = Reservation::all();
        
        return view('admin.invoice',[
            'invoices'=>$invoices,
            
            'business'=>$business,
            'now'=>$now,
            'attentions'=>$attentions,
            'rsv'=>$rsv,
        ]);
    }
    public function view_detail_invoice($id)
    {
        $invoice = InvoiceAdmin::find($id);
        $business = BusinessProfile::first();
        $now = Carbon::now();
        $attentions = Attention::where('page', 'Invoice-detail')->get();
        $reservation = Reservation::where('id',$invoice->rsv_id)->first();
        $in=Carbon::parse($reservation->checkin);
        $out=Carbon::parse($reservation->checkout);
        $dur_res = $in->diffInDays($out);
        $usdrates = UsdRates::where('name','USD')->first();
        $accommodations = Orders::where([
            ['service','Hotel'],['status','Active'],['rsv_id', $invoice->rsv_id],])
        ->orWhere([
            ['service','Hotel Promo'],['status','Active'],['rsv_id', $invoice->rsv_id],])
        ->orWhere([
            ['service','Hotel Package'],['status','Active'],['rsv_id', $invoice->rsv_id],])
        ->orderBy('checkin', 'asc')->get();
        $tours = Orders::where('status',"Active")
        ->where('service',"Tour Package")
        ->where('rsv_id',$invoice->rsv_id)
        ->get();
        $activities = Orders::where('status',"Active")
        ->where('service',"Activity")
        ->where('rsv_id',$invoice->rsv_id)
        ->get();
        $transports = Orders::where('status',"Active")
        ->where('service',"Transport")
        ->where('rsv_id',$invoice->rsv_id)
        ->get();
        $bank_acc = BankAccount::all();
        $additional_invoice = AdditionalInvoice::where('inv_id',$id)->get();
        $total_price_order = Orders::where('status',"Active")
        ->where('rsv_id',$invoice->rsv_id)
        ->sum('final_price');
        $sum_additional_invoice = AdditionalInvoice::where('inv_id',$invoice->id)
        ->sum('amount');
        return view('admin.invoice-detail',[
            'invoice'=>$invoice,
            'business'=>$business,
            'now'=>$now,
            'attentions'=>$attentions,
            'reservation'=>$reservation,
            'accommodations'=>$accommodations,
            'tours'=>$tours,
            'activities'=>$activities,
            'transports'=>$transports,
            'total_price_order'=>$total_price_order,
            'bank_acc'=>$bank_acc,
            'additional_invoice'=>$additional_invoice,
            'dur_res'=>$dur_res,
            'usdrates'=>$usdrates,
            'sum_additional_invoice'=>$sum_additional_invoice,
        ]);
    }

   // ADD INCLUDE ==================================================================================================================================================================================
   public function func_add_additional_inv(Request $request)
   {
    //    $validated = $request->validate([
    //        'rsv_id' => 'required',
    //        'remark' => 'required',
    //    ]);
        $amount = ($request->rate*$request->unit)*$request->times;
        $additionalinv = new AdditionalInvoice ([
            'date' =>$request->date,
            'inv_id' =>$request->inv_id,
            'description' =>$request->description,
            'rate' =>$request->rate,
            'unit' =>$request->unit,
            'times' =>$request->times,
            'amount' =>$amount,
        ]);
        // @dd($guest);
        $additionalinv->save();
        return redirect()->back()->with('success','Additional Service has been add to the Invoice');
    }
    
    // UPDATE Additional Invoice ==================================================================================================================================================================================
    public function func_update_additional_inv(Request $request,$id)
    {
        $add_inv = AdditionalInvoice::findOrFail($id);
        $amount = ($request->rate*$request->unit)*$request->times;
        $add_inv->update([
            'date' =>$request->date,
            'description' =>$request->description,
            'rate' =>$request->rate,
            'unit' =>$request->unit,
            'times' =>$request->times,
            'amount' =>$amount,
        ]);
        // @dd($guest);
        return redirect()->back()->with('success','Changes saved successfully');
    }
    // DELETE Additional Invoice ==================================================================================================================================================================================
    public function destroy_additional_inv(Request $request, $id)
    {
         $add_inv=AdditionalInvoice::findOrFail($id);
         $add_inv->delete();
         return redirect()->back()->with('success','Data has been removed');
    }
}
