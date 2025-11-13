<?php

namespace App\Http\Controllers;

use PDF;
use Image;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\User;
use App\Models\Guide;
use App\Models\Tours;
use App\Models\Brides;
use App\Models\Guests;
use App\Models\Hotels;
use App\Models\Orders;
use App\Models\Vendor;
use App\Models\Villas;
use App\Models\Wallet;
use App\Models\Drivers;
use App\Models\Flights;
use App\Models\LogData;
use App\Models\TaxDoku;
use App\Models\UserLog;
use App\Models\ExtraBed;
use App\Models\OrderLog;
use App\Models\UsdRates;
use App\Models\Weddings;
use App\Models\ActionLog;
use App\Models\Attention;
use App\Models\Countries;
use App\Models\HotelRoom;
use App\Models\Itinerary;
use App\Models\OrderNote;
use App\Models\HotelPrice;
use App\Models\TourPrices;
use App\Models\Transports;
use App\Models\BankAccount;
use App\Models\Reservation;
use Illuminate\Support\Str;
use App\Models\InvoiceAdmin;
use App\Models\OptionalRate;
use App\Models\OrderWedding;
use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Models\ExtraBedOrder;
use App\Models\VendorPackage;
use App\Models\WeddingVenues;
use App\Services\DokuService;
use App\Mail\ConfirmationMail;
use App\Models\AirportShuttle;
use App\Models\TransportPrice;
use App\Models\BusinessProfile;
use App\Models\AdditionalService;
use App\Models\OptionalRateOrder;
use App\Models\RemarkReservation;
use App\Models\DokuVirtualAccount;
use App\Models\ExcludeReservation;
use App\Models\IncludeReservation;
use App\Models\WeddingInvitations;
use App\Models\WeddingLunchVenues;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentConfirmation;
use App\Models\WeddingDinnerVenues;
use Illuminate\Support\Facades\Log;
use App\Models\WeddingAccomodations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Models\WeddingDinnerPackages;
use Illuminate\Support\Facades\Cache;
use App\Models\WeddingReceptionVenues;
use App\Models\WeddingPlannerTransport;
use Illuminate\Support\Facades\Storage;
use Google\Service\Dfareporting\Country;
use App\Models\WeddingAdditionalServices;
use App\Http\Requests\StoreactivitiesRequest;
use App\Http\Requests\UpdateactivitiesRequest;


class OrdersAdminController extends Controller
{
    protected $dokuService;

    public function __construct(DokuService $dokuService)
    {
        $this->dokuService = $dokuService;
        $this->middleware(['auth']);
        // $this->middleware(['auth','can:isAdmin']);
    }
    
    public function index()
    {
        $now = Carbon::now();
        $listed = date('Y-m-d',strtotime('-7 days',strtotime($now)));
        $business = BusinessProfile::where('id',1)->first();
        $activeorders=Orders::with('reservations.invoice.payment')
            ->where('status','active')
            ->where('checkin','>', $listed)
            ->orderBy('updated_at', 'ASC')
            ->get();
        $waitingorders=Orders::with('reservations')
            ->where('status','Pending')
            ->where('checkin','>', $listed)
            ->orderBy('updated_at', 'ASC')
            ->get();
        $rejectedorders=Orders::with('reservations')
            ->where('status','rejected')
            ->where('checkin','>', $listed)
            ->orderBy('updated_at', 'ASC')
            ->get();
        $invalidorders=Orders::with('reservations')
            ->where('status','invalid')
            ->where('checkin','>', $listed)
            ->orderBy('updated_at', 'ASC')
            ->get();
        $approvedorders=Orders::with('reservations')
            ->where('status','Approved')
            ->where('checkin','>', $listed)
            ->orderBy('updated_at', 'ASC')
            ->get();
        $paidorders=Orders::with('reservations')
            ->where('status','Paid')
            ->where('checkin','>', $listed)
            ->orderBy('updated_at', 'ASC')
            ->get();
        $confirmedorders=Orders::with('reservations')
            ->where('status','confirmed')
            ->where('checkin','>', $listed)
            ->orderBy('updated_at', 'ASC')
            ->get();
        
        $historyorders=Orders::where([['checkout','<=', $now]])
        ->orWhere([['status','Archive']])
        ->orderBy('updated_at', 'ASC')
        ->paginate(8);
       
        $attentions = Attention::where('page','orders-admin')->get();
        $users= User::all();
        $usdrates = UsdRates::where('id',1)->first();
        $reservations = Reservation::all();
        $invoices = InvoiceAdmin::all();
        $banks = BankAccount::all();

        $orderWeddings = OrderWedding::all();
        $bride = Brides::all();
        $additionalCharges = WeddingAdditionalServices::all();
        $transports = Transports::all();
        $weddingAccommodations = WeddingAccomodations::all();
        return view('admin.ordersadmin',[
            'now'=>$now,
            'usdrates'=>$usdrates,
            'reservations'=>$reservations,
            'invoices'=>$invoices,
            'banks'=>$banks,
            'users'=>$users,
            'attentions' => $attentions,
            'business'=>$business,
            "invalidorders" => $invalidorders,
            "waitingorders" => $waitingorders,
            "historyorders" => $historyorders,
            "rejectedorders" => $rejectedorders,
            "activeorders" => $activeorders,
            "approvedorders" => $approvedorders,
            "confirmedorders" => $confirmedorders,
            "paidorders" => $paidorders,
            "invoices" => $invoices,
            "listed" => $listed,
            "orderWeddings" => $orderWeddings,
            "additionalCharges" => $additionalCharges,
            "transports" => $transports,
            "weddingAccommodations" => $weddingAccommodations,
        ]);
    }
    // View Detail ORDER =========================================================================================>
    public function view_order_admin_detail($id)
    {
        $order = Orders::with(['optional_rate_orders','reservations.invoice'])->find($id);
        if (!$order) {
            return redirect('/orders-admin')->with('warning',"Sorry we couldn't find the order.");
        }
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $attentions = Attention::where('page',"admin-order-detail")->get();
        
        $rates = UsdRates::all();
        $banks = BankAccount::all();
        $admins = Auth::user()->all();
        $admin = Auth::user();
        $hotel = Hotels::with(['optionalrates'])->where('id',$order->service_id)->first();
        $optionalrates = $hotel->optionalrates??null;
        $villa = Villas::with(['optionalrates'])->where('id',$order->service_id)->first();
        $villa_optionalrates = $villa->optionalrates??null;
        $orderlogs = OrderLog::where('order_id',$id)->get();
        $guideOrder = Guide::where('id',$order->guide_id)->first();
        $driverOrder = Drivers::where('id',$order->driver_id)->first();
        $guides = Guide::All();
        $drivers = Drivers::All();
        $pickup_people = Guests::where('id',$order->pickup_name)->first();
        $airport_shuttles = AirportShuttle::where('order_id',$order->id)->get();

        $handled_by = User::where('id',$order->handled_by)->first();
        $extra_beds = ExtraBed::where('hotels_id',$order->service_id)->get();
        $agent = User::where('id',$order->sales_agent)->first();
        $optional_rate_orders = $order->optional_rate_orders;
        $optional_rate_order_total_price = $order->optional_rate_orders->sum('price_total');
        $users= User::where('id',$order->user_id)->first();
        $action_log = ActionLog::where('service_id',$id)
        ->where('service','Order')
        ->get();
        $additional_service = json_decode($order->additional_service);
        $additional_service_date = json_decode($order->additional_service_date);
        $additional_service_qty = json_decode($order->additional_service_qty);
        $additional_service_price = json_decode($order->additional_service_price);
        $total_additional_service = 0;
        if (!empty($additional_service)) {
            $total_additional_service = array_sum(
                array_map(function ($price, $qty) {
                    return $price * $qty;
                }, $additional_service_price, $additional_service_qty)
            );
        }
        if (!$order->rsv_id) {
            $rsv_no = $order->orderno;
            $reservation = new Reservation ([
                'rsv_no' =>$rsv_no,
                'checkin' =>$order->checkin,
                'checkout' =>$order->checkout,
                'agn_id'=>$agent->id,
                'adm_id'=>Auth::user()->id,
                'status'=>"Pending",
                'arrival_flight' =>$order->arrival_flight,
                'arrival_time' =>$order->arrival_time,
                'departure_flight'=>$order->departure_flight,
                'departure_time'=>$order->departure_time,
            ]);
            $reservation->save();
            if ($order->include) {
                $includeReservation = new IncludeReservation([
                    'rsv_id'=>$reservation->id,
                    'include'=>$order->include,
                ]);
                $includeReservation->save();
            }
            if (isset($order->note)) {
                $remarkReservation = new RemarkReservation([
                    'rsv_id'=>$reservation->id,
                    'remark'=>$order->note,
                ]);
                $remarkReservation->save();
            }
            $guests = Guests::where('order_id',$order->id)->get();
            $order->update([
                "rsv_id"=>$reservation->id,
            ]);
        }else{
            $reservation = Reservation::where('id',$order->rsv_id)->first();
            $guests = Guests::where('order_id',$order->id)->get();
        }
        $inv_no = "INV-".$reservation->rsv_no;
        if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf") or File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf")) {
            $status_contract = 1;
        }else{
            $status_contract = 0;
        }
        $invoice = InvoiceAdmin::where('rsv_id',$order->rsv_id)->first();
        $order_notes = OrderNote::where('order_id',$id)->get();
        if ($invoice) {
            $receipts = PaymentConfirmation::where('inv_id',$invoice->id)->get();
        }else{
            $receipts = null;
        }
        if ($receipts) {
            $total_payment_usd = $receipts->where('status', 'Valid')->where('kurs_name', 'USD')->sum('amount');
            $total_payment_cny = $receipts->where('status', 'Valid')->where('kurs_name', 'CNY')->sum('amount');
            $total_payment_twd = $receipts->where('status', 'Valid')->where('kurs_name', 'TWD')->sum('amount');
            $total_payment_idr = $receipts->where('status', 'Valid')->where('kurs_name', 'IDR')->sum('amount');
        }else{
            $total_payment_usd = 0;
            $total_payment_cny = 0;
            $total_payment_twd = 0;
            $total_payment_idr = 0;
        }

        $transports = Transports::where('status',"Active")->get();
        
        if ($order->service == "Wedding Package") {
            $wedding = Weddings::where('id', $order->service_id)->first();
            $wedding_order = OrderWedding::where('id', $order->wedding_order_id)->first();
            if ( $wedding_order) {
                $bride = Brides::where('id',$wedding_order->brides_id)->first();
            }else{
                $bride = null;
            }
            if ($hotel) {
                $wedding_room = HotelRoom::where('hotels_id',$hotel->id)->get();
            }else{
                $wedding_room = null;
            }
            $wedding_fixed_services = VendorPackage::where('type',"Fixed Service")->get();
            $wedding_venue = VendorPackage::where('type',"Wedding Venue")->get();
            $wedding_makeup = VendorPackage::where('type',"Make-up")->get();
            $wedding_decoration = VendorPackage::where('type',"Decoration")->get();
            $wedding_dinner_venue = VendorPackage::where('type',"Wedding Dinner")->get();
            $wedding_entertainment = VendorPackage::where('type',"Entertainment")->get();
            $wedding_documentation = VendorPackage::where('type',"Documentation")->get();
            $wedding_other_service = VendorPackage::where('type',"Other")->get();
            $wedding_transport = $transports;
            $wedding_transport_price = TransportPrice::where('type','Airport Shuttle')
            ->where('duration',$hotel->airport_duration)->get();
            $wedding_itineraries = Itinerary::where('order_id',$order->id)->orderBy('time','ASC')->get();
            $hotel_price = HotelPrice::where('hotels_id',$wedding_order->hotel_id)
            ->where('start_date','<', $wedding_order->wedding_date)
            ->where('end_date','>', $wedding_order->wedding_date)
            ->get();
        }else{
            $wedding = null;
            $wedding_order = null;
            $bride = null;
            $wedding_venue = null;
            $wedding_fixed_services = null;
            $wedding_room = null;
            $wedding_makeup = null;
            $wedding_decoration = null;
            $wedding_dinner_venue = null;
            $wedding_entertainment = null;
            $wedding_documentation = null;
            $wedding_other_service = null;
            $wedding_transport = null;
            $wedding_transport_price = null;
            $wedding_itineraries = null;
            $hotel_price = 0;
        }
        if ($invoice) {
            $doku_payment = DokuVirtualAccount::where('invoice_id', $invoice->id)
            ->where('expired_date','>=',$now)
            ->orderBy('expired_date','DESC')
            ->first();
            $doku_payment_paid = DokuVirtualAccount::where('invoice_id', $invoice->id)
            ->where('status','Paid')
            ->first();
            $dokuPayments = DokuVirtualAccount::where('invoice_id', $invoice->id)
            ->where('expired_date','>=',$now)
            ->get();
        }else{
            $doku_payment = null;
            $doku_payment_paid = null;
            $dokuPayments = null;
        }

        $guest_detail = json_decode($order->guest_detail);

        $nor = $order->number_of_room;
        $nogr = json_decode($order->number_of_guests_room);
        $special_day = json_decode($order->special_day);
        $special_date = json_decode($order->special_date);
        $extra_bed = json_decode($order->extra_bed);
        $r=1;
        $room_price_normal = $nor * $order->normal_price;
        $kick_back = $order->kick_back;
        $room_price_total = $nor * $order->normal_price;
        $final_price = $order->final_price;
        $extra_bed_price = json_decode($order->extra_bed_price);
        $extra_bed_id = json_decode($order->extra_bed_id);
        if ($nor or $order->number_of_guests < 1) {
            $order->optional_price = 0;
        }

        if (isset($order->promotion)){
            $promotion_name = json_decode($order->promotion);
            $promotion_disc = json_decode($order->promotion_disc);
            $total_promotion_disc = array_sum($promotion_disc);
            $cpn = count($promotion_name);
        }else{
            $total_promotion_disc = 0;
        }
        
        if (isset($extra_bed_price)) {
            $total_extra_bed = array_sum($extra_bed_price);
        }else{
            $total_extra_bed = 0;
        }
        $guest_pick_up = Guests::find($order->pickup_name)??null;
        $tours = Tours::all();
        return view('admin.ordersadmindetail',[
            'total_promotion_disc'=>$total_promotion_disc,
            'guest_detail'=>$guest_detail,
            'banks'=>$banks,
            'handled_by'=>$handled_by,
            'usdrates'=>$usdrates,
            'rates'=>$rates,
            'tax'=>$tax,
            'inv_no'=>$inv_no,
            'reservation'=>$reservation,
            'guests'=>$guests,
            'guest_pick_up'=>$guest_pick_up,
            'extra_beds'=>$extra_beds,
            'extra_bed'=>$extra_bed,
            'users'=>$users,
            'optionalrates'=>$optionalrates,
            'villa'=>$villa,
            'villa_optionalrates'=>$villa_optionalrates,
            'optional_rate_orders'=>$optional_rate_orders,
            'optional_rate_order_total_price'=>$optional_rate_order_total_price,
            'action_log' => $action_log,
            'now' => $now,
            'business'=>$business,
            'agent'=>$agent,
            'orderlogs'=>$orderlogs,
            'admin'=>$admin,
            'admins'=>$admins,
            'attentions'=>$attentions,
            'guides'=>$guides,
            'drivers'=>$drivers,
            'guideOrder'=>$guideOrder,
            'driverOrder'=>$driverOrder,
            'pickup_people'=>$pickup_people,
            'wedding_fixed_services'=>$wedding_fixed_services,
            'additional_service'=>$additional_service,
            'additional_service_date'=>$additional_service_date,
            'additional_service_price'=>$additional_service_price,
            'additional_service_qty'=>$additional_service_qty,
            'total_additional_service'=>$total_additional_service,
            'status_contract'=>$status_contract,
            'airport_shuttles'=>$airport_shuttles,
            'invoice'=>$invoice,
            'order_notes'=>$order_notes,
            'receipts'=>$receipts,
            'wedding'=>$wedding,
            'wedding_order'=>$wedding_order,
            'bride'=>$bride,
            'hotel'=>$hotel,
            'nogr'=>$nogr,
            'tours'=>$tours,
            'extra_bed_id'=>$extra_bed_id,
            'extra_bed_price'=>$extra_bed_price,
            
            'wedding_room'=>$wedding_room,
            'wedding_venue'=>$wedding_venue,
            'wedding_makeup'=>$wedding_makeup,
            'wedding_decoration'=>$wedding_decoration,
            'wedding_dinner_venue'=>$wedding_dinner_venue,
            'wedding_entertainment'=>$wedding_entertainment,
            'wedding_documentation'=>$wedding_documentation,
            'wedding_transport'=>$wedding_transport,
            'wedding_other_service'=>$wedding_other_service,
            'hotel_price'=>$hotel_price,
            'wedding_itineraries'=>$wedding_itineraries,
            'wedding_transport_price'=>$wedding_transport_price,
            'doku_payment'=>$doku_payment,
            'doku_payment_paid'=>$doku_payment_paid,
            'dokuPayments'=>$dokuPayments,
            'total_payment_usd' => $total_payment_usd,
            'total_payment_cny' => $total_payment_cny,
            'total_payment_twd' => $total_payment_twd,
            'total_payment_idr' => $total_payment_idr,
            'nor' => $nor,
            'special_day' => $special_day,
        ])->with('order',$order);
            
    }

    // Function Add Order Wedding Itinerary =========================================================================================>
    public function func_add_order_wedding_itinerary(Request $request, $id)
        {
            $now = Carbon::now();
            $today = date('Y-m-d H.i',strtotime($now));
            $order = Orders::findOrFail($id);
            $rsv_id = $order->rsv_id;
            $confirmation_order = $request->confirmation_order;
            $day_and_date = $request->day; 
            $day = substr($day_and_date,0,1);
            $date = substr($day_and_date,1,12);
            $duration = $request->duration;
            $time = date('H.i',strtotime($request->time));
            $author_id = Auth::user()->id;
            $any_action = Itinerary::where('created_at','>',$today)->get();
            $itineraries = new Itinerary([
                "author_id"=>$author_id,
                "rsv_id"=>$rsv_id,
                "order_id"=>$order->id,
                "date"=>$date,
                "time"=>$time,
                "day"=>$day,
                "duration"=>$duration,
                "itinerary"=>$request->itinerary,
            ]);
            $itineraries->save();
            
            if ($any_action) {
                $any_auth = $any_action->where('author_id',$author_id)->first();
                if (!$any_auth) {
                    $order_log = new OrderLog([
                        "order_id"=>$order->id,
                        "action"=>"Add Itinerary",
                        "url"=>$request->getClientIp(),
                        "method"=>"Add",
                        "agent"=>$order->name,
                        "admin"=>Auth::user()->id,
                    ]);
                    $order_log->save();
                }
            }else{
                $order_log = new OrderLog([
                    "order_id"=>$order->id,
                    "action"=>"Add Itinerary",
                    "url"=>$request->getClientIp(),
                    "method"=>"Add",
                    "agent"=>$order->name,
                    "admin"=>Auth::user()->id,
                ]);
                $order_log->save();
            }
            // dd($order);
            return redirect("/orders-admin-$id#additionalServices")->with('success','Order itinerary has been updated');
            // return redirect('/orders-admin-'.$id)->with('warning',"Sorry we couldn't find the order.");
        }

    // Update Order Itinerary =========================================================================================>
    public function func_update_order_itinerary(Request $request, $id)
    {
        $itinerary = Itinerary::findOrFail($id);
        $checkin = $request->checkin;
        $duration = $request->duration;
        $date_day = $checkin;
        $day_and_date = $request->day; 
        $day = substr($day_and_date,0,1);
        $date = substr($day_and_date,1,12);
        $duration = $request->duration;
        $time = date('H.i',strtotime($request->time));
        
        $itinerary->update([
            "date"=>$date,
            "time"=>$time,
            "day"=>$day,
            "duration"=>$duration,
            "itinerary"=>$request->itinerary,
        ]);
        // dd($itinerary);
        return redirect()->back()->with('success','Itinerary has been updated');
    }
    
    // Function Delete Guide to Order =========================================================================================>
    public function func_delete_order_wedding_itinerary(Request $request, $id)
    {
        $itinerary = Itinerary::findOrFail($id);
        $itinerary->delete();
        // @dd($order);
        return redirect()->to(app('url')->previous()."#weddingItinerary")->with('success','Service has been removed from itinerary');
    }
    // View detail order room =========================================================================================>
    public function admin_edit_order_room($id)
    {
        $order = Orders::where('id',$id)->first();
        if ($order->handled_by) {
            if ($order->handled_by == Auth::user()->id) {
                if ($order->status != "Approved") {
                    $now = Carbon::now();
                    $user_id = Auth::User()->id;
                    $usdrates = UsdRates::where('name','USD')->first();
                    $tax = Tax::where('id',1)->first();
                    $attentions = Attention::where('page','editorder-room')->get();
                    
                    $business = BusinessProfile::where('id',1)->first();
                    $extrabed = ExtraBed::where('hotels_id',$order->service_id)->get();
                    $room = HotelRoom::where('id',$order->subservice_id)->first();
                    if (!$order->handled_by) {
                        $handled_by = Auth::user()->id;
                    }else{
                        $handled_by = $order->handled_by;
                    }
                    return view('admin.admin-edit-order-room',compact('order'),[
                        'extrabed'=>$extrabed,
                        'room'=>$room,
                        'tax'=>$tax,
                        'now'=>$now,
                        'usdrates'=>$usdrates,
                        'business'=>$business,
                        'attentions'=>$attentions,
                        'handled_by'=>$handled_by,
                    ]);
                }else{
                    return redirect("/orders-admin-$id")->with('success','You can not change anything!');
                }
            }
        }else{
            if ($order->status != "Approved") {
                $now = Carbon::now();
                $user_id = Auth::User()->id;
                $usdrates = UsdRates::where('name','USD')->first();
                $tax = Tax::where('id',1)->first();
                $attentions = Attention::where('page','editorder-room')->get();
                $business = BusinessProfile::where('id',1)->first();
                $extrabed = ExtraBed::where('hotels_id',$order->service_id)->get();
                $room = HotelRoom::where('id',$order->subservice_id)->first();
                if (!$order->handled_by) {
                    $handled_by = Auth::user()->id;
                }else{
                    $handled_by = $order->handled_by;
                }
                return view('admin.admin-edit-order-room',compact('order'),[
                    'extrabed'=>$extrabed,
                    'room'=>$room,
                    'tax'=>$tax,
                    'now'=>$now,
                    'usdrates'=>$usdrates,
                    'business'=>$business,
                    'attentions'=>$attentions,
                    'handled_by'=>$handled_by,
                ]);
            }else{
                return redirect("/orders-admin-$id")->with('success','You can not change anything!');
            }
        }
    }
    // VIEW UPDATE ORDER WEDDING SERVICE =========================================================================================>
    public function view_update_wedding_service($id){
        $order = Orders::where('id',$id)->first();
        $taxes = Tax::where('id',1)->first();
        $wedding = Weddings::findOrFail($order->service_id);
        $wedding_order = OrderWedding::where('id',$order->wedding_order_id)->first();
        $wedding_venues = VendorPackage::where('status', "Active")->where('type','Wedding Venue')->where('hotel_id',$wedding_order->hotel_id)->get();
        $wedding_rooms = HotelRoom::where('status', "Active")->where('hotels_id',$wedding_order->hotel_id)->get();
        $wedding_makeups = VendorPackage::where('status', "Active")->where('type',"Make-up")->get();
        $wedding_decorations = VendorPackage::where('status', "Active")->where('type',"Decoration")->get();
        $wedding_dinner_venues = VendorPackage::where('status', "Active")->where('type',"Wedding Dinner")->get();
        $wedding_entertainments = VendorPackage::where('status', "Active")->where('type',"Entertainment")->get();
        $wedding_documentations = VendorPackage::where('status', "Active")->where('type',"Documentation")->get();
        $wedding_transports = Transports::where('status', "Active")->get();
        $wedding_others = VendorPackage::where('status', "Active")->where('type',"Other")->get();
        $transport_price = TransportPrice::all();
        $room_price = HotelPrice::where('hotels_id',$wedding_order->hotel_id)->get();
        $suite_and_villas = HotelRoom::where('status', "Active")->where('hotels_id',$wedding_order->hotel_id)->get();
        $vendors = Vendor::where('status','Active')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $hotels = Hotels::where('status', 'Active')->get();
        $hotel_price = HotelPrice::where('hotels_id',$wedding_order->hotel_id)
            ->where('start_date','<', $wedding_order->wedding_date)
            ->where('end_date','>', $wedding_order->wedding_date)
            ->get();
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        if ($order->handled_by) {
            if ($order->handled_by == Auth::user()->id) {
                if ($order->status != "Approved") {
                    return view('admin.update-wedding-services',[
                        'order'=>$order,
                        'wedding'=>$wedding,
                        'wedding_venues'=>$wedding_venues,
                        'vendors'=>$vendors,
                        'usdrates'=>$usdrates,
                        'taxes'=>$taxes,
                        'hotels'=>$hotels,
                        'wedding_order'=>$wedding_order,
                        'suite_and_villas'=>$suite_and_villas,
                        'room_price'=>$room_price,
                        'transport_price'=>$transport_price,
                        'wedding_rooms'=>$wedding_rooms,
                        'wedding_makeups'=>$wedding_makeups,
                        'wedding_decorations'=>$wedding_decorations,
                        'wedding_dinner_venues'=>$wedding_dinner_venues,
                        'wedding_entertainments'=>$wedding_entertainments,
                        'wedding_documentations'=>$wedding_documentations,
                        'wedding_transports'=>$wedding_transports,
                        'wedding_others'=>$wedding_others,
                        'hotel_price'=>$hotel_price,
                        'handled_by'=>$handled_by,
                    ]);
                }else{
                    return redirect("/orders-admin-$id")->with('success','The order have been activated, you can not change anything!');
                }
            }else{
                return redirect("/orders-admin-$id")->with('success','You can not change anything!');
            }
        }else{
            if ($order->status != "Approved") {
                return view('admin.update-wedding-services',[
                    'order'=>$order,
                    'wedding'=>$wedding,
                    'wedding_venues'=>$wedding_venues,
                    'vendors'=>$vendors,
                    'usdrates'=>$usdrates,
                    'taxes'=>$taxes,
                    'hotels'=>$hotels,
                    'wedding_order'=>$wedding_order,
                    'suite_and_villas'=>$suite_and_villas,
                    'room_price'=>$room_price,
                    'transport_price'=>$transport_price,
                    'wedding_rooms'=>$wedding_rooms,
                    'wedding_makeups'=>$wedding_makeups,
                    'wedding_decorations'=>$wedding_decorations,
                    'wedding_dinner_venues'=>$wedding_dinner_venues,
                    'wedding_entertainments'=>$wedding_entertainments,
                    'wedding_documentations'=>$wedding_documentations,
                    'wedding_transports'=>$wedding_transports,
                    'wedding_others'=>$wedding_others,
                    'hotel_price'=>$hotel_price,
                    'handled_by'=>$handled_by,
                ]);
            }else{
                return redirect("/orders-admin-$id")->with('success','The order have been activated, you can not change anything!');
            }
        }
        
    }
// UPDATE FLIGHT ==================================================================================================================================================================================
    public function func_update_flight(Request $request, $id)
    {
        $order=Orders::findOrFail($id);
        $reservation=Reservation::where('id',$order->rsv_id)->first();
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $order->update([
            'arrival_flight' =>$request->arrival_flight,
            'arrival_time' =>$request->arrival_time,
            'departure_flight'=>$request->departure_flight,
            'departure_time'=>$request->departure_time,
            'handled_by'=>$handled_by,
        ]);
        $reservation->update([
            'arrival_flight' =>$request->arrival_flight,
            'arrival_time' =>$request->arrival_time,
            'departure_flight'=>$request->departure_flight,
            'departure_time'=>$request->departure_time,
        ]);
        // @dd($guest);
        return redirect()->back()->with('success','Flight has been update to the order');
        // return redirect()->back()->with('error','Flight cannot be added, please check your form!');
    }
// UPDATE LUNCH VENUE ==================================================================================================================================================================================
    public function admin_func_update_lunch_venue(Request $request, $id)
    {
        $orderWedding = OrderWedding::findOrFail($id);
        $lunch_venue = WeddingLunchVenues::where('id',$request->lunch_venue_id)->first();
        $lunch_venue_price = $lunch_venue->publish_rate;
        $lunch_venue_date = date('Y-m-d H:i',strtotime($request->lunch_venue_date));
        $orderWedding->update([
            'lunch_venue_id' =>$request->lunch_venue_id,
            'lunch_venue_price' =>$lunch_venue_price,
            'lunch_venue_date' =>$lunch_venue_date,
        ]);
        return redirect()->back()->with('success','Lunch Venue has been update to the order');
    }
// DELETE LUNCH VENUE ==================================================================================================================================================================================
    public function admin_func_delete_lunch_venue(Request $request, $id)
    {
        $orderWedding = OrderWedding::findOrFail($id);
        $lunch_venue = NULL;
        $lunch_venue_price = NULL;
        $lunch_venue_date = NULL;
        $orderWedding->update([
            'lunch_venue_id' => NULL,
            'lunch_venue_price' =>$lunch_venue_price,
            'lunch_venue_date' =>$lunch_venue_date,
        ]);
        return redirect()->back()->with('success','Lunch Venue has been remove from the order');
    }
// Function edit Pick up and Drop off =========================================================================================>
    public function func_update_pickup_dropoff(Request $request, $id)
        {
            $order = Orders::findOrFail($id);
            if (!$order->handled_by) {
                $handled_by = Auth::user()->id;
            }else{
                $handled_by = $order->handled_by;
            }
            $order->update([
                "pickup_date"=>$request->pickup_date,
                "pickup_location"=>$request->pickup_location,
                "dropoff_date"=>$request->dropoff_date,
                "dropoff_location"=>$request->dropoff_location,
                "handled_by"=>$handled_by,
            ]);
            // @dd($order);
            return redirect()->back()->with('success','Pick up and Drop off has been updated');
        }
// Function Add Guide to Order =========================================================================================>
    public function func_add_guide_order(Request $request, $id)
        {
            $order = Orders::findOrFail($id);
            $reservation=Reservation::where('id',$order->rsv_id)->first();
            if (!$order->handled_by) {
                $handled_by = Auth::user()->id;
            }else{
                $handled_by = $order->handled_by;
            }
            $order->update([
                "guide_id"=>$request->guide_id,
                "handled_by"=>$handled_by,
            ]);
            $reservation->update([
                "guide_id"=>$request->guide_id,
            ]);
            // @dd($order);
            $order_log =new OrderLog([
                "order_id"=>$order->id,
                "action"=>"Add Guide",
                "url"=>$request->getClientIp(),
                "method"=>"Add",
                "agent"=>$order->name,
                "admin"=>Auth::user()->id,
            ]);
            $order_log->save();
            return redirect()->back()->with('success','Guide has been add to the order');
        }
// Function Edit Guide to Order =========================================================================================>
    public function func_edit_guide_order(Request $request, $id)
        {
            $order = Orders::findOrFail($id);
            $reservation=Reservation::where('id',$order->rsv_id)->first();
            if (!$order->handled_by) {
                $handled_by = Auth::user()->id;
            }else{
                $handled_by = $order->handled_by;
            }
            $order->update([
                "guide_id"=>$request->guide_id,
                "handled_by"=>$handled_by,
            ]);
            $reservation->update([
                "guide_id"=>$request->guide_id,
            ]);
            $order_log =new OrderLog([
                "order_id"=>$order->id,
                "action"=>"Update Guide",
                "url"=>$request->getClientIp(),
                "method"=>"Update",
                "agent"=>$order->name,
                "admin"=>Auth::user()->id,
            ]);
            $order_log->save();
            // @dd($order);
            return redirect()->back()->with('success','Guide has been change');
        }
// Function Delete Guide to Order =========================================================================================>
    public function func_delete_guide_order(Request $request, $id)
        {
            $admin = Auth::user();
            $order = Orders::findOrFail($id);
            $reservation=Reservation::where('id',$order->rsv_id)->first();
            $guide = null;
            if (!$order->handled_by) {
                $handled_by = Auth::id();
            }else{
                $handled_by = $order->handled_by;
            }
            $order->update([
                "guide_id"=>$guide,
                "handled_by"=>$handled_by,
            ]);
            $reservation->update([
                "guide_id"=>$guide,
            ]);
            // @dd($order);
            $order_log =new OrderLog([
                "order_id"=>$order->id,
                "action"=>"Delete Guide",
                "url"=>$request->getClientIp(),
                "method"=>"Delete",
                "agent"=>$order->name,
                "admin"=>$admin->id,
            ]);
            return redirect()->back()->with('success','Guide has been removed from the order');
        }
// Function Add Driver to Order =========================================================================================>
    public function func_add_driver_order(Request $request, $id)
        {
            $order = Orders::findOrFail($id);
            $reservation=Reservation::where('id',$order->rsv_id)->first();
            if (!$order->handled_by) {
                $handled_by = Auth::user()->id;
            }else{
                $handled_by = $order->handled_by;
            }
            $order->update([
                "driver_id"=>$request->driver_id,
                "handled_by"=>$handled_by,
            ]);
            $reservation->update([
                "driver_id"=>$request->driver_id,
            ]);
            // @dd($order);
            $order_log =new OrderLog([
                "order_id"=>$order->id,
                "action"=>"Add Driver",
                "url"=>$request->getClientIp(),
                "method"=>"Add",
                "agent"=>$order->name,
                "admin"=>Auth::user()->id,
            ]);
            $order_log->save();
            return redirect()->back()->with('success','Driver has been add to the order');
        }
// Function Edit Driver to Order =========================================================================================>
    public function func_edit_driver_order(Request $request, $id)
        {
            $order = Orders::findOrFail($id);
            $reservation=Reservation::where('id',$order->rsv_id)->first();
            if (!$order->handled_by) {
                $handled_by = Auth::user()->id;
            }else{
                $handled_by = $order->handled_by;
            }
            $order->update([
                "driver_id"=>$request->driver_id,
                "handled_by"=>$handled_by,
            ]);
            $reservation->update([
                "driver_id"=>$request->driver_id,
            ]);
            // @dd($order);
            $order_log =new OrderLog([
                "order_id"=>$order->id,
                "action"=>"Update Driver",
                "url"=>$request->getClientIp(),
                "method"=>"Update",
                "agent"=>$order->name,
                "admin"=>Auth::user()->id,
            ]);
            $order_log->save();
            return redirect()->back()->with('success','Driver has been change');
        }
