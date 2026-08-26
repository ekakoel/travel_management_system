@extends('layouts.head')

@section('title', __('messages.Partner'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/partners/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/partners/index.js') }}" defer></script>
@endpush

@section('content')
    @canany(['posDev','posAdm','posAuthor','posRsv'])
        @php
            $partnerSummary = [
                ['label' => 'Total Partners', 'value' => $partnerStats['total'] ?? 0, 'meta' => 'Registered partner profiles', 'icon' => 'fa fa-users', 'tone' => 'teal'],
                ['label' => 'Active', 'value' => $partnerStats['active'] ?? 0, 'meta' => 'Partners available for assignment', 'icon' => 'fa fa-check-circle', 'tone' => 'green'],
                ['label' => 'Draft', 'value' => $partnerStats['draft'] ?? 0, 'meta' => 'Partners waiting for review', 'icon' => 'fa fa-edit', 'tone' => 'amber'],
                ['label' => 'Transport', 'value' => $partnerStats['transport'] ?? 0, 'meta' => 'Transport provider profiles', 'icon' => 'fa fa-car', 'tone' => 'blue'],
            ];
        @endphp

        <div class="mobile-menu-overlay"></div>
        <main class="main-container partners-admin-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="partners-admin-hero"
                    eyebrow="Operations Resource"
                    title="Partner Manager"
                    description="Manage tourism service partners, contacts, service coverage, and operational assignment readiness."
                >
                <x-slot name="action">
                    <button type="button" class="backend-page-primary-action" data-toggle="modal" data-target="#partnerAddModal">
                        <i class="fa fa-plus"></i>
                        Add Partner
                    </button>
                </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar partners-admin-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Partner Manager</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ now()->format('d M Y') }}</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('invalid') || session()->has('error'))
                    <section class="backend-feedback partners-admin-feedback">
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

                        @if (session()->has('invalid') || session()->has('error'))
                            <div class="backend-alert backend-alert--danger">
                                <strong>{{ session('invalid') ?? session('error') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Partner summary">
                    @foreach ($partnerSummary as $stat)
                        <article class="backend-kpi-card backend-kpi-card--{{ $stat['tone'] }}">
                            <div class="backend-kpi-card__icon"><i class="{{ $stat['icon'] }}"></i></div>
                            <div>
                                <span>{{ $stat['label'] }}</span>
                                <strong>{{ number_format($stat['value']) }}</strong>
                                <small>{{ $stat['meta'] }}</small>
                            </div>
                        </article>
                    @endforeach
                </section>

                <section class="backend-filter-panel partners-admin-filter">
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">Search by name</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="partnerSearchName" class="backend-filter-control" type="search" placeholder="Search partner name" data-partner-filter="name">
                        </span>
                    </label>
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">Search by type</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="partnerSearchType" class="backend-filter-control" type="search" placeholder="Search partner type" data-partner-filter="type">
                        </span>
                    </label>
                </section>

                <section class="backend-panel partners-admin-panel">
                    <div class="backend-section-header partners-admin-panel__heading">
                        <div>
                            <span class="backend-section-header__label">Partner Directory</span>
                            <h2>All Partners</h2>
                        </div>
                        <p>Review contact details, service count, and partner status for operational assignment.</p>
                    </div>

                    <div class="backend-table-wrap partners-admin-table-wrap">
                        <table id="partnersAdminTable" class="backend-table partners-admin-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Type</th>
                                    <th>Services</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($partners as $partner)
                                    @php
                                        $coverUrl = $partner->coverUrl() ?: asset('frontend/img/business_partner.png');
                                        $serviceCount = (int) ($partner->activities_count ?? 0) + (int) ($partner->transports_count ?? 0);
                                    @endphp
                                    <tr data-partner-row data-partner-name="{{ strtolower($partner->name ?? '') }}" data-partner-type="{{ strtolower($partner->type ?? '') }}">
                                        <td data-label="No">{{ method_exists($partners, 'firstItem') ? $partners->firstItem() + $loop->index : $loop->iteration }}</td>
                                        <td data-label="Name">
                                            <div class="partners-admin-person">
                                                <img src="{{ $coverUrl }}" alt="{{ $partner->name }}">
                                                <div>
                                                    <strong>{{ $partner->name }}</strong>
                                                    <span>{{ $partner->location ?: '-' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Contact">
                                            <strong>{{ $partner->phone ?: '-' }}</strong>
                                            <span>{{ $partner->contact_person ?: '-' }}</span>
                                        </td>
                                        <td data-label="Type">
                                            <span class="backend-status-badge backend-status-badge--info partners-admin-type">{{ $partner->type ?: '-' }}</span>
                                        </td>
                                        <td data-label="Services">
                                            <strong>{{ number_format($serviceCount) }}</strong>
                                            <span>{{ $partner->status ?: '-' }}</span>
                                        </td>
                                        <td data-label="Action">
                                            <div class="backend-table-actions partners-admin-actions">
                                                <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#partnerDetail{{ $partner->id }}" aria-label="View {{ $partner->name }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#partnerEdit{{ $partner->id }}" aria-label="Edit {{ $partner->name }}">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </button>
                                                @can('posDev')
                                                    <form id="destroyPartner{{ $partner->id }}" action="{{ route('admin.partner.destroy', $partner->id) }}" method="post">
                                                        @csrf
                                                        @method('put')
                                                        <button type="submit" class="backend-icon-action is-danger" data-partner-delete="{{ $partner->name }}" aria-label="Delete {{ $partner->name }}">
                                                            <i class="fa fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="backend-table-empty">
                                                <i class="fa fa-user"></i>
                                                <strong>No partners found.</strong>
                                                <span>Add the first partner profile to start assigning partners to operations.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="backend-table-card-list partners-admin-card-list">
                        @forelse ($partners as $partner)
                            @php
                                $serviceCount = (int) ($partner->activities_count ?? 0) + (int) ($partner->transports_count ?? 0);
                            @endphp
                            <article class="backend-table-card partners-admin-card" data-partner-row data-partner-name="{{ strtolower($partner->name ?? '') }}" data-partner-type="{{ strtolower($partner->type ?? '') }}">
                                <div class="backend-table-card__header">
                                    <div>
                                        <span>Partner</span>
                                        <strong>{{ $partner->name }}</strong>
                                    </div>
                                    <span class="backend-status-badge backend-status-badge--info">{{ $partner->type ?: '-' }}</span>
                                </div>
                                <dl class="backend-table-card-grid">
                                    <div><dt>Phone</dt><dd>{{ $partner->phone ?: '-' }}</dd></div>
                                    <div><dt>Contact</dt><dd>{{ $partner->contact_person ?: '-' }}</dd></div>
                                    <div><dt>Location</dt><dd>{{ $partner->location ?: '-' }}</dd></div>
                                    <div><dt>Services</dt><dd>{{ number_format($serviceCount) }}</dd></div>
                                </dl>
                                <div class="backend-table-actions partners-admin-card__actions">
                                    <button type="button" class="backend-button backend-button-secondary" data-toggle="modal" data-target="#partnerDetail{{ $partner->id }}">View</button>
                                    @canany(['posDev','posAdm','posAuthor'])
                                        <button type="button" class="backend-button backend-button-primary" data-toggle="modal" data-target="#partnerEdit{{ $partner->id }}">Edit</button>
                                    @endcanany
                                </div>
                            </article>
                        @empty
                            <div class="backend-empty-state">
                                <i class="fa fa-user"></i>
                                <strong>No partners found.</strong>
                                <span>Add the first partner profile to start assigning partners to operations.</span>
                            </div>
                        @endforelse
                    </div>

                    @if (method_exists($partners, 'links') && $partners->hasPages())
                        <div class="backend-pagination partners-admin-pagination">
                            {{ $partners->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </main>

        @foreach ($partners as $partner)
            @php
                $coverUrl = $partner->coverUrl() ?: asset('frontend/img/business_partner.png');
                $serviceCount = (int) ($partner->activities_count ?? 0) + (int) ($partner->transports_count ?? 0);
            @endphp

            <div class="modal fade backend-modal partners-admin-modal" id="partnerDetail{{ $partner->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="backend-modal__header">
                            <div>
                                <span>Partner Detail</span>
                                <h3>{{ $partner->name }}</h3>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="backend-modal__body">
                            <div class="partners-admin-detail">
                                <aside class="partners-admin-profile">
                                    <img src="{{ $coverUrl }}" alt="{{ $partner->name }}">
                                    <strong>{{ $partner->name }}</strong>
                                    <span class="backend-status-badge backend-status-badge--info">{{ $partner->type ?: '-' }}</span>
                                    <small>{{ number_format($serviceCount) }} assigned services</small>
                                </aside>
                                <dl class="partners-admin-detail-grid">
                                    <div><dt>Phone</dt><dd>{{ $partner->phone ?: '-' }}</dd></div>
                                    <div><dt>Contact Person</dt><dd>{{ $partner->contact_person ?: '-' }}</dd></div>
                                    <div><dt>Location</dt><dd>{{ $partner->location ?: '-' }}</dd></div>
                                    <div><dt>Status</dt><dd>{{ $partner->status ?: '-' }}</dd></div>
                                    <div><dt>Created</dt><dd>{{ optional($partner->created_at)->format('d M Y H:i') ?: '-' }}</dd></div>
                                    <div><dt>Updated</dt><dd>{{ optional($partner->updated_at)->format('d M Y H:i') ?: '-' }}</dd></div>
                                    <div class="is-wide"><dt>Address</dt><dd>{!! $partner->address ?: '-' !!}</dd></div>
                                    <div class="is-wide">
                                        <dt>Map</dt>
                                        <dd>
                                            @if (filter_var($partner->map, FILTER_VALIDATE_URL))
                                                <a href="{{ $partner->map }}" target="_blank" rel="noopener">{{ $partner->map }}</a>
                                            @else
                                                {{ $partner->map ?: '-' }}
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="is-wide"><dt>Description</dt><dd>{{ trim(strip_tags((string) $partner->description)) ?: '-' }}</dd></div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @canany(['posDev','posAdm','posAuthor'])
                <div class="modal fade backend-modal partners-admin-modal" id="partnerEdit{{ $partner->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <div class="backend-modal__header">
                                <div>
                                    <span>Edit Partner</span>
                                    <h3>{{ $partner->name }}</h3>
                                </div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="updatePartner{{ $partner->id }}" action="{{ route('admin.partner.edit', $partner->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="backend-modal__body">
                                    @include('backend.operations.partners.partials.form', ['partner' => $partner])
                                </div>
                            </form>
                            <div class="backend-modal__footer">
                                <button type="submit" form="updatePartner{{ $partner->id }}" class="backend-button backend-button-primary">
                                    <i class="fa fa-check"></i>
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endcanany
        @endforeach

        @canany(['posDev','posAdm','posAuthor'])
            <div class="modal fade backend-modal partners-admin-modal" id="partnerAddModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="backend-modal__header">
                            <div>
                                <span>Add Partner</span>
                                <h3>Create partner profile</h3>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="addPartner" method="post" action="{{ route('admin.partner.create') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="backend-modal__body">
                                @include('backend.operations.partners.partials.form', ['partner' => null])
                            </div>
                        </form>
                        <div class="backend-modal__footer">
                            <button type="submit" form="addPartner" class="backend-button backend-button-primary">
                                <i class="fa fa-check"></i>
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endcanany
    @endcanany
@endsection
