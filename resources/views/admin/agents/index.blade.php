@extends('layouts.head')

@section('title', __('messages.Agent'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/agents/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/agents/index.js') }}" defer></script>
@endpush

@section('content')

    @canany(['posDev','posAdm'])

        <div class="mobile-menu-overlay"></div>
        <main class="main-container agents-admin-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero title="Agent Manager" description="Manage Bali Kami partner applications and Agent accounts."/>
                <section class="backend-page-toolbar drivers-admin-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">Provider</li>
                            <li class="breadcrumb-item active" aria-current="page">Driver Manager</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ $now->format('d M Y') }}</span>
                    </div>
                </section>
                @if (session('success'))
                    <div class="backend-feedback backend-feedback--success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="backend-feedback backend-feedback--error">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- ==========================================================
                    KPI
                =========================================================== --}}
                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="Agent verification summary">
                    <article class="backend-kpi-card backend-kpi-card--teal">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-users" aria-hidden="true"></i>
                        </div>
                        <div>
                            <span>Total Agents</span>
                            <strong>{{ $totalAgents }}</strong>
                            <small>Registered business name</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--blue">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                        </div>
                        <div>
                            <span>Pending</span>
                            <strong>{{ $pendingAgents }}</strong>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--green">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-check-square-o" aria-hidden="true"></i>
                        </div>
                        <div>
                            <span>Verified</span>
                            <strong>{{ $verifiedAgents }}</strong>
                            <small>Submitted document records</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--red">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-ban" aria-hidden="true"></i>
                        </div>
                        <div>
                            <span>Rejected</span>
                        <strong>{{ $rejectedAgents }}</strong>
                            <small>Agent login email</small>
                        </div>
                    </article>
                </section>

                {{-- Filter --}}
                <section class="backend-filter-panel drivers-admin-filter">
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">Search by Company</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="agent-company-filter" class="backend-filter-control" type="search" placeholder="Search company name" data-agent-filter="company">
                        </span>
                    </label>
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">Search by Contact Person</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="agent-contact-filter" class="backend-filter-control" type="search" placeholder="Search license" data-agent-filter="contact">
                        </span>
                    </label>
                </section>

                {{-- Desktop Table --}}

                <section class="backend-panel agents-admin-panel">

                    <div class="agents-admin-panel__heading">
                        <strong>Agent Applications</strong>
                    </div>

                    <div class="backend-table-wrap agents-admin-table-wrap">

                        <table class="backend-table agents-admin-table">

                            <thead>

                                <tr>
                                    <th>No</th>
                                    <th>Company</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>

                            </thead>

                            <tbody>

                                @forelse ($agents as $agent)

                                    <tr
                                        data-agent-row
                                        data-company="{{ $agent->company_name ?: $agent->name }}"
                                        data-contact="{{ $agent->contact_name ?: $agent->pic_name }}"
                                        data-status="{{ $agent->status }}"
                                    >

                                        <td>
                                            {{ $loop->iteration }}
                                        </td>

                                        <td>

                                            <div class="agents-admin-person">

                                                <div>

                                                    <strong>
                                                        {{ $agent->company_name ?: $agent->name ?: '-' }}
                                                    </strong>

                                                    <span>
                                                        {{ $agent->company_type ?: '-' }}
                                                    </span>

                                                </div>

                                            </div>

                                        </td>

                                        <td>

                                            <div>

                                                <strong>
                                                    {{ $agent->contact_name ?: $agent->pic_name ?: '-' }}
                                                </strong>

                                                @if ($agent->position)

                                                    <span>
                                                        {{ $agent->position }}
                                                    </span>

                                                @endif

                                            </div>

                                        </td>

                                        <td>
                                            {{ $agent->contact_email ?: ($agent->user?->email ?? '-') }}
                                        </td>

                                        <td>
                                            {{ $agent->phone ?: '-' }}
                                        </td>

                                        <td>

                                            @if ($agent->status === 'pending')

                                                <span class="backend-status backend-status--warning">
                                                    Pending
                                                </span>

                                            @elseif ($agent->status === 'verified')

                                                <span class="backend-status backend-status--success">
                                                    Verified
                                                </span>

                                            @elseif ($agent->status === 'rejected')

                                                <span class="backend-status backend-status--danger">
                                                    Rejected
                                                </span>

                                            @else

                                                <span class="backend-status">
                                                    {{ ucfirst($agent->status ?: 'Unknown') }}
                                                </span>

                                            @endif

                                        </td>

                                        <td>

                                            <div class="backend-table-actions">

                                                <a
                                                    href="{{ route('admin.agents.show', $agent->id) }}"
                                                    class="backend-icon-action"
                                                    title="View Detail"
                                                >
                                                    <i class="dw dw-eye"></i>
                                                </a>

                                                @if ($agent->status === 'pending')

                                                    <a
                                                        href="{{ route('admin.agents.verification', $agent->id) }}"
                                                        class="backend-icon-action"
                                                        title="Verification"
                                                    >
                                                        <i class="dw dw-check"></i>
                                                    </a>

                                                @endif

                                            </div>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="7">

                                            <div class="backend-empty-state">
                                                No Agent applications found.
                                            </div>

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    {{-- Mobile Cards --}}

                    <div class="backend-table-card-list agents-admin-card-list">

                        @forelse ($agents as $agent)

                            <article
                                class="backend-table-card"
                                data-agent-row
                                data-company="{{ $agent->company_name ?: $agent->name }}"
                                data-contact="{{ $agent->contact_name ?: $agent->pic_name }}"
                                data-status="{{ $agent->status }}"
                            >

                                <div class="backend-table-card__body">

                                    <div>
                                        <small>Company</small>
                                        <strong>
                                            {{ $agent->company_name ?: $agent->name ?: '-' }}
                                        </strong>
                                    </div>

                                    <div>
                                        <small>Contact</small>
                                        <strong>
                                            {{ $agent->contact_name ?: $agent->pic_name ?: '-' }}
                                        </strong>
                                    </div>

                                    <div>
                                        <small>Email</small>
                                        <strong>
                                            {{ $agent->contact_email ?: ($agent->user?->email ?? '-') }}
                                        </strong>
                                    </div>

                                    <div>
                                        <small>Phone</small>
                                        <strong>
                                            {{ $agent->phone ?: '-' }}
                                        </strong>
                                    </div>

                                    <div>
                                        <small>Status</small>

                                        @if ($agent->status === 'pending')

                                            <span class="backend-status backend-status--warning">
                                                Pending
                                            </span>

                                        @elseif ($agent->status === 'verified')

                                            <span class="backend-status backend-status--success">
                                                Verified
                                            </span>

                                        @elseif ($agent->status === 'rejected')

                                            <span class="backend-status backend-status--danger">
                                                Rejected
                                            </span>

                                        @endif

                                    </div>

                                </div>

                                <div class="backend-table-card__actions agents-admin-card__actions">

                                    <a
                                        href="{{ route('admin.agents.show', $agent->id) }}"
                                        class="ui-btn"
                                    >
                                        View Detail
                                    </a>

                                    @if ($agent->status === 'pending')

                                        <a
                                            href="{{ route('admin.agents.verification', $agent->id) }}"
                                            class="ui-btn"
                                        >
                                            Verification
                                        </a>

                                    @endif

                                </div>

                            </article>

                        @empty

                            <div class="backend-empty-state">
                                No Agent applications found.
                            </div>

                        @endforelse

                    </div>

                </section>

            </div>

        </main>

    @endcanany

@endsection