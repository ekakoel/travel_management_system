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
        <main class="main-container tour-detail-page" data-tour-gallery-base="{{ url('/tours/gallery') }}" data-tour-csrf="{{ csrf_token() }}" data-tour-price-form-context="{{ old('_tour_price_form_context') }}">
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
                                <i class="fa fa-pencil-alt"></i>
                                Edit Tour
                            </a>
                        </x-slot>
                    @endcanany
                </x-backend.page-hero>

                <section class="backend-page-toolbar tour-detail-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.tour-packages.index') }}">Tour Packages</a></li>
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
                                <div><dt>Pricing</dt><dd>Versioned policy</dd></div>
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
                                            <i class="fa fa-pencil-alt"></i>
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
                            <p>Prices are activated automatically after valid input is saved. Availability follows the validity dates and pax tier; quotation also requires a fresh USD rate and one effective Tour tax policy.</p>
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
                        <label class="backend-filter-field">
                            <span class="backend-filter-label">Review state</span>
                            <select class="backend-filter-control" data-tour-price-filter="review">
                                <option value="">All prices</option>
                                <option value="needs-review">Needs Review</option>
                            </select>
                        </label>
                    </section>

                    <div class="backend-table-wrap tour-detail-table-wrap">
                        <table id="tourPriceTable" class="backend-table tour-detail-price-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Pax Tier</th>
                                    <th>Contract Rate IDR</th>
                                    <th>Markup Type</th>
                                    <th>Markup</th>
                                    <th>Validity</th>
                                    <th>Availability</th>
                                    <th>Quoteable</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tourDetail->priceRows() as $row)
                                    @php($price = $row['model'])
                                    <tr data-tour-price-row data-tour-price-capacity="{{ strtolower($row['capacity']) }}" data-tour-price-review="{{ $row['needs_review'] ? 'needs-review' : '' }}">
                                        <td data-label="#">{{ $loop->iteration }}</td>
                                        <td data-label="Pax Tier">{{ $row['capacity'] }}</td>
                                        <td data-label="Contract Rate IDR">{{ $price->contract_rate_idr === null ? '-' : 'IDR '.number_format($price->contract_rate_idr, 0, '.', ',') }}</td>
                                        <td data-label="Markup Type">{{ $row['markup_type'] }}</td>
                                        <td data-label="Markup">{{ $row['markup_display'] }}</td>
                                        <td data-label="Validity">{{ $price->valid_from?->format('Y-m-d') ?? '-' }} — {{ $price->valid_until?->format('Y-m-d') ?? '-' }}</td>
                                        <td data-label="Availability"><span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $row['display_status'] }}</span></td>
                                        <td data-label="Quoteable">{{ $row['quoteable_status'] }}</td>
                                        <td data-label="Action">
                                            <div class="backend-table-actions">
                                                <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#detail-price-{{ $price->id }}" aria-label="View price detail">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                @canany(['posDev','posAuthor'])
                                                    <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#update-price-{{ $price->id }}" aria-label="Edit price">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </button>
                                                    <form action="{{ route('admin.tours.prices.destroy', [$tour, $price]) }}" method="post">
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
                                        <td colspan="9">
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
                                        <i class="fa fa-pencil-alt"></i>
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
                                    <span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $row['display_status'] }}</span>
                                </div>
                                <div class="backend-modal__body">
                                    @if (! $row['price_available'])
                                        <div class="alert alert-warning">
                                             This row is {{ strtolower($row['display_status']) }} and cannot be quoted or used for a new order. Review its canonical values, validity, pax tier, USD rate, tax policy, and overlapping prices.
                                        </div>
                                    @endif
                                    <dl class="backend-table-card-grid">
                                        <div><dt>Availability</dt><dd>{{ $row['display_status'] }}</dd></div>
                                        <div><dt>Quoteable</dt><dd>{{ $row['quoteable_status'] }}</dd></div>
                                        <div><dt>Contract Rate</dt><dd>{{ $price->contract_rate_idr === null ? '-' : 'IDR '.number_format($price->contract_rate_idr, 0, '.', ',') }}</dd></div>
                                        <div><dt>Markup Type</dt><dd>{{ $row['markup_type'] }}</dd></div>
                                        <div><dt>Markup</dt><dd>{{ $row['markup_display'] }}</dd></div>
                                        <div><dt>Tax</dt><dd>{{ $row['price_available'] ? currencyFormatUsd($row['tax_amount']) : '-' }}</dd></div>
                                        <div><dt>Price / Pax</dt><dd>{{ $row['price_available'] ? currencyFormatUsd($row['published_rate']) : '-' }}</dd></div>
                                        <div><dt>Validity</dt><dd>{{ $price->valid_from?->format('Y-m-d') ?? '-' }} — {{ $price->valid_until?->format('Y-m-d') ?? '-' }}</dd></div>
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
                                         <form id="fedit-price-{{ $price->id }}" action="{{ route('admin.tours.prices.update', [$tour, $price]) }}" method="post">
                                             @csrf
                                             @method('put')
                                             @include('backend.operations.tours.partials.price-fields', ['price' => $price, 'formContext' => 'update:'.$price->id])
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
                                     <form id="fadd-price-{{ $tour->id }}" action="{{ route('admin.tours.prices.store', $tour) }}" method="post">
                                         @csrf
                                         @include('backend.operations.tours.partials.price-fields', ['price' => null, 'formContext' => 'create'])
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
