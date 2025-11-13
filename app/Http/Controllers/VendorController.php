<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Hotels;
use App\Models\Vendor;
use App\Models\UserLog;
use App\Models\UsdRates;
use App\Models\Attention;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\VendorPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','can:isAdmin']);
    }
    public function index()
    {
        $vendors = Vendor::where('status','!=','Removed')->get();
        $activevendors = Vendor::where('status', 'Active')->get();
        $draftvendors = Vendor::where('status', 'Draft')->get();
        $attentions = Attention::where('page','vendors-admin')->get();
        $packages = VendorPackage::where('status','!=',"Removed")->get();
        return view('admin.vendorsadmin', compact('vendors'),[
            "draftvendors" => $draftvendors,
            "activevendors" => $activevendors,
            "attentions" => $attentions,
            "packages" => $packages,
        ]);
    }


    public function func_add_vendor(Request $request)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'location' => 'required',
                'cover' => 'required',
                'type' => 'required',
                'contact_name' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'term' => 'required',
            ]);
            $author_id = Auth::user()->id;
            $status = 'Draft';
            if($request->hasFile("cover")){
                $file=$request->file("cover");
                $coverName=time().'_'.$file->getClientOriginalName();
                $file->move("storage/vendors/covers/",$coverName);
                $vendor =new Vendor([
                    "name"=>$request->name,
                    "location"=>$request->location,
                    "cover" =>$coverName,
                    "type"=>$request->type,
                    "contact_name"=>$request->contact_name,
                    "phone"=>$request->phone,
                    "email"=>$request->email,
                    "description"=>$request->description,
                    "term"=>$request->term,
                    "status"=>$status,
                    "author_id"=>$author_id,
                ]);
                $vendor->save();
            }
            // USER LOG
            $action = "Add Vendor";
            $service = "Vendor";
            $subservice = "Vendor";
            $page = "vendors-admin";
            $note = "Add new Vendor id : ".$vendor->id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$vendor->id,
                "page"=>$page,
                "user_id"=>$author_id,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/detail-vendor-$vendor->id")->with('success', 'Vendor added successfully');
        }else{
            return redirect("/vendors-admin")->with('error','Maaf, anda tidak memiliki kewenangan untuk menambahkan Vendor');
        }
    }

    public function func_add_package(Request $request)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $validated = $request->validate([
                'service' => 'required|max:255',
                'duration' => 'required',
                'time' => 'required',
                'cover' => 'required',
                'type' => 'required',
            ]);
            $author = Auth::user()->id;
            $status = 'Draft';
            $usdrates = UsdRates::where('name','USD')->first();
            $tax = Tax::where('id',1)->first();
            $cr_usd = $request->contract_rate / $usdrates->rate;
            $cr_mr = $cr_usd + $request->markup;
            $service_tax = $cr_mr * ($tax->tax/100);
            $publish_rate = ceil($cr_mr + $service_tax);
            if($request->hasFile("cover")){
                $file=$request->file("cover");
                $coverName=time().'_'.$file->getClientOriginalName();
                $file->move("storage/vendors/package/",$coverName);
                $package =new VendorPackage([
                    "service"=>$request->service,
                    "duration"=>$request->duration,
                    "time"=>$request->time,
                    "cover" =>$coverName,
                    "venue" =>$request->venue,
                    "contract_rate"=>$request->contract_rate,
                    "markup"=>$request->markup,
                    "publish_rate"=>$publish_rate,
                    "type"=>$request->type,
                    "description"=>$request->description,
                    "capacity"=>$request->capacity,
                    "status"=>$status,
                    "vendor_id"=>$request->vendor_id,
                    "hotel_id"=>$request->hotel_id,
                    "author"=>$author,
                ]);
                $package->save();
            }
            // USER LOG
            $action = "Add Service";
            $service = "Wedding Package";
            $subservice = "Service";
            $page = "detail-vendor";
            $note = "Add new Service id : ".$package->id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$package->id,
                "page"=>$page,
                "user_id"=>$author,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/detail-vendor-$request->vendor_id")->with('success', 'Package added successfully');
        }else{
            return redirect("/vendors-admin")->with('error','Maaf, anda tidak memiliki kewenangan untuk menambahkan Vendor');
        }
    }
    // Function Update VENDOR =============================================================================================================>
    public function func_update_vendor(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $vendor=Vendor::findOrFail($id);
            if($request->hasFile("cover")){
                if (File::exists("storage/vendors/covers/".$vendor->cover)) {
                    File::delete("storage/vendors/covers/".$vendor->cover);
                }
                $file=$request->file("cover");
                $vendor->cover=time()."_".$file->getClientOriginalName();
                $file->move("storage/vendors/covers/",$vendor->cover);
                $request['cover']=$vendor->cover;
            }
            $vendor->update([
                "name"=>$request->name,
                "location"=>$request->location,
                "cover" =>$vendor->cover,
                "type"=>$request->type,
                "contact_name"=>$request->contact_name,
                "phone"=>$request->phone,
                "email"=>$request->email,
                "description"=>$request->description,
                "term"=>$request->term,
                "status"=>$request->status,
            ]);
            // USER LOG
            $author_id = Auth::user()->id;
            $action = "update Vendor";
            $service = "Vendor";
            $subservice = "Vendor";
            $page = "detail-vendor";
            $note = "update Vendor id : ".$id;
            $user_log =new UserLog([
                "action"=>$action,
                "service"=>$service,
                "subservice"=>$subservice,
                "subservice_id"=>$id,
                "page"=>$page,
                "user_id"=>$author_id,
                "user_ip"=>$request->getClientIp(),
                "note" =>$note, 
            ]);
            $user_log->save();
            return redirect("/detail-vendor-$id")->with('success','Vendor has been updated!');
        }else{
            return redirect("/vendors-admin")->with('error','Sorry, anda tidak bisa mengakses halaman tersebut');
        }
    }
    // Function Update VENDOR PACKAGE =============================================================================================================>
    public function func_update_vendor_package(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $package=VendorPackage::findOrFail($id);
            $usdrate = UsdRates::where('name','USD')->first();
            $tax = Tax::where('name',"Tax")->first();
            $cr_usd = $request->contract_rate / $usdrate->rate;
            $cr_mk = $cr_usd + $request->markup;
            $cr_mk_tax = $cr_mk*($tax->tax/100);
            $publish_rate = ceil($cr_mk + $cr_mk_tax);
            if($request->hasFile("cover")){
                if (File::exists("storage/vendors/package/".$package->cover)) {
                    File::delete("storage/vendors/package/".$package->cover);
                }
                $file=$request->file("cover");
                $package->cover=time()."_".$file->getClientOriginalName();
                $file->move("storage/vendors/package/",$package->cover);
                $request['cover']=$package->cover;
            }
            $package->update([
                "service"=>$request->service,
                "venue" =>$request->venue,
                "duration"=>$request->duration,
                "time"=>$request->time,
                "type"=>$request->type,
                "cover" =>$package->cover,
                "contract_rate"=>$request->contract_rate,
                "markup"=>$request->markup,
                "publish_rate"=>$publish_rate,
                "description"=>$request->description,
                "capacity"=>$request->capacity,
                "status"=>$request->status,
                "vendor_id"=>$request->vendor_id,
                "hotel_id"=>$request->hotel_id,
            ]);
            // dd($package);
            return back()->with('success','Package has been updated!');
        }else{
            return redirect("/vendors-admin")->with('error','Sorry, anda tidak bisa mengakses halaman tersebut');
        }
    }
    // Function Update VENDOR =============================================================================================================>
    public function func_remove_vendor(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $vendor=Vendor::findOrFail($id);
            $vendor->update([
                "status"=>"Removed",
            ]);
            return back()->with('error','Sorry, Vendor has been removed');
        }else{
            return redirect("/vendors-admin")->with('error','Sorry, anda tidak bisa mengakses halaman tersebut');
        }
    }
    // Function Update VENDOR =============================================================================================================>
    public function func_remove_package(Request $request,$id){
        if (Gate::allows('posDev') or Gate::allows('posAuthor')) {
            $package=VendorPackage::findOrFail($id);
            $package->update([
                "status"=>"Removed",
            ]);
            return back();
        }else{
            return redirect("/vendors-admin")->with('error','Sorry, anda tidak bisa mengakses halaman tersebut');
        }
    }
    // View Detail VENDOR =========================================================================================>
    public function view_vendor_detail($id){
        $now = Carbon::now();
        $tax = Tax::where('id',1)->first();
        $vendor = Vendor::find($id);
        $usdrates = UsdRates::where('name','USD')->first();
        $attentions = Attention::where('page','vendors-admin')->get();
        $author = Auth::user()->where('id',$vendor->author_id)->first();
        $packages = VendorPackage::where('vendor_id', $vendor->id)->where('status','!=','Removed')->get();
        $cpackages = count($packages);
        $hotels = Hotels::where('status', 'Active')
        ->get();
        

        if ($vendor->status == "Removed") {
            return redirect("/vendors-admin")->with('invalid','Sorry, the vendor you are looking for is not available!');
        }else{
            return view('admin.vendorsadmindetail',[
                'vendor'=>$vendor,
                'usdrates'=>$usdrates,
                'attentions'=>$attentions,
                'tax'=>$tax,
                'cpackages'=>$cpackages,
                'packages'=>$packages,
                'author'=>$author,
                'hotels'=>$hotels,
            ]);
        }
    }
   
}
