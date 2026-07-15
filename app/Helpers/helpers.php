<?php
use Carbon\Carbon;
use Intervention\Image\ImageManager;

use Illuminate\Http\Request;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

if (!function_exists('dateFormat')) {
    function dateFormat($date, $format = 'Y-m-d') {
        if (!$date) {
            return '';
        }

        return Carbon::parse($date)->translatedFormat($format);
    }
}

if (!function_exists('canonical_frontend_url')) {
    function canonical_frontend_url(?Request $request = null): string
    {
        $request ??= request();
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;

        if (in_array($routeName, ['view.hotel-prices.page', 'view.hotel-prices'], true)) {
            $code = $route->parameter('code');
            $checkin = $request->query('checkin') ?: session('booking_dates.checkin') ?: $request->input('checkin');
            $checkout = $request->query('checkout') ?: session('booking_dates.checkout') ?: $request->input('checkout');

            if ($code && $checkin && $checkout) {
                return route('view.hotel-prices.page', [
                    'code' => $code,
                    'checkin' => $checkin,
                    'checkout' => $checkout,
                ]);
            }
        }

        if (in_array($routeName, ['view.hotel-detail', 'view.hotel-detail-flyer', 'view.accommodation-detail', 'view.hotel-check-price', 'view.accommodation-check-price'], true)) {
            $code = $route->parameter('code');

            if ($code) {
                $parameters = ['code' => $code];

                if ($request->boolean('check_price')) {
                    $parameters['check_price'] = 1;
                }

                $targetRoute = match ($routeName) {
                    'view.hotel-detail',
                    'view.hotel-detail-flyer' => 'view.accommodation-detail',
                    'view.hotel-check-price' => 'view.accommodation-check-price',
                    default => $routeName,
                };

                return route($targetRoute, $parameters);
            }
        }

        if (in_array($routeName, ['view.order-hotel-normal', 'view.order-hotel-promo', 'view.order-hotel-package'], true)) {
            $hotelId = $request->input('hotel_id');
            $checkin = session('booking_dates.checkin') ?: $request->input('checkin');
            $checkout = session('booking_dates.checkout') ?: $request->input('checkout');

            if ($hotelId) {
                $hotel = \App\Models\Hotels::select('code')->find($hotelId);

                if ($hotel && $checkin && $checkout) {
                    return route('view.hotel-prices.page', [
                        'code' => $hotel->code,
                        'checkin' => $checkin,
                        'checkout' => $checkout,
                    ]);
                }

                if ($hotel) {
                    return route('view.accommodation-detail', ['code' => $hotel->code]);
                }
            }
        }

        if ($request->isMethod('get')) {
            return $request->fullUrl();
        }

        return url()->previous() ?: url('/');
    }
}

if (!function_exists('language_switch_url')) {
    function language_switch_url(string $locale, ?Request $request = null): string
    {
        $redirect = canonical_frontend_url($request);

        return route('language.switch', ['locale' => $locale, 'redirect' => $redirect]);
    }
}

if (!function_exists('localized_model_field')) {
    function localized_model_field($model, string $field): string
    {
        if (!$model) {
            return '';
        }

        $locale = app()->getLocale();
        $localizedField = match ($locale) {
            'zh' => $field . '_traditional',
            'zh-CN' => $field . '_simplified',
            default => $field,
        };

        $value = trim((string) data_get($model, $localizedField, ''));

        if ($value !== '') {
            return $value;
        }

        return trim((string) data_get($model, $field, ''));
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
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= $starSvg('#ffbf00', '#ffbf00');
    }
    if ($halfStar) {
        $html .= $starSvg('#ffbf00', '#C0C0C0');
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= $starSvg('#C0C0C0', '#C0C0C0');
    }

    return $html;
}
if (!function_exists('getThumbnail')) {
    function getThumbnail($path, $width = 380, $height = 200)
    {
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
            return asset('storage/' . $thumbnailPath);
        } catch (\Exception $e) {
            return asset('/storage/images/default.webp');
        }
    }
}


if (!function_exists('getThumbnails')) {
    function getThumbnails($path, array $sizes = [[200,200],[400,300],[800,600]])
    {
        ini_set('memory_limit', '2058M');
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
