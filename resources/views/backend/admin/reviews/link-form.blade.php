@extends('layouts.head')

@section('title', 'Tour Review Links')

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/admin/reviews/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/admin/reviews/index.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container tour-reviews-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero class="tour-reviews-hero">
                    <x-slot name="kicker">
                        Review Administration
                    </x-slot>
                    <x-slot name="heading">
                        Tour Review Links
                    </x-slot>
                    <x-slot name="copy">
                        <p>
                            Create time-limited review links with QR codes and share-ready copy for tour guests.
                        </p>
                    </x-slot>
                    <x-slot name="action">
                        <a href="{{ route('admin.reviews.index') }}" class="backend-page-primary-action">
                            <i class="fa fa-star"></i>
                            Review Queue
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar tour-reviews-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Tour Reviews</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Review Links</li>
                        </ol>
                    </nav>
                </section>

                @if ($errors->any() || session('success'))
                    <section class="backend-feedback tour-reviews-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger tour-reviews-alert tour-reviews-alert--danger">
                                <strong>Form needs attention.</strong>
                                @foreach ($errors->all() as $error)
                                    <span>{{ $error }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="backend-alert backend-alert--success tour-reviews-alert tour-reviews-alert--success">
                                <strong>{{ session('success') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <section class="tour-reviews-layout tour-reviews-layout--links">
                    <div class="tour-reviews-main">
                        <article class="backend-panel tour-reviews-panel">
                            <header class="backend-section-header tour-reviews-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">History</span>
                                    <h2>Active Review Links</h2>
                                    <p>Search, open, copy, or share active review links without using a wide data table.</p>
                                </div>
                            </header>

                            <div class="backend-form-grid backend-form-grid--compact tour-review-link-filters">
                                <label>
                                    <span>Search agent</span>
                                    <input class="backend-form-control" type="search" data-review-link-filter="agent" placeholder="Agent name">
                                </label>
                                <label>
                                    <span>Search booking code</span>
                                    <input class="backend-form-control" type="search" data-review-link-filter="booking" placeholder="Booking code">
                                </label>
                            </div>

                            <div class="tour-review-link-list">
                                @forelse ($reviewLinks as $link)
                                    @php
                                        $shareText = 'We kindly invite you to share your valuable feedback on your tour experience by clicking the link below. '.$link->link;
                                    @endphp

                                    <article class="tour-review-link-card" data-review-link-card data-agent="{{ strtolower($link->agent) }}" data-booking="{{ strtolower($link->booking_code) }}">
                                        <div class="tour-review-link-card__main">
                                            <span class="backend-status-badge backend-status-badge--success tour-reviews-badge is-success">Active</span>
                                            <h3>{{ $link->booking_code }}</h3>
                                            <dl>
                                                <div>
                                                    <dt>Agent</dt>
                                                    <dd>{{ $link->agent ?: '-' }}</dd>
                                                </div>
                                                <div>
                                                    <dt>Guests</dt>
                                                    <dd>{{ number_format($link->jumlah_review) }}</dd>
                                                </div>
                                                <div>
                                                    <dt>Expires</dt>
                                                    <dd>{{ \Carbon\Carbon::parse($link->expires_at)->format('d M Y H:i') }}</dd>
                                                </div>
                                            </dl>
                                            <a href="{{ $link->link }}" target="_blank" rel="noopener noreferrer">{{ $link->link }}</a>
                                        </div>
                                        <div class="tour-review-link-card__actions">
                                            <button type="button" class="tour-reviews-action is-muted" data-toggle="modal" data-target="#shareReviewLink{{ $link->id }}">
                                                <i class="fa fa-qrcode"></i>
                                                Share
                                            </button>
                                            <button type="button" class="tour-reviews-action is-success" data-copy-text="{{ $shareText }}">
                                                <i class="fa fa-copy"></i>
                                                Copy
                                            </button>
                                        </div>
                                    </article>

                                    <div class="modal fade tour-review-share-modal" id="shareReviewLink{{ $link->id }}" tabindex="-1" role="dialog" aria-labelledby="shareReviewLinkTitle{{ $link->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <header class="tour-review-share-modal__header">
                                                    <div>
                                                        <span class="backend-section-header__label">Share Review Link</span>
                                                        <h3 id="shareReviewLinkTitle{{ $link->id }}">{{ $link->booking_code }}</h3>
                                                    </div>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </header>
                                                <div class="tour-review-share-modal__body">
                                                    @if ($link->qr_code_path)
                                                        <img src="{{ asset('storage/reviews/qrcodes/'.$link->qr_code_path) }}" alt="QR code for {{ $link->booking_code }}">
                                                    @endif
                                                    <p>{{ $link->link }}</p>
                                                </div>
                                                <footer class="tour-review-share-modal__footer">
                                                    <a class="tour-reviews-action is-success" target="_blank" rel="noopener noreferrer" href="https://api.whatsapp.com/send?text={{ urlencode($shareText) }}">
                                                        <i class="fa fa-whatsapp"></i>
                                                        WhatsApp
                                                    </a>
                                                    <button type="button" class="tour-reviews-action is-muted" data-copy-text="{{ $shareText }}">
                                                        <i class="fa fa-copy"></i>
                                                        Copy Link
                                                    </button>
                                                </footer>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="tour-reviews-empty">
                                        <strong>No active review links.</strong>
                                        <span>Generate a link from the form to start collecting tour feedback.</span>
                                    </div>
                                @endforelse
                            </div>

                            @if ($reviewLinks->hasPages())
                                <div class="tour-reviews-pagination">
                                    {{ $reviewLinks->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        </article>
                    </div>

                    <aside class="tour-reviews-side">
                        <article class="backend-panel tour-reviews-panel">
                            <header class="backend-section-header tour-reviews-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Create Link</span>
                                    <h2>Generate Review Link</h2>
                                    <p>Booking code must be unique. Links expire automatically after 7 days.</p>
                                </div>
                            </header>

                            <form id="generateReviewLink" class="backend-form tour-review-link-form" action="{{ route('generate.review-link') }}" method="POST">
                                @csrf
                                <label>
                                    <span>Agent Name <b>*</b></span>
                                    <input type="text" name="agent" class="backend-form-control @error('agent') is-invalid @enderror" value="{{ old('agent') }}" required>
                                    @error('agent')<small>{{ $message }}</small>@enderror
                                </label>
                                <label>
                                    <span>Booking Code <b>*</b></span>
                                    <input type="text" name="booking_code" class="backend-form-control @error('booking_code') is-invalid @enderror" value="{{ old('booking_code') }}" required data-uppercase-input>
                                    @error('booking_code')<small>{{ $message }}</small>@enderror
                                </label>
                                <div class="tour-review-link-form__grid">
                                    <label>
                                        <span>Arrival Date <b>*</b></span>
                                        <input type="date" name="arrival_date" class="backend-form-control @error('arrival_date') is-invalid @enderror" value="{{ old('arrival_date') }}" required>
                                        @error('arrival_date')<small>{{ $message }}</small>@enderror
                                    </label>
                                    <label>
                                        <span>Departure Date <b>*</b></span>
                                        <input type="date" name="departure_date" class="backend-form-control @error('departure_date') is-invalid @enderror" value="{{ old('departure_date') }}" required>
                                        @error('departure_date')<small>{{ $message }}</small>@enderror
                                    </label>
                                </div>
                                <label>
                                    <span>Number of Guests <b>*</b></span>
                                    <input type="number" name="jumlah_review" class="backend-form-control @error('jumlah_review') is-invalid @enderror" value="{{ old('jumlah_review', 1) }}" min="1" required>
                                    @error('jumlah_review')<small>{{ $message }}</small>@enderror
                                </label>
                            </form>

                            <footer class="tour-reviews-panel__footer">
                                <button type="submit" form="generateReviewLink" class="backend-button backend-button-primary" data-review-submit>
                                    <i class="fa fa-plus"></i>
                                    Generate Link
                                </button>
                            </footer>
                        </article>
                    </aside>
                </section>

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
