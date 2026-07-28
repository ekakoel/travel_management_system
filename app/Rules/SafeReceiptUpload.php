<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class SafeReceiptUpload implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile || !$value->isValid()) {
            return;
        }

        $extension = strtolower((string) $value->getClientOriginalExtension());
        $path = $value->getRealPath();

        if (!$path) {
            $fail('The :attribute must be a valid receipt file.');

            return;
        }

        if (in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            $image = @getimagesize($path);
            $allowedImageMimes = ['image/jpeg', 'image/png'];

            if (!$image || !in_array((string) ($image['mime'] ?? ''), $allowedImageMimes, true)) {
                $fail('The :attribute must be a valid JPG or PNG image.');
            }

            return;
        }

        if ($extension === 'pdf') {
            $handle = @fopen($path, 'rb');
            $header = $handle ? fread($handle, 4) : false;

            if ($handle) {
                fclose($handle);
            }

            if ($header !== '%PDF') {
                $fail('The :attribute must be a valid PDF file.');
            }
        }
    }
}
