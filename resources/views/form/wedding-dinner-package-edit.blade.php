@section('title', __('messages.Weddings'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title">
                            <i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Dinner Package</div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/weddings-admin">Vendors</a></li>
                                <li class="breadcrumb-item"><a href="/weddings-hotel-admin-{{ $hotel->id }}">{{ $hotel->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $dinnerPackage->name }}</li>
                            </ol>
                        </nav>
                    </div>
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
                        {{-- ATTENTIONS --}}
                        <div class="col-md-4 mobile">
                            <div class="row">
                                @include('layouts.attentions')
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">Detail {{ $dinnerPackage->name }}</div>
                                </div>
                                <form id="update-dinner-package" action="/fupdate-dinner-package-{{ $dinnerPackage->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="cover" class="form-label">Status</label>
                                                        <select id="status" name="status" class="form-control custom-select @error('status') is-invalid @enderror" required>
                                                            <option selected="{{ $dinnerPackage->status }}">{{ $dinnerPackage->status }}</option>
                                                            <option value="Active">Active</option>
                                                            <option value="Draft">Draft</option>
                                                            <option value="Archived">Archived</option>
                                                        </select>
                                                        @error('status')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group">
                                                        <label for="dinner_venues_id" class="form-label">Dinner Venue <span>*</span></label>
                                                        <select id="dinner_venues_id" name="dinner_venues_id" class="custom-select col-12 @error('dinner_venues_id') is-invalid @enderror" required>
                                                            <option selected value="{{ $dinner_venue->id }}">{{ $dinner_venue->name." (".$dinner_venue->min_invitations." -> ".$dinner_venue->capacity." guests)" }}</option>
                                                           @foreach ($dinnerVenues as $dinnerVenue)
                                                                @if ($dinnerVenue->id != $dinner_venue->id)
                                                                    <option value="{{ $dinnerVenue->id }}" data-max="{{ $dinnerVenue->capacity }}" data-min="{{ $dinnerVenue->min_invitations }}">{{ $dinnerVenue->name." (".$dinnerVenue->min_invitations." -> ".$dinnerVenue->capacity." guests)" }}</option>
                                                                @endif
                                                           @endforeach
                                                        </select>
                                                        @error('status')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <div class="form-group">
                                                <label for="dinner_package_name" class="form-label">Package Name</label>
                                                <input type="text" name="dinner_package_name" class="form-control @error('dinner_package_name') is-invalid @enderror" placeholder="Name" value="{{ $dinnerPackage->name }}" required>
                                                @error('dinner_package_name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <div class="form-group">
                                                <label for="number_of_guests" class="form-label">Number of Invitations<span id="max_capacity_label"> (Min: {{ $dinner_venue->min_invitations }}, Max: {{ $dinner_venue->capacity }})</span></label>
                                                <input id="number_of_guests" min="{{ $dinner_venue->min_invitations }}" max="{{ $dinner_venue->capacity }}" type="number"  name="number_of_guests" class="form-control @error('number_of_guests') is-invalid @enderror" placeholder="Number of invitations" value="{{ $dinnerPackage->number_of_guests }}" required>
                                                @error('number_of_guests')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="include" class="form-label">Include</label>
                                                <textarea name="include" class="textarea_editor form-control @error('include') is-invalid @enderror" placeholder="Insert include" value="{{ $dinnerPackage->include }}">{{ $dinnerPackage->include }}</textarea>
                                                @error('include')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="additional_info" class="form-label">Additional Information</label>
                                                <textarea name="additional_info" class="textarea_editor form-control @error('additional_info') is-invalid @enderror" placeholder="Insert additional_info" value="{{ $dinnerPackage->additional_info }}">{{ $dinnerPackage->additional_info }}</textarea>
                                                @error('additional_info')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <div class="form-group">
                                                <label for="additional_guest_rate">Additional Guest Rate <span>*</span></label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="text" id="additional_guest_rate" name="additional_guest_rate"  class="form-control numeric-input @error('additional_guest_rate') is-invalid @enderror" value="{{ number_format($dinnerPackage->additional_guest_rate, 0, ",", ",") }}" required>
                                                    @error('additional_guest_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <div class="form-group">
                                                <label for="public_rate">Public Rate <span>*</span></label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="text" id="public_rate" name="public_rate"  class="form-control numeric-input @error('public_rate') is-invalid @enderror" value="{{ number_format($dinnerPackage->public_rate, 0, ",", ",") }}" required>
                                                    @error('public_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="update-dinner-package" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                    <a href="/weddings-hotel-admin-{{ $hotel->id }}#wedding-venues">
                                        <button type="button"class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- ATTENTIONS --}}
                        <div class="col-md-4 desktop">
                            <div class="row">
                                @include('layouts.attentions')
                            </div>
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
        <script>
            document.getElementById('dinner_venues_id').addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                var maxCapacity = selectedOption.getAttribute('data-max');
                var minCapacity = selectedOption.getAttribute('data-min');
                document.getElementById('number_of_guests').setAttribute('max', maxCapacity);
                document.getElementById('number_of_guests').setAttribute('min', minCapacity);
                document.getElementById('max_capacity_label').textContent = maxCapacity ? ' (Min: ' + minCapacity + ', Max: '+ maxCapacity +')' : '';
            });
        </script>
    @endcan

@endsection
    
