@extends('layouts.head')

@section('title', 'Home Slider Manager')

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/home-sliders/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/home-sliders/index.js') }}" defer></script>
@endpush

@section('content')

@canany(['posDev','posAdm','posAuthor'])

<main class="main-container home-sliders-admin-page">
    <div class="pd-ltr-20">

        <x-backend.page-hero
            class="home-sliders-admin-hero"
            eyebrow="Website Content"
            title="Home Slider Manager"
            description="Manage homepage promotional sliders and their display schedule."
        >
            <x-slot name="action">
                <button
                    type="button"
                    class="backend-page-primary-action"
                    data-toggle="modal"
                    data-target="#sliderAddModal"
                >
                    <i class="fa fa-plus"></i>
                    Add Slider
                </button>
            </x-slot>
        </x-backend.page-hero>

        <section class="backend-page-toolbar">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.panel-main.view') }}">
                            Admin Panel
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        Home Slider
                    </li>
                </ol>
            </nav>
        </section>

        @if ($errors->any() || session()->has('success') || session()->has('error'))
            <section class="backend-feedback">

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

        <section class="backend-kpi-grid backend-kpi-grid--3">

            <article class="backend-kpi-card backend-kpi-card--teal">
                <div class="backend-kpi-card__icon">
                    <i class="fa fa-images"></i>
                </div>
                <div>
                    <span>Total Sliders</span>
                    <strong>{{ number_format($sliderStats['total']) }}</strong>
                    <small>Registered homepage sliders</small>
                </div>
            </article>

            <article class="backend-kpi-card backend-kpi-card--green">
                <div class="backend-kpi-card__icon">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div>
                    <span>Active</span>
                    <strong>{{ number_format($sliderStats['active']) }}</strong>
                    <small>Currently enabled</small>
                </div>
            </article>

            <article class="backend-kpi-card backend-kpi-card--amber">
                <div class="backend-kpi-card__icon">
                    <i class="fa fa-eye-slash"></i>
                </div>
                <div>
                    <span>Inactive</span>
                    <strong>{{ number_format($sliderStats['inactive']) }}</strong>
                    <small>Disabled sliders</small>
                </div>
            </article>

        </section>

        <section class="backend-panel">

            <div class="backend-section-header">
                <div>
                    <span class="backend-section-header__label">
                        Homepage Content
                    </span>
                    <h2>All Sliders</h2>
                </div>

                <p>
                    Manage slider images, content, ordering, and visibility.
                </p>
            </div>

            <div class="backend-table-wrap">

                <table class="backend-table">

                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Preview</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Schedule</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($sliders as $slider)

                        <tr>

                            <td data-label="Order">
                                <strong>{{ $slider->sort_order }}</strong>
                            </td>

                            <td data-label="Preview">
                                <img
                                    src="{{ asset('storage/' . $slider->image) }}"
                                    alt="{{ $slider->title }}"
                                    style="width:140px;height:55px;object-fit:cover;"
                                >
                            </td>

                            <td data-label="Title">
                                <strong>{{ $slider->title ?: '-' }}</strong>
                                <span>
                                    {{ $slider->description ? Str::limit(strip_tags($slider->description), 70) : '-' }}
                                </span>
                            </td>

                            <td data-label="Status">

                                @if($slider->is_active)
                                    <span class="backend-status-badge backend-status-badge--success">
                                        Active
                                    </span>
                                @else
                                    <span class="backend-status-badge backend-status-badge--warning">
                                        Inactive
                                    </span>
                                @endif

                            </td>

                            <td data-label="Schedule">
                                <span>
                                    {{ $slider->start_at?->format('d M Y H:i') ?: 'No start' }}
                                </span>
                                <span>
                                    {{ $slider->end_at?->format('d M Y H:i') ?: 'No end' }}
                                </span>
                            </td>

                            <td data-label="Action">

                                <div class="backend-table-actions">

                                    <button
                                        type="button"
                                        class="backend-icon-action"
                                        data-toggle="modal"
                                        data-target="#sliderEdit{{ $slider->id }}"
                                        aria-label="Edit {{ $slider->title }}"
                                    >
                                        <i class="fa fa-pencil-alt"></i>
                                    </button>

                                    @can('posDev')
                                        <form
                                            action="{{ route('admin.home-slider.destroy', $slider->id) }}"
                                            method="post"
                                        >
                                            @csrf
                                            @method('delete')

                                            <button
                                                type="submit"
                                                class="backend-icon-action is-danger"
                                                aria-label="Delete {{ $slider->title }}"
                                            >
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
                                    <i class="fa fa-images"></i>
                                    <strong>No sliders found.</strong>
                                    <span>
                                        Add the first homepage slider.
                                    </span>
                                </div>
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

            @if($sliders->hasPages())
                <div class="backend-pagination">
                    {{ $sliders->links() }}
                </div>
            @endif

        </section>

    </div>
</main>

{{-- Add Modal --}}
<div
    class="modal fade backend-modal"
    id="sliderAddModal"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="backend-modal__header">
                <div>
                    <span>Add Home Slider</span>
                    <h3>Create homepage slider</h3>
                </div>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                >
                    <span>&times;</span>
                </button>
            </div>

            <form
                action="{{ route('admin.home-slider.create') }}"
                method="post"
                enctype="multipart/form-data"
            >

                @csrf

                <div class="backend-modal__body">
                    @include('backend.operations.home-sliders.partials.form', [
                        'slider' => null,
                    ])
                </div>

                <div class="backend-modal__footer">
                    <button
                        type="submit"
                        class="backend-button backend-button-primary"
                    >
                        <i class="fa fa-check"></i>
                        Save
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- Edit Modals --}}
@foreach($sliders as $slider)

<div
    class="modal fade backend-modal"
    id="sliderEdit{{ $slider->id }}"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="backend-modal__header">
                <div>
                    <span>Edit Home Slider</span>
                    <h3>{{ $slider->title ?: 'Slider #' . $slider->id }}</h3>
                </div>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                >
                    <span>&times;</span>
                </button>
            </div>

            <form
                action="{{ route('admin.home-slider.edit', $slider->id) }}"
                method="post"
                enctype="multipart/form-data"
            >

                @csrf
                @method('put')

                <div class="backend-modal__body">

                    @include('backend.operations.home-sliders.partials.form', [
                        'slider' => $slider,
                    ])

                </div>

                <div class="backend-modal__footer">

                    <button
                        type="submit"
                        class="backend-button backend-button-primary"
                    >
                        <i class="fa fa-check"></i>
                        Save Changes
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

@endforeach

@endcanany

@endsection