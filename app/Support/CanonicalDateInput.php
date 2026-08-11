<?php

namespace App\Support;

use DateTimeImmutable;

final class CanonicalDateInput
{
    private const CANONICAL_FORMAT = 'Y-m-d';

    /**
     * Formats previously emitted by the legacy backend Air Datepicker.
     *
     * Keep this allow-list explicit. Ambiguous numeric dates such as 01/02/2026
     * must not be guessed.
     */
    private const COMPATIBILITY_FORMATS = [
        'd F Y',
        'd M Y',
    ];

    public static function normalize(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        foreach ([self::CANONICAL_FORMAT, ...self::COMPATIBILITY_FORMATS] as $format) {
            $date = DateTimeImmutable::createFromFormat('!'.$format, $value);
            $errors = DateTimeImmutable::getLastErrors();
            $valid = $date !== false
                && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
                && $date->format($format) === $value;

            if ($valid) {
                return $date->format(self::CANONICAL_FORMAT);
            }
        }

        return $value;
    }
}
