<?php

namespace App\Mail;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Brides;
use App\Models\Hotels;
use App\Models\OrderWedding;
use App\Models\VendorPackage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use App\Models\WeddingDinnerPackages;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class weddingReservationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $id;
    
    public function __construct($id)
    {
       $this->id = $id;
    }

    
    public function build()
    {
        $id = $this->id;
        $now = Carbon::now();
        $orderWedding = OrderWedding::find($id);
        $agent = Auth::user();
        $order_link = 'https://online.balikamitour.com/orders-admin-'.$orderWedding->id;

        $brides = Brides::where('id',$orderWedding->brides_id)->first();
        $vendor = Hotels::where('id',$orderWedding->hotel_id)->first();
        $cv_decoration = VendorPackage::where('id',$orderWedding->ceremony_venue_decoration_id)->first();
        $rv_decoration = VendorPackage::where('id',$orderWedding->reception_venue_decoration_id)->first();
        $receptionVenue = WeddingDinnerPackages::where('id',$orderWedding->reception_venue_id)->first();
        $additionalServices = VendorPackage::where('type','Other')->get();
        $additional_service_ids = json_decode($orderWedding->additional_services);

        return $this->subject('New Wedding Reservation '.$orderWedding->orderno)
        ->view('emails.newWeddingReservationMail',[
            'now'=>$now,
            'orderWedding'=>$orderWedding,
            'agent'=>$agent,
            'order_link'=>$order_link,
            'brides'=>$brides,
            'vendor'=>$vendor,
            'cv_decoration'=>$cv_decoration,
            'rv_decoration'=>$rv_decoration,
            'receptionVenue'=>$receptionVenue,
            'additionalServices'=>$additionalServices,
            'additional_service_ids'=>$additional_service_ids,
         ]);
    }
}
