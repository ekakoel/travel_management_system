@section('title', 'Footer Manager')
@section('content')
@extends('layouts.head')

@php
    $settingGroups = [
        'brand' => [
            'title' => 'Brand & Trust',
            'description' => 'Logo accessibility text and trust badge wording shown on the left side of the footer.',
            'icon' => 'fa fa-shield',
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
    $totalLinks = $links->flatten(1)->count();
    $activeLinks = $links->flatten(1)->where('status', true)->count();
    $groupLabels = [
        'services' => 'Services',
        'quick_links' => 'Quick Links',
        'policies' => 'Policies',
    ];
@endphp

<div class="mobile-menu-overlay"></div>
<div class="main-container">
    <div class="pd-ltr-20">
        <div class="min-height-200px">
            <div class="page-header footer-manager-hero">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-12">
                        <div class="title">
                            <i class="icon-copy dw dw-browser2" aria-hidden="true"></i> Footer Manager
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Footer Manager</li>
                            </ol>
                        </nav>
                        <p class="footer-manager-lead mb-0">
                            Manage frontend footer copy, section labels, and navigation links from one focused workspace.
                        </p>
                    </div>
                    <div class="col-lg-4 col-md-12 text-lg-right mt-3 mt-lg-0">
                        <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-primary">
                            <i class="icon-copy fa fa-external-link" aria-hidden="true"></i> Preview Footer
                        </a>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger footer-manager-alert">
                    <strong>Please review the highlighted fields.</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success footer-manager-alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="footer-manager-overview">
                <div class="footer-manager-stat">
                    <span class="footer-manager-stat__label">Copy Settings</span>
                    <strong>{{ $settings->count() }}</strong>
                    <small>{{ $settings->where('status', true)->count() }} active</small>
                </div>
                <div class="footer-manager-stat">
                    <span class="footer-manager-stat__label">Link Groups</span>
                    <strong>{{ $links->count() }}</strong>
                    <small>Services, quick links, policies</small>
                </div>
                <div class="footer-manager-stat">
                    <span class="footer-manager-stat__label">Footer Links</span>
                    <strong>{{ $activeLinks }}/{{ $totalLinks }}</strong>
                    <small>active navigation items</small>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8 col-lg-7 col-md-12">
                    <div class="card-box mb-30 footer-manager-panel">
                        <div class="footer-manager-panel__header">
                            <div>
                                <span class="footer-manager-eyebrow">Content</span>
                                <h4 class="text-blue h4 mb-1">Footer Copy Settings</h4>
                                <p class="mb-0 text-muted">Edit grouped copy without scrolling through one long form.</p>
                            </div>
                            <button type="submit" form="footerSettingsForm" class="btn btn-primary">
                                <i class="icon-copy fa fa-check" aria-hidden="true"></i> Save Settings
                            </button>
                        </div>

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
                                        <div class="footer-setting-section">
                                            <button class="footer-setting-section__toggle {{ $loop->first ? '' : 'collapsed' }}" type="button" data-toggle="collapse" data-target="#footerSettingGroup{{ $groupKey }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="footerSettingGroup{{ $groupKey }}">
                                                <span class="footer-setting-section__icon">
                                                    <i class="{{ $group['icon'] }}" aria-hidden="true"></i>
                                                </span>
                                                <span>
                                                    <strong>{{ $group['title'] }}</strong>
                                                    <small>{{ $group['description'] }}</small>
                                                </span>
                                                <span class="footer-setting-section__meta">{{ $activeCount }}/{{ $groupSettings->count() }} active</span>
                                            </button>

                                            <div id="footerSettingGroup{{ $groupKey }}" class="collapse {{ $loop->first ? 'show' : '' }}" data-parent="#footerSettingsAccordion">
                                                <div class="footer-setting-section__body">
                                                    @foreach ($groupSettings as $setting)
                                                        <div class="footer-setting-item">
                                                            <div class="footer-setting-item__head">
                                                                <div>
                                                                    <strong>{{ ucwords(str_replace('_', ' ', $setting->key)) }}</strong>
                                                                    <span>{{ $setting->key }}</span>
                                                                </div>
                                                                <label class="footer-switch mb-0">
                                                                    <input type="hidden" name="settings[{{ $setting->id }}][status]" value="0">
                                                                    <input type="checkbox" name="settings[{{ $setting->id }}][status]" value="1" {{ $setting->status ? 'checked' : '' }}>
                                                                    <span>Active</span>
                                                                </label>
                                                            </div>

                                                            <div class="footer-setting-input-grid">
                                                                <div class="form-group mb-0">
                                                                    <label>English / Default</label>
                                                                    <textarea name="settings[{{ $setting->id }}][value]" class="form-control" rows="2">{{ old("settings.{$setting->id}.value", $setting->value) }}</textarea>
                                                                </div>
                                                                <div class="form-group mb-0">
                                                                    <label>Chinese Traditional</label>
                                                                    <textarea name="settings[{{ $setting->id }}][value_traditional]" class="form-control" rows="2">{{ old("settings.{$setting->id}.value_traditional", $setting->value_traditional) }}</textarea>
                                                                </div>
                                                                <div class="form-group mb-0">
                                                                    <label>Chinese Simplified</label>
                                                                    <textarea name="settings[{{ $setting->id }}][value_simplified]" class="form-control" rows="2">{{ old("settings.{$setting->id}.value_simplified", $setting->value_simplified) }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <div class="alert alert-warning mb-0">
                                        Footer settings are empty. Run <code>php artisan db:seed --class=FooterSeeder</code> to insert default settings.
                                    </div>
                                @endforelse

                                @if ($uncategorizedSettings->isNotEmpty())
                                    <div class="footer-setting-section">
                                        <button class="footer-setting-section__toggle collapsed" type="button" data-toggle="collapse" data-target="#footerSettingGroupOther" aria-expanded="false" aria-controls="footerSettingGroupOther">
                                            <span class="footer-setting-section__icon">
                                                <i class="fa fa-sliders" aria-hidden="true"></i>
                                            </span>
                                            <span>
                                                <strong>Other Settings</strong>
                                                <small>Settings that are not assigned to a standard footer section yet.</small>
                                            </span>
                                            <span class="footer-setting-section__meta">{{ $uncategorizedSettings->where('status', true)->count() }}/{{ $uncategorizedSettings->count() }} active</span>
                                        </button>
                                        <div id="footerSettingGroupOther" class="collapse" data-parent="#footerSettingsAccordion">
                                            <div class="footer-setting-section__body">
                                                @foreach ($uncategorizedSettings as $setting)
                                                    <div class="footer-setting-item">
                                                        <div class="footer-setting-item__head">
                                                            <div>
                                                                <strong>{{ ucwords(str_replace('_', ' ', $setting->key)) }}</strong>
                                                                <span>{{ $setting->key }}</span>
                                                            </div>
                                                            <label class="footer-switch mb-0">
                                                                <input type="hidden" name="settings[{{ $setting->id }}][status]" value="0">
                                                                <input type="checkbox" name="settings[{{ $setting->id }}][status]" value="1" {{ $setting->status ? 'checked' : '' }}>
                                                                <span>Active</span>
                                                            </label>
                                                        </div>
                                                        <div class="footer-setting-input-grid">
                                                            <div class="form-group mb-0">
                                                                <label>English / Default</label>
                                                                <textarea name="settings[{{ $setting->id }}][value]" class="form-control" rows="2">{{ old("settings.{$setting->id}.value", $setting->value) }}</textarea>
                                                            </div>
                                                            <div class="form-group mb-0">
                                                                <label>Chinese Traditional</label>
                                                                <textarea name="settings[{{ $setting->id }}][value_traditional]" class="form-control" rows="2">{{ old("settings.{$setting->id}.value_traditional", $setting->value_traditional) }}</textarea>
                                                            </div>
                                                            <div class="form-group mb-0">
                                                                <label>Chinese Simplified</label>
                                                                <textarea name="settings[{{ $setting->id }}][value_simplified]" class="form-control" rows="2">{{ old("settings.{$setting->id}.value_simplified", $setting->value_simplified) }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="footer-manager-panel__footer">
                                <span>Changes clear the footer cache automatically after save.</span>
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-copy fa fa-check" aria-hidden="true"></i> Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5 col-md-12">
                    <div class="card-box mb-30 footer-manager-panel footer-link-panel">
                        <div class="footer-manager-panel__header">
                            <div>
                                <span class="footer-manager-eyebrow">Navigation</span>
                                <h4 class="text-blue h4 mb-1">Footer Links</h4>
                                <p class="mb-0 text-muted">Manage grouped links in the exact footer structure.</p>
                            </div>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addFooterLinkModal">
                                <i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Link
                            </button>
                        </div>

                        <div class="footer-link-groups">
                            @forelse ($links as $group => $groupLinks)
                                <section class="footer-link-group-card">
                                    <div class="footer-link-group-card__head">
                                        <div>
                                            <h5>{{ $groupLabels[$group] ?? ucwords(str_replace('_', ' ', $group)) }}</h5>
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
                                                    <span class="{{ $link->status ? 'text-success' : 'text-muted' }}">{{ $link->status ? 'Active' : 'Inactive' }}</span>
                                                </div>
                                                <div class="footer-link-item__actions">
                                                    <button type="button" class="footer-text-action" data-toggle="modal" data-target="#editFooterLinkModal{{ $link->id }}">
                                                        <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                                                    </button>
                                                    <form action="{{ route('admin.footer-manager.links.destroy', $link) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="footer-text-action text-danger" onclick="return confirm('Remove this footer link?')">
                                                            <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </section>
                            @empty
                                <div class="alert alert-warning mb-0">
                                    Footer links are empty. Add links manually or run the footer seeder.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.footer-manager.partials.link-modal', [
    'modalId' => 'addFooterLinkModal',
    'title' => 'Add Footer Link',
    'action' => route('admin.footer-manager.links.store'),
    'method' => 'post',
    'link' => null,
])

@foreach ($links as $groupLinks)
    @foreach ($groupLinks as $link)
        @include('admin.footer-manager.partials.link-modal', [
            'modalId' => 'editFooterLinkModal' . $link->id,
            'title' => 'Edit Footer Link',
            'action' => route('admin.footer-manager.links.update', $link),
            'method' => 'put',
            'link' => $link,
        ])
    @endforeach
@endforeach

@push('styles')
<style>
    .footer-manager-hero {
        border: 0;
        border-radius: 22px;
        background: linear-gradient(135deg, #f7fbff 0%, #eef6f1 100%);
        box-shadow: 0 16px 45px rgba(33, 52, 72, 0.08);
    }

    .footer-manager-lead {
        color: #607087;
        max-width: 720px;
    }

    .footer-manager-alert {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 12px 28px rgba(33, 52, 72, 0.08);
    }

    .footer-manager-overview {
        display: grid;
        gap: 18px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-bottom: 24px;
    }

    .footer-manager-stat {
        background: #fff;
        border: 1px solid #e4ebf3;
        border-radius: 18px;
        box-shadow: 0 12px 34px rgba(33, 52, 72, 0.07);
        padding: 18px;
    }

    .footer-manager-stat__label,
    .footer-manager-eyebrow {
        color: #6b7c93;
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .footer-manager-stat strong {
        color: #152238;
        display: block;
        font-size: 30px;
        line-height: 1.1;
        margin-top: 8px;
    }

    .footer-manager-stat small {
        color: #6b7c93;
    }

    .footer-manager-panel {
        border-radius: 22px;
        overflow: hidden;
    }

    .footer-manager-panel__header,
    .footer-manager-panel__footer {
        align-items: center;
        display: flex;
        gap: 16px;
        justify-content: space-between;
        padding: 22px;
    }

    .footer-manager-panel__header {
        border-bottom: 1px solid #e8eef5;
    }

    .footer-manager-panel__footer {
        background: #f8fbff;
        border-top: 1px solid #e8eef5;
        color: #6b7c93;
    }

    .footer-settings-accordion,
    .footer-link-groups {
        padding: 18px;
    }

    .footer-setting-section {
        border: 1px solid #e3eaf2;
        border-radius: 18px;
        margin-bottom: 14px;
        overflow: hidden;
    }

    .footer-setting-section__toggle {
        align-items: center;
        background: #fff;
        border: 0;
        color: #152238;
        display: grid;
        gap: 14px;
        grid-template-columns: auto 1fr auto;
        padding: 18px;
        text-align: left;
        width: 100%;
    }

    .footer-setting-section__toggle.collapsed {
        background: #fbfdff;
    }

    .footer-setting-section__toggle small {
        color: #6b7c93;
        display: block;
        font-weight: 400;
        margin-top: 3px;
    }

    .footer-setting-section__icon {
        align-items: center;
        background: #eef7ff;
        border-radius: 14px;
        color: #1d72b8;
        display: inline-flex;
        height: 42px;
        justify-content: center;
        width: 42px;
    }

    .footer-setting-section__meta {
        background: #f1f6fb;
        border-radius: 999px;
        color: #52657a;
        font-size: 12px;
        font-weight: 700;
        padding: 7px 10px;
    }

    .footer-setting-section__body {
        background: #f9fbfe;
        border-top: 1px solid #e3eaf2;
        padding: 16px;
    }

    .footer-setting-item {
        background: #fff;
        border: 1px solid #e7edf5;
        border-radius: 16px;
        margin-bottom: 14px;
        padding: 16px;
    }

    .footer-setting-item:last-child {
        margin-bottom: 0;
    }

    .footer-setting-item__head {
        align-items: center;
        display: flex;
        gap: 14px;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .footer-setting-item__head span {
        color: #8795a8;
        display: block;
        font-size: 12px;
    }

    .footer-setting-input-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .footer-setting-input-grid label,
    .footer-link-modal label {
        color: #52657a;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
    }

    .footer-setting-input-grid textarea {
        min-height: 82px;
        resize: vertical;
    }

    .footer-switch {
        align-items: center;
        background: #f5f8fb;
        border-radius: 999px;
        color: #52657a;
        display: inline-flex;
        font-size: 12px;
        font-weight: 700;
        gap: 8px;
        padding: 7px 10px;
    }

    .footer-switch input[type="checkbox"] {
        appearance: auto;
        cursor: pointer;
        flex: 0 0 auto;
        height: 18px;
        margin: 0;
        opacity: 1;
        position: static !important;
        right: auto !important;
        top: auto !important;
        visibility: visible;
        width: 18px;
    }

    .footer-switch label,
    .footer-switch span {
        cursor: pointer;
        line-height: 1;
        margin-bottom: 0;
        position: static !important;
    }

    .footer-link-group-card {
        border: 1px solid #e3eaf2;
        border-radius: 18px;
        margin-bottom: 16px;
        overflow: hidden;
    }

    .footer-link-group-card__head {
        align-items: center;
        background: #f8fbff;
        border-bottom: 1px solid #e3eaf2;
        display: flex;
        justify-content: space-between;
        padding: 16px;
    }

    .footer-link-group-card__head h5 {
        color: #152238;
        font-size: 16px;
        margin: 0;
    }

    .footer-link-group-card__head small {
        color: #6b7c93;
    }

    .footer-link-group-card__head > span {
        background: #edf4fb;
        border-radius: 999px;
        color: #52657a;
        font-size: 11px;
        font-weight: 700;
        padding: 6px 9px;
    }

    .footer-link-list {
        display: grid;
        gap: 0;
    }

    .footer-link-item {
        border-bottom: 1px solid #eef2f7;
        display: grid;
        gap: 10px;
        grid-template-columns: 1fr auto;
        padding: 14px 16px;
    }

    .footer-link-item:last-child {
        border-bottom: 0;
    }

    .footer-link-item.is-muted {
        opacity: .62;
    }

    .footer-link-item__main strong,
    .footer-link-item__main small {
        display: block;
    }

    .footer-link-item__main small {
        color: #738399;
        margin-top: 3px;
        overflow-wrap: anywhere;
    }

    .footer-link-item__meta {
        color: #6b7c93;
        display: grid;
        font-size: 12px;
        gap: 4px;
        justify-items: end;
    }

    .footer-link-item__actions {
        align-items: center;
        display: flex;
        gap: 12px;
        grid-column: 1 / -1;
    }

    .footer-text-action {
        background: transparent;
        border: 0;
        color: #1d72b8;
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
        padding: 0;
    }

    .footer-text-action:hover {
        text-decoration: underline;
    }

    .footer-link-modal .modal-content {
        border: 0;
        border-radius: 22px;
        box-shadow: 0 24px 70px rgba(21, 34, 56, 0.24);
        overflow: hidden;
    }

    .footer-link-modal__header,
    .footer-link-modal__footer {
        align-items: center;
        display: flex;
        gap: 18px;
        justify-content: space-between;
        padding: 22px;
    }

    .footer-link-modal__header {
        background: linear-gradient(135deg, #f7fbff 0%, #eef6f1 100%);
        border-bottom: 1px solid #e4ebf3;
    }

    .footer-link-modal__header h4 {
        color: #152238;
        margin: 4px 0;
    }

    .footer-link-modal__header p {
        color: #607087;
        margin: 0;
    }

    .footer-link-modal__body {
        display: grid;
        gap: 16px;
        padding: 20px 22px;
    }

    .footer-link-modal__section {
        border: 1px solid #e4ebf3;
        border-radius: 16px;
        padding: 16px;
    }

    .footer-link-modal__section-title {
        align-items: flex-start;
        display: flex;
        gap: 12px;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .footer-link-modal__section-title strong {
        color: #152238;
        display: block;
    }

    .footer-link-modal__section-title span {
        color: #6b7c93;
        font-size: 12px;
        max-width: 330px;
        text-align: right;
    }

    .footer-link-modal__grid {
        display: grid;
        gap: 14px;
        grid-template-columns: 1fr 1.2fr .7fr;
    }

    .footer-link-modal__grid--two {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .footer-link-modal__grid .form-group {
        margin-bottom: 0;
    }

    .footer-link-modal__checks {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .footer-toggle {
        align-items: center;
        background: #f5f8fb;
        border: 1px solid #dfe8f2;
        border-radius: 999px;
        color: #52657a;
        cursor: pointer;
        display: inline-flex;
        font-size: 12px;
        font-weight: 800;
        gap: 9px;
        letter-spacing: .03em;
        margin: 0;
        min-height: 34px;
        padding: 6px 11px 6px 7px;
        position: relative;
        text-transform: uppercase;
        user-select: none;
    }

    .footer-toggle input[type="checkbox"] {
        cursor: pointer;
        height: 100% !important;
        inset: 0 !important;
        margin: 0 !important;
        opacity: 0;
        position: absolute !important;
        width: 100% !important;
        z-index: 2;
    }

    .footer-toggle__track {
        background: #c8d3df;
        border-radius: 999px;
        display: inline-flex;
        flex: 0 0 auto;
        height: 20px;
        padding: 2px;
        transition: background .18s ease;
        width: 38px;
    }

    .footer-toggle__knob {
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(21, 34, 56, .24);
        display: block;
        height: 16px;
        transform: translateX(0);
        transition: transform .18s ease;
        width: 16px;
    }

    .footer-toggle__text {
        line-height: 1;
        position: static !important;
        white-space: nowrap;
    }

    .footer-toggle input[type="checkbox"]:checked + .footer-toggle__track {
        background: #1d72b8;
    }

    .footer-toggle input[type="checkbox"]:checked + .footer-toggle__track .footer-toggle__knob {
        transform: translateX(18px);
    }

    .footer-toggle input[type="checkbox"]:focus-visible + .footer-toggle__track {
        box-shadow: 0 0 0 3px rgba(29, 114, 184, .18);
    }

    .footer-link-modal__footer {
        background: #f8fbff;
        border-top: 1px solid #e4ebf3;
    }

    @media (max-width: 1199px) {
        .footer-setting-input-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .footer-manager-overview {
            grid-template-columns: 1fr;
        }

        .footer-manager-panel__header,
        .footer-manager-panel__footer,
        .footer-setting-item__head {
            align-items: flex-start;
            flex-direction: column;
        }

        .footer-setting-section__toggle {
            grid-template-columns: auto 1fr;
        }

        .footer-setting-section__meta {
            grid-column: 1 / -1;
            justify-self: flex-start;
        }

        .footer-link-modal__header,
        .footer-link-modal__footer,
        .footer-link-modal__section-title {
            align-items: flex-start;
            flex-direction: column;
        }

        .footer-link-modal__section-title span {
            max-width: none;
            text-align: left;
        }

        .footer-link-modal__grid,
        .footer-link-modal__grid--two {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
@endsection