// Function Delete Driver to Order =========================================================================================>
    public function func_delete_driver_order(Request $request, $id)
        {
            $admin = Auth::user();
            $order = Orders::findOrFail($id);
            $reservation=Reservation::where('id',$order->rsv_id)->first();
            $driver = null;
            if (!$order->handled_by) {
                $handled_by = Auth::id();
            }else{
                $handled_by = $order->handled_by;
            }
            $order->update([
                "driver_id"=>$driver,
                "handled_by"=>$handled_by,
            ]);
            $reservation->update([
                "driver_id"=>$driver,
            ]);
            // @dd($order);
            $order_log =new OrderLog([
                "order_id"=>$order->id,
                "action"=>"Delete Driver",
                "url"=>$request->getClientIp(),
                "method"=>"Delete",
                "agent"=>$order->name,
                "admin"=>$admin->id,
            ]);
            $order_log->save();
            return redirect()->back()->with('success','Driver has been removed from the order');
        }
    // Function Edit Confirmation Order =========================================================================================>
    public function func_add_confirmation_order(Request $request, $id)
        {
            $order = Orders::findOrFail($id);
            $confirmation_order = $request->confirmation_order;
            if (!$order->handled_by) {
                $handled_by = Auth::user()->id;
            }else{
                $handled_by = $order->handled_by;
            }
            $order->update([
                "confirmation_order"=>$confirmation_order,
                "handled_by"=>$handled_by,
            ]);
            // @dd($order);
            return redirect()->back()->with('success','Driver has been change');
        }

    // Function Edit Confirmation Order =========================================================================================>
    public function func_edit_confirmation_order(Request $request, $id)
        {
            $order = Orders::findOrFail($id);
            $confirmation_order = $request->confirmation_order;
            if (!$order->handled_by) {
                $handled_by = Auth::user()->id;
            }else{
                $handled_by = $order->handled_by;
            }
            $order->update([
                "confirmation_order"=>$confirmation_order,
                "handled_by"=>$handled_by,
            ]);
            // @dd($order);
            return redirect()->back()->with('success','Driver has been change');
        }
    // Function Update Order Room =============================================================================================================>
    public function func_admin_update_order_room(Request $request,$id){
        $order=Orders::findOrFail($id);
        $croom = count($request->number_of_guests_room);
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $duration = $order->duration;
        $optional_price = $order->optional_price;
        $price_pax = $order->price_pax;
        $kb = ($order->kick_back_per_pax * $croom) * $order->duration ;
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        if (isset($kb)) {
            if ($kb > 0) {
                $kick_back = $kb;
            }else{
                $kick_back = 0;
            }
        }else{
            $kick_back = 0;
        }
        if ($croom > 0) {
            $number_of_guests = array_sum($request->number_of_guests_room);
            $extra_bed_proses = [];
            foreach ($request->number_of_guests_room as $jk) {
                if ($jk > $order->capacity ) {
                    array_push($extra_bed_proses,'Yes');
                }else{
                    array_push($extra_bed_proses,'No');
                }
            }
            $extra_bed_id_price = [];
            $extraBedId = [];
            for ($i=0; $i < $croom; $i++) {
                if ($extra_bed_proses[$i] == "Yes") {
                    if ($request->extra_bed_id[$i] == 0) {
                        $extrabeds = ExtraBed::where('hotels_id',$order->service_id)->first();
                        if ($extrabeds) {
                            $price_extra_bed = ($extrabeds->calculatePrice($usdrates,$tax))*$duration;
                            array_push($extra_bed_id_price,$price_extra_bed);
                            array_push($extraBedId,$extrabeds->id);
                        }else{
                            array_push($extra_bed_id_price,0);
                            array_push($extraBedId,0);
                        }
                    }else{
                        $extrabeds = ExtraBed::where('id',$request->extra_bed_id[$i])->first();
                        $price_extra_bed = ($extrabeds->calculatePrice($usdrates,$tax))*$duration;
                        array_push($extra_bed_id_price,$price_extra_bed);
                        array_push($extraBedId,$extrabeds->id);
                    }
                }else{
                    array_push($extra_bed_id_price,0);
                    array_push($extraBedId,0);
                }
            }
            $extra_bed_total_price = array_sum($extra_bed_id_price);
            $extra_bed_id = json_encode($extraBedId);
            $extra_bed_price = json_encode($extra_bed_id_price);
            $extra_bed = json_encode($extra_bed_proses);
            $guest_detail = json_encode($request->guest_detail);
            $special_day = json_encode($request->special_day);
            $special_date = json_encode($request->special_date);

            $promotionDiscount = json_decode($order->promotion_disc);
            if (isset($promotionDiscount)) {
                $countPromotionDiscount = array_sum($promotionDiscount);
            }else{
                $countPromotionDiscount = 0;
            }
            if (isset($order->additional_service_qty)) {
                if ($order->additional_service_qty != "null") {
                   
                    $aser_qty = json_decode($order->additional_service_qty);
                    $aser_price = json_decode($order->additional_service_price);
                    $caser_qty = count($aser_qty);
                    $tpaser = [];
                    for ($aser=0; $aser < $caser_qty ; $aser++) { 
                            $pr_aser = $aser_price[$aser] * $aser_qty[$aser];
                            array_push($tpaser, $pr_aser);
                    }
                    $total_Price_additional_service = array_sum($tpaser);
                }else{
                    $total_Price_additional_service = 0;
                }
            }else{
                $total_Price_additional_service = 0;
            }

            $number_of_guests_room = json_encode($request->number_of_guests_room);
            $total_extra_bed = array_sum($extra_bed_id_price);
            $normal_price = ($price_pax * $croom);
            $price_total = ($normal_price - $kick_back) + $total_extra_bed;
            $final_price = (($price_total + $optional_price) - $order->discounts - $order->bookingcode_disc - $countPromotionDiscount)+$total_Price_additional_service + $order->airport_shuttle_price;
        }else{
            $number_of_guests = 0;
            $number_of_guests_room = 0;
            $croom = 0;
            $extra_bed_proses = 0;
            $extra_bed_id = 0;
            $extra_bed_price = 0;
            $extra_bed = 0;
            $price_total = 0;
            $kick_back = 0;
            $guest_detail = 0;
            $special_day = 0;
            $special_date = 0;
            $normal_price = 0;
            $final_price = 0;
        }
        $order->update([
            "number_of_guests"=>$number_of_guests,
            "number_of_guests_room"=>$number_of_guests_room,
            "number_of_room"=>$croom,
            "guest_detail"=>$guest_detail,
            "request_quotation"=>$request->request_quotation,
            "extra_bed"=>$extra_bed,
            "extra_bed_id"=>$extra_bed_id,
            "extra_bed_price"=>$extra_bed_price,
            "extra_bed_total_price"=>$extra_bed_total_price,
            "special_day"=>$special_day,
            "special_date"=>$special_date,
            "normal_price"=>$normal_price,
            "price_total"=>$price_total,
            "final_price"=>$final_price,
            "kick_back"=>$kick_back,
            "handled_by"=>$handled_by,
        ]);
        // dd([$order]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Update Room",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        // Log::channel('orderlog')->info(Auth::user()->name." Edit Room Order");
        
       
        return redirect("/orders-admin-$id")->with('success','Your order has been updated');
    }
    // View Add Optional Rate Order =========================================================================================>
    public function add_optional_rate_order($id){
        $order = Orders::where('id', $id)->first();
        $attentions = Attention::where('page','orders-detail')->get();
        $business = BusinessProfile::where('id',1)->first();
        $optional_rate_orders = OptionalRateOrder::where('orders_id', $id)->get();
        $optionalrates = OptionalRate::all();
        $optional_services = OptionalRate::where('hotels_id', $order->service_id)
            ->orWhere('villas_id',$order->service_id)
            ->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        if ($order->handled_by) {
            if ($order->handled_by == Auth::user()->id) {
                if ($order->status != "Approved") {
                    return view('admin.additional_service_add',compact('order'),[
                        'usdrates'=>$usdrates,
                        'order'=> $order,
                        'business'=>$business,
                        'optional_rate_orders'=>$optional_rate_orders,
                        'attentions'=>$attentions,
                        'optionalrates'=>$optionalrates,
                        'optional_services'=>$optional_services,
                        'tax'=>$tax,
                        'usdrates'=>$usdrates,
                    ]);
                }else{
                    return redirect("/orders-admin-$id")->with('success','You can not change anything!');
                }
            }else{
                return redirect("/orders-admin-$id")->with('success','You can not change anything!');
            }
        }else{
            if ($order->status != "Approved") {
                return view('admin.additional_service_add',compact('order'),[
                    'usdrates'=>$usdrates,
                    'order'=> $order,
                    'business'=>$business,
                    'optional_rate_orders'=>$optional_rate_orders,
                    'attentions'=>$attentions,
                    'optionalrates'=>$optionalrates,
                    'optional_services'=>$optional_services,
                    'tax'=>$tax,
                    'usdrates'=>$usdrates,
                ]);
            }
        }
        
    }

    // Function Remove Optional Rate Order =============================================================================================================>
    public function remove_optional_rate_order($id)
    {
        $optionalRateOrder = OptionalRateOrder::find($id);

        if (!$optionalRateOrder) {
            return response()->json(['status' => 'error', 'message' => 'Data not found.'], 404);
        }

        // Optional: Tambahkan pengecekan jika perlu
        if ($optionalRateOrder->mandatory == 1) {
            return response()->json(['status' => 'error', 'message' => 'This item cannot be deleted.'], 403);
        }

        $order_id = $optionalRateOrder->orders_id;
        $optionalRateOrder->delete();

        $order = Orders::find($order_id);
        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found.'], 404);
        }

        // Hitung ulang harga
        $extra_bed_total_price = $order->extra_bed_total_price ?? 0;
        $airport_shuttle_price = $order->airport_shuttle_price ?? 0;
        $additional_service_total_price = $order->additional_service_total_price??0;
        $optional_price = OptionalRateOrder::where('orders_id', $order_id)->sum('price_total');
        
        $price_total = $order->normal_price + $extra_bed_total_price + $airport_shuttle_price + $additional_service_total_price + $optional_price;

        $kick_back = $order->kick_back;
        $discounts = $order->discounts;
        $bookingcode_disc = $order->bookingcode_disc ?? 0;
        $promotion_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc, true)) : 0;

        $final_price = $price_total - $kick_back - $discounts - $bookingcode_disc - $promotion_discount;

        $order->update([
            "optional_price" => $optional_price,
            "price_total" => $price_total,
            "final_price" => $final_price,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Data deleted successfully.']);
    }

    // Function Updated Optional Service / Additional Charge =============================================================================================================>
    public function func_update_optional_service_order(Request $request,$id){
        $now = Carbon::now();
        $order = Orders::find($id);
        $optionalrateorder=OptionalRateOrder::where("orders_id",$id)->first();
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $price_pax_nd = [];
        $price_total_nd = [];
        if ($order->handled_by) {
            $handled_by = $order->handled_by;
        }else{
            $handled_by = Auth::user()->id;
        }
        DB::transaction(function () use ($request, $order, $usdrates, $handled_by, $tax, $id, $now) {
            $service = $order->service;
            $total_optional_rate = 0;
            $add_optional_rate_order = [];
            $optional_rate_orders = collect($request->optional_rate_order_id ?? []);
            $optional_rate_ids = collect($request->optional_rate_id);
            $number_of_guests = collect($request->number_of_guest);
            $service_dates = collect($request->service_date);
            foreach ($optional_rate_ids as $index => $optional_rate_id) {
                $optional_rate_order = OptionalRateOrder::find($optional_rate_orders[$index] ?? null);
                $optional_rate = OptionalRate::find($optional_rate_id);
                if (!$optional_rate) {
                    continue;
                }
                $price_pax = $optional_rate->calculatePrice($usdrates, $tax);
                $price_total = $price_pax * $number_of_guests[$index];
                if ($optional_rate_order) {
                    $optional_rate_order->update([
                        'optional_rate_id' => $optional_rate_id,
                        'number_of_guest' => $number_of_guests[$index],
                        'service_date' => $service_dates[$index],
                        'price_pax' => $price_pax,
                        'price_total' => $price_total,
                    ]);
                } else {
                    $add_optional_rate_order[] = [
                        'optional_rate_id' => $optional_rate_id,
                        'service' => $service,
                        'orders_id' => $id,
                        'number_of_guest' => $number_of_guests[$index],
                        'service_date' => $service_dates[$index],
                        'price_pax' => $price_pax,
                        'price_total' => $price_total,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $total_optional_rate += $price_total;
            }
            if (!empty($add_optional_rate_order)) {
                OptionalRateOrder::insert($add_optional_rate_order);
            }

            $extra_bed_total_price = $order->extra_bed_total_price ?? 0;
            $airport_shuttle_price = $order->airport_shuttle_price ?? 0;
            $additional_service_total_price = $order->additional_service_total_price??0;
            $price_total = $order->normal_price + $extra_bed_total_price + $airport_shuttle_price + $additional_service_total_price + $total_optional_rate;
            $kick_back = $order->kick_back;
            $discounts = $order->discounts;
            $bookingcode_disc = $order->bookingcode_disc ?? 0;
            $promotion_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc, true)) : 0;
            $final_price = $price_total - $kick_back - $discounts - $bookingcode_disc - $promotion_discount;

            $order->update([
                "optional_price" => $total_optional_rate,
                "final_price" => $final_price,
                "price_total" => $price_total,
                "handled_by"=>$handled_by,
                "handled_date"=>$now,
            ]);
            $order_log =new OrderLog([
                "order_id"=>$order->id,
                "action"=>"Update Additional Charge",
                "url"=>$request->getClientIp(),
                "method"=>"Update",
                "agent"=>$order->name,
                "admin"=>Auth::user()->id,
            ]);
            $order_log->save();
        });
        //dd($order);
        return redirect("/orders-admin-$id#additionalCharge")->with('success','Additional Charge has been updated');
    }
    // Function Add Order Optional Service / Additional Charge =============================================================================================================>
    public function func_add_optional_service_order(Request $request,$id){
        $now = Carbon::now();
        $order = Orders::find($id);
        $or_nog = json_encode($request->optional_rate_id);
        $optional_rate_id = json_encode($request->optional_rate_id);
        $number_of_guest = json_encode($request->number_of_guest);
        $service_date = json_encode($request->service_date);
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        if ($order->handled_by) {
            $handled_by = $order->handled_by;
        }else{
            $handled_by = Auth::user()->id;
        }
        if ($order->service == "Hotel" || $order->service == "Hotel Package" || $order->service == "Hotel Promo") {
            $optional_rates = OptionalRate::where('hotels_id', $order->service_id)->get();
        }else{
            $optional_rates = OptionalRate::where('villas_id', $order->service_id)->get();
        }
        $optional_rate_order = [];
        $total_optional_rate = 0;

        if ($request->optional_rate_id) {
            foreach ($request->optional_rate_id as $index => $optional_rate_id) {
                $optional_rate = $optional_rates->firstWhere('id', $optional_rate_id);
                if (!$optional_rate) continue;
                $price_pax = $optional_rate->calculatePrice($usdrates, $tax);
                $price_total = $price_pax * $request->number_of_guest[$index];
                $optional_rate_order[] = [
                    'optional_rate_id' => $optional_rate_id,
                    'service' => $order->service,
                    'orders_id' => $order->id,
                    'number_of_guest' => $request->number_of_guest[$index],
                    'service_date' => $request->service_date[$index],
                    'price_pax' => $price_pax,
                    'price_total' => $price_total,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
    
                $total_optional_rate += $price_total;
            }
        }
        if (!empty($optional_rate_order)) {
            OptionalRateOrder::insert($optional_rate_order);
        }

        $extra_bed_total_price = $order->extra_bed_total_price ?? 0;
        $airport_shuttle_price = $order->airport_shuttle_price ?? 0;
        $additional_service_total_price = $order->additional_service_total_price??0;
        $price_total = $order->normal_price + $extra_bed_total_price + $airport_shuttle_price + $additional_service_total_price + $total_optional_rate;
        $kick_back = $order->kick_back;
        $discounts = $order->discounts;
        $bookingcode_disc = $order->bookingcode_disc ?? 0;
        $promotion_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc, true)) : 0;
        $final_price = $price_total - $kick_back - $discounts - $bookingcode_disc - $promotion_discount;

        $order->update([
            "optional_price" => $total_optional_rate,
            "price_total" => $price_total,
            "final_price" => $final_price,
            "handled_by"=>$handled_by,
            "handled_date"=>$now,
        ]);
       
        // dd($optional_rate_order);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Add Additional Charge",
            "url"=>$request->getClientIp(),
            "method"=>"Add",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/orders-admin-$id#additionalCharge")->with('success','Optional service added successfully!');
    }




    // Function Remove optional service =============================================================================================================>
    public function destroy_opser_order(Request $request,$id) {
        $order = $request->order_id;
        $optional_rate_order = OptionalRateOrder::findOrFail($id);
        $optional_rate_order->delete();
        return redirect("/optional-rate-add-$request->order_id")->with('success','Optional service has been removed!');
    }
    // Function Updated Optional Rate =============================================================================================================>
    public function func_update_optional_rate_order(Request $request,$id){
        $usdrates = UsdRates::where('name','USD')->first();
        $optionalrateorder=OptionalRateOrder::findOrFail($id);
        $order = Orders::where('id', $optionalrateorder->order_id)->first();
        $optionalrate_id=$request->optional_rate_id;
        $oprate=OptionalRate::where('id', $optionalrate_id)->first();
        $service_date = date('Y-m-d', strtotime($request->service_date));
        $opti_rate = OptionalRate::where('id',$request->optional_rate_id)->first();
        $price_unit = (ceil($opti_rate->contract_rate / $usdrates->rate))+$opti_rate->markup;
        $total_price = $price_unit * $request->qty;
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $optionalrateorder->update([
            "type"=>$oprate->type,
            "name"=>$oprate->name,
            "qty"=>$request->qty,
            "price_unit"=>$total_price,
            "description" =>$oprate->description,
            "note"=>$request->note,
            "service_date"=>$service_date,
            "optional_rate_id"=>$oprate->id,
        ]);
        $order->update([
            "optional_price"=>$total_price,
            "name"=>$oprate->name,
            "handled_by"=>$handled_by,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Update Additional Charge",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        // @dd($optionalrateorder);
        return redirect("/optional-rate-add-$request->order_id")->with('success','Optional Rate has been updated');
    }
    // Function Updated Bridal's Detail =============================================================================================================>
    public function func_update_bridal(Request $request,$id){
        $order = Orders::where('id', $id)->first();
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $order->update([
            "groom_name"=>$request->groom_name,
            "bride_name"=>$request->bride_name,
            "wedding_date"=>$request->wedding_date,
            "number_of_guests"=>$request->number_of_guests,
            "handled_by"=>$handled_by,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Update Bridal's detail",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        // @dd($optionalrateorder);
        return redirect("/orders-admin-$order->id")->with('success','Bridal detail has been updated');
    }

    // View edit Additional service =========================================================================================>
    public function edit_additional_services($id)
    {
        $order = Orders::where('id',$id)->first();
        $usdrates = UsdRates::where('name','USD')->first();
        $attentions = Attention::where('page','add-additional-service')->get();
        $additional_service = json_decode($order->additional_service);
        $additional_service_date = json_decode($order->additional_service_date);
        $additional_service_qty = json_decode($order->additional_service_qty);
        $additional_service_price = json_decode($order->additional_service_price);
        $order_wedding = OrderWedding::where('id',$order->wedding_order_id)->first();
        if ($order->handled_by) {
            if ($order->handled_by == Auth::user()->id) {
                if ($order->status != "Approved") {
                    return view('order.add-additional-services',[
                        'order'=> $order,
                        'usdrates'=> $usdrates,
                        'attentions'=> $attentions,
                        "additional_service_date"=>$additional_service_date,
                        "additional_service"=>$additional_service,
                        "additional_service_qty"=>$additional_service_qty,
                        "additional_service_price"=>$additional_service_price,
                        "order_wedding"=>$order_wedding,
                    ]);
                }else{
                    return redirect("/orders-admin-$id")->with('success','You can not change anything!');
                }
            }else{
                return redirect("/orders-admin-$id")->with('success','You can not change anything!');
            }
        }else{
            return view('order.add-additional-services',[
                'order'=> $order,
                'usdrates'=> $usdrates,
                'attentions'=> $attentions,
                "additional_service_date"=>$additional_service_date,
                "additional_service"=>$additional_service,
                "additional_service_qty"=>$additional_service_qty,
                "additional_service_price"=>$additional_service_price,
                "order_wedding"=>$order_wedding,
            ]);
        }
    }
    // View edit Itinerary =========================================================================================>
    public function admin_edit_order_itinerary($id)
    {
        $order = Orders::where('id',$id)->first();
        $usdrates = UsdRates::where('name','USD')->first();
        $attentions = Attention::where('page','add-order-itinerary')->get();
        $additional_service = json_decode($order->additional_service);
        $additional_service_date = json_decode($order->additional_service_date);
        $additional_service_qty = json_decode($order->additional_service_qty);
        $additional_service_price = json_decode($order->additional_service_price);
        $order_wedding = OrderWedding::where('id',$order->wedding_order_id)->firstOrFail();
        $wedding_itineraries = Itinerary::where('order_id',$order->id)->orderBy('day', 'asc')->get();
        return view('order.add-order-itinerary',[
            'order'=> $order,
            'usdrates'=> $usdrates,
            'attentions'=> $attentions,
            "additional_service_date"=>$additional_service_date,
            "additional_service"=>$additional_service,
            "additional_service_qty"=>$additional_service_qty,
            "additional_service_price"=>$additional_service_price,
            "order_wedding"=>$order_wedding,
            "wedding_itineraries"=>$wedding_itineraries,
        ]);
    }
    // VIEW EDIT AIRPORT SHUTTLE
    public function edit_airport_shuttle($id){
        $order = Orders::where('id',$id)->first();
        $usdrates = UsdRates::where('name','USD')->first();
        $attentions = Attention::where('page','add-additional-service')->get();
        $airport_shuttles = AirportShuttle::where('order_id',$id)->get();
        $transports = Transports::where('status',"Active")->get();
        $hotel = Hotels::where('id',$order->service_id)->first();
        if ($order->handled_by) {
            if ($order->handled_by == Auth::user()->id) {
                if ($order->status != "Approved") {
                    return view('order.edit-airport-shuttle',[
                        'order'=> $order,
                        'usdrates'=> $usdrates,
                        'attentions'=> $attentions,
                        'airport_shuttles'=> $airport_shuttles,
                        'transports'=> $transports,
                        'hotel'=> $hotel,
                        
                    ]);
                }else{
                    return redirect("/orders-admin-$id")->with('success','You can not change anything!');
                }
            }else{
                return redirect("/orders-admin-$id")->with('success','You can not change anything!');
            }
        }else{
            return view('order.edit-airport-shuttle',[
                'order'=> $order,
                'usdrates'=> $usdrates,
                'attentions'=> $attentions,
                'airport_shuttles'=> $airport_shuttles,
                'transports'=> $transports,
                'hotel'=> $hotel,
                
            ]);
        }
    }
    // FUNCTION REMOVE AIRPORT SHUTTLE
    public function func_remove_airport_shuttle(Request $request, $id)
    {
        $order =  Orders::where('id',$request->order_id)->first();
        $airport_shuttle = AirportShuttle::findOrFail($id);
        $airport_shuttle->delete();

        $asus = AirportShuttle::where('order_id',$order->id)->get();
        $airport_shuttle_price = 0;
        foreach ($asus as $asu) {
            $airport_shuttle_price = $airport_shuttle_price + $asu->price;
        }

        $p_disc = json_decode($order->promotion_disc);
        if (isset($p_disc)) {
            $promotion_disc = array_sum($p_disc);
        }else{
            $promotion_disc = 0;
        }
        // $adser = json_decode($order->additional_service_price);
        // if (isset($adser)) {
        //     $additional_service_price = array_sum($adser);
        // }else{
        //     $additional_service_price = 0;
        // }
        $adser_qty = json_decode($order->additional_service_qty);
        $adser_price = json_decode($order->additional_service_price);
        if (isset($adser_qty)) {
            $cadser = count($adser_qty);
            $adser_prices = 0;
            for ($adser=0; $adser < $cadser; $adser++) { 
                $adser_prices = ($adser_qty[$adser] * $adser_price[$adser])+$adser_prices;
            }
            // $adserp = array_sum($adser);
            $additional_service_price = $adser_prices;
        }else{
            $additional_service_price = 0;
        }
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $final_price = ($order->price_total + $order->optional_price) - $order->discounts - $order->bookingcode_disc - $promotion_disc + $additional_service_price + $airport_shuttle_price;
        $order->update([
            "airport_shuttle_price"=>$airport_shuttle_price,
            "final_price"=>$final_price,
            "handled_by"=>$handled_by,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Remove Airport Shuttle",
            "url"=>$request->getClientIp(),
            "method"=>"Remove",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect()->back()->with('success','Airport shuttle has been removed');
    }
    // FUNCTION ADD AIRPORT SHUTTLE
    public function func_add_airport_shuttle(Request $request){
        if ($request->dst == "Airport") {
            $nav = "Out";
        }else{
            $nav = "In";
        }
        $order_id =$request->order_id;
        $order =  Orders::where('id',$order_id)->first();
        $transport = Transports::find($request->transport);
        $id = $request->hotel_id;
        $hotel = Hotels::findOrFail($id);
        $date = Carbon::parse($request->date)->format('Y-m-d H:i');
        $number_of_guests = $transport->capacity;
        $airportshuttle = new AirportShuttle([
            "date"=>$date,
            "flight_number"=>$request->flight_number,
            "number_of_guests"=>$number_of_guests,
            "transport_id"=>$transport->id,
            "dst"=>$request->dst,
            "src"=>$request->src,
            "duration"=>$hotel->airport_duration,
            "distance"=>$hotel->airport_distance,
            "price"=>$request->price,
            "order_id"=>$order_id,
            "nav"=>"Out",
        ]);
        $airportshuttle->save();
        
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $asus = AirportShuttle::where('order_id',$order_id)->get();
        $airport_shuttle_price = 0;
        foreach ($asus as $asu) {
            $airport_shuttle_price = $airport_shuttle_price + $asu->price;
        }
    
        
        $p_disc = json_decode($order->promotion_disc);
        if (isset($p_disc)) {
            $promotion_disc = array_sum($p_disc);
        }else{
            $promotion_disc = 0;
        }
        $adser_qty = json_decode($order->additional_service_qty);
        $adser_price = json_decode($order->additional_service_price);
        if (isset($adser_qty)) {
            $cadser = count($adser_qty);
            $adser_prices = 0;
            for ($adser=0; $adser < $cadser; $adser++) { 
                $adser_prices = ($adser_qty[$adser] * $adser_price[$adser])+$adser_prices;
            }
            $additional_service_price = $adser_prices;
        }else{
            $additional_service_price = 0;
        }
    
        $final_price = $order->price_total + $order->optional_price - $order->discounts - $order->bookingcode_disc - $promotion_disc + $additional_service_price + $airport_shuttle_price;
        $order->update([
            "airport_shuttle_price"=>$airport_shuttle_price,
            "final_price"=>$final_price,
            "handled_by"=>$handled_by,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Add Airport Shuttle",
            "url"=>$request->getClientIp(),
            "method"=>"Add",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/edit-airport-shuttle-$order_id")->with('success','Airport Shuttle has been added');
    }
    
    public function func_edit_airport_shuttle(Request $request, $id)
    {
        $airport_shuttle = AirportShuttle::findOrFail($id);
        $order = Orders::find($request->order_id);
        $hotel = Hotels::find($request->hotel_id);
        $transport = Transports::find($request->transport);
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        if (!$hotel) {
            return redirect()->back()->with('error', 'Hotel not found.');
        }
        $number_of_guests = $transport->capacity;
        $handled_by = $order->handled_by ?? Auth::id();
        $nav = ($request->dst === "Airport") ? "Out" : "In";
        $date = Carbon::parse($request->date)->format('Y-m-d H:i');
        $airport_shuttle->update([
            "date" => $date,
            "flight_number" => $request->flight_number,
            "number_of_guests"=>$number_of_guests,
            "transport_id" => $request->transport,
            "dst" => $request->dst,
            "src" => $request->src,
            "duration" => $hotel->airport_duration,
            "distance" => $hotel->airport_distance,
            "price" => $request->price,
            "order_id" => $request->order_id,
            "nav" => $nav,
        ]);
        $airport_shuttle_price = AirportShuttle::where('order_id', $order->id)->sum('price');
        $promotion_disc = array_sum(json_decode($order->promotion_disc, true) ?? []);
        $adser_qty = json_decode($order->additional_service_qty, true) ?? [];
        $adser_price = json_decode($order->additional_service_price, true) ?? [];

        $additional_service_price = array_reduce(
            array_keys($adser_qty),
            fn($carry, $index) => $carry + ($adser_qty[$index] * ($adser_price[$index] ?? 0)),
            0
        );
        $final_price = (
            ($order->price_total + $order->optional_price) - 
            $order->discounts - $order->bookingcode_disc - $promotion_disc
        ) + $additional_service_price + $airport_shuttle_price;
        $order->update([
            "airport_shuttle_price" => $airport_shuttle_price,
            "final_price" => $final_price,
            "handled_by" => $handled_by,
        ]);
        OrderLog::create([
            "order_id" => $order->id,
            "action" => "Update Airport Shuttle",
            "url" => $request->ip(),
            "method" => "Update",
            "agent" => Auth::user()->name,
            "admin" => Auth::id(),
        ]);
        return redirect("/edit-airport-shuttle-{$request->order_id}")->with('success', 'Airport Shuttle has been updated');
    }

    // Function edit Additional service =========================================================================================>
    public function func_edit_additional_services(Request $request, $id)
    {
        $order = Orders::find($id);
        if (!$order) {
            return redirect("/orders")->with('warning', 'Order not found');
        }
        $promotion_disc = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc, true)) : 0;
        $new_adser = $request->additional_service ?? [];
        $new_adser_date = $request->additional_service_date ?? [];
        $new_adser_qty = $request->additional_service_qty ?? [];
        $new_adser_price = $request->additional_service_price ?? [];
        if (count($new_adser) !== count($new_adser_qty) || count($new_adser) !== count($new_adser_price)) {
            return redirect()->back()->with('error', 'Invalid additional service data.');
        }
        $total_additional_service = array_sum(array_map(function($qty, $price) {
                return $qty * $price;
            }, $new_adser_qty, $new_adser_price));

        $extra_bed_price = $order->extra_bed_total_price??0;
        $airport_shuttle_price = $order->airport_shuttle_price??0;
        $optional_price = $order->optional_price??0;
        $kick_back = $order->kick_back??0;
        $discounts = $order->discounts??0;
        $bookingcode_disc = $order->bookingcode_disc??0;
        $promotion_disc_array = json_decode($order->promotion_disc, true) ?? [];
        $promotion_disc = array_sum($promotion_disc_array);
        $price_total = ($order->normal_price + $extra_bed_price + $airport_shuttle_price + $total_additional_service + $optional_price);
        $final_price = ($price_total - $kick_back - $discounts - $bookingcode_disc - $promotion_disc);
        
            
        $handled_by = $order->handled_by ?? Auth::id();
        $order->update([
            "additional_service_date" => json_encode($new_adser_date),
            "additional_service" => json_encode($new_adser),
            "additional_service_qty" => json_encode($new_adser_qty),
            "additional_service_price" => json_encode($new_adser_price),
            "additional_service_total_price" => $total_additional_service,
            "price_total" => $price_total,
            "final_price" => $final_price,
            "handled_by" => $handled_by,
        ]);
        OrderLog::create([
            "order_id" => $id,
            "action" => "Edit Additional Service",
            "url" => $request->ip(),
            "method" => "Update",
            "agent" => Auth::user()->name,
            "admin" => Auth::id(),
        ]);
        return redirect("/orders-admin-$id")->with('success', 'Additional service has been updated');
    }

    // WEDDING=================================================================================================
    // function Update Order Wedding Venue >
    public function func_update_order_wedding_venue(Request $request,$id)
    {
        $order = Orders::where('id',$request->order_id)->first();
        $weddingOrder=OrderWedding::findOrFail($id);
        $venues_id = $request->wedding_venue_id;
        $wedding_venue_id = json_encode($venues_id);
        $usdrate = UsdRates::where('name','USD')->firstOrFail();
        $v_price = [];
        $services = VendorPackage::where('type',"Wedding Venue")->get();
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        if ($venues_id) {
            $c_venue_id = count($venues_id);
        }else{
            $c_venue_id = 0;
        }
        if ($c_venue_id > 0) {
            foreach ($venues_id as $venue_id) {
                $service = $services->where('id',$venue_id)->firstOrFail();
                $publish_rate = $service->publish_rate;
                if ($service) {
                    array_push($v_price,$publish_rate);
                }
            }
        }
        $venue_price = array_sum($v_price);
        $weddingOrder->update([
            "wedding_venue_id"=>$wedding_venue_id,
            "venue_price"=>$venue_price,
        ]);

        $usdrates = UsdRates::where('name','USD')->first();
        $wedding = Weddings::where('id',$order->service_id)->first();
        $adser_qty = json_decode($order->additional_service_qty);
        $adser_price = json_decode($order->additional_service_price);
        $adserPrice = [];
        if ($adser_qty) {
            $c_qty = count($adser_qty);
            for ($i=0; $i < $c_qty; $i++) { 
                $additionalServicePrice = $adser_qty[$i] * $adser_price[$i];
                $adser_price_first = array_push($adserPrice,$additionalServicePrice);
            }
            $additional_service_price = array_sum($adserPrice);
        }else{
            $additional_service_price = 0;
        }
        $wp = $venue_price + $weddingOrder->fixed_service_price + $weddingOrder->makeup_price + $weddingOrder->room_price + $weddingOrder->documentation_price + $weddingOrder->decoration_price + $weddingOrder->dinner_venue_price + $weddingOrder->entertainment_price + $weddingOrder->transport_price + $weddingOrder->other_price + $wedding->markup;
        $final_price = $wp + $order->optional_price - $order->discounts - $order->bookingcode_disc - $order->promotion_disc + $additional_service_price + $order->airport_shuttle_price;
        $order->update([
            "price_total"=>$wp,
            "final_price"=>$final_price,
            "handled_by"=>$handled_by,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Update Wedding Venue",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/update-wedding-service-$order->id")->with('success','The Wedding service has been successfully updated!');
    }
    // function Update Order Wedding Rooms >
    public function func_update_order_wedding_room(Request $request,$id)
    {
        $order = Orders::where('id',$request->order_id)->first();
        $weddingOrder=OrderWedding::findOrFail($id);
        $rooms_id = $request->rooms_id;
        $wedding_room_id = json_encode($rooms_id);
        $usdrate = UsdRates::where('name','USD')->firstOrFail();
        $taxes = Tax::where('id',1)->first();
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $r_price = [];
        if ($rooms_id) {
            $c_room_id = count($rooms_id);
        }else{
            $c_room_id = 0;
        }
        if ($c_room_id > 0) {
            foreach ($rooms_id as $room_id) {
                $service = HotelPrice::where('rooms_id', $room_id)
                ->where('start_date','<',$order->wedding_date)
                ->where("end_date",">",$order->wedding_date)
                ->first();
                if ($service) {
                    $cr_price_usd = ceil($service->contract_rate / $usdrate->rate);
                    $cr_mr = $cr_price_usd + $service->markup;
                    $tax_price = ceil(($cr_mr * $taxes->tax)/100);
                    $priceUsd = ($cr_mr +  $tax_price)*$order->duration;
                }else{
                    $priceUsd = 0;
                }
                if ($priceUsd > 0) {
                    array_push($r_price,$priceUsd);
                }
            }
        }
        $room_price = array_sum($r_price);
        $weddingOrder->update([
            "wedding_room_id"=>$wedding_room_id,
            "room_price"=>$room_price,
        ]);

       
        $wedding = Weddings::where('id',$order->service_id)->first();
        $adser_qty = json_decode($order->additional_service_qty);
        $adser_price = json_decode($order->additional_service_price);
        $adserPrice = [];
        if ($adser_qty) {
            $c_qty = count($adser_qty);
            for ($i=0; $i < $c_qty; $i++) { 
                $additionalServicePrice = $adser_qty[$i] * $adser_price[$i];
                $adser_price_first = array_push($adserPrice,$additionalServicePrice);
            }
            $additional_service_price = array_sum($adserPrice);
        }else{
            $additional_service_price = 0;
        }
        $wp = $weddingOrder->venue_price + $weddingOrder->fixed_service_price + $weddingOrder->makeup_price + $room_price + $weddingOrder->documentation_price + $weddingOrder->decoration_price + $weddingOrder->dinner_venue_price + $weddingOrder->entertainment_price + $weddingOrder->transport_price + $weddingOrder->other_price + $wedding->markup;
        $final_price = $wp + $order->optional_price - $order->discounts - $order->bookingcode_disc - $order->promotion_disc + $additional_service_price + $order->airport_shuttle_price;
        $order->update([
            "price_total"=>$wp,
            "final_price"=>$final_price,
            "handled_by"=>$handled_by,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Update Wedding Sites and Villas",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/update-wedding-service-$order->id")->with('success','The Wedding service has been successfully updated!');
    }
    // function Update Order Wedding Makeup >
    public function func_update_order_wedding_makeup(Request $request,$id)
    {
        $order = Orders::where('id',$request->order_id)->first();
        $weddingOrder=OrderWedding::findOrFail($id);
        $makeups_id = $request->wedding_makeup_id;
        $wedding_makeup_id = json_encode($makeups_id);
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $usdrate = UsdRates::where('name','USD')->firstOrFail();
        $m_price = [];
        $services = VendorPackage::where('type',"Make-up")->get();
        if ($makeups_id) {
            $c_makeup_id = count($makeups_id);
        }else{
            $c_makeup_id = 0;
        }
        if ($c_makeup_id > 0) {
            foreach ($makeups_id as $makeup_id) {
                $service = $services->where('id',$makeup_id)->firstOrFail();
                $publish_rate = $service->publish_rate;
                if ($service) {
                    array_push($m_price,$publish_rate);
                }
            }
        }
        $makeup_price = array_sum($m_price);
        $weddingOrder->update([
            "wedding_makeup_id"=>$wedding_makeup_id,
            "makeup_price"=>$makeup_price,
        ]);
        $wedding = Weddings::where('id',$order->service_id)->first();
        $adser_qty = json_decode($order->additional_service_qty);
        $adser_price = json_decode($order->additional_service_price);
        $adserPrice = [];
        if ($adser_qty) {
            $c_qty = count($adser_qty);
            for ($i=0; $i < $c_qty; $i++) { 
                $additionalServicePrice = $adser_qty[$i] * $adser_price[$i];
                $adser_price_first = array_push($adserPrice,$additionalServicePrice);
            }
            $additional_service_price = array_sum($adserPrice);
        }else{
            $additional_service_price = 0;
        }
        $wp = $weddingOrder->venue_price + $weddingOrder->fixed_service_price + $makeup_price + $weddingOrder->room_price + $weddingOrder->documentation_price + $weddingOrder->decoration_price + $weddingOrder->dinner_venue_price + $weddingOrder->entertainment_price + $weddingOrder->transport_price + $weddingOrder->other_price + $wedding->markup;
        $final_price = $wp + $order->optional_price - $order->discounts - $order->bookingcode_disc - $order->promotion_disc + $additional_service_price + $order->airport_shuttle_price;
        $order->update([
            "price_total"=>$wp,
            "final_price"=>$final_price,
            "handled_by"=>$handled_by,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Update Wedding Make-up",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/update-wedding-service-$order->id")->with('success','The Wedding Makeup has been successfully updated!');
    }
    // function Update Order Wedding Decoration >
    public function func_update_order_wedding_decoration(Request $request,$id)
    {
        $order = Orders::where('id',$request->order_id)->first();
        $weddingOrder=OrderWedding::findOrFail($id);
        $decorations_id = $request->wedding_decoration_id;
        $wedding_decoration_id = json_encode($decorations_id);
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $usdrate = UsdRates::where('name','USD')->firstOrFail();
        $decor_price = [];
        $services = VendorPackage::where('type',"Decoration")->get();
        if ($decorations_id) {
            $c_decorations_id = count($decorations_id);
        }else{
            $c_decorations_id = 0;
        }
        if ($c_decorations_id > 0) {
            foreach ($decorations_id as $decoration_id) {
                $service = $services->where('id',$decoration_id)->firstOrFail();
                $publish_rate = $service->publish_rate;
                if ($service) {
                    array_push($decor_price,$publish_rate);
                }
            }
        }
        $decoration_price = array_sum($decor_price);
        $weddingOrder->update([
            "wedding_decoration_id"=>$wedding_decoration_id,
            "decoration_price"=>$decoration_price,
        ]);
        $tax = Tax::where('id',1)->first();
        $wedding = Weddings::where('id',$order->service_id)->first();
        $adser_qty = json_decode($order->additional_service_qty);
        $adser_price = json_decode($order->additional_service_price);
        $adserPrice = [];
        if ($adser_qty) {
            $c_qty = count($adser_qty);
            for ($i=0; $i < $c_qty; $i++) { 
                $additionalServicePrice = $adser_qty[$i] * $adser_price[$i];
                $adser_price_first = array_push($adserPrice,$additionalServicePrice);
            }
            $additional_service_price = array_sum($adserPrice);
        }else{
            $additional_service_price = 0;
        }
        $wp = $weddingOrder->venue_price + $weddingOrder->fixed_service_price + $weddingOrder->makeup_price + $weddingOrder->room_price + $weddingOrder->documentation_price + $decoration_price + $weddingOrder->dinner_venue_price + $weddingOrder->entertainment_price + $weddingOrder->transport_price + $weddingOrder->other_price + $wedding->markup;
        $final_price = $wp + $order->optional_price - $order->discounts - $order->bookingcode_disc - $order->promotion_disc + $additional_service_price + $order->airport_shuttle_price;
        $order->update([
            "price_total"=>$wp,
            "final_price"=>$final_price,
            "handled_by"=>$handled_by,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Update Wedding Decoration",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/update-wedding-service-$order->id")->with('success','The Wedding Decoration has been successfully updated!');
    }
    // function Update Order Wedding Decoration >
    public function func_update_order_wedding_dinner_venue(Request $request,$id)
    {
        $order = Orders::where('id',$request->order_id)->first();
        $weddingOrder=OrderWedding::findOrFail($id);
        $dinner_venue_id = $request->wedding_dinner_venue_id;
        $wedding_dinner_venue_id = json_encode($dinner_venue_id);
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $usdrate = UsdRates::where('name','USD')->firstOrFail();
        $dinner_price = [];
        $services = VendorPackage::where('type',"Wedding Dinner")->get();
        if ($dinner_venue_id) {
            $c_dinner_venue_id = count($dinner_venue_id);
        }else{
            $c_dinner_venue_id = 0;
        }
        if ($c_dinner_venue_id > 0) {
            foreach ($dinner_venue_id as $dinner_id) {
                $service = $services->where('id',$dinner_id)->firstOrFail();
                $publish_rate = $service->publish_rate;
                if ($service) {
                    array_push($dinner_price,$publish_rate);
                }
            }
        }
        $dinner_venue_price = array_sum($dinner_price);
        $weddingOrder->update([
            "wedding_dinner_venue_id"=>$wedding_dinner_venue_id,
            "dinner_venue_price"=>$dinner_venue_price,
        ]);
        $tax = Tax::where('id',1)->first();
        $wedding = Weddings::where('id',$order->service_id)->first();
        $adser_qty = json_decode($order->additional_service_qty);
        $adser_price = json_decode($order->additional_service_price);
        $adserPrice = [];
        if ($adser_qty) {
            $c_qty = count($adser_qty);
            for ($i=0; $i < $c_qty; $i++) { 
                $additionalServicePrice = $adser_qty[$i] * $adser_price[$i];
                $adser_price_first = array_push($adserPrice,$additionalServicePrice);
            }
            $additional_service_price = array_sum($adserPrice);
        }else{
            $additional_service_price = 0;
        }
        $wp = $weddingOrder->venue_price + $weddingOrder->fixed_service_price + $weddingOrder->makeup_price + $weddingOrder->room_price + $weddingOrder->documentation_price + $weddingOrder->decoration_price + $dinner_venue_price + $weddingOrder->entertainment_price + $weddingOrder->transport_price + $weddingOrder->other_price + $wedding->markup;
        $final_price = $wp + $order->optional_price - $order->discounts - $order->bookingcode_disc - $order->promotion_disc + $additional_service_price + $order->airport_shuttle_price;
        $order->update([
            "price_total"=>$wp,
            "final_price"=>$final_price,
            "handled_by"=>$handled_by,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Update Dinner Venue",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/update-wedding-service-$order->id")->with('success','The Wedding dinner venue has been successfully updated!');
    }
    // function Update Order Wedding Entertainment >
    public function func_update_order_wedding_entertainment(Request $request,$id)
    {
        $order = Orders::where('id',$request->order_id)->first();
        $weddingOrder=OrderWedding::findOrFail($id);
        $entertainments_id = $request->wedding_entertainment_id;
        $wedding_entertainment_id = json_encode($entertainments_id);
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $usdrate = UsdRates::where('name','USD')->firstOrFail();
        $entertainment_price = [];
        $services = VendorPackage::where('type',"Entertainment")->get();
        if ($entertainments_id) {
            $c_entertainments_id = count($entertainments_id);
        }else{
            $c_entertainments_id = 0;
        }
        if ($c_entertainments_id > 0) {
            foreach ($entertainments_id as $ent_id) {
                $service = $services->where('id',$ent_id)->firstOrFail();
                $publish_rate = $service->publish_rate;
                if ($service) {
                    array_push($entertainment_price,$publish_rate);
                }
            }
        }
        $entertainment_price = array_sum($entertainment_price);
        $weddingOrder->update([
            "wedding_entertainment_id"=>$wedding_entertainment_id,
            "entertainment_price"=>$entertainment_price,
        ]);
        $wedding = Weddings::where('id',$order->service_id)->first();
        $adser_qty = json_decode($order->additional_service_qty);
        $adser_price = json_decode($order->additional_service_price);
        $adserPrice = [];
        if ($adser_qty) {
            $c_qty = count($adser_qty);
            for ($i=0; $i < $c_qty; $i++) { 
                $additionalServicePrice = $adser_qty[$i] * $adser_price[$i];
                $adser_price_first = array_push($adserPrice,$additionalServicePrice);
            }
            $additional_service_price = array_sum($adserPrice);
        }else{
            $additional_service_price = 0;
        }
        $wp = $weddingOrder->venue_price + $weddingOrder->fixed_service_price + $weddingOrder->makeup_price + $weddingOrder->room_price + $weddingOrder->documentation_price + $weddingOrder->decoration_price + $weddingOrder->dinner_venue_price + $entertainment_price + $weddingOrder->transport_price + $weddingOrder->other_price + $wedding->markup;
        $final_price = $wp + $order->optional_price - $order->discounts - $order->bookingcode_disc - $order->promotion_disc + $additional_service_price + $order->airport_shuttle_price;
        $order->update([
            "price_total"=>$wp,
            "final_price"=>$final_price,
            "handled_by"=>$handled_by,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Update Wedding Entertainment",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/update-wedding-service-$order->id")->with('success','The Wedding entertainment has been successfully updated!');
    }
    // function Update Order Wedding Documentation >
    public function func_update_order_wedding_documentation(Request $request,$id)
    {
        $order = Orders::where('id',$request->order_id)->first();
        $weddingOrder=OrderWedding::findOrFail($id);
        $documentations_id = $request->wedding_documentation_id;
        $wedding_documentation_id = json_encode($documentations_id);
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $usdrate = UsdRates::where('name','USD')->firstOrFail();
        $doc_price = [];
        $services = VendorPackage::where('type',"Documentation")->get();
        if ($documentations_id) {
            $c_documentations_id = count($documentations_id);
        }else{
            $c_documentations_id = 0;
        }
        if ($c_documentations_id > 0) {
            foreach ($documentations_id as $documentation_id) {
                $service = $services->where('id',$documentation_id)->firstOrFail();
                $publish_rate = $service->publish_rate;
                if ($service) {
                    array_push($doc_price,$publish_rate);
                }
            }
        }
        $documentation_price = array_sum($doc_price);

        $weddingOrder->update([
            "wedding_documentation_id"=>$wedding_documentation_id,
            "documentation_price"=>$documentation_price,
        ]);
        $wedding = Weddings::where('id',$order->service_id)->first();
        $adser_qty = json_decode($order->additional_service_qty);
        $adser_price = json_decode($order->additional_service_price);
        $adserPrice = [];
        if ($adser_qty) {
            $c_qty = count($adser_qty);
            for ($i=0; $i < $c_qty; $i++) { 
                $additionalServicePrice = $adser_qty[$i] * $adser_price[$i];
                $adser_price_first = array_push($adserPrice,$additionalServicePrice);
            }
            $additional_service_price = array_sum($adserPrice);
        }else{
            $additional_service_price = 0;
        }
        $wp = $weddingOrder->venue_price + $weddingOrder->fixed_service_price + $weddingOrder->makeup_price + $weddingOrder->room_price + $documentation_price + $weddingOrder->decoration_price + $weddingOrder->dinner_venue_price + $weddingOrder->entertainment_price + $weddingOrder->transport_price + $weddingOrder->other_price + $wedding->markup;
        $final_price = $wp + $order->optional_price - $order->discounts - $order->bookingcode_disc - $order->promotion_disc + $additional_service_price + $order->airport_shuttle_price;
        $order->update([
            "price_total"=>$wp,
            "final_price"=>$final_price,
            "handled_by"=>$handled_by,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Update Wedding Documentation",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/update-wedding-service-$order->id")->with('success','The Wedding documentation has been successfully updated!');
    }
    // function Update Order Wedding Transport >
    public function func_update_order_wedding_transport(Request $request,$id)
    {
        $order = Orders::where('id',$request->order_id)->first();
        $hotel = Hotels::where('id',$order->subservice_id)->first();
        $weddingOrder=OrderWedding::findOrFail($id);
        $transports_id = $request->wedding_transport_id;
        $wedding_transport_id = json_encode($transports_id);
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $usdrate = UsdRates::where('name','USD')->firstOrFail();
        $taxes = Tax::where('id',1)->first();
        $trans_price = [];
        $services = TransportPrice::where('duration',$hotel->airport_duration)
        ->where('type','Airport Shuttle')
        ->get();
        if ($transports_id) {
            $c_transports_id = count($transports_id);
        }else{
            $c_transports_id = 0;
        }
        if ($c_transports_id > 0) {
            foreach ($transports_id as $transport_id) {
                $service = $services->where('transports_id',$transport_id)->firstOrFail();
                $cr = ceil($service->contract_rate/$usdrate->rate);
                $cr_mr = $cr + $service->markup;
                $cr_mr_tax = ceil($cr_mr * ($taxes->tax / 100));
                $priceUsd = $cr_mr + $cr_mr_tax;
                if ($service) {
                    array_push($trans_price,$priceUsd);
                }
            }
        }
        $transport_price = array_sum($trans_price);
        $final_price = $order->price_total - $weddingOrder->transport_price + $transport_price;
        $weddingOrder->update([
            "wedding_transport_id"=>$wedding_transport_id,
            "transport_price"=>$transport_price,
        ]);
        $wedding = Weddings::where('id',$order->service_id)->first();
        $adser_qty = json_decode($order->additional_service_qty);
        $adser_price = json_decode($order->additional_service_price);
        $adserPrice = [];
        if ($adser_qty) {
            $c_qty = count($adser_qty);
            for ($i=0; $i < $c_qty; $i++) { 
                $additionalServicePrice = $adser_qty[$i] * $adser_price[$i];
                $adser_price_first = array_push($adserPrice,$additionalServicePrice);
            }
            $additional_service_price = array_sum($adserPrice);
        }else{
            $additional_service_price = 0;
        }
        $wp = $weddingOrder->venue_price + $weddingOrder->fixed_service_price + $weddingOrder->makeup_price + $weddingOrder->room_price + $weddingOrder->documentation_price + $weddingOrder->decoration_price + $weddingOrder->dinner_venue_price + $weddingOrder->entertainment_price + $transport_price + $weddingOrder->other_price + $wedding->markup;
        $final_price = $wp + $order->optional_price - $order->discounts - $order->bookingcode_disc - $order->promotion_disc + $additional_service_price + $order->airport_shuttle_price;
        $order->update([
            "price_total"=>$wp,
            "final_price"=>$final_price,
            "handled_by"=>$handled_by,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Update Wedding Transport",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/update-wedding-service-$order->id")->with('success','The Wedding transportation has been successfully updated!');
    }

    // function Update Order Wedding Other >
    public function func_update_order_wedding_other(Request $request,$id)
    {
        $order = Orders::where('id',$request->order_id)->first();
        $weddingOrder=OrderWedding::findOrFail($id);
        $others_id = $request->wedding_other_id;
        $wedding_other_id = json_encode($others_id);
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $usdrate = UsdRates::where('name','USD')->firstOrFail();
        $o_price = [];
        $services = VendorPackage::where('type',"Other")->get();
        if ($others_id) {
            $c_dinner_venue_id = count($others_id);
        }else{
            $c_dinner_venue_id = 0;
        }
        if ($c_dinner_venue_id > 0) {
            foreach ($others_id as $other_id) {
                $service = $services->where('id',$other_id)->firstOrFail();
                $publish_rate = $service->publish_rate;
                if ($service) {
                    array_push($o_price,$publish_rate);
                }
            }
        }
        $other_price = array_sum($o_price);

        $weddingOrder->update([
            "wedding_other_id"=>$wedding_other_id,
            "other_price"=>$other_price,
        ]);
        $wedding = Weddings::where('id',$order->service_id)->first();
        $adser_qty = json_decode($order->additional_service_qty);
        $adser_price = json_decode($order->additional_service_price);
        $adserPrice = [];
        if ($adser_qty) {
            $c_qty = count($adser_qty);
            for ($i=0; $i < $c_qty; $i++) { 
                $additionalServicePrice = $adser_qty[$i] * $adser_price[$i];
                $adser_price_first = array_push($adserPrice,$additionalServicePrice);
            }
            $additional_service_price = array_sum($adserPrice);
        }else{
            $additional_service_price = 0;
        }
        $wp = $weddingOrder->venue_price + $weddingOrder->fixed_service_price + $weddingOrder->makeup_price + $weddingOrder->room_price + $weddingOrder->documentation_price + $weddingOrder->decoration_price + $weddingOrder->dinner_venue_price + $weddingOrder->entertainment_price + $weddingOrder->transport_price + $other_price + $wedding->markup;
        $final_price = $wp + $order->optional_price - $order->discounts - $order->bookingcode_disc - $order->promotion_disc + $additional_service_price + $order->airport_shuttle_price;
        $order->update([
            "price_total"=>$wp,
            "final_price"=>$final_price,
            "handled_by"=>$handled_by,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Update Wedding Other Service",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/update-wedding-service-$order->id")->with('success','The Wedding other service has been successfully updated!');
    }

    // PDF =======================================================================================================================================>
    //Create PDF
    public function confirmation_order($id)
    {
        $order=Orders::findOrFail($id);
        $hotels=Hotels::all();
        $user_id = Auth::User()->id;
        $now = Carbon::now();
        $usdrates = UsdRates::where('name','USD')->first();
        $cnyrates = UsdRates::where('name','CNY')->first();
        $twdrates = UsdRates::where('name','TWD')->first();
        $tax = Tax::where('id',1)->first();
        $attentions = Attention::where('page','user-order-detail')->get();
        $business = BusinessProfile::where('id',1)->first();
        // $optional_rate_order = OptionalRateOrder::all();
        $optionalrates = OptionalRate::all();
        $optionalrate_meals = OptionalRate::with('hotels')->where('type',"Meals")->get();
        $optional_rate_orders = OptionalRateOrder::where('orders_id', $id)->first();
        $extra_beds = ExtraBed::all();
        $agent = User::where('id', $order->user_id)->first();
        $order_link = 'https://online.balikamitour.com/detail-order-'.$order->id;
        $admin = Auth::user()->where('id',$order->verified_by)->first();
        $agent = Auth::user()->where('id',$order->sales_agent)->first();
        $email = $order->email;
        $logoImage = public_path('storage/logo/bali-kami-tour-logo.png');
        $guest_name = Guests::where('rsv_id',$order->rsv_id)->get();

        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $pdsc = json_decode($order->promotion_disc);
        if (isset($pdsc)) {
            $promotion_disc = array_sum($pdsc);
        }else{
            $promotion_disc = 0;
        }


        $special_day = json_decode($order->special_day);
        $special_date = json_decode($order->special_date);
        $extra_bed = json_decode($order->extra_bed);
        $extra_bed_id = json_decode($order->extra_bed_id);
        $extra_bed_price = json_decode($order->extra_bed_price);
        $nor = $order->number_of_room;
        $pickup_people = Guests::where('id',$order->pickup_name)->first();
        $guide = Guide::where('id',$order->guide_id)->first();
        $driver = Drivers::where('id',$order->driver_id)->first();

        $ebp = json_decode($order->extra_bed_price);
        if(isset($ebp)){
            $extrabed_price = array_sum($ebp);
            $jml_extra_bed = 0;
            $cebp = count($ebp);
            for ($i=0; $i < $cebp; $i++) { 
                if ($ebp[$i]>0) {
                    $jml_extra_bed = $jml_extra_bed + 1;
                }
            }
        }else{
            $cebp = 0;
            $jml_extra_bed = 0;
            $extrabed_price = 0;
        }
    
        if ($order->service == "Tour Package"){
            $amount = $order->price_total;
            if($order->duration == "1D"){
                $order_duration = 1;
            }elseif($order->duration == "2D/1N"){
                $order_duration = 1;
            }elseif($order->duration == "3D/2N"){
                $order_duration = 2;
            }elseif($order->duration == "4D/3N"){
                $order_duration = 3;
            }elseif($order->duration == "5D/4N"){
                $order_duration = 4;
            }elseif($order->duration == "6D/5N"){
                $order_duration = 5;
            }else{
                $order_duration = 1;
            }
        }elseif($order->servide == "Activity"){
            $amount = $order->price_total;
            $order_duration = $order->duration;
        }else{
            $amount = (($order->price_pax * $order->duration)*$order->number_of_room);
            $order_duration = $order->duration;
        }
        if (isset($order->price_id)) {
            $price = TourPrices::where('id',$order->price_id)->first();
        }else{
            $price = "";
        }

        $invoice = InvoiceAdmin::where('rsv_id',$order->rsv_id)->first();
        $bankAccount = BankAccount::where("id",$invoice->bank_id)->first();
        if (isset($optional_rate_orders->optional_rate_id)){
            $opsirate_order_date = json_decode($optional_rate_orders->service_date);
            $opsirate_order_nog = json_decode($optional_rate_orders->number_of_guest);
            $opsirate_order_id = json_decode($optional_rate_orders->optional_rate_id);
            $opsirate_order_price_pax = json_decode($optional_rate_orders->price_pax);
            $opsirate_order_price_total = json_decode($optional_rate_orders->price_total);
        }else{
            $opsirate_order_date = null;
            $opsirate_order_nog = null;
            $opsirate_order_id = null;
            $opsirate_order_price_pax = null;
            $opsirate_order_price_total = null;
        }
        // WEDDING
        $bride = Brides::where('id',$order->pickup_name)->firstOrFail();
        $order_wedding = OrderWedding::where('id',$order->wedding_order_id)->first();
        $rooms = HotelRoom::where('hotels_id',$order->subservice_id)->get();
        $weddingVenues = VendorPackage::where('type',"Wedding Venue")->get();
        $weddingMakeups = VendorPackage::where('type',"Make-up")->get();
        $weddingDecorations = VendorPackage::where('type',"Decoration")->get();
        
        $weddingDinnerVenues = VendorPackage::where('type',"Wedding Dinner")->get();
        $weddingEntertainments = VendorPackage::where('type',"Entertainment")->get();
        $weddingDocumentations = VendorPackage::where('type',"Documentation")->get();
        $weddingTransportations = Transports::all();
        $weddingOthers = VendorPackage::where('type',"Other")->get();
        $weddingFixedServices = VendorPackage::where('type',"Fixed Service")->get();

        $wedding_hotel = Hotels::where('id',$order_wedding->wedding_id)->firstOrFail();
        $wedding_itineraries = Itinerary::where('order_id',$order->id)->orderBy('time','ASC')->get();

        $data = [
            'now'=>$now,
            'title'=>"Confirmation Order",
            'email'=>$email,
            'order'=>$order,
            'agent'=>$agent,
            'admin'=>$admin,
            'order_link'=>$order_link,
            'logoImage'=>public_path('storage/logo/bali-kami-tour-logo.png'),
        ];
        
        $airport_shuttles = AirportShuttle::where('order_id',$id)->get();
        return view('emails.orderContractWeddingEn',compact('order'),[
            'now'=>$now,
            'title'=>"Confirmation Order",
            'email'=>$email,
            'agent'=>$agent,
            'admin'=>$admin,
            'order_link'=>$order_link,
            'extra_beds'=>$extra_beds,
            'order'=>$order,
            'order_wedding'=>$order_wedding,
            'tax'=>$tax,
            'optionalrates'=>$optionalrates,
            'usdrates'=>$usdrates,
            'cnyrates'=>$cnyrates,
            'twdrates'=>$twdrates,
            'business'=>$business,
            'optional_rate_orders'=>$optional_rate_orders,
            'attentions'=>$attentions,
            'optionalrate_meals'=>$optionalrate_meals,
            'data'=>$data,
            'agent'=>$agent,
            'guest_name'=>$guest_name,
            'hotels'=>$hotels,
            'rooms'=>$rooms,
            'weddingVenues'=>$weddingVenues,
            'weddingMakeups'=>$weddingMakeups,
            'weddingDecorations'=>$weddingDecorations,
            'weddingDinnerVenues'=>$weddingDinnerVenues,
            'weddingEntertainments'=>$weddingEntertainments,
            'weddingDocumentations'=>$weddingDocumentations,
            'weddingTransportations'=>$weddingTransportations,
            'weddingOthers'=>$weddingOthers,
            'weddingFixedServices'=>$weddingFixedServices,
            'wedding_itineraries'=>$wedding_itineraries,
            'wedding_hotel'=>$wedding_hotel,
            'bride'=>$bride,
            'nor'=>$nor,
            'special_day'=>$special_day,
            'special_date'=>$special_date,
            'extra_bed'=>$extra_bed,
            'extra_bed_id'=>$extra_bed_id,
            'extra_bed_price'=>$extra_bed_price,
            'pickup_people'=>$pickup_people,
            'guide'=>$guide,
            'driver'=>$driver,
            'reservation'=>$reservation,
            'invoice'=>$invoice,
            'amount'=>$amount,
            'jml_extra_bed'=>$jml_extra_bed,
            'extrabed_price'=>$extrabed_price,
            'cebp'=>$cebp,
            'opsirate_order_date'=>$opsirate_order_date,
            'opsirate_order_nog'=>$opsirate_order_nog,
            'opsirate_order_id'=>$opsirate_order_id,
            'opsirate_order_price_pax'=>$opsirate_order_price_pax,
            'opsirate_order_price_total'=>$opsirate_order_price_total,
            'promotion_disc'=>$promotion_disc,
            'bankAccount'=>$bankAccount,
            'order_duration'=>$order_duration,
            'price'=>$price,
            'airport_shuttles'=>$airport_shuttles,
        ]);
    }
    public function print_contract_order($id) 
    {
        $order=Orders::findOrFail($id);
        $hotels=Hotels::all();
        $user_id = Auth::User()->id;
        $now = Carbon::now();
        $usdrates = UsdRates::where('name','USD')->first();
        $cnyrates = UsdRates::where('name','CNY')->first();
        $twdrates = UsdRates::where('name','TWD')->first();
        $tax = Tax::where('id',1)->first();
        $attentions = Attention::where('page','user-order-detail')->get();
        $business = BusinessProfile::where('id',1)->first();
        // $optional_rate_order = OptionalRateOrder::all();
        $optionalrates = OptionalRate::all();
        $optionalrate_meals = OptionalRate::with('hotels')->where('type',"Meals")->get();
        $optional_rate_orders = OptionalRateOrder::where('orders_id', $id)->get();
        $extra_beds = ExtraBed::all();
        $agent = User::where('id', $order->user_id)->first();
        $order_link = 'https://online.balikamitour.com/detail-order-'.$order->id;
        $admin = Auth::user()->where('id',$order->verified_by)->first();
        $agent = Auth::user()->where('id',$order->sales_agent)->first();
        $email = $order->email;
        $logoImage = public_path('storage/logo/bali-kami-tour-logo.png');
        $guest_name = Guests::where('rsv_id',$order->rsv_id)->get();
        $hotel = Hotels::find($order->service_id);
        $villa = Villas::find($order->service_id);
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $pdsc = json_decode($order->promotion_disc);
        if (isset($pdsc)) {
            $promotion_disc = array_sum($pdsc);
        }else{
            $promotion_disc = 0;
        }
        $tax_doku = TaxDoku::where('id','1')->first();

        $special_day = json_decode($order->special_day);
        $special_date = json_decode($order->special_date);
        $extra_bed = json_decode($order->extra_bed);
        $extra_bed_id = json_decode($order->extra_bed_id);
        $extra_bed_price = json_decode($order->extra_bed_price);
        $nor = $order->number_of_room;
        $pickup_people = Guests::where('id',$order->pickup_name)->first();
        $guide = Guide::where('id',$order->guide_id)->first();
        $driver = Drivers::where('id',$order->driver_id)->first();
        
        $ebp = json_decode($order->extra_bed_price);
        if(isset($ebp)){
            $extrabed_price = array_sum($ebp);
            $jml_extra_bed = 0;
            $cebp = count($ebp);
            for ($i=0; $i < $cebp; $i++) { 
                if ($ebp[$i]>0) {
                    $jml_extra_bed = $jml_extra_bed + 1;
                }
            }
        }else{
            $cebp = 0;
            $jml_extra_bed = 0;
            $extrabed_price = 0;
        }
    
        if ($order->service == "Tour Package"){
            $amount = $order->price_total;
            if($order->duration == "1D"){
                $order_duration = 1;
            }elseif($order->duration == "2D/1N"){
                $order_duration = 1;
            }elseif($order->duration == "3D/2N"){
                $order_duration = 2;
            }elseif($order->duration == "4D/3N"){
                $order_duration = 3;
            }elseif($order->duration == "5D/4N"){
                $order_duration = 4;
            }elseif($order->duration == "6D/5N"){
                $order_duration = 5;
            }else{
                $order_duration = 1;
            }
        }elseif($order->servide == "Activity"){
            $amount = $order->price_total;
            $order_duration = $order->duration;
        }else{
            $amount = (($order->price_pax * $order->duration)*$order->number_of_room);
            $order_duration = $order->duration;
        }

        
        if (isset($order->price_id)) {
            $price = TourPrices::where('id',$order->price_id)->first();
        }else{
            $price = null;
        }

        $invoice = InvoiceAdmin::where('rsv_id',$order->rsv_id)->first();
        $doku_payment = DokuVirtualAccount::where('invoice_id',$invoice->id)->first();
        $doku_virtual_account = DokuVirtualAccount::where('invoice_id', $invoice->id)
        ->where('expired_date','>=',$now)
        ->orderBy('expired_date','DESC')
        ->first();
        $bankAccount = BankAccount::where("id",$invoice->bank_id)->first();
        if (isset($optional_rate_orders->optional_rate_id)){
            $opsirate_order_date = json_decode($optional_rate_orders->service_date);
            $opsirate_order_nog = json_decode($optional_rate_orders->number_of_guest);
            $opsirate_order_id = json_decode($optional_rate_orders->optional_rate_id);
            $opsirate_order_price_pax = json_decode($optional_rate_orders->price_pax);
            $opsirate_order_price_total = json_decode($optional_rate_orders->price_total);
        }else{
            $opsirate_order_date = null;
            $opsirate_order_nog = null;
            $opsirate_order_id = null;
            $opsirate_order_price_pax = null;
            $opsirate_order_price_total = null;
        }
        $data = [
            'now'=>$now,
            'title'=>"Confirmation Order",
            'email'=>$email,
            'order'=>$order,
            'agent'=>$agent,
            'admin'=>$admin,
            'order_link'=>$order_link,
            'logoImage'=>public_path('storage/logo/bali-kami-tour-logo.png'),
        ];
        $airport_shuttles = AirportShuttle::where('order_id',$id)->get();

        // WEDDING
        if ($order->service == "Wedding Package") {
            $bride = Brides::where('id',$order->pickup_name)->first();
            $order_wedding = OrderWedding::where('id',$order->wedding_order_id)->first();
            $rooms = HotelRoom::where('hotels_id',$order->subservice_id)->get();
            $weddingVenues = VendorPackage::where('type',"Wedding Venue")->get();
            $weddingMakeups = VendorPackage::where('type',"Make-up")->get();
            $weddingDecorations = VendorPackage::where('type',"Decoration")->get();
            $weddingDinnerVenues = VendorPackage::where('type',"Wedding Dinner")->get();
            $weddingEntertainments = VendorPackage::where('type',"Entertainment")->get();
            $weddingDocumentations = VendorPackage::where('type',"Documentation")->get();
            $weddingTransportations = Transports::all();
            $weddingOthers = VendorPackage::where('type',"Other")->get();
            $weddingFixedServices = VendorPackage::where('type',"Fixed Service")->get();
            $wedding_hotel = Hotels::where('id',$order_wedding->hotel_id)->first();
            $wedding_itineraries = Itinerary::where('order_id',$order->id)->orderBy('time','ASC')->get();
        }

        if ($order->service == "Wedding Package") {
            return view('emails.printContractWedding',compact('order'),[
                'now'=>$now,
                'title'=>"Confirmation Order",
                'email'=>$email,
                'agent'=>$agent,
                'admin'=>$admin,
                'order_link'=>$order_link,
                'extra_beds'=>$extra_beds,
                'order'=>$order,
                'tax'=>$tax,
                'optionalrates'=>$optionalrates,
                'usdrates'=>$usdrates,
                'cnyrates'=>$cnyrates,
                'twdrates'=>$twdrates,
                'business'=>$business,
                'optional_rate_orders'=>$optional_rate_orders,
                'attentions'=>$attentions,
                'optionalrate_meals'=>$optionalrate_meals,
                'data'=>$data,
                'agent'=>$agent,
                'guest_name'=>$guest_name,
                'hotels'=>$hotels,
                'villa'=>$villa,
                'nor'=>$nor,
                
                'rooms'=>$rooms,
                'order_wedding'=>$order_wedding,
                'weddingVenues'=>$weddingVenues,
                'weddingMakeups'=>$weddingMakeups,
                'weddingDecorations'=>$weddingDecorations,
                'weddingDinnerVenues'=>$weddingDinnerVenues,
                'weddingEntertainments'=>$weddingEntertainments,
                'weddingDocumentations'=>$weddingDocumentations,
                'weddingTransportations'=>$weddingTransportations,
                'weddingOthers'=>$weddingOthers,
                'weddingFixedServices'=>$weddingFixedServices,
                'wedding_itineraries'=>$wedding_itineraries,
                'wedding_hotel'=>$wedding_hotel,
                'bride'=>$bride,

                'special_day'=>$special_day,
                'special_date'=>$special_date,
                'extra_bed'=>$extra_bed,
                'extra_bed_id'=>$extra_bed_id,
                'extra_bed_price'=>$extra_bed_price,
                'pickup_people'=>$pickup_people,
                'guide'=>$guide,
                'driver'=>$driver,
                'reservation'=>$reservation,
                'invoice'=>$invoice,
                'amount'=>$amount,
                'jml_extra_bed'=>$jml_extra_bed,
                'extrabed_price'=>$extrabed_price,
                'cebp'=>$cebp,
                'opsirate_order_date'=>$opsirate_order_date,
                'opsirate_order_nog'=>$opsirate_order_nog,
                'opsirate_order_id'=>$opsirate_order_id,
                'opsirate_order_price_pax'=>$opsirate_order_price_pax,
                'opsirate_order_price_total'=>$opsirate_order_price_total,
                'promotion_disc'=>$promotion_disc,
                'bankAccount'=>$bankAccount,
                'order_duration'=>$order_duration,
                'price'=>$price,
                'airport_shuttles'=>$airport_shuttles,
                'doku_payment'=>$doku_payment,
            ]);
        }else{
            return view('emails.printContract',compact('order'),[
                'now'=>$now,
                'title'=>"Confirmation Order",
                'email'=>$email,
                'agent'=>$agent,
                'admin'=>$admin,
                'order_link'=>$order_link,
                'extra_beds'=>$extra_beds,
                'order'=>$order,
                'tax'=>$tax,
                'optionalrates'=>$optionalrates,
                'usdrates'=>$usdrates,
                'cnyrates'=>$cnyrates,
                'twdrates'=>$twdrates,
                'business'=>$business,
                'optional_rate_orders'=>$optional_rate_orders,
                'attentions'=>$attentions,
                'optionalrate_meals'=>$optionalrate_meals,
                'data'=>$data,
                'hotel'=>$hotel,
                'villa'=>$villa,
                'agent'=>$agent,
                'guest_name'=>$guest_name,
                'hotels'=>$hotels,
                'nor'=>$nor,
                'special_day'=>$special_day,
                'special_date'=>$special_date,
                'extra_bed'=>$extra_bed,
                'extra_bed_id'=>$extra_bed_id,
                'extra_bed_price'=>$extra_bed_price,
                'pickup_people'=>$pickup_people,
                'guide'=>$guide,
                'driver'=>$driver,
                'reservation'=>$reservation,
                'invoice'=>$invoice,
                'amount'=>$amount,
                'jml_extra_bed'=>$jml_extra_bed,
                'extrabed_price'=>$extrabed_price,
                'cebp'=>$cebp,
                'opsirate_order_date'=>$opsirate_order_date,
                'opsirate_order_nog'=>$opsirate_order_nog,
                'opsirate_order_id'=>$opsirate_order_id,
                'opsirate_order_price_pax'=>$opsirate_order_price_pax,
                'opsirate_order_price_total'=>$opsirate_order_price_total,
                'promotion_disc'=>$promotion_disc,
                'bankAccount'=>$bankAccount,
                'order_duration'=>$order_duration,
                'price'=>$price,
                'airport_shuttles'=>$airport_shuttles,
                'tax_doku'=>$tax_doku,
                'doku_virtual_account'=>$doku_virtual_account,
                'doku_payment'=>$doku_payment,
            ]);
        }
    }
    // FUNCTION ADMIN UPDATE ORDER
    public function fadmin_update_order(Request $request,$id){
        $order=Orders::findOrFail($id);
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $order->update([
            "include"=>$request->include,
            "additional_info"=>$request->additional_info,
            "itinerary"=>$request->itinerary,
            "note"=>$request->note,
            "handled_by"=>$handled_by,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$id,
            "action"=>"Update Order",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/orders-admin-$id")->with('success','Order has been updated');
    }
    // Function Activated =============================================================================================================>
    public function func_activate_order(Request $request,$id){
        $order=Orders::where('id',$id)->first();
        $agent = User::where('id', $order->sales_agent)->first();
        $hotels=Hotels::all();
        $hotel=Hotels::find($order->service_id)??null;
        $villa=Villas::find($order->service_id)??null;
        $user_id = Auth::User()->id;
        $usdrates = UsdRates::where('name','USD')->first();
        $cnyrates = UsdRates::where('name','CNY')->first();
        $twdrates = UsdRates::where('name','TWD')->first();
        $idrrates = UsdRates::where('name','IDR')->first();
        $tax = Tax::where('id',1)->first();
        $attentions = Attention::where('page','user-order-detail')->get();
        $business = BusinessProfile::where('id',1)->first();
        $optionalrates = OptionalRate::with('hotels')->get();
        $optionalrate_meals = OptionalRate::with('hotels')->where('type',"Meals")->get();
        $optional_rate_orders = OptionalRateOrder::where('orders_id', $id)->get();
        $extra_beds = ExtraBed::all();
        $now = Carbon::now();
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $orderno = $order->orderno;
        if ($order->service == "Wedding Package") {
            $status = "Active";
        }else{
            $status = "Approved";
        }
        $email = $order->email;
        $bank_account = BankAccount::where('id',1)->first();
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        if (isset($bank_account)) {
            $bank = $bank_account->id;
        }else {
            $bank = null;
        }
        if ($order->service == "Tour Package"){
            $amount = $order->price_total;
            if($order->duration == "1D"){
                $order_duration = 1;
            }elseif($order->duration == "2D/1N"){
                $order_duration = 1;
            }elseif($order->duration == "3D/2N"){
                $order_duration = 2;
            }elseif($order->duration == "4D/3N"){
                $order_duration = 3;
            }elseif($order->duration == "5D/4N"){
                $order_duration = 4;
            }elseif($order->duration == "6D/5N"){
                $order_duration = 5;
            }else{
                $order_duration = 1;
            }
        }elseif($order->servise == "Activity"){
            $amount = $order->price_total;
            $order_duration = $order->duration;
        }else{
            $amount = (($order->price_pax * $order->duration)*$order->number_of_room);
            $order_duration = $order->duration;
        }
        $pdsc = json_decode($order->promotion_disc);

        $ebp = json_decode($order->extra_bed_price);
        if(isset($ebp)){
            $extrabed_price = array_sum($ebp);
            $jml_extra_bed = 0;
            $cebp = count($ebp);
            for ($i=0; $i < $cebp; $i++) { 
                if ($ebp[$i]>0) {
                    $jml_extra_bed = $jml_extra_bed + 1;
                }
            }
        }else{
            $cebp = 0;
            $jml_extra_bed = 0;
            $extrabed_price = 0;
        }
        if (isset($pdsc)) {
            $promotion_disc = array_sum($pdsc);
        }else{
            $promotion_disc = 0;
        }
        
        // email
        $special_day = json_decode($order->special_day);
        $special_date = json_decode($order->special_date);
        $extra_bed = json_decode($order->extra_bed);
        $extra_bed_id = json_decode($order->extra_bed_id);
        $extra_bed_price = json_decode($order->extra_bed_price);
        $nor = $order->number_of_room;
        $order_link = 'https://online.balikamitour.com/detail-order-'.$order->id;
        $guest_name = Guests::where('rsv_id',$order->rsv_id)->get();
        $nor = $order->number_of_room;
        $pickup_people = Guests::where('id',$order->pickup_name)->first();
        $guide = Guide::where('id',$order->guide_id)->first();
        $driver = Drivers::where('id',$order->driver_id)->first();
        if (isset($extra_bed)) {
            $cextra_bed = count($extra_bed);
        }else {
            $cextra_bed = 0;
        }

        // WEDDING
        if ($order->service == "Wedding Package") {
            $bride = Brides::where('id',$order->pickup_name)->first();
            $order_wedding = OrderWedding::where('id',$order->wedding_order_id)->first();
            $rooms = HotelRoom::where('hotels_id',$order->subservice_id)->get();
            $weddingVenues = VendorPackage::where('type',"Wedding Venue")->get();
            $weddingMakeups = VendorPackage::where('type',"Make-up")->get();
            $weddingDecorations = VendorPackage::where('type',"Decoration")->get();
            $weddingDinnerVenues = VendorPackage::where('type',"Wedding Dinner")->get();
            $weddingEntertainments = VendorPackage::where('type',"Entertainment")->get();
            $weddingDocumentations = VendorPackage::where('type',"Documentation")->get();
            $weddingTransportations = Transports::all();
            $weddingOthers = VendorPackage::where('type',"Other")->get();
            $weddingFixedServices = VendorPackage::where('type',"Fixed Service")->get();
            $wedding_hotel = Hotels::where('id',$order_wedding->wedding_id)->first();
            $wedding_itineraries = Itinerary::where('order_id',$order->id)->orderBy('time','ASC')->get();
        }else{
            $bride = null;
            $order_wedding = null;
            $rooms = null;
            $weddingVenues = null;
            $weddingMakeups = null;
            $weddingDecorations = null;
            $weddingDinnerVenues = null;
            $weddingEntertainments = null;
            $weddingDocumentations = null;
            $weddingTransportations = null;
            $weddingOthers = null;
            $weddingFixedServices = null;
    
            $wedding_hotel = null;
            $wedding_itineraries = null;
        }
        
        
        $airport_shuttles = AirportShuttle::where('order_id',$id)->get();
        $order->update([
            "status"=>$status,
            "verified_by"=>Auth::user()->id,
            "handled_by"=>$handled_by,
        ]);
        $reservation->update([
            "status"=>"Active",
            "send"=>"yes",
        ]);
        // INVOICE
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        $inv_no = "INV-".$reservation->rsv_no;
        $due_date = date('Y-m-d', strtotime("-7 days", strtotime($order->checkin)));
        $final_price = $order->final_price;
        $total_idr = $final_price * $usdrates->rate;
        $total_cny = ceil($total_idr / $cnyrates->rate);
        $total_twd = ceil($total_idr / $twdrates->rate);
        $rate_usd = $usdrates->rate;
        $rate_cny = $cnyrates->rate;
        $rate_twd = $twdrates->rate;
        $currency_id = $request->currency;
        if ($currency_id == 1) {
            $balance = $final_price;
            $currency = "USD";
        }elseif($currency_id == 2){
            $balance = $total_cny;
            $currency = "CNY";
        }elseif($currency_id == 3){
            $balance = $total_twd;
            $currency = "TWD";
        }else{
            $balance = $total_idr;
            $currency = "IDR";
        }
        $bank_id = $request->bank;
        if ($invoice) {
            $invoice->update([
                "inv_no"=>$inv_no,
                "rsv_id"=>$reservation->id,
                "inv_date"=>$now,
                "due_date"=>$due_date,
                "total_usd"=>$final_price,
                "total_idr"=>$total_idr,
                "total_cny"=>$total_cny,
                "total_twd"=>$total_twd,
                "rate_usd"=>$rate_usd,
                "sell_usd"=>$usdrates->sell,
                "rate_twd"=>$rate_twd,
                "sell_twd"=>$twdrates->sell,
                "rate_cny"=>$rate_cny,
                "sell_cny"=>$cnyrates->sell,
                "bank_id"=>$bank_id,
                "currency_id"=>$currency_id,
                "balance"=>$balance,
            ]);
        }else{
            $invoice = new InvoiceAdmin([
                "inv_no"=>$inv_no,
                "rsv_id"=>$reservation->id,
                "created_by"=>Auth::user()->id,
                "agent_id"=>$order->sales_agent,
                "inv_date"=>$now,
                "due_date"=>$due_date,
                "total_usd"=>$final_price,
                "total_idr"=>$total_idr,
                "total_cny"=>$total_cny,
                "total_twd"=>$total_twd,
                "rate_usd"=>$rate_usd,
                "sell_usd"=>$usdrates->sell,
                "rate_twd"=>$rate_twd,
                "sell_twd"=>$twdrates->sell,
                "rate_cny"=>$rate_cny,
                "sell_cny"=>$cnyrates->sell,
                "bank_id"=>$bank_id,
                "currency_id"=>$currency_id,
                "balance"=>$balance,
            ]);
            $invoice->save();
        }
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Confirm Order",
            "url"=>$request->getClientIp(),
            "method"=>"Confirm",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        $admin = Auth::user()->where('id',$order->verified_by)->first();
        $bankAccount = BankAccount::where("id",$invoice->bank_id)->first();
        $doku_payment = DokuVirtualAccount::where('invoice_id',$invoice->id)->first();
        $paymentResponse = $this->dokuService->createPayment($order, $invoice, $agent);
        if ($paymentResponse) {
            $doku_virtual_account = $paymentResponse;
        }else{
            $doku_virtual_account = null;
        }
        $tax_doku = TaxDoku::where('id','1')->first();
        $data = [
            'now'=>$now,
            'title'=>"Confirmation Order - ".$order->orderno,
            'email'=>$email,
            'agent'=>$agent,
            'admin'=>$admin,
            'order_link'=>$order_link,
            'extra_beds'=>$extra_beds,
            'order'=>$order,
            'tax'=>$tax,
            'optionalrates'=>$optionalrates,
            'usdrates'=>$usdrates,
            'cnyrates'=>$cnyrates,
            'twdrates'=>$twdrates,
            'idrrates'=>$idrrates,
            'business'=>$business,
            'optional_rate_orders'=>$optional_rate_orders,
            'attentions'=>$attentions,
            'optionalrate_meals'=>$optionalrate_meals,
            'logoImage'=> public_path('storage/logo/bali-kami-tour-logo.png'),
            'guest_name'=>$guest_name,
            'hotels'=>$hotels,
            'villa'=>$villa,
            'hotel'=>$hotel,
            'special_day'=>$special_day,
            'special_date'=>$special_date,
            'extra_bed'=>$extra_bed,
            'extra_bed_id'=>$extra_bed_id,
            'extra_bed_price'=>$extra_bed_price,
            'nor'=>$nor,
            'pickup_people'=>$pickup_people,
            'guide'=>$guide,
            'driver'=>$driver,
            'reservation'=>$reservation,
            'invoice'=>$invoice,
            'amount'=>$amount,
            'jml_extra_bed'=>$jml_extra_bed,
            'extrabed_price'=>$extrabed_price,
            'cebp'=>$cebp,
            'promotion_disc'=>$promotion_disc,
            'bankAccount'=>$bankAccount,
            'order_duration'=>$order_duration,
            'arrival_flight' =>$order->arrival_flight,
            'arrival_time' =>$order->arrival_time,
            'departure_flight'=>$order->departure_flight,
            'departure_time'=>$order->departure_time,
            'airport_shuttles'=>$airport_shuttles,
            'order_wedding'=>$order_wedding,
            'rooms'=>$rooms,
            'wedding_itineraries'=>$wedding_itineraries,
            'wedding_hotel'=>$wedding_hotel,
            'weddingVenues'=>$weddingVenues,
            'weddingMakeups'=>$weddingMakeups,
            'weddingDecorations'=>$weddingDecorations,
            'weddingDinnerVenues'=>$weddingDinnerVenues,
            'weddingEntertainments'=>$weddingEntertainments,
            'weddingDocumentations'=>$weddingDocumentations,
            'weddingTransportations'=>$weddingTransportations,
            'weddingOthers'=>$weddingOthers,
            'weddingFixedServices'=>$weddingFixedServices,
            'bride'=>$bride,
            'doku_payment'=>$doku_payment,
            'doku_virtual_account'=>$doku_virtual_account,
            'tax_doku'=>$tax_doku,
        ];
        
        if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf")) {
            File::delete("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
        }
        $pdf = PDF::loadView('emails.orderContractEn', $data);
        $pdf->save("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");

        if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf")) {
            File::delete("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        }
        $pdf = PDF::loadView('emails.orderContractZh', $data);
        $pdf->save("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");

        if (config('filesystems.default') == 'public'){
            $contract_en_path =realpath("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
            $contract_zh_path =realpath("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        }else {
            $contract_en_path = storage::url("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
            $contract_zh_path = storage::url("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        }

        

        Mail::send('emails.confirmationOrder', $data, function($message)use($data, $contract_en_path, $contract_zh_path) {
            $message->to($data["email"])
                ->subject($data["title"])
                ->attach($contract_en_path)
                ->attach($contract_zh_path);
        });
        return redirect("/orders-admin-$id");
    }

    public function func_generate_doku_payment(Request $request, $id){
        $order=Orders::find($id);
        $agent = User::where('id', $order->sales_agent)->first();
        $reservation = Reservation::find($order->rsv_id);
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        $paymentResponse = $this->dokuService->generateDokuPayment($order, $invoice, $agent);
        // dd($paymentResponse, );
        return redirect("/orders-admin-$id");
    }

    public function fgenerate_invoice(Request $request,$id){
        $order=Orders::with(['optional_rate_orders'])->find($id);
        $agent = User::where('id', $order->sales_agent)->first();
        $hotels=Hotels::all();
        $villa=Villas::find($order->service_id);
        $user_id = Auth::User()->id;
        $usdrates = UsdRates::where('name','USD')->first();
        $cnyrates = UsdRates::where('name','CNY')->first();
        $twdrates = UsdRates::where('name','TWD')->first();
        $idrrates = UsdRates::where('name','IDR')->first();
        $tax = Tax::where('id',1)->first();
        $attentions = Attention::where('page','user-order-detail')->get();
        $business = BusinessProfile::where('id',1)->first();
        $optionalrates = OptionalRate::with('hotels')->get();
        $optionalrate_meals = OptionalRate::with('hotels')->where('type',"Meals")->get();
        $optional_rate_orders = $order->optional_rate_orders;
        $extra_beds = ExtraBed::all();
        $now = Carbon::now();
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $orderno = $order->orderno;
        $email = $order->email;
        $bank_account = BankAccount::where('id',1)->first();
        if (isset($bank_account)) {
            $bank = $request->bank;
        }else {
            $bank = null;
        }
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Confirm Order",
            "url"=>$request->getClientIp(),
            "method"=>"Confirm",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        
        if ($order->service == "Tour Package"){
            $amount = $order->price_total;
            if($order->duration == "1D"){
                $order_duration = 1;
            }elseif($order->duration == "2D/1N"){
                $order_duration = 1;
            }elseif($order->duration == "3D/2N"){
                $order_duration = 2;
            }elseif($order->duration == "4D/3N"){
                $order_duration = 3;
            }elseif($order->duration == "5D/4N"){
                $order_duration = 4;
            }elseif($order->duration == "6D/5N"){
                $order_duration = 5;
            }else{
                $order_duration = 1;
            }
        }elseif($order->servise == "Activity"){
            $amount = $order->price_total;
            $order_duration = $order->duration;
        }else{
            $amount = $order->normal_price;
            $order_duration = $order->duration;
        }

        $bankAccount = BankAccount::where("id",1)->first();
        $pdsc = json_decode($order->promotion_disc);

        $ebp = json_decode($order->extra_bed_price);
        if(isset($ebp)){
            $extrabed_price = array_sum($ebp);
            $jml_extra_bed = 0;
            $cebp = count($ebp);
            for ($i=0; $i < $cebp; $i++) { 
                if ($ebp[$i]>0) {
                    $jml_extra_bed = $jml_extra_bed + 1;
                }
            }
        }else{
            $cebp = 0;
            $jml_extra_bed = 0;
            $extrabed_price = 0;
        }
        if (isset($pdsc)) {
            $promotion_disc = array_sum($pdsc);
        }else{
            $promotion_disc = 0;
        }
        
        // email
        $special_day = json_decode($order->special_day);
        $special_date = json_decode($order->special_date);
        $extra_bed = json_decode($order->extra_bed);
        $extra_bed_id = json_decode($order->extra_bed_id);
        $extra_bed_price = json_decode($order->extra_bed_price);
        $nor = $order->number_of_room;
        $order_link = 'https://online.balikamitour.com/detail-order-'.$order->id;
        $admin = Auth::user()->where('id',$order->verified_by)->first();
        $guest_name = Guests::where('rsv_id',$order->rsv_id)->get();
        $nor = $order->number_of_room;
        $pickup_people = Guests::where('id',$order->pickup_name)->first();
        $guide = Guide::where('id',$order->guide_id)->first();
        $driver = Drivers::where('id',$order->driver_id)->first();
        if (isset($extra_bed)) {
            $cextra_bed = count($extra_bed);
        }else {
            $cextra_bed = 0;
        }


        // INVOICE
        $inv_no = "INV-".$reservation->rsv_no;
        $due_date = date('Y-m-d', strtotime("-3 days", strtotime($order->checkin)));
        $rate_usd = $usdrates->rate;
        $rate_cny = $cnyrates->rate;
        $rate_twd = $twdrates->rate;
        $total_idr = ceil($order->final_price * $rate_usd);
        $total_cny = ceil($total_idr / $rate_cny);
        $total_twd = ceil($total_idr / $rate_twd);
        $currency_id = $request->currency;
        if ($currency_id == 1) {
            $balance = $order->final_price;
            $currency = "USD";
        }elseif($currency_id == 2){
            $balance = $total_cny;
            $currency = "CNY";
        }elseif($currency_id == 3){
            $balance = $total_twd;
            $currency = "TWD";
        }else{
            $balance = $total_idr;
            $currency = "IDR";
        }
        $invoice = new InvoiceAdmin([
            "inv_no"=>$inv_no,
            "rsv_id"=>$reservation->id,
            "inv_date"=>$now,
            "due_date"=>$due_date,
            "total_usd"=>$order->final_price,
            "total_idr"=>$total_idr,
            "total_cny"=>$total_cny,
            "total_twd"=>$total_twd,
            "bank_id"=>$bank,
            "rate_usd"=>$rate_usd,
            "sell_usd"=>$usdrates->sell,
            "rate_cny"=>$rate_cny,
            "sell_cny"=>$cnyrates->sell,
            "rate_twd"=>$rate_twd,
            "sell_twd"=>$twdrates->sell,
            "currency_id"=>$currency_id,
            "created_by"=>$user_id,
            "agent_id"=>$order->sales_agent,
            "balance"=>$balance,
        ]);
        $invoice->save();
        $airport_shuttles = AirportShuttle::where('order_id',$id)->get();
        $tax_doku = TaxDoku::where('id','1')->first();
        $doku_payment = DokuVirtualAccount::where('invoice_id',$invoice->id)->first();
        $data = [
            'now'=>$now,
            'title'=>"Confirmation Order",
            'email'=>$email,
            'agent'=>$agent,
            'admin'=>$admin,
            'order_link'=>$order_link,
            'extra_beds'=>$extra_beds,
            'order'=>$order,
            'tax'=>$tax,
            'optionalrates'=>$optionalrates,
            'usdrates'=>$usdrates,
            'business'=>$business,
            'optional_rate_orders'=> $optional_rate_orders,
            'attentions'=>$attentions,
            'optionalrate_meals'=>$optionalrate_meals,
            'logoImage'=> public_path('storage/logo/bali-kami-tour-logo.png'),
            'guest_name'=>$guest_name,
            'hotels'=>$hotels,
            'villa'=>$villa,
            'special_day'=>$special_day,
            'special_date'=>$special_date,
            'extra_bed'=>$extra_bed,
            'extra_bed_id'=>$extra_bed_id,
            'extra_bed_price'=>$extra_bed_price,
            'nor'=>$nor,
            'pickup_people'=>$pickup_people,
            'guide'=>$guide,
            'driver'=>$driver,
            'reservation'=>$reservation,
            'invoice'=>$invoice,
            'amount'=>$amount,
            'jml_extra_bed'=>$jml_extra_bed,
            'extrabed_price'=>$extrabed_price,
            'cebp'=>$cebp,
            'promotion_disc'=>$promotion_disc,
            'bankAccount'=>$bankAccount,
            'order_duration'=>$order_duration,
            'arrival_flight' =>$order->arrival_flight,
            'arrival_time' =>$order->arrival_time,
            'departure_flight'=>$order->departure_flight,
            'departure_time'=>$order->departure_time,
            'airport_shuttles'=>$airport_shuttles,
            'tax_doku'=>$tax_doku,
            'doku_payment'=>$doku_payment,
        ];
        if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf")) {
            File::delete("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
        }
        $pdf = PDF::loadView('emails.orderContractEn', $data);
        $pdf->save("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");

        if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf")) {
            File::delete("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        }
        $pdf = PDF::loadView('emails.orderContractZh', $data);
        $pdf->save("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");

        return redirect()->back()->with('success','Invoice generate successfuly');
    }

    public function test_contrat(Request $request,$id){
        $order=Orders::findOrFail($id);
        $agent = User::where('id', $order->sales_agent)->first();
        $hotels=Hotels::all();
        $hotel=Hotels::find($order->service_id);
        $villa=Villas::find($order->service_id);
        $user_id = Auth::User()->id;
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $attentions = Attention::where('page','user-order-detail')->get();
        $business = BusinessProfile::where('id',1)->first();
        $optionalrates = OptionalRate::with('hotels')->get();
        $optionalrate_meals = OptionalRate::with('hotels')->where('type',"Meals")->get();
        $optional_rate_orders = OptionalRateOrder::where('orders_id', $id)->get();
        $extra_beds = ExtraBed::all();
        $now = Carbon::now();
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $orderno = $order->orderno;
        $email = $order->email;
        $bank_account = BankAccount::where('id',1)->first();
        if (isset($bank_account)) {
            $bank = $bank_account->id;
        }else {
            $bank = null;
        }
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Confirm Order",
            "url"=>$request->getClientIp(),
            "method"=>"Confirm",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        
        if ($order->service == "Tour Package"){
            $amount = $order->price_total;
            if($order->duration == "1D"){
                $order_duration = 1;
            }elseif($order->duration == "2D/1N"){
                $order_duration = 1;
            }elseif($order->duration == "3D/2N"){
                $order_duration = 2;
            }elseif($order->duration == "4D/3N"){
                $order_duration = 3;
            }elseif($order->duration == "5D/4N"){
                $order_duration = 4;
            }elseif($order->duration == "6D/5N"){
                $order_duration = 5;
            }else{
                $order_duration = 1;
            }
        }elseif($order->servise == "Activity"){
            $amount = $order->price_total;
            $order_duration = $order->duration;
        }else{
            $amount = $order->normal_price;
            $order_duration = $order->duration;
        }

        $bankAccount = BankAccount::where("id",1)->first();
        $pdsc = json_decode($order->promotion_disc);

        $ebp = json_decode($order->extra_bed_price);
        if(isset($ebp)){
            $extrabed_price = array_sum($ebp);
            $jml_extra_bed = 0;
            $cebp = count($ebp);
            for ($i=0; $i < $cebp; $i++) { 
                if ($ebp[$i]>0) {
                    $jml_extra_bed = $jml_extra_bed + 1;
                }
            }
        }else{
            $cebp = 0;
            $jml_extra_bed = 0;
            $extrabed_price = 0;
        }

        // if (isset($optional_rate_orders->optional_rate_id)){
        //     $opsirate_order_date = json_decode($optional_rate_orders->service_date);
        //     $opsirate_order_nog = json_decode($optional_rate_orders->number_of_guest);
        //     $opsirate_order_id = json_decode($optional_rate_orders->optional_rate_id);
        //     $opsirate_order_price_pax = json_decode($optional_rate_orders->price_pax);
        //     $opsirate_order_price_total = json_decode($optional_rate_orders->price_total);
        // }else{
        //     $opsirate_order_date = null;
        //     $opsirate_order_nog = null;
        //     $opsirate_order_id = null;
        //     $opsirate_order_price_pax = null;
        //     $opsirate_order_price_total = null;
        // }

        
        if (isset($pdsc)) {
            $promotion_disc = array_sum($pdsc);
        }else{
            $promotion_disc = 0;
        }
        
        // email
        $special_day = json_decode($order->special_day);
        $special_date = json_decode($order->special_date);
        $extra_bed = json_decode($order->extra_bed);
        $extra_bed_id = json_decode($order->extra_bed_id);
        $extra_bed_price = json_decode($order->extra_bed_price);
        $nor = $order->number_of_room;
        $order_link = 'https://online.balikamitour.com/detail-order-'.$order->id;
        $admin = Auth::user()->where('id',$order->verified_by)->first();
        $guest_name = Guests::where('rsv_id',$order->rsv_id)->get();
        $nor = $order->number_of_room;
        $pickup_people = Guests::where('id',$order->pickup_name)->first();
        $guide = Guide::where('id',$order->guide_id)->first();
        $driver = Drivers::where('id',$order->driver_id)->first();
        if (isset($extra_bed)) {
            $cextra_bed = count($extra_bed);
        }else {
            $cextra_bed = 0;
        }


        // INVOICE
        $inv_no = "INV-".$reservation->rsv_no;
        $due_date = date('Y-m-d', strtotime("-3 days", strtotime($order->checkin)));
        $total_idr = ceil($order->final_price / $usdrates->rate);
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        $dataInvoice = [
            "invoice" => $invoice,
            "inv_no"=>$inv_no,
            "rsv_id"=>$reservation->id,
            "inv_date"=>$now,
            "due_date"=>$due_date,
            "total_usd"=>$order->final_price,
            "total_idr"=>$total_idr,
            "bank_id"=>$bank,
        ];
        $airport_shuttles = AirportShuttle::where('order_id',$id)->get();
        $doku_payment = DokuVirtualAccount::where('invoice_id',$invoice->id)->first();
        $doku_virtual_account = DokuVirtualAccount::where('invoice_id', $invoice->id)
        ->where('expired_date','>=',$now)
        ->first();
        $tax_doku = TaxDoku::where('id','1')->first();
        $data = [
            'now'=>$now,
            'title'=>"Confirmation Order",
            'email'=>$email,
            'agent'=>$agent,
            'admin'=>$admin,
            'order_link'=>$order_link,
            'extra_beds'=>$extra_beds,
            'order'=>$order,
            'tax'=>$tax,
            'optionalrates'=>$optionalrates,
            'usdrates'=>$usdrates,
            'business'=>$business,
            'optional_rate_orders'=>$optional_rate_orders,
            'attentions'=>$attentions,
            'optionalrate_meals'=>$optionalrate_meals,
            'logoImage'=> public_path('storage/logo/bali-kami-tour-logo.png'),
            'guest_name'=>$guest_name,
            'hotels'=>$hotels,
            'villa'=>$villa,
            'special_day'=>$special_day,
            'special_date'=>$special_date,
            'extra_bed'=>$extra_bed,
            'extra_bed_id'=>$extra_bed_id,
            'extra_bed_price'=>$extra_bed_price,
            'nor'=>$nor,
            'pickup_people'=>$pickup_people,
            'guide'=>$guide,
            'driver'=>$driver,
            'reservation'=>$reservation,
            'amount'=>$amount,
            'jml_extra_bed'=>$jml_extra_bed,
            'extrabed_price'=>$extrabed_price,
            'cebp'=>$cebp,
            // 'opsirate_order_date'=>$opsirate_order_date,
            // 'opsirate_order_nog'=>$opsirate_order_nog,
            // 'opsirate_order_id'=>$opsirate_order_id,
            // 'opsirate_order_price_pax'=>$opsirate_order_price_pax,
            // 'opsirate_order_price_total'=>$opsirate_order_price_total,
            'promotion_disc'=>$promotion_disc,
            'bankAccount'=>$bankAccount,
            'order_duration'=>$order_duration,
            'arrival_flight' =>$order->arrival_flight,
            'arrival_time' =>$order->arrival_time,
            'departure_flight'=>$order->departure_flight,
            'departure_time'=>$order->departure_time,
            'airport_shuttles'=>$airport_shuttles,
            'hotel'=>$hotel,
            'doku_virtual_account'=>$doku_virtual_account,
            'doku_payment'=>$doku_payment,
            'tax_doku'=>$tax_doku,
        ];
        // if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf")) {
        //     File::delete("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
        // }
        // $pdf = PDF::loadView('emails.orderContractEn', $data);
        // $pdf->save("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");

        // if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf")) {
        //     File::delete("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        // }
        // $pdf = PDF::loadView('emails.orderContractZh', $data);
        // $pdf->save("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");

        return view('emails.orderContractEn', $data, $dataInvoice, compact('order'),[
            'business' => $business,
            'reservation' => $reservation,
            'agent' => $agent,
        ]);
    }





    public function func_send_confirmation (Request $request,$id){
        $order=Orders::findOrFail($id);
        $user_id = Auth::User()->id;
        $agent = User::where('id', $order->user_id)->first();
        $order_link = 'https://online.balikamitour.com/detail-order-'.$order->id;
        $admin = Auth::user()->where('id',$order->verified_by)->first();
        $agent = Auth::user()->where('id',$order->sales_agent)->first();
        $email = $order->email;
        // Tambahan data
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $inv = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        $inv_no = $inv->inv_no;
        $reservation->update([
            "status"=>"Active",
            "send"=>"yes",
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Send Confirmation",
            "url"=>$request->getClientIp(),
            "method"=>"Send",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        $title = "Confirmation Order ".$reservation->rsv_no;
        $data = [
            'email'=>$email,
            'title'=>$title,
            'order'=>$order,
            'admin'=>$admin,
            'order_link'=>$order_link,
        ];
        if (config('filesystems.default') == 'public'){
            $contract_en_path =realpath("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
            $contract_zh_path =realpath("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        }else {
            $contract_en_path = storage::url("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
            $contract_zh_path = storage::url("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        }

        set_time_limit(300);
        Mail::send('emails.confirmationOrder', $data, function($message)use($data, $contract_en_path, $contract_zh_path) {
            $message->to($data["email"])
                ->subject($data["title"])
                ->attach($contract_en_path)
                ->attach($contract_zh_path);
        });
        
        return redirect()->back()->with('success','Confirmation email send successfuly');
    }



    // FUNCTION RESEND CONFIRMATION ORDER
    public function resend_confirmation_order(Request $request,$id){
        $order = Orders::findOrFail($id);
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $inv = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        $inv_no = $inv->inv_no;
        $email = $order->email;
        $adm = Auth::user()->where('id',$order->verified_by)->first();
        if (isset($adm)) {
            $admin = $adm;
        }else{
            $admin = Auth::user()->where('id',1)->first();
        }
        $title = "Confirmation Order ".$reservation->rsv_no;
        $order_link = 'https://online.balikamitour.com/detail-order-'.$order->id;
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Resend Confirmation",
            "url"=>$request->getClientIp(),
            "method"=>"Resend",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        $data = [
            'email'=>$email,
            'title'=>$title,
            'order'=>$order,
            'admin'=>$admin,
            'order_link'=>$order_link,
        ];
        if (config('filesystems.default') == 'public'){
            $contract_en_path =realpath("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
            $contract_zh_path =realpath("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        }else {
            $contract_en_path = storage::url("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
            $contract_zh_path = storage::url("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        }

        set_time_limit(300);
        Mail::send('emails.confirmationOrder', $data, function($message)use($data, $contract_en_path, $contract_zh_path) {
            $message->to($data["email"])
                ->subject($data["title"])
                ->attach($contract_en_path)
                ->attach($contract_zh_path);
        });
        return redirect()->back()->with('success','Confirmation email send successfuly');
    }
    public function fsend_approval_email(Request $request,$id){
        $order = Orders::findOrFail($id);
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $inv = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        $title = "Urgent: Please Approve Your Order Immediately";
        $email = $order->email;
        $order_link = 'https://online.balikamitour.com/detail-order-'.$order->id;
        if (isset($adm)) {
            $admin = $adm;
        }else{
            $admin = Auth::user()->where('id',1)->first();
        }
        $data = [
            'email'=>$email,
            'title'=>$title,
            'order'=>$order,
            'admin'=>$admin,
            'order_link'=>$order_link,
            'invs'=>$inv,
        ];
        set_time_limit(120);
        Mail::send('emails.approvalEmail', $data, function($message)use($data) {
            $message->to($data["email"])
                ->subject($data["title"]);
        });
        return redirect()->back()->with('success','Approval email send successfuly');
    }
    // FUNCTION UPDATE CONFIRMATION ORDER
    public function func_update_confirmation_number(Request $request,$id){
        $now = Carbon::now();
        $order = Orders::findOrFail($id);
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $order->update([
            "confirmation_order"=>$request->confirmation_order,
            "handled_by"=>$handled_by,
            "handled_date"=>$now,
        ]);
        $order_log =new OrderLog([
            "order_id"=> $id,
            "action"=>"Add Confirmation Number",
            "url"=>$request->getClientIp(),
            "method"=>"Add",
            "agent"=>Auth::user()->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect()->back()->with('success','Confirmation Order has been Update');
    }
// Function Updated Rejected =============================================================================================================>
    public function func_update_order_rejected(Request $request,$id){
        $order_rejected=Orders::findOrFail($id);
        $orderno = $order_rejected->orderno;
        $traveldate = date('Y-m-d', strtotime($request->traveldate));
        $status = "Rejected";
        $action = "Rejected Order";
        $checkin = date('Y-m-d', strtotime($request->checkin));
        $checkout = date('Y-m-d', strtotime($request->checkout));
        $order_rejected->update([
            "status"=>$status,
            "msg"=>$request->msg,
        ]);
        // USER LOG
        $action = "Update";
        $service = "Order";
        $subservice = $orderno;
        $page = "order-admin";
        $note = "Update order: ".$orderno." to : ".$status;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$id,
            "page"=>$page,
            "user_id"=>$request->author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note,
        ]);
        $user_log->save();
        $order_log =new OrderLog([
            "order_id"=>$order_rejected->id,
            "action"=>"Rejected Order",
            "url"=>$request->getClientIp(),
            "method"=>"Rejected",
            "agent"=>$order_rejected->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/orders-admin#rejectedorders")->with('success','Order has been successfully updated!');
    }
// Function Updated Discounts =============================================================================================================>
    public function func_update_order_discounts(Request $request,$id){
        $order=Orders::findOrFail($id);
        $price_total = $order->price_total;
        $discounts = $request->discounts;
        $final_price = $price_total - $discounts;
        $orderno =$order->orderno;
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $order->update([
            "discounts"=>$discounts,
            "alasan_discounts"=>$request->alasan_discounts,
            "final_price"=>$final_price,
            "handled_by"=>$handled_by,
        ]);
        //dd($order);
       // USER LOG
        $action = "Update Order Discounts";
        $service = "Order";
        $subservice = $orderno;
        $page = "order-admin-detail";
        $note = "Update Order Discount: from: ".$order->discounts." to : ".$request->discounts.", Because ".$request->alasan_discounts;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$id,
            "page"=>$page,
            "user_id"=>$request->author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note,
        ]);
        $user_log->save();
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Update Discount",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/orders-admin-$id")->with('success','Discounts has been successfully updated!');
    }
// Function Updated Discounts =============================================================================================================>
    public function func_finalization_order(Request $request,$id){
        $user = Auth::User();
        $order=Orders::findOrFail($id);
        if (!$order) {
            return redirect("/orders-admin")->with('error','The order can not find!');
        }
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        $doku_payment = DokuVirtualAccount::where('invoice_id', $invoice->id);
        if ($order->handled_by == $user->id) {
            $status = "Paid";
            $order->update([
                "status"=>$status,
            ]);
            $reservation->update([
                "status"=>$status,
            ]);
            $invoice->update([
                "balance"=>0,
            ]);
            $doku_payment->update([
                "status"=>$status,
            ]);
            $order_log =new OrderLog([
                "order_id"=>$order->id,
                "action"=>"Finalization",
                "url"=>$request->getClientIp(),
                "method"=>"Update",
                "agent"=>$order->name,
                "admin"=>Auth::user()->id,
            ]);
            $order_log->save();
            return redirect("/orders-admin-$id")->with('success','Discounts has been successfully updated!');
        }else{
            return redirect("/orders-admin")->with('error','You can not update the order!');
        }

    }
// Function Updated Discounts =============================================================================================================>
    public function func_remove_order_discounts(Request $request,$id){
        $order=Orders::findOrFail($id);
        if (!$order->handled_by) {
            $handled_by = Auth::user()->id;
        }else{
            $handled_by = $order->handled_by;
        }
        $final_price = $order->final_price + $order->discounts;
        $discounts = 0;
        $orderno =$order->orderno;
        $order->update([
            "discounts"=> $discounts,
            "alasan_discounts"=>NULL,
            "final_price"=>$final_price,
            "handled_by"=>$handled_by,
        ]);
        //dd($order);
       // USER LOG
       $action = "Remove Order Discounts";
       $service = "Order";
       $subservice = $orderno;
       $page = "order-admin-detail";
       $note = "Remove Order Discount: from: ".$order->discounts." to : ".$request->discounts;
       $user_log =new UserLog([
           "action"=>$action,
           "service"=>$service,
           "subservice"=>$subservice,
           "subservice_id"=>$id,
           "page"=>$page,
           "user_id"=>$request->author,
           "user_ip"=>$request->getClientIp(),
           "note" =>$note,
       ]);
       $user_log->save();
       $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Remove Discounts",
            "url"=>$request->getClientIp(),
            "method"=>"Remove",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/orders-admin-$id")->with('success','Discounts has been removed!');
    }
// Function Updated Invalid =============================================================================================================>
    public function func_update_order_invalid(Request $request,$id){
        $order_invalid=Orders::findOrFail($id);
        $orderno = $order_invalid->orderno;
        $status = "Invalid";
        $checkin = date('Y-m-d', strtotime($request->checkin));
        $checkout = date('Y-m-d', strtotime($request->checkout));
        $order_invalid->update([
            "status"=>$status,
            "msg"=>$request->msg,
        ]);
        // USER LOG
        $action = "Update";
        $service = "Order";
        $subservice = $orderno;
        $page = "order-admin";
        $note = "Update order: ".$orderno. " to : ". $status;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$id,
            "page"=>$page,
            "user_id"=>$request->author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note,
        ]);
        $user_log->save();
        $order_log =new OrderLog([
            "order_id"=>$order_invalid->id,
            "action"=>"Invalid Order",
            "url"=>$request->getClientIp(),
            "method"=>"Invalid",
            "agent"=>$order_invalid->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/orders-admin#invalidorders")->with('success','Order has been successfully updated!');
    }
// Function Archive Invalid =============================================================================================================>
    public function func_archive_order(Request $request,$id){
        $order_archive=Orders::findOrFail($id);
        $orderno = $order_archive->orderno;
        $status = "Archive";
        $order_archive->update([
            "status"=>$status,
            "msg"=>$request->msg,
        ]);
        // USER LOG
        $action = "Archived";
        $service = "Order";
        $subservice = $orderno;
        $page = "order-admin";
        $note = "Archived order: ".$orderno. " to : ". $status;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>$id,
            "page"=>$page,
            "user_id"=>$request->author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note,
        ]);
        $user_log->save();
        $order_log =new OrderLog([
            "order_id"=>$id,
            "action"=>"Archive Order",
            "url"=>$request->getClientIp(),
            "method"=>"Archive",
            "agent"=>$order_archive->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/orders-admin#invalidorders")->with('success','Order has been successfully archived!');
    }
    // Function Add Order Note =========================================================================================>
    public function func_add_order_note(Request $request, $id)
        {
            $order = Orders::findOrFail($id);
            if (!$order->handled_by) {
                $handled_by = Auth::user()->id;
            }else{
                $handled_by = $order->handled_by;
            }
            $order_note =new OrderNote([
                "order_id"=>$order->id,
                "note"=>$request->order_note,
                "user_id"=>Auth::user()->id,
                "status"=>$request->status,
                "handled_by"=>$handled_by,
            ]);
            $order_note->save();
            return redirect()->back()->with('success','Note has been add to the order');
        }
// Function Add Order Note =========================================================================================>
    public function func_add_order_wedding_note(Request $request, $id)
        {
            $orderWedding = OrderWedding::findOrFail($id);
            if (!$orderWedding->handled_by) {
                $handled_by = Auth::user()->id;
            }else{
                $handled_by = $orderWedding->handled_by;
            }
            $order_note =new OrderNote([
                "order_wedding_id"=>$orderWedding->id,
                "note"=>$request->order_note,
                "user_id"=>Auth::user()->id,
                "status"=>$request->status,
                "handled_by"=>$handled_by,
            ]);
            $order_note->save();
            return redirect()->back()->with('success','Note has been add to the order');
        }

    
    // Admin Add Payment Receipt =============================================================================================================>
    public function admin_add_payment_confirmation(Request $request,$id)
    {
        $now = Carbon::now();
        $order = Orders::findOrFail($id);
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        $status="Pending";
        $file=$request->file("receipt_name");
        $receipt_name=$invoice->inv_no.'_'.time().'_'.$file->getClientOriginalName();
        $file->move("storage/receipt/",$receipt_name);
        $receipt = new PaymentConfirmation([
            "receipt_img"=>$receipt_name,
            "inv_id"=>$invoice->id,
            "status"=>$status,
        ]);
        $receipt->save();
        $order_log = new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Upload Receipt",
            "url"=>$request->getClientIp(),
            "method"=>"Upload",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/orders-admin-$order->id")->with('success','Payment proof has been updated.');
    }
    // Admin Add Payment Receipt to ORDER WEDDING =============================================================================================================>
    public function admin_add_payment_confirmation_to_order_wedding(Request $request,$id)
    {
        $now = Carbon::now();
        $order = OrderWedding::find($id);
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        $status="Pending";
        
        if($request->hasFile("desktop_receipt_name")){
            $file=$request->file("desktop_receipt_name");
            $receipt_name=$invoice->inv_no.'_'.time().'_'.$file->getClientOriginalName();
            $file->move("storage/receipt/weddings/",$receipt_name);
            $receipt = new PaymentConfirmation([
                "receipt_img"=>$receipt_name,
                "inv_id"=>$invoice->id,
                "status"=>$status,
            ]);
            $receipt->save();
            $order_log = new OrderLog([
                "order_wedding_id"=>$order->id,
                "action"=>"Upload Receipt",
                "url"=>$request->getClientIp(),
                "method"=>"Upload",
                "agent"=>$order->name,
                "admin"=>Auth::user()->id,
            ]);
            $order_log->save();
            return redirect("/validate-orders-wedding-$order->id")->with('success','Payment proof has been updated.');
        }elseif ($request->hasFile("mobile_receipt_name")) {
            $file=$request->file("mobile_receipt_name");
            $receipt_name=$invoice->inv_no.'_'.time().'_'.$file->getClientOriginalName();
            $file->move("storage/receipt/weddings/",$receipt_name);
            $receipt = new PaymentConfirmation([
                "receipt_img"=>$receipt_name,
                "inv_id"=>$invoice->id,
                "status"=>$status,
            ]);
            $receipt->save();
            $order_log = new OrderLog([
                "order_wedding_id"=>$order->id,
                "action"=>"Upload Receipt",
                "url"=>$request->getClientIp(),
                "method"=>"Upload",
                "agent"=>$order->name,
                "admin"=>Auth::user()->id,
            ]);
            $order_log->save();
            return redirect("/validate-orders-wedding-$order->id")->with('success','Payment proof has been updated.');
        }else{
            return redirect("/validate-orders-wedding-$order->id")->with('error','Please try again');
        }
    }

// ===============================================================================================================================================================
// RECEIPT 
// ===============================================================================================================================================================
     // Function Confirmation Payment =============================================================================================================>
    public function fconfirmation_payment(Request $request,$id){
        $usdrate = UsdRates::where('name','USD')->first();
        $twdrate = UsdRates::where('name','TWD')->first();
        $cnyrate = UsdRates::where('name','CNY')->first();
        $receipt = PaymentConfirmation::find($id);
        $invoice = InvoiceAdmin::find($receipt->inv_id);
        $reservation = Reservation::find($invoice->rsv_id);
        $order = Orders::where("rsv_id",$reservation->id)->first();
        $agent = Auth::user()->where('id',$order->sales_agent)->first();
        $payment_date = date('Y-m-d',strtotime($request->payment_date));
        $status = $request->status;
        if ($request->kurs_id == "1") {
            $kurs_rate = $usdrate->rate;
        }elseif($request->kurs_id == "3") {
            $kurs_rate = $twdrate->rate;
        }elseif($request->kurs_id == "2") {
            $kurs_rate = $cnyrate->rate;
        }else{
            $kurs_rate = 1;
        }
        $transaction_code = "ORD".$order->orderno."/INV".$receipt->inv_id."/PAY".$receipt->id;
       
        // PAYMENT CALCULATE
            $rate_usd = $invoice->rate_usd;
            $sell_usd = $invoice->sell_usd;
            $rate_twd = $invoice->rate_twd;
            $sell_twd = $invoice->sell_twd;
            $rate_cny = $invoice->rate_cny;
            $sell_cny = $invoice->sell_cny;
            $invoice_balance = $invoice->balance;
            if ($invoice->currency->name == "USD") {
                if ($request->kurs_id == "1") {
                    $payment_usd = $request->amount;
                }elseif($request->kurs_id == "2"){
                    $payment_amount_idr = $request->amount * $rate_cny;
                    $payment_usd = ceil($payment_amount_idr / $sell_usd);
                }elseif($request->kurs_id == "3"){
                    $payment_amount_idr = $request->amount * $rate_twd;
                    $payment_usd = ceil($payment_amount_idr / $sell_usd);
                }else{
                    $payment_amount_idr = $request->amount;
                    $payment_usd = ceil($payment_amount_idr / $sell_usd);
                }
                $balance = $invoice_balance - $payment_usd;
                $new_balance = $invoice_balance + $payment_usd;
            }elseif($invoice->currency->name == "CNY") {
                if ($request->kurs_id == "1") {
                    $payment_amount_idr = $request->amount * $rate_usd;
                    $payment_cny = ceil($payment_amount_idr / $sell_cny);
                }elseif($request->kurs_id == "2"){
                    $payment_cny = $request->amount;
                }elseif($request->kurs_id == "3"){
                    $payment_amount_idr = $request->amount * $rate_twd;
                    $payment_cny = ceil($payment_amount_idr / $sell_cny);
                }else{
                    $payment_amount_idr = $request->amount;
                    $payment_cny = ceil($payment_amount_idr / $sell_cny);
                }
                $balance = $invoice_balance - $payment_cny;
                $new_balance = $invoice_balance + $payment_cny;
            }elseif($invoice->currency->name == "TWD") {
                if ($request->kurs_id == "1") {
                    $payment_amount_idr = $request->amount * $rate_usd;
                    $payment_twd = ceil($payment_amount_idr / $sell_twd);
                }elseif($request->kurs_id == "2"){
                    $payment_amount_idr = $request->amount * $rate_cny;
                    $payment_twd = ceil($payment_amount_idr / $sell_twd);
                }elseif($request->kurs_id == "3"){
                    $payment_twd = $request->amount;
                }else{
                    $payment_amount_idr = $request->amount;
                    $payment_twd = ceil($payment_amount_idr / $sell_twd);
                }
                $balance = $invoice_balance - $payment_twd;
                $new_balance = $invoice_balance + $payment_twd;
            }elseif($invoice->currency->name == "IDR") {
                if ($request->kurs_id == "1") {
                    $payment_amount_idr = $request->amount * $rate_usd;
                    $payment_idr = $payment_amount_idr;
                }elseif($request->kurs_id == "2"){
                    $payment_amount_idr = $request->amount * $rate_cny;
                    $payment_idr = $payment_amount_idr;
                }elseif($request->kurs_id == "3"){
                    $payment_amount_idr = $request->amount * $rate_twd;
                    $payment_idr = $payment_amount_idr;
                }else{
                    $payment_idr = $request->amount;
                }
                $balance = $invoice_balance - $payment_idr;
                $new_balance = $invoice_balance + $payment_idr;
            }
        
        if ($receipt->status == "Pending" && $request->status == "Valid") {
            $invoice->update([
                'balance'=>$balance,
            ]);
            if ($balance < 1) {
                $order->update([
                    'status'=>"Paid",
                ]);
            }
        }elseif ($receipt->status == "Valid" && $request->status == "Invalid") {
            $invoice->update([
                'balance'=>$new_balance,
            ]);
            if ($new_balance < 1) {
                $order->update([
                    'status'=>"Paid",
                ]);
            }
        }elseif ($receipt->status == "Invalid" && $request->status == "Valid") {
            $invoice->update([
                'balance'=>$balance,
            ]);
            if ($balance < 1) {
                $order->update([
                    'status'=>"Paid",
                ]);
            }
        }
       
        $kurs = UsdRates::find($request->kurs_id);
        // dd($receipt->status,$request->kurs_id, $status, $balance, $new_balance, $invoice->currency->name, $receipt, $invoice);
        $receipt->update([
            "status"=>$status,
            "amount"=>$request->amount,
            "note"=>$request->note,
            "kurs_id"=>$kurs->id,
            "payment_date"=>$payment_date,
        ]);
        $order_log =new OrderLog([
            "order_wedding_id"=>$order->id,
            "action"=>"Validate Payment Receipt",
            "url"=>$request->getClientIp(),
            "method"=>"Validate",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/orders-admin-$order->id")->with('success','Payment receipt has been successfully validate!');
    }
     // Function Confirmation Payment ORDER WEDDING =============================================================================================================>
    public function forder_wedding_confirmation_payment(Request $request,$id){
        $usdrate = UsdRates::where('name','USD')->first();
        $twdrate = UsdRates::where('name','TWD')->first();
        $cnyrate = UsdRates::where('name','CNY')->first();
        $order = OrderWedding::where("id",$request->order_id)->first();
        $agent = Auth::user()->where('id',$order->agent_id)->first();
        $receipt = PaymentConfirmation::find($id);
        $invoice = InvoiceAdmin::where("id",$receipt->inv_id)->first();
        $payment_date = date('Y-m-d',strtotime($request->payment_date));
        $status = $request->status;
        if ($request->kurs == "USD") {
            $kurs_rate = $usdrate->rate;
        }elseif($request->kurs == "TWD") {
            $kurs_rate = $twdrate->rate;
        }elseif($request->kurs == "CNY") {
            $kurs_rate = $cnyrate->rate;
        }else{
            $kurs_rate = 1;
        }
        $transaction_code = "ORD".$order->orderno."/INV".$receipt->inv_id."/PAY".$receipt->id;
       
        // PAYMENT CALCULATE
            $rate_usd = $invoice->rate_usd;
            $sell_usd = $invoice->sell_usd;
            $rate_twd = $invoice->rate_twd;
            $sell_twd = $invoice->sell_twd;
            $rate_cny = $invoice->rate_cny;
            $sell_cny = $invoice->sell_cny;
            $invoice_balance = $invoice->balance;
            if ($invoice->currency->name == "USD") {
                if ($request->kurs == "USD") {
                    $payment_usd = $request->amount;
                }elseif($request->kurs == "CNY"){
                    $payment_amount_idr = $request->amount * $rate_cny;
                    $payment_usd = ceil($payment_amount_idr / $sell_usd);
                }elseif($request->kurs == "TWD"){
                    $payment_amount_idr = $request->amount * $rate_twd;
                    $payment_usd = ceil($payment_amount_idr / $sell_usd);
                }else{
                    $payment_amount_idr = $request->amount;
                    $payment_usd = ceil($payment_amount_idr / $sell_usd);
                }
                $balance = $invoice_balance - $payment_usd;
                $new_balance = $invoice_balance + $payment_usd;
            }elseif($invoice->currency->name == "CNY") {
                if ($request->kurs == "USD") {
                    $payment_amount_idr = $request->amount * $rate_usd;
                    $payment_cny = ceil($payment_amount_idr / $sell_cny);
                }elseif($request->kurs == "CNY"){
                    $payment_cny = $request->amount;
                }elseif($request->kurs == "TWD"){
                    $payment_amount_idr = $request->amount * $rate_twd;
                    $payment_cny = ceil($payment_amount_idr / $sell_cny);
                }else{
                    $payment_amount_idr = $request->amount;
                    $payment_cny = ceil($payment_amount_idr / $sell_cny);
                }
                $balance = $invoice_balance - $payment_cny;
                $new_balance = $invoice_balance + $payment_cny;
            }elseif($invoice->currency->name == "TWD") {
                if ($request->kurs == "USD") {
                    $payment_amount_idr = $request->amount * $rate_usd;
                    $payment_twd = ceil($payment_amount_idr / $sell_twd);
                }elseif($request->kurs == "CNY"){
                    $payment_amount_idr = $request->amount * $rate_cny;
                    $payment_twd = ceil($payment_amount_idr / $sell_twd);
                }elseif($request->kurs == "TWD"){
                    $payment_twd = $request->amount;
                }else{
                    $payment_amount_idr = $request->amount;
                    $payment_twd = ceil($payment_amount_idr / $sell_twd);
                }
                $balance = $invoice_balance - $payment_twd;
                $new_balance = $invoice_balance + $payment_twd;
            }elseif($invoice->currency->name == "IDR") {
                if ($request->kurs == "USD") {
                    $payment_amount_idr = $request->amount * $rate_usd;
                    $payment_idr = $payment_amount_idr;
                }elseif($request->kurs == "CNY"){
                    $payment_amount_idr = $request->amount * $rate_cny;
                    $payment_idr = $payment_amount_idr;
                }elseif($request->kurs == "TWD"){
                    $payment_amount_idr = $request->amount * $rate_twd;
                    $payment_idr = $payment_amount_idr;
                }else{
                    $payment_idr = $request->amount;
                }
                $balance = $invoice_balance - $payment_idr;
                $new_balance = $invoice_balance + $payment_idr;
            }
        
        if ($receipt->status == "Pending" && $request->status == "Valid") {
            $invoice->update([
                'balance'=>$balance,
            ]);
        }elseif ($receipt->status == "Valid" && $request->status == "Invalid") {
            $invoice->update([
                'balance'=>$new_balance,
            ]);
        }elseif ($receipt->status == "Invalid" && $request->status == "Valid") {
            $invoice->update([
                'balance'=>$balance,
            ]);
        }
        if ($balance <= 1) {
            $order->update([
                'status'=>"Paid",
            ]);
        }else{
            $order->update([
                'status'=>"Approved",
            ]);
        }
        // dd($receipt->status, $status, $new_outstanding_balance_idr, $outstanding_balance_idr, $balance, $new_balance, $invoice->currency->name, $receipt, $invoice);
        $receipt->update([
            "status"=>$status,
            "amount"=>$request->amount,
            "note"=>$request->note,
            "kurs_id"=>$request->kurs,
            // "kurs_rate"=>$kurs_rate,
            "payment_date"=>$payment_date,
        ]);
        $order_log =new OrderLog([
            "order_wedding_id"=>$order->id,
            "action"=>"Validate Payment Receipt",
            "url"=>$request->getClientIp(),
            "method"=>"Validate",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/validate-orders-wedding-$order->id")->with('success','Payment receipt has been successfully validate!');
    }


     // Function Confirmation Payment =============================================================================================================>
    public function func_final_wedding_order(Request $request,$id){
        $order = Orders::where("id",$id)->first();
        $order->update([
            "status"=>"Paid",
        ]);
        
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Final Order",
            "url"=>$request->getClientIp(),
            "method"=>"Final",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/orders-admin-$order->id")->with('success','Order has been final!');
    }

    
    // VIEW VALIDATE ORDER WEDDING (ADMIN ORDER WEDDING) ok
    public function view_validate_order_wedding(Request $request,$id){
        $orderWedding = OrderWedding::where('id',$id)->first();
        $rates = UsdRates::all();
        $admin = Auth::user();
        $banks = BankAccount::all();
        if ($orderWedding) {
            $wedding = Weddings::where('id',$orderWedding->service_id)->first();
            if ($orderWedding->handled_by) {
                $admin_reservation = Auth::user()->where('id',$orderWedding->handled_by)->first();
            }else{
                $admin_reservation = Auth::user();
            }
            $reservation_staf = Auth::user();
            $agent = Auth::user()->where('id',$orderWedding->agent_id)->first();
            $now = Carbon::now();
            $attentions = Attention::where('page','weddings-admin')->get();
            $usdrates = UsdRates::where('name','USD')->first();
            $business = BusinessProfile::where('id',1)->first();
            $hotel = Hotels::where('id',$orderWedding->hotel_id)->first();
            $bride = Brides::where('id',$orderWedding->brides_id)->first();
            $ceremonyVenue = WeddingVenues::where('id',$orderWedding->ceremony_venue_id)->first();
            if ($ceremonyVenue) {
                $slots = json_decode($ceremonyVenue->slot);
            }else{
                $slots = "[09:00]";
            }
            $weddingDinners = WeddingDinnerVenues::where('hotel_id',$hotel->id)->get();
            $receptionVenues = WeddingReceptionVenues::where('hotel_id',$hotel->id)->get();
            $ceremonyVenues = WeddingVenues::where('hotels_id',$hotel->id)->get();
            $maxCapacity = WeddingVenues::where('hotels_id',$hotel->id)->orderBy('capacity','desc')->first();
            $vendor_packages = VendorPackage::where('status','Active')->get();
            $ceremonyVenueDecoration = VendorPackage::where('id',$orderWedding->ceremony_venue_decoration_id)->first();
            $decorationReceptionVenue = VendorPackage::where('id',$orderWedding->reception_venue_decoration_id)->first();
            $receptionVenuePackages = WeddingReceptionVenues::where('hotel_id',$hotel->id)->get();
            $receptionVenue = WeddingReceptionVenues::where('id',$orderWedding->reception_venue_id)->first();
            $weddingAccommodations = WeddingAccomodations::where('order_wedding_package_id',$orderWedding->id)->get();
            $accommodation_price = $weddingAccommodations->pluck('public_rate');
            $accommodation_containt_zero = $weddingAccommodations->contains('public_rate',0);
            $invitation_accommodation_price = $weddingAccommodations->pluck('public_rate')->sum();
    
            $ac_p = json_decode($accommodation_price);
            $hasZeroOrNullPublicRate = in_array(0,$ac_p);
            if($hasZeroOrNullPublicRate == 1){
                $is_valid = 0;
            }else{
                $is_valid = 1;
            }
            $transports = Transports::all();
            $transportOrdersId = json_decode($orderWedding->transport_id);
            $transportOrdersType = json_decode($orderWedding->transport_type);
            $transportInvitations = WeddingPlannerTransport::where('order_wedding_id',$orderWedding->id)->get();
            if ($receptionVenue) {
                $weddingDinner = WeddingDinnerVenues::where('id',$receptionVenue->dinner_venues_id)->first();
            }else{
                $weddingDinner = null;
            }
            $transport_price = $transportInvitations->pluck('price')->sum();
            $transportPriceContainZero = $transportInvitations->contains('price',0);
            $transport_bride_id = $orderWedding->transport_id;
            $transport_bride_type = "Airport Shuttle";
            
            
            $orderlogs = OrderLog::where('order_wedding_id',$id)->get();
            $order_notes = OrderNote::where('order_wedding_id',$id)->get();
            
            
            if (!$orderWedding->rsv_id) {
                $reservation = new Reservation ([
                    'rsv_no' =>$orderWedding->orderno,
                    'agn_id'=>$agent->id,
                    'adm_id'=>Auth::user()->id,
                    'status'=>"Draft",
                    'checkin'=>$orderWedding->checkin,
                    'checkout'=>$orderWedding->checkout,
                    'orders'=>$orderWedding->id,
                ]);
                $reservation->save();
                $orderWedding->update([
                    "rsv_id"=>$reservation->id,
                ]);
                $receipts = null;
                $invoice = NULL;
                $kurs = NULL;
    
            }else{
                $reservation = Reservation::where('id',$orderWedding->rsv_id)->first();
                if ($reservation) {
                    $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
                    if ($invoice) {
                        $receipts = PaymentConfirmation::where('inv_id',$invoice->id)->get();
                        $kurs = UsdRates::where('id',$invoice->currency_id)->first();
                    }else {
                        $receipts = null;
                        $kurs = NULL;
                    }
                }else{
                    $receipts = null;
                    $invoice = NULL;
                    $kurs = NULL;
                }
            }
            $flights = Flights::where('order_wedding_id',$orderWedding->id)->get();
            $guests = WeddingInvitations::where('order_wedding_id',$orderWedding->id)->get();
            $admins = Auth::user()->where('status',"Active")->get();
            $bride_accommodation = HotelRoom::where('id',$orderWedding->room_bride_id)->first();
            $wedding_accommodations = WeddingAccomodations::where('order_wedding_package_id',$orderWedding->id)
            ->where('room_for','Inv')->get();
            $accommodationPriceContainZero = $wedding_accommodations->contains('public_rate',0);
            $total_invitations = $wedding_accommodations->pluck('number_of_guests')->sum();
            $rooms = HotelRoom::where('hotels_id',$hotel->id)->get();
            $extra_beds = ExtraBed::where('hotels_id',$hotel->id)->get();
            $extraBedOrders = ExtraBedOrder::where('order_wedding_id',$orderWedding->id)->get();
            $extraBedOrderPrice = $extraBedOrders->pluck('total_price')->sum();
            $countries = Countries::all();
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
            $serviceRequestsStatusRequest = WeddingAdditionalServices::where('order_wedding_id',$orderWedding->id)->where('status',"Request")->get();
            $serviceRequests = WeddingAdditionalServices::where('order_wedding_id',$orderWedding->id)->where('status','!=',"Rejected")->get();
            $serviceRequestRejected = WeddingAdditionalServices::where('order_wedding_id',$orderWedding->id)->where('status',"Rejected")->get();
            $serviceRequestPrice = $serviceRequests->pluck('price')->sum();
            $serviceRequestPriceContainZero = $serviceRequests->contains('price',0);
            return view('admin.order.order-wedding-detail',[
                "agent"=>$agent,
                "banks"=>$banks,
                "rates"=>$rates,
                "dinnerVenue"=>$dinnerVenue,
                "dinnerVenues"=>$dinnerVenues,
                "lunchVenues"=>$lunchVenues,
                "lunchVenue"=>$lunchVenue,
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
                "receptionVenuePackages"=>$receptionVenuePackages,
                "weddingDinners"=>$weddingDinners,
                "weddingDinner"=>$weddingDinner,
                "admin_reservation"=>$admin_reservation,
                "reservation_staf"=>$reservation_staf,
                "receptionVenues"=>$receptionVenues,
                "weddingAccommodations"=>$weddingAccommodations,
                "accommodationPriceContainZero"=>$accommodationPriceContainZero,
                "transportOrdersId"=>$transportOrdersId,
                "transports"=>$transports,
                "transportOrdersType"=>$transportOrdersType,
                "transportInvitations"=>$transportInvitations,
                "transportPriceContainZero"=>$transportPriceContainZero,
                "transport_price"=>$transport_price,
                "transport_bride_id"=>$transport_bride_id,
                "transport_bride_type"=>$transport_bride_type,
                "wedding"=>$wedding,
                "is_valid"=>$is_valid,
                "flights"=>$flights,
                "guests"=>$guests,
                "invoice"=>$invoice,
                "kurs"=>$kurs,
                "reservation"=>$reservation,
                "orderlogs"=>$orderlogs,
                "order_notes"=>$order_notes,
                "receipts"=>$receipts,
                "admins"=>$admins,
                "admin"=>$admin,
                "bride_accommodation"=>$bride_accommodation,
                "wedding_accommodations"=>$wedding_accommodations,
                "total_invitations"=>$total_invitations,
                "rooms"=>$rooms,
                "extra_beds"=>$extra_beds,
                "extraBedOrderPrice"=>$extraBedOrderPrice,
                "accommodation_containt_zero"=>$accommodation_containt_zero,
                "accommodation_price"=>$accommodation_price,
                "invitation_accommodation_price"=>$invitation_accommodation_price,
                "extraBedOrders"=>$extraBedOrders,
                "countries"=>$countries,
                "serviceRequests"=>$serviceRequests,
                "serviceRequestPrice"=>$serviceRequestPrice,
                "serviceRequestPriceContainZero"=>$serviceRequestPriceContainZero,
                "serviceRequestRejected"=>$serviceRequestRejected,
                "serviceRequestsStatusRequest"=>$serviceRequestsStatusRequest,
            ]);
        }else{
            return redirect("/orders-admin")->with('error_messages','Order not found!');
        }
    }
// ===============================================================================================================================================================
// ACCOMMODATION 
// ===============================================================================================================================================================
    // VIEW VALIDATE ORDER WEDDING ACCOMMODATION (ADMIN ORDER WEDDING) ok
    public function view_validate_order_wedding_accommodation(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        $now = Carbon::now();
        $user_id = Auth::User()->id;
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $attentions = Attention::where('page','validate-order-wedding-accommodation')->get();
        $business = BusinessProfile::where('id',1)->first();
        $accommodationInvs = WeddingAccomodations::where('order_wedding_package_id',$id)->where('room_for',"Inv")->get();
        $brides = Brides::where('id',$orderWedding->brides_id)->first();
        $extraBeds = ExtraBed::where('hotels_id',$orderWedding->hotel_id)->get();
        $extraBedOrders = ExtraBedOrder::all();
        $accommodation_order_containt_zero = $accommodationInvs->contains('public_rate',0);
        $invitation_accommodation_price = $accommodationInvs->pluck('public_rate')->sum();
        $accommodation_bride = HotelRoom::where('id',$orderWedding->room_bride_id)->first();
        $hotel = Hotels::where('id',$orderWedding->hotel_id)->first();
        $rooms = $hotel->rooms;
        return view('admin.order.order-wedding-accommodation',[
            'now'=>$now,
            'user_id'=>$user_id,
            'usdrates'=>$usdrates,
            'tax'=>$tax,
            'attentions'=>$attentions,
            'business'=>$business,
            'orderWedding'=>$orderWedding,
            'accommodationInvs'=>$accommodationInvs,
            'extraBeds'=>$extraBeds,
            'extraBedOrders'=>$extraBedOrders,
            'accommodation_order_containt_zero'=>$accommodation_order_containt_zero,
            'invitation_accommodation_price'=>$invitation_accommodation_price,
            'accommodation_bride'=>$accommodation_bride,
            'brides'=> $brides,
            'hotel'=> $hotel,
            'rooms'=> $rooms,
        ]);
    }
    // FUNCTION ADD OR UPDATE DECORATION CEREMONY VENUE (ADMIN ORDER WEDDING) ok
    public function func_admin_add_order_wedding_accommodation(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        if ($request->rooms_id) {
            $now = Carbon::now();
            $tax = Tax::where('id',1)->first();
            $usdrates = UsdRates::where('name','USD')->first();
            $hotel = Hotels::where('id',$orderWedding->hotel_id)->first();
            $extra_bed_default = ExtraBed::where('hotels_id',$hotel->id)->first();
            if ($orderWedding->service == "Wedding Package") {
                $checkin = $orderWedding->checkin;
                $checkout = $orderWedding->checkout;
                $duration = $orderWedding->duration;
            }else{
                $checkin = $request->checkin;
                $checkout = $request->checkout;
                $in=Carbon::parse($checkin);
                $out=Carbon::parse($checkout);
                $duration = $in->diffInDays($out);
            }
            if ($request->number_of_guests > 2) {
                if ($request->extra_bed_id) {
                    $extra_bed = ExtraBed::where('id',$request->extra_bed_id)->first();
                    $extra_bed_id = $extra_bed->id;

                    $exb_cr = ($extra_bed->contract_rate / $usdrates->rate)+$extra_bed->markup;
                    $extra_price = ceil((($exb_cr*$tax->tax)/100)+$exb_cr)*$duration;
                    $extra_price_pax = $extra_price/$duration;
                }else{
                    $extra_bed_id = $extra_bed_default->id;
                    $exb_cr = ($extra_bed_default->contract_rate / $usdrates->rate)+$extra_bed_default->markup;
                    $extra_price = ceil((($exb_cr*$tax->tax)/100)+$exb_cr)*$duration;
                    $extra_price_pax = $extra_price/$duration;
                }
                $extra_bed_order = new ExtraBedOrder([
                    "order_wedding_id"=>$id,
                    "rooms_id"=>$request->rooms_id,
                    "extra_bed_id"=>$extra_bed_id,
                    "duration"=>$duration,
                    "price_pax"=>$extra_price_pax,
                    "total_price"=>$extra_price,
                ]);
                $extra_bed_order->save();
                $extra_bed_order_id = $extra_bed_order->id;
            }else{
                $extra_bed_id = NULL;
                $extra_bed_order_id = NULL;
            }

            // room id error saat room tidak dipilih !!!!
            $room = HotelRoom::where('id',$request->rooms_id)->first();
            $room_rate = HotelPrice::where('rooms_id',$room->id)->where('start_date','<=',$now)->where('end_date','>=',$now)->first();
            if ($room_rate) {
                $room_public_rate = (($room_rate->contract_rate / $usdrates->rate) + $room_rate->markup);
                $room_tax = ($room_public_rate * $tax->tax)/100;
                $public_rate = ceil($room_public_rate + $room_tax) * $duration;
            }else{
                $public_rate = 0;
            }
            $order_wedding_accommodation = new WeddingAccomodations([
                "order_wedding_package_id"=>$id,
                "room_for"=>"Inv",
                "hotels_id"=>$hotel->id,
                "rooms_id"=>$request->rooms_id,
                "checkin"=>$checkin,
                "checkout"=>$checkout,
                "duration"=>$duration,
                "number_of_guests"=>$request->number_of_guests,
                "guest_detail"=>$request->guest_detail,
                "extra_bed_id"=>$extra_bed_order_id,
                "remark"=>$request->remark,
                "note"=>$request->note,
                "public_rate"=>$public_rate,
                "status"=>"Active",
            ]);
            $order_wedding_accommodation->save();
            
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
            // dd($orderWedding );
            return redirect("/admin-validate-order-wedding-accommodation-$orderWedding->id")->with('success','Accommodation has been added!');
        }else{
            return redirect("/admin-validate-order-wedding-accommodation-$orderWedding->id")->with('errors_message','Please select room!');
        }
    }
    // FUNCTION UPDATE ACCOMMODATION INVITATION (ADMIN ORDER WEDDING) ok
    public function func_update_wedding_order_accommodation(Request $request,$id){
        $now = Carbon::now();
        $tax = Tax::where('id',1)->first();
        $usdrates = UsdRates::where('name','USD')->first();
        $wedding_accommodation = WeddingAccomodations::find($id);
        $orderWedding = OrderWedding::where('id',$wedding_accommodation->order_wedding_package_id)->first();
        $weddingAccommodations = WeddingAccomodations::where('order_wedding_package_id',$orderWedding->id)->where('room_for',"Inv")->get();
        

        if ($orderWedding->service == "Wedding Package") {
            $checkin = date('Y-m-d',strtotime($orderWedding->checkin));
            $checkout = date('Y-m-d',strtotime($orderWedding->checkout));
        }else{
            $checkin = date('Y-m-d',strtotime($request->checkin));
            $checkout = date('Y-m-d',strtotime($request->checkout));
        }
        $in = Carbon::parse(date('Y-m-d', strtotime($checkin)));
        $out = Carbon::parse(date('Y-m-d', strtotime($checkout)));
        $duration = $in->diffInDays($out);


        // EXTRA BED
        $extra_bed = ExtraBed::where('id',$request->extra_bed_id)->first();
        $extra_bed_default = ExtraBed::where('hotels_id',$wedding_accommodation->hotels_id)->first();
        
        if ($request->number_of_guests > 2) {
            if ($request->extra_bed_id) {
                $extra_bed = ExtraBed::where('id',$request->extra_bed_id)->first();
                $extra_bed_id = $extra_bed->id;

                $exb_cr = ($extra_bed->contract_rate / $usdrates->rate)+$extra_bed->markup;
                $extra_price = ceil((($exb_cr*$tax->tax)/100)+$exb_cr)*$duration;
                $extra_price_pax = $extra_price/$duration;
            }else{
                $extra_bed_id = $extra_bed_default->id;
                $exb_cr = ($extra_bed_default->contract_rate / $usdrates->rate)+$extra_bed_default->markup;
                $extra_price = ceil((($exb_cr*$tax->tax)/100)+$exb_cr)*$duration;
                $extra_price_pax = $extra_price/$duration;
            }
            $extra_bed_order = new ExtraBedOrder([
                "order_wedding_id"=>$orderWedding->id,
                "rooms_id"=>$request->rooms_id,
                "extra_bed_id"=>$extra_bed_id,
                "duration"=>$duration,
                "price_pax"=>$extra_price_pax,
                "total_price"=>$extra_price,
            ]);
            $extra_bed_order->save();
            $extra_bed_order_id = $extra_bed_order->id;
        }else{
            $extra_bed_order = ExtraBedOrder::where('id',$wedding_accommodation->extra_bed_id)->first();
            if ($extra_bed_order) {
                $extra_bed_order->delete();
            }
            $extra_bed_id = NULL;
            $extra_bed_order_id = NULL;
        }

        $room = HotelRoom::where('id',$request->rooms_id)->first();
        $room_rate = HotelPrice::where('rooms_id',$room->id)->where('start_date','<=',$now)->where('end_date','>=',$now)->first();
        if ($room_rate) {
            $room_public_rate = (($room_rate->contract_rate / $usdrates->rate) + $room_rate->markup);
            $room_tax = ($room_public_rate * $tax->tax)/100;
            $public_rate = ceil($room_public_rate + $room_tax) * $duration;
        }else{
            $public_rate = 0;
        }
        $wedding_accommodation->update([
            "number_of_guests"=>$request->number_of_guests,
            "extra_bed_id"=>$extra_bed_order_id,
            "guest_detail"=>$request->guest_detail,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "duration"=>$duration,
            "remark"=>$request->remark,
            "note"=>$request->note,
            "public_rate"=>$public_rate,
            "status"=>"Active",
        ]);
        
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
        // dd($wedding_accommodation, $order_wedding);
        return redirect("/admin-validate-order-wedding-accommodation-$orderWedding->id")->with('success','Accommodation has been updated!');
    }
    // FUNCTION DELETE ACCOMMODATION INVITATION (ADMIN ORDER WEDDING) ok
    public function func_delete_order_wedding_accommodation_invitation(Request $request, $id)
    {
        $accommodation = WeddingAccomodations::findOrFail($id);
        $orderWedding = OrderWedding::where('id',$accommodation->order_wedding_package_id)->first();
        $wedding_order_id = $orderWedding->id;
        $extra_bed = ExtraBedOrder::where('id',$accommodation->extra_bed_id)->first();
        $accommodation->delete();
        if ($extra_bed) {
            $extra_bed->delete();
        }

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

        // @dd($order);
        return redirect()->to(app('url')->previous())->with('success','Accommodation has been removed from the order');
    }
    // FUNCTION UPDATE PRICE ACCOMMODATION INVITATION (ADMIN ORDER WEDDING) ok
    public function admin_func_update_price_accommodation(Request $request,$id){
        $accommodationInv = WeddingAccomodations::find($id);
        $orderWedding = OrderWedding::where('id',$accommodationInv->order_wedding_package_id)->first();
        $duration = $accommodationInv->duration;
        $extra_bed = ExtraBedOrder::where('id',$accommodationInv->extra_bed_id)->first();
        $extra_bed_price_pax = $request->extra_bed_price;
        $extra_bed_price = $extra_bed_price_pax * $duration;
        $suite_and_villa_price = $request->suite_and_villa_price * $accommodationInv->duration;
        $accommodationInv->update([
            'public_rate'=>$suite_and_villa_price,
            'status'=>$request->status,
        ]);
        if ($extra_bed) {
            $extra_bed->update([
                'total_price'=>$extra_bed_price,
                'price_pax'=>$extra_bed_price_pax,
            ]);
        }

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

        return redirect("/admin-validate-order-wedding-accommodation-$orderWedding->id")->with('success','Accommodation price has been updated!');
    }
    // FUNCTION UPDATE ACCOMMODATION INVITATION (ADMIN ORDER WEDDING) ok
    public function admin_func_update_accommodation_invitation(Request $request,$id){
        $wedding_accommodation = WeddingAccomodations::find($id);
        $extraBedOrder = ExtraBedOrder::where('id',$wedding_accommodation->extra_bed_id)->first();
        $orderWedding = OrderWedding::where('id',$wedding_accommodation->order_wedding_package_id)->first();
        
        $duration = $wedding_accommodation->duration;
        $public_rate = $request->public_rate * $duration;
        
        // ACCOMMODATION
        $wedding_accommodation->update([
            "public_rate"=>$public_rate,
        ]);
        // EXTRA BED
        if ($extraBedOrder) {
            $extraBedOrder->update([
                "total_price"=>$request->extra_bed_price * $duration,
                "price_pax"=>$request->extra_bed_price,
            ]);
        }
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
        return redirect("/validate-orders-wedding-$orderWedding->id#weddingAccommodation")->with('success','Accommodation price has been updated!');
    }

    // FUNCTION UPDATE ACCOMMODATION BRIDES (ADMIN ORDER WEDDING) =====================================================================================================>
    public function admin_func_update_accommodation_brides(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        $order_wedding->update([
            "room_bride_price"=>$request->room_bride_price,
        ]);
        return redirect("/validate-orders-wedding-$order_wedding->id#weddingPackageServices")->with('success','Accommodation price has been updated!');
    }


// ===============================================================================================================================================================
// ADDITIONAL CHARGE 
// ===============================================================================================================================================================

    // FUNCTION ADD REQUEST SERVICE =====================================================================================================> ok
    public function func_admin_add_request_service(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        $date = date('Y-m-d',strtotime($request->date)).date(' H:i',strtotime($request->time));
        $status = "Approved";
        $wedding_additional_service = new WeddingAdditionalServices([
            'date'=>$date,
            'order_wedding_id'=>$id,
            'type'=>$request->type,
            'service'=>$request->service,
            'quantity'=>$request->quantity,
            'note'=>$request->note,
            'price'=>$request->price,
            'status'=>$status,
        ]);
        $wedding_additional_service->save();

        // FINAL PRICE 
        $requestServices = WeddingAdditionalServices::where('order_wedding_id',$orderWedding->id)->where('status','!=','Rejected')->get();
        $addser_price = $requestServices->pluck('price')->sum();

        if ($orderWedding->service == "Wedding Package") {
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $orderWedding->accommodation_price
                + $orderWedding->extra_bed_price
                + $orderWedding->transport_invitations_price
                + $addser_price // proses
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
                + $orderWedding->transport_invitations_price
                + $makeup_price
                + $documentation_price
                + $entertainment_price
                + $orderWedding->additional_services_price
                + $addser_price // proses
                + $orderWedding->markup;
        }
        $orderWedding->update([
            "addser_price"=>$addser_price,
            "final_price"=>$final_price,
        ]);
        $reservation = $orderWedding->reservation;
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        if ($invoice) {
            $payment_amount_usd = PaymentConfirmation::where('inv_id',$invoice->id)->where('status','Valid')->where('kurs_name','USD')->sum('amount');
            $payment_amount_idr = PaymentConfirmation::where('inv_id',$invoice->id)->where('status','Valid')->where('kurs_name','IDR')->sum('amount');
            $payment_amount_cny = PaymentConfirmation::where('inv_id',$invoice->id)->where('status','Valid')->where('kurs_name','CNY')->sum('amount');
            $payment_amount_twd = PaymentConfirmation::where('inv_id',$invoice->id)->where('status','Valid')->where('kurs_name','TWD')->sum('amount');
            $total_usd_to_idr = $payment_amount_usd * $invoice->rate_usd;
            $total_idr_to_idr = $payment_amount_idr;
            $total_cny_to_idr = $payment_amount_cny * $invoice->rate_cny;
            $total_twd_to_idr = $payment_amount_twd * $invoice->rate_twd;
            $total_payment_idr = $total_usd_to_idr + $total_idr_to_idr + $total_cny_to_idr + $total_twd_to_idr;
            $total_payment_usd = $total_payment_idr / $invoice->rate_usd;
            $total_payment_cny = $total_payment_idr / $invoice->rate_cny;
            $total_payment_twd = $total_payment_idr / $invoice->rate_twd;
            $total_usd = $final_price;
            $total_idr = $final_price * $invoice->rate_usd;
            $total_cny = ceil($total_idr / $invoice->rate_cny);
            $total_twd = ceil($total_idr / $invoice->rate_twd);
            if ($invoice->currency->name == "USD") {
                $balance = $total_usd - $total_payment_usd;
            }elseif($invoice->currency->name == "IDR"){
                $balance = $total_idr - $total_payment_idr;
            }elseif($invoice->currency->name == "CNY"){
                $balance = $total_cny - $total_payment_cny;
            }elseif($invoice->currency->name == "TWD"){
                $balance = $total_twd - $total_payment_twd;
            }
            $invoice->update([
                'total_usd'=>$total_usd,
                'total_idr'=>$total_idr,
                'total_cny'=>$total_cny,
                'total_twd'=>$total_twd,
                'balance'=>$balance,
            ]);
        }
        $order_log =new OrderLog([
            "order_wedding_id"=>$orderWedding->id,
            "action"=>"Add Additional Charge",
            "url"=>$request->getClientIp(),
            "method"=>"ADD",
            "agent"=>$orderWedding->agent_id,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();

        return redirect("/validate-orders-wedding-$id#weddingAdditionalCharge")->with('success',"Additional service has been added to the order");
    }
    // FUNCTION UPDATE REQUEST SERVICE ok
    public function func_admin_update_request_service(Request $request,$id){
        $wedding_additional_service = WeddingAdditionalServices::find($id);
        $orderWedding = OrderWedding::where('id',$wedding_additional_service->order_wedding_id)->first();
        $date = date('Y-m-d',strtotime($request->date)).date(' H:i',strtotime($request->time));
        $status = $request->status;
        if ($status == "Rejected") {
            $remark = $request->remark;
            $price = NULL;
            
        }else{
            $remark = NULL;
            $price = $request->price;
        }
        $wedding_additional_service ->update([
            'date'=>$date,
            'order_wedding_id'=>$orderWedding->id,
            'type'=>$request->type,
            'service'=>$request->service,
            'quantity'=>$request->quantity,
            'note'=>$request->note,
            'remark'=>$remark,
            'price'=>$price,
            'status'=>$status,
        ]);

        // FINAL PRICE 
        $requestServices = WeddingAdditionalServices::where('order_wedding_id',$orderWedding->id)->get();
        $addser_price = $requestServices->pluck('price')->sum();

        if ($orderWedding->service == "Wedding Package") {
            $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
            $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
            $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
            $final_price = $orderWedding->accommodation_price
                + $orderWedding->extra_bed_price
                + $orderWedding->transport_invitations_price
                + $addser_price // proses
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
                + $orderWedding->transport_invitations_price
                + $makeup_price
                + $documentation_price
                + $entertainment_price
                + $orderWedding->additional_services_price
                + $addser_price //proses
                + $orderWedding->markup;
        }
        $orderWedding->update([
            "addser_price"=>$addser_price,
            "final_price"=>$final_price,
        ]);
        $reservation = $orderWedding->reservation;
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        if ($invoice) {
            $payment_amount_usd = PaymentConfirmation::where('inv_id',$invoice->id)->where('status','Valid')->where('kurs_name','USD')->sum('amount');
            $payment_amount_idr = PaymentConfirmation::where('inv_id',$invoice->id)->where('status','Valid')->where('kurs_name','IDR')->sum('amount');
            $payment_amount_cny = PaymentConfirmation::where('inv_id',$invoice->id)->where('status','Valid')->where('kurs_name','CNY')->sum('amount');
            $payment_amount_twd = PaymentConfirmation::where('inv_id',$invoice->id)->where('status','Valid')->where('kurs_name','TWD')->sum('amount');
            $total_usd_to_idr = $payment_amount_usd * $invoice->rate_usd;
            $total_idr_to_idr = $payment_amount_idr;
            $total_cny_to_idr = $payment_amount_cny * $invoice->rate_cny;
            $total_twd_to_idr = $payment_amount_twd * $invoice->rate_twd;
            $total_payment_idr = $total_usd_to_idr + $total_idr_to_idr + $total_cny_to_idr + $total_twd_to_idr;
            $total_payment_usd = $total_payment_idr / $invoice->rate_usd;
            $total_payment_cny = $total_payment_idr / $invoice->rate_cny;
            $total_payment_twd = $total_payment_idr / $invoice->rate_twd;
            $total_usd = $final_price;
            $total_idr = $final_price * $invoice->rate_usd;
            $total_cny = ceil($total_idr / $invoice->rate_cny);
            $total_twd = ceil($total_idr / $invoice->rate_twd);
            if ($invoice->currency->name == "USD") {
                $balance = $total_usd - $total_payment_usd;
            }elseif($invoice->currency->name == "IDR"){
                $balance = $total_idr - $total_payment_idr;
            }elseif($invoice->currency->name == "CNY"){
                $balance = $total_cny - $total_payment_cny;
            }elseif($invoice->currency->name == "TWD"){
                $balance = $total_twd - $total_payment_twd;
            }
            $invoice->update([
                'total_usd'=>$total_usd,
                'total_idr'=>$total_idr,
                'total_cny'=>$total_cny,
                'total_twd'=>$total_twd,
                'balance'=>$balance,
            ]);
        }
        $order_log =new OrderLog([
            "order_wedding_id"=>$orderWedding->id,
            "action"=>"Edit Additional Charge",
            "url"=>$request->getClientIp(),
            "method"=>"EDIT",
            "agent"=>$orderWedding->agent_id,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/validate-orders-wedding-$orderWedding->id#weddingAdditionalCharge")->with('success',"Additional charge has been updated");
    }

// ===============================================================================================================================================================
// TRANSPORT - ORDER WEDDING
// ===============================================================================================================================================================
    // FUNCTION ADD TRANSPORT INVITATION (ADMIN ORDER WEDDING) OK
    public function admin_func_add_transport_invitation_wedding(Request $request,$id){
        $now = Carbon::now();
        $tax = Tax::where('id',1)->first();
        $usdrates = UsdRates::where('name','USD')->first();
        $orderWedding = OrderWedding::find($id);
        $transport = Transports::where('id',$request->transport_id)->first();
        $hotel = Hotels::where('id',$orderWedding->hotel_id)->first();
        $type = "Airport Shuttle";
        if ($request->type == "Airport Shuttle In") {
            $desc_type = "In";
        }else{
            $desc_type = "Out";
        }
        $date = date('Y-m-d',strtotime($request->date)).date(' H:i',strtotime($request->time));
        $passenger = $request->passenger;
        $number_of_guests = $transport->capacity;
        $duration = $hotel->airport_duration;
        $distance = $hotel->airport_distance;
        $transport_price = TransportPrice::where('transports_id',$transport->id)->where('type','Airport Shuttle')->where('duration',$duration)->first();
        if ($transport_price) {
            $tr_cr_mr = ($transport_price->contract_rate + $transport_price->markup)/$usdrates->rate;
            $tr_tax = ($tr_cr_mr*$tax->tax)/100;
            $price = ceil($tr_cr_mr + $tr_tax);
        }else{
            $price = 0;
        }
        $wedding_transport = new WeddingPlannerTransport([
            'order_wedding_id'=>$id,
            'transport_id'=>$request->transport_id,
            'type'=>$type,
            'desc_type'=>$desc_type,
            'date'=>$date,
            'passenger'=>$passenger,
            'number_of_guests'=>$number_of_guests,
            'duration'=>$duration,
            'distance'=>$distance,
            'remark'=>$request->remark,
            'price'=>$price,

        ]);
        $wedding_transport->save();
        // FINAL PRICE 
        $transportServices = WeddingPlannerTransport::where('order_wedding_id',$orderWedding->id)->get();
        $transport_invitations_price = $transportServices->pluck('price')->sum();

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
                + $transport_invitations_price // proses
                + $makeup_price
                + $documentation_price
                + $entertainment_price
                + $orderWedding->additional_services_price
                + $orderWedding->addser_price
                + $orderWedding->markup;
        }
        $orderWedding->update([
            "transport_invitations_price"=>$transport_invitations_price,
            "final_price"=>$final_price,
        ]);
        return redirect("/validate-orders-wedding-$orderWedding->id#weddingTransport")->with('success',"Transport has been added to the order");
    }
    // FUNCTION UPDATE TRANSPORT BRIDES (ADMIN ORDER WEDDING) ok
    public function admin_func_update_transport_invitation(Request $request,$id){
        $wedding_transport_order = WeddingPlannerTransport::find($id);
        $orderWedding = OrderWedding::where('id',$wedding_transport_order->order_wedding_id)->first();
        $wedding_transport_order->update([
            "price"=>$request->price,
        ]);
        // FINAL PRICE 
        $transportServices = WeddingPlannerTransport::where('order_wedding_id',$orderWedding->id)->get();
        $transport_invitations_price = $transportServices->pluck('price')->sum();

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
                + $transport_invitations_price // proses
                + $makeup_price
                + $documentation_price
                + $entertainment_price
                + $orderWedding->additional_services_price
                + $orderWedding->addser_price
                + $orderWedding->markup;
        }
        $orderWedding->update([
            "transport_invitations_price"=>$transport_invitations_price,
            "final_price"=>$final_price,
        ]);
        return redirect("/validate-orders-wedding-$orderWedding->id#weddingPackageServices")->with('success','Transport price has been updated!');
    }

    // FUNCTION VALIDATE BRIDE (ADMIN ORDER WEDDING) =====================================================================================================>
    public function func_validate_bride_order_wedding(Request $request,$id){
        $bride = Brides::find($id);
        $order_wedding = OrderWedding::where('id',$request->order_wedding_id)->first();
        $groom = $request->groom;
        $groom_chinese = $request->groom_chinese;
        $groom_pasport_id = $request->groom_pasport_id;
        $bride_name = $request->bride;
        $bride_chinese = $request->bride_chinese;
        $bride_pasport_id = $request->bride_pasport_id;
        $bride->update([
            "groom"=>$groom,
            'groom_chinese'=>$groom_chinese,
            'groom_pasport_id'=>$groom_pasport_id,
            'bride'=>$bride_name,
            'bride_chinese'=>$bride_chinese,
            'bride_pasport_id'=>$bride_pasport_id,
        ]);
        $order_wedding->update([
            "handled_by"=>Auth::user()->id,
        ]);
        return redirect("/validate-orders-wedding-$order_wedding->id")->with('success','Bride has been updated !');
    }
    // FUNCTION VALIDATE WEDDING & RECEPTION (ADMIN ORDER WEDDING) =====================================================================================================>
    public function func_validate_wedding_and_reservation(Request $request,$id){
        $now = Carbon::now();
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $orderWedding = OrderWedding::find($id);
        $duration = $orderWedding->duration;
        $number_of_invitation = $request->number_of_invitation;
        if($orderWedding->service == "Ceremony Venue"){
            $wedding_date = date('Y-m-d',strtotime($request->wedding_date));
            $reception_date_start = $orderWedding->reception_date_start;
            $lunch_venue_date = $orderWedding->lunch_venue_date;
            $dinner_venue_date = $orderWedding->dinner_venue_date;
            $checkin = NULL;
            $checkout = NULL;
        }elseif ($orderWedding->service == "Reception Venue") {
            $wedding_date = $orderWedding->wedding_date;
            $reception_date_start = date('Y-m-d',strtotime($request->reception_date_start));
            $lunch_venue_date = $orderWedding->lunch_venue_date;
            $dinner_venue_date = $orderWedding->dinner_venue_date;
            $checkin = NULL;
            $checkout = NULL;
        }elseif ($orderWedding->service == "Wedding Package") {
            $wedding_date = date('Y-m-d',strtotime($request->wedding_date));
            $reception_date_start = $wedding_date." 18:00";
            $lunch_venue_date = $wedding_date." 13:00";
            $dinner_venue_date = $wedding_date." 18:00";
            $checkin = date('Y-m-d',strtotime('-1 days',strtotime($wedding_date)));
            $checkout = date('Y-m-d',strtotime('+'.$duration.' days',strtotime($wedding_date)));
        }
        // Accommodation
        $accommodations = WeddingAccomodations::where('order_wedding_package_id',$orderWedding->id)->get();
        if ($accommodations) {
            foreach ($accommodations as $accommodation) {
                $room_price = HotelPrice::where('rooms_id',$accommodation->rooms_id)->where('start_date','<=',$checkin)->where('end_date','>=',$checkin)->first();
                if ($room_price) {
                    $room_contract_rate_usd = ($room_price->contract_rate / $usdrates->rate)+$room_price->markup;
                    $room_contract_tax = ($room_contract_rate_usd*$tax->tax)/100;
                    $room_public_rate = ceil($room_contract_rate_usd + $room_contract_tax) * $duration;
                }else{
                    $room_public_rate = 0;
                }
                $accommodation->update([
                    "public_rate"=>$room_public_rate,
                    "checkin"=>$checkin,
                    "checkout"=>$checkout,
                ]);
            }
        }
        // FINAL PRICE
        $weddingAccommodations = WeddingAccomodations::where('order_wedding_package_id',$orderWedding->id)->get();
        $extra_bed_orders = ExtraBedOrder::where('order_wedding_id',$orderWedding->id)->get();
        $accommodationPrice = $weddingAccommodations->pluck('public_rate')->sum();
        $extra_bed_price = $extra_bed_orders->pluck('total_price')->sum();
        
        $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
        $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
        $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
        $final_price = $accommodationPrice //proses
            + $extra_bed_price //proses
            + $orderWedding->transport_invitations_price
            + $orderWedding->addser_price
            + $orderWedding->package_price
            + $orderWedding->markup;
        $orderWedding->update([
            "wedding_date"=>$wedding_date,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "slot"=>$request->slot,
            "reception_date_start"=>$reception_date_start,
            "lunch_venue_date"=>$lunch_venue_date,
            "dinner_venue_date"=>$dinner_venue_date,
            'number_of_invitation'=>$number_of_invitation,
            'accommodation_price'=>$accommodationPrice,
            'extra_bed_price'=>$extra_bed_price,
            'final_price'=>$final_price,
        ]);
        return redirect("/validate-orders-wedding-$orderWedding->id")->with('success','Wedding and Reception has been updated !');
    }
    // OK FUNCTION UPDATE CEREMONY VENUE (ADMIN ORDER WEDDING)=========================================================================================>
    public function func_validate_wedding_order_ceremony_venue(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        if ($request->wedding_date) {
            $wedding_venue_id = $request->ceremonial_venue_id;
            $service = WeddingVenues::where('id',$wedding_venue_id)->first();
            if ($request->wedding_date) {
                $wedding_date = date('Y-m-d',strtotime($request->wedding_date));
            }else{
                $wedding_date = null;
            }
            $req_slot = $request->slot;
            $slots = json_decode($service->slot);
            $bps = json_decode($service->basic_price);
            $aps = json_decode($service->arrangement_price);
            $cbp = count($bps);
            foreach ($slots as $key => $slot) {
                if ( $req_slot == $slot) {
                    $basic_price = $bps[$key];
                    $arrangement_price = $aps[$key];
                    break;
                }else{
                    $basic_price = null;
                    $arrangement_price = null;
                }
            }
            if ($orderWedding->ceremony_venue_decoration_id) {
                $basic_or_arrangement = "Arrangement";
                $ceremony_venue_price = $arrangement_price;
            }else{
                $basic_or_arrangement = "Basic";
                $ceremony_venue_price = $basic_price;
            }
                // FINAL PRICE 
                
                if ($orderWedding->service == "Wedding Package") {
                    $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
                    $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
                    $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
                    $final_price = $orderWedding->accommodation_price
                        + $orderWedding->extra_bed_price
                        + $orderWedding->transport_invitations_price
                        + $orderWedding->addser_price
                        + $orderWedding->package_price
                        + $orderWedding->markup;
                }else{
                    $makeup_price = is_array($orderWedding->makeup_price) ? array_sum($orderWedding->makeup_price): $orderWedding->makeup_price;
                    $documentation_price = is_array($orderWedding->documentation_price) ? array_sum($orderWedding->documentation_price): $orderWedding->documentation_price;
                    $entertainment_price = is_array($orderWedding->entertainment_price) ? array_sum($orderWedding->entertainment_price): $orderWedding->entertainment_price;
                    $final_price = $ceremony_venue_price // proses
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
                        + $orderWedding->addser_price
                        + $orderWedding->markup;
                }
                $orderWedding->update([
                    'service_id'=>$wedding_venue_id,
                    'slot'=>$request->slot,
                    'basic_or_arrangement'=>$basic_or_arrangement,
                    'ceremony_venue_id'=>$wedding_venue_id,
                    'wedding_date'=>$wedding_date,
                    'ceremony_venue_price'=>$ceremony_venue_price,
                    'ceremony_venue_invitations'=>$request->ceremony_venue_invitations,
                    'final_price'=>$final_price,
                ]);
            if ($wedding_venue_id) {
                
                // dd($order_wedding);
                return redirect()->back()->with('success','Wedding order has been updated!');
            }else{
                return redirect("/validate-orders-wedding-$orderWedding->id")->with('errors_message','You have not yet decided on a ceremony venue!');
            }
        }else {
            return redirect("/validate-orders-wedding-$orderWedding->id")->with('errors_message','The wedding date has not been determined!');
        }
    }
    // OK FUNCTION REMOVE CEREMONY VENUE (ADMIN ORDER WEDDING)=========================================================================================>
    public function func_admin_delete_ceremony_venue(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        $ceremony_venue_price = null;
        $ceremony_venue_decoration_price = null;
        // FINAL PRICE
        $room_invitations_price = is_array($order_wedding->room_invitations_price) ? array_sum($order_wedding->room_invitations_price): $order_wedding->room_invitations_price;
        $transports_price = is_array($order_wedding->transports_price) ? array_sum($order_wedding->transports_price): $order_wedding->transports_price;
        $makeup_price = is_array($order_wedding->makeup_price) ? array_sum($order_wedding->makeup_price): $order_wedding->makeup_price;
        $documentation_price = is_array($order_wedding->documentation_price) ? array_sum($order_wedding->documentation_price): $order_wedding->documentation_price;
        $entertainment_price = is_array($order_wedding->entertainment_price) ? array_sum($order_wedding->entertainment_price): $order_wedding->entertainment_price;
        $additional_services_price = $order_wedding->additional_services_price ? $order_wedding->additional_services_price: 0;
        $final_price = $ceremony_venue_price 
            + $ceremony_venue_decoration_price 
            + $order_wedding->reception_venue_price 
            + $order_wedding->reception_venue_decoration_price
            + $order_wedding->room_bride_price
            + $room_invitations_price
            + $transports_price
            + $makeup_price
            + $documentation_price
            + $entertainment_price
            + $additional_services_price
            + $order_wedding->markup;

        if ($order_wedding) {
            $order_wedding->update([
                'slot'=>null,
                'wedding_date'=>null,
                'basic_or_arrangement'=>null,
                'ceremony_venue_duration'=>null,
                'ceremony_venue_id'=>null,
                'ceremony_venue_price'=>null,
                'ceremony_venue_decoration_id'=>null,
                'ceremony_venue_decoration_price'=>null,
                'ceremony_venue_invitations'=>null,
                'final_price'=>$final_price,
            ]);
            // dd($order_wedding);
            return redirect()->back()->with('success','Wedding order has been updated!');
        }else{
            return redirect("/validate-orders-wedding-$order_wedding->id")->with('errors_message','You have not yet decided on a ceremony venue!');
        }
    }
    // OK FUNCTION ADD OR UPDATE DECORATION CEREMONY VENUE (ADMIN ORDER WEDDING)=========================================================================================>
    public function func_admin_update_decoration_ceremony_venue(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        $ceremony_venue_decoration = VendorPackage::where('id',$request->ceremony_venue_decoration_id)->first();

        if ($ceremony_venue_decoration) {
            // FINAL PRICE
            $room_invitations_price = is_array($order_wedding->room_invitations_price) ? array_sum($order_wedding->room_invitations_price): $order_wedding->room_invitations_price;
            $transports_price = is_array($order_wedding->transports_price) ? array_sum($order_wedding->transports_price): $order_wedding->transports_price;
            $makeup_price = is_array($order_wedding->makeup_price) ? array_sum($order_wedding->makeup_price): $order_wedding->makeup_price;
            $documentation_price = is_array($order_wedding->documentation_price) ? array_sum($order_wedding->documentation_price): $order_wedding->documentation_price;
            $entertainment_price = is_array(json_decode($order_wedding->entertainment_price)) ? array_sum($order_wedding->entertainment_price): $order_wedding->entertainment_price;
            $additional_services_price = is_array(json_decode($order_wedding->additional_services_price)) ? array_sum(json_decode($order_wedding->additional_services_price)): 0;
            $final_price = $order_wedding->ceremony_venue_price 
                + $ceremony_venue_decoration->publish_rate 
                + $order_wedding->reception_venue_price 
                + $order_wedding->reception_venue_decoration_price
                + $order_wedding->room_bride_price
                + $room_invitations_price
                + $transports_price
                + $makeup_price
                + $documentation_price
                + $entertainment_price
                + $additional_services_price
                + $order_wedding->markup;
            $order_wedding->update([
                'ceremony_venue_decoration_id'=>$ceremony_venue_decoration->id,
                'ceremony_venue_decoration_price'=>$ceremony_venue_decoration->publish_rate,
                'basic_or_arrangement'=>"Arrangement",
                'final_price'=>$final_price,
            ]);
            // dd($order_wedding,$additional_services_price );
            return redirect("/validate-orders-wedding-$order_wedding->id#decoration-ceremony-venue")->with('success','Decoration has been updated!');
        }else{
            return redirect("/validate-orders-wedding-$order_wedding->id#decoration-ceremony-venue")->with('success','Nothing changed!');
        }
    }
    



    
    // OK FUNCTION REMOVE DECORATION CEREMONY VENUE (ADMIN ORDER WEDDING)=========================================================================================>
    public function func_admin_delete_decoration_ceremony_venue(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        // FINAL PRICE
        $ceremony_venue_decoration_price = 0;
        $room_invitations_price = is_array($order_wedding->room_invitations_price) ? array_sum($order_wedding->room_invitations_price): $order_wedding->room_invitations_price;
        $transports_price = is_array($order_wedding->transports_price) ? array_sum($order_wedding->transports_price): $order_wedding->transports_price;
        $makeup_price = is_array($order_wedding->makeup_price) ? array_sum($order_wedding->makeup_price): $order_wedding->makeup_price;
        $documentation_price = is_array($order_wedding->documentation_price) ? array_sum($order_wedding->documentation_price): $order_wedding->documentation_price;
        $entertainment_price = is_array($order_wedding->entertainment_price) ? array_sum($order_wedding->entertainment_price): 0;
        $additional_services_price = is_array(json_decode($order_wedding->additional_services_price)) ? array_sum(json_decode($order_wedding->additional_services_price)): 0;
        $final_price = $order_wedding->ceremony_venue_price 
            + $ceremony_venue_decoration_price
            + $order_wedding->reception_venue_price 
            + $order_wedding->reception_venue_decoration_price
            + $order_wedding->room_bride_price
            + $room_invitations_price
            + $transports_price
            + $makeup_price
            + $documentation_price
            + $entertainment_price
            + $additional_services_price
            + $order_wedding->markup;
        $order_wedding->update([
            'ceremony_venue_decoration_id'=>null,
            'ceremony_venue_decoration_price'=>null,
            'basic_or_arrangement'=>"Basic",
            'final_price'=>$final_price,
        ]);
        // dd($order_wedding);
        return redirect("/validate-orders-wedding-$order_wedding->id#decoration-ceremony-venue")->with('success','Decoration has been removed from wedding order!');
    }
    
    // OK FUNCTION ADD OR UPDATE RECEPTION VENUE (ADMIN ORDER WEDDING)=========================================================================================>
    public function admin_func_update_reception_venue(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        if ($request->reception_date_start) {
            
            $reception_venue = VendorPackage::where('id',$request->reception_venue_id)->first();

            if ($reception_venue) {
                $reception_time = date('H.i',strtotime($request->reception_time_start));
                $reception_date_start = date("Y-m-d",strtotime($request->reception_date_start)).date(" H.i",strtotime($reception_time));
                // FINAL PRICE
                $room_invitations_price = is_array($order_wedding->room_invitations_price) ? array_sum($order_wedding->room_invitations_price): $order_wedding->room_invitations_price;
                $transports_price = is_array($order_wedding->transports_price) ? array_sum($order_wedding->transports_price): $order_wedding->transports_price;
                $makeup_price = is_array($order_wedding->makeup_price) ? array_sum($order_wedding->makeup_price): $order_wedding->makeup_price;
                $documentation_price = is_array($order_wedding->documentation_price) ? array_sum($order_wedding->documentation_price): $order_wedding->documentation_price;
                $entertainment_price = is_array($order_wedding->entertainment_price) ? array_sum($order_wedding->entertainment_price): $order_wedding->entertainment_price;
                $additional_services_price = is_array(json_decode($order_wedding->additional_services_price)) ? array_sum(json_decode($order_wedding->additional_services_price)): 0;
                $final_price = $order_wedding->ceremony_venue_price 
                    + $order_wedding->ceremony_venue_decoration_price 
                    + $reception_venue->publish_rate 
                    + $order_wedding->reception_venue_decoration_price
                    + $order_wedding->room_bride_price
                    + $room_invitations_price
                    + $transports_price
                    + $makeup_price
                    + $documentation_price
                    + $entertainment_price
                    + $additional_services_price
                    + $order_wedding->markup;
                $order_wedding->update([
                    'reception_date_start'=>$reception_date_start,
                    'reception_venue_id'=>$reception_venue->id,
                    'reception_venue_price'=>$reception_venue->publish_rate,
                    'reception_venue_invitations'=>$request->reception_venue_invitations,
                    'final_price'=>$final_price,
                ]);
                return redirect("/validate-orders-wedding-$order_wedding->id#orderWeddingReceptionVenue")->with('success','Decoration has been updated!');
            }else{
                return redirect("/validate-orders-wedding-$order_wedding->id#orderWeddingReceptionVenue")->with('success','Nothing changed!');
            }
        }else{
            return redirect("/validate-orders-wedding-$order_wedding->id#orderWeddingReceptionVenue")->with('errors_message','Reception date required!');
        }
    }
    // OK FUNCTION REMOVE RECEPTION VENUE (ADMIN ORDER WEDDING)=========================================================================================>
    public function admin_func_delete_reception_venue(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        // FINAL PRICE
        $reception_venue_price = null;
        $reception_venue_decoration_price = null;
        $room_invitations_price = is_array($order_wedding->room_invitations_price) ? array_sum($order_wedding->room_invitations_price): $order_wedding->room_invitations_price;
        $transports_price = is_array($order_wedding->transports_price) ? array_sum($order_wedding->transports_price): $order_wedding->transports_price;
        $makeup_price = is_array($order_wedding->makeup_price) ? array_sum($order_wedding->makeup_price): $order_wedding->makeup_price;
        $documentation_price = is_array($order_wedding->documentation_price) ? array_sum($order_wedding->documentation_price): $order_wedding->documentation_price;
        $entertainment_price = is_array($order_wedding->entertainment_price) ? array_sum($order_wedding->entertainment_price): 0;
        $additional_services_price = is_array(json_decode($order_wedding->additional_services_price)) ? array_sum(json_decode($order_wedding->additional_services_price)): 0;
        $final_price = $order_wedding->ceremony_venue_price 
            + $order_wedding->ceremony_venue_decoration_price
            + $reception_venue_price 
            + $reception_venue_decoration_price
            + $order_wedding->room_bride_price
            + $room_invitations_price
            + $transports_price
            + $makeup_price
            + $documentation_price
            + $entertainment_price
            + $additional_services_price
            + $order_wedding->markup;
        $order_wedding->update([
            'reception_venue_id'=>null,
            'reception_venue_price'=>null,
            'reception_date_start'=>null,
            'reception_venue_decoration_id'=>null,
            'reception_venue_decoration_price'=>null,
            'reception_venue_invitations'=>null,
            'final_price'=>$final_price,
        ]);
        // dd($order_wedding);
        return redirect("/validate-orders-wedding-$order_wedding->id#orderWeddingReceptionVenue")->with('success','Reception venue has been removed from wedding order!');
    }

    // OK FUNCTION ADD OR UPDATE RECEPTION VENUE DECORATION (ADMIN ORDER WEDDING)=========================================================================================>
    public function admin_func_update_decoration_reception_venue(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        $reception_venue_decoration = VendorPackage::where('id',$request->reception_venue_decoration_id)->first();
        // FINAL PRICE
        $room_invitations_price = is_array($order_wedding->room_invitations_price) ? array_sum($order_wedding->room_invitations_price): $order_wedding->room_invitations_price;
        $transports_price = is_array($order_wedding->transports_price) ? array_sum($order_wedding->transports_price): $order_wedding->transports_price;
        $makeup_price = is_array($order_wedding->makeup_price) ? array_sum($order_wedding->makeup_price): $order_wedding->makeup_price;
        $documentation_price = is_array($order_wedding->documentation_price) ? array_sum($order_wedding->documentation_price): $order_wedding->documentation_price;
        $entertainment_price = is_array($order_wedding->entertainment_price) ? array_sum($order_wedding->entertainment_price): 0;
        $additional_services_price = is_array(json_decode($order_wedding->additional_services_price)) ? array_sum(json_decode($order_wedding->additional_services_price)): 0;
        $final_price = $order_wedding->ceremony_venue_price 
            + $order_wedding->ceremony_venue_decoration_price
            + $order_wedding->reception_venue_price 
            + $order_wedding->reception_venue_decoration_price 
            + $reception_venue_decoration->publish_rate
            + $order_wedding->room_bride_price
            + $room_invitations_price
            + $transports_price
            + $makeup_price
            + $documentation_price
            + $entertainment_price
            + $additional_services_price
            + $order_wedding->markup;

        if ($reception_venue_decoration) {
            $order_wedding->update([
                'reception_venue_decoration_id'=>$reception_venue_decoration->id,
                'reception_venue_decoration_price'=>$reception_venue_decoration->publish_rate,
                'final_price'=>$final_price,
            ]);
            return redirect("/validate-orders-wedding-$order_wedding->id#orderWeddingDecorationReceptionVenue")->with('success','Reception venue decoration has been updated!');
        }else{
            return redirect("/validate-orders-wedding-$order_wedding->id#orderWeddingDecorationReceptionVenue")->with('success','Nothing Changed!');
        }
        // dd($order_wedding);
    }

    // OK FUNCTION REMOVE RECEPTION VENUE (ADMIN ORDER WEDDING)=========================================================================================>
    public function admin_func_delete_decoration_reception_venue(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        // FINAL PRICE
        $reception_venue_decoration_price = null;
        $room_invitations_price = is_array($order_wedding->room_invitations_price) ? array_sum($order_wedding->room_invitations_price): $order_wedding->room_invitations_price;
        $transports_price = is_array($order_wedding->transports_price) ? array_sum($order_wedding->transports_price): $order_wedding->transports_price;
        $makeup_price = is_array($order_wedding->makeup_price) ? array_sum($order_wedding->makeup_price): $order_wedding->makeup_price;
        $documentation_price = is_array($order_wedding->documentation_price) ? array_sum($order_wedding->documentation_price): $order_wedding->documentation_price;
        $entertainment_price = is_array($order_wedding->entertainment_price) ? array_sum($order_wedding->entertainment_price): 0;
        $additional_services_price = is_array(json_decode($order_wedding->additional_services_price)) ? array_sum(json_decode($order_wedding->additional_services_price)): 0;
        $final_price = $order_wedding->ceremony_venue_price 
            + $order_wedding->ceremony_venue_decoration_price
            + $order_wedding->reception_venue_price 
            + $reception_venue_decoration_price 
            + $order_wedding->publish_rate
            + $order_wedding->room_bride_price
            + $room_invitations_price
            + $transports_price
            + $makeup_price
            + $documentation_price
            + $entertainment_price
            + $additional_services_price
            + $order_wedding->markup;
        $order_wedding->update([
            'reception_venue_decoration_id'=>null,
            'reception_venue_decoration_price'=>null,
            'final_price'=>$final_price,
        ]);
        // dd($order_wedding);
        return redirect("/validate-orders-wedding-$order_wedding->id#orderWeddingDecorationReceptionVenue")->with('success','Reception venue has been removed from wedding order!');
    }
    // OK FUNCTION ADD OR UPDATE ADDITIONAL SERVICE (ADMIN ORDER WEDDING)=========================================================================================>
    public function admin_func_add_additional_service_to_wedding_order(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
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
        $room_invitations_price = is_array($order_wedding->room_invitations_price) ? array_sum($order_wedding->room_invitations_price): $order_wedding->room_invitations_price;
        $transports_price = is_array($order_wedding->transports_price) ? array_sum($order_wedding->transports_price): $order_wedding->transports_price;
        $makeup_price = is_array($order_wedding->makeup_price) ? array_sum($order_wedding->makeup_price): $order_wedding->makeup_price;
        $documentation_price = is_array($order_wedding->documentation_price) ? array_sum($order_wedding->documentation_price): $order_wedding->documentation_price;
        $entertainment_price = is_array($order_wedding->entertainment_price) ? array_sum($order_wedding->entertainment_price): $order_wedding->entertainment_price;
        $sum_additional_services_price = is_array($additional_services_price) ? array_sum($additional_services_price): 0;
        $final_price = $order_wedding->ceremony_venue_price 
            + $order_wedding->ceremony_venue_decoration_price 
            + $order_wedding->reception_venue_price 
            + $order_wedding->reception_venue_decoration_price
            + $order_wedding->room_bride_price
            + $room_invitations_price
            + $transports_price
            + $makeup_price
            + $documentation_price
            + $entertainment_price
            + $sum_additional_services_price
            + $order_wedding->markup;
        $order_wedding->update([
            'additional_services'=>$additional_services,
            'additional_services_price'=>$sum_additional_services_price,
            'final_price'=>$final_price,
        ]);
        // dd($order_wedding);
        return redirect("/validate-orders-wedding-$order_wedding->id#additionalServices")->with('success','Your order has been updated!');
    }
    // OK FUNCTION ADD REMARK (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function func_validate_wedding_order_remark(Request $request,$id){
        $order_wedding = OrderWedding::find($id);
        $remark = $request->remark;
        $order_wedding->update([
            'remark'=>$remark,
        ]);
        // dd($order_wedding);
        return redirect("/validate-orders-wedding-$order_wedding->id#remarkPage")->with('success','Your order has been updated!');
    }
    // OK FUNCTION UPDATE CONFIRMATION NUMBER (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function admin_func_update_confirmation_numbber(Request $request,$id){
        $now = Carbon::now();
        $order_wedding = OrderWedding::find($id);
        if ($order_wedding->handled_by) {
            $admin_reservation = Auth::user()->where('id',$order_wedding->handled_by)->first();
        }else{
            $admin_reservation = Auth::user();
        }
        $confirmation_number = $request->confirmation_number;
        $order_wedding->update([
            'confirmation_number'=>$confirmation_number,
            'handled_by'=>$admin_reservation->id,
            'handled_date'=>$now,
        ]);
        $agent = Auth::user()->where('id',$order_wedding->agent_id)->first();
        $order_log =new OrderLog([
            "order_wedding_id"=>$order_wedding->id,
            "action"=>"Update confirmation number",
            "url"=>$request->getClientIp(),
            "method"=>"Update",
            "agent"=>$agent->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        // dd($order_wedding);
        return redirect("/validate-orders-wedding-$order_wedding->id")->with('success','Your order has been updated!');
    }
    // OK FUNCTION VALIDATE ORDER WEDDING (EDIT-ORDER-WEDDING-ORDERNO)=========================================================================================>
    public function admin_func_validate_order_wedding(Request $request,$id){
        ini_set('max_execution_time', 300);
        $order_wedding = OrderWedding::find($id);
        $now = Carbon::now();
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $agent = Auth::user()->where('id',$order_wedding->agent_id)->first();
        $admin = Auth::user()->where('id',$order_wedding->handled_by)->first();
        $wedding_package = Weddings::where('id',$order_wedding->service_id)->first();
        $status = "Approved";
        if ($order_wedding->handled_by) {
            $admin_reservation = Auth::user()->where('id',$order_wedding->handled_by)->first();
        }else{
            $admin_reservation = Auth::user();
        }
        if ($order_wedding->service == "Wedding Package") {
            $final_price = $wedding_package->publish_rate + $request->accommodation_price + $request->transport_price;
            $package_price = $wedding_package->publish_rate;
        }else{
            $final_price = $order_wedding->final_price;
            $package_price = NULL;
        }
        $order_wedding->update([
            'status'=>$status,
            'handled_by'=>$admin_reservation->id,
            'verified_by'=>$admin->id,
            'verified_date'=>$now,
        ]);
        $reservation = Reservation::where('id',$order_wedding->rsv_id)->first();
        if ($reservation) {
            $reservation->update([
                'checkin'=>$order_wedding->checkin,
                'checkout'=>$order_wedding->checkout,
                'status'=>"Active",
            ]);
        }

        // INVOICE
        $usdrates = UsdRates::where('name','USD')->first();
        $cnyrates = UsdRates::where('name','CNY')->first();
        $twdrates = UsdRates::where('name','TWD')->first();
        $idrrates = UsdRates::where('name','IDR')->first();
        $business = BusinessProfile::where('id',1)->first();
        
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        $invoices = InvoiceAdmin::where('agent_id',$agent->id)->get();
        if ($invoices) {
            $c_inv = count($invoices);
        }else{
            $c_inv = 0;
        }
        $inv_no = $reservation->rsv_no;
        $due_date = date('Y-m-d', strtotime("-7 days", strtotime($order_wedding->checkin)));
        $total_idr = $order_wedding->final_price * $usdrates->rate;
        $total_cny = ceil($total_idr / $cnyrates->rate);
        $total_twd = ceil($total_idr / $twdrates->rate);
        $currency_id = $request->currency;
        $bank_id = $request->bank;
        $currency = UsdRates::where('id',$currency_id)->first();
        $bank = BankAccount::where('currency',$currency->name)->first();
        if ($currency->name == 'USD') {
            $balance = $order_wedding->final_price;
        }elseif ($currency->name == 'CNY') {
            $balance = $total_cny;
        }elseif ($currency->name == 'TWD') {
            $balance = $total_twd;
        }elseif ($currency->name == 'IDR') {
            $balance = $total_idr;
        }
        $admin_code = date('Ymd',strtotime($now)).'-'.++$c_inv.' '.$admin->code;
        
        if ($invoice) {
            $invoice->update([
                "admin_code"=>$admin_code,
                "created_by"=>$admin->id,
                "agent_id"=>$agent->id,
                "inv_no"=>$inv_no,
                "rsv_id"=>$reservation->id,
                "inv_date"=>$now,
                "due_date"=>$due_date,
                "total_usd"=>$order_wedding->final_price,
                "total_idr"=>$total_idr,
                "total_cny"=>$total_cny,
                "total_twd"=>$total_twd,
                "rate_usd"=>$usdrates->rate,
                "sell_usd"=>$usdrates->sell,
                "rate_twd"=>$twdrates->rate,
                "sell_twd"=>$twdrates->sell,
                "rate_cny"=>$cnyrates->rate,
                "sell_cny"=>$cnyrates->sell,
                "balance"=>$balance,
                "bank_id"=>$bank_id,
                "currency_id"=>$currency_id,
            ]);
        }else{
            $invoice = new InvoiceAdmin([
                "admin_code"=>$admin_code,
                "created_by"=>$admin->id,
                "agent_id"=>$agent->id,
                "inv_no"=>$inv_no,
                "rsv_id"=>$reservation->id,
                "inv_date"=>$now,
                "due_date"=>$due_date,
                "total_usd"=>$order_wedding->final_price,
                "total_idr"=>$total_idr,
                "total_cny"=>$total_cny,
                "total_twd"=>$total_twd,
                "rate_usd"=>$usdrates->rate,
                "sell_usd"=>$usdrates->sell,
                "rate_twd"=>$twdrates->rate,
                "sell_twd"=>$twdrates->sell,
                "rate_cny"=>$cnyrates->rate,
                "sell_cny"=>$cnyrates->sell,
                "balance"=>$balance,
                "bank_id"=>$bank_id,
                "currency_id"=>$currency_id,
            ]);
            $invoice->save();
        }
        $agent = Auth::user()->where('id',$order_wedding->agent_id)->first();
        $email = $agent->email;
        
        $order_log =new OrderLog([
            "order_wedding_id"=>$order_wedding->id,
            "action"=>"Approve Order",
            "url"=>$request->getClientIp(),
            "method"=>"Approve",
            "agent"=>$agent->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();

        $wedding = Weddings::where('id',$order_wedding->service_id)->first();
        $attentions = Attention::where('page','user-order-detail')->get();
        $bride = Brides::where('id',$order_wedding->brides_id)->first();
        $transports = Transports::all();
        $transport_id = json_decode($order_wedding->transport_id);
        $transport_type = json_decode($order_wedding->transport_type);
        $logo = public_path('images/logo-color-bali-kami.png');
        $other_service_ids = json_decode($order_wedding->additional_services);
        $other_services = VendorPackage::where('type','Other')->get(); 
        $wedding_accommodations = WeddingAccomodations::where('order_wedding_package_id',$order_wedding->id)->get();
        $additional_transports = WeddingPlannerTransport::where('order_wedding_id',$order_wedding->id)->get();
        $additional_charges = WeddingAdditionalServices::where('order_wedding_id',$order_wedding->id)->where('status','Approved')->get();
        $flights = Flights::where('order_wedding_id',$order_wedding->id)->get();
        $bankAccounts = BankAccount::all();
        $bank_account = $invoice?BankAccount::where('id',$invoice->bank_id)->first():NULL;
        $additional_services= AdditionalService::where('rsv_id',$reservation->id)->get();
        $transactions_usd = Transactions::where('invoice_id',$invoice->id)->where('kurs',"USD")->where('status',"Active")->get();
        $transactions_cny = Transactions::where('invoice_id',$invoice->id)->where('kurs',"CNY")->where('status',"Active")->get();
        $transactions_twd = Transactions::where('invoice_id',$invoice->id)->where('kurs',"TWD")->where('status',"Active")->get();
        $transactions_idr = Transactions::where('invoice_id',$invoice->id)->where('kurs',"IDR")->where('status',"Active")->get();
        $total_deposit_usd = $transactions_usd->sum('amount');
        $total_deposit_cny = $transactions_cny->sum('amount');
        $total_deposit_twd = $transactions_twd->sum('amount');
        $total_deposit_idr = $transactions_idr->sum('amount');
        $balance_usd = $invoice->total_usd - $total_deposit_usd;
        $balance_cny = $invoice->total_cny - $total_deposit_cny;
        $balance_twd = $invoice->total_twd - $total_deposit_twd;
        $balance_idr = $invoice->total_idr - $total_deposit_idr;
        // CREATE DATA EMAIL
        $data = [
            "orderWedding"=>$order_wedding,
            "admin"=>$admin,
            'title'=>"Confirmation Order - ".$order_wedding->orderno,
            "email"=>$email,
            "business"=>$business,
            "attentions"=>$attentions,
            "tax"=>$tax,
            "twdrates"=>$twdrates,
            "cnyrates"=>$cnyrates,
            "usdrates"=>$usdrates,
            "now"=>$now,
            "order"=>$order_wedding,
            "reservation"=>$reservation,
            "agent"=>$agent,
            "bride"=>$bride,
            "order_wedding"=>$order_wedding,
            "logo"=>$logo,
            "wedding"=>$wedding,
            "transports"=>$transports,
            "transport_id"=>$transport_id,
            "transport_type"=>$transport_type,
            "other_service_ids"=>$other_service_ids,
            "other_services"=>$other_services,
            "wedding_accommodations"=>$wedding_accommodations,
            "additional_transports"=>$additional_transports,
            "additional_charges"=>$additional_charges,
            "flights"=>$flights,
            "invoice"=>$invoice,
            "bankAccounts"=>$bankAccounts,
            "bank_account"=>$bank_account,
            "additional_services"=>$additional_services,
            "transactions_usd"=>$transactions_usd,
            "transactions_cny"=>$transactions_cny,
            "transactions_twd"=>$transactions_twd,
            "transactions_idr"=>$transactions_idr,
            "balance_usd"=>$balance_usd,
            "balance_cny"=>$balance_cny,
            "balance_twd"=>$balance_twd,
            "balance_idr"=>$balance_idr,
            "order_link"=>'https://online.balikamitour.com/detail-order-wedding-'.$order_wedding->orderno,
            
        ];
        

        // GENERATE INVOICE DOCUMENT
        // if (File::exists("storage/document/invoice-".$inv_no."-".$order_wedding->id."_en.pdf")) {
        //     File::delete("storage/document/invoice-".$inv_no."-".$order_wedding->id."_en.pdf");
        // }
        // $pdf = PDF::loadView('emails.orderContractWeddingEn', $data);
        // $pdf->save("storage/document/invoice-".$inv_no."-".$order_wedding->id."_en.pdf");

        // if (File::exists("storage/document/invoice-".$inv_no."-".$order_wedding->id."_zh.pdf")) {
        //     File::delete("storage/document/invoice-".$inv_no."-".$order_wedding->id."_zh.pdf");
        // }

        // $pdf = PDF::loadView('emails.orderContractWeddingZh', $data);
        // $pdf->save("storage/document/invoice-".$inv_no."-".$order_wedding->id."_zh.pdf");

        // SEND INVOICE TO AGENT
        // if (config('filesystems.default') == 'public'){
        //     $contract_en_path =realpath("storage/document/invoice-".$inv_no."-".$order_wedding->id."_en.pdf");
        //     $contract_zh_path =realpath("storage/document/invoice-".$inv_no."-".$order_wedding->id."_zh.pdf");
        // }else {
        //     $contract_en_path = storage::url("storage/document/invoice-".$inv_no."-".$order_wedding->id."_en.pdf");
        //     $contract_zh_path = storage::url("storage/document/invoice-".$inv_no."-".$order_wedding->id."_zh.pdf");
        // }
        Mail::send('emails.weddingConfirmationOrder', $data, function($message)use($data) {
            $message->to($data["email"])
                ->subject($data["title"]);
                // ->attach($contract_en_path)
                // ->attach($contract_zh_path);
        });
        return redirect("/validate-orders-wedding-$order_wedding->id")->with('success','The order has been validated!');
    }
    // FUNCTION ADD ORDER WEDDING FLIGHT --------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_add_order_wedding_flight(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        $status = "Active";
        $group = $request->flight_group;
        $order_wedding_id = $id;
        $type = $request->type;
        $time = $request->time;
        $flight = $request->flight;
        $guests = $request->guests;
        $guests_contact = $request->guests_contact;
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
        return redirect("/validate-orders-wedding-$id#flightSchedule")->with('success','Flight schedule has been added!');
    }
    
    // FUNCTION DELETE ORDER WEDDING FLIGHT --------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_delete_order_wedding_flight_admin(Request $request,$id) {
        $flight = Flights::find($id);
        $order_wedding = OrderWedding::where('id',$flight->order_wedding_id)->first();
        $flight->delete();
        return redirect("/validate-orders-wedding-$order_wedding->id#flightSchedule")->with('success','Flight schedule has been removed!');
    }
    

// ===============================================================================================================================================================
// INVITATIONS - ORDER WEDDING
// ===============================================================================================================================================================
    // FUNCTION ADD INVITATION --------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_add_order_wedding_invitation(Request $request,$id){
        $orderWedding = OrderWedding::find($id);
        if($request->hasFile("cover")){
            $file=$request->file("cover");
            $coverName=time().'_'.$file->getClientOriginalName();
            $file->move("storage/weddings/invitations/",$coverName);
            $invitations = new WeddingInvitations ([
                'order_wedding_id' =>$id,
                'sex' =>"o",
                'name' =>$request->name,
                'chinese_name' =>$request->name_mandarin,
                'country' =>$request->country,
                'passport_no' =>$request->identification_no,
                'passport_img' =>$coverName,
                'phone' =>$request->phone,
            ]);
            $invitations->save();
            return redirect("/validate-orders-wedding-$orderWedding->id#weddingInvitations")->with('success','New invitation has been added!');
        }else{
            return redirect("/validate-orders-wedding-$orderWedding->id#weddingInvitations")->with('success','Passport img can not empty!');
        }
    }
    // FUNCTION ADD ORDER WEDDING INVITATION --------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_edit_order_wedding_invitation(Request $request,$id){
        $guest = WeddingInvitations::find($id);
        $orderWedding = OrderWedding::where('id',$guest->order_wedding_id)->first();
        if($request->hasFile("cover")){
            if (File::exists("storage/weddings/invitations/".$guest->passport_img)) {
                File::delete("storage/weddings/invitations/".$guest->passport_img);
            }
            $file=$request->file("cover");
            $coverName=time().'_'.$file->getClientOriginalName();
            $file->move("storage/weddings/invitations/",$coverName);
        }
        $guest->update ([
            'order_wedding_id' =>$orderWedding->id,
            'sex' =>"o",
            'name' =>$request->name,
            'chinese_name' =>$request->name_mandarin,
            'country' =>$request->country,
            'passport_no' =>$request->identification_no,
            'passport_img' =>$coverName,
            'phone' =>$request->phone,
        ]);
        return redirect("/validate-orders-wedding-$orderWedding->id#weddingInvitations")->with('success','New invitation has been added!');
    }

    // FUNCTION DELETE ORDER WEDDING INVITATION --------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_delete_order_wedding_invitation(Request $request,$id) {
        $guest = WeddingInvitations::find($id);
        $order_wedding = OrderWedding::where('id',$guest->order_wedding_id)->first();
        if (File::exists("storage/weddings/invitations/".$guest->passport_img)) {
            File::delete("storage/weddings/invitations/".$guest->passport_img);
        }
        $guest->delete();
        return redirect("/validate-orders-wedding-$order_wedding->id#weddingInvitations")->with('success','Invitation has been deleted!');
    }

    // Function Activated =============================================================================================================>
    public function func_confirm_order_wedding(Request $request,$id){
        $now = Carbon::now();
        $orderWedding = OrderWedding::find($id);
        $statusOrder = "Approved";
        $verifiedDate =Carbon::now();
        $agent = Auth::user()->where('id',$orderWedding->agent_id)->first();
        $admin = Auth::user();

        // CONFIRM ORDER
        $orderWedding->update([
            "status"=>$statusOrder,
            "verified_by"=>Auth::user()->id,
            "verified_date"=>$verifiedDate,
        ]);
        $order_log =new OrderLog([
            "order_id"=>$id,
            "action"=>"Confirm Order",
            "url"=>$request->getClientIp(),
            "method"=>"Confirm",
            "agent"=>$agent->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();

        //CREATE INVOICE
        $usdrates = UsdRates::where('name','USD')->first();
        $cnyrates = UsdRates::where('name','CNY')->first();
        $twdrates = UsdRates::where('name','TWD')->first();
        $idrrates = UsdRates::where('name','IDR')->first();
        $reservation = Reservation::where('id',$orderWedding->rsv_id)->first();
        $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        if ($orderWedding->service == "Ceremony Venue") {
            $due_date = date('Y-m-d', strtotime("-7 days", strtotime($orderWedding->wedding_date)));
        }elseif($orderWedding->service == "Reception Venue"){
            $due_date = date('Y-m-d', strtotime("-7 days", strtotime($orderWedding->reception_date_start)));
        }elseif($orderWedding->service == "Wedding Package"){
            $due_date = date('Y-m-d', strtotime("-7 days", strtotime($orderWedding->checkin)));
        }else{
            $due_date = date('Y-m-d', strtotime("+7 days", strtotime($now)));
        }
        $inv = InvoiceAdmin::where('created_at',$now)->get();
        $count_inv = count($inv)+1;
        $inv_no = date('Ymd',strtotime($now))."-".$count_inv." ".$admin->code;
        $total_idr = $orderWedding->final_price * $usdrates->rate;
        $total_cny = ceil($total_idr / $cnyrates->rate);
        $total_twd = ceil($total_idr / $twdrates->rate);
        $currency = $request->currency;
        if ($invoice) {
            $invoice->update([
                "inv_no"=>$inv_no,
                "rsv_id"=>$reservation->id,
                "inv_date"=>$now,
                "due_date"=>$due_date,
                "total_usd"=>$orderWedding->final_price,
                "total_idr"=>$total_idr,
                "total_cny"=>$total_cny,
                "total_twd"=>$total_twd,
                "bank_id"=>$currency,
            ]);
        }else{
            $invoice = new InvoiceAdmin([
                "inv_no"=>$inv_no,
                "rsv_id"=>$reservation->id,
                "inv_date"=>$now,
                "due_date"=>$due_date,
                "total_usd"=>$orderWedding->final_price,
                "total_idr"=>$total_idr,
                "total_cny"=>$total_cny,
                "total_twd"=>$total_twd,
                "bank_id"=>$currency,
            ]);
            $invoice->save();
        }
        // SEND EMAIL CONTRACT AND INVOICE
        $data = [
            "title"=>"Confirmation Order - ".$orderWedding->orderno,
            "agent_email"=>$agent->email,
            "order_id"=>$orderWedding->id,
            "reservation_id"=>$reservation->id,
            "invoice_id"=>$invoice->id,
            "orderWedding"=>$orderWedding,
        ];
        
        if (File::exists("storage/document/invoice-".$inv_no."-".$orderWedding->id."_en.pdf")) {
            File::delete("storage/document/invoice-".$inv_no."-".$orderWedding->id."_en.pdf");
        }
        $pdf = PDF::loadView('emails.orderContractWeddingEn', $data);
        $pdf->save("storage/document/invoice-".$inv_no."-".$orderWedding->id."_en.pdf");

        if (File::exists("storage/document/invoice-".$inv_no."-".$orderWedding->id."_zh.pdf")) {
            File::delete("storage/document/invoice-".$inv_no."-".$orderWedding->id."_zh.pdf");
        }
        $pdf = PDF::loadView('emails.orderContractWeddingZh', $data);
        $pdf->save("storage/document/invoice-".$inv_no."-".$orderWedding->id."_zh.pdf");
        

        if (config('filesystems.default') == 'public'){
            $contract_en_path =realpath("storage/document/invoice-".$inv_no."-".$orderWedding->id."_en.pdf");
            $contract_zh_path =realpath("storage/document/invoice-".$inv_no."-".$orderWedding->id."_zh.pdf");
        }else {
            $contract_en_path = storage::url("storage/document/invoice-".$inv_no."-".$orderWedding->id."_en.pdf");
            $contract_zh_path = storage::url("storage/document/invoice-".$inv_no."-".$orderWedding->id."_zh.pdf");
        }
        Mail::send('emails.weddingConfirmationOrder', $data, function($message)use($data, $contract_en_path, $contract_zh_path) {
            $message->to($data["agent_email"])
                ->subject($data["title"])
                ->attach($contract_en_path)
                ->attach($contract_zh_path);
        });

        // UPDATE RESERVATION AFTER SEND EMAIL
        $reservation->update([
            "status"=>"Active",
            "send"=>"yes",
        ]);

    }

    public function print_contract_wedding(Request $request ,$id){
        $order = OrderWedding::findOrFail($id);
        $order_wedding = OrderWedding::findOrFail($id);
        $wedding = Weddings::where('id',$order_wedding->service_id)->first();
        $now = Carbon::now();
        $agent = Auth::user()->where('id',$order->agent_id)->first();
        $usdrates = UsdRates::where('name','USD')->first();
        $cnyrates = UsdRates::where('name','CNY')->first();
        $twdrates = UsdRates::where('name','TWD')->first();
        $tax = Tax::where('id',1)->first();
        $attentions = Attention::where('page','user-order-detail')->get();
        $business = BusinessProfile::where('id',1)->first();
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $bride = Brides::where('id',$order->brides_id)->first();
        $transports = Transports::all();
        $transport_id = json_decode($order_wedding->transport_id);
        $transport_type = json_decode($order_wedding->transport_type);
        $logo = public_path('images/logo-color-bali-kami.png');
        $other_service_ids = json_decode($order_wedding->additional_services);
        $other_services = VendorPackage::where('type','Other')->get(); 
        $wedding_accommodations = WeddingAccomodations::where('order_wedding_package_id',$order_wedding->id)->get();
        $additional_transports = WeddingPlannerTransport::where('order_wedding_id',$order_wedding->id)->get();
        $additional_charges = WeddingAdditionalServices::where('order_wedding_id',$order_wedding->id)->where('status','Approved')->get();
        $flights = Flights::where('order_wedding_id',$order_wedding->id)->get();
        $invoice = $reservation->invoice;
        $bankAccounts = BankAccount::all();
        $bank_account = $invoice?BankAccount::where('id',$invoice->bank_id)->first():NULL;
        $additional_services= AdditionalService::where('rsv_id',$reservation->id)->get();
        $transactions_usd = Transactions::where('invoice_id',$invoice->id)->where('kurs',"USD")->where('status',"Active")->get();
        $transactions_cny = Transactions::where('invoice_id',$invoice->id)->where('kurs',"CNY")->where('status',"Active")->get();
        $transactions_twd = Transactions::where('invoice_id',$invoice->id)->where('kurs',"TWD")->where('status',"Active")->get();
        $transactions_idr = Transactions::where('invoice_id',$invoice->id)->where('kurs',"IDR")->where('status',"Active")->get();
        $total_deposit_usd = $transactions_usd->sum('amount');
        $total_deposit_cny = $transactions_cny->sum('amount');
        $total_deposit_twd = $transactions_twd->sum('amount');
        $total_deposit_idr = $transactions_idr->sum('amount');
        $balance_usd = $invoice->total_usd - $total_deposit_usd;
        $balance_cny = $invoice->total_cny - $total_deposit_cny;
        $balance_twd = $invoice->total_twd - $total_deposit_twd;
        $balance_idr = $invoice->total_idr - $total_deposit_idr;
        
        return view('emails.orderContractWeddingEn',[
            "business"=>$business,
            "attentions"=>$attentions,
            "tax"=>$tax,
            "twdrates"=>$twdrates,
            "cnyrates"=>$cnyrates,
            "usdrates"=>$usdrates,
            "now"=>$now,
            "order"=>$order,
            "reservation"=>$reservation,
            "agent"=>$agent,
            "bride"=>$bride,
            "order_wedding"=>$order_wedding,
            "logo"=>$logo,
            "wedding"=>$wedding,
            "transports"=>$transports,
            "transport_id"=>$transport_id,
            "transport_type"=>$transport_type,
            "other_service_ids"=>$other_service_ids,
            "other_services"=>$other_services,
            "wedding_accommodations"=>$wedding_accommodations,
            "additional_transports"=>$additional_transports,
            "additional_charges"=>$additional_charges,
            "flights"=>$flights,
            "invoice"=>$invoice,
            "bankAccounts"=>$bankAccounts,
            "bank_account"=>$bank_account,
            "additional_services"=>$additional_services,
            "transactions_usd"=>$transactions_usd,
            "transactions_cny"=>$transactions_cny,
            "transactions_twd"=>$transactions_twd,
            "transactions_idr"=>$transactions_idr,
            "balance_usd"=>$balance_usd,
            "balance_cny"=>$balance_cny,
            "balance_twd"=>$balance_twd,
            "balance_idr"=>$balance_idr,
        ]);
    }
    public function zh_print_contract_wedding(Request $request ,$id){
        $order = OrderWedding::findOrFail($id);
        $order_wedding = OrderWedding::findOrFail($id);
        $wedding = Weddings::where('id',$order_wedding->service_id)->first();
        $now = Carbon::now();
        $agent = Auth::user()->where('id',$order->agent_id)->first();
        $usdrates = UsdRates::where('name','USD')->first();
        $cnyrates = UsdRates::where('name','CNY')->first();
        $twdrates = UsdRates::where('name','TWD')->first();
        $tax = Tax::where('id',1)->first();
        $attentions = Attention::where('page','user-order-detail')->get();
        $business = BusinessProfile::where('id',1)->first();
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $bride = Brides::where('id',$order->brides_id)->first();
        $transports = Transports::all();
        $transport_id = json_decode($order_wedding->transport_id);
        $transport_type = json_decode($order_wedding->transport_type);
        $logo = public_path('images/logo-color-bali-kami.png');
        $other_service_ids = json_decode($order_wedding->additional_services);
        $other_services = VendorPackage::where('type','Other')->get(); 
        $wedding_accommodations = WeddingAccomodations::where('order_wedding_package_id',$order_wedding->id)->get();
        $additional_transports = WeddingPlannerTransport::where('order_wedding_id',$order_wedding->id)->get();
        $additional_charges = WeddingAdditionalServices::where('order_wedding_id',$order_wedding->id)->where('status','Approved')->get();
        $flights = Flights::where('order_wedding_id',$order_wedding->id)->get();
        $invoice = $reservation->invoice;
        $bankAccounts = BankAccount::all();
        $bank_account = $invoice?BankAccount::where('id',$invoice->bank_id)->first():NULL;
        $additional_services= AdditionalService::where('rsv_id',$reservation->id)->get();
        $transactions_usd = Transactions::where('invoice_id',$invoice->id)->where('kurs',"USD")->where('status',"Active")->get();
        $transactions_cny = Transactions::where('invoice_id',$invoice->id)->where('kurs',"CNY")->where('status',"Active")->get();
        $transactions_twd = Transactions::where('invoice_id',$invoice->id)->where('kurs',"TWD")->where('status',"Active")->get();
        $transactions_idr = Transactions::where('invoice_id',$invoice->id)->where('kurs',"IDR")->where('status',"Active")->get();
        $total_deposit_usd = $transactions_usd->sum('amount');
        $total_deposit_cny = $transactions_cny->sum('amount');
        $total_deposit_twd = $transactions_twd->sum('amount');
        $total_deposit_idr = $transactions_idr->sum('amount');
        $balance_usd = $invoice->total_usd - $total_deposit_usd;
        $balance_cny = $invoice->total_cny - $total_deposit_cny;
        $balance_twd = $invoice->total_twd - $total_deposit_twd;
        $balance_idr = $invoice->total_idr - $total_deposit_idr;
        
        return view('emails.orderContractWeddingZh',[
            "business"=>$business,
            "attentions"=>$attentions,
            "tax"=>$tax,
            "twdrates"=>$twdrates,
            "cnyrates"=>$cnyrates,
            "usdrates"=>$usdrates,
            "now"=>$now,
            "order"=>$order,
            "reservation"=>$reservation,
            "agent"=>$agent,
            "bride"=>$bride,
            "order_wedding"=>$order_wedding,
            "logo"=>$logo,
            "wedding"=>$wedding,
            "transports"=>$transports,
            "transport_id"=>$transport_id,
            "transport_type"=>$transport_type,
            "other_service_ids"=>$other_service_ids,
            "other_services"=>$other_services,
            "wedding_accommodations"=>$wedding_accommodations,
            "additional_transports"=>$additional_transports,
            "additional_charges"=>$additional_charges,
            "flights"=>$flights,
            "invoice"=>$invoice,
            "bankAccounts"=>$bankAccounts,
            "bank_account"=>$bank_account,
            "additional_services"=>$additional_services,
            "transactions_usd"=>$transactions_usd,
            "transactions_cny"=>$transactions_cny,
            "transactions_twd"=>$transactions_twd,
            "transactions_idr"=>$transactions_idr,
            "balance_usd"=>$balance_usd,
            "balance_cny"=>$balance_cny,
            "balance_twd"=>$balance_twd,
            "balance_idr"=>$balance_idr,
        ]);
    }
    public function en_print_contract_wedding(Request $request ,$id){
        $order = OrderWedding::findOrFail($id);
        $order_wedding = OrderWedding::findOrFail($id);
        $wedding = Weddings::where('id',$order_wedding->service_id)->first();
        $now = Carbon::now();
        $agent = Auth::user()->where('id',$order->agent_id)->first();
        $usdrates = UsdRates::where('name','USD')->first();
        $cnyrates = UsdRates::where('name','CNY')->first();
        $twdrates = UsdRates::where('name','TWD')->first();
        $tax = Tax::where('id',1)->first();
        $attentions = Attention::where('page','user-order-detail')->get();
        $business = BusinessProfile::where('id',1)->first();
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $bride = Brides::where('id',$order->brides_id)->first();
        $transports = Transports::all();
        $transport_id = $order_wedding->transport_id;
        $transport_type = "Airport Shuttle";
        $logo = public_path('images/logo-color-bali-kami.png');
        $other_service_ids = json_decode($order_wedding->additional_services);
        $other_services = VendorPackage::where('status','Active')->get(); 
        $wedding_accommodations = WeddingAccomodations::where('order_wedding_package_id',$order_wedding->id)->get();
        $additional_transports = WeddingPlannerTransport::where('order_wedding_id',$order_wedding->id)->get();
        $additional_charges = WeddingAdditionalServices::where('order_wedding_id',$order_wedding->id)->where('status','Approved')->get();
        $flights = Flights::where('order_wedding_id',$order_wedding->id)->get();
        $invoice = $reservation->invoice;
        $bankAccounts = BankAccount::all();
        $bank_account = $invoice?BankAccount::where('id',$invoice->bank_id)->first():NULL;
        $additional_services= AdditionalService::where('rsv_id',$reservation->id)->get();
        $transactions_usd = Transactions::where('invoice_id',$invoice->id)->where('kurs',"USD")->where('status',"Active")->get();
        $transactions_cny = Transactions::where('invoice_id',$invoice->id)->where('kurs',"CNY")->where('status',"Active")->get();
        $transactions_twd = Transactions::where('invoice_id',$invoice->id)->where('kurs',"TWD")->where('status',"Active")->get();
        $transactions_idr = Transactions::where('invoice_id',$invoice->id)->where('kurs',"IDR")->where('status',"Active")->get();
        $total_deposit_usd = $transactions_usd->sum('amount');
        $total_deposit_cny = $transactions_cny->sum('amount');
        $total_deposit_twd = $transactions_twd->sum('amount');
        $total_deposit_idr = $transactions_idr->sum('amount');
        $balance_usd = $invoice->total_usd - $total_deposit_usd;
        $balance_cny = $invoice->total_cny - $total_deposit_cny;
        $balance_twd = $invoice->total_twd - $total_deposit_twd;
        $balance_idr = $invoice->total_idr - $total_deposit_idr;
        
        return view('emails.orderContractWeddingEn',[
            "business"=>$business,
            "attentions"=>$attentions,
            "tax"=>$tax,
            "twdrates"=>$twdrates,
            "cnyrates"=>$cnyrates,
            "usdrates"=>$usdrates,
            "now"=>$now,
            "order"=>$order,
            "reservation"=>$reservation,
            "agent"=>$agent,
            "bride"=>$bride,
            "order_wedding"=>$order_wedding,
            "logo"=>$logo,
            "wedding"=>$wedding,
            "transports"=>$transports,
            "transport_id"=>$transport_id,
            "transport_type"=>$transport_type,
            "other_service_ids"=>$other_service_ids,
            "other_services"=>$other_services,
            "wedding_accommodations"=>$wedding_accommodations,
            "additional_transports"=>$additional_transports,
            "additional_charges"=>$additional_charges,
            "flights"=>$flights,
            "invoice"=>$invoice,
            "bankAccounts"=>$bankAccounts,
            "bank_account"=>$bank_account,
            "additional_services"=>$additional_services,
            "transactions_usd"=>$transactions_usd,
            "transactions_cny"=>$transactions_cny,
            "transactions_twd"=>$transactions_twd,
            "transactions_idr"=>$transactions_idr,
            "balance_usd"=>$balance_usd,
            "balance_cny"=>$balance_cny,
            "balance_twd"=>$balance_twd,
            "balance_idr"=>$balance_idr,
        ]);
    }

    public function view_contract_wedding_eng(Request $request ,$id){
        $order = OrderWedding::findOrFail($id);
        $order_wedding = OrderWedding::findOrFail($id);
        $wedding = Weddings::where('id',$order_wedding->service_id)->first();
        $now = Carbon::now();
        $agent = Auth::user()->where('id',$order->agent_id)->first();
        $usdrates = UsdRates::where('name','USD')->first();
        $cnyrates = UsdRates::where('name','CNY')->first();
        $twdrates = UsdRates::where('name','TWD')->first();
        $tax = Tax::where('id',1)->first();
        $attentions = Attention::where('page','user-order-detail')->get();
        $business = BusinessProfile::where('id',1)->first();
        $reservation = Reservation::where('id',$order->rsv_id)->first();
        $bride = Brides::where('id',$order->brides_id)->first();
        $transports = Transports::all();
        $transport_id = json_decode($order_wedding->transport_id);
        $transport_type = json_decode($order_wedding->transport_type);
        $logo = public_path('images/logo-color-bali-kami.png');
        $other_service_ids = json_decode($order_wedding->additional_services);
        $other_services = VendorPackage::where('type','Other')->get(); 
        $wedding_accommodations = WeddingAccomodations::where('order_wedding_package_id',$order_wedding->id)->get();
        $additional_transports = WeddingPlannerTransport::where('order_wedding_id',$order_wedding->id)->get();
        $additional_charges = WeddingAdditionalServices::where('order_wedding_id',$order_wedding->id)->where('status','Approved')->get();
        $flights = Flights::where('order_wedding_id',$order_wedding->id)->get();
        $invoice = $reservation->invoice;
        $bankAccounts = BankAccount::all();
        $bank_account = $invoice?BankAccount::where('id',$invoice->bank_id)->first():NULL;
        $additional_services= AdditionalService::where('rsv_id',$reservation->id)->get();
        $transactions_usd = Transactions::where('invoice_id',$invoice->id)->where('kurs',"USD")->where('status',"Active")->get();
        $transactions_cny = Transactions::where('invoice_id',$invoice->id)->where('kurs',"CNY")->where('status',"Active")->get();
        $transactions_twd = Transactions::where('invoice_id',$invoice->id)->where('kurs',"TWD")->where('status',"Active")->get();
        $transactions_idr = Transactions::where('invoice_id',$invoice->id)->where('kurs',"IDR")->where('status',"Active")->get();
        $total_deposit_usd = $transactions_usd->sum('amount');
        $total_deposit_cny = $transactions_cny->sum('amount');
        $total_deposit_twd = $transactions_twd->sum('amount');
        $total_deposit_idr = $transactions_idr->sum('amount');
        $balance_usd = $invoice->total_usd - $total_deposit_usd;
        $balance_cny = $invoice->total_cny - $total_deposit_cny;
        $balance_twd = $invoice->total_twd - $total_deposit_twd;
        $balance_idr = $invoice->total_idr - $total_deposit_idr;
        
        return view('emails.orderContractWeddingEn',[
            "business"=>$business,
            "attentions"=>$attentions,
            "tax"=>$tax,
            "twdrates"=>$twdrates,
            "cnyrates"=>$cnyrates,
            "usdrates"=>$usdrates,
            "now"=>$now,
            "order"=>$order,
            "reservation"=>$reservation,
            "agent"=>$agent,
            "bride"=>$bride,
            "order_wedding"=>$order_wedding,
            "logo"=>$logo,
            "wedding"=>$wedding,
            "transports"=>$transports,
            "transport_id"=>$transport_id,
            "transport_type"=>$transport_type,
            "other_service_ids"=>$other_service_ids,
            "other_services"=>$other_services,
            "wedding_accommodations"=>$wedding_accommodations,
            "additional_transports"=>$additional_transports,
            "additional_charges"=>$additional_charges,
            "flights"=>$flights,
            "invoice"=>$invoice,
            "bankAccounts"=>$bankAccounts,
            "bank_account"=>$bank_account,
            "additional_services"=>$additional_services,
            "transactions_usd"=>$transactions_usd,
            "transactions_cny"=>$transactions_cny,
            "transactions_twd"=>$transactions_twd,
            "transactions_idr"=>$transactions_idr,
            "balance_usd"=>$balance_usd,
            "balance_cny"=>$balance_cny,
            "balance_twd"=>$balance_twd,
            "balance_idr"=>$balance_idr,
        ]);
    }
    public function test_email_confirmation(Request $request ,$id){
        
        $order = Orders::findOrFail($id);
        $admin = User::find($order->handled_by);
        $order_link = 'https://online.balikamitour.com/detail-order-'.$order->id;
        return view('emails.confirmationOrder',[
            'order'=>$order,
            'admin'=>$admin,
            'order_link'=>$order_link,
        ]);
    }



    // public function Exampel_func_confirm_order_wedding(Request $request,$id){
        //     $order=Orders::where('id',$id)->first();
        //     $agent = User::where('id', $order->sales_agent)->first();
        //     $hotels=Hotels::all();
        //     $user_id = Auth::User()->id;
        //     $usdrates = UsdRates::where('name','USD')->first();
        //     $cnyrates = UsdRates::where('name','CNY')->first();
        //     $twdrates = UsdRates::where('name','TWD')->first();
        //     $idrrates = UsdRates::where('name','IDR')->first();
        //     $tax = Tax::where('id',1)->first();
        //     $attentions = Attention::where('page','user-order-detail')->get();
        //     $business = BusinessProfile::where('id',1)->first();
        //     $optionalrates = OptionalRate::with('hotels')->get();
        //     $optionalrate_meals = OptionalRate::with('hotels')->where('type',"Meals")->get();
        //     $optional_rate_orders = OptionalRateOrder::where('orders_id', $id)->first();
        //     $extra_beds = ExtraBed::all();
        //     $now = Carbon::now();
        //     $reservation = Reservation::where('id',$order->rsv_id)->first();
        //     $orderno = $order->orderno;
        //     if ($order->service == "Wedding Package") {
        //         $status = "Active";
        //     }else{
        //         $status = "Approved";
        //     }
        //     $email = $order->email;
        //     $bank_account = BankAccount::where('id',1)->first();
        //     if (!$order->handled_by) {
        //         $handled_by = Auth::user()->id;
        //     }else{
        //         $handled_by = $order->handled_by;
        //     }
        //     if (isset($bank_account)) {
        //         $bank = $bank_account->id;
        //     }else {
        //         $bank = null;
        //     }
        //     if ($order->service == "Tour Package"){
        //         $amount = $order->price_total;
        //         if($order->duration == "1D"){
        //             $order_duration = 1;
        //         }elseif($order->duration == "2D/1N"){
        //             $order_duration = 1;
        //         }elseif($order->duration == "3D/2N"){
        //             $order_duration = 2;
        //         }elseif($order->duration == "4D/3N"){
        //             $order_duration = 3;
        //         }elseif($order->duration == "5D/4N"){
        //             $order_duration = 4;
        //         }elseif($order->duration == "6D/5N"){
        //             $order_duration = 5;
        //         }else{
        //             $order_duration = 1;
        //         }
        //     }elseif($order->servise == "Activity"){
        //         $amount = $order->price_total;
        //         $order_duration = $order->duration;
        //     }else{
        //         $amount = (($order->price_pax * $order->duration)*$order->number_of_room);
        //         $order_duration = $order->duration;
        //     }
        //     $pdsc = json_decode($order->promotion_disc);

        //     $ebp = json_decode($order->extra_bed_price);
        //     if(isset($ebp)){
        //         $extrabed_price = array_sum($ebp);
        //         $jml_extra_bed = 0;
        //         $cebp = count($ebp);
        //         for ($i=0; $i < $cebp; $i++) { 
        //             if ($ebp[$i]>0) {
        //                 $jml_extra_bed = $jml_extra_bed + 1;
        //             }
        //         }
        //     }else{
        //         $cebp = 0;
        //         $jml_extra_bed = 0;
        //         $extrabed_price = 0;
        //     }
            

        //     if (isset($optional_rate_orders->optional_rate_id)){
        //         $opsirate_order_date = json_decode($optional_rate_orders->service_date);
        //         $opsirate_order_nog = json_decode($optional_rate_orders->number_of_guest);
        //         $opsirate_order_id = json_decode($optional_rate_orders->optional_rate_id);
        //         $opsirate_order_price_pax = json_decode($optional_rate_orders->price_pax);
        //         $opsirate_order_price_total = json_decode($optional_rate_orders->price_total);
        //     }else{
        //         $opsirate_order_date = null;
        //         $opsirate_order_nog = null;
        //         $opsirate_order_id = null;
        //         $opsirate_order_price_pax = null;
        //         $opsirate_order_price_total = null;
        //     }
        //     if (isset($pdsc)) {
        //         $promotion_disc = array_sum($pdsc);
        //     }else{
        //         $promotion_disc = 0;
        //     }
            
        //     $special_day = json_decode($order->special_day);
        //     $special_date = json_decode($order->special_date);
        //     $extra_bed = json_decode($order->extra_bed);
        //     $extra_bed_id = json_decode($order->extra_bed_id);
        //     $extra_bed_price = json_decode($order->extra_bed_price);
        //     $nor = $order->number_of_room;
        //     $order_link = 'https://online.balikamitour.com/detail-order-'.$order->id;
        //     $guest_name = Guests::where('rsv_id',$order->rsv_id)->get();
        //     $nor = $order->number_of_room;
        //     $pickup_people = Guests::where('id',$order->pickup_name)->first();
        //     $guide = Guide::where('id',$order->guide_id)->first();
        //     $driver = Drivers::where('id',$order->driver_id)->first();
        //     if (isset($extra_bed)) {
        //         $cextra_bed = count($extra_bed);
        //     }else {
        //         $cextra_bed = 0;
        //     }

        //     if ($order->service == "Wedding Package") {
        //         $bride = Brides::where('id',$order->pickup_name)->first();
        //         $order_wedding = OrderWedding::where('id',$order->wedding_order_id)->first();
        //         $rooms = HotelRoom::where('hotels_id',$order->subservice_id)->get();
        //         $weddingVenues = VendorPackage::where('type',"Wedding Venue")->get();
        //         $weddingMakeups = VendorPackage::where('type',"Make-up")->get();
        //         $weddingDecorations = VendorPackage::where('type',"Decoration")->get();
        //         $weddingDinnerVenues = VendorPackage::where('type',"Wedding Dinner")->get();
        //         $weddingEntertainments = VendorPackage::where('type',"Entertainment")->get();
        //         $weddingDocumentations = VendorPackage::where('type',"Documentation")->get();
        //         $weddingTransportations = Transports::all();
        //         $weddingOthers = VendorPackage::where('type',"Other")->get();
        //         $weddingFixedServices = VendorPackage::where('type',"Fixed Service")->get();
        //         $wedding_hotel = Hotels::where('id',$order_wedding->wedding_id)->first();
        //         $wedding_itineraries = Itinerary::where('order_id',$order->id)->orderBy('time','ASC')->get();
        //     }else{
        //         $bride = null;
        //         $order_wedding = null;
        //         $rooms = null;
        //         $weddingVenues = null;
        //         $weddingMakeups = null;
        //         $weddingDecorations = null;
        //         $weddingDinnerVenues = null;
        //         $weddingEntertainments = null;
        //         $weddingDocumentations = null;
        //         $weddingTransportations = null;
        //         $weddingOthers = null;
        //         $weddingFixedServices = null;
        
        //         $wedding_hotel = null;
        //         $wedding_itineraries = null;
        //     }

        //     $invoice = InvoiceAdmin::where('rsv_id',$reservation->id)->first();
        //     $inv_no = "INV-".$reservation->rsv_no;
        //     $due_date = date('Y-m-d', strtotime("-7 days", strtotime($order->checkin)));
        //     $total_idr = $order->final_price * $usdrates->rate;
        //     $total_cny = ceil($total_idr / $cnyrates->rate);
        //     $total_twd = ceil($total_idr / $twdrates->rate);
        //     $currency = $request->currency;
        //     if ($invoice) {
        //         $invoice->update([
        //             "inv_no"=>$inv_no,
        //             "rsv_id"=>$reservation->id,
        //             "inv_date"=>$now,
        //             "due_date"=>$due_date,
        //             "total_usd"=>$order->final_price,
        //             "total_idr"=>$total_idr,
        //             "total_cny"=>$total_cny,
        //             "total_twd"=>$total_twd,
        //             "bank_id"=>$currency,
        //         ]);
        //     }else{
        //         $invoice = new InvoiceAdmin([
        //             "inv_no"=>$inv_no,
        //             "rsv_id"=>$reservation->id,
        //             "inv_date"=>$now,
        //             "due_date"=>$due_date,
        //             "total_usd"=>$order->final_price,
        //             "total_idr"=>$total_idr,
        //             "total_cny"=>$total_cny,
        //             "total_twd"=>$total_twd,
        //             "bank_id"=>$currency,
        //         ]);
        //         $invoice->save();
        //     }
            
        //     $airport_shuttles = AirportShuttle::where('order_id',$id)->get();

        //     $order->update([
        //         "status"=>$status,
        //         "verified_by"=>Auth::user()->id,
        //         "handled_by"=>$handled_by,
        //     ]);
        //     $order_log =new OrderLog([
        //         "order_id"=>$order->id,
        //         "action"=>"Confirm Order",
        //         "url"=>$request->getClientIp(),
        //         "method"=>"Confirm",
        //         "agent"=>$order->name,
        //         "admin"=>Auth::user()->id,
        //     ]);
        //     $order_log->save();
        //     $reservation->update([
        //         "status"=>"Active",
        //         "send"=>"yes",
        //     ]);
        //     $admin = Auth::user()->where('id',$order->verified_by)->first();
        //     $bankAccount = BankAccount::where("id",$invoice->bank_id)->first();
        //     $data = [
        //         'now'=>$now,
        //         'title'=>"Confirmation Order - ".$order->orderno,
        //         'email'=>$email,
        //         'agent'=>$agent,
        //         'admin'=>$admin,
        //         'order_link'=>$order_link,
        //         'extra_beds'=>$extra_beds,
        //         'order'=>$order,
        //         'tax'=>$tax,
        //         'optionalrates'=>$optionalrates,
        //         'usdrates'=>$usdrates,
        //         'cnyrates'=>$cnyrates,
        //         'twdrates'=>$twdrates,
        //         'idrrates'=>$idrrates,
        //         'business'=>$business,
        //         'optional_rate_orders'=>$optional_rate_orders,
        //         'attentions'=>$attentions,
        //         'optionalrate_meals'=>$optionalrate_meals,
        //         'logoImage'=> public_path('storage/logo/bali-kami-tour-logo.png'),
        //         'guest_name'=>$guest_name,
        //         'hotels'=>$hotels,
        //         'special_day'=>$special_day,
        //         'special_date'=>$special_date,
        //         'extra_bed'=>$extra_bed,
        //         'extra_bed_id'=>$extra_bed_id,
        //         'extra_bed_price'=>$extra_bed_price,
        //         'nor'=>$nor,
        //         'pickup_people'=>$pickup_people,
        //         'guide'=>$guide,
        //         'driver'=>$driver,
        //         'reservation'=>$reservation,
        //         'invoice'=>$invoice,
        //         'amount'=>$amount,
        //         'jml_extra_bed'=>$jml_extra_bed,
        //         'extrabed_price'=>$extrabed_price,
        //         'cebp'=>$cebp,
        //         'opsirate_order_date'=>$opsirate_order_date,
        //         'opsirate_order_nog'=>$opsirate_order_nog,
        //         'opsirate_order_id'=>$opsirate_order_id,
        //         'opsirate_order_price_pax'=>$opsirate_order_price_pax,
        //         'opsirate_order_price_total'=>$opsirate_order_price_total,
        //         'promotion_disc'=>$promotion_disc,
        //         'bankAccount'=>$bankAccount,
        //         'order_duration'=>$order_duration,
        //         'arrival_flight' =>$order->arrival_flight,
        //         'arrival_time' =>$order->arrival_time,
        //         'departure_flight'=>$order->departure_flight,
        //         'departure_time'=>$order->departure_time,
        //         'airport_shuttles'=>$airport_shuttles,
        //         'order_wedding'=>$order_wedding,
        //         'rooms'=>$rooms,
        //         'wedding_itineraries'=>$wedding_itineraries,
        //         'wedding_hotel'=>$wedding_hotel,
        //         'weddingVenues'=>$weddingVenues,
        //         'weddingMakeups'=>$weddingMakeups,
        //         'weddingDecorations'=>$weddingDecorations,
        //         'weddingDinnerVenues'=>$weddingDinnerVenues,
        //         'weddingEntertainments'=>$weddingEntertainments,
        //         'weddingDocumentations'=>$weddingDocumentations,
        //         'weddingTransportations'=>$weddingTransportations,
        //         'weddingOthers'=>$weddingOthers,
        //         'weddingFixedServices'=>$weddingFixedServices,
        //         'bride'=>$bride,
                
        //     ];
        //     if ($order->service == "Wedding Package") {
        //         if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf")) {
        //             File::delete("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
        //         }
        //         $pdf = PDF::loadView('emails.orderContractWeddingEn', $data);
        //         $pdf->save("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
        
        //         if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf")) {
        //             File::delete("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        //         }
        //         $pdf = PDF::loadView('emails.orderContractWeddingZh', $data);
        //         $pdf->save("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        //     }else{
        //         if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf")) {
        //             File::delete("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
        //         }
        //         $pdf = PDF::loadView('emails.orderContractEn', $data);
        //         $pdf->save("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
        
        //         if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf")) {
        //             File::delete("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        //         }
        //         $pdf = PDF::loadView('emails.orderContractZh', $data);
        //         $pdf->save("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        //     }

        //     if (config('filesystems.default') == 'public'){
        //         $contract_en_path =realpath("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
        //         $contract_zh_path =realpath("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        //     }else {
        //         $contract_en_path = storage::url("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf");
        //         $contract_zh_path = storage::url("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf");
        //     }
        //     Mail::send('emails.confirmationOrder', $data, function($message)use($data, $contract_en_path, $contract_zh_path) {
        //         $message->to($data["email"])
        //             ->subject($data["title"])
        //             ->attach($contract_en_path)
        //             ->attach($contract_zh_path);
        //     });
        //     return redirect("/orders-admin-$id");
        // }


}
