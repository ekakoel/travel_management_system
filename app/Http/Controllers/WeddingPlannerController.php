<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Brides;
use App\Models\Guests;
use App\Models\Hotels;
use App\Models\Flights;
use App\Models\UserLog;
use App\Models\ExtraBed;
use App\Models\Weddings;
use App\Models\Attention;
use App\Models\Countries;
use App\Models\HotelRoom;
use App\Models\Transports;
use App\Models\BankAccount;
use App\Models\OrderWedding;
use Illuminate\Http\Request;
use App\Models\WeddingDinnerVenues;
use App\Models\WeddingVenues;
use App\Models\WeddingPlanner;
use App\Models\BusinessProfile;
use App\Models\WeddingInvitations;
use App\Models\WeddingAccomodations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\WeddingDinnerPackages;
use App\Models\WeddingPlannerTransport;
use App\Http\Requests\StoreWeddingPlannerRequest;
use App\Http\Requests\UpdateWeddingPlannerRequest;

class WeddingPlannerController extends Controller
{
    // VIEW WEDDING PLANNER
    public function index()
    {
        $agent_id = Auth::user()->id;
        $now = Carbon::now();
        $wedding_planners=WeddingPlanner::where('agent_id',$agent_id)
            ->where('wedding_date','>',$now)
            ->orderBy('wedding_date','DESC')->get();
        $weddings=Weddings::all();
        $hotels = Hotels::where('status','Active')->get();
        return view('main.wedding-planner',[
            'now'=>$now,
            'wedding_planners'=>$wedding_planners,
            'weddings'=>$weddings,
            'hotels'=>$hotels,
        ]);
    }
    // VIEW EDIT WEDDING PLANNER
    public function view_edit_wedding_planner($id){
        $wedding_planner = WeddingPlanner::find($id);
        if ($wedding_planner) {
            $agent_id = Auth::user()->id;
            if ($wedding_planner->agent_id == $agent_id) {
                $now = Carbon::now();
                $attentions = Attention::where('page','edit-wedding-planner')->get();
                $bride = $wedding_planner->bride;
                $hotel = Hotels::where('id',$wedding_planner->wedding_venue_id)->first();
                $rooms = HotelRoom::where('hotels_id',$hotel->id)->where('status','Active')->get();
                $ceremony_venues = WeddingVenues::where('hotels_id',$hotel->id)->get();
                $ceremony_venue = WeddingVenues::where('id',$wedding_planner->ceremonial_venue_id)->first();
                $reception_venue = WeddingDinnerVenues::where('id',$wedding_planner->dinner_venue_id)->first();
                $reception_venues = WeddingDinnerVenues::where('hotels_id',$hotel->id)
                ->where('status','Active')
                ->get();
                $business = BusinessProfile::where('id','=',1)->first();
                $handled_by = Auth::user()->where('id',$wedding_planner->handled_by)->first();
                $bride_wedding_accommodation = WeddingAccomodations::where('wedding_planner_id',$wedding_planner->id)
                ->where('room_for','Couple')->first();
                if ( $bride_wedding_accommodation) {
                    $bride_suite_villa = HotelRoom::where('id',$bride_wedding_accommodation->rooms_id)->first();
                }else{
                    $bride_suite_villa = null;
                }
                $flights = Flights::where('wedding_planner_id',$wedding_planner->id)->where('status','!=',"Cancel")->get();
                $flight_brides = Flights::where('wedding_planner_id',$wedding_planner->id)
                ->where('status','!=',"Cancel")
                ->where('type',"Bride")
                ->get();
                $flight_invitations = Flights::where('wedding_planner_id',$wedding_planner->id)
                ->where('status','!=',"Cancel")
                ->where('group',"Invitations")
                ->orderBy('time','DESC')
                ->get();
                $invitations_wedding_accommodation = WeddingAccomodations::where('wedding_planner_id',$wedding_planner->id)
                ->where('room_for','Inv')->get();
                $guests = Guests::where('wedding_planner_id',$id)->get();
                $invitations = WeddingInvitations::where('wedding_planner_id',$id)->paginate(8)->withQueryString();
                $cinvitations = WeddingInvitations::where('wedding_planner_id',$id)->get();
                $chunk_invitations = $cinvitations->chunk(10);
                $countries = Countries::all();
                $extra_beds = ExtraBed::where('hotels_id',$hotel->id)->get();
                $transports = Transports::where('status','Active')->get();



                $in = Carbon::parse("2024-08-01");
                $out = Carbon::parse("2024-08-03");
                $testdur = $in->diffInDays($out);

                return view('main.wedding-planner-edit',[
                    'wedding_planner'=>$wedding_planner,
                    'ceremony_venue'=>$ceremony_venue,
                    'ceremony_venues'=>$ceremony_venues,
                    'bride_wedding_accommodation'=>$bride_wedding_accommodation,
                    'invitations_wedding_accommodation'=>$invitations_wedding_accommodation,
                    'bride_suite_villa'=>$bride_suite_villa,
                    'rooms'=>$rooms,
                    'bride'=>$bride,
                    'hotel'=>$hotel,
                    'business'=>$business,
                    'flights'=>$flights,
                    'handled_by'=>$handled_by,
                    'now'=>$now,
                    'flight_invitations'=>$flight_invitations,
                    'invitations'=>$invitations,
                    'cinvitations'=>$cinvitations,
                    'chunk_invitations'=>$chunk_invitations,
                    'guests'=>$guests,
                    'countries'=>$countries,
                    'reception_venue'=>$reception_venue,
                    'reception_venues'=>$reception_venues,
                    'extra_beds'=>$extra_beds,
                    'attentions'=>$attentions,
                    'transports'=>$transports,
                    'testdur'=>$testdur,
                ]);
            }else{
                return redirect('wedding-planner')->with('success','Wedding planner not found');
            }
        }else {
            return redirect('wedding-planner')->with('success','Wedding planner not found');
        }
    }
    // FUNC ADD WEDDING PLANNER
    public function func_add_wedding_planner(Request $request)
    {
        $tgl_sekarang = Carbon::now();
        $now = date("Y-m-d",strtotime($tgl_sekarang));
        $user_id = Auth::user()->id;
        $agent = Auth::user()->where('id',$user_id)->first();
        if ($agent) {
            $code = $agent->code;
        }else{
            $code = $agent->username;
        }
        $a = $code.date(' ymd',strtotime($now))." A";
        $b = $code.date(' ymd',strtotime($now))." B";
        $c = $code.date(' ymd',strtotime($now))." C";
        $d = $code.date(' ymd',strtotime($now))." D";
        $e = $code.date(' ymd',strtotime($now))." E";
        $f = $code.date(' ymd',strtotime($now))." F";
        $g = $code.date(' ymd',strtotime($now))." G";
        $h = $code.date(' ymd',strtotime($now))." H";
        $i = $code.date(' ymd',strtotime($now))." I";
        $j = $code.date(' ymd',strtotime($now))." J";
        $k = $code.date(' ymd',strtotime($now))." K";
        $l = $code.date(' ymd',strtotime($now))." L";
        $m = $code.date(' ymd',strtotime($now))." M";
        $n = $code.date(' ymd',strtotime($now))." N";
        $o = $code.date(' ymd',strtotime($now))." O";
        $p = $code.date(' ymd',strtotime($now))." P";
        $q = $code.date(' ymd',strtotime($now))." Q";
        $r = $code.date(' ymd',strtotime($now))." R";
        $s = $code.date(' ymd',strtotime($now))." S";
        $t = $code.date(' ymd',strtotime($now))." T";
        $u = $code.date(' ymd',strtotime($now))." U";
        $v = $code.date(' ymd',strtotime($now))." V";
        $w = $code.date(' ymd',strtotime($now))." W";
        $x = $code.date(' ymd',strtotime($now))." X";
        $y = $code.date(' ymd',strtotime($now))." Y";
        $z = $code.date(' ymd',strtotime($now))." Z";
       
        $wp = WeddingPlanner::where('wedding_planner_no', $a)
        ->orWhere('wedding_planner_no', $b)
        ->orWhere('wedding_planner_no', $c)
        ->orWhere('wedding_planner_no', $d)
        ->orWhere('wedding_planner_no', $e)
        ->orWhere('wedding_planner_no', $f)
        ->orWhere('wedding_planner_no', $g)
        ->orWhere('wedding_planner_no', $h)
        ->orWhere('wedding_planner_no', $i)
        ->orWhere('wedding_planner_no', $j)
        ->orWhere('wedding_planner_no', $k)
        ->orWhere('wedding_planner_no', $l)
        ->orWhere('wedding_planner_no', $m)
        ->orWhere('wedding_planner_no', $n)
        ->orWhere('wedding_planner_no', $o)
        ->orWhere('wedding_planner_no', $p)
        ->orWhere('wedding_planner_no', $q)
        ->orWhere('wedding_planner_no', $r)
        ->orWhere('wedding_planner_no', $s)
        ->orWhere('wedding_planner_no', $t)
        ->orWhere('wedding_planner_no', $u)
        ->orWhere('wedding_planner_no', $v)
        ->orWhere('wedding_planner_no', $w)
        ->orWhere('wedding_planner_no', $x)
        ->orWhere('wedding_planner_no', $y)
        ->orWhere('wedding_planner_no', $z)
        ->get();
        $cwp = count($wp);
        

        if ($cwp == 0) {
            $wedding_planner_no = $a;
        }elseif($cwp == 1){
            $wedding_planner_no = $b;
        }elseif($cwp == 2){
            $wedding_planner_no = $c;
        }elseif($cwp == 3){
            $wedding_planner_no = $d;
        }elseif($cwp == 4){
            $wedding_planner_no = $e;
        }elseif($cwp == 5){
            $wedding_planner_no = $f;
        }elseif($cwp == 6){
            $wedding_planner_no = $g;
        }elseif($cwp == 7){
            $wedding_planner_no = $h;
        }elseif($cwp == 8){
            $wedding_planner_no = $i;
        }elseif($cwp == 9){
            $wedding_planner_no = $j;
        }elseif($cwp == 10){
            $wedding_planner_no = $k;
        }elseif($cwp == 11){
            $wedding_planner_no = $l;
        }elseif($cwp == 12){
            $wedding_planner_no = $m;
        }elseif($cwp == 13){
            $wedding_planner_no = $n;
        }elseif($cwp == 14){
            $wedding_planner_no = $o;
        }elseif($cwp == 15){
            $wedding_planner_no = $p;
        }elseif($cwp == 16){
            $wedding_planner_no = $q;
        }elseif($cwp == 17){
            $wedding_planner_no = $r;
        }elseif($cwp == 18){
            $wedding_planner_no = $s;
        }elseif($cwp == 19){
            $wedding_planner_no = $t;
        }elseif($cwp == 20){
            $wedding_planner_no = $u;
        }elseif($cwp == 21){
            $wedding_planner_no = $v;
        }elseif($cwp == 22){
            $wedding_planner_no = $w;
        }elseif($cwp == 23){
            $wedding_planner_no = $x;
        }elseif($cwp == 24){
            $wedding_planner_no = $y;
        }elseif($cwp == 25){
            $wedding_planner_no = $z;
        }else{
            $wedding_planner_no = $AA;
        }
        
        $status="Draft";
        $author = Auth::user()->id;
        $wedding_venue_id = $request->hotel_id;
        $hotel = Hotels::where('id',$wedding_venue_id)->first();
        $number_of_invitation = $request->number_of_invitations;
        $wedding_date = date('Y-m-d',strtotime($request->wedding_date));
        $checkin = date('Y-m-d', strtotime($request->checkin))." ".date('H.i',strtotime($hotel->check_in_time));
        $checkout = date('Y-m-d', strtotime($request->checkout))." ".date('H.i',strtotime($hotel->check_out_time));
        $in = Carbon::parse(date('Y-m-d', strtotime($request->checkin)));
        $out = Carbon::parse(date('Y-m-d', strtotime($request->checkout)));
        $duration = $in->diffInDays($out);
        $service = "Wedding Planner";
        $type = "Custom";

        $brides =new Brides([
            "bride"=>$request->bride_name,
            "bride_chinese"=>$request->bride_chinese,
            "bride_contact"=>$request->bride_contact,
            "groom"=>$request->groom_name,
            "weddingDate"=>$wedding_date,
            "groom_chinese"=>$request->groom_chinese,
            "groom_contact"=>$request->groom_contact,
        ]);
        $brides->save();

        $weddingPlanner =new WeddingPlanner([
            "wedding_planner_no"=>$wedding_planner_no,
            "type"=>$type,
            "bride_id"=>$brides->id,
            "wedding_date"=>$wedding_date,
            "number_of_invitations"=>$number_of_invitation,
            "wedding_venue_id"=>$wedding_venue_id,
            "agent_id"=>$author,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "duration"=>$duration,
            "status"=>$status,
        ]);
        $weddingPlanner->save();
        // dd($weddingPlanner);

        $orderWedding = new OrderWedding([
            "orderno"=>$wedding_planner_no,
            "service"=>$service,
            "wedding_planner_id"=>$weddingPlanner->id,
            "hotel_id"=>$wedding_venue_id,
            "duration"=>$duration,
            "wedding_date"=>$wedding_date,
            "brides_id"=>$brides->id,
            "number_of_invitation"=>$number_of_invitation,
        ]);
        $orderWedding->save();
        
        return redirect("/edit-wedding-planner-$weddingPlanner->id")->with('success', 'Wedding Planner has been created');
    }


