<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Guide;
use App\Models\Review;
use App\Models\Attention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreGuideRequest;
use App\Http\Requests\UpdateGuideRequest;

class GuideController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
   
    public function index()
    {
        $now = Carbon::now();
        $guides = Guide::select('guides.*')
        ->addSelect([
            'global_rating' => Review::selectRaw('AVG((attitude + time_control + knowledge + explanation + guide_neatness)/5)')
                ->whereColumn('guide_id', 'guides.id')
        ])
        ->orderByDesc('global_rating')
        ->get();
        $attentions = Attention::where('page','guides-admin')->get();
        return view('guides.guides-admin',[
            'now'=>$now,
            'guides'=>$guides,
            'attentions'=>$attentions,
        ]);
    }

    public function create(Request $request)
    {
        $name = $request->name;
        $sex = $request->sex;
        $language = $request->language;
        $phone = $request->phone;
        $email =$request->email;
        $address =$request->address;
        $country =$request->country;
        $guide =new Guide([
            "name"=>$name,
            "sex"=>$sex,
            "language"=>$language,
            "phone"=>$phone,
            "email"=>$email,
            "address"=>$address,
            "country"=>$country,
            
        ]);
        $guide->save();
        return redirect()->back()->with('success','New Guide has been created');
    }

    public function edit(Request $request,$id)
    {
        $guide=Guide::findOrFail($id);
        $guide->update([
            "name" =>$request->name, 
            "sex" =>$request->sex, 
            "language"=>$request->language,
            "phone"=>$request->phone,
            "email"=>$request->email,
            "address"=>$request->address,
            "country"=>$request->country,
        ]);
        return redirect("/guides-admin")->with('success','Guide has been updated');
    }
    
    public function destroy(Request $request,$id)
    {
        if (Gate::allows('posDev') or Gate::allows('posAuthor') or Gate::allows('posRsv')) {
            $guide=Guide::findOrFail($id);
            $guide->delete();
            return back()->with('success','Guide has been deleted');
        }else{
            return redirect("/guides-admin")->with('error','Akses ditolak');
        }
    }
}
