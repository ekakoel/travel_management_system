<?php

namespace Tests\Feature;

use App\Models\FooterLink;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FooterManagerLinkBooleanTest extends TestCase
{
    use DatabaseTransactions;

    private function makeAdmin(): User
    {
        return User::create([
            'username' => 'footer-admin-' . uniqid(),
            'name' => 'Footer Admin',
            'type' => 'admin',
            'code' => 'ADM',
            'email' => 'footer-admin-' . uniqid() . '@example.test',
            'position' => 'developer',
            'phone' => '08123456789',
            'office' => 'Bali Kami Tour',
            'address' => 'Bali',
            'country' => 'Indonesia',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'is_subscribed' => false,
            'subscriber' => false,
        ]);
    }

    public function test_footer_link_create_stores_open_new_tab_and_active_checkbox_values(): void
    {
        $admin = $this->makeAdmin();
        $label = 'Test Footer Link ' . uniqid();

        $response = $this->actingAs($admin)->post(route('admin.footer-manager.links.store'), [
            'group' => 'policies',
            'label' => $label,
            'route_name' => 'faq',
            'url' => null,
            'sort_order' => 40,
            'open_new_tab' => '1',
            'status' => '1',
        ]);

        $response->assertRedirect(route('admin.footer-manager.index'));

        $link = FooterLink::query()->where('label', $label)->firstOrFail();

        $this->assertTrue($link->open_new_tab);
        $this->assertTrue($link->status);
    }

    public function test_footer_link_update_stores_unchecked_open_new_tab_and_inactive_values(): void
    {
        $admin = $this->makeAdmin();
        $link = FooterLink::create([
            'group' => 'quick_links',
            'label' => 'Editable Footer Link ' . uniqid(),
            'route_name' => 'about-us',
            'sort_order' => 10,
            'open_new_tab' => true,
            'status' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.footer-manager.links.update', $link), [
            'group' => 'quick_links',
            'label' => $link->label,
            'route_name' => 'contact-us',
            'url' => null,
            'sort_order' => 20,
            'open_new_tab' => '0',
            'status' => '0',
        ]);

        $response->assertRedirect(route('admin.footer-manager.index'));

        $link->refresh();

        $this->assertFalse($link->open_new_tab);
        $this->assertFalse($link->status);
    }
}
