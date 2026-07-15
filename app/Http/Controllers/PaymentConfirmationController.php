<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\User;
use App\Models\Orders;
use App\Models\OrderLog;
use App\Models\Reservation;
use App\Models\InvoiceAdmin;
use App\Models\OrderWedding;
use Illuminate\Http\Request;
use App\Models\PaymentConfirmation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StorePaymentConfirmationRequest;
use App\Http\Requests\UpdatePaymentConfirmationRequest;

class PaymentConfirmationController extends Controller
{
    private function resolveOrderDetailRedirect(Orders $order): string
    {
        if ($order->service == "Hotel" || $order->service == "Hotel Promo" || $order->service == "Hotel Package") {
            return "/detail-order-hotel/$order->id";
        }

        if ($order->service == "Private Villa") {
            return "/detail-order-villa/$order->id";
        }

        if ($order->service == "Transport") {
            return "/detail-order-transport/$order->id";
        }

        return "/detail-order-tour/$order->id";
    }

    private function getInvoicePaymentDeadline(?InvoiceAdmin $invoice): ?Carbon
    {
        if (!$invoice || !$invoice->due_date) {
            return null;
        }

        return Carbon::parse($invoice->due_date);
    }

    private function hasPaymentSubmission(?InvoiceAdmin $invoice): bool
    {
        if (!$invoice) {
            return false;
        }

        return $invoice->payment()->whereIn('status', ['Pending', 'Valid', 'Paid'])->exists();
    }

    private function autoCancelExpiredOrder(Orders $order, ?InvoiceAdmin $invoice, Request $request): Orders
    {
        $deadline = $this->getInvoicePaymentDeadline($invoice);

        if (
            $order->status !== 'Approved'
            || !$deadline
            || !$deadline->isPast()
            || $this->hasPaymentSubmission($invoice)
        ) {
            return $order;
        }

        $order->update([
            'status' => 'Canceled',
            'msg' => 'Automatically canceled because no payment confirmation was submitted within 48 hours after approval.',
        ]);

        if ($order->rsv_id) {
            Reservation::where('id', $order->rsv_id)->update([
                'status' => 'Canceled',
            ]);
        }

        OrderLog::create([
            'order_id' => $order->id,
            'action' => 'Auto Cancel Payment Deadline',
            'url' => $request->getClientIp(),
            'method' => 'Update',
            'agent' => $order->name,
            'admin' => Auth::id(),
        ]);

        $order->status = 'Canceled';

        return $order;
    }

