<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\Guide;
use App\Models\Guests;
use App\Models\Hotels;
use App\Models\Orders;
use App\Models\Vendor;
use App\Models\Drivers;
use App\Models\LogData;
use App\Models\UserLog;
use App\Models\ExtraBed;
use App\Models\Partners;
use App\Models\UsdRates;
use App\Models\Weddings;
use App\Models\ActionLog;
use App\Models\Attention;
use App\Models\HotelRoom;
use App\Models\Itinerary;
use App\Models\HotelPrice;
use App\Models\Reservation;
use App\Models\InvoiceAdmin;
use App\Models\OptionalRate;
use App\Models\OrderWedding;
use Illuminate\Http\Request;
use App\Models\RestaurantRsv;
use App\Models\VendorPackage;
use Illuminate\Support\Carbon;
use App\Models\BusinessProfile;
use App\Models\AdditionalService;
use App\Models\OptionalRateOrder;
use App\Models\RemarkReservation;
use App\Models\ExcludeReservation;
use App\Models\IncludeReservation;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index()
    {
        $reservation_onprogress = Reservation::where('status','On Progress')->orderBy('created_at','DESC')->get();
        $reservation_active = Reservation::where('status','Active')->orderBy('created_at','DESC')->get();
        $reservation_archived = Reservation::where('status','Archived')->orderBy('created_at','DESC')->get();
        $reservations = Reservation::where('adm_id', Auth::user()->id)->get();
        $guests = Guests::all();
        $tgl = Carbon::now();
        $now = date('Y-m-d',strtotime($tgl));
        $business = BusinessProfile::first();
        $crsv = Reservation::all();
        $attentions = Attention::where('page','reservation-admin')->get();
        $agents = Auth::user()->where('status',"Active")->get();
        $crsv_no = count($crsv);
        
        $rsv_no = $crsv_no + 1;
        $invoices = InvoiceAdmin::all();
        
       
       
        return view('admin.reservations',compact('reservation_onprogress'),[
            'reservations'=>$reservations,
            'reservation_active'=>$reservation_active,
            'agents'=>$agents,
            'attentions'=>$attentions,
            'crsv'=>$crsv,
            'rsv_no'=>$rsv_no,
            'reservation_onprogress'=>$reservation_onprogress,
            'reservation_archived'=>$reservation_archived,
            'business'=>$business,
            'now' => $now,
            'guests' => $guests,
            'invoices' => $invoices,
        ]);
    }

    public function view_reservation_hotel($id){
        $hotels = Hotels::where('status','Active')->get();
        $guests = Guests::where('rsv_id',$id)->get();
        $extrabed = ExtraBed::all();
        
        $reservation = reservation::find($id);
       
        return view('admin.reservation-hotel',[
            'hotels'=>$hotels,
            
            'reservation' =>$reservation,
            
            'guests'=>$guests,
            'extrabed'=>$extrabed,
        ]);
    }


    public function func_download_rsv($id){
        $now = Carbon::now();
        $business = BusinessProfile::where('id','=',1)->first();
        $reservation = reservation::find($id);
        $in=Carbon::parse($reservation->checkin);
        $out=Carbon::parse($reservation->checkout);
        $dur_res = $in->diffInDays($out);
        $invoice = InvoiceAdmin::where('rsv_id',$id)->first();
        $agent = Auth::user()->where('id','=',$reservation->agn_id)->first();
        $guide = Guide::where('id','=',$reservation->guide_id)->first();
        $driver = Drivers::where('id','=',$reservation->driver_id)->first();
        $guests = Guests::where('rsv_id',"=",$reservation->id)->get();
        $orders = Orders::whereNull('rsv_id')->get();
        $extra_beds = ExtraBed::all();
        $order_track = Orders::all();
        $user = Auth::user()->all();
        $additionalservices = AdditionalService::where('rsv_id','=',$id)->get();
        $hotels = Hotels::where('status','Active')->get();
        $rooms = HotelRoom::where('status','Active')->get();
        $restaurants = RestaurantRsv::where('rsv_id',$id)->get();
        $includes = IncludeReservation::where('rsv_id',$id)->get();
        $excludes = ExcludeReservation::where('rsv_id',$id)->get();
        $remarks = RemarkReservation::where('rsv_id',$id)->get();
        $hotel_orders = Orders::where([
            ['rsv_id',$reservation->id],['service','Hotel'],['status', "Active"],])
        ->orWhere([
            ['rsv_id',$reservation->id],['service','Hotel Promo'],['status', "Active"],])
        ->orWhere([
            ['rsv_id',$reservation->id],['service','Hotel Package'],['status', "Active"],])->get();
        $opsi_rate_order = OptionalRateOrder::all();
        $optional_rates = OptionalRate::all();
        $order_accomodation = Orders::where([
            ['service','Hotel'],['status','Active'],['rsv_id', $id],])
        ->orWhere([
            ['service','Hotel Promo'],['status','Active'],['rsv_id', $id],])
        ->orWhere([
            ['service','Hotel Package'],['status','Active'],['rsv_id', $id],])
        ->orderBy('checkin', 'asc')->get();
        $order_tour = Orders::where('rsv_id','=', $id)
        ->where('service','Tour Package')
        ->where('status','=','Active')
        ->orderBy('checkin', 'asc')->get();
        $activities = Orders::where('rsv_id','=', $id)
        ->where('service','Activity')
        ->where('status','=','Active')
        ->orderBy('checkin', 'asc')->get();
        $activitytours= Orders::where([
            ['service','Tour Package'],['status','Active'],['rsv_id', $id],])
        ->orWhere([
            ['service','Activity'],['status','Active'],['rsv_id', $id],])
        ->orderBy('checkin', 'asc')->get();

        $itinerarys = Itinerary::where('rsv_id', $id)
        ->orderBy('date', 'asc')->get();
        $transports = Orders::where('rsv_id','=', $id)
        ->where('service','Transport')
        ->where('status','=','Active')
        ->orderBy('checkin', 'asc')->get();
        $optionalrateorders = OptionalRateOrder::all();
        $optionalrates = OptionalRate::with('hotels')->get();
        return view('admin.download-reservation',[
            'additionalservices' => $additionalservices,
            'transports' => $transports,
            'activities' => $activities,
            'order_track' => $order_track,
            'guests' => $guests,
            'hotels' => $hotels,
            'rooms' => $rooms,
            'extra_beds' => $extra_beds,
            'optionalrateorders' => $optionalrateorders,
            'optionalrates' => $optionalrates,
            'activitytours' => $activitytours,
            'dur_res' => $dur_res,
            'itinerarys' => $itinerarys,
            'hotel_orders' => $hotel_orders,
            'optional_rates' => $optional_rates,
            'opsi_rate_order' => $opsi_rate_order,
            'restaurants' => $restaurants,
            'includes' => $includes,
            'excludes' => $excludes,
            'remarks' => $remarks,
            'invoice' => $invoice,
            
            
            'driver' => $driver,
            'guide' => $guide,
            'order_tour'=>$order_tour,
            'orders' =>$orders,
            'agent'=>$agent,
            'now' => $now,
            'business'=>$business,
            'reservation' => $reservation,
            'order_accomodation' => $order_accomodation,
            'user' => $user,
        ]);
    }


    public function view_detail_reservation($id)
    {
        $now = Carbon::now();
        $business = BusinessProfile::where('id','=',1)->first();
        $reservation = reservation::find($id);
        $in=Carbon::parse($reservation->checkin);
        $out=Carbon::parse($reservation->checkout);
        $dur_res = $in->diffInDays($out);
        $invoice = InvoiceAdmin::where('rsv_id',$id)->first();
        $agent = Auth::user()->where('id','=',$reservation->agn_id)->first();
        $guide = Guide::where('id','=',$reservation->guide_id)->first();
        $guides = Guide::all();
        $driver = Drivers::where('id','=',$reservation->driver_id)->first();
        $drivers = Drivers::all();
        $guests = Guests::where('rsv_id',"=",$reservation->id)->get();
        $orders = Orders::whereNull('rsv_id')->get();
        $extra_beds = ExtraBed::all();
        $order_track = Orders::all();
        $user = Auth::user()->all();
        $additionalservices = AdditionalService::where('rsv_id','=',$id)->get();
        $hotels = Hotels::where('status','Active')->get();
        $rooms = HotelRoom::where('status','Active')->get();
        $restaurants = RestaurantRsv::where('rsv_id',$id)->get();
        $includes = IncludeReservation::where('rsv_id',$id)->get();
        $excludes = ExcludeReservation::where('rsv_id',$id)->get();
        $remarks = RemarkReservation::where('rsv_id',$id)->get();
        $hotel_orders = Orders::where([
            ['rsv_id',$reservation->id],['service','Hotel'],['status', "Active"],['checkin',">=",$now]])
        ->orWhere([
            ['rsv_id',$reservation->id],['service','Hotel Promo'],['status', "Active"],['checkin',">=",$now]])
        ->orWhere([
            ['rsv_id',$reservation->id],['service','Hotel Package'],['status', "Active"],['checkin',">=",$now]])->get();
        $opsi_rate_order = OptionalRateOrder::all();
        $optional_rates = OptionalRate::all();
        $order_accomodation = Orders::where([
            ['service','Hotel'],['rsv_id', $id],])
        ->orWhere([
            ['service','Hotel Promo'],['rsv_id', $id],])
        ->orWhere([
            ['service','Hotel Package'],['rsv_id', $id],])
        ->orderBy('checkin', 'asc')->get();
        $order_tour = Orders::where('rsv_id','=', $id)
        ->where('service','Tour Package')
        ->where('status','=','Active')
        ->where('checkin',">=",$now)
        ->orderBy('checkin', 'asc')->get();
        $activities = Orders::where('rsv_id','=', $id)
        ->where('service','Activity')
        ->where('status','=','Active')
        ->where('checkin',">=",$now)
        ->orderBy('checkin', 'asc')->get();
        $activitytours= Orders::where([
            ['service','Tour Package'],['status','Active'],['rsv_id', $id],['checkin',">=",$now]])
        ->orWhere([
            ['service','Activity'],['status','Active'],['rsv_id', $id],['checkin',">=",$now]])
        ->orderBy('checkin', 'asc')->get();

        $itinerarys = Itinerary::where('rsv_id', $id)
        ->orderBy('date', 'asc')->get();
        $transports = Orders::where('rsv_id','=', $id)
        ->where('service','Transport')
        ->where('status','=','Active')
        ->where('checkin',">=",$now)
        ->orderBy('checkin', 'asc')->get();
        $optionalrateorders = OptionalRateOrder::all();
        $optionalrates = OptionalRate::with('hotels')->get();
        return view('admin.reservation_detail',[
            'additionalservices' => $additionalservices,
            'transports' => $transports,
            'activities' => $activities,
            'order_track' => $order_track,
            'guests' => $guests,
            'hotels' => $hotels,
            'rooms' => $rooms,
            'extra_beds' => $extra_beds,
            'optionalrateorders' => $optionalrateorders,
            'optionalrates' => $optionalrates,
            'activitytours' => $activitytours,
            'dur_res' => $dur_res,
            'itinerarys' => $itinerarys,
            'hotel_orders' => $hotel_orders,
            'optional_rates' => $optional_rates,
            'opsi_rate_order' => $opsi_rate_order,
            'restaurants' => $restaurants,
            'includes' => $includes,
            'excludes' => $excludes,
            'remarks' => $remarks,
            'invoice' => $invoice,
            
            
            'driver' => $driver,
            'drivers' => $drivers,
            'guide' => $guide,
            'guides' => $guides,
            'order_tour'=>$order_tour,
            'orders' =>$orders,
            'agent'=>$agent,
            'now' => $now,
            'business'=>$business,
            'reservation' => $reservation,
            'order_accomodation' => $order_accomodation,
            'user' => $user,
        ]);
    }

    public function view_order_rsv($id)
    {
        $usdrates = UsdRates::where('name','USD')->first();
        $attentions = Attention::where('page','orders-detail')->get();
        $order = Orders::where('id','=', $id)->first();
        $business = BusinessProfile::where('id','=',1)->first();
        $optional_rate_order = OptionalRateOrder::all();
        $optionalrates = OptionalRate::all();
        return view('admin.detail_order_rsv',compact('order'),[
            'usdrates'=>$usdrates,
            'order'=> $order,
            'business'=>$business,
            'optional_rate_order'=>$optional_rate_order,
            'attentions'=>$attentions,
            'optionalrates'=>$optionalrates,
        ]);
    }
    public function view_reservation_edit($id)
    {
        $reservation = reservation::find($id);
        $userlog = UserLog::where('rsv_id',$id);
        $business = BusinessProfile::where('id','=',1)->first();
        $agent = Auth::user()->where('id','=',$reservation->agn_id)->first();
        $agents = Auth::user()->where('status',"Active")->get();
        $guide = Guide::all();
        return view('admin.reservation_edit',[
            'guide' => $guide,
            'agents' => $agents,
            'agent' => $agent,
            'userlog' => $userlog,
            'reservation' =>$reservation,
            'business'=>$business,
        ]);
    }
