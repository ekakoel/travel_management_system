<?php

namespace App\Http\Controllers;
use Image;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Tours;
use App\Models\LogData;
use App\Models\UserLog;
use App\Models\Partners;
use App\Models\TourType;
use App\Models\UsdRates;
use App\Models\ActionLog;
use App\Models\Attention;
use App\Models\TourPrices;
use App\Models\ToursImages;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoretoursRequest;
use App\Http\Requests\UpdatetoursRequest;

class ToursAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','can:isAdmin']);
    }
    public function index()
    {
        if (Gate::allows('posDev') || Gate::allows('posAuthor')) {
            // Ambil tax dan kurs USD dari cache
            $tax = Cache::remember('tax', 3600, function () {
                return Tax::select('name', 'tax')->where('name', 'tax')->first();
            });

            $usdrates = Cache::remember('usd_rates', 3600, function () {
                return UsdRates::select('name', 'rate')->where('name', 'USD')->first();
            });
            $activetours = Tours::with(['images', 'prices'])
                ->where('status', 'Active')
                ->get();
            $totalTours = $activetours->count();
            $archivetours = Tours::where('status', 'Archived')->get();
            $drafttours = Tours::where('status', 'Draft')->get();
            $activetours->each(function ($tour) use ($usdrates, $tax) {
                $tour->prices->each(function ($price) use ($usdrates, $tax) {
                    $price->calculated_price = $price->calculatePrice($usdrates, $tax);
                });
            });
            return view('admin.toursadmin', compact('activetours','archivetours','drafttours','totalTours','tax', 'usdrates'));
        }
    }
// View Admin Detail Tour =========================================================================================>
    public function view_detail_tour($id)
    {
        $now = Carbon::now();
        $tax = Cache::remember('tax', 3600, function () {
            return Tax::select('name', 'tax')->where('name', 'tax')->first();
        });
        $usdrates = Cache::remember('usd_rates', 3600, function () {
            return UsdRates::select('name', 'rate')->where('name', 'USD')->first();
        });
        
        $user = Auth::user()->all();
        $tour = Tours::with([
            'images',
            'prices' => function($q) use ($now) {
                $q->where('expired_date', '>=', $now);
            }
        ])->findOrFail($id);
        $tour->prices->transform(function ($price) use ($usdrates, $tax) {
            $price->calculated_price = $price->calculatePrice($usdrates, $tax);
            return $price;
        });
        $attentions = Attention::where('page','admin-tour-detail')->get();
        
        $action_log = ActionLog::where('service',"Tour Package")
        ->where('service_id',$id)->get();
        return view('admin.toursadmindetail',[
            'usdrates'=>$usdrates,
            'tour'=>$tour, 
            'attentions'=>$attentions,
            'action_log'=>$action_log,
            'user'=>$user,
            'tax'=>$tax,
        ]);
    }
// View Tour Edit =============================================================================================================>
    public function view_edit_tour($id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $attentions = Attention::where('page','admin-tour-edit')->get();
            $tour=Tours::findOrFail($id);
            $usdrates = UsdRates::where('name','USD')->first();
            $types = TourType::all();
            return view('backend.tours.update-tour', compact("types"),[
                'usdrates'=>$usdrates,
                'attentions'=>$attentions,
            ])->with('tour',$tour);
        }else{
            return redirect("/tours-admin")->with('error','Akses ditolak');
        }
    }
// View Add Tours =========================================================================================>
    public function view_add_tour()
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $attentions = Attention::where('page','admin-tour-add')->get();
            $tours = Tours::all();
            $partners = Partners::all();
            $types = TourType::all();
            return view('backend.tours.create-tour',compact("types"),[
                'attentions'=>$attentions,
                'partners'=>$partners,
            ])->with('tours',$tours);
        }else{
            return redirect("/tours-admin")->with('error','Akses ditolak');
        }
    }

