<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Spks;
use App\Models\Drivers;
use App\Models\Transports;
use Illuminate\Http\Request;
use App\Models\SpkDestinations;

class TransportManagementController extends Controller
{
    // Halaman check-in driver
    public function checkinPage(SpkDestinations $spkDestination)
    {
        return view('admin.transportmanagement.checkin', compact('spkDestination'));
    }
    public function show_spk(Request $request,$id,$spkNumber)
    {
       $spk = Spks::with([
            'driver', 
            'transport', 
            'destinations',
            'guests'
        ])->where('spk_number',$spkNumber)->first();
        
        if ($spk) {
            $now = Carbon::now();
            $expired_date = Carbon::now()->subDay()->format('Ymd');
            $vehicles = Transports::where('status','Active')->get();
            $drivers = Drivers::where('status','Active')->get();
            $guests = $spk->guests;
            $spk_date = date("Ymd",strtotime($spk->spk_date));
            if ( $spk_date < $expired_date) {
                return view('admin.transportmanagement.spks.expired', compact(
                    'now',
                    'spk',
                    'vehicles',
                    'drivers',
                    'guests',
                    'expired_date',
                ));
            }else{
                return view('admin.transportmanagement.spk', compact(
                    'now',
                    'spk',
                    'vehicles',
                    'drivers',
                    'guests',
                ));
            }
            
        }else{
            return view('admin.transportmanagement.spk-not-found');
        }
    }
    public function expired_spks(Request $request,$id,$spkNumber)
    {
       $spk = Spks::with([
            'driver', 
            'transport', 
            'destinations',
            'guests'
        ])->where('spk_number',$spkNumber)->first();
        return view('admin.transportmanagement.spks.expired', compact(
            'spk',
        ));
    }

    // Proses check-in
    public function checkin(Request $request, $id)
    {
        try {
            $request->validate([
                'latitude' => 'required',
                'longitude' => 'required',
            ]);
            $spkDestination = SpkDestinations::with('spk')->findOrFail($id);
            $spk = $spkDestination->spk;
            $spkDestination->update([
                'visited_at' => now(),
                'status' => 'Visited',
                'checkin_latitude' => $request->latitude,
                'checkin_longitude' => $request->longitude,
                'checkin_map_link' => "https://maps.google.com/?q={$request->latitude},{$request->longitude}"
            ]);

            return redirect()->route('view.spk', [
                'id' => $spk->id,
                'spkNumber' => $spk->spk_number
            ])->with('success', 'Check-in berhasil dicatat!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat check-in: ' . $e->getMessage());
        }
    }

}
