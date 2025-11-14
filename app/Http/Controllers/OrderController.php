<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\Tax;
use App\Models\User;
use App\Models\Guide;
use App\Models\Tours;
use App\Models\Brides;
use App\Models\Guests;
use App\Models\Hotels;
use App\Models\Orders;
use App\Models\Villas;
use App\Models\Drivers;
use App\Models\LogData;
use App\Models\TaxDoku;
use App\Models\UserLog;

use App\Models\ExtraBed;
use App\Models\OrderLog;
use App\Models\UsdRates;
use App\Models\Weddings;
use App\Models\Attention;
use App\Models\HotelRoom;
use App\Models\Promotion;
use App\Models\HotelPrice;
use App\Models\HotelPromo;
use App\Models\TourPrices;
use App\Models\Transports;

use App\Models\VillaPrice;
use App\Models\BookingCode;
use App\Models\Reservation;
use App\Models\HotelPackage;
use App\Models\InvoiceAdmin;
use App\Models\OptionalRate;
use App\Models\OrderWedding;
use Illuminate\Http\Request;
use App\Mail\ReservationMail;
use App\Models\VendorPackage;
use App\Models\AirportShuttle;
use App\Models\TransportPrice;
use App\Models\BusinessProfile;
use App\Models\AdditionalService;
use App\Models\OptionalRateOrder;
use App\Models\DokuVirtualAccount;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentConfirmation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Notifications\NotifikasiWhatsApp;
use Google\Service\ShoppingContent\Resource\Promotions;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    // VIEW ORDERS ================================================================================================> OK
    public function index()
    {   
        $ord = Orders::all();
        $orderno = count($ord);
        $business = BusinessProfile::where('id','=',1)->first();
        $now = Carbon::now();
        $archived = date('Y-m-d',strtotime('+7 days',strtotime($now)));
        $userid = Auth::user()->id;
        $attentions = Attention::where('page','orders')->get();
        $optional_rate_order = OptionalRateOrder::all();
        $optionalrates = OptionalRate::all();
        $wedding_order = OrderWedding::all();
        $brides = Brides::all();
        $tourorders = Orders::where('service','Tour Package')
            ->where('sales_agent','=', $userid)
            ->where('checkin', '>=' , $now)
            ->whereNotIn('status', ['Removed', 'Archive'])
            ->orderBy('updated_at','DESC')
            ->get();
        $hotelorders = Orders::whereIn('service', ['Hotel', 'Hotel Promo', 'Hotel Package'])
            ->whereNotIn('status', ['Removed', 'Archive'])
            ->where('sales_agent', $userid)
            ->where('checkin', '>=', $now)
            ->orderBy('updated_at', 'DESC')
            ->get();
        $activityorders = Orders::where('service','Activity')
            ->where('sales_agent','=', $userid)
            ->where('checkin', '>=' , $now)
            ->whereNotIn('status', ['Removed', 'Archive'])
            ->orderBy('updated_at','DESC')
            ->get();
        $transportorders = Orders::where('service','Transport')
            ->where('sales_agent','=', $userid)
            ->where('checkin', '>=' , $now)
            ->whereNotIn('status', ['Removed', 'Archive'])
            ->orderBy('updated_at','DESC')
            ->get();
        $villaorders = Orders::where('service', 'Private Villa')
            ->where('sales_agent', $userid)
            ->where('checkin', '>=', $now)
            ->whereNotIn('status', ['Removed', 'Archive'])
            ->orderBy('updated_at', 'DESC')
            ->get();
        $orderhistories = Orders::with(['reservations'])
            ->where('sales_agent', $userid)
            ->where('checkin', '<', $now)
            ->whereNotIn('status', ['Removed', 'Archive'])
            ->orderBy('updated_at', 'DESC')
            ->get();

        $weddingorders = OrderWedding::where('agent_id', $userid)
            ->orderBy('updated_at','DESC')
            ->get();
        $vendorPackages = VendorPackage::all();
        $hotelRoo = HotelRoom::all();


        $orders = Orders::where('sales_agent','=', $userid)
            ->whereNotIn('status', ['Removed', 'Archive'])
            ->where('checkin', '>=' , $now)
            ->orderBy('updated_at','DESC')
            ->get();
        
        if (isset($orders->checkin) == "")
            $checkin = $now;
        else
            $checkin = $orders->checkin;

        $activeorders = Orders::where('status','!=', 'Accepted')
            ->where('status','!=', 'Draft')
            ->where('sales_agent','=', $userid)
            ->where('checkin','>', $archived)
            ->orderBy('updated_at', 'DESC')
            ->get();

        $archivedorders = Orders::where('sales_agent','=', $userid)
            ->where('checkin','<', $now)
            ->orderBy('created_at', 'desc')
            ->get();
        $rejectedorders = Orders::where('sales_agent','=', $userid)
            ->where('status', 'Rejected')
            ->orderBy('created_at', 'desc')
            ->get();
        $reservations = Reservation::all();
        
        $statusMap = [
            'Rejected'  => ['class' => 'status-rejected', 'label' => ''],
            'Paid'      => ['class' => 'status-paid', 'label' => __('messages.Paid')],
            'Approved'  => ['class' => 'status-approved', 'label' => __('messages.Approved')],
            'Confirmed' => ['class' => 'status-confirmed', 'label' => __('messages.Confirmed')],
            'Canceled'  => ['class' => 'status-canceled', 'label' => __('messages.Canceled')],
            'Rejected'  => ['class' => 'status-rejected', 'label' => __('messages.Rejected')],
            'Invalid'   => ['class' => 'status-invalid', 'label' => __('messages.Invalid')],
            'Active'    => ['class' => 'status-progress', 'label' => __('messages.Active')],
            'Pending'   => ['class' => 'status-waiting', 'label' => __('messages.Pending')],
            'Draft'     => ['class' => 'status-draft', 'label' => __('messages.Draft')],
        ];
        return view('main.order',compact('orders'),[
            'orderno'=>$orderno,
            'optionalrates'=>$optionalrates,
            'optional_rate_order'=>$optional_rate_order,
            'attentions'=>$attentions,
            'archivedorders'=>$archivedorders,
            'rejectedorders'=>$rejectedorders,
            'business'=>$business,
            'now'=>$now,
            "checkin"=> $checkin,
            'orders'=> $orders,
            "activeorders"=>$activeorders,
            'weddingorders'=>$weddingorders,
            'vendorPackages'=>$vendorPackages,
            'transportorders'=>$transportorders,
            'villaorders'=>$villaorders,
            'orderhistories'=>$orderhistories,
            'activityorders'=>$activityorders,
            'hotelorders'=>$hotelorders,
            'tourorders'=>$tourorders,
            'reservations'=>$reservations,
            'wedding_order'=>$wedding_order,
            'brides'=>$brides,
            'userid'=>$userid,
            'statusMap'=>$statusMap,
        ]);
        
        
    }
    
    // VIEW USER ORDER HOTEL PROMO =================================================================================> OK
    public function order_hotel_promo(Request $request, $id)
    {
        $user_id = Auth::id();
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = UsdRates::where('name', 'USD')->first();
        $business = BusinessProfile::find(1);
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));
        $attentions = Attention::where('page', 'order-hotel-promo')->get();
        $room = HotelRoom::with('hotels')->findOrFail($id);
        $hotel = $room->hotels;
        if (!session()->has('booking_dates.checkin')) {
            return redirect("/hotel-$hotel->code");
        }
        $checkin = session('booking_dates.checkin');
        $checkout = session('booking_dates.checkout');
        $service = "Hotel Promo";
        $order_value = Orders::count();
        $orderNumber = "HPP" . date('ymd', strtotime($now)) . "-" . $order_value;
        $duration = Carbon::parse($checkin)->diffInDays(Carbon::parse($checkout));
        $room_capacity = $room->capacity_adult + $room->capacity_child;
        $prIds = $request->promo_id;
        $uniqueHotelPromoIds = array_unique(json_decode($request->promo_id));
        $promos = HotelPromo::whereIn('id', $uniqueHotelPromoIds)->get();
        $promo_name = $promos->pluck('name')->implode(', ');
        $promo_benefits = $promos->pluck('benefits')->implode('<br>');
        $promo_include = $promos->pluck('include')->implode('<br>');
        $promo_additional_info = $promos->pluck('additional_info')->implode('<br>');
        $transports = Transports::with('prices')
            ->select('id', 'name', 'brand', 'capacity')
            ->where('status', "Active")
            ->orderByDesc('capacity')
            ->get();
        $transport_prices = TransportPrice::where('duration','>=', $hotel->airport_duration)->get();
        $promo_price = $request->promo_price;
        $price_list = $request->price_list;
        $final_price = 0;
        $agents = User::where('status', "Active")
            ->whereNotNull('email_verified_at')
            ->where('is_approved', true)
            ->get();

        $optional_rates = OptionalRate::mustBuy($checkin, $checkout)->get();
        $totalPriceOptionalRates = $optional_rates->sum(function ($rate) use ($usdrates, $tax) {
            return $rate->calculatePrice($usdrates, $tax);
        });

        return view('form.order-hotel-promo', compact(
            'now', 'usdrates', 'tax', 'business', 'logoDark', 'altLogo',
            'service', 'orderNumber', 'checkin', 'checkout', 'duration',
            'hotel', 'promos', 'prIds', 'promo_name', 'room', 'room_capacity',
            'promo_benefits', 'promo_include', 'promo_additional_info',
            'transports', 'transport_prices', 'final_price', 'promo_price',
            'uniqueHotelPromoIds', 'price_list', 'agents','optional_rates','totalPriceOptionalRates'
        ));
    }

    // VIEW USER ORDER VILLA =================================================================================> OK
    public function order_villa(Request $request, $code)
    {
        if (!session()->has('booking_dates.checkin')) {
            return redirect("/villas/{$request->villa_id};");
        }
        $checkin = session('booking_dates.checkin');
        $checkout = session('booking_dates.checkout');
        $user_id = Auth::id();
        $user = Auth::user();
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));
        $attentions = Attention::where('page', 'order-villa-promo')->get();
        $duration = session('booking_dates.duration');
        $orders = Orders::where('sales_agent', $user_id)
            ->whereDate('created_at', $now)
            ->get();
        $order_value = $orders->count() + 1;
        function numberToLetters($number) {
            $letters = '';
            while ($number > 0) {
                $remainder = ($number - 1) % 26;
                $letters = chr(65 + $remainder) . $letters;
                $number = intdiv($number - 1, 26);
            }
            return $letters;
        }
        $order_suffix = numberToLetters($order_value);
        $orderNumber = $user->code . date('ymd', strtotime($now)) . $order_suffix;
        $villa = Villas::with([
            'galleries',
            'rooms' => fn($q) => $q->where('status', 1),
            'prices' => fn($q) => $q->where('start_date', '<=', session('booking_dates.checkin'))
                                    ->where('end_date', '>=', session('booking_dates.checkin'))
                                    ->where('status', "Active")
                                    ->first()
        ])->where('code', $code)->firstOrFail();

        $rooms = $villa->rooms;
        $number_of_adult = $rooms->sum('guest_adult');
        $number_of_children = $rooms->sum('guest_child');
        $occupancy = $number_of_adult + $number_of_children;
        $transport_duration = $villa->airport_duration ?? 2;
        $transports = Transports::with(['prices' => function ($q) use ($transport_duration) {
                $q->where('duration',$transport_duration);
            }])
            ->get();

        foreach ($transports as $transport) {
        // Ambil hanya 1 price yang cocok
            $price = $transport->prices->first();

            if ($price) {
                $transport->calculated_price = $price->calculatePrice($usdrates, $tax);
                $transport->calculated_price_id = $price->id;
            } else {
                $transport->calculated_price = null;
                $transport->calculated_price_id = null;
            }
        }
        $agents = User::where('status', "Active")
            ->whereNotNull('email_verified_at')
            ->get();
        $price = $villa->prices()
            ->where('start_date', '<=', session('booking_dates.checkin'))
            ->where('end_date', '>=', session('booking_dates.checkin'))
            ->where('status', 'Active')
            ->first();
        $calculatedPrice = 0;
        $found_price = false;
        for ($date = Carbon::parse($checkin); $date->lt(Carbon::parse($checkout)); $date->addDay()) {
            $price_loop = VillaPrice::where('villa_id', $villa->id)
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->first();

            if ($price_loop) {
                $night_price = $price_loop->calculatePrice($usdrates, $tax);
                $calculatedPrice += $night_price;
                $found_price = true;
            } else {
                $calculatedPrice = 0;
                break;
            }
        }
        $total_price = $calculatedPrice;
        $data = [
            'user' => $user,
            'price' => $price,
            'total_price' => $total_price,
            'transport_duration' => $transport_duration,
            'occupancy' => $occupancy,
            'number_of_adult' => $number_of_adult,
            'number_of_children' => $number_of_children,
            'now' => $now,
            'business' => $business,
            'checkin' => session('booking_dates.checkin'),
            'checkout' => session('booking_dates.checkout'),
            'duration' => session('booking_dates.duration'),
            'villa' => $villa,
            'rooms' => $rooms,
            'transports' => $transports,
            'usdrates' => $usdrates,
            'tax' => $tax,
            'agents' => $agents,
            'orderNumber' => $orderNumber,
            'attentions' => $attentions,
            'logoDark' => $logoDark,
            'altLogo' => $altLogo,
        ];
        return view('villas.order-villa',$data);
    }

    // CREATE ORDER VILLA =================================================================================> OK
    public function func_create_order_villa(Request $request){
        $user = Auth::user();
        $developerRoles = ["developer", "reservation", "author"];
        if (in_array($user->position, $developerRoles)) {
            $sales_agent = $request->user_id;
            $status = "Pending";
        } else {
            $sales_agent = $user->id;
            $status = "Draft";
        }
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $cnyrates = Cache::remember('cny_rate', 3600, fn() => UsdRates::where('name', 'CNY')->first());
        $twdrates = Cache::remember('twd_rate', 3600, fn() => UsdRates::where('name', 'TWD')->first());
        $usd_rate = $usdrates->rate;
        $cny_rate = $cnyrates->rate;
        $twd_rate = $twdrates->rate;
        $checkin = Carbon::parse(session('booking_dates.checkin'))->format('Y-m-d');
        $checkout = Carbon::parse(session('booking_dates.checkout'))->format('Y-m-d');
        $duration = session('booking_dates.duration') ?? Carbon::parse($checkin)->diffInDays(Carbon::parse($checkout));
        $villa = Villas::findOrFail($request->villa_id);
        $villa_price = VillaPrice::findOrFail($request->villa_price_id);
        $price = $villa_price->calculatePrice($usdrates, $tax);
        $calculatedPrice = 0;
        $found_price = false;
        for ($date = Carbon::parse($checkin); $date->lt(Carbon::parse($checkout)); $date->addDay()) {
            $price_loop = VillaPrice::where('villa_id', $villa->id)
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->first();
            if ($price_loop) {
                $night_price = $price_loop->calculatePrice($usdrates, $tax);
                $calculatedPrice += $night_price;
                $found_price = true;
            } else {
                $calculatedPrice = 0;
                break;
            }
        }
        $normal_price = $calculatedPrice;
        $rooms = $villa->rooms;
        $number_of_room = $rooms->count() ?? 1;
        $number_of_adult = $rooms->sum('guest_adult') ?? 0;
        $number_of_children = $rooms->sum('guest_child') ?? 0;
        $occupancy = $number_of_adult + $number_of_children ?? 0;
        $transport_in_id = $request->airport_shuttle_in;
        $transport_out_id = $request->airport_shuttle_out;
        $airport_shuttle_in_price = TransportPrice::find($request->transport_in_price_id) ?? null;
        $airport_shuttle_out_price = TransportPrice::find($request->transport_out_price_id) ?? null;
        $guests = $request->input('guests', []);
        $number_of_guests = count($guests);
        $airport_shuttle_price = $request->airport_shuttle_price;
        $price_total = $normal_price + $airport_shuttle_price;

        $order = Orders::create([
            'user_id' => $user->id,
            'orderno' => $request->orderno,
            'name' => $user->name,
            'email' => $user->email,
            'servicename' => $villa->name,
            'service' => 'Private Villa',
            'service_id' => $villa->id,
            'price_id' => $request->villa_price_id,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'location' => $villa->region,
            'number_of_guests' => $number_of_guests,
            'airport_shuttle_price' => $airport_shuttle_price,
            'capacity' => $occupancy,
            'benefits' => $villa_price->benefits,
            'additional_info' => $villa_price->additional_info,
            'cancellation_policy' => $villa_price->cancellation_policy,
            'note' => $request->note,
            'number_of_room' => $number_of_room,
            'duration' => $duration,
            'price_pax' => $calculatedPrice,
            'normal_price' => $normal_price,
            'price_total' => $price_total,
            'final_price' => $normal_price + $request->airport_shuttle_price,
            'usd_rate' => $usd_rate,
            'twd_rate' => $twd_rate,
            'cny_rate' => $cny_rate,
            'period_start' => $villa_price->start_date,
            'period_end' => $villa_price->end_date,
            'status' => $status,
            'sales_agent' => $sales_agent,
            'arrival_time' => $request->arrival_time,
            'arrival_flight' => $request->arrival_flight,
            'departure_flight' => $request->departure_flight,
            'departure_time' => $request->departure_time,
            'airport_shuttle_in' => $request->airport_shuttle_in,
            'airport_shuttle_out' => $request->airport_shuttle_out,
            'note' => $request->note
        ]);
        
        foreach ($guests as $guestData) {
            if (!empty($guestData['name'])) {
                Guests::create([
                    'name' => $guestData['name'],
                    'sex'  => $guestData['sex'] ?? null,
                    'age'  => $guestData['age'] ?? null,
                    'order_id'  => $order->id,
                ]);
            }
        }
        if ($request->airport_shuttle_in){
            $transport_in = Transports::find($request->airport_shuttle_in) ?? null;
            $arrival_time = date('Y-m-d H:i:s',strtotime($request->arrival_time));
            $arrival_flight = $request->arrival_flight??"Insert flight number!";
            AirportShuttle::create([
                "date"=>$arrival_time,
                "flight_number"=>$arrival_flight,
                "number_of_guests"=>$transport_in->capacity,
                "order_id"=>$order->id,
                "transport_id"=>$transport_in->id,
                "price_id"=>$request->transport_in_price_id,
                "src"=>"Airport",
                "dst"=>$villa->name,
                "duration"=>$villa->airport_duration,
                "distance"=>$villa->airport_distance,
                "price"=>$airport_shuttle_in_price->calculatePrice($usdrates, $tax),
                "nav"=>"In",
            ]);
        }
        if ($request->airport_shuttle_out){
            $transport_out = Transports::find($request->airport_shuttle_out) ?? null;
            $departure_time = date('Y-m-d H:i:s',strtotime($request->departure_time));
            $departure_flight = $request->departure_flight??"Insert flight number!";
            AirportShuttle::create([
                "date"=>$departure_time,
                "flight_number"=>$departure_flight,
                "number_of_guests"=>$transport_out->capacity,
                "order_id"=>$order->id,
                "transport_id"=>$transport_out->id,
                "price_id"=>$request->transport_out_price_id,
                "dst"=>"Airport",
                "src"=>$villa->name,
                "duration"=>$villa->airport_duration,
                "distance"=>$villa->airport_distance,
                "price"=>$airport_shuttle_out_price->calculatePrice($usdrates, $tax),
                "nav"=>"Out",
            ]);
        }

        $user_log_note = "Order created by {$user->name} ({$user->email}) for villa {$villa->name} ({$villa->code}) from {$checkin} to {$checkout}.";
        $user_log = UserLog::create([
            "action"=>"Create Order",
            "service"=>$order->service,
            "subservice"=>$order->subservice,
            "subservice_id"=>$order->subservice_id,
            "page"=>"villa-price-".$villa->code,
            "user_id"=>$user->id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$user_log_note, 
        ]);
        $order_log = OrderLog::create([
            "order_id" => $order->id,
            "action"=>"Create Order",
            "url"=>$request->getClientIp(),
            "method"=>"Create",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        session()->forget('booking_dates');
        if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
            $rquotation = $request->request_quotation;
            Mail::to(config('app.reservation_mail'))
            ->send(new ReservationMail($order->id,$rquotation));
            return redirect()->route('view.detail-order-admin', ['id' => $order->id])->with('success', __('messages.The order has been successfully created'));
        }else{
            return redirect()->route('view.edit-order-villa', ['id' => $order->id])->with('success', __('messages.Your order has been added to the order basket. Please ensure that all details are entered correctly before you confirm the order for further processing.'));
        }
    }

    // VIEW EDIT ORDER VILLA =============================================================================================> OK
    public function edit_order_villa($id)
    {
        $user_id = Auth::id(); 
        $now = Carbon::now();
        $order = Orders::where('sales_agent', $user_id)
            ->where('checkin', '>', $now)
            ->where('id',$id)
            ->first();
        $agent = User::find($order->sales_agent);
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));
        $villa = Villas::with(['galleries', 'rooms' => fn($q) => $q->where('status', 1)])
            ->findOrFail($order->service_id);
        $guests = Guests::where('order_id', $order->id)->get();
        $id_last_guest = Guests::max('id');
        $rooms = $villa->rooms;


        $airport_shuttles = AirportShuttle::where('order_id',$order->id)->get();
        $airport_shuttle_in = AirportShuttle::with('transport')->where('order_id',$order->id)->where('nav', 'In')->first();
        $airport_shuttle_out = AirportShuttle::with('transport')->where('order_id',$order->id)->where('nav', 'Out')->first();
        $airport_shuttle_any_zero = $airport_shuttles->contains(fn($shuttle) => $shuttle->price == 0);

        $total_price_airport_shuttle = $airport_shuttles->sum('price');
        $transport_in = Transports::find($airport_shuttle_in?->transport_id) ?? null;
        $transport_out = Transports::find($airport_shuttle_out?->transport_id) ?? null;

        $duration = $order->duration ?? Carbon::parse($order->checkin)->diffInDays(Carbon::parse($order->checkout));
        $hasInvalidOrder = !$order->number_of_room || !$order->number_of_guests_room || !$order->guest_detail;

        $number_of_adult = $rooms->sum('guest_adult');
        $number_of_children = $rooms->sum('guest_child');
        $occupancy = $order->capacity;
        $transport_duration = $villa->airport_duration ?? 2;
        $transports = Transports::with(['prices' => function ($q) use ($transport_duration) {
                $q->where('duration','>=',$transport_duration);
            }])
            ->get();

        foreach ($transports as $transport) {
            $price = $transport->prices->first();
            if ($price) {
                $transport->calculated_price = $price->calculatePrice($usdrates, $tax);
                $transport->calculated_price_id = $price->id;
            } else {
                $transport->calculated_price = null;
                $transport->calculated_price_id = null;
            }
        }
        $canEditOrder = in_array($order->status, ["Draft", "Invalid"]);
        $statusMap = [
            'Invalid'   => ['class' => 'order-status-invalid', 'label' => __('messages.Invalid')],
            'Draft'     => ['class' => 'order-status-draft', 'label' => __('messages.Draft')],
        ];
        if ($canEditOrder) {
            return view('order.user-edit-order', array_merge([
                'order' => $order,
                'tax' => $tax,
                'now' => $now,
                'usdrates' => $usdrates,
                'business' => $business,
                'villa' => $villa,
                'guests' => $guests,
                'id_last_guest' => $id_last_guest,
                'occupancy' => $occupancy,
                'transports' => $transports,
                'transport_in' => $transport_in,
                'airport_shuttle_in' => $airport_shuttle_in,
                'airport_shuttle_out' => $airport_shuttle_out,
                'airport_shuttle_any_zero' => $airport_shuttle_any_zero,
                'total_price_airport_shuttle' => $total_price_airport_shuttle,
                'attentions' => Attention::where('page', 'edit-order-villa')->get(),
                'hasInvalidOrder' => $hasInvalidOrder,
                'canEditOrder' => $canEditOrder,
                'agent' => $agent,
                'rooms' => $rooms,
                'number_of_adult' => $number_of_adult,
                'number_of_children' => $number_of_children,
                'duration' => $duration,
                'statusMap' => $statusMap,
            ]));
        }
        return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
    }
    
    // CHECKOUT ORDER VILLA ================================================================================> OK
    public function func_checkout_order_villa(Request $request, $id){
        $user = Auth::user();
        $order=Orders::where('id',$id)
            ->where('sales_agent',$user->id)
            ->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $villa = Villas::find($order->service_id);
        $status = "Pending";
        $inputGuests = $request->input('guests', []);
        $number_of_guests = count($inputGuests);
        $existingGuestIds = Guests::where('order_id', $order->id)->pluck('id')->toArray();
        $processedIds = [];
        $arrival_flight = $request->arrival_flight;
        $departure_flight = $request->departure_flight;
        $departure_time = date('Y-m-d H:i',strtotime($request->departure_time));
        $order->update([
            "status"=>$status,
            "note"=>$request->note,
            "number_of_guests"=>$number_of_guests,
            "airport_shuttle_price" =>$request->airport_shuttle_price,
            "final_price" =>$request->final_price, 
            "arrival_flight" =>$arrival_flight,
            "arrival_time" =>$request->arrival_time,
            "airport_shuttle_in" =>$request->airport_shuttle_in,
            "departure_flight" =>$request->departure_flight,
            "departure_time" =>$request->departure_time,
            "airport_shuttle_out" =>$request->airport_shuttle_out,
        ]);
        // GUEST
        foreach ($inputGuests as $guestData) {
            if (!empty($guestData['id'])) {
                $guest = Guests::find($guestData['id']);
                if ($guest) {
                    $guest->update([
                        'name' => $guestData['name'],
                        'sex' => $guestData['sex'],
                        'age' => $guestData['age'],
                    ]);
                }
            } else {
                $newGuest = Guests::create([
                    'order_id' => $order->id,
                    'name' => $guestData['name'],
                    'sex' => $guestData['sex'],
                    'age' => $guestData['age'],
                ]);
            }
        }
        // AIRPORT SHUTTLE
        $arrival = AirportShuttle::where('order_id', $order->id)
            ->where('nav', 'In')
            ->first();

        $arrivalInputValid = $request->filled('airport_shuttle_in');
        $price_in = TransportPrice::find($request->transport_in_price_id);

        if ($arrival) {
            if ($arrivalInputValid) {
                $flight_in_number = $request->arrival_flight ?? null;
                $date_in = date('Y-m-d H:i',strtotime($request->arrival_time ?? $order->checkin));
                $arrival->update([
                    'flight_number' => $flight_in_number,
                    'number_of_guests' => $number_of_guests,
                    'date' => $date_in,
                    'transport_id' => $request->airport_shuttle_in,
                    'price_id' => $request->transport_in_price_id,
                    'price' => $price_in->calculatePrice($usdrates,$tax),
                    'nav' => 'In',
                ]);
            } else {
                $arrival->delete();
            }
        } else {
            if ($arrivalInputValid) {
                
                $flight_in_number = $request->arrival_flight ?? null;
                $date_in = date('Y-m-d H:i',strtotime($request->arrival_time ?? $order->checkin));
                AirportShuttle::create([
                    'order_id' => $order->id,
                    'flight_number' => $flight_in_number,
                    'number_of_guests' => $number_of_guests,
                    'date' => $date_in,
                    'transport_id' => $request->airport_shuttle_in,
                    'price_id' => $request->transport_in_price_id,
                    'src' => "Airport",
                    'dst' => $villa->name,
                    'duration' => $villa->airport_duration,
                    'distance' => $villa->airport_distance,
                    'price' => $price_in->calculatePrice($usdrates,$tax),
                    'nav' => 'In',
                ]);
            }
        }

        // Departure
        $departure = AirportShuttle::where('order_id', $order->id)
            ->where('nav', 'Out')
            ->first();

        $departureInputValid = $request->filled('airport_shuttle_out');
        $price_out = TransportPrice::find($request->transport_out_price_id);

        if ($departure) {
            if ($departureInputValid) {
                
                $flight_out_number = $request->departure_flight ?? null;
                $date_out = date('Y-m-d H:i',strtotime($request->departure_time ?? $order->checkout));
                $departure->update([
                    'flight_number' => $flight_out_number,
                    'number_of_guests' => $number_of_guests,
                    'date' => $date_out,
                    'transport_id' => $request->airport_shuttle_out,
                    'price_id' => $request->transport_out_price_id,
                    'price' => $price_out->calculatePrice($usdrates,$tax),
                'nav' => 'Out',
                ]);
            } else {
                $departure->delete();
            }
        } else {
            if ($departureInputValid) {
                $flight_out_number = $request->departure_flight ?? null;
                $date_out = date('Y-m-d H:i',strtotime($request->departure_time ?? $order->checkout));
                AirportShuttle::create([
                    'order_id' => $order->id,
                    'flight_number' => $flight_out_number,
                    'number_of_guests' => $number_of_guests,
                    'date' => $date_out,
                    'transport_id' => $request->airport_shuttle_out,
                    'price_id' => $request->transport_out_price_id,
                    'src' => $villa->name,
                    'dst' => "Airport",
                    'duration' => $villa->airport_duration,
                    'distance' => $villa->airport_distance,
                    'price' => $price_out->calculatePrice($usdrates,$tax),
                    'nav' => 'Out',
                ]);
            }
        }
        
        // dd($order);
        $rquotation = $request->request_quotation;
        $agent = User::where('id',$order->user_id)->first();
        Mail::to(config('app.reservation_mail'))->send(new ReservationMail($id,$rquotation));
        $note = "Submited order no: ".$order->orderno;
        
        $user_log =new UserLog([
            "action"=>"Submit Order",
            "service"=>$order->service,
            "subservice"=>$order->subservice,
            "subservice_id"=>$id,
            "page"=>"edit-order-transport",
            "user_id"=>Auth::user()->id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>'Submit Order',
            "url"=>$request->getClientIp(),
            "method"=>"Submit",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/detail-order-villa/$order->id")->with('success','Your order has been submited, and we will validate your order');
    }

    // VIEW DETAIL ORDER HOTEL ===============================================================================================> OK
    public function detail_order_villa($id)
    {
        $user_id = Auth::id(); 
        $now = Carbon::now();
        $order = Orders::with(['optional_rate_orders', 'reservations.invoice'])
            ->where('sales_agent', $user_id)
            ->where('checkin', '>', $now)
            ->where('id',$id)
            ->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $statusMap = [
            'Pending'   => ['class' => 'order-status-pending', 'label' => __('messages.Pending')],
            'Rejected'     => ['class' => 'order-status-rejected', 'label' => __('messages.Rejected')],
            'Approved'     => ['class' => 'order-status-approved', 'label' => __('messages.Approved')],
            'Paid'     => ['class' => 'order-status-paid', 'label' => __('messages.Paid')],
            'Canceled'     => ['class' => 'order-status-canceled', 'label' => __('messages.Canceled')],
        ];
        $agent = User::find($order->sales_agent);
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::firstWhere('name', 'USD'));
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $attentions = Cache::remember('attention', 3600, fn() => Attention::where('page', 'orders')->get());
        $villa = Villas::with(['galleries', 'rooms' => fn($q) => $q->where('status', 1)])
            ->findOrFail($order->service_id);
        if ($villa) {
            $optional_rate = $villa->optionalrates;
        }else{
            $optional_rate = NULL;
        }
        $guests = Guests::where('order_id', $order->id)->get();
        $id_last_guest = Guests::max('id');
        $rooms = $villa->rooms;
        $number_of_adult = count($guests->where('age','Adult'));
        $number_of_children = count($guests->where('age','Child'));
        $occupancy_adult = $rooms->sum('guest_adult');
        $occupancy_children = $rooms->sum('guest_child');
        $occupancy = $order->capacity;



        $airport_shuttles = AirportShuttle::where('order_id', $order->id)->get();
        $airport_shuttle_any_zero = $airport_shuttles->contains(fn($shuttle) => $shuttle->price == 0);
        $total_price_airport_shuttle = $airport_shuttles->sum('price');
        $optional_rate_orders = $order->optional_rate_orders;
        $optionalServiceTotalPrice = $optional_rate_orders->sum('price_total');
        $reservation = Reservation::find($order->rsv_id)??null;
        if ($reservation) {
            $inv_no = "INV-".$reservation->rsv_no;
        }else{
            $inv_no = null;
        }
        $invoice = InvoiceAdmin::firstWhere('rsv_id', $order->rsv_id);
        $receipts = $invoice ? $invoice->payment : null;
        if ($invoice) {
            $doku_payment = DokuVirtualAccount::where('invoice_id', $invoice->id)
            ->where('expired_date','>=',$now)
            ->orderBy('expired_date', 'desc')
            ->first();
            $doku_payment_paid = DokuVirtualAccount::where('invoice_id', $invoice->id)
            ->where('status', 'Paid')
            ->first();
        }else{
            $doku_payment_paid = null;
            $doku_payment = null;
        }
        
        $decodedData = collect([
            'number_of_guests_room' => json_decode($order->number_of_guests_room, true),
            'guest_details' => json_decode($order->guest_detail, true),
            'special_days' => json_decode($order->special_day, true),
            'special_dates' => json_decode($order->special_date, true),
            'extra_beds' => json_decode($order->extra_bed, true),
            'extra_bed_prices' => json_decode($order->extra_bed_price, true),
            'extra_bed_total_prices' => json_decode($order->extra_bed_total_price, true),
            'additional_services' => json_decode($order->additional_service, true),
            'additional_services_date' => json_decode($order->additional_service_date, true),
            'additional_services_qty' => json_decode($order->additional_service_qty, true),
            'additional_services_price' => json_decode($order->additional_service_price, true),
            
        ]);
        $additional_services_data = collect($decodedData['additional_services'])->map(function ($service, $index) use ($decodedData) {
            return [
                'date' => $decodedData['additional_services_date'][$index] ?? null,
                'service' => $service,
                'qty' => $decodedData['additional_services_qty'][$index] ?? 0,
                'price' => $decodedData['additional_services_price'][$index] ?? 0,
            ];
        });
        $additionalServices = $additional_services_data->map(function ($service) {
            return [
                'date' => dateFormat($service['date']),
                'service' => $service['service'],
                'qty' => $service['qty'],
                'price' => $service['price'],
                'total' => $service['qty'] * $service['price'],
            ];
        });
        $additional_service_total_price = $additionalServices->sum(fn($service) => str_replace(".", "", $service['total']));
        $promotion_discounts = json_decode($order->promotion_disc, true);
        $total_promotion_disc = $promotion_discounts ? array_sum($promotion_discounts) : null;
        $kickback = $order->kick_back ? $order->kick_back : null;
        $discounts = [
            'Kick Back' => $kickback > 0 ? $kickback : null,
            'Promotion' => $total_promotion_disc > 0 ? $total_promotion_disc : null,
            'Booking Code' => $order->bookingcode_disc > 0 ? $order->bookingcode_disc : null,
            'Discounts' => $order->discounts > 0 ? $order->discounts : null
        ];
        $filteredDiscounts = array_filter($discounts, fn($value) => !is_null($value));
        $normal_price = $order->final_price + $total_promotion_disc + $order->bookingcode_disc + $order->discounts;
        $total_price_idr = $order->final_price * $usdrates->rate;
        $taxDoku = TaxDoku::find('1');
        $tax_doku = floor($total_price_idr * $taxDoku->tax_rate);
        $doku_total_price = $total_price_idr + $tax_doku;
        if (in_array($order->status, ["Pending", "Rejected","Approved","Paid","Canceled"])) {
            return view('order.user-detail-order', array_merge([
                'order' => $order,
                'tax' => $tax,
                'now' => $now,
                'usdrates' => $usdrates,
                'business' => $business,
                'attentions' => $attentions,
                'invoice' => $invoice,
                'reservation' => $reservation,
                'inv_no' => $inv_no,
                'statusMap' => $statusMap,
                'villa' => $villa,
                'guests' => $guests,
                'id_last_guest' => $id_last_guest,
                'rooms' => $rooms,
                'number_of_adult' => $number_of_adult,
                'number_of_children' => $number_of_children,
                'occupancy_adult' => $occupancy_adult,
                'occupancy_children' => $occupancy_children,
                'occupancy' => $occupancy,

                
                'airport_shuttles' => $airport_shuttles,
                'airport_shuttle_any_zero' => $airport_shuttle_any_zero,
                'total_price_airport_shuttle' => $total_price_airport_shuttle,
                'optional_rate' => $optional_rate,
                'optional_rate_orders' => $optional_rate_orders,
                'additionalServices' => $additionalServices,
                'additional_service_total_price' => $additional_service_total_price,
                'optionalServiceTotalPrice' => $optionalServiceTotalPrice,
                'total_promotion_disc' => $total_promotion_disc,
                'filteredDiscounts' => $filteredDiscounts,
                'normal_price' => $normal_price,
                'receipts' => $receipts,
                'agent' => $agent,
                'doku_payment' => $doku_payment,
                'doku_payment_paid' => $doku_payment_paid,
                'tax_doku' => $tax_doku,
                'taxDoku' => $taxDoku,
                'doku_total_price' => $doku_total_price,
                'total_price_idr' => $total_price_idr,
            ], $decodedData->toArray()));
        } else {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
    }




    // VIEW USER ORDER HOTEL PACKAGE ================================================================================> OK
    public function order_hotel_package(Request $request, $id)
    {
        $user_id = Auth::id();
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));
        $attentions = Attention::where('page', 'order-hotel-promo')->get();
        $room = HotelRoom::with('hotels')->findOrFail($id);
        $hotel = $room->hotels;
        if (!session()->has('booking_dates.checkin')) {
            return redirect("/hotel-$hotel->code");
        }
        $checkin = session('booking_dates.checkin');
        $checkout = session('booking_dates.checkout');
        $service = "Hotel Package";
        $order_value = Orders::count();
        $orderNumber = "HPA" . date('ymd', strtotime($now)) . "-" . $order_value;
        $duration = Carbon::parse($checkin)->diffInDays(Carbon::parse($checkout));
        $bedroom = $room->capacity / 2;
        $room_capacity = $room->capacity + $bedroom;
        $package = HotelPackage::find($request->package_id);
        $transports = Transports::with('prices')
            ->select('id', 'name', 'brand', 'capacity')
            ->where('status', "Active")
            ->orderByDesc('capacity')
            ->get();
        $transport_prices = TransportPrice::where('duration','>=', $hotel->airport_duration)->get();
        $agents = User::where('status', "Active")
            ->whereNotNull('email_verified_at')
            ->get();
        $final_price = $package->calculatePrice($usdrates,$tax);
        $data = ([
            'now' => $now,
            'usdrates' => $usdrates,
            'tax' => $tax,
            'business' => $business,
            'logoDark' => $logoDark,
            'altLogo' => $altLogo,
            'service' => $service,
            'orderNumber' => $orderNumber,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'duration' => $duration,
            'hotel' => $hotel,
            'room' => $room,
            'room_capacity' => $room_capacity,
            'package' => $package,
            'transports' => $transports,
            'transport_prices' => $transport_prices,
            'agents' => $agents,
            'final_price' => $final_price,
        ]);
        return view('form.order-hotel-package',$data);
    }
    // VIEW USER ORDER HOTEL NORMAL =================================================================================> OK
    public function order_hotel_normal(Request $request, $id)
    {
        if (!session()->has('booking_dates.checkin')) {
            return redirect("/hotel-{$request->hotel_id};");
        }
        $user_id = Auth::id();
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));
        $attentions = Attention::where('page', 'order-hotel-promo')->get();
        $promotions = Promotion::where('periode_start','<=',$now)->where('periode_end','>=',$now)->where('status','Active')->get();
        $promotions_id = json_encode($promotions->pluck('id'));
        $promotions_name = $promotions->pluck('name')->implode(', ');
        $promotions_discount = json_encode($promotions->pluck('discounts'));
        $total_promotions_discount = $promotions->sum('discounts');
        $orders = Orders::where('sales_agent', $user_id)->pluck('booking_code');
        $order_value = Orders::count() + 1;
        $orderNumber = "HNP" . date('ymd', strtotime($now)) . "-" . $order_value;
        $bk_code = BookingCode::where('code', $request->bookingcode)
            ->where('status', 'Active')
            ->first();
        [$bookingcode, $bookingcode_status] = $this->check_booking_code($bk_code, $orders, $now);
        $room = HotelRoom::with(['hotels'])->find($id);
        $hotel = $room->hotels;
        $bedroom = $room->capacity / 2;
        $room_capacity = $room->capacity + $bedroom;
        $transports = Transports::with('prices')
            ->where('status', 'Active')
            ->orderByDesc('capacity')
            ->get()
            ->map(function ($transport) use ($hotel, $usdrates, $tax) {
                $selectedPrice = optional($transport->prices->firstWhere('duration', '>=', optional($hotel)->airport_duration));
                $transport->calculated_price = $selectedPrice ? $selectedPrice->calculatePrice($usdrates, $tax) : 0;
                $transport->calculated_price_id = $selectedPrice->id ?? null;
                return $transport;
            });
        $extrabed = ExtraBed::where('hotels_id', $request->hotel_id)->get();
        $usdrates = UsdRates::where('name', 'USD')->first();
        $tax = Tax::where('name', "Tax")->first();
        $price_list = $request->price_list;
        $normal_price = $request->normal_price;
        $agents = User::where('status', "Active")
            ->whereNotNull('email_verified_at')
            ->get();
        $data = [
            'now' => $now,
            'business' => $business,
            'duration' => $request->duration,
            'checkin' => $request->checkin,
            'checkout' => $request->checkout,
            'price_pax' => $request->price_pax,
            'normal_price' => $normal_price,
            'kick_back' => $request->kick_back,
            'kick_back_per_pax' => $request->kick_back_per_pax,
            'service' => $request->service,
            'final_price' => $request->final_price,
            'promo_id' => $request->promo_id,
            'hotel' => $hotel,
            'room' => $room,
            'room_capacity' => $room_capacity,
            'transports' => $transports,
            'extrabed' => $extrabed,
            'usdrates' => $usdrates,
            'tax' => $tax,
            'agents' => $agents,
            'promotions' => $promotions,
            'promotions_id' => $promotions_id,
            'bookingcode' => $bookingcode,
            'bookingcode_status' => $bookingcode_status,
            'orderNumber' => $orderNumber,
            'logoDark' => $logoDark,
            'altLogo' => $altLogo,
            'price_list' => $price_list,
            'promotions_name' => $promotions_name,
            'promotions_discount' => $promotions_discount,
            'total_promotions_discount' => $total_promotions_discount,
        ];
        return view('form.order-hotel-normal',$data);
    }
    // FUNCTION USER CREATE ORDER HOTEL PACKAGE =====================================================================> OK
    public function func_create_order_hotel_package(Request $request, $id){
        $user = Auth::user();
        $developerRoles = ["developer", "reservation", "author"];
        if (in_array($user->position, $developerRoles)) {
            $sales_agent = $request->user_id;
            $status = "Pending";
        } else {
            $sales_agent = $user->id;
            $status = "Draft";
        }
        $user_id = $user->id;
        $email = $user->email;
        $name = $user->name;
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $cnyrates = Cache::remember('cny_rate', 3600, fn() => UsdRates::where('name', 'CNY')->first());
        $twdrates = Cache::remember('twd_rate', 3600, fn() => UsdRates::where('name', 'TWD')->first());
        $service = "Hotel Package";
        $package = HotelPackage::with(['hotels','room'])->find($id);
        $hotel = $package->hotels;
        $room = $package->room;
        $checkin = Carbon::parse(session('booking_dates.checkin'))->format('Y-m-d');
        $checkout = Carbon::parse(session('booking_dates.checkout'))->format('Y-m-d');
        $number_of_guests = array_sum($request->number_of_guests);
        $number_of_room = count($request->number_of_guests);
        $number_of_guests_room = json_encode($request->number_of_guests);
        $guest_detail = json_encode($request->guest_detail);
        $special_day = json_encode($request->special_day);
        $special_date = json_encode($request->special_date);
        $request_quotation = $request->request_quotation ? 1 : NULL;
        $duration = $request->duration;
        $cancellation_policy = $hotel->cancellation_policy;

        $extraBeds = ExtraBed::where('hotels_id', $hotel->id)->get();
        $extra_bed_proses = [];
        $extra_bed_id_price = [];
        $extrabed_id = [];
        foreach ($request->number_of_guests as $index => $number_of_guest) {
            $isExtraBedNeeded = $number_of_guest > $room->capacity;
            $extra_bed_proses[] = $isExtraBedNeeded ? 'Yes' : 'No';
            if ($isExtraBedNeeded) {
                $extraBed = isset($request->extra_bed_id[$index]) 
                    ? $extraBeds->find($request->extra_bed_id[$index]) 
                    : $extraBeds->first();

                if ($extraBed) {
                    $price_extra_bed = $extraBed->calculatePrice($usdrates, $tax) * $duration;
                    $extra_bed_id_price[] = $price_extra_bed;
                    $extrabed_id[] = $extraBed->id;
                } else {
                    $extra_bed_id_price[] = 0;
                    $extrabed_id[] = NULL;
                }
            } else {
                $extra_bed_id_price[] = 0;
                $extrabed_id[] = NULL;
            }
        }
        $extra_bed_id = json_encode($extrabed_id);
        $extra_bed_price_list = json_encode($extra_bed_id_price);
        $extra_bed_status = json_encode($extra_bed_proses);
        $total_extra_bed_price = array_sum($extra_bed_id_price);

        if ($request->airport_shuttle_in) {
            $transport_in_price = TransportPrice::find($request->airport_shuttle_in_price_id);
            $price_in_id = $transport_in_price ? $transport_in_price->id : null;
            $price_in = $transport_in_price ? $transport_in_price->calculatePrice($usdrates, $tax) : 0;
        }else{
            $price_in_id = null;
            $price_in = 0;
        }
        if ($request->airport_shuttle_out) {
            $transport_out_price = TransportPrice::find($request->airport_shuttle_out_price_id);
            $price_out_id = $transport_out_price ? $transport_out_price->id : null;
            $price_out = $transport_out_price ? $transport_out_price->calculatePrice($usdrates, $tax) : 0;
        }else{
            $price_out_id = null;
            $price_out = 0;
        }

        $airport_shuttle_prices = $price_in + $price_out;

        
        $price_pax = $package->calculatePrice($usdrates,$tax);
        $normal_price = $price_pax * $number_of_room;
        $price_total = $normal_price + $total_extra_bed_price ;
        $final_price = $price_total  + $airport_shuttle_prices;
        $usd_rate = $usdrates->rate;
        $cny_rate = $cnyrates->rate;
        $twd_rate = $twdrates->rate;

        $order = new Orders([
            'orderno'                   => $request->orderno,
            'service'                   => $service,
            'service_id'                => $hotel->id,
            'user_id'                   => $user->id,
            'name'                      => $name,
            'email'                     => $email,
            'servicename'               => $hotel->name,
            'subservice'                => $room->rooms,
            'subservice_id'             => $room->id,
            'package_name'              => $package->name,
            'checkin'                   => $checkin,
            'checkout'                  => $checkout,
            'location'                  => $hotel->region,
            'number_of_guests'          => $number_of_guests,
            'number_of_guests_room'     => $number_of_guests_room,
            'guest_detail'              => $guest_detail,
            'request_quotation'         => $request_quotation,
            'special_date'              => $special_date,
            'special_day'               => $special_day,
            'extra_bed'                 => $extra_bed_status,
            'capacity'                  => $room->capacity,
            'include'                   => $room->include,
            'benefits'                  => $package->benefits,
            'additional_info'           => $package->additional_info,
            'number_of_room'            => $number_of_room,
            'duration'                  => $duration,
            'price_pax'                 => $price_pax,
            'normal_price'              => $normal_price,
            'extra_bed_id'              => $extra_bed_id,
            'extra_bed_price'           => $extra_bed_price_list,
            'extra_bed_total_price'     => $total_extra_bed_price,
            'price_total'               => $price_total,
            'airport_shuttle_price'     => $airport_shuttle_prices,
            'final_price'               => $final_price,
            'usd_rate'                  => $usd_rate,
            'cny_rate'                  => $cny_rate,
            'twd_rate'                  => $twd_rate,
            'status'                    => $status,
            'sales_agent'               => $sales_agent,
            'arrival_flight'            => $request->arrival_flight,
            'arrival_time'              => $request->arrival_time,
            'airport_shuttle_in'        => $request->airport_shuttle_in,
            'departure_flight'          => $request->departure_flight,
            'departure_time'            => $request->departure_time,
            'airport_shuttle_out'       => $request->airport_shuttle_out,
            'note'                      => $request->note,
            'cancellation_policy'       => $cancellation_policy,
        ]);
        // dd($order);
        $order->save();

        if ($request->airport_shuttle_in || $request->airport_shuttle_out) {
            $order_airport_shuttle = $this->create_order_airport_shuttle($request, $hotel, $order, $price_in_id ,$price_out_id, $price_in, $price_out);
        }

        $note = "Created order hotel package with order no: ".$order->orderno;
        $user_log =new UserLog([
            "action"=>"Create Order",
            "service"=>$service,
            "subservice"=>$order->subservice,
            "subservice_id"=>$order->subservice_id,
            "page"=>"hotel-price-".$hotel->code,
            "user_id"=>$user_id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        $order_log =new OrderLog([
            "order_id" => $order->id,
            "action"=>"Create Order",
            "url"=>$request->getClientIp(),
            "method"=>"Create",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        session()->forget('booking_dates');
        if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
            $rquotation = $request->request_quotation;
            Mail::to(config('app.reservation_mail'))
            ->send(new ReservationMail($order->id,$rquotation));
            return redirect()->route('view.detail-order-admin', ['id' => $order->id])->with('success', __('messages.The order has been successfully created'));
        }else{
            return redirect()->route('view.edit-order-hotel', ['id' => $order->id])->with('success', __('messages.Your order has been added to the order basket. Please ensure that all details are entered correctly before you confirm the order for further processing.'));
        }
    }
    // FUNCTION USER CREATE ORDER HOTEL PROMO =======================================================================> OK
    public function func_create_order_hotel_promo(Request $request){
        $user = Auth::user();
        $developerRoles = ["developer", "reservation", "author"];
        if (in_array($user->position, $developerRoles)) {
            $sales_agent = $request->user_id;
            $status = "Pending";
        } else {
            $sales_agent = $user->id;
            $status = "Draft";
        }
        $checkin = Carbon::parse(session('booking_dates.checkin'))->format('Y-m-d');
        $checkout = Carbon::parse(session('booking_dates.checkout'))->format('Y-m-d');
        $user_id = $user->id;
        $email = $user->email;
        $name = $user->name;
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $cnyrates = Cache::remember('cny_rate', 3600, fn() => UsdRates::where('name', 'CNY')->first());
        $twdrates = Cache::remember('twd_rate', 3600, fn() => UsdRates::where('name', 'TWD')->first());
        $room = HotelRoom::with('hotels.extrabeds')->find($request->room_id);
        $hotel = $room->hotels;
        $hotel_id = $hotel->id;
        $extrabeds = $hotel->extrabeds;
        $request_quotation = $request->request_quotation ? 1 : NULL;
        $agent_id = $sales_agent;
        $promo_ids = json_decode($request->promo_id);
        $promos = HotelPromo::whereIn('id', $promo_ids)->get()->keyBy('id');
        $priceList = $request->price_list;
        $room_promo_price = array_sum(json_decode($priceList));
        $service = "Hotel Promo";
        $orderData = [
            'book_period_start'         => $promos->pluck('book_periode_start')->toJson(),
            'book_period_end'           => $promos->pluck('book_periode_end')->toJson(),
            'period_start'              => $promos->pluck('periode_start')->toJson(),
            'period_end'                => $promos->pluck('periode_end')->toJson(),
            'name'                      => $promos->pluck('name')->implode(', '),
            'benefits'                  => $promos->pluck('benefits')->implode('<br>'),
            'include'                   => $promos->pluck('include')->implode('<br>'),
            'additional_info'           => $promos->pluck('additional_info')->implode('<br>'),
            'room_promo_price'          => $room_promo_price,
        ];
        $data_includes = [];
        $data_benefits = [];
        $data_additional_info = [];
        foreach ($promo_ids as $pro_id) {
            $hotel_promo = HotelPromo::find($pro_id);
            if ($hotel_promo) {
                $includes[] = $hotel_promo->include;
                $benefits[] = $hotel_promo->benefits;
                $additional_info[] = $hotel_promo->additional_info;
            }
        }
        $include = implode('<br>', $data_includes);
        $benefits = implode('<br>', $data_benefits);
        $additional_info = implode('<br>', $data_additional_info);
        $total_room = count($request->number_of_guests);
        $cancellation_policy = $room->hotels->cancellation_policy;
        $remark = $request->note;
        $usd_rate_sell = $usdrates->sell;
        $usd_rate_buy = $usdrates->buy;
        $cny_rate_sell = $cnyrates->sell;
        $cny_rate_buy = $cnyrates->buy;
        $twd_rate_sell = $twdrates->sell;
        $twd_rate_buy = $twdrates->buy;
        $duration = $request->duration;
        $extraBeds = ExtraBed::where('hotels_id', $hotel->id)->get()->keyBy('id');
        $extra_bed_proses = [];
        $extra_bed_id_price = [];
        $extrabed_id = [];
        foreach ($request->number_of_guests as $index => $number_of_guest) {
            $isExtraBedNeeded = $number_of_guest > $room->capacity_adult;
            $extra_bed_proses[] = $isExtraBedNeeded ? 'Yes' : 'No';
            if ($isExtraBedNeeded) {
                $extraBed = isset($request->extra_bed_id[$index])
                    ? $request->extra_bed_id[$index] 
                    : $extraBeds->first();

                if ($extraBed) {
                    $price_extra_bed = $extraBed->calculatePrice($usdrates, $tax) * $duration;
                    $extra_bed_id_price[] = $price_extra_bed;
                    $extrabed_id[] = $extraBed->id;
                } else {
                    $extra_bed_id_price[] = 0;
                    $extrabed_id[] = NULL;
                }
            } else {
                $extra_bed_id_price[] = 0;
                $extrabed_id[] = NULL;
            }
        }
        $extra_bed_id = json_encode($extrabed_id);
        $extra_bed_price_list = json_encode($extra_bed_id_price);
        $extra_bed_status = json_encode($extra_bed_proses);
        $total_extra_bed_price= array_sum($extra_bed_id_price);
        $number_of_guests = array_sum($request->number_of_guests);
        
        
        if ($request->airport_shuttle_in) {
            $transport_in_price = TransportPrice::find($request->airport_shuttle_in_price_id);
            $price_in_id = $transport_in_price ? $transport_in_price->id : null;
            $price_in = $transport_in_price ? $transport_in_price->calculatePrice($usdrates, $tax) : 0;
        }else{
            $price_in_id = null;
            $price_in = 0;
        }
        if ($request->airport_shuttle_out) {
            $transport_out_price = TransportPrice::find($request->airport_shuttle_out_price_id);
            $price_out_id = $transport_out_price ? $transport_out_price->id : null;
            $price_out = $transport_out_price ? $transport_out_price->calculatePrice($usdrates, $tax) : 0;
        }else{
            $price_out_id = null;
            $price_out = 0;
        }
        $airport_shuttle_prices = $price_in + $price_out;
        

        // ini
        $optional_rates = OptionalRate::mustBuy($checkin, $checkout)->get();
        $totalPriceOptionalRates = $optional_rates->sum(function ($rate) use ($usdrates, $tax) {
            return $rate->calculatePrice($usdrates, $tax);
        });
        $optional_price = $totalPriceOptionalRates * $number_of_guests;
        $order = $this->create_order($request, $user_id, $name, $email, $orderData, $room, $request_quotation, $duration, $extra_bed_id, $extra_bed_proses, $extra_bed_price_list, $total_extra_bed_price, $extra_bed_status, $airport_shuttle_prices, $agent_id, $usdrates, $cnyrates, $twdrates, $tax, $status, $optional_price);
        if ($request->airport_shuttle_in || $request->airport_shuttle_out) {
            $order_airport_shuttle = $this->create_order_airport_shuttle($request, $hotel, $order, $price_in_id ,$price_out_id, $price_in, $price_out);
        }

        if ($optional_rates) {
            foreach ($optional_rates as $optional_rate) {
                $or_price_pax = $optional_rate->calculatePrice($usdrates, $tax);
                $or_price_total = $or_price_pax * $order->number_of_guests;
                $optional_rate_order =new OptionalRateOrder([
                    "orders_id"=>$order->id,
                    "service"=>$order->service,
                    "optional_rate_id"=>$optional_rate->id,
                    "number_of_guest"=>$order->number_of_guests,
                    "service_date"=>$optional_rate->active_date,
                    "price_pax"=>$or_price_pax,
                    "price_total" =>$or_price_total, 
                    "mandatory" =>1, 
                ]);
                $optional_rate_order->save();
            }
        }


        $note = "Created order hotel promo with order no: ".$order->orderno;
        // dd($order, $agent_id, $sales_agent);
        $user_log =new UserLog([
            "action"=>"Create Order",
            "service"=>$service,
            "subservice"=>$order->subservice,
            "subservice_id"=>$order->subservice_id,
            "page"=>"hotel-price-".$hotel->code,
            "user_id"=>$user_id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        $order_log =new OrderLog([
            "order_id" => $order->id,
            "action"=>"Create Order",
            "url"=>$request->getClientIp(),
            "method"=>"Create",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        session()->forget('booking_dates');
        if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
            $rquotation = $request->request_quotation;
            Mail::to(config('app.reservation_mail'))
            ->send(new ReservationMail($order->id,$rquotation));
            return redirect()->route('view.detail-order-admin', ['id' => $order->id])->with('success', __('messages.The order has been successfully created'));
        }else{
            return redirect()->route('view.edit-order-hotel', ['id' => $order->id])->with('success', __('messages.Your order has been added to the order basket. Please ensure that all details are entered correctly before you confirm the order for further processing.'));
        }
    }
    // FUNCTION USER CREATE ORDER HOTEL =============================================================================> OK
    public function func_create_order_hotel_normal(Request $request){
        $user = Auth::user();
        $developerRoles = ["developer", "reservation", "author"];
        if (in_array($user->position, $developerRoles)) {
            $sales_agent = $request->user_id;
            $status = "Pending";
        } else {
            $sales_agent = $user->id;
            $status = "Draft";
        }
        $user_id = $user->id;
        $email = $user->email;
        $name = $user->name;
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $cnyrates = Cache::remember('cny_rate', 3600, fn() => UsdRates::where('name', 'CNY')->first());
        $twdrates = Cache::remember('twd_rate', 3600, fn() => UsdRates::where('name', 'TWD')->first());
        $room = HotelRoom::find($request->room_id);
        $hotel = Hotels::find($request->hotel_id);
        $service = "Hotel";
        $service_id = $hotel->id;
        $checkin = Carbon::parse(session('booking_dates.checkin'))->format('Y-m-d');
        $checkout = Carbon::parse(session('booking_dates.checkout'))->format('Y-m-d');
        $number_of_guests = array_sum($request->number_of_guests);
        $number_of_room = count($request->number_of_guests);
        $number_of_guests_room = json_encode($request->number_of_guests);
        $guest_detail = json_encode($request->guest_detail);
        $special_day = json_encode($request->special_day);
        $special_date = json_encode($request->special_date);
        $request_quotation = $request->request_quotation ? 1 : NULL;
        $duration = $request->duration;
        $cancellation_policy = $hotel->cancellation_policy;

        $extraBeds = ExtraBed::where('hotels_id', $hotel->id)->get();
        $extra_bed_proses = [];
        $extra_bed_id_price = [];
        $extrabed_id = [];
        foreach ($request->number_of_guests as $index => $number_of_guest) {
            $isExtraBedNeeded = $number_of_guest > $room->capacity;
            $extra_bed_proses[] = $isExtraBedNeeded ? 'Yes' : 'No';
            if ($isExtraBedNeeded) {
                $extraBed = isset($request->extra_bed_id[$index]) 
                    ? $extraBeds->find($request->extra_bed_id[$index]) 
                    : $extraBeds->first();

                if ($extraBed) {
                    $price_extra_bed = $extraBed->calculatePrice($usdrates, $tax) * $duration;
                    $extra_bed_id_price[] = $price_extra_bed;
                    $extrabed_id[] = $extraBed->id;
                } else {
                    $extra_bed_id_price[] = 0;
                    $extrabed_id[] = NULL;
                }
            } else {
                $extra_bed_id_price[] = 0;
                $extrabed_id[] = NULL;
            }
        }
        $extra_bed_id = json_encode($extrabed_id);
        $extra_bed_price_list = json_encode($extra_bed_id_price);
        $extra_bed_status = json_encode($extra_bed_proses);
        $total_extra_bed_price = array_sum($extra_bed_id_price);

        if ($request->airport_shuttle_in) {
            $transport_in_price = TransportPrice::find($request->airport_shuttle_in_price_id);
            $price_in_id = $transport_in_price ? $transport_in_price->id : null;
            $price_in = $transport_in_price ? $transport_in_price->calculatePrice($usdrates, $tax) : 0;
        }else{
            $price_in_id = null;
            $price_in = 0;
        }
        if ($request->airport_shuttle_out) {
            $transport_out_price = TransportPrice::find($request->airport_shuttle_out_price_id);
            $price_out_id = $transport_out_price ? $transport_out_price->id : null;
            $price_out = $transport_out_price ? $transport_out_price->calculatePrice($usdrates, $tax) : 0;
        }else{
            $price_out_id = null;
            $price_out = 0;
        }

        $airport_shuttle_prices = $price_in + $price_out;
        $total_kick_back = $request->var_kick_back_total;
        $total_promotions_discount = $request->var_promotions_discount;
        $price_pax = $request->var_normal_price;
        $normal_price = $price_pax * $number_of_room;
        $price_total = ($normal_price - $total_kick_back) + $total_extra_bed_price ;
        $final_price = ($price_total - $total_promotions_discount)  + $airport_shuttle_prices;
        $usd_rate = $usdrates->rate;
        $cny_rate = $cnyrates->rate;
        $twd_rate = $twdrates->rate;
        $promotionsId = json_decode($request->promotions_id, true);
        $promotions = Promotion::whereIn('id', $promotionsId)->get();
        $promotion = json_encode($promotions->pluck('name'));
        $promotion_disc = json_encode($promotions->pluck('discounts'));
        $order = new Orders([
            'orderno'                   => $request->orderno,
            'service'                   => $service,
            'service_id'                => $hotel->id,
            'user_id'                   => $user->id,
            'name'                      => $name,
            'email'                     => $email,
            'servicename'               => $hotel->name,
            'subservice'                => $room->rooms,
            'subservice_id'             => $room->id,
            'checkin'                   => $checkin,
            'checkout'                  => $checkout,
            'location'                  => $hotel->region,
            'number_of_guests'          => $number_of_guests,
            'number_of_guests_room'     => $number_of_guests_room,
            'guest_detail'              => $guest_detail,
            'request_quotation'         => $request_quotation,
            'special_date'              => $special_date,
            'special_day'               => $special_day,
            'extra_bed'                 => $extra_bed_status,
            'capacity'                  => $room->capacity,
            'include'                   => $room->include,
            'additional_info'           => $room->additional_info,
            'number_of_room'            => $number_of_room,
            'duration'                  => $duration,
            'price_pax'                 => $price_pax,
            'normal_price'              => $normal_price,
            'kick_back'                 => $total_kick_back,
            'kick_back_per_pax'         => $request->var_kick_back_per_room,
            'extra_bed_id'              => $extra_bed_id,
            'extra_bed_price'           => $extra_bed_price_list,
            'extra_bed_total_price'     => $total_extra_bed_price,
            'price_total'               => $price_total,
            'promotion'                 => $promotion,
            'promotion_disc'            => $promotion_disc,
            'airport_shuttle_price'     => $airport_shuttle_prices,
            'final_price'               => $final_price,
            'usd_rate'                  => $usd_rate,
            'cny_rate'                  => $cny_rate,
            'twd_rate'                  => $twd_rate,
            'status'                    => $status,
            'sales_agent'               => $sales_agent,
            'arrival_flight'            => $request->arrival_flight,
            'arrival_time'              => $request->arrival_time,
            'airport_shuttle_in'        => $request->airport_shuttle_in,
            'departure_flight'          => $request->departure_flight,
            'departure_time'            => $request->departure_time,
            'airport_shuttle_out'       => $request->airport_shuttle_out,
            'note'                      => $request->note,
            'cancellation_policy'       => $cancellation_policy,
        ]);
        // dd($order);
        $order->save();

        if ($request->airport_shuttle_in || $request->airport_shuttle_out) {
            $order_airport_shuttle = $this->create_order_airport_shuttle($request, $hotel, $order, $price_in_id ,$price_out_id, $price_in, $price_out);
        }
        $note = "Created order hotel promo with order no: ".$order->orderno;
        $user_log =new UserLog([
            "action"=>"Create Order",
            "service"=>$service,
            "subservice"=>$order->subservice,
            "subservice_id"=>$order->subservice_id,
            "page"=>"hotel-price-".$hotel->code,
            "user_id"=>$user->id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        $order_log =new OrderLog([
            "order_id" => $order->id,
            "action"=>"Create Order",
            "url"=>$request->getClientIp(),
            "method"=>"Create",
            "agent"=>$order->name,
            "admin"=>$user->id,
        ]);
        $order_log->save();
        session()->forget('booking_dates');
        if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
            $rquotation = $request->request_quotation;
            Mail::to(config('app.reservation_mail'))
            ->send(new ReservationMail($order->id,$rquotation));
            return redirect()->route('view.detail-order-admin', ['id' => $order->id])->with('success', __('messages.The order has been successfully created'));
        }else{
            return redirect()->route('view.edit-order-hotel', ['id' => $order->id])->with('success', __('messages.Your order has been added to the order basket. Please ensure that all details are entered correctly before you confirm the order for further processing.'));
        }
    }

    // PRIVATE FUNCTION USER CREATE ORDER HOTEL ====================================================================> OK
    private function create_order($request, $user_id, $name, $email, $orderData, $room, $request_quotation, $duration, $extra_bed_id, $extra_bed_proses, $extra_bed_price_list, $total_extra_bed_price, $extra_bed_status, $airport_shuttle_prices, $agent_id, $usdrates, $cnyrates, $twdrates, $tax, $status, $optional_price){
        $hotel = $room->hotels;
        $number_of_room = count($request->number_of_guests);
        $number_of_guests = array_sum($request->number_of_guests);
        $number_of_guests_room = json_encode($request->number_of_guests);
        $guest_detail = json_encode($request->guest_detail);
        $special_day = json_encode($request->special_day);
        $special_date = json_encode($request->special_date);
        $checkin = date('Y-m-d', strtotime(session("booking_dates.checkin")));
        $checkout = date('Y-m-d', strtotime(session("booking_dates.checkout")));
        $capacity = $room->capacity_adult;
        if ($request->airport_shuttle_in || $request->airport_shuttle_out) {
            $arrival_time = $request->arrival_time ? $request->arrival_time : date('Y-m-d', strtotime(session("booking_dates.checkin")))." 11:00:00";
            $departure_time = $request->departure_time ? $request->departure_time : date('Y-m-d', strtotime(session("booking_dates.checkout")))." 11:00:00";
            $arrival_flight = $request->arrival_flight ? $request->arrival_flight : "Insert flight number";
            $departure_flight = $request->departure_flight ? $request->departure_flight : "Insert flight number";
        }else {
            $arrival_time = NULL;
            $departure_time = NULL;
            $arrival_flight = NULL;
            $departure_flight = NULL;
        }
        $normal_price = $orderData['room_promo_price'] * $number_of_room;
        $price_pax = ($normal_price / $number_of_room);
        $price_total = $normal_price + $total_extra_bed_price ;
        $final_price = $price_total + $airport_shuttle_prices;
        $promo_name = $orderData['name'];
        $book_period_start = $orderData['book_period_start'];
        $book_period_end = $orderData['book_period_end'];
        $period_start = $orderData['period_start'];
        $period_end = $orderData['period_end'];
        $include = $orderData['include'];
        $benefits = $orderData['benefits'];
        $additional_info = $orderData['additional_info'];
        $order =new Orders([
            "user_id"=>$user_id,
            "name"=>$name,
            "email"=>$email,
            "orderno"=>$request->orderno,
            "service"=>$request->service,
            "service_id"=>$hotel->id,
            "servicename" =>$hotel->name,
            "subservice"=>$room->rooms,
            "subservice_id"=>$room->id,
            "package_name"=>$request->package_name,
            "request_quotation"=>$request->request_quotation,
            "location"=>$hotel->region,
            "capacity"=>$capacity,
            "airport_shuttle_in"=>$request->airport_shuttle_in,
            "airport_shuttle_out"=>$request->airport_shuttle_out,
            "note"=>$request->note,
            "promo_id"=>$request->promo_id,
            'promo_name' => $promo_name,
            'book_period_start' => $book_period_start,
            'book_period_end' => $book_period_end,
            'period_start' => $period_start,
            'period_end' => $period_end,
            'include' => $include,
            'benefits' => $benefits,
            'additional_info' => $additional_info,
            "number_of_room"=>$number_of_room,
            "number_of_guests"=>$number_of_guests,
            "number_of_guests_room"=>$number_of_guests_room,
            "guest_detail"=>$guest_detail,
            "extra_bed"=>$extra_bed_status,
            "extra_bed_id"=>$extra_bed_id,
            "extra_bed_price"=>$extra_bed_price_list,
            "extra_bed_total_price"=>$total_extra_bed_price,
            "special_day"=>$special_day,
            "special_date"=>$special_date,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "sales_agent"=>$agent_id,
            "cancellation_policy"=>$hotel->cancellation_policy,
            "duration"=>$duration,
            "price_pax" =>$price_pax,
            "normal_price" =>$normal_price,
            "optional_price" =>$optional_price,
            "price_total" =>$price_total, 
            "final_price" =>$final_price, 
            "usd_rate" =>$usdrates->rate, 
            "cny_rate" =>$cnyrates->rate, 
            "twd_rate" =>$twdrates->rate, 
            "airport_shuttle_price"=>$airport_shuttle_prices,
            "arrival_flight"=>$arrival_flight,
            "arrival_time"=>$arrival_time,
            "departure_flight"=>$departure_flight,
            "departure_time"=>$departure_time,
            "status"=>$status,
        ]);
        $order->save();
        return $order;
    }

    // PRIVATE FUNCTION USER CREATE AIRPORT SHUTTLE ================================================================> OK
    private function create_order_airport_shuttle($request, $hotel, $order, $price_in_id ,$price_out_id, $price_in, $price_out)
    {
        $shuttles = [];
        $number_of_guests = array_sum($request->number_of_guests);
        if ($request->airport_shuttle_in) {
            $date_in = $request->arrival_time ? Carbon::parse($request->arrival_time)->format('Y-m-d H:i') : date('Y-m-d', strtotime(session("booking_dates.checkin")))." 11:00:00";
            $flight_number_in = $request->arrival_flight ? $request->arrival_flight : "Insert flight number";
            $shuttles[] = [
                'date' => $date_in,
                'flight_number' => $flight_number_in,
                'number_of_guests' => $number_of_guests,
                'order_id' => $order->id,
                'transport_id' => $request->airport_shuttle_in,
                'price_id' => $price_in_id,
                'src' => "Airport",
                'dst' => $hotel->name,
                'duration' => $hotel->airport_duration,
                'distance' => $hotel->airport_distance,
                'price' => $price_in,
                'nav' => "In",
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        if ($request->airport_shuttle_out) {
            $date_out = $request->departure_time ? Carbon::parse($request->departure_time)->format('Y-m-d H:i') : date('Y-m-d', strtotime(session("booking_dates.checkout")))." 11:00:00";
            $flight_number_out = $request->departure_flight ? $request->departure_flight : "Insert flight number";
            $shuttles[] = [
                'date' => $date_out,
                'flight_number' => $flight_number_out,
                'number_of_guests' => $number_of_guests,
                'order_id' => $order->id,
                'transport_id' => $request->airport_shuttle_out,
                'price_id' => $price_out_id,
                'src' => $hotel->name,
                'dst' => "Airport",
                'duration' => $hotel->airport_duration,
                'distance' => $hotel->airport_distance,
                'price' => $price_out,
                'nav' => "Out",
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        if (!empty($shuttles)) {
            AirportShuttle::insert($shuttles);
            return $shuttles;
        }
    }

    private function check_booking_code($bk_code, $orders, $now)
    {
        if (!$bk_code) {
            return [null, null];
        }
        if ($bk_code->used >= $bk_code->amount) {
            return [null, "Expired"];
        }
        if ($bk_code->expired_date < $now) {
            return [null, "Expired"];
        }
        if ($orders->contains($bk_code->code)) {
            return [null, "Used"];
        }
        return [$bk_code, "Valid"];
    }

    // VIEW EDIT ORDER HOTEL =============================================================================================> OK
    public function edit_order_hotel($id)
    {
        $user_id = Auth::id(); 
        $now = Carbon::now();
        $order = Orders::where('sales_agent', $user_id)
            ->where('checkin', '>', $now)
            ->where('id',$id)
            ->first();
        $agent = User::find($order->sales_agent);
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));
        $room = HotelRoom::find($order->subservice_id);
        $hotel = optional($room)->hotels;

        $optionalrates = OptionalRate::with('hotels')->get();
        $optionalrate_meals = OptionalRate::with('hotels')->where('type', "Meals")->get();
        $optional_rate_orders = OptionalRateOrder::where('orders_id', $id)->first();
        $tour_price = TourPrices::find($order->price_id);
        $tour_prices = TourPrices::where('tour_id', $order->subservice_id)
            ->where('status', "Active")
            ->orderBy('max_qty', 'ASC')
            ->get();
        $qty = TourPrices::max('max_qty');
        $tour = ($order->service == "Tour Package") ? Tours::find($order->service_id) : null;
        $order_optional_rates = OptionalRateOrder::with('optional_rate')
            ->where('service', 'Hotel Promo')
            ->where('orders_id', $order->id)
            ->get();
        $optionalServiceTotalPrice = $order_optional_rates->sum('price_total');
        $airport_shuttles = AirportShuttle::where('order_id',$order->id)->get();
        $airport_shuttle_any_zero = $airport_shuttles->contains(fn($shuttle) => $shuttle->price == 0);
        $total_price_airport_shuttle = $airport_shuttles->sum('price');
        $transports = Transports::with('prices')
            ->where('status', 'Active')
            ->orderByDesc('capacity')
            ->get()
            ->map(function ($transport) use ($hotel, $usdrates, $tax) {
                $selectedPrice = optional($transport->prices->firstWhere('duration', '>=', optional($hotel)->airport_duration));
                $transport->calculated_price = $selectedPrice ? $selectedPrice->calculatePrice($usdrates, $tax) : 0;
                $transport->calculated_price_id = $selectedPrice->id ?? null;
                return $transport;
            });
        $airport_shuttle_in = $airport_shuttles->firstWhere('nav', 'In');
        $airport_shuttle_out = $airport_shuttles->firstWhere('nav', 'Out');
        $decodedData = collect([
            'nor' => $order->number_of_room,
            'nogr' => json_decode($order->number_of_guests_room, true),
            'guest_detail' => json_decode($order->guest_detail, true),
            'special_day' => json_decode($order->special_day, true),
            'special_date' => json_decode($order->special_date, true),
            'extra_bed' => json_decode($order->extra_bed, true) ?? [],
            'extra_bed_id' => json_decode($order->extra_bed_id, true),
            'extra_bed_price' => json_decode($order->extra_bed_price, true),
        ]);
        $extra_bed_test = json_decode($order->extra_bed, true) ?? [];
        $extraBeds = ExtraBed::where('hotels_id', $hotel->id)->get();
        $serviceLabels = [
            'Hotel' => [['label' => 'messages.Hotel', 'value' => $order->servicename]],
            'Hotel Promo' => [
                ['label' => 'messages.Promo', 'value' => $order->promo_name],
                ['label' => 'messages.Hotel', 'value' => $order->servicename],
            ],
            'Hotel Package' => [
                ['label' => 'messages.Package', 'value' => $order->package_name],
                ['label' => 'messages.Hotel', 'value' => $order->servicename]
            ]
        ];
        $promotionName = json_decode($order->promotion);
        $promotion_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc)) : null;
        $promotions_name = $promotionName ? implode(', ',$promotionName): null;
        $services = $serviceLabels[$order->service] ?? [['label' => 'messages.Hotel', 'value' => $order->servicename]];
        $hasInvalidOrder = !$order->number_of_room || !$order->number_of_guests_room || !$order->guest_detail;
        $showExtraBedPrice = $order->extra_bed_total_price > 0;
        $multipleRooms = $order->number_of_room > 1;
        $canEditOrder = in_array($order->status, ["Draft", "Invalid"]);

        if ($canEditOrder) {
            return view('order.user-edit-order', array_merge([
                'order' => $order,
                'tax' => $tax,
                'now' => $now,
                'usdrates' => $usdrates,
                'business' => $business,
                'room' => $room,
                'hotel' => $hotel,
                'extraBeds' => $extraBeds,
                'optionalrates' => $optionalrates,
                'optional_rate_orders' => $optional_rate_orders,
                'optionalrate_meals' => $optionalrate_meals,
                'tour' => $tour,
                'tour_price' => $tour_price,
                'tour_prices' => $tour_prices,
                'transports' => $transports,
                'airport_shuttle_in' => $airport_shuttle_in,
                'airport_shuttle_out' => $airport_shuttle_out,
                'airport_shuttle_any_zero' => $airport_shuttle_any_zero,
                'total_price_airport_shuttle' => $total_price_airport_shuttle,
                'optionalServiceTotalPrice' => $optionalServiceTotalPrice,
                'qty' => $qty,
                'attentions' => Attention::where('page', 'edit-order')->get(),
                'hasInvalidOrder' => $hasInvalidOrder,
                'showExtraBedPrice' => $showExtraBedPrice,
                'multipleRooms' => $multipleRooms,
                'canEditOrder' => $canEditOrder,
                'services' => $services,
                'promotions_name' => $promotions_name,
                'promotion_discount' => $promotion_discount,
                'agent' => $agent,
                'extra_bed_test' => $extra_bed_test,
            ], $decodedData->toArray()));
        }
        return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
    }
    // VIEW EDIT ORDER TOUR =============================================================================================> OK
    public function edit_order_tour($id)
    {
        $user_id = Auth::id(); 
        $now = Carbon::now();
        $order = Orders::where('sales_agent', $user_id)
        ->where('checkin', '>', $now)
        ->where('id',$id)
        ->first();
        $agent = User::find($order->sales_agent);
        $tour = Tours::find($order->service_id);
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));
        $prices = $tour->prices()->where('status', 'Active')->get()->map(function ($p) use ($usdrates, $tax) {
            return [
                'min_qty' => $p->min_qty,
                'max_qty' => $p->max_qty,
                'price' => $p->calculatePrice($usdrates, $tax),
            ];
        });
        $langType = match (config('app.locale')) {
            'zh' => 'type_traditional',
            'zh-CN' => 'type_simplified',
            default => 'type',
        };
        $langName = match (config('app.locale')) {
            'zh' => 'name_traditional',
            'zh-CN' => 'name_simplified',
            default => 'name',
        };
        $langArea = match (config('app.locale')) {
            'zh' => 'area_traditional',
            'zh-CN' => 'area_simplified',
            default => 'area',
        };
        $langShortDescription = match (config('app.locale')) {
            'zh' => 'short_description_traditional',
            'zh-CN' => 'short_description_simplified',
            default => 'short_description',
        };
        $langDescription = match (config('app.locale')) {
            'zh' => 'description_traditional',
            'zh-CN' => 'description_simplified',
            default => 'description',
        };
        $langItinerary = match (config('app.locale')) {
            'zh' => 'itinerary_traditional',
            'zh-CN' => 'itinerary_simplified',
            default => 'itinerary',
        };
        $langInclude = match (config('app.locale')) {
            'zh' => 'include_traditional',
            'zh-CN' => 'include_simplified',
            default => 'include',
        };
        $langExclude = match (config('app.locale')) {
            'zh' => 'exclude_traditional',
            'zh-CN' => 'exclude_simplified',
            default => 'exclude',
        };
        $langAdditionalInfo = match (config('app.locale')) {
            'zh' => 'additional_info_traditional',
            'zh-CN' => 'additional_info_simplified',
            default => 'additional_info',
        };
        $langCancellationPolicy = match (config('app.locale')) {
            'zh' => 'cancellation_policy_traditional',
            'zh-CN' => 'cancellation_policy_simplified',
            default => 'cancellation_policy',
        };
        $decodedData = collect([
            'nor' => $order->number_of_room,
            'nogr' => json_decode($order->number_of_guests_room, true),
            'guest_detail' => json_decode($order->guest_detail, true),
            'special_day' => json_decode($order->special_day, true),
            'special_date' => json_decode($order->special_date, true),
            'extra_bed' => json_decode($order->extra_bed, true) ?? [],
            'extra_bed_id' => json_decode($order->extra_bed_id, true),
            'extra_bed_price' => json_decode($order->extra_bed_price, true),
        ]);
        $canEditOrder = in_array($order->status, ["Draft", "Invalid"]);

        if ($canEditOrder) {
            return view('order.user-edit-order', array_merge([
                'order' => $order,
                'tax' => $tax,
                'now' => $now,
                'usdrates' => $usdrates,
                'business' => $business,
                'tour' => $tour,
                'prices' => $prices,
                'attentions' => Attention::where('page', 'edit-order-tour')->get(),
                'langType'=>$langType,
                'langName'=>$langName,
                'langArea'=>$langArea,
                'langShortDescription'=>$langShortDescription,
                'langDescription'=>$langDescription,
                'langItinerary'=>$langItinerary,
                'langInclude'=>$langInclude,
                'langExclude'=>$langExclude,
                'langAdditionalInfo'=>$langAdditionalInfo,
                'langCancellationPolicy'=>$langCancellationPolicy,
            ], $decodedData->toArray()));
        }
        return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
    }

    // VIEW ORDER TRANSPORT ==============================================================================================> OK
    public function order_transport(Request $request,$id){
        $now = Carbon::now();
        $orderno = Orders::count() + 1;
        $price = TransportPrice::findOrFail($id);
        $transport = Transports::where('id',$price->transports_id)->first();
        $usdrates = UsdRates::where('name','USD')->first();
        $business = BusinessProfile::where('id','=',1)->first();
        $tax = Tax::where('id',1)->first();
        $transport_price = $price->calculatePrice($usdrates,$tax);
        $normal_price = $transport_price;
        $agents = Auth::user()->where('status',"Active")->get();
        $promotions = Promotion::where('status',"Active")->get();
        if (isset($promotions)){
            $pr = count($promotions);
            $promotion_price = 0;
            for ($i=0; $i < $pr; $i++) { 
                $promotion_price = $promotion_price + $promotions[$i]->discounts;
            }
        }else{
            $promotion_price = 0;
        }
        $bcode = session('bookingcode.code');
        $bdisc = session('bookingcode.discounts');
        $bookingcode = BookingCode::where('code',$bcode)->first();
        $bookingcode_disc = $bookingcode ? $bookingcode->discounts : 0;

        if (isset($bookingcode->code) or isset($promotions)) {
            if (isset($bookingcode->code)) {
                $price_per_pax = $normal_price;
                
                if (isset($promotions)) {
                    $final_price = $normal_price - $bookingcode->discounts - $promotion_price;
                }else{
                    $final_price = $normal_price - $bookingcode->discounts;
                }
            }else{
                $price_per_pax = $normal_price ;
                $final_price = $normal_price  - $promotion_price;
            }
        }else {
            $price_per_pax = $normal_price;
            $final_price = $normal_price;
        }
        return view('form.order-transport',[
            'now'=>$now,
            'orderno'=>$orderno,
            'price'=>$price,
            'transport'=>$transport,
            'usdrates'=>$usdrates,
            'business'=>$business,
            'agents'=>$agents,
            'promotions'=>$promotions,
            'promotion_price'=>$promotion_price,
            'bookingcode'=>$bookingcode,
            'normal_price'=>$normal_price,
            'final_price'=>$final_price,
            'transport_price'=>$transport_price,
            'bookingcode_disc'=>$bookingcode_disc,
            'bookingcode'=>$bookingcode,
        ]);
    }
    // FUNCTION USER CREATE ORDER TRANSPORT =========================================================================> OK
    public function func_create_order_transport(Request $request,$id){
        $user = Auth::user();
        $developerRoles = ["developer", "reservation", "author"];
        if (in_array($user->position, $developerRoles)) {
            $sales_agent = $request->user_id;
            $status = "Pending";
        } else {
            $sales_agent = $user->id;
            $status = "Draft";
        }
        $user_id = $user->id;
        $email = $user->email;
        $name = $user->name;
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $cnyrates = Cache::remember('cny_rate', 3600, fn() => UsdRates::where('name', 'CNY')->first());
        $twdrates = Cache::remember('twd_rate', 3600, fn() => UsdRates::where('name', 'TWD')->first());
        $idrrates = Cache::remember('idr_rate', 3600, fn() => UsdRates::where('name', 'IDR')->first());

        $transport = Transports::find($request->transport_id);
        $transport_price = TransportPrice::find($id);
        $service = "Transport";
        $service_id = $transport->id;
        $service_type = $transport->type;
        $service_name = $transport->brand." ".$transport->name;

        $promotions = Promotion::where('periode_start','<=',$now)->where('periode_end','>=',$now)->where('status','Active')->get();
        $promotions_id = json_encode($promotions->pluck('id'));
        $promotions_name = json_encode($promotions->pluck('name'));
        $promotions_discount = json_encode($promotions->pluck('discounts'));
        $total_promotions_discount = $promotions->sum('discounts');
        
        $bcode = session('bookingcode.code');
        $bdisc = session('bookingcode.discounts');
        $bookingcode = BookingCode::where('code',$bcode)->first();
        $bookingcode_disc = $bdisc ? $bdisc : 0;
        $bookingcode_id = $bookingcode ? $bookingcode->id : null;
        if ($bookingcode) {
            $bookingcode_used = $bookingcode->used + 1;
            $bookingcode->update([
                "used"=>$bookingcode_used,
            ]);
        }
       
        
        $duration = $request->duration;
        $price_pax = $transport_price->calculatePrice($usdrates,$tax);
        if ($transport_price->type == "Daily Rent") {
            $checkin = Carbon::parse($request->pickup_date)->format('Y-m-d H:i');
            $checkinTime = Carbon::parse($checkin);
            $normal_price = $price_pax * $duration;
            $price_total = $normal_price;
            $final_price = $price_total - $total_promotions_discount - $bookingcode_disc;
            $checkout = $checkinTime->addDays($duration);
        } elseif ($transport_price->type == "Airport Shuttle") {
            if ($request->airport_shuttle_type == "Arrival") {
                $checkin = Carbon::parse($request->arrival_time);
            }else{
                $checkin = Carbon::parse($request->departure_time);
            }
            $checkinTime = Carbon::parse($checkin);
            $normal_price = $price_pax;
            $price_total = $normal_price;
            $final_price = $price_total - $total_promotions_discount - $bookingcode_disc;
            $checkout = $checkinTime->addHours($duration);
        }else{
            $checkin = Carbon::parse($request->pickup_date)->format('Y-m-d H:i');
            $checkinTime = Carbon::parse($checkin);
            $normal_price = $price_pax;
            $price_total = $normal_price;
            $final_price = $price_total - $total_promotions_discount - $bookingcode_disc;
            $checkout = $checkinTime->addHours($duration);
        }

        $guest_detail = $request->guest_detail;
        $number_of_guests = $request->number_of_guests;
        $include = $transport->include;
        $additional_info = $transport->additional_info;
        $cancellation_policy = $transport->cancellation_policy;
        $order_tax = 0;
        if ($request->airport_shuttle_type == "Arrival") {
            $airport_shuttle_in = $transport->id;
            $airport_shuttle_out = null;
            $pickup_date = $checkin;
            $pickup_location = "Airport";
            $dropoff_date = $checkout;
            $dropoff_location = $transport_price->dst;
        }elseif ($request->airport_shuttle_type == "Departure") {
            $flightTime = Carbon::parse($request->departure_time);
            $pickup_date = date('Y-m-d H:i',strtotime('-'.($duration + 2).'hours',strtotime($request->departure_time)));
            $airport_shuttle_in = null;
            $airport_shuttle_out = $transport->id;
            $pickup_location =  $transport_price->dst;
            $dropoff_date = date('Y-m-d H:i',strtotime('+'.($duration).'hours',strtotime($pickup_date)));
            $dropoff_location = "Airport";
        }else{
            $airport_shuttle_in = null;
            $airport_shuttle_out = null;
            $pickup_date = $checkin;
            $pickup_location =  $request->pickup_location;
            $dropoff_date = $checkout;
            $dropoff_location = $request->dropoff_location;
        }
        $order =new Orders([
            "user_id"=>$user->id,
            "name"=>$name,
            "email"=>$email,
            "orderno"=>$request->orderno,
            "service"=>$service,
            "service_id"=>$service_id,
            "service_type"=>$request->airport_shuttle_type,
            "servicename" =>$service_name,
            "subservice"=>$transport_price->type,
            "subservice_id"=>$transport_price->id,
            "number_of_guests"=>$number_of_guests,
            "guest_detail"=>$guest_detail,
            "extra_time"=>$transport_price->extra_time,
            "price_id"=>$transport_price->id,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "src"=>$transport_price->src,
            "dst"=>$transport_price->dst,
            "sales_agent"=>$sales_agent,
            "pickup_name"=>$request->pickup_name,
            "bookingcode"=>$bookingcode_id,
            "bookingcode_disc"=>$bookingcode_disc,
            "capacity"=>$transport->capacity,
            "include" =>$include,
            "additional_info"=>$additional_info,
            "cancellation_policy"=>$cancellation_policy,
            "duration"=>$duration,
            "price_total" =>$price_total, 
            "promotion" =>$promotions_name, 
            "promotion_disc" =>$promotions_discount, 
            "final_price" =>$final_price, 
            "usd_rate" =>$usdrates->rate, 
            "cny_rate" =>$cnyrates->rate, 
            "twd_rate" =>$twdrates->rate, 
            "normal_price" =>$normal_price,
            "price_pax" =>$price_pax,
            "arrival_flight" =>$request->arrival_flight,
            "arrival_time" =>$request->arrival_time,
            "airport_shuttle_in" =>$airport_shuttle_in,
            "departure_flight" =>$request->departure_flight,
            "departure_time" =>$request->departure_time,
            "airport_shuttle_out" =>$airport_shuttle_out,
            "pickup_location" =>$pickup_location,
            "pickup_date" =>$pickup_date,
            "dropoff_date" =>$dropoff_date,
            "dropoff_location" =>$dropoff_location,
            "status"=>$status,
            "note"=>$request->note,
        ]);
        // dd($order);
        $order->save();
        
        $note = "Created Order with order no: ".$request->orderno;
        $user_log =new UserLog([
            "action"=>"Create Order",
            "service"=>"Transport",
            "subservice"=>$order->subservice,
            "subservice_id"=>$order->id,
            "page"=>"order-transport",
            "user_id"=>$user->id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Create Order",
            "url"=>$request->getClientIp(),
            "method"=>"Create",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        $subject = $request->orderno;
        if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
            $rquotation = $request->request_quotation;
            Mail::to(config('app.reservation_mail'))->send(new ReservationMail($order->id,$rquotation));
            return redirect('/orders-admin-'.$order->id)->with('success', __('messages.The order has been successfully created'));
        }else{
            return redirect()->route('view.edit-order-transport', ['id' => $order->id])->with('success', __('messages.Your order has been added to the order basket. Please ensure that all details are entered correctly before you confirm the order for further processing.'));
        }
    }
    // VIEW EDIT ORDER TRANSPORT =============================================================================================> OK
    public function edit_order_transport($id)
    {
        $user_id = Auth::id(); 
        $now = Carbon::now();
        $order = Orders::where('sales_agent', $user_id)
            ->where('checkin', '>', $now)
            ->where('id',$id)
            ->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));
        $transport = Transports::with(['prices'])->find($order->service_id);
        $transports = Transports::with('prices')
            ->where('status', 'Active')
            ->orderByDesc('capacity')
            ->get();
        $price = TransportPrice::find($order->price_id);
        $promotionName = json_decode($order->promotion);
        $promotion_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc)) : null;
        $promotions_name = $promotionName ? implode(', ',$promotionName): null;
        $canEditOrder = in_array($order->status, ["Draft", "Invalid"]);
        if ($canEditOrder) {
            return view('order.user-edit-order', [
                'order' => $order,
                'tax' => $tax,
                'now' => $now,
                'usdrates' => $usdrates,
                'business' => $business,
                'transport' => $transport,
                'transports' => $transports,
                'attentions' => Attention::where('page', 'edit-order')->get(),
                'canEditOrder' => $canEditOrder,
                'promotions_name' => $promotions_name,
                'promotion_discount' => $promotion_discount,
                'price' => $price,
            ]);
        }
        return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
    }
    // FUNCTION =============================================================================================================> OK
    public function func_submit_order_transport(Request $request,$id){
        $user = Auth::user();
        $order=Orders::where('id',$id)
            ->where('sales_agent',$user->id)
            ->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $duration = $request->duration;

        $total_promotions_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc,true)):0;
        $bookingcode_disc = $order->bookingcode_disc ? $order->bookingcode_disc : 0;
        $additional_service_price = $order->additional_service_price ? array_sum(json_decode($order->additional_service_price,true)) : 0;
        $status = "Pending";
        $duration = $request->duration;
        $price_pax = $order->price_pax;
        $transport = Transports::find($order->service_id);
        $transport_price = TransportPrice::find($order->subservice_id);
        if ($order->subservice == "Daily Rent") {
            $checkin = Carbon::parse($request->pickup_date)->format('Y-m-d H:i');
            $checkinTime = Carbon::parse($checkin);
            $normal_price = $price_pax * $duration;
            $price_total = $normal_price + $additional_service_price;
            $final_price = $price_total - $total_promotions_discount - $bookingcode_disc;
            $checkout = $checkinTime->addDays($duration);
        } elseif ($order->subservice == "Airport Shuttle") {
            if ($request->airport_shuttle_type == "Arrival") {
                $checkin = Carbon::parse($request->arrival_time)->format('Y-m-d H:i');
            }else{
                $checkin = Carbon::parse($request->departure_time)->format('Y-m-d H:i');
            }
            $checkinTime = Carbon::parse($checkin);
            $normal_price = $price_pax;
            $price_total = $normal_price + $additional_service_price;
            $final_price = $price_total - $total_promotions_discount - $bookingcode_disc;
            $checkout = $checkinTime->addHours($duration);
        }else{
            $checkin = Carbon::parse($request->pickup_date)->format('Y-m-d H:i');
            $checkinTime = Carbon::parse($checkin);
            $normal_price = $price_pax;
            $price_total = $normal_price + $additional_service_price;
            $final_price = $price_total - $total_promotions_discount - $bookingcode_disc;
            $checkout = $checkinTime->addHours($duration);
        }

        if ($request->airport_shuttle_type == "Arrival") {
            $airport_shuttle_in = $order->service_id;
            $airport_shuttle_out = null;
            $pickup_date = $checkin;
            $pickup_location = "Airport";
            $dropoff_date = $checkout;
            $dropoff_location = $order->dropoff_location;
        }elseif ($request->airport_shuttle_type == "Departure") {
            $pickup_date = date('Y-m-d H:i',strtotime('-'.($duration + 2).'hours',strtotime($request->departure_time)));
            $airport_shuttle_in = null;
            $airport_shuttle_out = $transport->id;
            $pickup_location =  $transport_price->dst;
            $dropoff_date = date('Y-m-d H:i',strtotime('+'.($duration).'hours',strtotime($pickup_date)));
            $dropoff_location = "Airport";
        }else{
            $airport_shuttle_in = null;
            $airport_shuttle_out = null;
            $pickup_date = $checkin;
            $pickup_location =  $request->pickup_location;
            $dropoff_date = $checkout;
            $dropoff_location = $request->dropoff_location;
        }
        // dd($request->pickup_date, $checkin, $checkout);
        $order->update([
            "status"=>$status,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "guest_detail"=>$request->guest_detail,
            "note"=>$request->note,
            "number_of_guests"=>$request->number_of_guests,
            "pickup_name"=>$request->pickup_name,
            "pickup_phone"=>$request->pickup_phone,
            "service_type"=>$request->airport_shuttle_type,
            "duration"=>$duration,
            "price_total" =>$price_total, 
            "final_price" =>$final_price, 
            "normal_price" =>$normal_price,
            "arrival_flight" =>$request->arrival_flight,
            "arrival_time" =>$request->arrival_time,
            "airport_shuttle_in" =>$airport_shuttle_in,
            "departure_flight" =>$request->departure_flight,
            "departure_time" =>$request->departure_time,
            "airport_shuttle_out" =>$airport_shuttle_out,
            "pickup_location" =>$pickup_location,
            "pickup_date" =>$pickup_date,
            "dropoff_date" =>$dropoff_date,
            "dropoff_location" =>$dropoff_location,
        ]);
        // dd($order);
        $rquotation = $request->request_quotation;
        $agent = User::where('id',$order->user_id)->first();
        Mail::to(config('app.reservation_mail'))->send(new ReservationMail($id,$rquotation));
        $note = "Submited order no: ".$order->orderno;
        
        $user_log =new UserLog([
            "action"=>"Submit Order",
            "service"=>$order->service,
            "subservice"=>$order->subservice,
            "subservice_id"=>$id,
            "page"=>"edit-order-transport",
            "user_id"=>Auth::user()->id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>'Submit Order',
            "url"=>$request->getClientIp(),
            "method"=>"Submit",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/detail-order-transport/$order->id")->with('success','Your order has been submited, and we will validate your order');
    }
    // VIEW DETAIL ORDER TRANSPORT ===============================================================================================> OK
    public function detail_order_transport($id)
    {
        $user_id = Auth::user()->id; 
        $now = Carbon::now();
        $order = Orders::with(['optional_rate_orders', 'reservations.invoice'])
            ->where('sales_agent', $user_id)
            ->where('checkin', '>', $now)
            ->where('id',$id)
            ->first();
        if (!$order || in_array($order->status, ["Draft", "Invalid", "Rejected"])) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $agent = User::find($order->sales_agent);
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::firstWhere('name', 'USD'));
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $attentions = Cache::remember('attention', 3600, fn() => Attention::where('page', 'orders')->get());
        $room = HotelRoom::find($order->subservice_id);
        $reservation = Reservation::find($order->rsv_id);
        $invoice = InvoiceAdmin::with(['payment'])->firstWhere('rsv_id', $order->rsv_id);
        if ($invoice) {
            $doku_payment = DokuVirtualAccount::where('invoice_id', $invoice->id)
            ->where('expired_date','>=',$now)
            ->orderBy('expired_date', 'desc')
            ->first();
            $receipts = PaymentConfirmation::where('inv_id', $invoice->id)->get();
            $doku_payment_paid = DokuVirtualAccount::where('invoice_id', $invoice->id)
            ->where('status', 'Paid')
            ->first();
        }else{
            $doku_payment_paid = null;
            $doku_payment = null;
            $receipts = null;
        }
        $promotion_discounts = json_decode($order->promotion_disc, true);
        $total_promotion_disc = $promotion_discounts ? array_sum($promotion_discounts) : 0;
        $bookingcode_disc = $order->bookingcode_disc > 0 ? $order->bookingcode_disc : 0;
        $discounts = $order->discounts > 0 ? $order->discounts : 0;
        $transport = Transports::find($order->service_id);
        $normal_price = $order->final_price + $total_promotion_disc + $order->bookingcode_disc + $order->discounts;
        $decodedData = collect([
            'number_of_guests_room' => json_decode($order->number_of_guests_room, true),
            'guest_details' => json_decode($order->guest_detail, true),
            'special_days' => json_decode($order->special_day, true),
            'special_dates' => json_decode($order->special_date, true),
            'extra_beds' => json_decode($order->extra_bed, true),
            'extra_bed_prices' => json_decode($order->extra_bed_price, true),
            'additional_services' => json_decode($order->additional_service, true),
            'additional_services_date' => json_decode($order->additional_service_date, true),
            'additional_services_qty' => json_decode($order->additional_service_qty, true),
            'additional_services_price' => json_decode($order->additional_service_price, true),
            
        ]);
        $additional_services_data = collect($decodedData['additional_services'])->map(function ($service, $index) use ($decodedData) {
            return [
                'date' => $decodedData['additional_services_date'][$index] ?? null,
                'service' => $service,
                'qty' => $decodedData['additional_services_qty'][$index] ?? 0,
                'price' => $decodedData['additional_services_price'][$index] ?? 0,
            ];
        });
        $additionalServices = $additional_services_data->map(function ($service) {
            return [
                'date' => dateFormat($service['date']),
                'service' => $service['service'],
                'qty' => $service['qty'],
                'price' => $service['price'],
                'total' => $service['qty'] * $service['price'],
            ];
        });
        $additional_service_total_price = $additionalServices->sum(fn($service) => str_replace(".", "", $service['total']));
        $discounts = [
            'bookingcode_disc' => __('messages.Booking Code'),
            'discounts' => __('messages.Discounts'),
            'kick_back' => __('messages.Kick Back'),
            'promotion_disc' => __('messages.Promotion'),
        ];
        
        $total_price_idr = $order->final_price * $usdrates->rate;
        $taxDoku = TaxDoku::find('1');
        $tax_doku = floor($total_price_idr * $taxDoku->tax_rate);
        $doku_total_price = $total_price_idr + $tax_doku;
        return view('order.user-detail-order',[
            'order' => $order,
            'tax' => $tax,
            'now' => $now,
            'usdrates' => $usdrates,
            'business' => $business,
            'attentions' => $attentions,
            'invoice' => $invoice,
            'reservation' => $reservation,
            'total_promotion_disc' => $total_promotion_disc,
            'normal_price' => $normal_price,
            'receipts' => $receipts,
            'bookingcode_disc' => $bookingcode_disc,
            'discounts' => $discounts,
            'transport' => $transport,
            'additionalServices' => $additionalServices,
            'additional_service_total_price' => $additional_service_total_price,
            'agent' => $agent,
            'doku_payment' => $doku_payment,
            'doku_payment_paid' => $doku_payment_paid,
            'tax_doku' => $tax_doku,
            'taxDoku' => $taxDoku,
            'doku_total_price' => $doku_total_price,
            'total_price_idr' => $total_price_idr,
        ]);
    }

    // USER EDIT ORDER ROOM ==============================================================================================> OK
    public function edit_order_room($id)
    {   
        $agent = Auth::user();
        $order = Orders::where('sales_agent', $agent->id)
            ->whereIn('status', ['Draft', 'Invalid'])
            ->where('id',$id)
            ->first();
        if (!$order) {
            return redirect('/orders')->with('error', __('messages.Your order was not found').'!');
        }
        $now = Carbon::now();
        $usdrates = Cache::remember('usd_rates', 60, fn() => UsdRates::where('name', 'USD')->first());
        $tax = Cache::remember('tax', 60, fn() => Tax::where('id', 1)->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $attentions = Attention::where('page', 'editorder-room')->get();
        $hotel = Hotels::find($order->service_id);
        $room = HotelRoom::find($order->subservice_id);
        $duration = Carbon::parse($order->checkin)->diffInDays(Carbon::parse($order->checkout));
        $extrabeds = $hotel->extrabeds->map(fn($eb) => tap($eb, function ($eb) use ($usdrates, $tax, $order) {
            $eb->price = $eb->calculatePrice($usdrates, $tax) * $order->duration;
        }));
        $date_stay = collect(range(0, $duration - 1))->map(fn($a) => date('Y-m-d', strtotime("+$a days", strtotime($order->checkin))));
        $decodedData = [
            'nor' => $order->number_of_room,
            'nogr' => json_decode($order->number_of_guests_room),
            'guest_name' => json_decode($order->guest_detail),
            'guest_detail' => json_decode($order->guest_detail),
            'special_day' => json_decode($order->special_day),
            'special_date' => json_decode($order->special_date),
            'extra_bed' => json_decode($order->extra_bed),
            'extra_bed_id' => json_decode($order->extra_bed_id),
            'extra_bed_price' => json_decode($order->extra_bed_price),
            'price_pax' => json_decode($order->price_pax),
        ];
        return view('order.edit-room', array_merge([
            'order' => $order,
            'extrabeds' => $extrabeds,
            'tax' => $tax,
            'now' => $now,
            'usdrates' => $usdrates,
            'business' => $business,
            'attentions' => $attentions,
            'hotel' => $hotel,
            'room' => $room,
            'date_stay' => $date_stay,
        ], $decodedData));
    }

    // Function Update Order Room ========================================================================================> OK
    public function func_update_order_room(Request $request,$id){
        $user = Auth::user();
        $order=Orders::where('sales_agent',$user->id)->where('id',$id)->first();
        if (!$order) {
            return redirect('/orders')->with('error', __('messages.Your order was not found').'!');
        }
        $croom = count($request->number_of_guests_room);
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $duration = $order->duration;
        $optional_price = $order->optional_price;
        $price_pax = $order->price_pax;
        $kick_back = ($order->kick_back_per_pax * $duration)*$croom;
        
        if ($request->number_of_guests_room > 0) {
            $number_of_guests = array_sum($request->number_of_guests_room);
            $extra_bed_proses = [];
            foreach ($request->number_of_guests_room as $jk) {
                if ($jk <= $order->capacity ) {
                    array_push($extra_bed_proses,'No');
                }else{
                    array_push($extra_bed_proses,'Yes');
                }
            }

            $extra_bed_id_price = [];          
            $extraBedId = [];          
            for ($i=0; $i < $croom; $i++) { 
                if ($extra_bed_proses[$i] == "Yes") {
                    if ($request->extra_bed_id[$i]) {
                        $extraBed = ExtraBed::find($request->extra_bed_id[$i]);
                        if ($extraBed) {
                            $price_extra_bed = ($extraBed->calculatePrice($usdrates, $tax)) * $duration; 
                            array_push($extra_bed_id_price,$price_extra_bed);
                            array_push($extraBedId,$request->extra_bed_id[$i]);
                        }else{
                            array_push($extra_bed_id_price,0);
                            array_push($extraBedId,$request->extra_bed_id[$i]);
                        }
                    }else{
                        $extraBed = ExtraBed::where('hotels_id',$order->service_id)->first();
                        $price_extra_bed = ($extraBed->calculatePrice($usdrates, $tax)) * $duration; 
                        if ($extraBed) {
                            array_push($extra_bed_id_price,$price_extra_bed);
                            array_push($extraBedId,$extraBed->id);
                        }else{
                            array_push($extra_bed_id_price,0);
                            array_push($extraBedId,null);
                        }
                    } 
                }else{
                    array_push($extra_bed_id_price,0);
                    array_push($extraBedId,null);
                }
            }
            $extra_bed_id = json_encode($extraBedId);
            $extra_bed_price = json_encode($extra_bed_id_price);
            $extra_bed_process = json_encode($extra_bed_proses);
            $guest_detail = json_encode($request->guest_detail);
            $special_day = json_encode($request->special_day);
            $special_date = json_encode($request->special_date);
            $pro_disc = json_decode($order->promotion_disc);
            $number_of_guests_room = json_encode($request->number_of_guests_room);
            $total_extra_bed = array_sum($extra_bed_id_price);
            if (isset($pro_disc)) {
                $promotion_disc = array_sum($pro_disc);
            }else{
                $promotion_disc = 0;
            }
            
            $price_pax = $order->price_pax;
            $price_total = ($price_pax * $croom) + $total_extra_bed;
            $final_price = ((($price_total + $optional_price + $order->additional_service_price + $order->airport_shuttle_price) - $order->discounts) - $order->bookingcode_disc) - $promotion_disc;
        
        }else{
            $number_of_guests = 0;
            $number_of_guests_room = 0;
            $croom = 0;
            $extra_bed_proses = 0;
            $extra_bed_id = 0;
            $extra_bed_price = 0;
            $extra_bed_process = 0;
            $price_total = 0;
            $kick_back = 0;
            $guest_detail = 0;
            $special_day = 0;
            $special_date = 0;
            $normal_price = 0;
            $airport_shuttle_price = 0;
            $final_price = 0;
        }

        $order->update([
            "number_of_guests"=>$number_of_guests,
            "number_of_guests_room"=>$number_of_guests_room,
            "number_of_room"=>$croom,
            "guest_detail"=>$guest_detail,
            "request_quotation"=>$request->request_quotation,
            "extra_bed"=>$extra_bed_process,
            "extra_bed_id"=>$extra_bed_id,
            "extra_bed_price"=>$extra_bed_price,
            "extra_bed_total_price"=>$total_extra_bed,
            "special_day"=>$special_day,
            "special_date"=>$special_date,
            "price_total"=>$price_total,
            "final_price"=>$final_price,
            "kick_back"=>$kick_back,
        
        ]);
        return redirect()->route('view.edit-order-hotel', ['id' => $id])->with('success',__('messages.Your order has been updated'));
    }
    

    // VIEW USER EDIT ORDER ADDITIONAL CHARGE =============================================================================> OK
    public function edit_order_additional_charge($id){   
        $user_id = Auth::id();
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $logoDark = Cache::remember('app.logo_dark', 3600, fn() => config('app.logo_dark'));
        $altLogo = Cache::remember('app.alt_logo', 3600, fn() => config('app.alt_logo'));
        $attentions = Attention::where('page', 'order-hotel-promo')->get();

        $order = Orders::with(['optional_rate_orders'])->where('id', $id)->where('sales_agent', $user_id)->first();
        $optional_rate_orders = $order->optional_rate_orders;
        $optional_services = OptionalRate::where('hotels_id', $order->service_id)->get();
        $in=Carbon::parse($order->checkin);
        $out=Carbon::parse($order->checkout);
        $duration = $in->diffInDays($out);
        $date_stay = collect(range(0, $duration - 1))
                ->map(fn($a) => Carbon::parse($in)->addDays($a)->toDateString());
                
        $order_wedding = OrderWedding::where('id',$order->wedding_order_id)->first();
        
        if ($order != "" or $order->status != "Pending" or $order->status != "Active"){
            return view('order.edit-order-additional-charge',compact('order'),[
                'tax'=>$tax,
                'now'=>$now,
                'usdrates'=>$usdrates,
                'business'=>$business,
                'order'=>$order,
                'attentions'=>$attentions,
                'optional_rate_orders'=>$optional_rate_orders,
                'duration'=>$duration,
                'optional_services'=>$optional_services,
                'order_wedding'=>$order_wedding,
                'date_stay'=>$date_stay,
            ]);
        }else{
            return redirect('/orders')->with('error',__('messages.Your order was not found').'!');
        }
  
    }

    // FUNCTION CREATE ORDER ADDITIONAL CHARGE ============================================================================> OK
    public function func_create_order_additional_charge(Request $request, $id)
    {
        $now = Carbon::now();
        $cacheTTL = 3600;
        $tax = Cache::remember('tax_1', $cacheTTL, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', $cacheTTL, fn() => UsdRates::where('name', 'USD')->first());
        $agent = Auth::user();
        $order = Orders::where('sales_agent', $agent->id)
            ->whereIn('status', ['Draft', 'Invalid', 'Rejected'])
            ->where('id',$id)->first();
        if (!$order) {
            return redirect("/orders")->with('warning', __('messages.Your order cannot be changed'));
        }
        $optional_rates = OptionalRate::where('hotels_id', $order->service_id)->get();
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
        $promotion_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc)) : 0;
        $final_price = $order->price_total + $total_optional_rate - $order->discounts - $order->bookingcode_disc - $promotion_discount + $order->airport_shuttle_price;
        $order->update([
            "optional_price" => $total_optional_rate,
            "final_price" => $final_price,
        ]);
        OrderLog::create([
            "order_id" => $order->id,
            "action" => "Create order additional charge",
            "url" => $request->getClientIp(),
            "method" => "Create",
            "agent" => $agent->name,
            "admin" => $agent->id,
        ]);
        return redirect()->route('view.edit-order-hotel', ['id' => $order->id])
            ->with('success', __('messages.Your order has been updated'));
    }


     // FUNCTION UPDATE ORDER ADDITIONAL CHARGE ============================================================================> OK
     public function func_update_order_additional_charge(Request $request,$id){
        $now = Carbon::now();
        $cacheTTL = 3600;
        $tax = Cache::remember('tax_1', $cacheTTL, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', $cacheTTL, fn() => UsdRates::where('name', 'USD')->first());
        $agent = Auth::user();
        $request->validate([
            'optional_rate_order_id' => 'array',
            'optional_rate_id' => 'required|array',
            'number_of_guest' => 'required|array',
            'service_date' => 'required|array',
        ]);
        $order = Orders::with(['optional_rate_orders.optional_rate'])
            ->where('sales_agent', $agent->id)
            ->where('checkin', '>=', $now)
            ->whereIn('status', ['Draft', 'Invalid'])
            ->where('id',$id)
            ->first();
        if (!$order) {
            return redirect("/hotel-promo-edit-order/$order->id")
                ->with('warning', __('messages.Your order cannot be changed'));
        }
        if (in_array($order->status, ['Draft', 'Invalid'])){
            DB::transaction(function () use ($request, $order, $usdrates, $tax, $agent, $id) {
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
                            'order_id' => $id,
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
                $promotion_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc)) : 0;
                $final_price = $order->price_total + $total_optional_rate - $order->discounts - $order->bookingcode_disc - $promotion_discount + $order->airport_shuttle_price;
                $order->update([
                    "optional_price" => $total_optional_rate,
                    "final_price" => $final_price,
                ]);
                OrderLog::create([
                    "order_id" => $order->id,
                    "action" => "Update order optional rate",
                    "url" => request()->ip(),
                    "method" => "Update",
                    "agent" => $agent->name,
                    "admin" => Auth::id(),
                ]);
            });
            return redirect()->route('view.edit-order-hotel', ['id' => $order->id])->with('success',__('messages.Your order has been updated'));
        }else{
            return redirect("/orders")->with('warning', __('messages.Your order cannot be changed'));
        }
    }


    // FUNCTION DELETE ORDER ADDITIONAL CHARGE =============================================================================> OK
    public function func_delete_order_additional_charge($id)
    {
        $user = Auth::user();
        $order_optional_rate = OptionalRateOrder::findOrFail($id);
        $order_id = $order_optional_rate->order_id;
        $order = Orders::where('id',$order_id)->first();
        if (!$order) {
            return redirect('/orders')->with('error', __('messages.Your order was not found').'!');
        }
        $order_optional_rate->delete();
        $optional_rate_price = OptionalRateOrder::where('orders_id', $order_id)->sum('price_total');
        $promotion_discount = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc)) : 0;
        $final_price = $order->price_total + $optional_rate_price - $order->discounts - $order->bookingcode_disc - $promotion_discount + $order->airport_shuttle_price;
        $order->update([
            "optional_price" => $optional_rate_price,
            "final_price" => $final_price,
        ]);
        return response()->json(['success' => true]);
        
    }    

    // FUNCTION SUBMIT ORDER ================================================================================================> OK
    public function func_submit_order_hotel(Request $request, $id)
    {
        $now = Carbon::now();
        $cacheTTL = 3600;
        $tax = Cache::remember('tax_1', $cacheTTL, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', $cacheTTL, fn() => UsdRates::where('name', 'USD')->first());
        $agent = Auth::user();
        $order = Orders::select('id', 'service_id', 'number_of_guests','price_total','optional_price','additional_service_price','airport_shuttle_price','discounts','bookingcode_disc','promotion_disc','final_price')
            ->where('sales_agent', $agent->id)
            ->where(function ($query) {
                $query->where('status', 'Draft')->orWhere('status', 'Invalid');
            })
            ->where('id', $id)
            ->first();

        if (!$order) {
            return redirect("/orders")->with('warning', __('messages.Your order cannot be changed'));
        }

        $hotel = Hotels::find($order->service_id);
        $number_of_guests = $order->number_of_guests;

        DB::transaction(function () use ($request, $order, $hotel, $number_of_guests, $agent) {
            $price_in = 0;
            $price_out = 0;
            if ($request->airport_shuttle_in) {
                $date_in = Carbon::parse($request->arrival_time)->format('Y-m-d H:i');
                $price_in = $request->airport_shuttle_in_price ?: 0;
                $arrival_flight = $request->arrival_flight ? $request->arrival_flight : "-";
                AirportShuttle::updateOrCreate(
                    ['order_id' => $order->id, 'nav' => "In"],
                    [
                        'date' => $date_in,
                        'flight_number' => $arrival_flight,
                        'number_of_guests' => $number_of_guests,
                        'transport_id' => $request->airport_shuttle_in,
                        'price_id' => $request->transport_in_price_id,
                        'src' => "Airport",
                        'dst' => $hotel->name,
                        'duration' => $hotel->airport_duration,
                        'distance' => $hotel->airport_distance,
                        'price' => $price_in,
                    ]
                );
            } else {
                AirportShuttle::where('order_id', $order->id)->where('nav', 'In')->delete();
            }
            if ($request->airport_shuttle_out) {
                $date_out = Carbon::parse($request->departure_time)->format('Y-m-d H:i');
                $price_out = $request->airport_shuttle_out_price ?: 0;
                $departure_flight = $request->departure_flight ? $request->departure_flight : "-";
                AirportShuttle::updateOrCreate(
                    ['order_id' => $order->id, 'nav' => "Out"],
                    [
                        'date' => $date_out,
                        'flight_number' => $departure_flight,
                        'number_of_guests' => $number_of_guests,
                        'transport_id' => $request->airport_shuttle_out,
                        'price_id' => $request->transport_out_price_id,
                        'src' => $hotel->name,
                        'dst' => "Airport",
                        'duration' => $hotel->airport_duration,
                        'distance' => $hotel->airport_distance,
                        'price' => $price_out,
                    ]
                );
            } else {
                AirportShuttle::where('order_id', $order->id)->where('nav', 'Out')->delete();
            }
            $additional_service_price = $order->additional_service_price ? array_sum(json_decode($order->additional_service_price)) : 0;
            $airport_shuttle_price = $price_in + $price_out;
            $promotion_disc = $order->promotion_disc ? array_sum(json_decode($order->promotion_disc)) : 0;
            $final_price = ($order->price_total + $order->optional_price + $additional_service_price + $airport_shuttle_price) - $order->discounts - $order->bookingcode_disc - $promotion_disc;
            $order->update([
                "airport_shuttle_price" => $airport_shuttle_price ?: null,
                "final_price" => $final_price,
                "arrival_flight" => $request->arrival_flight,
                "arrival_time" => $request->arrival_time ? Carbon::parse($request->arrival_time)->format('Y-m-d H:i') : null,
                "airport_shuttle_in" => $request->airport_shuttle_in,
                "departure_flight" => $request->departure_flight,
                "departure_time" => $request->departure_time ? Carbon::parse($request->departure_time)->format('Y-m-d H:i') : null,
                "airport_shuttle_out" => $request->airport_shuttle_out,
                "note" => $request->note,
                "request_quotation" => $request->request_quotation,
                "status" => 'Pending',
            ]);
            OrderLog::create([
                "order_id" => $order->id,
                "action" => "Submit Order ".$order->service,
                "url" => request()->ip(),
                "method" => "Submit",
                "agent" => $agent->name,
                "admin" => Auth::id(),
            ]);
            Mail::to(config('app.reservation_mail'))->send(new ReservationMail($order->id, $request->request_quotation));
        });
        return redirect()->route('view.detail-order-hotel', ['id' => $id])->with('success', __('messages.Your order has been submitted'));
    }


    
    // VIEW DETAIL ORDER HOTEL ===============================================================================================> OK
    public function detail_order_hotel($id)
    {
        $user_id = Auth::id(); 
        $now = Carbon::now();
        $order = Orders::with(['optional_rate_orders', 'reservations.invoice'])
            ->where('sales_agent', $user_id)
            ->where('checkin', '>', $now)
            ->where('id',$id)
            ->first();
        if (!$order || in_array($order->status, ["Draft", "Invalid", "Rejected"])) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $agent = User::find($order->sales_agent);
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::firstWhere('name', 'USD'));
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));
        $attentions = Cache::remember('attention', 3600, fn() => Attention::where('page', 'orders')->get());
        $room = HotelRoom::find($order->subservice_id);
        $hotel = Hotels::with(['optionalrates'])->where('id',$order->service_id)->first();
        if ($hotel) {
            $optional_rate = $hotel->optionalrates;
        }else{
            $optional_rate = NULL;
        }
        $airport_shuttles = AirportShuttle::where('order_id', $order->id)->get();
        $airport_shuttle_any_zero = $airport_shuttles->contains(fn($shuttle) => $shuttle->price == 0);
        $total_price_airport_shuttle = $airport_shuttles->sum('price');
        $optional_rate_orders = $order->optional_rate_orders;
        $optionalServiceTotalPrice = $optional_rate_orders->sum('price_total');
        $reservation = Reservation::find($order->rsv_id);
        $invoice = InvoiceAdmin::firstWhere('rsv_id', $order->rsv_id);
        $receipts = $invoice ? $invoice->payment : null;
        if ($invoice) {
            $doku_payment = DokuVirtualAccount::where('invoice_id', $invoice->id)
            ->where('expired_date','>=',$now)
            ->orderBy('expired_date', 'desc')
            ->first();
            $doku_payment_paid = DokuVirtualAccount::where('invoice_id', $invoice->id)
            ->where('status', 'Paid')
            ->first();
        }else{
            $doku_payment_paid = null;
            $doku_payment = null;
        }
        $decodedData = collect([
            'number_of_guests_room' => json_decode($order->number_of_guests_room, true),
            'guest_details' => json_decode($order->guest_detail, true),
            'special_days' => json_decode($order->special_day, true),
            'special_dates' => json_decode($order->special_date, true),
            'extra_beds' => json_decode($order->extra_bed, true),
            'extra_bed_prices' => json_decode($order->extra_bed_price, true),
            'extra_bed_total_prices' => json_decode($order->extra_bed_total_price, true),
            'additional_services' => json_decode($order->additional_service, true),
            'additional_services_date' => json_decode($order->additional_service_date, true),
            'additional_services_qty' => json_decode($order->additional_service_qty, true),
            'additional_services_price' => json_decode($order->additional_service_price, true),
            
        ]);
        $additional_services_data = collect($decodedData['additional_services'])->map(function ($service, $index) use ($decodedData) {
            return [
                'date' => $decodedData['additional_services_date'][$index] ?? null,
                'service' => $service,
                'qty' => $decodedData['additional_services_qty'][$index] ?? 0,
                'price' => $decodedData['additional_services_price'][$index] ?? 0,
            ];
        });
        $additionalServices = $additional_services_data->map(function ($service) {
            return [
                'date' => dateFormat($service['date']),
                'service' => $service['service'],
                'qty' => $service['qty'],
                'price' => $service['price'],
                'total' => $service['qty'] * $service['price'],
            ];
        });
        $additional_service_total_price = $additionalServices->sum(fn($service) => str_replace(".", "", $service['total']));
        $promotion_discounts = json_decode($order->promotion_disc, true);
        $total_promotion_disc = $promotion_discounts ? array_sum($promotion_discounts) : null;
        $discounts = [
            'Promotion' => $total_promotion_disc > 0 ? $total_promotion_disc : null,
            'Booking Code' => $order->bookingcode_disc > 0 ? $order->bookingcode_disc : null,
            'Discounts' => $order->discounts > 0 ? $order->discounts : null
        ];
        $filteredDiscounts = array_filter($discounts, fn($value) => !is_null($value));
        $normal_price = $order->final_price + $total_promotion_disc + $order->bookingcode_disc + $order->discounts;
        $total_price_idr = $order->final_price * $usdrates->rate;
        $taxDoku = TaxDoku::find('1');
        $tax_doku = floor($total_price_idr * $taxDoku->tax_rate);
        $doku_total_price = $total_price_idr + $tax_doku;
        return view('order.user-detail-order', array_merge([
            'order' => $order,
            'tax' => $tax,
            'now' => $now,
            'usdrates' => $usdrates,
            'business' => $business,
            'attentions' => $attentions,
            'invoice' => $invoice,
            'reservation' => $reservation,
            'hotel' => $hotel,
            'room' => $room,
            'airport_shuttles' => $airport_shuttles,
            'airport_shuttle_any_zero' => $airport_shuttle_any_zero,
            'total_price_airport_shuttle' => $total_price_airport_shuttle,
            'optional_rate' => $optional_rate,
            'optional_rate_orders' => $optional_rate_orders,
            'additionalServices' => $additionalServices,
            'additional_service_total_price' => $additional_service_total_price,
            'optionalServiceTotalPrice' => $optionalServiceTotalPrice,
            'total_promotion_disc' => $total_promotion_disc,
            'filteredDiscounts' => $filteredDiscounts,
            'normal_price' => $normal_price,
            'receipts' => $receipts,
            'agent' => $agent,
            'doku_payment' => $doku_payment,
            'doku_payment_paid' => $doku_payment_paid,
            'tax_doku' => $tax_doku,
            'taxDoku' => $taxDoku,
            'doku_total_price' => $doku_total_price,
            'total_price_idr' => $total_price_idr,
        ], $decodedData->toArray()));
    }








    public function detail_order($id)
    {   
        $user = Auth::user();
        $order = Orders::where('sales_agent',$user->id)->where('id',$id)->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $usdrates = UsdRates::where('name','USD')->first();
        $attentions = Attention::where('page','orders-detail')->get();
        $business = BusinessProfile::where('id','=',1)->first();
        $optional_rate_order = OptionalRateOrder::all();
        $optionalrates = OptionalRate::all();
        if ($order->status == "Draft") {
            return redirect('/orders')->with('warning',"Submit your order to see order detail");
        }else{
            return view('main.orderdetail',compact('order'),[
                'usdrates'=>$usdrates,
                'order'=> $order,
                'business'=>$business,
                'optional_rate_order'=>$optional_rate_order,
                'attentions'=>$attentions,
            ]);
        }
        
    }

    // USER ADD ORDER ---------------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_add_order(Request $request){
        if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
            $sales_agent = $request->user_id;
            $user_id = Auth::user()->id;
            $agent = User::where('id',$user_id)->first();
            $email = $agent->email;
            $name = $agent->name;
            $status = "Pending";
        }else{
            $sales_agent = Auth::user()->id;
            $user_id = Auth::user()->id;
            $name= Auth::user()->name;
            $email= Auth::user()->email;
            $status = "Draft";
        }

        $now = Carbon::now();
        $nog = $request->number_of_guests;
        $service = $request->service;
        $service_type = $request->service_type;
        $usdrates = UsdRates::where('name','USD')->first();
        $cnyrates = UsdRates::where('name','CNY')->first();
        $twdrates = UsdRates::where('name','TWD')->first();
        $idrrates = UsdRates::where('name','IDR')->first();
        $tax = Tax::where('id',1)->first();
        $prms = Promotion::where('status','Active')
        ->where("periode_start",'<=',$now)
        ->where('periode_end','>=',$now)
        ->get();
        if(count($prms)>0){
            $p_name = [];
            $p_disc = [];
            foreach ($prms as $prm) {
                array_push($p_name,$prm->name);
                array_push($p_disc,$prm->discounts);
            }
            $promotion_total_disc = array_sum($p_disc);
            $promotion = json_encode($p_name);
            $promotion_disc = json_encode($p_disc);
        }else{
            $promotion_total_disc = 0;
            $promotion= null;
            $promotion_disc = null;
        }
        
        $bcode = BookingCode::where('id',$request->bookingcode_id)->first();
        $wedding_date = date('Y-m-d',strtotime($request->wedding_date))." ".date('H.i',strtotime($request->wedding_date));
        if (isset($bcode)) {
            if ($bcode->expired_date > $now) {
                if ($bcode->amount == 0) {
                    $bookingcode = $bcode->code;
                    $bookingcode_disc = $bcode->discounts;
                    $bookingcode_status = "Valid";
                }elseif ($bcode->used < $bcode->amount) {
                    $ordercode = Orders::where('sales_agent',$user_id)
                    ->where('bookingcode', $bcode->code)->first();
                    if (isset($ordercode)) {
                        $bookingcode = null;
                        $bookingcode_disc = 0;
                        $bookingcode_status = "Used"; //code telah digunakan
                    }else{
                        $bookingcode = $bcode->code;
                        $bookingcode_disc = $bcode->discounts;
                        $bookingcode_status = "Valid";
                    }
                }else{
                    $bookingcode = null;
                    $bookingcode_disc = 0;
                    $bookingcode_status = "Expired"; //code habis digunakan
                }
            }else{
                $bookingcode = null;
                $bookingcode_disc = 0;
                $bookingcode_status = "Expired"; //code kedaluarsa
            }
        }else{
            $bookingcode = null;
            $bookingcode_disc = 0;
            $bookingcode_status = "Invalid"; //code habis digunakan
        }

        if ($service == "Tour Package") {
            // REQUEST 
            $number_of_guests_room = null;
            $number_of_room = null;
            $extra_bed = null;
            $extra_bed_price = null;
            $special_date = null;
            $special_day = null;
            $kick_back = null;
            $kick_back_per_pax = null;
            $pickup_name = null;

            $tour_id = $request->tour_id;
            $number_of_guests = $request->number_of_guests;
            $tp_id = Tours::find($tour_id);
            $price = TourPrice::where('status','Active')
                ->where('tour_id',$tour_id)
                ->where('min_qty','<=',$number_of_guests)
                ->where('max_qty','>=',$number_of_guests)
                ->first();

            $price_pax = $request->price_pax;
            $normal_price = $price_pax * $number_of_guests;
            $price_total = $normal_price;
            $final_price = $normal_price - $bookingcode_disc - $promotion_total_disc;
            $guest_detail = $request->guest_detail;
            $include = $request->include;
            $benefits = $request->benefits;
            $additional_info = $request->additional_info;
            $cancellation_policy = $request->cancellation_policy;
            $checkin = date('Y-m-d', strtotime($request->travel_date));
            if ($request->duration == "1D"){
                $checkout = date('Y-m-d',strtotime($checkin));
                $duration = 1;
            } elseif ($request->duration == "2D/1N"){
                $checkout = date('Y-m-d',strtotime('+1 days',strtotime($checkin)));
                $duration = 2;
            } elseif ($request->duration == "3D/2N"){
                $checkout = date('Y-m-d',strtotime('+2 days',strtotime($checkin)));
                $duration = 3;
            } elseif ($request->duration == "4D/3N"){
                $checkout = date('Y-m-d',strtotime('+3 days',strtotime($checkin)));
                $duration = 4;
            } elseif ($request->duration == "5D/4N"){
                $checkout = date('Y-m-d',strtotime('+4 days',strtotime($checkin)));
                $duration = 5;
            } elseif ($request->duration == "6D/5N"){
                $checkout = date('Y-m-d',strtotime('+5 days',strtotime($checkin)));
                $duration = 6;
            } else {
                $checkout = date('Y-m-d',strtotime('+6 days',strtotime($checkin)));
                $duration = 7;
            }
            $travel_date = $checkin;
            $orderWedding_id = "";
        } elseif ($service == "Activity") {
            $special_date = $request->special_date;
            $special_day = $request->special_day;
            $number_of_guests_room = $request->number_of_guests_room;
            $number_of_room = $request->number_of_room;
            $guest_detail = $request->guest_detail;
            $number_of_guests = $request->number_of_guests;
            $extra_bed = $request->extra_bed;
            $price_total = $request->price_pax * $nog;
            $checkin = date('Y-m-d', strtotime($request->travel_date));
            $checkout = date('Y-m-d', strtotime($request->checkout));
            $duration = $request->duration;
            $price_pax = $request->price_pax;
            $kick_back = $request->kick_back;
            $kick_back_per_pax = $request->kick_back_per_pax;
            $travel_date = $checkin;
            $extra_bed_price = $request->extra_bed_price;
            $normal_price = $price_total;
            $final_price = $normal_price - $bookingcode_disc - $promotion_total_disc;
            $include = $request->include;
            $benefits = $request->benefits;
            $additional_info = $request->additional_info;
            $cancellation_policy = $request->cancellation_policy;
            $pickup_name = null;
            $orderWedding_id = "";
        } elseif ($service == "Hotel") {
            $duration = $request->duration;
            $number_of_room = count($request->number_of_guests);
            $extra_bed_proses = [];
            foreach ($request->number_of_guests as $jk) {
                if ($jk < 3 ) {
                    array_push($extra_bed_proses,'No');
                }else{
                    array_push($extra_bed_proses,'Yes');
                }
            }
            $extra_bed_id_price = [];          
            for ($i=0; $i < $number_of_room; $i++) { 
                if ($extra_bed_proses[$i] == "Yes") {
                    if ($request->extra_bed_id[$i] == 0) {
                        array_push($extra_bed_id_price,0);
                    }else{
                        $extrabeds = ExtraBed::where('id',$request->extra_bed_id[$i])->first();
                        if (isset($extrabeds->contract_rate)) {
                            $contract_rate_eb = ceil($extrabeds->contract_rate/$usdrates->rate)+$extrabeds->markup;
                            $tax_usd_extra_bed = ceil(($contract_rate_eb * $tax->tax)/100);
                            $price_extra_bed = ($contract_rate_eb + $tax_usd_extra_bed)*$duration; 
                            array_push($extra_bed_id_price,$price_extra_bed);
                        }else{
                            array_push($extra_bed_id_price,0);
                        }
                    } 
                }else{
                    array_push($extra_bed_id_price,0);
                }
            }
            $include = $request->include;
            $benefits = $request->benefits;
            $additional_info = $request->additional_info;
            $cancellation_policy = $request->cancellation_policy;
            $extra_bed_id = json_encode($request->extra_bed_id);
            $extra_bed_price = json_encode($extra_bed_id_price);
            $extra_bed = json_encode($extra_bed_proses);
            $number_of_guests_room_array = array_sum($request->number_of_guests);
            $number_of_guests_room = json_encode($request->number_of_guests);
            $number_of_guests = json_encode($number_of_guests_room_array);
            $guest_detail = json_encode($request->guest_detail);
            $special_day = json_encode($request->special_day);
            $special_date = json_encode($request->special_date);
            $extra_bed_sum= array_sum($extra_bed_id_price);
            $extra_bed_total = json_encode($extra_bed_sum);
            $checkin = date('Y-m-d', strtotime($request->checkin));
            $checkout = date('Y-m-d', strtotime($request->checkout));
            $pickup_name = null;
            $kick_back_per_pax = $request->kick_back_per_pax;
            $kick_back = $request->kick_back;
            $normal_price = $request->normal_price;
            $price_pax = $normal_price / $duration;
            $price_total = ($normal_price * $number_of_room) + $extra_bed_sum - $kick_back ;
            $final_price = $price_total - $bookingcode_disc - $promotion_total_disc;
            $orderWedding_id = "";
        } elseif ($service == "Hotel Promo") {
            $duration = $request->duration;
            $number_of_room = count($request->number_of_guests);
            $extra_bed_proses = [];
            foreach ($request->number_of_guests as $jk) {
                if ($jk < 3 ) {
                    array_push($extra_bed_proses,'No');
                }else{
                    array_push($extra_bed_proses,'Yes');
                }
            }
            $extra_bed_id_price = [];          
            for ($i=0; $i < $number_of_room; $i++) { 
                if ($extra_bed_proses[$i] == "Yes") {
                    if ($request->extra_bed_id[$i] == 0) {
                        array_push($extra_bed_id_price,null);
                    }else{
                        $extrabeds = ExtraBed::where('id',$request->extra_bed_id[$i])->first();
                        $contract_rate_eb = ceil($extrabeds->contract_rate/$usdrates->rate)+$extrabeds->markup;
                        $tax_usd_extra_bed = ceil(($contract_rate_eb * $tax->tax)/100);
                        $price_extra_bed = ($contract_rate_eb + $tax_usd_extra_bed)*$duration; 
                        array_push($extra_bed_id_price,$price_extra_bed);
                    } 
                }else{
                    array_push($extra_bed_id_price,0);
                }
            }
            $extra_bed_id = json_encode($request->extra_bed_id);
            $extra_bed_price = json_encode($extra_bed_id_price);
            $extra_bed = json_encode($extra_bed_proses);
            $number_of_guests_room_array = array_sum($request->number_of_guests);
            $number_of_guests_room = json_encode($request->number_of_guests);
            $number_of_guests = json_encode($number_of_guests_room_array);
            $guest_detail = json_encode($request->guest_detail);
            $special_day = json_encode($request->special_day);
            $special_date = json_encode($request->special_date);
            $extra_bed_sum= array_sum($extra_bed_id_price);
            $extra_bed_total = json_encode($extra_bed_sum);
            $checkin = date('Y-m-d', strtotime($request->checkin));
            $checkout = date('Y-m-d', strtotime($request->checkout));
            $pickup_name = null;
            $kick_back_per_pax = $request->kick_back_per_pax;
            $kick_back = $request->kick_back;
            $normal_price = $request->normal_price;
            $price_pax = $normal_price / $duration;
            $price_total = ($normal_price * $number_of_room) + $extra_bed_sum ;
            $final_price = $price_total - $bookingcode_disc - $promotion_total_disc;
            $orderWedding_id = "";
            if (isset($request->include)) {
                // $inc = json_decode($request->include);
                $inc = json_decode($request->include);
                if (isset($inc)) {
                    if (count($inc)>0) {
                        $include = implode($inc);
                    }else{
                        $include = $request->include;
                    }
                }else{
                    $include = $request->include;
                }
                $include = $request->include;
            }else{
                $include = $request->include;
            }
            if (isset($request->benefits)) {
                $bnf = json_decode($request->benefits);
                if (isset($bnf)) {
                    if (count($bnf)>0) {
                        $benefits = implode($bnf);
                    }else{
                        $benefits = $request->benefits;
                    }
                }else{
                    $benefits = $request->benefits;
                }
                $benefits = $request->benefits;
            }else{
                $benefits = $request->benefits;
            }
            if (isset($request->additional_info)) {
                $addinf = json_decode($request->additional_info);
                if (isset($addinf)) {
                    if (count($addinf)>0) {
                        $additional_info = implode($addinf);
                    }else{
                        $additional_info = $request->cancellation_policy;
                    }
                }else{
                    $additional_info = $request->additional_info;
                }
            }else{
                $additional_info = $request->additional_info;
            }
            if (isset($request->cancellation_policy)) {
                $canpol = json_decode($request->cancellation_policy);
                if (isset($canpol)) {
                    if (count($canpol)>0) {
                        $cancellation_policy = implode($canpol);
                    }else{
                        $cancellation_policy = $request->cancellation_policy;
                    }
                }else{
                    $cancellation_policy = $request->cancellation_policy;
                }
            }else{
                $cancellation_policy = $request->cancellation_policy;
            }

        } elseif ($service == "Hotel Package") {
            $duration = $request->duration;
            $number_of_room = count($request->number_of_guests);
            $extra_bed_proses = [];
            foreach ($request->number_of_guests as $jk) {
                if ($jk < 3 ) {
                    array_push($extra_bed_proses,'No');
                }else{
                    array_push($extra_bed_proses,'Yes');
                }
            }
            $extra_bed_id_price = [];          
            for ($i=0; $i < $number_of_room; $i++) { 
                if ($extra_bed_proses[$i] == "Yes") {
                    if ($request->extra_bed_id[$i] == 0) {
                        array_push($extra_bed_id_price,null);
                    }else{
                        $extrabeds = ExtraBed::where('id',$request->extra_bed_id[$i])->first();
                        $contract_rate_eb = ceil($extrabeds->contract_rate/$usdrates->rate)+$extrabeds->markup;
                        $tax_usd_extra_bed = ceil(($contract_rate_eb * $tax->tax)/100);
                        $price_extra_bed = ($contract_rate_eb + $tax_usd_extra_bed)*$duration; 
                        array_push($extra_bed_id_price,$price_extra_bed);
                    } 
                }else{
                    array_push($extra_bed_id_price,0);
                }
            }
            $include = $request->include;
            $benefits = $request->benefits;
            $additional_info = $request->additional_info;
            $cancellation_policy = $request->cancellation_policy;
            $duration = $request->duration;
            $number_of_room = count($request->number_of_guests);
            $extra_bed_proses = [];
            foreach ($request->number_of_guests as $jk) {
                if ($jk < 3 ) {
                    array_push($extra_bed_proses,'No');
                }else{
                    array_push($extra_bed_proses,'Yes');
                }
            }
            $extra_bed_id_price = [];          
            for ($i=0; $i < $number_of_room; $i++) { 
                if ($extra_bed_proses[$i] == "Yes") {
                    if ($request->extra_bed_id[$i] == 0) {
                        array_push($extra_bed_id_price,null);
                    }else{
                        $extrabeds = ExtraBed::where('id',$request->extra_bed_id[$i])->first();
                        $contract_rate_eb = ceil($extrabeds->contract_rate/$usdrates->rate)+$extrabeds->markup;
                        $tax_usd_extra_bed = ceil(($contract_rate_eb * $tax->tax)/100);
                        $price_extra_bed = ($contract_rate_eb + $tax_usd_extra_bed)*$duration; 
                        array_push($extra_bed_id_price,$price_extra_bed);
                    } 
                }else{
                    array_push($extra_bed_id_price,0);
                }
            }
            $extra_bed_id = json_encode($request->extra_bed_id);
            $extra_bed_price = json_encode($extra_bed_id_price);
            $extra_bed = json_encode($extra_bed_proses);
            $number_of_guests_room_array = array_sum($request->number_of_guests);
            $number_of_guests_room = json_encode($request->number_of_guests);
            $number_of_guests = json_encode($number_of_guests_room_array);
            $guest_detail = json_encode($request->guest_detail);
            $special_day = json_encode($request->special_day);
            $special_date = json_encode($request->special_date);
            $extra_bed_sum= array_sum($extra_bed_id_price);
            $extra_bed_total = json_encode($extra_bed_sum);
            $checkin = date('Y-m-d', strtotime($request->checkin));
            $checkout = date('Y-m-d', strtotime($request->checkout));
            $pickup_name = null;
            $kick_back_per_pax = $request->kick_back_per_pax;
            $kick_back = $request->kick_back;
            $normal_price = $request->normal_price;
            $price_pax = $normal_price / $duration;
            $price_total = ($normal_price * $number_of_room) + $extra_bed_sum ;
            $final_price = $price_total - $bookingcode_disc - $promotion_total_disc;
            $orderWedding_id = "";
        } elseif ($service == "Transport") {
            $checkin = date('Y-m-d H.i', strtotime($request->travel_date));
            if ($service_type == "Daily Rent") {
                $price_pax = $request->price_pax;
                $normal_price = $request->normal_price * $request->duration;
                $price_total = $request->price_total * $request->duration;
                $final_price = $price_total - $promotion_total_disc - $bookingcode_disc;
                $checkout = date('Y-m-d H.i',strtotime('+'.$request->duration.'days',strtotime($checkin)));
            } else {
                $normal_price = $request->normal_price;
                $price_pax = $request->price_pax;
                $price_total = $request->price_total;
                $final_price = $price_total - $promotion_total_disc - $bookingcode_disc;
                $checkout = date('Y-m-d H.i', strtotime('+'.$request->duration.'hours',strtotime($checkin)));
            }
            $special_date = $request->special_date;
            $special_day = $request->special_day;
            $number_of_guests_room = $request->number_of_guests_room;
            $number_of_room = $request->number_of_room;
            $guest_detail = $request->guest_detail;
            $extra_bed = $request->extra_bed;
            $number_of_guests = $request->number_of_guests;
            $duration = $request->duration;
            $pickup_name = null;
            $kick_back = $request->kick_back;
            $kick_back_per_pax = $request->kick_back_per_pax;
            $extra_bed_price = $request->extra_bed_price;
            $include = $request->include;
            $benefits = $request->benefits;
            $additional_info = $request->additional_info;
            $cancellation_policy = $request->cancellation_policy;
            $orderWedding_id = "";
        } elseif ($service == "Wedding Package") {
            $brides =new Brides([
                "bride"=>$request->bride_name,
                "bride_chinese"=>$request->bride_chinese,
                "bride_contact"=>$request->bride_contact,
                "groom"=>$request->groom_name,
                "groom_chinese"=>$request->groom_chinese,
                "groom_contact"=>$request->groom_contact,
            ]);
            $brides->save();
            $special_date = $request->special_date;
            $special_day = $request->special_day;
            $number_of_guests = $request->number_of_guests;
            $checkin = date('Y-m-d', strtotime($request->checkin));
            if ($request->duration == "1D"){
                $checkout = date('Y-m-d',strtotime($checkin));
            } elseif ($request->duration == "2D/1N"){
                $checkout = date('Y-m-d',strtotime('+1 days',strtotime($checkin)));
            } elseif ($request->duration == "3D/2N"){
                $checkout = date('Y-m-d',strtotime('+2 days',strtotime($checkin)));
            } elseif ($request->duration == "4D/3N"){
                $checkout = date('Y-m-d',strtotime('+3 days',strtotime($checkin)));
            } elseif ($request->duration == "5D/4N"){
                $checkout = date('Y-m-d',strtotime('+4 days',strtotime($checkin)));
            } elseif ($request->duration == "6D/5N"){
                $checkout = date('Y-m-d',strtotime('+5 days',strtotime($checkin)));
            } elseif ($request->duration == "7D/6N"){
                $checkout = date('Y-m-d',strtotime('+6 days',strtotime($checkin)));
            } elseif ($request->duration == "8D/7N"){
                $checkout = date('Y-m-d',strtotime('+7 days',strtotime($checkin)));
            } else {
                $checkout = date('Y-m-d',strtotime('+8 days',strtotime($checkin)));
            }

            
            $number_of_guests_room = $request->number_of_guests_room;
            $number_of_room = $request->number_of_room;
            $guest_detail = $request->guest_detail;
            $extra_bed = $request->extra_bed;
            $in=Carbon::parse($checkin);
            $out=Carbon::parse($checkout);
            $duration = $in->diffInDays($out);
            $pickup_name = $brides->id;
            $include = $request->include;
            $benefits = $request->benefits;
            $additional_info = $request->additional_info;
            $cancellation_policy = $request->cancellation_policy;
            $wedding = Weddings::where('id',$request->wedding_id)->first();
            $hotel = Hotels::where('id',$wedding->hotel_id)->firstOrFail();
            if ($wedding->fixed_services_id !== null or $wedding->fixed_services_id) {
                $wed_fixed_services_id = json_decode($wedding->fixed_services_id);
                $fixed_services_p = [];
                if ($wed_fixed_services_id) {
                    foreach ($wed_fixed_services_id as $w_fixed_service_id) {
                        $wedding_fixed_services = VendorPackage::where('id',$w_fixed_service_id)->first();
                        if ($wedding_fixed_services) {
                            array_push($fixed_services_p,$wedding_fixed_services->publish_rate);
                        }
                    }
                    if ($fixed_services_p) {
                        $fixed_service_price = array_sum($fixed_services_p);
                    }else{
                        $fixed_service_price = 0;
                    }
                }else{
                    $fixed_service_price = 0;
                }
            }else{
                $fixed_service_price = 0;
            }
            if ($request->wedding_venue_id !== null or $request->wedding_venue_id) {
                $wedding_venue_id = json_encode($request->wedding_venue_id);
                $wed_venue_id = $request->wedding_venue_id;
                $venue_p = [];
                if ($wed_venue_id) {
                    foreach ($wed_venue_id as $w_venue_id) {
                        $wedding_venue = VendorPackage::where('id',$w_venue_id)->first();
                        if ($wedding_venue) {
                            array_push($venue_p,$wedding_venue->publish_rate);
                        }
                    }
                    if ($venue_p) {
                        $venue_price = array_sum($venue_p);
                    }else{
                        $venue_price = 0;
                    }
                }else{
                    $venue_price = 0;
                }
            }else{
                $venue_price = 0;
                $wedding_venue_id = $request->wedding_venue_id;
            }
            // WEDDING MAKEUP
            if ($request->makeup_id !== null or $request->makeup_id) {
                $makeup_id = json_encode($request->makeup_id);
                $wed_makeup_id = $request->makeup_id;
                if ($wed_makeup_id) {
                    $makeup_p = [];
                    foreach ($wed_makeup_id as $w_makeup_id) {
                        $wedding_makeup = VendorPackage::where('id',$w_makeup_id)->first();
                        if ($wedding_makeup) {
                            array_push($makeup_p,$wedding_makeup->publish_rate);
                        }
                    }
                    if ($makeup_p) {
                        $makeup_price = array_sum($makeup_p);
                    }else{
                        $makeup_price = 0;
                    }
                }else{
                    $makeup_price = 0;
                }
            }else{
                $makeup_price = 0;
                $makeup_id = $request->makeup_id;
            }
            // WEDDING SUITES AND VILLAS
            if ($request->suite_and_villas_id !== null or $request->suite_and_villas_id) {
                $suite_and_villas_id = json_encode($request->suite_and_villas_id);
                $wed_room_id = $request->suite_and_villas_id;
                if ($wed_room_id) {
                    $room_p = [];
                    foreach ($wed_room_id as $w_room_id) {
                        $hotel_room_price = HotelPrice::where('rooms_id',$w_room_id)
                        ->where('start_date','<',$wedding_date)
                        ->where('end_date','>',$wedding_date)
                        ->first();
                        if ($hotel_room_price) {
                            $cr_mr_room = ($hotel_room_price->contract_rate / $usdrates->rate) + $hotel_room_price->markup;
                            $room_tax = $cr_mr_room * ($tax->tax/100);
                            $hotel_r_p = ceil($cr_mr_room + $room_tax) * $duration;
                            array_push($room_p,$hotel_r_p);
                        }
                    }
                    if ($room_p) {
                        $room_price = array_sum($room_p);
                    }else{
                        $room_price = 0;
                    }
                }else{
                    $room_price = 0;
                }
            }else{
                $room_price = 0;
                $suite_and_villas_id = $request->suite_and_villas_id;
            }
            // WEDDING DOCUMENTATION
            if ($request->documentations_id !== null or $request->documentations_id) {
                $documentations_id = json_encode($request->documentations_id);
                $wed_documentations_id = $request->documentations_id;
                if ($wed_documentations_id) {
                    $documentations_p = [];
                    foreach ($wed_documentations_id as $w_documentations_id) {
                        $wedding_documentations = VendorPackage::where('id',$w_documentations_id)->first();
                        if ($wedding_documentations) {
                            array_push($documentations_p,$wedding_documentations->publish_rate);
                        }
                    }
                    if ($documentations_p) {
                        $documentation_price = array_sum($documentations_p);
                    }else{
                        $documentation_price = 0;
                    }
                }else{
                    $documentation_price = 0;
                }
            }else{
                $documentation_price = 0;
                $documentations_id = $request->documentations_id;
            }
            // WEDDING DECORATION
            if ($request->decorations_id !== null or $request->decorations_id) {
                $decorations_id = json_encode($request->decorations_id);
                $wed_decorations_id = $request->decorations_id;
                if ($wed_decorations_id) {
                    $decorations_p = [];
                    foreach ($wed_decorations_id as $w_decorations_id) {
                        $wedding_decorations = VendorPackage::where('id',$w_decorations_id)->first();
                        if ($wedding_decorations) {
                            array_push($decorations_p,$wedding_decorations->publish_rate);
                        }
                    }
                    if ($decorations_p) {
                        $decoration_price = array_sum($decorations_p);
                    }else{
                        $decoration_price = 0;
                    }
                }else{
                    $decoration_price = 0;
                }
            }else{
                $decoration_price = 0;
                $decorations_id = $request->decorations_id;
            }
            // WEDDING DINNER VENUE
            if ($request->dinner_venue_id !== null or $request->dinner_venue_id) {
                $dinner_venue_id = json_encode($request->dinner_venue_id);
                $wed_dinner_venue_id = $request->dinner_venue_id;
                if ($wed_dinner_venue_id) {
                    $dinner_venue_p = [];
                    foreach ($wed_dinner_venue_id as $w_dinner_venue_id) {
                        $wedding_dinner_venue = VendorPackage::where('id',$w_dinner_venue_id)->first();
                        if ($wedding_dinner_venue) {
                            array_push($dinner_venue_p,$wedding_dinner_venue->publish_rate);
                        }
                    }
                    if ($dinner_venue_p) {
                        $dinner_venue_price = array_sum($dinner_venue_p);
                    }else{
                        $dinner_venue_price = 0;
                    }
                }else{
                    $dinner_venue_price = 0;
                }
            }else{
                $dinner_venue_price = 0;
                $dinner_venue_id = $request->dinner_venue_id;
            }
            // WEDDING ENTERTAINMENT
            if ($request->entertainments_id !== null or $request->entertainments_id) {
                $entertainments_id = json_encode($request->entertainments_id);
                $wed_entertainment_id = $request->entertainments_id;
                if ($wed_entertainment_id) {
                    $entertainment_p = [];
                    foreach ($wed_entertainment_id as $w_entertainment_id) {
                        $wedding_entertainment = VendorPackage::where('id',$w_entertainment_id)->first();
                        if ($wedding_entertainment) {
                            array_push($entertainment_p,$wedding_entertainment->publish_rate);
                        }
                    }
                    if ($entertainment_p) {
                        $entertainment_price = array_sum($entertainment_p);
                    }else{
                        $entertainment_price = 0;
                    }
                }else{
                    $entertainment_price = 0;
                }
            }else{
                $entertainment_price = 0;
                $entertainments_id = $request->entertainments_id;
            }

            // WEDDING TRANSPORT
            if ($request->transport_id !== null or $request->transport_id) {
                $transport_id = json_encode($request->transport_id);
                $wed_transport_id = $request->transport_id;
                if ($wed_transport_id) {
                    $transport_p = [];
                    foreach ($wed_transport_id as $w_transport_id) {
                        $wedding_transport = TransportPrice::where('transports_id',$w_transport_id)
                        ->where('type','Airport Shuttle')
                        ->where('duration',$hotel->airport_duration)
                        ->first();
                        if ($wedding_transport) {
                            $trans_cr_mr = ceil($wedding_transport->contract_rate / $usdrates->rate)+$wedding_transport->markup;
                            $trans_tax = $trans_cr_mr * ($tax->tax/100);
                            $transport_p_price = ceil($trans_cr_mr + $trans_tax);
                            array_push($transport_p,$transport_p_price);
                        }
                    }
                    if ($transport_p) {
                        $transport_price = array_sum($transport_p);
                    }else{
                        $transport_price = 0;
                    }
                }else{
                    $transport_price = 0;
                }
            }else{
                $transport_price = 0;
                $transport_id = $request->transport_id;
            }

            // WEDDING OTHER
            if ($request->other_service_id !== null or $request->other_service_id) {
                $other_service_id = json_encode($request->other_service_id);
                $wed_other_id = $request->other_service_id;
                if ($wed_other_id) {
                    $other_p = [];
                    foreach ($wed_other_id as $w_other_id) {
                        $wedding_other = VendorPackage::where('id',$w_other_id)->first();
                        if ($wedding_other) {
                            array_push($other_p,$wedding_other->publish_rate);
                        }
                    }
                    if ($other_p) {
                        $other_price = array_sum($other_p);
                    }else{
                        $other_price = 0;
                    }
                }else{
                    $other_price = 0;
                }
            }else{
                $other_price = 0;
                $other_service_id = $request->other_service_id;
            }

            $kick_back_per_pax = $request->kick_back_per_pax;
            $extra_bed_price = $request->extra_bed_price;
            $price_pax = $request->price_pax;
            $kick_back = $request->kick_back;
            $normal_price = $wedding->markup + $fixed_service_price + $venue_price + $makeup_price + $room_price + $documentation_price  + $decoration_price + $dinner_venue_price + $entertainment_price + $transport_price + $other_price - $bookingcode_disc - $promotion_total_disc;
            $price_total = $normal_price;
            $final_price = $normal_price;
            $markup = $wedding->markup;

            $orderWedding =new OrderWedding([
                "wedding_id"=>$wedding->id,
                "hotel_id"=>$wedding->hotel_id,
                "duration"=>$duration,
                "wedding_date"=>$wedding_date,
                "brides_id"=>$brides->id,
                "number_of_invitation"=>$number_of_guests,

                "wedding_fixed_service_id"=>$wedding->fixed_services_id,
                "wedding_venue_id"=>$wedding_venue_id,
                "wedding_makeup_id"=>$makeup_id,
                "wedding_room_id"=>$suite_and_villas_id,
                "wedding_documentation_id"=>$documentations_id,
                "wedding_decoration_id"=>$decorations_id,
                "wedding_dinner_venue_id"=>$dinner_venue_id,
                "wedding_entertainment_id"=>$entertainments_id,
                "wedding_transport_id"=>$transport_id,
                "wedding_other_id"=>$other_service_id,

                "fixed_service_price"=>$fixed_service_price,
                "venue_price"=>$venue_price,
                "makeup_price"=>$makeup_price,
                "room_price"=>$room_price,
                "documentation_price"=>$documentation_price,
                "decoration_price"=>$decoration_price,
                "dinner_venue_price"=>$dinner_venue_price,
                "entertainment_price"=>$entertainment_price,
                "transport_price"=>$transport_price,
                "other_price"=>$other_price,
                "markup"=>$markup,
            ]);
            // dd($orderWedding);
            $orderWedding->save();
            $orderWedding_id = $orderWedding->id;
        } else {
            $special_date = $request->special_date;
            $special_day = $request->special_day;
            $number_of_guests_room = $request->number_of_guests_room;
            $number_of_room = $request->number_of_room;
            $guest_detail = $request->guest_detail;
            $extra_bed = $request->extra_bed;
            $number_of_guests = $request->number_of_guests;
            $price_total = $request->price_pax;
            $checkin = date('Y-m-d', strtotime($request->checkin));
            $checkout = date('Y-m-d', strtotime($request->travel_date));
            $duration = $request->duration;
            $price_pax = $request->price_pax;
            $kick_back = $request->kick_back;
            $kick_back_per_pax = $request->kick_back_per_pax;
            $extra_bed_price = $request->extra_bed_price;
            $final_price = $request->final_price - $promotion_total_disc;
            $normal_price = $request->normal_price;
            $include = $request->include;
            $benefits = $request->benefits;
            $additional_info = $request->additional_info;
            $cancellation_policy = $request->cancellation_policy;
            $orderWedding_id = "";
            $order_tax = 0;
        }
        $airport_shuttle_in = $request->airport_shuttle_in;
        $airport_shuttle_out = $request->airport_shuttle_out;
        $travel_date = date('Y-m-d H.i', strtotime($request->travel_date));
        $extra_bed_id = json_encode($request->extra_bed_id);
        $price_id = $request->price_id;
        $order =new Orders([
            "user_id"=>$user_id,
            "name"=>$name,
            "email"=>$email,
            "orderno"=>$request->orderno,
            "service"=>$request->service,
            "service_id"=>$request->service_id,
            "service_type"=>$request->service_type,
            "servicename" =>$request->servicename,
            "subservice"=>$request->subservice,
            "subservice_id"=>$request->subservice_id,
            "package_name"=>$request->package_name,
            "promo_name"=>$request->promo_name,
            "book_period_start"=>$request->book_period_start,
            "book_period_end"=>$request->book_period_end,
            "period_start"=>$request->period_start,
            "period_end"=>$request->period_end,
            "number_of_guests"=>$number_of_guests,
            "number_of_guests_room"=>$number_of_guests_room,
            "number_of_room"=>$number_of_room,
            "guest_detail"=>$guest_detail,
            "request_quotation"=>$request->request_quotation,
            "extra_bed"=>$extra_bed,
            "extra_bed_id"=>$extra_bed_id,
            "extra_bed_price"=>$extra_bed_price,
            "special_day"=>$special_day,
            "special_date"=>$special_date,
            "extra_time"=>$request->extra_time,
            "price_id"=>$request->price_id,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "src"=>$request->src,
            "dst"=>$request->dst,
            "sales_agent"=>$sales_agent,
            "airport_shuttle_in"=>$airport_shuttle_in,
            "airport_shuttle_out"=>$airport_shuttle_out,
            "pickup_name"=>$pickup_name,
            "pickup_date"=>$checkin,
            "pickup_location"=>$request->pickup_location,
            "dropoff_date"=>$checkout,
            "dropoff_location"=>$request->dropoff_location,
            "bookingcode"=>$bookingcode,
            "bookingcode_disc"=>$bookingcode_disc,
            "travel_date"=>$travel_date,
            "tour_type"=>$request->tour_type,
            "location"=>$request->location,
            "capacity"=>$request->capacity,
            "destinations" =>$request->destinations,
            "include" =>$include,
            "benefits" =>$benefits,
            "additional_info"=>$additional_info,
            "cancellation_policy"=>$cancellation_policy,
            "duration"=>$duration,
            "price_total" =>$price_total, 
            "promotion" =>$promotion, 
            "promotion_disc" =>$promotion_disc, 
            "final_price" =>$final_price, 
            "usd_rate" =>$usdrates->rate, 
            "cny_rate" =>$cnyrates->rate, 
            "twd_rate" =>$twdrates->rate, 
            "normal_price" =>$normal_price,
            "price_pax" =>$price_pax,
            "kick_back" =>$kick_back, 
            "kick_back_per_pax" =>$kick_back_per_pax, 
            "status"=>$status,
            "itinerary"=>$request->itinerary,
            "wedding_order_id"=>$orderWedding_id,
            "wedding_date"=>$wedding_date,
            "bride_name"=>$request->bride_name,
            "groom_name"=>$request->groom_name,
            
            "arrival_flight"=>$request->arrival_flight,
            "arrival_time"=>$request->arrival_time,
            "departure_flight"=>$request->departure_flight,
            "departure_time"=>$request->departure_time,
            "note"=>$request->note,
        ]);
        dd($order);
        $order->save();
        if (isset($bcode)) {
            $cbcode = $bcode->used + 1;
            $bcode->update([
                "used"=>$cbcode,
            ]);
        }
        $note = "Created Order with order no: ".$request->orderno;
        if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package") {
            $hotel = Hotels::where('id',$order->service_id)->first();
            $transport_in = Transports::where('id',$request->airport_shuttle_in)->first();
            if ($transport_in) {
                $transport_in_price = TransportPrice::where('transports_id',$transport_in->id)->where('type',"Airport Shuttle")->where('duration',$hotel->airport_duration)->first();
                if ($transport_in_price) {
                    $c_price_usd = ceil($transport_in_price->contract_rate/$usdrates->rate);
                    $c_price_markup = $c_price_usd + $transport_in_price->markup;
                    $c_price_tax = ceil($c_price_markup*($tax->tax/100));
                    $airport_shuttle_in_price = $c_price_markup + $c_price_tax;
                }else{
                    $airport_shuttle_in_price = 0;
                }
                $airport_shuttle_in =new AirportShuttle([
                    "date"=>$request->arrival_time,
                    "transport"=>$transport_in->name,
                    "src"=>"Airport",
                    "dst"=>$hotel->name,
                    "duration"=>$hotel->airport_duration,
                    "distance"=>$hotel->airport_distance,
                    "price"=>$airport_shuttle_in_price,
                    "order_id"=>$order->id,
                    "nav"=>"In",
                ]);
                $airport_shuttle_in->save();
                
            }
            $transport_out = Transports::where('id',$request->airport_shuttle_out)->first();
            if ($transport_out) {
                $transport_out_price = TransportPrice::where('transports_id',$transport_out->id)->where('type',"Airport Shuttle")->where('duration',$hotel->airport_duration)->first();
                if ($transport_out_price) {
                    $o_price_usd = ceil($transport_out_price->contract_rate/$usdrates->rate);
                    $o_price_markup = $o_price_usd + $transport_out_price->markup;
                    $o_price_tax = ceil($o_price_markup*($tax->tax/100));
                    $airport_shuttle_out_price = $o_price_markup + $o_price_tax;
                }else{
                    $airport_shuttle_out_price = 0;
                }
                if ($request->airport_shuttle_out) {
                    $airport_shuttle_out =new AirportShuttle([
                        "date"=>$request->departure_time,
                        "transport"=>$transport_out->name,
                        "src"=>$hotel->name,
                        "dst"=>"Airport",
                        "duration"=>$hotel->airport_duration,
                        "distance"=>$hotel->airport_distance,
                        "price"=>$airport_shuttle_out_price,
                        "order_id"=>$order->id,
                        "nav"=>"Out",
                    ]);
                    $airport_shuttle_out->save();
                }
            }
          
        }

        $user_log =new UserLog([
            "action"=>$request->action,
            "service"=>$request->service,
            "subservice"=>$request->subservice,
            "subservice_id"=>$order->id,
            "page"=>$request->page,
            "user_id"=>$user_id,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Create Order",
            "url"=>$request->getClientIp(),
            "method"=>"Create",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        $subject = $request->orderno;
        if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
            $rquotation = $request->request_quotation;
            Mail::to(config('app.reservation_mail'))->send(new ReservationMail($order->id,$rquotation));
            return redirect('/orders-admin-'.$order->id)->with('success', __('messages.The order has been successfully created'));
        }else{
            return redirect("/edit-order/$order->id")->with('success', __('messages.Your order has been added to the order basket. Please ensure that all details are entered correctly before you confirm the order for further processing.'));
        }
    }

    

    public function func_create_order_tour_package(Request $request, $id){
        try {
            
            $validated = $request->validate([
                'number_of_guests' => 'required|integer|min:2',
                'travel_date' => 'required|date',
                'pickup_location' => 'required|string|max:255',
                'dropoff_location' => 'required|string|max:255',
                'guest_detail' => 'required|string',
                'note' => 'nullable|string',
            ]);
            if (Auth::user()->position == "developer" || Auth::user()->position == "reservation" || Auth::user()->position == "author") {
                $sales_agent = $request->user_id;
                $user_id = Auth::user()->id;
                $agent = User::where('id',$user_id)->first();
                $email = $agent->email;
                $name = $agent->name;
                $status = "Pending";
            }else{
                $sales_agent = Auth::user()->id;
                $user_id = Auth::user()->id;
                $name= Auth::user()->name;
                $email= Auth::user()->email;
                $status = "Draft";
            }
            $user = Auth::user();
            $date = now()->format('ymd');
            $now = Carbon::now();
            $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
            $usdrates = UsdRates::where('name','USD')->first();
            $cnyrates = UsdRates::where('name','CNY')->first();
            $twdrates = UsdRates::where('name','TWD')->first();
            $idrrates = UsdRates::where('name','IDR')->first();
            $tour = Tours::findOrFail($id);
            $nog = $validated['number_of_guests'];
            $tourPrice = $tour->prices()
                ->where('status', 'Active')
                ->where('min_qty','<=',$nog)
                ->where('max_qty','>=',$nog)
                ->whereDate('expired_date', '>=', $now)
                ->orderBy('min_qty', 'asc')
                ->first();

            if (!$tourPrice) {
                return response()->json(['error' => 'No active price found for this tour.'], 400);
            }
            $pax = $nog;
            $servicename = $tour->name;
            $baseRate = $tourPrice->contract_rate;
            $markup = $tourPrice->markup;
            $pricePerPax = $tourPrice->calculatePrice($usdrates, $tax);
            $totalPrice = $pricePerPax * $pax;
            $checkin = Carbon::parse($validated['travel_date']);
            $duration_days = $tour->duration_days."D";
            $durnight = "/".$tour->duration_nights."N";
            $duration = $tour->duration_nights ?? 0;
            $tour_duration = $duration_days."".$durnight;
            $checkout = $checkin->copy()->addDays($duration);

            // Hitung jumlah order hari ini dari user terkait
            $orderCountToday = Orders::where('user_id', $user_id)
                ->whereDate('created_at', now()->toDateString())
                ->count();
            $suffix = chr(65 + $orderCountToday);
            $orderNumber = strtoupper($user->code) . $date . $suffix;

            $tour_type = $tour->type?->type;
            DB::beginTransaction();
            // dd($tour_duration,$duration_days,$tour);
            $order = Orders::create([
                'user_id' => $user_id,
                'orderno' => $orderNumber,
                'name' => $name,
                'email' => $email,
                'servicename' => $servicename,
                'service' => 'Tour Package',
                'service_id' => $id,
                'checkin' => $checkin,
                'checkout' => $checkout,
                'travel_date' => $checkin,
                'location' => $tour->area,
                'tour_type' => $tour_type,
                'number_of_guests' => $pax,
                'itinerary' => $tour->itinerary,
                'include' => $tour->include,
                'exclude' => $tour->exclude,
                'additional_info' => $tour->additional_info,
                'cancellation_policy' => $tour->cancellation_policy,
                'guest_detail' => $validated['guest_detail'],
                'pickup_location' => $validated['pickup_location'],
                'pickup_date' => $checkin,
                'dropoff_date' => $checkout,
                'dropoff_location' => $validated['dropoff_location'],
                'note' => $validated['note'] ?? null,
                'duration' => $tour_duration,
                'price_pax' => $pricePerPax,
                'normal_price' => $totalPrice,
                'price_total' => $totalPrice,
                'final_price' => $totalPrice,
                "usd_rate" =>$usdrates->rate, 
                "cny_rate" =>$cnyrates->rate, 
                "twd_rate" =>$twdrates->rate, 
                "sales_agent" =>$sales_agent, 
                'status' => $status
            ]);

            DB::commit();
            // dd($order);
            return redirect('edit-order-tour/'.$order->id)->with('success', __('messages.The order has been successfully created'));
            // ✅ 6. Kembalikan respons sukses ke AJAX
            return response()->json([
                'success' => true,
                'message' => 'Order successfully created!',
                'data' => [
                    'order_number' => $order->order_number,
                    'total_price' => number_format($order->total_price, 2),
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating order: ' . $e->getMessage(),
            ], 500);
        }
    }

    // View Order Tour ========================================================================================> OK
    public function detail_order_tour($id)
    {   
        $now = Carbon::now();
        $user = Auth::user();
        $order = Orders::where('sales_agent',$user->id)->where('id',$id)->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $tour = Tours::find($order->service_id);
        $usdrates = UsdRates::where('name','USD')->first();
        $attentions = Attention::where('page','orders')->get();
        $business = BusinessProfile::where('id','=',1)->first();
        $reservation = Reservation::find($order->rsv_id)??null;
        $promotion_discounts = json_decode($order->promotion_disc, true);
        $total_promotion_disc = $promotion_discounts ? array_sum($promotion_discounts) : null;
        $kickback = $order->kick_back ? $order->kick_back : null;
        $decodedData = collect([
            'number_of_guests_room' => json_decode($order->number_of_guests_room, true),
            'guest_details' => json_decode($order->guest_detail, true),
            'special_days' => json_decode($order->special_day, true),
            'special_dates' => json_decode($order->special_date, true),
            'extra_beds' => json_decode($order->extra_bed, true),
            'extra_bed_prices' => json_decode($order->extra_bed_price, true),
            'extra_bed_total_prices' => json_decode($order->extra_bed_total_price, true),
            'additional_services' => json_decode($order->additional_service, true),
            'additional_services_date' => json_decode($order->additional_service_date, true),
            'additional_services_qty' => json_decode($order->additional_service_qty, true),
            'additional_services_price' => json_decode($order->additional_service_price, true),
            
        ]);
        $additional_services_data = collect($decodedData['additional_services'])->map(function ($service, $index) use ($decodedData) {
            return [
                'date' => $decodedData['additional_services_date'][$index] ?? null,
                'service' => $service,
                'qty' => $decodedData['additional_services_qty'][$index] ?? 0,
                'price' => $decodedData['additional_services_price'][$index] ?? 0,
            ];
        });
        $additionalServices = $additional_services_data->map(function ($service) {
            return [
                'date' => dateFormat($service['date']),
                'service' => $service['service'],
                'qty' => $service['qty'],
                'price' => $service['price'],
                'total' => $service['qty'] * $service['price'],
            ];
        });
        $additional_service_total_price = $additionalServices->sum(fn($service) => str_replace(".", "", $service['total']));
        $discounts = [
            'Kick Back' => $kickback > 0 ? $kickback : null,
            'Promotion' => $total_promotion_disc > 0 ? $total_promotion_disc : null,
            'Booking Code' => $order->bookingcode_disc > 0 ? $order->bookingcode_disc : null,
            'Discounts' => $order->discounts > 0 ? $order->discounts : null
        ];
        $filteredDiscounts = array_filter($discounts, fn($value) => !is_null($value));
        $normal_price = $order->final_price + $total_promotion_disc + $order->bookingcode_disc + $order->discounts;
        $total_price_idr = $order->final_price * $usdrates->rate;
        $taxDoku = TaxDoku::find('1');
        $tax_doku = floor($total_price_idr * $taxDoku->tax_rate);
        $doku_total_price = $total_price_idr + $tax_doku;
        $invoice = InvoiceAdmin::firstWhere('rsv_id', $order->rsv_id);
        $receipts = $invoice ? $invoice->payment : null;
        if ($invoice) {
            $doku_payment = DokuVirtualAccount::where('invoice_id', $invoice->id)
            ->where('expired_date','>=',$now)
            ->orderBy('expired_date', 'desc')
            ->first();
            $doku_payment_paid = DokuVirtualAccount::where('invoice_id', $invoice->id)
            ->where('status', 'Paid')
            ->first();
        }else{
            $doku_payment_paid = null;
            $doku_payment = null;
        }
        $langType = match (config('app.locale')) {
            'zh' => 'type_traditional',
            'zh-CN' => 'type_simplified',
            default => 'type',
        };
        $langName = match (config('app.locale')) {
            'zh' => 'name_traditional',
            'zh-CN' => 'name_simplified',
            default => 'name',
        };
        $langArea = match (config('app.locale')) {
            'zh' => 'area_traditional',
            'zh-CN' => 'area_simplified',
            default => 'area',
        };
        $langShortDescription = match (config('app.locale')) {
            'zh' => 'short_description_traditional',
            'zh-CN' => 'short_description_simplified',
            default => 'short_description',
        };
        $langDescription = match (config('app.locale')) {
            'zh' => 'description_traditional',
            'zh-CN' => 'description_simplified',
            default => 'description',
        };
        $langItinerary = match (config('app.locale')) {
            'zh' => 'itinerary_traditional',
            'zh-CN' => 'itinerary_simplified',
            default => 'itinerary',
        };
        $langInclude = match (config('app.locale')) {
            'zh' => 'include_traditional',
            'zh-CN' => 'include_simplified',
            default => 'include',
        };
        $langExclude = match (config('app.locale')) {
            'zh' => 'exclude_traditional',
            'zh-CN' => 'exclude_simplified',
            default => 'exclude',
        };
        $langAdditionalInfo = match (config('app.locale')) {
            'zh' => 'additional_info_traditional',
            'zh-CN' => 'additional_info_simplified',
            default => 'additional_info',
        };
        $langCancellationPolicy = match (config('app.locale')) {
            'zh' => 'cancellation_policy_traditional',
            'zh-CN' => 'cancellation_policy_simplified',
            default => 'cancellation_policy',
        };
        return view('frontend.orders.detail-order-tour',compact('order'),[
            'usdrates'=>$usdrates,
            'order'=> $order,
            'business'=>$business,
            'attentions'=>$attentions,
            'tour'=>$tour,
            'invoice'=>$invoice,
            'receipts'=>$receipts,
            'doku_payment_paid'=>$doku_payment_paid,
            'doku_payment'=>$doku_payment,
            'reservation'=>$reservation,
            'now'=>$now,
            'langType'=>$langType,
            'langName'=>$langName,
            'langArea'=>$langArea,
            'langShortDescription'=>$langShortDescription,
            'langDescription'=>$langDescription,
            'langItinerary'=>$langItinerary,
            'langInclude'=>$langInclude,
            'langExclude'=>$langExclude,
            'langAdditionalInfo'=>$langAdditionalInfo,
            'langCancellationPolicy'=>$langCancellationPolicy,
            'filteredDiscounts'=>$filteredDiscounts,
            'additionalServices'=>$additionalServices,
            'total_price_idr'=>$total_price_idr,
            'tax_doku'=>$tax_doku,
            'doku_total_price'=>$doku_total_price,
        ]);
        
        
    }
    // Function Update Order Tour ========================================================================================> OK
    public function func_update_order_tour(Request $request,$id){
        try {
            $validated = $request->validate([
                'number_of_guests' => 'required|integer|min:2',
                'travel_date' => 'required|date',
                'pickup_location' => 'required|string|max:255',
                'dropoff_location' => 'required|string|max:255',
                'guest_detail' => 'required|string',
                'note' => 'nullable|string',
            ]);
            $now = Carbon::now();
            $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
            $user = Auth::user();
            $order=Orders::where('sales_agent',$user->id)->where('id',$id)->first();
            if (!$order) {
                return redirect('/orders')->with('error', __('messages.Your order was not found').'!');
            }
            $tour = Tours::findOrFail($order->service_id);
            $usdrates = UsdRates::where('name','USD')->first();
            $cnyrates = UsdRates::where('name','CNY')->first();
            $twdrates = UsdRates::where('name','TWD')->first();
            $idrrates = UsdRates::where('name','IDR')->first();
            $status = "Pending";
            $checkin = Carbon::parse($validated['travel_date']);
            $duration = $tour->duration_nights ?? 0;
            $checkout = $checkin->copy()->addDays($duration);
            $nog = $validated['number_of_guests'];
            $tourPrice = $tour->prices()
                ->where('status', 'Active')
                ->where('min_qty','<=',$nog)
                ->where('max_qty','>=',$nog)
                ->whereDate('expired_date', '>=', $now)
                ->orderBy('min_qty', 'asc')
                ->first();

            if (!$tourPrice) {
                return response()->json(['error' => 'No active price found for this tour.'], 400);
            }

            $pax = $nog;
            $pricePerPax = $tourPrice->calculatePrice($usdrates, $tax);
            $totalPrice = $pricePerPax * $pax;
            $checkin = Carbon::parse($validated['travel_date']);
            $duration = $tour->duration_nights ?? 0;
            $checkout = $checkin->copy()->addDays($duration);

            $order->update([
                'checkin' => $checkin,
                'checkout' => $checkout,
                'travel_date' => $checkin,
                'number_of_guests' => $nog,
                'guest_detail' => $validated['guest_detail'],
                'pickup_location' => $validated['pickup_location'],
                'dropoff_location' => $validated['dropoff_location'],
                'note' => $validated['note'] ?? null,
                'price_pax' => $pricePerPax,
                'normal_price' => $totalPrice,
                'price_total' => $totalPrice,
                'final_price' => $totalPrice,
                "usd_rate" =>$usdrates->rate,
                "cny_rate" =>$cnyrates->rate,
                "twd_rate" =>$twdrates->rate,
                'status' => $status,
            ]);
            return redirect()->route('view.orders')->with('success',__('messages.The order has been successfully updated'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating order: ' . $e->getMessage(),
            ], 500);
        }
    }
   

    // FUNCTION REMOVE ORDER ---------------------------------------------------------------------------------------------------------------------------------------------->
    public function func_remove_order(Request $request,$id){
        $user = Auth::user();
        $order=Orders::where('sales_agent',$user->id)->where('id',$id)->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $orderno = $order->orderno;
        $service = "Order";
        $action = "Remove Order";
        $msg = "User = Remove Order <br>Admin = ".$request->admin_msg;
        $order->update([
            "status"=>$request->status,
        ]);
        $log= new LogData ([
            'service' =>$orderno,
            'service_name'=>$service,
            'action'=>$action,
            'user_id'=>$request->author,
        ]);
        $log->save();
        return redirect("/orders")->with('success','Your order has been successfully removed!');
        
    }

    // FUNCTION DESTROY ORDER --------------------------------------------------------------------------------------------------------------------------------------------->
    public function destroy_order(Request $request,$id) {
        $user = Auth::user();
        $order = Orders::where('sales_agent',$user->id)->where('id',$id)->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $order->delete();
        return redirect("/orders")->with('success','Order has been deleted!');
        
    }

    // Function Remove optional service =============================================================================================================>
    public function destroy_opser_order(Request $request,$id) {
        $user = Auth::user();
        $order = Orders::where('sales_agent',$user->id)->where('id',$request->order_id)->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $optional_rate_order = OptionalRateOrder::findOrFail($id);
        $optional_rate_order->delete();
        return redirect("/order-$order")->with('success','Optional service has been removed!');
    }


    

    // Function Updated Activated =============================================================================================================>
    public function func_update_order(Request $request,$id){
        try {
            $user = Auth::user();
            ini_set('max_execution_time', 60);
            $order=Orders::where('sales_agent',$user->id)->where('id',$id)->first();
            if (!$order) {
                return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
            }
            $orderno = $order->orderno;
            $checkin = date('Y-m-d', strtotime($request->checkin));
            $checkout = date('Y-m-d', strtotime($request->checkout));
            $usdrate = UsdRates::where('id',1)->first();
            $tax = Tax::where('id',1)->first();
        
            $travel_date = date('Y-m-d H.i',strtotime($request->travel_date));
            $wedding_date = date('Y-m-d',strtotime($request->wedding_date))." ".date('H.i',strtotime($request->wedding_time));
            if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package") {
                $hotel = Hotels::where('id',$order->service_id)->first();
                $transport_in = Transports::where('id',$request->airport_shuttle_in)->first();
                if ($transport_in) {
                    $transport_in_price = TransportPrice::where('transports_id',$transport_in->id)->where('type',"Airport Shuttle")->where('duration',$hotel->airport_duration)->first();
                    if ($transport_in_price) {
                        $c_price_usd = ceil($transport_in_price->contract_rate/$usdrate->rate);
                        $c_price_markup = $c_price_usd + $transport_in_price->markup;
                        $c_price_tax = ceil($c_price_markup*($tax->tax/100));
                        $airport_shuttle_in_price = $c_price_markup + $c_price_tax;
                    }else{
                        $airport_shuttle_in_price = 0;
                    }
                }else {
                    $transport_in_price = TransportPrice::where('transports_id',$request->airport_shuttle_in)->where('type',"Airport Shuttle")->where('duration',$hotel->airport_duration)->first();
                    if ($transport_in_price) {
                        $c_price_usd = ceil($transport_in_price->contract_rate/$usdrate->rate);
                        $c_price_markup = $c_price_usd + $transport_in_price->markup;
                        $c_price_tax = ceil($c_price_markup*($tax->tax/100));
                        $airport_shuttle_in_price = $c_price_markup + $c_price_tax;
                    }else{
                        $airport_shuttle_in_price = 0;
                    }
                }
                $transport_out = Transports::where('id',$request->airport_shuttle_out)->first();
                if ($transport_out) {
                    $transport_out_price = TransportPrice::where('transports_id',$transport_out->id)->where('type',"Airport Shuttle")->where('duration',$hotel->airport_duration)->first();
                    if ($transport_out_price) {
                        $o_price_usd = ceil($transport_out_price->contract_rate/$usdrate->rate);
                        $o_price_markup = $o_price_usd + $transport_out_price->markup;
                        $o_price_tax = ceil($o_price_markup*($tax->tax/100));
                        $airport_shuttle_out_price = $o_price_markup + $o_price_tax;
                    }else{
                        $airport_shuttle_out_price = 0;
                    }
                }else{
                    $transport_out_price = TransportPrice::where('transports_id',$request->airport_shuttle_out)->where('type',"Airport Shuttle")->where('duration',$hotel->airport_duration)->first();
                    if ($transport_out_price) {
                        $o_price_usd = ceil($transport_out_price->contract_rate/$usdrate->rate);
                        $o_price_markup = $o_price_usd + $transport_out_price->markup;
                        $o_price_tax = ceil($o_price_markup*($tax->tax/100));
                        $airport_shuttle_out_price = $o_price_markup + $o_price_tax;
                    }else{
                        $airport_shuttle_out_price = 0;
                    }
                }
                if ($airport_shuttle_in_price == 0 or $airport_shuttle_out_price == 0) {
                    $airport_shuttle_price = 0;
                }else{
                    $airport_shuttle_price = $airport_shuttle_in_price + $airport_shuttle_out_price;
                }
            }else{
                $airport_shuttle_price = 0;
            }
            if ($order->service == "Activity") {
                $normal_price =$order->price_pax * $request->number_of_guests;
                $price_total = $normal_price;
                $final_price = $price_total - $order->bookingcode_disc - $request->promotion_disc - $order->discounts;
                $number_of_guests = $request->number_of_guests;
                $price_pax = $order->price_pax;
            }elseif($order->service == "Tour Package"){
                $pr_disc = json_decode($order->promotion_disc);
                if (isset($pr_disc)) {
                    $promotion_disc = array_sum($pr_disc);
                }else{
                    $promotion_disc = 0;
                }
                $ad_ser = json_decode($order->additional_service_price);
                if (isset($ad_ser)) {
                    $additional_service_price = array_sum($ad_ser);
                }else{
                    $additional_service_price = 0;
                }
                $normal_price = $request->normal_price;
                $price_total = $request->price_total;
                // $final_price = $request->final_price;
                $number_of_guests = $request->number_of_guests;
                $price_pax = $request->price_pax;
                $final_price = ($price_total - $order->discounts - $order->bookingcode_disc - $promotion_disc) + $additional_service_price + $order->airport_shuttle_price;
            }elseif($order->service == "Wedding Package"){
                $order_wedding = OrderWedding::where('id',$order->wedding_order_id)->firstOrFail();
                $bride = Brides::where('id',$order_wedding->brides_id)->firstOrFail();
                $number_of_guests = $request->number_of_guests;
                $final_price = $request->final_price;
                $price_total = $request->price_total;
                $normal_price = $request->normal_price;
                $price_pax = $request->price_pax;

                $order_wedding->update([
                    "wedding_date"=>$wedding_date,
                    "number_of_invitation"=>$number_of_guests,
                ]);
                $bride->update([
                    "bride"=>$request->bride_name,
                    "bride_chinese"=>$request->bride_chinese,
                    "bride_contact"=>$request->bride_contact,
                    "groom"=>$request->groom_name,
                    "groom_chinese"=>$request->groom_chinese,
                    "groom_contact"=>$request->groom_contact,
                ]);

            }else{
                $normal_price = $order->normal_price;
                $price_total = $order->price_total;
                $final_price = $order->final_price + $airport_shuttle_price;
                $number_of_guests = $order->number_of_guests;
                $price_pax = $order->price_pax;
            }
            $order->update([
                "status"=>$request->status,
                "guest_detail"=>$request->guest_detail,
                "arrival_flight"=>$request->arrival_flight,
                "arrival_time"=>$request->arrival_time,
                "departure_flight"=>$request->departure_flight,
                "departure_time"=>$request->departure_time,
                "airport_shuttle_in"=>$request->airport_shuttle_in,
                "airport_shuttle_out"=>$request->airport_shuttle_out,
                "note"=>$request->note,
                "kick_back"=>$request->kick_back,
                "request_quotation"=>$request->request_quotation,
                "travel_date"=>$travel_date,
                "number_of_guests"=>$number_of_guests,
                "final_price"=>$final_price,
                "bookingcode"=>$order->bookingcode,
                "bookingcode_disc"=>$order->bookingcode_disc,
                "airport_shuttle_price"=>$airport_shuttle_price,
                "price_total"=>$price_total,
                "normal_price"=>$normal_price,
                "price_pax"=>$price_pax,
                "pickup_location"=>$request->pickup_location,
                "dropoff_location"=>$request->dropoff_location,
                "groom_name"=>$request->groom_name,
                "bride_name"=>$request->bride_name,
                "wedding_date"=>$wedding_date,
                
            ]);
            // dd($order,$order_wedding,$bride);
            
            if (isset($request->airport_shuttle_in) or isset($request->airport_shuttle_out)) {
                $asins = AirportShuttle::where('order_id',$order->id)->get();
                if(isset($asins)){
                    foreach ($asins as $asin) {
                        $in_asin = $asins->where('nav',"In")->first();
                        $out_asin = $asins->where('nav',"Out")->first();
                        if (isset($in_asin)) {
                            if ($asin->nav == "In") {
                                $asin->update([
                                    "date"=>$request->arrival_time,
                                    "transport"=>$transport_in->name,
                                    "src"=>"Airport",
                                    "dst"=>$hotel->name,
                                    "duration"=>$hotel->airport_duration,
                                    "distance"=>$hotel->airport_distance,
                                    "price"=>$airport_shuttle_in_price,
                                ]);
                            }
                        }else{
                            $airport_shuttle_in =new AirportShuttle([
                                "date"=>$request->arrival_time,
                                "transport"=>$transport_in->name,
                                "src"=>"Airport",
                                "dst"=>$hotel->name,
                                "duration"=>$hotel->airport_duration,
                                "distance"=>$hotel->airport_distance,
                                "price"=>$airport_shuttle_in_price,
                                "order_id"=>$order->id,
                                "nav"=>"In",
                            ]);
                            $airport_shuttle_in->save();
                        }
                        if (isset($out_asin)) {
                            $out_asin->update([
                                "date"=>$request->departure_time,
                                "transport"=>$transport_out->name,
                                "src"=>$hotel->name,
                                "dst"=>"Airport",
                                "duration"=>$hotel->airport_duration,
                                "distance"=>$hotel->airport_distance,
                                "price"=>$airport_shuttle_out_price,
                            ]);
                        }else{
                            $airport_shuttle_out =new AirportShuttle([
                                "date"=>$request->departure_time,
                                "transport"=>$transport_out->name,
                                "src"=>$hotel->name,
                                "dst"=>"Airport",
                                "duration"=>$hotel->airport_duration,
                                "distance"=>$hotel->airport_distance,
                                "price"=>$airport_shuttle_out_price,
                                "order_id"=>$order->id,
                                "nav"=>"Out",
                            ]);
                            $airport_shuttle_out->save();
                        }
                    }
                }else {
                    if ($request->airport_shuttle_in) {
                        $airport_shuttle_in =new AirportShuttle([
                            "date"=>$request->arrival_time,
                            "transport"=>$transport_in->name,
                            "src"=>"Airport",
                            "dst"=>$hotel->name,
                            "duration"=>$hotel->airport_duration,
                            "distance"=>$hotel->airport_distance,
                            "price"=>$airport_shuttle_in_price,
                            "order_id"=>$order->id,
                            "nav"=>"In",
                        ]);
                        $airport_shuttle_in->save();
                    }
                    if ($request->airport_shuttle_out) {
                        $airport_shuttle_out =new AirportShuttle([
                            "date"=>$request->departure_time,
                            "transport"=>$transport_out->name,
                            "src"=>$hotel->name,
                            "dst"=>"Airport",
                            "duration"=>$hotel->airport_duration,
                            "distance"=>$hotel->airport_distance,
                            "price"=>$airport_shuttle_out_price,
                            "order_id"=>$order->id,
                            "nav"=>"Out",
                        ]);
                        $airport_shuttle_out->save();
                    }
                }
            }
            // dd($order, $asins);
            //Mail
            $rquotation = $request->request_quotation;
            $agent = User::where('id',$order->sales_agent)->first();
            Mail::to(config('app.reservation_mail'))
            // Mail::to(['reservation@balikamitour.com',config('app.reservation_mail')])
            ->send(new ReservationMail($id,$rquotation));
            $note = "Submited order no: ".$order->orderno;
            //dd($order);
            $user_log =new UserLog([
                "action"=>$request->action,
                "service"=>$order->service,
                "subservice"=>$order->subservice,
                "subservice_id"=>$id,
                "page"=>$request->page,
                "user_id"=>$request->author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            $order_log =new OrderLog([
                "order_id"=>$order->id,
                "action"=>'Submit Order',
                "url"=>$request->getClientIp(),
                "method"=>"Archive",
                "agent"=>$agent->name,
                "admin"=>Auth::user()->id,
            ]);
            $order_log->save();
            return redirect("/detail-order-$order->id")->with('success','Your order has been submited, and we will validate your order');
            
        } catch (\Exception $e) {
            Log::error('Error updating order: ' . $e->getMessage());
            if ($e instanceof \Symfony\Component\HttpFoundation\File\Exception\FileException) {
                return redirect()->back()->with('error', 'Please try again, your order has not been submitted due to a network issue.');
            } else {
                return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
            }
        }
    }


    public function func_approve_order(Request $request,$id){
        $user = Auth::user();
        $order = Orders::where('sales_agent', $user->id)->where('id', $id)->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $order->update([
            "status"=>"Approved",
        ]);
        $order_log =new OrderLog([
            "order_id"=>$order->id,
            "action"=>"Approve Order",
            "url"=>$request->getClientIp(),
            "method"=>"Approve",
            "agent"=>$order->name,
            "admin"=>Auth::user()->id,
        ]);
        $order_log->save();
        return redirect("/detail-order-$id")->with('success','Your order has been approved');
        
    }

    // Function Reupload accepted =============================================================================================================>
    public function func_reupload_order(Request $request,$id){
        $user = Auth::user();
        $order = Orders::where('sales_agent', $user->id)->where('id', $id)->first();
        if (!$order) {
            return redirect('/orders')->with('warning', __('messages.Your order was not found').'!');
        }
        $orderno = $order->orderno;
        $service = "Order";
        $action = "Reupload Order";
        $checkin = date('Y-m-d', strtotime($request->checkin));
        $checkout = date('Y-m-d', strtotime($request->checkout));
        $msg = "";

        $order->update([
            "status"=>$request->status,
            "msg"=>$msg,
        ]);

        // USER LOG
        $note = "Resubmit order no: ".$order->orderno;
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$request->subservice,
            "subservice_id"=>$request->subservice_id,
            "page"=>$request->page,
            "user_id"=>$request->author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        return redirect("/orders")->with('success','Your order has been resubmited, and we will validate your order');
    }

    // Function add optional rate to Order ======================================================================================= ==>
    public function func_add_optional_rate(Request $request){

        $usdrates = UsdRates::where('name','USD')->first();
        $opti_rate = OptionalRate::where('id','=',$request->optional_rate_id)->first();
        $type = $opti_rate->type;
        $name = $opti_rate->name;
        $price_unit = (ceil($opti_rate->contract_rate / $usdrates->rate))+$opti_rate->markup;
        $total_price = $price_unit * $request->qty;
        $description = $opti_rate->description;
        $status = "Active";
        $service_date = date("Y-m-d",strtotime($request->service_date));
        $optionalrateorder =new OptionalRateOrder([
            "orders_id"=>$request->order_id,
            "type"=>$type,
            "name"=>$name,
            "qty"=>$request->qty,
            "price_unit"=>$total_price,
            "description" =>$description,
            "note"=>$request->note,
            "status"=>$status,
            "author"=>$request->author,
            "service_date"=>$service_date,
            "optional_rate_id"=>$request->optional_rate_id,
        ]);
        // @dd($order);
        $optionalrateorder->save();
        return redirect("/order-$request->order_id")->with('success','Optional service added successfully');
    
    }
    

}
