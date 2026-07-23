@extends('layouts.head')

@section('title', __('messages.Transports'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/transports/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/transports/forms.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container transport-form-admin-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    eyebrow="Operations Inventory"
                    title="Add Transportation"
                    description="Create a transport inventory item using the standardized backend form layout."
                >
                    <x-slot name="action">
                        <a href="{{ route('transports-admin.index') }}" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Transports
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar transport-form-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('transports-admin.index') }}">Transportation</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Transport</li>
                        </ol>
                    </nav>
                </section>

                @include('backend.operations.transports.partials.form-feedback')

                <form id="create-transport" data-transport-form action="{{ route('admin.transports.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <section class="backend-panel transport-form-panel">
                        <div class="backend-section-header">
                            <div>
                                <span class="backend-section-header__label">Transport Profile</span>
                                <h2>Basic Information</h2>
                            </div>
                            <p>Fill in vehicle identity, capacity, content notes, and a cover image for this transport item.</p>
                        </div>

                        <div class="backend-form-grid">
                            @include('backend.operations.transports.partials.profile-fields', [
                                'transport' => null,
                                'types' => $type,
                                'brands' => $brand,
                                'isCreate' => true,
                            ])
                        </div>

                        <input name="author" value="{{ Auth::id() }}" type="hidden">
                        <input name="page" value="add-transport" type="hidden">
                        <input name="initial_state" value="" type="hidden">

                        <div class="backend-page-toolbar backend-form-actions">
                            <div class="backend-page-toolbar__actions">
                                <a href="{{ route('transports-admin.index') }}" class="backend-button backend-button-secondary">Cancel</a>
                                <button type="submit" class="backend-button backend-button-primary">
                                    <i class="fa fa-check"></i>
                                    Add Transportation
                                </button>
                            </div>
                        </div>
                    </section>
                </form>

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
