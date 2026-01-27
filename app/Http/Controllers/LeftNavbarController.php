<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeftNavbarController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
}
