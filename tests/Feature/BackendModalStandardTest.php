<?php

namespace Tests\Feature;

use Tests\TestCase;

class BackendModalStandardTest extends TestCase
{
    public function test_backend_modal_has_one_version_independent_close_contract(): void
    {
        $component = file_get_contents(resource_path('views/components/backend/modal-close.blade.php'));
        $javascript = file_get_contents(resource_path('backend/js/app.js'));
        $scss = file_get_contents(resource_path('backend/scss/components/_backend-modal.scss'));
        $standard = file_get_contents(base_path('docs/decisions/backend-ui-standards.md'));

        $this->assertStringContainsString('data-backend-modal-close', $component);
        $this->assertStringContainsString("function showBackendModal(modal)", $javascript);
        $this->assertStringContainsString("function closeBackendModal(modal)", $javascript);
        $this->assertStringContainsString("typeof bootstrapModal?.getOrCreateInstance === 'function'", $javascript);
        $this->assertStringContainsString("event.target.closest('[data-backend-modal-close]')", $javascript);
        $this->assertStringContainsString('window.showBackendModal = showBackendModal', $javascript);
        $this->assertStringContainsString('window.closeBackendModal = closeBackendModal', $javascript);
        $this->assertStringContainsString('.backend-modal-close', $scss);
        $this->assertStringContainsString('<x-backend.modal-close>', $standard);
        $this->assertStringContainsString('showBackendModal', $standard);
    }

    public function test_reservation_calendar_modal_uses_one_header_close_only(): void
    {
        $view = file_get_contents(resource_path('views/backend/operations/reservations/index.blade.php'));
        $javascript = file_get_contents(resource_path('backend/js/operations/reservations/index.js'));

        $this->assertSame(1, substr_count($view, '<x-backend.modal-close'));
        $this->assertStringNotContainsString('data-dismiss="modal"', $view);
        $this->assertStringNotContainsString('data-bs-dismiss="modal"', $view);
        $this->assertStringContainsString('window.showBackendModal(calendarModal)', $javascript);
        $this->assertSame(1, substr_count($view, "__('reservations.calendar_close')"));
    }
}
