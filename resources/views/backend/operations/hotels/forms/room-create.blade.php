@section('title', __('messages.Hotel Room'))
@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/hotels/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/hotels/forms.js') }}" defer></script>
@endpush

@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container hotel-form-page">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <x-backend.page-hero
                        class="hotel-form-hero"
                        eyebrow="Room Inventory"
                        title="Add New Room"
                        description="Create a room for {{ $hotels->name }} using the shared backend form standard."
                    >
                        <x-slot name="action">
                            <a href="{{ route('admin.hotels.show', $hotels->id) }}#rooms" class="backend-page-primary-action">
                                <i class="fa fa-arrow-left"></i>
                                Back to Detail
                            </a>
                        </x-slot>
                    </x-backend.page-hero>
                    <section class="backend-page-toolbar hotel-form-toolbar">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('hotels-admin.index') }}">Hotel Manager</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.hotels.show', $hotels->id) }}">{{ $hotels->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Room</li>
                            </ol>
                        </nav>
                        <div class="backend-page-toolbar__actions">
                            <span class="backend-status-badge backend-status-badge--draft">Room setup</span>
                        </div>
                    </section>
                    @if (count($errors) > 0)
                        <div class="backend-feedback hotel-form-feedback">
                            <div class="backend-alert backend-alert--danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            </div>
                        </div>
                    @endif
                    @if (\Session::has('success'))
                        <div class="backend-feedback hotel-form-feedback">
                            <div class="backend-alert backend-alert--success">
                            <ul>
                                <li>{!! \Session::get('success') !!}</li>
                            </ul>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-4 mobile">
                            <div class="row">
                                @include('admin.usd-rate')
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="backend-panel hotel-form-panel">
                                <div class="backend-section-header hotel-form-panel__heading">
                                    <div>
                                        <span class="backend-section-header__label">Room Inventory</span>
                                        <h2>Detail Rooms</h2>
                                    </div>
                                </div>
                                <div class="hotel-form-panel__body">
                                    <form id="add-room" action="{{ route('func.room.add') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="row">
                                                    <div class="col-12 col-sm-6">
                                                        <div class="backend-form-field">
                                                            <label for="cover" class="backend-form-label">Cover Image</label>
                                                            <div class="dropzone">
                                                                <div class="cover-preview-div">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="backend-form-field">
                                                    <label for="cover" class="backend-form-label">Cover Image </label><br>
                                                    <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}" required>
                                                    @error('cover')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="backend-form-field">
                                                    <label for="rooms" class="backend-form-label">Rooms Name </label>
                                                    <input type="text" id="rooms" name="rooms" class="backend-form-control @error('rooms') is-invalid @enderror" placeholder="ex: Superior" value="{{ old('rooms') }}" required>
                                                    @error('rooms')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="backend-form-field">
                                                    <label for="capacity_adult" class="backend-form-label">Capacity Adult</label>
                                                    <input type="number" id="capacity_adult" min="1" name="capacity_adult" class="backend-form-control @error('capacity_adult') is-invalid @enderror" placeholder="Insert capacity for adult" value="{{ old('capacity_adult') }}" required>
                                                    @error('capacity_adult')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="backend-form-field">
                                                    <label for="capacity_child" class="backend-form-label">Capacity Child</label>
                                                    <input type="number" id="capacity_child" name="capacity_child" class="backend-form-control @error('capacity_child') is-invalid @enderror" placeholder="Insert capacity for child" value="{{ old('capacity_child') }}">
                                                    @error('capacity_child')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="backend-form-field">
                                                    <label for="room_view" class="backend-form-label">Room View</label>
                                                    <input required type="text" id="room_view" name="room_view" class="backend-form-control @error('room_view') is-invalid @enderror" value="{{ old('room_view') }}" placeholder="Start typing..." data-hotel-autocomplete="room-view" data-hotel-autocomplete-url="{{ route('autocomplate.room_view') }}" data-hotel-autocomplete-results="views" data-hotel-autocomplete-target="#room-view-suggestions">
                                                    <div id="room-view-suggestions" class="hotel-form-suggestions" hidden></div>
                                                    @error('room_view')
                                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="backend-form-field">
                                                    <label for="beds" class="backend-form-label">Bed Type</label>
                                                    <input required type="text" id="bed_type" name="beds" class="backend-form-control @error('beds') is-invalid @enderror" value="{{ old('beds') }}" placeholder="Start typing..." data-hotel-autocomplete="bed-type" data-hotel-autocomplete-url="{{ route('autocomplate.bed_type') }}" data-hotel-autocomplete-results="beds" data-hotel-autocomplete-target="#bed-type-suggestions">
                                                    <div id="bed-type-suggestions" class="hotel-form-suggestions" hidden></div>
                                                    @error('beds')
                                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="backend-form-field">
                                                    <label for="size" class="backend-form-label">Room Size (m²)</label>
                                                    <input type="number" id="size" name="size" class="backend-form-control @error('size') is-invalid @enderror" value="{{ old('size') }}" placeholder="Insert size...">
                                                    @error('size')
                                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="backend-form-field">
                                                            <label for="include" class="backend-form-label">Include</label>
                                                            <textarea id="include" name="include" class="textarea_editor backend-form-control" data-backend-richtext="true" placeholder="Insert include">{{ old('include') }}</textarea>
                                                        </div>
                                                        @error('include')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="backend-form-field">
                                                            <label for="include_traditional" class="backend-form-label">Include (Traditional)</label>
                                                            <textarea id="include_traditional" name="include_traditional" class="textarea_editor backend-form-control" data-backend-richtext="true" placeholder="Insert include in Chinese traditional">{{ old('include_traditional') }}</textarea>
                                                        </div>
                                                        @error('include_traditional')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="backend-form-field">
                                                            <label for="include_simplified" class="backend-form-label">Include (Simplified)</label>
                                                            <textarea id="include_simplified" name="include_simplified" class="textarea_editor backend-form-control" data-backend-richtext="true" placeholder="Insert include in Chinese Simplified">{{ old('include_simplified') }}</textarea>
                                                        </div>
                                                        @error('include_simplified')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="backend-form-field">
                                                            <label for="amenities" class="backend-form-label">Amenities</label>
                                                            <textarea id="amenities" name="amenities" class="textarea_editor backend-form-control" data-backend-richtext="true" placeholder="Insert amenities">{{ old('amenities') }}</textarea>
                                                        </div>
                                                        @error('amenities')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="backend-form-field">
                                                            <label for="amenities_traditional" class="backend-form-label">Amenities (Traditional)</label>
                                                            <textarea id="amenities_traditional" name="amenities_traditional" class="textarea_editor backend-form-control" data-backend-richtext="true" placeholder="Insert amenities in Chinese traditional">{{ old('amenities_traditional') }}</textarea>
                                                        </div>
                                                        @error('amenities_traditional')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="backend-form-field">
                                                            <label for="amenities_simplified" class="backend-form-label">Amenities (Simplified)</label>
                                                            <textarea id="amenities_simplified" name="amenities_simplified" class="textarea_editor backend-form-control" data-backend-richtext="true" placeholder="Insert amenities in Chinese Simplified">{{ old('amenities_simplified') }}</textarea>
                                                        </div>
                                                        @error('amenities_simplified')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="backend-form-field">
                                                    <label for="additional_info" class="backend-form-label">Additional Information</label>
                                                    <textarea id="additional_info" name="additional_info" class="textarea_editor backend-form-control" data-backend-richtext="true" placeholder="Insert additional information">{{ old('additional_info') }}</textarea>
                                                </div>
                                                @error('additional_info')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <div class="backend-form-field">
                                                    <label for="additional_info_traditional" class="backend-form-label">Additional Information (Traditional)</label>
                                                    <textarea id="additional_info_traditional" name="additional_info_traditional" class="textarea_editor backend-form-control" data-backend-richtext="true" placeholder="Insert additional information in Chinese traditional">{{ old('additional_info_traditional') }}</textarea>
                                                </div>
                                                @error('additional_info_traditional')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <div class="backend-form-field">
                                                    <label for="additional_info_simplified" class="backend-form-label">Additional Information (Simplified)</label>
                                                    <textarea id="additional_info_simplified" name="additional_info_simplified" class="textarea_editor backend-form-control" data-backend-richtext="true" placeholder="Insert additional information in Chinese Simplified">{{ old('additional_info_simplified') }}</textarea>
                                                </div>
                                                @error('additional_info_simplified')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <input class="backend-form-control" id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                            <input class="backend-form-control" id="hotels_id" name="hotels_id" value="{{ $hotels->id }}" type="hidden">
                                        </div>
                                    </form>
                                </div>
                                <div class="backend-form-actions">
                                    <a href="{{ route('admin.hotels.show', $hotels->id) }}#rooms" class="backend-button backend-button-secondary"><i class="fa fa-times"></i> Cancel</a>
                                    <button type="submit" form="add-room" class="backend-button backend-button-primary"><i class="fa fa-check" aria-hidden="true"></i> Add Rooms</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 desktop">
                            <div class="row">
                                @include('admin.usd-rate')
                            </div>
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
@endsection
