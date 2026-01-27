@php
    use Carbon\Carbon;
@endphp
@section('title', __('messages.Partner Detail'))
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
                            <div class="title"><i class="micon fa fa-handshake-o" aria-hidden="true"></i> Detail {{ $partner->name }}</div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                    <li class="breadcrumb-item"><a href="/partners">Partners</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $partner->name }}</li>
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
                {{-- PARTNER DETAIL ======================================================================================================================= --}}
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
                                                        <p><b>{{ $partner->name }}</b></p>
                                                        @if (isset($author))
                                                            <p><b>Author :</b> {{ $author->name }}</p>
                                                        @else
                                                            <p><b>Author :</b> -</p>
                                                        @endif
                                                    @if ($cservice > 0)
                                                        <p><b>Services :</b> {{ $cservice }}</p>
                                                    @else
                                                        <p><b>Services :</b> 0</p>
                                                    @endif
                                                </div>
                                                <div class="col-6 text-right">
                                                    <p><b>{{ dateTimeFormat($partner->created_at) }}</b></p>
                                                    <p><i>{{ Carbon::parse($partner->created_at)->diffForHumans();  }}</i></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fa fa-handshake-o" aria-hidden="true"></i>Partner Details</div>
                                        <div class="status-card">
                                            @if ($partner->status == "Rejected")
                                                <div class="status-rejected"></div>
                                            @elseif ($partner->status == "Invalid")
                                                <div class="status-invalid"></div>
                                            @elseif ($partner->status == "Active")
                                                <div class="status-active"></div>
                                            @elseif ($partner->status == "Waiting")
                                                <div class="status-waiting"></div>
                                            @elseif ($partner->status == "Draft")
                                                <div class="status-draft"></div>
                                            @elseif ($partner->status == "Archived")
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
                                                                <p class="text"><i class="icon-copy fa fa-flag" aria-hidden="true"></i>{{ " ". $partner->type }}</p>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <img src="{{ asset ('storage/partners/covers/' . $partner->cover) }}" alt="{{ $partner->name }}" loading="lazy">
                                            </div>
                                        </div>
                                        <div class="card-content">
                                          
                                            <div class="card-text">
                                                <div class="row ">
                                                    <div class="col-12 col-sm-4">
                                                        <div class="card-subtitle">Address:</div>
                                                        <a target="__blank" href="{{ $partner->map }}"><p><i class="icon-copy fa fa-map-marker" aria-hidden="true"> </i>{{ ' '. $partner->address }}</p></a>
                                                    </div>
                                                    <div class="col-6 col-sm-4">
                                                        <div class="card-subtitle">Region:</div>
                                                        <p>{{ $partner->location }}</p>
                                                    </div>
                                                    <div class="col-6 col-sm-4">
                                                        <div class="card-subtitle">Contact Person:</div>
                                                        <p>{{ $partner->contact_person.' ('. $partner->phone.')' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @canany(['posDev','posAuthor'])
                                        <div class="card-box-footer">
                                            <a href="#" data-toggle="modal" data-target="#edit-partner"><button class="btn btn-primary"><i class="fa fa-pencil"></i> Edit Partner</button></a>
                                        </div>
                                        {{-- MODAL EDIT PARTNER --------------------------------------------------------------------------------------------------------------- --}}
                                        <div class="modal fade" id="edit-partner" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Partner</div>
                                                        </div>
                                                        <form id="update-partner" action="/fupdate-partner/{{ $partner->id }}" method="post" enctype="multipart/form-data">
                                                            @method('put')
                                                            {{ csrf_field() }}
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-12 col-sm-12 col-md-12 m-b-18">
                                                                        <div class="row">
                                                                            <div class="col-12 col-sm-6 col-md-6">
                                                                                <div class="card-subtitle m-b-8">Cover Image</div>
                                                                                <img src="{{ asset ('storage/partners/covers/' . $partner->cover) }}" alt="{{ $partner->name }}" loading="lazy">
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
                                                                            <label for="name">Profile Picture</label>
                                                                            <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ $partner->cover }}">
                                                                            @error('cover')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="status">Status</label>
                                                                                <select name="status" id="status"  type="text" class="custom-select @error('status') is-invalid @enderror" placeholder="Select status" required>
                                                                                    @if ($partner->status == "Draft")
                                                                                        <option selected value="{{ $partner->status }}">{{ $partner->status }}</option>
                                                                                        <option value="Active">Active</option>
                                                                                    @else
                                                                                        <option selected value="{{ $partner->status }}">{{ $partner->status }}</option>
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
                                                                            <label for="name">Partner Name</label>
                                                                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ $partner->name }}" required>
                                                                            @error('name')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="type">Type</label>
                                                                            <select name="type" id="type"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select type" required>
                                                                                <option selected value="{{ $partner->type }}">{{ $partner->type }}</option>
                                                                                <option value="Tourist Attraction">Tourist Attraction</option>
                                                                                <option value="Travel Agent">Travel Agent</option>
                                                                                <option value="Tour Agent">Tour Agent</option>
                                                                                <option value="Hotel">Hotel</option>
                                                                                <option value="Transport">Transport</option>
                                                                                <option value="Event Organizer">Event Organizer</option>
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
                                                                            <label for="contact_person">Contact Person</label>
                                                                            <input type="text" id="contact_person" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" placeholder="Name" value="{{ $partner->contact_person }}" required>
                                                                            @error('contact_person')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-sm-3 col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="phone">Phone</label>
                                                                            <input type="number" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Name" value="{{ $partner->phone }}" required>
                                                                            @error('phone')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="address">Address</label>
                                                                            <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Address" value="{{ $partner->address }}" required>
                                                                            @error('address')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="location">Location</label>
                                                                            <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" placeholder="Location" value="{{ $partner->location }}" required>
                                                                            @error('location')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-sm-6 col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="map">Map Link</label>
                                                                            <input type="text" id="map" name="map" class="form-control @error('map') is-invalid @enderror" placeholder="link" value="{{ $partner->map }}" required>
                                                                            @error('map')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="description">Description</label>
                                                                            <textarea name="description" id="description" wire:model="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Description" type="text">{!! $partner->description !!}</textarea>
                                                                            @error('description')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                            
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <input id="author_id" name="author_id" value="{{ Auth::user()->id }}" type="hidden">
                                                        </form>
                                                        <div class="card-box-footer">
                                                            <button type="submit" form="update-partner" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
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
                                                <div class="subtitle"><i class="icon-copy ion-ios-pulse-strong"></i>Partner Log</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                        <p><b>{{ $partner->name }}</b></p>
                                                        @if (isset($author))
                                                            <p><b>Author :</b> {{ $author->name }}</p>
                                                        @else
                                                            <p><b>Author :</b> -</p>
                                                        @endif
                                                    @if ($cservice > 0)
                                                        <p><b>Services :</b> {{ $cservice }}</p>
                                                    @else
                                                        <p><b>Services :</b> 0</p>
                                                    @endif
                                                </div>
                                                <div class="col-6 text-right">
                                                    <p><b>{{ dateTimeFormat($partner->created_at) }}</b></p>
                                                    <p><i>{{ Carbon::parse($partner->created_at)->diffForHumans();  }}</i></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                {{-- ACTIVITY ================================================================================================================================================================= --}}
                <div id="activity" class="product-wrap">
                    <div class="product-detail-wrap">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fa fa-child"></i>Activities</div>
                                    </div>
                                    @if (count($activitys) > 0)
                                        <div class="card-box-content">
                                            @foreach ($activitys as $activity)
                                                <div class="card">
                                                    <div class="image-container">
                                                        <a href="#" data-toggle="modal" data-target="#detail-activity-{{ $activity->id }}">
                                                            <div class="card-status">
                                                                @if ($activity->status == "Rejected")
                                                                    <div class="status-rejected"></div>
                                                                @elseif ($activity->status == "Invalid")
                                                                    <div class="status-invalid"></div>
                                                                @elseif ($activity->status == "Active")
                                                                    <div class="status-active"></div>
                                                                @elseif ($activity->status == "Waiting")
                                                                    <div class="status-waiting"></div>
                                                                @elseif ($activity->status == "Draft")
                                                                    <div class="status-draft"></div>
                                                                @elseif ($activity->status == "Archived")
                                                                    <div class="status-archived"></div>
                                                                @else
                                                                @endif
                                                            </div>
                                                            @if ($activity->status != "Active")
                                                                <img class="img-fluid rounded thumbnail-image grayscale" src="{{ url('storage/activities/activities-cover/' . $activity->cover) }}" alt="{{ $activity->activity }}">
                                                            @else
                                                                <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/activities/activities-cover/' . $activity->cover) }}" alt="{{ $activity->activity }}">
                                                            @endif
                                                            <div class="name-card">
                                                                <p>
                                                                    {{ $activity->name }}
                                                                </p>
                                                            </div>
                                                        </a>
                                                    </div>
                                                    @if ($activity->status != 'Draft')
                                                        <div class="price-card m-t-8">
                                                            @php
                                                                $usrate = ceil($activity->contract_rate / $usdrates->rate);
                                                                $harga_sebelum_tax = $usrate + $activity->markup;
                                                                $pajak = ceil($harga_sebelum_tax * $tax->tax / 100 );
                                                                $harga_final = $harga_sebelum_tax + $pajak;
                                                            @endphp
                                                            {{ '$ '. $harga_final}}
                                                        </div>
                                                    @endif
                                                    @canany(['posDev','posAuthor'])
                                                        <div class="card-delete-btn">
                                                            <form action="/remove-activity/{{ $activity->id }}" method="post">
                                                                @csrf
                                                                @method('delete')
                                                                <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                <input id="partners_id" name="partners_id" value="{{ $partner->id }}" type="hidden">
                                                                <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                            </form>
                                                        </div>
                                                    @endcanany
                                                </div>
                                                {{-- MODAL ACTIVITY DETAIL --------------------------------------------------------------------------------------------------------------- --}}
                                                <div class="modal fade" id="detail-activity-{{ $activity->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-eye"></i>Detail Activity {{ $activity->name }}</div>
                                                                    <div class="status-card">
                                                                        @if ($activity->status == "Rejected")
                                                                            <div class="status-rejected"></div>
                                                                        @elseif ($activity->status == "Invalid")
                                                                            <div class="status-invalid"></div>
                                                                        @elseif ($activity->status == "Active")
                                                                            <div class="status-active"></div>
                                                                        @elseif ($activity->status == "Waiting")
                                                                            <div class="status-waiting"></div>
                                                                        @elseif ($activity->status == "Draft")
                                                                            <div class="status-draft"></div>
                                                                        @elseif ($activity->status == "Archived")
                                                                            <div class="status-archived"></div>
                                                                        @else
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="page-card">
                                                                    <figure class="card-banner">
                                                                        <img src="{{ asset ('storage/activities/activities-cover/' . $activity->cover) }}" alt="{{ $activity->name }}" loading="lazy">
                                                                    </figure>
                                                                    <div class="card-content">
                                                                        <div class="card-text">
                                                                            <div class="row ">
                                                                                <div class="col-6 col-sm-6">
                                                                                    <div class="card-subtitle">Capacity:</div>
                                                                                    <p>{{ $activity->qty. " Guest" }}</p>
                                                                                </div>
                                                                                @if ($activity->status != 'Draft')
                                                                                    <div class="col-6 col-sm-6">
                                                                                        <div class="card-subtitle">Price:</div>
                                                                                        <p>{{ '$ '.$harga_final. " /pax" }}</p>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        @if ($activity->include != "")
                                                                            <hr class="form-hr">
                                                                            <div class="card-text">
                                                                                <div class="card-subtitle">Include:</div>
                                                                                <p>{!! $activity->include !!}</p>
                                                                            </div>
                                                                        @endif
                                                                        @if ($activity->additional_info != "")
                                                                            <hr class="form-hr">
                                                                            <div class="card-text">
                                                                                <div class="card-subtitle">additional_info:</div>
                                                                                <p>{!! $activity->additional_info !!}</p>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="card-box-footer">
                                                                    @canany(['posDev','posAuthor'])
                                                                        <a href="/edit-activity-{{ $activity->id }}">
                                                                            <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                                        </a>
                                                                    @endcanany
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="notif">!!! The partner doesn't have a service yet, please add a service now!</div>
                                    @endif
                                    @canany(['posDev','posAuthor'])
                                        <div class="card-box-footer">
                                            <a href="/partner-add-activity-{{ $partner->id }}">
                                                <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Activity</button>
                                            </a>
                                        </div>
                                    @endcanany
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- TOUR PACKAGE ================================================================================================================================================================= --}}
                <div id="tours" class="product-wrap m-b-18">
                    <div class="product-detail-wrap">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fa fa-briefcase"></i>Tour Package</div>
                                    </div>
                                    @if (count($tours) > 0)
                                        <div class="card-box-content">
                                            @foreach ($tours as $tour)
                                                <div class="card">
                                                    <div class="image-container">
                                                        <a href="#" data-toggle="modal" data-target="#detail-tour-{{ $tour->id }}">
                                                            <div class="card-status">
                                                                @if ($tour->status == "Rejected")
                                                                    <div class="status-rejected"></div>
                                                                @elseif ($tour->status == "Invalid")
                                                                    <div class="status-invalid"></div>
                                                                @elseif ($tour->status == "Active")
                                                                    <div class="status-active"></div>
                                                                @elseif ($tour->status == "Waiting")
                                                                    <div class="status-waiting"></div>
                                                                @elseif ($tour->status == "Draft")
                                                                    <div class="status-draft"></div>
                                                                @elseif ($tour->status == "Archived")
                                                                    <div class="status-archived"></div>
                                                                @else
                                                                @endif
                                                            </div>
                                                            @if ($tour->status != "Active")
                                                                <img class="img-fluid rounded thumbnail-image grayscale" src="{{ url('storage/tours/tours-cover/' . $tour->cover) }}" alt="{{ $tour->tour }}">
                                                            @else
                                                                <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/tours/tours-cover/' . $tour->cover) }}" alt="{{ $tour->tour }}">
                                                            @endif
                                                            <div class="name-card">
                                                                <p>
                                                                    {{ $tour->name }}
                                                                </p>
                                                            </div>
                                                        </a>
                                                    </div>
                                                    @php
                                                        $usr = ceil($tour->contract_rate / $usdrates->rate);
                                                        $h_sebelum_tax = $usr + $tour->markup;
                                                        $pj = ceil($h_sebelum_tax * ($tax->tax / 100) );
                                                        $h_final = ($h_sebelum_tax + $pj)*$tour->qty;
                                                    @endphp
                                                    @if ($tour->status != "Draft")
                                                        <div class="price-card m-t-8">
                                                            {{ '$ '. $h_final}}
                                                        </div>
                                                    @endif
                                                    @canany(['posDev','posAuthor'])
                                                        <div class="card-delete-btn">
                                                            <form action="/remove-tour/{{ $tour->id }}" method="post">
                                                                @csrf
                                                                @method('delete')
                                                                <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                <input id="partners_id" name="partners_id" value="{{ $partner->id }}" type="hidden">
                                                                <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                            </form>
                                                        </div>
                                                    @endcanany
                                                </div>
                                                {{-- MODAL TOUR DETAIL --------------------------------------------------------------------------------------------------------------- --}}
                                                <div class="modal fade" id="detail-tour-{{ $tour->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="subtitle"><i class="icon-copy fa fa-eye"></i>Detail {{ $tour->name }}</div>
                                                                    <div class="status-card">
                                                                        @if ($tour->status == "Rejected")
                                                                            <div class="status-rejected"></div>
                                                                        @elseif ($tour->status == "Invalid")
                                                                            <div class="status-invalid"></div>
                                                                        @elseif ($tour->status == "Active")
                                                                            <div class="status-active"></div>
                                                                        @elseif ($tour->status == "Waiting")
                                                                            <div class="status-waiting"></div>
                                                                        @elseif ($tour->status == "Draft")
                                                                            <div class="status-draft"></div>
                                                                        @elseif ($tour->status == "Archived")
                                                                            <div class="status-archived"></div>
                                                                        @else
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="page-card">
                                                                    <figure class="card-banner">
                                                                        <img src="{{ asset ('storage/tours/tours-cover/' . $tour->cover) }}" alt="{{ $tour->name }}" loading="lazy">
                                                                    </figure>
                                                                    <div class="card-content">
                                                                        <div class="card-text">
                                                                            <div class="row ">
                                                                                <div class="col-4 col-sm-4">
                                                                                    <div class="card-subtitle">Capacity:</div>
                                                                                    <p>{{ $tour->qty. " Guest" }}</p>
                                                                                </div>
                                                                                <div class="col-4 col-sm-4">
                                                                                    <div class="card-subtitle">Price:</div>
                                                                                    <p>{{ currencyFormatUsd($h_final/$tour->qty) }}/pax</p>
                                                                                </div>
                                                                                <div class="col-4 col-sm-4">
                                                                                    <div class="card-subtitle">Total Price:</div>
                                                                                    <p>{{ currencyFormatUsd($h_final) }}</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        @if ($tour->include != "")
                                                                            <hr class="form-hr">
                                                                            <div class="card-text">
                                                                                <div class="card-subtitle">Include:</div>
                                                                                <p>{!! $tour->include !!}</p>
                                                                            </div>
                                                                        @endif
                                                                        @if ($tour->additional_info != "")
                                                                            <hr class="form-hr">
                                                                            <div class="card-text">
                                                                                <div class="card-subtitle">additional_info:</div>
                                                                                <p>{!! $tour->additional_info !!}</p>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                               
                                                                <div class="card-box-footer">
                                                                    @canany(['posDev','posAuthor'])
                                                                        <a href="/edit-tour-{{ $tour->id }}">
                                                                            <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                                        </a>
                                                                    @endcanany
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="notif">!!! The partner doesn't have a service yet, please add a service now!</div>
                                    @endif
                                    @canany(['posDev','posAuthor'])
                                        <div class="card-box-footer">
                                            <a href="/partner-add-tour-{{ $partner->id }}">
                                                <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Tour Package</button>
                                            </a>
                                        </div>
                                    @endcanany
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- FOOTER PAGE ======================================================================================================================= --}}
                @include('layouts.footer')
            </div>
        </div>
    @endcan
@endsection