<?php

namespace Tests\Feature;

use Tests\TestCase;

class BackendRequiredMarkerStandardTest extends TestCase
{
    public function test_backend_required_marker_is_centralized_and_dynamic(): void
    {
        $javascript = file_get_contents(resource_path('backend/js/app.js'));
        $scss = file_get_contents(resource_path('backend/scss/components/_backend-form.scss'));
        $standard = file_get_contents(base_path('docs/decisions/backend-ui-standards.md'));

        $this->assertStringContainsString('input[required]:not([type="hidden"])', $javascript);
        $this->assertStringContainsString("'select[required]'", $javascript);
        $this->assertStringContainsString("'textarea[required]'", $javascript);
        $this->assertStringContainsString('function initBackendRequiredMarkers', $javascript);
        $this->assertStringContainsString('window.initBackendRequiredMarkers', $javascript);
        $this->assertStringContainsString('new MutationObserver', $javascript);
        $this->assertStringContainsString("candidate.textContent.trim() === '*'", $javascript);
        $this->assertStringContainsString("marker.setAttribute('aria-hidden', 'true')", $javascript);
        $this->assertStringContainsString(
            'label:not(.user-manager-check-field) > span.backend-required-marker',
            $scss
        );
        $this->assertStringContainsString('color: var(--backend-required) !important;', $scss);
        $this->assertStringContainsString('Atribut HTML `required` pada control adalah source of truth', $standard);
    }

    public function test_order_admin_required_controls_have_associated_labels(): void
    {
        $view = file_get_contents(resource_path('views/admin/ordersadmindetail.blade.php'));

        preg_match_all('/<(?:input|select|textarea)\b[^>]*\brequired\b[^>]*>/i', $view, $matches);

        // Blade expressions may contain `->`, so the lightweight tag regex
        // intentionally verifies only tags that can be parsed without rendering.
        $this->assertGreaterThanOrEqual(8, count($matches[0]));

        foreach ($matches[0] as $control) {
            $this->assertMatchesRegularExpression('/\bid="([^"]+)"/i', $control, $control);
            preg_match('/\bid="([^"]+)"/i', $control, $idMatch);
            $this->assertStringContainsString('for="'.$idMatch[1].'"', $view, $control);
        }

        foreach (['bank', 'currency', 'status', 'kurs_id', 'amount', 'payment_date'] as $name) {
            $this->assertMatchesRegularExpression(
                '/\bname="'.preg_quote($name, '/').'"[^\r\n]*\brequired\b/i',
                $view
            );
        }
    }
}