    // FUNC ADD WEDDING PLANNER INVITATIONS
    public function func_add_wedding_planner_invitations(Request $request,$id){
        $csex = count($request->sex);
        $sex = $request->sex;
        $name = $request->name;
        $chinese_name = $request->chinese_name;
        $country = $request->country;
        $passport_no = $request->passport_no;
        for ($inv=0; $inv < $csex; $inv++) { 
            $invitation = new WeddingInvitations([
                'wedding_planner_id'=>$id,
                'sex'=>$sex[$inv],
                'name'=>$name[$inv],
                'chinese_name'=>$chinese_name[$inv],
                'country'=>$country[$inv],
                'passport_no'=>$passport_no[$inv],
            ]);
            $invitation->save();
        }
        return redirect("edit-wedding-planner-$id#invitations")->with('success','New invitation has been add to the wedding planner');
    }

    // FUNC UPDATE WEDDING PLANNER INVITATIONS
    public function func_update_wedding_planner_invitations(Request $request,$id){
        $invitation = WeddingInvitations::find($id);
        $invitation->update([
            'sex'=>$request->sex,
            'name'=>$request->name,
            'passport_no'=>$request->passport_no,
        ]);
        // dd($invitation);
        return redirect()->back()->with('success','Invitation has been updated');
    }

