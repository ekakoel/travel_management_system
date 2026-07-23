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
    @can('isAdmin')
        <div class="main-container hotel-form-page">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <x-backend.page-hero
                        class="hotel-form-hero"
                        eyebrow="Hotel Profile"
                        title="Add Hotel"
                        description="Create a hotel profile using the shared backend form standard."
                    >
                        <x-slot name="action">
                            <a href="{{ route('hotels-admin.index') }}" class="backend-page-primary-action">
                                <i class="fa fa-arrow-left"></i>
                                Back to Hotels
                            </a>
                        </x-slot>
                    </x-backend.page-hero>
                    <section class="backend-page-toolbar hotel-form-toolbar">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('hotels-admin.index') }}">Hotel Manager</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Hotel</li>
                            </ol>
                        </nav>
                        <div class="backend-page-toolbar__actions">
                            <span class="backend-status-badge backend-status-badge--draft">Draft setup</span>
                        </div>
                    </section>

                    <div class="backend-feedback hotel-form-feedback">
                        @if (count($errors) > 0)
                            <div class="backend-alert backend-alert--danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="backend-alert backend-alert--danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (\Session::has('success'))
                            <div class="backend-alert backend-alert--success">
                                <ul>
                                    <li>{!! \Session::get('success') !!}</li>
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-4 mobile">
                            <div class="row">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="backend-panel hotel-form-panel">
                                <div class="backend-section-header hotel-form-panel__heading">
                                    <div>
                                        <span class="backend-section-header__label">Hotel Profile</span>
                                        <h2>Detail Hotel</h2>
                                    </div>
                                </div>
                                <form id="add-hotel" action="{{ route('func.hotel.add') }}" method="post" enctype="multipart/form-data" id="my-awesome-dropzone">
                                    @csrf
                                    <div class="row hotel-form-panel__body">
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
                                                <label for="cover" class="backend-form-label">Cover Image <span> *</span></label><br>
                                                <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}" required>
                                                @error('cover')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="name" class="backend-form-label">Name </label>
                                                <input type="text" id="name" name="name" class="backend-form-control @error('name') is-invalid @enderror" placeholder="Insert hotel name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="contact_person" class="backend-form-label">Contact Person </label>
                                                <input type="text" id="contact_person" name="contact_person" class="backend-form-control @error('contact_person') is-invalid @enderror" placeholder="Insert contact person" value="{{ old('contact_person') }}" required>
                                                @error('contact_person')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="phone" class="backend-form-label">Phone Number </label>
                                                <input type="number" id="phone" name="phone" class="backend-form-control @error('phone') is-invalid @enderror" placeholder="Insert contact person phone" value="{{ old('phone') }}" required>
                                                @error('phone')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="min_stay" class="backend-form-label">Min Stay </label>
                                                <input type="number" min="1" id="min_stay" name="min_stay" class="backend-form-control @error('min_stay') is-invalid @enderror" placeholder="Minimum stay" value="{{ old('min_stay') }}" required>
                                                @error('min_stay')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="max_stay" class="backend-form-label">Max Stay </label>
                                                <input type="number" min="1" id="max_stay" name="max_stay" class="backend-form-control @error('max_stay') is-invalid @enderror" placeholder="Maximum stay" value="{{ old('max_stay') }}" required>
                                                @error('max_stay')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="address" class="backend-form-label">Address </label>
                                                <input type="text" id="address" name="address" class="backend-form-control @error('address') is-invalid @enderror" placeholder="Insert address" value="{{ old('address') }}" required>
                                                @error('address')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="region" class="backend-form-label">Region </label>
                                                <input type="text" id="region" name="region" class="backend-form-control @error('region') is-invalid @enderror" placeholder="Insert region" value="{{ old('region') }}" required>
                                                @error('region')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="map" class="backend-form-label">Map Location </label>
                                                <input type="text" id="map" name="map" class="backend-form-control @error('map') is-invalid @enderror" placeholder="Google Map link" value="{{ old('map') }}" required>
                                                @error('map')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="web" class="backend-form-label">Website </label>
                                                <input type="text" id="web" name="web" class="backend-form-control @error('web') is-invalid @enderror" placeholder="Ex: www.example.com" value="{{ old('web') }}">
                                                @error('web')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="airport_distance" class="backend-form-label">Airport Distance (Hours)</label>
                                                <input type="number" min="1" id="airport_distance" name="airport_distance" class="backend-form-control @error('airport_distance') is-invalid @enderror" value="{{ old('airport_distance') }}" required>
                                                @error('airport_distance')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="airport_duration" class="backend-form-label">Airport Duration (Km)</label>
                                                <input type="number" min="1" id="airport_duration" name="airport_duration" class="backend-form-control @error('airport_duration') is-invalid @enderror" value="{{ old('airport_duration') }}" required>
                                                @error('airport_duration')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row my-18">
                                                <div class="col-md-12">
                                                    <div class="tab-inner-title">Description</div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="description" class="backend-form-label">English <span> *</span></label>
                                                        <textarea id="description" name="description" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert description">{{ old('description') }}</textarea>
                                                        @error('description')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="description_traditional" class="backend-form-label">Chinese Traditional <span> *</span></label>
                                                        <textarea id="description_traditional" name="description_traditional" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert description_traditional">{{ old('description_traditional') }}</textarea>
                                                        @error('description_traditional')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="description_simplified" class="backend-form-label">Chinese Simplified <span> *</span></label>
                                                        <textarea id="description_simplified" name="description_simplified" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert description_simplified">{{ old('description_simplified') }}</textarea>
                                                        @error('description_simplified')
                                                            <div class="backend-form-error">{{ $message }}</div>
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
                                                        <label for="facility" class="backend-form-label">English</label>
                                                        <textarea id="facility" name="facility" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert facility">{{ old('facility') }}</textarea>
                                                        @error('facility')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="facility_traditional" class="backend-form-label">Chinese Traditional</label>
                                                        <textarea id="facility_traditional" name="facility_traditional" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert facility_traditional">{{ old('facility_traditional') }}</textarea>
                                                        @error('facility_traditional')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="facility_simplified" class="backend-form-label">Chinese Simplified</label>
                                                        <textarea id="facility_simplified" name="facility_simplified" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert facility_simplified">{{ old('facility_simplified') }}</textarea>
                                                        @error('facility_simplified')
                                                            <div class="backend-form-error">{{ $message }}</div>
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
                                                        <textarea id="cancellation_policy" name="cancellation_policy" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert cancellation policy">{{ old('cancellation_policy') }}</textarea>
                                                        @error('cancellation_policy')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="cancellation_policy_traditional" class="backend-form-label">Chinese Traditional</label>
                                                        <textarea id="cancellation_policy_traditional" name="cancellation_policy_traditional" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert cancellation policy">{{ old('cancellation_policy_traditional') }}</textarea>
                                                        @error('cancellation_policy_traditional')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="cancellation_policy_simplified" class="backend-form-label">Chinese Simplified</label>
                                                        <textarea id="cancellation_policy_simplified" name="cancellation_policy_simplified" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert cancellation policy">{{ old('cancellation_policy_simplified') }}</textarea>
                                                        @error('cancellation_policy_simplified')
                                                            <div class="backend-form-error">{{ $message }}</div>
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
                                                        <textarea id="additional_info" name="additional_info" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert additional information">{{ old('additional_info') }}</textarea>
                                                        @error('additional_info')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="additional_info_traditional" class="backend-form-label">Chinese Traditional</label>
                                                        <textarea id="additional_info_traditional" name="additional_info_traditional" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert additional information">{{ old('additional_info_traditional') }}</textarea>
                                                        @error('additional_info_traditional')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="backend-form-field">
                                                        <label for="additional_info_simplified" class="backend-form-label">Chinese Simplified</label>
                                                        <textarea id="additional_info_simplified" name="additional_info_simplified" class="textarea_editor backend-form-control border-radius-0" data-backend-richtext="true" placeholder="Insert additional information">{{ old('additional_info_simplified') }}</textarea>
                                                        @error('additional_info_simplified')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input class="backend-form-control" id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                        <input id="page" name="page" value="add-hotel" type="hidden">
                                        <input id="initial_state" name="initial_state" value="" type="hidden">
                                    </div>
                                </form>
                                <div class="backend-form-actions">
                                    <a href="{{ route('hotels-admin.index') }}" class="backend-button backend-button-secondary"><i class="fa fa-times"></i> Cancel</a>
                                    <button type="submit" form="add-hotel" class="backend-button backend-button-primary"><i class="fa fa-check" aria-hidden="true"></i> Add Hotel</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 desktop">
                            <div class="row">
                            </div>
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
</section>
@endsection