// Function Add Tours =========================================================================================>
    public function func_add_tour(Request $request)
    {
        // 🔹 Validasi form input
        $validated = $request->validate([
            'cover' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:125',
            'name_traditional' => 'required|string|max:255',
            'name_simplified' => 'required|string|max:255',
            'type' => 'required|integer',
            'duration_days' => 'required|integer',
            'duration_nights' => 'required|integer',
            'short_description' => 'required|string',
            'short_description_traditional' => 'required|string',
            'short_description_simplified' => 'required|string',
            'description' => 'required|string',
            'description_traditional' => 'required|string',
            'description_simplified' => 'required|string',
            'itinerary' => 'required|string',
            'itinerary_traditional' => 'required|string',
            'itinerary_simplified' => 'required|string',
            'include' => 'required|string',
            'include_traditional' => 'required|string',
            'include_simplified' => 'required|string',
            'exclude' => 'required|string',
            'exclude_traditional' => 'required|string',
            'exclude_simplified' => 'required|string',
            'additional_info' => 'required|string',
            'additional_info_traditional' => 'required|string',
            'additional_info_simplified' => 'nullable|string',
        ]);

        // 🔹 Upload Cover Image
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            // $filePath = $file->move('storage/tours/tours-cover', $filename, 'public');
            $file->storeAs('tours/tours-cover', $filename);
            $validated['cover'] = $filename;
        }

        // 🔹 Simpan ke Database
        $tour = new Tours();
        $tour->cover = $validated['cover'];
        $tour->code = $validated['code'];
        $tour->name = $validated['name'];
        $tour->name_traditional = $validated['name_traditional'];
        $tour->name_simplified = $validated['name_simplified'];
        $tour->type_id = $validated['type'];
        $tour->duration_days = $validated['duration_days'];
        $tour->duration_nights = $validated['duration_nights'];
        $tour->short_description = $validated['short_description'];
        $tour->short_description_traditional = $validated['short_description_traditional'];
        $tour->short_description_simplified = $validated['short_description_simplified'];
        $tour->description = $validated['description'];
        $tour->description_traditional = $validated['description_traditional'];
        $tour->description_simplified = $validated['description_simplified'];
        $tour->itinerary = $validated['itinerary'];
        $tour->itinerary_traditional = $validated['itinerary_traditional'];
        $tour->itinerary_simplified = $validated['itinerary_simplified'];
        $tour->include = $validated['include'];
        $tour->include_traditional = $validated['include_traditional'];
        $tour->include_simplified = $validated['include_simplified'];
        $tour->exclude = $validated['exclude'];
        $tour->exclude_traditional = $validated['exclude_traditional'];
        $tour->exclude_simplified = $validated['exclude_simplified'];
        $tour->additional_info = $validated['additional_info'];
        $tour->additional_info_traditional = $validated['additional_info_traditional'];
        $tour->additional_info_simplified = $validated['additional_info_simplified'] ?? null;
        $tour->save();

        // 🔹 Redirect dengan pesan sukses
        return redirect("/detail-tour-$tour->id")->with('success','New Tour Package has been successfully created!');
        // return redirect()->back()->with('success', 'Tour package has been successfully created!');
    }

// Function Add Tours =========================================================================================>
    public function func_add_tour_price(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $tour = Tours::where('id',$id)->first();
            $expired_date = date('Y-m-d',strtotime($request->expired_date));
            $status = "Draft";
            $price =new TourPrices([
                "tour_id"=>$id,
                "min_qty"=>$request->min_qty,
                "max_qty"=>$request->max_qty,
                "contract_rate"=>$request->contract_rate,
                "markup"=>$request->markup,
                "expired_date"=>$expired_date,
                "status"=>$status,
            ]);
            $price->save();
            // USER LOG
            $author = Auth::user()->id;
            $action = "Add Tour Price";
            $service = "Tour";
            $subservice = "Tour Package";
            $page = "detail-tour";
            $note = "Add Tour Price: ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/detail-tour-$id#prices")->with('success','New Tour Package Price has been successfully created!');
        }else{
            return redirect("/tours-admin")->with('error','Akses ditolak');
        }
    }


// function Update Tour PRICE =============================================================================================================>
    public function func_update_tour_price(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $tour_price=TourPrices::findOrFail($id);
            $tour_price->update([
                "min_qty"=>$request->min_qty,
                "max_qty"=>$request->max_qty,
                "contract_rate"=>$request->contract_rate,
                "markup"=>$request->markup,
                "expired_date"=>$request->expired_date,
                "status"=>$request->status,
            ]);

            // USER LOG
            $author = Auth::user()->id;
            $action = "Update Tour Price";
            $service = "Tour";
            $subservice = "Price";
            $page = "detail-tour";
            $note = "Update Tour Price: ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/detail-tour-$tour_price->tour_id#prices")->with('success','The Tour Price has been successfully updated!');
        }else{
            return redirect("/tours-admin")->with('error','Akses ditolak');
        }
    }
