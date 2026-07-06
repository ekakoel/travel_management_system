<?php
use Carbon\Carbon;
if (!function_exists('dateTimeFormat')) {
    function dateTimeFormat($date, $format = 'Y-m-d H:i') {
        if (!$date) {
            return '';
        }

        return Carbon::parse($date)->translatedFormat($format);
    }
}