// VIEW ADD ACCOMMODATION ==================================================================================================================================================================================
    public function view_add_rsv_order($id)
    {
        $reservation = reservation::find($id);
        $orders =  Orders::where([['rsv_id', null],['status','=','Active'],['user_id', $reservation->agn_id],['service','Hotel']])
        ->orWhere([['rsv_id', null],['status','=','Active'],['user_id', $reservation->agn_id],['service','Hotel Promo']])
        ->orWhere([['rsv_id', null],['status','=','Active'],['user_id', $reservation->agn_id],['service','Hotel Package']])
        ->get();
        
        $business = BusinessProfile::where('id','=',1)->first();
        return view('form.add_rsv_order',[
            'orders'=>$orders,
            'reservation' =>$reservation,
            'business'=>$business,
        ]);
    }
// VIEW ADD ACCOMMODATION ==================================================================================================================================================================================
    public function view_add_rsv_transport($id)
    {
        $reservation = reservation::find($id);
        $orders =  Orders::where('rsv_id', null)
        ->where('status','Active')
        ->where('user_id',$reservation->agn_id)
        ->where('service','transport')
        ->get();
       
        $business = BusinessProfile::where('id','=',1)->first();
        return view('form.add_rsv_transport',[
            'orders'=>$orders,
            'reservation' =>$reservation,
            'business'=>$business,
        ]);
    }
    // VIEW ADD ACTIVITY TOUR ==================================================================================================================================================================================
    public function view_add_rsv_activity_tour($id)
    {
        $reservation = reservation::find($id);
        $orders =  Orders::where([['rsv_id', null],['status','=','Active'],['user_id', $reservation->agn_id],['service','Tour Package']])
        ->orWhere([['rsv_id', null],['status','=','Active'],['user_id', $reservation->agn_id],['service','Activity']])
        ->get();
        
        $business = BusinessProfile::where('id','=',1)->first();
        return view('form.add_rsv_activity_tour',[
            'orders'=>$orders,
            'reservation' =>$reservation,
            'business'=>$business,
        ]);
    }
    // VIEW ADD ITINERARY ==================================================================================================================================================================================
    public function view_add_itinerary($id)
    {
        $reservation = reservation::find($id);
        $orders =  Orders::where([['rsv_id', null],['status','=','Active'],['user_id', $reservation->agn_id],['service','Tour Package']])
        ->orWhere([['rsv_id', null],['status','=','Active'],['user_id', $reservation->agn_id],['service','Activity']])
        ->get();
        
        $business = BusinessProfile::where('id','=',1)->first();
        return view('form.add_rsv_itinerary',[
            'orders'=>$orders,
            'reservation' =>$reservation,
            'business'=>$business,
        ]);
    }

    public function func_remove_rsv_order(Request $request, $id)
    {
        $order = Orders::findOrFail($id);
        $action = "Remove";
        $service_name = "Reservation";
        $order->update([
            "rsv_id"=>$request->rsv_id,
        ]);
        $record = new UserLog ([
            'order_id' =>$request->order_id,
            'page'=>$service_name,
            'user_id'=>$request->author,
            'catatan'=>$request->catatan,
            'rsv_id'=>$request->reservation_id,
        ]);
        $log= new LogData ([
            'service' =>$request->service,
            'service_name'=>$service_name,
            'action'=>$action,
            'user'=>$request->author,
        ]);
        // return dd($record);
        $log->save();
        $record->save();
        
        return redirect()->back()->with('success','The order has been removed from the reservation');
    }
 
    // UPDATE ACCOMMODATION ==================================================================================================================================================================================
    public function func_update_accommodation(Request $request,$id)
    {
        $order = Orders::findOrFail($id);
        $order->update([
            'rsv_id' =>$request->rsv_id,
        ]);
        // @dd($guest);
        return redirect()->back()->with('success','Order has been add to the reservation');
        // return redirect()->back()->with('error','Failed to change data, please check your form!');
    }
    // UPDATE ACTIVITY TOUR ==================================================================================================================================================================================
    public function func_update_activity_tour(Request $request,$id)
    {
        $order = Orders::findOrFail($id);
        $order->update([
            'rsv_id' =>$request->rsv_id,
        ]);
        // @dd($guest);
        return redirect()->back()->with('success','Reservation has been updated');
        // return redirect()->back()->with('error','Failed to change data, please check your form!');
    }
    // ADD GUESTS ==================================================================================================================================================================================
    public function func_add_guest(Request $request,$id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'sex' => 'required',
        ]);
        $guest = new Guests ([
            'name' =>$request->name,
            'rsv_id' =>$request->rsv_id,
            'order_id' =>$id,
            'name_mandarin'=>$request->name_mandarin,
            'date_of_birth'=>$request->date_of_birth,
            'sex'=>$request->sex,
            'phone'=>$request->phone,
            'age'=>$request->age,
        ]);
        // @dd($guest);
        $guest->save();
        return redirect()->back()->with('success','Guest has been add to the reservation');
        return redirect()->back()->with('error','Guests cannot be added, please check your form!');
    }
    // ADD INVOICE ==================================================================================================================================================================================
    public function func_add_invoice(Request $request)
    {
        // $validated = $request->validate([
        //     'inv_no' => 'required',
        //     'rsv_id' => 'required',
        //     'inv_date' => 'required',
        //     'due_date' => 'required',
        // ]);
        $status = "Draft";
        $invoice = new InvoiceAdmin ([
            'inv_no' =>$request->inv_no,
            'rsv_id' =>$request->rsv_id,
            'inv_date'=>$request->inv_date,
            'due_date'=>$request->due_date,
            'total_usd'=>$request->total_usd,
            'total_idr'=>$request->total_idr,
            'bank_id'=>$request->bank_id,
        ]);
        // @dd($invoice);
        $invoice->save();
        return redirect()->back()->with('success','Invoice has been add to the reservation');
        return redirect()->back()->with('error','Invoice cannot be added, please check your form!');
    }
    // ADD RESTAURANT ==================================================================================================================================================================================
    public function func_add_restaurant(Request $request)
    {
        $validated = $request->validate([
            'rsv_id' => 'required',
            'date' => 'required',
            'breakfast' => 'required',
            'lunch' => 'required',
            'dinner' => 'required',
        ]);
        $date = date('Y-m-d',strtotime($request->date));
        $restaurant = new RestaurantRsv ([
            'rsv_id' =>$request->rsv_id,
            'date' =>$request->date,
            'breakfast' =>$request->breakfast,
            'lunch'=>$request->lunch,
            'dinner'=>$request->dinner,
        ]);
        // @dd($guest);
        $restaurant->save();
        return redirect()->back()->with('success','Guest has been add to the reservation');
        return redirect()->back()->with('error','Guests cannot be added, please check your form!');
    }
    // ADD INCLUDE ==================================================================================================================================================================================
    public function func_add_include(Request $request)
    {
        $validated = $request->validate([
            'rsv_id' => 'required',
            'include' => 'required',
        ]);
        $include = new IncludeReservation ([
            'rsv_id' =>$request->rsv_id,
            'include' =>$request->include,
        ]);
        // @dd($guest);
        $include->save();
        return redirect()->back()->with('success','Include has been add to the reservation');
        return redirect()->back()->with('error','Include cannot be added, please check your form!');
    }
    // ADD INCLUDE ==================================================================================================================================================================================
    public function func_add_exclude(Request $request)
    {
        $validated = $request->validate([
            'rsv_id' => 'required',
            'exclude' => 'required',
        ]);
        $exclude = new ExcludeReservation ([
            'rsv_id' =>$request->rsv_id,
            'exclude' =>$request->exclude,
        ]);
        // @dd($guest);
        $exclude->save();
        return redirect()->back()->with('success','Exclude has been add to the reservation');
        return redirect()->back()->with('error','Exclude cannot be added, please check your form!');
    }
    // ADD INCLUDE ==================================================================================================================================================================================
    public function func_add_remark(Request $request)
    {
        $validated = $request->validate([
            'rsv_id' => 'required',
            'remark' => 'required',
        ]);
        $remark = new RemarkReservation ([
            'rsv_id' =>$request->rsv_id,
            'remark' =>$request->remark,
        ]);
        // @dd($guest);
        $remark->save();
        return redirect()->back()->with('success','Remark has been add to the reservation');
        return redirect()->back()->with('error','Remark cannot be added, please check your form!');
    }
    // ADD ITINERARY ==================================================================================================================================================================================
    public function func_add_itinerary(Request $request)
    {
        $validated = $request->validate([
            'rsv_id' => 'required',
            'date' => 'required',
            'itinerary' => 'required',
        ]);
        $date = date('Y-m-d',strtotime($request->date));
        $itinerary = new Itinerary ([
            'rsv_id' =>$request->rsv_id,
            'date'=>$date,
            'itinerary'=>$request->itinerary,
        ]);
        // @dd($guest);
        $itinerary->save();
        return redirect()->back()->with('success','Itinerary has been add to the reservation');
        return redirect()->back()->with('error','Itinerary cannot be added, please check your form!');
    }
    
    // UPDATE BANK ACCOUNT ==================================================================================================================================================================================
    public function func_update_invoice_bank(Request $request, $id)
    {
        $invoice=InvoiceAdmin::findOrFail($id);
        $invoice->update([
            'bank_id' =>$request->bank_id,
        ]);
        // @dd($guest);
        return redirect()->back()->with('success','Bank account has been change');
        return redirect()->back()->with('error','Bank account cannot be change');
    }
    // UPDATE check in - check out ==================================================================================================================================================================================
    public function fupdate_cin_cut(Request $request, $id)
    {
        $check_in = substr($request->checkincout, 0, 10);
        $check_out = substr($request->checkincout, 14, 23);
        $checkin = date('Y-m-d',strtotime($check_in));
        $checkout = date('Y-m-d',strtotime($check_out));
        $reservation=Reservation::findOrFail($id);
        $reservation->update([
            'checkin' =>$checkin,
            'checkout' =>$checkout,
        ]);
        // @dd($guest);
        return redirect()->back()->with('success','Checkin and Checkout has been Updated to the reservation');
        return redirect()->back()->with('error','Checkin and Checkout cannot be update, please check your form!');
    }
    // UPDATE GUESTS ==================================================================================================================================================================================
    public function func_update_guest(Request $request, $id)
    {
        $guest=Guests::findOrFail($id);
        $guest->update([
            'name' =>$request->name,
            'name_mandarin' =>$request->name_mandarin,
            'date_of_birth'=>$request->date_of_birth,
            'sex'=>$request->sex,
            'age'=>$request->age,
            'phone'=>$request->phone,
        ]);
        // @dd($guest);
        return redirect()->back()->with('error','Guest cannot be update, please check your form!');
    }
    // UPDATE RESTAURANT ==================================================================================================================================================================================
    public function func_update_restaurant(Request $request, $id)
    {
        $restaurant=RestaurantRsv::findOrFail($id);
        $restaurant->update([
            'date' =>$request->date,
            'breakfast' =>$request->breakfast,
            'lunch'=>$request->lunch,
            'dinner'=>$request->dinner,
        ]);
        // @dd($guest);
        return redirect()->back()->with('success','Meal location has been updated to the reservation');
        return redirect()->back()->with('error','Meal location cannot be update, please check your form!');
    }
    // UPDATE INCLUDE ==================================================================================================================================================================================
    public function func_update_include(Request $request, $id)
    {
        $include=IncludeReservation::findOrFail($id);
        $include->update([
            'include' =>$request->include,
        ]);
        // @dd($guest);
        return redirect()->back()->with('success','include has been updated to the reservation');
        return redirect()->back()->with('error','include cannot be update, please check your form!');
    }
    // UPDATE EXCLUDE ==================================================================================================================================================================================
    public function func_update_exclude(Request $request, $id)
    {
        $exclude=ExcludeReservation::findOrFail($id);
        $exclude->update([
            'exclude' =>$request->exclude,
        ]);
        // @dd($guest);
        return redirect()->back()->with('success','exclude has been updated to the reservation');
        return redirect()->back()->with('error','exclude cannot be update, please check your form!');
    }
    // UPDATE REMARK ==================================================================================================================================================================================
    public function func_update_remark(Request $request, $id)
    {
        $remark=RemarkReservation::findOrFail($id);
        $remark->update([
            'remark' =>$request->remark,
        ]);
        // @dd($guest);
        return redirect()->back()->with('success','Remark has been updated to the reservation');
        return redirect()->back()->with('error','Remark cannot be update, please check your form!');
    }
    // UPDATE ITINERARY ==================================================================================================================================================================================
    public function func_update_itinerary(Request $request, $id)
    {
        $itinerary=Itinerary::findOrFail($id);
        $itinerary->update([
            'date' =>$request->date,
            'itinerary' =>$request->itinerary,
        ]);
        // @dd($guest);
        return redirect()->back()->with('success','Itinerary has been updated to the reservation');
        return redirect()->back()->with('error','Itinerary cannot be update, please check your form!');
    }
    // ACTIVATE RESERVATION ==================================================================================================================================================================================
    public function func_activate_reservation(Request $request, $id)
    {
        $reservation=Reservation::findOrFail($id);
        $status = "Active";
        $reservation->update([
            'status' =>$status,
        ]);
        // @dd($guest);
        return redirect()->back()->with('success','Reservation has been activated');
        return redirect()->back()->with('error','Reservation cannot be activate, please check your form!');
    }
    // DEACTIVATE RESERVATION ==================================================================================================================================================================================
    public function func_deactivate_reservation(Request $request, $id)
    {
        $reservation=Reservation::findOrFail($id);
        $status = "Draft";
        $reservation->update([
            'status' =>$status,
        ]);
        // @dd($guest);
        return redirect()->back()->with('success','Reservation has been deactivated');
        return redirect()->back()->with('error','Reservation cannot be deactivate, please check your form!');
    }
    // ADD RESERVATION ==================================================================================================================================================================================
    public function func_add_rsv_order(Request $request)
    {
        $tgl_sekarang = Carbon::now();
        $now = date("Y-m-d",strtotime($tgl_sekarang));
        $agent = Auth::user()->where('id',$request->agn_id)->first();
        $service_name = "Reservation";
        $status = "Draft";
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
       
        $rsva = Reservation::where('rsv_no', $a)
        ->orWhere('rsv_no', $b)
        ->orWhere('rsv_no', $c)
        ->orWhere('rsv_no', $d)
        ->orWhere('rsv_no', $e)
        ->orWhere('rsv_no', $f)
        ->orWhere('rsv_no', $g)
        ->orWhere('rsv_no', $h)
        ->orWhere('rsv_no', $i)
        ->orWhere('rsv_no', $j)
        ->orWhere('rsv_no', $k)
        ->orWhere('rsv_no', $l)
        ->orWhere('rsv_no', $m)
        ->orWhere('rsv_no', $n)
        ->orWhere('rsv_no', $o)
        ->orWhere('rsv_no', $p)
        ->orWhere('rsv_no', $q)
        ->orWhere('rsv_no', $r)
        ->orWhere('rsv_no', $s)
        ->orWhere('rsv_no', $t)
        ->orWhere('rsv_no', $u)
        ->orWhere('rsv_no', $v)
        ->orWhere('rsv_no', $w)
        ->orWhere('rsv_no', $x)
        ->orWhere('rsv_no', $y)
        ->orWhere('rsv_no', $z)
        ->get();
        $crsva = count($rsva);
        

        if ($crsva == 0) {
            $rsv_no = $a;
        }elseif($crsva == 1){
            $rsv_no = $b;
        }elseif($crsva == 2){
            $rsv_no = $c;
        }elseif($crsva == 3){
            $rsv_no = $d;
        }elseif($crsva == 4){
            $rsv_no = $e;
        }elseif($crsva == 5){
            $rsv_no = $f;
        }elseif($crsva == 6){
            $rsv_no = $g;
        }elseif($crsva == 7){
            $rsv_no = $h;
        }elseif($crsva == 8){
            $rsv_no = $i;
        }elseif($crsva == 9){
            $rsv_no = $j;
        }elseif($crsva == 10){
            $rsv_no = $k;
        }elseif($crsva == 11){
            $rsv_no = $l;
        }elseif($crsva == 12){
            $rsv_no = $m;
        }elseif($crsva == 13){
            $rsv_no = $n;
        }elseif($crsva == 14){
            $rsv_no = $o;
        }elseif($crsva == 15){
            $rsv_no = $p;
        }elseif($crsva == 16){
            $rsv_no = $q;
        }elseif($crsva == 17){
            $rsv_no = $r;
        }elseif($crsva == 18){
            $rsv_no = $s;
        }elseif($crsva == 19){
            $rsv_no = $t;
        }elseif($crsva == 20){
            $rsv_no = $u;
        }elseif($crsva == 21){
            $rsv_no = $v;
        }elseif($crsva == 22){
            $rsv_no = $w;
        }elseif($crsva == 23){
            $rsv_no = $x;
        }elseif($crsva == 24){
            $rsv_no = $y;
        }elseif($crsva == 25){
            $rsv_no = $z;
        }else{
            $rsv_no = $AA;
        }
        $check_in = substr($request->checkincout, 0, 10);
        $check_out = substr($request->checkincout, 14, 23);
        $checkin = date('Y-m-d',strtotime($check_in));
        $checkout = date('Y-m-d',strtotime($check_out));
        $reservation = new Reservation ([
            'rsv_no' =>$rsv_no,
            'checkin' =>$checkin,
            'checkout' =>$checkout,
            'agn_id'=>$request->agn_id,
            'adm_id'=>$request->author,
            'status'=>$status,
        ]);
        // @dd($reservation);
        $log= new LogData ([
            'service' =>$request->service,
            'service_name'=>$service_name,
            'action'=>$request->action,
            'user_id'=>$request->author,
        ]);
        // @dd($reservation);
        $log->save();
        $reservation->save();
        return redirect('/reservation-'.$reservation->id)->with('success','The Reservation has been created');
    }

    public function func_update_additional_service(Request $request, $id)
    {
        $additionalservices=AdditionalService::findOrFail($id);
        $tgl = date('Y-m-d', strtotime($request->tgl));
        $additionalservices->update([
            "rsv_id"=>$request->rsv_id,
            "tgl"=>$tgl,
            "service"=>$request->service,
            "type"=>$request->type,
            "location"=>$request->location,
            "qty"=>$request->qty,
            "price"=>$request->price,
            "loc_name"=>$request->loc_name,
            "note"=>$request->note,
        ]);
        return redirect()->back()->with('success','Additional Service has been updated');
    }
    
    /**
     * Simpan Reservation baru
     */
    public function func_add_reservation_transport(Request $request)
    {
        $now = Carbon::now();
        $agent = Auth::user();
        $prefix = $agent->code . date('ymd', strtotime($now));
        $suffixes = range('A', 'Z');
        $existing = Reservation::where('rsv_no', 'like', $prefix . '%')->count();
        $rsv_no = $prefix . ($suffixes[$existing] ?? 'AA');
        $reservation_date = date("Y-m-d", strtotime($request->reservation_date));
        $reservation = new Reservation ([
            'rsv_no' =>$rsv_no,
            'customer_name'=>$request->customer_name,
            'reservation_date'=>$reservation_date,
        ]);
        $reservation->save();
        return redirect("/transportation")->with('success', 'SPK created successfully.');
    }

    public function addReservation(Request $request)
    {
        $now = Carbon::now();
        $rsv_no = $request->rsv_no;
        $reservation_date = date("Y-m-d", strtotime($now));
        $service = "Transport";
        $checkincout = $request->cincout;
        $customer_name = $request->agent_name;

        [$checkin, $checkout] = $this->parseCheckInOut($checkincout);

        $reservation = new Reservation ([
            'rsv_no' =>$rsv_no,
            'service' =>$service,
            'customer_name'=>$customer_name,
            'reservation_date'=>$reservation_date,
            'checkin'=>$checkin,
            'checkout'=>$checkout,
        ]);
        // dd($reservation, $request->agent_name);
        $reservation->save();
        return redirect()->back()->with('success','Reservation has been created');
    }

    public function func_update_transport_management_reservation(Request $request, $id)
    {
        $reservation=Reservation::findOrFail($id);
        $rsv_no = $request->rsv_no;
        $customer_name = $request->customer_name;
        $status = $request->status;
        $checkincout = $request->cincout;
        [$checkin, $checkout] = $this->parseCheckInOut($checkincout);
        $reservation->update([
            "rsv_no"=>$rsv_no,
            "customer_name"=>$customer_name,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "status"=>$status,
        ]);
        return redirect("/transport-management")->with('success','Reservation has been updated');
    }
    private function parseCheckInOut($checkincout)
    {
        [$check_in, $check_out] = explode(' - ', $checkincout);
        return [
            date('Y-m-d', strtotime($check_in)),
            date('Y-m-d', strtotime($check_out))
        ];
    }

    public function func_update_reservation(Request $request, $id)
    {
        $reservation=Reservation::findOrFail($id);
        $agn_id=(int)$request->agn_id;
        $guide_id=(int)$request->guide_id;
        $tgl = date('Y-m-d', strtotime($request->pickup_date));
        $reservation->update([
            "agn_id"=>$agn_id,
            "guide_id"=>$guide_id,
            "no_of_gst" => $request->no_of_gst,
            "gst_name"=>$request->gst_name,
            "gst_phone"=>$request->gst_phone,
            "arrival_flight"=>$request->arrival_flight,
            "arrival_time"=>$request->arrival_time,
            "departure_flight"=>$request->departure_flight,
            "departure_time"=>$request->departure_time,
            "pickup_date"=>$tgl,
            "pickup_time"=>$request->pickup_time,
            "msg"=>$request->msg,
            "status"=>$request->status,
        ]);
        $action_log = new ActionLog([
            "user_id"=>$request->user_id,
            "action"=>$request->action,
            "service"=>$request->service,
            "page"=>$request->page,
            "note"=>$request->note,
        ]);
        $action_log->save();
        return redirect("/reservation-$reservation->id")->with('success','Reservation has been updated');
    }



    public function func_update_reservation_pickup_name(Request $request, $id)
    {
        $reservation=Reservation::findOrFail($id);
        $order = Orders::where('rsv_id',$id)->first();
        
        if ($request->pickup_name) {
            $guest=Guests::where('rsv_id',$id)->orWhere('order_id',$order->id)
            ->where('id',$request->pickup_name)->first();
            if (isset($guest)) {
                $guestId = $guest->id;
                $guestPhone = $guest->phone;
            }
        }else {
            $guestId= null;
            $guestPhone= null;
        }
        
        $check_in = substr($request->checkincout, 0, 10);
        $check_out = substr($request->checkincout, 13, 22);
        $checkin = date('Y-m-d',strtotime($check_in));
        $checkout = date('Y-m-d',strtotime($check_out));
        $reservation->update([
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "pickup_name"=>$guestId,
        ]);
        if ($order) {
            if (!$order->handled_by) {
                $handled_by = Auth::user()->id;
            }else{
                $handled_by = $order->handled_by;
            }
            $order->update([
                "pickup_name"=>$guestId,
                "handled_by"=>$handled_by,
            ]);
        }
        // @dd($reservation);
        return back()->with('success','Reservation has been updated');
        // return redirect("/reservation-$id")->with('success','Reservation has been updated');
    }

    public function func_add_additional_service(Request $request)
    {
        $tgl = date('Y-m-d', strtotime($request->tgl));

        $additionalservice = new AdditionalService([
            "rsv_id"=>$request->rsv_id,
            "admin_id"=>$request->admin_id,
            "tgl"=>$tgl,
            "service"=>$request->service,
            "type"=>$request->type,
            "location"=>$request->location,
            "qty"=>$request->qty,
            "price"=>$request->price,
            "loc_name"=>$request->loc_name,
            "note"=>$request->note,
        ]);
        $additionalservice->save();
        return redirect()->back()->with('success','Additional service successfully added');
    }

    // Function Delete Additional Service delete =============================================================================================================>
    public function destroy_additional_service(Request $request, $id)
    {
        $additionalservice=AdditionalService::findOrFail($id);
        $action_log = new ActionLog([
            "user_id"=>$request->user_id,
            "action"=>$request->action,
            "service"=>$request->service,
            "page"=>$request->page,
            "note"=>$request->note,
        ]);
        $additionalservice->delete();
        $action_log->save();
        return redirect()->back()->with('success','Additional service has been removed');
    }
    // Function Delete Reservation =============================================================================================================>
    public function destroy_rsv(Request $request, $id)
    {
        $reservation=Reservation::findOrFail($id);
        if ($reservation->inv_id != "") {
            $inv=InvoiceAdmin::where('id',$reservation->inv_id)->first();
            $inv->delete();
        }
        $reservation->delete();
        return redirect()->back()->with('success','Reservation has been removed');
    }
    // Function Delete Guest =============================================================================================================>
    public function destroy_guest(Request $request, $id)
    {
         $guest=Guests::findOrFail($id);
         $guest->delete();
         return redirect()->back()->with('success','Guest has been removed');
    }
    // Function Delete Restaurant =============================================================================================================>
    public function destroy_restaurant(Request $request, $id)
    {
         $restaurant=RestaurantRsv::findOrFail($id);
         $restaurant->delete();
         return redirect()->back()->with('success','Meal Location has been removed');
    }
    // Function Delete Itinerary =============================================================================================================>
    public function destroy_itinerary(Request $request, $id)
    {
         $itinerary=Itinerary::findOrFail($id);
         $itinerary->delete();
         return redirect()->back()->with('success','Itinerary has been removed');
    }
    // Function Delete INCLUDE =============================================================================================================>
    public function destroy_include(Request $request, $id)
    {
         $include=IncludeReservation::findOrFail($id);
         $include->delete();
         return redirect()->back()->with('success','Include has been removed');
    }
    // Function Delete EXCLUDE =============================================================================================================>
    public function destroy_exclude(Request $request, $id)
    {
         $exclude=ExcludeReservation::findOrFail($id);
         $exclude->delete();
         return redirect()->back()->with('success','Exclude has been removed');
    }
    // Function Delete REMARK =============================================================================================================>
    public function destroy_remark(Request $request, $id)
    {
         $remark=RemarkReservation::findOrFail($id);
         $remark->delete();
         return redirect()->back()->with('success','Remark has been removed');
    }
    
}
