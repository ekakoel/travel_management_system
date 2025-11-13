@php
    use Carbon\Carbon;
@endphp
@section('title', __('messages.Vendor Detail'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                {{-- NAVBAR PAGE ======================================================================================================================= --}}
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title"><i class="icon-copy fi-torso-business"></i> Detail {{ $vendor->name }}</div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                    <li class="breadcrumb-item"><a href="/vendors-admin">Vendors</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $vendor->name }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
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
                {{-- VENDOR DETAIL ======================================================================================================================= --}}
                <div class="product-wrap">
                    <div class="product-detail-wrap">
                        <div class="row">
                            <div class="col-md-4 mobile">
                                {{-- ATTENTIONS --}}
                                <div class="row">
                                    @include('layouts.attentions')
                                    <div class="col-md-12">
                                        <div class="card-box">
                                            <div class="row">
                                                <div class="col-6">
                                                        <p><b>{{ $vendor->name }}</b></p>
                                                        @if (isset($author))
                                                            <p><b>Author :</b> {{ $author->name }}</p>
                                                        @else
                                                            <p><b>Author :</b> -</p>
                                                        @endif
                                                    @if ($cpackages > 0)
                                                        <p><b>Services :</b> {{ $cpackages }}</p>
                                                    @else
                                                        <p><b>Services :</b> 0</p>
                                                    @endif
                                                </div>
                                                <div class="col-6 text-right">
                                                    <p><b>{{ dateTimeFormat($vendor->created_at) }}</b></p>
                                                    <p><i>{{ Carbon::parse($vendor->created_at)->diffForHumans();  }}</i></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fi-torso-business"></i> {{ $vendor->type }}</div>
                                        <div class="status-card">
                                            @if ($vendor->status == "Rejected")
                                                <div class="status-rejected"></div>
                                            @elseif ($vendor->status == "Invalid")
                                                <div class="status-invalid"></div>
                                            @elseif ($vendor->status == "Active")
                                                <div class="status-active"></div>
                                            @elseif ($vendor->status == "Waiting")
                                                <div class="status-waiting"></div>
                                            @elseif ($vendor->status == "Draft")
                                                <div class="status-draft"></div>
                                            @elseif ($vendor->status == "Archived")
                                                <div class="status-archived"></div>
                                            @else
                                            @endif
                                        </div>
                                    </div>
                                    <div class="page-card">
                                        <div class="image-container">
                                            <div class="card-banner">
                                                <div class="first m-t-18">
                                                    <ul class="card-lable">
                                                        <li class="item">
                                                            <div class="meta-box">
                                                                <p class="text"><i class="icon-copy fa fa-flag" aria-hidden="true"></i>{{ " ". $vendor->type }}</p>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <img src="{{ asset ('storage/vendors/covers/' . $vendor->cover) }}" alt="{{ $vendor->name }}" loading="lazy">
                                            </div>
                                        </div>
                                        <div class="card-content">
                                          
                                            <div class="card-text">
                                                <div class="row m-b-18">
                                                    <div class="col-6 col-sm-4">
                                                        <div class="card-subtitle">Name:</div>
                                                        <p>{{ ' '. $vendor->name }}</p>
                                                    </div>
                                                    <div class="col-6 col-sm-4">
                                                        <div class="card-subtitle">Location:</div>
                                                        <p>{{ $vendor->location }}</p>
                                                    </div>
                                                </div>
                                                <div class="row m-b-18">
                                                    <div class="col-6 col-sm-4">
                                                        <div class="card-subtitle">Contact Person:</div>
                                                        <p>{{ $vendor->contact_name }}</p>
                                                    </div>
                                                    <div class="col-6 col-sm-4">
                                                        <div class="card-subtitle">Phone:</div>
                                                        <p>{{ $vendor->phone }}</p>
                                                    </div>
                                                    <div class="col-6 col-sm-4">
                                                        <div class="card-subtitle">E-mail:</div>
                                                        <p>{{ $vendor->email }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-content">
                                        <div class="row">
                                            @if ($vendor->description)
                                                <div class="col-sm-12">
                                                    <div class="card-subtitle">Description:</div>
                                                    <p>{!! $vendor->description !!}</p>
                                                </div>
                                            @endif
                                            @if ($vendor->term)
                                                
                                                <div class="col-sm-12">
                                                    <div class="card-subtitle">Terms and Conditions:</div>
                                                    <p>{!! $vendor->term !!}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @canany(['posDev','weddingDvl','weddingAuthor'])
                                        <div class="card-box-footer">
                                            <a href="#" data-toggle="modal" data-target="#edit-vendor"><button class="btn btn-primary"><i class="fa fa-pencil"></i> Edit Vendor</button></a>
                                        </div>
                                        {{-- MODAL EDIT VENDOR --------------------------------------------------------------------------------------------------------------- --}}
                                        <div class="modal fade" id="edit-vendor" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Vendor</div>
                                                        </div>
                                                        <form id="update-vendor" action="/fupdate-vendor/{{ $vendor->id }}" method="post" enctype="multipart/form-data">
                                                            @method('put')
                                                            {{ csrf_field() }}
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    {{-- <div class="col-12 col-sm-12 col-md-12 m-b-18">
                                                                        <div class="row">
                                                                            <div class="col-12 col-sm-6 col-md-6">
                                                                                <div class="card-subtitle m-b-8">Cover Image</div>
                                                                                <img src="{{ asset ('storage/vendors/covers/' . $vendor->cover) }}" alt="{{ $vendor->name }}" loading="lazy">
                                                                            </div>
                                                                            <div class="col-12 col-sm-6 col-md-6">
                                                                                <div class="dropzone text-center b-0">
                                                                                    <div class="cover-preview-div">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="name">Cover</label>
                                                                            <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ $vendor->cover }}">
                                                                            @error('cover')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div> --}}
                                                                    <div class="col-12 col-sm-12 col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-12 col-sm-6">
                                                                                <div class="form-group">
                                                                                    <label for="edit-cover-img-preview" class="form-label">Cover Preview</label>
                                                                                    <div class="dropzone">
                                                                                        <div id="edit-cover-img-preview">
                                                                                            <img src="{{ asset ('storage/vendors/covers/' . $vendor->cover) }}" alt="{{ $vendor->name }}" loading="lazy">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="cover">Cover Image</label>
                                                                            <input type="file" name="cover" id="editCoverPackage" onchange="updateCoverPreview(event)" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}" required>
                                                                            @error('cover')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="status">Status <span>*</span></label>
                                                                                <select name="status" id="status"  type="text" class="custom-select @error('status') is-invalid @enderror" placeholder="Select status" required>
                                                                                    @if ($vendor->status == "Draft")
                                                                                        <option selected value="{{ $vendor->status }}">{{ $vendor->status }}</option>
                                                                                        <option value="Active">Active</option>
                                                                                    @else
                                                                                        <option selected value="{{ $vendor->status }}">{{ $vendor->status }}</option>
                                                                                        <option value="Draft">Draft</option>
                                                                                    @endif
                                                                                </select>
                                                                            @error('status')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="name">Name</label>
                                                                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ $vendor->name }}" required>
                                                                            @error('name')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="type">Type</label>
                                                                            <select name="type" id="type"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select type" required>
                                                                                <option selected value="{{ $vendor->type }}">{{ $vendor->type }}</option>
                                                                                <option value="Decoration">Decoration</option>
                                                                                <option value="Documentation">Documentation</option>
                                                                                <option value="Entertainment">Entertainment</option>
                                                                                <option value="Hotel">Hotel</option>
                                                                                <option value="Make-up Artist">Make-up Artist</option>
                                                                                <option value="Event Organizer">Event Organizer</option>
                                                                                <option value="Other">Other</option>
                                                                            </select>
                                                                            @error('type')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="col-12 col-sm-3 col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="contact_name">Contact Person</label>
                                                                            <input type="text" id="contact_name" name="contact_name" class="form-control @error('contact_name') is-invalid @enderror" placeholder="Name" value="{{ $vendor->contact_name }}" required>
                                                                            @error('contact_name')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-sm-3 col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="phone">Phone</label>
                                                                            <input type="number" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Name" value="{{ $vendor->phone }}" required>
                                                                            @error('phone')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="email">E-mail</label>
                                                                            <input type="text" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Address" value="{{ $vendor->email }}" required>
                                                                            @error('email')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="location">Location</label>
                                                                            <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" placeholder="Location" value="{{ $vendor->location }}" required>
                                                                            @error('location')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="description">Description</label>
                                                                            <textarea name="description" id="description" wire:model="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Description" type="text">{!! $vendor->description !!}</textarea>
                                                                            @error('description')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                            
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="term"></label>
                                                                            <textarea name="term" id="term" wire:model="term" class="textarea_editor form-control @error('term') is-invalid @enderror" placeholder="Description" type="text">{!! $vendor->term !!}</textarea>
                                                                            @error('term')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                            
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="card-box-footer">
                                                            <button type="submit" form="update-vendor" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcanany
                                </div>
                            </div>
                            <div class="col-md-4 desktop">
                                {{-- ATTENTIONS --}}
                                <div class="row">
                                    @include('layouts.attentions')
                                    <div class="col-md-12">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <div class="subtitle"><i class="icon-copy ion-ios-pulse-strong"></i>Vendor Log</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                        <p><b>{{ $vendor->name }}</b></p>
                                                        @if (isset($author))
                                                            <p><b>Author :</b> {{ $author->name }}</p>
                                                        @else
                                                            <p><b>Author :</b> -</p>
                                                        @endif
                                                    @if ($cpackages > 0)
                                                        <p><b>Services :</b> {{ $cpackages }}</p>
                                                    @else
                                                        <p><b>Services :</b> 0</p>
                                                    @endif
                                                </div>
                                                <div class="col-6 text-right">
                                                    <p><b>{{ dateTimeFormat($vendor->created_at) }}</b></p>
                                                    <p><i>{{ Carbon::parse($vendor->created_at)->diffForHumans();  }}</i></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                {{-- SERVICES ================================================================================================================================================================= --}}
                @if ($vendor->status == "Active")
                    <div id="package" class="product-wrap">
                        <div class="product-detail-wrap">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="icon-copy fa fa-cubes" aria-hidden="true"></i> Services</div>
                                        </div>
                                        @if ($cpackages > 0)
                                            @include('layouts.vendor_fixed_services')
                                            @include('layouts.vendor_decoration')
                                            @include('layouts.vendor_documentation')
                                            @include('layouts.vendor_entertainment')
                                            @include('layouts.vendor_makeup')
                                            @include('layouts.vendor_venue')
                                            @include('layouts.vendor_dinner_venue')
                                            @include('layouts.vendor_other')
                                        @else
                                            <div class="notif">!!! The vendor doesn't have a service yet, please add one!</div>
                                        @endif
                                        @canany(['posDev','weddingDvl','weddingAuthor'])
                                            <div class="card-box-footer">
                                                <a href="#" data-toggle="modal" data-target="#add-package"><button class="btn btn-primary"><i class="ion-plus-round"></i> Add Service</button></a>
                                            </div>
                                            {{-- MODAL ADD SERVICES --------------------------------------------------------------------------------------------------------------- --}}
                                            <div class="modal fade" id="add-package" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Create Service for {{ $vendor->name }}</div>
                                                            </div>
                                                            <form id="addPackage" action="/fadd-vendor-package" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                {{ csrf_field() }}
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-12 col-sm-12 col-md-12">
                                                                            <div class="row">
                                                                                <div class="col-12 col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="add-cover-img-preview" class="form-label">Cover Preview</label>
                                                                                        <div class="dropzone">
                                                                                            <div id="add-cover-img-preview">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-6 col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="cover">Cover Image</label>
                                                                                <input type="file" name="cover" id="addCoverPackage" onchange="addCoverPreview(event)" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}" required>
                                                                                @error('cover')
                                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-6 col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="service">Package Name</label>
                                                                                <input type="text" id="service" name="service" class="form-control @error('service') is-invalid @enderror" placeholder="Name" value="{{ old('service') }}" required>
                                                                                @error('service')
                                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-6 col-md-6">
                                                                            <div class="form-group ">
                                                                                <label for="type">Type <span>*</span></label>
                                                                                <select id="typeService" name="type" type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select type" required>
                                                                                    <option selected value="">Select Type</option>
                                                                                    <option value="Ceremony Venue Decoration">Ceremony Venue Decoration</option>
                                                                                    <option value="Reception Venue Decoration">Reception Venue Decoration</option>
                                                                                    <option value="Entertainment">Entertainment</option>
                                                                                    <option value="Make-up">Make-up</option>
                                                                                    <option value="Documentation">Documentation</option>
                                                                                    <option value="Other">Other</option>
                                                                                </select>
                                                                                @error('type')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div id="venueContainer" class="col-12 col-sm-6 col-md-6 hidden">
                                                                            <div class="form-group ">
                                                                                <label for="venue">Venue <span>*</span></label>
                                                                                <select name="venue" type="text" class="custom-select @error('venue') is-invalid @enderror" placeholder="Select venue">
                                                                                    <option selected value="">Select Venue</option>
                                                                                    <option value="Ceremony Venue">Ceremony Venue</option>
                                                                                    <option value="Reception Venue">Reception Venue</option>
                                                                                </select>
                                                                                @error('venue')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-6 col-sm-3 col-md-3">
                                                                            <div class="form-group">
                                                                                <label for="duration">Duration</label>
                                                                                <input type="number" id="duration" name="duration" class="form-control @error('duration') is-invalid @enderror" placeholder="Insert duration" value="{{ old('duration') }}" required>
                                                                                @error('duration')
                                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-6 col-sm-3 col-md-3">
                                                                            <div class="form-group ">
                                                                                <label for="time">Time <span>*</span></label>
                                                                                <select name="time" id="time"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select time">
                                                                                    <option selected value="minutes">Minutes</option>
                                                                                    <option value="hours">Hours</option>
                                                                                    <option value="days">Days</option>
                                                                                </select>
                                                                                @error('time')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group ">
                                                                                <label for="hotel_id">Hotel</label>
                                                                                <select name="hotel_id" id="hotel_id"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select hotel_id">
                                                                                    <option selected value="">Select Hotel</option>
                                                                                    @foreach ($hotels as $hotel)
                                                                                        <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                                @error('hotel_id')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-6 col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="capacity">Capacity</label>
                                                                                <div class="btn-icon">
                                                                                    <span><i class="icon-copy fa fa-users" aria-hidden="true"></i></span>
                                                                                    <input type="number" id="capacity" name="capacity" class="input-icon form-control @error('capacity') is-invalid @enderror" placeholder="Insert capacity" value="{{ old('capacity') }}">
                                                                                    @error('capacity')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-6 col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="contract_rate">Contract Rate <span>*</span></label>
                                                                                <div class="btn-icon">
                                                                                    <span>Rp</span>
                                                                                    <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ old('contract_rate') }}" required>
                                                                                    @error('contract_rate')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-6 col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="markup">Markup</label>
                                                                                <div class="btn-icon">
                                                                                    <span>$</span>
                                                                                    <input type="number" id="markup" name="markup" min="1" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ old('markup') }}">
                                                                                    @error('markup')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="description">Description</label>
                                                                                <textarea name="description" id="description-package" wire:model="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Description" type="text">{!! old('description') !!}</textarea>
                                                                                @error('description')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="addPackage" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Create</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcanany
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- FOOTER PAGE ======================================================================================================================= --}}
                @include('layouts.footer')
            </div>
        </div>
    @endcan
    <script>
        document.getElementById('typeService').addEventListener('change', function() {
            var venueContainer = document.getElementById('venueContainer');
            if (this.value === 'Other') {
                venueContainer.classList.remove('hidden');
            } else if(this.value === 'Entertainment') {
                venueContainer.classList.remove('hidden');
            }else {
                venueContainer.classList.add('hidden');
            }
        });
        document.getElementById('fixedType').addEventListener('change', function() {
            var venueContainer = document.getElementById('fixedContainer');
            if (this.value === 'Other') {
                venueContainer.classList.remove('hidden');
            } else if(this.value === 'Entertainment') {
                venueContainer.classList.remove('hidden');
            }else {
                venueContainer.classList.add('hidden');
            }
        });
    </script>
    <script>
        function addCoverPreview(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function() {
                var dataURL = reader.result;
                var previewDiv = document.getElementById('add-cover-img-preview');
                previewDiv.innerHTML = '';
                var imgElement = document.createElement('img');
                imgElement.src = dataURL;
                imgElement.className = 'img-fluid rounded';
                previewDiv.appendChild(imgElement);
            };
            reader.readAsDataURL(input.files[0]);
        }
    </script>
    <script>
        function updateCoverPreview(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function() {
                var dataURL = reader.result;
                var previewDiv = document.getElementById('edit-cover-img-preview');
                previewDiv.innerHTML = '';
                var imgElement = document.createElement('img');
                imgElement.src = dataURL;
                imgElement.className = 'img-fluid rounded';
                previewDiv.appendChild(imgElement);
            };
            reader.readAsDataURL(input.files[0]);
        }
    </script>
    <script>
        CKEDITOR.replace('update-description-package1');
        CKEDITOR.replace('update-description-package2');
        CKEDITOR.replace('update-description-package3');
        CKEDITOR.replace('update-description-package4');
        CKEDITOR.replace('update-description-package5');
        CKEDITOR.replace('update-description-package6');
        CKEDITOR.replace('update-description-package7');
        CKEDITOR.replace('update-description-package8');
        CKEDITOR.replace('update-description-package9');
        CKEDITOR.replace('update-description-package10');
        CKEDITOR.replace('update-description-package11');
        CKEDITOR.replace('update-description-package12');
        CKEDITOR.replace('update-description-package13');
        CKEDITOR.replace('update-description-package14');
    </script>
@endsection