    // FUNC ADD WEDDING PLANNER BRIDE'S FLIGHT
    public function func_add_wedding_planner_brides_flight(Request $request,$id){
        $wedding_planner_flight = WeddingPlanner::find($id);
        $wedding_planner_flight->update([
            "arrival_flight"=>$request->arrival_flight,
            "arrival_time"=>$request->arrival_time,
            "departure_flight"=>$request->departure_flight,
            "departure_time"=>$request->departure_time,
        ]);
        $flightsData = [
            [
                "type" => "Arrival",
                "flight" => $request->arrival_flight,
                "time" => $request->arrival_time,
                "number_of_guests" => 2,
                "wedding_planner_id" => $id,
                "status" => "Active",
            ],
            [
                "type" => "Departure",
                "flight" => $request->departure_flight,
                "time" => $request->departure_time,
                "number_of_guests" => 2,
                "wedding_planner_id" => $id,
                "status" => "Active",
            ],
            
        ];
        Flights::insert($flightsData);
        return redirect()->back()->with('success','Flight has been add to the wedding planner');
    }

    // FUNC UPDATE WEDDING PLANNER BRIDE'S FLIGHT
    public function func_update_wedding_planner_bride_flight(Request $request,$id){
        $weddingPlanner = WeddingPlanner::find($id);
        $wedding_planner_id = $request->wedding_planner_id;
        $flights = Flights::where('wedding_planner_id', $id )
        ->where('group',"Bride")
        ->where('status','!=',"Cancel")->get();
        if (count($flights)>0) {
            foreach ($flights as $flight) {
                $flight_schedule = Flights::where('id',$flight->id)->first();
                if ($flight_schedule->type == "Arrival") {
                    $flight_schedule->update([
                        "flight"=>$request->arrival_flight,
                        "time"=>$request->arrival_time,
                        "status"=>$request->arrival_flight_status,
                    ]);
                }else{
                    $flight_schedule->update([
                        "flight"=>$request->departure_flight,
                        "time"=>$request->departure_time,
                        "status"=>$request->departure_flight_status,
                    ]);
                }
            }
        }
        if ($request->arrival_flight_status == "Cancel" or $request->departure_flight_status == "Cancel") {
            $weddingPlanner->update([
                "arrival_flight"=>"",
                "arrival_time"=>"",
                "departure_flight"=>"",
                "departure_time"=>"",
            ]);
        }else{
            $weddingPlanner->update([
                "arrival_flight"=>$request->arrival_flight,
                "arrival_time"=>$request->arrival_time,
                "departure_flight"=>$request->departure_flight,
                "departure_time"=>$request->departure_time,
            ]);
        }
        return redirect("/edit-wedding-planner-$id")->with('success','Flight schedule has been update');
    }
    
   
    // FUNC ADD TRANSPORT TO WEDDING PLANNER
    public function func_add_transport_to_wedding_planner(Request $request,$id){
        $wedding_planner = WeddingPlanner::where('id',$id)->first();
        $hotel = Hotels::where('id',$wedding_planner->wedding_venue_id)->first();
        $date = date('Y-m-d',strtotime($request->date))." ".date('H.i',strtotime($request->time));
        if ($request->type == "Airport Shuttle") {
            $duration = $hotel->airport_duration;
            $distance = $hotel->airport_distance;
        }else{
            $duration = null;
            $distance = null;
        }
        $wedding_transport = new WeddingPlannerTransport([
            "wedding_planner_id"=>$id,
            "transport_id"=>$request->transport_id,
            "type"=>$request->type,
            "date"=>$date,
            "passenger"=>$request->passenger,
            "number_of_guests"=>$request->number_of_guests,
            "duration"=>$duration,
            "distance"=>$distance,
            "price"=>$request->price,
        ]);
        $wedding_transport->save();
        return redirect("/edit-wedding-planner-$wedding_planner->id#weddingTransport")->with('success','Transportation has been added to the wedding planner');
    }
    // FUNC DESTROY WEDDING PLANNER TRANSPORT
    public function func_remove_transport_from_wedding_planner(Request $request,$id){
        $wedding_planner_transport = WeddingPlannerTransport::find($id);
        $wp_id = $wedding_planner_transport->wedding_planner_id;
        $wedding_planner_transport->delete();
        return redirect("edit-wedding-planner-$wp_id#weddingTransport")->with('success','Wedding invitation has been change');
    }

