@section('title', __('messages.Weddings'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <x-backend.page-hero>
                        <x-slot name="heading">
                            <i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Dinner Package
                        </x-slot>
                    </x-backend.page-hero>
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
                    <div class="row">
                        <div class="col-md-4 mobile">
                            <div class="row">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">Add Dinner Package</div>
                                </div>
                                <form id="add-dinner-package" action="/fcreate-dinner-package/{{ $hotel->id }}" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="backend-form-field">
                                                <label for="dinner_venues_id" class="form-label">Dinner Venue <span>*</span></label>
                                                <select id="dinner_venues_id" name="dinner_venues_id" class="backend-form-control col-12 @error('dinner_venues_id') is-invalid @enderror" required>
                                                    <option selected value="">Select dinner venue</option>
                                                @foreach ($dinnerVenues as $dinnerVenue)
                                                        <option value="{{ $dinnerVenue->id }}" data-max="{{ $dinnerVenue->capacity }}" data-min="{{ $dinnerVenue->min_invitations }}">{{ $dinnerVenue->name." (".$dinnerVenue->min_invitations." -> ".$dinnerVenue->capacity." guests)" }}</option>
                                                @endforeach
                                                </select>
                                                @error('status')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="dinner_package_name" class="form-label">Package Name</label>
                                                <input type="text" name="dinner_package_name" class="backend-form-control @error('dinner_package_name') is-invalid @enderror" placeholder="Name" value="{{ old('dinner_package_name') }}" required>
                                                @error('dinner_package_name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="number_of_guests" class="form-label">Number of Invitations<span id="max_capacity_label"></span></label>
                                                <input id="number_of_guests" type="number"  name="number_of_guests" class="backend-form-control @error('number_of_guests') is-invalid @enderror" placeholder="Number of invitations" value="{{ old('number_of_guests') }}" required>
                                                @error('number_of_guests')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="backend-form-field">
                                                <label for="include" class="form-label">Include </label>
                                                <textarea data-backend-richtext="true" name="include" class="textarea_editor backend-form-control @error('include') is-invalid @enderror" placeholder="Insert include" value="{{ old('include') }}">{{ old('include') }}</textarea>
                                                @error('include')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="backend-form-field">
                                                <label for="additional_info" class="form-label">Additional Information</label>
                                                <textarea data-backend-richtext="true" name="additional_info" class="textarea_editor backend-form-control @error('additional_info') is-invalid @enderror" placeholder="Insert additional_info" value="{{ old('additional_info') }}">{!! old('additional_info') !!}</textarea>
                                                @error('additional_info')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="additional_guest_rate">Additional Guest Rate <span>*</span></label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="text" id="additional_guest_rate" name="additional_guest_rate"  class="backend-form-control numeric-input @error('additional_guest_rate') is-invalid @enderror" value="{{ old('additional_guest_rate') }}" required>
                                                    @error('additional_guest_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="public_rate">Public Rate <span>*</span></label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="text" id="public_rate" name="public_rate"  class="backend-form-control numeric-input @error('public_rate') is-invalid @enderror" value="{{ old('public_rate') }}" required>
                                                    @error('public_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="add-dinner-package" class="backend-button backend-button-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                    <a href="/weddings-hotel-admin-{{ $hotel->id }}#wedding-venues">
                                        <button type="button"class="backend-button backend-button-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                    </a>
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

@endsection
