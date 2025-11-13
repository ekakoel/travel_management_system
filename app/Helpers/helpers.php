<?php
use Carbon\Carbon;
use App\Models\UiConfig;
use Intervention\Image\ImageManager;

use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

if (!function_exists('dateFormat')) {
    function dateFormat($date, $format = "m/d/Y") {
        return Carbon::parse($date)->translatedFormat($format);
    }
}

if (!function_exists('ui_config')) {
    function ui_config($name, $default = false)
    {
        return Cache::remember("ui_config_{$name}", 30, function () use ($name, $default) {
            return UiConfig::where('name', $name)->value('status') ?? $default;
        });
    }
}

if (!function_exists('set_ui_config')) {
    function set_ui_config($name, $status, $message = '')
    {
        $config = UiConfig::updateOrCreate(['name' => $name], ['status' => $status, 'message' => $message]);
        Cache::forget("ui_config_{$name}"); // Hapus cache agar data terbaru diambil
        return $config;
    }
}

function renderStars($score, $max = 5) {
    static $starIdCounter = 0;

    $fullStars = floor($score);
    $halfStar = ($score - $fullStars >= 0.5) ? 1 : 0;
    $emptyStars = $max - $fullStars - $halfStar;

    $starSvg = function($colorLeft = '#FFD700', $colorRight = '#FFD700') use (&$starIdCounter) {
        $starIdCounter++;
        $gradId = 'halfGrad' . $starIdCounter;

        return '
        <svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" 
            viewBox="0 0 20 20" width="1em" height="1em" style="vertical-align:inherit; margin: -2px; ">
            <defs>
                <linearGradient id="'.$gradId.'" x1="0" x2="1" y1="0" y2="0">
                    <stop offset="50%" stop-color="'.$colorLeft.'"/>
                    <stop offset="50%" stop-color="'.$colorRight.'"/>
                </linearGradient>
            </defs>
            <path fill="url(#'.$gradId.')" d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 
                2 9.24l5.46 4.73L5.82 21z"/>
        </svg>';
    };

    $html = '';

    // Full stars (all yellow)
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= $starSvg('#ffbf00', '#ffbf00');
    }

    // Half star (left yellow, right gray)
    if ($halfStar) {
        $html .= $starSvg('#ffbf00', '#C0C0C0');
    }

    // Empty stars (all gray)
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= $starSvg('#C0C0C0', '#C0C0C0');
    }

    return $html;
}
// if (!function_exists('getThumbnail')) {
//     function getThumbnail($path, $width = 380, $height = 200)
//     {
//         $fileName  = pathinfo($path, PATHINFO_FILENAME);
//         $extension = pathinfo($path, PATHINFO_EXTENSION);

//         $thumbnailPath = "thumbnails/{$fileName}-{$width}x{$height}.{$extension}";
//         $disk = Storage::disk('public');

//         try {
//             // ✅ Cek file asli pakai Storage, bukan file_exists
//             if (!$disk->exists($path)) {
//                 return asset('images/default.webp');
//             }

//             // ✅ Generate thumbnail kalau belum ada
//             if (!$disk->exists($thumbnailPath)) {
//                 $manager = new ImageManager(new Driver());
//                 $fullPath = $disk->path($path);

//                 $image = $manager->read($fullPath)->cover($width, $height);
//                 $image->save($disk->path($thumbnailPath));
//             }

//             return $disk->url($thumbnailPath);

//         } catch (\Exception $e) {
//             // ✅ Kalau error lain (misal corrupt file)
//             return asset('storage/images/default.webp');
//         }
//     }
// }
if (!function_exists('getThumbnail')) {
    function getThumbnail($path, $width = 380, $height = 200)
    {
        // Hilangkan prefix 'storage/' agar cocok dengan disk('public')
        $path = preg_replace('/^storage\//', '', $path);

        $fileName  = pathinfo($path, PATHINFO_FILENAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $thumbnailPath = "thumbnails/{$fileName}-{$width}x{$height}.{$extension}";
        $disk = Storage::disk('public');

        try {
            if (!$disk->exists($path)) {
                return asset('/storage/images/default.webp');
            }

            if (!$disk->exists($thumbnailPath)) {
                $manager = new ImageManager(new Driver());
                $fullPath = $disk->path($path);
                $image = $manager->read($fullPath)->cover($width, $height);
                $image->save($disk->path($thumbnailPath));
            }

            return $disk->url($thumbnailPath);
        } catch (\Exception $e) {
            return asset('/storage/images/default.webp');
        }
    }
}


if (!function_exists('getThumbnails')) {
    function getThumbnails($path, array $sizes = [[200,200],[400,300],[800,600]])
    {
        ini_set('memory_limit', '2058M'); // atau lebih tinggi
        $urls = [];
        foreach ($sizes as $size) {
            [$w, $h] = $size;
            $urls["{$w}x{$h}"] = getThumbnail($path, $w, $h);
        }
        return $urls;
    }
}

if (!function_exists('distance_google')) {
    function distance_google($lat1, $lng1, $lat2, $lng2)
    {
        return app(MapController::class)->getDistance($lat1, $lng1, $lat2, $lng2);
    }
}

if (!function_exists('distance_haversine')) {
    function distance_haversine($lat1, $lng1, $lat2, $lng2)
    {
        return app(MapController::class)->haversine($lat1, $lng1, $lat2, $lng2);
    }
}