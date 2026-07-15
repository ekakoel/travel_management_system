<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class OrderDetailTourGuestDisplayTest extends TestCase
{
    public function test_tour_order_detail_eager_loads_guest_records(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrderController.php'));

        $this->assertStringContainsString(
            "Orders::with('guests')->where('sales_agent',\$user->id)->where('id',\$id)->first();",
            $controller
        );
    }

    public function test_modern_tour_order_detail_uses_guest_records_before_legacy_guest_detail_html(): void
    {
        $template = file_get_contents(resource_path('views/frontend/home/orders/details/tour-modern.blade.php'));

        $this->assertStringContainsString("\$order->guests()->get()", $template);
        $this->assertStringContainsString("\$guestRows->isNotEmpty()", $template);
        $this->assertStringContainsString("\$guest->identification_type", $template);
        $this->assertStringContainsString("\$guest->identification_no", $template);
        $this->assertStringContainsString("\$order->guest_detail", $template);
        $this->assertNotSame('', trim(Blade::compileString($template)));
    }

    public function test_legacy_tour_order_detail_uses_guest_records_before_legacy_guest_detail_html(): void
    {
        $template = file_get_contents(resource_path('views/frontend/home/orders/details/tour-legacy.blade.php'));

        $this->assertStringContainsString("\$order->guests()->get()", $template);
        $this->assertStringContainsString("\$tourOrderGuestRows->isNotEmpty()", $template);
        $this->assertStringContainsString("\$guest->identification_type", $template);
        $this->assertStringContainsString("\$guest->identification_no", $template);
        $this->assertStringContainsString("{!! \$order->guest_detail !!}", $template);
        $this->assertNotSame('', trim(Blade::compileString($template)));
    }
}
