<?php

namespace App\Http\Controllers;

use App\Models\Guests;
use App\Models\Flights;
use Illuminate\Http\Request;
use App\Http\Requests\StoreFlightsRequest;
use App\Http\Requests\UpdateFlightsRequest;

class FlightsController extends Controller
{
    // FUNC ADD WEDDING PLANNER INVITATIONS FLIGHT
    public function func_add_wedding_planner_invitations_flight(Request $request,$id){
        $status = "Active";
        $group = "Invitations";
        $wedding_planner_id = $request->wedding_planner_id;
        $type = $request->type;
        $time = $request->time;
        $flight = $request->flight;
        $guest_name = $request->guest_name;
        $contact = $request->contact;
        $number_of_guests = $request->number_of_guests;
        $sex = "o";
        $cflight = count($request->flight);
        for ($i=0; $i < $cflight; $i++) { 
            $time_formated = date('Y-m-d H.i',strtotime($time[$i]));
            $guests = new Guests([
                'name'=>$guest_name[$i],
                'sex'=>$sex,
                'phone'=>$contact[$i],
                'wedding_planner_id'=>$wedding_planner_id,
            ]);
            $guests->save();
            $flight_data = new Flights([
                'type'=>$type[$i],
                'group'=>$group,
                'flight'=>$flight[$i],
                'time'=>$time_formated,
                'guests_id'=>$guests->id,
                'number_of_guests'=>$number_of_guests[$i],
                'wedding_planner_id'=>$wedding_planner_id,
                'status'=>$status,
            ]);
            $flight_data->save();
        }
        return redirect()->back()->with('success','Flight has been add to the wedding planner');
    }
    // FUNC UPDATE WEDDING PLANNER INVITATIONS FLIGHT
    public function func_update_wedding_planner_invitations_flight(Request $request,$id){
        $invitation_flight = Flights::find($id);
        $guest = Guests::where('id',$invitation_flight->guests_id)->first();
        $time = date('Y-m-d H.i',strtotime($request->time));
        $invitation_flight->update([
            'type'=>$request->type,
            'flight'=>$request->flight,
            'time'=>$time,
            'number_of_guests'=>$request->number_of_guests,
            'status'=>$request->status,
        ]);
        $guest->update([
            'name'=>$request->guest_name,
            'phone'=>$request->contact,
        ]);
        return redirect("/edit-wedding-planner-$invitation_flight->wedding_planner_id#flightInvitations")->with('success','Flight schedule has been updated');
    }
    
    // FUNC DELETE WEDDING PLANNER INVITATIONS FLIGHT
    public function func_delete_wedding_planner_invitations_flight(Request $request,$id)
    {
        $flight = Flights::findOrFail($id);
        $guest = Guests::where('id',$flight->guests_id)->first();
        $flight->delete();
        $guest->delete();
        return redirect("/edit-wedding-planner-$flight->wedding_planner_id#flightInvitations")->with('success', 'Flight schedule has been deleted');
    }
}