// FUNCTION DELETE TOUR PRICE
    public function func_delete_tour_price(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $tour_price=TourPrices::findOrFail($id);
            $action="Delete Tour Price";
            $author= Auth::user()->id;
            $tour_price->delete();
            // USER LOG
            $action = "Remove";
            $service = "Tour Package";
            $subservice = "Price";
            $page = "detail-tour";
            $note = "Remove Tour Price on Tour : ".$tour_price->tour_id.", Price id : ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/detail-tour-$tour_price->tour_id#prices")->with('success','The Tour Price has been successfully deleted!');
        }else{
            return redirect("/tours-admin")->with('error','Akses ditolak');
        }
    }
// function Update Tour =============================================================================================================>
    
    public function func_update_tour(Request $request,$id)
    {
        $tour = Tours::findOrFail($id);
        $validated = $request->validate([
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'name_traditional' => 'required|string|max:255',
            'name_simplified' => 'required|string|max:255',
            'type' => 'required|integer',
            'duration_days' => 'required|integer',
            'duration_nights' => 'required|integer',
            'short_description' => 'required|string',
            'short_description_traditional' => 'required|string',
            'short_description_simplified' => 'required|string',
            'description' => 'required|string',
            'description_traditional' => 'required|string',
            'description_simplified' => 'required|string',
            'itinerary' => 'required|string',
            'itinerary_traditional' => 'required|string',
            'itinerary_simplified' => 'required|string',
            'include' => 'required|string',
            'include_traditional' => 'required|string',
            'include_simplified' => 'required|string',
            'exclude' => 'required|string',
            'exclude_traditional' => 'required|string',
            'exclude_simplified' => 'required|string',
            'additional_info' => 'required|string',
            'additional_info_traditional' => 'required|string',
            'additional_info_simplified' => 'required|string',
        ]);

        
        if($request->hasFile("cover")){
            if ($tour->cover && Storage::disk('public')->exists('tours/tours-cover/' . $tour->cover)) {
                Storage::disk('public')->delete('tours/tours-cover/' . $tour->cover);
            }
            $file = $request->file('cover');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('tours/tours-cover', $filename);
            $validated['cover'] = $filename;
        
        } else {
            $validated['cover'] = $tour->cover;
        }
        $tour->update($validated);
        return redirect("/detail-tour-$tour->id")->with('success','The Tour Package has been successfully updated!');
    }
// function Tour Remove =============================================================================================================>
    public function remove_tour(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $tour=Tours::findOrFail($id);
            $status = "Removed";
            $author = Auth::user()->id;
            $tour->update([
                "status"=>$status,
            ]);
            // USER LOG
            $action = "Remove Tour";
            $service = "Tour";
            $subservice = "Tour Package";
            $page = "tours-admin";
            $note = "Remove Tour Package: ".$id;
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
            return back()->with('success','The Tour Package has been successfully deleted!');
        }else{
            return redirect("/tours-admin")->with('error','Akses ditolak');
        }
    }

    // Function Add Image Galery
    public function add_galery_img(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'tour_id' => 'required|integer|exists:tours,id',
        ]);

        // Ambil file yang diupload
        $file = $request->file('file');
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

        // Simpan file ke storage
        $filePath = $file->move('storage/tours/tours-galleries', $filename, 'public');
        // Simpan ke database
        $image = ToursImages::create([
            'tour_id' => $request->tour_id,
            'image' => $filePath,
        ]);

        return response()->json([
            'success' => true,
            'image_id' => $image->id,
            'path' => asset('storage/' . $filePath),
        ]);
    }

    public function destroy_galery_img($id)
    {
        $image = ToursImages::findOrFail($id);

        // Hapus file dari storage
        // if (File::exists($tour->cover)) {
        //     File::delete($tour->cover);
        // }
        if ($image->image && Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }

        // Hapus record database
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
    }
}