    public function payment_confirmation(Request $request,$id)
    {
        $now = Carbon::now();
        $order = Orders::findOrFail($id);
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();

        $order = $this->autoCancelExpiredOrder($order, $invoice, $request);

        if ($order->status !== 'Approved' || !$invoice) {
            return redirect($this->resolveOrderDetailRedirect($order))->with('error', 'This order is no longer available for payment confirmation.');
        }

        if($request->hasFile("receipt_name")){
            $file=$request->file("receipt_name");
            $receipt_name=$invoice->inv_no.'_'.time().'_'.$file->getClientOriginalName();
            $file->move("storage/receipt/",$receipt_name);
            
            $status="Pending";
            $payment =new PaymentConfirmation([
                "receipt_img"=>$receipt_name,
                "inv_id"=>$invoice->id,
                "status"=>$status,
            ]);
            $payment->save();
            
            $order_log =new OrderLog([
                "order_id"=>$order->id,
                "action"=>"Upload Receipt",
                "url"=>$request->getClientIp(),
                "method"=>"Upload",
                "agent"=>$order->name,
                "admin"=>Auth::user()->id,
            ]);
            $agent = User::where('id',$order->user_id)->first();
            $order_log->save();
            $title = "Payment Confirmation ".$reservation->rsv_no;
            $order_link = 'https://online.balikamitour.com/orders-admin-'.$order->id;
            $data = [
                'now'=>$now,
                'agent'=>$agent,
                'title'=>$title,
                'order'=>$order,
                'order_link'=>$order_link,
            ];
            if (config('filesystems.default') == 'public'){
                $receipt =realpath("storage/receipt/".$receipt_name);
            }else {
                $receipt = storage::url("storage/receipt/".$receipt_name);
            }
            Mail::send('emails.paymentConfirmation', $data, function($message)use($data, $receipt) {
                $message->to(config('app.reservation_mail'))
                    ->subject($data["title"])
                    ->attach($receipt);
            });
            return redirect($this->resolveOrderDetailRedirect($order))->with('success','Payment proof has been sent.');
        }else{
            return redirect("/detail-order-$order->id")->with('error','Please try again');
        }
    }
    // WEDDING
    public function wedding_payment_confirmation(Request $request,$id)
    {
        $now = Carbon::now();
        $orderWedding = OrderWedding::findOrFail($id);
        $reservation = Reservation::where('id',$orderWedding->rsv_id)->first();
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        if($request->hasFile("receipt_name")){
            $file=$request->file("receipt_name");
            $receipt_name=$invoice->inv_no.'_'.time().'_'.$file->getClientOriginalName();
            $file->move("storage/receipt/weddings/",$receipt_name);
            $agent = User::where('id',$orderWedding->agent_id)->first();
            $status="Pending";
            $payment =new PaymentConfirmation([
                "receipt_img"=>$receipt_name,
                "inv_id"=>$invoice->id,
                "status"=>$status,
            ]);
            $payment->save();
            
            $order_log =new OrderLog([
                "order_id"=>$orderWedding->id,
                "action"=>"Upload Receipt",
                "url"=>$request->getClientIp(),
                "method"=>"Upload",
                "agent"=>$agent->name,
                "admin"=>Auth::user()->id,
            ]);
            
            $order_log->save();
            $title = "Wedding Payment Confirmation ".$reservation->rsv_no;
            $order_link = 'https://online.balikamitour.com/orders-admin-'.$orderWedding->id;
            $data = [
                'now'=>$now,
                'agent'=>$agent,
                'title'=>$title,
                'orderWedding'=>$orderWedding,
                'reservation'=>$reservation,
                'invoice'=>$invoice,
                'order_link'=>$order_link,
            ];
            if (config('filesystems.default') == 'public'){
                $receipt =realpath("storage/receipt/weddings/".$receipt_name);
            }else {
                $receipt = storage::url("storage/receipt/weddings/".$receipt_name);
            }
            Mail::send('emails.paymentConfirmationWedding', $data, function($message)use($data, $receipt) {
                $message->to(config('app.reservation_mail'))
                    ->subject($data["title"])
                    ->attach($receipt);
            });

            return redirect("/detail-order-wedding-$orderWedding->orderno")->with('success','Payment proof has been sent.');
        }else{
            return redirect("/detail-order-wedding-$orderWedding->orderno")->with('error','Please try again');
        }
    }
    public function update_payment_confirmation(Request $request,$id)
    {
        $now = Carbon::now();
        $order = Orders::findOrFail($id);
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        if ($order->service == "Wedding Package") {
            $receipt = PaymentConfirmation::where('id',$request->receipt_id)->first();
        }else{
            $receipt = PaymentConfirmation::where('inv_id',$invoice->id)->first();
        }
        if($request->hasFile("activity_receipt_name")){
            if (File::exists("storage/receipt/".$receipt->receipt_img)) {
                File::delete("storage/receipt/".$receipt->receipt_img);
            }
            $file=$request->file("activity_receipt_name");
            $receipt_name=$invoice->inv_no.'_'.time().'_'.$file->getClientOriginalName();
            $file->move("storage/receipt/",$receipt_name);
            $status="Pending";
            $receipt->update([
                "receipt_img"=>$receipt_name,
                "status"=>$status,
                "note"=>$note,
            ]);
            // dd($receipt);
            $order_log =new OrderLog([
                "order_id"=>$order->id,
                "action"=>"Change Receipt",
                "url"=>$request->getClientIp(),
                "method"=>"Update",
                "agent"=>$order->name,
                "admin"=>Auth::user()->id,
            ]);
            $order_log->save();
            return redirect("/detail-order-$order->id")->with('success','Payment proof has been updated.');
        }else{
            return redirect("/detail-order-$order->id")->with('error','Please try again');
        }
    }

}