    // FUNC UPDATE TRANSPORT FROM WEDDING PLANNER
    public function func_update_transport_from_wedding_planner(Request $request,$id){
        $transport = WeddingPlannerTransport::find($id);
        $wedding_planner = WeddingPlanner::where('id',$transport->wedding_planner_id)->first();
        $hotel = Hotels::where('id',$wedding_planner->wedding_venue_id)->first();
        $date = date('Y-m-d',strtotime($request->date))." ".date('H.i',strtotime($request->time));
        if ($request->type == "Airport Shuttle") {
            $duration = $hotel->airport_duration;
            $distance = $hotel->airport_distance;
        }else{
            $duration = null;
            $distance = null;
        }
        $transport->update([
            "transport_id"=>$request->edit_transport_id,
            "type"=>$request->type,
            "date"=>$date,
            "passenger"=>$request->passenger,
            "number_of_guests"=>$request->number_of_guests,
            "duration"=>$duration,
            "distance"=>$distance,
            "price"=>$request->price,
        ]);
        return redirect("/edit-wedding-planner-$wedding_planner->id#weddingTransport")->with('success','Transport has been updated');
    }

    // FUNC ADD CEREMONIAL VENUE TO WEDDING PLANNER
    public function func_add_ceremony_venue_to_wedding_planner(Request $request,$id){
        $wedding_planner_id = $request->wedding_planner_id;
        $wedding_planner = WeddingPlanner::where('id',$wedding_planner_id)->first();
        $wedding_planner->update([
            "ceremonial_venue_id"=>$id,
            "slot"=>$request->slot,
        ]);
        
        return redirect("/edit-wedding-planner-$wedding_planner->id#weddingPlannerService")->with('success','Ceremonial venue has been added to the wedding planner');
    }
    // FUNC ADD RECEPTION VENUE TO WEDDING PLANNER
    public function func_add_reception_venue_to_wedding_planner(Request $request,$id){
        $wedding_planner_id = $request->wedding_planner_id;
        $wedding_planner = WeddingPlanner::where('id',$wedding_planner_id)->first();
        $wedding_planner->update([
            "dinner_venue_id"=>$id,
        ]);
        return redirect("/edit-wedding-planner-$wedding_planner->id#weddingPlannerService")->with('success','Ceremonial venue has been added to the wedding planner');
    }

