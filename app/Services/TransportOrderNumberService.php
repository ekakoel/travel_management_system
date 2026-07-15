<?php

namespace App\Services;

use App\Models\Orders;
use App\Models\User;
use Carbon\Carbon;

class TransportOrderNumberService
{
    public function generate(User $agent, ?Carbon $orderDate = null): string
    {
        $orderDate ??= Carbon::now();
        $agentCode = strtoupper(preg_replace('/[^A-Z0-9]/', '', (string) $agent->code));
        $agentCode = $agentCode !== '' ? $agentCode : 'AGENT';
        $prefix = $agentCode . $orderDate->format('ymd');

        $lastSuffixNumber = Orders::where('sales_agent', $agent->id)
            ->where('service', 'Transport')
            ->where('orderno', 'like', $prefix . '%')
            ->pluck('orderno')
            ->map(function ($orderNumber) use ($prefix) {
                $suffix = substr((string) $orderNumber, strlen($prefix));

                return $this->lettersToNumber($suffix);
            })
            ->max() ?? 0;

        do {
            $lastSuffixNumber++;
            $orderNumber = $prefix . $this->numberToLetters($lastSuffixNumber);
        } while (Orders::where('orderno', $orderNumber)->exists());

        return $orderNumber;
    }

    public function numberToLetters(int $number): string
    {
        $letters = '';

        while ($number > 0) {
            $remainder = ($number - 1) % 26;
            $letters = chr(65 + $remainder) . $letters;
            $number = intdiv($number - 1, 26);
        }

        return $letters ?: 'A';
    }

    public function lettersToNumber(?string $letters): int
    {
        $letters = strtoupper(preg_replace('/[^A-Z]/', '', (string) $letters));

        if ($letters === '') {
            return 0;
        }

        $number = 0;

        foreach (str_split($letters) as $letter) {
            $number = ($number * 26) + (ord($letter) - 64);
        }

        return $number;
    }
}
