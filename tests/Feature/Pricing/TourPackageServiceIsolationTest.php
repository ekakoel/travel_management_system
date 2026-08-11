<?php

namespace Tests\Feature\Pricing;

use App\Http\Controllers\OrdersAdminController;
use App\Models\Orders;
use App\Services\Pricing\OrderPricingSnapshotReader;
use Tests\TestCase;

class TourPackageServiceIsolationTest extends TestCase
{
    public function test_tour_pricing_service_is_not_imported_by_other_service_or_spk_domains(): void
    {
        $paths = array_merge(
            glob(app_path('Services/Hotels/*.php')) ?: [],
            glob(app_path('Services/Transports/*.php')) ?: [],
            glob(app_path('Services/Activities/*.php')) ?: [],
            glob(app_path('Services/*Wedding*.php')) ?: [],
            glob(app_path('Services/*Spk*.php')) ?: [],
            [
                app_path('Http/Controllers/OrderWeddingController.php'),
                app_path('Http/Controllers/TransportManagementController.php'),
                app_path('Http/Controllers/SpksController.php'),
            ]
        );

        foreach (array_filter($paths, 'is_file') as $path) {
            $this->assertStringNotContainsString(
                'TourPackagePricingService',
                file_get_contents($path),
                $path
            );
        }
    }

    public function test_generic_order_route_rejects_only_tour_package(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrderController.php'));

        $this->assertStringContainsString(
            "if (\$request->input('service') === Orders::PUBLIC_TOUR_SERVICE)",
            $controller
        );
        $this->assertStringContainsString(
            'Tour Package orders must use the authoritative Tour Package order flow.',
            $controller
        );
        $this->assertStringContainsString('public function func_add_order(Request $request)', $controller);
    }

    public function test_shared_migrations_are_nullable_and_reader_is_explicitly_tour_only(): void
    {
        $ordersMigration = file_get_contents(
            database_path('migrations/2026_07_29_170400_add_pricing_summary_to_orders_table.php')
        );
        $reader = file_get_contents((new \ReflectionClass(OrderPricingSnapshotReader::class))->getFileName());

        foreach ([
            "'pricing_version', 64)->nullable()",
            "'pricing_snapshot_id')->nullable()",
            "'base_currency', 3)->nullable()",
            "'display_currency', 3)->nullable()",
            "'final_total_idr')->nullable()",
            "'final_total_usd_minor')->nullable()",
        ] as $needle) {
            $this->assertStringContainsString($needle, $ordersMigration);
        }

        $this->assertStringContainsString(
            '$order->service !== Orders::PUBLIC_TOUR_SERVICE',
            $reader
        );
        $this->assertStringContainsString(
            '$snapshot->service !== Orders::PUBLIC_TOUR_SERVICE',
            $reader
        );
    }

    public function test_historical_tour_surfaces_use_the_snapshot_reader_contract(): void
    {
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $adminController = file_get_contents(app_path('Http/Controllers/OrdersAdminController.php'));
        $reservationController = file_get_contents(app_path('Http/Controllers/ReservationController.php'));
        $invoiceDetailService = file_get_contents(app_path('Services/Invoices/InvoiceDetailService.php'));
        $invoiceEn = file_get_contents(resource_path('views/emails/invoiceTourEn.blade.php'));
        $invoiceZh = file_get_contents(resource_path('views/emails/invoiceTourZh.blade.php'));
        $customerDetail = file_get_contents(
            resource_path('views/frontend/home/orders/details/tour-modern.blade.php')
        );
        $reservationDetailService = file_get_contents(app_path('Services/Reservations/ReservationDetailService.php'));
        $report = file_get_contents(resource_path('views/backend/reports/downloads/tour.blade.php'));

        $this->assertStringContainsString(
            'OrderPricingSnapshotReader::class)->historicalValues($order, $invoice)',
            $orderController
        );
        $this->assertStringContainsString(
            'OrderPricingSnapshotReader::class)->historicalValues($order, $invoice)',
            $adminController
        );
        $this->assertStringContainsString('tourPricingValues', $reservationController);
        $this->assertStringContainsString(
            'historicalValues($order, $invoice)',
            $invoiceDetailService
        );
        $this->assertStringContainsString("\$tourPricing['total_usd']", $invoiceEn);
        $this->assertStringContainsString("\$tourPricing['total_usd']", $invoiceZh);
        $this->assertStringNotContainsString('$order->final_price', $invoiceEn);
        $this->assertStringNotContainsString('$order->final_price', $invoiceZh);
        $this->assertStringContainsString("\$tourPricing['total_usd']", $customerDetail);
        $this->assertStringContainsString("\$tourPricing['unit_price_usd']", $customerDetail);
        $this->assertStringContainsString("\$tourPricing['gross_total_usd']", $customerDetail);
        $this->assertStringContainsString("historicalValues(\$order, \$reservation->invoice)", $reservationDetailService);
        $this->assertStringNotContainsString('ceil(', $report);
        $this->assertStringNotContainsString('$usdrates', $report);
        $this->assertStringNotContainsString('$tax', $report);
    }

