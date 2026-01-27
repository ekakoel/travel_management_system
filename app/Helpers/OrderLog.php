<?php

namespace App\Helpers;

use Request;
use App\Models\OrderLog as OrderLogModel; // Pastikan nama alias tidak bentrok

class OrderLog
{
    public static function addToOrderLog($orderno)
    {
        $log = [];
        $log['orderno'] = $orderno;
        $log['action'] = $action;
        $log['url'] = Request::fullUrl();
        $log['method'] = Request::method();
        $log['agent'] = Request::header('user-agent');
        $log['admin'] = auth()->check() ? auth()->user()->id : 1;
        OrderLogModel::create($log);  // Menggunakan OrderLogModel untuk memanggil model yang benar
    }

    public static function getOrderLog()
    {
        return OrderLogModel::latest()->get();  // Menggunakan OrderLogModel untuk mendapatkan log
    }
}
