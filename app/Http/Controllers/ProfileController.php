<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoretoursRequest;
use App\Http\Requests\UpdatetoursRequest;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function profile()
    {
        return view('main.profile', [
            'title' => 'Users',
            "dusers" => User::all()
        ]);
    }

    public function users($email)
    {
        $duser = User::find($email);
        return view('main.profile', [
            'title' => 'Users',
            'dusers'=>$duser,
        ]);
    }
}
