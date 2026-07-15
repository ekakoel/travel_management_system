<?php

namespace Tests\Feature;

use App\Models\Activities;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ActivityDetailGuestManifestTest extends TestCase
{
    private function findApprovedPartnerUser(): User
    {
        return User::query()
            ->whereNotNull('email_verified_at')
            ->where('is_approved', 1)
            ->where('status', 'Active')
            ->whereRaw("TRIM(name) <> ''")
            ->whereRaw("TRIM(phone) <> ''")
            ->whereRaw("TRIM(office) <> ''")
            ->whereRaw("TRIM(address) <> ''")
            ->whereRaw("TRIM(country) <> ''")
            ->firstOrFail();
    }

    private function findOrderableActivity(): Activities
    {
        return Activities::query()
            ->where('status', 'Active')
            ->whereNotNull('code')
            ->whereRaw("TRIM(code) <> ''")
            ->firstOrFail();
    }

    public function test_activity_detail_renders_standard_guest_manifest_inputs(): void
    {
        $user = $this->findApprovedPartnerUser();
        $activity = $this->findOrderableActivity();

        $response = $this->actingAs($user)->get(route('view.activity-public-detail', $activity->code));

        $response->assertOk();
        $response->assertSee('data-activity-order-form', false);
        $response->assertSee('frontend-order-modal', false);
        $response->assertSee('data-activity-guest-table-body', false);
        $response->assertSee('data-activity-guest-save', false);
        $response->assertSee('data-activity-guest-field="name"', false);
        $response->assertSee('data-activity-guest-field="phone"', false);
        $response->assertSee('data-activity-guest-field="age"', false);
        $response->assertSee('data-activity-guest-field="sex"', false);
        $response->assertSee('data-activity-guest-field="is_leader"', false);
        $response->assertSee('data-activity-guest-inputs', false);
        $response->assertSee('data-activity-order-review="leader"', false);
        $response->assertSee('data-activity-order-overlay', false);
        $response->assertSee('aria-hidden="true"', false);
        $response->assertSee(__('messages.Price/Pax'));
        $response->assertSee(__('messages.Number of Guests'));
        $response->assertDontSee(__('messages.Normal Price'));
    }

    public function test_activity_order_requires_leader_guest_with_phone(): void
    {
        $user = $this->findApprovedPartnerUser();
        $activity = $this->findOrderableActivity();

        $payload = [
            'number_of_guests' => 2,
            'travel_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'note' => 'Activity regression test',
            'activity_order_source' => 'activity-detail-modern',
            'terms_accepted' => '1',
            'guests' => [
                [
                    'name' => 'Guest One',
                    'phone' => '',
                    'age' => 'Adult',
                    'sex' => 'Male',
                    'is_leader' => '1',
                ],
                [
                    'name' => 'Guest Two',
                    'phone' => '',
                    'age' => 'Child',
                    'sex' => 'Female',
                    'is_leader' => '0',
                ],
            ],
        ];

        $response = $this->from(route('view.activity-public-detail', $activity->code))
            ->actingAs($user)
            ->post(route('view.activity-order.store', $activity->code), $payload);

        $response->assertRedirect(route('view.activity-public-detail', $activity->code));
        $response->assertSessionHasErrors(['guests']);
    }

    public function test_activity_order_allows_fewer_guest_details_than_number_of_guests_when_leader_has_phone(): void
    {
        Mail::fake();

        $user = $this->findApprovedPartnerUser();
        $activity = $this->findOrderableActivity();

        $payload = [
            'number_of_guests' => 10,
            'travel_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'note' => 'Activity mismatch guest count test',
            'activity_order_source' => 'activity-detail-modern',
            'terms_accepted' => '1',
            'guests' => [
                [
                    'name' => 'Leader Guest',
                    'phone' => '+628123456789',
                    'age' => 'Adult',
                    'sex' => 'Male',
                    'is_leader' => '1',
                ],
            ],
        ];

        $response = $this->actingAs($user)
            ->post(route('view.activity-order.store', $activity->code), $payload);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_activity_detail_uses_active_locale_for_order_copy(): void
    {
        $user = $this->findApprovedPartnerUser();
        $activity = $this->findOrderableActivity();
        $originalPreferredLanguage = $user->preferred_language;

        try {
            $user->forceFill(['preferred_language' => 'zh-CN'])->save();
            $user->refresh();
            app()->setLocale('zh-CN');

            $response = $this->actingAs($user)
                ->get(route('view.activity-public-detail', $activity->code));

            $response->assertOk();
            $response->assertSee('data-locale="zh-CN"', false);
            $response->assertSee(__('activities.detail.order.processing_title'), false);
            $response->assertSee(__('activities.detail.order.cta_to_guest_details'), false);
        } finally {
            $user->forceFill(['preferred_language' => $originalPreferredLanguage])->save();
        }
    }
}
