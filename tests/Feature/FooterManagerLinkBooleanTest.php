<?php

namespace Tests\Feature;

use App\Models\FooterLink;
use App\Models\FooterSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
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

    public function test_footer_manager_uses_backend_structure_and_assets(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/FooterManagerController.php'));
        $view = file_get_contents(resource_path('views/backend/admin/footer-manager/index.blade.php'));
        $settingPartial = file_get_contents(resource_path('views/backend/admin/footer-manager/partials/setting-section.blade.php'));
        $modalPartial = file_get_contents(resource_path('views/backend/admin/footer-manager/partials/link-modal.blade.php'));
        $legacyView = file_get_contents(resource_path('views/admin/footer-manager/index.blade.php'));
        $scss = file_get_contents(resource_path('backend/scss/admin/footer-manager/_index.scss'));
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('views/backend/admin/footer-manager/index.blade.php'));
        $this->assertFileExists(resource_path('views/backend/admin/footer-manager/partials/setting-section.blade.php'));
        $this->assertFileExists(resource_path('views/backend/admin/footer-manager/partials/link-modal.blade.php'));
        $this->assertFileExists(resource_path('backend/js/admin/footer-manager/index.js'));
        $this->assertFileExists(resource_path('backend/scss/admin/footer-manager/index-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/admin/footer-manager/_index.scss'));
        $this->assertStringContainsString("view('backend.admin.footer-manager.index'", $controller);
        $this->assertStringContainsString('DB::transaction', $controller);
        $this->assertStringContainsString('summary', $controller);
        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('backend-button backend-button-primary', $view);
        $this->assertStringContainsString('backend-form-grid', $settingPartial . $modalPartial);
        $this->assertStringContainsString('backend-form-actions', $modalPartial);
        $this->assertStringContainsString('backend-page-toolbar footer-manager-toolbar', $view);
        $this->assertStringContainsString("route('admin.panel-main.view')", $view);
        $this->assertStringContainsString("mix('build/backend/css/admin/footer-manager/index.css')", $view);
        $this->assertStringContainsString("mix('build/backend/js/admin/footer-manager/index.js')", $view);
        $this->assertStringContainsString('backend-kpi-grid', $view);
        $this->assertStringContainsString('footer-manager-layout', $view);
        $this->assertStringContainsString('backend.admin.footer-manager.partials.setting-section', $view);
        $this->assertStringContainsString('backend.admin.footer-manager.partials.link-modal', $view);
        $this->assertStringContainsString('invalid-feedback', $settingPartial);
        $this->assertStringContainsString('invalid-feedback', $modalPartial);
        $this->assertStringContainsString('data-footer-delete-form', $view);
        $this->assertStringContainsString('data-footer-submit', $view);
        $this->assertStringContainsString("@include('backend.admin.footer-manager.index')", $legacyView);
        $this->assertStringNotContainsString('<style>', $view);
        $this->assertStringNotContainsString('card-box', $view);
        $this->assertStringContainsString('overflow-x: clip;', $scss);
        $this->assertStringContainsString('min-width: 0;', $scss);
        $this->assertStringContainsString('@media (max-width: 575px)', $scss);
        $this->assertStringContainsString("resources/backend/js/admin/footer-manager/index.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/admin/footer-manager/index-entry.scss", $mix);
    }

    public function test_footer_manager_page_renders_backend_view_and_updates_settings(): void
    {
        $admin = $this->makeAdmin();
        $setting = FooterSetting::query()->updateOrCreate(
            ['key' => 'test_footer_setting_' . uniqid()],
            [
                'value' => 'Old footer value',
                'value_traditional' => 'Old traditional value',
                'value_simplified' => 'Old simplified value',
                'status' => true,
            ]
        );

        Cache::put('footer_content.en', ['stale' => true], 3600);

        $response = $this->actingAs($admin)->get(route('admin.footer-manager.index'));

        $response->assertOk();
        $response->assertViewIs('backend.admin.footer-manager.index');
        $response->assertViewHasAll(['settings', 'links', 'summary']);
        $response->assertSee('Footer Manager');

        $this->actingAs($admin)
            ->put(route('admin.footer-manager.settings.update'), [
                'settings' => [
                    $setting->id => [
                        'value' => 'Updated footer value',
                        'value_traditional' => 'Updated traditional value',
                        'value_simplified' => 'Updated simplified value',
                        'status' => '0',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.footer-manager.index'));

        $setting->refresh();
        $this->assertSame('Updated footer value', $setting->value);
        $this->assertSame('Updated traditional value', $setting->value_traditional);
        $this->assertSame('Updated simplified value', $setting->value_simplified);
        $this->assertFalse((bool) $setting->status);
        $this->assertNull(Cache::get('footer_content.en'));
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

    public function test_footer_link_validation_and_delete_flow(): void
    {
        $admin = $this->makeAdmin();
        $existing = FooterLink::create([
            'group' => 'services',
            'label' => 'Duplicate Footer Link ' . uniqid(),
            'route_name' => 'about-us',
            'sort_order' => 10,
            'open_new_tab' => false,
            'status' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.footer-manager.links.store'), [
                'group' => 'services',
                'label' => $existing->label,
                'route_name' => 'contact-us',
                'url' => null,
                'sort_order' => 20,
                'status' => '1',
            ])
            ->assertSessionHasErrors('label');

        $this->actingAs($admin)
            ->delete(route('admin.footer-manager.links.destroy', $existing))
            ->assertRedirect(route('admin.footer-manager.index'));

        $this->assertDatabaseMissing('footer_links', [
            'id' => $existing->id,
        ]);
    }
}
