<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    // public function changeLanguage($locale)
    // {
    //     session(['locale' => $locale]);
    //     return redirect()->back();
    // }
    public function changeLanguage($locale)
    {
        session(['locale' => $locale]);

        $redirect = request()->get('redirect');

        // Validasi: redirect hanya jika URL valid dan mendukung GET
        if ($redirect && filter_var($redirect, FILTER_VALIDATE_URL)) {
            return redirect($redirect);
        }

        return redirect('/');
    }
}
