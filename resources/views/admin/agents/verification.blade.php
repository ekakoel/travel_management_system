@extends('layouts.head')

@section('title', 'Agent Verification')

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/agents/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/agents/index.js') }}" defer></script>
@endpush

@section('content')
    @canany(['posDev','posAuthor','posRsv','posAdm'])

        @php
            $companyName = $agent->company_name ?: ($agent->name ?: '-');
            $contactName = $agent->contact_name ?: ($agent->pic_name ?: '-');
            $contactEmail = $agent->contact_email ?: ($agent->email ?: '-');
            $companyAddress = $agent->company_address ?: ($agent->Address ?: '-');

            $status = $agent->status ?: 'pending';

            $statusClass = match (strtolower($status)) {
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

            $verificationErrors = $validationErrors ?? [];
        @endphp

        <div class="mobile-menu-overlay"></div>

        <main class="main-container agents-admin-page">
            <div class="pd-ltr-20">

                {{-- ==========================================================
                    PAGE HERO
                =========================================================== --}}
                <x-backend.page-hero
                    class="agents-admin-hero"
                    eyebrow="Operations Resource"
                    title="Agent Verification"
                    description="Review Agent registration information, business documents, and account details before approving the Bali Kami partner account."
                >
                    <x-slot name="action">
                        <a
                            href="{{ route('admin.agents.index') }}"
                            class="backend-page-primary-action"
                        >
                            <i class="fa fa-arrow-left"></i>
                            Back to Agents
                        </a>
                    </x-slot>
                </x-backend.page-hero>


                {{-- ==========================================================
                    TOOLBAR
                =========================================================== --}}
                <section class="backend-page-toolbar agents-admin-toolbar">

                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">

                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.panel-main.view') }}">
                                    Admin Panel
                                </a>
                            </li>

                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.agents.index') }}">
                                    Agent Manager
                                </a>
                            </li>

                            <li
                                class="breadcrumb-item active"
                                aria-current="page"
                            >
                                Verification
                            </li>

                        </ol>
                    </nav>

                    <div class="backend-page-toolbar__actions">

                        <span class="backend-status-badge backend-status-badge--{{ $statusClass }}">
                            {{ ucfirst($status) }}
                        </span>

                    </div>

                </section>


                {{-- ==========================================================
                    FEEDBACK
                =========================================================== --}}
                @if (
                    $errors->any() ||
                    session()->has('success') ||
                    session()->has('invalid') ||
                    session()->has('error') ||
                    !empty($verificationErrors)
                )

                    <section class="backend-feedback agents-admin-feedback">

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


                        @if (!empty($verificationErrors))
                            <div class="backend-alert backend-alert--danger">

                                <strong>
                                    Verification cannot be completed.
                                </strong>

                                <ul>
                                    @foreach ($verificationErrors as $error)
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


                        @if (session()->has('invalid') || session()->has('error'))
                            <div class="backend-alert backend-alert--danger">
                                <strong>
                                    {{ session('invalid') ?? session('error') }}
                                </strong>
                            </div>
                        @endif

                    </section>

                @endif


                {{-- ==========================================================
                    KPI
                =========================================================== --}}
                <section
                    class="backend-kpi-grid backend-kpi-grid--4"
                    aria-label="Agent verification summary"
                >

                    <article class="backend-kpi-card backend-kpi-card--teal">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-building-o"></i>
                        </div>

                        <div>
                            <span>Company</span>
                            <strong>{{ $companyName }}</strong>
                            <small>Registered business name</small>
                        </div>
                    </article>


                    <article class="backend-kpi-card backend-kpi-card--blue">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-user"></i>
                        </div>

                        <div>
                            <span>Contact</span>
                            <strong>{{ $contactName }}</strong>
                            <small>Primary contact person</small>
                        </div>
                    </article>


                    <article class="backend-kpi-card backend-kpi-card--amber">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-file-text-o"></i>
                        </div>

                        <div>
                            <span>Documents</span>
                            <strong>{{ count($translationDocuments) + ($agent->business_license ? 1 : 0) + ($agent->company_letter ? 1 : 0) + ($agent->tax_document ? 1 : 0) }}</strong>
                            <small>Submitted document records</small>
                        </div>
                    </article>


                    <article class="backend-kpi-card backend-kpi-card--green">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-envelope-o"></i>
                        </div>

                        <div>
                            <span>Email</span>
                            <strong>{{ $contactEmail }}</strong>
                            <small>Agent login email</small>
                        </div>
                    </article>

                </section>


                {{-- ==========================================================
                    APPLICATION INFORMATION
                =========================================================== --}}
                <section class="backend-panel agents-admin-panel">

                    <div class="backend-section-header agents-admin-panel__heading">

                        <div>
                            <span class="backend-section-header__label">
                                Agent Application
                            </span>

                            <h2>
                                Company & Contact Information
                            </h2>
                        </div>

                        <p>
                            Review the information submitted by the Agent during partner registration.
                        </p>

                    </div>


                    <div class="agents-admin-verification-content">

                        {{-- COMPANY INFORMATION --}}
                        <section class="agents-admin-verification-section">

                            <div class="agents-admin-section-header">
                                <div>
                                    <span>01</span>
                                    <h3>Company Information</h3>
                                </div>
                            </div>

                            <dl class="agents-admin-detail-grid">

                                <div>
                                    <dt>Legal Company Name</dt>
                                    <dd>{{ $companyName }}</dd>
                                </div>

                                <div>
                                    <dt>Company Type</dt>
                                    <dd>{{ $agent->company_type ?: '-' }}</dd>
                                </div>

                                <div>
                                    <dt>Company / Office</dt>
                                    <dd>{{ $companyName }}</dd>
                                </div>

                                <div>
                                    <dt>Country</dt>
                                    <dd>{{ $agent->country ?: '-' }}</dd>
                                </div>

                                <div class="agents-admin-detail-grid__full">
                                    <dt>Company Address</dt>
                                    <dd>{{ $companyAddress }}</dd>
                                </div>

                                <div>
                                    <dt>Website</dt>
                                    <dd>
                                        @if ($agent->website)
                                            <a
                                                href="{{ $agent->website }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                {{ $agent->website }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>

                                <div>
                                    <dt>Business Registration Number</dt>
                                    <dd>
                                        {{ $agent->business_license_number ?: '-' }}
                                    </dd>
                                </div>

                            </dl>

                        </section>


                        {{-- CONTACT INFORMATION --}}
                        <section class="agents-admin-verification-section">

                            <div class="agents-admin-section-header">
                                <div>
                                    <span>02</span>
                                    <h3>Contact Information</h3>
                                </div>
                            </div>

                            <dl class="agents-admin-detail-grid">

                                <div>
                                    <dt>Contact Person</dt>
                                    <dd>{{ $contactName }}</dd>
                                </div>

                                <div>
                                    <dt>Position</dt>
                                    <dd>{{ $agent->position ?: '-' }}</dd>
                                </div>

                                <div>
                                    <dt>Email</dt>
                                    <dd>{{ $contactEmail }}</dd>
                                </div>

                                <div>
                                    <dt>Phone</dt>
                                    <dd>{{ $agent->phone ?: '-' }}</dd>
                                </div>

                                <div>
                                    <dt>Preferred Contact</dt>
                                    <dd>{{ $agent->preferred_contact ?: '-' }}</dd>
                                </div>

                                <div>
                                    <dt>Main Market</dt>
                                    <dd>{{ $agent->main_market ?: '-' }}</dd>
                                </div>

                            </dl>

                        </section>


                        {{-- BUSINESS INFORMATION --}}
                        <section class="agents-admin-verification-section">

                            <div class="agents-admin-section-header">
                                <div>
                                    <span>03</span>
                                    <h3>Business Information</h3>
                                </div>
                            </div>

                            <dl class="agents-admin-detail-grid">

                                <div>
                                    <dt>Monthly Bali Clients</dt>
                                    <dd>
                                        {{ $agent->monthly_bali_clients ?: '-' }}
                                    </dd>
                                </div>

                                <div>
                                    <dt>Business License Number</dt>
                                    <dd>
                                        {{ $agent->business_license_number ?: '-' }}
                                    </dd>
                                </div>

                                <div class="agents-admin-detail-grid__full">
                                    <dt>Interested Services</dt>
                                    <dd>
                                        @if (!empty($interestedServices))

                                            <div class="agents-admin-tag-list">

                                                @foreach ($interestedServices as $service)
                                                    <span class="agents-admin-tag">
                                                        {{ $service }}
                                                    </span>
                                                @endforeach

                                            </div>

                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>

                            </dl>

                        </section>


                        {{-- DOCUMENTS --}}
                        <section class="agents-admin-verification-section">

                            <div class="agents-admin-section-header">

                                <div>
                                    <span>04</span>
                                    <h3>Submitted Documents</h3>
                                </div>

                            </div>


                            <div class="agents-admin-document-list">

                                {{-- Business License --}}
                                <div class="agents-admin-document-item">

                                    <div class="agents-admin-document-item__info">

                                        <i class="fa fa-id-card-o"></i>

                                        <div>
                                            <strong>
                                                Business License
                                            </strong>

                                            <span>
                                                {{ $agent->business_license ?: 'Not submitted' }}
                                            </span>
                                        </div>

                                    </div>

                                    @if ($agent->business_license)

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

                                    @endif

                                </div>


                                {{-- Company Letter --}}
                                <div class="agents-admin-document-item">

                                    <div class="agents-admin-document-item__info">

                                        <i class="fa fa-building-o"></i>

                                        <div>
                                            <strong>
                                                Company Letter
                                            </strong>

                                            <span>
                                                {{ $agent->company_letter ?: 'Not submitted' }}
                                            </span>
                                        </div>

                                    </div>

                                    @if ($agent->company_letter)

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

                                    @endif

                                </div>


                                {{-- Tax Document --}}
                                <div class="agents-admin-document-item">

                                    <div class="agents-admin-document-item__info">

                                        <i class="fa fa-file-text-o"></i>

                                        <div>
                                            <strong>
                                                Tax Document
                                            </strong>

                                            <span>
                                                {{ $agent->tax_document ?: 'Not submitted' }}
                                            </span>
                                        </div>

                                    </div>

                                    @if ($agent->tax_document)

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

                                    @endif

                                </div>


                                {{-- Translation Documents --}}
                                @foreach ($translationDocuments as $index => $document)

                                    <div class="agents-admin-document-item">

                                        <div class="agents-admin-document-item__info">

                                            <i class="fa fa-language"></i>

                                            <div>
                                                <strong>
                                                    Translation Document {{ $index + 1 }}
                                                </strong>

                                                <span>
                                                    {{ $document }}
                                                </span>
                                            </div>

                                        </div>

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

                                @endforeach


                                @if (
                                    !$agent->business_license &&
                                    !$agent->company_letter &&
                                    !$agent->tax_document &&
                                    empty($translationDocuments)
                                )

                                    <div class="backend-empty-state">
                                        <i class="fa fa-file-o"></i>

                                        <strong>
                                            No documents submitted.
                                        </strong>

                                        <span>
                                            No supporting documents are available for this Agent application.
                                        </span>
                                    </div>

                                @endif

                            </div>

                        </section>


                        
                        {{-- VERIFICATION CHECKLIST --}}
                        <section class="agents-admin-verification-section">

                            <form
                                action="{{ route('admin.agents.verify', $agent->id) }}"
                                method="POST"
                                class="agents-admin-verification-form"
                            >
                                @csrf
                                @method('PATCH')

                                <div class="agents-admin-section-header">

                                    <div>
                                        <span>05</span>
                                        <h3>Verification Checklist</h3>
                                    </div>

                                </div>

                                <div class="agents-admin-checklist">

                                    <label class="agents-admin-check-item">
                                        <input
                                            type="checkbox"
                                            name="checks[]"
                                            value="company_information"
                                            class="agents-admin-check"
                                            {{ in_array('company_information', old('checks', []), true) ? 'checked' : '' }}
                                        >

                                        <span>
                                            Company information has been reviewed.
                                        </span>
                                    </label>


                                    <label class="agents-admin-check-item">
                                        <input
                                            type="checkbox"
                                            name="checks[]"
                                            value="contact_information"
                                            class="agents-admin-check"
                                            {{ in_array('contact_information', old('checks', []), true) ? 'checked' : '' }}
                                        >

                                        <span>
                                            Contact person and contact information have been reviewed.
                                        </span>
                                    </label>


                                    <label class="agents-admin-check-item">
                                        <input
                                            type="checkbox"
                                            name="checks[]"
                                            value="business_information"
                                            class="agents-admin-check"
                                            {{ in_array('business_information', old('checks', []), true) ? 'checked' : '' }}
                                        >

                                        <span>
                                            Business registration information has been reviewed.
                                        </span>
                                    </label>


                                    <label class="agents-admin-check-item">
                                        <input
                                            type="checkbox"
                                            name="checks[]"
                                            value="submitted_documents"
                                            class="agents-admin-check"
                                            {{ in_array('submitted_documents', old('checks', []), true) ? 'checked' : '' }}
                                        >

                                        <span>
                                            Submitted documents have been reviewed.
                                        </span>
                                    </label>


                                    <label class="agents-admin-check-item">
                                        <input
                                            type="checkbox"
                                            name="checks[]"
                                            value="ready_for_approval"
                                            class="agents-admin-check"
                                            {{ in_array('ready_for_approval', old('checks', []), true) ? 'checked' : '' }}
                                        >

                                        <span>
                                            Agent registration information is ready for approval.
                                        </span>
                                    </label>

                                </div>


                                @error('checks')
                                    <div class="backend-alert backend-alert--danger">
                                        {{ $message }}
                                    </div>
                                @enderror


                                <div class="agents-admin-verification-actions">

                                    <button
                                        type="submit"
                                        class="backend-button backend-button-primary"
                                    >
                                        <i class="fa fa-check"></i>
                                        Verify Agent
                                    </button>

                                </div>

                            </form>

                        </section>


                        {{-- REJECTION --}}
                        @if ($status === 'pending')

                            <section class="agents-admin-verification-section">

                                <div class="agents-admin-section-header">

                                    <div>
                                        <span>06</span>
                                        <h3>Reject Application</h3>
                                    </div>

                                </div>


                                <form
                                    action="{{ route('admin.agents.reject', $agent->id) }}"
                                    method="POST"
                                    class="agents-admin-rejection-form"
                                >

                                    @csrf
                                    @method('PATCH')

                                    <div class="agents-admin-form-field">

                                        <label for="rejection_reason">
                                            Rejection Reason
                                        </label>

                                        <textarea
                                            id="rejection_reason"
                                            name="rejection_reason"
                                            rows="4"
                                            class="form-control"
                                            placeholder="Explain why this Agent application is being rejected..."
                                            required
                                        >{{ old('rejection_reason') }}</textarea>

                                    </div>


                                    <div class="agents-admin-verification-actions">

                                        <button
                                            type="submit"
                                            class="backend-button backend-button-danger"
                                        >
                                            <i class="fa fa-times"></i>
                                            Reject Application
                                        </button>

                                    </div>

                                </form>

                            </section>

                        @endif


                        {{-- PROCESSED STATUS --}}
                        @if ($status !== 'pending')

                            <section class="agents-admin-verification-section">

                                <div class="agents-admin-processed">

                                    <i class="fa fa-info-circle"></i>

                                    <div>

                                        <strong>
                                            This application has already been processed.
                                        </strong>

                                        <span>
                                            Current status:
                                            <b>{{ ucfirst($status) }}</b>
                                        </span>

                                    </div>

                                </div>

                            </section>

                        @endif

                    </div>

                </section>

            </div>
        </main>


        {{-- ==============================================================
            DOCUMENT MODAL
        ============================================================== --}}
        <div
            class="modal fade backend-modal agents-admin-modal"
            id="agentDocumentModal"
            tabindex="-1"
            role="dialog"
            aria-hidden="true"
        >

            <div
                class="modal-dialog modal-dialog-centered modal-xl"
                role="document"
            >

                <div class="modal-content">

                    <div class="backend-modal__header">

                        <div>
                            <span>Agent Document</span>

                            <h3 id="agentDocumentModalTitle">
                                Document
                            </h3>
                        </div>

                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">
                                &times;
                            </span>
                        </button>

                    </div>


                    <div class="backend-modal__body agents-admin-document-modal__body">

                        <div
                            class="agents-admin-document-loading"
                            id="agentDocumentLoading"
                        >
                            <i class="fa fa-spinner fa-spin"></i>
                            <span>Loading document...</span>
                        </div>

                        <iframe
                            id="agentDocumentFrame"
                            class="agents-admin-document-frame"
                            src="about:blank"
                            title="Agent Document"
                        ></iframe>

                    </div>


                    <div class="backend-modal__footer">

                        <a
                            href="#"
                            id="agentDocumentOpen"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="backend-button backend-button-secondary"
                        >
                            <i class="fa fa-external-link"></i>
                            Open in New Tab
                        </a>

                        <button
                            type="button"
                            class="backend-button backend-button-primary"
                            data-dismiss="modal"
                        >
                            Close
                        </button>

                    </div>

                </div>

            </div>

        </div>

    @endcanany
@endsection