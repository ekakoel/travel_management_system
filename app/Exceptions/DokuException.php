<?php

namespace App\Exceptions;

use RuntimeException;

class DokuException extends RuntimeException
{
    public static function notConfigured(): self
    {
        return new self('DOKU payment is not configured.');
    }

    public static function requestFailed(int $status): self
    {
        return new self('DOKU request failed with HTTP status '.$status.'.');
    }

    public static function invalidResponse(): self
    {
        return new self('DOKU returned an invalid response.');
    }
}
