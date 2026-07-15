<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class FrontendOrderModalStandardTest extends TestCase
{
    public function test_all_frontend_service_order_modals_use_the_shared_contract(): void
    {
        $templates = [
            resource_path('views/frontend/landing-page/activities/detail.blade.php'),
            resource_path('views/frontend/landing-page/tours/detail.blade.php'),
            resource_path('views/frontend/landing-page/transports/detail.blade.php'),
        ];

        $requiredClasses = [
            'frontend-order-modal',
            'frontend-order-modal__dialog',
            'frontend-order-modal__surface',
            'frontend-order-modal__form',
            'frontend-order-modal__service',
            'frontend-order-modal__nav',
            'frontend-order-modal__nav-item',
            'frontend-order-modal__panel',
            'frontend-order-modal__actions',
        ];

        foreach ($templates as $template) {
            $contents = file_get_contents($template);

            foreach ($requiredClasses as $requiredClass) {
                $this->assertStringContainsString($requiredClass, $contents, "$template must use $requiredClass");
            }
        }

        $overlayPartial = file_get_contents(resource_path('views/partials/form-submit-overlay.blade.php'));
        $this->assertStringContainsString('frontend-order-modal__overlay', $overlayPartial);
    }

    public function test_order_modal_page_entries_load_the_shared_style_as_the_final_layer(): void
    {
        $entries = [
            resource_path('frontend/scss/landing-page/activities/detail-entry.scss') => "@import '../../components/frontend-order-modal';",
            resource_path('frontend/scss/landing-page/tours/detail-entry.scss') => "@import '../../components/frontend-order-modal';",
            resource_path('frontend/scss/landing-page/transports/detail-entry.scss') => "@import '../../components/frontend-order-modal';",
        ];

        foreach ($entries as $entry => $expectedFinalImport) {
            $contents = trim(file_get_contents($entry));

            $this->assertStringEndsWith($expectedFinalImport, str_replace('"', "'", $contents));
        }
    }

    public function test_frontend_service_order_modal_templates_compile(): void
    {
        $templates = [
            resource_path('views/frontend/landing-page/activities/detail.blade.php'),
            resource_path('views/frontend/landing-page/tours/detail.blade.php'),
            resource_path('views/frontend/landing-page/transports/detail.blade.php'),
        ];

        foreach ($templates as $template) {
            $compiled = Blade::compileString(file_get_contents($template));

            $this->assertNotSame('', trim($compiled), "$template must compile to Blade output");
        }
    }

    public function test_transport_flight_fields_belong_to_service_before_guest_details(): void
    {
        $template = file_get_contents(resource_path('views/frontend/landing-page/transports/detail.blade.php'));
        $styles = file_get_contents(resource_path('frontend/scss/landing-page/transports/_detail.scss'));
        $script = file_get_contents(resource_path('frontend/js/landing-page/transports/detail.js'));
        $servicePanel = strpos($template, 'data-wizard-panel="1"');
        $flightFields = strpos($template, 'data-modal-flight-grid');
        $flightNumber = strpos($template, 'id="flight_number"');
        $flightDate = strpos($template, 'id="flight_date"');
        $duration = strpos($template, 'id="duration"');
        $pickupLocation = strpos($template, 'id="pickup_location"');
        $dropoffLocation = strpos($template, 'id="dropoff_location"');
        $guestPanel = strpos($template, 'data-wizard-panel="2"');

        $this->assertIsInt($servicePanel);
        $this->assertIsInt($flightFields);
        $this->assertIsInt($flightNumber);
        $this->assertIsInt($flightDate);
        $this->assertIsInt($duration);
        $this->assertIsInt($pickupLocation);
        $this->assertIsInt($dropoffLocation);
        $this->assertIsInt($guestPanel);
        $this->assertGreaterThan($servicePanel, $flightFields);
        $this->assertLessThan($guestPanel, $flightFields);
        $this->assertGreaterThan($flightNumber, $flightDate);
        $this->assertLessThan($guestPanel, $flightDate);
        $this->assertGreaterThan($flightDate, $duration);
        $this->assertLessThan($guestPanel, $duration);
        $this->assertGreaterThan($duration, $pickupLocation);
        $this->assertLessThan($guestPanel, $pickupLocation);
        $this->assertGreaterThan($pickupLocation, $dropoffLocation);
        $this->assertLessThan($guestPanel, $dropoffLocation);
        $this->assertSame(1, substr_count($template, 'data-modal-flight-grid'));
        $this->assertSame(1, substr_count($template, 'id="duration"'));
        $this->assertSame(1, substr_count($template, 'id="pickup_location"'));
        $this->assertSame(1, substr_count($template, 'id="dropoff_location"'));
        $this->assertStringContainsString('transport-reservation-grid--service-pair', $template);
        $this->assertStringNotContainsString('transport-reservation-service-panel', $template);
        $this->assertStringContainsString('.transport-reservation-grid--service-pair', $styles);
        $this->assertStringContainsString('.transport-reservation-grid--daily-rent-service', $styles);
        $this->assertStringContainsString('gap: 18px;', $styles);
        $this->assertStringContainsString("modalFlightGrid.classList.toggle('transport-reservation-grid--daily-rent-service', isDailyRent);", $script);
        $this->assertStringContainsString("@lang('transports.detail.order.guest_tab')", $template);
        $this->assertStringNotContainsString('Trip & Guests', $template);
        $this->assertStringNotContainsString('Trip and Guest Details', $template);
    }

    public function test_transport_modal_uses_visible_flight_date_as_the_hidden_service_date_source(): void
    {
        $template = file_get_contents(resource_path('views/frontend/landing-page/transports/detail.blade.php'));
        $controller = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $script = file_get_contents(resource_path('frontend/js/landing-page/transports/detail.js'));

        $this->assertStringContainsString('type="hidden" name="service_date"', $template);
        $this->assertSame(1, substr_count($template, 'name="service_date"'));
        $this->assertSame(1, substr_count($template, 'name="flight_date"'));
        $this->assertStringContainsString("for=\"flight_date\" data-transport-date-label>@lang('transports.detail.order.flight_date')", $template);
        $this->assertStringNotContainsString("\$validationRules['service_date']", $controller);
        $this->assertStringContainsString("\$validationRules['flight_date'] = ['required', 'date'];", $controller);
        $this->assertStringContainsString("\$validated['service_date'] = \$validated['flight_date'];", $controller);
        $this->assertStringContainsString("\$departureTime = \$validated['service_date'];", $controller);
        $this->assertStringContainsString("\$arrivalTime = \$validated['service_date'];", $controller);
        $this->assertStringContainsString('serviceDateInput.value = field.value;', $script);
        $this->assertStringContainsString('showInlineFieldError(field, message);', $script);
        $this->assertStringContainsString('focusFirstInvalidField(initialErrorStep);', $script);
        $this->assertStringContainsString("fieldError.className = 'alert-form';", $script);
        $this->assertStringContainsString('data-transport-order-number-input', $template);
        $this->assertStringContainsString('data-review-order-number', $template);
        $this->assertStringContainsString('data-order-number=', $template);
        $this->assertStringContainsString('data-transport-flight-date-label', $template);
        $this->assertStringContainsString('data-transport-validation-guest-name', $template);
        $this->assertStringContainsString('data-transport-guest-label-template', $template);
        $this->assertStringContainsString('data-transport-pax-label', $template);
        $this->assertStringContainsString('function syncTransportOrderNumberFromAgent()', $script);
        $this->assertStringContainsString("page.setAttribute('data-transport-booking-order-number', orderNumber);", $script);
        $this->assertStringContainsString("var flightDateLabel = page.getAttribute('data-transport-flight-date-label')", $script);
        $this->assertStringContainsString('function getGuestLabel(index)', $script);
        $this->assertStringContainsString("reviewGuestsTotal.textContent = guestEntries.length + ' ' + paxLabel;", $script);
        $this->assertStringNotContainsString('showServiceStepError', $script);
        $this->assertStringNotContainsString('showTripStepError', $script);
    }

    public function test_transport_frontend_order_copy_uses_language_files(): void
    {
        $template = file_get_contents(resource_path('views/frontend/landing-page/transports/detail.blade.php'));
        $script = file_get_contents(resource_path('frontend/js/landing-page/transports/detail.js'));

        foreach (['en', 'zh', 'zh-CN'] as $locale) {
            $this->assertFileExists(resource_path("lang/{$locale}/transports.php"));
        }

        $this->assertStringContainsString("@lang('transports.detail.order.processing_title')", $template);
        $this->assertStringContainsString("@lang('transports.detail.order.reservation_title')", $template);
        $this->assertStringContainsString("@lang('transports.detail.order.guest_label", $template);
        $this->assertStringContainsString("@lang('transports.detail.order.review_note')", $template);
        $this->assertStringContainsString('guestNameValidationMessage', $script);
        $this->assertStringContainsString('serviceRequiredValidationMessage', $script);
        $this->assertStringNotContainsString('Professional transport service ready for reservation', $template);
        $this->assertStringNotContainsString('Guest details will appear here after they are entered.', $template);
    }

    public function test_transport_review_tab_displays_guest_details_from_guest_inputs(): void
    {
        $template = file_get_contents(resource_path('views/frontend/landing-page/transports/detail.blade.php'));
        $script = file_get_contents(resource_path('frontend/js/landing-page/transports/detail.js'));
        $styles = file_get_contents(resource_path('frontend/scss/landing-page/transports/_detail.scss'));

        $this->assertStringContainsString('data-review-guest-table-body', $template);
        $this->assertStringContainsString('data-review-guest-empty', $template);
        $this->assertStringContainsString('transport-reservation-review-guests__table', $template);
        $this->assertStringContainsString("->filter(fn (\$guestEntry) => \$guestEntry['name'] !== '')", $template);

        $this->assertStringContainsString("var reviewGuestTableBody = page.querySelector('[data-review-guest-table-body]');", $script);
        $this->assertStringContainsString('function getReviewableGuestEntries()', $script);
        $this->assertStringContainsString('function renderReviewGuestRows(guestEntries)', $script);
        $this->assertStringContainsString("return guest.name !== '';", $script);
        $this->assertStringContainsString('cell.textContent = value;', $script);
        $this->assertStringContainsString('renderReviewGuestRows(guestEntries);', $script);

        $this->assertStringContainsString('.transport-reservation-review-guests__table-wrap', $styles);
        $this->assertStringContainsString('overflow-x: auto;', $styles);
    }

    public function test_tour_modal_follows_the_activity_modal_structure(): void
    {
        $template = file_get_contents(resource_path('views/frontend/landing-page/tours/detail.blade.php'));
        $modalStart = strpos($template, 'id="tourReservationModal"');
        $modal = substr($template, $modalStart);
        $service = strpos($modal, 'frontend-order-modal__service');
        $navigation = strpos($modal, 'frontend-order-modal__nav');
        $firstPanel = strpos($modal, 'frontend-order-modal__panel');

        $this->assertIsInt($modalStart);
        $this->assertIsInt($service);
        $this->assertIsInt($navigation);
        $this->assertIsInt($firstPanel);
        $this->assertLessThan($navigation, $service);
        $this->assertLessThan($firstPanel, $navigation);
        $this->assertSame(3, substr_count($modal, 'frontend-order-modal__nav-item'));
        $this->assertSame(3, substr_count($modal, 'class="frontend-order-modal__heading"'));
        $this->assertSame(3, substr_count($modal, 'frontend-order-modal__actions'));
        $this->assertStringNotContainsString('modal-dialog-scrollable', $modal);
        $this->assertStringNotContainsString('tour-reservation-section', $modal);
        $this->assertStringNotContainsString('tour-reservation-note', $modal);
        $this->assertStringNotContainsString('invalid-feedback d-block', $modal);
        $this->assertStringContainsString('type="submit" class="btn btn-primary" data-tour-wizard-submit', $modal);
    }

    public function test_shared_order_modal_enforces_fullscreen_overlay_and_interaction_lock(): void
    {
        $styles = file_get_contents(resource_path('frontend/scss/components/frontend-order-modal.scss'));

        $this->assertStringContainsString('.frontend-order-modal__overlay', $styles);
        $this->assertStringContainsString('position: fixed !important;', $styles);
        $this->assertStringContainsString('inset: 0 !important;', $styles);
        $this->assertStringContainsString('z-index: 2147483647 !important;', $styles);
        $this->assertStringContainsString('body.frontend-order-submit-locked', $styles);
        $this->assertStringContainsString('touch-action: none;', $styles);
    }

    public function test_service_order_modal_root_scrolls_instead_of_the_surface(): void
    {
        $styles = file_get_contents(resource_path('frontend/scss/components/frontend-order-modal.scss'));
        $tourStyles = file_get_contents(resource_path('frontend/scss/landing-page/tours/_detail.scss'));
        $transportStyles = file_get_contents(resource_path('frontend/scss/landing-page/transports/_detail.scss'));

        $this->assertStringContainsString('overflow-x: hidden;', $styles);
        $this->assertStringContainsString('overflow-y: auto;', $styles);
        $this->assertStringContainsString('overscroll-behavior: contain;', $styles);
        $this->assertStringContainsString(".frontend-order-modal__surface {\n    position: relative;\n    width: 100%;\n    overflow: visible;", str_replace("\r\n", "\n", $styles));
        $this->assertStringNotContainsString('max-height: calc(100dvh - 2rem);', $styles);
        $this->assertStringNotContainsString('max-height: calc(100dvh - 1.5rem);', $styles);
        $this->assertStringNotContainsString('scrollbar-gutter: stable;', $styles);

        $normalizedTourStyles = str_replace("\r\n", "\n", $tourStyles);
        $this->assertStringNotContainsString(".tour-reservation-modal .modal-content {\n    max-height:", $normalizedTourStyles);
        $this->assertStringNotContainsString(".tour-reservation-modal .modal-body {\n    min-height: 0;\n    flex: 1 1 auto;\n    overflow-y: auto;", $normalizedTourStyles);
        $this->assertStringContainsString(".transport-reservation-modal {\n        position: fixed;\n        inset: 0;", str_replace("\r\n", "\n", $transportStyles));
        $transportModalBlock = substr($transportStyles, strpos($transportStyles, '.transport-reservation-modal {'), 360);
        $this->assertStringContainsString('overflow-x: hidden;', $transportModalBlock);
        $this->assertStringContainsString('overflow-y: auto;', $transportModalBlock);
        $this->assertStringNotContainsString('overflow: hidden;', $transportModalBlock);
    }

    public function test_shared_order_modal_buttons_match_the_activity_order_button_contract(): void
    {
        $styles = file_get_contents(resource_path('frontend/scss/components/frontend-order-modal.scss'));
        $tourStyles = file_get_contents(resource_path('frontend/scss/landing-page/tours/_detail.scss'));

        $this->assertStringContainsString('.frontend-order-modal .btn {', $styles);
        $this->assertStringContainsString('min-height: var(--frontend-shell-button-height, 48px);', $styles);
        $this->assertStringContainsString('border-radius: var(--frontend-shell-radius-sm, 14px);', $styles);
        $this->assertStringContainsString('font-weight: 700;', $styles);
        $this->assertStringContainsString('.frontend-order-modal .btn-primary {', $styles);
        $this->assertStringContainsString('background: linear-gradient(135deg, var(--frontend-shell-primary, #0f5fa8) 0%, var(--frontend-shell-secondary, #0d7b8f) 100%);', $styles);
        $this->assertStringContainsString('.frontend-order-modal .btn-light {', $styles);
        $this->assertStringContainsString('.frontend-order-modal .btn-cancel {', $styles);
        $this->assertStringContainsString('.frontend-order-modal .btn.is-processing', $styles);
        $this->assertStringContainsString('gap: 0.55rem;', $tourStyles);
        $this->assertStringNotContainsString('gap: 0.6rem;', $tourStyles);
    }

    public function test_frontend_order_modals_use_flat_inner_content_except_media_and_navigation(): void
    {
        $sharedStyles = file_get_contents(resource_path('frontend/scss/components/frontend-order-modal.scss'));
        $activityStyles = file_get_contents(resource_path('frontend/scss/landing-page/activities/_detail.scss'));
        $transportStyles = file_get_contents(resource_path('frontend/scss/landing-page/transports/_detail.scss'));
        $tourStyles = file_get_contents(resource_path('frontend/scss/landing-page/tours/_detail.scss'));
        $bookingFormStyles = file_get_contents(resource_path('frontend/scss/components/frontend-forms.scss'));
        $frontendComponentStyles = file_get_contents(resource_path('frontend/scss/components/frontend-components.scss'));

        $this->assertStringContainsString(".frontend-order-modal__summary-card,\n.frontend-order-modal__price-card {\n    padding: 0.8rem 0;\n    border: 0;\n    border-radius: 0;\n    background: transparent;\n    box-shadow: none;", str_replace("\r\n", "\n", $sharedStyles));

        foreach ([$activityStyles, $transportStyles] as $styles) {
            $normalizedStyles = str_replace("\r\n", "\n", $styles);

            $this->assertStringContainsString("border-radius: 0;\n        background: transparent;\n        box-shadow: none;", $normalizedStyles);
            $this->assertStringContainsString('border-top: 1px solid #e2e8f0;', $normalizedStyles);
            $this->assertStringContainsString('border-left: 1px solid #e2e8f0;', $normalizedStyles);
        }

        $normalizedTourStyles = str_replace("\r\n", "\n", $tourStyles);

        $this->assertStringContainsString(".tour-guest-editor {\n    margin-top: 1rem;\n    padding-top: 1rem;\n    border-top: 1px solid #e2e8f0;\n    border-radius: 0;\n    background: transparent;", $normalizedTourStyles);
        $this->assertStringContainsString(".tour-review-grid > div {\n    padding: 0.85rem 0;\n    border: 0;\n    border-top: 1px solid #e2e8f0;\n    border-radius: 0;\n    background: transparent;", $normalizedTourStyles);
        $this->assertStringContainsString(".tour-reservation-modal .tour-order-summary,\n.tour-reservation-modal .tour-price-preview {\n    display: grid;\n    grid-template-columns: repeat(3, minmax(0, 1fr));\n    gap: 0;\n    margin-bottom: 1rem;\n    padding: 0;\n    border-top: 1px solid #dbe4ee;\n    border-bottom: 1px solid #dbe4ee;\n    border-radius: 0;\n    background: transparent;", $normalizedTourStyles);
        $this->assertStringContainsString(".tour-reservation-modal .tour-price-preview > div {\n    display: grid;\n    grid-auto-flow: column;\n    align-content: center;\n    justify-content: space-between;\n    padding: 0 18px;", $normalizedTourStyles);

        $normalizedBookingFormStyles = str_replace("\r\n", "\n", $bookingFormStyles);
        $normalizedFrontendComponentStyles = str_replace("\r\n", "\n", $frontendComponentStyles);

        $this->assertStringContainsString(".booking-review__card {\n    padding: 1rem 0;\n    border: 0;\n    border-radius: 0;\n    background: transparent;\n    box-shadow: none;", $normalizedBookingFormStyles);
        $this->assertStringContainsString(".booking-review__fact,\n.booking-review__room-item,\n.booking-review__transport-block {\n    padding: 0.85rem 0;\n    border: 0;\n    border-radius: 0;\n    background: transparent;\n    box-shadow: none;", $normalizedBookingFormStyles);
        $this->assertStringContainsString(".booking-review__room-item,\n.booking-review__transport-block,\n.booking-review__fact,\n.booking-review__empty {\n    border: 0;\n    border-radius: 0;\n    background: transparent;\n    box-shadow: none;", $normalizedFrontendComponentStyles);
    }

    public function test_frontend_order_review_separators_are_consistent_without_left_dividers(): void
    {
        $activityStyles = str_replace("\r\n", "\n", file_get_contents(resource_path('frontend/scss/landing-page/activities/_detail.scss')));
        $transportStyles = str_replace("\r\n", "\n", file_get_contents(resource_path('frontend/scss/landing-page/transports/_detail.scss')));
        $tourStyles = str_replace("\r\n", "\n", file_get_contents(resource_path('frontend/scss/landing-page/tours/_detail.scss')));

        $this->assertStringContainsString(".activity-reservation-review-grid {\n        border-top: 0;\n    }", $activityStyles);
        $this->assertStringContainsString(".activity-reservation-review-card,\n    .activity-reservation-review-card + .activity-reservation-review-card,\n    .activity-reservation-review-card:nth-child(3n + 1) {\n        padding-left: 0;\n        border-left: 0;\n        border-top: 1px solid #e2e8f0;", $activityStyles);

        $this->assertStringContainsString(".transport-reservation-review-grid {\n        border-top: 0;\n    }", $transportStyles);
        $this->assertStringContainsString(".transport-reservation-review-card,\n    .transport-reservation-review-card + .transport-reservation-review-card,\n    .transport-reservation-review-card:nth-child(odd) {\n        padding-left: 0;\n        border-left: 0;\n        border-top: 1px solid #e2e8f0;", $transportStyles);

        $this->assertStringContainsString(".tour-review-grid {\n    display: grid;\n    grid-template-columns: repeat(2, minmax(0, 1fr));\n    gap: 0;\n    margin-bottom: 1rem;\n    border-top: 0;", $tourStyles);
        $this->assertStringContainsString(".tour-review-grid > div:nth-child(even) {\n    padding-left: 0;\n    border-left: 0;", $tourStyles);
    }

    public function test_frontend_order_forms_require_terms_confirmation_before_submit(): void
    {
        $templates = [
            resource_path('views/frontend/landing-page/activities/detail.blade.php'),
            resource_path('views/frontend/landing-page/tours/detail.blade.php'),
            resource_path('views/frontend/landing-page/transports/detail.blade.php'),
            resource_path('views/frontend/home/booking/orders/hotel-normal.blade.php'),
            resource_path('views/frontend/home/booking/orders/hotel-package.blade.php'),
            resource_path('views/frontend/home/booking/orders/hotel-promo.blade.php'),
        ];

        foreach ($templates as $template) {
            $this->assertStringContainsString(
                "partials.order-confirmation-checkbox",
                file_get_contents($template),
                "$template must include the shared order confirmation checkbox"
            );
        }

        $partial = file_get_contents(resource_path('views/partials/order-confirmation-checkbox.blade.php'));
        $controller = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $activityScript = file_get_contents(resource_path('frontend/js/landing-page/activities/detail.js'));
        $transportScript = file_get_contents(resource_path('frontend/js/landing-page/transports/detail.js'));
        $hotelScript = file_get_contents(resource_path('frontend/js/pages/hotel-booking.js'));

        $this->assertStringContainsString('name="terms_accepted"', $partial);
        $this->assertStringContainsString('required', $partial);
        $this->assertStringContainsString('tour-detail.accept_terms_with_link', $partial);
        $this->assertStringContainsString("'terms_accepted' => ['accepted']", $controller);
        $this->assertStringContainsString('const termsCheckbox = orderForm.querySelector', $activityScript);
        $this->assertStringContainsString('validateField(termsCheckbox)', $activityScript);
        $this->assertStringContainsString('function validateTermsAccepted', $transportScript);
        $this->assertStringContainsString('validateTermsAccepted(false)', $transportScript);
        $this->assertStringContainsString('!formElement.checkValidity()', $hotelScript);
        $this->assertStringContainsString('formElement.reportValidity();', $hotelScript);
    }
}
