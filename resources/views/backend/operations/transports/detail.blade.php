@extends('layouts.head')

@section('title', __('messages.Transport Detail'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/transports/detail.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/transports/detail.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container transport-detail-admin-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="transport-detail-hero"
                    eyebrow="Operations Inventory"
                    title="{{ $transport->name }}"
                    description="Review transport profile, operating capacity, content notes, and active selling price rules."
                >
                    @canany(['posDev','posAuthor'])
                        <x-slot name="action">
                            <a href="{{ route('admin.transports.edit', $transport->id) }}" class="backend-page-primary-action">
                                <i class="fa fa-pencil-alt"></i>
                                Edit Transport
                            </a>
                        </x-slot>
                    @endcanany
                </x-backend.page-hero>

                <section class="backend-page-toolbar transport-detail-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.transports.index') }}">Transportation</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $transport->name }}</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--{{ $transportDetail->statusTone() }}">{{ $transportDetail->status() }}</span>
                        <span class="backend-status-badge backend-status-badge--info">{{ $transportDetail->capacity() }}</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('error'))
                    <section class="backend-feedback transport-detail-feedback">
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

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Transport detail summary">
                    @foreach ($transportDetail->stats() as $stat)
                        <article class="backend-kpi-card backend-kpi-card--{{ $stat['tone'] }}">
                            <div class="backend-kpi-card__icon"><i class="{{ $stat['icon'] }}"></i></div>
                            <div>
                                <span>{{ $stat['label'] }}</span>
                                <strong>{{ $stat['value'] }}</strong>
                                <small>{{ $stat['meta'] }}</small>
                            </div>
                        </article>
                    @endforeach
                </section>

                <x-backend.detail-layout class="transport-detail-layout">
                    <x-slot name="main">
                <section class="backend-panel transport-detail-panel m-b-18">
                    <div class="backend-section-header transport-detail-panel__heading">
                        <div>
                            <span class="backend-section-header__label">Transport Profile</span>
                            <h2>Detail Information</h2>
                        </div>
                    </div>

                    <div class="transport-detail-summary">
                        <figure class="backend-table-card transport-detail-cover">
                            <img
                                src="{{ asset('storage/transports/transports-cover/' . $transport->cover) }}"
                                alt="{{ $transport->name }}"
                                loading="lazy"
                                decoding="async"
                                width="360"
                                height="240"
                            >
                        </figure>

                        <article class="backend-table-card transport-detail-info-card">
                            <div class="backend-table-card__header">
                                <div>
                                    <span class="backend-table-card__label">Profile Summary</span>
                                    <strong>{{ $transport->name }}</strong>
                                </div>
                                <span class="backend-status-badge backend-status-badge--{{ $transportDetail->statusTone() }}">{{ $transportDetail->status() }}</span>
                            </div>
                            <dl class="backend-table-card-grid">
                                <div><dt>Name</dt><dd>{{ $transport->name }}</dd></div>
                                <div><dt>Partner</dt><dd>{{ $transport->partner?->name ?: '-' }}</dd></div>
                                <div><dt>Brand</dt><dd>{{ $transport->brand ?: '-' }}</dd></div>
                                <div><dt>Type</dt><dd>{{ $transport->type ?: '-' }}</dd></div>
                                <div><dt>Capacity</dt><dd>{{ $transportDetail->capacity() }}</dd></div>
                                <div><dt>Status</dt><dd><span class="backend-status-badge backend-status-badge--{{ $transportDetail->statusTone() }}">{{ $transportDetail->status() }}</span></dd></div>
                                <div><dt>Tax</dt><dd>{{ $taxes->tax ?? 0 }}%</dd></div>
                            </dl>
                        </article>

                        @foreach ($transportDetail->contentBlocks() as $label => $content)
                            @if (filled($content))
                                <article class="backend-table-card transport-detail-content-block">
                                    <div class="backend-table-card__header">
                                        <div>
                                            <span class="backend-table-card__label">Content</span>
                                            <strong>{{ $label }}</strong>
                                        </div>
                                    </div>
                                    <div class="transport-detail-richtext">{!! $content !!}</div>
                                </article>
                            @endif
                        @endforeach
                    </div>
                </section>

                <section id="prices" class="backend-panel transport-detail-panel">
                    <div class="backend-section-header transport-detail-panel__heading">
                        <div>
                            <span class="backend-section-header__label">Pricing</span>
                            <h2>Transport Prices</h2>
                        </div>
                        @canany(['posDev','posAuthor'])
                            <button type="button" class="backend-toolbar-action" data-toggle="modal" data-target="#add-transport-price">
                                <i class="fa fa-plus"></i>
                                Add Price
                            </button>
                        @endcanany
                    </div>

                    <section class="backend-filter-panel transport-detail-price-filter backend-filter-panel--flush">
                        <label class="backend-filter-field">
                            <span class="backend-filter-label">Filter by type</span>
                            <span class="backend-filter-search">
                                <i class="fa fa-search" aria-hidden="true"></i>
                                <input id="transportPriceSearchType" class="backend-filter-control" type="search" placeholder="Search price type" data-transport-price-filter="type">
                            </span>
                        </label>
                        <label class="backend-filter-field">
                            <span class="backend-filter-label">Filter by duration</span>
                            <span class="backend-filter-search">
                                <i class="fa fa-search" aria-hidden="true"></i>
                                <input id="transportPriceSearchDuration" class="backend-filter-control" type="search" placeholder="Search duration" data-transport-price-filter="duration">
                            </span>
                        </label>
                    </section>

                    <div class="backend-table-wrap transport-detail-table-wrap">
                        <table id="transportPriceTable" class="backend-table transport-detail-price-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Duration</th>
                                    <th>Route</th>
                                    <th>Contract Rate</th>
                                    <th>Published Rate</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transportDetail->priceRows() as $row)
                                    @php($price = $row['model'])
                                    <tr data-transport-price-row data-transport-price-type="{{ strtolower($price->type ?? '') }}" data-transport-price-duration="{{ strtolower((string) $price->duration) }}">
                                        <td data-label="#">{{ $loop->iteration }}</td>
                                        <td data-label="Type"><strong>{{ $price->type }}</strong><span>{{ $price->name ?: '-' }}</span></td>
                                        <td data-label="Duration">{{ $row['duration_label'] }}</td>
                                        <td data-label="Route">{{ $row['route_label'] }}</td>
                                        <td data-label="Contract Rate">
                                            <strong>{{ currencyFormatUsd($row['contract_rate_usd']) }}</strong>
                                            <span>{{ currencyFormatIdr($price->contract_rate) }}</span>
                                        </td>
                                        <td data-label="Published Rate">
                                            <strong>{{ currencyFormatUsd($row['published_rate_usd']) }}</strong>
                                            <span>{{ currencyFormatIdr($row['published_rate_usd'] * ($usdrates->rate ?? 0)) }}</span>
                                        </td>
                                        <td data-label="Action">
                                            <div class="backend-table-actions">
                                                <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#detail-price-{{ $price->id }}" aria-label="View price detail">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                @canany(['posDev','posAuthor'])
                                                    <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#edit-price-{{ $price->id }}" aria-label="Edit price">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </button>
                                                    <form action="{{ route('admin.transports.prices.destroy', $price->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <input type="hidden" name="transport_id" value="{{ $transport->id }}">
                                                        <input type="hidden" name="author" value="{{ Auth::id() }}">
                                                        <button type="submit" class="backend-icon-action is-danger" data-transport-price-delete="{{ $price->type }} {{ $row['duration_label'] }}" aria-label="Delete price">
                                                            <i class="fa fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endcanany
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="backend-table-empty">
                                                <i class="fa fa-tags"></i>
                                                <strong>No price rows.</strong>
                                                <span>Add a price row to publish transport selling rates.</span>
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
                        <section class="backend-panel backend-detail-side-card transport-detail-context-panel">
                            <div class="backend-section-header">
                                <div>
                                    <span class="backend-section-header__label">Context</span>
                                    <h2>Transport Snapshot</h2>
                                    <p>Quick operational context for this transport unit.</p>
                                </div>
                            </div>
                            <ul class="backend-detail-side-list">
                                <li>
                                    <span>Status</span>
                                    <strong><span class="backend-status-badge backend-status-badge--{{ $transportDetail->statusTone() }}">{{ $transportDetail->status() }}</span></strong>
                                    <small>Current publication state.</small>
                                </li>
                                <li>
                                    <span>Capacity</span>
                                    <strong>{{ $transportDetail->capacity() }}</strong>
                                    <small>Operational passenger capacity.</small>
                                </li>
                                <li>
                                    <span>Identity</span>
                                    <strong>{{ $transport->brand ?: '-' }} / {{ $transport->type ?: '-' }}</strong>
                                    <small>Brand and transport type.</small>
                                </li>
                                <li>
                                    <span>Price Rows</span>
                                    <strong>{{ number_format(count($transportDetail->priceRows())) }} rows</strong>
                                    <small>Published and draft selling rules.</small>
                                </li>
                            </ul>
                            @canany(['posDev','posAuthor'])
                                <div class="backend-detail-side-actions">
                                    <a href="{{ route('admin.transports.edit', $transport->id) }}" class="backend-page-primary-action">
                                        <i class="fa fa-pencil-alt"></i>
                                        Edit Transport
                                    </a>
                                    <button type="button" class="backend-toolbar-action" data-toggle="modal" data-target="#add-transport-price">
                                        <i class="fa fa-plus"></i>
                                        Add Price
                                    </button>
                                </div>
                            @endcanany
                        </section>
                    </x-slot>
                </x-backend.detail-layout>

                @foreach ($transportDetail->priceRows() as $row)
                    @php($price = $row['model'])
                    <div class="modal fade backend-modal transport-detail-modal" id="detail-price-{{ $price->id }}" tabindex="-1" role="dialog" aria-labelledby="detail-price-title-{{ $price->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="backend-modal__header">
                                    <div>
                                        <span class="backend-section-header__label">Price Detail</span>
                                        <h5 id="detail-price-title-{{ $price->id }}">{{ $transport->name }} | {{ $price->type }}</h5>
                                    </div>
                                    <span class="backend-status-badge backend-status-badge--info">{{ $row['duration_label'] }}</span>
                                </div>
                                <div class="backend-modal__body">
                                    <dl class="backend-table-card-grid">
                                        <div><dt>Type</dt><dd>{{ $price->type }}</dd></div>
                                        <div><dt>Duration</dt><dd>{{ $row['duration_label'] }}</dd></div>
                                        <div><dt>Origin</dt><dd>{{ $price->src ?: '-' }}</dd></div>
                                        <div><dt>Destination</dt><dd>{{ $price->dst ?: '-' }}</dd></div>
                                        <div><dt>Contract Rate</dt><dd>{{ currencyFormatIdr($price->contract_rate) }} / {{ currencyFormatUsd($row['contract_rate_usd']) }}</dd></div>
                                        <div><dt>Markup</dt><dd>{{ currencyFormatUsd($price->markup) }}</dd></div>
                                        <div><dt>Tax</dt><dd>{{ currencyFormatUsd($row['tax_amount_usd']) }}</dd></div>
                                        <div><dt>Published Rate</dt><dd>{{ currencyFormatUsd($row['published_rate_usd']) }}</dd></div>
                                        <div><dt>Extra Time</dt><dd>{{ $price->extra_time ?: 0 }}%</dd></div>
                                    </dl>
                                    @if (filled($price->additional_info))
                                        <article class="backend-table-card transport-detail-content-block">
                                            <div class="backend-table-card__header">
                                                <div>
                                                    <span class="backend-table-card__label">Content</span>
                                                    <strong>Additional Information</strong>
                                                </div>
                                            </div>
                                            <div class="transport-detail-richtext">{!! $price->additional_info !!}</div>
                                        </article>
                                    @endif
                                </div>
                                <div class="backend-modal__footer">
                                    <button type="button" class="backend-button backend-button-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @canany(['posDev','posAuthor'])
                        <div class="modal fade backend-modal transport-detail-modal" id="edit-price-{{ $price->id }}" tabindex="-1" role="dialog" aria-labelledby="edit-price-title-{{ $price->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="backend-modal__header">
                                        <div>
                                            <span class="backend-section-header__label">Edit Price</span>
                                            <h5 id="edit-price-title-{{ $price->id }}">{{ $transport->name }} | {{ $price->type }}</h5>
                                        </div>
                                    </div>
                                    <div class="backend-modal__body">
                                        <form id="update-price-{{ $price->id }}" class="backend-form-grid" action="{{ route('admin.transports.prices.update', $price->id) }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            @method('put')
                                            @include('backend.operations.transports.partials.price-fields', ['price' => $price])
                                            <input name="transports_id" value="{{ $transport->id }}" type="hidden">
                                            <input name="author" value="{{ Auth::id() }}" type="hidden">
                                            <input name="service_id" value="{{ $price->id }}" type="hidden">
                                        </form>
                                    </div>
                                    <div class="backend-modal__footer">
                                        <button type="button" class="backend-button backend-button-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" form="update-price-{{ $price->id }}" class="backend-button backend-button-primary">
                                            <i class="fa fa-check"></i>
                                            Save Changes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcanany
                @endforeach

                @canany(['posDev','posAuthor'])
                    <div class="modal fade backend-modal transport-detail-modal" id="add-transport-price" tabindex="-1" role="dialog" aria-labelledby="add-transport-price-title" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="backend-modal__header">
                                    <div>
                                        <span class="backend-section-header__label">Add Price</span>
                                        <h5 id="add-transport-price-title">{{ $transport->name }}</h5>
                                    </div>
                                </div>
                                <div class="backend-modal__body">
                                    <form id="create-transport-price" class="backend-form-grid" action="{{ route('admin.transports.prices.store') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        @include('backend.operations.transports.partials.price-fields', ['price' => null])
                                        <input name="transports_id" value="{{ $transport->id }}" type="hidden">
                                        <input name="author" value="{{ Auth::id() }}" type="hidden">
                                    </form>
                                </div>
                                <div class="backend-modal__footer">
                                    <button type="button" class="backend-button backend-button-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" form="create-transport-price" class="backend-button backend-button-primary">
                                        <i class="fa fa-plus"></i>
                                        Add Price
                                    </button>
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
