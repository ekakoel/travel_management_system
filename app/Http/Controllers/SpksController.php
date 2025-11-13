<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Spks;
use App\Models\User;
use App\Models\Guests;
use GuzzleHttp\Client;
use App\Models\Drivers;
use App\Models\Transports;
use App\Models\Reservation;
use Illuminate\Http\Request;
use GuzzleHttp\TransferStats;
use App\Models\AirportShuttle;
use App\Models\SpkDestinations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\StoreSpksRequest;
use App\Http\Requests\UpdateSpksRequest;
use Illuminate\Support\Facades\Validator;


class SpksController extends Controller
{
     public function __construct()
    {
        $this->middleware(['auth','verified','type:admin']);
    }

    public function index(Request $request)
    {
        $today = Carbon::now()->format('Y-m-d');
        $expired_date = Carbon::now()->subDay()->format('Y-m-d');
        // ✅ Update Status SPK langsung dengan query tanpa loop
        Spks::whereDate('spk_date',$today)
            ->where('status', 'Pending')
            ->update(['status' => 'In Progress']);

        $spk_in_progress = Spks::with('destinations') // pastikan relasi sudah ada
        ->where('status', 'In Progress')
        ->get();
        
        $operator = User::where('type','admin')->where('Status','Active')->orderBy('name', 'asc')->get();

        foreach ($spk_in_progress as $spk) {
            // Cek apakah semua destinasi sudah berstatus "Visited"
            $allVisited = $spk->destinations->every(function ($destination) {
                return $destination->status === 'Visited';
            });

            if ($allVisited && $spk->destinations->count() > 0 && $spk->spk_date < $expired_date) {
                // Update status SPK menjadi "Complete"
                $spk->update(['status' => 'Completed']);
            }
        }

        $xpiredSpks = Spks::where('spk_date','<', $expired_date)
            ->whereNotIn('status', ['Completed', 'Canceled'])
            ->pluck('id')
            ->unique()
            ->toArray();
        Spks::whereIn('id', $xpiredSpks)
            ->update(['status' => 'Expired']);

        // ✅ Data utama
        $transports   = Transports::where('status', 'Active')->get();
        $spks         = Spks::with(['driver', 'transport'])
            ->whereNotIn('status', ['Completed', 'Canceled'])
            ->where('spk_date','>=', $expired_date)
            ->orderBy('spk_date', 'asc')
            ->get();

        // ✅ Query archive dengan filter dinamis
        $query = Spks::where('spk_date','<=', $expired_date)
            ->whereNotIn('status', ['Pending', 'In Progress']);

        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%' . $request->order_number . '%');
        }


        $spk_archives = Spks::where('spk_date','<=', $expired_date)
            ->whereNotIn('status', ['Pending', 'In Progress'])
            ->orderBy('spk_date', 'DESC')
            ->get();
            // ->paginate(10);

        // ✅ Return partial kalau AJAX
        if ($request->ajax()) {
            return view('admin.transportmanagement.partials.spk-archive', compact('spk_archives'))->render();
        }