    public function test_payment_confirmation_uses_committed_invoice_balance(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrdersAdminController.php'));

        $this->assertStringContainsString('$invoice_balance = $invoice->balance;', $controller);
        $this->assertStringContainsString(
            'DB::transaction(function () use ($request, $receipt, $invoice, $order',
            $controller
        );
    }

    public function test_admin_tour_order_detail_uses_snapshot_and_canonical_workflow_contract(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrdersAdminController.php'));
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $reservationController = file_get_contents(app_path('Http/Controllers/ReservationController.php'));
        $view = file_get_contents(resource_path('views/admin/ordersadmindetail.blade.php'));

        foreach ([
            "'tourPricing' => \$tourPricing",
            "'tourOrder' => \$tourOrder",
            'private function activateTourPackageOrder',
            'private function persistTourPackageInvoice',
            'private function agentCommunicationSummary',
            "'status' => 'Approved'",
            "Reservation::where('id', \$order->rsv_id)->lockForUpdate()",
            "'Deleted',",
            'Tour Package orders become Paid only through a valid payment confirmation.',
        ] as $needle) {
            $this->assertStringContainsString($needle, $controller);
        }

        foreach ([
            "\$tourPricing['unit_price_usd']",
            "\$tourPricing['gross_total_usd']",
            'Tour supplier confirmation number',
            "['m', 'Male']",
            "['f', 'Female']",
            "['Rejected', 'Invalid', 'Canceled']",
            'nl2br(e($note->note))',
            "\$agentCommunication['status_reason']",
            "\$agentCommunication['context']",
        ] as $needle) {
            $this->assertStringContainsString($needle, $view);
        }

        $this->assertStringNotContainsString('{{ $order->msg }}', $view);
        $this->assertStringContainsString("'msg' => null", $orderController);
        $this->assertStringNotContainsString('buildTourOrderMessagePayload', $orderController);

        $this->assertStringContainsString("'in:Male,Female'", $reservationController);
        $this->assertStringContainsString("'in:Adult,Child'", $reservationController);
        $this->assertStringContainsString("'in:'.\$order->rsv_id", $reservationController);
    }

    public function test_agent_communication_hides_legacy_json_and_exposes_only_meaningful_context(): void
    {
        $method = new \ReflectionMethod(OrdersAdminController::class, 'agentCommunicationSummary');
        $method->setAccessible(true);
        $controller = app(OrdersAdminController::class);
        $order = new Orders();
        $order->forceFill([
            'service' => Orders::PUBLIC_TOUR_SERVICE,
            'status' => 'Pending',
            'note' => 'Call the agent before pickup.',
            'msg' => json_encode([
                'lead_guest' => ['name' => 'Adrian'],
                'guests' => [['name' => 'Adrian']],
                'pricing' => ['total_price' => '310.22'],
                'special_request' => null,
            ], JSON_THROW_ON_ERROR),
        ]);

        $summary = $method->invoke($controller, $order);

        $this->assertNull($summary['status_reason']);
        $this->assertSame([
            ['label' => 'Order remark', 'value' => 'Call the agent before pickup.'],
        ], $summary['context']->all());

        $order->forceFill(['status' => 'Invalid', 'msg' => 'Pickup location cannot be confirmed.']);
        $summary = $method->invoke($controller, $order);

        $this->assertSame('Invalid', $summary['status']);
        $this->assertSame('Pickup location cannot be confirmed.', $summary['status_reason']);
    }
}
