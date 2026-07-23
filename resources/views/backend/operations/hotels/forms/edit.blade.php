@section('title', __('messages.Hotels'))
@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/hotels/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/hotels/forms.js') }}" defer></script>
@endpush

@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container hotel-form-page">
        @can('isAdmin')
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <x-backend.page-hero
                        class="hotel-form-hero"
                        eyebrow="Hotel Profile"
                        title="Hotel Edit"
                        description="Maintain {{ $hotels->name }} using the shared backend form standard."
                    >
                        <x-slot name="action">
                            <a href="{{ route('admin.hotels.show', $hotels->id) }}" class="backend-page-primary-action">
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
                                <li class="breadcrumb-item active" aria-current="page">Edit Hotel</li>
                            </ol>
                        </nav>
                        <div class="backend-page-toolbar__actions">
                            <span class="backend-status-badge backend-status-badge--{{ strtolower($hotels->status ?? '') === 'active' ? 'active' : 'draft' }}">{{ $hotels->status }}</span>
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
                    @if (session('error'))
                        <div class="backend-feedback hotel-form-feedback">
                            <div class="backend-alert backend-alert--danger" role="alert">
                            {{ session('error') }}
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
                        {{-- HOTEL DETAIL --}}
                        <div class="col-md-8">
                            <div class="backend-panel hotel-form-panel">
                                <div class="backend-section-header hotel-form-panel__heading">
                                    <div>
                                        <span class="backend-section-header__label">Hotel Profile</span>
                                        <h2>{{ $hotels->name }}</h2>
                                    </div>
                                </div>
                                <form id="update-hotel" action="{{ route("func.hotel.edit",$hotels->id) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                                    <div class="row hotel-form-panel__body">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="preview-cover">
                                                        <img src="{{ asset('storage/hotels/hotels-cover/'. $hotels->cover)  }}" alt="{{ $hotels->name }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="dropzone">
                                                        <div class="cover-preview-div">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="cover" class="backend-form-label">Cover Image </label>
                                                        <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" placeholder="Choose Cover">
                                                        @error('cover')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="cover" class="backend-form-label">Status</label>
                                                        <select id="status" name="status" class="backend-form-control  @error('status') is-invalid @enderror" required>
                                                            <option selected="{{ $hotels->status }}">{{ $hotels->status }}</option>
                                                            <option value="Active">Active</option>
                                                            <option value="Draft">Draft</option>
                                                            <option value="Archived">Archived</option>
                                                        </select>
                                                        @error('status')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="contact_person" class="backend-form-label">Contact Person </label>
                                                <input type="text" id="contact_person" name="contact_person" class="backend-form-control @error('contact_person') is-invalid @enderror" placeholder="Insert contact person" value="{{ $hotels->contact_person }}" required>
                                                @error('contact_person')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="phone" class="backend-form-label">Phone Number </label>
                                                <input type="number" id="phone" name="phone" class="backend-form-control @error('phone') is-invalid @enderror" placeholder="Insert contact person phone" value="{{ $hotels->phone }}" required>
                                                @error('phone')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="name" class="backend-form-label">Hotel Name </label>
                                                <input type="text" id="name" name="name" class="backend-form-control @error('name') is-invalid @enderror" placeholder="Insert hotel name" value="{{ $hotels->name }}" required>
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3 col-md-3">
                                            <div class="backend-form-field">
                                                <label for="min_stay" class="backend-form-label">Minimum Stay </label>
                                                <input type="number" min="1" max="7" id="min_stay" name="min_stay" class="backend-form-control @error('min_stay') is-invalid @enderror" placeholder="Minimum stay" value="{{ $hotels->min_stay }}" required>
                                                @error('min_stay')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3 col-md-3">
                                            <div class="backend-form-field">
                                                <label for="max_stay" class="backend-form-label">Maximum Stay </label>
                                                <input type="number" min="8"  id="max_stay" name="max_stay" class="backend-form-control @error('max_stay') is-invalid @enderror" placeholder="Maximum stay" value="{{ $hotels->max_stay }}" required>
                                                @error('max_stay')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="address" class="backend-form-label">Address </label>
                                                <input type="text" id="address" name="address" class="backend-form-control @error('address') is-invalid @enderror" placeholder="Insert address" value="{{ $hotels->address }}" required>
                                                @error('address')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="region" class="backend-form-label">Region </label>
                                                <input type="text" id="region" name="region" class="backend-form-control @error('region') is-invalid @enderror" placeholder="Insert region" value="{{ $hotels->region }}" required>
                                                @error('region')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="web" class="backend-form-label">Website </label>
                                                <input type="text" id="web" name="web" class="backend-form-control @error('web') is-invalid @enderror" placeholder="Ex: www.example.com" value="{{ $hotels->web }}" required>
                                                @error('web')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="map" class="backend-form-label">Map Location </label>
                                                <input type="text" id="map" name="map" class="backend-form-control @error('map') is-invalid @enderror" placeholder="Google Map link" value="{{ $hotels->map }}" required>
                                                @error('map')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="airport_duration" class="backend-form-label">Airport Duration (Hours)</label>
                                                <input type="number" min="1" id="airport_duration" name="airport_duration" class="backend-form-control @error('airport_duration') is-invalid @enderror" placeholder="Duration to airport" value="{{ $hotels->airport_duration }}" required>
                                                @error('airport_duration')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="airport_distance" class="backend-form-label">Airport Distance (Km)</label>
                                                <input type="number" min="1" id="airport_distance" name="airport_distance" class="backend-form-control @error('airport_distance') is-invalid @enderror" placeholder="Distance to airport" value="{{ $hotels->airport_distance }}" required>
                                                @error('airport_distance')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row my-18">
                                                <div class="col-md-12">
                                                    <div class="tab-inner-title">Description</div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="description" class="backend-form-label">English </label>
                                                        <textarea id="description" name="description" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert description">{!! $hotels->description !!}</textarea>
                                                        @error('description')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="description_traditional" class="backend-form-label">Chinese Traditional </label>
                                                        <textarea id="description_traditional" name="description_traditional" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert description_traditional">{!! $hotels->description_traditional !!}</textarea>
                                                        @error('description_traditional')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="description_simplified" class="backend-form-label">Chinese Simplified </label>
                                                        <textarea id="description_simplified" name="description_simplified" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert description_simplified">{!! $hotels->description_simplified !!}</textarea>
                                                        @error('description_simplified')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row my-18">
                                                <div class="col-md-12">
                                                    <div class="tab-inner-title">Facility</div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="facility" class="backend-form-label">English </label>
                                                        <textarea id="facility" name="facility" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert facility">{!! $hotels->facility !!}</textarea>
                                                        @error('facility')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="facility_traditional" class="backend-form-label">Chinese Traditional </label>
                                                        <textarea id="facility_traditional" name="facility_traditional" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert facility_traditional">{!! $hotels->facility_traditional !!}</textarea>
                                                        @error('facility_traditional')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="facility_simplified" class="backend-form-label">Chinese Simplified </label>
                                                        <textarea id="facility_simplified" name="facility_simplified" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert facility_simplified">{!! $hotels->facility_simplified !!}</textarea>
                                                        @error('facility_simplified')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row my-18">
                                                <div class="col-md-12">
                                                    <div class="tab-inner-title">Cancellation Policy</div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="cancellation_policy" class="backend-form-label">English</label>
                                                        <textarea id="cancellation_policy" name="cancellation_policy" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert cancellation policy">{!! $hotels->cancellation_policy !!}</textarea>
                                                        @error('cancellation_policy')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="cancellation_policy_traditional" class="backend-form-label">Chinese Traditional</label>
                                                        <textarea id="cancellation_policy_traditional" name="cancellation_policy_traditional" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert cancellation policy">{!! $hotels->cancellation_policy_traditional !!}</textarea>
                                                        @error('cancellation_policy_traditional')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="cancellation_policy_simplified" class="backend-form-label">Chinese Simplified</label>
                                                        <textarea id="cancellation_policy_simplified" name="cancellation_policy_simplified" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert cancellation policy">{!! $hotels->cancellation_policy_simplified !!}</textarea>
                                                        @error('cancellation_policy_simplified')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row my-18">
                                                <div class="col-md-12">
                                                    <div class="tab-inner-title">Additional Information</div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="additional_info" class="backend-form-label">English</label>
                                                        <textarea id="additional_info" name="additional_info" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert additional information">{!! $hotels->additional_info !!}</textarea>
                                                        @error('additional_info')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="additional_info_traditional" class="backend-form-label">Chinese Traditional</label>
                                                        <textarea id="additional_info_traditional" name="additional_info_traditional" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert additional information">{!! $hotels->additional_info_traditional !!}</textarea>
                                                        @error('additional_info_traditional')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="additional_info_simplified" class="backend-form-label">Chinese Simplified</label>
                                                        <textarea id="additional_info_simplified" name="additional_info_simplified" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert additional information">{!! $hotels->additional_info_simplified !!}</textarea>
                                                        @error('additional_info_simplified')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input class="backend-form-control" id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                        <input id="page" name="page" value="edit-hotel" type="hidden">
                                    </div>
                                </form>
                                <div class="backend-form-actions">
                                    <a href="{{ route('admin.hotels.show', $hotels->id) }}" class="backend-button backend-button-secondary"><i class="fa fa-times"></i> Cancel</a>
                                    <button type="submit" form="update-hotel" class="backend-button backend-button-primary"><i class="fa fa-check" aria-hidden="true"></i> Update</button>
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
        @endcan
    @endsection
