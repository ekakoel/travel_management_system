<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Brides;
use App\Models\Hotels;
use App\Models\Orders;
use App\Models\Weddings;
use App\Models\HotelRoom;
use App\Models\Transports;
use App\Models\InvoiceAdmin;
use App\Models\OrderWedding;
use Illuminate\Http\Request;
use App\Models\VendorPackage;
use App\Http\Livewire\Wedding;
use App\Mail\ConfirmationMail;
use App\Models\AirportShuttle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\WeddingDinnerPackages;

class MailController extends Controller
{
    public function index(){
        // Mail::to('reservationbalikami@gmail.com','admin-1@balikamitour.com','yuli@balikamitour.com','pasek@balikamitour.com')->send(new ReservationMail());
        // return "Email sudah terkirim.";
        Mail::to(config('app.reservation_mail'))
        ->send(new ConfirmationMail($id));
    }



    public function view_email_order_wedding(){
        $id = 67;
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
        return view('emails.newWeddingReservationMail',[
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

    public function view_email_booking(){
        $order = Orders::where('id',191)->first();
        $order_wedding = OrderWedding::where('id',$order->wedding_order_id)->first();
        $wedding = Weddings::where('id',$order_wedding->wedding_id)->first();
        $order_wedding_brides = Brides::where('id',$order_wedding->brides_id)->first();
        $wedding_services = VendorPackage::where('status','!=','Removed')->get();
        $wedding_room_services = HotelRoom::where('hotels_id',$order_wedding->hotel_id)->get();
        $wedding_transport_services = Transports::where('status','!=','Removed')->get();
        $agent = Auth::user()->where('id',$order->user_id)->first();
        $now = Carbon::now();
        $order_link = "/orders-admin-$order->id";
        $airport_shuttles = AirportShuttle::where('order_id', $order->id)->get();
        return view('emails.newBooking',[
            'order'=>$order,
            'order_wedding'=>$order_wedding,
            'wedding'=>$wedding,
            'order_wedding_brides'=>$order_wedding_brides,
            'wedding_services'=>$wedding_services,
            'agent'=>$agent,
            'now'=>$now,
            'order_link'=>$order_link,
            'airport_shuttles'=>$airport_shuttles,
            'wedding_room_services'=>$wedding_room_services,
            'wedding_transport_services'=>$wedding_transport_services,
        ]);
    }

    public function view_confirm_email(){
    $now = Carbon::now();
    $orderWedding = OrderWedding::where('id',67)->first();
    $agent = User::where('id', $orderWedding->user_id)->first();
    $order_link = 'https://online.balikamitour.com/detail-order-wedding-'.$orderWedding->orderno;
    $admin = Auth::user()->where('id',$orderWedding->verified_by)->first();
    $invs = InvoiceAdmin::where('rsv_id',$orderWedding->rsv_id)->first();
    $invoice = public_path('storage/document/invoice-'.$orderWedding->id.'.pdf');
    $airport_shuttles = AirportShuttle::where('order_id', $orderWedding->id)->get();
    $bride = Brides::where('id',$orderWedding->brides_id)->first();
    return view('emails.weddingConfirmationOrder',[
        'now'=>$now,
        'agent'=>$agent,
        'admin'=>$admin,
        'order_link'=>$order_link,
        'invs'=>$invs,
        'invoice'=>$invoice,
        'airport_shuttles'=>$airport_shuttles,
        'orderWedding'=>$orderWedding,
        'bride'=>$bride,
        ]);
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