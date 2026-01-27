<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    public function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');

        $response = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json", [
            'units' => 'metric',
            'origins' => "$lat1,$lng1",
            'destinations' => "$lat2,$lng2",
            'key' => $apiKey,
        ]);

        $data = $response->json();

        if (
            isset($data['rows'][0]['elements'][0]['status']) &&
            $data['rows'][0]['elements'][0]['status'] === 'OK'
        ) {
            return [
                'distance' => $data['rows'][0]['elements'][0]['distance']['text'] ?? null,
                'value' => ($data['rows'][0]['elements'][0]['distance']['value'] ?? 0) / 1000,
                'duration' => $data['rows'][0]['elements'][0]['duration']['text'] ?? null,
            ];
        }

        return null;
    }

    // === HAVERSINE FORMULA (garis lurus) ===
    public function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }
}
