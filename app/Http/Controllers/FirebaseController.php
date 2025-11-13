<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase;
use Kreait\Firebase\Factory;

class FirebaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index()
    {
        $firebase = (new Factory)
            ->withServiceAccount(__DIR__.'/lisensi-sipo-firebase-adminsdk-pw7rm-1e22ac41ac.json')
            ->withDatabaseUri('https://lisensi-sipo-default-rtdb.asia-southeast1.firebasedatabase.app');
        $database = $firebase->createDatabase();
        $lic_app = $database->getReference('lic_app/app_loc');
 
        echo '<pre>';
        print_r($lic_app->getvalue());
        echo '</pre>';
    }
}
