@extends('layouts.head')

@section('title', 'Footer Manager')

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/admin/footer-manager/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/admin/footer-manager/index.js') }}" defer></script>
@endpush

@section('content')
    @can('posDev')
        @php
            $settingGroups = [
                'brand' => [
                    'title' => 'Brand & Trust',
                    'description' => 'Logo accessibility text and trust badge wording shown on the left side of the footer.',
                    'icon' => 'fa fa-certificate',
                    'keys' => ['logo_aria', 'highlights_aria', 'highlight_worldwide_agents', 'highlight_indonesia_supply', 'highlight_global_access'],
                ],
                'contact_newsletter' => [
                    'title' => 'Contact & Newsletter',
                    'description' => 'Labels and copy for contact, newsletter subscription, and social media blocks.',
                    'icon' => 'fa fa-envelope-open',
                    'keys' => ['contact_title', 'newsletter_title', 'newsletter_copy', 'newsletter_placeholder', 'newsletter_button', 'social_title'],
                ],
                'navigation' => [
                    'title' => 'Navigation Titles',
                    'description' => 'Section titles and aria labels for Services, Quick Links, and Policies.',
                    'icon' => 'fa fa-list',
                    'keys' => ['services_title', 'services_aria', 'quick_links_title', 'quick_links_aria', 'policies_title', 'policies_aria'],
                ],
                'platform' => [
                    'title' => 'Platform & Copyright',
                    'description' => 'Footer platform statement and copyright suffix.',
                    'icon' => 'fa fa-copyright',
                    'keys' => ['platform_title', 'platform_copy', 'copyright_suffix'],
                ],
            ];

            $settingsByKey = $settings->keyBy('key');
            $assignedSettingKeys = collect($settingGroups)->flatMap(fn ($group) => $group['keys']);
            $uncategorizedSettings = $settings->reject(fn ($setting) => $assignedSettingKeys->contains($setting->key));
            $groupLabels = [
                'services' => 'Services',
                'quick_links' => 'Quick Links',
                'policies' => 'Policies',
            ];
        @endphp

        <div class="mobile-menu-overlay"></div>
        <main class="main-container footer-manager-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="footer-manager-hero"
                    eyebrow="Footer Administration"
                    title="Footer Manager"
                    description="Manage frontend footer copy, section labels, localized content, and navigation links from one backend workspace."
                />

                <section class="backend-page-toolbar footer-manager-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Footer Manager</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="backend-toolbar-action footer-manager-toolbar-action">
                            <i class="fa fa-external-link"></i>
                            Preview Footer
                        </a>
                    </div>
                </section>

                @if ($errors->any() || session('success'))
                    <section class="backend-feedback footer-manager-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger footer-manager-alert footer-manager-alert--danger">
                                <strong>Please review the highlighted fields.</strong>
                                @foreach ($errors->all() as $error)
                                    <span>{{ $error }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="backend-alert backend-alert--success footer-manager-alert footer-manager-alert--success">
                                <strong>{{ session('success') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--3" aria-label="Footer summary">
                    <article class="backend-kpi-card backend-kpi-card--teal">
                        <div class="backend-kpi-card__icon"><i class="fa fa-cogs"></i></div>
                        <div>
                            <span>Copy Settings</span>
                            <strong>{{ number_format($summary['settings']) }}</strong>
                            <small>{{ number_format($summary['activeSettings']) }} active.</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--blue">
                        <div class="backend-kpi-card__icon"><i class="fa fa-folder-open"></i></div>
                        <div>
                            <span>Link Groups</span>
                            <strong>{{ number_format($summary['groups']) }}</strong>
                            <small>Services, quick links, and policies.</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--green">
                        <div class="backend-kpi-card__icon"><i class="fa fa-link"></i></div>
                        <div>
                            <span>Footer Links</span>
                            <strong>{{ number_format($summary['activeLinks']) }}/{{ number_format($summary['links']) }}</strong>
                            <small>Active navigation items.</small>
                        </div>
                    </article>
                </section>

                <section class="footer-manager-layout">
                    <div class="footer-manager-main">
                        <article class="backend-panel footer-manager-panel">
                            <header class="backend-section-header footer-manager-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Content</span>
                                    <h2>Footer Copy Settings</h2>
                                    <p>Edit grouped footer copy and localized labels without scrolling through one long unstructured form.</p>
                                </div>
                                <button type="submit" form="footerSettingsForm" class="backend-button backend-button-primary" data-footer-submit>
                                    <i class="fa fa-check"></i>
                                    Save Settings
                                </button>
                            </header>

                            <form id="footerSettingsForm" action="{{ route('admin.footer-manager.settings.update') }}" method="post">
                                @csrf
                                @method('put')

                                <div id="footerSettingsAccordion" class="footer-settings-accordion">
                                    @forelse ($settingGroups as $groupKey => $group)
                                        @php
                                            $groupSettings = collect($group['keys'])
                                                ->map(fn ($key) => $settingsByKey->get($key))
                                                ->filter();
                                            $activeCount = $groupSettings->where('status', true)->count();
                                        @endphp

                                        @if ($groupSettings->isNotEmpty())
                                            @include('backend.admin.footer-manager.partials.setting-section', [
                                                'groupKey' => $groupKey,
                                                'group' => $group,
                                                'groupSettings' => $groupSettings,
                                                'activeCount' => $activeCount,
                                                'isOpen' => $loop->first,
                                            ])
                                        @endif
                                    @empty
                                        <div class="footer-manager-empty">
                                            <strong>Footer settings are empty.</strong>
                                            <span>Run <code>php artisan db:seed --class=FooterSeeder</code> to insert default settings.</span>
                                        </div>
                                    @endforelse

                                    @if ($uncategorizedSettings->isNotEmpty())
                                        @include('backend.admin.footer-manager.partials.setting-section', [
                                            'groupKey' => 'Other',
                                            'group' => [
                                                'title' => 'Other Settings',
                                                'description' => 'Settings that are not assigned to a standard footer section yet.',
                                                'icon' => 'fa fa-sliders',
                                            ],
                                            'groupSettings' => $uncategorizedSettings,
                                            'activeCount' => $uncategorizedSettings->where('status', true)->count(),
                                            'isOpen' => false,
                                        ])
                                    @endif
                                </div>

                                <footer class="footer-manager-panel__footer">
                                    <span>Changes clear the footer cache automatically after save.</span>
                                    <button type="submit" class="backend-button backend-button-primary" data-footer-submit>
                                        <i class="fa fa-check"></i>
                                        Save Settings
                                    </button>
                                </footer>
                            </form>
                        </article>
                    </div>

                    <aside class="footer-manager-side">
                        <article class="backend-panel footer-manager-panel">
                            <header class="backend-section-header footer-manager-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Navigation</span>
                                    <h2>Footer Links</h2>
                                    <p>Manage grouped links in the same structure used by the frontend footer.</p>
                                </div>
                                <button type="button" class="backend-button backend-button-primary" data-toggle="modal" data-target="#addFooterLinkModal">
                                    <i class="fa fa-plus"></i>
                                    Add Link
                                </button>
                            </header>

                            <div class="footer-link-groups">
                                @forelse ($links as $group => $groupLinks)
                                    <section class="footer-link-group-card">
                                        <div class="footer-link-group-card__head">
                                            <div>
                                                <h3>{{ $groupLabels[$group] ?? ucwords(str_replace('_', ' ', $group)) }}</h3>
                                                <small>{{ $groupLinks->where('status', true)->count() }} active of {{ $groupLinks->count() }} links</small>
                                            </div>
                                            <span>{{ $group }}</span>
                                        </div>

                                        <div class="footer-link-list">
                                            @foreach ($groupLinks as $link)
                                                <article class="footer-link-item {{ $link->status ? '' : 'is-muted' }}">
                                                    <div class="footer-link-item__main">
                                                        <strong>{{ $link->label }}</strong>
                                                        <small>{{ $link->route_name ?: $link->url }}</small>
                                                    </div>
                                                    <div class="footer-link-item__meta">
                                                        <span>#{{ $link->sort_order }}</span>
                                                        <span class="backend-status-badge {{ $link->status ? 'backend-status-badge--success' : 'backend-status-badge--muted' }} footer-manager-badge {{ $link->status ? 'is-success' : 'is-muted' }}">{{ $link->status ? 'Active' : 'Inactive' }}</span>
                                                    </div>
                                                    <div class="footer-link-item__actions">
                                                        <button type="button" class="footer-manager-text-action" data-toggle="modal" data-target="#editFooterLinkModal{{ $link->id }}">
                                                            <i class="fa fa-pencil-square"></i>
                                                            Edit
                                                        </button>
                                                        <form action="{{ route('admin.footer-manager.links.destroy', $link) }}" method="post" data-footer-delete-form>
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="backend-button-delete-text footer-manager-text-action">
                                                                <i class="fa fa-trash-alt"></i>
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </article>
                                            @endforeach
                                        </div>
                                    </section>
                                @empty
                                    <div class="footer-manager-empty">
                                        <strong>Footer links are empty.</strong>
                                        <span>Add links manually or run the footer seeder.</span>
                                    </div>
                                @endforelse
                            </div>
                        </article>
                    </aside>
                </section>
            </div>
        </main>

        @include('backend.admin.footer-manager.partials.link-modal', [
            'modalId' => 'addFooterLinkModal',
            'title' => 'Add Footer Link',
            'action' => route('admin.footer-manager.links.store'),
            'method' => 'post',
            'link' => null,
        ])

        @foreach ($links as $groupLinks)
            @foreach ($groupLinks as $link)
                @include('backend.admin.footer-manager.partials.link-modal', [
                    'modalId' => 'editFooterLinkModal' . $link->id,
                    'title' => 'Edit Footer Link',
                    'action' => route('admin.footer-manager.links.update', $link),
                    'method' => 'put',
                    'link' => $link,
                ])
            @endforeach
        @endforeach
    @endcan
@endsection
