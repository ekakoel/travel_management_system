@extends('layouts.head')

@section('title', $definition['title'])

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/transports/index.js') }}" defer></script>
@endpush

@section('content')
    @canany(['posDev', 'posAuthor'])
        @php($field = $definition['field'])
        <main class="main-container transport-master-data-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    eyebrow="Operations Inventory"
                    title="{{ $definition['title'] }}"
                    description="Manage reusable values available when configuring Transport inventory."
                >
                    <x-slot name="action">
                        <button type="button" class="backend-page-primary-action" data-toggle="modal" data-target="#{{ $field }}CreateModal">
                            <i class="fa fa-plus"></i>
                            Add {{ $definition['singular'] }}
                        </button>
                    </x-slot>
                </x-backend.page-hero>

                <x-backend.breadcrumb-toolbar
                    class="transport-master-data-toolbar"
                    :items="[
                        ['label' => 'Admin Panel', 'url' => route('admin.panel-main.view')],
                        ['label' => 'Transportation', 'url' => route('admin.transports.index')],
                    ]"
                    current="{{ $definition['title'] }}"
                />

                @if ($errors->any() || session()->has('success') || session()->has('error'))
                    <section class="backend-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger">
                                <strong>Action needs attention.</strong>
                                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif
                        @if (session('success'))<div class="backend-alert backend-alert--success">{{ session('success') }}</div>@endif
                        @if (session('error'))<div class="backend-alert backend-alert--danger">{{ session('error') }}</div>@endif
                    </section>
                @endif

                <section class="backend-filter-panel transport-master-data-filter">
                    <form method="get" action="{{ route($definition['route'] . '.index') }}" class="backend-form-grid">
                        <label class="backend-filter-field">
                            <span class="backend-filter-label">Search</span>
                            <span class="backend-filter-search">
                                <i class="fa fa-search" aria-hidden="true"></i>
                                <input type="search" name="search" class="backend-filter-control" value="{{ request('search') }}" placeholder="Search {{ strtolower($definition['singular']) }}" data-transport-filter="name">
                            </span>
                        </label>
                        <div class="backend-filter-actions">
                            <button type="submit" class="backend-button backend-button-primary"><i class="fa fa-search"></i> Search</button>
                            <a href="{{ route($definition['route'] . '.index') }}" class="backend-button backend-button-secondary">Reset</a>
                        </div>
                    </form>
                </section>

                <section class="backend-panel transport-master-data-panel">
                    <div class="backend-section-header">
                        <div><span class="backend-section-header__label">Master Data</span><h2>{{ $definition['title'] }} Directory</h2></div>
                        <p>Values cannot be deleted or renamed while they are used by Transport records.</p>
                    </div>
                    <div class="backend-table-wrap">
                        <table class="backend-table">
                            <thead><tr><th>{{ $definition['singular'] }}</th><th>Used By</th><th>Created</th><th>Action</th></tr></thead>
                            <tbody>
                                @forelse ($items as $item)
                                    @php($value = $item->{$field})
                                    <tr data-transport-row data-transport-name="{{ strtolower($value) }}">
                                        <td data-label="{{ $definition['singular'] }}"><strong>{{ $value }}</strong></td>
                                        <td data-label="Used By">{{ number_format($item->transports_count) }} transports</td>
                                        <td data-label="Created">{{ optional($item->created_at)->format('d M Y') ?: '-' }}</td>
                                        <td data-label="Action">
                                            <div class="backend-table-actions">
                                                <button type="button" class="backend-icon-action backend-icon-action--edit" data-toggle="modal" data-target="#{{ $field }}EditModal{{ $item->id }}" aria-label="Edit {{ $value }}"><i class="fa fa-pencil-alt"></i></button>
                                                @if ($item->transports_count === 0)
                                                    <form method="post" action="{{ route($definition['route'] . '.destroy', [$definition['parameter'] => $item]) }}">
                                                        @csrf @method('delete')
                                                        <button type="submit" class="backend-icon-action backend-icon-action--delete" data-transport-delete="{{ $value }}" aria-label="Delete {{ $value }}"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                @else
                                                    <span class="backend-status-badge backend-status-badge--muted">In use</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4"><div class="backend-empty-state"><strong>No {{ strtolower($definition['title']) }} found.</strong><span>Add a value to make it available in Transport forms.</span></div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $items->links() }}
                </section>
            </div>
        </main>

        <div class="modal fade backend-modal transport-master-data-modal" id="{{ $field }}CreateModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">
                <div class="backend-modal__header"><div><span>{{ $definition['singular'] }}</span><h2>Add {{ $definition['singular'] }}</h2></div><x-backend.modal-close label="Close add {{ $definition['singular'] }} modal" /></div>
                <form method="post" action="{{ route($definition['route'] . '.store') }}" class="backend-form">
                    @csrf
                    <div class="backend-modal__body"><p class="backend-form-help">Create a reusable value for Transport configuration.</p><div class="backend-form-field"><label for="{{ $field }}-create" class="backend-form-label">{{ $definition['singular'] }}</label><input id="{{ $field }}-create" name="{{ $field }}" type="text" class="backend-form-control @error($field) is-invalid @enderror" value="{{ old($field) }}" maxlength="255" required autofocus>@error($field)<span class="invalid-feedback d-block">{{ $message }}</span>@enderror</div></div>
                    <div class="backend-modal__footer"><button type="button" class="backend-button backend-button-secondary" data-backend-modal-close>Cancel</button><button type="submit" class="backend-button backend-button-primary"><i class="fa fa-check"></i> Create {{ $definition['singular'] }}</button></div>
                </form>
            </div></div>
        </div>

        @foreach ($items as $item)
            @php($value = $item->{$field})
            <div class="modal fade backend-modal transport-master-data-modal" id="{{ $field }}EditModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">
                    <div class="backend-modal__header"><div><span>{{ $definition['singular'] }}</span><h2>Edit {{ $value }}</h2></div><x-backend.modal-close label="Close edit {{ $definition['singular'] }} modal" /></div>
                    <form method="post" action="{{ route($definition['route'] . '.update', [$definition['parameter'] => $item]) }}" class="backend-form">
                        @csrf @method('put')
                        <div class="backend-modal__body"><p class="backend-form-help">Update this reusable value. Values in use cannot be renamed.</p><div class="backend-form-field"><label for="{{ $field }}-edit-{{ $item->id }}" class="backend-form-label">{{ $definition['singular'] }}</label><input id="{{ $field }}-edit-{{ $item->id }}" name="{{ $field }}" type="text" class="backend-form-control" value="{{ old($field, $value) }}" maxlength="255" required></div></div>
                        <div class="backend-modal__footer"><button type="button" class="backend-button backend-button-secondary" data-backend-modal-close>Cancel</button><button type="submit" class="backend-button backend-button-primary"><i class="fa fa-check"></i> Save Changes</button></div>
                    </form>
                </div></div>
            </div>
        @endforeach
    @endcanany
@endsection