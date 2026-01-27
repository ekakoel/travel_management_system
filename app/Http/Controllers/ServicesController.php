<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TourPhoto;
use App\Models\Tours;

class ServicesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function store(Request $request)
    {
        if($request->hasFile("photo")){
            $files=$request->file("photo");
            foreach($files as $file){
                $imageName=time().'_'.$file->getClientOriginalName();
                $request['tours_id']=$addtour->id;
                $request['photo']=$imageName;
                $file->move(\public_path("/tour"),$imageName);
                Image::create($request->all());

            $addtour =new addtour([
                'name' =>$request->name,
                "author" =>$request->author,
                'type' =>$request->type,
                'duration' =>$request->duration,
                'description' =>$request->description,
                'destination' =>$request->destination,
                'itinerary' =>$request->itinerary,
                'include' => $request->include,
                'exclude' =>$request->exclude,
                'note' =>$request->note,
                'price' =>$request->price,
                'qty' =>$request->qty,
                'photo' =>$imageName,
            ]);
           $addtour->save();
            }
            return redirect("/services");
        }
    }
}
