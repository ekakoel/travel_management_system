<?php

namespace Tests\Feature;

use App\Models\InvoiceAdmin;
use App\Models\PaymentConfirmation;
use App\Services\Invoices\InvoicePaymentStateResolver;
use Tests\TestCase;

class BackendInvoiceIndexStandardTest extends TestCase
{
    public function test_invoice_index_uses_the_canonical_backend_work_queue(): void
    {
        $view = file_get_contents(resource_path('views/backend/finance/invoices/index.blade.php'));

        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('backend-kpi-grid', $view);
        $this->assertStringContainsString('backend-filter-panel', $view);
        $this->assertStringContainsString('backend-table-card-list', $view);
        $this->assertStringContainsString('data-invoice-filter="search"', $view);
        $this->assertStringContainsString("\$row['detail_url']", $view);
        $this->assertStringNotContainsString('Auth::', $view);
        $this->assertStringNotContainsString('@php', $view);
        $this->assertStringNotContainsString('data-toggle=', $view);
        $this->assertStringNotContainsString('data-target=', $view);
        $this->assertStringNotContainsString('onclick=', $view);
        $this->assertStringNotContainsString('class="modal', $view);
    }

    public function test_invoice_index_projection_is_bounded_and_eager_loaded(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/InvoiceAdminController.php'));
        $service = file_get_contents(app_path('Services/Invoices/InvoiceIndexService.php'));

        $this->assertStringContainsString('InvoiceIndexService $invoiceIndex', $controller);
        $this->assertStringContainsString("'reservations.agent:id,name,office'", $service);
        $this->assertStringContainsString("'payment:id,inv_id,status'", $service);
        $this->assertStringContainsString("->withCount('additionalinv')", $service);
        $this->assertStringContainsString('->where(\'due_date\', \'>\', $now)', $service);
        $this->assertStringNotContainsString('BusinessProfile::', $controller);
        $this->assertStringNotContainsString('Reservation::all()', $controller);
    }

    public function test_payment_state_resolver_is_shared_and_uses_committed_payment_data(): void
    {
        $resolver = app(InvoicePaymentStateResolver::class);
        $invoice = new InvoiceAdmin();
        $invoice->setRelation('payment', collect());

        $this->assertSame('paid', $resolver->resolve($invoice, 0)['key']);
        $this->assertSame('unpaid', $resolver->resolve($invoice, 100)['key']);

        $invoice->setRelation('payment', collect([(new PaymentConfirmation())->forceFill(['status' => 'Pending'])]));
        $this->assertSame('review', $resolver->resolve($invoice, 100)['key']);

        $invoice->setRelation('payment', collect([(new PaymentConfirmation())->forceFill(['status' => 'Valid'])]));
        $this->assertSame('partial', $resolver->resolve($invoice, 50)['key']);
    }

    public function test_invoice_index_assets_are_registered(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));
        $script = file_get_contents(resource_path('backend/js/finance/invoices/index.js'));

        $this->assertStringContainsString('resources/backend/js/finance/invoices/index.js', $mix);
        $this->assertStringContainsString('resources/backend/scss/finance/invoices/index-entry.scss', $mix);
        $this->assertStringContainsString('[data-invoice-row]', $script);
        $this->assertStringContainsString('[data-invoice-filter-reset]', $script);
    }
}
