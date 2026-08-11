<?php

namespace App\Http\Controllers;

use App\Exceptions\PricingException;
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
use App\Models\HotelRoom;
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
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Backend\Operations\Reservations\StoreManualReservationRequest;
use App\Services\Reservations\ReservationAdminService;
use App\Services\Reservations\ReservationDetailService;
use App\Services\Pricing\OrderPricingSnapshotReader;
use App\Services\Tours\TourOrderManifestService;
use App\Services\Orders\OrderPaymentDeadlineService;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index(ReservationAdminService $reservationAdmin)
    {
        return view('admin.reservations', $reservationAdmin->indexData(Auth::user()));
    }

    public function view_reservation_hotel($id){
        $hotels = Hotels::where('status','Active')->get();
        $guests = Guests::where('rsv_id',$id)->get();
        $extrabed = ExtraBed::all();
        
        $reservation = Reservation::findOrFail($id);
       
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
        $activitytours= Orders::with('activePricingSnapshot')->where([
            ['service','Tour Package'],['status','Active'],['rsv_id', $id],])
        ->orWhere([
            ['service','Activity'],['status','Active'],['rsv_id', $id],])
        ->orderBy('checkin', 'asc')->get();
        $tourPricingValues = $this->tourPricingValues($activitytours, $invoice);

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
            'tourPricingValues' => $tourPricingValues,
            'dur_res' => $dur_res,
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


    public function view_detail_reservation($id, ?ReservationDetailService $reservationDetail = null)
    {
        $reservation = Reservation::query()
            ->whereNull('deleted_at')
            ->where('status', 'Active')
            ->find($id);

        if (! $reservation) {
            return redirect()->route('view.reservation')
                ->with('invalid', __('reservations.active_only_detail'));
        }

        return view(
            'backend.operations.reservations.detail',
            ($reservationDetail ?? app(ReservationDetailService::class))->data($reservation)
        );
    }

    private function tourPricingValues($orders, ?InvoiceAdmin $invoice)
    {
        return $orders
            ->where('service', Orders::PUBLIC_TOUR_SERVICE)
            ->mapWithKeys(fn (Orders $order) => [
                $order->id => app(OrderPricingSnapshotReader::class)
                    ->historicalValues($order, $invoice),
            ]);
    }

    public function view_order_rsv($id)
    {
        $usdrates = UsdRates::where('name','USD')->first();
        $order = Orders::where('id','=', $id)->first();
        $business = BusinessProfile::where('id','=',1)->first();
        $optional_rate_order = OptionalRateOrder::all();
        $optionalrates = OptionalRate::all();
        return view('admin.detail_order_rsv',compact('order'),[
            'usdrates'=>$usdrates,
            'order'=> $order,
            'business'=>$business,
            'optional_rate_order'=>$optional_rate_order,
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
        return view('backend.operations.reservations.actions.add-order',[
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
        return view('backend.operations.reservations.actions.add-transport',[
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
        return view('backend.operations.reservations.actions.add-activity-tour',[
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
        $order = Orders::findOrFail($id);
        $tourRules = $order->service === Orders::PUBLIC_TOUR_SERVICE
            ? ['sex' => ['required', 'in:Male,Female'], 'age' => ['required', 'in:Adult,Child']]
            : ['sex' => ['required']];
        $validated = $request->validate(array_merge([
            'name' => ['required', 'string', 'max:255'],
            'rsv_id' => ['required', 'integer', 'in:'.$order->rsv_id],
            'phone' => ['nullable', 'string', 'max:50'],
        ], $tourRules));
        if ($order->service === Orders::PUBLIC_TOUR_SERVICE) {
            try {
                app(TourOrderManifestService::class)->addGuest($order, [
                    'name' => $validated['name'],
                    'name_mandarin' => $request->name_mandarin,
                    'date_of_birth' => $request->date_of_birth,
                    'sex' => $validated['sex'],
                    'phone' => $validated['phone'] ?? null,
                    'age' => $validated['age'],
                ], (int) Auth::id(), $request->getClientIp());

                return redirect()->back()->with('success', 'Guest added and Tour price recalculated.');
            } catch (PricingException $exception) {
                return redirect()->back()->withErrors([
                    'guests' => 'Guest was not added because no valid Tour price is available for the new pax count.',
                ]);
            }
        }
        $guest = new Guests ([
            'name' =>$validated['name'],
            'rsv_id' =>$validated['rsv_id'],
            'order_id' =>$id,
            'name_mandarin'=>$request->name_mandarin,
            'date_of_birth'=>$request->date_of_birth,
            'sex'=>$validated['sex'],
            'phone'=>$validated['phone'] ?? null,
            'age'=>$validated['age'] ?? $request->age,
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
        $invoiceStartedAt = Carbon::now();
        $invoice = new InvoiceAdmin ([
            'inv_no' =>$request->inv_no,
            'rsv_id' =>$request->rsv_id,
            'inv_date'=>$invoiceStartedAt,
            'due_date'=>app(OrderPaymentDeadlineService::class)->deadlineFrom($invoiceStartedAt),
            'total_usd'=>$request->total_usd,
            'total_idr'=>$request->total_idr,
            'bank_id'=>$request->bank_id,
        ]);
        // @dd($invoice);
        $invoice->save();
        return redirect()->back()->with('success','Invoice has been add to the reservation');
        return redirect()->back()->with('error','Invoice cannot be added, please check your form!');
    }

    public function create_reservation_invoice(Reservation $reservation)
    {
        if ($reservation->status !== 'Active' || $reservation->deleted_at) {
            return redirect()->route('view.reservation')
                ->with('invalid', __('reservations.active_only_detail'));
        }

        $invoiceStartedAt = Carbon::now();
        $invoice = DB::transaction(function () use ($reservation, $invoiceStartedAt) {
            $lockedReservation = Reservation::query()
                ->whereKey($reservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            return InvoiceAdmin::query()->firstOrCreate(
                ['rsv_id' => $lockedReservation->id],
                [
                    'inv_no' => 'INV-'.$lockedReservation->rsv_no,
                    'inv_date' => $invoiceStartedAt->toDateTimeString(),
                    'due_date' => app(OrderPaymentDeadlineService::class)
                        ->deadlineFrom($invoiceStartedAt)
                        ->toDateTimeString(),
                    'bank_id' => 1,
                    'created_by' => Auth::id(),
                    'agent_id' => $lockedReservation->agn_id,
                ]
            );
        });

        return redirect()->route('view.reservation.detail', $reservation)
            ->with(
                'success',
                $invoice->wasRecentlyCreated
                    ? __('reservations.invoice_created_success')
                    : __('reservations.invoice_already_exists')
            );
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
        $order = Orders::query()
            ->when($guest->order_id, fn ($query) => $query->where('id', $guest->order_id))
            ->when(!$guest->order_id, fn ($query) => $query->where('rsv_id', $guest->rsv_id))
            ->first();
        $tourRules = $order?->service === Orders::PUBLIC_TOUR_SERVICE
            ? ['sex' => ['required', 'in:Male,Female'], 'age' => ['required', 'in:Adult,Child']]
            : ['sex' => ['required']];
        $validated = $request->validate(array_merge([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ], $tourRules));
        if ($order?->service === Orders::PUBLIC_TOUR_SERVICE) {
            try {
                app(TourOrderManifestService::class)->updateGuest($guest, [
                    'name' => $validated['name'],
                    'name_mandarin' => $request->name_mandarin,
                    'date_of_birth' => $request->date_of_birth,
                    'sex' => $validated['sex'],
                    'age' => $validated['age'],
                    'phone' => $validated['phone'] ?? null,
                ], (int) Auth::id(), $request->getClientIp());

                return redirect()->back()->with('success', 'Guest updated and Tour price recalculated.');
            } catch (PricingException $exception) {
                return redirect()->back()->withErrors([
                    'guests' => 'Guest was not updated because no valid Tour price is available for the manifest.',
                ]);
            }
        }
        $guest->update([
            'name' =>$validated['name'],
            'name_mandarin' =>$request->name_mandarin,
            'date_of_birth'=>$request->date_of_birth,
            'sex'=>$validated['sex'],
            'age'=>$validated['age'] ?? $request->age,
            'phone'=>$validated['phone'] ?? null,
        ]);
        return redirect()->back()->with('success','Guest has been updated.');
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
        return redirect()->route('view.reservation')->with('success', __('reservations.deactivated_success'));
        return redirect()->back()->with('error','Reservation cannot be deactivate, please check your form!');
    }
    // ADD RESERVATION ==================================================================================================================================================================================
    public function func_add_rsv_order(
        StoreManualReservationRequest $request,
        ReservationAdminService $reservationAdmin
    )
    {
        $reservation = $reservationAdmin->createManual($request->validated(), Auth::user());

        return redirect()->route('view.reservation')
            ->with('success', __('reservations.draft_created_hidden'));
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
            'service' =>'Transport',
            'agn_id'=>$agent->id,
            'adm_id'=>$agent->id,
            'status'=>'Pending',
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
            'agn_id'=>Auth::id(),
            'adm_id'=>Auth::id(),
            'status'=>'Pending',
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
        return redirect()->route('view.reservation.detail', $reservation)->with('success','Reservation has been updated');
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
    public function destroy_rsv($id, ReservationAdminService $reservationAdmin)
    {
        $reservation = Reservation::findOrFail($id);
        $reservationAdmin->deleteManualDraft($reservation, Auth::user());

        return redirect()->route('view.reservation')->with('success', 'Draft reservation has been removed.');
    }
    // Function Delete Guest =============================================================================================================>
    public function destroy_guest(Request $request, $id)
    {
         $guest=Guests::findOrFail($id);
         $order = $guest->order_id
             ? Orders::find($guest->order_id)
             : Orders::where('rsv_id', $guest->rsv_id)->first();
         if ($order?->service === Orders::PUBLIC_TOUR_SERVICE) {
             try {
                 app(TourOrderManifestService::class)->deleteGuest(
                     $guest,
                     (int) Auth::id(),
                     $request->getClientIp()
                 );

                 return redirect()->back()->with('success', 'Guest removed and Tour price recalculated.');
             } catch (PricingException $exception) {
                 return redirect()->back()->withErrors([
                     'guests' => 'Guest was not removed because no valid Tour price is available for the remaining pax count.',
                 ]);
             }
         }
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
