<?php

namespace App\Support;

use App\ValueObjects\Money;

final class MoneyFormatter
{
    public function format(Money $money): string
    {
        if ($money->currency === Money::USD) {
            return '$'
                .number_format(intdiv($money->amount, 100), 0, '.', ',')
                .'.'
                .str_pad((string) ($money->amount % 100), 2, '0', STR_PAD_LEFT);
        }

        return 'Rp '.number_format($money->amount, 0, ',', '.');
    }

    public function decimal(Money $money): string
    {
        if ($money->currency === Money::USD) {
            return sprintf('%d.%02d', intdiv($money->amount, 100), $money->amount % 100);
        }

        return (string) $money->amount;
    }
}
