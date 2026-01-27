<?php
use Carbon\Carbon;
if (!function_exists('dateTimeFormat')) {
    function dateTimeFormat($date, $format = 'm/d/Y (H:i)') {
        return Carbon::parse($date)->translatedFormat($format);
    }
}