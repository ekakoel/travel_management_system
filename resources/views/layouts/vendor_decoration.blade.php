@php
    $reception_venue_decoration = $packages->where('type','Reception Venue Decoration');
    $ceremony_venue_decoration = $packages->where('type','Ceremony Venue Decoration');
@endphp

@if (count($ceremony_venue_decoration)>0)
    <div class="card-title" style="width: 100%">
        Ceremony Venue Decorations
    </div>
    <div class="card-box-content">
        @foreach ($ceremony_venue_decoration as $cerno=>$ceremony_decoration)
            @php
                $ceremony_decoration_hotel = $hotels->where('id',$ceremony_decoration->hotel_id)->first();
                $decoration_cr = $ceremony_decoration->contract_rate/$usdrates->rate;
                $decoration_tax = ($decoration_cr + $ceremony_decoration->markup)*($tax->tax/100);
            @endphp
            <div class="card">
                <a href="#" data-toggle="modal" data-target="#detail-package-{{ $ceremony_decoration->id }}">
                    <div class="image-container">
                        @if ($ceremony_decoration->status != "Active")
                            <img class="img-fluid rounded thumbnail-image grayscale" src="{{ url('storage/vendors/package/' . $ceremony_decoration->cover) }}" alt="{{ $ceremony_decoration->service }}">
                        @else
                            <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/vendors/package/' . $ceremony_decoration->cover) }}" alt="{{ $ceremony_decoration->service }}">
                        @endif
                        
                        <div class="card-status">
                            @if ($ceremony_decoration->status == "Rejected")
                                <div class="status-rejected"></div>
                            @elseif ($ceremony_decoration->status == "Invalid")
                                <div class="status-invalid"></div>
                            @elseif ($ceremony_decoration->status == "Active")
                                <div class="status-active"></div>
                            @elseif ($ceremony_decoration->status == "Waiting")
                                <div class="status-waiting"></div>
                            @elseif ($ceremony_decoration->status == "Draft")
                                <div class="status-draft"></div>
                            @elseif ($ceremony_decoration->status == "Archived")
                                <div class="status-archived"></div>
                            @else
                            @endif
                        </div>
                        <div class="name-card">
                            <p>
                                {{ $ceremony_decoration->service }}
                            </p>
                        </div>
                    </div>
                </a>
                <div class="price-card m-t-8">
                    {{"$ " . number_format($ceremony_decoration->publish_rate) }}
                </div>
                @canany(['posDev','weddingDvl','weddingAuthor'])
                    <div class="card-delete-btn">
                        <a href="#" data-toggle="modal" data-target="#edit-package-{{ $ceremony_decoration->id }}">
                            <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Update"><i class="icon-copy fa fa-pencil"></i></button>
                        </a>
                        <form action="/fremove-vendor-package/{{ $ceremony_decoration->id }}" method="post" enctype="multipart/form-data">
                            @method('put')
                            {{ csrf_field() }}
                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                        </form>
                    </div>
                @endcanany
            </div>
            {{-- MODAL SERVICE DETAIL --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="detail-package-{{ $ceremony_decoration->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-eye"></i>Detail Service {{ $ceremony_decoration->service }}</div>
                                <div class="status-card">
                                    @if ($ceremony_decoration->status == "Rejected")
                                        <div class="status-rejected"></div>
                                    @elseif ($ceremony_decoration->status == "Invalid")
                                        <div class="status-invalid"></div>
                                    @elseif ($ceremony_decoration->status == "Active")
                                        <div class="status-active"></div>
                                    @elseif ($ceremony_decoration->status == "Waiting")
                                        <div class="status-waiting"></div>
                                    @elseif ($ceremony_decoration->status == "Draft")
                                        <div class="status-draft"></div>
                                    @elseif ($ceremony_decoration->status == "Archived")
                                        <div class="status-archived"></div>
                                    @else
                                    @endif
                                </div>
                            </div>
                            <div class="page-card">
                                <figure class="card-banner">
                                    <img src="{{ asset ('storage/vendors/package/' . $ceremony_decoration->cover) }}" alt="{{ $ceremony_decoration->name }}" loading="lazy">
                                </figure>
                                <div class="card-content">
                                    <div class="card-text">
                                        <div class="row ">
                                            <div class="col-6 col-sm-4">
                                                <div class="card-subtitle">Vendor</div>
                                                <p>{{ $vendor->name }}</p>
                                            </div>
                                            @if ($ceremony_decoration_hotel)
                                                <div class="col-6 col-sm-4">
                                                    <div class="card-subtitle">Service</div>
                                                    <p>{{ $ceremony_decoration->service." (".$ceremony_decoration->duration." ".$ceremony_decoration->time.")" }}</p>
                                                </div>
                                                <div class="col-6 col-sm-4">
                                                    <div class="card-subtitle">Hotel</div>
                                                    <p>{{ $ceremony_decoration_hotel->name }}</p>
                                                </div>
                                            @else
                                                <div class="col-6 col-sm-8">
                                                    <div class="card-subtitle">Service</div>
                                                    <p>{{ $ceremony_decoration->service." (".$ceremony_decoration->duration." ".$ceremony_decoration->time.")" }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row ">
                                            <div class="col-6 col-sm-4 m-b-8">
                                                @php
                                                    $decoration_cr_usd = ceil($ceremony_decoration->contract_rate / $usdrates->rate);
                                                    $decoration_mr_idr = ceil($ceremony_decoration->markup * $usdrates->rate);
                                                    $decoration_tax_idr = ceil($decoration_tax * $usdrates->rate);
                                                    $decoration_publish_idr = ceil($ceremony_decoration->publish_rate * $usdrates->rate);
                                                @endphp
                                                <div class="card-subtitle">Contract Rate</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($ceremony_decoration->contract_rate) }}</div>
                                                <div class="rate-usd">{{ currencyFormatUsd($decoration_cr_usd) }}</div>
                                            </div>
                                            <div class="col-6 col-sm-4 m-b-8">
                                                <div class="card-subtitle">Markup</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($decoration_mr_idr) }}</div>
                                                <div class="rate-usd">{{ currencyFormatUsd($ceremony_decoration->markup) }}</div>
                                            </div>
                                            <div class="col-6 col-sm-4 m-b-8">
                                                <div class="card-subtitle">Tax {{ $tax->tax."%" }}</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($decoration_tax_idr) }}</div>
                                                <div class="rate-usd">{{ currencyFormatUsd($decoration_tax) }}</div>
                                            </div>
                                            <div class="col-6 col-sm-4 m-b-8">
                                                <div class="card-subtitle">Publish Rate</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($decoration_publish_idr) }}</div>
                                                <div class="usd-rate">{{ currencyFormatUsd($ceremony_decoration->publish_rate) }}</div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    @if ($ceremony_decoration->description)
                                        <div class="card-text">
                                            <div class="card-subtitle">Description</div>
                                            <p>{!! $ceremony_decoration->description !!}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card-box-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- MODAL UPDATE SERVICE --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="edit-package-{{ $ceremony_decoration->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update Service {{ $ceremony_decoration->service }}</div>
                            </div>
                            <form id="update-package-{{ $ceremony_decoration->id }}" action="/fupdate-vendor-package/{{ $ceremony_decoration->id }}" method="post" enctype="multipart/form-data">
                                @method('put')
                                {{ csrf_field() }}
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="update-cover-other-img-preview-{{ $cerno }}" class="form-label">Cover Preview</label>
                                                        <div class="dropzone">
                                                            <div id="update-cover-ceremony-decoration-img-preview-{{ $cerno }}">
                                                                <img class="img-fluid" src="{{ asset ('storage/vendors/package/' . $ceremony_decoration->cover) }}" alt="{{ $ceremony_decoration->service }}" loading="lazy">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="addCoverCeremonyDecorationPreview-{{ $cerno }}">Cover Image</label>
                                                <input type="file" name="cover" id="addCoverCeremonyDecorationPreview-{{ $cerno }}" onchange="updateCoverCeremonyDecorationPreview(event, {{ $cerno }})" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status"  type="text" class="custom-select @error('status') is-invalid @enderror" placeholder="Select status" required>
                                                    @if ($ceremony_decoration->status == "Draft")
                                                        <option selected value="{{ $ceremony_decoration->status }}">{{ $ceremony_decoration->status }}</option>
                                                        <option value="Active">Active</option>
                                                    @else
                                                        <option selected value="{{ $ceremony_decoration->status }}">{{ $ceremony_decoration->status }}</option>
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
                                                <label for="service">Service Name</label>
                                                <input type="text" name="service" class="form-control @error('service') is-invalid @enderror" placeholder="Name" value="{{ $ceremony_decoration->service }}" required>
                                                @error('service')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            
                                            <div class="form-group ">
                                                <label for="hotel_id">Hotel</label>
                                                <select name="hotel_id" id="hotel_id"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select hotel_id">
                                                    @if ($ceremony_decoration_hotel)
                                                        <option selected value="{{ $ceremony_decoration->hotel_id }}">{{ $ceremony_decoration_hotel->name }}</option>
                                                        <option value="">None</option>
                                                    @else
                                                        <option selected value="">Select Hotel</option>
                                                    @endif
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
                                            <div class="form-group ">
                                                <label for="type">Type <span>*</span></label>
                                                <select id="ceremonyType" name="type" type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select type" required>
                                                    <option {{ $ceremony_decoration->type == NULL?"selected":""; }} value="">Select Type</option>
                                                    <option {{ $ceremony_decoration->type == "Ceremony Venue Decoration"?"selected":""; }} value="Ceremony Venue Decoration">Ceremony Venue Decoration</option>
                                                    <option {{ $ceremony_decoration->type == "Reception Venue Decoration"?"selected":""; }} value="Reception Venue Decoration">Reception Venue Decoration</option>
                                                    <option {{ $ceremony_decoration->type == "Documentation"?"selected":""; }} value="Documentation">Documentation</option>
                                                    <option {{ $ceremony_decoration->type == "Entertainment"?"selected":""; }} value="Entertainment">Entertainment</option>
                                                    <option {{ $ceremony_decoration->type == "Maku-up"?"selected":""; }} value="Make-up">Make-up</option>
                                                    <option {{ $ceremony_decoration->type == "Other"?"selected":""; }} value="Other">Other</option>
                                                </select>
                                                @error('type')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div id="ceremonyContainer" class="col-12 col-sm-6 col-md-6 hidden">
                                            <div class="form-group ">
                                                <label for="venue">Venue <span>*</span></label>
                                                <select name="venue" type="text" class="custom-select @error('venue') is-invalid @enderror" placeholder="Select venue">
                                                    <option {{ $ceremony_decoration->venue == NULL?"selected":""; }} value="">Select Venue</option>
                                                    <option {{ $ceremony_decoration->venue == "Ceremony Venue"?"selected":""; }} value="Ceremony Venue">Ceremony Venue</option>
                                                    <option {{ $ceremony_decoration->venue == "Reception Venue"?"selected":""; }} value="Reception Venue">Reception Venue</option>
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
                                                <label for="capacity">Capacity</label>
                                                <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" placeholder="Insert capacity" value="{{ $ceremony_decoration->capacity }}">
                                                @error('capacity')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3 col-md-3">
                                            <div class="form-group">
                                                <label for="duration">Duration</label>
                                                <input type="number" name="duration" class="form-control @error('duration') is-invalid @enderror" placeholder="Insert duration" value="{{ $ceremony_decoration->duration }}" required>
                                                @error('duration')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3 col-md-3">
                                            <div class="form-group ">
                                                <label for="time">Time <span>*</span></label>
                                                <select name="time" id="time"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select time" required>
                                                    @if ($ceremony_decoration->time == 'minutes')
                                                        <option selected value="{{ $ceremony_decoration->time }}">Minutes</option>
                                                        <option value="hours">Hours</option>
                                                        <option value="days">Days</option>
                                                    @elseif ($ceremony_decoration->time == 'hours')
                                                        <option value="minutes">Minutes</option>
                                                        <option selected value="{{ $ceremony_decoration->time }}">Hours</option>
                                                        <option value="days">Days</option>
                                                    @else
                                                        <option value="minutes">Minutes</option>
                                                        <option selected value="{{ $ceremony_decoration->time }}">Days</option>
                                                        <option value="hours">Hours</option>
                                                    @endif
                                                </select>
                                                @error('time')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="contract_rate">Contract Rate <span>*</span></label>
                                                <div class="btn-icon">
                                                    <span>Rp</span>
                                                    <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ $ceremony_decoration->contract_rate }}" required>
                                                    @error('contract_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="markup">Markup</label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ $ceremony_decoration->markup }}">
                                                    @error('markup')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="publish_rate">Publish Rate</label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input readonly type="number" id="publish_rate" name="publish_rate" class="input-icon form-control @error('publish_rate') is-invalid @enderror" placeholder="Insert publish_rate" value="{{ $ceremony_decoration->publish_rate }}" required>
                                                    @error('publish_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea name="description" id="update-description-package{{ ++$cerno }}" wiremodel="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Description" type="text">{!! $ceremony_decoration->description !!}</textarea>
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
                                <button type="submit" form="update-package-{{ $ceremony_decoration->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@if (count($reception_venue_decoration)>0)
    <div class="card-title" style="width: 100%">
        Reception Venue Decorations
    </div>
    <div class="card-box-content m-b-18">
        @foreach ($reception_venue_decoration as $recno=>$reception_decoration)
            @php
                $reception_decoration_hotel = $hotels->where('id',$reception_decoration->hotel_id)->first();
                $decoration_cr = $reception_decoration->contract_rate/$usdrates->rate;
                $decoration_tax = ($decoration_cr + $reception_decoration->markup)*($tax->tax/100);
            @endphp
            <div class="card">
                <a href="#" data-toggle="modal" data-target="#detail-package-{{ $reception_decoration->id }}">
                    <div class="image-container">
                        @if ($reception_decoration->status != "Active")
                            <img class="img-fluid rounded thumbnail-image grayscale" src="{{ url('storage/vendors/package/' . $reception_decoration->cover) }}" alt="{{ $reception_decoration->service }}">
                        @else
                            <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/vendors/package/' . $reception_decoration->cover) }}" alt="{{ $reception_decoration->service }}">
                        @endif
                        
                        <div class="card-status">
                            @if ($reception_decoration->status == "Rejected")
                                <div class="status-rejected"></div>
                            @elseif ($reception_decoration->status == "Invalid")
                                <div class="status-invalid"></div>
                            @elseif ($reception_decoration->status == "Active")
                                <div class="status-active"></div>
                            @elseif ($reception_decoration->status == "Waiting")
                                <div class="status-waiting"></div>
                            @elseif ($reception_decoration->status == "Draft")
                                <div class="status-draft"></div>
                            @elseif ($reception_decoration->status == "Archived")
                                <div class="status-archived"></div>
                            @else
                            @endif
                        </div>
                        <div class="name-card">
                            <p>
                                {{ $reception_decoration->service }}
                            </p>
                        </div>
                    </div>
                </a>
                <div class="price-card m-t-8">
                    {{"$ " . number_format($reception_decoration->publish_rate) }}
                </div>
                @canany(['posDev','weddingDvl','weddingAuthor'])
                    <div class="card-delete-btn">
                        <a href="#" data-toggle="modal" data-target="#edit-reception-decoration-{{ $reception_decoration->id }}">
                            <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Update"><i class="icon-copy fa fa-pencil"></i></button>
                        </a>
                        <form action="/fremove-vendor-package/{{ $reception_decoration->id }}" method="post" enctype="multipart/form-data">
                            @method('put')
                            {{ csrf_field() }}
                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                        </form>
                    </div>
                @endcanany
            </div>
            {{-- MODAL SERVICE DETAIL --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="detail-package-{{ $reception_decoration->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-eye"></i>Detail Service {{ $reception_decoration->service }}</div>
                                <div class="status-card">
                                    @if ($reception_decoration->status == "Rejected")
                                        <div class="status-rejected"></div>
                                    @elseif ($reception_decoration->status == "Invalid")
                                        <div class="status-invalid"></div>
                                    @elseif ($reception_decoration->status == "Active")
                                        <div class="status-active"></div>
                                    @elseif ($reception_decoration->status == "Waiting")
                                        <div class="status-waiting"></div>
                                    @elseif ($reception_decoration->status == "Draft")
                                        <div class="status-draft"></div>
                                    @elseif ($reception_decoration->status == "Archived")
                                        <div class="status-archived"></div>
                                    @else
                                    @endif
                                </div>
                            </div>
                            <div class="page-card">
                                <figure class="card-banner">
                                    <img src="{{ asset ('storage/vendors/package/' . $reception_decoration->cover) }}" alt="{{ $reception_decoration->name }}" loading="lazy">
                                </figure>
                                <div class="card-content">
                                    <div class="card-text">
                                        <div class="row ">
                                            <div class="col-6 col-sm-4">
                                                <div class="card-subtitle">Vendor</div>
                                                <p>{{ $vendor->name }}</p>
                                            </div>
                                            @if ($reception_decoration_hotel)
                                                <div class="col-6 col-sm-4">
                                                    <div class="card-subtitle">Service</div>
                                                    <p>{{ $reception_decoration->service." (".$reception_decoration->duration." ".$reception_decoration->time.")" }}</p>
                                                </div>
                                                <div class="col-6 col-sm-4">
                                                    <div class="card-subtitle">Hotel</div>
                                                    <p>{{ $reception_decoration_hotel->name }}</p>
                                                </div>
                                            @else
                                                <div class="col-6 col-sm-8">
                                                    <div class="card-subtitle">Service</div>
                                                    <p>{{ $reception_decoration->service." (".$reception_decoration->duration." ".$reception_decoration->time.")" }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row ">
                                            <div class="col-6 col-sm-4 m-b-8">
                                                @php
                                                    $decoration_cr_usd = ceil($reception_decoration->contract_rate / $usdrates->rate);
                                                    $decoration_mr_idr = ceil($reception_decoration->markup * $usdrates->rate);
                                                    $decoration_tax_idr = ceil($decoration_tax * $usdrates->rate);
                                                    $decoration_publish_idr = ceil($reception_decoration->publish_rate * $usdrates->rate);
                                                @endphp
                                                <div class="card-subtitle">Contract Rate</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($reception_decoration->contract_rate) }}</div>
                                                <div class="rate-usd">{{ currencyFormatUsd($decoration_cr_usd) }}</div>
                                            </div>
                                            <div class="col-6 col-sm-4 m-b-8">
                                                <div class="card-subtitle">Markup</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($decoration_mr_idr) }}</div>
                                                <div class="rate-usd">{{ currencyFormatUsd($reception_decoration->markup) }}</div>
                                            </div>
                                            <div class="col-6 col-sm-4 m-b-8">
                                                <div class="card-subtitle">Tax {{ $tax->tax."%" }}</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($decoration_tax_idr) }}</div>
                                                <div class="rate-usd">{{ currencyFormatUsd($decoration_tax) }}</div>
                                            </div>
                                            <div class="col-6 col-sm-4 m-b-8">
                                                <div class="card-subtitle">Publish Rate</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($decoration_publish_idr) }}</div>
                                                <div class="usd-rate">{{ currencyFormatUsd($reception_decoration->publish_rate) }}</div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    @if ($reception_decoration->description)
                                        <div class="card-text">
                                            <div class="card-subtitle">Description</div>
                                            <p>{!! $reception_decoration->description !!}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card-box-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- MODAL UPDATE SERVICE --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="edit-reception-decoration-{{ $reception_decoration->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update Service {{ $reception_decoration->service }}</div>
                            </div>
                            <form id="update-package-{{ $reception_decoration->id }}" action="/fupdate-vendor-package/{{ $reception_decoration->id }}" method="post" enctype="multipart/form-data">
                                @method('put')
                                {{ csrf_field() }}
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="update-cover-other-img-preview-{{ $recno }}" class="form-label">Cover Preview</label>
                                                        <div class="dropzone">
                                                            <div id="update-cover-reception-decoration-img-preview-{{ $recno }}">
                                                                <img class="img-fluid" src="{{ asset ('storage/vendors/package/' . $reception_decoration->cover) }}" alt="{{ $reception_decoration->service }}" loading="lazy">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="addCoverReceptionDecorationPreview-{{ $recno }}">Cover Image</label>
                                                <input type="file" name="cover" id="addCoverReceptionDecorationPreview-{{ $recno }}" onchange="updateCoverReceptionDecorationPreview(event, {{ $recno }})" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status"  type="text" class="custom-select @error('status') is-invalid @enderror" placeholder="Select status" required>
                                                    @if ($reception_decoration->status == "Draft")
                                                        <option selected value="{{ $reception_decoration->status }}">{{ $reception_decoration->status }}</option>
                                                        <option value="Active">Active</option>
                                                    @else
                                                        <option selected value="{{ $reception_decoration->status }}">{{ $reception_decoration->status }}</option>
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
                                                <label for="service">Service Name</label>
                                                <input type="text" name="service" class="form-control @error('service') is-invalid @enderror" placeholder="Name" value="{{ $reception_decoration->service }}" required>
                                                @error('service')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            
                                            <div class="form-group ">
                                                <label for="hotel_id">Hotel</label>
                                                <select name="hotel_id" id="hotel_id"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select hotel_id">
                                                    @if ($reception_decoration_hotel)
                                                        <option selected value="{{ $reception_decoration->hotel_id }}">{{ $reception_decoration_hotel->name }}</option>
                                                        <option value="">None</option>
                                                    @else
                                                        <option selected value="">Select Hotel</option>
                                                    @endif
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
                                            <div class="form-group ">
                                                <label for="type">Type <span>*</span></label>
                                                <select id="receptionType" name="type" type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select type" required>
                                                    <option {{ $reception_decoration->type == NULL?"selected":""; }} value="">Select Type</option>
                                                    <option {{ $reception_decoration->type == "Ceremony Venue Decoration"?"selected":""; }} value="Ceremony Venue Decoration">Ceremony Venue Decoration</option>
                                                    <option {{ $reception_decoration->type == "Reception Venue Decoration"?"selected":""; }} value="Reception Venue Decoration">Reception Venue Decoration</option>
                                                    <option {{ $reception_decoration->type == "Documentation"?"selected":""; }} value="Documentation">Documentation</option>
                                                    <option {{ $reception_decoration->type == "Entertainment"?"selected":""; }} value="Entertainment">Entertainment</option>
                                                    <option {{ $reception_decoration->type == "Maku-up"?"selected":""; }} value="Make-up">Make-up</option>
                                                    <option {{ $reception_decoration->type == "Other"?"selected":""; }} value="Other">Other</option>
                                                </select>
                                                @error('type')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div id="receptionContainer" class="col-12 col-sm-6 col-md-6 hidden">
                                            <div class="form-group ">
                                                <label for="venue">Venue <span>*</span></label>
                                                <select name="venue" type="text" class="custom-select @error('venue') is-invalid @enderror" placeholder="Select venue">
                                                    <option {{ $reception_decoration->venue == NULL?"selected":""; }} value="">Select Venue</option>
                                                    <option {{ $reception_decoration->venue == "Ceremony Venue"?"selected":""; }} value="Ceremony Venue">Ceremony Venue</option>
                                                    <option {{ $reception_decoration->venue == "Reception Venue"?"selected":""; }} value="Reception Venue">Reception Venue</option>
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
                                                <label for="capacity">Capacity</label>
                                                <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" placeholder="Insert capacity" value="{{ $reception_decoration->capacity }}">
                                                @error('capacity')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3 col-md-3">
                                            <div class="form-group">
                                                <label for="duration">Duration</label>
                                                <input type="number" name="duration" class="form-control @error('duration') is-invalid @enderror" placeholder="Insert duration" value="{{ $reception_decoration->duration }}" required>
                                                @error('duration')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3 col-md-3">
                                            <div class="form-group ">
                                                <label for="time">Time <span>*</span></label>
                                                <select name="time" id="time"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select time" required>
                                                    @if ($reception_decoration->time == 'minutes')
                                                        <option selected value="{{ $reception_decoration->time }}">Minutes</option>
                                                        <option value="hours">Hours</option>
                                                        <option value="days">Days</option>
                                                    @elseif ($reception_decoration->time == 'hours')
                                                        <option value="minutes">Minutes</option>
                                                        <option selected value="{{ $reception_decoration->time }}">Hours</option>
                                                        <option value="days">Days</option>
                                                    @else
                                                        <option value="minutes">Minutes</option>
                                                        <option selected value="{{ $reception_decoration->time }}">Days</option>
                                                        <option value="hours">Hours</option>
                                                    @endif
                                                </select>
                                                @error('time')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="contract_rate">Contract Rate <span>*</span></label>
                                                <div class="btn-icon">
                                                    <span>Rp</span>
                                                    <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ $reception_decoration->contract_rate }}" required>
                                                    @error('contract_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="markup">Markup</label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ $reception_decoration->markup }}">
                                                    @error('markup')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="publish_rate">Publish Rate</label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input readonly type="number" id="publish_rate" name="publish_rate" class="input-icon form-control @error('publish_rate') is-invalid @enderror" placeholder="Insert publish_rate" value="{{ $reception_decoration->publish_rate }}" required>
                                                    @error('publish_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea name="description" id="update-description-package{{ ++$recno }}" wiremodel="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Description" type="text">{!! $reception_decoration->description !!}</textarea>
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
                                <button type="submit" form="update-package-{{ $reception_decoration->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
    <script>
        document.getElementById('ceremonyType').addEventListener('change', function() {
            var venueContainer = document.getElementById('ceremonyContainer');
            if (this.value === 'Other') {
                venueContainer.classList.remove('hidden');
            } else if(this.value === 'Entertainment') {
                venueContainer.classList.remove('hidden');
            }else {
                venueContainer.classList.add('hidden');
            }
        });
        document.getElementById('receptionType').addEventListener('change', function() {
            var venueContainer = document.getElementById('receptionContainer');
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
        function updateCoverReceptionDecorationPreview(event, recno) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function() {
                var dataURL = reader.result;
                var previewDiv = document.getElementById('update-cover-reception-decoration-img-preview-' + recno);
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
        function updateCoverCeremonyDecorationPreview(event, cerno) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function() {
                var dataURL = reader.result;
                var previewDiv = document.getElementById('update-cover-ceremony-decoration-img-preview-' + cerno);
                previewDiv.innerHTML = '';
                var imgElement = document.createElement('img');
                imgElement.src = dataURL;
                imgElement.className = 'img-fluid rounded';
                previewDiv.appendChild(imgElement);
            };
            reader.readAsDataURL(input.files[0]);
        }
    </script>    