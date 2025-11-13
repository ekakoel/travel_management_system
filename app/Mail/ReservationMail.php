<?php

namespace App\Mail;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Brides;
use App\Models\Orders;
use App\Models\Weddings;
use App\Models\HotelRoom;
use App\Models\Transports;
use App\Models\OrderWedding;
use App\Models\VendorPackage;
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
        if ($order->service == "Wedding Package") {
            $order_wedding = OrderWedding::where('id', $order->wedding_order_id)->first();
            $wedding = Weddings::where('id',$order_wedding->wedding_id)->first();
            $order_wedding_brides = Brides::where('id',$order_wedding->brides_id)->first();
            $wedding_services = VendorPackage::where('status','!=','Removed')->get();
            $wedding_room_services = HotelRoom::where('hotels_id',$order_wedding->hotel_id)->get();
            $wedding_transport_services = Transports::where('status','!=','Removed')->get();
        }else{
            $order_wedding = "";
            $wedding = "";
            $order_wedding_brides = "";
            $wedding_services ="";
            $wedding_room_services = "";
            $wedding_transport_services = "";
        }
        $agent = User::where('id', $order->user_id)->first();
        $order_link = 'https://online.balikamitour.com/orders-admin-'.$order->id;
        $rquotation = $this->quotation;
        $airport_shuttles = AirportShuttle::where('order_id', $order->id)->get();
        return $this->subject('New Reservation '.$order->orderno)
        ->view('emails.newBooking',[
            'rquotation'=>$rquotation,
            'now'=>$now,
            'order'=>$order,
            'order_wedding'=>$order_wedding,
            'wedding'=>$wedding,
            'order_wedding_brides'=>$order_wedding_brides,
            'wedding_services'=>$wedding_services,
            'agent'=>$agent,
            'order_link'=>$order_link,
            'airport_shuttles'=>$airport_shuttles,
            'wedding_room_services'=>$wedding_room_services,
            'wedding_transport_services'=>$wedding_transport_services,
         ]);
    }
}
