<?php

if (!function_exists('formatMoneyValue')) {
    function formatMoneyValue($amount, int $decimals = 2): string
    {
        return number_format((float) $amount, $decimals, ',', '.');
    }
}

if (!function_exists('currencyFormatUsd')) {
    function currencyFormatUsd($amount)
    {
        return '$'.' '. formatMoneyValue($amount, 2);
    }
}

if (!function_exists('currencyFormatIdr')) {
    function currencyFormatIdr($amount)
    {
        return 'Rp'.' '. formatMoneyValue($amount, 0);
    }
}

if (!function_exists('currencyFormatTwd')) {
    function currencyFormatTwd($amount)
    {
        return 'NT$'.' '. formatMoneyValue($amount, 2);
    }
}

if (!function_exists('currencyFormatCny')) {
    function currencyFormatCny($amount)
    {
        return '¥'.' '. formatMoneyValue($amount, 2);
    }
}
