<?php

namespace Tests\Feature;

use App\Models\Activities;
use App\Models\BankAccount;
use App\Models\BusinessProfile;
use App\Models\Hotels;
use App\Models\ManualBook;
use App\Models\OptionalRate;
use App\Models\Orders;
use App\Models\Review;
use App\Models\Services;
use App\Models\Tax;
use App\Models\TermAndCondition;
use App\Models\Tours;
use App\Models\TourPrices;
use App\Models\TransportPrice;
use App\Models\Transports;
use App\Models\UsdRates;
use App\Models\User;
use App\Models\WebsiteVisit;
use App\Services\RegistrationAccessService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProjectStructureStandardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_project_structure_documentation_and_migration_tracker_exist(): void
    {
        foreach ([
            'docs/README.md',
            'docs/architecture.md',
            'docs/database.md',
            'docs/coding-standards.md',
            'docs/security-rules.md',
            'docs/status-contract.md',
            'docs/frontend-standards.md',
            'docs/testing.md',
            'docs/modules/accommodation.md',
            'docs/modules/transport.md',
            'docs/modules/tour-package.md',
            'docs/modules/activity.md',
        ] as $documentationPath) {
            $this->assertFileExists(base_path($documentationPath));
        }

        $this->assertDirectoryExists(base_path('docs/decisions'));
        $this->assertDirectoryExists(base_path('docs/modules'));
        $this->assertFileExists(base_path('docs/decisions/project-structure-migration-todo.md'));

        $standard = file_get_contents(base_path('docs/architecture.md'));
        $tracker = file_get_contents(base_path('docs/decisions/project-structure-migration-todo.md'));

        $this->assertStringContainsString('frontend/landing-page', $standard);
        $this->assertStringContainsString('frontend/home', $standard);
        $this->assertStringContainsString('Guard Rule Untuk File Baru', $standard);
        $this->assertStringContainsString('Table Display Standard', $standard);
        $this->assertStringContainsString('multi-display', $standard);
        $this->assertStringContainsString('min-width: 0', $standard);
        $this->assertStringContainsString('resources/frontend/scss/components/frontend-components.scss', $standard);
        $this->assertStringContainsString('Transport Domain Inventory', $tracker);
        $this->assertStringContainsString('Next Execution Plan', $tracker);
    }

    public function test_frontend_and_backend_target_directories_are_seeded(): void
    {
        $requiredPaths = [
            resource_path('views/frontend/landing-page/transports/.gitkeep'),
            resource_path('views/frontend/home/orders/.gitkeep'),
            resource_path('views/frontend/home/profile/.gitkeep'),
            resource_path('views/frontend/shared/.gitkeep'),
            resource_path('frontend/js/landing-page/transports/.gitkeep'),
            resource_path('frontend/js/home/orders/.gitkeep'),
            resource_path('frontend/js/home/profile/.gitkeep'),
            resource_path('frontend/js/shared/.gitkeep'),
            resource_path('frontend/scss/landing-page/transports/.gitkeep'),
            resource_path('frontend/scss/home/orders/.gitkeep'),
            resource_path('frontend/scss/home/profile/.gitkeep'),
            resource_path('frontend/scss/shared/.gitkeep'),
            resource_path('views/backend/operations/.gitkeep'),
            resource_path('views/backend/sales/.gitkeep'),
            resource_path('backend/js/admin/.gitkeep'),
            resource_path('backend/js/operations/.gitkeep'),
            resource_path('backend/js/sales/.gitkeep'),
            resource_path('backend/scss/admin/.gitkeep'),
            resource_path('backend/scss/operations/.gitkeep'),
            resource_path('backend/scss/sales/.gitkeep'),
        ];

        foreach ($requiredPaths as $path) {
            $this->assertFileExists($path);
        }
    }

    public function test_frontend_table_display_standard_is_registered(): void
    {
        $frontendScss = file_get_contents(resource_path('frontend/scss/components/frontend-components.scss'));

        $this->assertStringContainsString('.frontend-table-shell', $frontendScss);
        $this->assertStringContainsString('.frontend-page-shell [class*="table-wrap"]', $frontendScss);
        $this->assertStringContainsString('overflow-x: auto;', $frontendScss);
        $this->assertStringContainsString('scrollbar-gutter: stable;', $frontendScss);
        $this->assertStringContainsString('touch-action: pan-x;', $frontendScss);
        $this->assertStringContainsString('min-width: 720px;', $frontendScss);
        $this->assertStringContainsString('overflow-wrap: anywhere;', $frontendScss);
        $this->assertStringContainsString('white-space: nowrap;', $frontendScss);
    }

    public function test_backend_ui_standard_and_sidebar_theme_are_scoped_to_backend_assets(): void
    {
        $standard = file_get_contents(base_path('docs/decisions/backend-ui-standards.md'));
        $layout = file_get_contents(resource_path('views/layouts/head.blade.php'));
        $sidebar = file_get_contents(resource_path('views/backend/partials/left-navbar.blade.php'));
        $backendScss = file_get_contents(resource_path('backend/scss/app.scss'));
        $themeScss = file_get_contents(resource_path('backend/scss/components/_backend-theme.scss'));

        $this->assertFileExists(base_path('docs/decisions/backend-ui-standards.md'));
        $this->assertFileExists(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $this->assertFileExists(resource_path('backend/scss/components/_backend-theme.scss'));
        $this->assertFileExists(resource_path('backend/scss/components/_backend-hero.scss'));
        $this->assertFileExists(resource_path('backend/scss/components/_backend-kpi.scss'));
        $this->assertFileExists(resource_path('backend/scss/components/_backend-panel.scss'));
        $this->assertFileExists(resource_path('backend/scss/components/_backend-filter.scss'));
        $this->assertFileExists(resource_path('backend/scss/components/_backend-actions.scss'));
        $this->assertFileExists(resource_path('backend/scss/components/_backend-status.scss'));
        $this->assertFileExists(resource_path('backend/scss/components/_backend-alert.scss'));
        $this->assertFileExists(resource_path('backend/scss/components/_backend-list.scss'));
        $this->assertFileExists(resource_path('backend/scss/components/_backend-empty-state.scss'));
        $this->assertFileExists(resource_path('backend/scss/components/_backend-modal.scss'));
        $this->assertFileExists(resource_path('views/components/backend/page-hero.blade.php'));
        $this->assertFileExists(resource_path('views/components/backend/breadcrumb-toolbar.blade.php'));
        $this->assertFileExists(resource_path('views/components/backend/detail-layout.blade.php'));
        $this->assertFileExists(resource_path('backend/scss/components/_backend-detail-layout.scss'));
        $this->assertFileExists(resource_path('backend/scss/components/_backend-breadcrumb.scss'));
        $this->assertFileExists(resource_path('backend/scss/components/_backend-sidebar.scss'));
        $this->assertStringContainsString('berlaku hanya untuk area backend/internal staff', $standard);
        $this->assertStringContainsString('backend-page-hero', $standard);
        $this->assertStringContainsString('setiap aksi utama di dalam hero', $standard);
        $this->assertStringContainsString('Shared Style Governance', $standard);
        $this->assertStringContainsString('KPI Standard', $standard);
        $this->assertStringContainsString('backend-kpi-card', $standard);
        $this->assertStringContainsString('Panel and Section Header Standard', $standard);
        $this->assertStringContainsString('backend-panel', $standard);
        $this->assertStringContainsString('backend-section-header', $standard);
        $this->assertStringContainsString('Toolbar Filter Standard', $standard);
        $this->assertStringContainsString('backend-toolbar-filter', $standard);
        $this->assertStringContainsString('Table Action Button Standard', $standard);
        $this->assertStringContainsString('backend-icon-action--view', $standard);
        $this->assertStringContainsString('backend-icon-action--edit', $standard);
        $this->assertStringContainsString('backend-icon-action--delete', $standard);
        $this->assertStringContainsString('Status Badge Standard', $standard);
        $this->assertStringContainsString('backend-status-badge', $standard);
        $this->assertStringContainsString('Alert Standard', $standard);
        $this->assertStringContainsString('backend-alert', $standard);
        $this->assertStringContainsString('List and Empty State Standard', $standard);
        $this->assertStringContainsString('backend-list-item', $standard);
        $this->assertStringContainsString('backend-empty-state', $standard);
        $this->assertStringContainsString('Modal Standard', $standard);
        $this->assertStringContainsString('backend-modal__header', $standard);
        $this->assertStringContainsString('Detail Layout and Context Side Panel Standard', $standard);
        $this->assertStringContainsString('x-backend.detail-layout', $standard);
        $this->assertStringContainsString('backend-detail-side', $standard);
        $this->assertStringContainsString('New Backend Page Checklist', $standard);
        $this->assertStringContainsString('Backend UI PR Review Checklist', $standard);
        $this->assertStringContainsString('View backend baru harus berada di namespace backend/domain yang sesuai', $standard);
        $this->assertStringContainsString('Tidak ada visual primitive baru di SCSS halaman', $standard);
        $this->assertStringContainsString('Roadmap `docs/decisions/backend-ui-standardization-roadmap.md` diperbarui sesuai progress', $standard);
        $this->assertStringContainsString('Breadcrumb Standard', $standard);
        $this->assertStringContainsString('x-backend.breadcrumb-toolbar', $standard);
        $this->assertStringContainsString('backend-page-toolbar', $standard);
        $this->assertStringContainsString('backend-breadcrumb-toolbar', $standard);
        $this->assertStringContainsString("route('admin.panel-main.view')", $standard);
        $this->assertStringContainsString('Button Standard', $standard);
        $this->assertStringContainsString('Cancel', $standard);
        $this->assertStringContainsString('--backend-button-hover-transform', $standard);
        $this->assertStringContainsString('Link Standard', $standard);
        $this->assertStringContainsString('Semua link backend wajib tanpa underline', $standard);
        $this->assertStringContainsString('Form Label Standard', $standard);
        $this->assertStringContainsString('--backend-required', $standard);
        $this->assertStringContainsString('Checkbox Standard', $standard);
        $this->assertStringContainsString('Table Display Standard', $standard);
        $this->assertStringContainsString('multi-display', $standard);
        $this->assertStringContainsString('tidak boleh bergantung pada horizontal scroll', $standard);
        $this->assertStringContainsString("mix('build/backend/css/app.css')", $layout);
        $this->assertStringContainsString("backend.partials.left-navbar", $layout);
        $this->assertStringContainsString('backend-sidebar', $sidebar);
        $this->assertStringContainsString('backend-sidebar__profile', $sidebar);
        $this->assertStringContainsString('backend-sidebar__section-label', $sidebar);
        $this->assertStringContainsString("\$serviceItem['public_route']", $sidebar);
        $this->assertStringContainsString("\$serviceItem['admin_route']", $sidebar);
        $this->assertStringNotContainsString('route(\'view.\'.$menuitem->nicname)', $sidebar);
        $this->assertStringContainsString("@import 'components/backend-theme';", $backendScss);
        $this->assertStringContainsString("@import 'components/backend-hero';", $backendScss);
        $this->assertStringContainsString("@import 'components/backend-breadcrumb';", $backendScss);
        $this->assertStringContainsString("@import 'components/backend-sidebar';", $backendScss);
        $this->assertStringContainsString("@import 'components/backend-kpi';", $backendScss);
        $this->assertStringContainsString("@import 'components/backend-panel';", $backendScss);
        $this->assertStringContainsString("@import 'components/backend-detail-layout';", $backendScss);
        $this->assertStringContainsString("@import 'components/backend-filter';", $backendScss);
        $this->assertStringContainsString("@import 'components/backend-actions';", $backendScss);
        $this->assertStringContainsString("@import 'components/backend-status';", $backendScss);
        $this->assertStringContainsString("@import 'components/backend-alert';", $backendScss);
        $this->assertStringContainsString("@import 'components/backend-list';", $backendScss);
        $this->assertStringContainsString("@import 'components/backend-empty-state';", $backendScss);
        $this->assertStringContainsString("@import 'components/backend-modal';", $backendScss);

        $heroScss = file_get_contents(resource_path('backend/scss/components/_backend-hero.scss'));
        $kpiScss = file_get_contents(resource_path('backend/scss/components/_backend-kpi.scss'));
        $panelScss = file_get_contents(resource_path('backend/scss/components/_backend-panel.scss'));
        $filterScss = file_get_contents(resource_path('backend/scss/components/_backend-filter.scss'));
        $actionsScss = file_get_contents(resource_path('backend/scss/components/_backend-actions.scss'));
        $statusScss = file_get_contents(resource_path('backend/scss/components/_backend-status.scss'));
        $alertScss = file_get_contents(resource_path('backend/scss/components/_backend-alert.scss'));
        $listScss = file_get_contents(resource_path('backend/scss/components/_backend-list.scss'));
        $emptyStateScss = file_get_contents(resource_path('backend/scss/components/_backend-empty-state.scss'));
        $modalScss = file_get_contents(resource_path('backend/scss/components/_backend-modal.scss'));
        $detailLayoutScss = file_get_contents(resource_path('backend/scss/components/_backend-detail-layout.scss'));
        $this->assertStringContainsString('.backend-page-hero .backend-page-primary-action', $heroScss);
        $this->assertStringContainsString('.backend-page-hero .backend-page-primary-action:focus-visible', $heroScss);
        $this->assertStringContainsString('var(--backend-button-focus-ring), var(--backend-button-hover-shadow)', $heroScss);
        $this->assertStringNotContainsString('body.sidebar-light .main-container .page-header', $heroScss);
        $this->assertStringContainsString('min-height: 142px;', $heroScss);
        $this->assertStringContainsString('min-width: 160px;', $heroScss);
        $this->assertStringContainsString('.backend-kpi-grid--6', $kpiScss);
        $this->assertStringContainsString('.backend-kpi-card__icon', $kpiScss);
        $this->assertStringContainsString('.backend-panel', $panelScss);
        $this->assertStringContainsString('.backend-section-header', $panelScss);
        $this->assertStringContainsString('.backend-section-header__label', $panelScss);
        $this->assertStringContainsString('.backend-toolbar-filter', $filterScss);
        $this->assertStringContainsString('.backend-toolbar-filter__label', $filterScss);
        $this->assertStringContainsString('.backend-toolbar-filter__control', $filterScss);
        $this->assertStringContainsString('.backend-filter-panel', $filterScss);
        $this->assertStringContainsString('grid-template-columns: repeat(auto-fit, minmax(180px, 260px));', $filterScss);
        $this->assertStringContainsString('justify-content: start;', $filterScss);
        $this->assertStringContainsString('max-width: 260px;', $filterScss);
        $this->assertStringContainsString('grid-template-columns: 1fr;', $filterScss);
        $this->assertStringContainsString('body.sidebar-light .backend-filter-panel--flush', $filterScss);
        $this->assertStringContainsString('min-height: 34px;', $filterScss);
        $this->assertStringContainsString('font-size: 10px;', $filterScss);
        $this->assertStringContainsString('.backend-toolbar-action', $filterScss);
        $this->assertStringContainsString('.backend-icon-action--view', $actionsScss);
        $this->assertStringContainsString('.backend-icon-action--edit', $actionsScss);
        $this->assertStringContainsString('.backend-icon-action--delete', $actionsScss);
        $this->assertStringContainsString('body.sidebar-light .backend-icon-action.is-danger', $actionsScss);
        $this->assertStringContainsString('background: #e0f2fe;', $actionsScss);
        $this->assertStringContainsString('background: #fef3c7;', $actionsScss);
        $this->assertStringContainsString('background: #fee2e2;', $actionsScss);
        $this->assertStringContainsString('body.sidebar-light .backend-detail-layout', $detailLayoutScss);
        $this->assertStringContainsString('grid-template-columns: minmax(0, 7fr) minmax(260px, 3fr);', $detailLayoutScss);
        $this->assertStringContainsString('Main 70% | Sidebar 30%', file_get_contents(base_path('docs/decisions/backend-page-layout-standard.md')));
        $this->assertStringContainsString('body.sidebar-light .backend-detail-side', $detailLayoutScss);
        $this->assertStringContainsString('position: sticky;', $detailLayoutScss);
        $this->assertStringContainsString('body.sidebar-light .backend-detail-side-card', $detailLayoutScss);
        $this->assertStringContainsString('body.sidebar-light .backend-detail-side-card__body', $detailLayoutScss);
        $this->assertStringContainsString('body.sidebar-light .backend-detail-side-list', $detailLayoutScss);
        $this->assertStringContainsString('body.sidebar-light .backend-detail-side-list > div', $detailLayoutScss);
        $this->assertStringContainsString('body.sidebar-light .backend-detail-side-list dt', $detailLayoutScss);
        $this->assertStringContainsString('body.sidebar-light .backend-detail-side-list dd', $detailLayoutScss);
        $this->assertStringContainsString('body.sidebar-light .backend-detail-side-actions .backend-button', $detailLayoutScss);
        $this->assertStringContainsString('must not add another `<aside class="backend-detail-side">`', file_get_contents(base_path('docs/decisions/backend-page-layout-standard.md')));

        foreach ([
            resource_path('backend/scss/operations/hotels/_index.scss'),
            resource_path('backend/scss/operations/hotels/_detail.scss'),
            resource_path('backend/scss/operations/guides/_index.scss'),
            resource_path('backend/scss/operations/drivers/_index.scss'),
        ] as $actionDomainScss) {
            $contents = file_get_contents($actionDomainScss);
            $this->assertStringNotContainsString('.backend-icon-action.is-danger', $contents, $actionDomainScss);
            $this->assertStringNotContainsString('backend-danger-soft', $contents, $actionDomainScss);
        }

        foreach ([
            resource_path('backend/scss/operations/transports/_detail.scss'),
            resource_path('backend/scss/operations/tours/_detail.scss'),
            resource_path('backend/scss/operations/hotels/_detail.scss'),
            resource_path('backend/scss/operations/hotels/_index.scss'),
            resource_path('backend/scss/operations/guides/_index.scss'),
            resource_path('backend/scss/operations/drivers/_index.scss'),
        ] as $filterDomainScss) {
            $contents = file_get_contents($filterDomainScss);
            $this->assertStringNotContainsString('filter {' . PHP_EOL . '  display: grid;', $contents, $filterDomainScss);
            $this->assertStringNotContainsString('filter {' . PHP_EOL . '  border-radius:', $contents, $filterDomainScss);
            $this->assertStringNotContainsString('filter {' . PHP_EOL . '  box-shadow:', $contents, $filterDomainScss);
        }
        $this->assertStringContainsString('.backend-status-badge', $statusScss);
        $this->assertStringContainsString('.backend-status-badge--connected', $statusScss);
        $this->assertStringContainsString('.backend-alert', $alertScss);
        $this->assertStringContainsString('.backend-alert--warning', $alertScss);
        $this->assertStringContainsString('.backend-list', $listScss);
        $this->assertStringContainsString('.backend-list-item__meta', $listScss);
        $this->assertStringContainsString('.backend-empty-state', $emptyStateScss);
        $this->assertStringContainsString('.backend-empty-state--compact', $emptyStateScss);
        $this->assertStringContainsString('.backend-modal__header', $modalScss);
        $this->assertStringContainsString('.backend-modal__body', $modalScss);
        $this->assertStringContainsString('.backend-modal__footer', $modalScss);

        $internalViews = [
            resource_path('views/admin/agents/index.blade.php'),
            resource_path('views/admin/agents/show.blade.php'),
            resource_path('views/admin/notifications/index.blade.php'),
            resource_path('views/admin/services.blade.php'),
            resource_path('views/backend/admin/users/index.blade.php'),
            resource_path('views/backend/operations/hotels/forms/add-promo.blade.php'),
            resource_path('views/backend/reports/downloads/index.blade.php'),
        ];

        foreach ($internalViews as $internalView) {
            $internalViewContents = file_get_contents($internalView);
            if (preg_match("/@include\\('([^']+)'\\)/", $internalViewContents, $matches)) {
                $includedPath = resource_path('views/' . str_replace('.', '/', $matches[1]) . '.blade.php');
                $internalViewContents = file_get_contents($includedPath);
            }

            $this->assertStringContainsString('<x-backend.page-hero', $internalViewContents, $internalView);
        }
        $this->assertStringContainsString('--backend-danger: #dc2626;', $themeScss);
        $this->assertStringContainsString('--backend-required: #d90606;', $themeScss);
        $this->assertStringContainsString('--backend-button-hover-transform: translateY(-2px);', $themeScss);
        $this->assertStringContainsString('--backend-button-hover-shadow:', $themeScss);
        $this->assertStringContainsString('--backend-button-focus-ring:', $themeScss);
        $this->assertStringContainsString('body.sidebar-light a,', $themeScss);
        $this->assertStringContainsString('body.sidebar-light a.text-decoration-underline', $themeScss);
        $this->assertStringContainsString('text-decoration: none !important;', $themeScss);
        $this->assertStringContainsString('text-decoration-line: none !important;', $themeScss);
        $this->assertStringContainsString('body.sidebar-light button:not(:disabled):not(.disabled):hover', $themeScss);
        $this->assertStringContainsString('body.sidebar-light .main-container label:not(.user-manager-check-field)', $themeScss);
        $this->assertStringContainsString('color: var(--backend-muted-link) !important;', $themeScss);
        $this->assertStringContainsString('body.sidebar-light .main-container label b', $themeScss);
        $this->assertStringContainsString('body.sidebar-light .main-container input[type="checkbox"]', $themeScss);
        $this->assertStringContainsString('appearance: none;', $themeScss);
        $this->assertStringContainsString('background-position: 21px 50%;', $themeScss);
        $this->assertStringContainsString('.custom-control.custom-checkbox', $themeScss);
        $this->assertStringContainsString('body.sidebar-light .backend-table-wrap', $themeScss);
        $this->assertStringContainsString('body.sidebar-light .table-container', $themeScss);
        $this->assertStringContainsString('overflow-x: auto;', $themeScss);
        $this->assertStringContainsString('scrollbar-gutter: stable;', $themeScss);
        $this->assertStringContainsString('touch-action: pan-x;', $themeScss);
        $this->assertStringContainsString('min-width: 760px;', $themeScss);
        $this->assertStringContainsString('overflow-wrap: anywhere;', $themeScss);
        $this->assertStringContainsString('body.sidebar-light .modal button[data-dismiss="modal"]:not(.close)', $themeScss);
        $this->assertStringContainsString('body.sidebar-light .modal button.close[data-dismiss="modal"]', $themeScss);

        $breadcrumbScss = file_get_contents(resource_path('backend/scss/components/_backend-breadcrumb.scss'));
        $breadcrumbComponent = file_get_contents(resource_path('views/components/backend/breadcrumb-toolbar.blade.php'));
        $this->assertStringContainsString('body.sidebar-light .backend-page-toolbar', $breadcrumbScss);
        $this->assertStringContainsString('body.sidebar-light .backend-breadcrumb-toolbar', $breadcrumbScss);
        $this->assertStringContainsString('body.sidebar-light .backend-breadcrumb-nav', $breadcrumbScss);
        $this->assertStringContainsString('text-overflow: ellipsis', $breadcrumbScss);
        $this->assertStringContainsString('flex-direction: column;', $breadcrumbScss);
        $this->assertStringContainsString('body.sidebar-light .breadcrumb-item a', $breadcrumbScss);
        $this->assertStringContainsString('text-decoration: none', $breadcrumbScss);
        $this->assertStringContainsString('var(--backend-brand-strong)', $breadcrumbScss);
        $this->assertStringContainsString('backend-breadcrumb-toolbar', $breadcrumbComponent);
        $this->assertStringContainsString('backend-breadcrumb-nav', $breadcrumbComponent);
        $this->assertStringContainsString('backend-breadcrumb', $breadcrumbComponent);
        $this->assertStringContainsString('@isset($actions)', $breadcrumbComponent);

        $backendScssFiles = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('backend/scss'))
        );

        foreach ($backendScssFiles as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'scss') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            $this->assertStringNotContainsString('text-decoration: underline', $contents, $file->getPathname());
            $this->assertStringNotContainsString('text-decoration-line: underline', $contents, $file->getPathname());
        }
    }

    public function test_backend_richtext_textarea_standard_is_shared_and_global(): void
    {
        $standard = file_get_contents(base_path('docs/decisions/backend-ui-standards.md'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $richtextRoadmap = file_get_contents(base_path('docs/decisions/backend-richtext-textarea-roadmap.md'));
        $backendJs = file_get_contents(resource_path('backend/js/app.js'));
        $backendScss = file_get_contents(resource_path('backend/scss/app.scss'));
        $richtextScss = file_get_contents(resource_path('backend/scss/components/_backend-richtext.scss'));
        $footJs = file_get_contents(resource_path('views/layouts/footjs.blade.php'));

        $this->assertFileExists(resource_path('backend/scss/components/_backend-richtext.scss'));
        $this->assertFileExists(base_path('docs/decisions/backend-richtext-textarea-roadmap.md'));
        $this->assertStringContainsString('Rich Text Area Standard', $standard);
        $this->assertStringContainsString('initBackendRichText', $standard);
        $this->assertStringContainsString('data-backend-richtext="true"', $standard);
        $this->assertStringContainsString('data-backend-richtext="false"', $standard);
        $this->assertStringContainsString('Rich text textarea shared tersedia', $roadmap);
        $this->assertStringContainsString('Guardrail rich text backend', $roadmap);
        $this->assertStringContainsString('Gunakan `docs/decisions/backend-richtext-textarea-roadmap.md`', $roadmap);
        $this->assertStringContainsString('Phase RT-1 - Shared Foundation', $richtextRoadmap);
        $this->assertStringContainsString('resources/views/backend` memiliki 167 textarea', $richtextRoadmap);
        $this->assertStringContainsString('Phase RT-6 - Final Acceptance', $richtextRoadmap);
        $this->assertStringContainsString('const RICHTEXT_SELECTOR', $backendJs);
        $this->assertStringContainsString('body.sidebar-light .main-container textarea:not([data-backend-richtext="false"])', $backendJs);
        $this->assertStringContainsString('body.sidebar-light .modal textarea:not([data-backend-richtext="false"])', $backendJs);
        $this->assertStringContainsString('textarea.textarea_editor', $backendJs);
        $this->assertStringNotContainsString("import '../../js/app';", $backendJs);
        $this->assertStringNotContainsString("require('./bootstrap')", $backendJs);
        $this->assertStringNotContainsString("require('jquery')", $backendJs);
        $this->assertStringContainsString('window.initBackendRichText = initBackendRichText;', $backendJs);
        $this->assertStringContainsString('window.setBackendRichTextValue = setBackendRichTextValue;', $backendJs);
        $this->assertStringContainsString("document.readyState === 'loading'", $backendJs);
        $this->assertStringContainsString('data(\'backend-richtext-ready\', true)', $backendJs);
        $this->assertStringContainsString('shown.bs.modal', $backendJs);
        $this->assertStringContainsString('.summernote({', $backendJs);
        $this->assertStringContainsString("@import 'components/backend-richtext';", $backendScss);
        $this->assertStringContainsString('body.sidebar-light .note-editor.note-frame', $richtextScss);
        $this->assertStringContainsString('body.sidebar-light .note-editor .note-toolbar', $richtextScss);
        $this->assertStringContainsString('overflow: visible;', $richtextScss);
        $this->assertStringContainsString('body.sidebar-light .note-editor .note-dropdown-menu', $richtextScss);
        $this->assertStringContainsString('body.sidebar-light .note-editor .dropdown-menu', $richtextScss);
        $this->assertStringContainsString('body.sidebar-light .note-tooltip', $richtextScss);
        $this->assertStringContainsString('z-index: 1085;', $richtextScss);
        $this->assertStringContainsString('z-index: 1090;', $richtextScss);
        $this->assertStringContainsString("mix('build/backend/js/app.js')", $footJs);
        $this->assertStringNotContainsString("$('.textarea_editor').summernote", $footJs);
    }

    public function test_backend_form_standard_is_shared_and_global(): void
    {
        $standard = file_get_contents(base_path('docs/decisions/backend-ui-standards.md'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $formRoadmap = file_get_contents(base_path('docs/decisions/backend-form-standardization-roadmap.md'));
        $backendScss = file_get_contents(resource_path('backend/scss/app.scss'));
        $formScss = file_get_contents(resource_path('backend/scss/components/_backend-form.scss'));

        $this->assertFileExists(resource_path('backend/scss/components/_backend-form.scss'));
        $this->assertFileExists(base_path('docs/decisions/backend-form-standardization-roadmap.md'));
        $this->assertStringContainsString("@import 'components/backend-form';", $backendScss);
        $this->assertStringContainsString('Form Control Standard', $standard);
        $this->assertStringContainsString('resources/backend/scss/components/_backend-form.scss', $standard);
        $this->assertStringContainsString('backend-form-control', $standard);
        $this->assertStringContainsString('backend-button-primary', $standard);
        $this->assertStringContainsString('backend-button-danger', $standard);
        $this->assertStringContainsString('docs/decisions/backend-form-standardization-roadmap.md', $roadmap);
        $this->assertStringContainsString('Shared form style tersedia', $roadmap);
        $this->assertStringContainsString('Phase BF-1 - Shared Foundation', $formRoadmap);
        $this->assertStringContainsString('Phase BF-5 - Final Acceptance', $formRoadmap);

        foreach ([
            '--backend-form-height',
            '--backend-form-height-compact',
            '--backend-form-radius',
            '--backend-form-bg',
            '--backend-form-border',
            '--backend-form-focus-ring',
            '--backend-form-danger-ring',
            '.backend-form-grid',
            '.backend-form-field',
            '.backend-form-control',
            '.backend-form-actions',
            '.backend-button-primary',
            '.backend-button-secondary',
            '.backend-button-danger',
            '.form-control',
            '.custom-select',
            '.form-group',
            '.invalid-feedback',
            '.btn-primary',
            '.btn-success',
            '.btn-danger',
            'input[type="checkbox"]',
            'input[type="radio"]',
            ':disabled',
            ':focus-visible',
            ':active',
        ] as $expectedRule) {
            $this->assertStringContainsString($expectedRule, $formScss);
        }

        $this->assertStringNotContainsString('body:not(.sidebar-light)', $formScss);
    }

    public function test_backend_table_action_columns_are_right_aligned_by_shared_standard(): void
    {
        $standard = file_get_contents(base_path('docs/decisions/backend-ui-standards.md'));
        $pageLayoutStandard = file_get_contents(base_path('docs/decisions/backend-page-layout-standard.md'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $actionsScss = file_get_contents(resource_path('backend/scss/components/_backend-actions.scss'));

        $this->assertStringContainsString('td[data-label="Action"]', $actionsScss);
        $this->assertStringContainsString('td[data-label="Actions"]', $actionsScss);
        $this->assertStringContainsString('th.backend-table-action-column', $actionsScss);
        $this->assertStringContainsString('text-align: right;', $actionsScss);
        $this->assertStringContainsString('margin-left: auto;', $actionsScss);
        $this->assertStringContainsString('justify-content: flex-end;', $actionsScss);
        $this->assertStringContainsString('Semua cell action pada `backend-table` wajib memakai `data-label="Action"`', $standard);
        $this->assertStringContainsString('backend-table-action-column', $standard);
        $this->assertStringContainsString('Table Action Alignment', $pageLayoutStandard);
        $this->assertStringContainsString('Kolom action pada shared `backend-table` distandarkan rata kanan', $roadmap);
    }

    public function test_hotel_room_status_toggle_is_available_on_backend_detail(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $roomController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelRoomAdminController.php'));
        $roomsPartial = file_get_contents(resource_path('views/backend/operations/hotels/partials/rooms.blade.php'));
        $roomModal = file_get_contents(resource_path('views/backend/operations/hotels/modals/room-preview.blade.php'));
        $backendAppJs = file_get_contents(resource_path('backend/js/app.js'));
        $accommodationDocs = file_get_contents(base_path('docs/modules/accommodation.md'));

        $this->assertStringContainsString("Route::patch('/hotel-rooms/{room}/status'", $routes);
        $this->assertStringContainsString("name('hotels.room.status.update')", $routes);
        $this->assertStringContainsString('public function updateStatus(Request $request, HotelRoom $room, HotelAuditService $audit): JsonResponse', $roomController);
        $this->assertStringContainsString("'status' => ['required', Rule::in(['Active', 'Draft'])]", $roomController);
        $this->assertStringContainsString("\$audit->userLog(", $roomController);
        $this->assertStringContainsString("'Update Room Status'", $roomController);
        $this->assertStringContainsString("'next_status' => \$room->status === 'Active' ? 'Draft' : 'Active'", $roomController);
        $this->assertStringContainsString("route('admin.hotels.room.status.update'", $roomsPartial);
        $this->assertStringContainsString('data-backend-status-toggle', $roomsPartial);
        $this->assertStringContainsString('data-backend-status-badge-target="#hotelRoomStatusBadge{{ $room->id }}, #hotelRoomModalStatusBadge{{ $room->id }}"', $roomsPartial);
        $this->assertStringContainsString('data-backend-status-toggle-label', $roomsPartial);
        $this->assertStringContainsString('id="hotelRoomModalStatusBadge{{ $room->id }}"', $roomModal);
        $this->assertStringContainsString('document.querySelectorAll(badgeTarget).forEach', $backendAppJs);
        $this->assertStringContainsString('data-toggle="modal" data-target="#hotelRoomDetail', $roomsPartial);
        $this->assertLessThan(
            strpos($roomsPartial, 'data-toggle="modal" data-target="#hotelRoomDetail'),
            strpos($roomsPartial, 'data-backend-status-toggle')
        );
        $this->assertStringContainsString('Backend Hotel detail menampilkan toggle status manual pada setiap Room', $accommodationDocs);
    }

    public function test_backend_create_edit_detail_layout_standard_and_activity_forms_reference_are_enforced(): void
    {
        $agents = file_get_contents(base_path('AGENTS.md'));
        $docsIndex = file_get_contents(base_path('docs/README.md'));
        $standard = file_get_contents(base_path('docs/decisions/backend-page-layout-standard.md'));
        $backendFormScss = file_get_contents(resource_path('backend/scss/components/_backend-form.scss'));
        $detailLayoutScss = file_get_contents(resource_path('backend/scss/components/_backend-detail-layout.scss'));
        $activityCreate = file_get_contents(resource_path('views/backend/operations/activities/forms/create.blade.php'));
        $activityEdit = file_get_contents(resource_path('views/backend/operations/activities/forms/edit.blade.php'));
        $activityDetail = file_get_contents(resource_path('views/backend/operations/activities/detail.blade.php'));
        $activityFormsJs = file_get_contents(resource_path('backend/js/operations/activities/forms.js'));
        $activityDetailViewModel = file_get_contents(app_path('ViewModels/Activities/ActivityDetailViewModel.php'));

        $this->assertFileExists(base_path('docs/decisions/backend-page-layout-standard.md'));
        $this->assertStringContainsString('docs/decisions/backend-page-layout-standard.md', $agents);
        $this->assertStringContainsString('decisions/backend-page-layout-standard.md', $docsIndex);

        foreach ([
            'Mandatory Compliance',
            'use canonical two-column layout',
            'provide a right sidebar',
            'use semantic main sections',
            'use horizontal translation groups',
            'Create Sidebar',
            'Edit Sidebar',
            'Detail Sidebar',
            'Do not duplicate service/resource fields from the main column in the sidebar.',
            'The standardized backend Activity Create, Edit, Detail, and Gallery pages are',
            'Future backend',
            'Create/Edit/Detail standardization work should match the Activity page',
            'Page hero',
            'Breadcrumb/status toolbar',
            'Two-column detail layout',
            'Main semantic panels',
            'Right admin sidebar',
            'Bottom form actions when the page mutates data',
            'do not create a separate card for every field',
            'Basic Information',
            'Gallery or Cover and Media',
            'Operational Information',
            'Pricing',
            'Content and Translations',
            'Basic Information should place the primary image or cover on the left',
            'Gallery must be its own main-column panel below Basic Information.',
            'Operational Information, Pricing, and Content panels follow Gallery',
            'Rich text/content translation panels must use equal-height display blocks',
            'Place Cover and Media as the first main-column panel',
            'Place price validity fields inside Pricing',
            'Keep lifecycle status controls in a dedicated first sidebar card',
            'Preserve the remaining section order from Detail whenever the fields exist.',
            'Keep editable service fields in the main column.',
            'Keep cover upload/change controls in the main column.',
            'Put form submit controls in the canonical bottom `backend-form-actions`',
            'Monetary Display Standard',
            'must show both USD and IDR',
            'Display order is mandatory:',
            'Activity is the first implementation of this standard.',
            'English',
            'Traditional Chinese',
            'Simplified Chinese',
            'x-backend.detail-layout',
            'backend-form-actions',
            'data-backend-picker="date"',
            'data-backend-money-unit',
        ] as $requiredContract) {
            $this->assertStringContainsString($requiredContract, $standard);
        }

        foreach ([
            '.backend-translation-group',
            '.backend-translation-grid',
            '.backend-translation-field',
            'grid-template-columns: repeat(3, minmax(0, 1fr));',
            'grid-template-columns: repeat(2, minmax(0, 1fr));',
            'grid-template-columns: 1fr;',
        ] as $translationPrimitive) {
            $this->assertStringContainsString($translationPrimitive, $backendFormScss);
        }

        $this->assertStringContainsString('grid-template-columns: minmax(0, 7fr) minmax(260px, 3fr);', $detailLayoutScss);
        $this->assertStringContainsString('grid-template-columns: 1fr;', $detailLayoutScss);

        foreach ([
            '<x-backend.detail-layout class="activity-create-layout">',
            '<x-slot name="side">',
            'backend-detail-side-card activity-create-context-panel',
            'backend-detail-side-list',
            'Basic Information',
            'Operational Information',
            'Pricing Inputs',
            'Cover Image',
            'Content and Translations',
            'data-backend-translation-group',
            'backend-translation-grid',
            'Traditional Chinese',
            'Simplified Chinese',
            'data-backend-picker="date"',
            'data-backend-money-unit="IDR"',
            'data-backend-money-unit="USD"',
            'data-backend-richtext="true"',
            'backend-form-actions activity-form-actions',
        ] as $activityReferencePattern) {
            $this->assertStringContainsString($activityReferencePattern, $activityCreate);
        }

        foreach ([
            '<x-backend.detail-layout class="activity-edit-layout">',
            '<x-slot name="side">',
            'backend-detail-side-card activity-edit-context-panel',
            'backend-detail-side-list',
            'Current Status',
            'Record Metadata',
            'Pricing Diagnostics',
            'Selling Price',
            'sellingPriceIdr()',
            'Basic Information',
            'Operational Information',
            'Pricing Inputs',
            'Cover Image',
            'Content and Translations',
            'data-backend-translation-group',
            'backend-translation-grid',
            'Traditional Chinese',
            'Simplified Chinese',
            'data-backend-picker="date"',
            'data-backend-money-unit="IDR"',
            'data-backend-money-unit="USD"',
            'data-activity-pricing-preview',
            'data-activity-pricing-preview-usd',
            'data-activity-pricing-preview-idr',
            'data-activity-pricing-preview-message',
            'data-backend-richtext="true"',
            'backend-form-actions activity-form-actions',
            'Manage Gallery',
        ] as $activityEditPattern) {
            $this->assertStringContainsString($activityEditPattern, $activityEdit);
        }

        $this->assertStringNotContainsString('Partner Context', $activityEdit);
        $this->assertStringNotContainsString('Pricing Context', $activityEdit);
        $this->assertLessThan(strpos($activityCreate, 'Basic Information'), strpos($activityCreate, 'Cover Image'));
        $this->assertLessThan(strpos($activityEdit, 'Basic Information'), strpos($activityEdit, 'Cover Image'));
        $this->assertLessThan(strpos($activityCreate, 'Creation Guidance'), strpos($activityCreate, 'Initial Status'));
        $this->assertLessThan(strpos($activityEdit, 'name="status"'), strpos($activityEdit, 'Current Status'));
        $this->assertLessThan(strpos($activityEdit, 'Record Metadata'), strpos($activityEdit, 'name="status"'));
        $this->assertLessThan(strpos($activityCreate, 'Valid Until'), strpos($activityCreate, 'Markup'));
        $this->assertLessThan(strpos($activityEdit, 'Valid Until'), strpos($activityEdit, 'Markup'));
        $this->assertStringContainsString('initializePricingPreview', $activityFormsJs);
        $this->assertStringContainsString('data-activity-pricing-preview', $activityFormsJs);
        $this->assertStringContainsString('contractRateInput.addEventListener', $activityFormsJs);
        $this->assertStringContainsString('markupInput.addEventListener', $activityFormsJs);

        foreach ([
            '<x-backend.detail-layout class="activity-detail-layout">',
            '<x-slot name="side">',
            'backend-detail-side-card activity-detail-context-panel',
            'backend-detail-side-list',
            'Current Status',
            'Activity ID',
            'Activity Code',
            'Media Maintenance',
            'Basic Information',
            'Operational Information',
            'Pricing Inputs',
            'Gallery Images',
            'Content and Translations',
            'activityDetail->translationGroups()',
            'data-backend-translation-group',
            'backend-translation-grid',
            'ActivityPricingService',
            'activityDetail->sellingPrice()',
            'activityDetail->sellingPriceIdr()',
            'activityDetail->contractRateUsd()',
            'currencyFormatIdr($activity->contract_rate)',
            'activityDetail->markupIdr()',
            '<span class="backend-section-header__label">Gallery</span>',
            'activity-detail-profile-card',
            'Add / Edit Gallery',
            'Back to Activities',
        ] as $activityDetailPattern) {
            $this->assertStringContainsString($activityDetailPattern, $activityDetail);
        }

        $this->assertStringNotContainsString('Preview Limit', $activityDetail);
        $this->assertStringNotContainsString('Pricing Context', $activityDetail);
        $this->assertStringNotContainsString('activityDetail->contractRateIdr()', $activityDetail);

        $this->assertStringContainsString('Traditional Chinese', $activityDetailViewModel);
        $this->assertStringContainsString('Simplified Chinese', $activityDetailViewModel);

        foreach ([
            'Description Traditional',
            'Description Simplified',
            'Itinerary Traditional',
            'Include Simplified',
            'Cancellation Policy Traditional',
            'Additional Information Simplified',
            'date-picker',
            'onclick',
            'name="author"',
        ] as $legacyPattern) {
            $this->assertStringNotContainsString($legacyPattern, $activityCreate);
            $this->assertStringNotContainsString($legacyPattern, $activityEdit);
            $this->assertStringNotContainsString($legacyPattern, $activityDetail);
        }

        $this->assertStringNotContainsString('name="status"', $activityCreate);
        $this->assertStringContainsString('name="status"', $activityEdit);
        $this->assertStringNotContainsString('name="initial_state"', $activityEdit);
        $this->assertStringNotContainsString('name="page"', $activityEdit);
        $this->assertStringNotContainsString('data-backend-richtext', $activityDetail);
        $this->assertStringNotContainsString('data-backend-picker', $activityDetail);
        $this->assertStringNotContainsString('textarea', $activityDetail);
        $this->assertStringNotContainsString("mix('build/backend/js/operations/activities/index.js')", $activityDetail);
    }

    public function test_backend_monetary_inputs_have_a_shared_currency_unit_contract(): void
    {
        $backendJs = file_get_contents(resource_path('backend/js/app.js'));
        $formScss = file_get_contents(resource_path('backend/scss/components/_backend-form.scss'));
        $layout = file_get_contents(resource_path('views/layouts/head.blade.php'));
        $tourPriceFields = file_get_contents(resource_path('views/backend/operations/tours/partials/price-fields.blade.php'));
        $currencyAdmin = file_get_contents(resource_path('views/backend/developer/currency.blade.php'));
        $mappedFields = [
            'additional_guest_rate' => 'USD',
            'additional_service_price' => 'USD',
            'agent_rate' => 'USD',
            'arrangement_price' => 'USD',
            'basic_price' => 'USD',
            'contract_rate' => 'IDR',
            'contract_rate_idr' => 'IDR',
            'fee' => 'USD',
            'holiday_price' => 'USD',
            'kick_back' => 'USD',
            'markup' => 'USD',
            'price' => 'USD',
            'public_rate' => 'USD',
            'publish_rate' => 'USD',
            'rate' => 'USD',
            'tax' => '%',
            'week_day_price' => 'USD',
        ];

        foreach ($mappedFields as $field => $unit) {
            $this->assertStringContainsString("{$field}: '{$unit}'", $backendJs);
        }

        $this->assertStringContainsString('function initBackendMoneyInputs(', $backendJs);
        $this->assertStringContainsString('window.initBackendMoneyInputs = initBackendMoneyInputs', $backendJs);
        $this->assertStringContainsString('.backend-money-control', $formScss);
        $this->assertStringContainsString('.backend-money-control__unit', $formScss);
        $this->assertStringContainsString('data-backend-money-hint=', $layout);
        $this->assertStringContainsString('data-backend-money-label=', $layout);
        $this->assertStringContainsString('formatBackendMoneyInput', $backendJs);
        $this->assertStringContainsString("unit === 'IDR' ? '.' : ','", $backendJs);
        $this->assertStringContainsString("document.addEventListener('formdata'", $backendJs);
        $this->assertStringContainsString('controls.forEach(restoreBackendMoneyInput)', $backendJs);
        $this->assertStringContainsString('data-backend-money-unit-source="[data-tour-markup-type]"', $tourPriceFields);
        $this->assertStringContainsString('data-backend-money-unit-map="percentage:%|usd:USD|idr:IDR"', $tourPriceFields);
        $this->assertStringContainsString('data-backend-money-unit-source-target', $tourPriceFields);
        $this->assertSame(2, substr_count($currencyAdmin, 'data-backend-money-unit="IDR"'));

        foreach (['en', 'zh', 'zh-CN'] as $locale) {
            $messages = file_get_contents(resource_path("lang/{$locale}/messages.php"));
            $this->assertStringContainsString("'Backend monetary input hint'", $messages);
            $this->assertStringContainsString("'Monetary input unit'", $messages);
        }

        $moneyNamePattern = '/(?:price|rate|cost|amount|markup|tax|discount|fee|charge|kick.?back|deposit|total)/i';
        $views = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views/backend'), \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($views as $view) {
            if (!$view->isFile() || $view->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($view->getPathname());
            preg_match_all('/<input\b[^>]*\bname="([^"]+)"[^>]*>/is', $contents, $inputs, PREG_SET_ORDER);

            foreach ($inputs as $input) {
                if (str_contains(strtolower($input[0]), 'type="hidden"')) {
                    continue;
                }

                $field = preg_replace('/\[[^\]]*\].*$/', '', $input[1]);
                if (!preg_match($moneyNamePattern, $field)) {
                    continue;
                }

                $covered = array_key_exists($field, $mappedFields)
                    || str_contains($input[0], 'data-backend-money-unit');

                $this->assertTrue($covered, "Missing backend monetary unit contract for {$field} in {$view->getPathname()}");
            }
        }
    }

    public function test_product_operation_forms_use_backend_form_standard(): void
    {
        $formDirectories = [
            resource_path('views/backend/operations/hotels/forms'),
            resource_path('views/backend/operations/activities/forms'),
            resource_path('views/backend/operations/tours/forms'),
            resource_path('views/backend/operations/tours/partials'),
            resource_path('views/backend/operations/transports/forms'),
            resource_path('views/backend/operations/transports/partials'),
        ];

        $surface = '';
        $viewCount = 0;

        foreach ($formDirectories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            $views = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));

            foreach ($views as $view) {
                if (!$view->isFile() || $view->getExtension() !== 'php') {
                    continue;
                }

                $viewCount++;
                $contents = file_get_contents($view->getPathname());
                $surface .= $contents;

                foreach ([
                    'class="form-group"',
                    'class="form-control',
                    'class="custom-select',
                    'class="custom-file-input',
                    'alert-form alert-danger',
                    'btn btn-',
                    'btn-primary',
                    'btn-success',
                    'btn-secondary',
                    'btn-danger',
                    'backend-primary-action',
                    'backend-secondary-action',
                    'hotel-form-grid',
                    'hotel-form-field',
                    'hotel-form-actions',
                    'hotel-form-help',
                    'transport-form-grid',
                    'transport-form-actions',
                    'activity-form-actions',
                    'activity-gallery-actions',
                    'tour-form-actions',
                ] as $legacyPattern) {
                    $this->assertStringNotContainsString($legacyPattern, $contents, $view->getPathname());
                }
            }
        }

        $this->assertGreaterThan(15, $viewCount);

        foreach ([
            'backend-form-grid',
            'backend-form-field',
            'backend-form-control',
            'backend-form-actions',
            'backend-button backend-button-primary',
            'backend-button backend-button-secondary',
        ] as $sharedPattern) {
            $this->assertStringContainsString($sharedPattern, $surface);
        }

        foreach ([
            resource_path('backend/scss/operations/hotels/_forms.scss'),
            resource_path('backend/scss/operations/activities/_forms.scss'),
            resource_path('backend/scss/operations/tours/_forms.scss'),
            resource_path('backend/scss/operations/transports/_forms.scss'),
        ] as $domainFormScss) {
            $this->assertFileExists($domainFormScss);

            $contents = file_get_contents($domainFormScss);

            foreach ([
                '.backend-form-control',
                '.backend-form-field',
                '.form-control',
                '.custom-select',
                '.form-group',
                '.custom-file-input',
                'input,',
                'select,',
                'textarea {',
                'textarea.backend-form-control',
                'btn-primary',
                'btn-success',
                'btn-secondary',
                'btn-danger',
            ] as $domainOverride) {
                $this->assertStringNotContainsString($domainOverride, $contents, $domainFormScss);
            }
        }

        $formRoadmap = file_get_contents(base_path('docs/decisions/backend-form-standardization-roadmap.md'));

        foreach ([
            '- [x] Refactor form Hotels agar markup utama memakai `backend-form-*`.',
            '- [x] Refactor form Activities agar markup utama memakai `backend-form-*`.',
            '- [x] Refactor form Tours agar markup utama memakai `backend-form-*`.',
            '- [x] Refactor form Transports agar markup utama memakai `backend-form-*`.',
            '- [x] Pastikan tidak ada SCSS domain product yang mendefinisikan ulang visual dasar form.',
        ] as $checkedItem) {
            $this->assertStringContainsString($checkedItem, $formRoadmap);
        }
    }

    public function test_admin_content_and_user_forms_use_backend_form_standard(): void
    {
        $viewTargets = [
            resource_path('views/backend/admin/company-profile'),
            resource_path('views/backend/admin/footer-manager'),
            resource_path('views/backend/admin/terms'),
            resource_path('views/backend/admin/reviews'),
            resource_path('views/backend/admin/users'),
            resource_path('views/backend/developer/currency.blade.php'),
        ];

        $surface = '';
        $viewCount = 0;

        foreach ($viewTargets as $target) {
            $paths = is_dir($target)
                ? new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($target))
                : [new \SplFileInfo($target)];

            foreach ($paths as $view) {
                if (!$view->isFile() || $view->getExtension() !== 'php') {
                    continue;
                }

                $viewCount++;
                $contents = file_get_contents($view->getPathname());
                $surface .= $contents;

                foreach ([
                    'class="form-group"',
                    'class="form-control',
                    'class="custom-select',
                    'class="custom-file-input',
                    'alert-form alert-danger',
                    'btn btn-',
                    'btn-primary',
                    'btn-success',
                    'btn-secondary',
                    'btn-danger',
                    'backend-primary-action',
                    'backend-secondary-action',
                    'company-profile-primary-action',
                    'footer-manager-primary-action',
                    'footer-manager-ghost-action',
                    'terms-admin-primary-action',
                    'terms-admin-ghost-action',
                    'currency-admin-primary-action',
                    'currency-admin-ghost-action',
                    'currency-admin-secondary-action',
                    'user-manager-primary-action',
                    'backend-danger-action',
                    'tour-reviews-primary-action',
                ] as $legacyPattern) {
                    $this->assertStringNotContainsString($legacyPattern, $contents, $view->getPathname());
                }

                $this->assertDoesNotMatchRegularExpression('/<[^>]+class="[^"]+"[^>]+class="/', $contents, $view->getPathname());
            }
        }

        $this->assertGreaterThan(10, $viewCount);

        foreach ([
            'backend-form-grid',
            'backend-form-control',
            'backend-form-actions',
            'backend-button backend-button-primary',
            'backend-button backend-button-secondary',
        ] as $sharedPattern) {
            $this->assertStringContainsString($sharedPattern, $surface);
        }

        foreach ([
            resource_path('backend/scss/admin/company-profile/_edit.scss'),
            resource_path('backend/scss/admin/footer-manager/_index.scss'),
            resource_path('backend/scss/admin/terms/_index.scss'),
            resource_path('backend/scss/admin/reviews/_index.scss'),
            resource_path('backend/scss/admin/currency/_index.scss'),
            resource_path('backend/scss/admin/users/_manager.scss'),
        ] as $domainScss) {
            $this->assertFileExists($domainScss);

            $contents = file_get_contents($domainScss);

            foreach ([
                '.backend-form-control',
                '.backend-form-field',
                '.form-control',
                '.custom-select',
                '.form-group',
                '.custom-file-input',
                'input,',
                'select,',
                'textarea {',
                'input:focus',
                'select:focus',
                'textarea:focus',
                'btn-primary',
                'btn-success',
                'btn-secondary',
                'btn-danger',
            ] as $domainOverride) {
                $this->assertStringNotContainsString($domainOverride, $contents, $domainScss);
            }
        }

        $formRoadmap = file_get_contents(base_path('docs/decisions/backend-form-standardization-roadmap.md'));

        foreach ([
            '- [x] Refactor Company Profile, Footer Manager, Terms, Reviews, Currency, User Manager agar markup form memakai `backend-form-*`.',
            '- [x] Pastikan modal admin memakai `backend-form-actions` dan `backend-button-*`.',
            '- [x] Pastikan tidak ada button legacy visual yang belum dinormalisasi.',
        ] as $checkedItem) {
            $this->assertStringContainsString($checkedItem, $formRoadmap);
        }
    }

    public function test_remaining_operations_and_legacy_admin_forms_use_backend_form_standard(): void
    {
        $viewTargets = [
            resource_path('views/backend/operations/guides'),
            resource_path('views/backend/operations/drivers'),
            resource_path('views/backend/operations/partners/forms'),
            resource_path('views/backend/operations/weddings/forms'),
            resource_path('views/backend/operations/orders/actions'),
            resource_path('views/backend/operations/reservations/actions'),
            resource_path('views/admin/transportmanagement'),
            resource_path('views/admin/villas'),
            resource_path('views/admin/weddingsadmin.blade.php'),
            resource_path('views/admin/weddingsadmindetail.blade.php'),
            resource_path('views/admin/vendorsadmin.blade.php'),
            resource_path('views/admin/vendorsadmindetail.blade.php'),
            resource_path('views/admin/promotion.blade.php'),
        ];

        $surface = '';
        $viewCount = 0;

        foreach ($viewTargets as $target) {
            if (!file_exists($target)) {
                continue;
            }

            $paths = is_dir($target)
                ? new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($target))
                : [new \SplFileInfo($target)];

            foreach ($paths as $view) {
                if (!$view->isFile() || $view->getExtension() !== 'php') {
                    continue;
                }

                $viewCount++;
                $contents = file_get_contents($view->getPathname());
                $surface .= $contents;

                foreach ([
                    'class="form-group"',
                    'class="form-control',
                    'class="custom-select',
                    'class="custom-file-input',
                    'btn btn',
                    'btn-primary',
                    'btn-success',
                    'btn-secondary',
                    'btn-danger',
                    'backend-primary-action',
                    'backend-secondary-action',
                    'backend-danger-action',
                ] as $legacyPattern) {
                    $this->assertStringNotContainsString($legacyPattern, $contents, $view->getPathname());
                }

                $this->assertDoesNotMatchRegularExpression('/<[^>]+class="[^"]+"[^>]+class="/', $contents, $view->getPathname());
                $this->assertDoesNotMatchRegularExpression('/<input[^>]*type="hidden"[^>]*class="backend-form-control"/', $contents, $view->getPathname());
                $this->assertDoesNotMatchRegularExpression('/<input[^>]*type="checkbox"[^>]*class="backend-form-control"/', $contents, $view->getPathname());
            }
        }

        $this->assertGreaterThan(20, $viewCount);

        foreach ([
            'backend-form-control',
            'backend-form-field',
            'backend-button backend-button-primary',
            'backend-button backend-button-secondary',
            'backend-button backend-button-danger',
        ] as $sharedPattern) {
            $this->assertStringContainsString($sharedPattern, $surface);
        }

        foreach ([
            resource_path('backend/scss/operations/guides/_index.scss'),
            resource_path('backend/scss/operations/drivers/_index.scss'),
            resource_path('backend/scss/operations/orders-admin/_detail.scss'),
            resource_path('backend/scss/operations/transport-management/_index.scss'),
            resource_path('backend/scss/operations/transport-management/_detail.scss'),
        ] as $domainScss) {
            $this->assertFileExists($domainScss);

            $contents = file_get_contents($domainScss);

            foreach ([
                '.backend-form-control',
                '.backend-form-field',
                '.form-control',
                '.custom-select',
                '.form-group',
                '.custom-file-input',
                'input,',
                'select,',
                'textarea {',
                'input:focus',
                'select:focus',
                'textarea:focus',
                'btn-primary',
                'btn-success',
                'btn-secondary',
                'btn-danger',
            ] as $domainOverride) {
                $this->assertStringNotContainsString($domainOverride, $contents, $domainScss);
            }
        }

        $formRoadmap = file_get_contents(base_path('docs/decisions/backend-form-standardization-roadmap.md'));

        foreach ([
            '- [x] Refactor Guides, Drivers, Partners, Weddings, Orders Admin, Reservations, Transport Management, Villas, Vendors, Promotions.',
            '- [x] Audit `resources/views/admin` yang masih aktif agar tetap kompatibel dengan shared form.',
            '- [x] Hapus style form domain yang sudah digantikan oleh shared component.',
        ] as $checkedItem) {
            $this->assertStringContainsString($checkedItem, $formRoadmap);
        }
    }

    public function test_hotel_backend_textareas_explicitly_use_shared_richtext_standard(): void
    {
        $hotelViews = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views/backend/operations/hotels'))
        );

        $textareaCount = 0;

        foreach ($hotelViews as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            preg_match_all('/<textarea\b([^>]*)>/i', $contents, $matches);

            foreach ($matches[1] as $attributes) {
                $textareaCount++;
                $this->assertStringContainsString(
                    'data-backend-richtext="true"',
                    $attributes,
                    $file->getPathname()
                );
                $this->assertStringNotContainsString(
                    'data-backend-richtext="false"',
                    $attributes,
                    $file->getPathname()
                );
            }

            $this->assertStringNotContainsString('.summernote(', $contents, $file->getPathname());
        }

        $hotelJsFiles = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('backend/js/operations/hotels'))
        );

        foreach ($hotelJsFiles as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'js') {
                continue;
            }

            $this->assertStringNotContainsString('.summernote(', file_get_contents($file->getPathname()), $file->getPathname());
        }

        $this->assertSame(51, $textareaCount);
    }

    public function test_activity_backend_textareas_explicitly_use_shared_richtext_standard(): void
    {
        $activityViews = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views/backend/operations/activities'))
        );

        $textareaCount = 0;

        foreach ($activityViews as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            preg_match_all('/<textarea\b([^>]*)>/i', $contents, $matches);

            foreach ($matches[1] as $attributes) {
                $textareaCount++;
                $this->assertStringContainsString(
                    'data-backend-richtext="true"',
                    $attributes,
                    $file->getPathname()
                );
                $this->assertStringNotContainsString(
                    'data-backend-richtext="false"',
                    $attributes,
                    $file->getPathname()
                );
            }

            $this->assertStringNotContainsString('.summernote(', $contents, $file->getPathname());
        }

        $activityJsFiles = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('backend/js/operations/activities'))
        );

        foreach ($activityJsFiles as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'js') {
                continue;
            }

            $this->assertStringNotContainsString('.summernote(', file_get_contents($file->getPathname()), $file->getPathname());
        }

        $this->assertSame(10, $textareaCount);
    }

    public function test_tour_backend_textareas_explicitly_use_shared_richtext_standard(): void
    {
        $tourViews = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views/backend/operations/tours'))
        );

        $textareaCount = 0;

        foreach ($tourViews as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            preg_match_all('/<textarea\b([^>]*)>/i', $contents, $matches);

            foreach ($matches[1] as $attributes) {
                $textareaCount++;
                $this->assertStringContainsString(
                    'data-backend-richtext="true"',
                    $attributes,
                    $file->getPathname()
                );
                $this->assertStringNotContainsString(
                    'data-backend-richtext="false"',
                    $attributes,
                    $file->getPathname()
                );
            }

            $this->assertStringNotContainsString('.summernote(', $contents, $file->getPathname());
        }

        $tourJsFiles = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('backend/js/operations/tours'))
        );

        foreach ($tourJsFiles as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'js') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            $this->assertStringNotContainsString('.summernote(', $contents, $file->getPathname());
        }

        $tourFormsJs = file_get_contents(resource_path('backend/js/operations/tours/forms.js'));

        $this->assertStringContainsString('window.initBackendRichText', $tourFormsJs);
        $this->assertStringContainsString('window.setBackendRichTextValue', $tourFormsJs);
        $this->assertSame(50, $textareaCount);
    }

    public function test_transport_backend_textareas_explicitly_use_shared_richtext_standard(): void
    {
        $transportViews = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views/backend/operations/transports'))
        );

        $textareaCount = 0;

        foreach ($transportViews as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            preg_match_all('/<textarea\b([^>]*)>/i', $contents, $matches);

            foreach ($matches[1] as $attributes) {
                $textareaCount++;
                $this->assertStringContainsString(
                    'data-backend-richtext="true"',
                    $attributes,
                    $file->getPathname()
                );
                $this->assertStringNotContainsString(
                    'data-backend-richtext="false"',
                    $attributes,
                    $file->getPathname()
                );
            }

            $this->assertStringNotContainsString('.summernote(', $contents, $file->getPathname());
        }

        $transportJsFiles = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('backend/js/operations/transports'))
        );

        foreach ($transportJsFiles as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'js') {
                continue;
            }

            $this->assertStringNotContainsString('.summernote(', file_get_contents($file->getPathname()), $file->getPathname());
        }

        $this->assertSame(5, $textareaCount);
    }

    public function test_backend_product_domains_do_not_initialize_richtext_locally(): void
    {
        foreach (['hotels', 'activities', 'tours', 'transports'] as $domain) {
            $viewsDirectory = resource_path("views/backend/operations/{$domain}");
            $jsDirectory = resource_path("backend/js/operations/{$domain}");

            foreach ([$viewsDirectory, $jsDirectory] as $directory) {
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));

                foreach ($files as $file) {
                    if (!$file->isFile() || !in_array($file->getExtension(), ['blade.php', 'php', 'js'], true)) {
                        continue;
                    }

                    $contents = file_get_contents($file->getPathname());

                    $this->assertStringNotContainsString('.summernote(', $contents, $file->getPathname());
                }
            }
        }
    }

    public function test_backend_admin_content_textareas_explicitly_use_shared_richtext_standard(): void
    {
        $domains = [
            'company-profile' => 1,
            'footer-manager' => 3,
            'terms' => 3,
        ];

        foreach ($domains as $domain => $expectedTextareaCount) {
            $viewsDirectory = resource_path("views/backend/admin/{$domain}");
            $jsDirectory = resource_path("backend/js/admin/{$domain}");
            $views = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($viewsDirectory));
            $textareaCount = 0;

            foreach ($views as $file) {
                if (!$file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $contents = file_get_contents($file->getPathname());
                foreach (preg_grep('/<textarea\b/i', preg_split('/\R/', $contents)) as $textareaLine) {
                    $textareaCount++;
                    $this->assertStringContainsString(
                        'data-backend-richtext="true"',
                        $textareaLine,
                        $file->getPathname()
                    );
                    $this->assertStringNotContainsString(
                        'data-backend-richtext="false"',
                        $textareaLine,
                        $file->getPathname()
                    );
                }

                $this->assertStringNotContainsString('.summernote(', $contents, $file->getPathname());
            }

            $scripts = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($jsDirectory));

            foreach ($scripts as $file) {
                if (!$file->isFile() || $file->getExtension() !== 'js') {
                    continue;
                }

                $this->assertStringNotContainsString('.summernote(', file_get_contents($file->getPathname()), $file->getPathname());
            }

            $this->assertSame($expectedTextareaCount, $textareaCount, $domain);
        }
    }

    public function test_backend_remaining_operations_textareas_explicitly_use_shared_richtext_standard(): void
    {
        $domains = [
            'guides' => 1,
            'drivers' => 1,
            'partners' => 11,
            'weddings' => 31,
        ];

        foreach ($domains as $domain => $expectedTextareaCount) {
            $viewsDirectory = resource_path("views/backend/operations/{$domain}");
            $views = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($viewsDirectory));
            $textareaCount = 0;

            foreach ($views as $file) {
                if (!$file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $contents = file_get_contents($file->getPathname());

                foreach (preg_grep('/<textarea\b/i', preg_split('/\R/', $contents)) as $textareaLine) {
                    $textareaCount++;
                    $this->assertStringContainsString(
                        'data-backend-richtext="true"',
                        $textareaLine,
                        $file->getPathname()
                    );
                    $this->assertStringNotContainsString(
                        'data-backend-richtext="false"',
                        $textareaLine,
                        $file->getPathname()
                    );
                }

                $this->assertStringNotContainsString('.summernote(', $contents, $file->getPathname());
            }

            $jsDirectory = resource_path("backend/js/operations/{$domain}");

            if (is_dir($jsDirectory)) {
                $scripts = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($jsDirectory));

                foreach ($scripts as $file) {
                    if (!$file->isFile() || $file->getExtension() !== 'js') {
                        continue;
                    }

                    $this->assertStringNotContainsString('.summernote(', file_get_contents($file->getPathname()), $file->getPathname());
                }
            }

            $this->assertSame($expectedTextareaCount, $textareaCount, $domain);
        }
    }

    public function test_backend_operations_textareas_all_use_shared_richtext_standard(): void
    {
        $operationsViews = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views/backend/operations'))
        );
        $textareaCount = 0;

        foreach ($operationsViews as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());

            foreach (preg_grep('/<textarea\b/i', preg_split('/\R/', $contents)) as $textareaLine) {
                $textareaCount++;
                $this->assertStringContainsString(
                    'data-backend-richtext="true"',
                    $textareaLine,
                    $file->getPathname()
                );
                $this->assertStringNotContainsString(
                    'data-backend-richtext="false"',
                    $textareaLine,
                    $file->getPathname()
                );
            }

            $this->assertStringNotContainsString('.summernote(', $contents, $file->getPathname());
        }

        $operationsJs = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('backend/js/operations'))
        );

        foreach ($operationsJs as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'js') {
                continue;
            }

            $this->assertStringNotContainsString('.summernote(', file_get_contents($file->getPathname()), $file->getPathname());
        }

        $this->assertSame(160, $textareaCount);
    }

    public function test_legacy_admin_textareas_keep_shared_richtext_compatibility(): void
    {
        $legacyAdminViews = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views/admin'))
        );
        $textareaCount = 0;
        $legacyTextareaEditorCount = 0;

        foreach ($legacyAdminViews as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());

            foreach (preg_grep('/<textarea\b/i', preg_split('/\R/', $contents)) as $textareaLine) {
                $textareaCount++;
                $this->assertStringContainsString(
                    'data-backend-richtext="true"',
                    $textareaLine,
                    $file->getPathname()
                );

                if (str_contains($textareaLine, 'textarea_editor')) {
                    $legacyTextareaEditorCount++;
                }
            }

            $this->assertStringNotContainsString('.summernote(', $contents, $file->getPathname());
        }

        $backendJs = file_get_contents(resource_path('backend/js/app.js'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-richtext-textarea-roadmap.md'));

        $this->assertStringContainsString('textarea.textarea_editor', $backendJs);
        $this->assertStringContainsString('Pertahankan kompatibilitas `textarea_editor`', $roadmap);
        $this->assertSame(111, $textareaCount);
        $this->assertSame(99, $legacyTextareaEditorCount);
    }

    public function test_transport_public_views_are_routed_to_landing_page_structure(): void
    {
        $frontEndController = file_get_contents(app_path('Http/Controllers/FrontEndController.php'));
        $homeController = file_get_contents(app_path('Http/Controllers/HomeController.php'));

        $this->assertFileExists(resource_path('views/frontend/landing-page/transports/index.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/transports/detail.blade.php'));
        $this->assertStringContainsString("view('frontend.landing-page.transports.index'", $frontEndController);
        $this->assertStringContainsString("view('frontend.landing-page.transports.index'", $homeController);
        $this->assertStringContainsString("view('frontend.landing-page.transports.detail'", $homeController);
        $this->assertStringNotContainsString("view('home.landing-page.transport'", $frontEndController);
        $this->assertStringNotContainsString("view('home.transports.detail'", $homeController);
    }

    public function test_transport_public_assets_are_sourced_from_landing_page_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/landing-page/transports/index.js'));
        $this->assertFileExists(resource_path('frontend/js/landing-page/transports/detail.js'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/transports/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/transports/_index.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/transports/detail-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/transports/_detail.scss'));
        $this->assertStringContainsString("resources/frontend/js/landing-page/transports/index.js", $mix);
        $this->assertStringContainsString("resources/frontend/js/landing-page/transports/detail.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/transports/index-entry.scss", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/transports/detail-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/transportations-index.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/transport-detail.js", $mix);
    }

    public function test_transport_directory_route_renders_landing_page_view(): void
    {
        $response = $this->get(route('view.transport-services'));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.transports.index');
        $response->assertViewHasAll([
            'transports',
            'types',
            'brands',
            'directoryStats',
            'searchName',
            'searchType',
            'searchBrand',
            'minimumCapacity',
        ]);
    }

    public function test_transport_detail_route_renders_landing_page_view(): void
    {
        $transport = Transports::create([
            'name' => 'Route Test Transport',
            'code' => 'RTT-' . uniqid(),
            'type' => 'Daily Rent',
            'brand' => 'Route Brand',
            'description' => 'Route test transport description.',
            'include' => 'Driver and fuel',
            'additional_info' => 'Route test additional information.',
            'cancellation_policy' => 'Route test cancellation policy.',
            'capacity' => '5',
            'cover' => 'default.webp',
            'status' => 'Active',
            'author_id' => 1,
        ]);

        TransportPrice::create([
            'transports_id' => $transport->id,
            'type' => 'Daily Rent',
            'src' => 'Hotel',
            'dst' => 'Ubud',
            'duration' => 10,
            'contract_rate' => 500000,
            'markup' => 10,
            'extra_time' => 50000,
            'additional_info' => 'Route test price.',
            'author_id' => 1,
        ]);

        $response = $this->get(route('transport.show', $transport->id));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.transports.detail');
        $response->assertViewHasAll([
            'transport',
            'prices',
            'priceGroups',
            'similarTransports',
            'orderNumber',
            'agents',
            'transportOrderNumbersByAgent',
        ]);
    }

    public function test_activity_public_views_are_routed_to_landing_page_structure(): void
    {
        $frontEndController = file_get_contents(app_path('Http/Controllers/FrontEndController.php'));

        $this->assertFileExists(resource_path('views/frontend/landing-page/activities/index.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/activities/detail.blade.php'));
        $this->assertStringContainsString("view('frontend.landing-page.activities.index'", $frontEndController);
        $this->assertStringContainsString("view('frontend.landing-page.activities.detail'", $frontEndController);
        $this->assertStringNotContainsString("view('frontend.activities.index'", $frontEndController);
        $this->assertStringNotContainsString("view('frontend.activities.detail'", $frontEndController);
    }

    public function test_activity_public_assets_are_sourced_from_landing_page_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/landing-page/activities/index.js'));
        $this->assertFileExists(resource_path('frontend/js/landing-page/activities/detail.js'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/activities/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/activities/_index.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/activities/detail-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/activities/_detail.scss'));
        $this->assertStringContainsString("resources/frontend/js/landing-page/activities/index.js", $mix);
        $this->assertStringContainsString("resources/frontend/js/landing-page/activities/detail.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/activities/index-entry.scss", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/activities/detail-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/activities-index.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/activity-detail.js", $mix);
    }

    public function test_activity_directory_route_renders_landing_page_view(): void
    {
        $response = $this->get(route('view.activities-service'));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.activities.index');
        $response->assertViewHasAll([
            'activities',
            'locationOptions',
            'typeOptions',
            'searchName',
            'searchLocation',
            'searchType',
            'featuredActivity',
            'directoryStats',
        ]);
    }

    public function test_activity_detail_route_renders_landing_page_view(): void
    {
        $activity = Activities::create([
            'name' => 'Route Test Activity',
            'code' => 'RTA-' . uniqid(),
            'type' => 'Adventure',
            'location' => 'Ubud',
            'map' => 'https://example.test/map',
            'description' => 'Route test activity description.',
            'itinerary' => 'Route test itinerary.',
            'duration' => '2 hours',
            'include' => 'Guide',
            'additional_info' => 'Route test additional information.',
            'cancellation_policy' => 'Route test cancellation policy.',
            'contract_rate' => 250000,
            'markup' => 10,
            'qty' => '10',
            'min_pax' => '1',
            'status' => 'Active',
            'validity' => now()->addMonth()->toDateString(),
            'author_id' => 1,
            'cover' => 'default.webp',
        ]);

        $response = $this->get(route('view.activity-public-detail', $activity->code));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.activities.detail');
        $response->assertViewHasAll([
            'activity',
            'galleryImages',
            'activitySections',
            'summaryStats',
            'overviewFacts',
            'sidebarFacts',
            'nearActivities',
            'activityOrderForm',
        ]);
    }

    public function test_tour_public_views_are_routed_to_landing_page_structure(): void
    {
        $frontEndController = file_get_contents(app_path('Http/Controllers/FrontEndController.php'));
        $toursController = file_get_contents(app_path('Http/Controllers/ToursController.php'));

        $this->assertFileExists(resource_path('views/frontend/landing-page/tours/directory.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/tours/detail.blade.php'));
        $this->assertStringContainsString("view('frontend.landing-page.tours.directory'", $frontEndController);
        $this->assertStringContainsString("view('frontend.landing-page.tours.detail'", $toursController);
        $this->assertStringNotContainsString("view('frontend.tours.directory'", $frontEndController);
        $this->assertStringNotContainsString("view('frontend.tours.detail-modern'", $toursController);
    }

    public function test_tour_public_assets_are_sourced_from_landing_page_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/landing-page/tours/index.js'));
        $this->assertFileExists(resource_path('frontend/js/landing-page/tours/detail.js'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/tours/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/tours/_directory.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/tours/detail-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/tours/_detail.scss'));
        $this->assertStringContainsString("resources/frontend/js/landing-page/tours/index.js", $mix);
        $this->assertStringContainsString("resources/frontend/js/landing-page/tours/detail.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/tours/index-entry.scss", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/tours/detail-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/tour-packages-index.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/tour-detail.js", $mix);
    }

    public function test_tour_directory_route_renders_landing_page_view(): void
    {
        $response = $this->get(route('view.tour-package-services'));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.tours.directory');
        $response->assertViewHasAll([
            'tours',
            'areaOptions',
            'typeOptions',
            'searchName',
            'searchArea',
            'searchType',
            'featuredTour',
            'directoryStats',
        ]);
    }

    public function test_tour_detail_route_renders_landing_page_view(): void
    {
        $tourData = [
            'name' => 'Route Test Tour',
            'code' => 'RTT-' . uniqid(),
            'name_traditional' => 'Route Test Tour',
            'name_simplified' => 'Route Test Tour',
            'slug' => 'route-test-tour-' . uniqid(),
            'cover' => 'default.webp',
            'short_description' => 'Route test tour short description.',
            'description' => 'Route test tour description.',
            'package_highlights' => 'Route test tour highlights.',
            'duration_days' => 1,
            'duration_nights' => 0,
            'itinerary' => 'Route test itinerary.',
            'include' => 'Guide',
            'exclude' => 'Personal expenses',
            'additional_info' => 'Route test additional information.',
            'cancellation_policy' => 'Route test cancellation policy.',
            'status' => 'Active',
        ];

        foreach (['area', 'area_traditional', 'area_simplified'] as $areaColumn) {
            if (Schema::hasColumn('tours', $areaColumn)) {
                $tourData[$areaColumn] = 'Bali';
            }
        }

        $tour = Tours::create($tourData);

        $response = $this->get(route('view.tour-detail', $tour->slug));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.tours.detail');
        $response->assertViewHasAll([
            'tour',
            'neartours',
            'prices',
            'tourGeneratedItinerary',
            'tourMapLocations',
            'canViewTourRates',
            'tourRateAccess',
        ]);
    }

    public function test_accommodation_public_views_are_routed_to_landing_page_structure(): void
    {
        $frontEndController = file_get_contents(app_path('Http/Controllers/FrontEndController.php'));

        $this->assertFileExists(resource_path('views/frontend/landing-page/accommodations/index.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/accommodations/detail.blade.php'));
        $this->assertStringContainsString("view('frontend.landing-page.accommodations.index'", $frontEndController);
        $this->assertStringContainsString("view('frontend.landing-page.accommodations.detail'", $frontEndController);
        $this->assertStringNotContainsString("view('frontend.accommodations.index'", $frontEndController);
        $this->assertStringNotContainsString("view('frontend.accommodations.detail'", $frontEndController);
    }

    public function test_accommodation_public_assets_are_sourced_from_landing_page_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/landing-page/accommodations/index.js'));
        $this->assertFileExists(resource_path('frontend/js/landing-page/accommodations/detail.js'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/accommodations/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/accommodations/_index.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/accommodations/detail-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/accommodations/_detail.scss'));
        $this->assertStringContainsString("resources/frontend/js/landing-page/accommodations/index.js", $mix);
        $this->assertStringContainsString("resources/frontend/js/landing-page/accommodations/detail.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/accommodations/index-entry.scss", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/accommodations/detail-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/accommodations-index.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/accommodation-detail.js", $mix);
    }

    public function test_accommodation_directory_route_renders_landing_page_view(): void
    {
        $response = $this->get(route('view.accommodation-services'));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.accommodations.index');
        $response->assertViewHasAll([
            'hotels',
            'regions',
            'searchName',
            'searchRegion',
            'promoAvailable',
            'featuredHotel',
            'directoryStats',
        ]);
    }

    public function test_accommodation_detail_route_renders_landing_page_view(): void
    {
        $hotel = Hotels::create([
            'name' => 'Route Test Hotel',
            'code' => 'RTH-' . uniqid(),
            'region' => 'Bali',
            'address' => 'Route Test Address',
            'airport_duration' => 1,
            'airport_distance' => 12,
            'contact_person' => 'Route Contact',
            'phone' => '08123456789',
            'description' => 'Route test hotel description.',
            'facility' => 'Pool',
            'additional_info' => 'Route test additional information.',
            'wedding_info' => 'Route test wedding information.',
            'entrance_fee' => '0',
            'wedding_cancellation_policy' => 'Route test wedding cancellation policy.',
            'status' => 'Active',
            'cover' => 'default.webp',
            'author_id' => 1,
            'min_stay' => '1',
            'max_stay' => '14',
            'check_in_time' => '14:00',
            'check_out_time' => '12:00',
            'map' => 'https://example.test/map',
            'benefits' => 'Route test benefits.',
            'optional_rate' => '0',
            'cancellation_policy' => 'Route test cancellation policy.',
        ]);

        $response = $this->get(route('view.hotel-detail', $hotel->code));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.accommodations.detail');
        $response->assertViewHasAll([
            'hotel',
            'canUseCheckPriceForm',
            'checkPriceCta',
        ]);
    }

    public function test_hotel_availability_view_is_routed_to_frontend_home_booking_structure(): void
    {
        $hotelsController = file_get_contents(app_path('Http/Controllers/HotelsController.php'));
        $accommodationDocs = file_get_contents(base_path('docs/modules/accommodation.md'));

        $this->assertFileExists(resource_path('views/frontend/home/booking/hotel-availability.blade.php'));
        $this->assertStringContainsString("view('frontend.home.booking.hotel-availability'", $hotelsController);
        $this->assertStringContainsString('$lastStayDate = Carbon::parse($checkout)->subDay()->format(\'Y-m-d\');', $hotelsController);
        $this->assertStringContainsString('$activeRoomIds = $hotel->rooms->pluck(\'id\')->all();', $hotelsController);
        $this->assertStringContainsString("->whereDate('start_date', '<=', \$lastStayDate)", $hotelsController);
        $this->assertStringContainsString("->whereDate('end_date', '>=', \$checkin)", $hotelsController);
        $this->assertStringContainsString("->whereDate('periode_start', '<=', \$lastStayDate)", $hotelsController);
        $this->assertStringContainsString("->whereDate('periode_end', '>=', \$checkin)", $hotelsController);
        $this->assertStringContainsString("->whereDate('stay_period_start', '<=', \$checkin)", $hotelsController);
        $this->assertStringContainsString("->whereDate('stay_period_end', '>=', \$lastStayDate)", $hotelsController);
        $this->assertStringContainsString('selected stay window yang sama', $accommodationDocs);
        $this->assertStringNotContainsString("view('main.hotelavailability'", $hotelsController);
    }

    public function test_hotel_availability_assets_are_sourced_from_frontend_home_booking_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/home/booking/hotel-availability.js'));
        $this->assertFileExists(resource_path('frontend/scss/home/booking/hotel-availability-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/home/booking/_hotel-availability.scss'));
        $this->assertStringContainsString("resources/frontend/js/home/booking/hotel-availability.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/home/booking/hotel-availability-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/hotel-availability.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/scss/pages/hotel-availability-entry.scss", $mix);
    }

    public function test_hotel_availability_route_renders_frontend_home_booking_view(): void
    {
        $hotel = Hotels::create([
            'name' => 'Route Availability Hotel',
            'code' => 'RAH-' . uniqid(),
            'region' => 'Bali',
            'address' => 'Route Availability Address',
            'airport_duration' => 1,
            'airport_distance' => 12,
            'contact_person' => 'Route Contact',
            'phone' => '08123456789',
            'description' => 'Route availability hotel description.',
            'facility' => 'Pool',
            'additional_info' => 'Route availability additional information.',
            'wedding_info' => 'Route availability wedding information.',
            'entrance_fee' => '0',
            'wedding_cancellation_policy' => 'Route availability wedding cancellation policy.',
            'status' => 'Active',
            'cover' => 'default.webp',
            'author_id' => 1,
            'min_stay' => '1',
            'max_stay' => '14',
            'check_in_time' => '14:00',
            'check_out_time' => '12:00',
            'map' => 'https://example.test/map',
            'benefits' => 'Route availability benefits.',
            'optional_rate' => '0',
            'cancellation_policy' => 'Route availability cancellation policy.',
        ]);

        $response = $this
            ->withoutMiddleware()
            ->get(route('view.hotel-prices.page', [
                'code' => $hotel->code,
                'checkin' => now()->addDays(7)->toDateString(),
                'checkout' => now()->addDays(9)->toDateString(),
            ]));

        $response->assertOk();
        $response->assertViewIs('frontend.home.booking.hotel-availability');
        $response->assertViewHasAll([
            'hotel',
            'nearhotels',
            'promotions',
            'duration',
            'checkin',
            'checkout',
            'rateSections',
            'hasAnyResults',
        ]);
    }

    public function test_static_landing_page_views_are_routed_to_landing_page_structure(): void
    {
        $homeController = file_get_contents(app_path('Http/Controllers/HomeController.php'));
        $appServiceProvider = file_get_contents(app_path('Providers/AppServiceProvider.php'));

        $this->assertFileExists(resource_path('views/frontend/landing-page/about/index.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/contact/index.blade.php'));
        $this->assertStringContainsString("view('frontend.landing-page.about.index'", $homeController);
        $this->assertStringContainsString("view('frontend.landing-page.contact.index'", $homeController);
        $this->assertStringContainsString("View::composer('frontend.landing-page.about.index'", $appServiceProvider);
        $this->assertStringNotContainsString("view('home.landing-page.about'", $homeController);
        $this->assertStringNotContainsString("view('home.landing-page.contact'", $homeController);
        $this->assertStringNotContainsString("View::composer('home.landing-page.about'", $appServiceProvider);
    }

    public function test_static_landing_page_assets_are_sourced_from_landing_page_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/scss/landing-page/about/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/about/_index.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/contact/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/contact/_index.scss'));
        $this->assertStringContainsString("resources/frontend/scss/landing-page/about/index-entry.scss", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/contact/index-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/scss/pages/about-page-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/scss/pages/contact-page-entry.scss", $mix);
    }

    public function test_about_route_renders_landing_page_view(): void
    {
        $response = $this->get(route('about-us'));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.about.index');
        $response->assertViewHas('businessProfile');
    }

    public function test_contact_route_renders_landing_page_view(): void
    {
        $response = $this->get(route('contact-us'));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.contact.index');
        $response->assertViewHasAll([
            'businessProfile',
            'contactData',
        ]);
    }

    public function test_public_policy_views_are_routed_to_landing_page_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/TermAndConditionController.php'));

        $this->assertFileExists(resource_path('views/frontend/landing-page/policies/terms-and-conditions.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/policies/privacy-policy.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/policies/faq.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/policies/partials/public-policy-page.blade.php'));
        $this->assertStringContainsString("view('frontend.landing-page.policies.terms-and-conditions'", $controller);
        $this->assertStringContainsString("view('frontend.landing-page.policies.privacy-policy'", $controller);
        $this->assertStringContainsString("view('frontend.landing-page.policies.faq'", $controller);
        $this->assertStringNotContainsString("view('privacy-policy.terms-and-conditions'", $controller);
        $this->assertStringNotContainsString("view('privacy-policy.faq'", $controller);
    }

    public function test_public_policy_assets_are_sourced_from_landing_page_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/scss/landing-page/policies/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/policies/_index.scss'));
        $this->assertStringContainsString("resources/frontend/scss/landing-page/policies/index-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/scss/pages/public-policy-entry.scss", $mix);
    }

    public function test_public_policy_routes_render_landing_page_views(): void
    {
        $termsResponse = $this->get(route('terms-and-conditions'));
        $privacyResponse = $this->get(route('privacy-policy'));
        $faqResponse = $this->get(route('faq'));
        $helpResponse = $this->get(route('help'));

        $termsResponse->assertOk();
        $termsResponse->assertViewIs('frontend.landing-page.policies.terms-and-conditions');
        $privacyResponse->assertOk();
        $privacyResponse->assertViewIs('frontend.landing-page.policies.privacy-policy');
        $faqResponse->assertOk();
        $faqResponse->assertViewIs('frontend.landing-page.policies.faq');
        $helpResponse->assertOk();
        $helpResponse->assertViewIs('frontend.landing-page.policies.faq');
    }

    public function test_backend_terms_manager_is_sourced_from_backend_admin_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/TermAndConditionController.php'));
        $routeFile = file_get_contents(base_path('routes/web.php'));
        $view = file_get_contents(resource_path('views/backend/admin/terms/index.blade.php'));
        $modal = file_get_contents(resource_path('views/backend/admin/terms/partials/policy-modal.blade.php'));
        $scss = file_get_contents(resource_path('backend/scss/admin/terms/_index.scss'));
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('views/backend/admin/terms/index.blade.php'));
        $this->assertFileExists(resource_path('views/backend/admin/terms/partials/policy-modal.blade.php'));
        $this->assertFileExists(resource_path('backend/js/admin/terms/index.js'));
        $this->assertFileExists(resource_path('backend/scss/admin/terms/index-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/admin/terms/_index.scss'));
        $this->assertStringContainsString("view('backend.admin.terms.index'", $controller);
        $this->assertStringContainsString('validatePolicy', $controller);
        $this->assertStringContainsString('recordPolicyLog', $controller);
        $this->assertStringContainsString('Auth::id()', $controller);
        $this->assertStringContainsString("Route::put('/fupdate-policy/{id}'", $routeFile);
        $this->assertStringContainsString("->name('term-and-condition.policy.update')", $routeFile);
        $this->assertStringContainsString("->name('term-and-condition.policy.store')", $routeFile);
        $this->assertStringContainsString("->name('term-and-condition.policy.destroy')", $routeFile);
        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('backend-page-toolbar terms-admin-toolbar', $view);
        $this->assertStringContainsString("route('admin.panel-main.view')", $view);
        $this->assertStringContainsString("mix('build/backend/css/admin/terms/index.css')", $view);
        $this->assertStringContainsString("mix('build/backend/js/admin/terms/index.js')", $view);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--4', $view);
        $this->assertStringContainsString('backend-kpi-card backend-kpi-card--', $view);
        $this->assertStringContainsString('backend-kpi-card__icon', $view);
        $this->assertStringContainsString('backend-feedback', $view);
        $this->assertStringContainsString('backend-alert backend-alert--', $view);
        $this->assertStringContainsString('backend-status-badge', $view);
        $this->assertStringNotContainsString('terms-admin-stats', $view);
        $this->assertStringContainsString('backend-panel terms-admin-section', $view);
        $this->assertStringContainsString('backend-panel terms-admin-guide', $view);
        $this->assertStringContainsString('backend-section-header terms-admin-section__heading', $view);
        $this->assertStringContainsString('backend-section-header terms-admin-guide__heading', $view);
        $this->assertStringContainsString('backend-section-header__label', $view);
        $this->assertStringNotContainsString('terms-admin-section__header', $view);
        $this->assertStringNotContainsString('terms-admin-eyebrow', $view);
        $this->assertStringContainsString('backend.admin.terms.partials.policy-modal', $view);
        $this->assertStringContainsString("route('term-and-condition.policy.update'", $view);
        $this->assertStringContainsString("route('term-and-condition.policy.store')", $view);
        $this->assertStringContainsString("route('term-and-condition.policy.destroy'", $view);
        $this->assertStringNotContainsString('card-box-title', $view);
        $this->assertStringNotContainsString('name="author"', $view);
        $this->assertStringNotContainsString('name="author"', $modal);
        $this->assertStringContainsString('overflow-x: clip;', $scss);
        $this->assertStringContainsString('min-width: 0;', $scss);
        $this->assertStringContainsString('grid-template-columns: minmax(0, 1fr);', $scss);
        $this->assertStringNotContainsString('.terms-admin-stats', $scss);
        $this->assertStringNotContainsString('.terms-admin-alert', $scss);
        $this->assertStringNotContainsString('.terms-admin-badge', $scss);
        $this->assertStringNotContainsString('.terms-admin-section__header', $scss);
        $this->assertStringNotContainsString('.terms-admin-eyebrow', $scss);
        $this->assertStringContainsString('@media (max-width: 575px)', $scss);
        $this->assertStringContainsString("resources/backend/js/admin/terms/index.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/admin/terms/index-entry.scss", $mix);
    }

    public function test_backend_terms_manager_create_update_and_delete_flow(): void
    {
        $developer = User::forceCreate([
            'username' => 'developer-terms-crud',
            'name' => 'Developer Terms CRUD',
            'type' => 'admin',
            'email' => 'developer-terms-crud@example.test',
            'position' => 'developer',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'is_subscribed' => false,
            'subscriber' => false,
        ]);

        $existingPolicy = TermAndCondition::create([
            'type' => 'User',
            'name_id' => 'Kebijakan Awal',
            'name_en' => 'Existing Policy',
            'name_zh' => 'Existing Policy ZH',
            'policy_id' => '<p>Konten awal.</p>',
            'policy_en' => '<p>Existing content.</p>',
            'policy_zh' => '<p>Existing content ZH.</p>',
            'status' => 'Draft',
        ]);

        $response = $this->actingAs($developer)->get(route('view.term-and-condition'));

        $response->assertOk();
        $response->assertViewIs('backend.admin.terms.index');
        $response->assertViewHasAll([
            'policySections',
            'policyTypes',
            'summary',
        ]);
        $response->assertSee('Terms and Conditions');
        $response->assertSee('Existing Policy');

        $this->actingAs($developer)
            ->put(route('term-and-condition.policy.store'), [
                'type' => 'FAQ',
                'name_id' => 'Pertanyaan Baru',
                'name_en' => 'New FAQ Question',
                'name_zh' => 'New FAQ Question ZH',
                'policy_id' => '<p>Jawaban baru.</p>',
                'policy_en' => '<p>New answer.</p>',
                'policy_zh' => '<p>New answer ZH.</p>',
                'status' => 'Active',
            ])
            ->assertRedirect(route('view.term-and-condition'));

        $createdPolicy = TermAndCondition::where('name_en', 'New FAQ Question')->firstOrFail();
        $this->assertSame('FAQ', $createdPolicy->type);
        $this->assertSame('Active', $createdPolicy->status);

        $this->actingAs($developer)
            ->put(route('term-and-condition.policy.update', $createdPolicy->id), [
                'type' => 'Promotion',
                'name_id' => 'Pertanyaan Baru Diperbarui',
                'name_en' => 'Updated Policy Question',
                'name_zh' => 'Updated Policy Question ZH',
                'policy_id' => '<p>Jawaban diperbarui.</p>',
                'policy_en' => '<p>Updated answer.</p>',
                'policy_zh' => '<p>Updated answer ZH.</p>',
                'status' => 'Draft',
            ])
            ->assertRedirect(route('view.term-and-condition'));

        $createdPolicy->refresh();
        $this->assertSame('Promotion', $createdPolicy->type);
        $this->assertSame('Updated Policy Question', $createdPolicy->name_en);
        $this->assertSame('Draft', $createdPolicy->status);

        $this->actingAs($developer)
            ->delete(route('term-and-condition.policy.destroy', $createdPolicy->id))
            ->assertRedirect(route('view.term-and-condition'));

        $this->assertDatabaseMissing('term_and_conditions', [
            'id' => $createdPolicy->id,
        ]);

        $this->assertDatabaseHas('term_and_conditions', [
            'id' => $existingPolicy->id,
        ]);
    }

    public function test_footer_policy_links_include_faqs(): void
    {
        $footerSeeder = file_get_contents(database_path('seeders/FooterSeeder.php'));
        $footerFaqMigration = file_get_contents(database_path('migrations/2026_07_15_150000_add_faqs_to_footer_policy_links.php'));

        $this->assertStringContainsString("'group' => 'policies'", $footerSeeder);
        $this->assertStringContainsString("'label' => 'FAQs'", $footerSeeder);
        $this->assertStringContainsString("'route_name' => 'faq'", $footerSeeder);
        $this->assertStringContainsString("'group' => 'policies'", $footerFaqMigration);
        $this->assertStringContainsString("'label' => 'FAQs'", $footerFaqMigration);
        $this->assertStringContainsString("'route_name' => 'faq'", $footerFaqMigration);
    }

    public function test_backend_company_profile_is_sourced_from_backend_admin_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/BusinessProfileController.php'));
        $view = file_get_contents(resource_path('views/backend/admin/company-profile/edit.blade.php'));
        $fieldPartial = file_get_contents(resource_path('views/backend/admin/company-profile/partials/field.blade.php'));
        $logoPartial = file_get_contents(resource_path('views/backend/admin/company-profile/partials/logo-field.blade.php'));
        $legacyWrapper = file_get_contents(resource_path('views/admin/business-profile/edit.blade.php'));
        $scss = file_get_contents(resource_path('backend/scss/admin/company-profile/_edit.scss'));
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('views/backend/admin/company-profile/edit.blade.php'));
        $this->assertFileExists(resource_path('views/backend/admin/company-profile/partials/field.blade.php'));
        $this->assertFileExists(resource_path('views/backend/admin/company-profile/partials/logo-field.blade.php'));
        $this->assertFileExists(resource_path('backend/js/admin/company-profile/edit.js'));
        $this->assertFileExists(resource_path('backend/scss/admin/company-profile/edit-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/admin/company-profile/_edit.scss'));
        $this->assertStringContainsString("view('backend.admin.company-profile.edit'", $controller);
        $this->assertStringContainsString('profileSummary', $controller);
        $this->assertStringContainsString('logoUrl', $controller);
        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('backend-page-toolbar company-profile-toolbar', $view);
        $this->assertStringContainsString('backend-page-toolbar__actions', $view);
        $this->assertStringContainsString('backend-toolbar-action company-profile-toolbar-action', $view);
        $this->assertStringContainsString('backend-feedback', $view);
        $this->assertStringContainsString('backend-alert backend-alert--', $view);
        $this->assertStringContainsString("route('admin.panel-main.view')", $view);
        $this->assertStringContainsString("mix('build/backend/css/admin/company-profile/edit.css')", $view);
        $this->assertStringContainsString("mix('build/backend/js/admin/company-profile/edit.js')", $view);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--4', $view);
        $this->assertStringContainsString('backend-kpi-card backend-kpi-card--', $view);
        $this->assertStringContainsString('backend-kpi-card__icon', $view);
        $this->assertStringNotContainsString('company-profile-stats', $view);
        $this->assertStringContainsString('company-profile-layout', $view);
        $this->assertStringContainsString('backend-panel company-profile-panel', $view);
        $this->assertStringContainsString('backend-section-header', $view);
        $this->assertStringContainsString('backend-section-header__label', $view);
        $this->assertStringNotContainsString('company-profile-panel__header', $view);
        $this->assertStringNotContainsString('company-profile-eyebrow', $view);
        $this->assertStringContainsString('backend.admin.company-profile.partials.field', $view);
        $this->assertStringContainsString('backend.admin.company-profile.partials.logo-field', $view);
        $publicContentPosition = strpos($view, '<span class="backend-section-header__label">Public Content</span>');
        $contactPosition = strpos($view, '<span class="backend-section-header__label">Contact</span>');
        $digitalChannelsPosition = strpos($view, '<span class="backend-section-header__label">Digital Channels</span>');
        $this->assertIsInt($publicContentPosition);
        $this->assertIsInt($contactPosition);
        $this->assertIsInt($digitalChannelsPosition);
        $this->assertGreaterThan($publicContentPosition, $contactPosition);
        $this->assertGreaterThan($contactPosition, $digitalChannelsPosition);
        $this->assertStringContainsString("@include('backend.admin.company-profile.edit')", $legacyWrapper);
        $this->assertStringNotContainsString('<style>', $view);
        $this->assertStringNotContainsString('card-box', $view);
        $this->assertStringContainsString('invalid-feedback', $fieldPartial);
        $this->assertStringContainsString('data-company-logo-preview', $logoPartial);
        $this->assertStringContainsString('overflow-x: clip;', $scss);
        $this->assertStringContainsString('min-width: 0;', $scss);
        $this->assertStringContainsString('grid-template-columns: minmax(0, 1fr);', $scss);
        $this->assertStringNotContainsString('.company-profile-stats', $scss);
        $this->assertStringNotContainsString('.company-profile-alert', $scss);
        $this->assertStringNotContainsString('.company-profile-panel__header', $scss);
        $this->assertStringNotContainsString('.company-profile-eyebrow', $scss);
        $this->assertStringContainsString('@media (max-width: 575px)', $scss);
        $this->assertStringContainsString("resources/backend/js/admin/company-profile/edit.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/admin/company-profile/edit-entry.scss", $mix);
    }

    public function test_backend_company_profile_page_update_and_logo_upload_flow(): void
    {
        $disk = config('filesystems.default');
        Storage::fake($disk);

        $developer = User::forceCreate([
            'username' => 'developer-company-profile',
            'name' => 'Developer Company Profile',
            'type' => 'admin',
            'email' => 'developer-company-profile@example.test',
            'position' => 'developer',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'is_subscribed' => false,
            'subscriber' => false,
        ]);

        BusinessProfile::query()->updateOrCreate(
            ['profile_key' => 'primary'],
            [
                'name' => 'Initial Company',
                'nickname' => 'Initial Brand',
                'type' => 'Travel Agent',
                'address' => 'Initial Address',
                'email' => 'initial@example.test',
            ]
        );

        $response = $this->actingAs($developer)->get(route('admin.company-profile.edit'));

        $response->assertOk();
        $response->assertViewIs('backend.admin.company-profile.edit');
        $response->assertViewHasAll([
            'businessProfile',
            'summary',
            'logoUrl',
            'logoDarkUrl',
        ]);
        $response->assertSee('Company Profile');

        $this->actingAs($developer)
            ->put(route('admin.company-profile.update'), [
                'name' => 'PT Test Company',
                'nickname' => 'Test Brand',
                'type' => 'B2B Travel Partner',
                'license' => 'LIC-001',
                'tax_number' => 'TAX-001',
                'tax_id' => 'NPWP-001',
                'caption' => 'Premium travel partner',
                'address' => 'Jl. Test Company',
                'map' => 'https://www.google.com/maps/embed?pb=test',
                'phone' => '+62 361 111111',
                'phone_2' => '+62 361 222222',
                'phone_3' => '+62 361 333333',
                'whatsapp' => '+62 812 111111',
                'email' => 'company-profile@example.test',
                'website' => 'https://example.test',
                'instagram' => 'https://instagram.test/company',
                'facebook' => 'https://facebook.test/company',
                'twitter' => 'https://x.test/company',
                'youtube' => 'https://youtube.test/company',
                'linkedin' => 'https://linkedin.test/company',
                'public_tagline' => 'Partner ready travel company',
                'public_tagline_traditional' => 'Traditional tagline',
                'public_tagline_simplified' => 'Simplified tagline',
                'public_description' => 'English public description.',
                'public_description_traditional' => 'Traditional public description.',
                'public_description_simplified' => 'Simplified public description.',
                'logo' => UploadedFile::fake()->image('light-logo.png', 320, 120),
                'logo_dark' => UploadedFile::fake()->image('dark-logo.png', 320, 120),
            ])
            ->assertRedirect(route('admin.company-profile.edit'));

        $profile = BusinessProfile::where('profile_key', 'primary')->firstOrFail();
        $this->assertSame('PT Test Company', $profile->name);
        $this->assertSame('Test Brand', $profile->nickname);
        $this->assertSame('company-profile@example.test', $profile->email);
        $this->assertSame('Partner ready travel company', $profile->public_tagline);
        $this->assertNotEmpty($profile->logo);
        $this->assertNotEmpty($profile->logo_dark);
        Storage::disk($disk)->assertExists('public/logo/'.$profile->logo);
        Storage::disk($disk)->assertExists('public/logo/'.$profile->logo_dark);
    }

    public function test_backend_footer_manager_is_sourced_from_backend_admin_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/FooterManagerController.php'));
        $view = file_get_contents(resource_path('views/backend/admin/footer-manager/index.blade.php'));
        $linkModalPartial = file_get_contents(resource_path('views/backend/admin/footer-manager/partials/link-modal.blade.php'));
        $settingSectionPartial = file_get_contents(resource_path('views/backend/admin/footer-manager/partials/setting-section.blade.php'));
        $legacyWrapper = file_get_contents(resource_path('views/admin/footer-manager/index.blade.php'));
        $legacyModalWrapper = file_get_contents(resource_path('views/admin/footer-manager/partials/link-modal.blade.php'));
        $scss = file_get_contents(resource_path('backend/scss/admin/footer-manager/_index.scss'));
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('views/backend/admin/footer-manager/index.blade.php'));
        $this->assertFileExists(resource_path('views/backend/admin/footer-manager/partials/link-modal.blade.php'));
        $this->assertFileExists(resource_path('views/backend/admin/footer-manager/partials/setting-section.blade.php'));
        $this->assertFileExists(resource_path('backend/js/admin/footer-manager/index.js'));
        $this->assertFileExists(resource_path('backend/scss/admin/footer-manager/index-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/admin/footer-manager/_index.scss'));
        $this->assertStringContainsString("view('backend.admin.footer-manager.index'", $controller);
        $this->assertStringContainsString('summary', $controller);
        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('backend-page-toolbar footer-manager-toolbar', $view);
        $this->assertStringContainsString('backend-page-toolbar__actions', $view);
        $this->assertStringContainsString('backend-toolbar-action footer-manager-toolbar-action', $view);
        $this->assertStringContainsString('backend-feedback', $view);
        $this->assertStringContainsString('backend-alert backend-alert--', $view);
        $this->assertStringContainsString('backend-status-badge', $view);
        $this->assertStringContainsString("route('admin.panel-main.view')", $view);
        $this->assertStringContainsString("mix('build/backend/css/admin/footer-manager/index.css')", $view);
        $this->assertStringContainsString("mix('build/backend/js/admin/footer-manager/index.js')", $view);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--3', $view);
        $this->assertStringContainsString('backend-kpi-card backend-kpi-card--', $view);
        $this->assertStringContainsString('backend-kpi-card__icon', $view);
        $this->assertStringNotContainsString('footer-manager-stats', $view);
        $this->assertStringContainsString('footer-manager-layout', $view);
        $this->assertStringContainsString('backend-panel footer-manager-panel', $view);
        $this->assertStringContainsString('backend-section-header footer-manager-panel__heading', $view);
        $this->assertStringContainsString('backend-section-header__label', $view);
        $this->assertStringNotContainsString('footer-manager-panel__header', $view);
        $this->assertStringNotContainsString('footer-manager-eyebrow', $view);
        $this->assertStringContainsString('backend.admin.footer-manager.partials.setting-section', $view);
        $this->assertStringContainsString('backend.admin.footer-manager.partials.link-modal', $view);
        $this->assertStringContainsString("@include('backend.admin.footer-manager.index')", $legacyWrapper);
        $this->assertStringContainsString("@include('backend.admin.footer-manager.partials.link-modal')", $legacyModalWrapper);
        $this->assertStringNotContainsString('<style>', $view);
        $this->assertStringNotContainsString('card-box', $view);
        $this->assertStringContainsString('footer-setting-section__toggle', $settingSectionPartial);
        $this->assertStringContainsString('footer-link-modal__grid', $linkModalPartial);
        $this->assertStringContainsString('overflow-x: clip;', $scss);
        $this->assertStringContainsString('min-width: 0;', $scss);
        $this->assertStringContainsString('grid-template-columns: minmax(0, 1fr);', $scss);
        $this->assertStringNotContainsString('.footer-manager-stats', $scss);
        $this->assertStringNotContainsString('.footer-manager-alert', $scss);
        $this->assertStringNotContainsString('.footer-manager-panel__header', $scss);
        $this->assertStringContainsString('@media (max-width: 575px)', $scss);
        $this->assertStringContainsString("resources/backend/js/admin/footer-manager/index.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/admin/footer-manager/index-entry.scss", $mix);
    }

    public function test_profile_view_is_routed_to_frontend_home_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/ProfileController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/profile/index.blade.php'));
        $this->assertStringContainsString("view('frontend.home.profile.index'", $controller);
        $this->assertStringNotContainsString("view('main.profile'", $controller);
    }

    public function test_profile_assets_are_sourced_from_frontend_home_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/home/profile/index.js'));
        $this->assertFileExists(resource_path('frontend/scss/home/profile/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/home/profile/_index.scss'));
        $this->assertStringContainsString("resources/frontend/js/home/profile/index.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/home/profile/index-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/profile.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/scss/pages/profile-entry.scss", $mix);
    }

    public function test_profile_route_renders_frontend_home_view(): void
    {
        $user = User::forceCreate([
            'name' => 'Profile Structure User',
            'username' => 'profile-structure',
            'email' => 'profile-structure@example.test',
            'password' => 'secret',
            'type' => 'user',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertOk();
        $response->assertViewIs('frontend.home.profile.index');
        $response->assertViewHas('profileUser');
    }

    public function test_manual_book_view_is_routed_to_frontend_home_structure(): void
    {
        $manualBookController = file_get_contents(app_path('Http/Controllers/ManualBookController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/manual-book/index.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/main/manual-book.blade.php'));
        $this->assertStringContainsString("view('frontend.home.manual-book.index'", $manualBookController);
        $this->assertStringNotContainsString("view('main.manual-book'", $manualBookController);
    }

    public function test_manual_book_assets_are_sourced_from_frontend_home_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/home/manual-book/index.js'));
        $this->assertFileExists(resource_path('frontend/scss/home/manual-book/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/home/manual-book/_index.scss'));
        $this->assertStringContainsString("resources/frontend/js/home/manual-book/index.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/home/manual-book/index-entry.scss", $mix);
    }

    public function test_manual_book_route_renders_frontend_home_view(): void
    {
        $user = User::forceCreate([
            'name' => 'Manual Book Structure User',
            'username' => 'manual-book-structure',
            'email' => 'manual-book-structure@example.test',
            'password' => 'secret',
            'type' => 'user',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
        ]);

        ManualBook::create([
            'name' => 'Partner User Guide',
            'language' => 'en',
            'file_name' => 'partner-user-guide.pdf',
        ]);

        $response = $this->actingAs($user)->get(route('view.manual-book'));

        $response->assertOk();
        $response->assertViewIs('frontend.home.manual-book.index');
        $response->assertViewHas('manualBooks');
        $response->assertSee('Partner User Guide');
    }

    public function test_orders_dashboard_view_is_routed_to_frontend_home_structure(): void
    {
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $menuController = file_get_contents(app_path('Http/Controllers/MenuController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/index.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.index'", $orderController);
        $this->assertStringContainsString("view('frontend.home.orders.index'", $menuController);
        $this->assertStringNotContainsString("view('main.order'", $orderController);
        $this->assertStringNotContainsString("view('main.order'", $menuController);
    }

    public function test_orders_dashboard_assets_are_sourced_from_frontend_home_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/home/orders/index.js'));
        $this->assertFileExists(resource_path('frontend/scss/home/orders/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/home/orders/_index.scss'));
        $this->assertStringContainsString("resources/frontend/js/home/orders/index.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/home/orders/index-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/frontend-orders.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/scss/pages/frontend-orders-entry.scss", $mix);
    }

    public function test_orders_dashboard_route_renders_frontend_home_view(): void
    {
        $user = User::forceCreate([
            'name' => 'Orders Structure User',
            'username' => 'orders-structure',
            'email' => 'orders-structure@example.test',
            'password' => 'secret',
            'type' => 'user',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('view.orders'));

        $response->assertOk();
        $response->assertViewIs('frontend.home.orders.index');
        $response->assertViewHas('orders');
    }

    public function test_order_detail_view_is_routed_to_frontend_home_structure(): void
    {
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/detail.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.detail'", $orderController);
        $this->assertStringNotContainsString("view('main.orderdetail'", $orderController);
    }

    public function test_order_detail_assets_are_sourced_from_frontend_home_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/home/orders/detail.js'));
        $this->assertFileExists(resource_path('frontend/scss/home/orders/detail-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/home/orders/_detail.scss'));
        $this->assertStringContainsString("resources/frontend/js/home/orders/detail.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/home/orders/detail-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/order-detail.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/scss/pages/order-detail-entry.scss", $mix);
    }

    public function test_order_detail_route_renders_frontend_home_view(): void
    {
        $user = User::forceCreate([
            'name' => 'Order Detail Structure User',
            'username' => 'order-detail-structure',
            'email' => 'order-detail-structure@example.test',
            'password' => 'secret',
            'type' => 'user',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
        ]);

        $order = Orders::forceCreate([
            'user_id' => $user->id,
            'sales_agent' => $user->id,
            'orderno' => 'ODT260715A',
            'confirmation_order' => 'CONFIRM',
            'name' => $user->name,
            'email' => $user->email,
            'servicename' => 'Structure Test Service',
            'service' => 'Additional Service',
            'checkin' => now()->toDateString(),
            'checkout' => now()->addDay()->toDateString(),
            'number_of_guests' => 1,
            'guest_detail' => 'Structure Guest',
            'price_total' => 100,
            'final_price' => 100,
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($user)->get(route('view.detail-order', ['id' => $order->id]));

        $response->assertOk();
        $response->assertViewIs('frontend.home.orders.detail');
        $response->assertViewHas('order');
    }

    public function test_orders_history_view_is_routed_to_frontend_home_structure(): void
    {
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/history.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.history'", $orderController);
        $this->assertStringNotContainsString("view('layouts.order-history'", $orderController);
    }

    public function test_orders_history_route_renders_frontend_home_view(): void
    {
        $user = User::forceCreate([
            'name' => 'Orders History Structure User',
            'username' => 'orders-history-structure',
            'email' => 'orders-history-structure@example.test',
            'password' => 'secret',
            'type' => 'user',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('orders.history'));

        $response->assertOk();
        $response->assertViewIs('frontend.home.orders.history');
        $response->assertViewHas('historyItems');
    }

    public function test_tour_order_edit_view_is_routed_to_frontend_home_structure(): void
    {
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/edit-tour.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.edit-tour'", $orderController);
        $this->assertStringNotContainsString("view('frontend.orders.edit-order-tour'", $orderController);
    }

    public function test_tour_order_edit_assets_are_sourced_from_frontend_home_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/home/orders/edit.js'));
        $this->assertStringContainsString("resources/frontend/js/home/orders/edit.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/order-edit.js", $mix);
    }

    public function test_tour_order_edit_route_renders_frontend_home_view(): void
    {
        \Illuminate\Support\Facades\Cache::forget('tax_1');
        \Illuminate\Support\Facades\Cache::forget('usd_rate');

        $user = User::forceCreate([
            'name' => 'Tour Edit Structure User',
            'username' => 'tour-edit-structure',
            'email' => 'tour-edit-structure@example.test',
            'password' => 'secret',
            'type' => 'user',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
        ]);

        Tax::unguarded(function () {
            Tax::updateOrCreate(['id' => 1], ['name' => 'Structure Tax', 'tax' => 0]);
        });

        UsdRates::updateOrCreate(
            ['name' => 'USD'],
            ['rate' => 10000, 'sell' => 10000, 'buy' => 10000, 'difference' => 0]
        );

        $tour = Tours::forceCreate([
            'name' => 'Structure Tour Edit Package',
            'code' => 'STE',
            'name_traditional' => 'Structure Tour Edit Package',
            'name_simplified' => 'Structure Tour Edit Package',
            'slug' => 'structure-tour-edit-package',
            'short_description' => 'Structure test tour.',
            'description' => 'Structure test tour.',
            'include' => 'Guide',
            'exclude' => 'Personal expense',
            'additional_info' => 'Structure info',
            'cancellation_policy' => 'Structure cancellation',
            'status' => 'Active',
        ]);

        TourPrices::forceCreate([
            'tour_id' => $tour->id,
            'min_qty' => 2,
            'max_qty' => 20,
            'contract_rate' => 100000,
            'markup' => 10,
            'expired_date' => now()->addYear()->toDateString(),
            'status' => 'Active',
        ]);

        $order = Orders::forceCreate([
            'user_id' => $user->id,
            'sales_agent' => $user->id,
            'orderno' => 'TOE260715A',
            'confirmation_order' => 'CONFIRM',
            'name' => $user->name,
            'email' => $user->email,
            'servicename' => $tour->name,
            'service' => 'Tour Package',
            'service_id' => $tour->id,
            'checkin' => now()->addDays(7)->toDateString(),
            'checkout' => now()->addDays(8)->toDateString(),
            'travel_date' => now()->addDays(7),
            'number_of_guests' => 2,
            'guest_detail' => '[]',
            'pickup_location' => 'Hotel Lobby',
            'dropoff_location' => 'Hotel Lobby',
            'price_pax' => 20,
            'price_total' => 40,
            'final_price' => 40,
            'status' => 'Draft',
        ]);

        $response = $this->actingAs($user)->get(route('view.edit-order-tour', ['id' => $order->id]));

        $response->assertOk();
        $response->assertViewIs('frontend.home.orders.edit-tour');
        $response->assertViewHasAll(['order', 'tour', 'prices']);
    }

    public function test_legacy_order_edit_wrapper_is_routed_to_frontend_home_structure(): void
    {
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/edit-legacy.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.edit-legacy'", $orderController);
        $this->assertStringNotContainsString("view('order.user-edit-order'", $orderController);
    }

    public function test_transport_order_edit_partial_is_sourced_from_frontend_home_structure(): void
    {
        $wrapper = file_get_contents(resource_path('views/frontend/home/orders/edit-legacy.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/partials/edit-transport.blade.php'));
        $this->assertStringContainsString("@include('frontend.home.orders.partials.edit-transport')", $wrapper);
        $this->assertStringNotContainsString("@include('order.edit-order-transport')", $wrapper);
    }

    public function test_villa_order_edit_partial_is_sourced_from_frontend_home_structure(): void
    {
        $wrapper = file_get_contents(resource_path('views/frontend/home/orders/edit-legacy.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/partials/edit-villa.blade.php'));
        $this->assertStringContainsString("@include('frontend.home.orders.partials.edit-villa')", $wrapper);
        $this->assertStringNotContainsString("@include('order.edit-order-villa')", $wrapper);
    }

    public function test_hotel_order_edit_partial_is_sourced_from_frontend_home_structure(): void
    {
        $wrapper = file_get_contents(resource_path('views/frontend/home/orders/edit-legacy.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/partials/edit-hotel.blade.php'));
        $this->assertStringContainsString("@include('frontend.home.orders.partials.edit-hotel')", $wrapper);
        $this->assertStringNotContainsString("@include('order.edit-order-hotel')", $wrapper);
    }

    public function test_activity_order_edit_partial_is_sourced_from_frontend_home_structure(): void
    {
        $wrapper = file_get_contents(resource_path('views/frontend/home/orders/edit-legacy.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/partials/edit-activity.blade.php'));
        $this->assertStringContainsString("@include('frontend.home.orders.partials.edit-activity')", $wrapper);
        $this->assertStringNotContainsString("@include('order.edit-order-activity')", $wrapper);
    }

    public function test_additional_charge_order_edit_view_is_routed_to_frontend_home_structure(): void
    {
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/edit-additional-charge.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.edit-additional-charge'", $orderController);
        $this->assertStringNotContainsString("view('order.edit-order-additional-charge'", $orderController);
    }

    public function test_additional_charge_order_edit_route_renders_frontend_home_view(): void
    {
        \Illuminate\Support\Facades\Cache::forget('tax_1');
        \Illuminate\Support\Facades\Cache::forget('usd_rate');
        \Illuminate\Support\Facades\Cache::forget('business_profile');

        $user = User::forceCreate([
            'name' => 'Additional Charge Structure User',
            'username' => 'additional-charge-structure',
            'email' => 'additional-charge-structure@example.test',
            'password' => 'secret',
            'type' => 'user',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
        ]);

        Tax::unguarded(function () {
            Tax::updateOrCreate(['id' => 1], ['tax' => 0]);
        });

        UsdRates::updateOrCreate(
            ['name' => 'USD'],
            ['rate' => 10000, 'sell' => 10000, 'buy' => 10000, 'difference' => 0]
        );

        BusinessProfile::unguarded(function () {
            BusinessProfile::updateOrCreate(
                ['id' => 1],
                [
                    'profile_key' => 'primary',
                    'name' => 'Structure Business',
                    'caption' => 'Structure Caption',
                    'logo' => 'storage/logo.png',
                    'logo_dark' => 'storage/logo-dark.png',
                ]
            );
        });

        $hotel = Hotels::forceCreate([
            'name' => 'Structure Additional Charge Hotel',
            'code' => 'SACH',
            'region' => 'Bali',
            'address' => 'Structure Address',
            'contact_person' => 'Structure Contact',
            'phone' => '08123456789',
            'description' => 'Structure hotel description.',
            'facility' => 'Pool',
            'status' => 'Active',
            'cover' => 'storage/hotels/structure-cover.jpg',
            'author_id' => $user->id,
            'cancellation_policy' => 'Structure cancellation',
        ]);

        OptionalRate::forceCreate([
            'hotels_id' => $hotel->id,
            'name' => 'Structure Breakfast',
            'service' => 'Hotel',
            'service_id' => $hotel->id,
            'type' => 'Meals',
            'mandatory' => 0,
            'contract_rate' => 100000,
            'markup' => 10,
            'description' => 'Structure optional service.',
        ]);

        $order = Orders::forceCreate([
            'user_id' => $user->id,
            'sales_agent' => $user->id,
            'orderno' => 'ACE260715A',
            'confirmation_order' => 'CONFIRM',
            'name' => $user->name,
            'email' => $user->email,
            'servicename' => $hotel->name,
            'service' => 'Hotel',
            'service_id' => $hotel->id,
            'checkin' => now()->addDays(7)->toDateString(),
            'checkout' => now()->addDays(9)->toDateString(),
            'number_of_guests' => 2,
            'guest_detail' => '[]',
            'price_total' => 100,
            'final_price' => 100,
            'status' => 'Draft',
        ]);

        $response = $this->actingAs($user)->get(route('view.edit-order-additional-charge', ['id' => $order->id]));

        $response->assertOk();
        $response->assertViewIs('frontend.home.orders.edit-additional-charge');
        $response->assertViewHasAll(['order', 'optional_services', 'date_stay']);
    }

    public function test_legacy_optional_service_order_edit_view_is_retired(): void
    {
        $routeFile = file_get_contents(base_path('routes/web.php'));
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $views = collect(glob(resource_path('views/**/*.blade.php'), GLOB_BRACE))
            ->map(fn ($path) => str_replace('\\', '/', $path))
            ->reject(fn ($path) => str_ends_with($path, 'views/order/edit-order-optional-service.blade.php'))
            ->map(fn ($path) => file_get_contents($path))
            ->implode("\n");

        $this->assertFileDoesNotExist(resource_path('views/order/edit-order-optional-service.blade.php'));
        $this->assertStringNotContainsString('edit-order-optional-service', $routeFile);
        $this->assertStringNotContainsString("view('order.edit-order-optional-service'", $orderController);
        $this->assertStringNotContainsString("@include('order.edit-order-optional-service')", $views);
    }

    public function test_wedding_order_views_are_routed_to_frontend_home_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrderWeddingController.php'));
        $legacyDetailWrapper = file_get_contents(resource_path('views/frontend/home/orders/details/legacy.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/weddings/edit.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/weddings/detail.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/edit-order-wedding.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/detail-order-wedding.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/edit-order-wedding.blade copy.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/order-wedding-detail.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.weddings.edit'", $controller);
        $this->assertStringContainsString("view('frontend.home.orders.weddings.detail'", $controller);
        $this->assertStringContainsString("@include('frontend.home.orders.weddings.detail')", $legacyDetailWrapper);
        $this->assertStringNotContainsString("view('order.edit-order-wedding'", $controller);
        $this->assertStringNotContainsString("view('order.detail-order-wedding'", $controller);
        $this->assertStringNotContainsString("@include('order.detail-order-wedding')", $legacyDetailWrapper);
    }

    public function test_wedding_order_legacy_partials_are_sourced_from_frontend_home_structure(): void
    {
        $backupForm = file_get_contents(resource_path('views/frontend/home/orders/weddings/legacy/order-weddings-backup.blade.php'));
        $partials = [
            'order_wedding_decoration',
            'order_wedding_dinner_venue',
            'order_wedding_documentation',
            'order_wedding_entertainment',
            'order_wedding_fixed_service',
            'order_wedding_makeup',
            'order_wedding_other',
            'order_wedding_room',
            'order_wedding_transport',
            'order_wedding_venues',
        ];

        foreach ($partials as $partial) {
            $this->assertFileExists(resource_path("views/frontend/home/orders/weddings/partials/{$partial}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/order/{$partial}.blade.php"));
            $this->assertStringContainsString("@include('frontend.home.orders.weddings.partials.{$partial}')", $backupForm);
            $this->assertStringNotContainsString("@include('order.{$partial}')", $backupForm);
        }
    }

    public function test_service_order_detail_views_are_sourced_from_frontend_home_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $legacyWrapper = file_get_contents(resource_path('views/frontend/home/orders/details/legacy.blade.php'));
        $tourModern = file_get_contents(resource_path('views/frontend/home/orders/details/tour-modern.blade.php'));
        $transportModern = file_get_contents(resource_path('views/frontend/home/orders/details/transport-modern.blade.php'));
        $paymentStatus = file_get_contents(resource_path('views/partials/user-order-payment-status.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/details/legacy.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/details/activity.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/details/tour-modern.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/details/tour-legacy.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/details/villa.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/details/transport-modern.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/details/transport-legacy.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/frontend/orders/detail-order-tour-modern.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/frontend/orders/detail-order-tour.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/frontend/orders/detail-order-transport-modern.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/user-detail-order.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/detail-order-activity.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/detail-order-villa.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/detail-order-transport.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/detail-order-tour.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.details.legacy'", $controller);
        $this->assertStringContainsString("view('frontend.home.orders.details.tour-modern'", $controller);
        $this->assertStringNotContainsString("view('order.user-detail-order'", $controller);
        $this->assertStringNotContainsString("view('frontend.orders.detail-order-tour-modern'", $controller);
        $this->assertStringContainsString("@include('frontend.home.orders.details.partials.hotel-detail-modern')", $legacyWrapper);
        $this->assertStringContainsString("@include('frontend.home.orders.details.villa')", $legacyWrapper);
        $this->assertStringContainsString("@include('frontend.home.orders.details.activity')", $legacyWrapper);
        $this->assertStringContainsString("@include('frontend.home.orders.details.tour-modern')", $legacyWrapper);
        $this->assertStringContainsString("@include('frontend.home.orders.details.transport-modern')", $legacyWrapper);
        $this->assertStringNotContainsString("@include('frontend.orders.detail-order-tour-modern')", $legacyWrapper);
        $this->assertStringNotContainsString("@include('frontend.orders.detail-order-transport-modern')", $legacyWrapper);
        $this->assertStringContainsString("@include('frontend.home.orders.details.partials.invoice-action-buttons'", $tourModern);
        $this->assertStringContainsString("@include('frontend.home.orders.details.partials.invoice-action-buttons'", $transportModern);
        $this->assertStringContainsString("@include('frontend.home.orders.details.partials.invoice-action-buttons'", $paymentStatus);
    }

    public function test_service_order_detail_shared_partials_are_sourced_from_frontend_home_structure(): void
    {
        $partials = [
            'hotel-detail-modern',
            'hotel-detail-modern-addons',
            'hotel-detail-modern-modals',
            'hotel-detail-modern-price',
            'hotel-detail-modern-sidebar',
            'invoice-action-buttons',
            'invoice-preview-modal',
            'invoice-preview-modal-compact',
            'legacy-order-payment-sidebar',
        ];

        foreach ($partials as $partial) {
            $this->assertFileExists(resource_path("views/frontend/home/orders/details/partials/{$partial}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/order/partials/{$partial}.blade.php"));
        }

        $detailFiles = collect([
            resource_path('views/frontend/home/orders/details/activity.blade.php'),
            resource_path('views/frontend/home/orders/details/villa.blade.php'),
            resource_path('views/frontend/home/orders/details/transport-legacy.blade.php'),
            resource_path('views/frontend/home/orders/details/partials/hotel-detail-modern.blade.php'),
            resource_path('views/frontend/home/orders/details/partials/hotel-detail-modern-sidebar.blade.php'),
            resource_path('views/frontend/home/orders/details/partials/invoice-action-buttons.blade.php'),
            resource_path('views/frontend/home/orders/details/partials/legacy-order-payment-sidebar.blade.php'),
        ])->map(fn ($path) => file_get_contents($path))->implode("\n");

        $this->assertStringContainsString('frontend.home.orders.details.partials', $detailFiles);
        $this->assertStringNotContainsString('order.partials.', $detailFiles);
    }

    public function test_admin_order_helper_views_are_sourced_from_backend_operations_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrdersAdminController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $indexView = file_get_contents(resource_path('views/admin/ordersadmin.blade.php'));
        $indexScss = file_get_contents(resource_path('backend/scss/operations/orders-admin/_index.scss'));
        $detailView = file_get_contents(resource_path('views/admin/ordersadmindetail.blade.php'));
        $detailScss = file_get_contents(resource_path('backend/scss/operations/orders-admin/_detail.scss'));

        $this->assertFileExists(resource_path('views/backend/operations/orders/actions/add-additional-services.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/orders/actions/edit-airport-shuttle.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/add-additional-services.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/add-order-itinerary.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/backend/operations/orders/actions/add-order-itinerary.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/edit-airport-shuttle.blade.php'));
        $this->assertStringContainsString("view('backend.operations.orders.actions.add-additional-services'", $controller);
        $this->assertStringContainsString("view('backend.operations.orders.actions.edit-airport-shuttle'", $controller);
        $this->assertStringNotContainsString("view('order.add-additional-services'", $controller);
        $this->assertStringNotContainsString("view('order.add-order-itinerary'", $controller);
        $this->assertStringNotContainsString("view('backend.operations.orders.actions.add-order-itinerary'", $controller);
        $this->assertStringNotContainsString('admin-edit-order-itinerary', $routes);
        $this->assertStringNotContainsString("view('order.edit-airport-shuttle'", $controller);
        $this->assertStringContainsString("Route::get('/orders-admin'", $routes);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--4', $indexView);
        $this->assertStringContainsString('backend-kpi-card backend-kpi-card--', $indexView);
        $this->assertStringContainsString('backend-feedback', $indexView);
        $this->assertStringContainsString('backend-alert backend-alert--', $indexView);
        $this->assertStringContainsString('backend-filter-panel orders-admin-filter', $indexView);
        $this->assertStringContainsString('backend-filter-label', $indexView);
        $this->assertStringContainsString('backend-filter-search', $indexView);
        $this->assertStringContainsString('backend-filter-control', $indexView);
        $this->assertStringContainsString('backend-status-badge', $indexView);
        $this->assertStringContainsString('backend-panel orders-admin-panel', $indexView);
        $this->assertStringContainsString('backend-section-header', $indexView);
        $this->assertStringContainsString('backend-section-header__label', $indexView);
        $this->assertStringNotContainsString('class="orders-admin-panel"', $indexView);
        $this->assertStringNotContainsString('orders-admin-panel__header', $indexView);
        $this->assertStringNotContainsString('orders-admin-summary', $indexView);
        $this->assertStringNotContainsString('.orders-admin-summary', $indexScss);
        $this->assertStringNotContainsString('.orders-admin-alert', $indexScss);
        $this->assertStringNotContainsString('.orders-admin-status--', $indexScss);
        $this->assertStringNotContainsString('.orders-admin-panel__header', $indexScss);
        $this->assertStringContainsString("view('admin.ordersadmindetail'", $controller);
        $this->assertStringContainsString("Route::get('/orders-admin-{id}'", $routes);
        $this->assertStringContainsString('<x-backend.page-hero class="orders-admin-detail-hero"', $detailView);
        $this->assertStringContainsString('class="backend-page-primary-action"', $detailView);
        $this->assertStringContainsString('backend-panel orders-admin-detail-panel', $detailView);
        $this->assertStringContainsString('backend-section-header', $detailView);
        $this->assertStringContainsString('backend-section-header__label', $detailView);
        $this->assertStringNotContainsString('class="orders-admin-detail-panel"', $detailView);
        $this->assertStringNotContainsString('orders-admin-detail-panel__header', $detailView);
        $this->assertStringContainsString("route('orders-admin')", $detailView);
        $this->assertStringContainsString('orders-admin-detail-toolbar__meta', $detailView);
        $this->assertStringContainsString('orders-admin-detail-status', $detailView);
        $this->assertStringNotContainsString('.orders-admin-detail-hero h1', $detailScss);
        $this->assertStringNotContainsString('.orders-admin-detail-hero p', $detailScss);
        $this->assertStringNotContainsString('.orders-admin-detail-panel__header', $detailScss);
    }

    public function test_reservation_helper_views_are_sourced_from_backend_operations_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/ReservationController.php'));
        $actions = [
            'add-order' => 'add_rsv_order',
            'add-transport' => 'add_rsv_transport',
            'add-activity-tour' => 'add_rsv_activity_tour',
        ];

        foreach ($actions as $target => $legacy) {
            $this->assertFileExists(resource_path("views/backend/operations/reservations/actions/{$target}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/form/{$legacy}.blade.php"));
        }

        $this->assertStringContainsString("view('backend.operations.reservations.actions.add-order'", $controller);
        $this->assertStringContainsString("view('backend.operations.reservations.actions.add-transport'", $controller);
        $this->assertStringContainsString("view('backend.operations.reservations.actions.add-activity-tour'", $controller);
        $this->assertStringNotContainsString("view('form.add_rsv_order'", $controller);
        $this->assertStringNotContainsString("view('form.add_rsv_transport'", $controller);
        $this->assertStringNotContainsString("view('form.add_rsv_activity_tour'", $controller);
        $this->assertFileDoesNotExist(resource_path('views/backend/operations/reservations/actions/add-itinerary.blade.php'));
        $this->assertStringNotContainsString("view('backend.operations.reservations.actions.add-itinerary'", $controller);
        $this->assertStringNotContainsString("view('form.add_rsv_itinerary'", $controller);
    }

    public function test_transport_admin_forms_are_sourced_from_backend_operations_structure(): void
    {
        $adminController = file_get_contents(app_path('Http/Controllers/TransportsAdminController.php'));
        $publicController = file_get_contents(app_path('Http/Controllers/TransportsController.php'));

        $this->assertFileExists(resource_path('views/backend/operations/transports/forms/create.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/transports/forms/edit.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/transports/forms/gallery-edit.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/transportadd.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/transportedit.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/transportgaleryedit.blade.php'));
        $this->assertStringContainsString("view('backend.operations.transports.forms.create'", $adminController);
        $this->assertStringContainsString("view('backend.operations.transports.forms.edit'", $adminController);
        $this->assertStringContainsString("view('backend.operations.transports.forms.gallery-edit'", $publicController);
        $this->assertStringNotContainsString("view('form.transportadd'", $adminController);
        $this->assertStringNotContainsString("view('form.transportedit'", $adminController);
        $this->assertStringNotContainsString("view('form.transportgaleryedit'", $publicController);
    }

    public function test_guides_admin_is_sourced_from_backend_operations_structure_and_assets(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/GuideController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $view = file_get_contents(resource_path('views/backend/operations/guides/index.blade.php'));
        $formPartial = file_get_contents(resource_path('views/backend/operations/guides/partials/form.blade.php'));
        $legacyWrapper = file_get_contents(resource_path('views/guides/guides-admin.blade.php'));
        $scss = file_get_contents(resource_path('backend/scss/operations/guides/_index.scss'));
        $js = file_get_contents(resource_path('backend/js/operations/guides/index.js'));
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('views/backend/operations/guides/index.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/guides/partials/form.blade.php'));
        $this->assertFileExists(resource_path('backend/js/operations/guides/index.js'));
        $this->assertFileExists(resource_path('backend/scss/operations/guides/index-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/operations/guides/_index.scss'));
        $this->assertStringContainsString("view('backend.operations.guides.index'", $controller);
        $this->assertStringNotContainsString("view('guides.guides-admin'", $controller);
        $this->assertStringContainsString("@include('backend.operations.guides.index')", $legacyWrapper);
        $this->assertStringContainsString("Route::get('/guides-admin'", $routes);
        $this->assertStringContainsString("->name('guides-admin.index')", $routes);
        $this->assertStringContainsString("->name('destroy-guide')", $routes);
        $this->assertStringContainsString("route('guides-admin.index')", $controller);
        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('backend-page-primary-action', $view);
        $this->assertStringContainsString('backend-page-toolbar guides-admin-toolbar', $view);
        $this->assertStringContainsString("route('admin.panel-main.view')", $view);
        $this->assertStringContainsString('backend-feedback', $view);
        $this->assertStringContainsString('backend-alert backend-alert--', $view);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--4', $view);
        $this->assertStringContainsString('backend-kpi-card backend-kpi-card--', $view);
        $this->assertStringContainsString('backend-filter-panel guides-admin-filter', $view);
        $this->assertStringContainsString('backend-filter-field', $view);
        $this->assertStringContainsString('backend-filter-search', $view);
        $this->assertStringContainsString('backend-panel guides-admin-panel', $view);
        $this->assertStringContainsString('backend-section-header guides-admin-panel__heading', $view);
        $this->assertStringContainsString('backend-table guides-admin-table', $view);
        $this->assertStringContainsString('backend-table-card-list guides-admin-card-list', $view);
        $this->assertStringContainsString('backend-status-badge', $view);
        $this->assertStringContainsString('backend-modal guides-admin-modal', $view);
        $this->assertStringContainsString('backend-modal__header', $view);
        $this->assertStringContainsString('backend-modal__body', $view);
        $this->assertStringContainsString('backend-modal__footer', $view);
        $this->assertStringContainsString("route('create-guide')", $view);
        $this->assertStringContainsString("route('edit-guide'", $view);
        $this->assertStringContainsString("route('destroy-guide'", $view);
        $this->assertStringContainsString("backend.operations.guides.partials.form", $view);
        $this->assertStringContainsString('guides-admin-form-grid', $formPartial);
        $this->assertStringContainsString('data-guide-filter="name"', $view);
        $this->assertStringContainsString('data-guide-row', $view);
        $this->assertStringContainsString('data-guide-delete', $view);
        $this->assertStringContainsString('data-guide-filter="name"', $js);
        $this->assertStringContainsString('data-guide-delete', $js);
        $this->assertStringNotContainsString('<script>', $view);
        $this->assertStringNotContainsString('card-box', $view);
        $this->assertStringNotContainsString('card-box', $formPartial);
        $this->assertStringNotContainsString('style=', $view);
        $this->assertStringNotContainsString('class="data-table table stripe hover"', $view);
        $this->assertStringContainsString('backend-filter-panel guides-admin-filter', $view);
        $this->assertStringNotContainsString('.guides-admin-filter', $scss);
        $this->assertStringContainsString('.guides-admin-detail-grid', $scss);
        $this->assertStringContainsString('grid-template-columns: minmax(0, 1fr);', $scss);
        $this->assertStringContainsString("resources/backend/js/operations/guides/index.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/operations/guides/index-entry.scss", $mix);
    }

    public function test_drivers_admin_is_sourced_from_backend_operations_structure_and_assets(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/DriversController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $view = file_get_contents(resource_path('views/backend/operations/drivers/index.blade.php'));
        $formPartial = file_get_contents(resource_path('views/backend/operations/drivers/partials/form.blade.php'));
        $legacyWrapper = file_get_contents(resource_path('views/drivers/drivers-admin.blade.php'));
        $scss = file_get_contents(resource_path('backend/scss/operations/drivers/_index.scss'));
        $js = file_get_contents(resource_path('backend/js/operations/drivers/index.js'));
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('views/backend/operations/drivers/index.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/drivers/partials/form.blade.php'));
        $this->assertFileExists(resource_path('backend/js/operations/drivers/index.js'));
        $this->assertFileExists(resource_path('backend/scss/operations/drivers/index-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/operations/drivers/_index.scss'));
        $this->assertStringContainsString("view('backend.operations.drivers.index'", $controller);
        $this->assertStringNotContainsString("view('drivers.drivers-admin'", $controller);
        $this->assertStringContainsString("@include('backend.operations.drivers.index')", $legacyWrapper);
        $this->assertStringContainsString("Route::get('/drivers-admin'", $routes);
        $this->assertStringContainsString("->name('drivers-admin.index')", $routes);
        $this->assertStringContainsString("->name('admin.driver.destroy')", $routes);
        $this->assertStringContainsString("route('drivers-admin.index')", $controller);
        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('backend-page-primary-action', $view);
        $this->assertStringContainsString('backend-page-toolbar drivers-admin-toolbar', $view);
        $this->assertStringContainsString("route('admin.panel-main.view')", $view);
        $this->assertStringContainsString('backend-feedback', $view);
        $this->assertStringContainsString('backend-alert backend-alert--', $view);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--4', $view);
        $this->assertStringContainsString('backend-kpi-card backend-kpi-card--', $view);
        $this->assertStringContainsString('backend-filter-panel drivers-admin-filter', $view);
        $this->assertStringContainsString('backend-filter-field', $view);
        $this->assertStringContainsString('backend-filter-search', $view);
        $this->assertStringContainsString('backend-panel drivers-admin-panel', $view);
        $this->assertStringContainsString('backend-section-header drivers-admin-panel__heading', $view);
        $this->assertStringContainsString('backend-table drivers-admin-table', $view);
        $this->assertStringContainsString('backend-table-card-list drivers-admin-card-list', $view);
        $this->assertStringContainsString('backend-status-badge', $view);
        $this->assertStringContainsString('backend-modal drivers-admin-modal', $view);
        $this->assertStringContainsString('backend-modal__header', $view);
        $this->assertStringContainsString('backend-modal__body', $view);
        $this->assertStringContainsString('backend-modal__footer', $view);
        $this->assertStringContainsString("route('admin.driver.create')", $view);
        $this->assertStringContainsString("route('admin.driver.edit'", $view);
        $this->assertStringContainsString("route('admin.driver.destroy'", $view);
        $this->assertStringContainsString("backend.operations.drivers.partials.form", $view);
        $this->assertStringContainsString('drivers-admin-form-grid', $formPartial);
        $this->assertStringContainsString('data-driver-filter="name"', $view);
        $this->assertStringContainsString('data-driver-row', $view);
        $this->assertStringContainsString('data-driver-delete', $view);
        $this->assertStringContainsString('data-driver-filter="name"', $js);
        $this->assertStringContainsString('data-driver-delete', $js);
        $this->assertStringNotContainsString('<script>', $view);
        $this->assertStringNotContainsString('card-box', $view);
        $this->assertStringNotContainsString('card-box', $formPartial);
        $this->assertStringNotContainsString('style=', $view);
        $this->assertStringNotContainsString('class="data-table table stripe hover"', $view);
        $this->assertStringContainsString('backend-filter-panel drivers-admin-filter', $view);
        $this->assertStringNotContainsString('.drivers-admin-filter', $scss);
        $this->assertStringContainsString('.drivers-admin-detail-grid', $scss);
        $this->assertStringContainsString('grid-template-columns: minmax(0, 1fr);', $scss);
        $this->assertStringContainsString("resources/backend/js/operations/drivers/index.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/operations/drivers/index-entry.scss", $mix);
    }

    public function test_transport_management_index_uses_backend_shared_structure_and_kpi(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/SpksController.php'));
        $routeFile = file_get_contents(base_path('routes/web.php'));
        $view = file_get_contents(resource_path('views/admin/transportmanagement/spks/index.blade.php'));
        $detailView = file_get_contents(resource_path('views/admin/transportmanagement/spks/detail-spk.blade.php'));
        $archivePartial = file_get_contents(resource_path('views/admin/transportmanagement/partials/spk-archive.blade.php'));
        $detailModalsPartial = file_get_contents(resource_path('views/admin/transportmanagement/spks/partials/detail-modals.blade.php'));
        $scss = file_get_contents(resource_path('backend/scss/operations/transport-management/_index.scss'));
        $detailScss = file_get_contents(resource_path('backend/scss/operations/transport-management/_detail.scss'));
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('views/admin/transportmanagement/spks/index.blade.php'));
        $this->assertFileExists(resource_path('views/admin/transportmanagement/partials/spk-archive.blade.php'));
        $this->assertFileExists(resource_path('backend/js/operations/transport-management/index.js'));
        $this->assertFileExists(resource_path('backend/scss/operations/transport-management/index-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/operations/transport-management/_index.scss'));
        $this->assertStringContainsString("view('admin.transportmanagement.spks.index'", $controller);
        $this->assertStringContainsString('statusSummary', $controller);
        $this->assertStringContainsString("Route::get('/transport-management'", $routeFile);
        $this->assertStringContainsString("->name('view.transport-management.index')", $routeFile);
        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('backend-page-primary-action', $view);
        $this->assertStringContainsString('backend-page-toolbar transport-management-toolbar', $view);
        $this->assertStringContainsString('backend-feedback', $view);
        $this->assertStringContainsString('backend-alert backend-alert--', $view);
        $this->assertStringContainsString('backend-status-badge', $view);
        $this->assertStringContainsString('backend-feedback', $detailView);
        $this->assertStringContainsString('backend-alert backend-alert--', $detailView);
        $this->assertStringContainsString('backend-status-badge', $detailView);
        $this->assertStringContainsString('backend-modal transport-spk-detail-modal', $detailView);
        $this->assertStringContainsString('backend-modal transport-management-modal', $archivePartial);
        $this->assertStringContainsString('backend-modal__header transport-management-modal__header', $archivePartial);
        $this->assertStringContainsString('backend-modal__body transport-management-modal__body', $archivePartial);
        $this->assertStringContainsString('backend-modal__footer transport-management-modal__footer', $archivePartial);
        $this->assertStringContainsString('backend-modal transport-spk-detail-modal', $detailModalsPartial);
        $this->assertStringContainsString('backend-modal__header transport-spk-detail-modal__header', $detailModalsPartial);
        $this->assertStringContainsString('backend-modal__body transport-spk-detail-modal__body', $detailModalsPartial);
        $this->assertStringContainsString('backend-modal__footer transport-spk-detail-modal__footer', $detailModalsPartial);
        $this->assertStringContainsString("route('admin.panel-main.view')", $view);
        $this->assertStringContainsString("mix('build/backend/css/operations/transport-management/index.css')", $view);
        $this->assertStringContainsString("mix('build/backend/js/operations/transport-management/index.js')", $view);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--4', $view);
        $this->assertStringContainsString('backend-kpi-card backend-kpi-card--', $view);
        $this->assertStringContainsString('backend-kpi-card__icon', $view);
        $this->assertStringNotContainsString('transport-management-stats', $view);
        $this->assertStringNotContainsString('class="transport-management-stat"', $view);
        $this->assertStringContainsString('backend-panel transport-management-panel', $view);
        $this->assertStringContainsString('backend-section-header transport-management-panel__heading', $view);
        $this->assertStringContainsString('backend-section-header__label', $view);
        $this->assertStringNotContainsString('transport-management-panel__header', $view);
        $this->assertStringNotContainsString('transport-management-eyebrow', $view);
        $this->assertStringContainsString('backend-panel transport-spk-detail-panel', $detailView);
        $this->assertStringContainsString('backend-section-header transport-spk-detail-panel__heading', $detailView);
        $this->assertStringContainsString('backend-section-header transport-spk-detail-section__heading', $detailView);
        $this->assertStringContainsString('backend-section-header__label', $detailView);
        $this->assertStringNotContainsString('transport-spk-detail-panel__header', $detailView);
        $this->assertStringNotContainsString('transport-spk-detail-section__header', $detailView);
        $this->assertStringContainsString('backend-table transport-management-table', $view);
        $this->assertStringContainsString('backend-table-card transport-management-card', $view);
        $this->assertStringContainsString('spkArchiveResults', $view);
        $this->assertStringContainsString('admin.transportmanagement.partials.spk-archive', $view);
        $this->assertStringContainsString('transport-management-archive-table', $archivePartial);
        $this->assertStringContainsString('modalContainer', $archivePartial);
        $this->assertStringContainsString('minmax(0, 1fr)', $scss);
        $this->assertStringContainsString('.transport-management-grid', $scss);
        $this->assertStringNotContainsString('.transport-management-stats', $scss);
        $this->assertStringNotContainsString('.transport-management-alert', $scss);
        $this->assertStringNotContainsString('.transport-management-status', $scss);
        $this->assertStringNotContainsString('.transport-management-modal .modal-content', $scss);
        $this->assertStringNotContainsString("background: #f8fbff;\n  padding: 18px;", $scss);
        $this->assertStringNotContainsString('.transport-management-panel__header', $scss);
        $this->assertStringNotContainsString('.transport-management-eyebrow', $scss);
        $this->assertStringNotContainsString('.transport-spk-detail-alert', $detailScss);
        $this->assertStringNotContainsString('.transport-spk-detail-status', $detailScss);
        $this->assertStringNotContainsString('.transport-spk-detail-modal .modal-content', $detailScss);
        $this->assertStringNotContainsString("background: #f8fbff;\n  padding: 18px;", $detailScss);
        $this->assertStringNotContainsString('.transport-spk-detail-panel__header', $detailScss);
        $this->assertStringNotContainsString('.transport-spk-detail-section__header', $detailScss);
        $this->assertStringContainsString("resources/backend/js/operations/transport-management/index.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/operations/transport-management/index-entry.scss", $mix);
    }

    public function test_activity_admin_forms_are_sourced_from_backend_operations_structure(): void
    {
        $adminController = file_get_contents(app_path('Http/Controllers/ActivitiesAdminController.php'));
        $publicController = file_get_contents(app_path('Http/Controllers/ActivitiesController.php'));

        $this->assertFileExists(resource_path('views/backend/operations/activities/forms/create.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/activities/forms/edit.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/activities/forms/gallery-edit.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/activityadd.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/activityedit.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/activitygaleryedit.blade.php'));
        $this->assertStringContainsString("view('backend.operations.activities.forms.create'", $adminController);
        $this->assertStringContainsString("view('backend.operations.activities.forms.edit'", $adminController);
        $this->assertStringContainsString("view('backend.operations.activities.forms.gallery-edit'", $publicController);
        $this->assertStringNotContainsString("view('form.activityadd'", $adminController);
        $this->assertStringNotContainsString("view('form.activityedit'", $adminController);
        $this->assertStringNotContainsString("view('form.activitygaleryedit'", $publicController);
    }

    public function test_hotel_admin_forms_are_sourced_from_backend_operations_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/HotelsAdminController.php'));
        $forms = [
            'create' => 'hoteladd',
            'edit' => 'hoteledit',
            'gallery-edit' => 'hotelgaleryedit',
            'add-normal-price' => 'hotel-add-normal-price',
            'normal-price-create' => 'hotel-add-normal-price',
            'add-promo' => 'hotelpromoadd',
            'promo-create' => 'hotelpromoadd',
            'promo-edit' => 'hotelpromoadd',
            'package-create' => 'hotelpackageadd',
            'package-edit' => 'hotelpackageedit',
            'room-create' => 'roomadd',
            'room-edit' => 'roomedit',
        ];

        foreach ($forms as $target => $legacy) {
            $this->assertFileExists(resource_path("views/backend/operations/hotels/forms/{$target}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/form/{$legacy}.blade.php"));
        }

        $this->assertFileExists(resource_path('views/backend/operations/hotels/modals/normal-price-edit.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/hotels/modals/additional-charge-form.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/backend/operations/hotels/forms/normal-price-edit.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/backend/operations/hotels/forms/additional-charge-create.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/backend/operations/hotels/forms/additional-charge-edit.blade.php'));

        $this->assertStringContainsString("view('backend.operations.hotels.forms.create'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.edit'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.gallery-edit'", $controller);
        $this->assertStringContainsString("@include('backend.operations.hotels.forms.normal-price-create')", file_get_contents(resource_path('views/backend/operations/hotels/forms/add-normal-price.blade.php')));
        $this->assertStringContainsString("@include('backend.operations.hotels.forms.promo-create')", file_get_contents(resource_path('views/backend/operations/hotels/forms/add-promo.blade.php')));
        $this->assertStringContainsString("view('backend.operations.hotels.forms.normal-price-create'", $controller);
        $this->assertStringNotContainsString("view('backend.operations.hotels.forms.normal-price-edit'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.promo-create'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.promo-edit'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.package-create'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.package-edit'", $controller);
        $this->assertStringNotContainsString("view('backend.operations.hotels.forms.additional-charge-create'", $controller);
        $this->assertStringNotContainsString("view('backend.operations.hotels.forms.additional-charge-edit'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.room-create'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.room-edit'", $controller);
        $this->assertStringNotContainsString("view('form.hoteladd'", $controller);
        $this->assertStringNotContainsString("view('form.hoteledit'", $controller);
        $this->assertStringNotContainsString("view('form.hotelgaleryedit'", $controller);
        $this->assertStringNotContainsString("view('form.hotel-add-normal-price'", $controller);
        $this->assertStringNotContainsString("view('form.hotelpromoadd'", $controller);
        $this->assertStringNotContainsString("view('form.hotelpackageadd'", $controller);
        $this->assertStringNotContainsString("view('form.hotelpackageedit'", $controller);
        $this->assertStringNotContainsString("view('form.additional-charge-add'", $controller);
        $this->assertStringNotContainsString("view('form.additional-charge-edit'", $controller);
        $this->assertStringNotContainsString("view('form.roomadd'", $controller);
        $this->assertStringNotContainsString("view('form.roomedit'", $controller);
    }

    public function test_hotel_admin_remaining_forms_and_contract_modal_use_backend_standard_assets(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/HotelsAdminController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $js = file_get_contents(resource_path('backend/js/operations/hotels/forms.js'));
        $scss = file_get_contents(resource_path('backend/scss/operations/hotels/_forms.scss'));
        $panelScss = file_get_contents(resource_path('backend/scss/components/_backend-panel.scss'));
        $formFiles = [
            'create.blade.php',
            'edit.blade.php',
            'gallery-edit.blade.php',
            'room-create.blade.php',
            'room-edit.blade.php',
        ];
        $formContent = collect($formFiles)
            ->map(fn ($file) => file_get_contents(resource_path("views/backend/operations/hotels/forms/{$file}")))
            ->implode("\n");
        $contractModal = file_get_contents(resource_path('views/backend/operations/hotels/modals/contract-preview.blade.php'));

        foreach ($formFiles as $file) {
            $view = file_get_contents(resource_path("views/backend/operations/hotels/forms/{$file}"));
            $this->assertStringContainsString("mix('build/backend/css/operations/hotels/forms.css')", $view, $file);
            $this->assertStringContainsString("mix('build/backend/js/operations/hotels/forms.js')", $view, $file);
            $this->assertStringContainsString('<x-backend.page-hero', $view, $file);
            $this->assertStringContainsString('backend-page-primary-action', $view, $file);
            $this->assertTrue(
                str_contains($view, 'backend-page-toolbar hotel-form-toolbar')
                || str_contains($view, '<x-backend.breadcrumb-toolbar'),
                $file
            );
            $this->assertStringContainsString('backend-panel', $view, $file);
            $this->assertStringContainsString('hotel-form-panel', $view, $file);
            $this->assertStringContainsString('backend-section-header', $view, $file);
            $this->assertStringContainsString('backend-section-header__label', $view, $file);
        }

        $createView = file_get_contents(resource_path('views/backend/operations/hotels/forms/create.blade.php'));
        $editView = file_get_contents(resource_path('views/backend/operations/hotels/forms/edit.blade.php'));
        $galleryView = file_get_contents(resource_path('views/backend/operations/hotels/forms/gallery-edit.blade.php'));
        $roomCreateView = file_get_contents(resource_path('views/backend/operations/hotels/forms/room-create.blade.php'));
        $roomEditView = file_get_contents(resource_path('views/backend/operations/hotels/forms/room-edit.blade.php'));
        $backendController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelAdminController.php'));
        $galleryController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelGalleryAdminController.php'));
        $galleryRequest = file_get_contents(app_path('Http/Requests/StoreHotelGalleryImagesRequest.php'));
        $roomController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelRoomAdminController.php'));
        $storeRoomRequest = file_get_contents(app_path('Http/Requests/StoreHotelRoomRequest.php'));
        $updateRoomRequest = file_get_contents(app_path('Http/Requests/UpdateHotelRoomRequest.php'));
        $updateRequest = file_get_contents(app_path('Http/Requests/UpdateHotelRequest.php'));

        $this->assertStringContainsString('Gate::any([\'posDev\', \'posAuthor\', \'posAdm\'])', $backendController);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.create')", $backendController);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.edit'", $backendController);
        $this->assertStringContainsString('User::find($hotel->author_id)', $backendController);
        $this->assertStringNotContainsString('return parent::view_add_hotel();', $backendController);
        $this->assertStringNotContainsString('return parent::view_edit_hotel($id);', $backendController);
        $this->assertStringContainsString('<x-backend.detail-layout class="hotel-create-layout">', $createView);
        $this->assertStringContainsString('backend-panel backend-form-panel hotel-form-panel', $createView);
        $this->assertStringContainsString('backend-form-panel__body', $createView);
        $this->assertStringContainsString('<x-slot name="side">', $createView);
        $this->assertStringContainsString('@canany([\'posDev\',\'posAuthor\',\'posAdm\'])', $createView);
        $this->assertStringContainsString('Cover / Media', $createView);
        $this->assertStringContainsString('Basic Information', $createView);
        $this->assertStringContainsString('Stay and Access', $createView);
        $this->assertStringContainsString('Content and Translations', $createView);
        $this->assertStringContainsString('Initial Status', $createView);
        $this->assertStringContainsString('Hotel Setup Guidance', $createView);
        $this->assertStringContainsString('Next Step Context', $createView);
        $this->assertStringContainsString('data-hotel-cover-input', $createView);
        $this->assertStringContainsString('data-hotel-cover-preview', $createView);
        $this->assertStringContainsString('backend-translation-group', $createView);
        $this->assertStringContainsString('backend-translation-grid', $createView);
        $this->assertStringContainsString('Traditional Chinese', $createView);
        $this->assertStringContainsString('Simplified Chinese', $createView);
        $this->assertStringContainsString('data-backend-richtext="true"', $createView);
        $this->assertStringContainsString('backend-form-actions hotel-form-actions', $createView);
        $this->assertStringContainsString('data-hotel-cover-input', $js);
        $this->assertStringContainsString('.hotel-form-cover-control', $scss);
        $this->assertStringContainsString('.hotel-form-cover-preview', $scss);
        $this->assertStringContainsString('.backend-form-panel', $panelScss);
        $this->assertStringContainsString('.backend-form-panel__body', $panelScss);
        $this->assertStringNotContainsString('name="author"', $createView);
        $this->assertStringNotContainsString('name="page"', $createView);
        $this->assertStringNotContainsString('name="initial_state"', $createView);
        $this->assertStringNotContainsString('@can(\'isAdmin\')', $createView);
        $this->assertStringNotContainsString('tab-inner-title', $createView);
        $this->assertStringNotContainsString('dropzone', $createView);
        $this->assertStringNotContainsString('class="row"', $createView);
        $this->assertStringNotContainsString('col-md-', $createView);

        $this->assertStringContainsString('<x-backend.detail-layout class="hotel-edit-layout">', $editView);
        $this->assertStringContainsString('backend-panel backend-form-panel hotel-form-panel', $editView);
        $this->assertStringContainsString('backend-form-panel__body', $editView);
        $this->assertStringContainsString('<x-slot name="side">', $editView);
        $this->assertStringContainsString('Cover / Media', $editView);
        $this->assertStringContainsString('Basic Information', $editView);
        $this->assertStringContainsString('Stay and Access', $editView);
        $this->assertStringContainsString('Content and Translations', $editView);
        $this->assertStringContainsString('Current Status', $editView);
        $this->assertStringContainsString('Metadata', $editView);
        $this->assertStringContainsString('Stay Summary', $editView);
        $this->assertStringContainsString('Related Management', $editView);
        $this->assertStringContainsString('name="status"', $editView);
        $this->assertStringContainsString('name="map"', $editView);
        $this->assertStringContainsString('data-hotel-cover-input', $editView);
        $this->assertStringContainsString('data-hotel-cover-preview', $editView);
        $this->assertStringContainsString('backend-translation-group', $editView);
        $this->assertStringContainsString('backend-translation-grid', $editView);
        $this->assertStringContainsString('Traditional Chinese', $editView);
        $this->assertStringContainsString('Simplified Chinese', $editView);
        $this->assertStringContainsString('data-backend-richtext="true"', $editView);
        $this->assertStringContainsString('backend-form-actions hotel-form-actions', $editView);
        $this->assertStringContainsString("route('admin.hotels.gallery.edit'", $editView);
        $this->assertStringContainsString("route('admin.hotels.prices.create'", $editView);
        $this->assertStringContainsString("route('admin.hotels.promos.create'", $editView);
        $this->assertStringContainsString("route('admin.hotels.packages.create'", $editView);
        $this->assertStringNotContainsString('name="author"', $editView);
        $this->assertStringNotContainsString('name="page"', $editView);
        $this->assertStringNotContainsString('admin.usd-rate', $editView);
        $this->assertStringNotContainsString('@can(\'isAdmin\')', $editView);
        $this->assertStringNotContainsString('tab-inner-title', $editView);
        $this->assertStringNotContainsString('dropzone', $editView);
        $this->assertStringNotContainsString('preview-cover', $editView);
        $this->assertStringNotContainsString('class="row"', $editView);
        $this->assertStringNotContainsString('col-md-', $editView);
        $this->assertStringContainsString("'map' => ['required', 'string']", $updateRequest);
        $this->assertStringContainsString("'status' => ['required', 'in:Active,Draft,Archived']", $updateRequest);

        $this->assertStringContainsString('<x-backend.detail-layout class="hotel-gallery-layout">', $galleryView);
        $this->assertStringContainsString('<x-backend.breadcrumb-toolbar', $galleryView);
        $this->assertStringContainsString('Current Gallery', $galleryView);
        $this->assertStringContainsString('Add New Images', $galleryView);
        $this->assertStringContainsString('Hotel Context', $galleryView);
        $this->assertStringContainsString('Media Guidance', $galleryView);
        $this->assertStringContainsString('Related Actions', $galleryView);
        $this->assertStringContainsString("route('admin.hotels.gallery.store'", $galleryView);
        $this->assertStringContainsString("route('admin.hotels.images.destroy', [\$hotels->id, \$img->id])", $galleryView);
        $this->assertStringContainsString('data-hotel-gallery-input', $galleryView);
        $this->assertStringContainsString('data-hotel-gallery-preview', $galleryView);
        $this->assertStringNotContainsString("route('func.hotel.edit'", $galleryView);
        $this->assertStringNotContainsString('name="name"', $galleryView);
        $this->assertStringNotContainsString('name="region"', $galleryView);
        $this->assertStringNotContainsString('name="description"', $galleryView);
        $this->assertStringNotContainsString('dropzone', $galleryView);
        $this->assertStringNotContainsString('images-preview-div', $galleryView);
        $this->assertStringContainsString('StoreHotelGalleryImagesRequest', $galleryController);
        $this->assertStringContainsString('withCount(\'images\')', $galleryController);
        $this->assertStringContainsString('$hotel->images()->create', $galleryController);
        $this->assertStringContainsString('$hotel->images()->whereKey($imageId)->firstOrFail()', $galleryController);
        $this->assertStringContainsString('DB::transaction', $galleryController);
        $this->assertStringContainsString('deleteGalleryImage($fileName)', $galleryController);
        $this->assertStringContainsString("'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096']", $galleryRequest);
        $this->assertStringContainsString('data-hotel-gallery-input', $js);
        $this->assertStringContainsString('.hotel-gallery-grid', $scss);
        $this->assertStringContainsString('.hotel-gallery-card', $scss);
        $this->assertStringContainsString('.hotel-gallery-upload-preview', $scss);

        $this->assertStringContainsString('<x-backend.detail-layout>', $roomCreateView);
        $this->assertStringContainsString('backend-panel backend-form-panel hotel-form-panel', $roomCreateView);
        $this->assertStringContainsString('backend-form-panel__body', $roomCreateView);
        $this->assertStringContainsString('<x-slot name="side">', $roomCreateView);
        $this->assertStringContainsString('Cover / Media', $roomCreateView);
        $this->assertStringContainsString('Basic Information', $roomCreateView);
        $this->assertStringContainsString('Occupancy and Inventory', $roomCreateView);
        $this->assertStringContainsString('Content and Translations', $roomCreateView);
        $this->assertStringContainsString('Hotel Context', $roomCreateView);
        $this->assertStringContainsString('Initial State', $roomCreateView);
        $this->assertStringContainsString('Inventory Guidance', $roomCreateView);
        $this->assertStringContainsString('Occupancy Guidance', $roomCreateView);
        $this->assertStringContainsString('Next Step', $roomCreateView);
        $this->assertStringContainsString('backend-panel backend-detail-side-card hotel-room-create-context-panel', $roomCreateView);
        $this->assertStringContainsString('backend-detail-side-card__body', $roomCreateView);
        $this->assertStringContainsString('backend-detail-side-actions', $roomCreateView);
        $this->assertStringContainsString('name="hotels_id"', $roomCreateView);
        $this->assertStringContainsString('name="hotel_context"', $roomCreateView);
        $this->assertStringContainsString('data-hotel-cover-input', $roomCreateView);
        $this->assertStringContainsString('data-hotel-cover-preview', $roomCreateView);
        $this->assertStringContainsString('data-hotel-autocomplete="room-view"', $roomCreateView);
        $this->assertStringContainsString('data-hotel-autocomplete="bed-type"', $roomCreateView);
        $this->assertStringContainsString('backend-translation-group', $roomCreateView);
        $this->assertStringContainsString('backend-translation-grid', $roomCreateView);
        $this->assertStringContainsString('Traditional Chinese', $roomCreateView);
        $this->assertStringContainsString('Simplified Chinese', $roomCreateView);
        $this->assertStringContainsString('data-backend-richtext="true"', $roomCreateView);
        $this->assertStringNotContainsString('room-create-layout', $roomCreateView);
        $this->assertStringNotContainsString('name="author"', $roomCreateView);
        $this->assertStringNotContainsString('admin.usd-rate', $roomCreateView);
        $this->assertStringNotContainsString('@can(\'isAdmin\')', $roomCreateView);
        $this->assertStringNotContainsString('tab-inner-title', $roomCreateView);
        $this->assertStringNotContainsString('dropzone', $roomCreateView);
        $this->assertStringNotContainsString('cover-preview-div', $roomCreateView);
        $this->assertStringNotContainsString('<aside class="backend-detail-side"', $roomCreateView);
        $this->assertStringNotContainsString('class="row"', $roomCreateView);
        $this->assertStringNotContainsString('col-md-', $roomCreateView);
        $this->assertStringNotContainsString('style=', $roomCreateView);
        $this->assertStringNotContainsString('onclick', $roomCreateView);
        $this->assertStringContainsString('Gate::any([\'posDev\', \'posAuthor\', \'posAdm\'])', $roomController);
        $this->assertStringContainsString('Crypt::encryptString', $roomController);
        $this->assertStringContainsString('DB::transaction', $roomController);
        $this->assertStringContainsString('$request->resolvedHotelId()', $roomController);
        $this->assertStringNotContainsString('return parent::view_add_room($id);', $roomController);
        $this->assertStringContainsString("'hotel_context' => ['required', 'string']", $storeRoomRequest);
        $this->assertStringContainsString("return Gate::any(['posDev', 'posAuthor', 'posAdm']);", $storeRoomRequest);
        $this->assertStringContainsString("'capacity_adult' => ['required', 'integer', 'min:1']", $storeRoomRequest);
        $this->assertStringContainsString('public function resolvedHotelId(): ?int', $storeRoomRequest);
        $this->assertStringContainsString('Crypt::decryptString', $storeRoomRequest);
        $this->assertStringContainsString('The selected Hotel does not match this Room form context.', $storeRoomRequest);

        $this->assertStringContainsString('<x-backend.detail-layout>', $roomEditView);
        $this->assertStringContainsString('backend-panel backend-form-panel hotel-form-panel', $roomEditView);
        $this->assertStringContainsString('backend-form-panel__body', $roomEditView);
        $this->assertStringContainsString('<x-slot name="side">', $roomEditView);
        $this->assertStringContainsString('Cover / Media', $roomEditView);
        $this->assertStringContainsString('Basic Information', $roomEditView);
        $this->assertStringContainsString('Occupancy and Inventory', $roomEditView);
        $this->assertStringContainsString('Content and Translations', $roomEditView);
        $this->assertStringContainsString('Current Status', $roomEditView);
        $this->assertStringContainsString('Hotel Context', $roomEditView);
        $this->assertStringContainsString('Room Metadata', $roomEditView);
        $this->assertStringContainsString('Occupancy Summary', $roomEditView);
        $this->assertStringContainsString('Inventory Summary', $roomEditView);
        $this->assertStringContainsString('Related Management', $roomEditView);
        $this->assertStringContainsString('backend-panel backend-detail-side-card hotel-room-edit-context-panel', $roomEditView);
        $this->assertStringContainsString('backend-detail-side-card__body', $roomEditView);
        $this->assertStringContainsString('backend-detail-side-actions', $roomEditView);
        $this->assertStringContainsString('name="status"', $roomEditView);
        $this->assertStringContainsString('data-hotel-cover-input', $roomEditView);
        $this->assertStringContainsString('data-hotel-cover-preview', $roomEditView);
        $this->assertStringContainsString('data-hotel-autocomplete="room-view"', $roomEditView);
        $this->assertStringContainsString('data-hotel-autocomplete="bed-type"', $roomEditView);
        $this->assertStringContainsString('backend-translation-group', $roomEditView);
        $this->assertStringContainsString('backend-translation-grid', $roomEditView);
        $this->assertStringContainsString('Traditional Chinese', $roomEditView);
        $this->assertStringContainsString('Simplified Chinese', $roomEditView);
        $this->assertStringContainsString('data-backend-richtext="true"', $roomEditView);
        $this->assertStringNotContainsString('room-edit-layout', $roomEditView);
        $this->assertStringNotContainsString('name="author"', $roomEditView);
        $this->assertStringNotContainsString('name="page"', $roomEditView);
        $this->assertStringNotContainsString('name="hotels_id"', $roomEditView);
        $this->assertStringNotContainsString('admin.usd-rate', $roomEditView);
        $this->assertStringNotContainsString('@can(\'isAdmin\')', $roomEditView);
        $this->assertStringNotContainsString('tab-inner-title', $roomEditView);
        $this->assertStringNotContainsString('dropzone', $roomEditView);
        $this->assertStringNotContainsString('preview-cover', $roomEditView);
        $this->assertStringNotContainsString('cover-preview-div', $roomEditView);
        $this->assertStringNotContainsString('<aside class="backend-detail-side"', $roomEditView);
        $this->assertStringNotContainsString('class="row"', $roomEditView);
        $this->assertStringNotContainsString('col-md-', $roomEditView);
        $this->assertStringNotContainsString('style=', $roomEditView);
        $this->assertStringNotContainsString('onclick', $roomEditView);
        $this->assertStringContainsString('HotelRoom::with(\'hotels\')->findOrFail($id)', $roomController);
        $this->assertStringContainsString("'statusOptions' => ['Active', 'Draft', 'Archived']", $roomController);
        $this->assertStringContainsString('$hotelId = $room->hotels_id', $roomController);
        $this->assertStringContainsString("'hotels_id' => \$hotelId", $roomController);
        $this->assertStringNotContainsString('return parent::view_edit_room($id);', $roomController);
        $this->assertStringContainsString("return Gate::any(['posDev', 'posAuthor', 'posAdm']);", $updateRoomRequest);
        $this->assertStringContainsString("'capacity_adult' => ['required', 'integer', 'min:1']", $updateRoomRequest);
        $this->assertStringContainsString("'inventory' => ['required', 'integer', 'min:0']", $updateRoomRequest);
        $this->assertStringContainsString("'status' => ['required', 'in:Active,Draft,Archived']", $updateRoomRequest);
        $this->assertStringNotContainsString("'hotels_id' =>", $updateRoomRequest);

        $this->assertStringContainsString('backend-feedback hotel-form-feedback', $formContent);
        $this->assertStringContainsString('backend-alert backend-alert--', $formContent);
        $this->assertStringContainsString('backend-status-badge backend-status-badge--', $formContent);
        $this->assertStringContainsString("route('admin.panel-main.view')", $formContent);
        $this->assertStringContainsString("route('admin.hotels.index')", $formContent);
        $this->assertStringContainsString("route('admin.hotels.show'", $formContent);
        $this->assertStringContainsString("route('admin.hotel.store')", $formContent);
        $this->assertStringContainsString("route('func.hotel.edit'", $formContent);
        $this->assertStringContainsString("route('admin.hotels.gallery.store'", $formContent);
        $this->assertStringContainsString("route('admin.hotels.room.store')", $formContent);
        $this->assertStringContainsString("route('admin.hotels.room.update'", $formContent);
        $this->assertStringContainsString("route('admin.hotels.images.destroy'", $formContent);
        $this->assertStringContainsString("name('hotels.gallery.store')", $routes);
        $this->assertStringContainsString("name('hotels.images.destroy')", $routes);
        $this->assertStringContainsString('data-hotel-autocomplete="room-view"', $formContent);
        $this->assertStringContainsString('data-hotel-autocomplete="bed-type"', $formContent);
        $this->assertStringContainsString('[data-hotel-autocomplete]', $js);
        $this->assertStringContainsString('.hotel-form-gallery-grid', $scss);
        $this->assertStringContainsString('.hotel-form-suggestions', $scss);
        $this->assertStringContainsString('backend-modal', $contractModal);
        $this->assertStringContainsString('backend-modal__header', $contractModal);
        $this->assertStringContainsString('backend-modal__body', $contractModal);
        $this->assertStringContainsString('backend-modal__footer', $contractModal);
        $this->assertStringNotContainsString('hotelPackageAddModal', $contractModal);

        foreach ([$formContent, $contractModal] as $content) {
            $this->assertStringNotContainsString('<script>', $content);
            $this->assertStringNotContainsString('onkeyup=', $content);
            $this->assertStringNotContainsString('card-box', $content);
            $this->assertStringNotContainsString('style=', $content);
            $this->assertStringNotContainsString('btn btn-', $content);
            $this->assertStringNotContainsString('href="/detail-hotel-', $content);
            $this->assertStringNotContainsString('href="/edit-hotel-', $content);
            $this->assertStringNotContainsString('href="/edit-room-', $content);
            $this->assertStringNotContainsString('action="/fadd', $content);
            $this->assertStringNotContainsString('action="/fedit', $content);
            $this->assertStringNotContainsString('action="/fupdate', $content);
            $this->assertStringNotContainsString('action="/delete', $content);
            $this->assertStringNotContainsString('action="/remove', $content);
        }

        $this->assertStringNotContainsString('redirect("/detail-hotel-', $controller);
        $this->assertStringNotContainsString('redirect("/hotels-admin"', $controller);
        $this->assertStringContainsString('redirectToHotelDetail', $controller);
        $this->assertStringContainsString('redirectToHotelsIndexWithError', $controller);
    }

    public function test_hotel_normal_price_routes_are_decomposed_to_dedicated_controller(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $controller = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelNormalPriceAdminController.php'));

        $this->assertFileExists(app_path('Http/Controllers/Backend/Operations/Hotels/HotelNormalPriceAdminController.php'));
        $this->assertStringContainsString('use App\Http\Controllers\Backend\Operations\Hotels\HotelNormalPriceAdminController;', $routes);
        $this->assertStringContainsString("[HotelNormalPriceAdminController::class,'create']", $routes);
        $this->assertStringContainsString("[HotelNormalPriceAdminController::class,'store']", $routes);
        $this->assertStringContainsString("[HotelNormalPriceAdminController::class,'update']", $routes);
        $this->assertStringContainsString("[HotelNormalPriceAdminController::class,'destroy']", $routes);
        $this->assertStringNotContainsString("[HotelNormalPriceAdminController::class,'edit']", $routes);
        $this->assertStringNotContainsString('/edit-hotel-price/{id}', $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class,'view_add_hotel_price']", $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class,'view_edit_hotel_price']", $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class,'func_add_price']", $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class,'func_edit_price']", $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class,'destroy_price']", $routes);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.normal-price-create'", $controller);
        $this->assertStringNotContainsString("view('backend.operations.hotels.forms.normal-price-edit'", $controller);
        $this->assertStringContainsString('redirectToHotelDetail', $controller);
        $this->assertStringContainsString('redirectToHotelsIndexWithError', $controller);
    }

    public function test_hotel_promo_routes_are_decomposed_to_dedicated_controller(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $controller = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelPromoAdminController.php'));

        $this->assertFileExists(app_path('Http/Controllers/Backend/Operations/Hotels/HotelPromoAdminController.php'));
        $this->assertStringContainsString('use App\Http\Controllers\Backend\Operations\Hotels\HotelPromoAdminController;', $routes);
        $this->assertStringContainsString("[HotelPromoAdminController::class,'create']", $routes);
        $this->assertStringContainsString("[HotelPromoAdminController::class,'edit']", $routes);
        $this->assertStringContainsString("[HotelPromoAdminController::class,'store']", $routes);
        $this->assertStringContainsString("[HotelPromoAdminController::class,'update']", $routes);
        $this->assertStringContainsString("[HotelPromoAdminController::class,'updateStatus']", $routes);
        $this->assertStringContainsString("[HotelPromoAdminController::class, 'destroy']", $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class,'view_add_promo']", $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class,'view_edit_promo']", $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class,'func_add_promo']", $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class,'func_edit_promo']", $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class, 'destroy_promo']", $routes);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.promo-create'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.promo-edit'", $controller);
        $this->assertStringContainsString('function updateStatus', $controller);
        $this->assertStringContainsString("Rule::in(['Active', 'Draft'])", $controller);
        $this->assertStringContainsString('DB::transaction', $controller);
        $this->assertStringContainsString("'action' => 'Update Promo Status'", $controller);
        $this->assertStringContainsString('response()->json', $controller);
        $this->assertStringContainsString('redirectToHotelDetail', $controller);
        $this->assertStringContainsString('redirectToHotelsIndexWithError', $controller);
    }

    public function test_hotel_package_routes_are_decomposed_to_dedicated_controller(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $controller = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelPackageAdminController.php'));

        $this->assertFileExists(app_path('Http/Controllers/Backend/Operations/Hotels/HotelPackageAdminController.php'));
        $this->assertStringContainsString('use App\Http\Controllers\Backend\Operations\Hotels\HotelPackageAdminController;', $routes);
        $this->assertStringContainsString("[HotelPackageAdminController::class,'create']", $routes);
        $this->assertStringContainsString("[HotelPackageAdminController::class,'edit']", $routes);
        $this->assertStringContainsString("[HotelPackageAdminController::class,'store']", $routes);
        $this->assertStringContainsString("[HotelPackageAdminController::class,'update']", $routes);
        $this->assertStringContainsString("[HotelPackageAdminController::class,'updateStatus']", $routes);
        $this->assertStringContainsString("[HotelPackageAdminController::class, 'destroy']", $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class,'view_add_package']", $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class,'view_edit_package']", $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class,'func_add_package']", $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class,'func_edit_package']", $routes);
        $this->assertStringNotContainsString("[HotelsAdminController::class, 'destroy_package']", $routes);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.package-create'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.package-edit'", $controller);
        $this->assertStringContainsString('function updateStatus', $controller);
        $this->assertStringContainsString("Rule::in(['Active', 'Draft'])", $controller);
        $this->assertStringContainsString('DB::transaction', $controller);
        $this->assertStringContainsString("'action' => 'Update Package Status'", $controller);
        $this->assertStringContainsString('response()->json', $controller);
        $this->assertStringContainsString('redirectToHotelDetail', $controller);
        $this->assertStringContainsString('redirectToHotelsIndexWithError', $controller);
    }

    public function test_hotel_package_cancellation_policy_is_localized_and_snapshotted_for_order_detail(): void
    {
        $model = file_get_contents(app_path('Models/HotelPackage.php'));
        $ordersModel = file_get_contents(app_path('Models/Orders.php'));
        $storeRequest = file_get_contents(app_path('Http/Requests/StoreHotelPackageRequest.php'));
        $updateRequest = file_get_contents(app_path('Http/Requests/UpdateHotelPackageRequest.php'));
        $controller = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelPackageAdminController.php'));
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $createView = file_get_contents(resource_path('views/backend/operations/hotels/forms/package-create.blade.php'));
        $editView = file_get_contents(resource_path('views/backend/operations/hotels/forms/package-edit.blade.php'));
        $detailView = file_get_contents(resource_path('views/frontend/home/orders/details/partials/hotel-detail-modern.blade.php'));
        $migration = file_get_contents(database_path('migrations/2026_08_10_150000_add_cancellation_policy_to_hotel_packages_and_orders.php'));

        foreach (['cancellation_policy', 'cancellation_policy_traditional', 'cancellation_policy_simplified'] as $field) {
            $this->assertStringContainsString("'{$field}'", $model);
            $this->assertStringContainsString("'{$field}'", $ordersModel);
            $this->assertStringContainsString("'{$field}' => ['nullable', 'string']", $storeRequest);
            $this->assertStringContainsString("'{$field}' => ['nullable', 'string']", $updateRequest);
            $this->assertStringContainsString("'{$field}' => \$validated['{$field}'] ?? null", $controller);
            $this->assertStringContainsString("'name' => '{$field}'", $createView);
            $this->assertStringContainsString("'name' => '{$field}'", $editView);
            $this->assertStringContainsString("'{$field}'", $migration);
        }

        $this->assertStringContainsString('hotelPackageCancellationPolicySnapshot', $orderController);
        $this->assertStringContainsString("'cancellation_policy_traditional' => \$cancellationPolicies['cancellation_policy_traditional']", $orderController);
        $this->assertStringContainsString("'cancellation_policy_simplified' => \$cancellationPolicies['cancellation_policy_simplified']", $orderController);
        $this->assertStringContainsString('data_get($order, $langCancellationPolicy) ?: $order->cancellation_policy', $detailView);
        $this->assertStringContainsString("__('messages.Cancellation Policy')", $detailView);
    }

    public function test_hotel_core_crud_routes_are_decomposed_to_dedicated_controllers(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));

        $controllers = [
            'HotelAdminController',
            'HotelRoomAdminController',
            'HotelContractAdminController',
            'HotelAdditionalChargeAdminController',
            'HotelGalleryAdminController',
        ];

        foreach ($controllers as $controller) {
            $this->assertFileExists(app_path("Http/Controllers/Backend/Operations/Hotels/{$controller}.php"));
            $this->assertStringContainsString("use App\\Http\\Controllers\\Backend\\Operations\\Hotels\\{$controller};", $routes);
        }

        $this->assertStringContainsString("[HotelAdminController::class,'index']", $routes);
        $this->assertStringContainsString("[HotelAdminController::class,'show']", $routes);
        $this->assertStringContainsString("[HotelAdminController::class,'create']", $routes);
        $this->assertStringContainsString("[HotelAdminController::class,'edit']", $routes);
        $this->assertStringContainsString("[HotelAdminController::class,'store']", $routes);
        $this->assertStringContainsString("[HotelAdminController::class,'update']", $routes);
        $this->assertStringContainsString("[HotelAdminController::class,'destroy']", $routes);

        $this->assertStringContainsString("[HotelRoomAdminController::class,'create']", $routes);
        $this->assertStringContainsString("[HotelRoomAdminController::class,'edit']", $routes);
        $this->assertStringContainsString("[HotelRoomAdminController::class,'store']", $routes);
        $this->assertStringContainsString("[HotelRoomAdminController::class,'update']", $routes);
        $this->assertStringContainsString("[HotelRoomAdminController::class,'destroy']", $routes);

        $this->assertStringContainsString("[HotelContractAdminController::class,'store']", $routes);
        $this->assertStringContainsString("[HotelContractAdminController::class,'update']", $routes);
        $this->assertStringContainsString("[HotelContractAdminController::class,'destroy']", $routes);

        $this->assertStringContainsString("[HotelAdditionalChargeAdminController::class,'store']", $routes);
        $this->assertStringContainsString("[HotelAdditionalChargeAdminController::class,'update']", $routes);
        $this->assertStringContainsString("[HotelAdditionalChargeAdminController::class,'destroy']", $routes);
        $this->assertStringNotContainsString("[HotelAdditionalChargeAdminController::class,'create']", $routes);
        $this->assertStringNotContainsString("[HotelAdditionalChargeAdminController::class,'edit']", $routes);

        $this->assertStringContainsString("[HotelGalleryAdminController::class,'edit']", $routes);
        $this->assertStringContainsString("[HotelGalleryAdminController::class,'store']", $routes);
        $this->assertStringContainsString("[HotelGalleryAdminController::class,'destroyCover']", $routes);
        $this->assertStringContainsString("[HotelGalleryAdminController::class,'destroyImage']", $routes);
    }

    public function test_hotels_admin_controller_is_no_longer_bound_to_active_hotel_crud_routes(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));

        $legacyRouteBindings = [
            "[HotelsAdminController::class,'index']",
            "[HotelsAdminController::class,'view_detail_hotel']",
            "[HotelsAdminController::class,'view_edit_hotel']",
            "[HotelsAdminController::class,'view_add_hotel']",
            "[HotelsAdminController::class,'func_add_hotel']",
            "[HotelsAdminController::class,'func_edit_hotel']",
            "[HotelsAdminController::class,'remove_hotel']",
            "[HotelsAdminController::class,'view_add_room']",
            "[HotelsAdminController::class,'view_edit_room']",
            "[HotelsAdminController::class,'func_add_room']",
            "[HotelsAdminController::class,'func_edit_room']",
            "[HotelsAdminController::class,'destroy_room']",
            "[HotelsAdminController::class,'func_add_contract']",
            "[HotelsAdminController::class,'func_edit_hotel_contract']",
            "[HotelsAdminController::class,'delete_contract']",
            "[HotelsAdminController::class,'view_add_additional_charge']",
            "[HotelsAdminController::class,'view_edit_additional_charge']",
            "[HotelsAdminController::class,'func_add_additional_charge']",
            "[HotelsAdminController::class,'func_edit_additional_charge']",
            "[HotelsAdminController::class,'delete_additional_charge']",
            "[HotelsAdminController::class,'view_edit_galery_hotel']",
            "[HotelsAdminController::class,'delete_cover_hotel']",
            "[HotelsAdminController::class,'delete_image_hotel']",
        ];

        foreach ($legacyRouteBindings as $binding) {
            $this->assertStringNotContainsString($binding, $routes);
        }
    }

    public function test_hotel_form_requests_are_registered_for_phase_8f_validation(): void
    {
        $requestFiles = [
            'StoreHotelRequest',
            'UpdateHotelRequest',
            'StoreHotelRoomRequest',
            'UpdateHotelRoomRequest',
            'StoreHotelContractRequest',
            'UpdateHotelContractRequest',
            'StoreHotelNormalPriceRequest',
            'UpdateHotelNormalPriceRequest',
            'StoreHotelPromoRequest',
            'UpdateHotelPromoRequest',
            'StoreHotelPackageRequest',
            'UpdateHotelPackageRequest',
            'StoreHotelAdditionalChargeRequest',
            'UpdateHotelAdditionalChargeRequest',
        ];

        foreach ($requestFiles as $requestFile) {
            $path = app_path("Http/Requests/{$requestFile}.php");
            $contents = file_get_contents($path);

            $this->assertFileExists($path);

            if (in_array($requestFile, ['StoreHotelRoomRequest', 'UpdateHotelRoomRequest'], true)) {
                $this->assertStringContainsString("Gate::any(['posDev', 'posAuthor', 'posAdm'])", $contents);
            } elseif ($requestFile === 'StoreHotelNormalPriceRequest') {
                $this->assertStringContainsString("Gate::any(['posDev', 'posAuthor'])", $contents);
            } else {
                $this->assertStringContainsString('return true;', $contents);
            }
        }
    }

    public function test_hotel_form_requests_cover_primary_business_validation_rules(): void
    {
        $contractCreate = file_get_contents(app_path('Http/Requests/StoreHotelContractRequest.php'));
        $normalPriceCreate = file_get_contents(app_path('Http/Requests/StoreHotelNormalPriceRequest.php'));
        $normalPriceUpdate = file_get_contents(app_path('Http/Requests/UpdateHotelNormalPriceRequest.php'));
        $promoCreate = file_get_contents(app_path('Http/Requests/StoreHotelPromoRequest.php'));
        $promoUpdate = file_get_contents(app_path('Http/Requests/UpdateHotelPromoRequest.php'));
        $packageCreate = file_get_contents(app_path('Http/Requests/StoreHotelPackageRequest.php'));
        $packageUpdate = file_get_contents(app_path('Http/Requests/UpdateHotelPackageRequest.php'));
        $additionalChargeCreate = file_get_contents(app_path('Http/Requests/StoreHotelAdditionalChargeRequest.php'));

        $this->assertStringContainsString("'period_end' => ['required', 'date', 'after_or_equal:period_start']", $contractCreate);
        $this->assertStringContainsString("'file_name' => ['required', 'file', 'mimes:pdf'", $contractCreate);

        $this->assertStringContainsString("'hotel_context' => ['required', 'string']", $normalPriceCreate);
        $this->assertStringContainsString('public function resolvedHotelId(): ?int', $normalPriceCreate);
        $this->assertStringContainsString("'rooms_id.*' => ['required', 'integer', Rule::exists('hotel_rooms', 'id'), \$this->roomBelongsToHotelRule()]", $normalPriceCreate);
        $this->assertStringContainsString("'end_date.*' => ['required', 'date']", $normalPriceCreate);
        $this->assertStringContainsString("strtotime(\$endDate) < strtotime(\$startDate)", $normalPriceCreate);
        $this->assertStringContainsString("'contract_rate.*' => ['required', 'numeric', 'min:0']", $normalPriceCreate);
        $this->assertStringContainsString("'markup.*' => ['required', 'numeric', 'min:0']", $normalPriceCreate);
        $this->assertStringContainsString("'rooms_id' => ['required', 'integer', Rule::exists('hotel_rooms', 'id'), \$this->roomBelongsToHotelRule()]", $normalPriceUpdate);
        $this->assertStringContainsString("'end_date' => ['required', 'date', 'after_or_equal:start_date']", $normalPriceUpdate);

        $this->assertStringContainsString("'book_periode_end' => ['required', 'date', 'after_or_equal:book_periode_start']", $promoCreate);
        $this->assertStringContainsString("'periode_end' => ['required', 'date', 'after_or_equal:periode_start']", $promoCreate);
        $this->assertStringContainsString("'minimum_stay' => ['required', 'integer', 'min:1']", $promoCreate);
        $this->assertStringContainsString('roomBelongsToHotelRule', $promoCreate);
        $this->assertStringContainsString("'hotel_context' => ['required', 'string']", $promoCreate);
        $this->assertStringContainsString('public function resolvedHotelId(): ?int', $promoCreate);
        $this->assertStringContainsString('Gate::any([\'posDev\', \'posAuthor\'])', $promoCreate);
        $this->assertStringContainsString("'hotel_context' => ['required', 'string']", $promoUpdate);
        $this->assertStringContainsString("'book_periode_end' => ['required', 'date', 'after_or_equal:book_periode_start']", $promoUpdate);
        $this->assertStringContainsString("'periode_end' => ['required', 'date', 'after_or_equal:periode_start']", $promoUpdate);
        $this->assertStringContainsString('public function resolvedHotelId(): ?int', $promoUpdate);
        $this->assertStringContainsString('Gate::any([\'posDev\', \'posAuthor\'])', $promoUpdate);

        $this->assertStringContainsString("'duration' => ['required', 'integer', 'min:1']", $packageCreate);
        $this->assertStringContainsString("'stay_period_end' => ['required', 'date', 'after_or_equal:stay_period_start']", $packageCreate);
        $this->assertStringContainsString('roomBelongsToHotelRule', $packageCreate);
        $this->assertStringContainsString("'hotel_context' => ['required', 'string']", $packageCreate);
        $this->assertStringContainsString('public function resolvedHotelId(): ?int', $packageCreate);
        $this->assertStringContainsString('Gate::any([\'posDev\', \'posAuthor\'])', $packageCreate);
        $this->assertStringContainsString("'hotel_context' => ['required', 'string']", $packageUpdate);
        $this->assertStringContainsString("'stay_period_end' => ['required', 'date', 'after_or_equal:stay_period_start']", $packageUpdate);
        $this->assertStringContainsString('public function resolvedHotelId(): ?int', $packageUpdate);
        $this->assertStringContainsString('Gate::any([\'posDev\', \'posAuthor\'])', $packageUpdate);

        $this->assertStringContainsString("'mandatory_start' => ['required_if:mandatory,1', 'nullable', 'date']", $additionalChargeCreate);
        $this->assertStringContainsString("'mandatory_end' => ['required_if:mandatory,1', 'nullable', 'date', 'after_or_equal:mandatory_start']", $additionalChargeCreate);
        $this->assertStringContainsString("'contract_rate' => ['required', 'numeric', 'min:0']", $additionalChargeCreate);
        $this->assertStringContainsString("'markup' => ['required', 'numeric', 'min:0']", $additionalChargeCreate);
    }

    public function test_hotel_domain_controllers_use_phase_8f_form_requests(): void
    {
        $hotelController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelAdminController.php'));
        $roomController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelRoomAdminController.php'));
        $contractController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelContractAdminController.php'));
        $normalPriceController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelNormalPriceAdminController.php'));
        $promoController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelPromoAdminController.php'));
        $packageController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelPackageAdminController.php'));
        $additionalChargeController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelAdditionalChargeAdminController.php'));

        $this->assertStringContainsString('store(StoreHotelRequest $request)', $hotelController);
        $this->assertStringContainsString('update(UpdateHotelRequest $request, $id)', $hotelController);
        $this->assertStringContainsString('store(StoreHotelRoomRequest $request', $roomController);
        $this->assertStringContainsString('update(UpdateHotelRoomRequest $request, $id', $roomController);
        $this->assertStringContainsString('store(StoreHotelContractRequest $request', $contractController);
        $this->assertStringContainsString('update(UpdateHotelContractRequest $request, $id', $contractController);
        $this->assertStringContainsString('store(StoreHotelNormalPriceRequest $request)', $normalPriceController);
        $this->assertStringContainsString('update(UpdateHotelNormalPriceRequest $request, $id)', $normalPriceController);
        $this->assertStringNotContainsString('$request->validate([', $normalPriceController);
        $this->assertStringContainsString('store(StoreHotelPromoRequest $request)', $promoController);
        $this->assertStringContainsString('update(UpdateHotelPromoRequest $request, $id)', $promoController);
        $this->assertStringNotContainsString('$request->validate([', $promoController);
        $this->assertStringContainsString('store(StoreHotelPackageRequest $request)', $packageController);
        $this->assertStringContainsString('update(UpdateHotelPackageRequest $request, $id)', $packageController);
        $this->assertStringContainsString('store(StoreHotelAdditionalChargeRequest $request)', $additionalChargeController);
        $this->assertStringContainsString('update(UpdateHotelAdditionalChargeRequest $request, $id)', $additionalChargeController);
    }

    public function test_hotel_phase_8g_services_are_registered(): void
    {
        $services = [
            'HotelInventoryService',
            'HotelPricingService',
            'HotelContractService',
            'HotelStatusService',
            'HotelAssetService',
            'HotelAuditService',
        ];

        foreach ($services as $service) {
            $this->assertFileExists(app_path("Services/Hotels/{$service}.php"));
        }
    }

    public function test_hotel_pricing_service_calculates_published_rates_consistently(): void
    {
        $pricing = new \App\Services\Hotels\HotelPricingService();
        $usdRate = (object) ['rate' => 15000];
        $tax = (object) ['tax' => 10];
        $package = (object) [
            'contract_rate' => 1500000,
            'markup' => 20,
            'duration' => 2,
        ];
        $normalPrice = (object) [
            'contract_rate' => 1500000,
            'markup' => 20,
            'kick_back' => 10,
        ];

        $this->assertSame(100, $pricing->contractRateUsd(1500000, $usdRate));
        $this->assertSame(120, $pricing->subtotalUsd(1500000, 20, $usdRate));
        $this->assertSame(12, $pricing->taxAmount(1500000, 20, $usdRate, $tax));
        $this->assertSame(132, $pricing->publishedRate(1500000, 20, $usdRate, $tax));
        $this->assertSame(132, $pricing->normalPricePublishedRate($normalPrice, $usdRate, $tax));
        $this->assertSame(122, $pricing->normalPriceNetRate($normalPrice, $usdRate, $tax));
        $this->assertSame(242, $pricing->packagePublishedRate($package, $usdRate, $tax));

        $breakdown = $pricing->rateBreakdown(1500000, 20, $usdRate, $tax, 1, 10);
        $this->assertSame(132, $breakdown['published_rate']);
        $this->assertSame(1980000, $breakdown['published_rate_idr']);
        $this->assertSame(20, $breakdown['markup_usd']);
        $this->assertSame(300000, $breakdown['markup_idr']);
        $this->assertSame(12, $breakdown['tax_usd']);
        $this->assertSame(180000, $breakdown['tax_idr']);
        $this->assertSame(122, $breakdown['net_rate']);
        $this->assertSame(1830000, $breakdown['net_rate_idr']);
    }

    public function test_hotel_phase_8g_services_are_used_by_active_hotels_layer(): void
    {
        $hotelAdminController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelAdminController.php'));
        $roomController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelRoomAdminController.php'));
        $contractController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelContractAdminController.php'));
        $galleryController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelGalleryAdminController.php'));
        $priceModel = file_get_contents(app_path('Models/HotelPrice.php'));
        $promoModel = file_get_contents(app_path('Models/HotelPromo.php'));
        $packageModel = file_get_contents(app_path('Models/HotelPackage.php'));
        $optionalRateModel = file_get_contents(app_path('Models/OptionalRate.php'));

        $this->assertStringContainsString('HotelInventoryService', $hotelAdminController);
        $this->assertStringContainsString('$inventoryService->detailData', $hotelAdminController);
        $this->assertStringContainsString('HotelAssetService', $roomController);
        $this->assertStringContainsString('HotelAuditService', $roomController);
        $this->assertStringContainsString('HotelStatusService', $roomController);
        $this->assertStringContainsString('uploadRoomCover', $roomController);
        $this->assertStringContainsString('replaceRoomCover', $roomController);
        $this->assertStringContainsString('deleteRoomCover', $roomController);
        $this->assertStringContainsString('HotelContractService', $contractController);
        $this->assertStringContainsString('HotelAuditService', $contractController);
        $this->assertStringContainsString('HotelAssetService', $galleryController);
        $this->assertStringContainsString('deleteHotelCover', $galleryController);
        $this->assertStringContainsString('deleteGalleryImage', $galleryController);

        foreach ([$priceModel, $promoModel, $packageModel, $optionalRateModel] as $model) {
            $this->assertStringContainsString('HotelPricingService', $model);
            $this->assertStringContainsString('app(HotelPricingService::class)', $model);
        }
    }

    public function test_hotel_phase_8g_services_expose_business_rule_methods(): void
    {
        $inventoryService = file_get_contents(app_path('Services/Hotels/HotelInventoryService.php'));
        $statusService = file_get_contents(app_path('Services/Hotels/HotelStatusService.php'));
        $contractService = file_get_contents(app_path('Services/Hotels/HotelContractService.php'));
        $assetService = file_get_contents(app_path('Services/Hotels/HotelAssetService.php'));
        $auditService = file_get_contents(app_path('Services/Hotels/HotelAuditService.php'));

        $this->assertStringContainsString('function detailData', $inventoryService);
        $this->assertStringContainsString('function summary', $inventoryService);
        $this->assertStringContainsString('expirePromosForHotel', $inventoryService);
        $this->assertStringContainsString('expirePackagesForHotel', $inventoryService);

        $this->assertStringContainsString('function expirePromosForHotel', $statusService);
        $this->assertStringContainsString('function expirePackagesForHotel', $statusService);
        $this->assertStringContainsString('function defaultHotelStatus', $statusService);
        $this->assertStringContainsString('function defaultRoomStatus', $statusService);
        $this->assertStringContainsString('function shouldDraftHotel', $statusService);

        $this->assertStringContainsString('function upload', $contractService);
        $this->assertStringContainsString('function replace', $contractService);
        $this->assertStringContainsString('function previewUrl', $contractService);
        $this->assertStringContainsString('function delete', $contractService);

        $this->assertStringContainsString('function uploadHotelCover', $assetService);
        $this->assertStringContainsString('function replaceHotelCover', $assetService);
        $this->assertStringContainsString('function uploadRoomCover', $assetService);
        $this->assertStringContainsString('function replaceRoomCover', $assetService);
        $this->assertStringContainsString('function deleteGalleryImage', $assetService);

        $this->assertStringContainsString('function userLog', $auditService);
        $this->assertStringContainsString('function actionLog', $auditService);
    }

    public function test_hotel_status_and_contract_services_have_safe_default_behaviour(): void
    {
        $status = new \App\Services\Hotels\HotelStatusService();
        $contract = new \App\Models\Contract(['file_name' => 'sample-contract.pdf']);
        $contractService = new \App\Services\Hotels\HotelContractService(new \App\Services\Hotels\HotelAssetService());

        $this->assertSame('Draft', $status->defaultHotelStatus());
        $this->assertSame('Active', $status->defaultRoomStatus());
        $this->assertStringEndsWith('storage/hotels/hotels-contract/sample-contract.pdf', $contractService->previewUrl($contract));
    }

    public function test_hotel_phase_8h_inventory_service_uses_eager_loading_and_view_model(): void
    {
        $service = file_get_contents(app_path('Services/Hotels/HotelInventoryService.php'));
        $viewModel = file_get_contents(app_path('ViewModels/Hotels/HotelDetailViewModel.php'));
        $controller = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelAdminController.php'));

        $this->assertFileExists(app_path('ViewModels/Hotels/HotelDetailViewModel.php'));
        $this->assertStringContainsString("'rooms'", $service);
        $this->assertStringContainsString("'prices.rooms'", $service);
        $this->assertStringContainsString("'promos.rooms'", $service);
        $this->assertStringContainsString("'packages.room'", $service);
        $this->assertStringContainsString("'optionalrates'", $service);
        $this->assertStringContainsString("'contracts'", $service);
        $this->assertStringContainsString("Cache::remember('usd_rates'", $service);
        $this->assertStringContainsString("Cache::remember('hotel_tax_rate'", $service);
        $this->assertStringContainsString('new HotelDetailViewModel', $service);
        $this->assertStringContainsString("'hotelDetail' => \$viewModel", $service);
        $this->assertStringContainsString('$inventoryService->detailData', $controller);

        foreach (['stats', 'normalPriceRows', 'promoRows', 'packageRows', 'additionalChargeRows', 'createdAge'] as $method) {
            $this->assertStringContainsString("function {$method}", $viewModel);
        }
    }

    public function test_hotel_phase_8h_detail_blade_renders_prepared_data_without_heavy_dependencies(): void
    {
        $paths = [
            resource_path('views/backend/operations/hotels/detail.blade.php'),
            resource_path('views/backend/operations/hotels/partials/normal-prices.blade.php'),
            resource_path('views/backend/operations/hotels/partials/promo-prices.blade.php'),
            resource_path('views/backend/operations/hotels/partials/package-prices.blade.php'),
            resource_path('views/backend/operations/hotels/partials/additional-charges.blade.php'),
            resource_path('views/backend/operations/hotels/partials/audit-summary.blade.php'),
            resource_path('views/backend/operations/hotels/partials/price-breakdown.blade.php'),
            resource_path('views/backend/operations/hotels/modals/price-calculation.blade.php'),
        ];

        $content = collect($paths)->map(fn ($path) => file_get_contents($path))->implode("\n");

        $this->assertStringContainsString('$hotelDetail->stats()', $content);
        $this->assertStringContainsString('$hotelDetail->normalPriceRows()', $content);
        $this->assertStringContainsString('$hotelDetail->promoRows()', $content);
        $this->assertStringContainsString('$hotelDetail->packageRows()', $content);
        $this->assertStringContainsString('$hotelDetail->additionalChargeRows()', $content);
        $this->assertStringContainsString('$hotelDetail->createdAge()', $content);
        $this->assertStringContainsString("modals.price-calculation", $content);
        $this->assertStringContainsString('hotel-price-calculation-summary', $content);
        $this->assertStringContainsString('hotel-price-calculation-summary--package', $content);
        $this->assertStringContainsString('hotel-price-calculation-summary__item--agent', $content);
        $this->assertStringContainsString('Agent Rate', $content);
        $this->assertStringContainsString('Agent Rate Per Night', $content);
        $this->assertStringContainsString('Package Total', $content);
        $this->assertStringContainsString('Package total: {{ currencyFormatUsd($row[\'package_total_rate\']) }}', $content);
        $this->assertStringContainsString("currencyFormatUsd(\$pricing['net_rate'] ?? (\$pricing['published_rate'] ?? 0))", $content);
        $this->assertStringContainsString("currencyFormatIdr(\$pricing['net_rate_idr'] ?? (\$pricing['published_rate_idr'] ?? 0))", $content);
        $this->assertStringContainsString("currencyFormatIdr(\$pricing['markup_idr'] ?? 0)", $content);
        $this->assertStringContainsString("currencyFormatIdr(\$pricing['tax_idr'] ?? 0)", $content);
        $this->assertStringContainsString("currencyFormatIdr(\$pricing['effective_contract_rate_idr'] ?? 0)", $content);
        $this->assertStringNotContainsString('<span>Published Rate</span>', $content);
        $this->assertStringContainsString('data-toggle="modal"', $content);
        $this->assertStringNotContainsString('data-hotel-price-breakdown-toggle', $content);
        $this->assertStringNotContainsString('calculatePrice(', $content);
        $this->assertStringNotContainsString('Carbon::parse', $content);
        $this->assertStringNotContainsString('::where(', $content);
        $this->assertStringNotContainsString('->where(', $content);
        $this->assertStringNotContainsString('HotelPrice', $content);
        $this->assertStringNotContainsString('HotelPromo', $content);
        $this->assertStringNotContainsString('HotelPackage', $content);
        $this->assertStringNotContainsString('OptionalRate', $content);
    }

    public function test_hotel_backend_inventory_filters_expired_prices_at_the_query_boundary(): void
    {
        $inventory = file_get_contents(app_path('Services/Hotels/HotelInventoryService.php'));
        $statusService = file_get_contents(app_path('Services/Hotels/HotelStatusService.php'));
        $indexController = file_get_contents(app_path('Http/Controllers/HotelsAdminController.php'));

        foreach ([
            app_path('Models/HotelPrice.php'),
            app_path('Models/HotelPromo.php'),
            app_path('Models/HotelPackage.php'),
            app_path('Models/OptionalRate.php'),
        ] as $modelPath) {
            $this->assertStringContainsString('function scopeNotExpired(', file_get_contents($modelPath));
        }

        $this->assertGreaterThanOrEqual(4, substr_count($inventory, '->notExpired($now)'));
        $this->assertStringContainsString("'optionalrates' => fn (\$query) => \$query->notExpired(\$now)", $inventory);
        $this->assertStringContainsString('HotelPrice::notExpired($now)', $indexController);
        $this->assertStringContainsString('HotelPromo::notExpired($now)', $indexController);
        $this->assertStringContainsString('HotelPackage::notExpired($now)', $indexController);
        $this->assertStringContainsString("whereDate('book_periode_end', '<', \$today)", $statusService);
        $this->assertStringContainsString("whereDate('stay_period_end', '<', \$today)", $statusService);
    }

    public function test_hotel_phase_8i_backend_views_and_assets_follow_project_architecture(): void
    {
        $backendHotelViews = collect(glob(resource_path('views/backend/operations/hotels/**/*.blade.php'), GLOB_BRACE));
        $backendHotelViews = $backendHotelViews->merge(glob(resource_path('views/backend/operations/hotels/*.blade.php')))->unique()->values();

        $this->assertNotEmpty($backendHotelViews);

        foreach ($backendHotelViews as $path) {
            $this->assertStringContainsString(
                str_replace('\\', '/', resource_path('views/backend/operations/hotels')),
                str_replace('\\', '/', $path)
            );
        }

        foreach ([
            resource_path('backend/js/operations/hotels/index.js'),
            resource_path('backend/js/operations/hotels/detail.js'),
            resource_path('backend/js/operations/hotels/forms.js'),
            resource_path('backend/scss/operations/hotels/index-entry.scss'),
            resource_path('backend/scss/operations/hotels/detail-entry.scss'),
            resource_path('backend/scss/operations/hotels/forms-entry.scss'),
            resource_path('backend/scss/operations/hotels/_index.scss'),
            resource_path('backend/scss/operations/hotels/_detail.scss'),
            resource_path('backend/scss/operations/hotels/_forms.scss'),
        ] as $assetPath) {
            $this->assertFileExists($assetPath);
        }

        $mix = file_get_contents(base_path('webpack.mix.js'));
        $this->assertStringContainsString("resources/backend/js/operations/hotels/index.js", $mix);
        $this->assertStringContainsString("resources/backend/js/operations/hotels/detail.js", $mix);
        $this->assertStringContainsString("resources/backend/js/operations/hotels/forms.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/operations/hotels/index-entry.scss", $mix);
        $this->assertStringContainsString("resources/backend/scss/operations/hotels/detail-entry.scss", $mix);
        $this->assertStringContainsString("resources/backend/scss/operations/hotels/forms-entry.scss", $mix);
    }

    public function test_hotel_phase_8i_legacy_admin_hotel_views_are_wrappers_only(): void
    {
        $wrappers = [
            resource_path('views/admin/hotelsadmin.blade.php') => "@include('backend.operations.hotels.index')",
            resource_path('views/admin/hotelsadmindetail.blade.php') => "@include('backend.operations.hotels.detail')",
            resource_path('views/admin/hotel-normal-price.blade.php') => "@include('backend.operations.hotels.partials.normal-prices')",
            resource_path('views/admin/hotel-promo-price.blade.php') => "@include('backend.operations.hotels.partials.promo-prices')",
            resource_path('views/admin/hotel-package-price.blade.php') => "@include('backend.operations.hotels.partials.package-prices')",
        ];

        foreach ($wrappers as $path => $expectedInclude) {
            $this->assertFileExists($path);
            $this->assertSame($expectedInclude, trim(file_get_contents($path)));
        }
    }

    public function test_hotel_phase_8i_backend_pages_use_shared_ui_components(): void
    {
        $pageViews = [
            resource_path('views/backend/operations/hotels/index.blade.php'),
            resource_path('views/backend/operations/hotels/detail.blade.php'),
            resource_path('views/backend/operations/hotels/forms/create.blade.php'),
            resource_path('views/backend/operations/hotels/forms/edit.blade.php'),
            resource_path('views/backend/operations/hotels/forms/gallery-edit.blade.php'),
            resource_path('views/backend/operations/hotels/forms/room-create.blade.php'),
            resource_path('views/backend/operations/hotels/forms/room-edit.blade.php'),
            resource_path('views/backend/operations/hotels/forms/normal-price-create.blade.php'),
            resource_path('views/backend/operations/hotels/forms/promo-create.blade.php'),
            resource_path('views/backend/operations/hotels/forms/promo-edit.blade.php'),
            resource_path('views/backend/operations/hotels/forms/package-create.blade.php'),
            resource_path('views/backend/operations/hotels/forms/package-edit.blade.php'),
        ];

        foreach ($pageViews as $path) {
            $content = file_get_contents($path);

            $this->assertStringContainsString('<x-backend.page-hero', $content, $path);
            $this->assertStringContainsString('backend-page-primary-action', $content, $path);
            $this->assertStringContainsString('backend-status-badge', $content, $path);
            $this->assertStringContainsString('backend-feedback', $content, $path);
            $this->assertStringContainsString('backend-alert', $content, $path);
        }

        $hotelViews = collect($pageViews)
            ->merge(glob(resource_path('views/backend/operations/hotels/partials/*.blade.php')))
            ->merge(glob(resource_path('views/backend/operations/hotels/modals/*.blade.php')))
            ->map(fn ($path) => file_get_contents($path))
            ->implode("\n");

        foreach ([
            'backend-toolbar-action',
            'backend-icon-action',
            'backend-table',
            'backend-table-card',
            'backend-table-empty',
            'backend-empty-state',
            'backend-modal',
            'backend-modal__header',
            'backend-modal__body',
            'backend-modal__footer',
        ] as $sharedClass) {
            $this->assertStringContainsString($sharedClass, $hotelViews);
        }
    }

    public function test_hotel_phase_8i_backend_blades_do_not_use_legacy_visual_primitives(): void
    {
        $paths = collect(glob(resource_path('views/backend/operations/hotels/**/*.blade.php'), GLOB_BRACE))
            ->merge(glob(resource_path('views/backend/operations/hotels/*.blade.php')))
            ->unique()
            ->values();

        $this->assertNotEmpty($paths);

        $forbiddenStrings = [
            'card-box',
            'btn-view',
            'btn-edit',
            'btn-delete',
            'status-active',
            'status-draft',
            'data-table table',
            'data-table.table',
            'onkeyup',
        ];

        foreach ($paths as $path) {
            $content = file_get_contents($path);

            foreach ($forbiddenStrings as $forbidden) {
                $this->assertStringNotContainsString($forbidden, $content, "{$forbidden} found in {$path}");
            }

            $this->assertDoesNotMatchRegularExpression('/\sstyle\s*=/i', $content, "inline style found in {$path}");
            $this->assertDoesNotMatchRegularExpression('/<script(?![^>]*\bsrc=)/i', $content, "inline script found in {$path}");
        }
    }

    public function test_hotel_phase_8i_operation_scss_does_not_redeclare_legacy_primitives(): void
    {
        $paths = collect(glob(resource_path('backend/scss/operations/hotels/*.scss')))->values();
        $content = $paths->map(fn ($path) => file_get_contents($path))->implode("\n");

        foreach (['.card-box', '.btn-view', '.btn-edit', '.btn-delete', '.status-active', '.status-draft', '.data-table'] as $forbiddenSelector) {
            $this->assertStringNotContainsString($forbiddenSelector, $content);
        }

        $this->assertStringContainsString('hotel-detail', $content);
        $this->assertStringContainsString('hotel-form', $content);
        $this->assertStringContainsString('hotels-admin', $content);
    }

    public function test_hotel_phase_8j_final_workspace_acceptance_routes_are_complete(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));

        $expectedRoutes = [
            "name('admin.hotels.index')",
            "name('admin.hotels.create')",
            "name('admin.hotels.show')",
            "name('admin.hotels.edit')",
            "name('admin.hotels.destroy')",
            "name('admin.hotel.store')",
            "name('func.hotel.edit')",
            "name('admin.hotels.contracts.store')",
            "name('admin.hotels.contracts.update')",
            "name('admin.hotels.contracts.destroy')",
            "name('admin.hotels.room.create')",
            "name('admin.hotels.room.edit')",
            "name('admin.hotels.room.store')",
            "name('admin.hotels.room.update')",
            "name('admin.hotels.room.status.update')",
            "name('admin.hotels.room.delete')",
            "name('admin.hotels.prices.create')",
            "name('admin.hotels.normal-prices.store')",
            "name('admin.hotels.normal-prices.update')",
            "name('admin.hotels.normal-prices.destroy')",
            "name('admin.hotels.promos.create')",
            "name('admin.hotels.promos.edit')",
            "name('admin.hotels.promos.store')",
            "name('admin.hotels.promos.update')",
            "name('admin.hotels.promos.destroy')",
            "name('admin.hotels.packages.create')",
            "name('admin.hotels.packages.edit')",
            "name('admin.hotels.packages.store')",
            "name('admin.hotels.packages.update')",
            "name('admin.hotels.packages.destroy')",
            "name('admin.hotels.additional-charges.store')",
            "name('admin.hotels.additional-charges.update')",
            "name('admin.hotels.additional-charges.destroy')",
            "name('hotels.gallery.edit')",
            "name('hotels.gallery.store')",
            "name('hotels.cover.destroy')",
            "name('hotels.images.destroy')",
        ];

        foreach ($expectedRoutes as $routeName) {
            $this->assertStringContainsString($routeName, $routes);
        }

        foreach ([
            'HotelAdminController',
            'HotelContractAdminController',
            'HotelRoomAdminController',
            'HotelNormalPriceAdminController',
            'HotelPromoAdminController',
            'HotelPackageAdminController',
            'HotelAdditionalChargeAdminController',
            'HotelGalleryAdminController',
        ] as $controller) {
            $this->assertStringContainsString($controller, $routes);
        }
    }

    public function test_hotel_phase_8j_final_workspace_acceptance_layers_are_complete(): void
    {
        $requiredFiles = [
            app_path('Http/Controllers/Backend/Operations/Hotels/HotelAdminController.php'),
            app_path('Http/Controllers/Backend/Operations/Hotels/HotelRoomAdminController.php'),
            app_path('Http/Controllers/Backend/Operations/Hotels/HotelContractAdminController.php'),
            app_path('Http/Controllers/Backend/Operations/Hotels/HotelNormalPriceAdminController.php'),
            app_path('Http/Controllers/Backend/Operations/Hotels/HotelPromoAdminController.php'),
            app_path('Http/Controllers/Backend/Operations/Hotels/HotelPackageAdminController.php'),
            app_path('Http/Controllers/Backend/Operations/Hotels/HotelAdditionalChargeAdminController.php'),
            app_path('Http/Controllers/Backend/Operations/Hotels/HotelGalleryAdminController.php'),
            app_path('Services/Hotels/HotelInventoryService.php'),
            app_path('Services/Hotels/HotelPricingService.php'),
            app_path('Services/Hotels/HotelContractService.php'),
            app_path('Services/Hotels/HotelStatusService.php'),
            app_path('Services/Hotels/HotelAssetService.php'),
            app_path('Services/Hotels/HotelAuditService.php'),
            app_path('ViewModels/Hotels/HotelDetailViewModel.php'),
            resource_path('views/backend/operations/hotels/index.blade.php'),
            resource_path('views/backend/operations/hotels/detail.blade.php'),
            resource_path('backend/js/operations/hotels/index.js'),
            resource_path('backend/js/operations/hotels/detail.js'),
            resource_path('backend/js/operations/hotels/forms.js'),
            resource_path('backend/scss/operations/hotels/index-entry.scss'),
            resource_path('backend/scss/operations/hotels/detail-entry.scss'),
            resource_path('backend/scss/operations/hotels/forms-entry.scss'),
        ];

        foreach ($requiredFiles as $path) {
            $this->assertFileExists($path);
        }
    }

    public function test_hotel_phase_8j_final_roadmap_is_marked_complete_through_acceptance(): void
    {
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));

        $this->assertStringContainsString('### Phase 8J - Final Hotel Workspace Acceptance', $roadmap);
        $this->assertStringNotContainsString("### Phase 8J - Final Hotel Workspace Acceptance\n\n- [ ]", $roadmap);
    }

    public function test_activities_phase_1_route_names_and_roadmap_are_registered(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));

        foreach ([
            "name('activities.index')",
            "name('activities.show')",
            "name('activities.create')",
            "name('activities.edit')",
            "name('activities.gallery.edit')",
            "name('gallery-activities.update')",
            "name('activities.store')",
            "name('activities.update')",
            "name('activities.destroy')",
            "name('activities.cover.destroy')",
            "name('activities.images.destroy')",
        ] as $routeName) {
            $this->assertStringContainsString($routeName, $routes);
        }

        $this->assertStringContainsString('Operations Activities memakai namespace/backend UI modern, Form Request, service, dan view model.', $roadmap);
        $this->assertStringContainsString('Backend Edit Activity memakai canonical Create/Edit/Detail page layout,', $roadmap);
    }

    public function test_activities_phase_1_existing_backend_form_views_are_in_operations_architecture(): void
    {
        foreach ([
            resource_path('views/backend/operations/activities/index.blade.php'),
            resource_path('views/backend/operations/activities/detail.blade.php'),
            resource_path('views/backend/operations/activities/forms/create.blade.php'),
            resource_path('views/backend/operations/activities/forms/edit.blade.php'),
            resource_path('views/backend/operations/activities/forms/gallery-edit.blade.php'),
        ] as $path) {
            $this->assertFileExists($path);
        }
    }

    public function test_activities_phase_1_legacy_admin_views_are_compatibility_wrappers(): void
    {
        $wrappers = [
            resource_path('views/admin/activitiesadmin.blade.php') => "@include('backend.operations.activities.index')",
            resource_path('views/admin/activitiesadmindetail.blade.php') => "@include('backend.operations.activities.detail')",
        ];

        foreach ($wrappers as $path => $expectedInclude) {
            $this->assertFileExists($path);
            $this->assertSame($expectedInclude, trim(file_get_contents($path)));
        }
    }

    public function test_activities_phase_1_controller_and_forms_use_route_names(): void
    {
        $adminController = file_get_contents(app_path('Http/Controllers/ActivitiesAdminController.php'));
        $backendActivityController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Activities/ActivityAdminController.php'));
        $backendGalleryController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Activities/ActivityGalleryAdminController.php'));
        $activityController = file_get_contents(app_path('Http/Controllers/ActivitiesController.php'));
        $formContent = collect([
            resource_path('views/backend/operations/activities/forms/create.blade.php'),
            resource_path('views/backend/operations/activities/forms/edit.blade.php'),
            resource_path('views/backend/operations/activities/forms/gallery-edit.blade.php'),
            resource_path('views/backend/operations/activities/index.blade.php'),
            resource_path('views/backend/operations/activities/detail.blade.php'),
        ])->map(fn ($path) => file_get_contents($path))->implode("\n");

        $this->assertStringContainsString('extends ActivityAdminController', $adminController);
        $this->assertStringContainsString("view('backend.operations.activities.index'", $backendActivityController);
        $this->assertStringContainsString("view('backend.operations.activities.detail'", $backendActivityController);
        $this->assertStringContainsString("view('backend.operations.activities.forms.gallery-edit'", $backendGalleryController);
        $this->assertStringNotContainsString("view('backend.operations.activities.index'", $activityController);
        $this->assertStringContainsString("redirect()->route('admin.activities.index')", $backendActivityController);
        $this->assertStringContainsString("redirect()->route('admin.activities.show'", $backendActivityController);
        $this->assertStringNotContainsString('redirect("/activities-admin"', $adminController);
        $this->assertStringNotContainsString('redirect("/detail-activity-', $adminController);
        $this->assertStringContainsString("route('admin.activities.store')", $formContent);
        $this->assertStringContainsString("route('admin.activities.update'", $formContent);
        $this->assertStringContainsString("route('admin.activities.images.destroy'", $formContent);
        $this->assertStringContainsString("route('admin.activities.show'", $formContent);
        $this->assertStringContainsString("route('admin.activities.create')", $formContent);
        $this->assertStringContainsString("route('admin.activities.edit'", $formContent);
        $this->assertStringContainsString("route('admin.activities.destroy'", $formContent);

        foreach ([
            'action="/fadd-activity"',
            'action="/fupdate-activity',
            'action="/fdelete-activity-img',
            'href="/detail-activity-',
            'href="/edit-activity-',
            'href="/add-activity"',
            'action="/remove-activity',
        ] as $legacyPattern) {
            $this->assertStringNotContainsString($legacyPattern, $formContent);
        }
    }

    public function test_activities_phase_2_index_and_detail_use_backend_shared_ui(): void
    {
        $index = file_get_contents(resource_path('views/backend/operations/activities/index.blade.php'));
        $detail = file_get_contents(resource_path('views/backend/operations/activities/detail.blade.php'));
        $content = $index . "\n" . $detail;

        foreach ([
            '<x-backend.page-hero',
            'backend-page-toolbar',
            'backend-feedback',
            'backend-alert',
            'backend-kpi-grid',
            'backend-kpi-card',
            'backend-panel',
            'backend-section-header',
            'backend-page-primary-action',
            'backend-toolbar-action',
            'backend-icon-action',
            'backend-status-badge',
            'backend-table',
            'backend-table-card-list',
            'backend-table-card',
            'backend-table-empty',
            'backend-empty-state',
        ] as $sharedClass) {
            $this->assertStringContainsString($sharedClass, $content);
        }

        foreach ([
            'card-box',
            'btn-view',
            'btn-edit',
            'btn-delete',
            'status-active',
            'status-draft',
            'data-table',
            'style=',
            'onkeyup',
            'onclick',
        ] as $legacyPattern) {
            $this->assertStringNotContainsString($legacyPattern, $content);
        }
    }

    public function test_activities_phase_2_forms_use_backend_shared_ui(): void
    {
        $forms = collect([
            resource_path('views/backend/operations/activities/forms/create.blade.php'),
            resource_path('views/backend/operations/activities/forms/edit.blade.php'),
            resource_path('views/backend/operations/activities/forms/gallery-edit.blade.php'),
        ])->map(fn ($path) => file_get_contents($path))->implode("\n");

        foreach ([
            '<x-backend.page-hero',
            'backend-page-toolbar',
            'backend-feedback',
            'backend-alert',
            'backend-panel',
            'backend-section-header',
            'backend-form-label',
            'backend-page-primary-action',
            'backend-button backend-button-primary',
            'backend-button backend-button-secondary',
            'backend-status-badge',
            'backend-empty-state',
            'activity-gallery-layout',
            'activity-gallery-manager',
            'data-activity-gallery-preview-target="#activityGalleryPreview"',
        ] as $sharedPattern) {
            $this->assertStringContainsString($sharedPattern, $forms);
        }

        foreach ([
            'card-box',
            'card-box-title',
            'card-box-footer',
            'btn-view',
            'btn-edit',
            'btn-delete',
            'btn btn-',
            'alert alert-',
            'alert-form',
            'style=',
            'onkeyup',
            'onclick',
            'href="/',
            'action="/',
            'detail-activity-',
            'edit-activity-',
            'fupdate-activity',
            'fdelete-activity-img',
        ] as $legacyPattern) {
            $this->assertStringNotContainsString($legacyPattern, $forms);
        }
    }

    public function test_activities_phase_3_assets_are_in_backend_operations_architecture(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));
        $index = file_get_contents(resource_path('views/backend/operations/activities/index.blade.php'));
        $detail = file_get_contents(resource_path('views/backend/operations/activities/detail.blade.php'));
        $forms = collect([
            resource_path('views/backend/operations/activities/forms/create.blade.php'),
            resource_path('views/backend/operations/activities/forms/edit.blade.php'),
            resource_path('views/backend/operations/activities/forms/gallery-edit.blade.php'),
        ])->map(fn ($path) => file_get_contents($path))->implode("\n");
        $indexJs = file_get_contents(resource_path('backend/js/operations/activities/index.js'));
        $formsJs = file_get_contents(resource_path('backend/js/operations/activities/forms.js'));

        foreach ([
            resource_path('backend/js/operations/activities/index.js'),
            resource_path('backend/js/operations/activities/forms.js'),
            resource_path('backend/scss/operations/activities/index-entry.scss'),
            resource_path('backend/scss/operations/activities/forms-entry.scss'),
            resource_path('backend/scss/operations/activities/_index.scss'),
            resource_path('backend/scss/operations/activities/_forms.scss'),
        ] as $path) {
            $this->assertFileExists($path);
        }

        $this->assertStringContainsString("resources/backend/js/operations/activities/index.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/operations/activities/index-entry.scss", $mix);
        $this->assertStringContainsString("resources/backend/js/operations/activities/forms.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/operations/activities/forms-entry.scss", $mix);
        $this->assertStringContainsString("mix('build/backend/js/operations/activities/index.js')", $index);
        $this->assertStringContainsString("mix('build/backend/css/operations/activities/index.css')", $index);
        $this->assertStringContainsString("mix('build/backend/css/operations/activities/index.css')", $detail);
        $this->assertStringNotContainsString("mix('build/backend/js/operations/activities/index.js')", $detail);
        $this->assertStringContainsString("mix('build/backend/js/operations/activities/forms.js')", $forms);
        $this->assertStringContainsString("mix('build/backend/css/operations/activities/forms.css')", $forms);
        $this->assertStringContainsString('data-activity-delete', $index);
        $this->assertStringContainsString('data-activity-gallery-delete', $forms);
        $this->assertStringContainsString('data-activity-file-input', $forms);
        $this->assertStringContainsString('data-activity-gallery-preview', $forms);
        $this->assertStringContainsString('window.confirm', $indexJs);
        $this->assertStringContainsString('window.confirm', $formsJs);
        $this->assertStringContainsString('activityFileInputTarget', $formsJs);
        $this->assertStringContainsString('activityGalleryPreviewTarget', $formsJs);
        $this->assertStringNotContainsString("multiple\n                                            required", $forms);
        $this->assertStringNotContainsString('onclick', $index);
        $this->assertStringNotContainsString('onclick', $forms);
    }

    public function test_activities_phase_4_controller_decomposition_and_validation_are_in_place(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $legacyAdminController = file_get_contents(app_path('Http/Controllers/ActivitiesAdminController.php'));
        $publicController = file_get_contents(app_path('Http/Controllers/ActivitiesController.php'));
        $activityController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Activities/ActivityAdminController.php'));
        $galleryController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Activities/ActivityGalleryAdminController.php'));
        $storeRequest = file_get_contents(app_path('Http/Requests/StoreActivityAdminRequest.php'));
        $updateRequest = file_get_contents(app_path('Http/Requests/UpdateActivityAdminRequest.php'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));

        foreach ([
            app_path('Http/Controllers/Backend/Operations/Activities/ActivityAdminController.php'),
            app_path('Http/Controllers/Backend/Operations/Activities/ActivityGalleryAdminController.php'),
            app_path('Http/Requests/StoreActivityAdminRequest.php'),
            app_path('Http/Requests/UpdateActivityAdminRequest.php'),
        ] as $path) {
            $this->assertFileExists($path);
        }

        $this->assertStringContainsString('Backend\Operations\Activities\ActivityAdminController', $routes);
        $this->assertStringContainsString('Backend\Operations\Activities\ActivityGalleryAdminController', $routes);
        $this->assertStringContainsString("[ActivityAdminController::class,'create']", $routes);
        $this->assertStringContainsString("[ActivityAdminController::class,'edit']", $routes);
        $this->assertStringContainsString("[ActivityAdminController::class,'store']", $routes);
        $this->assertStringContainsString("[ActivityAdminController::class,'update']", $routes);
        $this->assertStringContainsString("[ActivityGalleryAdminController::class,'edit']", $routes);
        $this->assertStringContainsString("[ActivityGalleryAdminController::class,'destroy']", $routes);
        $this->assertStringContainsString("[ActivityGalleryAdminController::class,'destroyCover']", $routes);
        $this->assertStringContainsString("[ActivityGalleryAdminController::class,'destroyImage']", $routes);
        $this->assertStringContainsString('class ActivitiesAdminController extends ActivityAdminController', $legacyAdminController);
        $this->assertStringNotContainsString('view_edit_galery_activity', $publicController);
        $this->assertStringNotContainsString('destroy_activity', $publicController);
        $this->assertStringNotContainsString('delete_image_activity', $publicController);
        $this->assertStringNotContainsString('delete_cover_activity', $publicController);
        $this->assertStringContainsString('StoreActivityAdminRequest $request', $activityController);
        $this->assertStringContainsString('UpdateActivityAdminRequest $request', $activityController);
        $this->assertStringContainsString("\$request->validated()", $activityController);
        $this->assertStringContainsString("ActivitiesImages::create", $activityController);

        foreach ([
            "'contract_rate' => ['required', 'integer', 'min:1']",
            "'qty' => ['required', 'integer', 'min:1', 'gte:min_pax']",
            "'min_pax' => ['required', 'integer', 'min:1']",
            "'validity' => ['required', 'date']",
            "'partners_id' => ['required', 'exists:partners,id']",
        ] as $validationRule) {
            $this->assertStringContainsString($validationRule, $storeRequest);
            $this->assertStringContainsString($validationRule, $updateRequest);
        }

        $this->assertStringContainsString("'cover' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']", $storeRequest);
        $this->assertStringContainsString("'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']", $updateRequest);
        $this->assertStringContainsString("'status' => ['required', 'in:Active,Draft,Archived']", $updateRequest);
        $this->assertStringContainsString('Operations Activities memakai namespace/backend UI modern, Form Request, service, dan view model.', $roadmap);
    }

    public function test_activities_phase_5_service_layer_and_view_models_are_in_place(): void
    {
        $activityController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Activities/ActivityAdminController.php'));
        $galleryController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Activities/ActivityGalleryAdminController.php'));
        $indexView = file_get_contents(resource_path('views/backend/operations/activities/index.blade.php'));
        $detailView = file_get_contents(resource_path('views/backend/operations/activities/detail.blade.php'));
        $inventoryService = file_get_contents(app_path('Services/Activities/ActivityInventoryService.php'));
        $pricingService = file_get_contents(app_path('Services/Activities/ActivityPricingService.php'));
        $assetService = file_get_contents(app_path('Services/Activities/ActivityAssetService.php'));
        $auditService = file_get_contents(app_path('Services/Activities/ActivityAuditService.php'));
        $indexViewModel = file_get_contents(app_path('ViewModels/Activities/ActivityIndexViewModel.php'));
        $detailViewModel = file_get_contents(app_path('ViewModels/Activities/ActivityDetailViewModel.php'));
        $quoteRequest = file_get_contents(app_path('Http/Requests/Activities/QuoteActivityRequest.php'));
        $activityDetailJs = file_get_contents(resource_path('frontend/js/landing-page/activities/detail.js'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));

        foreach ([
            app_path('Services/Activities/ActivityInventoryService.php'),
            app_path('Services/Activities/ActivityPricingService.php'),
            app_path('Services/Activities/ActivityAssetService.php'),
            app_path('Services/Activities/ActivityAuditService.php'),
            app_path('ViewModels/Activities/ActivityIndexViewModel.php'),
            app_path('ViewModels/Activities/ActivityDetailViewModel.php'),
        ] as $path) {
            $this->assertFileExists($path);
        }

        $this->assertStringContainsString('ActivityInventoryService $inventory', $activityController);
        $this->assertStringContainsString('ActivityAssetService $assets', $activityController);
        $this->assertStringContainsString('ActivityAuditService $audit', $activityController);
        $this->assertStringContainsString('ActivityAssetService $assets', $galleryController);
        $this->assertStringContainsString('ActivityAuditService $audit', $galleryController);
        $this->assertStringContainsString('$assets,', $galleryController);
        $this->assertStringContainsString('$inventory->indexData()', $activityController);
        $this->assertStringContainsString('$inventory->detailData', $activityController);
        $this->assertStringContainsString('$assets->uploadCover', $activityController);
        $this->assertStringContainsString('$assets->deleteCover', $activityController);
        $this->assertStringContainsString('$assets->uploadGalleryImage', $activityController);
        $this->assertStringContainsString('$audit->userLog', $activityController);
        $this->assertStringContainsString('$activityIndex->stats()', $indexView);
        $this->assertStringContainsString('$activityIndex->rows()', $indexView);
        $this->assertStringContainsString("currencyFormatIdr(\$row['published_rate_idr'])", $indexView);
        $this->assertStringContainsString('$activityDetail->stats()', $detailView);
        $this->assertStringContainsString('$activityDetail->taxAmount()', $detailView);
        $this->assertStringContainsString('$activityDetail->taxAmountIdr()', $detailView);
        $this->assertStringContainsString('$activityDetail->translationGroups()', $detailView);
        $this->assertStringContainsString('<x-backend.detail-layout class="activity-detail-layout">', $detailView);
        $this->assertStringContainsString('backend-detail-side-card activity-detail-context-panel', $detailView);
        $this->assertStringContainsString('backend-detail-side-list', $detailView);
        $this->assertStringContainsString('backend-detail-side-actions', $detailView);
        $this->assertStringContainsString('activity-detail-info-grid', $detailView);
        $this->assertStringContainsString('activity-detail-gallery-preview', $detailView);
        $this->assertStringContainsString('Content and Translations', $detailView);
        $this->assertStringContainsString('activity-detail-richtext', $detailView);
        $this->assertStringContainsString('decoding="async"', $detailView);
        $this->assertStringContainsString('width="360"', $detailView);
        $this->assertStringNotContainsString('data-backend-richtext', $detailView);
        $this->assertStringNotContainsString('activity-gallery__grid', $detailView);
        $this->assertStringNotContainsString('img-fluid', $detailView);
        $this->assertStringNotContainsString('p-3 mb-0', $detailView);
        $this->assertStringNotContainsString('$contractUsd =', $indexView);
        $this->assertStringNotContainsString('$contractUsd =', $detailView);
        $this->assertStringNotContainsString('$publishedRate =', $indexView);
        $this->assertStringNotContainsString('$publishedRate =', $detailView);
        $this->assertStringContainsString('new ActivityIndexViewModel', $inventoryService);
        $this->assertStringContainsString('new ActivityDetailViewModel', $inventoryService);
        $this->assertStringContainsString('public function quote', $pricingService);
        $this->assertStringNotContainsString('public function publishedRate', $pricingService);
        $this->assertStringNotContainsString('public function contractRateUsd', $pricingService);
        $this->assertStringContainsString('priceAvailable', $indexViewModel . $detailViewModel);
        $this->assertStringContainsString('published_rate_idr', $indexViewModel);
        $this->assertStringContainsString('dualCurrencyPrice', $detailViewModel);
        $this->assertStringContainsString("'travel_date'", $quoteRequest);
        $this->assertStringContainsString('travel_date: travelDateInput.value', $activityDetailJs);
        $this->assertStringContainsString('replaceCover', $assetService);
        $this->assertStringContainsString('uploadGalleryImage', $assetService);
        $this->assertStringContainsString("UserLog::create", $auditService);
        $this->assertStringContainsString('function rows()', $indexViewModel);
        $this->assertStringContainsString('function stats()', $detailViewModel);
        $activityScss = file_get_contents(resource_path('backend/scss/operations/activities/_index.scss'));
        $this->assertStringNotContainsString('grid-template-columns: minmax(0, 3fr) minmax(240px, 1fr);', $activityScss);
        $this->assertStringContainsString('.activity-detail-profile-card', $activityScss);
        $this->assertStringContainsString('grid-template-columns: minmax(220px, 34%) minmax(0, 1fr);', $activityScss);
        $this->assertStringContainsString('.activity-detail-info-grid', $activityScss);
        $this->assertStringContainsString('.activity-detail-gallery-section', $activityScss);
        $this->assertStringContainsString('.activity-detail-gallery-preview', $activityScss);
        $this->assertStringContainsString('.activity-detail-translation-group', $activityScss);
        $this->assertStringContainsString('.activity-detail-richtext', $activityScss);
        $this->assertStringContainsString('height: 220px;', $activityScss);
        $this->assertStringContainsString(
            '- [x] Operations Activities memakai namespace/backend UI modern, Form Request, service, dan view model.',
            $roadmap
        );
    }

    public function test_activities_phase_6_final_acceptance_structure_is_complete(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $manifest = file_get_contents(public_path('mix-manifest.json'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $activityViews = collect([
            resource_path('views/backend/operations/activities/index.blade.php'),
            resource_path('views/backend/operations/activities/detail.blade.php'),
            resource_path('views/backend/operations/activities/forms/create.blade.php'),
            resource_path('views/backend/operations/activities/forms/edit.blade.php'),
            resource_path('views/backend/operations/activities/forms/gallery-edit.blade.php'),
            resource_path('views/admin/activitiesadmin.blade.php'),
            resource_path('views/admin/activitiesadmindetail.blade.php'),
        ])->map(fn ($path) => file_get_contents($path))->implode("\n");

        foreach ([
            "name('activities.index')",
            "name('activities.show')",
            "name('activities.create')",
            "name('activities.edit')",
            "name('activities.gallery.edit')",
            "name('gallery-activities.update')",
            "name('activities.store')",
            "name('activities.update')",
            "name('activities.destroy')",
            "name('activities.cover.destroy')",
            "name('activities.images.destroy')",
        ] as $routeName) {
            $this->assertStringContainsString($routeName, $routes);
        }

        foreach ([
            public_path('build/backend/js/operations/activities/index.js'),
            public_path('build/backend/js/operations/activities/forms.js'),
            public_path('build/backend/css/operations/activities/index.css'),
            public_path('build/backend/css/operations/activities/forms.css'),
        ] as $path) {
            $this->assertFileExists($path);
        }

        $this->assertStringContainsString('/build/backend/js/operations/activities/index.js', $manifest);
        $this->assertStringContainsString('/build/backend/js/operations/activities/forms.js', $manifest);
        $this->assertStringContainsString('/build/backend/css/operations/activities/index.css', $manifest);
        $this->assertStringContainsString('/build/backend/css/operations/activities/forms.css', $manifest);
        $this->assertStringNotContainsString('card-box', $activityViews);
        $this->assertStringNotContainsString('btn btn-', $activityViews);
        $this->assertStringNotContainsString('style=', $activityViews);
        $this->assertStringNotContainsString('onclick', $activityViews);
        $this->assertStringContainsString('Operations Activities memakai namespace/backend UI modern, Form Request, service, dan view model.', $roadmap);
    }

    public function test_hotel_admin_price_and_promo_create_forms_use_backend_standard_assets(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/HotelsAdminController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $normalPriceView = file_get_contents(resource_path('views/backend/operations/hotels/forms/normal-price-create.blade.php'));
        $promoView = file_get_contents(resource_path('views/backend/operations/hotels/forms/promo-create.blade.php'));
        $priceRowPartial = file_get_contents(resource_path('views/backend/operations/hotels/forms/partials/normal-price-row.blade.php'));
        $legacyNormalPriceWrapper = file_get_contents(resource_path('views/backend/operations/hotels/forms/add-normal-price.blade.php'));
        $legacyPromoWrapper = file_get_contents(resource_path('views/backend/operations/hotels/forms/add-promo.blade.php'));
        $promoController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelPromoAdminController.php'));
        $promoRequest = file_get_contents(app_path('Http/Requests/StoreHotelPromoRequest.php'));
        $hotelPromoModel = file_get_contents(app_path('Models/HotelPromo.php'));
        $hotelPromoMigration = file_get_contents(database_path('migrations/2022_09_20_094058_create_hotel_promos_table.php'));
        $accommodationDocs = file_get_contents(base_path('docs/modules/accommodation.md'));
        $formContent = $normalPriceView . "\n" . $promoView . "\n" . $priceRowPartial;
        $scss = file_get_contents(resource_path('backend/scss/operations/hotels/_forms.scss'));
        $js = file_get_contents(resource_path('backend/js/operations/hotels/forms.js'));
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('views/backend/operations/hotels/forms/normal-price-create.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/hotels/forms/promo-create.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/hotels/forms/partials/normal-price-row.blade.php'));
        $this->assertFileExists(resource_path('backend/js/operations/hotels/forms.js'));
        $this->assertFileExists(resource_path('backend/scss/operations/hotels/forms-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/operations/hotels/_forms.scss'));
        $this->assertStringContainsString("@include('backend.operations.hotels.forms.normal-price-create')", $legacyNormalPriceWrapper);
        $this->assertStringContainsString("@include('backend.operations.hotels.forms.promo-create')", $legacyPromoWrapper);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.normal-price-create'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.promo-create'", $controller);
        $this->assertStringContainsString("name('admin.hotels.normal-prices.store')", $routes);
        $this->assertStringContainsString("name('admin.hotels.normal-prices.update')", $routes);
        $this->assertStringContainsString("mix('build/backend/css/operations/hotels/forms.css')", $normalPriceView);
        $this->assertStringContainsString("mix('build/backend/js/operations/hotels/forms.js')", $normalPriceView);
        $this->assertStringContainsString("mix('build/backend/css/operations/hotels/forms.css')", $promoView);
        $this->assertStringContainsString("mix('build/backend/js/operations/hotels/forms.js')", $promoView);
        $this->assertStringContainsString("resources/backend/js/operations/hotels/forms.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/operations/hotels/forms-entry.scss", $mix);
        $this->assertStringContainsString('<x-backend.page-hero', $formContent);
        $this->assertStringContainsString('backend-page-primary-action', $formContent);
        $this->assertStringContainsString("route('admin.panel-main.view')", $formContent);
        $this->assertStringContainsString("route('admin.hotels.index')", $formContent);
        $this->assertStringContainsString("route('admin.hotels.show'", $formContent);
        $this->assertStringContainsString("route('admin.hotels.normal-prices.store')", $formContent);
        $this->assertStringContainsString("route('admin.hotels.promos.store')", $formContent);
        $this->assertStringContainsString('<x-backend.breadcrumb-toolbar', $formContent);
        $this->assertStringContainsString('class="hotel-form-toolbar"', $formContent);
        $this->assertStringContainsString('backend-feedback hotel-form-feedback', $formContent);
        $this->assertStringContainsString('backend-alert backend-alert--', $formContent);
        $this->assertStringContainsString('backend-panel backend-form-panel hotel-form-panel', $formContent);
        $this->assertStringContainsString('backend-section-header', $formContent);
        $this->assertStringContainsString('backend-form-panel__body', $formContent);
        $this->assertStringContainsString('backend-section-header__label', $formContent);
        $this->assertStringContainsString('backend-status-badge backend-status-badge--', $formContent);
        $this->assertStringContainsString('data-hotel-price-repeater', $normalPriceView);
        $this->assertStringContainsString('data-hotel-price-template', $normalPriceView);
        $this->assertStringContainsString('data-hotel-price-add', $normalPriceView);
        $this->assertStringContainsString('data-hotel-price-remove', $priceRowPartial);
        $this->assertStringContainsString('<x-backend.breadcrumb-toolbar', $normalPriceView);
        $this->assertStringContainsString('<x-backend.detail-layout>', $normalPriceView);
        $this->assertStringContainsString('<x-slot name="main">', $normalPriceView);
        $this->assertStringContainsString('<x-slot name="side">', $normalPriceView);
        $this->assertStringContainsString('Hotel & Room', $normalPriceView);
        $this->assertStringContainsString('Price Period', $normalPriceView);
        $this->assertStringContainsString('Pricing Context', $normalPriceView);
        $this->assertStringContainsString('name="hotel_context"', $normalPriceView);
        $this->assertStringNotContainsString('name="author"', $normalPriceView);
        $this->assertStringNotContainsString('@include(\'admin.usd-rate\')', $normalPriceView);
        $this->assertStringContainsString('data-backend-picker="date"', $priceRowPartial);
        $this->assertStringContainsString('data-backend-picker-format="yyyy-mm-dd"', $priceRowPartial);
        $this->assertStringContainsString('data-backend-money-unit="IDR"', $priceRowPartial);
        $this->assertStringContainsString('data-backend-money-unit="USD"', $priceRowPartial);
        $this->assertStringNotContainsString('type="date"', $priceRowPartial);
        $this->assertStringNotContainsString('type="number"', $priceRowPartial);
        $this->assertStringContainsString('<x-backend.breadcrumb-toolbar', $promoView);
        $this->assertStringContainsString('<x-backend.detail-layout>', $promoView);
        $this->assertStringContainsString('<x-slot name="main">', $promoView);
        $this->assertStringContainsString('<x-slot name="side">', $promoView);
        $this->assertStringContainsString('name="hotel_context"', $promoView);
        $this->assertStringContainsString('backend-detail-side-card hotel-promo-create-context-panel', $promoView);
        $this->assertStringContainsString('Initial Status', $promoView);
        $this->assertStringContainsString('Hotel Context', $promoView);
        $this->assertStringContainsString('Promo Rules', $promoView);
        $this->assertStringContainsString('Pricing Context', $promoView);
        $this->assertStringContainsString('Basic Information', $promoView);
        $this->assertStringContainsString('Promotion Period', $promoView);
        $this->assertStringContainsString('Pricing Inputs', $promoView);
        $this->assertStringContainsString('Benefits and Inclusions', $promoView);
        $this->assertStringContainsString('data-backend-picker="date"', $promoView);
        $this->assertStringContainsString('data-backend-picker-format="yyyy-mm-dd"', $promoView);
        $this->assertStringContainsString('data-backend-money-unit="IDR"', $promoView);
        $this->assertStringContainsString('data-backend-money-unit="USD"', $promoView);
        $this->assertStringContainsString('backend-translation-group', $promoView);
        $this->assertStringContainsString('backend-translation-grid', $promoView);
        $this->assertStringContainsString('backend-translation-field', $promoView);
        $this->assertStringContainsString("'name' => 'benefits_traditional'", $promoView);
        $this->assertStringContainsString("'name' => 'benefits_simplified'", $promoView);
        $this->assertStringContainsString("'name' => 'include_traditional'", $promoView);
        $this->assertStringContainsString("'name' => 'include_simplified'", $promoView);
        $this->assertStringContainsString("'name' => 'additional_info_traditional'", $promoView);
        $this->assertStringContainsString("'name' => 'additional_info_simplified'", $promoView);
        $this->assertStringContainsString('name="minimum_stay"', $promoView);
        $this->assertStringContainsString('name="contract_rate"', $promoView);
        $this->assertStringContainsString('name="markup"', $promoView);
        $this->assertStringContainsString("HotelRoom::where('hotels_id', \$hotel->id)", $promoController);
        $this->assertStringContainsString("Crypt::encryptString((string) \$hotel->id)", $promoController);
        $this->assertStringContainsString('$request->validated()', $promoController);
        $this->assertStringContainsString('$request->resolvedHotelId()', $promoController);
        $this->assertStringContainsString('DB::transaction', $promoController);
        $this->assertStringContainsString('UserLog::create', $promoController);
        $this->assertStringContainsString("'status' => 'Draft'", $promoController);
        $this->assertStringContainsString("'author' => auth()->id()", $promoController);
        $this->assertStringContainsString("'hotel_context' => ['required', 'string']", $promoRequest);
        $this->assertStringContainsString('public function resolvedHotelId(): ?int', $promoRequest);
        $this->assertStringContainsString('$this->roomBelongsToHotelRule()', $promoRequest);
        $this->assertStringContainsString('Gate::any([\'posDev\', \'posAuthor\'])', $promoRequest);
        $this->assertStringContainsString("'benefits_traditional' => ['nullable', 'string']", $promoRequest);
        $this->assertStringContainsString("'additional_info_simplified' => ['nullable', 'string']", $promoRequest);
        $this->assertStringContainsString('class HotelPromo extends Model', $hotelPromoModel);
        $this->assertStringContainsString("Schema::create('hotel_promos'", $hotelPromoMigration);
        $this->assertFileDoesNotExist(app_path('Models/HotelPromoPrice.php'));
        $this->assertStringContainsString('Source of truth untuk Promotion Price saat ini adalah record `hotel_promos`.', $accommodationDocs);
        $this->assertStringContainsString('[data-hotel-price-repeater]', $js);
        $this->assertStringContainsString('[data-hotel-price-add]', $js);
        $this->assertStringContainsString('[data-hotel-price-remove]', $js);
        $this->assertStringContainsString('window.initBackendMoneyInputs?.(clone)', $js);
        $this->assertStringContainsString('window.initBackendDatePickers?.(clone)', $js);
        $this->assertStringContainsString('.hotel-form-layout', $scss);
        $this->assertStringContainsString('.hotel-form-panel', $scss);
        $this->assertStringContainsString('.hotel-form-price-row', $scss);
        $this->assertStringNotContainsString('class="backend-form-control date-picker', $promoView);
        $this->assertStringNotContainsString('name="author"', $promoView);
        $this->assertStringNotContainsString('name="service"', $promoView);
        $this->assertStringNotContainsString('name="status"', $promoView);
        $this->assertStringNotContainsString('cancellation_policy', $promoView);
        $this->assertStringNotContainsString('hotel-form-layout', $normalPriceView);
        $this->assertStringNotContainsString('hotel-form-sidebar', $normalPriceView);
        $this->assertStringNotContainsString('hotel-form-layout', $promoView);
        $this->assertStringNotContainsString('hotel-form-sidebar', $promoView);
        $this->assertStringNotContainsString('<script>', $formContent);
        $this->assertStringNotContainsString('onkeyup=', $formContent);
        $this->assertStringNotContainsString('card-box', $formContent);
        $this->assertStringNotContainsString('style=', $formContent);
        $this->assertStringNotContainsString('action="/fadd-price"', $formContent);
        $this->assertStringNotContainsString('action="/fadd-promo"', $formContent);
        $this->assertStringNotContainsString('href="/detail-hotel-', $formContent);
        $this->assertStringNotContainsString('btn btn-primary', $formContent);
        $this->assertStringNotContainsString('btn btn-danger', $formContent);
    }

    public function test_hotel_admin_additional_charge_crud_uses_detail_modal_standard(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelAdditionalChargeAdminController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $model = file_get_contents(app_path('Models/OptionalRate.php'));
        $modal = file_get_contents(resource_path('views/backend/operations/hotels/modals/additional-charge-form.blade.php'));
        $partial = file_get_contents(resource_path('views/backend/operations/hotels/partials/additional-charges.blade.php'));
        $auditSummary = file_get_contents(resource_path('views/backend/operations/hotels/partials/audit-summary.blade.php'));
        $storeRequest = file_get_contents(app_path('Http/Requests/StoreHotelAdditionalChargeRequest.php'));
        $updateRequest = file_get_contents(app_path('Http/Requests/UpdateHotelAdditionalChargeRequest.php'));
        $content = $modal . "\n" . $partial . "\n" . $auditSummary;

        $this->assertFileDoesNotExist(resource_path('views/backend/operations/hotels/forms/additional-charge-create.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/backend/operations/hotels/forms/additional-charge-edit.blade.php'));
        $this->assertStringNotContainsString("name('admin.hotels.additional-charges.create')", $routes);
        $this->assertStringNotContainsString("name('admin.hotels.additional-charges.edit')", $routes);
        $this->assertStringContainsString("name('admin.hotels.additional-charges.store')", $routes);
        $this->assertStringContainsString("name('admin.hotels.additional-charges.update')", $routes);
        $this->assertStringContainsString("name('admin.hotels.additional-charges.destroy')", $routes);
        $this->assertStringContainsString('hotel-additional-charge-form-modal', $modal);
        $this->assertStringContainsString('hotelAdditionalChargeAdd{{ $hotel->id }}', $content);
        $this->assertStringContainsString('hotelAdditionalChargeEdit{{ $additionalCharge->id }}', $content);
        $this->assertStringContainsString("route('admin.hotels.additional-charges.store')", $modal);
        $this->assertStringContainsString("route('admin.hotels.additional-charges.update'", $modal);
        $this->assertStringContainsString("route('admin.hotels.additional-charges.destroy'", $partial);
        $this->assertStringContainsString('data-backend-picker="date"', $modal);
        $this->assertStringContainsString('data-backend-money-unit="IDR"', $modal);
        $this->assertStringContainsString('data-backend-money-unit="USD"', $modal);
        $this->assertStringContainsString('data-backend-richtext="true"', $modal);
        $this->assertStringContainsString('name="hotel_id"', $modal);
        $this->assertStringNotContainsString('name="service_id"', $modal);
        $this->assertStringNotContainsString('name="author"', $modal);
        $this->assertStringContainsString('store(StoreHotelAdditionalChargeRequest $request, HotelAuditService $audit)', $controller);
        $this->assertStringContainsString('update(UpdateHotelAdditionalChargeRequest $request, $id, HotelAuditService $audit)', $controller);
        $this->assertStringContainsString('destroy(Request $request, $id, HotelAuditService $audit)', $controller);
        $this->assertStringContainsString('lockForUpdate()', $controller);
        $this->assertStringContainsString('mandatoryDates', $controller);
        $this->assertStringContainsString("'hotels_id' => \$hotel->id", $controller);
        $this->assertStringContainsString("'service_id' => \$hotel->id", $controller);
        $this->assertStringContainsString("'service_id' => \$hotelId", $controller);
        $this->assertStringContainsString("'must_buy_start' => \$mandatoryDates['start']", $controller);
        $this->assertStringContainsString("'must_buy_end' => \$mandatoryDates['end']", $controller);
        $this->assertStringContainsString("Gate::any(['posDev', 'posAuthor', 'posAdm'])", $storeRequest);
        $this->assertStringContainsString("Gate::any(['posDev', 'posAuthor', 'posAdm'])", $updateRequest);
        $this->assertStringContainsString('getMandatoryStartAttribute', $model);
        $this->assertStringContainsString('getMandatoryEndAttribute', $model);
        $this->assertStringContainsString('Backend Hotel Detail Additional Charge Management', file_get_contents(base_path('docs/modules/accommodation.md')));
        $this->assertStringNotContainsString('<script>', $content);
        $this->assertStringNotContainsString('onkeyup=', $content);
        $this->assertStringNotContainsString('card-box', $content);
        $this->assertStringNotContainsString('style=', $content);
        $this->assertStringNotContainsString('action="/fadd-additional-charge"', $content);
        $this->assertStringNotContainsString('action="/fupdate-additional-charge/', $content);
        $this->assertStringNotContainsString('href="/detail-hotel-', $content);
        $this->assertStringNotContainsString('btn btn-primary', $content);
        $this->assertStringNotContainsString('btn btn-danger', $content);
    }

    public function test_hotel_admin_package_forms_use_backend_standard_assets(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/HotelsAdminController.php'));
        $packageController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelPackageAdminController.php'));
        $storeRequest = file_get_contents(app_path('Http/Requests/StoreHotelPackageRequest.php'));
        $updateRequest = file_get_contents(app_path('Http/Requests/UpdateHotelPackageRequest.php'));
        $accommodationDocs = file_get_contents(base_path('docs/modules/accommodation.md'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $packageCreateView = file_get_contents(resource_path('views/backend/operations/hotels/forms/package-create.blade.php'));
        $packageEditView = file_get_contents(resource_path('views/backend/operations/hotels/forms/package-edit.blade.php'));
        $formContent = $packageCreateView . "\n" . $packageEditView;

        $this->assertFileExists(resource_path('views/backend/operations/hotels/forms/package-create.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/hotels/forms/package-edit.blade.php'));
        $this->assertStringContainsString("view('backend.operations.hotels.forms.package-create'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.package-edit'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.package-create'", $packageController);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.package-edit'", $packageController);
        $this->assertStringContainsString("name('hotels.packages.create')", $routes);
        $this->assertStringContainsString("name('hotels.packages.edit')", $routes);
        $this->assertStringContainsString("name('admin.hotels.packages.status.update')", $routes);
        $this->assertStringContainsString("name('admin.hotels.packages.store')", $routes);
        $this->assertStringContainsString("name('admin.hotels.packages.update')", $routes);
        $this->assertStringContainsString("mix('build/backend/css/operations/hotels/forms.css')", $packageCreateView);
        $this->assertStringContainsString("mix('build/backend/js/operations/hotels/forms.js')", $packageCreateView);
        $this->assertStringContainsString("mix('build/backend/css/operations/hotels/forms.css')", $packageEditView);
        $this->assertStringContainsString("mix('build/backend/js/operations/hotels/forms.js')", $packageEditView);
        $this->assertStringContainsString('<x-backend.page-hero', $formContent);
        $this->assertStringContainsString('backend-page-primary-action', $formContent);
        $this->assertStringContainsString("route('admin.panel-main.view')", $formContent);
        $this->assertStringContainsString("route('admin.hotels.index')", $formContent);
        $this->assertStringContainsString("route('admin.hotels.show'", $formContent);
        $this->assertStringContainsString("route('admin.hotels.packages.store')", $packageCreateView);
        $this->assertStringContainsString("route('admin.hotels.packages.update'", $packageEditView);
        $this->assertStringContainsString('<x-backend.breadcrumb-toolbar', $formContent);
        $this->assertStringContainsString('class="hotel-form-toolbar"', $formContent);
        $this->assertStringContainsString('<x-backend.detail-layout>', $formContent);
        $this->assertStringContainsString('<x-slot name="main">', $formContent);
        $this->assertStringContainsString('<x-slot name="side">', $formContent);
        $this->assertStringContainsString('backend-feedback hotel-form-feedback', $formContent);
        $this->assertStringContainsString('backend-alert backend-alert--', $formContent);
        $this->assertStringContainsString('backend-panel backend-form-panel hotel-form-panel', $formContent);
        $this->assertStringContainsString('backend-detail-side-card hotel-package-create-context-panel', $packageCreateView);
        $this->assertStringContainsString('backend-detail-side-card hotel-package-edit-context-panel', $packageEditView);
        $this->assertStringContainsString('backend-section-header', $formContent);
        $this->assertStringContainsString('backend-form-panel__body', $formContent);
        $this->assertStringContainsString('backend-section-header__label', $formContent);
        $this->assertStringContainsString('backend-status-badge backend-status-badge--', $formContent);
        $this->assertStringContainsString('Basic Information', $formContent);
        $this->assertStringContainsString('Package Configuration', $formContent);
        $this->assertStringContainsString('Availability', $formContent);
        $this->assertStringContainsString('Pricing Inputs', $formContent);
        $this->assertStringContainsString('Benefits and Inclusions', $formContent);
        $this->assertStringContainsString('Cancellation Policy', $formContent);
        $this->assertStringContainsString('Initial Status', $packageCreateView);
        $this->assertStringContainsString('Current Status', $packageEditView);
        $this->assertStringContainsString('Metadata', $packageEditView);
        $this->assertStringContainsString('Package Summary', $packageEditView);
        $this->assertStringContainsString('name="rooms_id"', $formContent);
        $this->assertStringContainsString('name="hotel_context"', $formContent);
        $this->assertStringContainsString('name="duration"', $formContent);
        $this->assertStringContainsString('name="stay_period_start"', $formContent);
        $this->assertStringContainsString('name="stay_period_end"', $formContent);
        $this->assertStringContainsString('name="contract_rate"', $formContent);
        $this->assertStringContainsString('name="markup"', $formContent);
        $this->assertStringContainsString('name="status"', $packageEditView);
        $this->assertStringContainsString('data-backend-picker="date"', $formContent);
        $this->assertStringContainsString('data-backend-money-unit="IDR"', $formContent);
        $this->assertStringContainsString('data-backend-money-unit="USD"', $formContent);
        $this->assertStringContainsString('backend-translation-group', $formContent);
        $this->assertStringContainsString('backend-translation-grid', $formContent);
        $this->assertStringContainsString('backend-translation-field', $formContent);
        $this->assertStringContainsString('Crypt::encryptString((string) $hotel->id)', $packageController);
        $this->assertStringContainsString('$request->resolvedHotelId()', $packageController);
        $this->assertStringContainsString('lockForUpdate()', $packageController);
        $this->assertStringContainsString("'status' => 'Draft'", $packageController);
        $this->assertStringContainsString("'hotel_context' => ['required', 'string']", $storeRequest);
        $this->assertStringContainsString("'hotel_context' => ['required', 'string']", $updateRequest);
        $this->assertStringContainsString('public function resolvedHotelId(): ?int', $storeRequest);
        $this->assertStringContainsString('public function resolvedHotelId(): ?int', $updateRequest);
        $this->assertStringContainsString('Backend Add/Edit Hotel Package', $accommodationDocs);
        $this->assertFileDoesNotExist(app_path('Models/HotelPackagePrice.php'));
        $this->assertStringNotContainsString('name="author"', $formContent);
        $this->assertStringNotContainsString('class="backend-form-control date-picker', $formContent);
        $this->assertStringNotContainsString('hotel-form-layout', $formContent);
        $this->assertStringNotContainsString('hotel-form-sidebar', $formContent);
        $this->assertStringNotContainsString('<script>', $formContent);
        $this->assertStringNotContainsString('onkeyup=', $formContent);
        $this->assertStringNotContainsString('card-box', $formContent);
        $this->assertStringNotContainsString('style=', $formContent);
        $this->assertStringNotContainsString('action="/fadd-package"', $formContent);
        $this->assertStringNotContainsString('action="/fedit-package-', $formContent);
        $this->assertStringNotContainsString('href="/detail-hotel-', $formContent);
        $this->assertStringNotContainsString('btn btn-primary', $formContent);
        $this->assertStringNotContainsString('btn btn-danger', $formContent);
    }

    public function test_hotel_admin_promo_edit_form_and_normal_price_modal_use_backend_standard_assets(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/HotelsAdminController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $promoView = file_get_contents(resource_path('views/backend/operations/hotels/forms/promo-edit.blade.php'));
        $promoController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelPromoAdminController.php'));
        $promoRequest = file_get_contents(app_path('Http/Requests/UpdateHotelPromoRequest.php'));
        $accommodationDocs = file_get_contents(base_path('docs/modules/accommodation.md'));
        $normalPricePartial = file_get_contents(resource_path('views/backend/operations/hotels/partials/normal-prices.blade.php'));
        $normalPriceModal = file_get_contents(resource_path('views/backend/operations/hotels/modals/normal-price-edit.blade.php'));
        $formContent = $promoView . "\n" . $normalPricePartial . "\n" . $normalPriceModal;

        $this->assertFileDoesNotExist(resource_path('views/backend/operations/hotels/forms/normal-price-edit.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/hotels/modals/normal-price-edit.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/hotels/forms/promo-edit.blade.php'));
        $this->assertStringNotContainsString("view('backend.operations.hotels.forms.normal-price-edit'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.promo-edit'", $controller);
        $this->assertStringNotContainsString("name('admin.hotels.prices.edit')", $routes);
        $this->assertStringNotContainsString('/edit-hotel-price/{id}', $routes);
        $this->assertStringContainsString("name('admin.hotels.promos.edit')", $routes);
        $this->assertStringContainsString("name('admin.hotels.normal-prices.update')", $routes);
        $this->assertStringContainsString("name('admin.hotels.promos.update')", $routes);
        $this->assertStringContainsString("mix('build/backend/css/operations/hotels/forms.css')", $promoView);
        $this->assertStringContainsString("mix('build/backend/js/operations/hotels/forms.js')", $promoView);
        $this->assertStringContainsString('<x-backend.page-hero', $promoView);
        $this->assertStringContainsString('backend-page-primary-action', $promoView);
        $this->assertStringContainsString("route('admin.panel-main.view')", $promoView);
        $this->assertStringContainsString("route('admin.hotels.index')", $promoView);
        $this->assertStringContainsString("route('admin.hotels.show'", $promoView);
        $this->assertStringContainsString("route('admin.hotels.normal-prices.update'", $normalPriceModal);
        $this->assertStringContainsString("route('admin.hotels.promos.update'", $promoView);
        $this->assertStringContainsString('<x-backend.breadcrumb-toolbar', $promoView);
        $this->assertStringContainsString('class="hotel-form-toolbar"', $promoView);
        $this->assertStringContainsString('<x-backend.detail-layout>', $promoView);
        $this->assertStringContainsString('<x-slot name="main">', $promoView);
        $this->assertStringContainsString('<x-slot name="side">', $promoView);
        $this->assertStringContainsString('backend-feedback hotel-form-feedback', $promoView);
        $this->assertStringContainsString('backend-alert backend-alert--', $promoView);
        $this->assertStringContainsString('backend-panel backend-form-panel hotel-form-panel', $promoView);
        $this->assertStringContainsString('backend-detail-side-card hotel-promo-edit-context-panel', $promoView);
        $this->assertStringContainsString('backend-section-header', $formContent);
        $this->assertStringContainsString('backend-form-panel__body', $promoView);
        $this->assertStringContainsString('backend-section-header__label', $formContent);
        $this->assertStringContainsString('backend-status-badge backend-status-badge--', $formContent);
        $this->assertStringContainsString('Basic Information', $promoView);
        $this->assertStringContainsString('Promotion Period', $promoView);
        $this->assertStringContainsString('Pricing Inputs', $promoView);
        $this->assertStringContainsString('Benefits and Inclusions', $promoView);
        $this->assertStringContainsString('Current Status', $promoView);
        $this->assertStringContainsString('Metadata', $promoView);
        $this->assertStringContainsString('Hotel Context', $promoView);
        $this->assertStringContainsString('Promo Summary', $promoView);
        $this->assertStringContainsString('Pricing Context', $promoView);
        $this->assertStringContainsString('Related Actions', $promoView);
        $this->assertStringContainsString('hotel-normal-price-edit-modal', $normalPriceModal);
        $this->assertStringContainsString('data-target="#hotelNormalPriceEdit{{ $price->id }}"', $normalPricePartial);
        $this->assertStringContainsString('backend-modal__header', $normalPriceModal);
        $this->assertStringContainsString('backend-modal__body', $normalPriceModal);
        $this->assertStringContainsString('backend-modal__footer', $normalPriceModal);
        $this->assertStringContainsString('name="rooms_id"', $formContent);
        $this->assertStringContainsString('name="start_date"', $normalPriceModal);
        $this->assertStringContainsString('name="end_date"', $normalPriceModal);
        $this->assertStringContainsString('name="hotel_context"', $promoView);
        $this->assertStringContainsString('data-backend-picker="date"', $promoView);
        $this->assertStringContainsString('data-backend-picker="date"', $normalPriceModal);
        $this->assertStringContainsString('data-backend-money-unit="IDR"', $promoView);
        $this->assertStringContainsString('data-backend-money-unit="USD"', $promoView);
        $this->assertStringContainsString('data-backend-money-unit="IDR"', $normalPriceModal);
        $this->assertStringContainsString('data-backend-money-unit="USD"', $normalPriceModal);
        $this->assertStringContainsString('backend-translation-group', $promoView);
        $this->assertStringContainsString('backend-translation-grid', $promoView);
        $this->assertStringContainsString('backend-translation-field', $promoView);
        $this->assertStringContainsString('name="status"', $promoView);
        $this->assertStringContainsString('name="promotion_type"', $promoView);
        $this->assertStringContainsString('name="minimum_stay"', $promoView);
        $this->assertStringContainsString('name="contract_rate"', $formContent);
        $this->assertStringContainsString('name="markup"', $formContent);
        $this->assertStringContainsString('HotelPromo::with([\'hotels\', \'rooms\'])->findOrFail($id)', $promoController);
        $this->assertStringContainsString("Crypt::encryptString((string) \$hotel->id)", $promoController);
        $this->assertStringContainsString('$request->resolvedHotelId()', $promoController);
        $this->assertStringContainsString('lockForUpdate()', $promoController);
        $this->assertStringContainsString("'page' => 'detail-hotel#promo'", $promoController);
        $this->assertStringContainsString("'hotel_context' => ['required', 'string']", $promoRequest);
        $this->assertStringContainsString('public function resolvedHotelId(): ?int', $promoRequest);
        $this->assertStringContainsString('Gate::any([\'posDev\', \'posAuthor\'])', $promoRequest);
        $this->assertStringContainsString('Backend Edit Hotel Promo', $accommodationDocs);
        $this->assertStringNotContainsString('name="author"', $normalPriceModal);
        $this->assertStringNotContainsString('name="author"', $promoView);
        $this->assertStringNotContainsString('class="backend-form-control date-picker', $promoView);
        $this->assertStringNotContainsString('type="number" required data-backend-money-unit', $promoView);
        $this->assertStringNotContainsString('hotel-form-layout', $promoView);
        $this->assertStringNotContainsString('hotel-form-sidebar', $promoView);
        $this->assertStringNotContainsString('<script>', $formContent);
        $this->assertStringNotContainsString('onkeyup=', $formContent);
        $this->assertStringNotContainsString('card-box', $formContent);
        $this->assertStringNotContainsString('style=', $formContent);
        $this->assertStringNotContainsString("route('admin.hotels.prices.edit'", $normalPricePartial);
        $this->assertStringNotContainsString('action="/fedit-price-', $formContent);
        $this->assertStringNotContainsString('action="/fedit-promo-', $formContent);
        $this->assertStringNotContainsString('href="/detail-hotel-', $formContent);
        $this->assertStringNotContainsString('btn btn-primary', $formContent);
        $this->assertStringNotContainsString('btn btn-danger', $formContent);
    }

    public function test_hotels_admin_index_is_sourced_from_backend_operations_structure_and_assets(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/HotelsAdminController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $legacyWrapper = file_get_contents(resource_path('views/admin/hotelsadmin.blade.php'));
        $view = file_get_contents(resource_path('views/backend/operations/hotels/index.blade.php'));
        $scss = file_get_contents(resource_path('backend/scss/operations/hotels/_index.scss'));
        $js = file_get_contents(resource_path('backend/js/operations/hotels/index.js'));
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('views/backend/operations/hotels/index.blade.php'));
        $this->assertFileExists(resource_path('views/admin/hotelsadmin.blade.php'));
        $this->assertFileExists(resource_path('backend/js/operations/hotels/index.js'));
        $this->assertFileExists(resource_path('backend/scss/operations/hotels/index-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/operations/hotels/_index.scss'));
        $this->assertStringContainsString("@include('backend.operations.hotels.index')", $legacyWrapper);
        $this->assertStringContainsString("view('backend.operations.hotels.index'", $controller);
        $this->assertStringNotContainsString("view('admin.hotelsadmin'", $controller);
        $this->assertStringContainsString('$now = Carbon::now();', $controller);
        $this->assertStringContainsString('$queryDate = $now->toDateString();', $controller);
        $this->assertStringNotContainsString('$now = Carbon::now()->toDateString();', $controller);
        $this->assertStringContainsString("{{ \$now->format('d M Y') }}", $view);
        $this->assertStringContainsString("name('admin.hotels.create')", $routes);
        $this->assertStringContainsString("name('admin.hotels.show')", $routes);
        $this->assertStringContainsString("name('admin.hotels.edit')", $routes);
        $this->assertStringContainsString("name('admin.hotels.destroy')", $routes);
        $this->assertStringContainsString("mix('build/backend/css/operations/hotels/index.css')", $view);
        $this->assertStringContainsString("mix('build/backend/js/operations/hotels/index.js')", $view);
        $this->assertStringContainsString("resources/backend/js/operations/hotels/index.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/operations/hotels/index-entry.scss", $mix);
        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('backend-page-primary-action', $view);
        $this->assertStringContainsString("route('admin.panel-main.view')", $view);
        $this->assertStringContainsString('backend-page-toolbar hotels-admin-toolbar', $view);
        $this->assertStringContainsString('backend-feedback hotels-admin-feedback', $view);
        $this->assertStringContainsString('backend-alert backend-alert--', $view);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--4', $view);
        $this->assertStringContainsString('backend-kpi-card backend-kpi-card--', $view);
        $this->assertStringContainsString('backend-filter-panel hotels-admin-filter', $view);
        $this->assertStringContainsString('backend-panel hotels-admin-panel', $view);
        $this->assertStringContainsString('backend-section-header hotels-admin-panel__heading', $view);
        $this->assertStringContainsString('backend-section-header__label', $view);
        $this->assertStringContainsString('backend-table hotels-admin-table', $view);
        $this->assertStringContainsString('backend-table-card hotels-admin-card', $view);
        $this->assertStringContainsString('backend-status-badge backend-status-badge--', $view);
        $this->assertStringContainsString('backend-table-empty', $view);
        $this->assertStringContainsString('backend-empty-state', $view);
        $this->assertStringContainsString("route('admin.hotels.create')", $view);
        $this->assertStringContainsString("route('admin.hotels.show'", $view);
        $this->assertStringContainsString("route('admin.hotels.edit'", $view);
        $this->assertStringContainsString("route('admin.hotels.destroy'", $view);
        $this->assertStringContainsString('[data-hotel-filter="name"]', $js);
        $this->assertStringContainsString('[data-hotel-filter="location"]', $js);
        $this->assertStringContainsString('[data-hotel-delete]', $js);
        $this->assertStringContainsString('backend-filter-panel hotels-admin-filter', $view);
        $this->assertStringNotContainsString('.hotels-admin-filter', $scss);
        $this->assertStringNotContainsString('<script>', $view);
        $this->assertStringNotContainsString('onkeyup=', $view);
        $this->assertStringNotContainsString('card-box', $view);
        $this->assertStringNotContainsString('data-table table stripe hover', $view);
        $this->assertStringNotContainsString('style=', $view);
        $this->assertStringNotContainsString('href="/detail-hotel-', $view);
        $this->assertStringNotContainsString('href="/edit-hotel-', $view);
        $this->assertStringNotContainsString('action="/remove-hotel/', $view);
    }

    public function test_hotel_admin_detail_is_sourced_from_backend_operations_structure_and_assets(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/HotelsAdminController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $legacyWrapper = file_get_contents(resource_path('views/admin/hotelsadmindetail.blade.php'));
        $view = file_get_contents(resource_path('views/backend/operations/hotels/detail.blade.php'));
        $detailPartials = [
            'partials/profile-summary.blade.php',
            'partials/audit-summary.blade.php',
            'partials/contracts.blade.php',
            'partials/rooms.blade.php',
            'partials/extra-beds.blade.php',
            'partials/normal-prices.blade.php',
            'partials/promo-prices.blade.php',
            'partials/package-prices.blade.php',
            'partials/additional-charges.blade.php',
            'modals/additional-charge-form.blade.php',
            'modals/contract-preview.blade.php',
            'modals/extra-bed-form.blade.php',
            'modals/normal-price-edit.blade.php',
            'modals/room-preview.blade.php',
        ];
        $detailContent = collect($detailPartials)
            ->map(fn ($partial) => file_get_contents(resource_path("views/backend/operations/hotels/{$partial}")))
            ->prepend($view)
            ->implode("\n");
        $roomsPartial = file_get_contents(resource_path('views/backend/operations/hotels/partials/rooms.blade.php'));
        $additionalChargePartial = file_get_contents(resource_path('views/backend/operations/hotels/partials/additional-charges.blade.php'));
        $additionalChargeForm = file_get_contents(resource_path('views/backend/operations/hotels/modals/additional-charge-form.blade.php'));
        $extraBedPartial = file_get_contents(resource_path('views/backend/operations/hotels/partials/extra-beds.blade.php'));
        $extraBedForm = file_get_contents(resource_path('views/backend/operations/hotels/modals/extra-bed-form.blade.php'));
        $normalPricePartial = file_get_contents(resource_path('views/backend/operations/hotels/partials/normal-prices.blade.php'));
        $promoPricePartial = file_get_contents(resource_path('views/backend/operations/hotels/partials/promo-prices.blade.php'));
        $auditSummary = file_get_contents(resource_path('views/backend/operations/hotels/partials/audit-summary.blade.php'));
        $roomController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelRoomAdminController.php'));
        $additionalChargeController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Hotels/HotelAdditionalChargeAdminController.php'));
        $extraBedController = file_get_contents(app_path('Http/Controllers/ExtraBedController.php'));
        $storeExtraBedRequest = file_get_contents(app_path('Http/Requests/StoreExtraBedRequest.php'));
        $updateExtraBedRequest = file_get_contents(app_path('Http/Requests/UpdateExtraBedRequest.php'));
        $accommodationDocs = file_get_contents(base_path('docs/modules/accommodation.md'));
        $viewModel = file_get_contents(app_path('ViewModels/Hotels/HotelDetailViewModel.php'));
        $inventoryService = file_get_contents(app_path('Services/Hotels/HotelInventoryService.php'));
        $scss = file_get_contents(resource_path('backend/scss/operations/hotels/_detail.scss'));
        $modalScss = file_get_contents(resource_path('backend/scss/components/_backend-modal.scss'));
        $statusScss = file_get_contents(resource_path('backend/scss/components/_backend-status.scss'));
        $js = file_get_contents(resource_path('backend/js/operations/hotels/detail.js'));
        $backendAppJs = file_get_contents(resource_path('backend/js/app.js'));
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('views/backend/operations/hotels/detail.blade.php'));
        foreach ($detailPartials as $partial) {
            $this->assertFileExists(resource_path("views/backend/operations/hotels/{$partial}"));
        }
        $this->assertFileDoesNotExist(resource_path('views/admin/extrabed.blade.php'));
        $this->assertFileExists(resource_path('views/admin/hotelsadmindetail.blade.php'));
        $this->assertFileExists(resource_path('backend/js/operations/hotels/detail.js'));
        $this->assertFileExists(resource_path('backend/scss/operations/hotels/detail-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/operations/hotels/_detail.scss'));
        $this->assertStringContainsString("@include('backend.operations.hotels.detail')", $legacyWrapper);
        $this->assertStringContainsString("view('backend.operations.hotels.detail'", $controller);
        $this->assertStringNotContainsString("view('admin.hotelsadmindetail'", $controller);
        $this->assertStringContainsString("name('hotels.show')", $routes);
        $this->assertStringContainsString("name('hotels.room.create')", $routes);
        $this->assertStringContainsString("name('hotels.room.edit')", $routes);
        $this->assertStringContainsString("name('hotels.room.status.update')", $routes);
        $this->assertStringNotContainsString("name('admin.hotels.prices.edit')", $routes);
        $this->assertStringContainsString("name('hotels.promos.create')", $routes);
        $this->assertStringContainsString("name('admin.hotels.promos.edit')", $routes);
        $this->assertStringContainsString("name('admin.hotels.promos.status.update')", $routes);
        $this->assertStringContainsString("name('hotels.packages.create')", $routes);
        $this->assertStringContainsString("name('hotels.packages.edit')", $routes);
        $this->assertStringNotContainsString("name('admin.hotels.additional-charges.create')", $routes);
        $this->assertStringNotContainsString("name('admin.hotels.additional-charges.edit')", $routes);
        $this->assertStringContainsString("name('hotels.room.delete')", $routes);
        $this->assertStringContainsString("name('admin.hotels.normal-prices.destroy')", $routes);
        $this->assertStringContainsString("name('admin.hotels.additional-charges.destroy')", $routes);
        $this->assertStringContainsString("name('hotels.contracts.store')", $routes);
        $this->assertStringContainsString("name('hotels.contracts.update')", $routes);
        $this->assertStringContainsString("name('hotels.contracts.destroy')", $routes);
        $this->assertStringContainsString("mix('build/backend/css/operations/hotels/detail.css')", $view);
        $this->assertStringContainsString("mix('build/backend/js/operations/hotels/detail.js')", $view);
        $this->assertStringContainsString("resources/backend/js/operations/hotels/detail.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/operations/hotels/detail-entry.scss", $mix);
        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('<x-backend.detail-layout class="hotel-detail-layout">', $view);
        $this->assertStringContainsString('<x-slot name="main">', $view);
        $this->assertStringContainsString('<x-slot name="side">', $view);
        $this->assertStringContainsString("backend.operations.hotels.partials.profile-summary", $view);
        $this->assertStringContainsString("backend.operations.hotels.partials.audit-summary", $view);
        $this->assertStringContainsString("backend.operations.hotels.partials.contracts", $view);
        $this->assertStringContainsString("backend.operations.hotels.partials.rooms", $view);
        $this->assertStringContainsString("backend.operations.hotels.partials.extra-beds", $view);
        $this->assertStringContainsString("backend.operations.hotels.partials.normal-prices", $view);
        $this->assertStringContainsString("backend.operations.hotels.partials.promo-prices", $view);
        $this->assertStringContainsString("backend.operations.hotels.partials.package-prices", $view);
        $this->assertStringContainsString("backend.operations.hotels.partials.additional-charges", $view);
        $this->assertStringContainsString("backend.operations.hotels.modals.contract-preview", $view);
        $this->assertStringContainsString("backend.operations.hotels.modals.room-preview", $view);
        $this->assertStringContainsString('backend-page-primary-action', $detailContent);
        $this->assertStringContainsString("route('admin.panel-main.view')", $view);
        $this->assertStringContainsString("route('admin.hotels.index')", $view);
        $this->assertStringContainsString('backend-page-toolbar hotel-detail-toolbar', $view);
        $this->assertStringContainsString('backend-feedback hotel-detail-feedback', $view);
        $this->assertStringContainsString('backend-alert backend-alert--', $view);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--5', $view);
        $this->assertStringContainsString('backend-kpi-card backend-kpi-card--', $view);
        $this->assertStringNotContainsString('number_format($stat[\'value\'])', $view);
        $this->assertStringContainsString("'label' => 'Status'", $viewModel);
        $this->assertStringContainsString("'label' => 'Rooms'", $viewModel);
        $this->assertStringNotContainsString("'label' => 'Contracts'", $viewModel);
        $this->assertStringNotContainsString("'label' => 'Pricing Rows'", $viewModel);
        $this->assertStringContainsString('$hotelDetail->pricingAgentRateChart()', $view);
        $this->assertStringContainsString('hotel-pricing-agent-chart-card', $view);
        $this->assertStringContainsString('Hotel pricing agent rate chart', $view);
        $this->assertStringContainsString('hotel-pricing-agent-chart-card__legend', $view);
        $this->assertStringContainsString('hotel-pricing-agent-chart-card__scale', $view);
        $this->assertStringContainsString('hotel-pricing-agent-chart-card__month-label', $view);
        $this->assertStringContainsString("'month_labels' =>", $viewModel);
        $this->assertStringContainsString("'label' => 'Normal Price'", $viewModel);
        $this->assertStringContainsString("'label' => 'Promo Price'", $viewModel);
        $this->assertStringContainsString("'label' => 'Package Price'", $viewModel);
        $this->assertStringContainsString('$rates->max()', $viewModel);
        $this->assertStringContainsString('$currentMonthOffset = max(Carbon::parse($this->now)->month - 1, 0);', $viewModel);
        $this->assertStringContainsString('$this->normalPrices', $viewModel);
        $this->assertStringContainsString('$this->promos', $viewModel);
        $this->assertStringContainsString('$this->packages', $viewModel);
        $this->assertStringContainsString('private readonly ?Collection $chartNormalPrices = null', $viewModel);
        $this->assertStringContainsString('private function chartNormalPrices(): Collection', $viewModel);
        $this->assertStringContainsString('return $this->chartNormalPrices ?? $this->normalPrices;', $viewModel);
        $this->assertStringContainsString('$yearStart = $today->copy()->startOfYear()->toDateString();', $inventoryService);
        $this->assertStringContainsString('$chartNormalPrices = HotelPrice::with(\'rooms\')', $inventoryService);
        $this->assertStringContainsString('$chartPromos = HotelPromo::with(\'rooms\')', $inventoryService);
        $this->assertStringContainsString('$chartPackages = HotelPackage::with(\'room\')', $inventoryService);
        $this->assertStringContainsString("->whereDate('end_date', '>=', \$yearStart)", $inventoryService);
        $this->assertStringContainsString("->whereDate('periode_end', '>=', \$yearStart)", $inventoryService);
        $this->assertStringContainsString("->whereDate('stay_period_end', '>=', \$yearStart)", $inventoryService);
        $this->assertStringContainsString('normalPrices: $normalPrices', $inventoryService);
        $this->assertStringContainsString('chartNormalPrices: $chartNormalPrices', $inventoryService);
        $this->assertStringContainsString('year to date highest agent rate', $viewModel);
        $this->assertStringContainsString('$duration = max((int) ($package->duration ?? 1), 1);', $viewModel);
        $this->assertStringContainsString("ceil(((int) (\$pricing['net_rate'] ?? 0)) / \$duration)", $viewModel);
        $this->assertStringContainsString('monthly highest agent rate', $viewModel);
        $this->assertStringContainsString('function pricingAgentRateChart', $viewModel);
        $this->assertStringContainsString('HotelPricingService::rateBreakdown()', $accommodationDocs);
        $this->assertStringContainsString('chart Pricing Agent Rate tahunan', $accommodationDocs);
        $this->assertStringContainsString('Agent Rate tertinggi per bulan', $accommodationDocs);
        $this->assertStringContainsString('tanpa memfilter status `Active` / `Draft`', $accommodationDocs);
        $this->assertStringContainsString('dari awal tahun sampai bulan berjalan', $accommodationDocs);
        $this->assertStringContainsString('query year to date tersendiri', $accommodationDocs);
        $this->assertStringContainsString('Rate Plan Normal Price di backend detail hanya boleh menampilkan Normal Price', $accommodationDocs);
        $this->assertStringContainsString('Dataset chart historical tidak boleh dipakai untuk Rate Plan table', $accommodationDocs);
        $this->assertStringContainsString('dinormalisasi menjadi Agent Rate per durasi package', $accommodationDocs);
        $this->assertStringContainsString("'promos' => fn (\$query) => \$query->whereDate('book_periode_end', '>=', \$now)->orderByDesc('book_periode_end')", $inventoryService);
        $this->assertStringContainsString('reject(fn ($promo) => $this->hasExpired($promo->book_periode_end))', $viewModel);
        $this->assertStringContainsString('booking period end-nya belum terlewat', $accommodationDocs);
        $this->assertStringContainsString('tidak boleh menyembunyikan promo hanya karena stay period end', $accommodationDocs);
        $this->assertStringContainsString("'label' => 'Latest Price'", $viewModel);
        $this->assertStringContainsString('backend-panel hotel-detail-panel', $detailContent);
        $this->assertStringContainsString('backend-detail-side-card hotel-detail-context-panel', $detailContent);
        $this->assertStringContainsString('backend-detail-side-card hotel-detail-audit-panel', $detailContent);
        $this->assertStringContainsString('backend-detail-side-list', $detailContent);
        $this->assertStringContainsString('backend-detail-side-actions', $detailContent);
        $this->assertStringContainsString('Quick Actions', $auditSummary);
        $this->assertStringContainsString('Record Ownership', $auditSummary);
        $this->assertStringNotContainsString('<span>Status</span>', $auditSummary);
        $this->assertStringNotContainsString('<span>Rooms</span>', $auditSummary);
        $this->assertStringNotContainsString('<span>Contracts</span>', $auditSummary);
        $this->assertStringNotContainsString('<span>Pricing Rows</span>', $auditSummary);
        $this->assertStringNotContainsString('<span>Latest Price</span>', $auditSummary);
        $this->assertStringContainsString('backend-section-header hotel-detail-panel__heading', $detailContent);
        $this->assertStringContainsString('backend-section-header__label', $detailContent);
        $this->assertStringContainsString('hotel-detail-profile-summary', $detailContent);
        $this->assertStringContainsString('hotel-detail-media-column', $detailContent);
        $this->assertStringContainsString('hotel-detail-stay-grid', $detailContent);
        $this->assertStringContainsString('<div><dt>Min Stay</dt>', $detailContent);
        $this->assertStringContainsString('<div><dt>Airport Duration</dt>', $detailContent);
        $this->assertStringContainsString('decoding="async"', $detailContent);
        $this->assertStringContainsString('width="640"', $detailContent);
        $this->assertStringContainsString('.hotel-detail-profile-summary', $scss);
        $this->assertStringContainsString('.hotel-detail-media-column', $scss);
        $this->assertStringContainsString('.hotel-detail-stay-grid', $scss);
        $this->assertStringContainsString('grid-column: 1 / -1;', $scss);
        $this->assertStringContainsString('grid-template-columns: repeat(4, minmax(0, 1fr));', $scss);
        $this->assertStringContainsString('grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);', $scss);
        $this->assertStringContainsString('aspect-ratio: 16 / 9;', $scss);
        $this->assertStringContainsString('max-height: 360px;', $scss);
        $this->assertStringContainsString('backend-filter-panel hotel-detail-filter', $detailContent);
        $this->assertStringContainsString('backend-table hotel-detail-table', $detailContent);
        $this->assertStringContainsString("route('admin.hotels.room.status.update'", $roomsPartial);
        $this->assertStringContainsString('data-backend-status-toggle', $roomsPartial);
        $this->assertStringContainsString('data-backend-status-badge-target="#hotelRoomStatusBadge{{ $room->id }}, #hotelRoomModalStatusBadge{{ $room->id }}"', $roomsPartial);
        $this->assertStringContainsString('id="hotelRoomModalStatusBadge{{ $room->id }}"', $detailContent);
        $this->assertStringContainsString('data-backend-status-toggle-label', $roomsPartial);
        $this->assertStringContainsString('<div><small>Inventory</small><b>{{ $room->inventory ?? \'-\' }}</b></div>', $roomsPartial);
        $this->assertStringNotContainsString('<div><small>Status</small><b>{{ $room->status }}</b></div>', $roomsPartial);
        $this->assertStringContainsString('public function updateStatus(Request $request, HotelRoom $room, HotelAuditService $audit): JsonResponse', $roomController);
        $this->assertStringContainsString("'status' => ['required', Rule::in(['Active', 'Draft'])]", $roomController);
        $this->assertStringContainsString("\$audit->userLog(", $roomController);
        $this->assertStringContainsString("'Update Room Status'", $roomController);
        $this->assertStringContainsString("'next_status' => \$room->status === 'Active' ? 'Draft' : 'Active'", $roomController);
        $this->assertStringContainsString('Backend Hotel detail menampilkan toggle status manual pada setiap Room', $accommodationDocs);
        $this->assertStringContainsString('Backend Hotel Detail Additional Charge Management', $accommodationDocs);
        $this->assertStringContainsString('hotel-additional-charge-form-modal', $detailContent);
        $this->assertStringContainsString('hotelAdditionalChargeAdd{{ $hotel->id }}', $detailContent);
        $this->assertStringContainsString('hotelAdditionalChargeEdit{{ $additionalCharge->id }}', $detailContent);
        $this->assertStringContainsString("route('admin.hotels.additional-charges.store')", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.additional-charges.update'", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.additional-charges.destroy'", $detailContent);
        $this->assertStringContainsString('data-backend-money-unit="IDR"', $additionalChargeForm);
        $this->assertStringContainsString('data-backend-money-unit="USD"', $additionalChargeForm);
        $this->assertStringContainsString('data-backend-picker="date"', $additionalChargeForm);
        $this->assertStringContainsString('View calculation', $additionalChargePartial);
        $this->assertStringContainsString('store(StoreHotelAdditionalChargeRequest $request, HotelAuditService $audit)', $additionalChargeController);
        $this->assertStringContainsString('update(UpdateHotelAdditionalChargeRequest $request, $id, HotelAuditService $audit)', $additionalChargeController);
        $this->assertStringContainsString('destroy(Request $request, $id, HotelAuditService $audit)', $additionalChargeController);
        $this->assertStringContainsString('lockForUpdate()', $additionalChargeController);
        $this->assertStringContainsString('Backend Hotel Detail Extra Bed Management', $accommodationDocs);
        $this->assertStringContainsString('$hotelDetail->extraBedRows()', $detailContent);
        $this->assertStringContainsString('hotel-extra-bed-form-modal', $detailContent);
        $this->assertStringContainsString('hotelExtraBedAdd{{ $hotel->id }}', $detailContent);
        $this->assertStringContainsString('hotelExtraBedEdit{{ $extraBed->id }}', $detailContent);
        $this->assertStringContainsString("route('func.extrabed.add')", $detailContent);
        $this->assertStringContainsString("route('func.extrabed.edit'", $detailContent);
        $this->assertStringContainsString("route('func.extrabed.delete'", $detailContent);
        $this->assertStringContainsString("name('func.extrabed.add')", $routes);
        $this->assertStringContainsString("name('func.extrabed.edit')", $routes);
        $this->assertStringContainsString("name('func.extrabed.delete')", $routes);
        $this->assertStringContainsString('data-backend-money-unit="IDR"', $extraBedForm);
        $this->assertStringContainsString('data-backend-money-unit="USD"', $extraBedForm);
        $this->assertStringContainsString('data-backend-richtext="true"', $extraBedForm);
        $this->assertStringContainsString('View calculation', $extraBedPartial);
        $this->assertStringContainsString('function extraBedRows', $viewModel);
        $this->assertStringContainsString('extraBedFallbackDescription', $viewModel);
        $this->assertStringContainsString('rateBreakdown(', $viewModel);
        $this->assertStringContainsString('public function func_add_extra_bed(StoreExtraBedRequest $request, HotelAuditService $audit)', $extraBedController);
        $this->assertStringContainsString('public function fedit_extra_bed(UpdateExtraBedRequest $request, $id, HotelAuditService $audit)', $extraBedController);
        $this->assertStringContainsString('public function fdelete_extra_bed(Request $request, $id, HotelAuditService $audit)', $extraBedController);
        $this->assertStringContainsString('lockForUpdate()', $extraBedController);
        $this->assertStringContainsString('redirectToHotelDetail', $extraBedController);
        $this->assertStringContainsString("'exists:hotels,id'", $storeExtraBedRequest);
        $this->assertStringContainsString("Rule::in(['Adult', 'Children', 'Guest'])", $storeExtraBedRequest);
        $this->assertStringContainsString("Rule::in(['Adult', 'Children', 'Guest'])", $updateExtraBedRequest);
        $this->assertStringNotContainsString('UserLog', $extraBedController);
        $this->assertStringNotContainsString('$request->author', $extraBedController);
        $this->assertStringNotContainsString('resources/views/admin/extrabed.blade.php', $detailContent);
        $this->assertStringContainsString('$hotelDetail->normalPriceGroups()', $detailContent);
        $this->assertStringContainsString('hotel-normal-price-groups', $detailContent);
        $this->assertStringContainsString('hotel-normal-price-group__header', $detailContent);
        $this->assertStringNotContainsString('<th>Status</th>', $normalPricePartial);
        $this->assertStringNotContainsString('data-label="Status"', $normalPricePartial);
        $this->assertStringContainsString('function normalPriceGroups', $viewModel);
        $this->assertStringContainsString("->groupBy(fn (\$row) => (string) (\$row['model']->rooms_id ?? 'unassigned'))", $viewModel);
        $this->assertStringContainsString('normalPriceStayPeriodSortKey($row[\'model\'])', $viewModel);
        $this->assertStringContainsString('private function normalPriceStayPeriodSortKey(object $price): string', $viewModel);
        $this->assertStringContainsString("Carbon::parse(\$price->start_date)->toDateString()", $viewModel);
        $this->assertStringContainsString('Card/group Normal Price juga wajib diurutkan berdasarkan stay period paling', $accommodationDocs);
        $this->assertStringContainsString('$hotelDetail->promoGroups()', $detailContent);
        $this->assertStringContainsString('hotel-promo-price-groups', $detailContent);
        $this->assertStringContainsString('hotel-promo-price-group__header', $detailContent);
        $this->assertStringContainsString('data-backend-status-toggle', $detailContent);
        $this->assertStringContainsString('data-backend-status-url', $detailContent);
        $this->assertStringContainsString("route('admin.hotels.promos.status.update'", $detailContent);
        $this->assertStringNotContainsString('data-backend-status-badge-target', $promoPricePartial);
        $this->assertStringContainsString('data-backend-status-toggle-label', $detailContent);
        $this->assertStringContainsString('function promoGroups', $viewModel);
        $this->assertStringContainsString('promoStayPeriodSortKey($row[\'model\'])', $viewModel);
        $this->assertStringContainsString('private function promoStayPeriodSortKey(object $promo): string', $viewModel);
        $this->assertStringContainsString("Carbon::parse(\$promo->periode_start)->toDateString()", $viewModel);
        $this->assertStringContainsString('Card/group Promotion Price juga wajib diurutkan berdasarkan stay period start', $accommodationDocs);
        $this->assertStringContainsString('$hotelDetail->packageGroups()', $detailContent);
        $this->assertStringContainsString('hotel-package-price-groups', $detailContent);
        $this->assertStringContainsString('hotel-package-price-group__header', $detailContent);
        $this->assertStringContainsString("route('admin.hotels.packages.status.update'", $detailContent);
        $this->assertStringContainsString('data-backend-status-toggle', $detailContent);
        $this->assertStringContainsString('data-backend-status-url', $detailContent);
        $this->assertStringContainsString('data-backend-status-toggle-label', $detailContent);
        $this->assertStringContainsString('function packageGroups', $viewModel);
        $this->assertStringContainsString('packageStayPeriodSortKey($row[\'model\'])', $viewModel);
        $this->assertStringContainsString('private function packageStayPeriodSortKey(object $package): string', $viewModel);
        $this->assertStringContainsString("Carbon::parse(\$package->stay_period_start)->toDateString()", $viewModel);
        $this->assertStringContainsString('private function packagePerNightPricing(array $pricing, int $duration): array', $viewModel);
        $this->assertStringContainsString("'display_mode'] = 'package_per_night'", $viewModel);
        $this->assertStringContainsString("'package_total_published_rate'", $viewModel);
        $this->assertStringContainsString('Panel Package Price di backend detail hanya boleh menampilkan Package Price', $accommodationDocs);
        $this->assertStringContainsString('Card/group Package Price juga wajib diurutkan berdasarkan stay period start', $accommodationDocs);
        $this->assertStringContainsString('Tabel Package Price wajib menampilkan Published Rate per night', $accommodationDocs);
        $this->assertStringContainsString('Calculation modal Package Price wajib memakai breakdown per-night allocation', $accommodationDocs);
        $this->assertStringContainsString('Summary calculation modal Package Price wajib menampilkan `Agent Rate Per', $accommodationDocs);
        $this->assertStringContainsString('.hotel-normal-price-groups', $scss);
        $this->assertStringContainsString('.hotel-normal-price-group__header', $scss);
        $this->assertStringContainsString('.hotel-promo-price-groups', $scss);
        $this->assertStringContainsString('.hotel-promo-price-group__header', $scss);
        $this->assertStringContainsString('.hotel-price-calculation-summary--package .hotel-price-calculation-summary__item--agent', $scss);
        $this->assertStringContainsString('.hotel-pricing-agent-chart-card__legend', $scss);
        $this->assertStringContainsString('.hotel-pricing-agent-chart-card__scale', $scss);
        $this->assertStringContainsString('.hotel-pricing-agent-chart-card__month-label', $scss);
        $this->assertStringContainsString('.backend-status-toggle', $statusScss);
        $this->assertStringContainsString('.backend-status-toggle__track', $statusScss);
        $this->assertStringContainsString('.backend-status-toggle__knob', $statusScss);
        $this->assertStringContainsString('data-backend-status-toggle', $backendAppJs);
        $this->assertStringContainsString('handleBackendStatusToggleClick', $backendAppJs);
        $this->assertStringContainsString("method: 'PATCH'", $backendAppJs);
        $this->assertStringContainsString("'X-CSRF-TOKEN': backendCsrfToken()", $backendAppJs);
        $this->assertStringContainsString('updateBackendStatusBadge', $backendAppJs);
        $this->assertStringContainsString('document.querySelectorAll(badgeTarget).forEach', $backendAppJs);
        $this->assertStringContainsString('.hotel-package-price-groups', $scss);
        $this->assertStringContainsString('.hotel-package-price-group__header', $scss);
        $this->assertStringContainsString('backend-table-empty', $detailContent);
        $this->assertStringContainsString('backend-empty-state', $detailContent);
        $this->assertStringContainsString('backend-status-badge backend-status-badge--', $detailContent);
        $this->assertStringContainsString('backend-modal', $detailContent);
        $this->assertStringContainsString('backend-modal__header', $detailContent);
        $this->assertStringContainsString('backend-modal__body', $detailContent);
        $this->assertStringContainsString('backend-modal__footer', $detailContent);
        $this->assertStringContainsString('modal-dialog modal-dialog-centered modal-xl', $detailContent);
        $this->assertStringContainsString('backend-modal-detail hotel-room-detail-modal-layout', $detailContent);
        $this->assertStringContainsString('backend-modal-detail__media', $detailContent);
        $this->assertStringContainsString('backend-modal-detail__content', $detailContent);
        $this->assertStringContainsString('backend-modal-detail__summary', $detailContent);
        $this->assertStringContainsString('backend-modal-detail__grid', $detailContent);
        $this->assertStringContainsString('backend-modal-detail__section', $detailContent);
        $this->assertStringContainsString('Author-facing summary of Room master data', $detailContent);
        $this->assertStringContainsString('<dt>Total Guests</dt>', $detailContent);
        $this->assertStringContainsString('<dt>Inventory</dt>', $detailContent);
        $this->assertStringContainsString('.backend-modal-detail', $modalScss);
        $this->assertStringContainsString('.backend-modal-detail__media', $modalScss);
        $this->assertStringContainsString('.backend-modal-detail__content', $modalScss);
        $this->assertStringContainsString('.backend-modal-detail__grid', $modalScss);
        $this->assertStringContainsString('.backend-modal-detail__section', $modalScss);
        $this->assertStringContainsString('.hotel-room-detail-modal__heading', $scss);
        $this->assertStringContainsString("route('admin.hotels.edit'", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.gallery.edit'", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.room.create'", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.room.edit'", $detailContent);
        $this->assertStringNotContainsString("route('admin.hotels.prices.edit'", $detailContent);
        $this->assertStringContainsString('hotel-normal-price-edit-modal', $detailContent);
        $this->assertStringContainsString("route('admin.hotels.normal-prices.update'", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.promos.create'", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.promos.edit'", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.packages.create'", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.packages.edit'", $detailContent);
        $this->assertStringNotContainsString("route('admin.hotels.additional-charges.create'", $detailContent);
        $this->assertStringNotContainsString("route('admin.hotels.additional-charges.edit'", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.room.delete'", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.normal-prices.destroy'", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.additional-charges.destroy'", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.contracts.store'", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.contracts.update'", $detailContent);
        $this->assertStringContainsString("route('admin.hotels.contracts.destroy'", $detailContent);
        $this->assertStringNotContainsString("route('func.package.add')", $detailContent);
        $this->assertStringNotContainsString('hotelPackageAddModal', $detailContent);
        $this->assertStringNotContainsString("@include('admin.usd-rate')", $detailContent);
        $this->assertStringContainsString('[data-hotel-detail-filter]', $js);
        $this->assertStringContainsString('[data-hotel-detail-delete]', $js);
        $this->assertStringNotContainsString('.hotel-detail-layout {', $scss);
        $this->assertStringNotContainsString('.hotel-detail-sidebar', $scss);
        $this->assertStringNotContainsString('.hotel-detail-log-card', $scss);
        $this->assertStringNotContainsString('<script>', $detailContent);
        $this->assertStringNotContainsString('onkeyup=', $detailContent);
        $this->assertStringNotContainsString('card-box', $detailContent);
        $this->assertStringNotContainsString('data-table table', $detailContent);
        $this->assertStringNotContainsString('style=', $detailContent);
        $this->assertStringNotContainsString('href="/detail-hotel-', $detailContent);
        $this->assertStringNotContainsString('href="/edit-hotel-', $detailContent);
        $this->assertStringNotContainsString('href="/edit-room-', $detailContent);
        $this->assertStringNotContainsString('action="/fdelete-room/', $detailContent);
        $this->assertStringNotContainsString('action="/delete-price/', $detailContent);
        $this->assertStringNotContainsString('action="/fdelete-contract/', $detailContent);
        $this->assertStringNotContainsString('action="/fupdate-hotel-contract/', $detailContent);
        $this->assertStringNotContainsString('action="/fadd-hotel-contract"', $detailContent);
    }

    public function test_wedding_admin_forms_are_sourced_from_backend_operations_structure(): void
    {
        $controllers = collect([
            app_path('Http/Controllers/WeddingsController.php'),
            app_path('Http/Controllers/HotelsAdminController.php'),
            app_path('Http/Controllers/WeddingReceptionVenuesController.php'),
            app_path('Http/Controllers/WeddingLunchVenuesController.php'),
            app_path('Http/Controllers/WeddingDinnerVenuesController.php'),
            app_path('Http/Controllers/WeddingMenuController.php'),
        ])->map(fn ($path) => file_get_contents($path))->implode("\n");

        $forms = [
            'create' => 'weddingadd',
            'edit' => 'weddingedit',
            'venue-create' => 'wedding-venue-add',
            'venue-edit' => 'wedding-venue-edit',
            'reception-venue-edit' => 'wedding-reception-venue-edit',
            'lunch-venue-edit' => 'wedding-lunch-venue-edit',
            'dinner-venue-create' => 'wedding-dinner-venue-add',
            'dinner-venue-edit' => 'wedding-dinner-venue-edit',
            'dinner-package-create' => 'wedding-dinner-package-add',
            'dinner-package-edit' => 'wedding-dinner-package-edit',
            'food-and-beverage-create' => 'wedding-add-food-and-beverage',
        ];

        foreach ($forms as $target => $legacy) {
            $this->assertFileExists(resource_path("views/backend/operations/weddings/forms/{$target}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/form/{$legacy}.blade.php"));
        }

        $this->assertStringContainsString("view('backend.operations.weddings.forms.create'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.edit'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.venue-create'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.venue-edit'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.reception-venue-edit'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.lunch-venue-edit'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.dinner-venue-create'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.dinner-venue-edit'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.dinner-package-create'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.dinner-package-edit'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.food-and-beverage-create'", $controllers);
        $this->assertStringNotContainsString("view('form.weddingadd'", $controllers);
        $this->assertStringNotContainsString("view('form.weddingedit'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-venue-add'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-venue-edit'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-reception-venue-edit'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-lunch-venue-edit'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-dinner-venue-add'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-dinner-venue-edit'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-dinner-package-add'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-dinner-package-edit'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-add-food-and-beverage'", $controllers);
    }

    public function test_partner_crud_uses_backend_index_modal_flow(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/PartnersController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));

        $this->assertFileExists(resource_path('views/backend/operations/partners/index.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/partners/partials/form.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/partners.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/partner-detail.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/partnerdetail.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/backend/operations/partners/forms/add-activity.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/backend/operations/partners/forms/add-tour.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/partner-add-activity.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/partner-add-tour.blade.php'));
        $this->assertStringContainsString("view('backend.operations.partners.index'", $controller);
        $this->assertStringNotContainsString("view('admin.partner-detail'", $controller);
        $this->assertStringNotContainsString('detail-partner', $routes);
        $this->assertStringNotContainsString("view('form.partner-add-activity'", $controller);
        $this->assertStringNotContainsString("view('form.partner-add-tour'", $controller);
    }

    public function test_frontend_order_booking_forms_are_sourced_from_frontend_home_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $forms = [
            'hotel-normal' => 'order-hotel-normal',
            'hotel-package' => 'order-hotel-package',
            'hotel-promo' => 'order-hotel-promo',
            'transport' => 'order-transport',
        ];

        foreach ($forms as $target => $legacy) {
            $this->assertFileExists(resource_path("views/frontend/home/booking/orders/{$target}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/form/{$legacy}.blade.php"));
        }

        $this->assertStringContainsString("view('frontend.home.booking.orders.hotel-normal'", $controller);
        $this->assertStringContainsString("view('frontend.home.booking.orders.hotel-package'", $controller);
        $this->assertStringContainsString("view('frontend.home.booking.orders.hotel-promo'", $controller);
        $this->assertStringContainsString("view('frontend.home.booking.orders.transport'", $controller);
        $this->assertStringNotContainsString("view('form.order-hotel-normal'", $controller);
        $this->assertStringNotContainsString("view('form.order-hotel-package'", $controller);
        $this->assertStringNotContainsString("view('form.order-hotel-promo'", $controller);
        $this->assertStringNotContainsString("view('form.order-transport'", $controller);
    }

    public function test_legacy_form_view_namespace_has_no_active_files(): void
    {
        $legacyFormPath = resource_path('views/form');
        $legacyFiles = [];

        if (is_dir($legacyFormPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($legacyFormPath, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $legacyFiles[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
                }
            }
        }

        $this->assertFileExists(resource_path('views/frontend/home/orders/weddings/legacy/order-weddings.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/weddings/legacy/order-weddings-backup.blade.php'));
        $this->assertSame([], $legacyFiles);
    }

    public function test_review_views_are_sourced_from_frontend_and_backend_structures(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/ReviewController.php'));
        $routeFile = file_get_contents(base_path('routes/web.php'));
        $reviewJs = file_get_contents(resource_path('backend/js/admin/reviews/index.js'));
        $reviewScss = file_get_contents(resource_path('backend/scss/admin/reviews/_index.scss'));
        $frontendReviewFiles = [
            'wedding-index',
            'print-wedding-reviews',
            'create',
            'create-review',
            'create-wedding-review',
            'wedding_review_link_form',
            'layouts/app',
            'partials/review_card',
            'partials/review_modal',
        ];

        foreach ($frontendReviewFiles as $reviewFile) {
            $this->assertFileExists(resource_path("views/frontend/home/reviews/{$reviewFile}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/home/reviews/{$reviewFile}.blade.php"));
        }

        $this->assertFileExists(resource_path('views/backend/admin/reviews/index.blade.php'));
        $this->assertFileExists(resource_path('views/backend/admin/reviews/link-form.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/frontend/home/reviews/index.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/frontend/home/reviews/review_link_form.blade.php'));
        $this->assertFileExists(resource_path('backend/scss/admin/reviews/index-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/admin/reviews/_index.scss'));
        $this->assertFileExists(resource_path('backend/js/admin/reviews/index.js'));

        $adminReviewView = file_get_contents(resource_path('views/backend/admin/reviews/index.blade.php'));
        $adminReviewCardPartial = file_get_contents(resource_path('views/backend/admin/reviews/partials/review-card.blade.php'));
        $adminReviewPrintPartial = file_get_contents(resource_path('views/backend/admin/reviews/partials/print-brief.blade.php'));
        $adminReviewLinkView = file_get_contents(resource_path('views/backend/admin/reviews/link-form.blade.php'));

        $this->assertStringContainsString("view('backend.admin.reviews.index'", $controller);
        $this->assertStringContainsString("view('backend.admin.reviews.link-form'", $controller);
        $this->assertStringContainsString("view('frontend.home.reviews.wedding-index'", $controller);
        $this->assertStringContainsString("view('frontend.home.reviews.print-wedding-reviews'", $controller);
        $this->assertStringContainsString("view('frontend.home.reviews.create'", $controller);
        $this->assertStringContainsString("view('frontend.home.reviews.wedding_review_link_form'", $controller);
        $this->assertStringNotContainsString("print_reviews", $controller);
        $this->assertStringNotContainsString("/reviews/print/{bookingCode}", $routeFile);
        $this->assertStringNotContainsString("view('home.reviews.", $controller);
        $this->assertStringNotContainsString("view('frontend.home.reviews.print-reviews'", $controller);
        $this->assertStringNotContainsString("view('frontend.home.reviews.index'", $controller);
        $this->assertStringNotContainsString("view('frontend.home.reviews.review_link_form'", $controller);
        $this->assertStringContainsString('<x-backend.page-hero', $adminReviewView);
        $this->assertStringContainsString('backend-page-toolbar', $adminReviewView);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--4', $adminReviewView);
        $this->assertStringContainsString('backend-kpi-card backend-kpi-card--', $adminReviewView);
        $this->assertStringContainsString('backend-kpi-card__icon', $adminReviewView);
        $this->assertStringContainsString('backend-feedback', $adminReviewView);
        $this->assertStringContainsString('backend-alert backend-alert--', $adminReviewView);
        $this->assertStringContainsString('backend-status-badge', $adminReviewView);
        $this->assertStringContainsString('backend-feedback', $adminReviewLinkView);
        $this->assertStringContainsString('backend-alert backend-alert--', $adminReviewLinkView);
        $this->assertStringContainsString('backend-status-badge', $adminReviewLinkView);
        $this->assertStringNotContainsString('tour-reviews-stats', $adminReviewView);
        $this->assertStringContainsString('backend-panel tour-reviews-panel', $adminReviewView);
        $this->assertStringContainsString('backend-section-header tour-reviews-panel__heading', $adminReviewView);
        $this->assertStringContainsString('backend-section-header__label', $adminReviewView);
        $this->assertStringNotContainsString('tour-reviews-panel__header', $adminReviewView);
        $this->assertStringNotContainsString('tour-reviews-eyebrow', $adminReviewView);
        $this->assertStringContainsString("route('admin.reviews.destroy'", $adminReviewCardPartial);
        $this->assertStringContainsString('data-tour-review-action', $adminReviewCardPartial);
        $this->assertStringContainsString('tour-review-rating-chips', $adminReviewCardPartial);
        $this->assertStringNotContainsString('.tour-reviews-alert', $reviewScss);
        $this->assertStringNotContainsString('.tour-reviews-badge', $reviewScss);
        $this->assertStringContainsString('tour-review-rating-groups', $adminReviewCardPartial);
        $this->assertStringContainsString('@unless ($grouped)', $adminReviewCardPartial);
        $this->assertStringContainsString("'General' => [", $adminReviewCardPartial);
        $this->assertStringContainsString("'Transportation' => [", $adminReviewCardPartial);
        $this->assertStringContainsString("'Driver' => [", $adminReviewCardPartial);
        $this->assertStringContainsString("'Guide' => [", $adminReviewCardPartial);
        $this->assertStringContainsString("'Travel Mood' => [", $adminReviewCardPartial);
        $this->assertStringContainsString("'Travel Mood' => \$review->travel_mood", $adminReviewCardPartial);
        $this->assertStringNotContainsString('Kepuasan Wisatawan', $adminReviewCardPartial);
        $this->assertStringContainsString('tour-review-excerpt', $adminReviewCardPartial);
        $this->assertStringContainsString('tour-review-booking-group', $adminReviewView);
        $this->assertStringContainsString('tour-review-booking-group__score', $adminReviewView);
        $this->assertStringContainsString('tour-review-booking-group__case', $adminReviewView);
        $this->assertStringContainsString('data-review-group-toggle', $adminReviewView);
        $this->assertStringContainsString('data-review-group-list hidden', $adminReviewView);
        $this->assertStringContainsString('data-review-print-trigger', $adminReviewView);
        $this->assertStringContainsString('backend.admin.reviews.partials.print-brief', $adminReviewView);
        $this->assertStringContainsString('<dt>Agent</dt>', $adminReviewView);
        $this->assertStringContainsString('<dt>Arrival</dt>', $adminReviewView);
        $this->assertStringContainsString('<dt>Departure</dt>', $adminReviewView);
        $this->assertStringContainsString("'allowDelete' => false", $adminReviewView);
        $this->assertStringContainsString('data-review-link-filter', $adminReviewLinkView);
        $this->assertStringContainsString('data-copy-text', $adminReviewLinkView);
        $this->assertStringContainsString('backend-panel tour-reviews-panel', $adminReviewLinkView);
        $this->assertStringContainsString('backend-section-header tour-reviews-panel__heading', $adminReviewLinkView);
        $this->assertStringContainsString('backend-section-header__label', $adminReviewLinkView);
        $this->assertStringNotContainsString('tour-reviews-panel__header', $adminReviewLinkView);
        $this->assertStringNotContainsString('tour-reviews-eyebrow', $adminReviewLinkView);
        $this->assertFileDoesNotExist(resource_path('views/frontend/home/reviews/print-reviews.blade.php'));
        $this->assertStringContainsString('Tour Review Brief', $adminReviewPrintPartial);
        $this->assertStringContainsString('tour-review-print-sheet', $adminReviewPrintPartial);
        $this->assertStringContainsString('Average Ratings', $adminReviewPrintPartial);
        $this->assertStringContainsString('Guest Notes', $adminReviewPrintPartial);
        $this->assertStringContainsString('data-review-print-sheet', $adminReviewPrintPartial);
        $this->assertStringContainsString('data-review-print-trigger', $reviewJs);
        $this->assertStringContainsString('data-review-print-frame', $reviewJs);
        $this->assertStringContainsString('buildPrintDocument', $reviewJs);
        $this->assertStringContainsString('cloneNode(true)', $reviewJs);
        $this->assertStringContainsString('frameWindow.print();', $reviewJs);
        $this->assertStringContainsString('size: A4 portrait;', $reviewScss);
        $this->assertStringContainsString('body.is-tour-review-printing > :not(.tour-review-print-root)', $reviewScss);
        $this->assertStringContainsString('body.is-tour-review-printing .tour-review-print-root .tour-review-print-sheet.is-printing', $reviewScss);
        $this->assertStringContainsString('width: 100%;', $reviewScss);
        $this->assertStringContainsString('max-width: none;', $reviewScss);
        $this->assertStringNotContainsString('visibility: hidden !important;', $reviewScss);
        $this->assertStringContainsString('margin: 6mm;', $reviewScss);
        $this->assertStringContainsString('grid-template-columns: repeat(5, minmax(0, 1fr));', $reviewScss);
        $this->assertStringNotContainsString('.tour-reviews-stats', $reviewScss);
        $this->assertStringNotContainsString('.tour-reviews-panel__header', $reviewScss);
        $this->assertStringNotContainsString('.tour-reviews-eyebrow', $reviewScss);

        $reviewFormTemplates = collect([
            resource_path('views/frontend/home/reviews/create.blade.php'),
            resource_path('views/frontend/home/reviews/create-review.blade.php'),
            resource_path('views/frontend/home/reviews/create-wedding-review.blade.php'),
        ])->map(fn ($path) => file_get_contents($path))->implode("\n");

        $this->assertStringContainsString("@extends('frontend.home.reviews.layouts.app')", $reviewFormTemplates);
        $this->assertStringNotContainsString("@extends('home.reviews.layouts.app')", $reviewFormTemplates);
    }

    public function test_backend_tour_review_page_and_moderation_flow(): void
    {
        $developer = User::forceCreate([
            'username' => 'developer-tour-review',
            'name' => 'Developer Tour Review',
            'type' => 'admin',
            'email' => 'developer-tour-review@example.test',
            'password' => Hash::make('password'),
            'position' => 'developer',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
            'is_subscribed' => false,
            'subscriber' => false,
        ]);

        $review = Review::forceCreate([
            'booking_code' => 'TRV-TEST',
            'travel_agent' => 'Test Agent',
            'arrival_date' => now()->toDateString(),
            'departure_date' => now()->addDay()->toDateString(),
            'accommodation' => 5,
            'meals' => 4,
            'tour_sites' => 5,
            'transportation_cleanliness' => 4,
            'transportation_air_condition' => 5,
            'driver_punctuality' => 4,
            'driver_driving_skills' => 5,
            'driver_neatness' => 4,
            'attitude' => 5,
            'explanation' => 4,
            'knowledge' => 5,
            'time_control' => 4,
            'guide_neatness' => 5,
            'travel_mood' => 'Very Satisfied',
            'customer_name' => 'Review Guest',
            'customer_review' => 'Great tour service.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($developer)->get(route('admin.reviews.index'));

        $response->assertOk();
        $response->assertViewIs('backend.admin.reviews.index');
        $response->assertSee('Tour Reviews');
        $response->assertSee('Review Guest');

        $this->actingAs($developer)
            ->patch(route('admin.reviews.updateStatus', $review), ['status' => 'accepted'])
            ->assertRedirect();

        $this->assertSame('accepted', $review->fresh()->status);

        $approvedResponse = $this->actingAs($developer)->get(route('admin.reviews.index', ['tab' => 'approvedReviews']));
        $approvedResponse->assertOk();
        $approvedResponse->assertSee('Approved Case');
        $approvedResponse->assertSee('Booking Code');
        $approvedResponse->assertSee('Reviews');
        $approvedResponse->assertSee('Tour Review Brief');
        $approvedResponse->assertSee('Average Ratings');
        $approvedResponse->assertSee('Guest Notes');
        $approvedResponse->assertSee('data-review-print-trigger', false);

        $this->actingAs($developer)
            ->delete(route('admin.reviews.destroy', $review))
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'status' => 'accepted']);

        $this->actingAs($developer)
            ->patch(route('admin.reviews.updateStatus', $review), ['status' => 'rejected'])
            ->assertRedirect();

        $this->actingAs($developer)
            ->delete(route('admin.reviews.destroy', $review))
            ->assertRedirect();

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_backend_tour_review_link_generation_validation_and_success_flow(): void
    {
        Storage::fake('public');

        $developer = User::forceCreate([
            'username' => 'developer-review-link',
            'name' => 'Developer Review Link',
            'type' => 'admin',
            'email' => 'developer-review-link@example.test',
            'password' => Hash::make('password'),
            'position' => 'developer',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
            'is_subscribed' => false,
            'subscriber' => false,
        ]);

        $response = $this->actingAs($developer)->get(route('view.generate-review-link'));

        $response->assertOk();
        $response->assertViewIs('backend.admin.reviews.link-form');
        $response->assertSee('Tour Review Links');

        $this->actingAs($developer)
            ->post(route('generate.review-link'), [
                'agent' => 'Test Agent',
                'booking_code' => 'TRVLINK',
                'arrival_date' => now()->addDay()->toDateString(),
                'departure_date' => now()->toDateString(),
                'jumlah_review' => 2,
            ])
            ->assertSessionHasErrors('departure_date');

        $this->actingAs($developer)
            ->post(route('generate.review-link'), [
                'agent' => 'Test Agent',
                'booking_code' => 'TRVLINK',
                'arrival_date' => now()->toDateString(),
                'departure_date' => now()->addDays(2)->toDateString(),
                'jumlah_review' => 2,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('temporary_review_links', [
            'agent' => 'Test Agent',
            'booking_code' => 'TRVLINK',
            'jumlah_review' => 2,
        ]);
        Storage::disk('public')->assertExists('reviews/qrcodes/TRVLINK.svg');
    }

    public function test_home_public_legacy_routes_are_sourced_from_frontend_landing_page_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/HomeController.php'));

        $this->assertFileExists(resource_path('views/frontend/landing-page/services/index.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/landing-page/services.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/landing-page/accommodation.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/landing-page/tour-package.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/hotels/detail.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/tour-packages/detail.blade.php'));
        $this->assertStringContainsString("app(FrontEndController::class)->hotels_service", $controller);
        $this->assertStringContainsString("view('frontend.landing-page.services.index'", $controller);
        $routes = file_get_contents(base_path('routes/web.php'));
        $this->assertStringNotContainsString("app(FrontEndController::class)->tour_package_services", $controller);
        $this->assertSame(2, substr_count($routes, "[FrontEndController::class,'tour_package_services']"));
        $this->assertStringContainsString("name('tour-package-service')", $routes);
        $this->assertStringContainsString("redirect()->route('view.hotel-detail'", $controller);
        $this->assertStringContainsString("redirect()->route('view.tour-detail'", $controller);
        $this->assertStringNotContainsString("view('home.landing-page.accommodation'", $controller);
        $this->assertStringNotContainsString("view('home.landing-page.services'", $controller);
        $this->assertStringNotContainsString("view('home.landing-page.tour-package'", $controller);
        $this->assertStringNotContainsString("view('home.hotels.detail'", $controller);
        $this->assertStringNotContainsString("view('home.tour-packages.detail'", $controller);

        $servicesResponse = $this->get(route('services'));
        $servicesResponse->assertOk();
        $servicesResponse->assertViewIs('frontend.landing-page.services.index');
        $servicesResponse->assertViewHasAll(['serviceCards', 'servicePreviews']);

        $legacyTourDirectoryResponse = $this->get(route('tour-package-service'));
        $legacyTourDirectoryResponse->assertOk();
        $legacyTourDirectoryResponse->assertViewIs('frontend.landing-page.tours.directory');
    }

    public function test_legacy_tour_directory_route_does_not_bypass_controller_dependency_injection(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/HomeController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));

        $this->assertStringNotContainsString("app(FrontEndController::class)->tour_package_services", $controller);
        $this->assertStringNotContainsString('function tour_package_service(', $controller);
        $this->assertSame(2, substr_count($routes, "[FrontEndController::class,'tour_package_services']"));
        $this->assertStringContainsString(
            "Route::get('/tour-package-service',[FrontEndController::class,'tour_package_services'])->name('tour-package-service')",
            $routes
        );
    }

    public function test_home_agent_and_shared_partials_are_sourced_from_frontend_structure(): void
    {
        $agentController = file_get_contents(app_path('Http/Controllers/AgentRegistrationController.php'));
        $accommodationDetail = file_get_contents(resource_path('views/frontend/landing-page/accommodations/detail.blade.php'));
        $legacyLayout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/agents/register.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/shared/room-modal.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/shared/footer-legacy.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/agents/register.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/partials/room-modal.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/partials/footer.blade.php'));
        $this->assertStringContainsString("view('frontend.home.agents.register'", $agentController);
        $this->assertStringNotContainsString("view('home.agents.register'", $agentController);
        $this->assertStringContainsString("@include('frontend.shared.room-modal')", $accommodationDetail);
        $this->assertStringNotContainsString("@include('home.partials.room-modal')", $accommodationDetail);
        $this->assertStringContainsString("@include('frontend.shared.footer-legacy')", $legacyLayout);
        $this->assertStringNotContainsString("@include('home.partials.footer')", $legacyLayout);
    }

    public function test_legacy_home_view_namespace_has_no_active_blade_files(): void
    {
        $legacyHomePath = resource_path('views/home');
        $legacyFiles = [];

        if (is_dir($legacyHomePath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($legacyHomePath, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                    $legacyFiles[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
                }
            }
        }

        $this->assertSame([], $legacyFiles);
    }

    public function test_known_orphan_main_legacy_files_are_removed(): void
    {
        $orphanFiles = [
            'bookingcode-promotion.blade.php',
            'createdata.sql',
            'dashboard.blade.php',
            'download-data-hotel.blade.php',
            'error-500.blade.php',
            'error-msg.blade.php',
            'loading-page.blade.php',
            'test-input.blade.php',
            'wedding-planner-detail.blade.php',
            'weddingdetail.blade.php',
            'weddingsearch.blade.php',
        ];

        foreach ($orphanFiles as $orphanFile) {
            $this->assertFileDoesNotExist(resource_path("views/main/{$orphanFile}"));
        }
    }

    public function test_backend_admin_user_views_are_sourced_from_backend_admin_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/UsersController.php'));
        $routeFile = file_get_contents(base_path('routes/web.php'));
        $view = file_get_contents(resource_path('views/backend/admin/users/manager.blade.php'));
        $scss = file_get_contents(resource_path('backend/scss/admin/users/_manager.scss'));
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('views/backend/admin/users/index.blade.php'));
        $this->assertFileExists(resource_path('views/backend/admin/users/show.blade.php'));
        $this->assertFileExists(resource_path('views/backend/admin/users/manager.blade.php'));
        $this->assertFileExists(resource_path('views/backend/admin/users/partials/manager-form.blade.php'));
        $this->assertFileExists(resource_path('backend/js/admin/users/manager.js'));
        $this->assertFileExists(resource_path('backend/scss/admin/users/manager-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/admin/users/_manager.scss'));
        $this->assertFileDoesNotExist(resource_path('views/admin/users.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/userdetail.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/user-manager.blade.php'));
        $this->assertStringContainsString("view('backend.admin.users.index'", $controller);
        $this->assertStringContainsString("view('backend.admin.users.show'", $controller);
        $this->assertStringContainsString("view('backend.admin.users.manager'", $controller);
        $this->assertStringContainsString('paginate(15)', $controller);
        $this->assertStringContainsString("->orderBy('name')", $controller);
        $this->assertStringContainsString("->orderBy('username')", $controller);
        $this->assertStringContainsString('validateManagedUser', $controller);
        $this->assertStringContainsString('recordUserManagerLog', $controller);
        $this->assertStringContainsString("Route::delete('/fremove-user-{id}'", $routeFile);
        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('backend-page-toolbar user-manager-toolbar', $view);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--4', $view);
        $this->assertStringContainsString('backend-kpi-card backend-kpi-card--', $view);
        $this->assertStringContainsString('backend-feedback', $view);
        $this->assertStringContainsString('backend-alert backend-alert--', $view);
        $this->assertStringContainsString('backend-filter-panel backend-filter-grid user-manager-filter', $view);
        $this->assertStringContainsString('backend-filter-field', $view);
        $this->assertStringContainsString('backend-filter-actions', $view);
        $this->assertStringContainsString('backend-status-badge', $view);
        $this->assertStringContainsString('backend-panel user-manager-panel', $view);
        $this->assertStringContainsString('backend-section-header', $view);
        $this->assertStringContainsString('backend-section-header__label', $view);
        $this->assertStringNotContainsString('user-manager-stats', $view);
        $this->assertStringNotContainsString('user-manager-panel__header', $view);
        $this->assertStringNotContainsString('user-manager-eyebrow', $view);
        $this->assertStringContainsString("route('admin.panel-main.view')", $view);
        $this->assertStringNotContainsString("url('/admin/panel')", $view);
        $this->assertStringContainsString("mix('build/backend/css/admin/users/manager.css')", $view);
        $this->assertStringContainsString("mix('build/backend/js/admin/users/manager.js')", $view);
        $this->assertStringContainsString("backend.admin.users.partials.manager-form", $view);
        $this->assertStringContainsString('data-label="User"', $view);
        $this->assertStringContainsString('data-label="Actions"', $view);
        $this->assertStringContainsString('user-manager-mobile-list', $view);
        $this->assertStringContainsString('user-manager-mobile-card__section', $view);
        $this->assertStringNotContainsString('card-box-title', $view);
        $this->assertStringContainsString('.user-manager-form-grid label > span', $scss);
        $this->assertStringNotContainsString('.user-manager-stats', $scss);
        $this->assertStringNotContainsString('.user-manager-alert', $scss);
        $this->assertStringNotContainsString('.user-manager-badge', $scss);
        $this->assertStringNotContainsString('.user-manager-panel__header', $scss);
        $this->assertStringNotContainsString('.user-manager-eyebrow', $scss);
        $this->assertStringContainsString('color: var(--backend-muted-link);', $scss);
        $this->assertStringContainsString('color: var(--backend-required);', $scss);
        $this->assertStringContainsString('.user-manager-check-field input', $scss);
        $this->assertStringContainsString('width: 44px;', $scss);
        $this->assertStringNotContainsString('width: auto;', $scss);
        $this->assertStringContainsString('overflow-x: clip;', $scss);
        $this->assertStringContainsString('overflow: visible;', $scss);
        $this->assertStringContainsString('min-width: 0;', $scss);
        $this->assertStringContainsString('overflow-wrap: anywhere;', $scss);
        $this->assertStringNotContainsString('linear-gradient(270deg, rgba(15, 23, 42, 0.20)', $scss);
        $this->assertStringNotContainsString('touch-action: pan-x;', $scss);
        $this->assertStringNotContainsString('min-width: 1080px;', $scss);
        $this->assertStringContainsString('table-layout: fixed;', $scss);
        $this->assertStringContainsString('.user-manager-mobile-list', $scss);
        $this->assertStringContainsString('.user-manager-mobile-card', $scss);
        $this->assertStringContainsString('.user-manager-mobile-card__section', $scss);
        $this->assertStringContainsString('@media (max-width: 1199px)', $scss);
        $this->assertStringContainsString('@media (max-width: 575px)', $scss);
        $this->assertStringContainsString('display: none;', $scss);
        $this->assertStringContainsString('grid-template-columns: repeat(3, minmax(0, 1fr));', $scss);
        $this->assertStringContainsString('grid-template-columns: minmax(0, 1fr);', $scss);
        $this->assertStringContainsString('flex-direction: column;', $scss);
        $this->assertStringContainsString("resources/backend/js/admin/users/manager.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/admin/users/manager-entry.scss", $mix);
        $this->assertStringNotContainsString("view('admin.users'", $controller);
        $this->assertStringNotContainsString("view('admin.userdetail'", $controller);
        $this->assertStringNotContainsString("view('admin.user-manager'", $controller);
    }

    public function test_user_manager_page_returns_backend_standard_data(): void
    {
        $developer = User::forceCreate([
            'username' => 'developer-user-manager-view',
            'name' => 'Developer User Manager View',
            'type' => 'admin',
            'email' => 'developer-user-manager-view@example.test',
            'position' => 'developer',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'is_subscribed' => false,
            'subscriber' => false,
        ]);

        User::forceCreate([
            'username' => 'managed-view-user',
            'name' => 'Managed View User',
            'type' => 'user',
            'email' => 'managed-view-user@example.test',
            'position' => 'reservation',
            'status' => 'Active',
            'is_approved' => false,
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'is_subscribed' => false,
            'subscriber' => false,
        ]);

        $response = $this->actingAs($developer)->get(route('user-manager', [
            'search' => 'Managed View',
            'approval' => 'pending',
        ]));

        $response->assertOk();
        $response->assertViewIs('backend.admin.users.manager');
        $response->assertViewHasAll([
            'users',
            'summary',
            'filters',
            'positions',
            'statuses',
            'types',
        ]);
        $response->assertSee('User Access Directory');
        $response->assertSee('Managed View User');
        $this->assertArrayHasKey('pendingApproval', $response->viewData('summary'));
    }

    public function test_user_manager_create_update_approve_and_remove_flow(): void
    {
        Mail::fake();

        $developer = User::forceCreate([
            'username' => 'developer-user-manager-crud',
            'name' => 'Developer User Manager CRUD',
            'type' => 'admin',
            'email' => 'developer-user-manager-crud@example.test',
            'position' => 'developer',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'is_subscribed' => false,
            'subscriber' => false,
        ]);

        $this->actingAs($developer)
            ->post(route('create-user'), [
                'name' => 'Managed CRUD User',
                'username' => 'managed-crud-user',
                'email' => 'managed-crud-user@example.test',
                'type' => 'user',
                'position' => 'reservation',
                'code' => 'mcu',
                'phone' => '123456',
                'office' => 'Bali Kami',
                'address' => 'Test Address',
                'country' => 'Indonesia',
                'comment' => 'Created from feature test',
            ])
            ->assertRedirect(route('user-manager'));

        $createdUser = User::where('email', 'managed-crud-user@example.test')->firstOrFail();
        $this->assertSame('MCU', $createdUser->code);
        $this->assertTrue((bool) $createdUser->is_approved);
        $this->assertNotNull($createdUser->email_verified_at);

        $this->actingAs($developer)
            ->put(route('edit-user', $createdUser->id), [
                'managed_user_id' => $createdUser->id,
                'name' => 'Managed CRUD User Updated',
                'username' => 'managed-crud-user',
                'email' => 'managed-crud-user@example.test',
                'type' => 'user',
                'position' => 'staff',
                'status' => 'Block',
                'is_approved' => '1',
                'code' => 'mcu2',
                'phone' => '654321',
                'office' => 'Updated Office',
                'address' => 'Updated Address',
                'country' => 'Singapore',
                'comment' => 'Updated from feature test',
            ])
            ->assertRedirect(route('user-manager'));

        $createdUser->refresh();
        $developer->refresh();
        $this->assertSame('Managed CRUD User Updated', $createdUser->name);
        $this->assertSame('staff', $createdUser->position);
        $this->assertSame('Block', $createdUser->status);
        $this->assertFalse((bool) $createdUser->is_approved);
        $this->assertSame('Active', $developer->status);
        $this->assertTrue((bool) $developer->is_approved);

        $this->actingAs($developer)
            ->put(route('approve-user', $createdUser->id))
            ->assertRedirect(route('user-manager'));

        $createdUser->refresh();
        $this->assertSame('Active', $createdUser->status);
        $this->assertTrue((bool) $createdUser->is_approved);

        $this->actingAs($developer)
            ->delete(route('remove-user', $createdUser->id))
            ->assertRedirect(route('user-manager'));

        $this->assertDatabaseMissing('users', [
            'email' => 'managed-crud-user@example.test',
        ]);
    }

    public function test_user_manager_rejects_self_deactivation(): void
    {
        $developer = User::forceCreate([
            'username' => 'dev-self-deactivate',
            'name' => 'Developer Self Deactivation',
            'type' => 'admin',
            'email' => 'developer-self-deactivation@example.test',
            'position' => 'developer',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'is_subscribed' => false,
            'subscriber' => false,
        ]);

        $this->actingAs($developer)
            ->put(route('edit-user', $developer->id), [
                'managed_user_id' => $developer->id,
                'name' => 'Developer Self Deactivation',
                'username' => 'dev-self-deactivate',
                'email' => 'developer-self-deactivation@example.test',
                'type' => 'admin',
                'position' => 'developer',
                'status' => 'Block',
                'is_approved' => '0',
                'code' => 'DEV',
            ])
            ->assertRedirect(route('user-manager'))
            ->assertSessionHas('invalid', 'You cannot deactivate or unapprove your own account.');

        $developer->refresh();
        $this->assertSame('Active', $developer->status);
        $this->assertTrue((bool) $developer->is_approved);
    }

    public function test_backend_finance_and_report_views_are_sourced_from_backend_structure(): void
    {
        $invoiceController = file_get_contents(app_path('Http/Controllers/InvoiceAdminController.php'));
        $downloadController = file_get_contents(app_path('Http/Controllers/DownloadDataHotelController.php'));
        $reportViews = [
            'index',
            'hotel',
            'hotel-test',
            'hotel-package',
            'hotel-promo',
            'tour',
        ];

        $this->assertFileExists(resource_path('views/backend/finance/invoices/index.blade.php'));
        $this->assertFileExists(resource_path('views/backend/finance/invoices/detail.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/invoice.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/invoice-detail.blade.php'));
        $this->assertStringContainsString("view('backend.finance.invoices.index'", $invoiceController);
        $this->assertStringContainsString("view('backend.finance.invoices.detail'", $invoiceController);
        $this->assertStringNotContainsString("view('admin.invoice'", $invoiceController);
        $this->assertStringNotContainsString("view('admin.invoice-detail'", $invoiceController);

        foreach ($reportViews as $reportView) {
            $this->assertFileExists(resource_path("views/backend/reports/downloads/{$reportView}.blade.php"));
        }

        $this->assertFileDoesNotExist(resource_path('views/main/download.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/main/downloadhotel.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/main/download-test.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/main/download-hotel-package.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/main/download-hotel-promo.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/main/download-data-tour.blade.php'));
        $this->assertStringContainsString("view('backend.reports.downloads.index'", $downloadController);
        $this->assertStringContainsString("view('backend.reports.downloads.hotel'", $downloadController);
        $this->assertStringContainsString("view('backend.reports.downloads.hotel-test'", $downloadController);
        $this->assertStringContainsString("view('backend.reports.downloads.hotel-package'", $downloadController);
        $this->assertStringContainsString("view('backend.reports.downloads.hotel-promo'", $downloadController);
        $this->assertStringContainsString("view('backend.reports.downloads.tour'", $downloadController);
        $this->assertStringContainsString("PDF::loadView('backend.reports.downloads.hotel'", $downloadController);
        $this->assertStringNotContainsString("view('main.download", $downloadController);
        $this->assertStringNotContainsString("PDF::loadView('main.downloadhotel'", $downloadController);
    }

    public function test_remaining_user_order_edit_legacy_views_are_cleaned_up(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $legacyWrapper = file_get_contents(resource_path('views/frontend/home/orders/edit-legacy.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/edit-room.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/edit-room.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/edit-order-tour.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/edit-order-hotel-promo.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/view-order-hotel-promo.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/add-order-wedding.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.edit-room'", $controller);
        $this->assertStringNotContainsString("view('order.edit-room'", $controller);
        $this->assertStringNotContainsString("@include('order.edit-order-tour')", $legacyWrapper);
    }

    public function test_legacy_order_view_namespace_has_no_active_blade_files(): void
    {
        $legacyOrderPath = resource_path('views/order');
        $legacyFiles = [];

        if (is_dir($legacyOrderPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($legacyOrderPath, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                    $legacyFiles[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
                }
            }
        }

        $this->assertSame([], $legacyFiles);
    }

    public function test_wedding_order_package_sections_are_sourced_from_frontend_home_structure(): void
    {
        $weddingEdit = file_get_contents(resource_path('views/frontend/home/orders/weddings/edit.blade.php'));
        $sections = [
            'accommodation',
            'additional-services',
            'bride',
            'ceremony-and-decoration-venue',
            'flight',
            'include-services',
            'invitations',
            'reception-and-decoration-venue',
            'suite-and-villa-brides',
            'suite-and-villa-invitations',
            'transports',
            'wedding-detail',
            'wedding-dinner-venue',
            'wedding-lunch-venue',
            'wedding-venue',
        ];

        foreach ($sections as $section) {
            $this->assertFileExists(resource_path("views/frontend/home/orders/weddings/sections/{$section}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/order-wedding-package/{$section}.blade.php"));
        }

        $this->assertStringContainsString("@include('frontend.home.orders.weddings.sections.bride')", $weddingEdit);
        $this->assertStringContainsString("@include('frontend.home.orders.weddings.sections.accommodation')", $weddingEdit);
        $this->assertStringContainsString("@include('frontend.home.orders.weddings.sections.transports')", $weddingEdit);
        $this->assertStringNotContainsString("@include('order-wedding-package.", $weddingEdit);
    }

    public function test_admin_panel_view_is_sourced_from_backend_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/AdminPanelController.php'));
        $view = file_get_contents(resource_path('views/backend/developer/index.blade.php'));

        $this->assertFileExists(resource_path('views/backend/developer/index.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/adminpanel.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/panels/index.blade.php'));
        $this->assertStringContainsString("view('backend.developer.index'", $controller);
        $this->assertStringNotContainsString("view('admin.adminpanel'", $controller);
        $this->assertStringNotContainsString("view('admin.panels.index'", $controller);
        $this->assertStringNotContainsString('main-card-box', $view);
        $this->assertStringNotContainsString('Contract Rate Trend', $view);
        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/chart.js', $view);
        $this->assertStringNotContainsString('adminPanelPriceChart', $view);
        $this->assertStringNotContainsString('Upcoming Pipeline', $view);
        $this->assertStringNotContainsString('Latest Orders', $view);
        $this->assertStringNotContainsString('$orderPipeline', $view);
        $this->assertStringContainsString('Platform Health Checks', $view);
        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('backend-page-primary-action', $view);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--4', $view);
        $this->assertStringContainsString('backend-kpi-card backend-kpi-card--', $view);
        $this->assertStringContainsString('backend-panel admin-registration-access', $view);
        $this->assertStringContainsString('backend-panel admin-analytics-section', $view);
        $this->assertStringContainsString('backend-section-header', $view);
        $this->assertStringContainsString('backend-section-header__label', $view);
        $this->assertStringNotContainsString('admin-panel-section ', $view);
        $this->assertStringNotContainsString('admin-panel-section__header', $view);
        $this->assertStringNotContainsString('admin-panel-section__label', $view);
        $this->assertStringNotContainsString('admin-panel-stat-grid', $view);
        $this->assertStringNotContainsString('admin-panel-stat admin-panel-stat--', $view);
        $this->assertStringNotContainsString('UI Configuration Snapshot', $view);
        $this->assertStringNotContainsString('UI Config', $view);
        $this->assertStringContainsString('Website Analytics', $view);
        $this->assertStringContainsString('Traffic Overview', $view);
        $this->assertStringContainsString("trafficAnalytics['series']", $view);
        $this->assertStringNotContainsString("@include('backend.developer.partials.", $view);
        $this->assertStringContainsString("mix('build/backend/css/admin/panel/index.css')", $view);
        $this->assertStringContainsString("mix('build/backend/js/admin/panel/index.js')", $view);
    }

    public function test_admin_panel_assets_are_sourced_from_backend_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));
        $scss = file_get_contents(resource_path('backend/scss/admin/panel/_index.scss'));

        $this->assertFileExists(resource_path('backend/js/admin/panel/index.js'));
        $this->assertFileExists(resource_path('backend/scss/admin/panel/index-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/admin/panel/_index.scss'));
        $this->assertStringContainsString("resources/backend/js/admin/panel/index.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/admin/panel/index-entry.scss", $mix);
        $this->assertStringContainsString('.admin-analytics-chart__bars', $scss);
        $this->assertStringContainsString('.admin-analytics-breakdown', $scss);
        $this->assertStringContainsString('.admin-panel-wide', $scss);
        $this->assertStringNotContainsString('.admin-panel-section {', $scss);
        $this->assertStringNotContainsString('.admin-panel-section__header', $scss);
        $this->assertStringNotContainsString('.admin-panel-section__label', $scss);
        $this->assertStringNotContainsString('.admin-panel-stat', $scss);
        $this->assertStringNotContainsString('.admin-panel-stat-grid', $scss);
    }

    public function test_currency_page_is_sourced_from_backend_structure_and_assets(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/UsdRatesController.php'));
        $bankController = file_get_contents(app_path('Http/Controllers/BankAccountController.php'));
        $view = file_get_contents(resource_path('views/backend/developer/currency.blade.php'));
        $partial = file_get_contents(resource_path('views/backend/developer/partials/currency-bank-modal.blade.php'));
        $mix = file_get_contents(base_path('webpack.mix.js'));
        $scss = file_get_contents(resource_path('backend/scss/admin/currency/_index.scss'));

        $this->assertFileExists(resource_path('views/backend/developer/currency.blade.php'));
        $this->assertFileExists(resource_path('views/backend/developer/partials/currency-bank-modal.blade.php'));
        $this->assertFileExists(resource_path('backend/js/admin/currency/index.js'));
        $this->assertFileExists(resource_path('backend/scss/admin/currency/index-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/admin/currency/_index.scss'));
        $this->assertStringContainsString("view('backend.developer.currency'", $controller);
        $this->assertStringContainsString('Cache::remember', $controller);
        $this->assertStringContainsString('externalRates', $controller);
        $this->assertStringContainsString('validateBankAccount', $bankController);
        $this->assertStringContainsString("'account_name' => ['required'", $bankController);
        $this->assertStringContainsString("mix('build/backend/css/admin/currency/index.css')", $view);
        $this->assertStringContainsString("mix('build/backend/js/admin/currency/index.js')", $view);
        $this->assertStringContainsString("@include('backend.developer.partials.currency-bank-modal'", $view);
        $this->assertStringContainsString('currency-admin-page', $view);
        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('backend-page-toolbar currency-admin-toolbar', $view);
        $this->assertStringContainsString('backend-page-primary-action', $view);
        $this->assertStringContainsString('backend-feedback', $view);
        $this->assertStringContainsString('backend-alert backend-alert--', $view);
        $this->assertStringContainsString('backend-modal currency-admin-modal', $view);
        $this->assertStringContainsString('backend-modal__header currency-admin-modal__header', $view);
        $this->assertStringContainsString('backend-modal__body currency-admin-modal__body', $view);
        $this->assertStringContainsString('backend-modal__footer currency-admin-modal__footer', $view);
        $this->assertStringContainsString('backend-empty-state currency-admin-empty', $view);
        $this->assertStringContainsString('backend-kpi-card backend-kpi-card--blue', $view);
        $this->assertStringContainsString('backend-kpi-card__icon', $view);
        $this->assertStringContainsString('backend-panel currency-bank-section', $view);
        $this->assertStringContainsString('backend-section-header currency-admin-section-heading', $view);
        $this->assertStringContainsString('backend-section-header__label', $view);
        $this->assertStringNotContainsString('currency-bank-summary', $view);
        $this->assertStringNotContainsString('<span class="currency-admin-eyebrow">Bank Accounts</span>', $view);
        $this->assertStringContainsString('currency-rate-card', $scss);
        $this->assertStringContainsString('currency-bank-card', $scss);
        $this->assertStringNotContainsString('.currency-admin-section-heading h2', $scss);
        $this->assertStringNotContainsString('.currency-admin-alert', $scss);
        $this->assertStringNotContainsString('.currency-admin-empty', $scss);
        $this->assertStringNotContainsString('.currency-admin-modal .modal-content', $scss);
        $this->assertStringNotContainsString("background: #f8fbff;\n  padding: 18px;", $scss);
        $this->assertStringNotContainsString('.currency-bank-summary', $scss);
        $this->assertStringContainsString('.currency-admin-modal .currency-admin-form-grid label > span', $scss);
        $this->assertStringContainsString('color: var(--backend-muted-link) !important;', $scss);
        $this->assertStringContainsString('color: var(--backend-required) !important;', $scss);
        $this->assertStringNotContainsString('background-color: #ffffff !important;', $scss);
        $this->assertStringNotContainsString('background: var(--backend-danger);', $scss);
        $this->assertStringContainsString('transform: var(--backend-button-hover-transform);', $scss);
        $this->assertStringContainsString('Save Account', $partial);
        $this->assertStringNotContainsString('card-box m-b-18', $view);
        $this->assertStringContainsString("resources/backend/js/admin/currency/index.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/admin/currency/index-entry.scss", $mix);
    }

    public function test_currency_controller_returns_backend_currency_view_data_with_cached_external_rates(): void
    {
        Cache::forget('backend.currency.external_rates');
        config(['services.exchange_rate.key' => 'testing-key']);

        Http::fake([
            'https://v6.exchangerate-api.com/*' => Http::response([
                'result' => 'success',
                'base_code' => 'USD',
                'conversion_rates' => [
                    'IDR' => 15000,
                    'USD' => 1,
                    'CNY' => 7.5,
                    'TWD' => 30,
                ],
            ], 200),
        ]);

        UsdRates::forceCreate([
            'name' => 'USD',
            'rate' => '14950',
            'sell' => '14950',
            'buy' => '14850',
            'difference' => '100',
        ]);
        UsdRates::forceCreate([
            'name' => 'CNY',
            'rate' => '2050',
            'sell' => '2050',
            'buy' => '2020',
            'difference' => '30',
        ]);
        UsdRates::forceCreate([
            'name' => 'TWD',
            'rate' => '505',
            'sell' => '505',
            'buy' => '500',
            'difference' => '5',
        ]);
        Tax::forceCreate([
            'name' => 'Default Tax',
            'tax' => 1.5,
        ]);
        BankAccount::forceCreate([
            'bank' => 'Test Bank',
            'currency' => 'USD',
            'account_name' => 'Test Account',
            'account_number' => '123456789',
            'location' => 'Indonesia',
            'address' => 'Test Address',
            'telephone' => '123',
            'swift_code' => 'TESTIDJA',
            'bank_code' => '001',
        ]);

        $view = app(\App\Http\Controllers\UsdRatesController::class)->index();

        $this->assertInstanceOf(\Illuminate\View\View::class, $view);
        $this->assertSame('backend.developer.currency', $view->name());
        $this->assertArrayHasKey('currencyRates', $view->getData());
        $this->assertArrayHasKey('externalRates', $view->getData());
        $this->assertArrayHasKey('tax', $view->getData());
        $this->assertArrayHasKey('bank_acc', $view->getData());
        $this->assertSame('available', $view->getData()['externalRates']['status']);
        $this->assertEquals(15000, $view->getData()['externalRates']['rates']['USD']);
        $this->assertEquals(2000, $view->getData()['externalRates']['rates']['CNY']);
        $this->assertEquals(500, $view->getData()['externalRates']['rates']['TWD']);
        $this->assertCount(3, $view->getData()['currencyRates']);
    }

    public function test_admin_panel_controller_returns_backend_dashboard_view_data(): void
    {
        Services::forceCreate([
            'name' => 'Hotels',
            'nicname' => 'hotels',
            'icon' => 'fa fa-hotel',
            'status' => 'Active',
        ]);
        WebsiteVisit::forceCreate([
            'visitor_hash' => 'visitor-a',
            'method' => 'GET',
            'path' => '/accommodations',
            'url' => 'https://example.test/accommodations',
            'route_name' => 'view.accommodation-services',
            'page_title' => 'Accommodations',
            'area' => 'landing-page',
            'country_code' => 'ID',
            'country_name' => 'Indonesia',
            'device_type' => 'desktop',
            'visit_date' => now()->toDateString(),
            'occurred_at' => now(),
        ]);

        $view = app(\App\Http\Controllers\AdminPanelController::class)->index();

        $this->assertInstanceOf(\Illuminate\View\View::class, $view);
        $this->assertSame('backend.developer.index', $view->name());
        $this->assertArrayHasKey('dashboardStats', $view->getData());
        $this->assertArrayHasKey('services', $view->getData());
        $this->assertArrayHasKey('currencyRates', $view->getData());
        $this->assertArrayHasKey('expectedCurrencies', $view->getData());
        $this->assertArrayHasKey('missingCurrencyRates', $view->getData());
        $this->assertArrayHasKey('developerHealthChecks', $view->getData());
        $this->assertArrayHasKey('trafficAnalytics', $view->getData());
        $this->assertArrayHasKey('registrationAccess', $view->getData());
        $this->assertArrayNotHasKey('configs', $view->getData());
        $this->assertArrayNotHasKey('uiConfigSummary', $view->getData());
        $this->assertArrayNotHasKey('orderPipeline', $view->getData());
        $this->assertArrayNotHasKey('recentOrders', $view->getData());
        $this->assertArrayNotHasKey('validOrderRevenue', $view->getData());
        $this->assertEqualsCanonicalizing([
            'dashboardStats',
            'services',
            'currencyRates',
            'expectedCurrencies',
            'missingCurrencyRates',
            'developerHealthChecks',
            'trafficAnalytics',
            'registrationAccess',
        ], array_intersect(array_keys($view->getData()), [
            'dashboardStats',
            'services',
            'currencyRates',
            'expectedCurrencies',
            'missingCurrencyRates',
            'developerHealthChecks',
            'trafficAnalytics',
            'registrationAccess',
        ]));
        $this->assertArrayHasKey('summary', $view->getData()['trafficAnalytics']);
        $this->assertArrayHasKey('series', $view->getData()['trafficAnalytics']);
        $this->assertArrayHasKey('topCountries', $view->getData()['trafficAnalytics']);
        $this->assertGreaterThanOrEqual(1, $view->getData()['trafficAnalytics']['summary'][0]['value']);
    }

    public function test_registration_access_control_files_and_middleware_are_registered(): void
    {
        $kernel = file_get_contents(app_path('Http/Kernel.php'));
        $registerController = file_get_contents(app_path('Http/Controllers/Auth/RegisterController.php'));
        $agentController = file_get_contents(app_path('Http/Controllers/AgentRegistrationController.php'));
        $adminController = file_get_contents(app_path('Http/Controllers/AdminPanelController.php'));
        $routeFile = file_get_contents(base_path('routes/web.php'));
        $migration = file_get_contents(database_path('migrations/2026_07_16_090000_create_system_settings_table.php'));
        $view = file_get_contents(resource_path('views/backend/developer/index.blade.php'));

        $this->assertFileExists(app_path('Models/SystemSetting.php'));
        $this->assertFileExists(app_path('Services/RegistrationAccessService.php'));
        $this->assertFileExists(app_path('Http/Middleware/EnsureRegistrationIsOpen.php'));
        $this->assertStringContainsString("'registration.open'", $kernel);
        $this->assertStringContainsString('registration.open', $registerController);
        $this->assertStringContainsString('registration.open', $agentController);
        $this->assertStringContainsString('updateRegistrationAccess', $adminController);
        $this->assertStringContainsString('admin-panel.registration-access.update', $routeFile);
        $this->assertStringContainsString("Schema::create('system_settings'", $migration);
        $this->assertStringContainsString('registration_access', $migration);
        $this->assertStringContainsString('Registration Access', $view);
    }

    public function test_website_analytics_tracking_files_are_registered(): void
    {
        $kernel = file_get_contents(app_path('Http/Kernel.php'));
        $migration = file_get_contents(database_path('migrations/2026_07_15_180000_create_website_visits_table.php'));

        $this->assertFileExists(app_path('Models/WebsiteVisit.php'));
        $this->assertFileExists(app_path('Http/Middleware/TrackWebsiteVisit.php'));
        $this->assertFileExists(database_path('migrations/2026_07_15_180000_create_website_visits_table.php'));
        $this->assertStringContainsString('TrackWebsiteVisit::class', $kernel);
        $this->assertStringContainsString("Schema::create('website_visits'", $migration);
        $this->assertStringContainsString('visitor_hash', $migration);
        $this->assertStringContainsString('country_code', $migration);
        $this->assertStringContainsString('visit_date', $migration);
    }

    public function test_registration_access_control_blocks_public_register_pages_and_submit_requests(): void
    {
        app(RegistrationAccessService::class)->update(false);

        $this->get(route('register'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('registration');

        $this->post(route('register'), [
            'name' => 'Blocked User',
            'username' => 'blockeduser',
            'email' => 'blocked-register@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => '1',
        ])->assertRedirect(route('login'))
            ->assertSessionHasErrors('registration');

        $this->assertDatabaseMissing('users', [
            'email' => 'blocked-register@example.test',
        ]);

        $this->get(route('agent.register'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('registration');

        $this->post(route('agent.register.submit'), [
            'company_name' => 'Blocked Agent',
            'pic_name' => 'Blocked PIC',
            'email' => 'blocked-agent@example.test',
            'phone' => '123456',
            'country' => 'ID',
            'company_address' => 'Blocked Address',
            'agree_terms' => '1',
        ])->assertRedirect(route('login'))
            ->assertSessionHasErrors('registration');

        $this->assertDatabaseMissing('agents', [
            'email' => 'blocked-agent@example.test',
        ]);
    }

    public function test_developer_can_toggle_registration_access_from_admin_panel(): void
    {
        $developer = User::forceCreate([
            'username' => 'developer-registration-toggle',
            'name' => 'Developer Toggle',
            'type' => 'admin',
            'email' => 'developer-registration-toggle@example.test',
            'position' => 'developer',
            'is_approved' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'is_subscribed' => false,
            'subscriber' => false,
        ]);

        app(RegistrationAccessService::class)->update(true);

        $this->actingAs($developer)
            ->put(route('admin-panel.registration-access.update'), ['enabled' => '0'])
            ->assertRedirect('/admin/panel');

        $this->assertFalse(app(RegistrationAccessService::class)->enabled());
        $this->assertDatabaseHas('system_settings', [
            'key' => RegistrationAccessService::SETTING_KEY,
            'status' => false,
        ]);

        $this->actingAs($developer)
            ->put(route('admin-panel.registration-access.update'), ['enabled' => '1'])
            ->assertRedirect('/admin/panel');

        $this->assertTrue(app(RegistrationAccessService::class)->enabled());
    }

    public function test_ui_config_feature_is_removed_from_project_runtime(): void
    {
        $routeFile = file_get_contents(base_path('routes/web.php'));
        $kernel = file_get_contents(app_path('Http/Kernel.php'));
        $helpers = file_get_contents(app_path('Helpers/helpers.php'));
        $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
        $footJs = file_get_contents(resource_path('views/layouts/footjs.blade.php'));
        $loginView = file_get_contents(resource_path('views/auth/login.blade.php'));
        $registerView = file_get_contents(resource_path('views/auth/register.blade.php'));
        $welcomeView = file_get_contents(resource_path('views/welcome.blade.php'));

        $this->assertFileDoesNotExist(app_path('Http/Controllers/UiConfigController.php'));
        $this->assertFileDoesNotExist(app_path('Models/UiConfig.php'));
        $this->assertFileDoesNotExist(app_path('Http/Middleware/CheckPageAccess.php'));
        $this->assertFileDoesNotExist(app_path('Policies/UiConfigPolicy.php'));
        $this->assertFileDoesNotExist(app_path('Http/Requests/StoreUiConfigRequest.php'));
        $this->assertFileDoesNotExist(app_path('Http/Requests/UpdateUiConfigRequest.php'));
        $this->assertFileDoesNotExist(database_path('factories/UiConfigFactory.php'));
        $this->assertFileDoesNotExist(database_path('seeders/UiConfigSeeder.php'));
        $this->assertFileDoesNotExist(database_path('migrations/2025_03_06_090758_create_ui_configs_table.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/ui-config.blade.php'));
        $this->assertStringNotContainsString('UiConfigController', $routeFile);
        $this->assertStringNotContainsString('/ui-config', $routeFile);
        $this->assertStringNotContainsString('admin.ui-config', $routeFile);
        $this->assertStringNotContainsString('page.access', $routeFile);
        $this->assertStringNotContainsString('page.access', $kernel);
        $this->assertStringNotContainsString('CheckPageAccess', $kernel);
        $this->assertStringNotContainsString('ui_config', $helpers);
        $this->assertStringNotContainsString('UiConfig', $helpers);
        $this->assertStringNotContainsString('uiEnabled', $provider);
        $this->assertStringNotContainsString('/ui-config/toggle', $footJs);
        $this->assertStringNotContainsString('@uiEnabled', $loginView);
        $this->assertStringNotContainsString('@uiEnabled', $registerView);
        $this->assertStringNotContainsString('@uiEnabled', $welcomeView);
        $this->assertStringNotContainsString('@elseUiEnabled', $registerView);
        $this->assertStringNotContainsString('@endUiEnabled', $loginView . $registerView . $welcomeView);
    }

    public function test_tours_phase_1_uses_backend_operations_structure_and_route_names(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Backend/Operations/Tours/TourAdminController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $indexView = file_get_contents(resource_path('views/backend/operations/tours/index.blade.php'));
        $detailView = file_get_contents(resource_path('views/backend/operations/tours/detail.blade.php'));
        $legacyIndexWrapper = file_get_contents(resource_path('views/admin/toursadmin.blade.php'));
        $legacyDetailWrapper = file_get_contents(resource_path('views/admin/toursadmindetail.blade.php'));
        $mix = file_get_contents(base_path('webpack.mix.js'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));

        $this->assertFileExists(resource_path('views/backend/operations/tours/index.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/tours/detail.blade.php'));
        $this->assertFileExists(resource_path('backend/js/operations/tours/index.js'));
        $this->assertFileExists(resource_path('backend/js/operations/tours/detail.js'));
        $this->assertFileExists(resource_path('backend/scss/operations/tours/index-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/operations/tours/detail-entry.scss'));
        $this->assertStringContainsString("view('backend.operations.tours.index'", $controller);
        $this->assertStringContainsString("view('backend.operations.tours.detail'", $controller);
        $this->assertStringNotContainsString("view('admin.toursadmin'", $controller);
        $this->assertStringNotContainsString("view('admin.toursadmindetail'", $controller);
        $this->assertStringContainsString("@include('backend.operations.tours.index')", $legacyIndexWrapper);
        $this->assertStringContainsString("@include('backend.operations.tours.detail')", $legacyDetailWrapper);
        $this->assertStringContainsString("Route::get('/tours-admin'", $routes);
        $this->assertStringContainsString("->name('admin.tour-packages.index')", $routes);
        $this->assertStringContainsString("->name('admin.tours.show')", $routes);
        $this->assertStringContainsString("->name('admin.tours.create')", $routes);
        $this->assertStringContainsString("->name('admin.tours.edit')", $routes);
        $this->assertStringContainsString("->name('admin.tours.destroy')", $routes);
        $this->assertStringContainsString("->name('admin.tours.store')", $routes);
        $this->assertStringContainsString("->name('admin.tours.update')", $routes);
        $this->assertStringContainsString("->name('admin.tours.prices.store')", $routes);
        $this->assertStringContainsString("->name('admin.tours.prices.update')", $routes);
        $this->assertStringContainsString("->name('admin.tours.prices.destroy')", $routes);
        $this->assertStringContainsString("route('admin.tour-packages.index')", $controller);
        $this->assertStringContainsString("route('admin.tours.show'", $controller);
        $this->assertStringNotContainsString('redirect("/tours-admin', $controller);
        $this->assertStringNotContainsString('redirect("/detail-tour-', $controller);
        $this->assertStringContainsString('<x-backend.page-hero', $indexView);
        $this->assertStringContainsString('<x-backend.page-hero', $detailView);
        $this->assertStringContainsString('backend-page-primary-action', $indexView);
        $this->assertStringContainsString('backend-page-primary-action', $detailView);
        $this->assertStringContainsString('backend-page-toolbar tours-admin-toolbar', $indexView);
        $this->assertStringContainsString('backend-page-toolbar tour-detail-toolbar', $detailView);
        $this->assertStringContainsString('backend-feedback', $indexView);
        $this->assertStringContainsString('backend-feedback', $detailView);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--4', $indexView);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--4', $detailView);
        $this->assertStringContainsString('backend-panel tours-admin-panel', $indexView);
        $this->assertStringContainsString('backend-panel tour-detail-panel', $detailView);
        $this->assertStringContainsString('backend-section-header', $indexView);
        $this->assertStringContainsString('backend-section-header', $detailView);
        $this->assertStringContainsString('backend-table tours-admin-table', $indexView);
        $this->assertStringContainsString('backend-table tour-detail-price-table', $detailView);
        $this->assertStringContainsString('<th>Published Rate</th>', $detailView);
        $this->assertStringContainsString('data-label="Published Rate"', $detailView);
        $this->assertStringContainsString('tour-detail-rate', $detailView);
        $this->assertStringContainsString('tour-detail-rate-idr', $detailView);
        $this->assertStringContainsString('tour-price-calculation-action', $detailView);
        $this->assertStringContainsString('View calculation', $detailView);
        $this->assertStringContainsString('tourPriceCalculation{{ $price->id }}', $detailView);
        $this->assertStringContainsString('Agent Rate / Published Rate', $detailView);
        $this->assertStringContainsString('tour-price-calculation-summary', $detailView);
        $this->assertStringContainsString('tour-price-calculation', $detailView);
        $this->assertStringNotContainsString('<th>Contract Rate IDR</th>', $detailView);
        $this->assertStringNotContainsString('<th>Markup Type</th>', $detailView);
        $this->assertStringNotContainsString('<th>Markup</th>', $detailView);
        $this->assertStringNotContainsString('<th>Quoteable</th>', $detailView);
        $this->assertStringContainsString('backend-table-card-list tours-admin-card-list', $indexView);
        $this->assertStringContainsString('backend-status-badge', $indexView);
        $this->assertStringContainsString('backend-status-badge', $detailView);
        $this->assertStringContainsString('backend-modal tour-detail-modal', $detailView);
        $this->assertStringContainsString('data-tour-filter="name"', $indexView);
        $this->assertStringContainsString('data-tour-delete', $indexView);
        $this->assertStringContainsString('data-tour-price-filter="capacity"', $detailView);
        $this->assertStringNotContainsString('data-tour-gallery', $detailView);
        $this->assertStringNotContainsString('<script>', $indexView);
        $this->assertStringNotContainsString('<script>', $detailView);
        $this->assertStringNotContainsString('card-box', $indexView);
        $this->assertStringNotContainsString('btn-view', $indexView);
        $this->assertStringNotContainsString('btn-edit', $indexView);
        $this->assertStringNotContainsString('btn-delete', $indexView);
        $this->assertStringContainsString("resources/backend/js/operations/tours/index.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/operations/tours/index-entry.scss", $mix);
        $this->assertStringContainsString("resources/backend/js/operations/tours/detail.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/operations/tours/detail-entry.scss", $mix);
        $this->assertStringContainsString('## Tours Backend Standardization Roadmap', $roadmap);
        $this->assertStringContainsString('- [x] Tambahkan route name final `admin.tours.*` untuk profile CRUD.', $roadmap);
    }

    public function test_tours_phase_2_index_detail_and_price_modal_use_shared_backend_ui(): void
    {
        $indexView = file_get_contents(resource_path('views/backend/operations/tours/index.blade.php'));
        $detailView = file_get_contents(resource_path('views/backend/operations/tours/detail.blade.php'));
        $priceFieldsPartial = file_get_contents(resource_path('views/backend/operations/tours/partials/price-fields.blade.php'));
        $indexJs = file_get_contents(resource_path('backend/js/operations/tours/index.js'));
        $detailJs = file_get_contents(resource_path('backend/js/operations/tours/detail.js'));
        $indexScss = file_get_contents(resource_path('backend/scss/operations/tours/_index.scss'));
        $detailScss = file_get_contents(resource_path('backend/scss/operations/tours/_detail.scss'));
        $backendAppScss = file_get_contents(resource_path('backend/scss/app.scss'));
        $tourImagesModel = file_get_contents(app_path('Models/ToursImages.php'));
        $tourAssetService = file_get_contents(app_path('Services/Tours/TourAssetService.php'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $tourDocs = file_get_contents(base_path('docs/modules/tour-package.md'));

        $this->assertStringContainsString('<x-backend.page-hero', $indexView);
        $this->assertStringContainsString('<x-backend.page-hero', $detailView);
        $this->assertStringContainsString('backend-page-toolbar tours-admin-toolbar', $indexView);
        $this->assertStringContainsString('backend-page-toolbar tour-detail-toolbar', $detailView);
        $this->assertStringContainsString('backend-feedback tours-admin-feedback', $indexView);
        $this->assertStringContainsString('backend-feedback tour-detail-feedback', $detailView);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--4', $indexView);
        $this->assertStringContainsString('backend-kpi-grid backend-kpi-grid--4', $detailView);
        $this->assertStringContainsString('backend-filter-panel tours-admin-filter', $indexView);
        $this->assertStringContainsString('backend-filter-panel tour-detail-price-filter', $detailView);
        $this->assertStringContainsString('backend-panel tours-admin-panel', $indexView);
        $this->assertStringContainsString('backend-panel tour-detail-panel', $detailView);
        $this->assertStringContainsString('backend-section-header tours-admin-panel__heading', $indexView);
        $this->assertStringContainsString('backend-section-header tour-detail-panel__heading', $detailView);
        $this->assertStringContainsString('backend-table tours-admin-table', $indexView);
        $this->assertStringContainsString('backend-table tour-detail-price-table', $detailView);
        $this->assertStringContainsString('backend-table-card-list tours-admin-card-list', $indexView);
        $this->assertStringContainsString('backend-table-empty', $indexView);
        $this->assertStringContainsString('backend-table-empty', $detailView);
        $this->assertStringContainsString('backend-empty-state', $indexView);
        $this->assertStringContainsString('backend-empty-state', $detailView);
        $this->assertStringContainsString('backend-status-badge', $indexView);
        $this->assertStringContainsString('backend-status-badge', $detailView);
        $this->assertStringContainsString('backend-page-primary-action', $indexView);
        $this->assertStringContainsString('backend-page-primary-action', $detailView);
        $this->assertStringContainsString('backend-toolbar-action', $detailView);
        $this->assertStringContainsString('backend-icon-action', $indexView);
        $this->assertStringContainsString('backend-icon-action', $detailView);
        $this->assertStringContainsString('backend-modal tour-detail-modal', $detailView);
        $this->assertStringContainsString('<x-backend.modal-close', $detailView);
        $this->assertStringContainsString('backend-modal__header-actions', $detailView);
        $this->assertStringContainsString('id="add-price"', $detailView);
        $this->assertStringContainsString('backend-form-grid tour-detail-form-grid', $priceFieldsPartial);
        $this->assertStringContainsString('backend-form-label', $priceFieldsPartial);
        $this->assertStringContainsString('data-backend-money-unit="IDR"', $priceFieldsPartial);
        $this->assertStringContainsString('data-backend-picker="date"', $priceFieldsPartial);
        $this->assertStringContainsString('required', $priceFieldsPartial);
        $this->assertStringContainsString('<x-backend.detail-layout class="tour-detail-layout">', $detailView);
        $this->assertStringContainsString('backend-detail-side-card tour-detail-context-panel', $detailView);
        $this->assertStringContainsString('backend-detail-side-list', $detailView);
        $this->assertStringContainsString('backend-detail-side-actions', $detailView);
        $this->assertStringContainsString('tour-detail-info-card', $detailView);
        $this->assertStringContainsString('Profile Summary', $detailView);
        $this->assertStringContainsString('tour-detail-richtext', $detailView);
        $this->assertStringContainsString('decoding="async"', $detailView);
        $this->assertStringContainsString('width="360"', $detailView);
        $this->assertStringNotContainsString('data-dismiss="modal"', $detailView);
        $this->assertStringContainsString('data-tour-filter="name"', $indexView);
        $this->assertStringContainsString('data-tour-filter="code"', $indexView);
        $this->assertStringContainsString('data-tour-price-filter="capacity"', $detailView);
        $this->assertStringContainsString('data-tour-filter', $indexJs);
        $this->assertStringContainsString('data-tour-delete', $indexJs);
        $this->assertStringContainsString('data-tour-price-filter', $detailJs);
        $this->assertStringContainsString('.tours-admin-panel', $indexScss);
        $this->assertStringContainsString('.tour-detail-modal .backend-modal__header-actions', $detailScss);
        $this->assertStringContainsString('.tour-detail-rate', $detailScss);
        $this->assertStringContainsString('.tour-detail-rate-idr', $detailScss);
        $this->assertStringContainsString('.tour-price-calculation-action', $detailScss);
        $this->assertStringContainsString('.tour-price-calculation-summary', $detailScss);
        $this->assertStringContainsString('.tour-price-calculation', $detailScss);
        $this->assertStringContainsString('grid-template-columns: minmax(220px, 320px) minmax(0, 1fr);', $detailScss);
        $this->assertStringContainsString('max-height: 240px;', $detailScss);
        $this->assertStringContainsString('.tour-detail-info-card .backend-table-card__header strong', $detailScss);
        $this->assertStringContainsString('Backend Tour Detail modal Price', $tourDocs);
        $this->assertStringContainsString('tabel Tour Prices pada detail backend hanya menampilkan', $tourDocs);
        $this->assertStringContainsString('Agent Rate/Published Rate dari quote canonical', $tourDocs);
        $this->assertStringContainsString('Backend Tour Gallery management sudah dihapus', $tourDocs);
        $this->assertStringContainsString('`tours_images` tetap dipertahankan', $tourDocs);
        $this->assertFileDoesNotExist(resource_path('views/partials/modal-dropzone.blade.php'));
        $this->assertFileDoesNotExist(app_path('Http/Controllers/Backend/Operations/Tours/TourGalleryAdminController.php'));
        $this->assertFileDoesNotExist(resource_path('backend/scss/components/_backend-media.scss'));
        $this->assertStringNotContainsString("public const TYPE_GALLERY = 'Gallery';", $tourImagesModel);
        $this->assertStringNotContainsString("'type',", $tourImagesModel);
        $this->assertStringNotContainsString('ToursImages::TYPE_GALLERY', $tourAssetService);
        $this->assertStringNotContainsString('data-tour-gallery', $detailView . $detailJs);
        $this->assertStringNotContainsString('BackendTourGalleryManager', $detailJs);
        $this->assertStringNotContainsString('galleryBase', $detailJs);
        $this->assertStringNotContainsString('tour-detail-gallery', $detailView . $detailScss);
        $this->assertStringNotContainsString('tour-gallery-manager', $detailScss);
        $this->assertStringNotContainsString('tour-gallery-upload-modal', $detailScss);
        $this->assertStringNotContainsString('backend-media-grid', $detailView . $backendAppScss);
        $this->assertStringNotContainsString("@import 'components/backend-media';", $backendAppScss);

        foreach ([$indexView, $detailView, $priceFieldsPartial] as $viewContent) {
            $this->assertStringNotContainsString('card-box', $viewContent);
            $this->assertStringNotContainsString('btn-view', $viewContent);
            $this->assertStringNotContainsString('btn-edit', $viewContent);
            $this->assertStringNotContainsString('btn-delete', $viewContent);
            $this->assertStringNotContainsString('class="btn btn-', $viewContent);
            $this->assertStringNotContainsString('data-table table', $viewContent);
            $this->assertStringNotContainsString('style=', $viewContent);
            $this->assertStringNotContainsString('onclick=', $viewContent);
            $this->assertStringNotContainsString('onkeyup=', $viewContent);
            $this->assertStringNotContainsString('img-fluid', $viewContent);
        }

        $this->assertStringNotContainsString('function searchTourByName', $indexView);
        $this->assertStringNotContainsString('function searchPriceByCapacity', $detailView);
        $this->assertStringNotContainsString('function deleteImage', $detailView);
        $this->assertStringNotContainsString('.card-box', $indexScss . $detailScss);
        $this->assertStringNotContainsString('.btn-view', $indexScss . $detailScss);
        $this->assertStringNotContainsString('.btn-edit', $indexScss . $detailScss);
        $this->assertStringNotContainsString('.btn-delete', $indexScss . $detailScss);
        $this->assertStringContainsString('- [x] Standarisasi index Tours memakai shared hero, toolbar, feedback, KPI, filter, panel, table, card list mobile, status badge, empty state, dan button/action backend.', $roadmap);
        $this->assertStringNotContainsString('panel, gallery, pricing table', $roadmap);
        $this->assertStringNotContainsString('Tour Gallery detail memakai satu modal manager', $roadmap);
    }

    public function test_tours_phase_3_forms_are_sourced_from_backend_operations_assets(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Backend/Operations/Tours/TourAdminController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $createView = file_get_contents(resource_path('views/backend/operations/tours/forms/create.blade.php'));
        $editView = file_get_contents(resource_path('views/backend/operations/tours/forms/edit.blade.php'));
        $repeaterPartial = file_get_contents(resource_path('views/backend/operations/tours/partials/tour-location-repeater.blade.php'));
        $formsJs = file_get_contents(resource_path('backend/js/operations/tours/forms.js'));
        $formsScss = file_get_contents(resource_path('backend/scss/operations/tours/_forms.scss'));
        $mix = file_get_contents(base_path('webpack.mix.js'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $tourDocs = file_get_contents(base_path('docs/modules/tour-package.md'));

        $this->assertFileExists(resource_path('views/backend/operations/tours/forms/create.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/tours/forms/edit.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/tours/partials/tour-location-repeater.blade.php'));
        $this->assertFileExists(resource_path('backend/js/operations/tours/forms.js'));
        $this->assertFileExists(resource_path('backend/scss/operations/tours/forms-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/operations/tours/_forms.scss'));
        $this->assertFileDoesNotExist(resource_path('views/backend/tours/create-tour.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/backend/tours/update-tour.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/backend/tours/partials/tour-location-repeater.blade.php'));
        $this->assertStringContainsString("view('backend.operations.tours.forms.create'", $controller);
        $this->assertStringContainsString("view('backend.operations.tours.forms.edit'", $controller);
        $this->assertStringNotContainsString("view('backend.tours.create-tour'", $controller);
        $this->assertStringNotContainsString("view('backend.tours.update-tour'", $controller);
        $this->assertStringContainsString("route('admin.tours.store')", $createView);
        $this->assertStringContainsString("route('admin.tours.update'", $editView);
        $this->assertStringContainsString("->name('admin.tours.create')", $routes);
        $this->assertStringContainsString("route('admin.tours.show'", $editView);
        $this->assertStringContainsString("mix('build/backend/css/operations/tours/forms.css')", $createView);
        $this->assertStringContainsString("mix('build/backend/js/operations/tours/forms.js')", $createView);
        $this->assertStringContainsString("mix('build/backend/css/operations/tours/forms.css')", $editView);
        $this->assertStringContainsString("mix('build/backend/js/operations/tours/forms.js')", $editView);
        $this->assertStringContainsString('<x-backend.page-hero', $createView);
        $this->assertStringContainsString('<x-backend.page-hero', $editView);
        $this->assertStringContainsString('<x-backend.breadcrumb-toolbar', $createView);
        $this->assertStringContainsString('class="tour-form-toolbar"', $createView);
        $this->assertStringContainsString('backend-page-toolbar tour-form-toolbar', $editView);
        $this->assertStringContainsString('backend-feedback tour-form-feedback', $createView);
        $this->assertStringContainsString('backend-feedback tour-form-feedback', $editView);
        $this->assertStringContainsString('backend-panel tour-form-panel', $createView);
        $this->assertStringContainsString('backend-panel tour-form-panel', $editView);
        $this->assertStringContainsString('backend-section-header tour-form-panel__heading', $createView);
        $this->assertStringContainsString('backend-section-header tour-form-panel__heading', $editView);
        $this->assertStringContainsString('tour-form-panel__body', $createView);
        $this->assertStringContainsString('tour-form-panel__body', $editView);
        $this->assertStringContainsString('backend-form-actions', $createView);
        $this->assertStringContainsString('backend-form-actions', $editView);
        $this->assertStringContainsString('backend-button backend-button-primary', $createView);
        $this->assertStringContainsString('backend-button backend-button-primary', $editView);
        $this->assertStringContainsString('backend-button backend-button-secondary', $createView);
        $this->assertStringContainsString('backend-button backend-button-secondary', $editView);
        $this->assertStringContainsString('<x-backend.detail-layout class="tour-create-layout">', $createView);
        $this->assertStringContainsString('backend-detail-side-card tour-create-context-panel', $createView);
        $this->assertStringContainsString('backend-detail-side-list', $createView);
        $this->assertStringContainsString('backend-form-panel__body tour-form-panel__body', $createView);
        $this->assertStringContainsString('data-tour-create-wizard', $createView);
        $this->assertStringContainsString('data-tour-wizard-step="basic"', $createView);
        $this->assertStringContainsString('data-tour-wizard-step="route"', $createView);
        $this->assertStringContainsString('data-tour-wizard-step="content"', $createView);
        $this->assertStringContainsString('data-tour-wizard-step="media"', $createView);
        $this->assertStringContainsString('data-tour-wizard-step="review"', $createView);
        $this->assertStringContainsString('data-tour-wizard-submit', $createView);
        $this->assertStringContainsString('Review & Create', $createView);
        $this->assertStringContainsString('tour-create-review-layout', $createView);
        $this->assertStringContainsString('data-tour-review-route-days', $createView);
        $this->assertStringContainsString('data-tour-review-content-list', $createView);
        $this->assertStringContainsString('data-tour-review-content-summary', $createView);
        $this->assertStringContainsString('data-tour-review-code', $createView);
        $this->assertStringNotContainsString('Setup Progress', $createView);
        $this->assertStringNotContainsString('Tour Summary', $createView);
        $this->assertStringNotContainsString('tour-create-progress-list', $createView);
        $this->assertStringContainsString('backend-translation-group', $createView);
        $this->assertStringContainsString('backend-translation-grid', $createView);
        $this->assertStringContainsString('backend-translation-field', $createView);
        $this->assertStringContainsString('data-backend-richtext="true"', $createView);
        $this->assertStringContainsString('data-tour-cover-input', $createView);
        $this->assertStringContainsString('data-tour-cover-preview', $createView);
        $this->assertStringNotContainsString('name="itinerary"', $createView);
        $this->assertStringNotContainsString('name="itinerary_traditional"', $createView);
        $this->assertStringNotContainsString('name="itinerary_simplified"', $createView);
        $this->assertStringNotContainsString('name="itinerary"', $editView);
        $this->assertStringNotContainsString('name="itinerary_traditional"', $editView);
        $this->assertStringNotContainsString('name="itinerary_simplified"', $editView);
        $this->assertStringContainsString('<x-backend.detail-layout class="tour-create-layout tour-edit-layout">', $editView);
        $this->assertStringContainsString('data-tour-wizard-step="basic"', $editView);
        $this->assertStringContainsString('data-tour-wizard-step="route"', $editView);
        $this->assertStringContainsString('data-tour-wizard-step="content"', $editView);
        $this->assertStringContainsString('data-tour-wizard-step="media"', $editView);
        $this->assertStringContainsString('data-tour-wizard-step="review"', $editView);
        $this->assertStringContainsString('Review & Update', $editView);
        $this->assertStringContainsString('This Tour uses legacy manual itinerary content.', $editView);
        $this->assertStringContainsString('Saving other fields will preserve the legacy itinerary columns.', $editView);
        $this->assertStringContainsString('name="status"', $editView);
        $this->assertStringContainsString('Current Status', $editView);
        $this->assertStringContainsString('Setup Progress', $editView);
        $this->assertStringContainsString('Route Summary', $editView);
        $this->assertStringContainsString('Related Actions', $editView);
        $this->assertStringContainsString('TourPrice Boundary', $createView);
        $this->assertStringContainsString('Create Tour does not calculate or write TourPrice records.', $createView);
        $this->assertStringContainsString("backend.operations.tours.partials.tour-location-repeater", $createView);
        $this->assertStringContainsString("backend.operations.tours.partials.tour-location-repeater", $editView);
        $this->assertStringContainsString('data-tour-locations-repeater', $repeaterPartial);
        $this->assertStringContainsString('data-allow-empty', $repeaterPartial);
        $this->assertStringContainsString('data-tour-location-empty', $repeaterPartial);
        $this->assertStringContainsString('No tour stops added yet.', $repeaterPartial);
        $this->assertStringContainsString('data-toggle-tour-location-editor', $repeaterPartial);
        $this->assertStringContainsString('data-resolve-tour-location', $repeaterPartial);
        $this->assertStringContainsString('data-show-tour-manual-coordinates', $repeaterPartial);
        $this->assertStringContainsString('data-resolve-url', $repeaterPartial);
        $this->assertStringContainsString('data-references-url', $repeaterPartial);
        $this->assertStringContainsString('data-add-tour-location', $repeaterPartial);
        $this->assertStringContainsString('data-remove-tour-location', $repeaterPartial);
        $this->assertStringNotContainsString('tour-location-item border rounded p-3', $repeaterPartial);
        $this->assertStringContainsString('backend-button backend-button-secondary', $repeaterPartial);
        $this->assertStringContainsString('backend-icon-action is-danger', $repeaterPartial);
        $this->assertStringContainsString('data-tour-locations-repeater', $formsJs);
        $this->assertStringContainsString('data-add-tour-location', $formsJs);
        $this->assertStringContainsString('data-remove-tour-location', $formsJs);
        $this->assertStringContainsString('data-tour-location-name', $formsJs);
        $this->assertStringContainsString('data-tour-location-map-url', $formsJs);
        $this->assertStringContainsString('data-tour-cover-input', $formsJs);
        $this->assertStringContainsString('tour:create-summary-refresh', $formsJs);
        $this->assertStringContainsString('data-tour-wizard-panel', $formsJs);
        $this->assertStringContainsString('data-tour-wizard-current-label', $formsJs);
        $this->assertStringContainsString('data-tour-review-route-days', $formsJs);
        $this->assertStringContainsString('data-tour-review-content-list', $formsJs);
        $this->assertStringContainsString('summernote', $formsJs);
        $this->assertStringContainsString('.tour-form-page', $formsScss);
        $this->assertStringContainsString('.tour-create-wizard', $formsScss);
        $this->assertStringContainsString('.tour-create-wizard__steps', $formsScss);
        $this->assertStringContainsString('.tour-location-empty', $formsScss);
        $this->assertStringContainsString('.tour-create-review-layout', $formsScss);
        $this->assertStringContainsString('.tour-create-review-route', $formsScss);
        $this->assertStringContainsString('.tour-create-review-content', $formsScss);
        $this->assertStringContainsString('.tour-create-review-grid', $formsScss);
        $this->assertStringContainsString('.tour-form-cover-control', $formsScss);
        $this->assertStringContainsString('.tour-create-layout .backend-translation-group + .backend-form-grid', $formsScss);
        $this->assertStringContainsString('.tour-location-repeater', $formsScss);
        $this->assertStringContainsString('.tour-location-suggest__menu', $formsScss);
        $this->assertStringContainsString('.tour-location-marker-preview', $formsScss);
        $this->assertStringContainsString('## Backend Add Tour Standard', $tourDocs);
        $this->assertStringContainsString('Backend Add Tour adalah Create page untuk master `Tours` saja.', $tourDocs);
        $this->assertStringContainsString('`TourPrice` adalah resource pricing terpisah.', $tourDocs);
        $this->assertStringContainsString('`x-backend.detail-layout` dengan main/sidebar 70%/30%', $tourDocs);
        $this->assertStringContainsString('wizard 5 step: Basic Information, Route & Itinerary,', $tourDocs);
        $this->assertStringContainsString('`0` lokasi valid', $tourDocs);
        $this->assertStringContainsString('Create Tour tidak menampilkan atau menerima input manual `itinerary`', $tourDocs);
        $this->assertStringContainsString('`BuildsTourLocationItinerary` menghasilkan output saat data dibaca/render', $tourDocs);
        $this->assertStringContainsString('Repeater locations tampil flat/unframed', $tourDocs);
        $this->assertStringContainsString('right sidebar berisi initial status dan pricing boundary', $tourDocs);
        $this->assertStringContainsString('Edit Tour memakai wizard 5 step yang sama dengan Create Tour', $tourDocs);
        $this->assertStringContainsString('Update Tour tidak merender input manual `itinerary`', $tourDocs);
        $this->assertStringContainsString("resources/backend/js/operations/tours/forms.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/operations/tours/forms-entry.scss", $mix);

        foreach ([$createView, $editView, $repeaterPartial] as $viewContent) {
            $this->assertStringNotContainsString('card-box', $viewContent);
            $this->assertStringNotContainsString('btn-view', $viewContent);
            $this->assertStringNotContainsString('btn-edit', $viewContent);
            $this->assertStringNotContainsString('btn-delete', $viewContent);
            $this->assertStringNotContainsString('class="btn btn-', $viewContent);
            $this->assertStringNotContainsString('button class="btn', $viewContent);
            $this->assertStringNotContainsString('data-table table', $viewContent);
            $this->assertStringNotContainsString('style=', $viewContent);
            $this->assertStringNotContainsString('onclick=', $viewContent);
            $this->assertStringNotContainsString('onkeyup=', $viewContent);
            $this->assertStringNotContainsString('<script>', $viewContent);
            $this->assertStringNotContainsString('<style>', $viewContent);
            $this->assertStringNotContainsString('backend.tours.partials', $viewContent);
        }

        $this->assertStringNotContainsString('.card-box', $formsScss);
        $this->assertStringNotContainsString('.btn-view', $formsScss);
        $this->assertStringNotContainsString('.btn-edit', $formsScss);
        $this->assertStringNotContainsString('.btn-delete', $formsScss);
        $this->assertStringNotContainsString('id="add-tour"', $createView);
        $this->assertStringNotContainsString('id="my-awesome-dropzone"', $createView);
        $this->assertStringNotContainsString('name="initial_state"', $createView);
        $this->assertStringNotContainsString('name="status"', $createView);
        $this->assertStringNotContainsString('cover-preview-div', $createView);
        $this->assertStringContainsString('- [x] Pindahkan create/edit Tours dari `resources/views/backend/tours` ke `resources/views/backend/operations/tours/forms`.', $roadmap);
        $this->assertStringContainsString('- [x] Pecah inline script location repeater menjadi JS domain Tours.', $roadmap);
    }

    public function test_tours_phase_4_controller_decomposition_and_form_requests_are_registered(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $profileController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Tours/TourAdminController.php'));
        $priceController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Tours/TourPriceAdminController.php'));
        $locationService = file_get_contents(app_path('Services/Tours/TourLocationService.php'));
        $storeTourRequest = file_get_contents(app_path('Http/Requests/Backend/Operations/Tours/StoreTourAdminRequest.php'));
        $updateTourRequest = file_get_contents(app_path('Http/Requests/Backend/Operations/Tours/UpdateTourAdminRequest.php'));
        $storePriceRequest = file_get_contents(app_path('Http/Requests/Backend/Operations/Tours/StoreTourPriceAdminRequest.php'));
        $updatePriceRequest = file_get_contents(app_path('Http/Requests/Backend/Operations/Tours/UpdateTourPriceAdminRequest.php'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $createView = file_get_contents(resource_path('views/backend/operations/tours/forms/create.blade.php'));
        $formsJs = file_get_contents(resource_path('backend/js/operations/tours/forms.js'));
        $tourDocs = file_get_contents(base_path('docs/modules/tour-package.md'));

        $this->assertFileExists(app_path('Http/Controllers/Backend/Operations/Tours/TourAdminController.php'));
        $this->assertFileExists(app_path('Http/Controllers/Backend/Operations/Tours/TourPriceAdminController.php'));
        $this->assertFileDoesNotExist(app_path('Http/Controllers/Backend/Operations/Tours/TourGalleryAdminController.php'));
        $this->assertFileExists(app_path('Services/Tours/TourLocationService.php'));
        $this->assertFileExists(app_path('Http/Requests/Backend/Operations/Tours/StoreTourAdminRequest.php'));
        $this->assertFileExists(app_path('Http/Requests/Backend/Operations/Tours/UpdateTourAdminRequest.php'));
        $this->assertFileExists(app_path('Http/Requests/Backend/Operations/Tours/StoreTourPriceAdminRequest.php'));
        $this->assertFileExists(app_path('Http/Requests/Backend/Operations/Tours/UpdateTourPriceAdminRequest.php'));
        $this->assertStringContainsString('use App\Http\Controllers\Backend\Operations\Tours\TourAdminController;', $routes);
        $this->assertStringContainsString('use App\Http\Controllers\Backend\Operations\Tours\TourPriceAdminController;', $routes);
        $this->assertStringNotContainsString('TourGalleryAdminController', $routes);
        $this->assertStringContainsString("[TourAdminController::class,'index']", $routes);
        $this->assertStringContainsString("[TourAdminController::class,'show']", $routes);
        $this->assertStringContainsString("[TourAdminController::class,'create']", $routes);
        $this->assertStringContainsString("[TourAdminController::class,'edit']", $routes);
        $this->assertStringContainsString("[TourAdminController::class,'store']", $routes);
        $this->assertStringContainsString("[TourAdminController::class,'update']", $routes);
        $this->assertStringContainsString("[TourAdminController::class,'destroy']", $routes);
        $this->assertStringContainsString("[TourPriceAdminController::class,'store']", $routes);
        $this->assertStringContainsString("[TourPriceAdminController::class,'update']", $routes);
        $this->assertStringContainsString("[TourPriceAdminController::class,'destroy']", $routes);
        $this->assertStringNotContainsString("func.tour-gallery", $routes);
        $this->assertStringNotContainsString("[ToursAdminController::class,'index']", $routes);
        $this->assertStringNotContainsString("[ToursAdminController::class,'view_detail_tour']", $routes);
        $this->assertStringNotContainsString("[ToursAdminController::class,'view_add_tour']", $routes);
        $this->assertStringNotContainsString("[ToursAdminController::class,'view_edit_tour']", $routes);
        $this->assertStringNotContainsString("[ToursAdminController::class,'func_add_tour']", $routes);
        $this->assertStringNotContainsString("[ToursAdminController::class,'func_update_tour']", $routes);
        $this->assertStringNotContainsString("[ToursAdminController::class,'func_add_tour_price']", $routes);
        $this->assertStringNotContainsString("[ToursAdminController::class,'func_update_tour_price']", $routes);
        $this->assertStringNotContainsString("[ToursAdminController::class,'func_delete_tour_price']", $routes);
        $this->assertStringNotContainsString('[ToursImagesController::class, \'upload\']', $routes);
        $this->assertStringContainsString('StoreTourAdminRequest', $profileController);
        $this->assertStringContainsString('UpdateTourAdminRequest', $profileController);
        $this->assertStringContainsString('TourLocationService', $profileController);
        $this->assertStringContainsString('validateLocations', $profileController);
        $this->assertStringContainsString('->sync($tour, $locations)', $profileController);
        $this->assertStringContainsString("\$tour->status = 'Draft';", $profileController);
        $this->assertStringContainsString("'Add Tour', 'Tour Package', \$tour->id, 'add-tour'", $profileController);
        $this->assertStringContainsString('StoreTourPriceAdminRequest', $priceController);
        $this->assertStringContainsString('UpdateTourPriceAdminRequest', $priceController);
        $this->assertStringContainsString('resolveCoordinates', $locationService);
        $this->assertStringContainsString('searchReferences', $locationService);
        $this->assertStringContainsString("'cover' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'", $storeTourRequest);
        $this->assertStringContainsString("'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'", $updateTourRequest);
        $this->assertStringNotContainsString("'status' => 'nullable|string|max:255'", $storeTourRequest);
        $this->assertStringNotContainsString("'itinerary' => 'required|string'", $storeTourRequest);
        $this->assertStringNotContainsString("'itinerary_traditional' => 'required|string'", $storeTourRequest);
        $this->assertStringNotContainsString("'itinerary_simplified' => 'required|string'", $storeTourRequest);
        $this->assertStringContainsString("'package_highlights' => 'nullable|string'", $storeTourRequest);
        $this->assertStringContainsString("'include' => 'nullable|string'", $storeTourRequest);
        $this->assertStringContainsString("'exclude' => 'nullable|string'", $storeTourRequest);
        $this->assertStringContainsString("'additional_info' => 'nullable|string'", $storeTourRequest);
        $this->assertStringContainsString("['Include', false, 'include', 'include_traditional', 'include_simplified']", $formsJs);
        $this->assertStringContainsString("['Exclude', false, 'exclude', 'exclude_traditional', 'exclude_simplified']", $formsJs);
        $this->assertStringContainsString("['Additional Information', false, 'additional_info', 'additional_info_traditional', 'additional_info_simplified']", $formsJs);
        $this->assertStringContainsString("0 of 9 required fields filled", $createView);
        $this->assertStringContainsString('`package_highlights`, `include`, `exclude`, dan', $tourDocs);
        $this->assertStringNotContainsString("'itinerary' => 'required|string'", $updateTourRequest);
        $this->assertStringNotContainsString("'itinerary_traditional' => 'required|string'", $updateTourRequest);
        $this->assertStringNotContainsString("'itinerary_simplified' => 'required|string'", $updateTourRequest);
        $this->assertStringContainsString("'status' => ['required', Rule::in(['Active', 'Draft'])]", $updateTourRequest);
        $this->assertStringContainsString('foreach ($this->tourDetailFields() as $field)', $profileController);
        $this->assertStringNotContainsString("'itinerary',", $profileController);
        $this->assertStringContainsString("'type' => 'required|integer|exists:tour_types,id'", $storeTourRequest);
        $this->assertStringContainsString("'duration_days' => 'required|integer|min:1'", $storeTourRequest);
        $this->assertStringContainsString("'duration_nights' => 'required|integer|min:0'", $storeTourRequest);
        $this->assertStringContainsString("'min_qty' => ['required', 'integer', 'min:1']", $storePriceRequest);
        $this->assertStringContainsString("'max_qty' => ['required', 'integer', 'gte:min_qty']", $storePriceRequest);
        $this->assertStringContainsString("'contract_rate_idr' => ['required', 'integer', 'min:1']", $storePriceRequest);
        $this->assertStringContainsString("'markup_type' => ['required', Rule::in(TourPrices::MARKUP_TYPES)]", $storePriceRequest);
        $this->assertStringContainsString("'markup_amount' => ['required', 'regex:", $storePriceRequest);
        $this->assertStringContainsString("'valid_from' => ['required', 'date_format:Y-m-d']", $storePriceRequest);
        $this->assertStringContainsString("'valid_until' => ['required', 'date_format:Y-m-d'", $storePriceRequest);
        $this->assertStringContainsString('use App\\Models\\TourPrices;', $updatePriceRequest);
        $this->assertStringContainsString('overlapExceptPriceId', $updatePriceRequest);
        $this->assertStringContainsString('- [x] Buat namespace controller `App\Http\Controllers\Backend\Operations\Tours`.', $roadmap);
        $this->assertStringContainsString('- [x] Buat Form Request untuk create/update Tour dan create/update Tour Price.', $roadmap);
    }

    public function test_tours_phase_5_service_layer_and_view_models_are_registered(): void
    {
        $profileController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Tours/TourAdminController.php'));
        $priceController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Tours/TourPriceAdminController.php'));
        $indexView = file_get_contents(resource_path('views/backend/operations/tours/index.blade.php'));
        $detailView = file_get_contents(resource_path('views/backend/operations/tours/detail.blade.php'));
        $inventory = file_get_contents(app_path('Services/Tours/TourInventoryService.php'));
        $tourPackagePricing = file_get_contents(app_path('Services/Tours/TourPackagePricingService.php'));
        $tourPriceModel = file_get_contents(app_path('Models/TourPrices.php'));
        $taxResolver = file_get_contents(app_path('Services/Pricing/TaxResolver.php'));
        $pricingEngine = file_get_contents(app_path('Services/Pricing/PricingEngine.php'));
        $pricing = file_get_contents(app_path('Services/Tours/TourPricingService.php'));
        $assets = file_get_contents(app_path('Services/Tours/TourAssetService.php'));
        $locations = file_get_contents(app_path('Services/Tours/TourLocationService.php'));
        $audit = file_get_contents(app_path('Services/Tours/TourAuditService.php'));
        $indexVm = file_get_contents(app_path('ViewModels/Tours/TourIndexViewModel.php'));
        $detailVm = file_get_contents(app_path('ViewModels/Tours/TourDetailViewModel.php'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));

        $this->assertFileExists(app_path('Services/Tours/TourInventoryService.php'));
        $this->assertFileExists(app_path('Services/Tours/TourPricingService.php'));
        $this->assertFileExists(app_path('Services/Tours/TourAssetService.php'));
        $this->assertFileExists(app_path('Services/Tours/TourLocationService.php'));
        $this->assertFileExists(app_path('Services/Tours/TourAuditService.php'));
        $this->assertFileExists(app_path('ViewModels/Tours/TourIndexViewModel.php'));
        $this->assertFileExists(app_path('ViewModels/Tours/TourDetailViewModel.php'));
        $this->assertStringContainsString('TourInventoryService', $profileController);
        $this->assertStringContainsString('$inventory->indexData()', $profileController);
        $this->assertStringContainsString('$inventory->detailData((int) $id)', $profileController);
        $this->assertStringContainsString('$inventory->formOptions()', $profileController);
        $this->assertStringContainsString('$inventory->editData((int) $id)', $profileController);
        $this->assertStringContainsString('TourAssetService', $profileController);
        $this->assertStringContainsString('TourLocationService', $profileController);
        $this->assertStringContainsString('TourAuditService', $profileController);
        $this->assertStringContainsString('TourAuditService', $priceController);
        $this->assertStringNotContainsString('Cache::remember', $profileController);
        $this->assertStringNotContainsString('ActionLog::', $profileController);
        $this->assertStringNotContainsString('UsdRates::', $profileController);
        $this->assertStringNotContainsString('Tax::', $profileController);
        $this->assertStringNotContainsString('Storage::disk', $profileController);
        $this->assertStringContainsString('TourIndexViewModel', $inventory);
        $this->assertStringContainsString('TourDetailViewModel', $inventory);
        $this->assertStringContainsString('indexData', $inventory);
        $this->assertStringContainsString('detailData', $inventory);
        $this->assertStringContainsString('formOptions', $inventory);
        $this->assertStringContainsString('editData', $inventory);
        $this->assertStringContainsString("'types' => TourType::query()->orderBy('type')->get()", $inventory);
        $this->assertStringNotContainsString("'tours' => Tours::all()", $inventory);
        $this->assertStringNotContainsString("'partners' => Partners::all()", $inventory);
        $this->assertStringContainsString('TourPackagePricingService', $inventory);
        $this->assertStringContainsString('quoteEachTier', $indexVm . $detailVm);
        $this->assertStringContainsString('resolveStoredUsdSell', $tourPackagePricing);
        $this->assertStringContainsString("resolveStored('Tour Package'", $tourPackagePricing);
        $this->assertStringNotContainsString('resolveUsdSell($calculatedAt)', $tourPackagePricing);
        $this->assertStringNotContainsString("resolve('Tour Package'", $tourPackagePricing);
        $this->assertStringContainsString('public function resolveStored', $taxResolver);
        $this->assertStringContainsString('Tax::query()->orderBy(\'id\')->first()', $taxResolver);
        $this->assertStringContainsString("public const ROUNDING_POLICY = 'ceiling-whole-usd-v1'", $pricingEngine);
        $this->assertStringContainsString('roundUsdMinorUpToWhole', $pricingEngine);
        $this->assertStringContainsString('raw_unit_price_usd_minor', $pricingEngine);
        $this->assertStringNotContainsString("public const ROUNDING_POLICY = 'half-up-v1'", $pricingEngine);
        $this->assertStringNotContainsString("->whereNotNull('markup_source')", $tourPriceModel);
        $this->assertStringNotContainsString("->whereNotNull('markup_verified_at')", $tourPriceModel);
        $this->assertStringNotContainsString("->whereNotNull('markup_verified_by')", $tourPriceModel);
        $this->assertStringNotContainsString("&& ! blank(\$this->markup_source)", $tourPriceModel);
        $this->assertStringNotContainsString("&& \$this->markup_verified_at !== null", $tourPriceModel);
        $this->assertStringNotContainsString("&& \$this->markup_verified_by !== null", $tourPriceModel);
        $this->assertStringContainsString('createPrice', $pricing);
        $this->assertStringContainsString('updatePrice', $pricing);
        $this->assertStringContainsString('deletePrice', $pricing);
        $this->assertStringNotContainsString('publishedRate', $pricing);
        $this->assertStringNotContainsString('contractRateUsd', $pricing);
        $this->assertStringNotContainsString('taxAmount', $pricing);
        $this->assertStringContainsString('uploadCover', $assets);
        $this->assertStringContainsString('replaceCover', $assets);
        $this->assertStringNotContainsString('uploadGallery', $assets);
        $this->assertStringNotContainsString('replaceGallery', $assets);
        $this->assertStringNotContainsString('deleteGallery', $assets);
        $this->assertStringContainsString('uploadMarker', $assets);
        $this->assertStringContainsString('deleteMarker', $assets);
        $this->assertStringContainsString('validateLocations', $locations);
        $this->assertStringContainsString('resolveCoordinates', $locations);
        $this->assertStringContainsString('searchReferences', $locations);
        $this->assertStringContainsString('isBlankLocation', $locations);
        $this->assertStringContainsString("'_marker_image_file'", $locations);
        $this->assertStringContainsString('$this->assets->uploadMarker($markerImageFile)', $locations);
        $this->assertStringNotContainsString('$markerImage = $this->assets->uploadMarker($markerImageFile);', $locations);
        $this->assertStringContainsString('public function userLog', $audit);
        $this->assertStringContainsString("'user_id' => auth()->id()", $audit);
        $this->assertStringNotContainsString("input('author'", $audit);
        $this->assertStringContainsString('stats', $indexVm);
        $this->assertStringContainsString('rows', $indexVm);
        $this->assertStringContainsString('statusTone', $indexVm);
        $this->assertStringContainsString('stats', $detailVm);
        $this->assertStringContainsString('contentBlocks', $detailVm);
        $this->assertStringContainsString('priceRows', $detailVm);
        $this->assertStringContainsString("'published_rate_idr'", $detailVm);
        $this->assertStringContainsString("'markup_idr'", $detailVm);
        $this->assertStringContainsString("'tax_amount_idr'", $detailVm);
        $this->assertStringContainsString('$tourIndex->stats()', $indexView);
        $this->assertStringContainsString('$tourIndex->rows()', $indexView);
        $this->assertStringContainsString('$tourDetail->stats()', $detailView);
        $this->assertStringContainsString('$tourDetail->contentBlocks()', $detailView);
        $this->assertStringContainsString('$tourDetail->priceRows()', $detailView);
        $this->assertStringNotContainsString('calculated_price', $indexView . $detailView);
        $this->assertStringNotContainsString('ceil(', $indexView . $detailView);
        $this->assertStringNotContainsString('TourPrices::', $profileController . $priceController);
        $this->assertStringContainsString(
            '- [x] Operations Tours memakai namespace/backend UI modern, Form Request, service, dan view model.',
            $roadmap
        );
    }

    public function test_tours_phase_6_final_acceptance_locks_routes_assets_and_legacy_audit(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $mix = file_get_contents(base_path('webpack.mix.js'));
        $manifest = file_get_contents(public_path('mix-manifest.json'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $profileController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Tours/TourAdminController.php'));
        $priceController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Tours/TourPriceAdminController.php'));
        $indexView = file_get_contents(resource_path('views/backend/operations/tours/index.blade.php'));
        $detailView = file_get_contents(resource_path('views/backend/operations/tours/detail.blade.php'));
        $legacyIndexWrapper = file_get_contents(resource_path('views/admin/toursadmin.blade.php'));
        $legacyDetailWrapper = file_get_contents(resource_path('views/admin/toursadmindetail.blade.php'));

        $this->assertStringContainsString('Backend\Operations\Tours\TourAdminController', $routes);
        $this->assertStringContainsString('Backend\Operations\Tours\TourPriceAdminController', $routes);
        $this->assertStringNotContainsString('Backend\Operations\Tours\TourGalleryAdminController', $routes);
        $this->assertStringNotContainsString('ToursAdminController::class', $routes);
        $this->assertStringNotContainsString('ToursImagesController::class', $routes);
        $this->assertStringContainsString("->name('admin.tour-packages.index')", $routes);
        $this->assertStringContainsString("->name('admin.tours.create')", $routes);
        $this->assertStringContainsString("->name('admin.tours.store')", $routes);
        $this->assertStringContainsString("->name('admin.tours.show')", $routes);
        $this->assertStringContainsString("->name('admin.tours.edit')", $routes);
        $this->assertStringContainsString("->name('admin.tours.update')", $routes);
        $this->assertStringContainsString("->name('admin.tours.destroy')", $routes);
        $this->assertStringContainsString("->name('admin.tours.prices.store')", $routes);
        $this->assertStringContainsString("->name('admin.tours.prices.update')", $routes);
        $this->assertStringContainsString("->name('admin.tours.prices.destroy')", $routes);
        $this->assertStringNotContainsString("->name('func.tour-gallery.upload')", $routes);
        $this->assertStringNotContainsString("->name('func.tour-gallery.update')", $routes);
        $this->assertStringNotContainsString("->name('func.tour-gallery.destroy')", $routes);

        $this->assertFileExists(resource_path('views/backend/operations/tours/index.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/tours/detail.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/tours/forms/create.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/tours/forms/edit.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/tours/partials/tour-location-repeater.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/partials/modal-dropzone.blade.php'));
        $this->assertFileDoesNotExist(app_path('Http/Controllers/Backend/Operations/Tours/TourGalleryAdminController.php'));
        $this->assertFileDoesNotExist(resource_path('views/backend/tours/create-tour.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/backend/tours/update-tour.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/backend/tours/partials/tour-location-repeater.blade.php'));
        $this->assertStringContainsString("@include('backend.operations.tours.index')", $legacyIndexWrapper);
        $this->assertStringContainsString("@include('backend.operations.tours.detail')", $legacyDetailWrapper);

        foreach ([
            'resources/backend/js/operations/tours/index.js',
            'resources/backend/js/operations/tours/detail.js',
            'resources/backend/js/operations/tours/forms.js',
            'resources/backend/scss/operations/tours/index-entry.scss',
            'resources/backend/scss/operations/tours/detail-entry.scss',
            'resources/backend/scss/operations/tours/forms-entry.scss',
        ] as $assetPath) {
            $this->assertFileExists(base_path($assetPath));
            $this->assertStringContainsString($assetPath, $mix);
        }

        foreach ([
            '/build/backend/js/operations/tours/index.js',
            '/build/backend/js/operations/tours/detail.js',
            '/build/backend/js/operations/tours/forms.js',
            '/build/backend/css/operations/tours/index.css',
            '/build/backend/css/operations/tours/detail.css',
            '/build/backend/css/operations/tours/forms.css',
        ] as $compiledAsset) {
            $this->assertStringContainsString($compiledAsset, $manifest);
        }

        $legacyUiSurface = $indexView . $detailView
            . file_get_contents(resource_path('views/backend/operations/tours/forms/create.blade.php'))
            . file_get_contents(resource_path('views/backend/operations/tours/forms/edit.blade.php'))
            . file_get_contents(resource_path('views/backend/operations/tours/partials/tour-location-repeater.blade.php'))
            . $legacyIndexWrapper
            . $legacyDetailWrapper;

        foreach ([
            'card-box',
            'btn-view',
            'btn-edit',
            'btn-delete',
            'data-table table',
            'style=',
            'onclick=',
            'onkeyup=',
            '<style>',
            '<script>',
            'class="btn btn-',
            'button class="btn',
            'backend.tours.partials',
        ] as $legacyPattern) {
            $this->assertStringNotContainsString($legacyPattern, $legacyUiSurface);
        }

        $controllerSurface = $profileController . $priceController . $indexView . $detailView;

        foreach ([
            'Cache::remember',
            'ActionLog::',
            'UsdRates::',
            'Tax::',
            'TourType::',
            'Partners::',
            'Storage::disk',
            'TourPrices::',
            'UserLog::',
            'calculatePrice',
            'calculated_price',
            'ceil(',
        ] as $controllerPattern) {
            $this->assertStringNotContainsString($controllerPattern, $controllerSurface);
        }

        foreach ([
            '- [x] Semua CRUD Tour berjalan dari route name final.',
            '- [x] Semua halaman/form Tours lolos `php artisan view:cache`.',
            '- [x] Semua test struktur Tours lolos.',
            '- [x] Semua test validasi/service Tours lolos.',
            '- [x] `npm run development` berhasil menghasilkan asset Tours sesuai `webpack.mix.js`.',
            '- [x] `git diff --check` bersih untuk file Tours dan roadmap.',
            '- [x] Developer sudah menandai checklist roadmap sesuai progres terakhir.',
        ] as $checkedItem) {
            $this->assertStringContainsString($checkedItem, $roadmap);
        }
    }

    public function test_transports_phase_1_route_view_and_asset_foundation_is_registered(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $mix = file_get_contents(base_path('webpack.mix.js'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $controller = file_get_contents(app_path('Http/Controllers/TransportsAdminController.php'));
        $legacyIndexWrapper = file_get_contents(resource_path('views/admin/transportsadmin.blade.php'));
        $legacyDetailWrapper = file_get_contents(resource_path('views/admin/transportsadmindetail.blade.php'));
        $indexView = file_get_contents(resource_path('views/backend/operations/transports/index.blade.php'));
        $detailView = file_get_contents(resource_path('views/backend/operations/transports/detail.blade.php'));
        $createForm = file_get_contents(resource_path('views/backend/operations/transports/forms/create.blade.php'));
        $editForm = file_get_contents(resource_path('views/backend/operations/transports/forms/edit.blade.php'));
        $galleryForm = file_get_contents(resource_path('views/backend/operations/transports/forms/gallery-edit.blade.php'));

        $this->assertFileExists(resource_path('views/backend/operations/transports/index.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/transports/detail.blade.php'));
        $this->assertStringContainsString("@include('backend.operations.transports.index')", $legacyIndexWrapper);
        $this->assertStringContainsString("@include('backend.operations.transports.detail')", $legacyDetailWrapper);
        $this->assertStringContainsString("view('backend.operations.transports.index'", $controller);
        $this->assertStringContainsString("view('backend.operations.transports.detail'", $controller);

        foreach ([
            "->name('admin.transports.index')",
            "->name('admin.transports.create')",
            "->name('admin.transports.store')",
            "->name('admin.transports.show')",
            "->name('admin.transports.edit')",
            "->name('admin.transports.update')",
            "->name('admin.transports.destroy')",
            "->name('admin.transports.gallery.edit')",
            "->name('admin.transports.cover.destroy')",
            "->name('admin.transports.prices.store')",
            "->name('admin.transports.prices.update')",
            "->name('admin.transports.prices.destroy')",
        ] as $routeName) {
            $this->assertStringContainsString($routeName, $routes);
        }

        foreach ([
            "route('admin.transports.show'",
            "route('admin.transports.edit'",
            "route('admin.transports.destroy'",
            "route('admin.transports.create'",
            "route('admin.transports.index'",
            "route('admin.transports.prices.store'",
            "route('admin.transports.prices.update'",
            "route('admin.transports.prices.destroy'",
        ] as $routeHelper) {
            $this->assertStringContainsString($routeHelper, $indexView . $detailView . $createForm . $editForm . $galleryForm);
        }

        foreach ([
            '/add-transport',
            '/edit-transport-',
            '/edit-galery-transport-',
            '/fadd-transport',
            '/fadd-transport-price',
            '/fupdate-transport',
            '/fupdate-transport-price',
            '/delete-transport',
            '/fdelete-transport-price',
            '/fdelete-transport-cover',
            '/detail-transport-',
            '/transports-admin',
        ] as $legacyUrl) {
            $this->assertStringNotContainsString($legacyUrl, $indexView . $detailView . $createForm . $editForm . $galleryForm);
        }

        foreach ([
            'resources/backend/js/operations/transports/index.js',
            'resources/backend/js/operations/transports/detail.js',
            'resources/backend/js/operations/transports/forms.js',
            'resources/backend/scss/operations/transports/index-entry.scss',
            'resources/backend/scss/operations/transports/detail-entry.scss',
            'resources/backend/scss/operations/transports/forms-entry.scss',
        ] as $assetPath) {
            $this->assertFileExists(base_path($assetPath));
            $this->assertStringContainsString($assetPath, $mix);
        }

        foreach ([
            '- [x] Audit batas domain Transports master data dan pisahkan dari Transport Management/SPK.',
            '- [x] Pindahkan source view index/detail Transports ke `resources/views/backend/operations/transports`.',
            '- [x] Beri route name final untuk Transports profile, price, gallery, dan cover.',
            '- [x] Tambahkan guard test struktur Phase 1 agar fondasi route/view/asset tidak regress.',
        ] as $checkedItem) {
            $this->assertStringContainsString($checkedItem, $roadmap);
        }
    }

    public function test_transports_phase_2_index_and_detail_use_shared_backend_ui(): void
    {
        $indexView = file_get_contents(resource_path('views/backend/operations/transports/index.blade.php'));
        $detailView = file_get_contents(resource_path('views/backend/operations/transports/detail.blade.php'));
        $priceFields = file_get_contents(resource_path('views/backend/operations/transports/partials/price-fields.blade.php'));
        $indexJs = file_get_contents(resource_path('backend/js/operations/transports/index.js'));
        $detailJs = file_get_contents(resource_path('backend/js/operations/transports/detail.js'));
        $detailScss = file_get_contents(resource_path('backend/scss/operations/transports/_detail.scss'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $surface = $indexView . $detailView;

        foreach ([
            'backend-page-toolbar',
            'backend-feedback',
            'backend-kpi-grid',
            'backend-filter-panel',
            'backend-panel',
            'backend-section-header',
            'backend-table',
            'backend-status-badge',
            'backend-table-empty',
            'backend-icon-action',
            'backend-primary-action',
            'backend-secondary-action',
        ] as $sharedClass) {
            $this->assertStringContainsString($sharedClass, $surface);
        }

        foreach ([
            'backend-modal',
            'transport-detail-modal',
            '<x-backend.detail-layout class="transport-detail-layout">',
            'backend-detail-side-card transport-detail-context-panel',
            'backend-detail-side-list',
            'backend-detail-side-actions',
            'transport-detail-info-card',
            'Profile Summary',
            'transport-detail-richtext',
            'backend-form-grid',
            "backend.operations.transports.partials.price-fields",
        ] as $detailClass) {
            $this->assertStringContainsString($detailClass, $detailView);
        }

        foreach ([
            'card-box',
            'btn-view',
            'btn-edit',
            'btn-delete',
            'data-table table',
            'style=',
            'onclick=',
            'onkeyup=',
            '<style>',
            '<script>',
            'class="btn btn-',
            'button class="btn',
            'searchTransportByName',
            'searchTransportByType',
            'searchPriceByType',
            'searchPriceByDuration',
        ] as $legacyPattern) {
            $this->assertStringNotContainsString($legacyPattern, $surface);
        }

        $this->assertStringContainsString('data-transport-filter="name"', $indexView);
        $this->assertStringContainsString('data-transport-filter="type"', $indexView);
        $this->assertStringContainsString('data-transport-row', $indexView);
        $this->assertStringContainsString('data-transport-delete', $indexView);
        $this->assertStringContainsString('data-transport-price-filter="type"', $detailView);
        $this->assertStringContainsString('data-transport-price-filter="duration"', $detailView);
        $this->assertStringContainsString('data-transport-price-row', $detailView);
        $this->assertStringContainsString('data-transport-price-delete', $detailView);
        $this->assertStringContainsString('data-transport-filter', $indexJs);
        $this->assertStringContainsString('data-transport-delete', $indexJs);
        $this->assertStringContainsString('data-transport-price-filter', $detailJs);
        $this->assertStringContainsString('data-transport-price-delete', $detailJs);
        $this->assertStringContainsString('backend-form-control', $priceFields);
        $this->assertStringContainsString('decoding="async"', $detailView);
        $this->assertStringContainsString('width="360"', $detailView);
        $this->assertStringContainsString('grid-template-columns: minmax(220px, 320px) minmax(0, 1fr);', $detailScss);
        $this->assertStringContainsString('max-height: 240px;', $detailScss);
        $this->assertStringContainsString('.transport-detail-info-card .backend-table-card__header strong', $detailScss);
        $this->assertStringContainsString("route('admin.transports.prices.store')", $detailView);
        $this->assertStringContainsString("route('admin.transports.prices.update'", $detailView);
        $this->assertStringContainsString("route('admin.transports.prices.destroy'", $detailView);

        foreach ([
            '- [x] Refactor index Transports agar memakai shared hero, toolbar, feedback, KPI, panel, table, status badge, empty state, dan button standard.',
            '- [x] Refactor detail Transports agar memakai shared hero, toolbar, feedback, KPI/summary, panel, section header, table, status badge, modal, dan button standard.',
            '- [x] Hilangkan `card-box`, `btn-view`, `btn-edit`, `btn-delete`, inline `style`, inline `onclick`, inline `onkeyup`, dan `<script>` legacy dari index/detail.',
            '- [x] Pindahkan behavior search/delete confirmation index/detail ke JS domain Transports.',
            '- [x] Pastikan route/action detail pricing tetap memakai route name final.',
        ] as $checkedItem) {
            $this->assertStringContainsString($checkedItem, $roadmap);
        }
    }

    public function test_transports_phase_3_forms_and_gallery_use_shared_backend_ui_and_assets(): void
    {
        $createForm = file_get_contents(resource_path('views/backend/operations/transports/forms/create.blade.php'));
        $editForm = file_get_contents(resource_path('views/backend/operations/transports/forms/edit.blade.php'));
        $galleryForm = file_get_contents(resource_path('views/backend/operations/transports/forms/gallery-edit.blade.php'));
        $profileFields = file_get_contents(resource_path('views/backend/operations/transports/partials/profile-fields.blade.php'));
        $hiddenFields = file_get_contents(resource_path('views/backend/operations/transports/partials/profile-hidden-fields.blade.php'));
        $feedback = file_get_contents(resource_path('views/backend/operations/transports/partials/form-feedback.blade.php'));
        $formsJs = file_get_contents(resource_path('backend/js/operations/transports/forms.js'));
        $formsScss = file_get_contents(resource_path('backend/scss/operations/transports/_forms.scss'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $adminController = file_get_contents(app_path('Http/Controllers/TransportsAdminController.php'));
        $publicController = file_get_contents(app_path('Http/Controllers/TransportsController.php'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $surface = $createForm . $editForm . $galleryForm . $profileFields . $hiddenFields . $feedback;

        foreach ([
            'backend-page-toolbar',
            'backend-feedback',
            'backend-panel',
            'backend-section-header',
            'backend-form-field',
            'backend-form-control',
            'backend-button backend-button-primary',
            'backend-button backend-button-secondary',
            'transport-cover-preview',
            'data-transport-form',
        ] as $sharedPattern) {
            $this->assertStringContainsString($sharedPattern, $surface);
        }

        foreach ([
            'transport-gallery-page',
            'transport-gallery-grid',
            'data-transport-gallery-input',
            'data-transport-gallery-preview',
            'data-transport-gallery-delete',
            "route('admin.transports.images.destroy'",
        ] as $galleryPattern) {
            $this->assertStringContainsString($galleryPattern, $galleryForm);
        }

        foreach ([
            'card-box',
            'btn btn-',
            'btn-primary',
            'btn-danger',
            'btn-info',
            'custom-file-input',
            'custom-select',
            'class="form-control',
            'class="textarea_editor form-control',
            'style=',
            'onclick=',
            'onchange=',
            '<style>',
            '<script>',
            '/fdelete-transport-img',
            '/detail-transport-',
        ] as $legacyPattern) {
            $this->assertStringNotContainsString($legacyPattern, $createForm . $editForm . $galleryForm);
        }

        foreach ([
            "route('admin.transports.store')",
            "route('admin.transports.update'",
            "route('admin.transports.show'",
            "route('admin.transports.index')",
        ] as $routeHelper) {
            $this->assertStringContainsString($routeHelper, $createForm . $editForm . $galleryForm);
        }

        $this->assertStringContainsString("->name('admin.transports.images.destroy')", $routes);
        $this->assertStringContainsString('TransportsImages::create', $adminController);
        $this->assertStringContainsString('storage/transports/transports-gallery/', $adminController);
        $this->assertStringContainsString('storage/transports/transports-gallery/', $publicController);
        $this->assertStringContainsString('data-transport-cover-input', $formsJs);
        $this->assertStringContainsString('data-transport-gallery-input', $formsJs);
        $this->assertStringContainsString('data-transport-gallery-delete', $formsJs);
        $this->assertStringContainsString('transport-gallery-preview__item', $formsJs);
        $this->assertStringNotContainsString('.transport-form-grid', $formsScss);
        $this->assertStringContainsString('.transport-gallery-grid', $formsScss);

        foreach ([
            '- [x] Standardisasi create, edit, dan gallery-edit agar memakai shared hero, toolbar, feedback, panel, section header, form label, file input, dan button standard.',
            '- [x] Hilangkan `card-box`, button Bootstrap langsung, inline style/script, dan struktur form lama.',
            '- [x] Pindahkan behavior preview/upload/gallery interaction ke `resources/backend/js/operations/transports/forms.js`.',
            '- [x] Pastikan semua form action memakai route name final.',
        ] as $checkedItem) {
            $this->assertStringContainsString($checkedItem, $roadmap);
        }
    }

    public function test_transports_phase_4_controller_decomposition_and_form_requests_are_registered(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $profileController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Transports/TransportAdminController.php'));
        $priceController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Transports/TransportPriceAdminController.php'));
        $galleryController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Transports/TransportGalleryAdminController.php'));
        $storeRequest = file_get_contents(app_path('Http/Requests/Backend/Operations/Transports/StoreTransportAdminRequest.php'));
        $updateRequest = file_get_contents(app_path('Http/Requests/Backend/Operations/Transports/UpdateTransportAdminRequest.php'));
        $storePriceRequest = file_get_contents(app_path('Http/Requests/Backend/Operations/Transports/StoreTransportPriceAdminRequest.php'));
        $updatePriceRequest = file_get_contents(app_path('Http/Requests/Backend/Operations/Transports/UpdateTransportPriceAdminRequest.php'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));

        $this->assertFileExists(app_path('Http/Controllers/Backend/Operations/Transports/TransportAdminController.php'));
        $this->assertFileExists(app_path('Http/Controllers/Backend/Operations/Transports/TransportPriceAdminController.php'));
        $this->assertFileExists(app_path('Http/Controllers/Backend/Operations/Transports/TransportGalleryAdminController.php'));
        $this->assertFileExists(app_path('Http/Requests/Backend/Operations/Transports/StoreTransportAdminRequest.php'));
        $this->assertFileExists(app_path('Http/Requests/Backend/Operations/Transports/UpdateTransportAdminRequest.php'));
        $this->assertFileExists(app_path('Http/Requests/Backend/Operations/Transports/StoreTransportPriceAdminRequest.php'));
        $this->assertFileExists(app_path('Http/Requests/Backend/Operations/Transports/UpdateTransportPriceAdminRequest.php'));

        foreach ([
            'Backend\Operations\Transports\TransportAdminController',
            'Backend\Operations\Transports\TransportPriceAdminController',
            'Backend\Operations\Transports\TransportGalleryAdminController',
        ] as $controllerImport) {
            $this->assertStringContainsString($controllerImport, $routes);
        }

        foreach ([
            '[TransportAdminController::class,\'index\']',
            '[TransportAdminController::class,\'show\']',
            '[TransportAdminController::class,\'create\']',
            '[TransportAdminController::class,\'edit\']',
            '[TransportAdminController::class,\'store\']',
            '[TransportAdminController::class,\'update\']',
            '[TransportAdminController::class,\'destroy\']',
            '[TransportPriceAdminController::class,\'store\']',
            '[TransportPriceAdminController::class,\'update\']',
            '[TransportPriceAdminController::class,\'destroy\']',
            '[TransportGalleryAdminController::class,\'edit\']',
            '[TransportGalleryAdminController::class,\'destroyCover\']',
            '[TransportGalleryAdminController::class,\'destroyImage\']',
        ] as $routeTarget) {
            $this->assertStringContainsString($routeTarget, $routes);
        }

        foreach ([
            'TransportsAdminController::class',
            'TransportsAdminController::class,\'view_add_transport\'',
            'TransportsAdminController::class,\'view_edit_transport\'',
            'TransportsAdminController::class,\'view_detail_transport\'',
            'TransportsAdminController::class,\'func_add_transport\'',
            'TransportsAdminController::class,\'func_add_transport_price\'',
            'TransportsAdminController::class,\'func_update_transport\'',
            'TransportsAdminController::class,\'func_update_transport_price\'',
            'TransportsAdminController::class,\'remove_transport\'',
            'TransportsAdminController::class,\'remove_transport_price\'',
            'TransportsController::class,\'view_edit_galery_transport\'',
            'TransportsController::class,\'delete_cover_transport\'',
            'TransportsController::class,\'delete_image_transport\'',
        ] as $legacyRouteTarget) {
            $this->assertStringNotContainsString($legacyRouteTarget, $routes);
        }

        $this->assertStringContainsString('StoreTransportAdminRequest $request', $profileController);
        $this->assertStringContainsString('UpdateTransportAdminRequest $request', $profileController);
        $this->assertStringContainsString('StoreTransportPriceAdminRequest $request', $priceController);
        $this->assertStringContainsString('UpdateTransportPriceAdminRequest $request', $priceController);
        $this->assertStringContainsString('public function edit($id', $galleryController);
        $this->assertStringContainsString('public function destroyCover($id', $galleryController);
        $this->assertStringContainsString('public function destroyImage($id', $galleryController);
        $this->assertStringContainsString("'cover' => 'required|image", $storeRequest);
        $this->assertStringContainsString("'cover' => 'nullable|image", $updateRequest);
        $this->assertStringContainsString("'images' => 'nullable|array'", $updateRequest);
        $this->assertStringContainsString("'transports_id' => 'required|integer|exists:transports,id'", $storePriceRequest);
        $this->assertStringContainsString('class UpdateTransportPriceAdminRequest extends StoreTransportPriceAdminRequest', $updatePriceRequest);

        foreach ([
            'func_add_transport(',
            'func_update_transport(',
            'func_add_transport_price(',
            'func_update_transport_price(',
            'remove_transport(',
            'remove_transport_price(',
            'view_edit_galery_transport(',
            'delete_cover_transport(',
            'delete_image_transport(',
        ] as $legacyMethod) {
            $this->assertStringNotContainsString($legacyMethod, $profileController . $priceController . $galleryController);
        }

        foreach ([
            '- [x] Buat namespace controller baru `App\Http\Controllers\Backend\Operations\Transports`.',
            '- [x] Pisahkan profile CRUD ke `TransportAdminController`.',
            '- [x] Pisahkan price CRUD ke `TransportPriceAdminController`.',
            '- [x] Pisahkan gallery/cover lifecycle ke `TransportGalleryAdminController`.',
            '- [x] Buat Form Request untuk create/update Transport dan create/update Transport Price.',
            '- [x] Update route agar memakai controller baru tanpa memutus URL legacy.',
        ] as $checkedItem) {
            $this->assertStringContainsString($checkedItem, $roadmap);
        }
    }

    public function test_transports_phase_5_services_and_view_models_own_backend_data_preparation(): void
    {
        $profileController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Transports/TransportAdminController.php'));
        $priceController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Transports/TransportPriceAdminController.php'));
        $galleryController = file_get_contents(app_path('Http/Controllers/Backend/Operations/Transports/TransportGalleryAdminController.php'));
        $inventoryService = file_get_contents(app_path('Services/Transports/TransportInventoryService.php'));
        $pricingService = file_get_contents(app_path('Services/Transports/TransportPricingService.php'));
        $assetService = file_get_contents(app_path('Services/Transports/TransportAssetService.php'));
        $auditService = file_get_contents(app_path('Services/Transports/TransportAuditService.php'));
        $indexViewModel = file_get_contents(app_path('ViewModels/Transports/TransportIndexViewModel.php'));
        $detailViewModel = file_get_contents(app_path('ViewModels/Transports/TransportDetailViewModel.php'));
        $indexView = file_get_contents(resource_path('views/backend/operations/transports/index.blade.php'));
        $detailView = file_get_contents(resource_path('views/backend/operations/transports/detail.blade.php'));
        $transportPriceModel = file_get_contents(app_path('Models/TransportPrice.php'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $controllers = $profileController . $priceController . $galleryController;

        foreach ([
            app_path('Services/Transports/TransportInventoryService.php'),
            app_path('Services/Transports/TransportPricingService.php'),
            app_path('Services/Transports/TransportAssetService.php'),
            app_path('Services/Transports/TransportAuditService.php'),
            app_path('ViewModels/Transports/TransportIndexViewModel.php'),
            app_path('ViewModels/Transports/TransportDetailViewModel.php'),
        ] as $filePath) {
            $this->assertFileExists($filePath);
        }

        foreach ([
            'TransportInventoryService $inventory',
            '$inventory->indexData()',
            '$inventory->formOptions()',
            '$inventory->editData((int) $id)',
            '$inventory->detailData((int) $id)',
            'TransportAssetService $assets',
            '$assets->uploadCover',
            '$assets->replaceCover',
            '$assets->uploadGallery',
            'TransportAuditService $audit',
            '$audit->userLog',
        ] as $profilePattern) {
            $this->assertStringContainsString($profilePattern, $profileController);
        }

        foreach ([
            'TransportPricingService $pricing',
            '$pricing->createPrice',
            '$pricing->updatePrice',
            '$pricing->deletePrice',
            'TransportAuditService $audit',
        ] as $pricePattern) {
            $this->assertStringContainsString($pricePattern, $priceController);
        }

        foreach ([
            'TransportAssetService $assets',
            'TransportInventoryService $inventory',
            '$inventory->galleryData((int) $id)',
            '$assets->deleteCover',
            '$assets->deleteGallery',
        ] as $galleryPattern) {
            $this->assertStringContainsString($galleryPattern, $galleryController);
        }

        foreach ([
            'UsdRates::',
            'Tax::',
            'BusinessProfile::',
            'TransportType::',
            'TransportBrand::',
            'TransportPrice::',
            'UserLog::',
            'File::',
            'TransportsImages::create',
            'private function uploadCover',
            'private function userLog',
        ] as $legacyControllerPattern) {
            $this->assertStringNotContainsString($legacyControllerPattern, $controllers);
        }

        foreach ([
            'TransportIndexViewModel',
            'TransportDetailViewModel',
            'public function indexData()',
            'public function detailData(int $transportId)',
            'public function formOptions()',
            'public function editData(int $transportId)',
            'public function galleryData(int $transportId)',
        ] as $inventoryPattern) {
            $this->assertStringContainsString($inventoryPattern, $inventoryService);
        }

        foreach ([
            'public function createPrice',
            'public function updatePrice',
            'public function deletePrice',
            'public function contractRateUsd',
            'public function taxAmount',
            'public function publishedRate',
        ] as $pricingPattern) {
            $this->assertStringContainsString($pricingPattern, $pricingService);
        }

        foreach ([
            'public function uploadCover',
            'public function replaceCover',
            'public function deleteCover',
            'public function uploadGallery',
            'public function deleteGallery',
        ] as $assetPattern) {
            $this->assertStringContainsString($assetPattern, $assetService);
        }

        $this->assertStringContainsString('public function userLog', $auditService);
        $this->assertStringContainsString('public function stats()', $indexViewModel);
        $this->assertStringContainsString('public function rows()', $indexViewModel);
        $this->assertStringContainsString('public function archivedRows()', $indexViewModel);
        $this->assertStringContainsString('public function stats()', $detailViewModel);
        $this->assertStringContainsString('public function contentBlocks()', $detailViewModel);
        $this->assertStringContainsString('public function priceRows()', $detailViewModel);
        $this->assertStringContainsString("'name'", $transportPriceModel);
        $this->assertStringContainsString('$transportIndex->stats()', $indexView);
        $this->assertStringContainsString('$transportIndex->rows()', $indexView);
        $this->assertStringContainsString('$transportIndex->archivedRows()', $indexView);
        $this->assertStringContainsString('$transportDetail->stats()', $detailView);
        $this->assertStringContainsString('$transportDetail->contentBlocks()', $detailView);
        $this->assertStringContainsString('$transportDetail->priceRows()', $detailView);

        foreach ([
            'ceil(',
            'collect($prices',
            '$priceRows',
        ] as $legacyBladePattern) {
            $this->assertStringNotContainsString($legacyBladePattern, $detailView);
        }

        foreach ([
            '- [x] Buat `TransportInventoryService` untuk index/detail summary dan form options.',
            '- [x] Buat `TransportPricingService` untuk kalkulasi contract rate, markup, tax, published rate, dan price CRUD.',
            '- [x] Buat `TransportAssetService` untuk cover/gallery lifecycle.',
            '- [x] Buat `TransportAuditService` untuk UserLog agar controller tidak membuat log manual.',
            '- [x] Buat `TransportIndexViewModel` dan `TransportDetailViewModel`.',
            '- [x] Kurangi query/kalkulasi berulang di Blade dan controller.',
        ] as $checkedItem) {
            $this->assertStringContainsString($checkedItem, $roadmap);
        }
    }

    public function test_transports_phase_6_final_acceptance_is_locked(): void
    {
        $routes = file_get_contents(base_path('routes/web.php'));
        $webpackMix = file_get_contents(base_path('webpack.mix.js'));
        $mixManifest = file_get_contents(public_path('mix-manifest.json'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $backendTransportsViews = [
            resource_path('views/backend/operations/transports/index.blade.php'),
            resource_path('views/backend/operations/transports/detail.blade.php'),
            resource_path('views/backend/operations/transports/forms/create.blade.php'),
            resource_path('views/backend/operations/transports/forms/edit.blade.php'),
            resource_path('views/backend/operations/transports/forms/gallery-edit.blade.php'),
            resource_path('views/backend/operations/transports/partials/profile-fields.blade.php'),
            resource_path('views/backend/operations/transports/partials/price-fields.blade.php'),
        ];
        $surface = '';

        foreach ($backendTransportsViews as $viewPath) {
            $this->assertFileExists($viewPath);
            $surface .= file_get_contents($viewPath);
        }

        foreach ([
            "->name('admin.transports.index')",
            "->name('admin.transports.create')",
            "->name('admin.transports.store')",
            "->name('admin.transports.show')",
            "->name('admin.transports.edit')",
            "->name('admin.transports.update')",
            "->name('admin.transports.destroy')",
            "->name('admin.transports.gallery.edit')",
            "->name('admin.transports.cover.destroy')",
            "->name('admin.transports.images.destroy')",
            "->name('admin.transports.prices.store')",
            "->name('admin.transports.prices.update')",
            "->name('admin.transports.prices.destroy')",
        ] as $routeName) {
            $this->assertStringContainsString($routeName, $routes);
        }

        foreach ([
            "route('admin.transports.create')",
            "route('admin.transports.store')",
            "route('admin.transports.show'",
            "route('admin.transports.edit'",
            "route('admin.transports.update'",
            "route('admin.transports.destroy'",
            "route('admin.transports.images.destroy'",
            "route('admin.transports.prices.store')",
            "route('admin.transports.prices.update'",
            "route('admin.transports.prices.destroy'",
            "route('admin.transports.index')",
        ] as $routeHelper) {
            $this->assertStringContainsString($routeHelper, $surface);
        }

        foreach ([
            '/fadd-transport',
            '/fadd-transport-price',
            '/fupdate-transport',
            '/fupdate-transport-price',
            '/delete-transport',
            '/fdelete-transport-price',
            '/fdelete-transport-cover',
            '/fdelete-transport-img',
            '/detail-transport-',
            '/edit-transport-',
            '/edit-galery-transport-',
        ] as $legacyUrl) {
            $this->assertStringNotContainsString($legacyUrl, $surface);
        }

        foreach ([
            '<x-backend.page-hero',
            'backend-page-toolbar',
            'backend-feedback',
            'backend-kpi-grid',
            'backend-panel',
            'backend-section-header',
            'backend-table',
            'backend-status-badge',
            'backend-empty-state',
            'backend-primary-action',
            'backend-secondary-action',
            'backend-icon-action',
            'backend-modal',
            'backend-form-field',
            'backend-form-control',
        ] as $sharedPattern) {
            $this->assertStringContainsString($sharedPattern, $surface);
        }

        foreach ([
            'card-box',
            'btn-view',
            'btn-edit',
            'btn-delete',
            'btn btn-',
            'btn-primary',
            'btn-danger',
            'btn-info',
            'style=',
            'onclick=',
            'onkeyup=',
            'onchange=',
            '<style>',
            '<script>',
            'data-table table',
            'custom-file-input',
            'custom-select',
            'class="form-control',
            'class="textarea_editor form-control',
        ] as $legacyPattern) {
            $this->assertStringNotContainsString($legacyPattern, $surface);
        }

        foreach ([
            "resources/backend/js/operations/transports/index.js",
            "resources/backend/js/operations/transports/detail.js",
            "resources/backend/js/operations/transports/forms.js",
            "resources/backend/scss/operations/transports/index-entry.scss",
            "resources/backend/scss/operations/transports/detail-entry.scss",
            "resources/backend/scss/operations/transports/forms-entry.scss",
        ] as $mixSource) {
            $this->assertStringContainsString($mixSource, $webpackMix);
        }

        foreach ([
            '/build/backend/js/operations/transports/index.js',
            '/build/backend/js/operations/transports/detail.js',
            '/build/backend/js/operations/transports/forms.js',
            '/build/backend/css/operations/transports/index.css',
            '/build/backend/css/operations/transports/detail.css',
            '/build/backend/css/operations/transports/forms.css',
        ] as $assetPath) {
            $this->assertFileExists(public_path(ltrim($assetPath, '/')));
            $this->assertStringContainsString($assetPath, $mixManifest);
        }

        foreach ([
            '- [x] Semua CRUD Transports berjalan dari route name final.',
            '- [x] Semua halaman/form Transports lolos `php artisan view:cache`.',
            '- [x] Semua test struktur Transports lolos.',
            '- [x] Semua test validasi/service Transports lolos.',
            '- [x] `npm run development` berhasil menghasilkan asset Transports sesuai `webpack.mix.js`.',
            '- [x] `git diff --check` bersih untuk file Transports dan roadmap.',
            '- [x] Developer sudah menandai checklist roadmap sesuai progres terakhir.',
        ] as $checkedItem) {
            $this->assertStringContainsString($checkedItem, $roadmap);
        }
    }

    public function test_backend_form_phase_5_global_acceptance_has_no_legacy_form_or_button_patterns(): void
    {
        $viewRoots = [
            resource_path('views/admin'),
            resource_path('views/backend'),
        ];

        $partialViews = [
            resource_path('views/partials/admin-order-note-sidebar.blade.php'),
            resource_path('views/partials/admin-order-status-sidebar.blade.php'),
            resource_path('views/partials/admin-order-receipt-report-sidebar.blade.php'),
            resource_path('views/partials/modal-add-payment-receipt.blade.php'),
            resource_path('views/partials/modal-detail-spk.blade.php'),
            resource_path('views/partials/modal-tour-package-admin.blade.php'),
        ];

        $viewFiles = [];

        foreach ($viewRoots as $root) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));

            foreach ($iterator as $file) {
                if (! $file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $pathname = $file->getPathname();

                if (! str_ends_with($pathname, '.blade.php')) {
                    continue;
                }

                $viewFiles[] = $pathname;
            }
        }

        foreach ($partialViews as $partialView) {
            $this->assertFileExists($partialView);
            $viewFiles[] = $partialView;
        }

        $legacyPattern = '/(?<!backend-)form-group(?!-icon)|(?<!backend-)form-control|custom-select|custom-file-input|btn btn|(?<!backend-button )btn-primary|(?<!backend-button )btn-success|(?<!backend-button )btn-secondary|(?<!backend-button )btn-danger|backend-primary-action|backend-secondary-action|backend-danger-action/';

        foreach (array_unique($viewFiles) as $viewFile) {
            $contents = file_get_contents($viewFile);

            $this->assertDoesNotMatchRegularExpression($legacyPattern, $contents, $viewFile);
            $this->assertDoesNotMatchRegularExpression('/<[^>]+class="[^"]+"[^>]+class="/', $contents, $viewFile);
            $this->assertDoesNotMatchRegularExpression('/<input[^>]*type="hidden"[^>]*class="backend-form-control"/', $contents, $viewFile);
            $this->assertDoesNotMatchRegularExpression('/<input[^>]*type="checkbox"[^>]*class="backend-form-control"/', $contents, $viewFile);
        }

        $scssRoots = [
            resource_path('backend/scss/admin'),
            resource_path('backend/scss/operations'),
        ];

        $scssLegacyPattern = '/(?<!backend-)form-group(?!-icon)|(?<!backend-)form-control|custom-select|custom-file-input|btn-primary|btn-success|btn-secondary|btn-danger|input\s*,|select\s*,|textarea\s*\{|input:focus|select:focus|textarea:focus/';

        foreach ($scssRoots as $root) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));

            foreach ($iterator as $file) {
                if (! $file->isFile() || $file->getExtension() !== 'scss') {
                    continue;
                }

                $contents = file_get_contents($file->getPathname());

                $this->assertDoesNotMatchRegularExpression($scssLegacyPattern, $contents, $file->getPathname());
            }
        }

        $roadmap = file_get_contents(base_path('docs/decisions/backend-form-standardization-roadmap.md'));

        foreach ([
            '- [x] Audit global `resources/views/admin`, `resources/views/backend`, dan partial backend/admin tidak menemukan `form-control`, `custom-select`, `custom-file-input`, `btn btn-*`, atau action button legacy.',
            '- [x] Audit SCSS domain/admin tidak menemukan form control/button base style di domain page yang menggantikan shared style.',
        ] as $checkedItem) {
            $this->assertStringContainsString($checkedItem, $roadmap);
        }
    }

    public function test_backend_legacy_ui_phase_1_create_hotel_order_uses_shared_ui_and_domain_asset(): void
    {
        $view = file_get_contents(resource_path('views/admin/create-hotel-order.blade.php'));
        $script = file_get_contents(resource_path('backend/js/operations/orders-admin/create-hotel-order.js'));
        $webpackMix = file_get_contents(base_path('webpack.mix.js'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-legacy-ui-deep-cleanup-roadmap.md'));
        $uiRoadmap = file_get_contents(base_path('docs/decisions/backend-ui-standardization-roadmap.md'));
        $sharedFormScss = file_get_contents(resource_path('backend/scss/components/_backend-form.scss'));

        $this->assertStringContainsString("mix('build/backend/css/app.css')", $view);
        $this->assertStringContainsString('data-create-hotel-order-page', $view);
        $this->assertStringContainsString('id="hotelOrderRoomTemplate"', $view);
        $this->assertStringContainsString("mix('build/backend/js/operations/orders-admin/create-hotel-order.js')", $view);
        $this->assertStringContainsString('.js(\'resources/backend/js/operations/orders-admin/create-hotel-order.js\'', $webpackMix);
        $this->assertStringContainsString('data-hotel-order-remove', $script);
        $this->assertStringContainsString('data-hotel-order-guest-count', $script);
        $this->assertStringContainsString('window.fRequest', $script);
        $this->assertStringContainsString('.backend-help-icon', $sharedFormScss);
        $this->assertStringContainsString('.backend-inline-amount', $sharedFormScss);

        foreach ([
            'card-box',
            'style=',
            'onchange=',
            '<script type="text/javascript"',
            '$(document).ready',
            '$("#dynamic_field").append',
            'btn-remove',
        ] as $legacyPattern) {
            $this->assertStringNotContainsString($legacyPattern, $view);
        }

        foreach ([
            '- [x] Audit `resources/views/admin/create-hotel-order.blade.php`.',
            '- [x] Pindahkan behavior add/remove room dari inline `<script>` ke `resources/backend/js/operations/orders-admin/create-hotel-order.js`.',
            '- [x] Daftarkan asset create-hotel-order di `webpack.mix.js`.',
            '- [x] Ganti inline `onchange` room guest dengan data attribute.',
            '- [x] Ganti card utama ke `backend-panel`.',
            '- [x] Kurangi inline style utama dengan shared utility class.',
        ] as $checkedItem) {
            $this->assertStringContainsString($checkedItem, $roadmap);
        }

        $this->assertStringContainsString('Backend Legacy UI Deep Cleanup dimulai', $uiRoadmap);
    }

    public function test_backend_legacy_ui_phase_2_reservation_detail_is_replaced_by_canonical_domain_view(): void
    {
        $view = file_get_contents(resource_path('views/backend/operations/reservations/detail.blade.php'));
        $context = file_get_contents(resource_path('views/backend/operations/reservations/partials/context.blade.php'));
        $script = file_get_contents(resource_path('backend/js/operations/reservations/detail.js'));
        $webpackMix = file_get_contents(base_path('webpack.mix.js'));
        $roadmap = file_get_contents(base_path('docs/decisions/backend-legacy-ui-deep-cleanup-roadmap.md'));

        $this->assertStringContainsString("mix('build/backend/js/operations/reservations/detail.js')", $view);
        $this->assertStringContainsString(".js('resources/backend/js/operations/reservations/detail.js'", $webpackMix);
        $this->assertFileDoesNotExist(resource_path('views/admin/reservation_detail.blade.php'));
        $this->assertStringContainsString('<x-backend.detail-layout', $view);
        $this->assertStringContainsString('data-confirm-delete', $context);
        $this->assertStringContainsString('data-confirm-delete', $script);
        $this->assertStringContainsString('window.confirm', $script);

        foreach ([
            'onclick=',
            'onkeyup=',
            'onchange=',
            '<script type="text/javascript"',
            'document.write(htl())',
            'class="btn-delete"',
        ] as $legacyPattern) {
            $this->assertStringNotContainsString($legacyPattern, $view);
        }

        foreach ([
            '- [x] Audit section dan modal terbesar di `resources/views/admin/reservation_detail.blade.php`.',
            '- [x] Petakan section besar: Reservation, Flight, Agent, Guest, Guide, Driver, Accommodation, Activity/Tour, Transport, Restaurant, Include, Exclude, Remark, Sidebar Attention/Notes.',
            '- [x] Pindahkan confirm/delete behavior ke asset backend domain.',
            '- [x] Pindahkan section detail ke partial domain responsive dan retire Blade legacy.',
            '- [x] Pindahkan section navigation/print behavior ke asset backend domain.',
        ] as $roadmapItem) {
            $this->assertStringContainsString($roadmapItem, $roadmap);
        }
    }
}
