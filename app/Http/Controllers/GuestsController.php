<?php

namespace App\Http\Controllers;

use App\Models\Guests;
use Illuminate\Http\Request;

class GuestsController extends Controller
{
    public function remove($id)
    {
        $guest = Guests::findOrFail($id);
        $guest->delete();

        return response()->json(['success' => true]);
    }
}
