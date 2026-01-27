<?php

namespace App\Http\Controllers;
use App\Models\Tours;
use Illuminate\Http\Request;

class TourPackageController extends Controller
{   
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function index()
    {
        return view('index', [
            'title' => 'Tours Package',
            "tours" => Tours::all()

        ]);
    }
}
