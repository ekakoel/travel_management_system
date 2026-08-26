@extends('layouts.head')

@section('title', 'Company Profile')

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/admin/company-profile/edit.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/admin/company-profile/edit.js') }}" defer></script>
@endpush

@section('content')
    @canany(['posDev','posAdm'])
        <div class="mobile-menu-overlay"></div>
        <main class="main-container company-profile-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="company-profile-hero"
                    eyebrow="Brand Administration"
                    title="Company Profile"
                    description="Manage the company identity, logos, public profile copy, contact channels, social links, and map data used by public pages, invoices, footer, and shared layouts."
                />

                <section class="backend-page-toolbar company-profile-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Company Profile</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <a href="{{ route('about-us') }}" target="_blank" rel="noopener noreferrer" class="backend-toolbar-action company-profile-toolbar-action">
                            <i class="fa fa-external-link"></i>
                            Preview Public Page
                        </a>
                    </div>
                </section>

                @if ($errors->any() || session('success'))
                    <section class="backend-feedback company-profile-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger company-profile-alert company-profile-alert--danger">
                                <strong>Please review the highlighted fields.</strong>
                                @foreach ($errors->all() as $error)
                                    <span>{{ $error }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="backend-alert backend-alert--success company-profile-alert company-profile-alert--success">
                                <strong>{{ session('success') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Company profile summary">
                    <article class="backend-kpi-card backend-kpi-card--teal">
                        <div class="backend-kpi-card__icon"><i class="fa fa-building"></i></div>
                        <div>
                            <span>Brand</span>
                            <strong>{{ $summary['brand'] }}</strong>
                            <small>Public display name.</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--blue">
                        <div class="backend-kpi-card__icon"><i class="fa fa-briefcase"></i></div>
                        <div>
                            <span>Business Type</span>
                            <strong>{{ $summary['type'] }}</strong>
                            <small>Used on About and Contact.</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--amber">
                        <div class="backend-kpi-card__icon"><i class="fa fa-phone"></i></div>
                        <div>
                            <span>Primary Contact</span>
                            <strong>{{ $summary['contact'] }}</strong>
                            <small>Shown in shared layouts.</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--green">
                        <div class="backend-kpi-card__icon"><i class="fa fa-file-text-o"></i></div>
                        <div>
                            <span>Public Copy</span>
                            <strong>{{ $summary['public'] }}</strong>
                            <small>About page content state.</small>
                        </div>
                    </article>
                </section>

                <form class="company-profile-form" action="{{ route('admin.company-profile.update') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('put')

                    <section class="company-profile-layout">
                        <div class="company-profile-main">
                            <article class="backend-panel company-profile-panel">
                                <header class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Identity</span>
                                        <h2>Company Identity</h2>
                                        <p>Core business data used by operational documents and public-facing company sections.</p>
                                    </div>
                                </header>
                                <div class="backend-form-grid company-profile-form-grid">
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'name', 'label' => 'Legal Company Name', 'required' => true, 'value' => $businessProfile->name])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'nickname', 'label' => 'Public Brand Name', 'value' => $businessProfile->nickname])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'type', 'label' => 'Business Type', 'value' => $businessProfile->type, 'placeholder' => 'B2B Travel Agent'])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'caption', 'label' => 'Short Caption', 'value' => $businessProfile->caption])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'license', 'label' => 'Business License', 'value' => $businessProfile->license])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'tax_number', 'label' => 'Tax Number', 'value' => $businessProfile->tax_number])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'tax_id', 'label' => 'Tax ID', 'value' => $businessProfile->tax_id])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'address', 'label' => 'Office Address', 'value' => $businessProfile->address, 'type' => 'textarea', 'wide' => true])
                                </div>
                            </article>

                            <article class="backend-panel company-profile-panel">
                                <header class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Public Content</span>
                                        <h2>About Page Copy</h2>
                                        <p>Localized profile copy used on About Us, footer, and reusable company blocks.</p>
                                    </div>
                                </header>
                                <div class="company-profile-language-grid">
                                    <section class="company-profile-language-panel">
                                        <header>
                                            <span>EN</span>
                                            <strong>English Content</strong>
                                        </header>
                                        @include('backend.admin.company-profile.partials.field', ['name' => 'public_tagline', 'label' => 'Tagline', 'value' => $businessProfile->public_tagline])
                                        @include('backend.admin.company-profile.partials.field', ['name' => 'public_description', 'label' => 'Description', 'value' => $businessProfile->public_description, 'type' => 'textarea', 'rows' => 7])
                                    </section>
                                    <section class="company-profile-language-panel">
                                        <header>
                                            <span>ZH-TW</span>
                                            <strong>Traditional Chinese</strong>
                                        </header>
                                        @include('backend.admin.company-profile.partials.field', ['name' => 'public_tagline_traditional', 'label' => 'Tagline', 'value' => $businessProfile->public_tagline_traditional])
                                        @include('backend.admin.company-profile.partials.field', ['name' => 'public_description_traditional', 'label' => 'Description', 'value' => $businessProfile->public_description_traditional, 'type' => 'textarea', 'rows' => 7])
                                    </section>
                                    <section class="company-profile-language-panel">
                                        <header>
                                            <span>ZH-CN</span>
                                            <strong>Simplified Chinese</strong>
                                        </header>
                                        @include('backend.admin.company-profile.partials.field', ['name' => 'public_tagline_simplified', 'label' => 'Tagline', 'value' => $businessProfile->public_tagline_simplified])
                                        @include('backend.admin.company-profile.partials.field', ['name' => 'public_description_simplified', 'label' => 'Description', 'value' => $businessProfile->public_description_simplified, 'type' => 'textarea', 'rows' => 7])
                                    </section>
                                </div>
                            </article>

                            <article class="backend-panel company-profile-panel">
                                <header class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Contact</span>
                                        <h2>Office Channels</h2>
                                    </div>
                                </header>
                                <div class="backend-form-grid company-profile-form-grid">
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'email', 'label' => 'Email', 'value' => $businessProfile->email, 'inputType' => 'email'])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'phone', 'label' => 'Primary Phone', 'value' => $businessProfile->phone])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'phone_2', 'label' => 'Secondary Phone', 'value' => $businessProfile->phone_2])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'phone_3', 'label' => 'Additional Phone', 'value' => $businessProfile->phone_3])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'whatsapp', 'label' => 'WhatsApp', 'value' => $businessProfile->whatsapp])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'map', 'label' => 'Google Maps Embed URL', 'value' => $businessProfile->map, 'help' => 'Use a Google Maps URL that contains /maps/embed so the public iframe can render.', 'wide' => true])
                                </div>
                            </article>

                            <article class="backend-panel company-profile-panel">
                                <header class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Digital Channels</span>
                                        <h2>Public Links</h2>
                                    </div>
                                </header>
                                <div class="backend-form-grid company-profile-form-grid">
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'website', 'label' => 'Website', 'value' => $businessProfile->website])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'instagram', 'label' => 'Instagram', 'value' => $businessProfile->instagram])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'facebook', 'label' => 'Facebook', 'value' => $businessProfile->facebook])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'twitter', 'label' => 'Twitter / X', 'value' => $businessProfile->twitter])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'youtube', 'label' => 'YouTube', 'value' => $businessProfile->youtube])
                                    @include('backend.admin.company-profile.partials.field', ['name' => 'linkedin', 'label' => 'LinkedIn', 'value' => $businessProfile->linkedin])
                                </div>
                            </article>
                        </div>

                        <aside class="company-profile-side">
                            <article class="backend-panel company-profile-panel">
                                <header class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Brand Assets</span>
                                        <h2>Logos</h2>
                                    </div>
                                </header>
                                <div class="company-profile-logo-stack">
                                    @include('backend.admin.company-profile.partials.logo-field', [
                                        'name' => 'logo',
                                        'label' => 'Light Mode Logo',
                                        'logoUrl' => $logoUrl,
                                        'variant' => 'light',
                                        'alt' => ($businessProfile->name ?: 'Company').' light mode logo',
                                        'help' => 'Recommended for bright backgrounds. PNG/WebP, max 2 MB.',
                                    ])
                                    @include('backend.admin.company-profile.partials.logo-field', [
                                        'name' => 'logo_dark',
                                        'label' => 'Dark Mode Logo',
                                        'logoUrl' => $logoDarkUrl,
                                        'variant' => 'dark',
                                        'alt' => ($businessProfile->name ?: 'Company').' dark mode logo',
                                        'help' => 'Recommended for dark backgrounds. PNG/WebP, max 2 MB.',
                                    ])
                                </div>
                            </article>
                        </aside>
                    </section>

                    <section class="backend-form-actions company-profile-actionbar">
                        <div>
                            <strong>Ready to update company data?</strong>
                            <p>Changes are available to public pages after saving.</p>
                        </div>
                        <button type="submit" class="backend-button backend-button-primary">
                            <i class="fa fa-check"></i>
                            Save Company Profile
                        </button>
                    </section>
                </form>

                @include('layouts.footer')
            </div>
        </main>
    @endcanany
@endsection
