@extends('layouts.head')

@section('title', __('messages.Guide'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/guides/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/guides/index.js') }}" defer></script>
@endpush

@section('content')
    @canany(['posDev','posAdm','posAuthor','posRsv'])
        @php
            $guideCount = $guides->count();
            $reviewedCount = $guides->filter(fn ($guide) => (float) ($guide->global_rating ?? 0) > 0)->count();
            $mandarinCount = $guides->filter(fn ($guide) => str_contains(strtolower($guide->language ?? ''), 'mandarin'))->count();
            $englishCount = $guides->filter(fn ($guide) => str_contains(strtolower($guide->language ?? ''), 'english'))->count();
            $guideSummary = [
                ['label' => 'Total Guides', 'value' => $guideCount, 'meta' => 'Registered guide profiles', 'icon' => 'fa fa-users', 'tone' => 'teal'],
                ['label' => 'Reviewed', 'value' => $reviewedCount, 'meta' => 'Guides with review score', 'icon' => 'fa fa-star', 'tone' => 'amber'],
                ['label' => 'Mandarin', 'value' => $mandarinCount, 'meta' => 'Mandarin language guides', 'icon' => 'fa fa-language', 'tone' => 'blue'],
                ['label' => 'English', 'value' => $englishCount, 'meta' => 'English language guides', 'icon' => 'fa fa-comments', 'tone' => 'green'],
            ];
        @endphp

        <div class="mobile-menu-overlay"></div>
        <main class="main-container guides-admin-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="guides-admin-hero"
                    eyebrow="Operations Resource"
                    title="Guide Manager"
                    description="Manage tour guide profiles, contact details, language capabilities, and review performance used by reservations, orders, and review workflows."
                >
                <x-slot name="action">
                    <button type="button" class="backend-page-primary-action" data-toggle="modal" data-target="#guideAddModal">
                        <i class="fa fa-plus"></i>
                        Add Guide
                    </button>
                </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar guides-admin-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Guide Manager</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ $now->format('d M Y') }}</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('invalid') || session()->has('error'))
                    <section class="backend-feedback guides-admin-feedback">
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

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Guide summary">
                    @foreach ($guideSummary as $stat)
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

                <section class="backend-filter-panel guides-admin-filter">
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">Search by name</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="guideSearchName" class="backend-filter-control" type="search" placeholder="Search guide name" data-guide-filter="name">
                        </span>
                    </label>
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">Search by language</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="guideSearchLanguage" class="backend-filter-control" type="search" placeholder="Search language" data-guide-filter="language">
                        </span>
                    </label>
                </section>

                <section class="backend-panel guides-admin-panel">
                    <div class="backend-section-header guides-admin-panel__heading">
                        <div>
                            <span class="backend-section-header__label">Guide Directory</span>
                            <h2>All Guides</h2>
                        </div>
                        <p>Review contact details, rating performance, and language coverage for operational assignment.</p>
                    </div>

                    <div class="backend-table-wrap guides-admin-table-wrap">
                        <table id="guidesAdminTable" class="backend-table guides-admin-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Language</th>
                                    <th>Rating</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($guides as $no => $guide)
                                    @php
                                        $avg = $guide->averageRating();
                                        $summary = $guide->reviewSummary();
                                        $rating = (float) ($summary['global_rating'] ?? 0);
                                        $fullStars = (int) floor($rating);
                                        $halfStar = ($rating - $fullStars) >= 0.5;
                                        $emptyStars = max(0, 5 - $fullStars - ($halfStar ? 1 : 0));
                                        $avatar = $guide->sex === 'f'
                                            ? asset('storage/user/profile/default_user_female_img.png')
                                            : asset('storage/user/profile/default_user_img.png');
                                    @endphp
                                    <tr data-guide-row data-guide-name="{{ strtolower($guide->name ?? '') }}" data-guide-language="{{ strtolower($guide->language ?? '') }}">
                                        <td data-label="No">{{ $loop->iteration }}</td>
                                        <td data-label="Name">
                                            <div class="guides-admin-person">
                                                <img src="{{ $avatar }}" alt="{{ $guide->name }}">
                                                <div>
                                                    <strong>{{ $guide->name }}</strong>
                                                    <span>{{ $guide->country ?: '-' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Contact">
                                            <strong>{{ $guide->phone ?: '-' }}</strong>
                                            <span>{{ $guide->email ?: '-' }}</span>
                                        </td>
                                        <td data-label="Language">
                                            <span class="backend-status-badge backend-status-badge--info guides-admin-language">{{ $guide->language ?: '-' }}</span>
                                        </td>
                                        <td data-label="Rating">
                                            <div class="guides-admin-rating" aria-label="Guide rating {{ number_format($rating, 1) }}">
                                                @for ($i = 0; $i < $fullStars; $i++)
                                                    <i class="fa fa-star" aria-hidden="true"></i>
                                                @endfor
                                                @if ($halfStar)
                                                    <i class="fa fa-star-half-o" aria-hidden="true"></i>
                                                @endif
                                                @for ($i = 0; $i < $emptyStars; $i++)
                                                    <i class="fa fa-star-o" aria-hidden="true"></i>
                                                @endfor
                                                <strong>{{ number_format($rating, 1) }}</strong>
                                                <span>{{ number_format($summary['count'] ?? 0) }} reviews</span>
                                            </div>
                                        </td>
                                        <td data-label="Action">
                                            <div class="backend-table-actions guides-admin-actions">
                                                <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#guideDetail{{ $guide->id }}" aria-label="View {{ $guide->name }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="backend-icon-action" data-toggle="modal" data-target="#guideEdit{{ $guide->id }}" aria-label="Edit {{ $guide->name }}">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </button>
                                                @canany(['posDev'])
                                                    <form id="destroyGuide{{ $guide->id }}" action="{{ route('admin.guide.destroy', $guide->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="backend-icon-action is-danger" data-guide-delete="{{ $guide->name }}" aria-label="Delete {{ $guide->name }}">
                                                            <i class="fa fa-trash-alt"></i>
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
                                                <i class="fa fa-user"></i>
                                                <strong>No guides found.</strong>
                                                <span>Add the first guide profile to start assigning guides to operations.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="backend-table-card-list guides-admin-card-list">
                        @forelse ($guides as $guide)
                            @php
                                $summary = $guide->reviewSummary();
                                $rating = (float) ($summary['global_rating'] ?? 0);
                            @endphp
                            <article class="backend-table-card guides-admin-card" data-guide-row data-guide-name="{{ strtolower($guide->name ?? '') }}" data-guide-language="{{ strtolower($guide->language ?? '') }}">
                                <div class="backend-table-card__header">
                                    <div>
                                        <span>Guide</span>
                                        <strong>{{ $guide->name }}</strong>
                                    </div>
                                    <span class="backend-status-badge backend-status-badge--info">{{ $guide->language ?: '-' }}</span>
                                </div>
                                <dl class="backend-table-card-grid">
                                    <div>
                                        <dt>Phone</dt>
                                        <dd>{{ $guide->phone ?: '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Email</dt>
                                        <dd>{{ $guide->email ?: '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Country</dt>
                                        <dd>{{ $guide->country ?: '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Rating</dt>
                                        <dd>{{ number_format($rating, 1) }} / 5</dd>
                                    </div>
                                </dl>
                                <div class="backend-table-actions guides-admin-card__actions">
                                    <button type="button" class="backend-button backend-button-secondary" data-toggle="modal" data-target="#guideDetail{{ $guide->id }}">View</button>
                                    @canany(['posDev','posAuthor'])
                                        <button type="button" class="backend-button backend-button-primary" data-toggle="modal" data-target="#guideEdit{{ $guide->id }}">Edit</button>
                                    @endcanany
                                </div>
                            </article>
                        @empty
                            <div class="backend-empty-state">
                                <i class="fa fa-user"></i>
                                <strong>No guides found.</strong>
                                <span>Add the first guide profile to start assigning guides to operations.</span>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </main>

        @foreach ($guides as $guide)
            @php
                $avg = $guide->averageRating();
                $summary = $guide->reviewSummary();
                $rating = (float) ($summary['global_rating'] ?? 0);
                $avatar = $guide->sex === 'f'
                    ? asset('storage/user/profile/default_user_female_img.png')
                    : asset('storage/user/profile/default_user_img.png');
            @endphp

            <div class="modal fade backend-modal guides-admin-modal" id="guideDetail{{ $guide->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="backend-modal__header">
                            <div>
                                <span>Guide Detail</span>
                                <h3>{{ $guide->name }}</h3>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="backend-modal__body">
                            <div class="guides-admin-detail">
                                <aside class="guides-admin-profile">
                                    <img src="{{ $avatar }}" alt="{{ $guide->name }}">
                                    <strong>{{ $guide->name }}</strong>
                                    <span class="backend-status-badge backend-status-badge--info">{{ $guide->language ?: '-' }}</span>
                                    <small>{{ number_format($rating, 1) }} / 5 from {{ number_format($summary['count'] ?? 0) }} reviews</small>
                                </aside>
                                <dl class="guides-admin-detail-grid">
                                    <div><dt>Phone</dt><dd>{{ $guide->phone ?: '-' }}</dd></div>
                                    <div><dt>Email</dt><dd>{{ $guide->email ?: '-' }}</dd></div>
                                    <div><dt>Country</dt><dd>{{ $guide->country ?: '-' }}</dd></div>
                                    <div><dt>Address</dt><dd>{!! $guide->address ?: '-' !!}</dd></div>
                                    <div><dt>Attitude</dt><dd>{{ number_format((float) ($avg->attitude ?? 0), 1) }} ★</dd></div>
                                    <div><dt>Knowledge</dt><dd>{{ number_format((float) ($avg->knowledge ?? 0), 1) }} ★</dd></div>
                                    <div><dt>Explanation</dt><dd>{{ number_format((float) ($avg->explanation ?? 0), 1) }} ★</dd></div>
                                    <div><dt>Time Control</dt><dd>{{ number_format((float) ($avg->time_control ?? 0), 1) }} ★</dd></div>
                                    <div><dt>Neatness</dt><dd>{{ number_format((float) ($avg->guide_neatness ?? 0), 1) }} ★</dd></div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade backend-modal guides-admin-modal" id="guideEdit{{ $guide->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="backend-modal__header">
                            <div>
                                <span>Edit Guide</span>
                                <h3>{{ $guide->name }}</h3>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="updateGuide{{ $guide->id }}" action="{{ route('admin.guide.edit', $guide->id) }}" method="post">
                            @csrf
                            <div class="backend-modal__body">
                                @include('backend.operations.guides.partials.form', ['guide' => $guide])
                            </div>
                        </form>
                        <div class="backend-modal__footer">
                            <button type="submit" form="updateGuide{{ $guide->id }}" class="backend-button backend-button-primary">
                                <i class="fa fa-check"></i>
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="modal fade backend-modal guides-admin-modal" id="guideAddModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="backend-modal__header">
                        <div>
                            <span>Add Guide</span>
                            <h3>Create guide profile</h3>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="addGuide" method="post" action="{{ route('admin.guide.create') }}">
                        @csrf
                        <div class="backend-modal__body">
                            @include('backend.operations.guides.partials.form', ['guide' => null])
                        </div>
                    </form>
                    <div class="backend-modal__footer">
                        <button type="submit" form="addGuide" class="backend-button backend-button-primary">
                            <i class="fa fa-check"></i>
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endcanany
@endsection
