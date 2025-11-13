<?php

if (!function_exists('currencyFormatUsd')) {
    function currencyFormatUsd($amount)
    {
        return '$ ' . number_format($amount, 2, '.', ',');
    }
}
if (!function_exists('currencyFormatIdr')) {
    function currencyFormatIdr($amount)
    {
        return 'Rp ' . number_format($amount, 0, '.', ',');
    }
}
if (!function_exists('currencyFormatTwd')) {
    function currencyFormatTwd($amount)
    {
        return 'NT$ ' . number_format($amount, 2, '.', ',');
    }
}
if (!function_exists('currencyFormatCny')) {
    function currencyFormatCny($amount)
    {
        return '¥ ' . number_format($amount, 2, '.', ',');
    }
}
