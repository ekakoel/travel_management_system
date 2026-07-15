@section('title', __('messages.Activity'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Activity</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                @if (isset($activities->partners_id))
                                    <li class="breadcrumb-item"><a href="/detail-partner-{{ $activities->partners_id }}">{{ $partner->name }}</a></li>
                                @else
                                    <li class="breadcrumb-item">?</li>
                                @endif
                                <li class="breadcrumb-item"><a href="/activities-admin">Activities</a></li>
                                <li class="breadcrumb-item"><a href="/detail-activity-{{ $activities->id }}">Activity Detail</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ "Edit Activity ". $activities->name }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        {{-- ATTENTIONS --}}
                        <div class="col-md-4 mobile">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card-box p-b-18 m-b-18">
                                        <div class="subtitle"><i class="icon-copy fa fa-money"></i>USD Rate : {{ currencyFormatIdr($usdrates->rate) }}</div>
                                    </div>
                                </div>
                                @include('layouts.attentions')
                            </div>
                        </div>
                        <div class="col-md-8 m-b-18">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-pencil"></i>{{ "Edit ". $activities->name }}</div>
                                </div>
                                <form id="edit-activities" action="/fupdate-activity/{{ $activities->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="dropzone">
                                                        <div class="cover-preview-div">
                                                            <img src="{{ asset('storage/activities/activities-cover/' . $activities->cover)  }}" alt="{{ $activities->name }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="cover">Cover Image </label>
                                                <input type="file" name="cover" id="cover" class="form-control custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="cover">Status </label>
                                                <select id="status" name="status" class="custom-select col-12 @error('status') is-invalid @enderror" required>
                                                    <option selected="{{ $activities->status }}">{{ $activities->status }}</option>
                                                    <option value="Active">Active</option>
                                                    <option value="Draft">Draft</option>
                                                    <option value="Archived">Archived</option>
                                                </select>
                                                @error('status')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="name">Name </label>
                                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert name..." value="{{ $activities->name }}" required>
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="partners_id">Partner <span> *</span></label>
                                                <select id="partners_id" name="partners_id" value="{{ old('partners_id') }}" class="custom-select col-12 @error('partners_id') is-invalid @enderror" required>
                                                    @if (isset($partner))
                                                        <option selected value="{{ $partner->id }}">{{ $partner->name }}</option>
                                                    @else
                                                        <option selected value="">Select Partner</option>
                                                    @endif
                                                    @foreach ($partners as $partner)
                                                        <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('partners_id')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="map">Map </label>
                                                <input type="text" id="map" name="map" class="form-control @error('map') is-invalid @enderror" placeholder="Insert Google Map..." value="{{ $activities->map }}" required>
                                                @error('map')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="location">Location </label>
                                                <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" placeholder="Insert location..." value="{{ $activities->location }}" required>
                                                @error('location')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="type">Type <span> *</span></label>
                                                <select id="type" name="type" class="custom-select @error('type') is-invalid @enderror" required>
                                                    <option selected value="{{ $activities->type }}">{{ $activities->type }}</option>
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
                                            <div class="form-group">
                                                <label for="duration">Duration <span> *</span></label>
                                                <select id="duration" name="duration" value="{{ old('duration') }}" class="custom-select col-12 @error('duration') is-invalid @enderror" required>
                                                    <option selected value="{{ $activities->duration }}">{{ $activities->duration }}</option>
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
                                            <div class="form-group">
                                                <label for="min_pax">Minimum Order </label>
                                                <input type="number" id="min_pax" name="min_pax" value="{{ $activities->min_pax }}" class="form-control @error('min_pax') is-invalid @enderror" placeholder="Minimum order" required>
                                                @error('min_pax')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="qty">Capacity </label>
                                                <input type="number" id="qty" name="qty" value="{{ $activities->qty }}" class="form-control @error('qty') is-invalid @enderror" placeholder="Capacity" required>
                                                @error('qty')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="contract_rate">Contract Rate <span> *</span></label>
                                                <div class="btn-icon">
                                                    <span>Rp</span>
                                                    <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ $activities->contract_rate }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="markup">Markup <span> *</span></label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ $activities->markup }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="validity">Valid Until </label>
                                                <input type="text" id="validity" name="validity" value="{{ date('d M Y',strtotime($activities->validity)) }}" class="form-control date-picker @error('validity') is-invalid @enderror" placeholder="Select date" required>
                                                @error('validity')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="description">Description </label>
                                                <textarea id="description" name="description" class="textarea_editor form-control border-radius-0" required>{!! $activities->description !!}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="itinerary">Itinerary </label>
                                                <textarea id="itinerary" name="itinerary" class="textarea_editor form-control border-radius-0" required>{!! $activities->itinerary !!}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="include">Include </label>
                                                <textarea id="include" name="include" class="textarea_editor form-control border-radius-0" required>{!! $activities->include !!}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="cancellation_policy">Cancellation Policy</label>
                                                <textarea id="cancellation_policy" name="cancellation_policy" class="textarea_editor form-control border-radius-0">{!! $activities->cancellation_policy !!}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="additional_info">Additional Information</label>
                                                <textarea id="additional_info" name="additional_info" class="textarea_editor form-control border-radius-0">{!! $activities->additional_info !!}</textarea>
                                            </div>
                                        </div>
                                        <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                        <input id="page" name="page" value="admin-tour-edit" type="hidden">
                                        <input id="initial_state" name="initial_state" value="{{ $activities->status }}" type="hidden">
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="edit-activities" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Save</button>
                                    <a href="detail-activity-{{ $activities['id'] }}">
                                        <button type="button"class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- ATTENTIONS --}}
                        <div class="col-md-4 desktop">
                            <div class="row">
                                @include('admin.usd-rate')
                                @include('layouts.attentions')
                            </div>
                        </div>
                    </div>
                       
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
@endsection