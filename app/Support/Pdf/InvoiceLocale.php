<?php

namespace App\Support\Pdf;

use InvalidArgumentException;

final class InvoiceLocale
{
    public const ENGLISH = 'en';
    public const SIMPLIFIED_CHINESE = 'zh-CN';
    public const TRADITIONAL_CHINESE = 'zh';

    public static function all(): array
    {
        return [
            self::ENGLISH,
            self::SIMPLIFIED_CHINESE,
            self::TRADITIONAL_CHINESE,
        ];
    }

    public static function labels(): array
    {
        return [
            self::ENGLISH => 'English',
            self::SIMPLIFIED_CHINESE => 'Chinese Simplified',
            self::TRADITIONAL_CHINESE => 'Chinese Traditional',
        ];
    }

    public static function assertSupported(string $locale): string
    {
        if (!in_array($locale, self::all(), true)) {
            throw new InvalidArgumentException("Unsupported invoice locale [{$locale}].");
        }

        return $locale;
    }

    public static function fromApplicationLocale(?string $locale): string
    {
        return match ($locale) {
            self::SIMPLIFIED_CHINESE => self::SIMPLIFIED_CHINESE,
            self::TRADITIONAL_CHINESE => self::TRADITIONAL_CHINESE,
            default => self::ENGLISH,
        };
    }
}