    // FUNCTION UPDATE WEDDING COUPLE
    public function func_update_wedding_planner_wedding(Request $request,$id){
        $wedding_planner = WeddingPlanner::find($id);
        $hotel = Hotels::where('id',$request->hotel_id)->first();
        $checkin = date('Y-m-d', strtotime($request->checkin))." ".date('H.i',strtotime($hotel->check_in_time));
        $checkout = date('Y-m-d', strtotime($request->checkout))." ".date('H.i',strtotime($hotel->check_out_time));
        $in = Carbon::parse($request->checkin);
        $out = Carbon::parse($request->checkout);
        $duration = $in->diffInDays($out);
        $wedding_planner->update([
            "wedding_date"=>$request->wedding_date,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "duration"=>$duration,
            "number_of_invitations"=>$request->number_of_invitations,
        ]);
        return redirect()->back()->with('success','Wedding detail has been updated');
    }

    // FUNCTION UPDATE WEDDING PLANNER BRIDE
    public function func_update_wedding_planner_bride(Request $request,$id){
        $bride = Brides::find($id);
        $bride->update([
            "groom"=>$request->groom_name,
            "groom_chinese"=>$request->groom_chinese,
            "groom_contact"=>$request->groom_contact,
            "bride"=>$request->bride_name,
            "bride_chinese"=>$request->bride_chinese,
            "bride_contact"=>$request->bride_contact,
        ]);
        return redirect()->back()->with('success','Couple detail has been updated');
    }

