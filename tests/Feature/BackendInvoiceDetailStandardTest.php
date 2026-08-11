<?php

namespace Tests\Feature;

use Tests\TestCase;

class BackendInvoiceDetailStandardTest extends TestCase
{
    public function test_invoice_detail_uses_canonical_backend_workspace_primitives(): void
    {
        $view = file_get_contents(resource_path('views/backend/finance/invoices/detail.blade.php'));
        $context = file_get_contents(resource_path('views/backend/finance/invoices/partials/context.blade.php'));
        $modals = file_get_contents(resource_path('views/backend/finance/invoices/partials/modals.blade.php'));

        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('<x-backend.detail-layout', $view);
        $this->assertStringContainsString('backend-kpi-grid', $view);
        $this->assertStringContainsString('data-invoice-section', $view);
        $this->assertStringContainsString("@include('backend.finance.invoices.partials.context')", $view);
        $this->assertStringContainsString("@include('backend.finance.invoices.partials.modals')", $view);
        $this->assertStringContainsString('backend-detail-side-card', $context);
        $this->assertStringContainsString('<x-backend.modal-close', $modals);
        $this->assertStringNotContainsString('Auth::', $view);
        $this->assertStringNotContainsString('::where(', $view);
        $this->assertStringNotContainsString('@php', $view);
        $this->assertStringNotContainsString('onclick=', $view.$context.$modals);
    }

    public function test_invoice_controller_delegates_projection_and_mutations_to_services(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/InvoiceAdminController.php'));
        $detailService = file_get_contents(app_path('Services/Invoices/InvoiceDetailService.php'));
        $mutationService = file_get_contents(app_path('Services/Invoices/InvoiceMutationService.php'));

        $this->assertStringContainsString('InvoiceDetailService $invoiceDetail', $controller);
        $this->assertStringContainsString('InvoiceMutationService $mutations', $controller);
        $this->assertStringContainsString("'reservations.agent'", $detailService);
        $this->assertStringContainsString("'transactions.wallet'", $detailService);
        $this->assertStringContainsString('historicalValues($order, $invoice)', $detailService);
        $this->assertStringContainsString('DB::transaction', $mutationService);
        $this->assertStringContainsString('lockForUpdate()', $mutationService);
        $this->assertStringContainsString('round($rate * $unit * $times, 2)', $mutationService);
    }

    public function test_invoice_mutations_have_authorization_and_server_validation(): void
    {
        foreach ([
            app_path('Http/Requests/StoreAdditionalInvoiceRequest.php'),
            app_path('Http/Requests/UpdateAdditionalInvoiceRequest.php'),
            app_path('Http/Requests/Invoices/UpdateInvoiceBankRequest.php'),
        ] as $requestFile) {
            $request = file_get_contents($requestFile);

            $this->assertStringContainsString('function authorize', $request);
            $this->assertStringContainsString('$this->user()', $request);
            $this->assertStringNotContainsString('return false;', $request);
        }

        $store = file_get_contents(app_path('Http/Requests/StoreAdditionalInvoiceRequest.php'));
        $this->assertStringContainsString("'date_format:Y-m-d'", $store);
        $this->assertStringContainsString("'description' => ['required'", $store);
        $this->assertStringContainsString("'rate' => ['required', 'numeric'", $store);
    }

    public function test_invoice_workspace_has_the_same_keys_in_all_supported_locales(): void
    {
        $english = require resource_path('lang/en/invoices.php');
        $simplified = require resource_path('lang/zh-CN/invoices.php');
        $traditional = require resource_path('lang/zh/invoices.php');

        $this->assertSame([], array_diff(array_keys($english), array_keys($simplified)));
        $this->assertSame([], array_diff(array_keys($simplified), array_keys($english)));
        $this->assertSame([], array_diff(array_keys($english), array_keys($traditional)));
        $this->assertSame([], array_diff(array_keys($traditional), array_keys($english)));
        $this->assertIsString(json_encode($english, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
        $this->assertIsString(json_encode($simplified, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
        $this->assertIsString(json_encode($traditional, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }
}
