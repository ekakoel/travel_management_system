@php
    $package_dinner_venues = $packages->where('type','Wedding Dinner')
@endphp
@if (count($package_dinner_venues)>0)
    <div class="card-title m-t-18" style="width: 100%">
        Dinner Venue
    </div>
    <div class="card-box-content">
        @foreach ($package_dinner_venues as $no=>$package_dinner_venue)
            @php
                $package_dinner_venue_hotel = $hotels->where('id',$package_dinner_venue->hotel_id)->first();
            @endphp
            <div class="card">
                <a href="#" data-toggle="modal" data-target="#detail-package-{{ $package_dinner_venue->id }}">
                    <div class="image-container">
                        @if ($package_dinner_venue->status != "Active")
                            <img class="img-fluid rounded thumbnail-image grayscale" src="{{ url('storage/vendors/package/' . $package_dinner_venue->cover) }}" alt="{{ $package_dinner_venue->service }}">
                        @else
                            <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/vendors/package/' . $package_dinner_venue->cover) }}" alt="{{ $package_dinner_venue->service }}">
                        @endif
                        
                        <div class="card-status">
                            @if ($package_dinner_venue->status == "Rejected")
                                <div class="status-rejected"></div>
                            @elseif ($package_dinner_venue->status == "Invalid")
                                <div class="status-invalid"></div>
                            @elseif ($package_dinner_venue->status == "Active")
                                <div class="status-active"></div>
                            @elseif ($package_dinner_venue->status == "Waiting")
                                <div class="status-waiting"></div>
                            @elseif ($package_dinner_venue->status == "Draft")
                                <div class="status-draft"></div>
                            @elseif ($package_dinner_venue->status == "Archived")
                                <div class="status-archived"></div>
                            @else
                            @endif
                        </div>
                        <div class="name-card">
                            <p>
                                {{ $package_dinner_venue->service }}
                            </p>
                        </div>
                    </div>
                </a>
                
                <div class="price-card m-t-8">
                    {{"$ " . number_format($package_dinner_venue->publish_rate) }}
                </div>
                @canany(['posDev','weddingDvl','weddingAuthor'])
                    <div class="card-delete-btn">
                        <a href="#" data-toggle="modal" data-target="#edit-package-{{ $package_dinner_venue->id }}">
                            <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Update"><i class="icon-copy fa fa-pencil"></i></button>
                        </a>
                        <form action="/fremove-vendor-package/{{ $package_dinner_venue->id }}" method="post" enctype="multipart/form-data">
                            @method('put')
                            {{ csrf_field() }}
                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                        </form>
                    </div>
                @endcanany
            </div>
            {{-- MODAL SERVICE DETAIL --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="detail-package-{{ $package_dinner_venue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-eye"></i>Detail Service {{ $package_dinner_venue->service }}</div>
                                <div class="status-card">
                                    @if ($package_dinner_venue->status == "Rejected")
                                        <div class="status-rejected"></div>
                                    @elseif ($package_dinner_venue->status == "Invalid")
                                        <div class="status-invalid"></div>
                                    @elseif ($package_dinner_venue->status == "Active")
                                        <div class="status-active"></div>
                                    @elseif ($package_dinner_venue->status == "Waiting")
                                        <div class="status-waiting"></div>
                                    @elseif ($package_dinner_venue->status == "Draft")
                                        <div class="status-draft"></div>
                                    @elseif ($package_dinner_venue->status == "Archived")
                                        <div class="status-archived"></div>
                                    @else
                                    @endif
                                </div>
                            </div>
                            <div class="page-card">
                                <figure class="card-banner">
                                    <img src="{{ asset ('storage/vendors/package/' . $package_dinner_venue->cover) }}" alt="{{ $package_dinner_venue->name }}" loading="lazy">
                                </figure>
                                <div class="card-content">
                                    <div class="card-text">
                                        <div class="row ">
                                            <div class="col-6 col-sm-4">
                                                <div class="card-subtitle">Vendor</div>
                                                <p>{{ $vendor->name }}</p>
                                            </div>
                                            @if ($package_dinner_venue_hotel)
                                                <div class="col-6 col-sm-4">
                                                    <div class="card-subtitle">Service</div>
                                                    <p>{{ $package_dinner_venue->service." (".$package_dinner_venue->duration." ".$package_dinner_venue->time.")" }}</p>
                                                </div>
                                                <div class="col-6 col-sm-4">
                                                    <div class="card-subtitle">Hotel</div>
                                                    <p>{{ $package_dinner_venue_hotel->name }}</p>
                                                </div>
                                            @else
                                                <div class="col-6 col-sm-8">
                                                    <div class="card-subtitle">Service</div>
                                                    <p>{{ $package_dinner_venue->service." (".$package_dinner_venue->duration." ".$package_dinner_venue->time.")" }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row ">
                                            <div class="col-6 col-sm-4 m-b-8">
                                                @php
                                                    $dinner_venue_cr = $package_dinner_venue->contract_rate/$usdrates->rate;
                                                    $dinner_venue_tax = ($dinner_venue_cr + $package_dinner_venue->markup)*($tax->tax/100);
                                                    $dinner_venue_cr_usd = ceil($package_dinner_venue->contract_rate / $usdrates->rate);
                                                    $dinner_venue_mr_idr = ceil($package_dinner_venue->markup * $usdrates->rate);
                                                    $dinner_venue_tax_idr = ceil($dinner_venue_tax * $usdrates->rate);
                                                    $dinner_venue_publish_idr = ceil($package_dinner_venue->publish_rate * $usdrates->rate);
                                                @endphp
                                                <div class="card-subtitle">Contract Rate</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($package_dinner_venue->contract_rate) }}</div>
                                                <div class="rate-usd">{{ currencyFormatUsd($dinner_venue_cr_usd) }}</div>
                                            </div>
                                            <div class="col-6 col-sm-4 m-b-8">
                                                <div class="card-subtitle">Markup</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($dinner_venue_mr_idr) }}</div>
                                                <div class="rate-usd">{{ currencyFormatUsd($package_dinner_venue->markup) }}</div>
                                            </div>
                                            <div class="col-6 col-sm-4 m-b-8">
                                                <div class="card-subtitle">Tax {{ $tax->tax."%" }}</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($dinner_venue_tax_idr) }}</div>
                                                <div class="rate-usd">{{ currencyFormatUsd($dinner_venue_tax) }}</div>
                                            </div>
                                            <div class="col-6 col-sm-4 m-b-8">
                                                <div class="card-subtitle">Publish Rate</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($dinner_venue_publish_idr) }}</div>
                                                <div class="usd-rate">{{ currencyFormatUsd($package_dinner_venue->publish_rate) }}</div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    @if ($package_dinner_venue->description)
                                        <div class="card-text">
                                            <div class="card-subtitle">Description</div>
                                            <p>{!! $package_dinner_venue->description !!}</p>
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
            {{-- MODAL UPDATE PACKAGE --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="edit-package-{{ $package_dinner_venue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update Package {{ $package_dinner_venue->service }}</div>
                            </div>
                            <form id="update-package-{{ $package_dinner_venue->id }}" action="/fupdate-vendor-package/{{ $package_dinner_venue->id }}" method="post" enctype="multipart/form-data">
                                @method('put')
                                {{ csrf_field() }}
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="card-subtitle m-b-8">Cover Image</div>
                                                    <img src="{{ asset ('storage/vendors/package/' . $package_dinner_venue->cover) }}" alt="{{ $package_dinner_venue->service }}" loading="lazy">
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="dropzone text-center b-0">
                                                        <div class="update-cover-package-preview-div">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="cover">Cover Image</label>
                                                <input type="file" name="cover" id="updateCoverPackage" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ $package_dinner_venue->cover }}">
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status"  type="text" class="custom-select @error('status') is-invalid @enderror" placeholder="Select status" required>
                                                    @if ($package_dinner_venue->status == "Draft")
                                                        <option selected value="{{ $package_dinner_venue->status }}">{{ $package_dinner_venue->status }}</option>
                                                        <option value="Active">Active</option>
                                                    @else
                                                        <option selected value="{{ $package_dinner_venue->status }}">{{ $package_dinner_venue->status }}</option>
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
                                                <label for="service">Package Name</label>
                                                <input type="text" name="service" class="form-control @error('service') is-invalid @enderror" placeholder="Name" value="{{ $package_dinner_venue->service }}" required>
                                                @error('service')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            
                                            <div class="form-group ">
                                                <label for="hotel_id">Hotel</label>
                                                <select name="hotel_id" id="hotel_id"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select hotel_id">
                                                    @if ($package_dinner_venue_hotel)
                                                        <option selected value="{{ $package_dinner_venue->hotel_id }}">{{ $package_dinner_venue_hotel->name }}</option>
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
                                        <div class="col-6 col-sm-3 col-md-3">
                                            <div class="form-group ">
                                                <label for="type">Type <span>*</span></label>
                                                <select name="type" id="type"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select type" required>
                                                    <option selected value="{{ $package_dinner_venue->type }}">{{ $package_dinner_venue->type }}</option>
                                                    <option value="Decoration">Decoration</option>
                                                    <option value="Documentation">Documentation</option>
                                                    <option value="Entertainment">Entertainment</option>
                                                    <option value="Make-up">Make-up</option>
                                                    <option value="Wedding Dinner">Wedding Dinner</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                                @error('type')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3 col-md-3">
                                            <div class="form-group">
                                                <label for="capacity">Capacity</label>
                                                <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" placeholder="Insert capacity" value="{{ $package_dinner_venue->capacity }}">
                                                @error('capacity')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3 col-md-3">
                                            <div class="form-group">
                                                <label for="duration">Duration</label>
                                                <input type="number" name="duration" class="form-control @error('duration') is-invalid @enderror" placeholder="Insert duration" value="{{ $package_dinner_venue->duration }}" required>
                                                @error('duration')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3 col-md-3">
                                            <div class="form-group ">
                                                <label for="time">Time <span>*</span></label>
                                                <select name="time" id="time"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select time" required>
                                                    @if ($package_dinner_venue->time == 'minutes')
                                                        <option selected value="{{ $package_dinner_venue->time }}">Minutes</option>
                                                        <option value="hours">Hours</option>
                                                        <option value="days">Days</option>
                                                    @elseif ($package_dinner_venue->time == 'hours')
                                                        <option value="minutes">Minutes</option>
                                                        <option selected value="{{ $package_dinner_venue->time }}">Hours</option>
                                                        <option value="days">Days</option>
                                                    @else
                                                        <option value="minutes">Minutes</option>
                                                        <option selected value="{{ $package_dinner_venue->time }}">Days</option>
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
                                                    <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ $package_dinner_venue->contract_rate }}" required>
                                                    @error('contract_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="markup">Markup <span>*</span></label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ $package_dinner_venue->markup }}" required>
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
                                                    <input readonly type="number" id="publish_rate" name="publish_rate" class="input-icon form-control @error('publish_rate') is-invalid @enderror" placeholder="Insert publish rate" value="{{ $package_dinner_venue->publish_rate }}" required>
                                                    @error('publish_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea name="description" id="update-description-package{{ ++$no }}" wire:model="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Description" type="text">{!! $package_dinner_venue->description !!}</textarea>
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
                                <button type="submit" form="update-package-{{ $package_dinner_venue->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif