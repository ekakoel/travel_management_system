<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_internal_user_can_access_admin_dashboard(): void
    {
        $user = $this->makeUser('developer');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('backend.admin.dashboard.index');
        $response->assertSee('Admin Dashboard');
        $response->assertViewHasAll([
            'kpis',
            'services',
            'recentActivities',
            'upcomingServices',
            'attentionItems',
            'orderStatus',
            'reservationStatus',
        ]);
        $this->assertSame('month', $response->viewData('period'));

        $view = file_get_contents(resource_path('views/backend/admin/dashboard/index.blade.php'));
        $this->assertStringContainsString('<x-backend.page-hero class="admin-dashboard-hero"', $view);
        $this->assertStringContainsString('backend-page-toolbar admin-dashboard-toolbar', $view);
        $this->assertStringContainsString('class="backend-toolbar-filter admin-dashboard-filter"', $view);
        $this->assertStringContainsString('backend-toolbar-filter__label', $view);
        $this->assertStringContainsString('backend-toolbar-filter__control', $view);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--6', $view);
        $this->assertStringContainsString('backend-kpi-card backend-kpi-card--', $view);
        $this->assertStringContainsString('backend-status-badge backend-status-badge--info admin-dashboard-badge', $view);
        $this->assertStringContainsString('backend-list admin-dashboard-list', $view);
        $this->assertStringContainsString('backend-list-item admin-dashboard-list__item', $view);
        $this->assertStringContainsString('backend-list-item__meta admin-dashboard-list__meta', $view);
        $this->assertStringContainsString('backend-empty-state backend-empty-state--compact admin-dashboard-empty', $view);
        $this->assertStringContainsString('class="backend-panel"', $view);
        $this->assertStringContainsString('class="backend-section-header"', $view);
        $this->assertStringContainsString('backend-section-header__label', $view);
        $this->assertStringNotContainsString('admin-dashboard-section', $view);
        $this->assertStringNotContainsString('<x-slot name="action">', $view);

        $scss = file_get_contents(resource_path('backend/scss/admin/dashboard/_index.scss'));
        $this->assertStringNotContainsString('.admin-dashboard-section', $scss);
        $this->assertStringNotContainsString('.admin-dashboard-section__header', $scss);
        $this->assertStringNotContainsString('.admin-dashboard-section__label', $scss);
        $this->assertStringNotContainsString('.admin-dashboard-filter', $scss);
        $this->assertStringNotContainsString('.admin-dashboard-badge', $scss);
        $this->assertStringNotContainsString('.admin-dashboard-list {', $scss);
        $this->assertStringNotContainsString('.admin-dashboard-list__item {', $scss);
        $this->assertStringNotContainsString('.admin-dashboard-empty', $scss);
        $this->assertStringNotContainsString('.admin-dashboard-stat ', $scss);
        $this->assertStringNotContainsString('.admin-dashboard-stat,', $scss);
        $this->assertStringNotContainsString('.admin-dashboard-stat-grid', $scss);
        $this->assertStringNotContainsString('.admin-dashboard-stat__icon', $scss);
    }

    public function test_agent_cannot_access_admin_dashboard(): void
    {
        $agent = $this->makeUser('agent');

        $this->actingAs($agent)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_dashboard_navigation_is_hidden_from_agent(): void
    {
        $internal = $this->makeUser('reservation', 'reservation-nav@example.test');
        $agent = $this->makeUser('agent', 'agent-nav@example.test');

        $this->actingAs($internal);
        $this->assertStringContainsString(route('admin.dashboard'), view('layouts.left-navbar')->render());
        $this->assertStringContainsString(route('admin.dashboard'), view('component.menu')->render());

        $this->actingAs($agent);
        $this->assertStringNotContainsString(route('admin.dashboard'), view('layouts.left-navbar')->render());
        $this->assertStringNotContainsString(route('admin.dashboard'), view('component.menu')->render());
    }

    private function makeUser(string $position, ?string $email = null): User
    {
        $attributes = [
            'username' => $position . '-' . uniqid(),
            'name' => ucfirst($position) . ' User',
            'email' => $email ?: $position . '-' . uniqid() . '@example.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'type' => $position === 'agent' ? 'user' : 'admin',
            'position' => $position,
            'status' => 'Active',
            'is_subscribed' => false,
            'subscriber' => false,
        ];

        if (Schema::hasColumn('users', 'is_approved')) {
            $attributes['is_approved'] = true;
        }

        return User::create($attributes);
    }
}
