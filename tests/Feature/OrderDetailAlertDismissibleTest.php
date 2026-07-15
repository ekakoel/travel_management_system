<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class OrderDetailAlertDismissibleTest extends TestCase
{
    public function test_legacy_tour_order_detail_alerts_are_dismissible(): void
    {
        $template = file_get_contents(resource_path('views/frontend/home/orders/details/tour-legacy.blade.php'));

        $this->assertSame(2, substr_count($template, 'alert-dismissible fade show'));
        $this->assertSame(2, substr_count($template, 'data-dismiss="alert"'));
        $this->assertSame(2, substr_count($template, 'data-bs-dismiss="alert"'));
        $this->assertSame(2, substr_count($template, 'aria-label="@lang(\'messages.Close\')"'));
        $this->assertNotSame('', trim(Blade::compileString($template)));
    }

    public function test_shared_frontend_alert_partial_supports_dismissible_alerts(): void
    {
        $template = file_get_contents(resource_path('views/partials/alerts.blade.php'));

        $this->assertStringContainsString('alert-dismissible fade show', $template);
        $this->assertStringContainsString('role="alert"', $template);
        $this->assertStringContainsString('data-dismiss="alert"', $template);
        $this->assertStringContainsString('data-bs-dismiss="alert"', $template);
        $this->assertNotSame('', trim(Blade::compileString($template)));
    }
}
