@php
    $package_makeups = $packages->where('type','Make-up');
@endphp
@if (count($package_makeups)>0)
    <div class="card-title m-t-18" style="width: 100%">
        Make-up
    </div>
    <div class="card-box-content">
        @foreach ($package_makeups as $makeupno=>$package_makeup)
            @php
                $package_makeup_hotel = $hotels->where('id',$package_makeup->hotel_id)->first();
            @endphp
            <div class="card">
                <a href="#" data-toggle="modal" data-target="#detail-package-{{ $package_makeup->id }}">
                    <div class="image-container">
                        @if ($package_makeup->status != "Active")
                            <img class="img-fluid rounded thumbnail-image grayscale" src="{{ url('storage/vendors/package/' . $package_makeup->cover) }}" alt="{{ $package_makeup->service }}">
                        @else
                            <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/vendors/package/' . $package_makeup->cover) }}" alt="{{ $package_makeup->service }}">
                        @endif
                        <div class="card-status">
                            @if ($package_makeup->status == "Rejected")
                                <div class="status-rejected"></div>
                            @elseif ($package_makeup->status == "Invalid")
                                <div class="status-invalid"></div>
                            @elseif ($package_makeup->status == "Active")
                                <div class="status-active"></div>
                            @elseif ($package_makeup->status == "Waiting")
                                <div class="status-waiting"></div>
                            @elseif ($package_makeup->status == "Draft")
                                <div class="status-draft"></div>
                            @elseif ($package_makeup->status == "Archived")
                                <div class="status-archived"></div>
                            @else
                            @endif
                        </div>
                        <div class="name-card">
                            <p>
                                {{ $package_makeup->service }}
                            </p>
                        </div>
                    </div>
                </a>
                <div class="price-card m-t-8">
                    {{"$ " . number_format($package_makeup->publish_rate) }}
                </div>
                @canany(['posDev','weddingDvl','weddingAuthor'])
                    <div class="card-delete-btn">
                        <a href="#" data-toggle="modal" data-target="#edit-package-{{ $package_makeup->id }}">
                            <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Update"><i class="icon-copy fa fa-pencil"></i></button>
                        </a>
                        <form action="/fremove-vendor-package/{{ $package_makeup->id }}" method="post" enctype="multipart/form-data">
                            @method('put')
                            {{ csrf_field() }}
                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                        </form>
                    </div>
                @endcanany
            </div>
            {{-- MODAL SERVICE DETAIL --------------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="detail-package-{{ $package_makeup->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-eye"></i>Detail Service {{ $package_makeup->service }}</div>
                                <div class="status-card">
                                    @if ($package_makeup->status == "Rejected")
                                        <div class="status-rejected"></div>
                                    @elseif ($package_makeup->status == "Invalid")
                                        <div class="status-invalid"></div>
                                    @elseif ($package_makeup->status == "Active")
                                        <div class="status-active"></div>
                                    @elseif ($package_makeup->status == "Waiting")
                                        <div class="status-waiting"></div>
                                    @elseif ($package_makeup->status == "Draft")
                                        <div class="status-draft"></div>
                                    @elseif ($package_makeup->status == "Archived")
                                        <div class="status-archived"></div>
                                    @else
                                    @endif
                                </div>
                            </div>
                            <div class="page-card">
                                <figure class="card-banner">
                                    <img src="{{ asset ('storage/vendors/package/' . $package_makeup->cover) }}" alt="{{ $package_makeup->name }}" loading="lazy">
                                </figure>
                                <div class="card-content">
                                    <div class="card-text">
                                        <div class="row ">
                                            <div class="col-6 col-sm-4">
                                                <div class="card-subtitle">Vendor</div>
                                                <p>{{ $vendor->name }}</p>
                                            </div>
                                            @if ($package_makeup_hotel)
                                                <div class="col-6 col-sm-4">
                                                    <div class="card-subtitle">Service</div>
                                                    <p>{{ $package_makeup->service." (".$package_makeup->duration." ".$package_makeup->time.")" }}</p>
                                                </div>
                                                <div class="col-6 col-sm-4">
                                                    <div class="card-subtitle">Hotel</div>
                                                    <p>{{ $package_makeup_hotel->name }}</p>
                                                </div>
                                            @else
                                                <div class="col-6 col-sm-8">
                                                    <div class="card-subtitle">Service</div>
                                                    <p>{{ $package_makeup->service." (".$package_makeup->duration." ".$package_makeup->time.")" }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row ">
                                            <div class="col-6 col-sm-4 m-b-8">
                                                @php
                                                    $makeup_cr = $package_makeup->contract_rate/$usdrates->rate;
                                                    $makeup_tax = ($makeup_cr + $package_makeup->markup)*($tax->tax/100);
                                                    $makeup_cr_usd = ceil($package_makeup->contract_rate / $usdrates->rate);
                                                    $makeup_mr_idr = ceil($package_makeup->markup * $usdrates->rate);
                                                    $makeup_tax_idr = ceil($makeup_tax * $usdrates->rate);
                                                    $makeup_publish_idr = ceil($package_makeup->publish_rate * $usdrates->rate);
                                                @endphp
                                                <div class="card-subtitle">Contract Rate</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($package_makeup->contract_rate) }}</div>
                                                <div class="rate-usd">{{ currencyFormatUsd($makeup_cr_usd) }}</div>
                                            </div>
                                            <div class="col-6 col-sm-4 m-b-8">
                                                <div class="card-subtitle">Markup</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($makeup_mr_idr) }}</div>
                                                <div class="rate-usd">{{ currencyFormatUsd($package_makeup->markup) }}</div>
                                            </div>
                                            <div class="col-6 col-sm-4 m-b-8">
                                                <div class="card-subtitle">Tax {{ $tax->tax."%" }}</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($makeup_tax_idr) }}</div>
                                                <div class="rate-usd">{{ currencyFormatUsd($makeup_tax) }}</div>
                                            </div>
                                            <div class="col-6 col-sm-4 m-b-8">
                                                <div class="card-subtitle">Publish Rate</div>
                                                <div class="idr-rate">{{ currencyFormatIdr($makeup_publish_idr) }}</div>
                                                <div class="usd-rate">{{ currencyFormatUsd($package_makeup->publish_rate) }}</div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    @if ($package_makeup->description)
                                        <div class="card-text">
                                            <div class="card-subtitle">Description</div>
                                            <p>{!! $package_makeup->description !!}</p>
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
            <div class="modal fade" id="edit-package-{{ $package_makeup->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update Package {{ $package_makeup->service }}</div>
                            </div>
                            <form id="update-package-{{ $package_makeup->id }}" action="/fupdate-vendor-package/{{ $package_makeup->id }}" method="post" enctype="multipart/form-data">
                                @method('put')
                                {{ csrf_field() }}
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="update-cover-makeup-img-preview-{{ $makeupno }}" class="form-label">Cover Preview</label>
                                                        <div class="dropzone">
                                                            <div id="update-cover-makeup-img-preview-{{ $makeupno }}">
                                                                <img class="img-fluid" src="{{ asset ('storage/vendors/package/' . $package_makeup->cover) }}" alt="{{ $package_makeup->service }}" loading="lazy">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="addCoverMakeupPreview-{{ $makeupno }}">Cover Image</label>
                                                <input type="file" name="cover" id="addCoverMakeupPreview-{{ $makeupno }}" onchange="updateCoverMakeupPreview(event, {{ $makeupno }})" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ $package_makeup->service }}">
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status"  type="text" class="custom-select @error('status') is-invalid @enderror" placeholder="Select status" required>
                                                    @if ($package_makeup->status == "Draft")
                                                        <option selected value="{{ $package_makeup->status }}">{{ $package_makeup->status }}</option>
                                                        <option value="Active">Active</option>
                                                    @else
                                                        <option selected value="{{ $package_makeup->status }}">{{ $package_makeup->status }}</option>
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
                                                <input type="text" name="service" class="form-control @error('service') is-invalid @enderror" placeholder="Name" value="{{ $package_makeup->service }}" required>
                                                @error('service')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            
                                            <div class="form-group ">
                                                <label for="hotel_id">Hotel</label>
                                                <select name="hotel_id" id="hotel_id"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select hotel_id">
                                                    @if ($package_makeup_hotel)
                                                        <option selected value="{{ $package_makeup->hotel_id }}">{{ $package_makeup_hotel->name }}</option>
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
                                                <select id="makeupType" name="type" type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select type" required>
                                                    <option {{ $package_makeup->type == NULL?"selected":""; }} value="">Select Type</option>
                                                    <option {{ $package_makeup->type == "Ceremony Venue Decoration"?"selected":""; }} value="Ceremony Venue Decoration">Ceremony Venue Decoration</option>
                                                    <option {{ $package_makeup->type == "Reception Venue Decoration"?"selected":""; }} value="Reception Venue Decoration">Reception Venue Decoration</option>
                                                    <option {{ $package_makeup->type == "Documentation"?"selected":""; }} value="Documentation">Documentation</option>
                                                    <option {{ $package_makeup->type == "Entertainment"?"selected":""; }} value="Entertainment">Entertainment</option>
                                                    <option {{ $package_makeup->type == "Maku-up"?"selected":""; }} value="Make-up">Make-up</option>
                                                    <option {{ $package_makeup->type == "Other"?"selected":""; }} value="Other">Other</option>
                                                </select>
                                                @error('type')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div id="makeupContainer" class="col-12 col-sm-6 col-md-6 hidden">
                                            <div class="form-group ">
                                                <label for="venue">Venue <span>*</span></label>
                                                <select name="venue" type="text" class="custom-select @error('venue') is-invalid @enderror" placeholder="Select venue">
                                                    <option {{ $package_makeup->venue == NULL?"selected":""; }} value="">Select Venue</option>
                                                    <option {{ $package_makeup->venue == "Ceremony Venue"?"selected":""; }} value="Ceremony Venue">Ceremony Venue</option>
                                                    <option {{ $package_makeup->venue == "Reception Venue"?"selected":""; }} value="Reception Venue">Reception Venue</option>
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
                                                <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" placeholder="Insert capacity" value="{{ $package_makeup->capacity }}">
                                                @error('capacity')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3 col-md-3">
                                            <div class="form-group">
                                                <label for="duration">Duration</label>
                                                <input type="number" name="duration" class="form-control @error('duration') is-invalid @enderror" placeholder="Insert duration" value="{{ $package_makeup->duration }}" required>
                                                @error('duration')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6 col-sm-3 col-md-3">
                                            <div class="form-group ">
                                                <label for="time">Time <span>*</span></label>
                                                <select name="time" id="time"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select time" required>
                                                    @if ($package_makeup->time == 'minutes')
                                                        <option selected value="{{ $package_makeup->time }}">Minutes</option>
                                                        <option value="hours">Hours</option>
                                                        <option value="days">Days</option>
                                                    @elseif ($package_makeup->time == 'hours')
                                                        <option value="minutes">Minutes</option>
                                                        <option selected value="{{ $package_makeup->time }}">Hours</option>
                                                        <option value="days">Days</option>
                                                    @else
                                                        <option value="minutes">Minutes</option>
                                                        <option selected value="{{ $package_makeup->time }}">Days</option>
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
                                                    <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ $package_makeup->contract_rate }}" required>
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
                                                    <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ $package_makeup->markup }}" required>
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
                                                    <input readonly type="number" id="publish_rate" name="publish_rate" class="input-icon form-control @error('publish_rate') is-invalid @enderror" placeholder="Insert publish rate" value="{{ $package_makeup->publish_rate }}" required>
                                                    @error('publish_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea name="description" id="update-description-package{{ ++$makeupno }}" wire:model="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Description" type="text">{!! $package_makeup->description !!}</textarea>
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
                                <button type="submit" form="update-package-{{ $package_makeup->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <script>
        document.getElementById('makeupType').addEventListener('change', function() {
            var venueContainer = document.getElementById('makeupContainer');
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
        function updateCoverMakeupPreview(event, makeupno) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function() {
                var dataURL = reader.result;
                var previewDiv = document.getElementById('update-cover-makeup-img-preview-' + makeupno);
                previewDiv.innerHTML = '';
                var imgElement = document.createElement('img');
                imgElement.src = dataURL;
                imgElement.className = 'img-fluid rounded';
                previewDiv.appendChild(imgElement);
            };
            reader.readAsDataURL(input.files[0]);
        }
    </script>
@endif