<?php

namespace App\Http\Controllers;
use Image;
use DateTime;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Hotels;
use App\Models\Markup;
use App\Models\LogData;
use App\Models\UserLog;
use App\Models\Contract;
use App\Models\ExtraBed;
use App\Models\RoomView;
use App\Models\UsdRates;
use App\Models\ActionLog;
use App\Models\HotelRoom;
use App\Models\HotelPrice;
use App\Models\HotelPromo;
use Illuminate\Support\Str;
use App\Models\HotelPackage;
use App\Models\HotelsImages;
use App\Models\OptionalRate;
use Illuminate\Http\Request;
use App\Models\WeddingVenues;
use App\Models\RoomFacilities;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StorehotelsRequest;
use App\Http\Requests\UpdatehotelsRequest;

class HotelsAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified','type:admin']);
    }

    private function redirectToHotelsIndexWithError(string $message = null)
    {
        $message = $message ?? __('messages.You are not authorized to perform this action.');
        return redirect()->route('admin.hotels.index')->with('error', $message);
    }

    private function redirectToHotelDetail($hotelId, ?string $anchor = null)
    {
        $url = route('admin.hotels.show', $hotelId);

        if ($anchor) {
            $url .= '#'.ltrim($anchor, '#');
        }

        return redirect($url);
    }


    
// View Detail Hotel =========================================================================================>
    public function view_detail_hotel($id){
        $today = Carbon::now();
        $now = now()->toDateString();
        $usdrates = Cache::remember('usd_rates', 3600, function () {
            return UsdRates::select('name', 'rate')->where('name', 'USD')->first();
        });
        $taxes = Tax::where('id',1)->first();
        $tax = $taxes;
        // Update semua promo yang sudah habis masa book periodenya
        HotelPromo::where('hotels_id',$id)
                ->where('book_periode_end', '<', $today)
              ->where('status', '!=', 'Expired')
              ->update(['status' => 'Expired']);
              
        $hotel = Hotels::with([
            'rooms',
            'rooms.prices' => function ($que) use ($now){
                $que->active($now);
            },
            'promos' => function ($query) use ($now){
                // $query->validForBooking($now);
                $query->where('book_periode_end','>=',$now);
            },
            'packages' => function ($q) use ($now){
                $q->active($now);
            }
            ])->findOrFail($id);
        $rooms = $hotel->rooms;
        $normal_prices = HotelPrice::where('end_date','>=',$now)
        ->where('hotels_id',$hotel->id)->get();

        $promos = HotelPromo::where('hotels_id',$id)
        // ->validForBooking($now)
        ->where('book_periode_end','>=',$now)
        ->get();
        $packages = $hotel->packages->where('stay_period_end','>=',$now)
        ->where('hotels_id',$hotel->id);
        $additional_charges = $hotel->optionalrates;
        $markups = Markup::where('service','Hotel')
            ->where('service_id',$id)->first();
            if ($markups != "") {
                $markup = $markups;
            } else {
                $markup = "";
            }
        

        $contracts = Contract::where('period_end','>',$now)->where('hotels_id',$id)->get();
        
        $extra_bed = ExtraBed::where('hotels_id',$id)->get();
        // $prices = HotelPrice::where('hotels_id','=',$id)
        //     ->where('end_date','>',$now)
        //     ->orderBy('start_date', 'asc')->get();
        
        $moonnow = date('m', strtotime($now));
        $user = Auth::user()->all();
        $author = Auth::user()->where('id',$hotel->author_id)->first();
        $action_log = ActionLog::where('service',"Hotel")->get();
        $weddingVenues = WeddingVenues::where('hotels_id',$id)->get();
        $latest_price = $hotel = Hotels::withMax(['prices as date'], 'end_date')->findOrFail($id);
        $priceokt = HotelPrice::where('hotels_id','=',$id)
            ->where('rooms_id','=', 1)
            ->orderBy('start_date', 'DESC')->get();
            return view('backend.operations.hotels.detail',[
                'taxes'=>$taxes,
                'additional_charges'=>$additional_charges,
                'extra_bed'=>$extra_bed,
                'usdrates'=>$usdrates,
                'tax'=>$tax,
                'markup'=>$markup,
                'user'=>$user,
                'action_log'=>$action_log,
                'packages' => $packages,
                'priceokt'=>$priceokt,
                'moonnow'=>$moonnow,
                'hotel'=>$hotel,
                'rooms'=>$rooms,
                'latest_price'=>$latest_price,
                'now'=>$now,
                'contracts'=>$contracts,
                'author'=>$author,
                'promos'=>$promos,
                'weddingVenues'=>$weddingVenues,
                'normal_prices'=>$normal_prices,
            ]);
        }