    // FUNCTION UPDATE WEDDING PLANNER RECEPTION VENUE
    public function func_update_wedding_planner_reception_venue(Request $request,$id){
        $wedding_planner = WeddingPlanner::find($id);
        $dinner_venue_time_start = date('Y-m-d',strtotime($request->dinner_venue_time_start)).date(' H.i',strtotime($request->time));
        $dinner_venue_time_end = date('Y-m-d',strtotime($request->dinner_venue_time_start))." 23:00";
        $wedding_planner->update([
            "dinner_venue_id"=>$request->dinner_venue_id,
            "dinner_venue_time_start"=>$dinner_venue_time_start,
            "dinner_venue_time_end"=>$dinner_venue_time_end,
        ]);
        
        return redirect("edit-wedding-planner-$id#weddingPlannerService")->with('success','Reception venue has been added to the wedding planner');
    }

    // FUNC UPDATE WEDDING PLANNER CEREMONIAL VENUE
    public function func_update_wedding_planner_ceremonial_venue(Request $request,$id){
        $wedding_planner = WeddingPlanner::find($id);
        $wedding_planner->update([
            "ceremonial_venue_id"=>$request->ceremonial_venue_id,
            "slot"=>$request->slot,
        ]);
        // dd($wedding_order);
        return redirect("edit-wedding-planner-$id#weddingPlannerService")->with('success','Wedding detail has been updated');
    }

    // FUNC DELETE WEDDING PLANNER CEREMONIAL VENUE
    public function func_delete_wedding_planner_ceremonial_venue(Request $request,$id){
        $wedding_planner = WeddingPlanner::find($id);
        $wedding_planner->update([
            "ceremonial_venue_id"=>null,
            "slot"=>null,
        ]);
        // dd($wedding_planner);
        return redirect("edit-wedding-planner-$id#weddingPlannerService")->with('success','Ceremonial venue has been remove from wedding planner');
    }
    // FUNC DELETE WEDDING PLANNER CEREMONIAL VENUE
    public function func_delete_wedding_planner_reception_venue(Request $request,$id){
        $wedding_planner = WeddingPlanner::find($id);
        $wedding_planner->update([
            "dinner_venue_id"=>null,
            "dinner_venue_time_start"=>null,
            "dinner_venue_time_end"=>null,
        ]);
        // dd($wedding_planner);
        return redirect("edit-wedding-planner-$id#weddingPlannerService")->with('success','Reception venue has been remove from wedding planner');
    }
    
