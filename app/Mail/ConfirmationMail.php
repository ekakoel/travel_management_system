<?php

namespace App\Mail;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Orders;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $id;
    
    public function __construct($id)
    {
       $this->id = $id;
    }

    
    public function build()
    {
        $now = Carbon::now();
        $order = Orders::where('id', $this->id)->first();
        $agent = User::where('id', $order->user_id)->first();
        $order_link = 'https://online.balikamitour.com/detail-order-'.$order->id;
        $admin = Auth::user()->where('id',$order->verified_by)->first();
        $invs = InvoiceAdmin::where('rsv_id',$order->rsv_id)->first();
        $invoice = public_path('storage/document/invoice-'.$order->id.'.pdf');
        $airport_shuttles = AirportShuttle::where('order_id', $order->id)->get();
        return $this->view('emails.confirmationOrder',[
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
}
