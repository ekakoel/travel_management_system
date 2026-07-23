@section('title', __('messages.Partner'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <x-backend.page-hero>
                        <x-slot name="heading">
                            <i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Activity
                        </x-slot>
                    </x-backend.page-hero>
                    <div class="info-action">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (\Session::has('success'))
                            <div class="alert alert-success">
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
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">Add Activity to {{ $partners->name }}</div>
                                </div>
                                <form id="add-activity" action="/fpartner-add-activity" method="post" enctype="multipart/form-data" id="addhotel">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="card-subtitle m-b-8">Cover Image</div>
                                                    <div class="dropzone text-center pd-20 m-b-18">
                                                        <div class="cover-preview-div">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="backend-form-field">
                                                <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="name">Name</label>
                                                <input type="text" id="name" name="name" class="backend-form-control @error('name') is-invalid @enderror" placeholder="Insert activity name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="map">Map</label>
                                                <input type="text" id="map" name="map" class="backend-form-control @error('map') is-invalid @enderror" placeholder="Activity Location" value="{{ old('map') }}" required>
                                                @error('map')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="location">Location</label>
                                                <input type="text" id="location" name="location" class="backend-form-control @error('location') is-invalid @enderror" placeholder="Activity Location" value="{{ old('location') }}" required>
                                                @error('location')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="type">Type<span> *</span></label>
                                                <select id="type" name="type" class="backend-form-control col-12 @error('type') is-invalid @enderror" value="{{ old('type') }}" required>
                                                    <option selected value="">Select Type</option>
                                                    @foreach ($type as $type)
                                                        <option value="{{ $type->type }}">{{ $type->type }}</option>
                                                    @endforeach
                                                </select>
                                                @error('type')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="duration">Duration<span> *</span></label>
                                                <select id="duration" name="duration" value="{{ old('duration') }}" class="backend-form-control col-12 @error('duration') is-invalid @enderror" required>
                                                    <option selected="" value="">Select Duration</option>
                                                    <option value="15 Minutes">15 Minutes</option>
                                                    <option value="30 Minutes">30 Minutes</option>
                                                    <option value="1 Hour">1 Hours</option>
                                                    <option value="2 Hours">2 Hours</option>
                                                    <option value="3 Hours">3 Hours</option>
                                                    <option value="4 Hours">4 Hours</option>
                                                    <option value="5 Hours">5 Hours</option>
                                                    <option value="6 Hours">6 Hours</option>
                                                    <option value="7 Hours">7 Hours</option>
                                                    <option value="8 Hours">8 Hours</option>
                                                    <option value="9 Hours">9 Hours</option>
                                                    <option value="10 Hours">Full Day (10 hours)</option>
                                                </select>
                                                @error('duration')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="min_pax">Minimum Order</label>
                                                <input type="number" id="min_pax" name="min_pax" value="{{ old('min_pax') }}" class="backend-form-control @error('min_pax') is-invalid @enderror" placeholder="Minimum Order" required>
                                                @error('min_pax')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="qty">Capacity</label>
                                                <input type="number" id="qty" name="qty" value="{{ old('qty') }}" class="backend-form-control @error('qty') is-invalid @enderror" placeholder="Insert capacity" required>
                                                @error('qty')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="contract_rate">Contract Rate<span> *</span></label>

                                                    <div class="btn-icon">
                                                        <span>Rp</span>
                                                        <input type="number" id="contract_rate" name="contract_rate" class="input-icon backend-form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ old('contract_rate') }}" required>
                                                    </div>

                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="markup">Markup<span> *</span></label>

                                                    <div class="btn-icon">
                                                        <span>$</span>
                                                        <input type="number" id="markup" name="markup" class="input-icon backend-form-control @error('markup') is-invalid @enderror" placeholder="Insert Markup" value="{{ old("markup") }}">
                                                    </div>

                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="validity">Validity</label>
                                                <input type="text" id="validity" name="validity" value="{{ old('validity') }}" class="backend-form-control date-picker @error('validity') is-invalid @enderror" placeholder="Select date" required>
                                                @error('validity')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="backend-form-field">
                                                <label for="description">Description</label>
                                                <textarea data-backend-richtext="true" id="description" name="description" class="textarea_editor backend-form-control border-radius-0 @error('itinerary') is-invalid @enderror" placeholder="Insert description..." value="{{ old('description') }}" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="backend-form-field">
                                                <label for="itinerary">Itinerary</label>
                                                <textarea data-backend-richtext="true" id="itinerary" name="itinerary" class="textarea_editor backend-form-control border-radius-0 @error('itinerary') is-invalid @enderror" placeholder="Enter text..." value="{{ old('itinerary') }}"></textarea>
                                                @error('itinerary')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="backend-form-field">
                                                <label for="include">Include</label>
                                                <textarea data-backend-richtext="true" id="include" name="include" class="textarea_editor backend-form-control border-radius-0 @error('include') is-invalid @enderror" placeholder="Enter text..." value="{{ old('include') }}"></textarea>
                                                @error('include')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="backend-form-field">
                                                <label for="cancellation_policy">Cancellation Policy</label>
                                                <textarea data-backend-richtext="true" id="cancellation_policy" name="cancellation_policy" class="textarea_editor backend-form-control border-radius-0 @error('cancellation_policy') is-invalid @enderror" placeholder="Enter text..." value="{{ old('cancellation_policy') }}"></textarea>
                                                @error('cancellation_policy')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="backend-form-field">
                                                <label for="additional_info">Additional Information</label>
                                                <textarea data-backend-richtext="true" id="additional_info" name="additional_info" class="textarea_editor backend-form-control border-radius-0 @error('additional_info') is-invalid @enderror" placeholder="Enter text..." value="{{ old('additional_info') }}"></textarea>
                                                @error('additional_info')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                        <input id="partners_id" name="partners_id" value="{{ $partners->id }}" type="hidden">
                                        <input id="page" name="page" value="add-activity" type="hidden">
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="add-activity" class="backend-button backend-button-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add</button>
                                    <a href="/detail-partner-{{ $partners->id }}">
                                        <button type="button"class="backend-button backend-button-danger"><i class="icon-copy fa fa-remove" aria-hidden="true"></i> Cancel</button>
                                    </a>
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
