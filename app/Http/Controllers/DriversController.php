<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Review;
use App\Models\Drivers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DriversController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $drivers = Drivers::select('drivers.*')
        ->addSelect([
            'global_rating' => Review::selectRaw('AVG((driver_punctuality + driver_driving_skills + driver_neatness)/3)')
                ->whereColumn('driver_id', 'drivers.id')
        ])
        ->orderByDesc('global_rating')
        ->get();
        return view('backend.operations.drivers.index',[
            'now'=>$now,
            'drivers'=>$drivers,
        ]);
    }

    public function create(Request $request)
    {
        $name = $request->name;
        $phone = $request->phone;
        $email =$request->email;
        $license =$request->license;
        $address =$request->address;
        $country =$request->country;
        $driver =new Drivers([
            "name"=>$name,
            "phone"=>$phone,
            "email"=>$email,
            "license"=>$license,
            "address"=>$address,
            "country"=>$country,
            "status"=>$request->status ?: 'Active',
            
        ]);
        $driver->save();
        return redirect()->back()->with('success','New Drivers has been created');
    }

    public function edit(Request $request,$id)
    {
        $driver=Drivers::findOrFail($id);
        $driver->update([
            "name" =>$request->name, 
            "phone"=>$request->phone,
            "email"=>$request->email,
            "license"=>$request->license,
            "address"=>$request->address,
            "country"=>$request->country,
            "status"=>$request->status ?: 'Active',
        ]);
        return redirect()->route('drivers-admin.index')->with('success','Drivers has been updated');
    }
    
    public function destroy(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posRsv')) {
            $driver=Drivers::findOrFail($id);
            $driver->delete();
            return back()->with('success','Drivers has been deleted');
        }else{
            return redirect()->route('drivers-admin.index')->with('error','Akses ditolak');
        }
    }
}
