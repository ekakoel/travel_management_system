<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activities;

class RactivitiesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function ractivities()
    {
        $ractivities = Activities::all();
        return view('recent.ractivities', [
            'title' => 'Recent Activities',
            "ractivities" => $ractivities
        ]);
    }

    public function detail($id){
        $dactivity = Activities::find($id);
        return view('main.detail',[
                'dactivities'=>$dactivity,
            ]);

        } 
}
