@extends('layouts.head')

@section('title', __('messages.Tour Detail'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/tours/detail.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/tours/detail.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container tour-detail-page" data-tour-gallery-base="{{ url('/tours/gallery') }}" data-tour-csrf="{{ csrf_token() }}">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="tour-detail-hero"
                    eyebrow="Operations Inventory"
                    title="{{ $tour->name }}"
                    description="Review tour profile, content completeness, gallery assets, and active package pricing."
                >
                    @canany(['posDev','posAuthor'])
                        <x-slot name="action">
                            <a href="{{ route('admin.tours.edit', $tour->id) }}" class="backend-page-primary-action">
                                <i class="fa fa-pencil"></i>
                                Edit Tour
                            </a>
                        </x-slot>
                    @endcanany
                </x-backend.page-hero>

                <section class="backend-page-toolbar tour-detail-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tours-admin.index') }}">Tour Packages</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $tour->name }}</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--{{ $tourDetail->statusTone() }}">{{ $tourDetail->status() }}</span>
                        <span class="backend-status-badge backend-status-badge--info">{{ $tourDetail->duration() }}</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('error'))
                    <section class="backend-feedback tour-detail-feedback">
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

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Tour detail summary">
                    @foreach ($tourDetail->stats() as $stat)
                        <article class="backend-kpi-card backend-kpi-card--{{ $stat['tone'] }}">
                            <div class="backend-kpi-card__icon"><i class="{{ $stat['icon'] }}"></i></div>
                            <div><span>{{ $stat['label'] }}</span><strong>{{ $stat['value'] }}</strong><small>{{ $stat['meta'] }}</small></div>
                        </article>
                    @endforeach
                </section>

                <x-backend.detail-layout class="tour-detail-layout">
                    <x-slot name="main">
                <section class="backend-panel tour-detail-panel m-b-18">
                    <div class="backend-section-header tour-detail-panel__heading">
                        <div>
                            <span class="backend-section-header__label">Tour Profile</span>
                            <h2>Detail Information</h2>
                        </div>
                    </div>
                    <div class="tour-detail-summary">
                        <figure class="backend-table-card tour-detail-cover">
                            <img
                                src="{{ asset('storage/tours/tours-cover/' . $tour->cover) }}"
                                alt="{{ $tour->name }}"
                                loading="lazy"
                                decoding="async"
                                width="360"
                                height="240"
                            >
                        </figure>

                        <article class="backend-table-card tour-detail-info-card">
                            <div class="backend-table-card__header">
                                <div>
                                    <span class="backend-table-card__label">Profile Summary</span>
                                    <strong>{{ $tour->name }}</strong>
                                </div>
                                <span class="backend-status-badge backend-status-badge--{{ $tourDetail->statusTone() }}">{{ $tourDetail->status() }}</span>
                            </div>
                            <dl class="backend-table-card-grid">
                                <div><dt>Code</dt><dd>{{ $tour->code ?: '-' }}</dd></div>
                                <div><dt>Type</dt><dd>{{ $tour->type?->type ?: '-' }}</dd></div>
                                <div><dt>Area</dt><dd>{{ $tour->area ?: '-' }}</dd></div>
                                <div><dt>Duration</dt><dd>{{ $tourDetail->duration() }}</dd></div>
                                <div><dt>Status</dt><dd><span class="backend-status-badge backend-status-badge--{{ $tourDetail->statusTone() }}">{{ $tourDetail->status() }}</span></dd></div>
                                <div><dt>Tax</dt><dd>{{ $tax->tax ?? 0 }}%</dd></div>
                            </dl>
                        </article>

                        @foreach ($tourDetail->contentBlocks() as $label => $content)
                            @if (filled($content))
                                <article class="backend-table-card tour-detail-content-block">
                                    <div class="backend-table-card__header">
                                        <div>
                                            <span class="backend-table-card__label">Content</span>
                                            <strong>{{ $label }}</strong>
                                        </div>
                                    </div>
                                    <div class="tour-detail-richtext">{!! $content !!}</div>
                                </article>
                            @endif
                        @endforeach
                    </div>
                </section>

                <section class="backend-panel tour-detail-panel m-b-18">
                    <div class="backend-section-header tour-detail-panel__heading">
                        <div>
                            <span class="backend-section-header__label">Gallery</span>
                            <h2>Tour Gallery</h2>
                        </div>
                        @canany(['posDev','posAuthor'])
                            @include('partials.modal-dropzone', compact('tour'))
                        @endcanany
                    </div>
                    <div class="tour-detail-gallery">
                        @forelse ($tour->images as $tourImage)
                            <figure class="backend-table-card tour-detail-gallery__item" id="image-{{ $tourImage->id }}">
                                <img src="{{ getThumbnail('storage/tours/tour-gallery/' . $tourImage->image, 380, 200) }}" alt="{{ $tour->name }} gallery image" loading="lazy">
                                @canany(['posDev','posAuthor'])
                                    <figcaption class="backend-table-actions">
                                        <button type="button" class="backend-icon-action is-danger" data-tour-gallery-delete="{{ $tourImage->id }}" aria-label="Delete gallery image">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                        <button type="button" class="backend-icon-action" data-tour-gallery-update="{{ $tourImage->id }}" aria-label="Update gallery image">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    </figcaption>
                                @endcanany
                            </figure>
                        @empty
                            <div class="backend-empty-state">
                                <i class="fa fa-picture-o"></i>
                                <strong>No gallery images.</strong>
                                <span>Upload images to improve tour package presentation.</span>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section id="prices" class="backend-panel tour-detail-panel">
                    <div class="backend-section-header tour-detail-panel__heading">
                        <div>
                            <span class="backend-section-header__label">Pricing</span>
                            <h2>Tour Prices</h2>
                        </div>
                        @canany(['posDev','posAuthor'])
                            <button type="button" class="backend-toolbar-action" data-toggle="modal" data-target="#add-price">
                                <i class="fa fa-plus"></i>
                                Add Price
                            </button>
                        @endcanany
                    </div>

                    <section class="backend-filter-panel tour-detail-price-filter backend-filter-panel--flush">
                        <label class="backend-filter-field">
                            <span class="backend-filter-label">Filter by capacity</span>
                            <span class="backend-filter-search">
                                <i class="fa fa-search" aria-hidden="true"></i>
                                <input id="tourPriceSearchCapacity" class="backend-filter-control" type="search" placeholder="Search capacity" data-tour-price-filter="capacity">
                            </span>
                        </label>
                    </section>

                    <div class="backend-table-wrap tour-detail-table-wrap">
                        <table id="tourPriceTable" class="backend-table tour-detail-price-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Capacity</th>
                                    <th>Expired Date</th>
                                    <th>Public Rate / Pax</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tourDetail->priceRows() as $row)
                                    @php($price = $row['model'])
                                    <tr data-tour-price-row data-tour-price-capacity="{{ strtolower($row['capacity']) }}">
                                        <td data-label="#">{{ $loop->iteration }}</td>
                                        <td data-label="Capacity">{{ $row['capacity'] }}</td>
                                        <td data-label="Expired Date">{{ dateFormat($price->expired_date) }}</td>
                                        <td data-label="Public Rate / Pax">{{ currencyFormatUsd($row['published_rate']) }}</td>
                                        <td data-label="Status"><span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $price->status }}</span></td>
                                        <td data-label="Action">
                                            <div class="backend-table-actions">
                                                <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#detail-price-{{ $price->id }}" aria-label="View price detail">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                @canany(['posDev','posAuthor'])
                                                    <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#update-price-{{ $price->id }}" aria-label="Edit price">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('admin.tours.prices.destroy', $price->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="backend-icon-action is-danger" data-tour-price-delete="{{ $row['capacity'] }}" aria-label="Delete price">
                                                            <i class="fa fa-trash-o"></i>
                                                        </button>
                                                    </form>
                                                @endcanany
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="backend-table-empty">
                                                <i class="fa fa-tags"></i>
                                                <strong>No price rows.</strong>
                                                <span>Add a price row to publish tour selling rates.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                    </x-slot>

                    <x-slot name="side">
                        <section class="backend-panel backend-detail-side-card tour-detail-context-panel">
                            <div class="backend-section-header">
                                <div>
                                    <span class="backend-section-header__label">Context</span>
                                    <h2>Tour Snapshot</h2>
                                    <p>Quick operational context for this tour package.</p>
                                </div>
                            </div>
                            <ul class="backend-detail-side-list">
                                <li>
                                    <span>Status</span>
                                    <strong><span class="backend-status-badge backend-status-badge--{{ $tourDetail->statusTone() }}">{{ $tourDetail->status() }}</span></strong>
                                    <small>Current package publication state.</small>
                                </li>
                                <li>
                                    <span>Duration</span>
                                    <strong>{{ $tourDetail->duration() }}</strong>
                                    <small>Operational package duration.</small>
                                </li>
                                <li>
                                    <span>Gallery</span>
                                    <strong>{{ number_format($tour->images->count()) }} images</strong>
                                    <small>Visual assets attached to this tour.</small>
                                </li>
                                <li>
                                    <span>Price Rows</span>
                                    <strong>{{ number_format(count($tourDetail->priceRows())) }} rows</strong>
                                    <small>Active and draft pricing configurations.</small>
                                </li>
                            </ul>
                            @canany(['posDev','posAuthor'])
                                <div class="backend-detail-side-actions">
                                    <a href="{{ route('admin.tours.edit', $tour->id) }}" class="backend-page-primary-action">
                                        <i class="fa fa-pencil"></i>
                                        Edit Tour
                                    </a>
                                    <button type="button" class="backend-toolbar-action" data-toggle="modal" data-target="#add-price">
                                        <i class="fa fa-plus"></i>
                                        Add Price
                                    </button>
                                </div>
                            @endcanany
                        </section>
                    </x-slot>
                </x-backend.detail-layout>

                @foreach ($tourDetail->priceRows() as $row)
                    @php($price = $row['model'])
                    <div class="modal fade backend-modal tour-detail-modal" id="detail-price-{{ $price->id }}" tabindex="-1" role="dialog" aria-labelledby="detail-price-title-{{ $price->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="backend-modal__header">
                                    <div>
                                        <span class="backend-section-header__label">Price Detail</span>
                                        <h5 id="detail-price-title-{{ $price->id }}">{{ $tour->name }} | {{ $row['capacity'] }}</h5>
                                    </div>
                                    <span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $price->status }}</span>
                                </div>
                                <div class="backend-modal__body">
                                    <dl class="backend-table-card-grid">
                                        <div><dt>USD Rate</dt><dd>{{ number_format($usdrates->rate ?? 0) }}</dd></div>
                                        <div><dt>Contract Rate</dt><dd>{{ currencyFormatUsd($row['contract_rate_usd']) }}</dd></div>
                                        <div><dt>Markup</dt><dd>{{ currencyFormatUsd($price->markup) }}</dd></div>
                                        <div><dt>Tax</dt><dd>{{ currencyFormatUsd($row['tax_amount']) }}</dd></div>
                                        <div><dt>Price / Pax</dt><dd>{{ currencyFormatUsd($row['published_rate']) }}</dd></div>
                                        <div><dt>Expired Date</dt><dd>{{ dateFormat($price->expired_date) }}</dd></div>
                                    </dl>
                                </div>
                                <div class="backend-modal__footer">
                                    <button type="button" class="backend-button backend-button-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @canany(['posDev','posAuthor'])
                        <div class="modal fade backend-modal tour-detail-modal" id="update-price-{{ $price->id }}" tabindex="-1" role="dialog" aria-labelledby="update-price-title-{{ $price->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="backend-modal__header">
                                        <div>
                                            <span class="backend-section-header__label">Edit Price</span>
                                            <h5 id="update-price-title-{{ $price->id }}">{{ $tour->name }} | {{ $row['capacity'] }}</h5>
                                        </div>
                                    </div>
                                    <div class="backend-modal__body">
                                        <form id="fedit-price-{{ $price->id }}" action="{{ route('admin.tours.prices.update', $price->id) }}" method="post">
                                            @csrf
                                            @method('put')
                                            <div class="tour-detail-form-grid">
                                                <label class="backend-form-field">
                                                    <span>Minimum Guests <em>*</em></span>
                                                    <input name="min_qty" type="number" min="1" class="backend-form-control @error('min_qty') is-invalid @enderror" value="{{ $price->min_qty }}" required>
                                                </label>
                                                <label class="backend-form-field">
                                                    <span>Maximum Guests <em>*</em></span>
                                                    <input name="max_qty" type="number" min="1" class="backend-form-control @error('max_qty') is-invalid @enderror" value="{{ $price->max_qty }}" required>
                                                </label>
                                                <label class="backend-form-field">
                                                    <span>Status <em>*</em></span>
                                                    <select name="status" class="backend-form-control @error('status') is-invalid @enderror" required>
                                                        <option {{ $price->status === 'Draft' ? 'selected' : '' }} value="Draft">Draft</option>
                                                        <option {{ $price->status === 'Active' ? 'selected' : '' }} value="Active">Active</option>
                                                    </select>
                                                </label>
                                                <label class="backend-form-field">
                                                    <span>Contract Rate <em>*</em></span>
                                                    <input name="contract_rate" type="number" min="1" class="backend-form-control @error('contract_rate') is-invalid @enderror" value="{{ $price->contract_rate }}" required>
                                                </label>
                                                <label class="backend-form-field">
                                                    <span>Markup <em>*</em></span>
                                                    <input name="markup" type="number" min="1" class="backend-form-control @error('markup') is-invalid @enderror" value="{{ $price->markup }}" required>
                                                </label>
                                                <label class="backend-form-field">
                                                    <span>Expired Date <em>*</em></span>
                                                    <input name="expired_date" class="backend-form-control date-picker @error('expired_date') is-invalid @enderror" value="{{ $price->expired_date }}" required>
                                                </label>
                                            </div>
                                            <input type="hidden" name="tours_id" value="{{ $tour->id }}">
                                        </form>
                                    </div>
                                    <div class="backend-modal__footer">
                                        <button type="submit" form="fedit-price-{{ $price->id }}" class="backend-button backend-button-primary">Update</button>
                                        <button type="button" class="backend-button backend-button-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcanany
                @endforeach

                @canany(['posDev','posAuthor'])
                    <div class="modal fade backend-modal tour-detail-modal" id="add-price" tabindex="-1" role="dialog" aria-labelledby="add-price-title" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="backend-modal__header">
                                    <div>
                                        <span class="backend-section-header__label">Add Price</span>
                                        <h5 id="add-price-title">{{ $tour->name }}</h5>
                                    </div>
                                </div>
                                <div class="backend-modal__body">
                                    <form id="fadd-price-{{ $tour->id }}" action="{{ route('admin.tours.prices.store', $tour->id) }}" method="post">
                                        @csrf
                                        <div class="tour-detail-form-grid">
                                            <label class="backend-form-field">
                                                <span>Minimum Guests <em>*</em></span>
                                                <input name="min_qty" type="number" min="1" class="backend-form-control @error('min_qty') is-invalid @enderror" required>
                                            </label>
                                            <label class="backend-form-field">
                                                <span>Maximum Guests <em>*</em></span>
                                                <input name="max_qty" type="number" min="1" class="backend-form-control @error('max_qty') is-invalid @enderror" required>
                                            </label>
                                            <label class="backend-form-field">
                                                <span>Contract Rate / Pax <em>*</em></span>
                                                <input name="contract_rate" type="number" min="1" class="backend-form-control @error('contract_rate') is-invalid @enderror" required>
                                            </label>
                                            <label class="backend-form-field">
                                                <span>Markup <em>*</em></span>
                                                <input name="markup" type="number" min="1" class="backend-form-control @error('markup') is-invalid @enderror" required>
                                            </label>
                                            <label class="backend-form-field">
                                                <span>Expired Date <em>*</em></span>
                                                <input name="expired_date" class="backend-form-control date-picker @error('expired_date') is-invalid @enderror" required>
                                            </label>
                                        </div>
                                        <input type="hidden" name="tours_id" value="{{ $tour->id }}">
                                    </form>
                                </div>
                                <div class="backend-modal__footer">
                                    <button type="submit" form="fadd-price-{{ $tour->id }}" class="backend-button backend-button-primary">Add</button>
                                    <button type="button" class="backend-button backend-button-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcanany

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
