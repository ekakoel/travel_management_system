<?php

namespace App\Mail;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Orders;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\AirportShuttle;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReservationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $id;
    
    public function __construct($id,$rquotation)
    {
       $this->id = $id;
       $this->quotation = $rquotation;
    }

    
    public function build()
    {
        $now = Carbon::now();
        $order = Orders::where('id', $this->id)->first();
        $agent = User::where('id', $order->user_id)->first();
        $order_link = 'https://online.balikamitour.com/orders-admin-'.$order->id;
        $rquotation = $this->quotation;
        $airport_shuttles = AirportShuttle::where('order_id', $order->id)->get();
        return $this->subject('New Reservation '.$order->orderno)
        ->view('emails.newBooking',[
            'rquotation'=>$rquotation,
            'now'=>$now,
            'order'=>$order,
            'agent'=>$agent,
            'order_link'=>$order_link,
            'airport_shuttles'=>$airport_shuttles,
         ]);
    }
}