    // FUNC DESTROY WEDDING PLANNER
    public function func_destroy_wedding_planner(Request $request,$id){
        $wedding_planner = WeddingPlanner::find($id);
        $wedding_planner->delete();
        return redirect()->back()->with('success','Wedding planner has been deleted');
    }
    // FUNC DESTROY WEDDING PLANNER INVITATION
    public function func_destroy_wedding_planner_invitation(Request $request,$id){
        $invitation = WeddingInvitations::find($id);
        $wp_id = $invitation->wedding_planner_id;
        $invitation->delete();
        return redirect("edit-wedding-planner-$wp_id#Invitations")->with('success','Wedding invitation has been change');
    }
   
    // FUNC DESTROY WEDDING PLANNER BRIDE ACCOMMODATION
    public function func_destroy_wedding_planner_bride_accommodation(Request $request,$id){
        $wedding_accommodation = WeddingAccomodations::find($id);
        $wp_id = $wedding_accommodation->wedding_planner_id;
        $wedding_accommodation->delete();
        return redirect("edit-wedding-planner-$wp_id#accommodations")->with('success','Bride Accommodation has been removed');
    }

    // VIEW EDIT WEDDING ACCOMODATION
    public function view_update_wedding_accommodation($id){
        $countries = Countries::all();
        $wedding_planner = WeddingPlanner::find($id);
        $bride = Brides::where('id',$wedding_planner->bride_id)->first();
        $hotel = Hotels::where('id',$wedding_planner->wedding_venue_id)->first();
        $rooms = HotelRoom::where('hotels_id',$hotel->id)->get();
        $extrabed = ExtraBed::where('hotels_id',$hotel->id)->get();
        $agent_id = Auth::user()->id;
        $invitations = WeddingInvitations::where('wedding_planner_id',$id)->get();
        $accommodation_invs = WeddingAccomodations::where('wedding_planner_id', $wedding_planner->id)
        ->where('room_for',"Inv")
        ->get();
        $accommodation_couple = WeddingAccomodations::where('wedding_planner_id', $wedding_planner->id)
        ->where('room_for',"Couple")
        ->first();
        if ($accommodation_couple) {
            $room = HotelRoom::where('id',$accommodation_couple->rooms_id)->first();
        }else{
            $room = null;
        }
        return view('main.wedding-accommodations-update',[
            'countries' => $countries,
            'wedding_planner' => $wedding_planner,
            'agent_id' => $agent_id,
            'invitations' => $invitations,
            'accommodation_invs' => $accommodation_invs,
            'accommodation_couple' => $accommodation_couple,
            'hotel' => $hotel,
            'room' => $room,
            'rooms' => $rooms,
            'extrabed' => $extrabed,
            'bride' => $bride,

        ]);
    }

    // FUNCTION UPDATE WEDDING PLANNER BRIDE ACCOMMODATION
    public function func_update_wedding_planner_bride_accommodation(Request $request,$id)
    {
        $wedding_accommodation = WeddingAccomodations::find($id);
        $wedding_accommodation->update([
            "rooms_id"=>$request->rooms_id,
        ]);
        // dd($wedding_planner);
        return redirect("edit-wedding-planner-$wedding_accommodation->wedding_planner_id#accommodations")->with('success','Bride accommodation has been updated');
    }
    // FUNCTION UPDATE WEDDING PLANNER INVITATIONS ACCOMMODATION
    public function func_update_wedding_planner_invitations_accommodation(Request $request,$id)
    {
        $wedding_accommodation = WeddingAccomodations::find($id);
        $wedding_accommodation->update([
            "rooms_id"=>$request->rooms_id,
            "number_of_guests"=>$request->number_of_guests,
            "guest_detail"=>$request->guest_detail,
            "extra_bed_id"=>$request->extra_bed_id,
            "remark"=>$request->remark,
        ]);
        // dd($wedding_accommodation);
        return redirect("edit-wedding-planner-$wedding_accommodation->wedding_planner_id#accommodations")->with('success','Invitations accommodation has been updated');
    }

