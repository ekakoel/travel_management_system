<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\StoremenuRequest;
use App\Http\Requests\UpdatemenuRequest;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index()
    {   
        $menu_order = Orders::where('user_id','=', Auth::user()->id)->get();
        $order_active = Orders::where('user_id','=', Auth::user()->id)
            ->where('status','Active')->get();
        $order_rejected = Orders::where('user_id','=', Auth::user()->id)
            ->where('status','Rejected')->get();
        $order_invalid = Orders::where('user_id','=', Auth::user()->id)
            ->where('status','Invalid')->get();
        $order_confirmed = Orders::where('user_id','=', Auth::user()->id)
            ->where('status','Confirmed')->get();
        $order_approved = Orders::where('user_id','=', Auth::user()->id)
            ->where('status','Approved')->get();
        return view('main.order',compact('orders'),[
            'menu_order'=>$menu_order,
            'order_active'=>$order_active,
            'order_rejected'=>$order_rejected,
            'order_invalid'=>$order_invalid,
            'order_confirmed'=>$order_confirmed,
            'order_approved'=>$order_approved,
        ]);
    }
}
