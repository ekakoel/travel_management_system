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
        <main class="main-container tour-detail-page" data-tour-price-form-context="{{ old('_tour_price_form_context') }}">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="tour-detail-hero"
                    eyebrow="Operations Inventory"
                    title="{{ $tour->name }}"
                    description="Review tour profile, content completeness, and active package pricing."
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
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">Admin Panel</a></li>
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
                <section class="backend-panel tour-detail-panel">
                    <div class="backend-section-header tour-detail-panel__heading">
                        <div>
                            <span class="backend-section-header__label">Tour Profile</span>
                            <h2>Detail Information</h2>
                        </div>
                    </div>
                    <div class="tour-detail-summary">
                        @php($profileDestinations = $tourDetail->destinations())
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

                        <article class="backend-table-card tour-detail-content-block">
                            <div class="backend-table-card__header">
                                <div>
                                    <span class="backend-table-card__label">Destinations</span>
                                    <strong>{{ number_format($profileDestinations->count()) }} stop{{ $profileDestinations->count() === 1 ? '' : 's' }}</strong>
                                </div>
                            </div>
                            <div class="tour-detail-richtext">
                                @if ($profileDestinations->isEmpty())
                                    <p class="tour-profile-destinations__empty">No route destinations have been added yet.</p>
                                @else
                                    <div class="tour-profile-destination-list">
                                        @foreach ($profileDestinations as $destination)
                                            <li>
                                                <span class="tour-profile-destination-list__number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                                <div class="tour-profile-destination-list__content">
                                                    <strong>{{ $destination['name'] }}</strong>
                                                    <small>
                                                        @if ($destination['day'] > 1)
                                                            Day {{ $destination['day'] ?: '-' }}
                                                        @endif
                                                        @if ($destination['time'])
                                                            <span>{{ $destination['time'] }}</span>
                                                        @endif
                                                        <span>{{ $destination['type'] }}</span>
                                                        @unless ($destination['is_active'])
                                                            <span>Draft</span>
                                                        @endunless
                                                    </small>
                                                </div>
                                            </li>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
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

                    <div class="backend-table-wrap tour-detail-table-wrap">
                        <table id="tourPriceTable" class="backend-table tour-detail-price-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Pax Tier</th>
                                    <th>Published Rate</th>
                                    <th>Validity</th>
                                    <th>Availability</th>
                                    <th class="backend-table-action-column">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tourDetail->priceRows() as $row)
                                    @php($price = $row['model'])
                                    <tr>
                                        <td data-label="#">{{ $loop->iteration }}</td>
                                        <td data-label="Pax Tier">{{ $row['capacity'] }}</td>
                                        <td data-label="Published Rate">
                                            @if ($row['price_available'])
                                                <span class="tour-detail-rate">{{ currencyFormatUsd($row['published_rate']) }}</span>
                                                <small class="tour-detail-rate-idr">{{ currencyFormatIdr($row['published_rate_idr']) }}</small>
                                            @else
                                                <span class="tour-detail-rate">-</span>
                                            @endif
                                            <button type="button" class="tour-price-calculation-action" data-toggle="modal" data-target="#tourPriceCalculation{{ $price->id }}">
                                                View calculation
                                            </button>
                                        </td>
                                        <td data-label="Validity">{{ $price->valid_from?->format('Y-m-d') ?? '-' }} - {{ $price->valid_until?->format('Y-m-d') ?? '-' }}</td>
                                        <td data-label="Availability"><span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $row['display_status'] }}</span></td>
                                        <td data-label="Action">
                                            <div class="backend-table-actions">
                                                @canany(['posDev','posAuthor'])
                                                    <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#update-price-{{ $price->id }}" aria-label="Edit price">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </button>
                                                    <form action="{{ route('admin.tours.prices.destroy', [$tour, $price]) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="backend-icon-action is-danger" data-tour-price-delete="{{ $row['capacity'] }}" aria-label="Delete price">
                                                            <i class="fa fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span>-</span>
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
                    <div class="modal fade backend-modal tour-detail-modal tour-price-calculation-modal" id="tourPriceCalculation{{ $price->id }}" tabindex="-1" role="dialog" aria-labelledby="tourPriceCalculationTitle{{ $price->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="backend-modal__header">
                                    <div>
                                        <span class="backend-section-header__label">Price Calculation</span>
                                        <h2 id="tourPriceCalculationTitle{{ $price->id }}">{{ $tour->name }} | {{ $row['capacity'] }}</h2>
                                        <p>{{ $price->valid_from?->format('Y-m-d') ?? '-' }} - {{ $price->valid_until?->format('Y-m-d') ?? '-' }}</p>
                                    </div>
                                    <div class="backend-modal__header-actions">
                                        <span class="backend-status-badge backend-status-badge--{{ $row['status_tone'] }}">{{ $row['display_status'] }}</span>
                                        <button type="button" class="backend-modal__close" data-dismiss="modal" aria-label="Close">&times;</button>
                                    </div>
                                </div>
                                <div class="backend-modal__body">
                                    @if (! $row['price_available'])
                                        <div class="backend-alert backend-alert--warning">
                                            This row is {{ strtolower($row['display_status']) }} and cannot currently be quoted. Review canonical values, validity, pax tier, USD rate, tax policy, and overlapping prices.
                                        </div>
                                    @endif

                                    <div class="tour-price-calculation-summary" aria-label="Agent rate summary">
                                        <div>
                                            <span>Agent Rate / Published Rate</span>
                                            <strong>{{ $row['price_available'] ? currencyFormatUsd($row['published_rate']) : '-' }}</strong>
                                            <small>{{ $row['price_available'] && $row['published_rate_idr'] !== null ? currencyFormatIdr($row['published_rate_idr']) : '-' }}</small>
                                        </div>
                                    </div>

                                    <div class="tour-price-calculation" aria-label="Rate calculation breakdown">
                                        <div>
                                            <span>Contract</span>
                                            <strong>{{ currencyFormatIdr($row['contract_rate_idr'] ?? 0) }}</strong>
                                            <small>{{ $row['price_available'] ? currencyFormatUsd($row['contract_rate_usd']) : 'USD rate unavailable' }}</small>
                                        </div>
                                        <div>
                                            <span>Markup</span>
                                            <strong>{{ $row['price_available'] ? currencyFormatUsd($row['markup_usd']) : $row['markup_display'] }}</strong>
                                            <small>Type: {{ $row['markup_type'] }}</small>
                                            <small>Calculation: {{ $row['markup_calculation'] }}</small>
                                            <small>{{ $row['price_available'] && $row['markup_idr'] !== null ? currencyFormatIdr($row['markup_idr']) : $row['markup_type'] }}</small>
                                        </div>
                                        <div>
                                            <span>Tax</span>
                                            <strong>{{ $row['price_available'] ? currencyFormatUsd($row['tax_amount']) : '-' }}</strong>
                                            <small>{{ $row['price_available'] && $row['tax_amount_idr'] !== null ? currencyFormatIdr($row['tax_amount_idr']) : '-' }}{{ $row['tax_percent'] !== null ? ' - '.number_format((float) $row['tax_percent'], 2).'%' : '' }}</small>
                                        </div>
                                        <div>
                                            <span>Formula</span>
                                            <strong>Contract + Markup + Tax</strong>
                                            <small>Calculated server-side by canonical Tour Package pricing.</small>
                                        </div>
                                    </div>
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
                                        <h2 id="update-price-title-{{ $price->id }}">{{ $tour->name }} | {{ $row['capacity'] }}</h2>
                                    </div>
                                    <button type="button" class="backend-modal__close" data-dismiss="modal" aria-label="Close">&times;</button>
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
                                        <h2 id="add-price-title">{{ $tour->name }}</h2>
                                        <p>Create a pax tier with server-authoritative pricing validation.</p>
                                    </div>
                                    <button type="button" class="backend-modal__close" data-dismiss="modal" aria-label="Close">&times;</button>
                                </div>
                                <div class="backend-modal__body">
                                     <form id="fadd-price-{{ $tour->id }}" action="{{ route('admin.tours.prices.store', $tour) }}" method="post">
                                         @csrf
                                         @include('backend.operations.tours.partials.price-fields', ['price' => null, 'formContext' => 'create'])
                                    </form>
                                </div>
                                <div class="backend-modal__footer">
                                    <button type="submit" form="fadd-price-{{ $tour->id }}" class="backend-button backend-button-primary">Add</button>
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
