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
        $this->assertStringContainsString('control.append(spinner)', $javascript);
        $this->assertStringNotContainsString('control.prepend(spinner)', $javascript);
        $this->assertStringContainsString("'--backend-action-spinner-color'", $javascript);
        $this->assertStringContainsString("event.target.closest('[data-backend-action-loading]')", $javascript);
        $this->assertStringContainsString('window.setBackendActionLoading = setBackendActionLoading', $javascript);
        $this->assertStringContainsString("document.addEventListener('click', handleBackendActionClick)", $javascript);
        $this->assertStringContainsString("window.addEventListener('pageshow'", $javascript);
        $this->assertStringContainsString('.backend-action-spinner', $scss);
        $this->assertStringContainsString('position: absolute', $scss);
        $this->assertStringContainsString('color: transparent !important', $scss);
        $this->assertStringContainsString('> :not(.backend-action-spinner)', $scss);
        $this->assertStringContainsString('.backend-content-spinner', $scss);
        $this->assertStringContainsString('@keyframes backend-action-spin', $scss);
        $this->assertStringNotContainsString("@include('partials.loading-form'", $orderDetail);
    }

    public function test_page_scripts_do_not_render_competing_backend_button_spinners(): void
    {
        foreach ([
            resource_path('backend/js/admin/footer-manager/index.js'),
            resource_path('backend/js/admin/reviews/index.js'),
            resource_path('backend/js/operations/transport-management/index.js'),
            resource_path('backend/js/operations/transport-management/detail.js'),
            resource_path('views/admin/transportmanagement/detail-spk.blade.php'),
        ] as $file) {
            $contents = file_get_contents($file);

            $this->assertStringNotContainsString('fa-spinner', $contents);
            $this->assertStringNotContainsString('spinner-border', $contents);
        }
    }

    public function test_frontend_button_spinners_use_the_shared_action_spinner(): void
    {
        $scss = file_get_contents(resource_path('frontend/scss/components/_frontend-loading.scss'));

        $this->assertStringContainsString('.frontend-action-spinner', $scss);
        $this->assertStringContainsString("@import 'components/frontend-loading'", file_get_contents(resource_path('frontend/scss/app.scss')));

        foreach ([
            'frontend/js/pages/transport-booking.js',
            'frontend/js/pages/hotel-booking.js',
            'frontend/js/landing-page/transports/detail.js',
            'frontend/js/landing-page/tours/detail.js',
            'frontend/js/landing-page/activities/detail.js',
            'frontend/js/home/orders/edit.js',
            'frontend/js/home/orders/detail.js',
        ] as $relativePath) {
            $contents = file_get_contents(resource_path($relativePath));

            $this->assertStringContainsString('frontend-action-spinner', $contents);
            $this->assertStringNotContainsString('booking-submit-button__spinner', $contents);
            $this->assertStringNotContainsString('spinner--button', $contents);
        }
    }
}
