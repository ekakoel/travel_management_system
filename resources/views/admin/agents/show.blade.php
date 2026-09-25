@extends('layouts.head')

@section('title', 'Agent Detail')

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/agents/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/agents/index.js') }}" defer></script>
@endpush

@section('content')
    @canany(['posDev', 'posAuthor', 'posRsv', 'posAdm'])

        @php
            $companyName = $agent->company_name ?: ($agent->name ?: '-');
            $contactName = $agent->contact_name ?: ($agent->pic_name ?: '-');
            $contactEmail = $agent->contact_email ?: ($agent->email ?: '-');
            $companyAddress = $agent->company_address ?: ($agent->Address ?: '-');

            $status = strtolower($agent->status ?: 'pending');

            $statusLabel = match ($status) {
                'verified' => 'Verified',
                'rejected' => 'Rejected',
                'pending' => 'Pending',
                default => ucfirst($status),
            };

            $statusTone = match ($status) {
                'verified' => 'active',
                'rejected' => 'inactive',
                default => 'warning',
            };

            $interestedServices = $agent->interested_services;

            if (is_string($interestedServices)) {
                $decodedServices = json_decode($interestedServices, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $interestedServices = $decodedServices;
                }
            }

            $interestedServices = is_array($interestedServices)
                ? $interestedServices
                : (filled($interestedServices) ? [$interestedServices] : []);

            $translationDocuments = $agent->translation_documents;

            if (is_string($translationDocuments)) {
                $decodedDocuments = json_decode($translationDocuments, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $translationDocuments = $decodedDocuments;
                }
            }

            $translationDocuments = is_array($translationDocuments)
                ? $translationDocuments
                : [];

            $documents = collect();

            if (method_exists($agent, 'documents')) {
                $documents = $agent->relationLoaded('documents')
                    ? $agent->documents
                    : $agent->documents()->get();
            }

            $documentCount = $documents->count();

            $userStatus = $agent->user?->status ?: '-';

            $userApprovalStatus = $agent->user?->is_approved
                ? 'Approved'
                : 'Not Approved';

            $approvedAt = $agent->user?->approved_at
                ? \Illuminate\Support\Carbon::parse($agent->user->approved_at)
                : null;

            $createdAt = $agent->created_at
                ? \Illuminate\Support\Carbon::parse($agent->created_at)
                : null;

            $updatedAt = $agent->updated_at
                ? \Illuminate\Support\Carbon::parse($agent->updated_at)
                : null;
        @endphp

        <div class="mobile-menu-overlay"></div>

        <main class="main-container agent-detail-page">
            <div class="pd-ltr-20">

                {{-- =========================================================
                    HERO
                ========================================================== --}}
                <x-backend.page-hero
                    class="agent-detail-hero"
                    eyebrow="Operations Resource"
                    title="{{ $companyName }}"
                    description="Review Agent profile, business information, verification status, documents, and account details."
                >
                </x-backend.page-hero>


                {{-- =========================================================
                    TOOLBAR
                ========================================================== --}}
                <section class="backend-page-toolbar agent-detail-toolbar">

                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">

                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.panel-main.view') }}">
                                    Admin Panel
                                </a>
                            </li>

                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.agents.index') }}">
                                    Agents
                                </a>
                            </li>

                            <li
                                class="breadcrumb-item active"
                                aria-current="page"
                            >
                                {{ $companyName }}
                            </li>

                        </ol>
                    </nav>

                    <div class="backend-page-toolbar__actions">

                        <span class="backend-status-badge backend-status-badge--{{ $statusTone }}">
                            {{ $statusLabel }}
                        </span>

                        @if ($agent->user)
                            <span class="backend-status-badge backend-status-badge--info">
                                Account Linked
                            </span>
                        @else
                            <span class="backend-status-badge backend-status-badge--warning">
                                No Account
                            </span>
                        @endif

                    </div>

                </section>


                {{-- =========================================================
                    FEEDBACK
                ========================================================== --}}
                @if ($errors->any() || session()->has('success') || session()->has('error'))
                    <section class="backend-feedback agent-detail-feedback">

                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger">

                                <strong>
                                    Action needs attention.
                                </strong>

                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>

                            </div>
                        @endif

                        @if (session()->has('success'))
                            <div class="backend-alert backend-alert--success">
                                <strong>
                                    {{ session('success') }}
                                </strong>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="backend-alert backend-alert--danger">
                                <strong>
                                    {{ session('error') }}
                                </strong>
                            </div>
                        @endif

                    </section>
                @endif


                {{-- =========================================================
                    KPI
                ========================================================== --}}
                <section
                    class="backend-kpi-grid backend-kpi-grid--4"
                    aria-label="Agent detail summary"
                >

                    <article class="backend-kpi-card backend-kpi-card--{{ $statusTone }}">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-user-check"></i>
                        </div>

                        <div>
                            <span>Status</span>
                            <strong>{{ $statusLabel }}</strong>
                            <small>Current Agent verification state.</small>
                        </div>
                    </article>


                    <article class="backend-kpi-card backend-kpi-card--info">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-file-alt"></i>
                        </div>

                        <div>
                            <span>Documents</span>
                            <strong>{{ number_format($documentCount) }}</strong>
                            <small>Submitted Agent documents.</small>
                        </div>
                    </article>


                    <article class="backend-kpi-card backend-kpi-card--success">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-id-card"></i>
                        </div>

                        <div>
                            <span>Account</span>
                            <strong>
                                {{ $agent->user ? 'Linked' : 'Missing' }}
                            </strong>
                            <small>
                                {{ $userApprovalStatus }}
                            </small>
                        </div>
                    </article>


                    <article class="backend-kpi-card backend-kpi-card--warning">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-calendar-check"></i>
                        </div>

                        <div>
                            <span>Approved At</span>

                            <strong>
                                {{ $approvedAt?->format('d M Y') ?? '-' }}
                            </strong>

                            <small>
                                {{ $approvedAt?->format('H:i') ?? 'Not approved yet' }}
                            </small>
                        </div>
                    </article>

                </section>


                {{-- =========================================================
                    DETAIL LAYOUT
                ========================================================== --}}
                <x-backend.detail-layout class="agent-detail-layout">

                    {{-- =====================================================
                        MAIN
                    ====================================================== --}}
                    <x-slot name="main">


                        
                                {{-- VERIFICATION --}}
                                <section class="backend-panel agent-detail-verification-panel">

                                    <div class="backend-section-header agent-detail-panel__heading">

                                        <div>
                                            <span class="backend-section-header__label">
                                                Verification
                                            </span>

                                            <h2>
                                                Verification Status
                                            </h2>

                                            <p>
                                                Current operational verification state of this Agent account.
                                            </p>
                                        </div>

                                    </div>


                                    <div class="agent-detail-verification-summary">

                                        <div>
                                            <span>Status</span>

                                            <strong>
                                                <span class="backend-status-badge backend-status-badge--{{ $statusTone }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </strong>

                                            <small>
                                                Current Agent verification state.
                                            </small>
                                        </div>


                                        <div>
                                            <span>Approved At</span>

                                            <strong>
                                                {{ $approvedAt?->format('d M Y H:i') ?? '-' }}
                                            </strong>

                                            <small>
                                                {{ $approvedAt ? 'Agent approval timestamp.' : 'Agent has not been approved.' }}
                                            </small>
                                        </div>


                                        <div>
                                            <span>Account</span>

                                            <strong>
                                                {{ $userApprovalStatus }}
                                            </strong>

                                            <small>
                                                Linked user account approval state.
                                            </small>
                                        </div>

                                    </div>


                                    @if ($status === 'rejected' && filled($agent->rejection_reason))

                                        <div class="backend-alert backend-alert--danger">

                                            <strong>
                                                Rejection Reason
                                            </strong>

                                            <p>
                                                {{ $agent->rejection_reason }}
                                            </p>

                                        </div>

                                    @endif


                                    @if ($status === 'pending')

                                        <div class="agent-detail-verification-actions">

                                            <a
                                                href="{{ route('admin.agents.verification', $agent->id) }}"
                                                class="backend-page-primary-action"
                                            >
                                                <i class="fa fa-shield-alt"></i>
                                                Open Verification
                                            </a>

                                        </div>

                                    @endif

                                </section>
                        {{-- =================================================
                            AGENT PROFILE
                        ================================================== --}}
                        <section class="backend-panel agent-detail-panel">
                            <div class="backend-section-header agent-detail-panel__heading m-b-18">
                                <div>
                                    <span class="backend-section-header__label">
                                        Agent Profile
                                    </span>
                                    <h2>
                                        Detail Information
                                    </h2>
                                </div>
                            </div>


                            
                            <div class="agent-detail-summary">

                                {{-- PROFILE SUMMARY --}}
                                <article class="backend-table-card agent-detail-info-card m-b-18">
                                    <div class="backend-table-card__header">
                                        <div>
                                            <span class="backend-table-card__label">
                                                Busines Information
                                            </span>
                                            <strong>
                                                Busines Profile
                                            </strong>
                                        </div>
                                        <span class="backend-status-badge backend-status-badge--{{ $statusTone }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    <dl class="backend-table-card-grid">
                                        <div>
                                            <dt>Busines Name</dt>
                                            <dd>
                                                {{ $companyName }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt>Type</dt>
                                            <dd>
                                                {{ $companyType ?: '-' }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt>License Number</dt>
                                            <dd>
                                                {{ $agent->business_license_number ?: ($agent->registration_number ?: '-') }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt>Address</dt>
                                            <dd>
                                                {{ $companyAddress }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt>Country</dt>
                                            <dd>
                                                {{ $agent->country ?: '-' }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt>Website</dt>
                                            <dd>
                                                {{ $agent->website ?: '-' }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt>Registered</dt>
                                            <dd>
                                                {{ $createdAt?->format('d M Y H:i') ?? '-' }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt>Created At</dt>
                                            <dd>
                                                {{ $createdAt?->format('d M Y H:i') ?? '-' }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt>Updated At</dt>
                                            <dd>
                                                {{ $updatedAt?->format('d M Y H:i') ?? '-' }}
                                            </dd>
                                        </div>
                                    </dl>
                                </article>

                                {{-- REGISTERED BY --}}
                                <article class="backend-table-card agent-detail-content-block m-b-18">
                                    <div class="backend-table-card__header">
                                        <div>
                                            <span class="backend-table-card__label">
                                                Contact Person
                                            </span>
                                            <strong>
                                                Registration Details
                                            </strong>
                                        </div>
                                    </div>
                                    <dl class="backend-table-card-grid">
                                        <div>
                                            <dt>Name</dt>
                                            <dd>
                                                {{ $contactName }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt>Email</dt>
                                            <dd>
                                                {{ $contactEmail }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt>Phone</dt>
                                            <dd>
                                                {{ $agent->phone ?: ($agent->contact_phone ?: '-') }}
                                            </dd>
                                        </div>
                                        
                                    </dl>
                                </article>
                                {{-- INTERESTED SERVICES --}}
                                <article class="backend-table-card agent-detail-content-block m-b-18">
                                    <div class="backend-table-card__header">
                                        <div>
                                            <span class="backend-table-card__label">
                                                Services
                                            </span>
                                            <strong>
                                                Interested Services
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="agent-detail-richtext">
                                        @if (empty($interestedServices))
                                            <p>
                                                No interested services have been submitted.
                                            </p>
                                        @else
                                            <div class="agent-detail-service-list">
                                                @foreach ($interestedServices as $service)
                                                    <span class="backend-status-badge backend-status-badge--info">
                                                        {{ is_array($service) ? ($service['name'] ?? '-') : $service }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </article>
                                {{-- DOCUMENTS --}}
                                <article class="backend-table-card agent-detail-content-block m-b-18">
                                    <div class="backend-table-card__header">
                                        <div>
                                            <span class="backend-table-card__label">
                                                Documents
                                            </span>
                                            <strong>
                                                Submitted Documents
                                            </strong>
                                        </div>
                                        <span class="backend-status-badge backend-status-badge--info">
                                            {{ number_format($documentCount) }} document{{ $documentCount === 1 ? '' : 's' }}
                                        </span>
                                    </div>


                                    <div class="agent-detail-document-list">
                                        @forelse ($documents as $document)
                                            <div class="agent-detail-document-item">
                                                
                                                <div class="agent-detail-document-item__content">
                                                    <i class="fa fa-file-alt"> </i>
                                                    <strong>
                                                        {{ ucwords(str_replace('_', ' ', $document->document_type)) }}
                                                    </strong>
                                                    <small>
                                                        {{ $document->original_filename ?: 'Document' }}
                                                    </small>

                                                </div>

                                                <div class="agent-detail-document-item__action">

                                                    <a href="{{ route('admin.agents.document', [ 'id' => $agent->id, 'type' => 'business_license', ]) }}" target="_blank" rel="noopener" class="backend-button backend-button-secondary" > <i class="fa fa-eye"></i> View </a>

                                                </div>

                                            </div>

                                        @empty

                                            <div class="backend-table-empty">

                                                <i class="fa fa-file-alt"></i>

                                                <strong>
                                                    No documents.
                                                </strong>

                                                <span>
                                                    No Agent documents have been submitted.
                                                </span>

                                            </div>

                                        @endforelse

                                    </div>

                                </article>


                                {{-- LEGACY / FALLBACK DOCUMENTS --}}
                                @if (
                                    $documents->isEmpty() &&
                                    (
                                        filled($agent->business_license) ||
                                        filled($agent->company_letter) ||
                                        filled($agent->tax_document) ||
                                        !empty($translationDocuments)
                                    )
                                )

                                    <article class="backend-table-card agent-detail-content-block">

                                        <div class="backend-table-card__header">

                                            <div>
                                                <span class="backend-table-card__label">
                                                    Documents
                                                </span>

                                                <strong>
                                                    Submitted Files
                                                </strong>
                                            </div>

                                        </div>


                                        <div class="agent-detail-document-list">

                                            @if ($agent->business_license)

                                                <div class="agent-detail-document-item">

                                                    <div class="agent-detail-document-item__icon">
                                                        <i class="fa fa-file-pdf"></i>
                                                    </div>

                                                    <div class="agent-detail-document-item__content">

                                                        <strong>
                                                            Business License
                                                        </strong>

                                                        <small>
                                                            Business registration document
                                                        </small>

                                                    </div>

                                                    <div class="agent-detail-document-item__action">

                                                        <button
                                                            type="button"
                                                            class="backend-button backend-button-secondary"
                                                            data-toggle="modal"
                                                            data-target="#agentDocumentModal"
                                                            data-document-url="{{ route('admin.agents.document', [
                                                                'id' => $agent->id,
                                                                'type' => 'business_license',
                                                            ]) }}"
                                                            data-document-title="Business License"
                                                        >
                                                            <i class="fa fa-eye"></i>
                                                            View
                                                        </button>

                                                    </div>

                                                </div>

                                            @endif


                                            @if ($agent->company_letter)

                                                <div class="agent-detail-document-item">

                                                    <div class="agent-detail-document-item__icon">
                                                        <i class="fa fa-file-pdf"></i>
                                                    </div>

                                                    <div class="agent-detail-document-item__content">

                                                        <strong>
                                                            Company Letter
                                                        </strong>

                                                        <small>
                                                            Company supporting document
                                                        </small>

                                                    </div>

                                                    <div class="agent-detail-document-item__action">

                                                        <button
                                                            type="button"
                                                            class="backend-button backend-button-secondary"
                                                            data-toggle="modal"
                                                            data-target="#agentDocumentModal"
                                                            data-document-url="{{ route('admin.agents.document', [
                                                                'id' => $agent->id,
                                                                'type' => 'company_letter',
                                                            ]) }}"
                                                            data-document-title="Company Letter"
                                                        >
                                                            <i class="fa fa-eye"></i>
                                                            View
                                                        </button>

                                                    </div>

                                                </div>

                                            @endif


                                            @if ($agent->tax_document)

                                                <div class="agent-detail-document-item">

                                                    <div class="agent-detail-document-item__icon">
                                                        <i class="fa fa-file-pdf"></i>
                                                    </div>

                                                    <div class="agent-detail-document-item__content">

                                                        <strong>
                                                            Tax Document
                                                        </strong>

                                                        <small>
                                                            Tax registration document
                                                        </small>

                                                    </div>

                                                    <div class="agent-detail-document-item__action">

                                                        <button
                                                            type="button"
                                                            class="backend-button backend-button-secondary"
                                                            data-toggle="modal"
                                                            data-target="#agentDocumentModal"
                                                            data-document-url="{{ route('admin.agents.document', [
                                                                'id' => $agent->id,
                                                                'type' => 'tax_document',
                                                            ]) }}"
                                                            data-document-title="Tax Document"
                                                        >
                                                            <i class="fa fa-eye"></i>
                                                            View
                                                        </button>

                                                    </div>

                                                </div>

                                            @endif


                                            @foreach ($translationDocuments as $index => $document)

                                                <div class="agent-detail-document-item">

                                                    <div class="agent-detail-document-item__icon">
                                                        <i class="fa fa-file-pdf"></i>
                                                    </div>

                                                    <div class="agent-detail-document-item__content">

                                                        <strong>
                                                            Translation Document {{ $index + 1 }}
                                                        </strong>

                                                        <small>
                                                            Supporting translation document
                                                        </small>

                                                    </div>

                                                    <div class="agent-detail-document-item__action">

                                                        <button
                                                            type="button"
                                                            class="backend-button backend-button-secondary"
                                                            data-toggle="modal"
                                                            data-target="#agentDocumentModal"
                                                            data-document-url="{{ route('admin.agents.document', [
                                                                'id' => $agent->id,
                                                                'type' => 'translation_document',
                                                                'index' => $index,
                                                            ]) }}"
                                                            data-document-title="Translation Document {{ $index + 1 }}"
                                                        >
                                                            <i class="fa fa-eye"></i>
                                                            View
                                                        </button>

                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>

                                    </article>

                                @endif


                                


                            </div>

                        </section>


                    </x-slot>


                    {{-- =====================================================
                        SIDE
                    ====================================================== --}}
                    <x-slot name="side">

                        <section class="backend-panel backend-detail-side-card agent-detail-context-panel">

                            <div class="backend-section-header">

                                <div>

                                    <span class="backend-section-header__label">
                                        Context
                                    </span>

                                    <h2>
                                        Agent Snapshot
                                    </h2>

                                    <p>
                                        Quick operational context for this Agent account.
                                    </p>

                                </div>

                            </div>


                            <ul class="backend-detail-side-list">

                                <li>

                                    <span>Status</span>

                                    <strong>
                                        <span class="backend-status-badge backend-status-badge--{{ $statusTone }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </strong>

                                    <small>
                                        Current Agent verification state.
                                    </small>

                                </li>


                                <li>

                                    <span>Company</span>

                                    <strong>
                                        {{ $companyName }}
                                    </strong>

                                    <small>
                                        Registered business name.
                                    </small>

                                </li>


                                <li>

                                    <span>Contact</span>

                                    <strong>
                                        {{ $contactName }}
                                    </strong>

                                    <small>
                                        Primary Agent contact person.
                                    </small>

                                </li>


                                <li>

                                    <span>Documents</span>

                                    <strong>
                                        {{ number_format($documentCount) }}
                                    </strong>

                                    <small>
                                        Submitted documents.
                                    </small>

                                </li>


                                <li>

                                    <span>User Account</span>

                                    <strong>
                                        {{ $agent->user ? 'Linked' : 'Missing' }}
                                    </strong>

                                    <small>
                                        {{ $userStatus }}
                                    </small>

                                </li>


                                <li>

                                    <span>Approved At</span>

                                    <strong>
                                        {{ $approvedAt?->format('d M Y H:i') ?? '-' }}
                                    </strong>

                                    <small>
                                        Agent approval timestamp.
                                    </small>

                                </li>

                            </ul>


                            @canany(['posDev', 'posAuthor'])

                                <div class="backend-detail-side-actions">

                                    {{-- <a
                                        href="{{ route('admin.agents.edit', $agent->id) }}"
                                        class="backend-page-primary-action"
                                    >
                                        <i class="fa fa-pencil-alt"></i>
                                        Edit Agent
                                    </a> --}}


                                    @if ($status === 'pending')

                                        <a
                                            href="{{ route('admin.agents.verification', $agent->id) }}"
                                            class="backend-toolbar-action"
                                        >
                                            <i class="fa fa-shield-alt"></i>
                                            Verify Agent
                                        </a>

                                    @endif

                                </div>

                            @endcanany

                        </section>


                        {{-- ACCOUNT --}}
                        <section class="backend-panel backend-detail-side-card agent-detail-account-panel">

                            <div class="backend-section-header">

                                <div>

                                    <span class="backend-section-header__label">
                                        Account
                                    </span>

                                    <h2>
                                        User Account
                                    </h2>

                                </div>

                            </div>


                            <ul class="backend-detail-side-list">

                                <li>

                                    <span>Email</span>

                                    <strong>
                                        {{ $agent->user?->email ?: $contactEmail }}
                                    </strong>

                                    <small>
                                        Login email associated with the Agent.
                                    </small>

                                </li>


                                <li>

                                    <span>Status</span>

                                    <strong>
                                        {{ $userStatus }}
                                    </strong>

                                    <small>
                                        Current user account status.
                                    </small>

                                </li>


                                <li>

                                    <span>Approval</span>

                                    <strong>
                                        {{ $userApprovalStatus }}
                                    </strong>

                                    <small>
                                        User account approval state.
                                    </small>

                                </li>


                                <li>

                                    <span>Approved At</span>

                                    <strong>
                                        {{ $approvedAt?->format('d M Y H:i') ?? '-' }}
                                    </strong>

                                    <small>
                                        User account approval timestamp.
                                    </small>

                                </li>

                            </ul>

                        </section>

                    </x-slot>

                </x-backend.detail-layout>

            </div>
        </main>

    @endcanany
@endsection