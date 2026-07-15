<?php

namespace Tests\Feature;

use App\Models\TourPrices;
use App\Models\Tours;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TourDetailGuestManifestTest extends TestCase
{
    use WithFaker;

    private function findApprovedTourUser(): User
    {
        return User::query()
            ->whereNotNull('email_verified_at')
            ->where('is_approved', 1)
            ->where('status', 'Active')
            ->whereNotNull('name')
            ->whereNotNull('phone')
            ->whereNotNull('office')
            ->whereNotNull('address')
            ->whereNotNull('country')
            ->whereRaw("TRIM(name) <> ''")
            ->whereRaw("TRIM(phone) <> ''")
            ->whereRaw("TRIM(office) <> ''")
            ->whereRaw("TRIM(address) <> ''")
            ->whereRaw("TRIM(country) <> ''")
            ->firstOrFail();
    }

    private function findOrderableTour(): Tours
    {
        return Tours::query()
            ->whereNotNull('slug')
            ->whereRaw("TRIM(slug) <> ''")
            ->whereHas('prices', function ($query) {
                $query->where('status', 'Active');
            })
            ->with(['prices' => function ($query) {
                $query->where('status', 'Active')->orderBy('min_qty');
            }])
            ->firstOrFail();
    }

    public function test_detail_tour_renders_standard_guest_manifest_inputs_for_approved_user(): void
    {
        $user = $this->findApprovedTourUser();
        $tour = $this->findOrderableTour();

        $response = $this->actingAs($user)->get(route('view.tour-detail', $tour->slug));

        $response->assertOk();
        $response->assertSee('frontend-order-modal', false);
        $response->assertSee('frontend-order-modal__overlay', false);
        $response->assertSee('data-tour-order-form', false);
        $response->assertSee('data-tour-guest-table-body', false);
        $response->assertSee('data-tour-guest-save', false);
        $response->assertSee('data-tour-guest-field="name"', false);
        $response->assertSee('data-tour-guest-field="phone"', false);
        $response->assertSee('data-tour-guest-field="age"', false);
        $response->assertSee('data-tour-guest-field="sex"', false);
        $response->assertSee('data-tour-guest-field="identification_type"', false);
        $response->assertSee('data-tour-guest-field="identification_no"', false);
        $response->assertSee('data-tour-guest-field="is_leader"', false);
        $response->assertSee('data-tour-guest-inputs', false);
        $response->assertDontSee('data-tour-guest-list', false);
        $response->assertDontSee('data-tour-guest-template', false);
        $response->assertDontSee('date_of_birth', false);
        $response->assertSee('data-tour-review-field="guestCount"', false);
        $response->assertSee('data-tour-review-value="guestManifest"', false);
        $response->assertSee('data-tour-review-value="leader"', false);
        $response->assertSee('data-tour-review-guest-table-body', false);
        $response->assertSee('data-tour-review-guest-empty-row', false);

        $html = $response->getContent();
        $script = file_get_contents(resource_path('frontend/js/landing-page/tours/detail.js'));
        $this->assertSame(1, substr_count($html, __('tour-detail.trip_information_hint')));
        $this->assertSame(1, substr_count($html, __('tour-detail.guest_manifest_hint')));
        $this->assertStringContainsString('const renderGuestTable = () =>', $script);
        $this->assertStringContainsString('const renderReviewGuestTable = () =>', $script);
        $this->assertStringContainsString('data-tour-guest-edit', $script);
        $this->assertStringContainsString('renderGuestHiddenInputs();', $script);
        $this->assertStringContainsString('renderReviewGuestTable();', $script);
        $this->assertStringNotContainsString('syncGuestRowsToRequestedCount', $script);
    }

    public function test_tour_review_places_price_preview_above_terms_confirmation(): void
    {
        $template = file_get_contents(resource_path('views/frontend/landing-page/tours/detail.blade.php'));
        $reviewPanel = substr($template, strpos($template, 'data-tour-review-value="travelDate"'));
        $reviewGuestTable = strpos($reviewPanel, 'data-tour-review-guest-table-body');
        $pricePreview = strpos($reviewPanel, 'data-tour-price-preview');
        $termsConfirmation = strpos($reviewPanel, 'partials.order-confirmation-checkbox');

        $this->assertIsInt($reviewGuestTable);
        $this->assertIsInt($pricePreview);
        $this->assertIsInt($termsConfirmation);
        $this->assertLessThan($pricePreview, $reviewGuestTable);
        $this->assertLessThan($termsConfirmation, $pricePreview);
    }

    public function test_tour_order_requires_leader_phone_when_guest_leader_has_no_phone(): void
    {
        $user = $this->findApprovedTourUser();
        $tour = $this->findOrderableTour();
        /** @var TourPrices $price */
        $price = $tour->prices->firstOrFail();
        $guestCount = max(2, (int) $price->min_qty);

        $payload = [
            'submission_token' => 'tour-test-' . uniqid(),
            'number_of_guests' => $guestCount,
            'tour_price_id' => $price->id,
            'travel_date' => now()->addDays(7)->toDateString(),
            'pickup_location' => 'Hotel Test Pickup',
            'dropoff_location' => 'Hotel Test Dropoff',
            'lead_guest_email' => 'qa@example.com',
            'preferred_language' => 'English',
            'special_request' => 'Regression test',
            'terms_accepted' => '1',
            'guests' => [
                [
                    'name' => 'Guest One',
                    'phone' => '',
                    'age' => 'Adult',
                    'sex' => 'Male',
                    'identification_type' => 'Passport',
                    'identification_no' => 'P123456',
                    'is_leader' => '1',
                ],
                [
                    'name' => 'Guest Two',
                    'phone' => '',
                    'age' => 'Adult',
                    'sex' => 'Female',
                    'identification_type' => 'Passport',
                    'identification_no' => 'P654321',
                    'is_leader' => '0',
                ],
            ],
        ];

        $response = $this->from(route('view.tour-detail', $tour->slug))
            ->actingAs($user)
            ->post(route('func.order-tour-package.create', $tour->id), $payload);

        $response->assertRedirect(route('view.tour-detail', $tour->slug));
        $response->assertSessionHasErrors(['lead_guest_phone']);
    }

    public function test_tour_order_allows_one_guest_detail_for_a_larger_booking(): void
    {
        $user = $this->findApprovedTourUser();
        $tour = $this->findOrderableTour();
        /** @var TourPrices $price */
        $price = $tour->prices->firstOrFail();
        $guestCount = max(10, (int) $price->min_qty);

        $payload = [
            'submission_token' => 'tour-manifest-test-' . uniqid(),
            'user_id' => $user->id,
            'number_of_guests' => $guestCount,
            'tour_price_id' => $price->id,
            'travel_date' => now()->addDays(7)->toDateString(),
            'pickup_location' => 'Hotel Test Pickup',
            'dropoff_location' => 'Hotel Test Dropoff',
            'terms_accepted' => '1',
            'guests' => [[
                'name' => 'Leader Guest',
                'phone' => '+628123456789',
                'age' => 'Adult',
                'sex' => 'Male',
                'identification_type' => 'Passport',
                'identification_no' => 'P123456',
                'is_leader' => '1',
            ]],
        ];

        $response = $this->actingAs($user)
            ->post(route('func.order-tour-package.create', $tour->id), $payload);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }
}
