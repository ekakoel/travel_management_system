<?php

namespace Tests\Unit\Pdf;

use App\Support\Pdf\InvoiceLocale;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class InvoiceLocaleTest extends TestCase
{
    public function test_canonical_invoice_locales_are_explicit_and_stable(): void
    {
        $this->assertSame(['en', 'zh-CN', 'zh'], InvoiceLocale::all());
        $this->assertSame('zh-CN', InvoiceLocale::fromApplicationLocale('zh-CN'));
        $this->assertSame('zh', InvoiceLocale::fromApplicationLocale('zh'));
        $this->assertSame('en', InvoiceLocale::fromApplicationLocale('id'));
    }

    public function test_unknown_invoice_locale_is_rejected_instead_of_falling_back(): void
    {
        $this->expectException(InvalidArgumentException::class);

        InvoiceLocale::assertSupported('zh-TW');
    }
}
