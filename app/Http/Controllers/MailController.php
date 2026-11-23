<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Hotels;
use App\Models\Orders;
use App\Models\InvoiceAdmin;
use Illuminate\Http\Request;
use App\Mail\ConfirmationMail;
use App\Models\AirportShuttle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function index(){
        // Mail::to('reservationbalikami@gmail.com','admin-1@balikamitour.com','yuli@balikamitour.com','pasek@balikamitour.com')->send(new ReservationMail());
        // return "Email sudah terkirim.";
        Mail::to(config('app.reservation_mail'))
        ->send(new ConfirmationMail($id));
    }



    public function view_email_approval(){
    $now = Carbon::now();
    $order = Orders::where('id', 133)->first();
    $agent = User::where('id', $order->user_id)->first();
    $order_link = 'https://online.balikamitour.com/detail-order-'.$order->id;
    $admin = Auth::user()->where('id',$order->verified_by)->first();
    $invs = InvoiceAdmin::where('rsv_id',$order->rsv_id)->first();
    $invoice = public_path('storage/document/invoice-'.$order->id.'.pdf');
    $airport_shuttles = AirportShuttle::where('order_id', $order->id)->get();
    return view('emails.approvalEmail',[
        'now'=>$now,
        'order'=>$order,
        'agent'=>$agent,
        'admin'=>$admin,
        'order_link'=>$order_link,
        'invs'=>$invs,
        'invoice'=>$invoice,
        'airport_shuttles'=>$airport_shuttles,
     ]);
    }
    public function view_email_payment_confirmation(){
        $now = Carbon::now();
        $order = Orders::where('id', 133)->first();
        $agent = User::where('id', $order->user_id)->first();
        $order_link = 'https://online.balikamitour.com/orders-admin-'.$order->id;
        $admin = Auth::user()->where('id',$order->verified_by)->first();
        $invs = InvoiceAdmin::where('rsv_id',$order->rsv_id)->first();
        $invoice = public_path('storage/document/invoice-'.$order->id.'.pdf');
        $airport_shuttles = AirportShuttle::where('order_id', $order->id)->get();
        return view('emails.paymentConfirmation',[
            'now'=>$now,
            'order'=>$order,
            'agent'=>$agent,
            'admin'=>$admin,
            'order_link'=>$order_link,
            'invs'=>$invs,
            'invoice'=>$invoice,
            'airport_shuttles'=>$airport_shuttles,
        ]);
    }

    public function sendEmailApproval()
    {
        $details = [
            'title' => 'Account Approval',
            'body' => 'Your account has been approved and can be used to access all services provided by PT. Bali Kami Tour. Thank you.'
        ];
        Mail::to('recipient@example.com')->send(new DemoEmail($details));
        return "Email sent successfully!";
    }
}