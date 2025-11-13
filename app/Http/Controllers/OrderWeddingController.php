<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Brides;
use App\Models\Guests;
use App\Models\Hotels;
use App\Models\Orders;
use App\Models\Flights;
use App\Models\ExtraBed;
use App\Models\UsdRates;
use App\Models\Weddings;
use App\Models\Attention;
use App\Models\Countries;
use App\Models\HotelRoom;
use App\Models\HotelPrice;
use App\Models\Transports;
use App\Models\Reservation;
use Illuminate\Support\Str;
use App\Models\InvoiceAdmin;
use App\Models\OrderWedding;
use Illuminate\Http\Request;
use App\Models\ExtraBedOrder;
use App\Models\VendorPackage;
use App\Models\WeddingVenues;
use App\Models\TransportPrice;
use App\Models\WeddingPlanner;
use App\Models\BusinessProfile;
use App\Models\AdditionalService;
use App\Models\WeddingInvitations;
use App\Models\WeddingLunchVenues;
use App\Models\PaymentConfirmation;
use App\Models\WeddingDinnerVenues;
use App\Mail\weddingReservationMail;
use App\Models\WeddingAccomodations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Models\WeddingDinnerPackages;
use Intervention\Image\Facades\Image;
use App\Models\WeddingReceptionVenues;
use App\Models\WeddingPlannerTransport;
use Illuminate\Support\Facades\Storage;
use Google\Service\Dfareporting\Country;
use App\Models\WeddingAdditionalServices;
use App\Http\Requests\StoreOrderWeddingRequest;
use App\Http\Requests\UpdateOrderWeddingRequest;

class OrderWeddingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function store_invoice_pdf(Request $request,$id)
    {
        $orderWedding = OrderWedding::find($id);
        $agent = Auth::user()->where('id',$orderWedding->agent_id)->first();
        $admin = Auth::user()->where('id',$orderWedding->handled_by)->first();
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:2048', // Max 2MB
        ]);
        $reservation = Reservation::where('id',$orderWedding->rsv_id)->first();
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        
        $filePath = 'document/invoices/';
        $fileName = $orderWedding->orderno.'.pdf';
        // Simpan file ke storage/app/public/pdfs dengan nama yang diatur
        $path = $request->file('pdf_file')->storeAs($filePath, $fileName, 'public');
        $bride = Brides::where('id',$orderWedding->brides_id)->first();
        $content = $request->email_content;
        $data = [
            "title"=>"Confirmation Order - ".$orderWedding->orderno,
            "agent_email"=>$agent->email,
            "orderWedding"=>$orderWedding,
            "admin"=>$admin,
            "content"=>$content,
        ];
        if (config('filesystems.default') == 'public'){
            $invoice_path =realpath("storage/document/invoices/".$orderWedding->orderno.".pdf");
        }else {
            $invoice_path = storage::url("storage/document/invoices/".$orderWedding->orderno."_en.pdf");
        }
        Mail::send('emails.weddingSendInvoice', $data, function($message)use($data, $invoice_path) {
            $message->to($data["agent_email"])
                ->subject($data["title"])
                ->attach($invoice_path);
        });
        $reservation->update([
            "status"=>"Active",
            "send"=>"yes",
        ]);
        return back()->with('success', 'Email successfully sent.')->with('file', $path);
    }
    
    // VIEW ORDER WEDDING DETAIL =========================================================================================>
    public function detail_order_wedding($orderno)
    {
        $agent = Auth::user();
        $orderWedding = OrderWedding::where('orderno',$orderno)->first();
        if ($orderWedding) {
            $bride = Brides::where('id',$orderWedding->brides_id)->first();
            if ($orderWedding->agent_id == $agent->id) {
                $now = Carbon::now();
                $attentions = Attention::where('page','weddings-admin')->get();
                $usdrates = UsdRates::where('name','USD')->first();
                $business = BusinessProfile::where('id','=',1)->first();
                $hotel = Hotels::where('id',$orderWedding->hotel_id)->first();
                $rooms = $hotel->rooms;
                $bride = Brides::where('id',$orderWedding->brides_id)->first();
                $ceremonyVenue = WeddingVenues::where('id',$orderWedding->ceremony_venue_id)->first();
                if ($ceremonyVenue) {
                    $slots = json_decode($ceremonyVenue->slot);
                }else{
                    $slots = "[09:00]";
                }
                $suites_and_villas_inv = WeddingAccomodations::where('hotels_id',$hotel->id)->where('order_wedding_package_id', $orderWedding->id)->get();
                $weddingDinners = WeddingDinnerVenues::where('hotel_id',$hotel->id)->get();
                $ceremonyVenues = WeddingVenues::where('hotels_id',$hotel->id)->get();
                $maxCapacity = WeddingVenues::where('hotels_id',$hotel->id)->orderBy('capacity','desc')->first();
                $vendor_packages = VendorPackage::where('status','Active')->get();
                $ceremonyVenueDecoration = VendorPackage::where('id',$orderWedding->ceremony_venue_decoration_id)->first();
                $decorationReceptionVenue = VendorPackage::where('id',$orderWedding->reception_venue_decoration_id)->first();
                $receptionVenues = WeddingReceptionVenues::where('hotel_id',$hotel->id)->get();
                $receptionVenue = WeddingReceptionVenues::where('id',$orderWedding->reception_venue_id)->first();
                $weddingPackage = Weddings::where('id',$orderWedding->service_id)->first();
                $lunchVenue = WeddingLunchVenues::where('id',$orderWedding->lunch_venue_id)->first();
                $suiteAndVilla = HotelRoom::where('id',$orderWedding->room_bride_id)->first();
                $suites_and_villas = HotelRoom::where('hotels_id',$orderWedding->room_bride_id)->first();
                $dinnerVenue = WeddingDinnerVenues::where('id',$orderWedding->dinner_venue_id)->first();
                $transports = Transports::all();
                $transports_orders = WeddingPlannerTransport::where('order_wedding_id',$orderWedding->id)->get();
                
                $transportContainsNullPrice = $transports_orders->contains('price',0);
                $bride_transports_id = $orderWedding->transport_id;
                $bride_transports_type = "Airport Shuttle";
                $wedding_accommodations = WeddingAccomodations::where('order_wedding_package_id',$orderWedding->id)
                    ->where('room_for','Inv')->get();
                $accommodation_containt_zero = $wedding_accommodations->contains('public_rate',0);
                $wedding_accommodation_price = $wedding_accommodations->pluck('public_rate')->sum();
                $bride_accommodation = HotelRoom::where('id',$orderWedding->room_bride_id)->first();
                $flights = $orderWedding->flights;
                $additional_charges = WeddingAdditionalServices::where('order_wedding_id',$orderWedding->id)->where('status','!=','Rejected')->get();
                $additional_charge_price = $additional_charges->pluck('price')->sum();
                $additionalChargeContainZero = $additional_charges->contains('price',0);
                $additional_services = AdditionalService::where('order_wedding_id',$orderWedding->id)->get();
                $additional_service_price = $additional_services->pluck('price')->sum();
                $invitations = WeddingInvitations::where('order_wedding_id',$orderWedding->id)->get();
                $reservation = Reservation::where('id',$orderWedding->rsv_id)->first();
                
                if ($reservation) {
                    $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
                }else{
                    $invoice = NULL;

                }
                if ($invoice) {
                    $receipts = PaymentConfirmation::where('inv_id',$invoice->id)->get();
                }else{
                    $receipts = null;
                }
                if ($receptionVenue) {
                    $weddingDinner = WeddingDinnerVenues::where('id',$receptionVenue->dinner_venues_id)->first();
                }else{
                    $weddingDinner = null;
                }
                if ($orderWedding->status == "Draft") {
                    return redirect("/edit-order-wedding-$orderno")->with('warning','Order not found!');
                }else{
                    return view('order.detail-order-wedding',[
                        "business"=>$business,
                        "orderWedding"=>$orderWedding,
                        "hotel"=>$hotel,
                        "ceremonyVenue"=>$ceremonyVenue,
                        "ceremonyVenues"=>$ceremonyVenues,
                        "bride"=>$bride,
                        "attentions"=>$attentions,
                        "now"=>$now,
                        "usdrates"=>$usdrates,
                        "slots"=>$slots,
                        "maxCapacity"=>$maxCapacity,
                        "vendor_packages"=>$vendor_packages,
                        "ceremonyVenueDecoration"=>$ceremonyVenueDecoration,
                        "receptionVenue"=>$receptionVenue,
                        "decorationReceptionVenue"=>$decorationReceptionVenue,
                        "receptionVenues"=>$receptionVenues,
                        "weddingDinners"=>$weddingDinners,
                        "weddingDinner"=>$weddingDinner,
                        "weddingPackage"=>$weddingPackage,
                        "lunchVenue"=>$lunchVenue,
                        "dinnerVenue"=>$dinnerVenue,
                        "suiteAndVilla"=>$suiteAndVilla,
                        "transports"=>$transports,
                        "bride_transports_id"=>$bride_transports_id,
                        "bride_transports_type"=>$bride_transports_type,
                        "suites_and_villas_inv"=>$suites_and_villas_inv,
                        "transports_orders"=>$transports_orders,
                        "wedding_accommodations"=>$wedding_accommodations,
                        "bride_accommodation"=>$bride_accommodation,
                        "rooms"=>$rooms,
                        "flights"=>$flights,
                        "additional_charges"=>$additional_charges,
                        "additional_charge_price"=>$additional_charge_price,
                        "additionalChargeContainZero"=>$additionalChargeContainZero,
                        "additional_services"=>$additional_services,
                        "additional_service_price"=>$additional_service_price,
                        "accommodation_containt_zero"=>$accommodation_containt_zero,
                        "wedding_accommodation_price"=>$wedding_accommodation_price,
                        "transportContainsNullPrice"=>$transportContainsNullPrice,
                        "invitations"=>$invitations,
                        "reservation"=>$reservation,
                        "invoice"=>$invoice,
                        "receipts"=>$receipts,
                    ]);
                }
            }else{
                return redirect("/orders")->with('warning','Order not found!');
            }
        }else{
            return redirect("/orders")->with('warning','Order not found!');
        }
    }
    // VIEW ORDER WEDDING DETAIL =========================================================================================>
    public function edit_order_wedding($orderno)
    {
        $agent = Auth::user();
        $orderWedding = OrderWedding::where('orderno',$orderno)->first();
        $countries = Countries::all();
        if ($orderWedding) {
            if ($orderWedding->status == "Draft") {
                if ($orderWedding->agent_id == $agent->id) {
                    $wedding = Weddings::where('id',$orderWedding->service_id)->first();
                    $orderWedding->bride_transports_id ? $bride_transport_id = json_decode($orderWedding->bride_transports_id): $bride_transport_id = null;
                    $orderWedding->bride_transports_type ? $bride_transport_type = json_decode($orderWedding->bride_transports_type): $bride_transport_type = null;
                    $bride_transport_price = $orderWedding->transport_price;
                    $orderWedding->bride_transports_date ? $bride_transport_date = json_decode($orderWedding->bride_transports_date): $bride_transport_date = null;
                    $now = Carbon::now();
                    $attentions = Attention::where('page','edit-order-wedding')->get();
                    $usdrates = UsdRates::where('name','USD')->first();
                    $business = BusinessProfile::where('id','=',1)->first();
                    $hotel = Hotels::where('id',$orderWedding->hotel_id)->first();
                    $rooms = HotelRoom::where('hotels_id',$hotel->id)->get();
                    $bride = Brides::where('id',$orderWedding->brides_id)->first();
                    $ceremonyVenue = WeddingVenues::where('id',$orderWedding->ceremony_venue_id)->first();
                    if ($ceremonyVenue) {
                        $slots = json_decode($ceremonyVenue->slot);
                    }else{
                        $slots = "[09:00]";
                    }
                    $weddingDinners = WeddingDinnerVenues::where('hotel_id',$hotel->id)->get();
                    if ($wedding) {
                        $weddingDinner = WeddingDinnerVenues::where('id',$wedding->dinner_venue_id)->first();
                        $dinnerVenue = WeddingDinnerVenues::where('id',$wedding->dinner_venue_id)->first();
                        $dinnerVenues = WeddingDinnerVenues::where('hotel_id',$wedding->hotel_id)->get();
                        $lunchVenues = WeddingLunchVenues::where('hotel_id',$wedding->hotel_id)->get();
                        $lunchVenue = WeddingLunchVenues::where('id',$wedding->lunch_venue_id)->first();
                    }else{
                        $weddingDinner = NULL;
                        $dinnerVenue = NULL;
                        $dinnerVenues = NULL;
                        $lunchVenues = NULL;
                        $lunchVenue = NULL;
                    }
                    $ceremonyVenues = WeddingVenues::where('hotels_id',$hotel->id)->get();
                    $maxCapacityCeremonyVenue = WeddingVenues::where('hotels_id',$hotel->id)->orderBy('capacity','desc')->first();
                    $maxCapacityReceptionVenue = WeddingDinnerPackages::where('hotels_id',$hotel->id)->orderBy('number_of_guests','desc')->first();
                    $vendor_packages = VendorPackage::where('status','Active')->get();
                    $ceremonyVenueDecoration = VendorPackage::where('id',$orderWedding->ceremony_venue_decoration_id)->first();
                    $decorationReceptionVenue = VendorPackage::where('id',$orderWedding->reception_venue_decoration_id)->first();
                    $receptionVenues = WeddingReceptionVenues::where('hotel_id',$hotel->id)->get();
                    $receptionVenue = WeddingReceptionVenues::where('id',$orderWedding->reception_venue_id)->first();
                    $receptionPackage = $orderWedding->reception_venue;

                    if ($orderWedding->suite_villa_invitations) {
                        $suiteAndVillaInvitations = $orderWedding->suite_villa_invitations;
                    }else {
                        $suiteAndVillaInvitations = null;
                    }

                    $wedding_accommodations = WeddingAccomodations::where('order_wedding_package_id',$orderWedding->id)
                    ->where('room_for','Inv')->get();
                    $extra_beds = ExtraBed::where('hotels_id',$hotel->id)->get();
                    $extra_bed_orders = ExtraBedOrder::where('order_wedding_id',$orderWedding->id)->get();
                    $extra_bed_orders_price = $extra_bed_orders->pluck('total_price')->sum();

                    $accommodation_containt_zero = $wedding_accommodations->contains('public_rate',0);
                    $wedding_accommodation_price = $wedding_accommodations->pluck('public_rate')->sum();
                    $bride_accommodation = HotelRoom::where('id',$orderWedding->room_bride_id)->first();
                    $flights = $orderWedding->flights;
                    $total_invitations = $wedding_accommodations->pluck('number_of_guests')->sum();
                    $transports = Transports::where('status','Active')->get();
                    $transport_orders = WeddingPlannerTransport::where('order_wedding_id',$orderWedding->id)->get();
                    $transport_orders_prices = $transport_orders->pluck('price')->sum();
                    $transportContainsNullPrice = $transport_orders->contains('price',0);
                    $brides_transport = Transports::where('id',$orderWedding->transport_id)->first();
                    $brides_transport_id = $orderWedding->transport_id;
                    $brides_transport_type = $orderWedding->transport_type;
                    $additionalServices = WeddingAdditionalServices::where('order_wedding_id',$orderWedding->id)->where('status','!=','Rejected')->get();
                    $additionalServicesPrices = $additionalServices->sum('price');
                    $additionalServicesPricesTba = $additionalServices->contains('price',0);
                    $guests = WeddingInvitations::where('order_wedding_id',$orderWedding->id)->get();
                    return view('order.edit-order-wedding',[
                        "business"=>$business,
                        "orderWedding"=>$orderWedding,
                        "hotel"=>$hotel,
                        "ceremonyVenue"=>$ceremonyVenue,
                        "ceremonyVenues"=>$ceremonyVenues,
                        "bride"=>$bride,
                        "attentions"=>$attentions,
                        "now"=>$now,
                        "usdrates"=>$usdrates,
                        "slots"=>$slots,
                        "maxCapacityCeremonyVenue"=>$maxCapacityCeremonyVenue,
                        "maxCapacityReceptionVenue"=>$maxCapacityReceptionVenue,
                        "vendor_packages"=>$vendor_packages,
                        "ceremonyVenueDecoration"=>$ceremonyVenueDecoration,
                        "receptionVenue"=>$receptionVenue,
                        "receptionVenues"=>$receptionVenues,
                        "decorationReceptionVenue"=>$decorationReceptionVenue,
                        "weddingDinners"=>$weddingDinners,
                        "weddingDinner"=>$weddingDinner,
                        "dinnerVenue"=>$dinnerVenue,
                        "dinnerVenues"=>$dinnerVenues,
                        "lunchVenues"=>$lunchVenues,
                        "lunchVenue"=>$lunchVenue,
                        "wedding"=>$wedding,
                        "guests"=>$guests,
                        
                        "receptionPackage"=>$receptionPackage,
                        "rooms"=>$rooms,
                        "suiteAndVillaInvitations"=>$suiteAndVillaInvitations,
                        "flights"=>$flights,
                        "wedding_accommodations"=>$wedding_accommodations,
                        "wedding_accommodation_price"=>$wedding_accommodation_price,
                        "bride_accommodation"=>$bride_accommodation,
                        "extra_beds"=>$extra_beds,
                        "extra_bed_orders"=>$extra_bed_orders,
                        "extra_bed_orders_price"=>$extra_bed_orders_price,
                        "total_invitations"=>$total_invitations,
                        "transports"=>$transports,
                        "transport_orders"=>$transport_orders,
                        "bride_transport_date"=>$bride_transport_date,
                        "bride_transport_id"=>$bride_transport_id,
                        "bride_transport_type"=>$bride_transport_type,
                        "bride_transport_price"=>$bride_transport_price,
                        "transportContainsNullPrice"=>$transportContainsNullPrice,
                        "transport_orders_prices"=>$transport_orders_prices,
                        "additionalServices"=>$additionalServices,
                        "additionalServicesPrices"=>$additionalServicesPrices,
                        "additionalServicesPricesTba"=>$additionalServicesPricesTba,
                        "accommodation_containt_zero"=>$accommodation_containt_zero,
                        "countries"=>$countries,
                        "brides_transport"=>$brides_transport,
                        "brides_transport_id"=>$brides_transport_id,
                        "brides_transport_type"=>$brides_transport_type,
                    ]);
                }else{
                    return redirect('/orders')->with('warning','Order not found!');
                }
            }else{
                return redirect("/detail-order-wedding-$orderno")->with('warning','Order not found!');
            }
        }else{
            return redirect("/orders")->with('warning','Order not found!');
        }
    }

    // FUNCTION UPDATE WEDDING ORDER BRIDE
    public function func_update_wedding_order_brides(Request $request,$id){
        $brides = Brides::find($id);
        $brides->update([
            'groom'=>$request->groom,
            'groom_chinese'=>$request->groom_chinese,
            'groom_contact'=>$request->groom_contact,
            'groom_pasport_id'=>$request->groom_pasport_id,
            'bride'=>$request->bride,
            'bride_chinese'=>$request->bride_chinese,
            'bride_contact'=>$request->bride_contact,
            'bride_pasport_id'=>$request->bride_pasport_id,
        ]);
        return redirect()->back()->with('success','Bride detail has been updated!');
    }

    // FUNCTION UPDATE WEDDING ORDER FLIGHT
    public function func_update_order_wedding_flight(Request $request,$id){
        $flight = Flights::find($id);
        $flight->update([
            'type'=>$request->type,
            'group'=>$request->flight_group,
            'flight'=>$request->flight,
            'time'=>$request->time,
            'guests'=>$request->guests,
            'guests_contact'=>$request->guests_contact,
            'number_of_guests'=>$request->number_of_guests,
        ]);
        return redirect()->back()->with('success','Flight schedule has been updated!');
    }

    // FUNCTION ADD ORDER WEDDING INVITATION --------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_add_invitation_to_order_wedding(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        if($request->hasFile("cover")){
            $file=$request->file("cover");
            $coverName=time()."_".$file->getClientOriginalName();
            $img = Image::make($file->getRealPath());
            $img->resize(800, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $img->save(public_path('storage/guests/id_passport/' . $coverName));
            $request['cover']=$coverName;
        }else{
            $coverName = NULL;
        }
        $wedding_invitations = new WeddingInvitations ([
            'order_wedding_id' =>$id,
            'sex' =>'o',
            'name' =>$request->name,
            'chinese_name' =>$request->name_mandarin,
            'country' =>$request->country,
            'passport_no' =>$request->identification_no,
            'passport_img' =>$coverName,
            'phone' =>$request->phone,
        ]);
        $wedding_invitations->save();
        return redirect("/edit-order-wedding-$orderWedding->orderno#weddingInvitations")->with('success','New invitation has been added!');
    }
    // FUNCTION UPDATE ORDER WEDDING INVITATION --------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_update_invitation_to_order_wedding(Request $request,$id){
        $invitation = WeddingInvitations::find($id);
        $orderWedding = OrderWedding::where('id', $invitation->order_wedding_id)->first();

        $coverName = $invitation->passport_img; 

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $coverName = time() . '.' . $file->getClientOriginalExtension();

            $img = Image::make($file->getRealPath());
            $img->resize(800, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $img->save(storage_path('app/public/guests/id_passport/' . $coverName));

            if ($invitation->passport_img && $invitation->passport_img != 'default.jpg') {
                Storage::delete('public/guests/id_passport/' . $invitation->passport_img);
            }
        }
        $invitation->update([
            'name' => $request->name,
            'chinese_name' => $request->chinese_name,
            'country' => $request->country,
            'passport_no' => $request->identification_no,
            'passport_img' => $coverName,
            'phone' => $request->phone,
        ]);

        return redirect("/edit-order-wedding-$orderWedding->orderno#weddingInvitations")
            ->with('success', 'New invitation has been added!');
    }
    // FUNCTION UPDATE WEDDING ORDER FLIGHT (ADMIN)
    public function func_update_order_wedding_flight_admin(Request $request,$id){
        $flight = Flights::find($id);
        $guest = Guests::where('id',$flight->guests_id)->first();
        $date = date('Y-m-d H:i',strtotime($request->time));
        $flight->update([
            'type'=>$request->type,
            'group'=>$request->group,
            'flight'=>$request->flight,
            'time'=>$date,
            'guests'=>$request->guests,
            'guests_contact'=>$request->guests_contact,
            'number_of_guests'=>$request->number_of_guests,
        ]);
        return redirect()->back()->with('success','Flight schedule has been updated!');
    }
    // FUNCTION UPDATE WEDDING ORDER WEDDING
    public function func_update_wedding_order_wedding(Request $request,$id){
        $tax = Tax::where('id',1)->first();
        $usdrates = UsdRates::where('name','USD')->first();
        $orderWedding = OrderWedding::find($id);
        $weddingPackage = Weddings::where('id',$orderWedding->service_id)->first();

        if ($orderWedding->service == "Wedding Package") {
            $ord_wedding_date = date('Y-m-d',strtotime($orderWedding->wedding_date));
            $wedding_date = date('Y-m-d',strtotime($request->wedding_date));
            $duration = $orderWedding->duration;
            if ($ord_wedding_date != $wedding_date) { // jika wedding date dirubah
                $reception_date_start = date('Y-m-d',strtotime($wedding_date))." 18:00";
            }else{
                $reception_date_start = $orderWedding->reception_date_start;
            }
            // jika slot dirubah
            if ($orderWedding->slot != $request->slot) {
                $ceremonyVenue = WeddingVenues::where('id', $orderWedding->ceremony_venue_id)->first();
                $slot = $request->slot;
                if ($ceremonyVenue) {
                    $ceremony_venue_slot = json_decode($ceremonyVenue->slot);
                    $ceremony_venue_basic_price = json_decode($ceremonyVenue->basic_price);
                    $ceremony_venue_arrangement_price = json_decode($ceremonyVenue->arrangement_price);
                    if ($ceremony_venue_slot) {
                        $count_ceremony_venue_slot = count($ceremony_venue_slot);
                        for ($i=0; $i < $count_ceremony_venue_slot ; $i++) { 
                            $cvs = date('H:i',strtotime($ceremony_venue_slot[$i]));
                            $r_slot = date('H:i',strtotime($request->slot));
        
                            if ($cvs == $r_slot) {
                                if ($orderWedding->ceremony_venue_decoration_id) {
                                    $ceremony_venue_price = $ceremony_venue_arrangement_price[$i];
                                }else{
                                    $ceremony_venue_price = $ceremony_venue_basic_price[$i];
                                }
                            }
                        }
                    }
                }
            }else{
                $ceremony_venue_price = $orderWedding->ceremony_venue_price;
                $slot = $orderWedding->slot;
            }
            if ($orderWedding->lunch_venue_id) {
                $lunch_venue_date = date('Y-m-d',strtotime($wedding_date))." 13:00";
            }else{
                $lunch_venue_date = NULL;
            }
            if ($orderWedding->dinner_venue_id) {
                $dinner_venue_date = date('Y-m-d',strtotime($wedding_date))." 18:00";
            }else{
                $dinner_venue_date = NULL;
            }
            $lunch_venue_date = date('Y-m-d',strtotime($wedding_date))." 13:00";
            $dinner_venue_date = date('Y-m-d',strtotime($wedding_date))." 18:00";
        }elseif($orderWedding->service == "Ceremony Venue"){
            $ord_wedding_date = date('Y-m-d',strtotime($orderWedding->wedding_date));
            $wedding_date = date('Y-m-d',strtotime($request->wedding_date));
            $duration = 2;
            if ($orderWedding->reception_venue_id) {
                $reception_date_start = date('Y-m-d',strtotime($request->reception_date_start)).date(' H:i',strtotime($request->reception_venue_slot));
            }else{
                $reception_date_start = NULL;
            }
            // jika slot dirubah
            if ($orderWedding->slot != $request->slot) {
                $ceremonyVenue = WeddingVenues::where('id', $orderWedding->ceremony_venue_id)->first();
                $slot = $request->slot;
                if ($ceremonyVenue) {
                    $ceremony_venue_slot = json_decode($ceremonyVenue->slot);
                    $ceremony_venue_basic_price = json_decode($ceremonyVenue->basic_price);
                    $ceremony_venue_arrangement_price = json_decode($ceremonyVenue->arrangement_price);
                    if ($ceremony_venue_slot) {
                        $count_ceremony_venue_slot = count($ceremony_venue_slot);
                        for ($i=0; $i < $count_ceremony_venue_slot ; $i++) { 
                            $cvs = date('H:i',strtotime($ceremony_venue_slot[$i]));
                            $r_slot = date('H:i',strtotime($request->slot));
        
                            if ($cvs == $r_slot) {
                                if ($orderWedding->ceremony_venue_decoration_id) {
                                    $ceremony_venue_price = $ceremony_venue_arrangement_price[$i];
                                }else{
                                    $ceremony_venue_price = $ceremony_venue_basic_price[$i];
                                }
                            }
                        }
                    }
                }
            }else{
                $ceremony_venue_price = $orderWedding->ceremony_venue_price;
                $slot = $orderWedding->slot;
            }
            $lunch_venue_date = NULL;
            $dinner_venue_date = NULL;
        }elseif($orderWedding->service == "Reception Venue"){
            $duration = 4;
            $reception_date_start = date('Y-m-d',strtotime($request->reception_date_start)).date(' H:i',strtotime($request->reception_venue_slot));
            // jika slot dirubah
            if ($orderWedding->ceremony_venue_id) {
                $wedding_date = date("Y-m-d",strtotime($request->wedding_date));
                if ($orderWedding->slot != $request->slot) {
                    $ceremonyVenue = WeddingVenues::where('id', $orderWedding->ceremony_venue_id)->first();
                    $slot = $request->slot;
                    if ($ceremonyVenue) {
                        $ceremony_venue_slot = json_decode($ceremonyVenue->slot);
                        $ceremony_venue_basic_price = json_decode($ceremonyVenue->basic_price);
                        $ceremony_venue_arrangement_price = json_decode($ceremonyVenue->arrangement_price);
                        if ($ceremony_venue_slot) {
                            $count_ceremony_venue_slot = count($ceremony_venue_slot);
                            for ($i=0; $i < $count_ceremony_venue_slot ; $i++) { 
                                $cvs = date('H:i',strtotime($ceremony_venue_slot[$i]));
                                $r_slot = date('H:i',strtotime($request->slot));
            
                                if ($cvs == $r_slot) {
                                    if ($orderWedding->ceremony_venue_decoration_id) {
                                        $ceremony_venue_price = $ceremony_venue_arrangement_price[$i];
                                    }else{
                                        $ceremony_venue_price = $ceremony_venue_basic_price[$i];
                                    }
                                }
                            }
                        }
                    }
                }else{
                    $ceremony_venue_price = $orderWedding->ceremony_venue_price;
                    $slot = $orderWedding->slot;
                }
            }else{
                $wedding_date = NULL;
                $ceremony_venue_price = NULL;
                $slot = NULL;
            }
            $lunch_venue_date = NULL;
            $dinner_venue_date = NULL;
        }

        $checkin = date('Y-m-d',strtotime('-1 days',strtotime($wedding_date)));
        $checkout = date('Y-m-d',strtotime('+'.$duration.' days',strtotime($checkin)));
        

        if ($wedding_date != date('Y-m-d',strtotime($orderWedding->wedding_date))) {
            $wedding_accommodations = $orderWedding->suite_villa_invitations;
            $bride_room_id = $orderWedding->room_bride_id;
            
            if ($bride_room_id) {
                $bhp = [];
                $bcin = $checkin;
                for ($bp=0; $bp < $orderWedding->duration; $bp++) {
                    $bride_hotel_price = HotelPrice::where('rooms_id',$bride_room_id)
                    ->where('start_date','<=',$bcin)
                    ->where('end_date','>=',$bcin)
                    ->first();
                    if ($bride_hotel_price) {
                        $usd_rate = $usdrates->rate;
                        $bcr = $bride_hotel_price->contract_rate;
                        $bmrk = $bride_hotel_price->markup;
                        $bcr_usd = ceil($bcr / $usd_rate);
                        $btx = ceil((($bcr_usd + $bmrk) * $tax->tax)/100);
                        $bpr = $bcr_usd + $bmrk + $btx;
                        array_push($bhp,$bpr);
                    }else{
                        $bpr = 0;
                        array_push($bhp,$bpr);
                    }
                }
                $rbp = array_sum($bhp);
                $room_bride_price = $rbp;
            }else{
                $room_bride_price = null;
            }
            if ($wedding_accommodations) {
                foreach ($wedding_accommodations as $wedding_accommodation) {
                    $room_id = $wedding_accommodation->rooms_id;
                    
                    $hp = [];
                    $cin = $checkin;
                    for ($i=0; $i < $orderWedding->duration; $i++) { 
                        $cin = date('Y-m-d',strtotime('+'.$i.' days',strtotime($cin)));
                        $hotel_price = HotelPrice::where('rooms_id',$room_id)
                        ->where('start_date','<=',$cin)
                        ->where('end_date','>=',$cin)
                        ->first();
                        if ($hotel_price) {
                            $rate_usd = $usdrates->rate;
                            $cr = $hotel_price->contract_rate;
                            $mrk = $hotel_price->markup;
                            $cr_usd = ceil($cr / $rate_usd);
                            $tx = ceil((($cr_usd + $mrk) * $tax->tax)/100);
                            $pr = $cr_usd + $mrk + $tx;
                            array_push($hp,$pr);
                        }else{
                            $pr = 0;
                            array_push($hp,$pr);
                        }
                        if ($bride_hotel_price) {
                            
                        }else{
                           
                        }
                    }
                    $pub_rate = array_sum($hp);
                    
                    $extra_bed = ExtraBed::where('id',$wedding_accommodation->extra_bed_id)->first();
                    if ($extra_bed) {
                        $tax_rate = ceil(((($extra_bed->contract_rate / $usdrates->rate) + $extra_bed->markup)* $tax->tax) / 100);
                        $exb_rate = ceil(($extra_bed->contract_rate / $usdrates->rate) + $extra_bed->markup);
                        $public_rate = $pub_rate + $exb_rate;
                    }else{
                        $public_rate = $pub_rate;
                    }
                    $wedding_accommodation->update([
                        'checkin'=>$checkin,
                        'checkout'=>$checkout,
                        'public_rate'=>$public_rate,
                    ]);
                }
                $accommodation_price = $wedding_accommodations->pluck('public_rate')->sum();
            }else{
                $accommodation_price = $orderWedding->accommodation_price;
            }
        }else{
            $accommodation_price = $orderWedding->accommodation_price;
        }
       
        // FINAL PRICE 
        if ($orderWedding->service == "Wedding Package") {
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $accommodation_price //proses
                + $orderWedding->extra_bed_price
                + $orderWedding->transport_invitations_price
                + $orderWedding->addser_price
                + $orderWedding->package_price
                + $orderWedding->markup;
        }else{
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $ceremony_venue_price //proses
                + $orderWedding->ceremony_venue_decoration_price 
                + $orderWedding->reception_venue_price 
                + $orderWedding->reception_venue_decoration_price
                + $orderWedding->dinner_venue_price
                + $orderWedding->lunch_venue_price
                + $orderWedding->room_bride_price 
                + $accommodation_price //proses
                + $orderWedding->extra_bed_price
                + $orderWedding->transport_price
                + $orderWedding->transport_invitations_price
                + $makeup_price
                + $documentation_price
                + $entertainment_price
                + $orderWedding->additional_services_price
                + $orderWedding->addser_price
                + $orderWedding->markup;
        }
        $orderWedding->update([
            'wedding_date'=>$wedding_date,
            'slot'=>$slot,
            'checkin'=>$checkin,
            'checkout'=>$checkout,
            'reception_date_start'=>$reception_date_start,
            'ceremony_venue_price'=>$ceremony_venue_price,
            'accommodation_price'=>$accommodation_price,
            'room_bride_price'=>$orderWedding->room_bride_price,
            'lunch_venue_date'=>$lunch_venue_date,
            'dinner_venue_date'=>$dinner_venue_date,
            'final_price'=>$final_price,
        ]);
        // dd($orderWedding);
        
        return redirect()->back()->with('success','Wedding detail has been updated!');
    }
    // FUNCTION ADD BRIDES FLIGHT
    public function func_order_wedding_bride_flight(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        $arrival = "Arrival";
        $departure = "Departure";
        $group = "Bride";
        $arrival_flight = $request->arrival_flight;
        $arrival_time = $request->arrival_time;
        $departure_flight = $request->departure_flight;
        $departure_time = $request->departure_time;
        $number_of_guests = 2;
        $order_wedding_id = $id;
        $status = "Active";
        if ($arrival_flight) {
            $bride_flight = new Flights([
                "type"=>$arrival,
                "group"=>$group,
                "flight"=>$arrival_flight,
                "time"=>$arrival_time,
                "number_of_guests"=>$number_of_guests,
                "order_wedding_id"=>$order_wedding_id,
                "status"=>$status,
            ]);
            $bride_flight->save();
        }
        if ($departure_flight) {
            $bride_flight = new Flights([
                "type"=>$departure,
                "group"=>$group,
                "flight"=>$departure_flight,
                "time"=>$departure_time,
                "number_of_guests"=>$number_of_guests,
                "order_wedding_id"=>$order_wedding_id,
                "status"=>$status,
            ]);
            $bride_flight->save();
        }
        return redirect("/edit-order-wedding-$orderWedding->orderno#brideFlight")->with('success', 'Flight scedule has been created successfully');
    }
    // FUNC ADD ORDER WEDDING FLIGHT
    public function func_add_order_wedding_flight(Request $request,$id){
        $status = "Active";
        $group = $request->flight_group;
        $order_wedding_id = $id;
        $type = $request->type;
        $time = $request->time;
        $guests = $request->guests;
        $guests_contact = $request->guests_contact;
        $flight = $request->flight;
        $contact = $request->contact;
        $number_of_guests = $request->number_of_guests;
        $sex = "o";
        $cflight = count($request->flight);
        for ($i=0; $i < $cflight; $i++) { 
            $time_formated = date('Y-m-d H.i',strtotime($time[$i]));
            $flight_data = new Flights([
                'type'=>$type[$i],
                'group'=>$group[$i],
                'flight'=>$flight[$i],
                'time'=>$time_formated,
                'guests'=>$guests[$i],
                'guests_contact'=>$guests_contact[$i],
                'number_of_guests'=>$number_of_guests[$i],
                'order_wedding_id'=>$order_wedding_id,
                'status'=>$status,
            ]);
            $flight_data->save();
        }
        return redirect()->back()->with('success','Flight has been add to the wedding planner');
    }
    // FUNCTION CREATE ORDER CEREMONY VENUE (ORDER CEREMONY VENUE) =========================================================================================>
    public function func_create_order_wedding_venue(Request $request,$id){
        $code = Str::random(26);
        // ORDERNO GENERATOR
            $tgl_sekarang = Carbon::now();
            $now = date("Y-m-d",strtotime($tgl_sekarang));
            if (Auth::user()->position == "weddingDvl" || Auth::user()->position == "weddingSls" || Auth::user()->position == "weddingAuthor" || Auth::user()->position == "weddingRsv") {
                $agent = Auth::user()->where('id',$request->user_id)->first();
            }else{
                $user_id = Auth::user()->id;
                $agent = Auth::user()->where('id',$user_id)->first();
            }
            $a = $agent->code.date('-ymd-',strtotime($now))."A";
            $b = $agent->code.date('-ymd-',strtotime($now))."B";
            $c = $agent->code.date('-ymd-',strtotime($now))."C";
            $d = $agent->code.date('-ymd-',strtotime($now))."D";
            $e = $agent->code.date('-ymd-',strtotime($now))."E";
            $f = $agent->code.date('-ymd-',strtotime($now))."F";
            $g = $agent->code.date('-ymd-',strtotime($now))."G";
            $h = $agent->code.date('-ymd-',strtotime($now))."H";
            $i = $agent->code.date('-ymd-',strtotime($now))."I";
            $j = $agent->code.date('-ymd-',strtotime($now))."J";
            $k = $agent->code.date('-ymd-',strtotime($now))."K";
            $l = $agent->code.date('-ymd-',strtotime($now))."L";
            $m = $agent->code.date('-ymd-',strtotime($now))."M";
            $n = $agent->code.date('-ymd-',strtotime($now))."N";
            $o = $agent->code.date('-ymd-',strtotime($now))."O";
            $p = $agent->code.date('-ymd-',strtotime($now))."P";
            $q = $agent->code.date('-ymd-',strtotime($now))."Q";
            $r = $agent->code.date('-ymd-',strtotime($now))."R";
            $s = $agent->code.date('-ymd-',strtotime($now))."S";
            $t = $agent->code.date('-ymd-',strtotime($now))."T";
            $u = $agent->code.date('-ymd-',strtotime($now))."U";
            $v = $agent->code.date('-ymd-',strtotime($now))."V";
            $w = $agent->code.date('-ymd-',strtotime($now))."W";
            $x = $agent->code.date('-ymd-',strtotime($now))."X";
            $y = $agent->code.date('-ymd-',strtotime($now))."Y";
            $z = $agent->code.date('-ymd-',strtotime($now))."Z";
            $orderw = OrderWedding::where('orderno', $a)
            ->orWhere('orderno', $b)
            ->orWhere('orderno', $c)
            ->orWhere('orderno', $d)
            ->orWhere('orderno', $e)
            ->orWhere('orderno', $f)
            ->orWhere('orderno', $g)
            ->orWhere('orderno', $h)
            ->orWhere('orderno', $i)
            ->orWhere('orderno', $j)
            ->orWhere('orderno', $k)
            ->orWhere('orderno', $l)
            ->orWhere('orderno', $m)
            ->orWhere('orderno', $n)
            ->orWhere('orderno', $o)
            ->orWhere('orderno', $p)
            ->orWhere('orderno', $q)
            ->orWhere('orderno', $r)
            ->orWhere('orderno', $s)
            ->orWhere('orderno', $t)
            ->orWhere('orderno', $u)
            ->orWhere('orderno', $v)
            ->orWhere('orderno', $w)
            ->orWhere('orderno', $x)
            ->orWhere('orderno', $y)
            ->orWhere('orderno', $z)
            ->get();
            $crsva = count($orderw);

            if ($crsva == 0) {
                $order_no = $a;
            }elseif($crsva == 1){
                $order_no = $b;
            }elseif($crsva == 2){
                $order_no = $c;
            }elseif($crsva == 3){
                $order_no = $d;
            }elseif($crsva == 4){
                $order_no = $e;
            }elseif($crsva == 5){
                $order_no = $f;
            }elseif($crsva == 6){
                $order_no = $g;
            }elseif($crsva == 7){
                $order_no = $h;
            }elseif($crsva == 8){
                $order_no = $i;
            }elseif($crsva == 9){
                $order_no = $j;
            }elseif($crsva == 10){
                $order_no = $k;
            }elseif($crsva == 11){
                $order_no = $l;
            }elseif($crsva == 12){
                $order_no = $m;
            }elseif($crsva == 13){
                $order_no = $n;
            }elseif($crsva == 14){
                $order_no = $o;
            }elseif($crsva == 15){
                $order_no = $p;
            }elseif($crsva == 16){
                $order_no = $q;
            }elseif($crsva == 17){
                $order_no = $r;
            }elseif($crsva == 18){
                $order_no = $s;
            }elseif($crsva == 19){
                $order_no = $t;
            }elseif($crsva == 20){
                $order_no = $u;
            }elseif($crsva == 21){
                $order_no = $v;
            }elseif($crsva == 22){
                $order_no = $w;
            }elseif($crsva == 23){
                $order_no = $x;
            }elseif($crsva == 24){
                $order_no = $y;
            }elseif($crsva == 25){
                $order_no = $z;
            }else{
                $order_no = $AA;
            }
        $orderno = $order_no;
        $service = "Ceremony Venue";
        $service_id = $id;
        $duration = 1;
        $ceremony_venue_duration = 2;
        $groom = $request->groom_name;
        $bride = $request->bride_name;
        $number_of_invitation = $request->number_of_invitations;
        $slot = $request->slot;
        $hotel_id = $request->hotel_id;
        $basic_price = $request->ceremony_price;
        $wedding_date = date('Y-m-d',strtotime($request->wedding_date));
        $checkin = $wedding_date;
        $checkout = $wedding_date;
        $status = "Draft";
        $groom_pasport_id = $request->groom_id;
        $bride_pasport_id = $request->brides_id;
        $brides =new Brides([
            "groom"=>$groom,
            "groom_pasport_id"=>$groom_pasport_id,
            "bride"=>$bride,
            "bride_pasport_id"=>$bride_pasport_id,
        ]);
        $brides->save();
        $order_wedding_venue =new OrderWedding([
            "orderno"=>$orderno,
            "service"=>$service,
            "service_id"=>$service_id,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "slot"=>$slot,
            "hotel_id"=>$hotel_id,
            "wedding_date"=>$wedding_date,
            "brides_id"=>$brides->id,
            "basic_or_arrangement"=>"Basic",
            "number_of_invitation"=>$number_of_invitation,
            "ceremony_venue_invitations"=>$number_of_invitation,
            "ceremony_venue_duration"=>$ceremony_venue_duration,
            "ceremony_venue_id"=>$service_id,
            "ceremony_venue_price"=>$basic_price,
            "final_price"=>$basic_price,
            "agent_id"=>Auth::user()->id,
            "status"=>$status,
        ]);
        // dd($order_wedding_venue);
        $order_wedding_venue->save();
        return redirect("/edit-order-wedding-$orderno")->with('success', 'Order Ceremony Venue has been created successfully');
    }
    // FUNCTION CREATE ORDER WEDDING PACKAGE (ORDER WEDDING PACKAGE) =========================================================================================>
    public function func_add_order_wedding_package(Request $request,$id){
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        // ORDERNO GENERATOR
            $tgl_sekarang = Carbon::now();
            $code = Str::random(26);
            $now = date("Y-m-d",strtotime($tgl_sekarang));
            if (Auth::user()->position == "weddingDvl" || Auth::user()->position == "weddingSls" || Auth::user()->position == "weddingAuthor" || Auth::user()->position == "weddingRsv"){
                $agent = Auth::user()->where('id',$request->agent_id)->first();
            }else{
                $user_id = Auth::user()->id;
                $agent = Auth::user()->where('id',$user_id)->first();
            }
            $a = $agent->code.date('ymd',strtotime($now))."A";
            $b = $agent->code.date('ymd',strtotime($now))."B";
            $c = $agent->code.date('ymd',strtotime($now))."C";
            $d = $agent->code.date('ymd',strtotime($now))."D";
            $e = $agent->code.date('ymd',strtotime($now))."E";
            $f = $agent->code.date('ymd',strtotime($now))."F";
            $g = $agent->code.date('ymd',strtotime($now))."G";
            $h = $agent->code.date('ymd',strtotime($now))."H";
            $i = $agent->code.date('ymd',strtotime($now))."I";
            $j = $agent->code.date('ymd',strtotime($now))."J";
            $k = $agent->code.date('ymd',strtotime($now))."K";
            $l = $agent->code.date('ymd',strtotime($now))."L";
            $m = $agent->code.date('ymd',strtotime($now))."M";
            $n = $agent->code.date('ymd',strtotime($now))."N";
            $o = $agent->code.date('ymd',strtotime($now))."O";
            $p = $agent->code.date('ymd',strtotime($now))."P";
            $q = $agent->code.date('ymd',strtotime($now))."Q";
            $r = $agent->code.date('ymd',strtotime($now))."R";
            $s = $agent->code.date('ymd',strtotime($now))."S";
            $t = $agent->code.date('ymd',strtotime($now))."T";
            $u = $agent->code.date('ymd',strtotime($now))."U";
            $v = $agent->code.date('ymd',strtotime($now))."V";
            $w = $agent->code.date('ymd',strtotime($now))."W";
            $x = $agent->code.date('ymd',strtotime($now))."X";
            $y = $agent->code.date('ymd',strtotime($now))."Y";
            $z = $agent->code.date('ymd',strtotime($now))."Z";
            $orderw = OrderWedding::where('orderno', $a)
            ->orWhere('orderno', $b)
            ->orWhere('orderno', $c)
            ->orWhere('orderno', $d)
            ->orWhere('orderno', $e)
            ->orWhere('orderno', $f)
            ->orWhere('orderno', $g)
            ->orWhere('orderno', $h)
            ->orWhere('orderno', $i)
            ->orWhere('orderno', $j)
            ->orWhere('orderno', $k)
            ->orWhere('orderno', $l)
            ->orWhere('orderno', $m)
            ->orWhere('orderno', $n)
            ->orWhere('orderno', $o)
            ->orWhere('orderno', $p)
            ->orWhere('orderno', $q)
            ->orWhere('orderno', $r)
            ->orWhere('orderno', $s)
            ->orWhere('orderno', $t)
            ->orWhere('orderno', $u)
            ->orWhere('orderno', $v)
            ->orWhere('orderno', $w)
            ->orWhere('orderno', $x)
            ->orWhere('orderno', $y)
            ->orWhere('orderno', $z)
            ->get();
            $crsva = count($orderw);

            if ($crsva == 0) {
                $order_no = $a;
            }elseif($crsva == 1){
                $order_no = $b;
            }elseif($crsva == 2){
                $order_no = $c;
            }elseif($crsva == 3){
                $order_no = $d;
            }elseif($crsva == 4){
                $order_no = $e;
            }elseif($crsva == 5){
                $order_no = $f;
            }elseif($crsva == 6){
                $order_no = $g;
            }elseif($crsva == 7){
                $order_no = $h;
            }elseif($crsva == 8){
                $order_no = $i;
            }elseif($crsva == 9){
                $order_no = $j;
            }elseif($crsva == 10){
                $order_no = $k;
            }elseif($crsva == 11){
                $order_no = $l;
            }elseif($crsva == 12){
                $order_no = $m;
            }elseif($crsva == 13){
                $order_no = $n;
            }elseif($crsva == 14){
                $order_no = $o;
            }elseif($crsva == 15){
                $order_no = $p;
            }elseif($crsva == 16){
                $order_no = $q;
            }elseif($crsva == 17){
                $order_no = $r;
            }elseif($crsva == 18){
                $order_no = $s;
            }elseif($crsva == 19){
                $order_no = $t;
            }elseif($crsva == 20){
                $order_no = $u;
            }elseif($crsva == 21){
                $order_no = $v;
            }elseif($crsva == 22){
                $order_no = $w;
            }elseif($crsva == 23){
                $order_no = $x;
            }elseif($crsva == 24){
                $order_no = $y;
            }elseif($crsva == 25){
                $order_no = $z;
            }else{
                $order_no = $AA;
            }

        $weddingPackage = Weddings::find($id);
        $ceremonyVenue = $weddingPackage->ceremony_venue;
        $ceremonyVenueDecoration = $weddingPackage->ceremony_venue_decoration;
        $receptionVenuePackages = $weddingPackage->reception_package;
        $receptionVenue = $weddingPackage->dinner_venue;
        $receptionVenueDecoration = $weddingPackage->reception_venue_decoration;
        $hotel = $weddingPackage->hotels;
        $hotel_id = $hotel->id;
        $slot = $request->slot;
        $orderno = $order_no;
        $service = "Wedding Package";
        $service_id = $weddingPackage->id;
        $duration = $weddingPackage->duration;
        $wedding_date = date('Y-m-d',strtotime($request->wedding_date));
        $number_of_invitation = $request->number_of_invitations;
        $checkin = date('Y-m-d',strtotime('-1 days',strtotime($wedding_date)));
        $checkout = date('Y-m-d',strtotime('+'.$duration.' days',strtotime($checkin)));
        $basic_or_arrangement = "Arrangement";
        $ceremony_venue_duration = 2;
        $ceremony_venue_id = $weddingPackage->ceremony_venue_id;
        $ceremony_venue_price = $weddingPackage->ceremony_venue_price;
        $ceremony_venue_decoration_id = $weddingPackage->ceremony_venue_decoration_id;
        $ceremony_venue_decoration_price = $weddingPackage->ceremony_venue_decoration_price;
        $ceremony_venue_invitations = $request->number_of_invitations;
        
        $reception_date_start = date('Y-m-d',strtotime($request->wedding_date))." 18:00";
        $reception_venue_id = $weddingPackage->reception_venue_id;
        if ($receptionVenue) {
            $reception_venue_price = $receptionVenue->publish_rate;
        }else {
            $reception_venue_price = 0;
        }
        $reception_venue_decoration_id = $weddingPackage->reception_venue_decoration_id;
        $reception_venue_decoration_price = $weddingPackage->reception_venue_decoration_price;
        $reception_venue_invitations = $request->number_of_invitations;
        $room_bride_id = $weddingPackage->suites_and_villas_id;
        $rbprice = $weddingPackage->suites_and_villas_price;
        $rb_pr = HotelPrice::where('rooms_id',$room_bride_id)
        ->where('start_date','<=',$checkin)
        ->where('end_date','>=',$checkin)
        ->first();
        if ($rbprice) {
            $room_bride_price = $rbprice;
        }else{
            if ($rb_pr) {
                $cr_usd = ceil($rb_pr->contract_rate / $usdrates->rate);
                $rb_tax = ceil((($cr_usd + $rb_pr->markup)*$tax->tax)/100);
                $room_bride_price = $rb_pr->markup + $rb_tax + $cr_usd;
            }else {
                $room_bride_price = 0;
            }
        }
        if (Auth::user()->position == "weddingDvl" || Auth::user()->position == "weddingAuthor" || Auth::user()->position == "weddingSls" || Auth::user()->position == "weddingRsv") {
            $agent_id = $request->agent_id;
            $handled_by = Auth::user()->id;
            $handled_date = $now;
            $status = "Pending";
        }else{
            $agent_id = $agent->id;
            $handled_by = null;
            $handled_date = null;
            $status = "Draft";
        }
        $addser_id = $weddingPackage->additional_service_id;
        $additional_services_id = json_decode($addser_id);
        if (is_array($additional_services_id)) {
            $additional_services = VendorPackage::whereIn('id', $additional_services_id)->get();
            $additional_services_price = $additional_services->sum('publish_rate');
        } else {
            $additional_services = null;
            $additional_services_price = null;
        }
        
        $groom = $request->groom_name;
        $bride = $request->bride_name;
        $groom_pasport_id = $request->groom_id;
        $bride_pasport_id = $request->bride_id;
        $brides =new Brides([
            "groom"=>$groom,
            "groom_pasport_id"=>$groom_pasport_id,
            "bride"=>$bride,
            "bride_pasport_id"=>$bride_pasport_id,
        ]);
        $brides->save();
        $brides_id = $brides->id;
        $dinner_venue_time_end = date('Y-m-d',strtotime('+'.$duration.'days',strtotime($reception_date_start)));
        
        $dinner_venue_id = $weddingPackage->dinner_venue_id;
        $dinner_venue_date = $reception_date_start;
        $dinner_venue_price = $weddingPackage->dinner_venue_price;
        $lunch_venue_id = $weddingPackage->lunch_venue_id;
        $lunch_venue_date = date('Y-m-d',strtotime($wedding_date))." 13:00";
        $lunch_venue_price = $weddingPackage->lunch_venue_price;
        $transport_id = $weddingPackage->transport_id;
        $transport_type = "Airport Shuttle";
        $transport = Transports::where('id',$transport_id)->first();
        $transportPrice = TransportPrice::where('transports_id',$transport_id)->where('type','Airport Shuttle')->where('duration',$hotel->airport_duration)->first();
        if ($transportPrice) {
            $trns_cr = $transportPrice->contract_rate;
            $trns_markup = $transportPrice->contract_rate;
            $trns_usd = ($trns_cr/$usdrates->rate) + $trns_markup;
            $trns_tax = ceil(($trns_usd*$tax->tax)/100);
            $transport_price = $trns_usd + $trns_tax;
            
        }else{
            $transport_price = 0;
        }
        
        $carbonDate = Carbon::parse($request->wedding_date);
        $weekday = $carbonDate->format('l');
        if ($weekday == "Saturday") {
            $final_price = $weddingPackage->holiday_price;
            $package_price = $weddingPackage->holiday_price;
            $price_type = "Holiday";
        }elseif($weekday == "Sunday"){
            $final_price = $weddingPackage->holiday_price;
            $package_price = $weddingPackage->holiday_price;
            $price_type = "Holiday";
        }else{
            $final_price = $weddingPackage->week_day_price;
            $package_price = $weddingPackage->week_day_price;
            $price_type = "Week Day";
        }

        $order_wedding_venue =new OrderWedding([
            "orderno"=>$orderno,
            "service"=>$service,
            "service_id"=>$service_id,
            "slot"=>$slot,
            "hotel_id"=>$hotel_id,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "duration"=>$duration,
            "wedding_date"=>$wedding_date,
            "brides_id"=>$brides_id,
            "basic_or_arrangement"=>$basic_or_arrangement,
            "number_of_invitation"=>$number_of_invitation,
            "ceremony_venue_duration"=>$ceremony_venue_duration,
            "ceremony_venue_id"=>$ceremony_venue_id,
            "ceremony_venue_price"=>$ceremony_venue_price,
            "ceremony_venue_decoration_id"=>$ceremony_venue_decoration_id,
            "ceremony_venue_decoration_price"=>$ceremony_venue_decoration_price,
            "dinner_venue_id"=>$dinner_venue_id,
            "dinner_venue_date"=>$dinner_venue_date,
            "dinner_venue_price"=>$dinner_venue_price,
            "lunch_venue_id"=>$lunch_venue_id,
            "lunch_venue_date"=>$lunch_venue_date,
            "lunch_venue_price"=>$lunch_venue_price,
            "ceremony_venue_invitations"=>$ceremony_venue_invitations,
            "reception_date_start"=>$reception_date_start,
            "reception_venue_id"=>$reception_venue_id,
            "reception_venue_price"=>$reception_venue_price,
            "reception_venue_decoration_id"=>$reception_venue_decoration_id,
            "reception_venue_decoration_price"=>$reception_venue_decoration_price,
            "reception_venue_invitations"=>$reception_venue_invitations,
            "room_bride_id"=>$room_bride_id,
            "room_bride_price"=>$room_bride_price,
            "additional_services"=>$addser_id,
            "additional_services_price"=>$additional_services_price,
            "transport_id"=>$transport_id,
            "transport_type"=>$transport_type,
            "transport_price"=>$transport_price,

            "price_type"=>$price_type,
            "final_price"=>$final_price,
            "package_price"=>$package_price,
            "agent_id"=>$agent_id,
            "handled_by"=>$handled_by,
            "handled_date"=>$handled_date,
            "status"=>$status,
        ]);
        // dd($order_wedding_venue);
        $order_wedding_venue->save();
        // return response()->json([
        //     $order_wedding_venue,
        // ]);
        if (Auth::user()->position == "weddingDvl" || Auth::user()->position == "weddingSls" || Auth::user()->position == "weddingAuthor" || Auth::user()->position == "weddingRsv") {
            return redirect("/validate-orders-wedding-$orderno")->with('success', 'Wedding package order has been created successfully');
        }else{
            return redirect("/edit-order-wedding-$order_wedding_venue->orderno")->with('success', 'Wedding package order has been created successfully');
        }
    }

    // FUNCTION ADD ADDITIONAL SERVICES TO ORDER WEDDING
    public function func_add_order_wedding_additional_service(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        $date = date('Y-m-d',strtotime($request->date)).date(' H:i',strtotime($request->time));
        $price = 0;
        $additional_service = new WeddingAdditionalServices([
            "date" => $date,
            "order_wedding_id" => $orderWedding->id,
            "type" => $request->type,
            "service" => $request->service,
            "quantity" => $request->quantity,
            "note" => $request->note,
            "price" => $price,
            "status" => "Request",
        ]);
        $additional_service->save();

        // FINAL PRICE
        // $additional_services = WeddingAdditionalServices::where('order_wedding_id',$id)->where('status',"Approved")->get();
        // $addser_price = $additional_services->sum('price');
        
        // $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
        // $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
        // $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
        // $number_of_invitation = $request->reception_venue_invitations;
        // $reception_venue_invitations = $request->reception_venue_invitations;
       
        // $final_price = $orderWedding->ceremony_venue_price 
        //     + $orderWedding->room_bride_price 
        //     + $orderWedding->accommodation_price 
        //     + $orderWedding->ceremony_venue_decoration_price 
        //     + $orderWedding->reception_venue_price 
        //     + $orderWedding->reception_venue_decoration_price
        //     + $orderWedding->transport_price
        //     + $makeup_price
        //     + $documentation_price
        //     + $entertainment_price
        //     + $orderWedding->additional_services_price
        //     + $orderWedding->markup;
        //     + $orderWedding->lunch_venue_price;
        //     + $orderWedding->dinner_venue_price;
        //     + $addser_price;
        // $orderWedding->update([
        //     'addser_price'=>$addser_price,
        //     'final_price'=>$final_price,
        // ]);
        return redirect("/edit-order-wedding-$orderWedding->orderno#additionalServices")->with('success','Service has been added to the order!');
    }

    // FUNCTION ADD TRANSPORT TO ORDER WEDDING
    public function func_add_wedding_order_transport(Request $request,$id){
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $orderWedding = OrderWedding::find($id);
        if ($request->transport_id) {
        
            $transport_id = $request->transport_id;
            $transport = Transports::where('id',$transport_id)->first();
            
            $hotel = Hotels::where('id',$orderWedding->hotel_id)->first();
            $order_wedding_id = $id;
            $transportPrice = TransportPrice::where('transports_id',$transport_id)
            ->where('duration',$hotel->airport_duration)
            ->first();
            if($transportPrice){
                $tp_usd = ($transportPrice->contract_rate / $usdrates->rate)+$transportPrice->markup;
                $tp_tax = ($tp_usd * $tax->tax) / 100;
                $transport_price = ceil($tp_usd + $tp_tax);
            }else{
                $transport_price = null;
            }

            if ($request->type == "Airport Shuttle In") {
                $desc_type = "In";
                $type = "Airport Shuttle";
            }elseif ($request->type == "Airport Shuttle Out") {
                $type = "Airport Shuttle";
                $desc_type = "Out";
            }else {
                $type = $request->type;
                $desc_type = null;
                
            }
            $date = date('Y-m-d',strtotime($request->date)).date(' H:i',strtotime($request->time));
            $passenger = $request->passenger;
            if ($request->passenger == "Bride") {
                $number_of_guests = 2;
            }else{
                $number_of_guests = $transport->capacity;
            }
            $duration = $hotel->airport_duration;
            $distance = $hotel->airport_distance;
            $remark = $request->remark;
            $price = $request->price;
            $order_wedding_transport = new WeddingPlannerTransport([
                "order_wedding_id" => $order_wedding_id,
                "transport_id" => $transport_id,
                "type" => $type,
                "desc_type" => $desc_type,
                "date" => $date,
                "passenger" => $passenger,
                "number_of_guests" => $number_of_guests,
                "duration" => $duration,
                "distance" => $distance,
                "remark" => $remark,
                "price" => $transport_price,
            ]);
            $order_wedding_transport->save();

            // FINAL PRICE
            $transport_orders = WeddingPlannerTransport::where('order_wedding_id',$orderWedding->id)->get();
            $transport_invitations_price = $transport_orders->pluck('price')->sum();

            if ($orderWedding->service == "Wedding Package") {
                $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
                $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
                $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
                $final_price = $orderWedding->accommodation_price
                    + $orderWedding->extra_bed_price
                    + $transport_invitations_price //proses
                    + $orderWedding->addser_price
                    + $orderWedding->package_price
                    + $orderWedding->markup;
            }else{
                $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
                $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
                $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
                $final_price = $orderWedding->ceremony_venue_price
                    + $orderWedding->ceremony_venue_decoration_price 
                    + $orderWedding->reception_venue_price 
                    + $orderWedding->reception_venue_decoration_price
                    + $orderWedding->dinner_venue_price
                    + $orderWedding->lunch_venue_price
                    + $orderWedding->room_bride_price 
                    + $orderWedding->accommodation_price
                    + $orderWedding->extra_bed_price
                    + $orderWedding->transport_price
                    + $transport_invitations_price //proses
                    + $makeup_price
                    + $documentation_price
                    + $entertainment_price
                    + $orderWedding->additional_services_price
                    + $orderWedding->addser_price
                    + $orderWedding->markup;
            }
            $orderWedding->update([
                'transport_invitations_price'=>$transport_invitations_price,
                'final_price'=>$final_price,
            ]);
            return redirect("/edit-order-wedding-$orderWedding->orderno#weddingTransport")->with('success','Transport has been added to your order!');
        }else{
            return redirect("/edit-order-wedding-$orderWedding->orderno#weddingTransport")->with('errors_message','Please select transport!');
        }
    }
    // FUNCTION UPDATE TRANSPORT TO ORDER WEDDING >
    public function func_user_update_order_wedding_transport(Request $request,$id)
    {
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $order_wedding_transport = WeddingPlannerTransport::where('id',$id)->first();
        $transport = Transports::where('id',$request->edit_transport_id)->first();
        $orderWedding = OrderWedding::where('id',$order_wedding_transport->order_wedding_id)->first();
        $hotel = Hotels::where('id',$orderWedding->hotel_id)->first();
        $date = date('Y-m-d',strtotime($request->date))." ".date('H.i',strtotime($request->time));
        if ($request->passenger == "Bride") {
            $number_of_guests = 2;
        }else{
            $number_of_guests = $transport->capacity;
        }
        
        $transportPrice = TransportPrice::where('transports_id',$transport->id)
        ->where('duration',$hotel->airport_duration)
        ->first();
        if($transportPrice){
            $tp_usd = ($transportPrice->contract_rate / $usdrates->rate)+$transportPrice->markup;
            $tp_tax = ($tp_usd * $tax->tax) / 100;
            $transport_price = ceil($tp_usd + $tp_tax);
        }else{
            $transport_price = null;
        }
        if ($request->type == "Airport Shuttle In") {
            $desc_type = "In";
            $type = "Airport Shuttle";
        }elseif ($request->type == "Airport Shuttle Out") {
            $type = "Airport Shuttle";
            $desc_type = "Out";
        }else {
            $type = $request->type;
            $desc_type = null;
        }

        $order_wedding_transport->update([
            'transport_id'=>$transport->id,
            'type'=>$type,
            'desc_type'=>$desc_type,
            'date'=>$date,
            'Passenger'=>$request->passenger,
            'number_of_guests'=>$number_of_guests,
            'duration'=>$hotel->airport_duration,
            'distance'=>$hotel->airport_distance,
            'remark'=>$request->remark,
            'price'=>$transport_price,
        ]);
        
        // FINAL PRICE
        $transport_orders = WeddingPlannerTransport::where('order_wedding_id',$orderWedding->id)->get();
        $transport_invitations_price = $transport_orders->pluck('price')->sum();

       if ($orderWedding->service == "Wedding Package") {
           $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
           $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
           $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
           $final_price = $orderWedding->accommodation_price
               + $orderWedding->extra_bed_price
               + $transport_invitations_price //proses
               + $orderWedding->addser_price
               + $orderWedding->package_price
               + $orderWedding->markup;
       }else{
           $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
           $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
           $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
           $final_price = $orderWedding->ceremony_venue_price
               + $orderWedding->ceremony_venue_decoration_price 
               + $orderWedding->reception_venue_price 
               + $orderWedding->reception_venue_decoration_price
               + $orderWedding->dinner_venue_price
               + $orderWedding->lunch_venue_price
               + $orderWedding->room_bride_price 
               + $orderWedding->accommodation_price
               + $orderWedding->extra_bed_price
               + $orderWedding->transport_price
               + $transport_invitations_price //proses
               + $makeup_price
               + $documentation_price
               + $entertainment_price
               + $orderWedding->additional_services_price
               + $orderWedding->addser_price
               + $orderWedding->markup;
       }
        $orderWedding->update([
            'transport_invitations_price'=>$transport_invitations_price,
            'final_price'=>$final_price,
        ]);
        // dd($order_wedding, $order_wedding_transport,$ordered_transport_price);
        return redirect("/edit-order-wedding-$orderWedding->orderno#weddingTransport")->with('success','The Wedding transportation has been successfully updated!');
    }
    //REMOVE TRANSPORT FROM ORDER WEDDING
    public function func_remove_transport_from_order_wedding(Request $request,$id){
        $order_wedding_transport = WeddingPlannerTransport::find($id);
        $orderWedding = OrderWedding::where('id',$order_wedding_transport->order_wedding_id)->first();
        
        $order_wedding_transport->delete();
        // FINAL PRICE
        $transport_orders = WeddingPlannerTransport::where('order_wedding_id',$orderWedding->id)->get();
        $transport_invitations_price = $transport_orders->pluck('price')->sum();

       if ($orderWedding->service == "Wedding Package") {
           $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
           $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
           $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
           $final_price = $orderWedding->accommodation_price
               + $orderWedding->extra_bed_price
               + $transport_invitations_price //proses
               + $orderWedding->addser_price
               + $orderWedding->package_price
               + $orderWedding->markup;
       }else{
           $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
           $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
           $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
           $final_price = $orderWedding->ceremony_venue_price
               + $orderWedding->ceremony_venue_decoration_price 
               + $orderWedding->reception_venue_price 
               + $orderWedding->reception_venue_decoration_price
               + $orderWedding->dinner_venue_price
               + $orderWedding->lunch_venue_price
               + $orderWedding->room_bride_price 
               + $orderWedding->accommodation_price
               + $orderWedding->extra_bed_price
               + $orderWedding->transport_price
               + $transport_invitations_price //proses
               + $makeup_price
               + $documentation_price
               + $entertainment_price
               + $orderWedding->additional_services_price
               + $orderWedding->addser_price
               + $orderWedding->markup;
       }
        $orderWedding->update([
            'transport_invitations_price'=>$transport_invitations_price,
            'final_price'=>$final_price,
        ]);
        return redirect("/edit-order-wedding-$orderWedding->orderno#weddingTransport")->with('success','Transport has been removed from your order!');
    }

    //REMOVE REQUEST SERVICE FROM ORDER WEDDING
    public function func_remove_request_service_from_order_wedding(Request $request,$id){
        $request_service = WeddingAdditionalServices::find($id);
        $orderWedding = OrderWedding::where('id',$request_service->order_wedding_id)->first();
        
        
        // FINAL PRICE
        $requestServices = WeddingAdditionalServices::where('order_wedding_id',$orderWedding->id)->get();
        $addser_price = $requestServices->pluck('price')->sum();
        
        $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
        $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
        $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
        $final_price = $orderWedding->ceremony_venue_price 
            + $orderWedding->ceremony_venue_decoration_price 
            + $orderWedding->reception_venue_price 
            + $orderWedding->reception_venue_decoration_price
            + $orderWedding->dinner_venue_price
            + $orderWedding->lunch_venue_price
            + $orderWedding->room_bride_price 
            + $orderWedding->accommodation_price
            + $orderWedding->extra_bed_price
            + $orderWedding->transport_price
            + $orderWedding->transport_invitations_price
            + $makeup_price
            + $documentation_price
            + $entertainment_price
            + $orderWedding->additional_services_price
            + $addser_price //Process
            + $orderWedding->markup;
        $orderWedding->update([
            'addser_price'=>$addser_price,
            'final_price'=>$final_price,
        ]);
        $request_service->delete();
        return redirect("/edit-order-wedding-$orderWedding->orderno#requestServices")->with('success','Request has been removed from the order!');
    }

    // OK FUNCTION UPDATE CEREMONY VENUE (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_update_wedding_order_ceremony_venue(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        $ceremony_venue_id = $request->ceremonial_venue_id ? $request->ceremonial_venue_id : $orderWedding->service_id;
        $req_slot = $request->slot;
        $ceremony_venue = WeddingVenues::where('id',$ceremony_venue_id)->first();
        if ($orderWedding->service == "Ceremony Venue") {
            $service_id = $ceremony_venue->id;
            $wedding_date = $orderWedding->wedding_date;
        }else{
            $wedding_date = date('Y-m-d',strtotime($request->wedding_date));
            $service_id = $orderWedding->service_id;
        }
        if ($ceremony_venue) {
            $slots = json_decode($ceremony_venue->slot);
            $bps = json_decode($ceremony_venue->basic_price);
            $aps = json_decode($ceremony_venue->arrangement_price);
            $c_slot = count($slots);
            for ($i=0; $i < $c_slot; $i++) { 
                if ( date('Hi',strtotime($slots[$i])) == date('Hi',strtotime($req_slot) )) {
                    $slot = $slots[$i];
                    $basic_price = $bps[$i];
                    $arrangement_price = $aps[$i];
                    $ceremony_venue_duration = 2;
                    break;
                }else{
                    $slot = NULL;
                    $basic_price = NULL;
                    $arrangement_price = NULL;
                    $ceremony_venue_duration = NULL;
                }
            }
            // FINAL PRICE 
            if ($orderWedding->ceremony_venue_decoration_id) {
                $basic_or_arrangement = "Arrangement";
                $ceremony_venue_price = $arrangement_price;
            }else{
                $basic_or_arrangement = "Basic";
                $ceremony_venue_price = $basic_price;
            }
            if ($orderWedding->service == "Wedding Package") {
                $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
                $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
                $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
                $final_price = $orderWedding->accommodation_price
                    + $orderWedding->extra_bed_prices
                    + $orderWedding->transport_invitations_price
                    + $orderWedding->addser_price
                    + $orderWedding->package_price
                    + $orderWedding->markup;
            }else{
                $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
                $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
                $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
                $final_price = $ceremony_venue_price //proses
                    + $orderWedding->ceremony_venue_decoration_price
                    + $orderWedding->reception_venue_price 
                    + $orderWedding->reception_venue_decoration_price
                    + $orderWedding->dinner_venue_price
                    + $orderWedding->lunch_venue_price
                    + $orderWedding->room_bride_price 
                    + $orderWedding->accommodation_price
                    + $orderWedding->extra_bed_prices
                    + $orderWedding->transport_price
                    + $orderWedding->transport_invitations_price
                    + $makeup_price
                    + $documentation_price
                    + $entertainment_price
                    + $orderWedding->additional_services_price
                    + $orderWedding->addser_price
                    + $orderWedding->markup;
            }
            $orderWedding->update([
                'service_id'=>$service_id,
                'wedding_date'=>$wedding_date,
                'number_of_invitation'=>$request->number_of_invitation,
                'ceremony_venue_invitations'=>$request->number_of_invitation,
                'slot'=>$slot,
                'basic_or_arrangement'=>$basic_or_arrangement,
                'ceremony_venue_id'=>$ceremony_venue_id,
                'ceremony_venue_duration'=>$ceremony_venue_duration,
                'ceremony_venue_price'=>$ceremony_venue_price,
                'final_price'=>$final_price,
            ]);
            // dd($orderWedding);
            return redirect()->back()->with('success','Wedding order has been updated!');
        }else{
            return redirect()->back()->with('success','Ceremony Venue not found!');
        }
    }
    // OK FUNCTION REMOVE CEREMONY VENUE (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_delete_ceremony_venue(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        $slot = NULL;
        $wedding_date = NULL;
        $basic_or_arrangement = NULL;
        $ceremony_venue_duration = NULL;
        $ceremony_venue_id = NULL;
        $ceremony_venue_price = NULL;
        $ceremony_venue_decoration_id = NULL;
        $ceremony_venue_decoration_price = NULL;
        // FINAL PRICE
        if ($orderWedding->service == "Wedding Package") {
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $orderWedding->accommodation_price
                + $orderWedding->extra_bed_prices
                + $orderWedding->transport_invitations_price
                + $orderWedding->addser_price
                + $orderWedding->package_price
                + $orderWedding->markup;
        }else{
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $ceremony_venue_price //proses
                + $ceremony_venue_decoration_price //proses
                + $orderWedding->reception_venue_price 
                + $orderWedding->reception_venue_decoration_price
                + $orderWedding->dinner_venue_price
                + $orderWedding->lunch_venue_price
                + $orderWedding->room_bride_price 
                + $orderWedding->accommodation_price
                + $orderWedding->extra_bed_prices
                + $orderWedding->transport_price
                + $orderWedding->transport_invitations_price
                + $makeup_price
                + $documentation_price
                + $entertainment_price
                + $orderWedding->additional_services_price
                + $orderWedding->addser_price
                + $orderWedding->markup;
        }
        $order_wedding->update([
            'slot'=>$slot,
            'wedding_date'=>$wedding_date,
            'basic_or_arrangement'=>$basic_or_arrangement,
            'ceremony_venue_duration'=>$ceremony_venue_duration,
            'ceremony_venue_id'=>$ceremony_venue_id,
            'ceremony_venue_price'=>$ceremony_venue_price,
            'ceremony_venue_decoration_id'=>$ceremony_venue_decoration_id,
            'ceremony_venue_decoration_price'=>$ceremony_venue_decoration_price,
            'final_price'=>$final_price,
        ]);
        // dd($order_wedding);
        return redirect()->back()->with('success','Wedding order has been updated!');
    }

    // OK FUNCTION ADD OR UPDATE DECORATION CEREMONY VENUE (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_update_decoration_ceremony_venue(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        $ceremony_venue_decoration = VendorPackage::where('id',$request->ceremony_venue_decoration_id)->first();
        $ceremony_venue = WeddingVenues::where('id',$orderWedding->service_id)->first();
        $arrangement_price = json_decode($ceremony_venue->arrangement_price);
        $slot = json_decode($ceremony_venue->slot);
        $c_slot = count($slot);
        for ($i=0; $i < $c_slot; $i++) { 
            if ( date('Hi',strtotime($slot[$i])) == date('Hi',strtotime($orderWedding->slot) )) {
                $cvp = $arrangement_price[$i];
                break;
            }else{
                $cvp = NULL;
            }
        }
        if ($request->ceremony_venue_decoration_id) {
            // FINAL PRICE 
            $ceremony_venue_decoration_price = $ceremony_venue_decoration->publish_rate;
            $ceremony_venue_price = $cvp;
            if ($orderWedding->service == "Wedding Package") {
                $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
                $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
                $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
                $final_price = $orderWedding->accommodation_price
                    + $orderWedding->extra_bed_prices
                    + $orderWedding->transport_invitations_price
                    + $orderWedding->addser_price
                    + $orderWedding->package_price
                    + $orderWedding->markup;
            }else{
                $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
                $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
                $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
                $final_price = $ceremony_venue_price //proses
                    + $ceremony_venue_decoration_price //proses
                    + $orderWedding->reception_venue_price 
                    + $orderWedding->reception_venue_decoration_price
                    + $orderWedding->dinner_venue_price
                    + $orderWedding->lunch_venue_price
                    + $orderWedding->room_bride_price 
                    + $orderWedding->accommodation_price
                    + $orderWedding->extra_bed_prices
                    + $orderWedding->transport_price
                    + $orderWedding->transport_invitations_price
                    + $makeup_price
                    + $documentation_price
                    + $entertainment_price
                    + $orderWedding->additional_services_price
                    + $orderWedding->addser_price
                    + $orderWedding->markup;
            }
            $orderWedding->update([
                'ceremony_venue_price'=>$ceremony_venue_price,
                'ceremony_venue_decoration_id'=>$ceremony_venue_decoration->id,
                'ceremony_venue_decoration_price'=>$ceremony_venue_decoration_price,
                'basic_or_arrangement'=>"Arrangement",
                'final_price'=>$final_price,
            ]);
            // dd($order_wedding,$additional_services_price );
            return redirect("/edit-order-wedding-$orderWedding->orderno#decoration-ceremony-venue")->with('success','Decoration has been updated!');
        }else{
            return redirect("/edit-order-wedding-$orderWedding->orderno#decoration-ceremony-venue")->with('success','Nothing changed!');
        }
    }
    // OK FUNCTION REMOVE DECORATION CEREMONY VENUE (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_delete_decoration_ceremony_venue(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        $ceremony_venue = WeddingVenues::where('id',$orderWedding->service_id)->first();
        $basic_price = json_decode($ceremony_venue->basic_price);
        $slot = json_decode($ceremony_venue->slot);
        $w_slot = $orderWedding->slot;
        $c_slot = count($slot);
        for ($i=0; $i < $c_slot; $i++) { 
            if ( $slot[$i] == $w_slot) {
                $cvp = $basic_price[$i];
                break;
            }else{
                $cvp = NULL;
            }
        }
        // FINAL PRICE
        if ($ceremony_venue) {
            $ceremony_venue_decoration_price = NULL;
            $ceremony_venue_price = $cvp;
            $reception_venue_price =$orderWedding->reception_venue_price;
            if ($orderWedding->service == "Wedding Package") {
                $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
                $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
                $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
                $final_price = $orderWedding->accommodation_price
                    + $orderWedding->extra_bed_prices
                    + $orderWedding->transport_invitations_price
                    + $orderWedding->addser_price
                    + $orderWedding->package_price
                    + $orderWedding->markup;
            }else{
                $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
                $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
                $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
                $final_price = $ceremony_venue_price
                + $reception_venue_price 
                + $orderWedding->reception_venue_decoration_price
                + $orderWedding->dinner_venue_price
                + $orderWedding->lunch_venue_price
                + $orderWedding->room_bride_price 
                + $orderWedding->accommodation_price
                + $orderWedding->extra_bed_prices
                + $orderWedding->transport_price
                + $orderWedding->transport_invitations_price
                + $makeup_price
                + $documentation_price
                + $entertainment_price
                + $orderWedding->additional_services_price
                + $orderWedding->addser_price
                + $orderWedding->markup
                + $ceremony_venue_decoration_price;
            }
            $orderWedding->update([
                'ceremony_venue_price'=>$ceremony_venue_price,
                'ceremony_venue_decoration_id'=>NULL,
                'ceremony_venue_decoration_price'=>NULL,
                'basic_or_arrangement'=>"Basic",
                'final_price'=>$final_price,
            ]);
    
            // dd($orderWedding,$ceremony_venue,$basic_price, $cvp, $slot[0], $orderWedding->slot, $ceremony_venue_decoration_price);
            return redirect("/edit-order-wedding-$orderWedding->orderno#decoration-ceremony-venue")->with('success','Decoration has been removed from wedding order!');
        }else{
            return redirect("/edit-order-wedding-$orderWedding->orderno#decoration-ceremony-venue")->with('success','Nothink change!');
        }
    }

    // OK FUNCTION ADD OR UPDATE RECEPTION VENUE (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_update_reception_venue(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        if ($request->reception_venue_id) {
            if ($request->reception_date_start) {
                $reception_venue = WeddingReceptionVenues::where('id',$request->reception_venue_id)->first();
                if ($reception_venue) {
                    $reception_venue_id = $reception_venue->id;
                    $reception_venue_price = $reception_venue->publish_rate;
                }else{
                    $reception_venue_id = NULL;
                    $reception_venue_price = NULL;
                }
                $reception_date_start = date("Y-m-d",strtotime($request->reception_date_start)).date(" H:i",strtotime($request->reception_time_start));
                $reception_venue_invitations = $request->reception_venue_invitations;
                // FINAL PRICE
                if ($orderWedding->service == "Wedding Package") {
                    $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
                    $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
                    $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
                    $final_price = $orderWedding->accommodation_price
                        + $orderWedding->extra_bed_prices
                        + $orderWedding->transport_invitations_price
                        + $orderWedding->addser_price
                        + $orderWedding->package_price
                        + $orderWedding->markup;
                }else{
                    $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
                    $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
                    $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
                    $final_price = $orderWedding->ceremony_venue_price
                        + $orderWedding->ceremony_venue_decoration_price
                        + $reception_venue_price //proses
                        + $orderWedding->reception_venue_decoration_price
                        + $orderWedding->dinner_venue_price
                        + $orderWedding->lunch_venue_price
                        + $orderWedding->room_bride_price 
                        + $orderWedding->accommodation_price
                        + $orderWedding->extra_bed_prices
                        + $orderWedding->transport_price
                        + $orderWedding->transport_invitations_price
                        + $makeup_price
                        + $documentation_price
                        + $entertainment_price
                        + $orderWedding->additional_services_price
                        + $orderWedding->addser_price
                        + $orderWedding->markup;
                }
                $orderWedding->update([
                    'reception_date_start'=>$reception_date_start,
                    'reception_venue_id'=>$reception_venue_id,
                    'reception_venue_price'=>$reception_venue_price,
                    'reception_venue_invitations'=>$reception_venue_invitations,
                    'final_price'=>$final_price,
                ]);
                return redirect("/edit-order-wedding-$orderWedding->orderno#orderWeddingReceptionVenue")->with('success','Decoration has been updated!');
            }else{
                return redirect("/edit-order-wedding-$orderWedding->orderno#orderWeddingReceptionVenue")->with('errors_message','Reception date required!');
            }
        }else {
            return redirect("/edit-order-wedding-$orderWedding->orderno#orderWeddingReceptionVenue")->with('errors_message','Reception venue not yet selected!');
        }
    }
    // OK FUNCTION REMOVE RECEPTION VENUE (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_delete_reception_venue(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        // FINAL PRICE
        $reception_venue_price = null;
        $reception_venue_decoration_price = null;

        if ($orderWedding->service == "Wedding Package") {
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $orderWedding->accommodation_price
                + $orderWedding->extra_bed_prices
                + $orderWedding->transport_invitations_price
                + $orderWedding->addser_price
                + $orderWedding->package_price
                + $orderWedding->markup;
        }else{
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $orderWedding->ceremony_venue_price
                + $orderWedding->ceremony_venue_decoration_price
                + $reception_venue_price //proses
                + $reception_venue_decoration_price //proses
                + $orderWedding->dinner_venue_price
                + $orderWedding->lunch_venue_price
                + $orderWedding->room_bride_price 
                + $orderWedding->accommodation_price
                + $orderWedding->extra_bed_prices
                + $orderWedding->transport_price
                + $orderWedding->transport_invitations_price
                + $makeup_price
                + $documentation_price
                + $entertainment_price
                + $orderWedding->additional_services_price
                + $orderWedding->addser_price
                + $orderWedding->markup;
        }
        $orderWedding->update([
            'reception_venue_id'=>NULL,
            'reception_venue_price'=>NULL,
            'reception_date_start'=>NULL,
            'reception_venue_decoration_id'=>NULL,
            'reception_venue_decoration_price'=>NULL,
            'final_price'=>$final_price,
        ]);
        // dd($orderWedding);
        return redirect("/edit-order-wedding-$orderWedding->orderno#orderWeddingReceptionVenue")->with('success','Reception venue has been removed from wedding order!');
    }

    // OK FUNCTION ADD OR UPDATE RECEPTION VENUE DECORATION (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_update_decoration_reception_venue(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        $reception_venue_decoration = VendorPackage::where('id',$request->reception_venue_decoration_id)->first();
        $reception_venue_decoration_price = $reception_venue_decoration->publish_rate;
        // FINAL PRICE
        if ($orderWedding->service == "Wedding Package") {
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $orderWedding->accommodation_price
                + $orderWedding->extra_bed_prices
                + $orderWedding->transport_invitations_price
                + $orderWedding->addser_price
                + $orderWedding->package_price
                + $orderWedding->markup;
        }else{
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $orderWedding->ceremony_venue_price
                + $orderWedding->ceremony_venue_decoration_price
                + $orderWedding->reception_venue_price 
                + $reception_venue_decoration_price
                + $orderWedding->dinner_venue_price
                + $orderWedding->lunch_venue_price
                + $orderWedding->room_bride_price 
                + $orderWedding->accommodation_price
                + $orderWedding->extra_bed_prices
                + $orderWedding->transport_price
                + $orderWedding->transport_invitations_price
                + $makeup_price
                + $documentation_price
                + $entertainment_price
                + $orderWedding->additional_services_price
                + $orderWedding->addser_price
                + $orderWedding->markup;
        }
        $orderWedding->update([
            'reception_venue_decoration_id'=>$reception_venue_decoration->id,
            'reception_venue_decoration_price'=>$reception_venue_decoration_price,
            'final_price'=>$final_price,
        ]);
        // dd($orderWedding);
        return redirect("/edit-order-wedding-$orderWedding->orderno#orderWeddingDecorationReceptionVenue")->with('success','Reception venue decoration has been updated!');
    }

    // OK FUNCTION REMOVE RECEPTION VENUE (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_delete_decoration_reception_venue(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        $reception_venue_decoration_price = null;
        // FINAL PRICE
        if ($orderWedding->service == "Wedding Package") {
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $orderWedding->accommodation_price
                + $orderWedding->extra_bed_prices
                + $orderWedding->transport_invitations_price
                + $orderWedding->addser_price
                + $orderWedding->package_price
                + $orderWedding->markup;
        }else{
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $orderWedding->ceremony_venue_price
                + $orderWedding->ceremony_venue_decoration_price
                + $orderWedding->reception_venue_price 
                + $reception_venue_decoration_price //proses
                + $orderWedding->dinner_venue_price
                + $orderWedding->lunch_venue_price
                + $orderWedding->room_bride_price 
                + $orderWedding->accommodation_price
                + $orderWedding->extra_bed_prices
                + $orderWedding->transport_price
                + $orderWedding->transport_invitations_price
                + $makeup_price
                + $documentation_price
                + $entertainment_price
                + $orderWedding->additional_services_price
                + $orderWedding->addser_price
                + $orderWedding->markup;
        }
        
        $orderWedding->update([
            'reception_venue_decoration_id'=>NULL,
            'reception_venue_decoration_price'=>NULL,
            'final_price'=>$final_price,
        ]);
        // dd($orderWedding);
        return redirect("/edit-order-wedding-$orderWedding->orderno#orderWeddingDecorationReceptionVenue")->with('success','Reception venue decoration has been removed from wedding order!');
    }

    // OK FUNCTION SUBMIT ORDER WEDDING (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_submit_order_wedding(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        $status = "Pending";
        $orderWedding->update([
            'status'=>$status,
        ]);
        Mail::to(config('app.reservation_mail'))->send(new weddingReservationMail($id));
        return redirect("/detail-order-wedding-$orderWedding->orderno")->with('success','Your order has been submited!');
    }


    // OK FUNCTION ADD OR UPDATE ADDITIONAL SERVICE (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_additional_service_to_order_wedding(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        $additional_services = json_encode($request->addser_id);
        if ($request->addser_id) {
            $c_addser = count($request->addser_id);
            $additional_service_id = $request->addser_id;
            $additional_services_price = [];
            for ($i=0; $i < $c_addser; $i++) { 
                $addser_price = VendorPackage::where('id',$additional_service_id[$i])->first();
                array_push($additional_services_price,$addser_price->publish_rate);
            }
        }else{
            $c_addser = 0;
            $additional_services_price = null;
            $additional_services = null;
        }
        // FINAL PRICE
        $sum_additional_services_price = is_array($additional_services_price) ? array_sum($additional_services_price): 0;
        
        $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
        $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
        $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
        $final_price = $orderWedding->ceremony_venue_price 
            + $orderWedding->ceremony_venue_decoration_price 
            + $orderWedding->reception_venue_price 
            + $orderWedding->reception_venue_decoration_price
            + $orderWedding->dinner_venue_price
            + $orderWedding->lunch_venue_price
            + $orderWedding->room_bride_price 
            + $orderWedding->accommodation_price
            + $orderWedding->extra_bed_price
            + $orderWedding->transport_price
            + $orderWedding->transport_invitations_price
            + $makeup_price
            + $documentation_price
            + $entertainment_price
            + $sum_additional_services_price //process
            + $orderWedding->addser_price
            + $orderWedding->markup;
        $orderWedding->update([
            'additional_services'=>$additional_services,
            'additional_services_price'=>$sum_additional_services_price,
            'final_price'=>$final_price,
        ]);
        return redirect("/edit-order-wedding-$orderWedding->orderno#additionalServices")->with('success','Your order has been updated!');
    }

    // OK FUNCTION ADD REMARK (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_add_order_wedding_remark(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        $remark = $request->remark;
        $order_wedding->update([
            'remark'=>$remark,
        ]);
        // dd($order_wedding);
        return redirect("/edit-order-wedding-$order_wedding->orderno#remarkPage")->with('success','Your order has been updated!');
    }

    // ADD ORDER WEDDING ACCOMMODATION =========================================================================================>
    public function func_add_order_wedding_accommodation(Request $request,$id)
    {
        $request->validate([
            'rooms_id'=>'required',
            'room_for'=>'required|in:Couple,Inv',
            'guests_detail'=>'required_if:type,Inv',
            'number_of_guests'=>'required_if:type,Inv',
        ]);
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        
        $orderWedding = OrderWedding::find($id);
        $status = "Requested";
        if ($orderWedding->service == "Wedding Package") {
            $checkin = $orderWedding->checkin;
            $checkout = $orderWedding->checkout;
            $duration = $orderWedding->duration;
        }else{
            $checkin = date('Y-m-d',strtotime($request->checkin));
            $checkout = date('Y-m-d',strtotime($request->checkout));
            $in=Carbon::parse($checkin);
            $out=Carbon::parse($checkout);
            $duration = $in->diffInDays($out);
        }
        if ($request->room_for == "Couple") {
            $bride = Brides::where('id',$orderWedding->brides_id)->first();
            $guest_detail = $bride->groom.', '.$bride->bride;
            $number_of_guests = 2;
            $extra_bed_id = null;
        }else{
            $guest_detail = $request->guest_detail;
            $number_of_guests = $request->number_of_guests;
            $extra_bed_id = $request->extra_bed_id;
        }
        
        $room_id = $request->rooms_id;
        $hp = [];
        $cin = $checkin;
        for ($j=0; $j < $duration; $j++) { 
            $cin = date('Y-m-d',strtotime('+'.$j.' days',strtotime($cin)));
            $hotel_price = HotelPrice::where('rooms_id',$room_id)
            ->where('start_date','<=',$cin)
            ->where('end_date','>=',$cin)
            ->first();
            if ($hotel_price) {
                $rate_usd = $usdrates->rate;
                $cr = $hotel_price->contract_rate;
                $mrk = $hotel_price->markup;
                $cr_usd = ceil($cr / $rate_usd);
                $tx = ceil((($cr_usd + $mrk) * $tax->tax)/100);
                $pr = $cr_usd + $mrk + $tx;
                array_push($hp,$pr);
            }
        }
        $pub_rate = array_sum($hp);
        if ($number_of_guests>2) {
            if ($extra_bed_id) {
                $extra_bed = ExtraBed::where('id',$extra_bed_id)->first();
                if ($extra_bed) {
                    $tax_rate = ceil(((($extra_bed->contract_rate / $usdrates->rate) + $extra_bed->markup)* $tax->tax) / 100);
                    $exb_rate = ceil(($extra_bed->contract_rate / $usdrates->rate) + $extra_bed->markup);
                    $public_rate = $pub_rate + ($exb_rate * $duration);
                    $extra_bed_order =new ExtraBedOrder([
                        "order_wedding_id"=>$orderWedding->id,
                        "rooms_id"=>$request->rooms_id,
                        "extra_bed_id"=>$extra_bed->id,
                        "duration"=>$duration,
                        "price_pax"=>$exb_rate,
                        "total_price"=>$exb_rate * $duration,
                    ]);
                    $extra_bed_order->save();
                    $extra_bed_order_id = $extra_bed_order->id;
                }else{
                    $public_rate = $pub_rate;
                    $extra_bed_order_id = NULL;
                }
            }else{
                $extra_bed = ExtraBed::where('hotels_id',$orderWedding->hotel_id)->where('type',"Adult")->first();
                if ($extra_bed) {
                    $tax_rate = ceil(((($extra_bed->contract_rate / $usdrates->rate) + $extra_bed->markup)* $tax->tax) / 100);
                    $exb_rate = ceil(($extra_bed->contract_rate / $usdrates->rate) + $extra_bed->markup);
                    $public_rate = $pub_rate + ($exb_rate * $duration);
                    $extra_bed_order =new ExtraBedOrder([
                        "order_wedding_id"=>$orderWedding->id,
                        "rooms_id"=>$request->rooms_id,
                        "extra_bed_id"=>$extra_bed->id,
                        "duration"=>$duration,
                        "price_pax"=>$exb_rate,
                        "total_price"=>$exb_rate * $duration,
                    ]);
                    $extra_bed_order->save();
                    $extra_bed_order_id = $extra_bed_order->id;
                }else{
                    $public_rate = $pub_rate;
                    $extra_bed_order_id = NULL;
                }
            }
        }else{
            $public_rate = $pub_rate;
            $extra_bed_order_id = NULL;
        }
        $wedding_accommodation =new WeddingAccomodations([
            "order_wedding_package_id"=>$orderWedding->id,
            "room_for"=>$request->room_for,
            "hotels_id"=>$orderWedding->hotel_id,
            "rooms_id"=>$request->rooms_id,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "duration"=>$duration,
            "guest_detail"=>$guest_detail,
            "extra_bed_id"=>$extra_bed_order_id,
            "number_of_guests"=>$number_of_guests,
            "status"=>$status,
            "public_rate"=>$public_rate,
        ]);
        $wedding_accommodation->save();
        
        // FINAL PRICE 
        $weddingAccommodations = WeddingAccomodations::where('order_wedding_package_id',$orderWedding->id)->get();
        $accommodation_price = $weddingAccommodations->pluck('public_rate')->sum();
        $extra_bed_orders = ExtraBedOrder::where('order_wedding_id',$orderWedding->id)->get();
        $extra_bed_prices = $extra_bed_orders->pluck('total_price')->sum();

        if ($orderWedding->service == "Wedding Package") {
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $accommodation_price //proses
                + $extra_bed_prices //proses
                + $orderWedding->transport_invitations_price
                + $orderWedding->addser_price
                + $orderWedding->package_price
                + $orderWedding->markup;
        }else{
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $orderWedding->ceremony_venue_price
                + $orderWedding->ceremony_venue_decoration_price 
                + $orderWedding->reception_venue_price 
                + $orderWedding->reception_venue_decoration_price
                + $orderWedding->dinner_venue_price
                + $orderWedding->lunch_venue_price
                + $orderWedding->room_bride_price 
                + $accommodation_price //proses
                + $extra_bed_prices //proses
                + $orderWedding->transport_price
                + $orderWedding->transport_invitations_price
                + $makeup_price
                + $documentation_price
                + $entertainment_price
                + $orderWedding->additional_services_price
                + $orderWedding->addser_price
                + $orderWedding->markup;
        }
        $orderWedding->update([
            "accommodation_price"=>$accommodation_price,
            "extra_bed_price"=>$extra_bed_prices,
            "final_price"=>$final_price,
        ]);
        // dd($wedding_accommodation);
        return redirect("edit-order-wedding-$orderWedding->orderno#accommodations")->with('success','Accomodation has been added');
    }
    // OK FUNCTION UPDATE WEDDING ACCOMMODATION (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_update_order_wedding_accommodation(Request $request,$id){
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $wedding_accommodation = WeddingAccomodations::find($id);
        $orderWedding = OrderWedding::where('id',$wedding_accommodation->order_wedding_package_id)->first();
        $room_id = $request->rooms_id;
        if ($orderWedding->service == "Wedding Package") {
            $checkin = $orderWedding->checkin;
            $checkout = $orderWedding->checkout;
            $duration = $orderWedding->duration;
        }else{
            $checkin = date('Y-m-d', strtotime($request->checkin));
            $checkout = date('Y-m-d', strtotime($request->checkout));
            $in = Carbon::parse(date('Y-m-d', strtotime($checkin)));
            $out = Carbon::parse(date('Y-m-d', strtotime($checkout)));
            $duration = $in->diffInDays($out);
        }
        if ($request->room_for == "Couple") {
            $bride = Brides::where('id',$orderWedding->brides_id)->first();
            $guest_detail = $bride->groom.', '.$bride->bride;
            $number_of_guests = 2;
            $extra_bed_id = null;
        }else{
            $guest_detail = $request->guest_detail;
            $number_of_guests = $request->number_of_guests;
            $extra_bed_id = $request->extra_bed_id;
        }
        $room_id = $request->rooms_id;
        $hp = [];
        $cin = $checkin;
        
        for ($j=0; $j < $duration; $j++) { 
            $cin = date('Y-m-d',strtotime('+'.$j.' days',strtotime($cin)));
            $hotel_price = HotelPrice::where('rooms_id',$room_id)
            ->where('start_date','<=',$cin)
            ->where('end_date','>=',$cin)
            ->first();
            if ($hotel_price) {
                $rate_usd = $usdrates->rate;
                $cr = $hotel_price->contract_rate;
                $mrk = $hotel_price->markup;
                $cr_usd = ceil($cr / $rate_usd);
                $tx = ceil((($cr_usd + $mrk) * $tax->tax)/100);
                $pr = $cr_usd + $mrk + $tx;
                array_push($hp,$pr);
            }
        }
        $pub_rate = array_sum($hp);
        if ($number_of_guests>2) {
            if ($extra_bed_id) {
                $extra_bed = ExtraBed::where('id',$extra_bed_id)->first();
                if ($extra_bed) {
                    $tax_rate = ceil(((($extra_bed->contract_rate / $usdrates->rate) + $extra_bed->markup)* $tax->tax) / 100);
                    $exb_rate = ceil(($extra_bed->contract_rate / $usdrates->rate) + $extra_bed->markup);
                    $public_rate = $pub_rate + ($exb_rate * $duration);
                    $extra_bed_order =new ExtraBedOrder([
                        "order_wedding_id"=>$orderWedding->id,
                        "rooms_id"=>$request->rooms_id,
                        "extra_bed_id"=>$extra_bed->id,
                        "duration"=>$duration,
                        "price_pax"=>$exb_rate,
                        "total_price"=>$exb_rate * $duration,
                    ]);
                    $extra_bed_order->save();
                    $extra_bed_order_id = $extra_bed_order->id;
                }else{
                    $public_rate = $pub_rate;
                    $extra_bed_order_id = NULL;
                }
            }else{
                $extra_bed = ExtraBed::where('hotels_id',$orderWedding->hotel_id)->where('type',"Adult")->first();
                if ($extra_bed) {
                    $tax_rate = ceil(((($extra_bed->contract_rate / $usdrates->rate) + $extra_bed->markup)* $tax->tax) / 100);
                    $exb_rate = ceil(($extra_bed->contract_rate / $usdrates->rate) + $extra_bed->markup);
                    $public_rate = $pub_rate + ($exb_rate * $duration);
                    $extra_bed_order =new ExtraBedOrder([
                        "order_wedding_id"=>$orderWedding->id,
                        "rooms_id"=>$request->rooms_id,
                        "extra_bed_id"=>$extra_bed->id,
                        "duration"=>$duration,
                        "price_pax"=>$exb_rate,
                        "total_price"=>$exb_rate * $duration,
                    ]);
                    $extra_bed_order->save();
                    $extra_bed_order_id = $extra_bed_order->id;
                }else{
                    $public_rate = $pub_rate;
                    $extra_bed_order_id = NULL;
                }
            }
        }else{
            $extra_bed_order = ExtraBedOrder::where('id',$wedding_accommodation->extra_bed_id)->first();
            if ($extra_bed_order) {
                $extra_bed_order->delete();
            }
            $public_rate = $pub_rate;
            $extra_bed_order_id = NULL;
        }
        $wedding_accommodation->update([
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "rooms_id"=>$request->rooms_id,
            "guest_detail"=>$request->guest_detail,
            "extra_bed_id"=>$extra_bed_order_id,
            "number_of_guests"=>$request->number_of_guests,
            "public_rate"=>$public_rate,
        ]);
        
        // FINAL PRICE 
        $weddingAccommodations = WeddingAccomodations::where('order_wedding_package_id',$orderWedding->id)->get();
        $accommodation_price = $weddingAccommodations->pluck('public_rate')->sum();
        $extra_bed_orders = ExtraBedOrder::where('order_wedding_id',$orderWedding->id)->get();
        $extra_bed_price = $extra_bed_orders->pluck('total_price')->sum();

        if ($orderWedding->service == "Wedding Package") {
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $accommodation_price //proses
                + $extra_bed_price //proses
                + $orderWedding->transport_invitations_price
                + $orderWedding->addser_price
                + $orderWedding->package_price
                + $orderWedding->markup;
        }else{
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $orderWedding->ceremony_venue_price
                + $orderWedding->ceremony_venue_decoration_price 
                + $orderWedding->reception_venue_price 
                + $orderWedding->reception_venue_decoration_price
                + $orderWedding->dinner_venue_price
                + $orderWedding->lunch_venue_price
                + $orderWedding->room_bride_price 
                + $accommodation_price //proses
                + $extra_bed_price //proses
                + $orderWedding->transport_price
                + $orderWedding->transport_invitations_price
                + $makeup_price
                + $documentation_price
                + $entertainment_price
                + $orderWedding->additional_services_price
                + $orderWedding->addser_price
                + $orderWedding->markup;
        }
        $orderWedding->update([
            'accommodation_price'=>$accommodation_price,
            'extra_bed_price'=>$extra_bed_price,
            'final_price'=>$final_price,
        ]);
        return redirect("/edit-order-wedding-$orderWedding->orderno#accommodations")->with('success','Accomodation has been updated');
    }
    // OK FUNCTION DELETE ACCOMMODATION (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_delete_order_wedding_accommodation(Request $request,$id){
        $wedding_accommodation = WeddingAccomodations::find($id);
        $orderWedding = OrderWedding::where('id',$wedding_accommodation->order_wedding_package_id)->first();
        $extra_bed_order = ExtraBedOrder::where('id',$wedding_accommodation->extra_bed_id)->first();
        $wedding_accommodation->delete();
        if ($extra_bed_order) {
            $extra_bed_order->delete();
        }

        // FINAL PRICE 
        $weddingAccommodations = WeddingAccomodations::where('order_wedding_package_id',$orderWedding->id)->get();
        $accommodation_price = $weddingAccommodations->pluck('public_rate')->sum();
        $extra_bed_orders = ExtraBedOrder::where('order_wedding_id',$orderWedding->id)->get();
        $extra_bed_price = $extra_bed_orders->pluck('total_price')->sum();

        if ($orderWedding->service == "Wedding Package") {
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $accommodation_price //proses
                + $extra_bed_price //proses
                + $orderWedding->transport_invitations_price
                + $orderWedding->addser_price
                + $orderWedding->package_price
                + $orderWedding->markup;
        }else{
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $orderWedding->ceremony_venue_price
                + $orderWedding->ceremony_venue_decoration_price 
                + $orderWedding->reception_venue_price 
                + $orderWedding->reception_venue_decoration_price
                + $orderWedding->dinner_venue_price
                + $orderWedding->lunch_venue_price
                + $orderWedding->room_bride_price 
                + $accommodation_price //proses
                + $extra_bed_price //proses
                + $orderWedding->transport_price
                + $orderWedding->transport_invitations_price
                + $makeup_price
                + $documentation_price
                + $entertainment_price
                + $orderWedding->additional_services_price
                + $orderWedding->addser_price
                + $orderWedding->markup;
        }
        $orderWedding->update([
            'accommodation_price'=>$accommodation_price,
            'extra_bed_price'=>$extra_bed_price,
            'final_price'=>$final_price,
        ]);
        // dd($orderWedding,$accommodationPrice);
        return redirect("/edit-order-wedding-$orderWedding->orderno#accommodations")->with('success','Your order has been updated!');
    }

    // OK FUNCTION DELETE REMARK (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_delete_order_wedding_remark(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        $order_wedding->update([
            'remark'=> null,
        ]);
        // dd($order_wedding);
        return redirect("/edit-order-wedding-$order_wedding->orderno#remarkPage")->with('success','Your order has been updated!');
    }

    // FUNCTION ADD DECORATION TO CEREMONY VENUE
    public function func_add_decoration_to_ceremony_venue(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        $decoration_ceremony_venue = VendorPackage::where('id',$request->decoration_ceremony_venue_id)->first();
        $basic_or_arrangement = "Arrangement";
        $order_wedding->update([
            'basic_or_arrangement'=>$basic_or_arrangement,
            'decoration_ceremony_venue_id'=>$decoration_ceremony_venue->id,
            'ceremony_venue_decoration_price'=>$decoration_ceremony_venue->publish_rate,
        ]);
        dd($order_wedding);
        return redirect()->back()->with('success','Decoration has been added to the wedding order!');
    }

    // FUNCTION ADD ORDER WEDDING ADDITIONAL SERVICE DECORATION
    public function func_add_wedding_order_addser_decoration(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        $order_wedding->update([
            'wedding_decoration_id'=>$request->wedding_decoration_id,
        ]);
        // dd($order_wedding);
        return redirect()->back()->with('success','Decoration has been added to the wedding order!');
    }

    // FUNCTION CREATE ORDER CEREMONY VENUE (ORDER CEREMONY VENUE) =========================================================================================>
    public function func_add_order_reception_venue(Request $request,$id){
        $code = Str::random(26);
        // ORDERNO GENERATOR
            $tgl_sekarang = Carbon::now();
            $now = date("Y-m-d",strtotime($tgl_sekarang));
            if (Auth::user()->position == "weddingDvl" || Auth::user()->position == "weddingSls" || Auth::user()->position == "weddingAuthor" || Auth::user()->position == "weddingRsv") {
                $agent = Auth::user()->where('id',$request->user_id)->first();
            }else{
                $user_id = Auth::user()->id;
                $agent = Auth::user()->where('id',$user_id)->first();
            }
            $a = $agent->code.date('-ymd-',strtotime($now))."A";
            $b = $agent->code.date('-ymd-',strtotime($now))."B";
            $c = $agent->code.date('-ymd-',strtotime($now))."C";
            $d = $agent->code.date('-ymd-',strtotime($now))."D";
            $e = $agent->code.date('-ymd-',strtotime($now))."E";
            $f = $agent->code.date('-ymd-',strtotime($now))."F";
            $g = $agent->code.date('-ymd-',strtotime($now))."G";
            $h = $agent->code.date('-ymd-',strtotime($now))."H";
            $i = $agent->code.date('-ymd-',strtotime($now))."I";
            $j = $agent->code.date('-ymd-',strtotime($now))."J";
            $k = $agent->code.date('-ymd-',strtotime($now))."K";
            $l = $agent->code.date('-ymd-',strtotime($now))."L";
            $m = $agent->code.date('-ymd-',strtotime($now))."M";
            $n = $agent->code.date('-ymd-',strtotime($now))."N";
            $o = $agent->code.date('-ymd-',strtotime($now))."O";
            $p = $agent->code.date('-ymd-',strtotime($now))."P";
            $q = $agent->code.date('-ymd-',strtotime($now))."Q";
            $r = $agent->code.date('-ymd-',strtotime($now))."R";
            $s = $agent->code.date('-ymd-',strtotime($now))."S";
            $t = $agent->code.date('-ymd-',strtotime($now))."T";
            $u = $agent->code.date('-ymd-',strtotime($now))."U";
            $v = $agent->code.date('-ymd-',strtotime($now))."V";
            $w = $agent->code.date('-ymd-',strtotime($now))."W";
            $x = $agent->code.date('-ymd-',strtotime($now))."X";
            $y = $agent->code.date('-ymd-',strtotime($now))."Y";
            $z = $agent->code.date('-ymd-',strtotime($now))."Z";
            $orderw = OrderWedding::where('orderno', $a)
            ->orWhere('orderno', $b)
            ->orWhere('orderno', $c)
            ->orWhere('orderno', $d)
            ->orWhere('orderno', $e)
            ->orWhere('orderno', $f)
            ->orWhere('orderno', $g)
            ->orWhere('orderno', $h)
            ->orWhere('orderno', $i)
            ->orWhere('orderno', $j)
            ->orWhere('orderno', $k)
            ->orWhere('orderno', $l)
            ->orWhere('orderno', $m)
            ->orWhere('orderno', $n)
            ->orWhere('orderno', $o)
            ->orWhere('orderno', $p)
            ->orWhere('orderno', $q)
            ->orWhere('orderno', $r)
            ->orWhere('orderno', $s)
            ->orWhere('orderno', $t)
            ->orWhere('orderno', $u)
            ->orWhere('orderno', $v)
            ->orWhere('orderno', $w)
            ->orWhere('orderno', $x)
            ->orWhere('orderno', $y)
            ->orWhere('orderno', $z)
            ->get();
            $crsva = count($orderw);

            if ($crsva == 0) {
                $order_no = $a;
            }elseif($crsva == 1){
                $order_no = $b;
            }elseif($crsva == 2){
                $order_no = $c;
            }elseif($crsva == 3){
                $order_no = $d;
            }elseif($crsva == 4){
                $order_no = $e;
            }elseif($crsva == 5){
                $order_no = $f;
            }elseif($crsva == 6){
                $order_no = $g;
            }elseif($crsva == 7){
                $order_no = $h;
            }elseif($crsva == 8){
                $order_no = $i;
            }elseif($crsva == 9){
                $order_no = $j;
            }elseif($crsva == 10){
                $order_no = $k;
            }elseif($crsva == 11){
                $order_no = $l;
            }elseif($crsva == 12){
                $order_no = $m;
            }elseif($crsva == 13){
                $order_no = $n;
            }elseif($crsva == 14){
                $order_no = $o;
            }elseif($crsva == 15){
                $order_no = $p;
            }elseif($crsva == 16){
                $order_no = $q;
            }elseif($crsva == 17){
                $order_no = $r;
            }elseif($crsva == 18){
                $order_no = $s;
            }elseif($crsva == 19){
                $order_no = $t;
            }elseif($crsva == 20){
                $order_no = $u;
            }elseif($crsva == 21){
                $order_no = $v;
            }elseif($crsva == 22){
                $order_no = $w;
            }elseif($crsva == 23){
                $order_no = $x;
            }elseif($crsva == 24){
                $order_no = $y;
            }elseif($crsva == 25){
                $order_no = $z;
            }else{
                $order_no = $AA;
            }
        $orderno = $order_no;
        $service = "Reception Venue";
        $service_id = $id;
        $duration = 4;
        $groom = $request->groom_name;
        $bride = $request->bride_name;
        $number_of_invitation = $request->number_of_invitations;
        $groom_pasport_id =$request->groom_id;
        $bride_pasport_id =$request->brides_id;
        $reception_date_start = date('Y-m-d',strtotime($request->reception_date_start))." 18:00";
        $checkin = date('Y-m-d',strtotime($request->reception_date_start));
        $checkout = date('Y-m-d',strtotime($request->reception_date_start));
        $status = "Draft";
        $brides =new Brides([
            "groom"=>$groom,
            "groom_pasport_id"=>$groom_pasport_id,
            "bride"=>$bride,
            "bride_pasport_id"=>$bride_pasport_id,
        ]);
        $brides->save();
        $order_wedding_venue =new OrderWedding([
            "orderno"=>$orderno,
            "service"=>$service,
            "service_id"=>$service_id,
            "hotel_id"=>$request->hotel_id,
            "brides_id"=>$brides->id,
            "number_of_invitation"=>$number_of_invitation,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "reception_date_start"=>$reception_date_start,
            "reception_venue_id"=>$service_id,
            "reception_venue_price"=>$request->price,
            "reception_venue_invitations"=>$number_of_invitation,

            "final_price"=>$request->price,
            "agent_id"=>Auth::user()->id,
            "status"=>$status,
        ]);
        // dd($order_wedding_venue);
        $order_wedding_venue->save();
        return redirect("/edit-order-wedding-$orderno")->with('success', 'Your order has been created successfully');
    }
   
    // FUNCTION UPDATE ORDER WEDDING ADDITIONAL SERVICE DECORATION
    public function func_delete_wedding_order_addser_decoration(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        $order_wedding->update([
            'wedding_decoration_id'=>null,
        ]);
        // dd($order_wedding);
        return redirect()->back()->with('success','Decoration has been removed from wedding order!');
    }
    
    // FUNCTION DELETE WEDDING ORDER --------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_delete_order_wedding(Request $request,$id) {
        $wedding_order = OrderWedding::findOrFail($id);
        $wedding_order->delete();
        return redirect("/orders")->with('success','Order has been deleted!');
    }
    // FUNCTION DELETE ORDER WEDDING FLIGHT --------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_delete_order_wedding_flight(Request $request,$id) {
        $flight = Flights::find($id);
        $orderWedding = OrderWedding::where('id',$flight->order_wedding_id)->first();
        $flight->delete();
        return redirect("/edit-order-wedding-$orderWedding->orderno")->with('success','Flight schedule has been deleted!');
    }
    // FUNCTION DELETE ORDER WEDDING FLIGHT --------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_delete_invitation_to_order_wedding(Request $request,$id) {
        $invitation = WeddingInvitations::find($id);
        $orderWedding = OrderWedding::where('id',$invitation->order_wedding_id)->first();
        $invitation->delete();
        return redirect("/edit-order-wedding-$orderWedding->orderno#weddingInvitations")->with('success','Invitation has been deleted!');
    }
    
}