// View Hotel Edit =============================================================================================================>
    public function view_edit_hotel($id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $hotels=Hotels::findOrFail($id);
            $usdrates=UsdRates::where('name','USD')->first();
            return view('backend.operations.hotels.forms.edit',[
                'usdrates'=>$usdrates,
                ])->with('hotels',$hotels);
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

// View Add Hotel Price =============================================================================================================>
    public function view_add_hotel_price($id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $hotels=Hotels::findOrFail($id);
            $usdrates=UsdRates::where('name','USD')->first();
            $rooms = HotelRoom::where('hotels_id','=',$id)->orderBy('created_at', 'desc')->get();
            $markups = Markup::where('service','Hotel')
            ->where('service_id',$id)->first();
            if ($markups != "") {
                $markup = $markups;
            } else {
                $markup = "";
            }
            return view('backend.operations.hotels.forms.normal-price-create',[
                'usdrates'=>$usdrates,
                'markups'=>$markups,
                'rooms'=>$rooms,
                ])->with('hotels',$hotels);
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }


// View Room Edit =============================================================================================================>
    public function view_edit_room($id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $room=HotelRoom::findOrFail($id);
            $hotel=Hotels::where('id','=', $room->hotels_id)->first();
            $roomViews = RoomView::all();
            return view('backend.operations.hotels.forms.room-edit',[
                'hotel'=>$hotel,
                'roomViews'=>$roomViews,
            ])->with('room',$room);
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }
// View Room Edit =============================================================================================================>
    public function view_edit_wedding_venue($id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $wedding_venue=WeddingVenues::findOrFail($id);
            $hotel=Hotels::where('id','=', $wedding_venue->hotels_id)->first();
            return view('backend.operations.weddings.forms.venue-edit',[
                'hotel'=>$hotel,
            ])->with('wedding_venue',$wedding_venue);
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

// View Add Hotels =========================================================================================>
    public function view_add_hotel(){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $hotels = Hotels::all();
            return view('backend.operations.hotels.forms.create',[
            ])->with('hotels',$hotels);
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

// View Edit Galery =============================================================================================================>
    public function view_edit_galery_hotel($id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $hotels=Hotels::findOrFail($id);
            return view('backend.operations.hotels.forms.gallery-edit')->with('hotels',$hotels);
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }
// View Add Room =========================================================================================>
    public function view_add_room($id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $hotels=Hotels::findOrFail($id);
            $usdrates=UsdRates::where('name','USD')->first();
            return view('backend.operations.hotels.forms.room-create',[
                'usdrates'=>$usdrates,
            ])->with('hotels',$hotels);
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }
// View Add Wedding Venue =========================================================================================>
    public function view_add_wedding_venue($id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $hotels=Hotels::findOrFail($id);
            $usdrates=UsdRates::where('name','USD')->first();
            return view('backend.operations.weddings.forms.venue-create',[
                'usdrates'=>$usdrates,
            ])->with('hotels',$hotels);
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

// View Add Promo =============================================================================================================>
    public function view_add_promo($id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $hotel=Hotels::findOrFail($id);
            $rooms = HotelRoom::where('hotels_id', $id)->get();
            return view('backend.operations.hotels.forms.promo-create')->with('hotel',$hotel);
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

    public function view_edit_promo($id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $promo = HotelPromo::findOrFail($id);
            $hotel = Hotels::with('rooms')->findOrFail($promo->hotels_id);
            $rooms = HotelRoom::where('hotels_id', $hotel->id)->orderBy('created_at', 'desc')->get();

            return view('backend.operations.hotels.forms.promo-edit', [
                'promo' => $promo,
                'hotel' => $hotel,
                'rooms' => $rooms,
            ]);
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

    public function view_add_package($id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $hotel = Hotels::with('rooms')->findOrFail($id);
            $rooms = HotelRoom::where('hotels_id', $hotel->id)->orderBy('created_at', 'desc')->get();

            return view('backend.operations.hotels.forms.package-create', [
                'hotel' => $hotel,
                'rooms' => $rooms,
            ]);
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

    public function view_edit_package($id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $package = HotelPackage::with(['room'])->findOrFail($id);
            $hotel = Hotels::with('rooms')->findOrFail($package->hotels_id);
            $rooms = HotelRoom::where('hotels_id', $hotel->id)->orderBy('created_at', 'desc')->get();

            return view('backend.operations.hotels.forms.package-edit', [
                'package' => $package,
                'hotel' => $hotel,
                'rooms' => $rooms,
            ]);
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

// Function Add Hotels =========================================================================================>
    public function func_add_hotel(Request $request){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $userId = auth()->id();
            $validated = $request->validate([
                'name' => 'required|max:255',
                'region' => 'required',
                'address' => 'required',
                'description' => 'required',
                'phone' => 'required',
                'web' => 'required',
                'cover' => 'required',
                'airport_duration' => 'required',
                'airport_distance' => 'required',
            ]);

            if($request->hasFile("cover")){
                $file=$request->file("cover");
                $coverName=time().'_'.$file->getClientOriginalName();
                $file->move("storage/hotels/hotels-cover/",$coverName);
                $status="Draft";
                $code=Str::random(26);
                $hotel =new Hotels([
                    "name"=>$request->name,
                    "code"=>$code,
                    "region"=>$request->region,
                    "address" =>$request->address, 
                    "contact_person"=>$request->contact_person,
                    "description"=>$request->description,
                    "description_traditional"=>$request->description_traditional,
                    "description_simplified"=>$request->description_simplified,
                    "phone"=>$request->phone,
                    "additional_info"=>$request->additional_info,
                    "additional_info_traditional"=>$request->additional_info_traditional,
                    "additional_info_simplified"=>$request->additional_info_simplified,
                    "facility"=>$request->facility,
                    "facility_traditional"=>$request->facility_traditional,
                    "facility_simplified"=>$request->facility_simplified,
                    "status"=>$status,
                    "web" => $request->web,
                    "map" => $request->map,
                    "include" => $request->include,
                    "include_traditional" => $request->include_traditional,
                    "include_simplified" => $request->include_simplified,
                    "author_id"=>$userId,
                    "cancellation_policy"=>$request->cancellation_policy,
                    "cancellation_policy_traditional"=>$request->cancellation_policy_traditional,
                    "cancellation_policy_simplified"=>$request->cancellation_policy_simplified,
                    "cover" =>$coverName,
                    "min_stay" =>$request->min_stay,
                    "max_stay" =>$request->max_stay,
                    "airport_distance" =>$request->airport_distance,
                    "airport_duration" =>$request->airport_duration,
                ]);
                $hotel->save();
            }
            // USER LOG
            $action = "Add Hotel";
            $service = "Hotel";
            $subservice = "Hotel";
            $page = "add-hotel";
            $note = "Add new Hotel with Hotel id : ".$hotel->id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$hotel->id,
                "page"=>$page,
                "user_id"=>$userId,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return $this->redirectToHotelDetail($hotel->id)->with('success', 'Hotel added successfully');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }
// Function Add Contract =========================================================================================>
public function func_add_contract(Request $request){
    if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
        if ($request->hasFile("file_name")) {
            $userId = auth()->id();
            $request->validate([
                'file_name' => 'required|file|mimes:pdf,doc,docx',
                'period_start' => 'required|date',
                'period_end' => 'required|date|after_or_equal:period_start',
                'hotels_id' => 'required|exists:hotels,id',
                'contract_name' => 'required|string',
            ]);
        
            try {
                $fileContract = $request->file("file_name");
                $contractName = time() . "_" . $fileContract->getClientOriginalName();
                $fileContract->move("storage/hotels/hotels-contract/", $contractName);
        
                $periodStart = date('Y-m-d', strtotime($request->period_start));
                $periodEnd = date('Y-m-d', strtotime($request->period_end));
                $hotelsId = $request->hotels_id;
        
                $contract = new Contract([
                    "name" => $request->contract_name,
                    "hotels_id" => $hotelsId,
                    "period_start" => $periodStart,
                    "period_end" => $periodEnd,
                    "file_name" => $contractName,
                ]);
                $contract->save();
                 // USER LOG
                $action = "Add Contract";
                $service = "Hotel";
                $subservice = "Contract";
                $page = "hotel_detail";
                $note = "Add new contract : ".$hotelsId;
                $user_log =new UserLog([
                    "action"=>$action,
                    "service"=>$service,
                    "subservice"=>$subservice,
                    "subservice_id"=>$hotelsId,
                    "page"=>$page,
                    "user_id"=>$userId,
                    "user_ip"=>$request->getClientIp(),
                    "note" =>$note, 
                ]);
                $user_log->save();
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to save contract: ' . $e->getMessage()], 500);
            }
        }
       
        return $this->redirectToHotelDetail($hotelsId)->with('success', 'Hotel contract added successfully');
    }else{
        return $this->redirectToHotelsIndexWithError();
    }
}

// Function Add Room =========================================================================================>
    public function func_add_room(Request $request){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $userId = auth()->id();
            if($request->hasFile("cover")){
                $file=$request->file("cover");
                $coverName=time().'_'.$file->getClientOriginalName();
                $file->move("storage/hotels/hotels-room/",$coverName);
                $request->validate([
                    'room_view' => 'required',
                    'custom_room_view' => 'required_if:room_view,custom|max:100',
                    'beds' => 'required',
                    'custom_beds' => 'required_if:beds,custom|max:100',
                ]);
                $status="Active";
                $service="Room";
                $action="Add Room";
                $roomViewName = $request->room_view === 'custom'
                    ? $request->custom_room_view
                    : $request->room_view;
                $bed_type = $request->beds === 'custom'
                    ? $request->custom_beds
                    : $request->beds;
                $hotelroom =new HotelRoom([
                    "hotels_id"=>$request->hotels_id,
                    "cover"=>$coverName,
                    "rooms"=>$request->rooms,
                    "view"=>$roomViewName,
                    "beds"=>$bed_type,
                    "size"=>$request->size,
                    "capacity_adult" =>$request->capacity_adult, 
                    "capacity_child" =>$request->capacity_child, 
                    "include"=>$request->include,
                    "include_traditional"=>$request->include_traditional,
                    "include_simplified"=>$request->include_simplified,
                    "additional_info"=>$request->additional_info,
                    "additional_info_traditional"=>$request->additional_info_traditional,
                    "additional_info_simplified"=>$request->additional_info_simplified,
                    "amenities"=>$request->amenities,
                    "amenities_traditional"=>$request->amenities_traditional,
                    "amenities_simplified"=>$request->amenities_simplified,
                    "status"=>$status,
                ]);
                $hotelroom->save();
            }
            
            // USER LOG
            $action = "Add Rooms";
            $service = "Hotel";
            $subservice = "Room";
            $page = "add-room";
            $note = "Add new rooms at Hotel id : ".$request->hotels_id.", Room id : ".$hotelroom->id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$request->hotels_id,
                "page"=>$page,
                "user_id"=>$userId,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return $this->redirectToHotelDetail($request->hotels_id, 'rooms')->with('success', 'Rooms added successfully');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

// ADD PRICES ==================================================================================================================================================>
    public function func_add_price(Request $request){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $userId = auth()->id();
            $validated = $request->validate([
                'hotels_id' => 'required',
                'rooms_id' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'markup' => 'required',
                'contract_rate' => 'required',
            ]);
            $jr = count($request->rooms_id);
            for ($i=0; $i < $jr; $i++) { 
                $start_date = date('Y-m-d',strtotime($request->start_date[$i]));
                $end_date = date('Y-m-d',strtotime($request->end_date[$i]));
                $markup = $request->markup[$i];
                $kick_back = $request->kick_back[$i];
                $contract_rate = $request->contract_rate[$i];
                $rooms_id = $request->rooms_id[$i];
                $hotels_id = $request->hotels_id;
                $price =new HotelPrice([
                    "hotels_id"=>$hotels_id,
                    "rooms_id"=>$rooms_id,
                    "start_date"=>$start_date,
                    "end_date"=>$end_date,
                    "contract_rate" =>$contract_rate, 
                    "markup" =>$markup, 
                    "kick_back" =>$kick_back, 
                    "author" =>$userId, 
                ]);
                $price->save();
            }
            return $this->redirectToHotelDetail($request->hotels_id, 'normalPrice')->with('success', 'Price added successfully');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }
    // Function Add Promo =========================================================================================>
    public function func_add_promo(Request $request){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $userId = auth()->id();
            $validated = $request->validate([
                'hotels_id' => 'required',
                'rooms_id' => 'required',
                'name' => 'required',
                'book_periode_start' => 'required',
                'book_periode_end' => 'required',
                'periode_start' => 'required',
                'minimum_stay' => 'required',
                'contract_rate' => 'required',
                'markup'=> 'required',
            ]);
            $status = "Draft";
            $book_periode_start = date('Y-m-d', strtotime($request->book_periode_start));
            $book_periode_end = date('Y-m-d', strtotime($request->book_periode_end));
            $periode_start = date('Y-m-d', strtotime($request->periode_start));
            $periode_end = date('Y-m-d', strtotime($request->periode_end));
            $email_status = 0;
            $send_to_spesific_email = 0;
            $spesific_email = "";
            $promo =new HotelPromo([
                "promotion_type"=>$request->promotion_type,
                "quotes"=>$request->quotes,
                "hotels_id"=>$request->hotels_id,
                "rooms_id"=>$request->rooms_id,
                "name"=>$request->name,
                "book_periode_start" =>$book_periode_start, 
                "book_periode_end"=>$book_periode_end,
                "periode_start"=>$periode_start,
                "periode_end"=>$periode_end,
                "contract_rate"=>$request->contract_rate,
                "minimum_stay"=>$request->minimum_stay,
                "markup"=>$request->markup,
                "booking_code"=>$request->booking_code,
                "benefits"=>$request->benefits,
                "benefits_traditional"=>$request->benefits_traditional,
                "benefits_simplified"=>$request->benefits_simplified,
                "email_status"=>$email_status,
                "send_to_spesific_email"=>$send_to_spesific_email,
                "spesific_email"=>$spesific_email,
                "status"=>$status,
                "author"=>$userId,
                "include"=>$request->include,
                "include_traditional"=>$request->include_traditional,
                "include_simplified"=>$request->include_simplified,
                "additional_info"=>$request->additional_info,
                "additional_info_traditional"=>$request->additional_info_traditional,
                "additional_info_simplified"=>$request->additional_info_simplified,
                "cancellation_policy"=>$request->cancellation_policy,
                "cancellation_policy_traditional"=>$request->cancellation_policy_traditional,
                "cancellation_policy_simplified"=>$request->cancellation_policy_simplified,
            ]);
            $promo->save();
            return $this->redirectToHotelDetail($request->hotels_id, 'promo')->with('success', 'Promo added successfully');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }
    
    // Function Add Package =========================================================================================>
    public function func_add_package(Request $request){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $userId = auth()->id();
            $status = "Draft";
            $stay_period_start = date('Y-m-d', strtotime($request->stay_period_start));
            $stay_period_end = date('Y-m-d', strtotime($request->stay_period_end));
            $package =new HotelPackage([
                "rooms_id"=>$request->rooms_id,
                "hotels_id"=>$request->hotels_id,
                "name"=>$request->name,
                "duration" =>$request->duration, 
                "stay_period_start" =>$stay_period_start, 
                "stay_period_end" =>$stay_period_end,
                "contract_rate"=>$request->contract_rate,
                "markup"=>$request->markup,
                "booking_code"=>$request->booking_code,
                "benefits"=>$request->benefits,
                "benefits_traditional"=>$request->benefits_traditional,
                "benefits_simplified"=>$request->benefits_simplified,
                "include"=>$request->include,
                "include_traditional"=>$request->include_traditional,
                "include_simplified"=>$request->include_simplified,
                "additional_info"=>$request->additional_info,
                "additional_info_traditional"=>$request->additional_info_traditional,
                "additional_info_simplified"=>$request->additional_info_simplified,
                "author"=>$userId,
                "status"=>$status,
            ]);
            $package->save();
            // USER LOG
            $action = "Add Package";
            $service = "Hotel";
            $subservice = "Package";
            $page = "detail-hotel#package";
            $note = "Add Package to Hotel id : ".$request->hotels_id.", Room id : ".$request->rooms_id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$package->id,
                "page"=>$page,
                "user_id"=>$userId,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return $this->redirectToHotelDetail($request->hotels_id, 'package')->with('success', 'Package added successfully');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

    
    // Function Update Hotel =============================================================================================================>
    public function func_edit_hotel(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $userId = auth()->id();
            $hotel=Hotels::findOrFail($id);
            $service="Hotel";
            $action="Update";
            if($request->hasFile("cover")){
                if (File::exists("storage/hotels/hotels-cover/".$hotel->cover)) {
                    File::delete("storage/hotels/hotels-cover/".$hotel->cover);
                }
                $file=$request->file("cover");
                $hotel->cover=time()."_".$file->getClientOriginalName();
                $file->move("storage/hotels/hotels-cover/",$hotel->cover);
                $request['cover']=$hotel->cover;
                
            }

            $hotel->update([
                "name"=>$request->name,
                "region"=>$request->region,
                "address"=>$request->address,
                "contact_person"=>$request->contact_person,
                "phone"=>$request->phone,
                "description"=>$request->description,
                "description_traditional"=>$request->description_traditional,
                "description_simplified"=>$request->description_simplified,
                "facility"=>$request->facility,
                "facility_traditional"=>$request->facility_traditional,
                "facility_simplified"=>$request->facility_simplified,
                "additional_info"=>$request->additional_info,
                "additional_info_traditional"=>$request->additional_info_traditional,
                "additional_info_simplified"=>$request->additional_info_simplified,
                "status"=>$request->status,
                "web"=>$request->web,
                "map"=>$request->map,
                "cover" =>$hotel->cover,
                "cancellation_policy"=>$request->cancellation_policy,
                "cancellation_policy_traditional"=>$request->cancellation_policy_traditional,
                "cancellation_policy_simplified"=>$request->cancellation_policy_simplified,
                "author_id"=>$userId,
                "min_stay" =>$request->min_stay,
                "max_stay" =>$request->max_stay,
                "airport_distance" =>$request->airport_distance,
                "airport_duration" =>$request->airport_duration,
            ]);

            // USER LOG
            $action = "Update";
            $service = "Hotel";
            $subservice = "Hotel";
            $page = "detail-hotel";
            $note = "Update hotel : ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$userId,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return $this->redirectToHotelDetail($hotel->id)->with('success','The Hotel has been updated!');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }
    
    // Function Edit room =============================================================================================================>
    public function func_edit_room(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $userId = auth()->id();
            $room=HotelRoom::findOrFail($id);
            $hotel_id=$room->hotels_id;
            $action="Update";
            $request->validate([
                'room_view' => 'required',
                'custom_room_view' => 'required_if:room_view,custom|max:100',
                'beds' => 'required',
                'custom_beds' => 'required_if:beds,custom|max:100',
            ]);
            $roomViewName = $request->room_view === 'custom'
                ? $request->custom_room_view
                : $request->room_view;
            $bed_type = $request->beds === 'custom'
                ? $request->custom_beds
                : $request->beds;
            if($request->hasFile("cover")){
                if (File::exists("storage/hotels/hotels-room/".$room->cover)) {
                    File::delete("storage/hotels/hotels-room/".$room->cover);
                }
                $file=$request->file("cover");
                $room->cover=time()."_".$file->getClientOriginalName();
                $file->move("storage/hotels/hotels-room/",$room->cover);
                $request['cover']=$room->cover;
            }

            $room->update([
                "hotels_id"=>$request->hotels_id,
                "cover"=>$room->cover,
                "rooms"=>$request->rooms,
                "view"=>$roomViewName,
                "beds"=>$bed_type,
                "size"=>$request->size,
                "capacity_adult" =>$request->capacity_adult, 
                "capacity_child" =>$request->capacity_child, 
                "include"=>$request->include,
                "include_traditional"=>$request->include_traditional,
                "include_simplified"=>$request->include_simplified,
                "amenities"=>$request->amenities,
                "amenities_traditional"=>$request->amenities_traditional,
                "amenities_simplified"=>$request->amenities_simplified,
                "additional_info"=>$request->additional_info,
                "additional_info_traditional"=>$request->additional_info_traditional,
                "additional_info_simplified"=>$request->additional_info_simplified,
                "status"=>$request->status,
            ]);

            // USER LOG
            $action = "Update";
            $service = "Hotel";
            $subservice = "Room";
            $page = "edit-room";
            $note = "Update room on hotel : ".$request->hotels_id.", Room id : ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$userId,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return $this->redirectToHotelDetail($hotel_id, 'rooms')->with('success','The room has been updated!');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }
    
    
    // Function Edit Price =============================================================================================================>
    public function func_edit_price(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $userId = auth()->id();
            $price=HotelPrice::findOrFail($id);
            $hotel_id=$request->hotels_id;
            $action="Update Normal Price";
            $service="Hotel";
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));

            $price->update([
                "hotels_id"=>$request->hotels_id,
                "rooms_id"=>$request->rooms_id,
                "start_date"=>$start_date,
                "end_date"=>$end_date,
                "markup"=>$request->markup,
                "kick_back"=>$request->kick_back,
                "contract_rate"=>$request->contract_rate,
            ]);

            // USER LOG
            $action = "Update Normal Price";
            $service = "Hotel";
            $subservice = "Normal Price";
            $page = "detail-hotel#normal-price";
            $note = "Update normal price to Hotel id : ".$request->hotels_id.", Room id : ".$request->rooms_id.", Start date : ".$start_date.", End date : ".$end_date.", Markup : ".$request->markup.", Contract rate : ".$request->contract_rate;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$userId,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return $this->redirectToHotelDetail($hotel_id, 'normalPrice')->with('success','The Price has been updated!');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }
    // Function Edit Contract =============================================================================================================>
    public function func_edit_hotel_contract(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $userId = auth()->id();
            $contract=Contract::findOrFail($id);
            $period_start = date('Y-m-d', strtotime($request->period_start));
            $period_end = date('Y-m-d', strtotime($request->period_end));
            if($request->hasFile("file_name")){
                if (File::exists("storage/hotels/hotels-contract/".$contract->file_name)) {
                    File::delete("storage/hotels/hotels-contract/".$contract->file_name);
                }
                $file=$request->file("file_name");
                $contract->file_name = time()."_".$file->getClientOriginalName();
                $file->move("storage/hotels/hotels-contract/",$contract->file_name);
                $file_name = $contract->file_name;
                
            }
            $contract->update([
                "name"=>$request->contract_name,
                "file_name"=>$contract->file_name,
                "period_start"=>$period_start,
                "period_end"=>$period_end,
            ]);

            // USER LOG
            $action = "Update Hotel Contract";
            $service = "Hotel";
            $subservice = "Contract";
            $page = "detail-hotel";
            $note = "Update contract to Hotel id : ".$request->hotels_id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$userId,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return $this->redirectToHotelDetail($request->hotels_id, 'contracts')->with('success','Contract has been updated!');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

// Function Edit Promo =============================================================================================================>
    public function func_edit_promo(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $userId = auth()->id();
            $promo=HotelPromo::findOrFail($id);
            $hotel_id=$request->hotels_id;
            $book_periode_start = date('Y-m-d', strtotime($request->book_periode_start));
            $book_periode_end = date('Y-m-d', strtotime($request->book_periode_end));
            $periode_start = date('Y-m-d', strtotime($request->periode_start));
            $periode_end = date('Y-m-d', strtotime($request->periode_end));

            $promo->update([
                "hotels_id"=>$request->hotels_id,
                "promotion_type"=>$request->promotion_type,
                "quotes"=>$request->quotes,
                "rooms_id"=>$request->rooms_id,
                "name"=>$request->name,
                "book_periode_start" =>$book_periode_start, 
                "book_periode_end"=>$book_periode_end,
                "periode_start"=>$periode_start,
                "periode_end"=>$periode_end,
                "minimum_stay"=>$request->minimum_stay,
                "contract_rate"=>$request->contract_rate,
                "markup"=>$request->markup,
                "booking_code"=>$request->booking_code,
                "benefits"=>$request->benefits,
                "benefits_traditional"=>$request->benefits_traditional,
                "benefits_simplified"=>$request->benefits_simplified,
                "include"=>$request->include,
                "include_traditional"=>$request->include_traditional,
                "include_simplified"=>$request->include_simplified,
                "additional_info"=>$request->additional_info,
                "additional_info_traditional"=>$request->additional_info_traditional,
                "additional_info_simplified"=>$request->additional_info_simplified,
                "status"=>$request->status,
                "author"=>$userId,
            ]);

            // USER LOG
            $action = "Update Promo";
            $service = "Hotel";
            $subservice = "Promo";
            $page = "detail-hotel#promos";
            $note = "Update Promo on Hotel id : ".$request->hotels_id.", Room id : ".$request->rooms_id.", Promo id : ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$userId,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return $this->redirectToHotelDetail($hotel_id, 'promo')->with('success','The Promo has been updated!');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

// Function Edit Package =============================================================================================================>
    public function func_edit_package(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $userId = auth()->id();
            $package=HotelPackage::findOrFail($id);
            $hotel_id=$request->hotels_id;
            $stay_period_start = date('Y-m-d', strtotime($request->stay_period_start));
            $stay_period_end = date('Y-m-d', strtotime($request->stay_period_end));
            // $duration = $inpack->diffInDays($outpack);

            $package->update([
                "rooms_id"=>$request->rooms_id,
                "hotels_id"=>$request->hotels_id,
                "name"=>$request->name,
                "duration"=>$request->duration,
                "stay_period_start" =>$stay_period_start, 
                "stay_period_end" =>$stay_period_end,
                "contract_rate"=>$request->contract_rate,
                "markup"=>$request->markup,
                "booking_code"=>$request->booking_code,
                "benefits"=>$request->benefits,
                "benefits_traditional"=>$request->benefits_traditional,
                "benefits_simplified"=>$request->benefits_simplified,
                "include"=>$request->include,
                "include_traditional"=>$request->include_traditional,
                "include_simplified"=>$request->include_simplified,
                "additional_info"=>$request->additional_info,
                "additional_info_traditional"=>$request->additional_info_traditional,
                "additional_info_simplified"=>$request->additional_info_simplified,
                "author"=>$userId,
                "status"=>$request->status,
            ]);
            // USER LOG
            $action = "Update Package";
            $service = "Hotel";
            $subservice = "Package";
            $page = "detail-hotel#package";
            $note = "Update Package on Hotel id : ".$request->hotels_id.", Room id : ".$request->rooms_id.", Package id : ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$userId,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return $this->redirectToHotelDetail($hotel_id, 'package')->with('success','The Package has been updated!');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

// function Tour Remove =============================================================================================================>
    public function remove_hotel(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $hotel=Hotels::findOrFail($id);
            $userId = auth()->id();
            $status = "Removed";
            $hotel->update([
                "status"=>$status,
            ]);
            // USER LOG
            $action = "Remove Hotel";
            $service = "Hotel";
            $subservice = "Hotel";
            $page = "hotel-admin";
            $note = "Remove Hotel id : ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$userId,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return back()->with('success','The Hotel has been successfully deleted!');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }
// Function Delete Hotel =============================================================================================================>
    public function destroy_hotel($id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $hotels=Hotels::findOrFail($id);
            $service="Hotel";
            $action="Delete";
            $author= Auth::user()->id;

            if (File::exists("storage/hotels/hotels-cover/".$hotels->cover)) {
                File::delete("storage/hotels/hotels-cover/".$hotels->cover);
            }
            $images=HotelsImages::where("hotels_id",$hotels->id)->get();
            foreach($images as $image){
                if (File::exists("storage/hotels/hotels-galery/".$image->image)) {
                    File::delete("storage/hotels/hotels-galery/".$image->image);
                }
            }
            $hotels->delete();
            $log= new LogData ([
                'service' =>$service,
                'service_name'=>$hotels->name,
                'action'=>$action,
                'user'=>$author,
            ]);
            $log->save();
            return back();
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }
    
// Function Delete Room =============================================================================================================>
    public function destroy_room(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $room=HotelRoom::findOrFail($id);
            $userId = auth()->id();
            $hotel= Hotels::where('id','=',$room->hotels_id)->first();
            $service_name=$room->rooms;
            $service=$hotel->name;
            $action="Delete Room";
            $author= Auth::user()->id;

            // USER LOG
            $action = "Remove";
            $service = "Hotel";
            $subservice = "Room";
            $page = "detail-hotel#rooms";
            $note = "Remove room on hotel : ".$request->hotels_id.", Room id : ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$userId,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            $room->delete();
            return $this->redirectToHotelDetail($request->hotels_id, 'rooms')->with('success','The Room has been successfully deleted!');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

// Function Delete Price =============================================================================================================>
    public function destroy_price(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $price=HotelPrice::findOrFail($id);
            $hotel= Hotels::where('id','=',$price->hotels_id)->first();
            $room=HotelRoom::where('id','=',$price->rooms_id)->first();
            $author= Auth::user()->id;
            // USER LOG
            $action = "Remove";
            $service = "Hotel";
            $subservice = "Normal Price";
            $page = "detail-hotel#normal-price";
            $note = "Remove normal price Hotel id : ".$hotel->id.", Room id : ".$room->id.", Price id : ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$price->id,
                "page"=>$page,
                "user_id"=>$author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            $price->delete();
            return back()->with('success','The Price has been successfully deleted!');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }
    // Function Delete Promo =============================================================================================================>
    public function destroy_promo(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $promo=HotelPromo::findOrFail($id);
            $userId = auth()->id();
            // USER LOG
            $action = "Remove";
            $service = "Hotel";
            $subservice = "Promo";
            $page = "detail-hotel#promo";
            $note = "Remove Promo on hotel : ".$request->hotels_id.", Promo id : ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$userId,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            $promo->delete();
            return back()->with('success','The Promo has been successfully deleted!');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

    // Function Delete Package =============================================================================================================>
    public function destroy_package(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $package=HotelPackage::findOrFail($id);
            // USER LOG
            $action = "Remove";
            $userId = auth()->id();
            $service = "Hotel";
            $subservice = "Package";
            $page = "detail-hotel#package";
            $note = "Remove Package on hotel : ".$request->hotels_id.", Package id : ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$userId,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            $package->delete();
            return back()->with('success','The Package has been successfully deleted!');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

// Function Delete Hotel Galery  =============================================================================================================>
    public function delete_image_hotel($id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $images=HotelsImages::findOrFail($id);
            if (File::exists("storage/hotels/hotels-galery/".$images->image)) 
            {
            File::delete("storage/hotels/hotels-galery/".$images->image);
            }
            HotelsImages::find($id)->delete();
            return back();
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

// Function Delete Hotel Cover =============================================================================================================>
    public function delete_cover_hotel($id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $cover=Hotels::findOrFail($id)->cover;
            if (File::exists("storage/hotels/hotels-cover/".$cover)) 
            {
                File::delete("storage/hotels/hotels-cover/".$cover);
            }
            return back();
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }
// Function Delete Hotel Contract =============================================================================================================>
    public function delete_contract(Request $request, $id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posAdm')) {
            $contract=Contract::findOrFail($id);
            $userId = auth()->id();
            if (File::exists("storage/hotels/hotels-contract/".$request->file_name)) 
            {
                File::delete("storage/hotels/hotels-contract/".$request->file_name);
            }
            $action = "Remove";
            $service = "Hotel";
            $subservice = "Contract";
            $page = "detail-hotel";
            $note = "Remove Contract on hotel : ".$request->hotels_id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$userId,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            $contract->delete();
            return $this->redirectToHotelDetail($request->hotels_id, 'contracts')->with('success','The Contract has been successfully deleted!');
        }else{
            return $this->redirectToHotelsIndexWithError();
        }
    }

// Modal Hotel  =============================================================================================================>
    public function modal($id){
        $modal=Hotels::findOrFail($id);
        return view('backend.operations.hotels.forms.gallery-edit')->with('hotels',$modal);
        }
}
