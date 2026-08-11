<?php

namespace Tests\Feature;

use Tests\TestCase;

class BackendSubmitGuardStandardTest extends TestCase
{
    public function test_backend_mutation_forms_use_one_shared_spinner_and_double_submit_guard(): void
    {
        $javascript = file_get_contents(resource_path('backend/js/app.js'));
        $scss = file_get_contents(resource_path('backend/scss/components/_backend-form.scss'));
        $orderDetail = file_get_contents(resource_path('views/admin/ordersadmindetail.blade.php'));

        $this->assertStringContainsString('function initBackendSubmitGuards', $javascript);
        $this->assertStringContainsString('function handleBackendActionClick', $javascript);
        $this->assertStringContainsString('function primeBackendSubmitting', $javascript);
        $this->assertStringContainsString("form.dataset.backendSubmitPending = 'true'", $javascript);
        $this->assertStringContainsString("form.dataset.backendSubmitting = 'true'", $javascript);
        $this->assertStringContainsString("event.stopImmediatePropagation()", $javascript);
        $this->assertStringContainsString("control.classList.toggle('is-submitting', loading)", $javascript);
        $this->assertStringContainsString("spinner.className = 'backend-action-spinner'", $javascript);
        $this->assertStringContainsString("event.target.closest('[data-backend-action-loading]')", $javascript);
        $this->assertStringContainsString('window.setBackendActionLoading = setBackendActionLoading', $javascript);
        $this->assertStringContainsString("document.addEventListener('click', handleBackendActionClick)", $javascript);
        $this->assertStringContainsString("window.addEventListener('pageshow'", $javascript);
        $this->assertStringContainsString('.backend-action-spinner', $scss);
        $this->assertStringContainsString('@keyframes backend-action-spin', $scss);
        $this->assertStringNotContainsString("@include('partials.loading-form'", $orderDetail);
    }
}
