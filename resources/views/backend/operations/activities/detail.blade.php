@extends('layouts.head')

@section('title', __('messages.Activity Detail'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/activities/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/activities/index.js') }}" defer></script>
@endpush
@php
    $galleryImages = $activity->images ?? collect();
    $featuredImage = $galleryImages->first();
    $previewImages = $galleryImages->skip(1)->take(4);
@endphp

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container activity-detail-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    eyebrow="Operations Inventory"
                    title="{{ $activity->name }}"
                    description="Review activity profile, partner, operational capacity, pricing, validity, and gallery assets."
                >
                    @canany(['posDev','posAuthor'])
                        <x-slot name="action">
                            <a href="{{ route('admin.activities.edit', $activity->id) }}" class="backend-page-primary-action">
                                <i class="fa fa-pencil-alt"></i>
                                Edit Activity
                            </a>
                        </x-slot>
                    @endcanany
                </x-backend.page-hero>

                <section class="backend-page-toolbar activity-detail-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.activities.index') }}">Activities</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $activity->name }}</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--{{ $activityDetail->statusTone() }}">{{ $activityDetail->status() }}</span>
                        <span class="backend-status-badge backend-status-badge--info">{{ $activity->validity ? dateFormat($activity->validity) : 'No validity' }}</span>
                    </div>
                </section>

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
                                    <span class="backend-section-header__label">Activity Profile</span>
                                    <h2>Detail Information</h2>
                                </div>
                            </div>
                            
                            <div class="activity-detail-summary">
                                <figure class="backend-table-card activity-detail-cover">
                                    <img
                                        src="{{ asset('storage/activities/activities-cover/' . $activity->cover) }}"
                                        alt="{{ $activity->name }}"
                                        loading="lazy"
                                        decoding="async"
                                        width="360"
                                        height="240"
                                    >
                                </figure>

                                <article class="backend-table-card activity-detail-info-card">
                                    <div class="backend-table-card__header">
                                        <div>
                                            <span class="backend-table-card__label">Profile Summary</span>
                                            <strong>{{ $activity->name }}</strong>
                                        </div>
                                        <span class="backend-status-badge backend-status-badge--{{ $activityDetail->statusTone() }}">{{ $activityDetail->status() }}</span>
                                    </div>
                                    <dl class="backend-table-card-grid">
                                        <div><dt>Partner</dt><dd>{{ $partner?->name ?: '-' }}</dd></div>
                                        <div><dt>Location</dt><dd>{{ $activity->location ?: '-' }}</dd></div>
                                        <div><dt>Duration</dt><dd>{{ $activity->duration ?: '-' }}</dd></div>
                                        <div><dt>Contract Rate</dt><dd>{{ currencyFormatIdr($activity->contract_rate) }}</dd></div>
                                        <div><dt>Markup</dt><dd>{{ currencyFormatUsd($activity->markup) }}</dd></div>
                                        <div><dt>Tax</dt><dd>{{ currencyFormatUsd($activityDetail->taxAmount()) }} ({{ $taxes->tax ?? 0 }}%)</dd></div>
                                        <div><dt>Validity</dt><dd>{{ $activity->validity ? dateFormat($activity->validity) : '-' }}</dd></div>
                                        <div><dt>Status</dt><dd><span class="backend-status-badge backend-status-badge--{{ $activityDetail->statusTone() }}">{{ $activityDetail->status() }}</span></dd></div>
                                    </dl>
                                </article>

                                @if ($activity->images->count())
                                        <article class="backend-table-card activity-detail-content-block">
                                            <div class="backend-table-card__header">
                                                <div>
                                                    <span class="backend-table-card__label">Gallery</span>
                                                    <strong>Explore {{ $activity->name }}</strong>
                                                </div>
                                            </div>
                                            <div class="activity-gallery__grid">
    
                                                @foreach($activity->images as $image)
    
                                                    <a
                                                        href="{{ asset('storage/'.$image->image) }}"
                                                        class="activity-gallery__item"
                                                        target="_blank"
                                                    >
                                                        <img
                                                            src="{{ asset('storage/'.$image->image) }}"
                                                            alt="{{ $activity->name }}"
                                                            loading="lazy"
                                                        >
                                                    </a>
    
                                                @endforeach
    
                                            </div>
                                        </article>
                                        
                                @endif

                                @foreach ($activityDetail->contentBlocks() as $label => $content)
                                    @if (filled($content))
                                        <article class="backend-table-card activity-detail-content-block">
                                            <div class="backend-table-card__header">
                                                <div>
                                                    <span class="backend-table-card__label">Content</span>
                                                    <strong>{{ $label }}</strong>
                                                </div>
                                            </div>
                                            <div class="activity-detail-richtext">{!! $content !!}</div>
                                        </article>
                                    @endif
                                @endforeach
                            </div>
                        </section>
                    </x-slot>

                    <x-slot name="side">
                        <section class="backend-panel backend-detail-side-card activity-detail-context-panel">
                            <div class="backend-section-header">
                                <div>
                                    <span class="backend-section-header__label">Context</span>
                                    <h2>Activity Snapshot</h2>
                                    <p>Quick operational context for this activity.</p>
                                </div>
                            </div>
                            <ul class="backend-detail-side-list">
                                <li>
                                    <span>Status</span>
                                    <strong><span class="backend-status-badge backend-status-badge--{{ $activityDetail->statusTone() }}">{{ $activityDetail->status() }}</span></strong>
                                    <small>Current publication state.</small>
                                </li>
                                <li>
                                    <span>Partner</span>
                                    <strong>{{ $partner?->name ?: '-' }}</strong>
                                    <small>Operational supplier.</small>
                                </li>
                                <li>
                                    <span>Validity</span>
                                    <strong>{{ $activity->validity ? dateFormat($activity->validity) : '-' }}</strong>
                                    <small>Contract or selling validity.</small>
                                </li>
                                <li>
                                    <span>Published Price</span>
                                    <strong>{{ currencyFormatUsd($activityDetail->publishedRate()) }}</strong>
                                    <small>Calculated selling rate including markup and tax.</small>
                                </li>
                            </ul>
                            @canany(['posDev','posAuthor'])
                                <div class="backend-detail-side-actions">
                                    <a href="{{ route('admin.activities.edit', $activity->id) }}" class="backend-page-primary-action">
                                        <i class="fa fa-pencil-alt"></i>
                                        Edit Activity
                                    </a>
                                    <a href="{{ route('admin.activities.gallery.edit', $activity->id) }}" class="backend-toolbar-action">
                                        <i class="fa fa-picture-o"></i>
                                        Edit Gallery
                                    </a>
                                </div>
                            @endcanany
                        </section>
                    </x-slot>
                </x-backend.detail-layout>

                @include('layouts.footer')
            </div>
        </main>
        
    @endcan
@endsection