<?php

namespace Tests\Feature;

use App\Models\InvoiceAdmin;
use App\Models\Orders;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class BackendOrderInvoiceWorkflowTest extends TestCase
{
    public function test_invoice_workflow_actions_use_named_routes_with_the_expected_http_methods(): void
    {
        $routes = app('router')->getRoutes();
        $regenerate = $routes->getByName('admin.orders.invoice.regenerate');
        $print = $routes->getByName('admin.orders.document.print');

        $this->assertNotNull($regenerate);
        $this->assertSame('fregenerate-invoice-pdf/{order}', $regenerate->uri());
        $this->assertSame(['PUT'], $regenerate->methods());
        $this->assertNotNull($print);
        $this->assertSame(['GET', 'HEAD'], $print->methods());

        $view = file_get_contents(resource_path('views/admin/ordersadmindetail.blade.php'));

        foreach ([
            'admin.orders.workflow.activate',
            'admin.orders.invoice.generate',
            'admin.orders.invoice.regenerate',
            'admin.orders.confirmation.send',
            'admin.orders.confirmation.resend',
            'admin.orders.approval-email.send',
            'admin.orders.document.print',
        ] as $routeName) {
            $this->assertStringContainsString("route('{$routeName}'", $view);
        }

        $this->assertStringNotContainsString("url('/fregenerate-invoice-pdf-'", $view);
        $this->assertStringContainsString('<form id="regenerateInvoice"', $view);
        $this->assertStringContainsString("@method('put')", $view);
    }

    public function test_order_and_invoice_use_reservation_as_the_canonical_link(): void
    {
        $orderRelation = (new Orders())->reservation();
        $invoiceRelation = (new InvoiceAdmin())->reservation();

        $this->assertInstanceOf(BelongsTo::class, $orderRelation);
        $this->assertSame('rsv_id', $orderRelation->getForeignKeyName());
        $this->assertInstanceOf(BelongsTo::class, $invoiceRelation);
        $this->assertSame('rsv_id', $invoiceRelation->getForeignKeyName());

        $controller = file_get_contents(app_path('Http/Controllers/OrdersAdminController.php'));

        $this->assertStringContainsString("'reservation.invoice'", $controller);
        $this->assertStringContainsString('$order->reservation?->invoice', $controller);
        $this->assertStringContainsString('saveStandardInvoicePdfDocuments($order, $invoice)', $controller);
    }
}