        $vehicles = Transports::where('status','Active')->get();
        $drivers  = Drivers::where('status','Active')->get();
        $statusColors = [
            'Pending' => 'bg-secondary',
            'In Progress' => 'bg-dark',
            'Completed' => 'bg-primary'
        ];
        return view('admin.transportmanagement.spks.index', compact(
            'transports',
            'spks',
            'vehicles',
            'drivers',
            'spk_archives',
            'statusColors',
            'operator'
        ), [
            "now" => now()->format('Y-m-d'),
            "today" => $today
        ]);
    }

    public function view_transport_management(Request $request)
    {
        $today = now()->toDateString();

        // ✅ Update Status SPK langsung dengan query tanpa loop
        Spks::whereDate('spk_date',$today)
            ->where('status', '!=', 'In Progress')
            ->update(['status' => 'In Progress']);

        // ✅ Update Reservation status -> Completed jika semua destinasi sudah dikunjungi
        $rsvs = Reservation::with(['spks.destinations'])
            ->where('service', 'Transport')
            ->where('status', '!=', 'Completed')
            ->get();

        foreach ($rsvs as $rsv) {
            $allVisited = $rsv->spks->isNotEmpty() && $rsv->spks->every(function ($spk) {
                return $spk->destinations->isNotEmpty() &&
                    $spk->destinations->every(fn($d) => $d->status === 'Visited');
            });

            // Cek apakah reservation_date sudah lewat hari ini
            $isCheckoutPassed = $rsv->reservation_date < $today;

            if ($allVisited || $isCheckoutPassed) {
                $rsv->update(['status' => 'Completed']);
            }
        }

        // ✅ Data utama
        $transports   = Transports::where('status', 'Active')->get();
        $spks         = Spks::with(['reservation', 'driver', 'transport'])->get();
        $reservations = Reservation::whereNotIn('status', ['Cancelled', 'Completed'])
            ->where('service', 'Transport')
            ->orderBy('checkin', 'asc')
            ->get();

        // ✅ Query archive dengan filter dinamis
        $query = Reservation::where('status', 'Completed')
            ->where('service', 'Transport');

        if ($request->filled('rsv_no')) {
            $query->where('rsv_no', 'like', '%' . $request->rsv_no . '%');
        }

        if ($request->filled('checkin')) {
            $query->whereDate('checkin', $request->checkin);
        }

        $reservation_archives = $query
            ->orderBy('checkin', 'asc')
            ->paginate(10);

        // ✅ Return partial kalau AJAX
        if ($request->ajax()) {
            return view('admin.transportmanagement.partials.reservation-archive', compact('reservation_archives'))->render();
        }

        $vehicles = Transports::where('status','Active')->get();
        $drivers  = Drivers::where('status','Active')->get();

        return view('admin.transportmanagement.index', compact(
            'transports',
            'reservations',
            'spks',
            'vehicles',
            'drivers',
            'reservation_archives'
        ), [
            "now" => now()->format('Y-m-d'),
            "today" => $today
        ]);
    }


    public function generate(Request $request)
    {
        $request->validate([
            'order_number'      => 'required|string|max:255',
            'type'              => 'required|string|max:255',
            'number_of_guests'  => 'required|string|max:5',
            'transport_id'      => 'required|exists:transports,id',
            'driver_id'         => 'required|exists:drivers,id',
        ]);


        $prefix = $request->order_number;
        $countToday = Spks::whereDate('created_at', Carbon::today())->count() + 1;
        $spkNumber = $prefix . "-" . str_pad($countToday, 3, '0', STR_PAD_LEFT);

        $spk = $reservation->spks()->create([
            'spk_number'        => $spkNumber,
            'transport_id'      => $request->vehicle_id,
            'driver_id'         => $request->driver_id,
            'spk_date'           => $checkin,
            'number_of_guests'  => $request->number_of_guests,
            'status'            => 'Pending',
        ]);
        session()->flash('highlight_spk', $spk->id);
        // dd($spk);
        return response()->json([
            'success' => true,
            'id' => $spk->id,
            'spk'     => $spk,
            'reservation_id' => $reservation->id,
            'message' => 'SPK generated successfully!',
        ]);
    }
    
    public function create()
    {
        $reservations = Reservation::all();
        $drivers = Driver::all();
        $vehicles = Vehicle::all();

        return view('spks.create', compact('reservations', 'drivers', 'vehicles'));
    }

    
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'order_number'     => 'nullable|string|max:100',
            'operator_id'      => 'nullable|integer|min:1',
            'type'             => 'nullable|string|max:50',
            'driver_id'        => 'nullable|exists:drivers,id',
            'transport_id'     => 'nullable|exists:transports,id',
            'plate_number'     => 'nullable|string|max:50',
            'number_of_guests' => 'nullable|integer|min:1',
            'spk_date'         => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }
        $typeCodes = [
            'Airport Shuttle' => 'AS',
            'Hotel Transfer'  => 'HT',
            'Tour'            => 'TP',
            'Daily Rent'      => 'DR',
        ];
        $spk_date = $request->spk_date ? Carbon::parse($request->spk_date)->format('Y-m-d') : null;
        $typeCode = $typeCodes[$request->type] ?? 'OT';
        $today = now()->toDateString();
        $prefix = "SPK-".$request->order_number."-".$typeCode.Carbon::parse($today)->format('ymd');
        $countSpkDate = Spks::whereDate('spk_date', $spk_date)->count() + 1;
        $spkNumber = $prefix . "-" . str_pad($countSpkDate, 3, '0', STR_PAD_LEFT);
        try {
            DB::beginTransaction();

            $spk = Spks::create([
                'order_number'     => $request->order_number,
                'operator_id'      => $request->operator_id,
                'type'             => $request->type,
                'driver_id'        => $request->driver_id,
                'transport_id'     => $request->transport_id,
                'plate_number'     => $request->plate_number,
                'spk_number'       => $spkNumber,
                'number_of_guests' => $request->number_of_guests,
                'spk_date'         => $spk_date,
                'status'           => 'Pending',
            ]);

            DB::commit();
            return redirect()->route('view.transport-management.index')->with('success','SPK berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('view.transport-management.index')->with('success','SPK berhasil ditambahkan');
        }
    }

    public function func_add_spk_destination(Request $request, $id)
    {
        $spks = Spks::findOrFail($id);
        $status = "Pending";
        $result = $this->getLatLngFromShortUrl($request->destination_address);
        $latitude = $result['latitude'] ?? null;
        $longitude = $result['longitude'] ?? null;
        $time = $request->time ? Carbon::parse($request->time)->format('H:i:s') : null;
        $spk_date = $request->date;
        $date = $spk_date ? Carbon::parse($spk_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        if ($date && $time) {
            $dateTime = $date.' '. $time;
        } else {
            $dateTime = null;
        }
        $spk_destination = new SpkDestinations([
            'spk_id' => $id,
            'date' => $dateTime,
            'destination_name' => $request->destination_name,
            'destination_address' => $request->destination_address,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'description' => $request->description,
            'status' => $status,
        ]);
        $spk_destination->save();

        return redirect()->route('view.detail-spk',$id)->with('success', 'Spks created successfully.');
    }
    
    public function func_update_spk(Request $request, $id)
    {
        $spk = Spks::findOrFail($id);
        // dd($request->all());
        $validated = $request->validate([
            'status' => 'required|string|max:255',
            'order_number' => 'required|string|max:255',
            'spk_type' => 'required|string|max:255',
            'spk_date' => 'required|date',
            'number_of_guests' => 'required|integer|min:1',
            'transport_id' => 'required|exists:transports,id',
            'plate_number' => 'required|string',
            'driver_id' => 'required|exists:drivers,id',
        ]);
        $spk_date = Carbon::parse($validated['spk_date'])->format('Y-m-d');

        $spk->update([
            'status' => $request->status,
            'order_number' => $validated['order_number'],
            'type' => $validated['spk_type'],
            'spk_date' => $spk_date,
            'number_of_guests' => $validated['number_of_guests'],
            'transport_id' => $validated['transport_id'],
            'plate_number' => $validated['plate_number'],
            'driver_id' => $validated['driver_id'],
        ]);

        return redirect()->route('view.detail-spk',$id)->with('success', 'Spks has been updated.');
    }

    public function func_update_spk_destination(Request $request, $id)
    {
        $validated = $request->validate([
            'destination_name' => 'required|string|max:255',
            'destination_address' => 'required|string',
            'description' => 'nullable|string',
        ]);
        $now = Carbon::now();
        $spks_destination = SpkDestinations::with('spk')->findOrFail($id);
        $spk = $spks_destination->spk;
        $time = $request->time ? Carbon::parse($request->time)->format('H:i') : null;
        $spk_date = $request->date;
        $date = $spk_date ? Carbon::parse($spk_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $dateTime = ($date && $time) ? $date.' '.$time : null;
        $destination_name = $request->destination_name;
        $destination_address = $request->destination_address;
        $result = $this->getLatLngFromShortUrl($request->destination_address);
        $latitude = $result['latitude'] ?? null;
        $longitude = $result['longitude'] ?? null;
        // dd($request->time);
        $spks_destination->update([
            "date"=>$dateTime,
            "destination_name"=>$destination_name,
            "destination_address"=>$destination_address,
            "longitude"=>$longitude,
            "latitude"=>$latitude,
            "description"=>$request->description,
        ]);
        return redirect()->route('view.detail-spk',$spk->id)->with('success', 'Spks has been updated.');
    }

    
    public function show($id)
    {
        $now = Carbon::now();
        $spk = Spks::with([
            'driver', 
            'airport_shuttles', 
            'transport', 
            'destinations',
            'guests'
        ])->findOrFail($id);
        // if (date('Y-m-d',strtotime($spk->spk_date)) < $now) {
        //     return redirect()->route('view.transport-management.index')->with('success', 'Spks tidak terdaftar atau tidak ditemukan!.');
        // }
        $destinationsJson = $spk->destinations->map(function($d){
            return [
                'lat' => (float)$d->latitude,
                'lng' => (float)$d->longitude,
                'name' => $d->destination_name ?? '',
                'status' => $d->status ?? 'Pending'
            ];
        })->toJson();
        $vehicles = Transports::where('status','Active')->get();
        $drivers = Drivers::where('status','Active')->get();
        $guests = $spk->guests;
        $airport_shuttles = $spk->airport_shuttles;
        $bgStatus = [
            'Pending' => 'bg-secondary',
            'In Progress' => 'bg-primary'
        ];
        return view('admin.transportmanagement.spks.detail-spk', compact(
            'spk',
            'vehicles',
            'drivers',
            'guests',
            'airport_shuttles',
            'bgStatus',
            'destinationsJson'
        ));
    }

    public function edit(Spks $spk)
    {
        $reservations = Reservation::all();
        $drivers = Driver::all();
        $vehicles = Vehicle::all();

        return view('spks.edit', compact('spk', 'reservations', 'drivers', 'vehicles'));
    }

    public function update(Request $request, Spks $spk)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'spk_number' => 'required|string|unique:spks,spk_number,' . $spk->id,
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $spk->update($request->all());

        return redirect()->route('spks.index')->with('success', 'Spks updated successfully.');
    }

    public function func_delete_spk_destination($id)
    {
        try {
            $spk_destination = SpkDestinations::findOrFail($id);
            $spk_destination->delete();
            return redirect()->back()->with('success', 'SPK berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus SPK');
        }
    }

    public function destroy($id)
    {
        try {
            $spk = Spks::findOrFail($id);
            $spk->delete();
            if (request()->ajax()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'SPK berhasil dihapus',
                    'id'      => $id
                ]);
            }
            return redirect()->back()->with('success', 'SPK berhasil dihapus');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Gagal menghapus SPK'
                ], 500);
            }
            return redirect()->back()->with('error', 'Gagal menghapus SPK');
        }
    }

    public static function getLatLngFromShortUrl($shortUrl)
    {
        $finalUrl = null;
        $client = new Client([
            'allow_redirects' => true,
            'on_stats' => function (TransferStats $stats) use (&$finalUrl) {
                $finalUrl = (string) $stats->getEffectiveUri();
            }
        ]);
        $client->get($shortUrl);
        if ($finalUrl) {
            // Cari pola latitude, longitude dari URL final
            preg_match('/(-?\d+\.\d+),\s*(-?\d+\.\d+)/', $finalUrl, $matches);
            if (count($matches) >= 3) {
                return [
                    'latitude' => $matches[1],
                    'longitude' => $matches[2],
                ];
            }
        }
        return null;
    }
    public function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $response = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json", [
            'units' => 'metric',
            'origins' => "$lat1,$lng1",
            'destinations' => "$lat2,$lng2",
            'key' => $apiKey,
        ]);
        $data = $response->json();
        if (isset($data['rows'][0]['elements'][0]['distance'])) {
            return [
                'distance' => $data['rows'][0]['elements'][0]['distance']['text'], // e.g. "12.3 km"
                'value' => $data['rows'][0]['elements'][0]['distance']['value'] / 1000, // in km
                'duration' => $data['rows'][0]['elements'][0]['duration']['text'] // waktu tempuh
            ];
        }
        return null;
    }
    public function spk_detail($id)
    {
        $spk = Spks::with(['driver', 'transport', 'reservation', 'destinations'])->findOrFail($id);
        $html = view('partials.spk-detail-body', compact('spk'))->render();
        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    // Print SPK dengan QR Code
    public function print($id)
    {
        $spk = Spks::with('reservation', 'destinations', 'guests')->findOrFail($id);

        return view('admin.transportmanagement.print', compact('spk'));
    }
    

    public function func_add_guest(Request $request, $id)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'name_mandarin'         => 'nullable|string|max:255',
            'sex'                   => 'required|in:m,f',
            'age'                   => 'required|in:Adult,Child',
            'phone'                 => 'nullable|string|max:20',
        ]);

        $guest = new Guests([
            'spk_id' => $id,
            'name' => $request->name,
            'name_mandarin' => $request->name_mandarin,
            'sex' => $request->sex,
            'age' => $request->age,
            'phone' => $request->phone
        ]);
        $guest->save();

        return redirect()->back()->with('success', 'Guest berhasil ditambahkan ke SPK.');
    }

    public function func_delete_guest($id)
    {
        $guest = Guests::findOrFail($id);
        $guest->delete();

        return redirect()->back()->with('success', 'Guest berhasil dihapus dari SPK.');
    }
    public function func_delete_airport_shuttle($id)
    {
        $airport_shuttle = AirportShuttle::findOrFail($id);
        $airport_shuttle->delete();

        return redirect()->back()->with('success', 'Airport Shuttle berhasil dihapus dari SPK');
    }

    public function func_update_airport_shuttle(Request $request, $id)
    {
        $airport_shuttle = AirportShuttle::findOrFail($id);
        $spk = Spks::findOrFail($airport_shuttle->spk_id);
        if (!$airport_shuttle) {
            return redirect()->back()->with('error', 'Airport Shuttle gagal diupdate!');
        }
        $nav = $request->nav;
        $flight_date = Carbon::parse($request->flight_date)->format('Y-m-d');
        $flight_time = Carbon::parse($request->flight_time)->format('H:i');
        $date = $flight_date.' '.$flight_time;
        $transport_id = $spk->transport_id;
        $airport_shuttle->update([
            "date"=>$date,
            "flight_number"=>$request->flight_number,
            "nav"=>$nav,
        ]);
        return redirect()->back()->with('success', 'Airport Shuttle berhasil diubah!');
    }
    public function func_update_spk_guest(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'name_mandarin' => 'nullable|string|max:255',
            'sex'           => 'required|in:m,f',
            'age'           => 'required|in:Adult,Child',
            'phone'         => 'nullable|string|max:20',
        ]);

        $guest = Guests::findOrFail($id);

        $guest->update([
            'name'          => $request->name,
            'name_mandarin' => $request->name_mandarin,
            'sex'           => $request->sex,
            'age'           => $request->age,
            'phone'         => $request->phone,
        ]);

        return redirect()->back()->with('success', 'Guest data updated successfully!');
    }


    // FUNCTION ADD AIRPORT SHUTTLE
    public function func_add_airport_shuttle(Request $request, $id){
        $spk = Spks::findOrFail($id);
        if (!$spk) {
            return redirect()->back()->with('error', 'SPK can not be found!');
        }
        $nav = $request->nav;
        $number_of_guests = $spk->number_of_guests;
        $flight_date = Carbon::parse($request->flight_date)->format('Y-m-d');
        $flight_time = Carbon::parse($request->flight_time)->format('H:i');
        $date = $flight_date.' '.$flight_time;
        $transport_id = $spk->transport_id;
        $airportshuttle = new AirportShuttle([
            "date"=>$date,
            "spk_id"=>$id,
            "flight_number"=>$request->flight_number,
            "number_of_guests"=>$number_of_guests,
            "transport_id"=>$transport_id,
            "nav"=>$nav,
        ]);
        $airportshuttle->save();
        return redirect()->back()->with('success','Airport Shuttle has been added');
    }

}