    // FUNCTION ADD WEDDING PLANNER ACCOMMODATION
    public function func_add_wedding_planner_accommodation(Request $request,$id)
    {
        $request->validate([
            'rooms_id'=>'required',
            'room_for'=>'required|in:Couple,Inv',
            'guests_detail'=>'required_if:type,Inv',
            'number_of_guests'=>'required_if:type,Inv',
        ]);
        $wedding_planner = WeddingPlanner::find($id);
        $status = "Requested";
        $checkin = $wedding_planner->checkin;
        $checkout = $wedding_planner->checkout;
        if ($request->room_for == "Couple") {
            $bride = Brides::where('id',$wedding_planner->bride_id)->first();
            $guest_detail = $bride->groom.', '.$bride->bride;
            $number_of_guests = 2;
            $extra_bed_id = null;
        }else{
            $guest_detail = $request->guest_detail;
            $number_of_guests = $request->number_of_guests;
            $extra_bed_id = $request->extra_bed_id;
        }
        $wedding_accommodation =new WeddingAccomodations([
            "wedding_planner_id"=>$wedding_planner->id,
            "room_for"=>$request->room_for,
            "hotels_id"=>$wedding_planner->wedding_venue_id,
            "rooms_id"=>$request->rooms_id,
            "checkin"=>$checkin,
            "checkout"=>$checkout,
            "guest_detail"=>$guest_detail,
            "extra_bed_id"=>$extra_bed_id,
            "number_of_guests"=>$number_of_guests,
            "status"=>$status,
        ]);
        $wedding_accommodation->save();
        
        // dd($wedding_accommodation);
        return redirect("edit-wedding-planner-$wedding_accommodation->wedding_planner_id#accommodations")->with('success','Accomodation has been added');
    }
    // FUNCTION ADD WEDDING ACCOMMODATION
    public function func_add_wedding_accommodations(Request $request,$id)
    {
        $wedding_planner = WeddingPlanner::find($id);
        $order_wedding = OrderWedding::where('wedding_planner_id',$id)->first();
        $rooms_id = $request->wedding_room_id;
        $room_for = "Inv";
        $rid = count($rooms_id);
        $status = "Requested";
        for ($i=0; $i < $rid; $i++) { 
            $wedding_accomodations =new WeddingAccomodations([
                "wedding_planner_id"=>$id,
                "room_for"=>$room_for,
                "hotels_id"=>$wedding_planner->wedding_venue_id,
                "rooms_id"=>$rooms_id[$i],
                "guest_detail"=>$request->guest_detail[$i],
                "extra_bed_id"=>$request->extra_bed_id[$i],
                "number_of_guests"=>$request->number_of_guests[$i],
                "status"=>$status,
            ]);
            $wedding_accomodations->save();
        }
        // dd($wedding_accomodations);
        return redirect("edit-wedding-planner-$wedding_accommodation->wedding_planner_id#accommodations")->with('success','Accomodation has been added');
    }

    public function func_submit_wedding_planner(Request $request,$id){
        $wedding_planner = WeddingPlanner::find($id);
        $wedding_planner->update([
            "status"=>"Pending",
        ]);
        // CREATE ORDER WEDDING DINNER VENUE
        if ($wedding_planner->dinner_venue_id) {
            $startTime = date("Y-m-d H.i",strtotime($wedding_planner->dinner_venue_time_start)); 
            $endTime = date("Y-m-d H.i",strtotime($wedding_planner->dinner_venue_time_end));
            $start = Carbon::parse($startTime);
            $end = Carbon::parse($endTime);
            $duration = $end->diffInHours($start);
            $dinner_venue_package = WeddingDinnerPackages::where('id',$wedding_planner->dinner_venue_id)->first();
            $wedding_order = new OrderWedding([
                "orderno"=>$wedding_planner->wedding_planner_no,
                "wedding_planner_id"=>$wedding_planner->id,
                "service"=>"Dinner Venue",
                "service_id"=>$wedding_planner->dinner_venue_id,
                "number_of_invitation"=>$wedding_planner->number_of_invitations,
                "date_start"=>$wedding_planner->dinner_venue_time_start,
                "date_end"=>$wedding_planner->dinner_venue_time_end,
                "duration"=>$duration,
            ]);
        }
        Mail::to(config('app.reservation_mail'))
        ->send(new ReservationMail($id,$rquotation));
        // dd($wedding_planner);
        return redirect("edit-wedding-planner-$id#weddingPlannerService")->with('success','Wedding planner has been send');
    }
}
