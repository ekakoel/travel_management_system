@extends('layouts.head')

@section('title', __('messages.Activity Detail'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/activities/index.css') }}">
@endpush

@php
    $galleryImages = $activity->images ?? collect();
    $galleryPreviewImages = $galleryImages->take(6);
    $canManageActivity = auth()->user()?->canAny(['posDev', 'posAuthor', 'posAdm']) ?? false;
@endphp

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container activity-detail-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    eyebrow="Operations Inventory"
                    title="{{ $activity->name }}"
                    description="Review activity profile, partner, operational capacity, pricing, validity, and media context."
                >
                    @if ($canManageActivity)
                        <x-slot name="action">
                            <div class="activity-detail-hero-actions">
                                <a href="{{ route('admin.activities.edit', $activity->id) }}" class="backend-page-primary-action">
                                    <i class="fa fa-pencil-alt"></i>
                                    Edit Activity
                                </a>
                                <a href="{{ route('admin.activities.gallery.edit', $activity->id) }}" class="backend-toolbar-action">
                                    <i class="fa fa-picture-o"></i>
                                    Add / Edit Gallery
                                </a>
                            </div>
                        </x-slot>
                    @endif
                </x-backend.page-hero>

                <x-backend.breadcrumb-toolbar
                    class="activity-detail-toolbar"
                    :items="[
                        ['label' => 'Admin Panel', 'url' => route('admin.panel-main.view')],
                        ['label' => 'Activities', 'url' => route('admin.activities.index')],
                    ]"
                    :current="$activity->name"
                >
                    <x-slot name="actions">
                        <span class="backend-status-badge backend-status-badge--{{ $activityDetail->statusTone() }}">{{ $activityDetail->status() }}</span>
                        <span class="backend-status-badge backend-status-badge--info">{{ $activity->validity ? dateFormat($activity->validity) : 'No validity' }}</span>
                    </x-slot>
                </x-backend.breadcrumb-toolbar>

                @if ($errors->any() || session()->has('success') || session()->has('error'))
                    <section class="backend-feedback activity-detail-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger">
                                <strong>Action needs attention.</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session()->has('success'))
                            <div class="backend-alert backend-alert--success">
                                <strong>{{ session('success') }}</strong>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="backend-alert backend-alert--danger">
                                <strong>{{ session('error') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Activity summary">
                    @foreach ($activityDetail->stats() as $stat)
                        <article class="backend-kpi-card backend-kpi-card--{{ $stat['tone'] }}">
                            <div class="backend-kpi-card__icon"><i class="{{ $stat['icon'] }}"></i></div>
                            <div>
                                <span>{{ $stat['label'] }}</span>
                                <strong>{!! $stat['value'] !!}</strong>
                                <small>{{ $stat['meta'] }}</small>
                            </div>
                        </article>
                    @endforeach
                </section>

                <x-backend.detail-layout class="activity-detail-layout">
                    <x-slot name="main">
                        <section class="backend-panel activity-detail-panel">
                            <div class="backend-section-header activity-detail-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Basic Information</span>
                                    <h2>Activity Profile</h2>
                                </div>
                                <p>Core information used to identify, categorize, and assign this Activity.</p>
                            </div>

                            <div class="activity-detail-profile-card">
                                <figure class="activity-detail-cover">
                                    @if ($activity->coverUrl())
                                        <img
                                            src="{{ $activity->coverUrl() }}"
                                            alt="{{ $activity->name }}"
                                            loading="lazy"
                                            decoding="async"
                                            width="360"
                                            height="240"
                                        >
                                    @else
                                        <figcaption>No cover image available.</figcaption>
                                    @endif
                                </figure>

                                <dl class="activity-detail-info-grid activity-detail-profile-grid">
                                    <div class="activity-detail-info-item is-primary">
                                        <dt>Activity Name</dt>
                                        <dd>{{ $activity->name ?: '-' }}</dd>
                                    </div>
                                    <div class="activity-detail-info-item">
                                        <dt>Partner</dt>
                                        <dd>{{ $partner?->name ?: '-' }}</dd>
                                    </div>
                                    <div class="activity-detail-info-item">
                                        <dt>Category / Type</dt>
                                        <dd>{{ $activity->type ?: '-' }}</dd>
                                    </div>
                                    <div class="activity-detail-info-item">
                                        <dt>Location</dt>
                                        <dd>{{ $activity->location ?: '-' }}</dd>
                                    </div>
                                    <div class="activity-detail-info-item is-wide">
                                        <dt>Map</dt>
                                        <dd>{{ $activity->map ?: '-' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </section>

                        <section class="backend-panel activity-detail-panel">
                            <div class="backend-section-header activity-detail-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Gallery</span>
                                    <h2>Gallery Images</h2>
                                </div>
                            </div>

                            <div class="activity-detail-gallery-section">
                                <div class="activity-detail-gallery-preview" aria-label="Activity gallery preview">
                                    @forelse ($galleryPreviewImages as $image)
                                        <a
                                            href="{{ asset('storage/'.$image->image) }}"
                                            class="activity-detail-gallery-preview__item"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            <img src="{{ asset('storage/'.$image->image) }}" alt="{{ $activity->name }} gallery image" loading="lazy">
                                        </a>
                                    @empty
                                        <p class="activity-detail-empty-copy">No gallery images yet.</p>
                                    @endforelse
                                </div>

                                @if ($canManageActivity)
                                    <div class="activity-detail-section-actions">
                                        <a href="{{ route('admin.activities.gallery.edit', $activity->id) }}" class="backend-page-primary-action">
                                            <i class="fa fa-picture-o"></i>
                                            Add / Edit Gallery
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </section>

                        <section class="backend-panel activity-detail-panel">
                            <div class="backend-section-header activity-detail-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Operations</span>
                                    <h2>Operational Information</h2>
                                </div>
                                <p>Read-only lifecycle, duration, validity, and guest limits used by booking validation.</p>
                            </div>
                            <dl class="activity-detail-info-grid">
                                <div class="activity-detail-info-item">
                                    <dt>Status</dt>
                                    <dd><span class="backend-status-badge backend-status-badge--{{ $activityDetail->statusTone() }}">{{ $activityDetail->status() }}</span></dd>
                                </div>
                                <div class="activity-detail-info-item">
                                    <dt>Duration</dt>
                                    <dd>{{ $activity->duration ?: '-' }}</dd>
                                </div>
                                <div class="activity-detail-info-item">
                                    <dt>Valid Until</dt>
                                    <dd>{{ $activity->validity ? dateFormat($activity->validity) : '-' }}</dd>
                                </div>
                                <div class="activity-detail-info-item">
                                    <dt>Minimum Pax</dt>
                                    <dd>{{ $activity->min_pax ?: '-' }}</dd>
                                </div>
                                <div class="activity-detail-info-item">
                                    <dt>Capacity</dt>
                                    <dd>{{ $activity->qty ?: '-' }}</dd>
                                </div>
                            </dl>
                        </section>

                        <section class="backend-panel activity-detail-panel">
                            <div class="backend-section-header activity-detail-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Pricing</span>
                                    <h2>Pricing Inputs</h2>
                                </div>
                                <p>Master inputs and canonical calculated selling price resolved through ActivityPricingService.</p>
                            </div>
                            <dl class="activity-detail-info-grid">
                                <div class="activity-detail-info-item">
                                    <dt>Contract Rate</dt>
                                    <dd>
                                        @if ($activityDetail->priceAvailable())
                                            {{ currencyFormatUsd($activityDetail->contractRateUsd()) }}
                                            <small class="d-block text-muted">{{ currencyFormatIdr($activity->contract_rate) }}</small>
                                        @else
                                            {{ currencyFormatIdr($activity->contract_rate) }}
                                        @endif
                                    </dd>
                                </div>
                                <div class="activity-detail-info-item">
                                    <dt>Markup</dt>
                                    <dd>
                                        {{ currencyFormatUsd($activityDetail->markupUsd() ?? $activity->markup) }}
                                        @if ($activityDetail->priceAvailable())
                                            <small class="d-block text-muted">{{ currencyFormatIdr($activityDetail->markupIdr()) }}</small>
                                        @endif
                                    </dd>
                                </div>
                                <div class="activity-detail-info-item">
                                    <dt>Tax</dt>
                                    <dd>
                                        @if ($activityDetail->priceAvailable())
                                            {{ currencyFormatUsd($activityDetail->taxAmount()) }} ({{ $activityDetail->taxPercentage() }}%)
                                            <small class="d-block text-muted">{{ currencyFormatIdr($activityDetail->taxAmountIdr()) }}</small>
                                        @else
                                            {{ $activityDetail->pricingUnavailableMessage() }}
                                        @endif
                                    </dd>
                                </div>
                                <div class="activity-detail-info-item is-primary">
                                    <dt>Selling Price</dt>
                                    <dd>
                                        @if ($activityDetail->priceAvailable())
                                            {{ currencyFormatUsd($activityDetail->sellingPrice()) }}
                                            <small class="d-block text-muted">{{ currencyFormatIdr($activityDetail->sellingPriceIdr()) }}</small>
                                        @else
                                            {{ __('messages.Price cannot be calculated.') }}
                                            <small class="d-block text-muted">{{ $activityDetail->pricingUnavailableMessage() }}</small>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </section>

                        <section class="backend-panel activity-detail-panel">
                            <div class="backend-section-header activity-detail-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Content</span>
                                    <h2>Content and Translations</h2>
                                </div>
                                <p>Read-only customer-facing copy in the canonical language order.</p>
                            </div>

                            <div class="activity-detail-translations">
                                @foreach ($activityDetail->translationGroups() as $group)
                                    <section class="backend-translation-group activity-detail-translation-group" data-backend-translation-group>
                                        <div class="backend-translation-group__header">
                                            <h3 class="backend-translation-group__title">{{ $group['title'] }}</h3>
                                            <p class="backend-translation-group__description">{{ $group['description'] }}</p>
                                        </div>

                                        <div class="backend-translation-grid">
                                            @foreach ($group['fields'] as $field)
                                                <article class="backend-translation-field">
                                                    <h4 class="backend-form-label">{{ $field['label'] }}</h4>
                                                    <div class="activity-detail-richtext">
                                                        @if (filled($field['content']))
                                                            {!! $field['content'] !!}
                                                        @else
                                                            <p class="activity-detail-empty-copy">No content.</p>
                                                        @endif
                                                    </div>
                                                </article>
                                            @endforeach
                                        </div>
                                    </section>
                                @endforeach
                            </div>
                        </section>
                    </x-slot>

                    <x-slot name="side">
                        <section class="backend-panel backend-detail-side-card activity-detail-context-panel">
                            <div class="backend-section-header">
                                <div>
                                    <span class="backend-section-header__label">Current Status</span>
                                    <h2><span class="backend-status-badge backend-status-badge--{{ $activityDetail->statusTone() }}">{{ $activityDetail->status() }}</span></h2>
                                </div>
                                <p>Administrative metadata and maintenance actions for this Activity record.</p>
                            </div>
                            <ul class="backend-detail-side-list">
                                <li>
                                    <span>Activity ID</span>
                                    <strong>#{{ $activity->id }}</strong>
                                    <small>Internal database identifier for admin reference.</small>
                                </li>
                                <li>
                                    <span>Activity Code</span>
                                    <strong>{{ $activity->code ?: '-' }}</strong>
                                    <small>Internal code used by legacy Activity links and lookup flows.</small>
                                </li>
                                <li>
                                    <span>Author ID</span>
                                    <strong>{{ $activity->author_id ?: '-' }}</strong>
                                    <small>Latest administrative owner stored on the Activity record.</small>
                                </li>
                                <li>
                                    <span>Created At</span>
                                    <strong>{{ $activity->created_at ? $activity->created_at->format('d M Y H:i') : '-' }}</strong>
                                    <small>Initial record timestamp.</small>
                                </li>
                                <li>
                                    <span>Updated At</span>
                                    <strong>{{ $activity->updated_at ? $activity->updated_at->format('d M Y H:i') : '-' }}</strong>
                                    <small>Last persisted update timestamp.</small>
                                </li>
                                <li>
                                    <span>Media Maintenance</span>
                                    <strong>{{ number_format($galleryImages->count()) }} gallery image(s)</strong>
                                    <small>Use gallery action below to add or remove supporting images.</small>
                                </li>
                            </ul>
                            <div class="backend-detail-side-actions">
                                @if ($canManageActivity)
                                    <a href="{{ route('admin.activities.edit', $activity->id) }}" class="backend-page-primary-action">
                                        <i class="fa fa-pencil-alt"></i>
                                        Edit Activity
                                    </a>
                                    <a href="{{ route('admin.activities.gallery.edit', $activity->id) }}" class="backend-toolbar-action">
                                        <i class="fa fa-picture-o"></i>
                                        Add / Edit Gallery
                                    </a>
                                @endif
                                <a href="{{ route('admin.activities.index') }}" class="backend-toolbar-action">
                                    <i class="fa fa-arrow-left"></i>
                                    Back to Activities
                                </a>
                            </div>
                        </section>
                    </x-slot>
                </x-backend.detail-layout>

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
