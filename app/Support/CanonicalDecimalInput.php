<?php

namespace App\Support;

final class CanonicalDecimalInput
{
    public static function normalize(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = trim($value);

        if (! preg_match('/^\d+\.\d+$/', $value)) {
            return $value;
        }

        return rtrim(rtrim($value, '0'), '.');
    }
}
