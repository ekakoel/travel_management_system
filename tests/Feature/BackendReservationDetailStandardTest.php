<?php

namespace Tests\Feature;

use Tests\TestCase;

class BackendReservationDetailStandardTest extends TestCase
{
    public function test_reservation_detail_uses_backend_detail_primitives_and_domain_assets(): void
    {
        $view = file_get_contents(resource_path('views/backend/operations/reservations/detail.blade.php'));
        $context = file_get_contents(resource_path('views/backend/operations/reservations/partials/context.blade.php'));
        $services = file_get_contents(resource_path('views/backend/operations/reservations/partials/services.blade.php'));
        $webpack = file_get_contents(base_path('webpack.mix.js'));

        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('<x-backend.detail-layout', $view);
        $this->assertStringContainsString('backend-page-toolbar', $view);
        $this->assertStringContainsString('backend-kpi-grid', $view);
        $this->assertStringContainsString('backend-detail-side-card', $context);
        $this->assertStringContainsString("partials.overview", $view);
        $this->assertStringContainsString("partials.manifest", $view);
        $this->assertStringContainsString("partials.services", $view);
        $this->assertStringContainsString('data-reservation-print', $context);
        $this->assertStringContainsString("route('admin.order.show'", file_get_contents(app_path('Services/Reservations/ReservationDetailService.php')));
        $this->assertStringContainsString("['detail_url']", $services);
        $this->assertStringNotContainsString('data-toggle="modal"', $view.$context.$services);
        $this->assertStringNotContainsString('name="inv_no"', $context);
        $this->assertStringNotContainsString('name="bank_id"', $context);
        $this->assertStringContainsString("mix('build/backend/css/operations/reservations/detail.css')", $view);
        $this->assertStringContainsString(".sass('resources/backend/scss/operations/reservations/detail-entry.scss'", $webpack);
    }

    public function test_detail_projection_is_scoped_and_controller_remains_thin(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/ReservationController.php'));
        $service = file_get_contents(app_path('Services/Reservations/ReservationDetailService.php'));

        $this->assertStringContainsString('?ReservationDetailService $reservationDetail = null', $controller);
        $this->assertStringContainsString("'backend.operations.reservations.detail'", $controller);
        $this->assertStringContainsString("app(ReservationDetailService::class)", $controller);
        $this->assertStringContainsString("->where('rsv_id', \$reservation->id)", $service);
        $this->assertStringContainsString("'agent',", $service);
        $this->assertStringContainsString("'invoice',", $service);
        $this->assertStringNotContainsString('Orders::all()', $service);
        $this->assertStringNotContainsString('User::all()', $service);
        $this->assertStringNotContainsString('OptionalRate::all()', $service);
        $this->assertStringNotContainsString('json_decode', $service);
        $this->assertStringContainsString("orWhere('status', '!=', 'Deleted')", $service);
        $this->assertStringContainsString('firstOrCreate(', $controller);
        $this->assertStringContainsString('->lockForUpdate()', $controller);
        $this->assertStringContainsString("route('view.reservation.invoice.store'", file_get_contents(resource_path('views/backend/operations/reservations/partials/context.blade.php')));
    }

    public function test_detail_copy_exists_for_all_supported_backend_locales(): void
    {
        foreach (['en', 'zh-CN', 'zh'] as $locale) {
            $copy = require resource_path("lang/{$locale}/reservations.php");

            foreach (['detail_title', 'detail_description', 'quick_actions', 'detail_sections', 'reservation_overview', 'guest_manifest', 'linked_services', 'trip_notes'] as $key) {
                $this->assertArrayHasKey($key, $copy, "Missing {$locale}.reservations.{$key}");
                $this->assertNotSame('', trim($copy[$key]));
            }
        }
    }

    public function test_reservation_detail_icons_match_the_loaded_font_awesome_4_contract(): void
    {
        $service = file_get_contents(app_path('Services/Reservations/ReservationDetailService.php'));
        $view = file_get_contents(resource_path('views/backend/operations/reservations/detail.blade.php'));
        $kpiStyles = file_get_contents(resource_path('backend/scss/components/_backend-kpi.scss'));
        $partials = collect(glob(resource_path('views/backend/operations/reservations/partials/*.blade.php')))
            ->map(fn ($file) => file_get_contents($file))
            ->implode("\n");

        foreach (['fa fa-calendar', 'fa fa-users', 'fa fa-shopping-cart', 'fa fa-file-text-o'] as $icon) {
            $this->assertStringContainsString("'icon' => '{$icon}'", $service);
        }

        foreach (['fas ', 'far ', 'fa-calendar-alt', 'fa-clipboard-check', 'fa-concierge-bell', 'fa-shuttle-van'] as $unsupported) {
            $this->assertStringNotContainsString($unsupported, $service.$partials);
        }

        foreach (['blue', 'teal', 'amber', 'green'] as $tone) {
            $this->assertStringContainsString("'tone' => '{$tone}'", $service);
        }

        foreach (['info', 'success', 'warning', 'primary'] as $unsupportedTone) {
            $this->assertStringNotContainsString("'tone' => '{$unsupportedTone}'", $service);
        }

        $this->assertStringContainsString('aria-hidden="true"><i class="{{ $stat[\'icon\'] }}"></i>', $view);
        $this->assertStringContainsString('background: #475569;', $kpiStyles);
        $this->assertStringContainsString('font-family: FontAwesome !important;', $kpiStyles);
    }

    public function test_print_summary_uses_the_existing_global_print_area_contract(): void
    {
        $view = file_get_contents(resource_path('views/backend/operations/reservations/detail.blade.php'));
        $script = file_get_contents(resource_path('backend/js/operations/reservations/detail.js'));
        $styles = file_get_contents(resource_path('backend/scss/operations/reservations/_detail.scss'));
        $globalStyles = file_get_contents(public_path('css/style.css'));

        $this->assertStringContainsString('body * {', $globalStyles);
        $this->assertStringContainsString('.print-area, .print-area *', $globalStyles);
        $this->assertStringContainsString('class="main-container reservation-detail-page print-area"', $view);
        $this->assertStringContainsString('data-reservation-print-area', $view);
        $this->assertStringContainsString('reservation-detail-print-header', $view);
        $this->assertStringContainsString("window.addEventListener('beforeprint'", $script);
        $this->assertStringContainsString("window.addEventListener('afterprint'", $script);
        $this->assertStringContainsString('event.preventDefault();', $script);
        $this->assertStringContainsString('window.print();', $script);
        $this->assertStringContainsString('.reservation-detail-page.print-area *', $styles);
        $this->assertStringContainsString('position: static !important;', $styles);
        $this->assertStringContainsString('@page {', $styles);
    }
}
