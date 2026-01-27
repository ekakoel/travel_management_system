{{-- RECEPTION VENUE --}}
<div id="receptionVenue" class="card-box">
    <div class="card-box-title">
        <div class="subtitle"><i class="icon-copy dw dw-pinwheel"></i> Reception Venue </div>
    </div>
    @if ($receptionVenues)
        <div class="card-box-content m-b-8">
            @foreach ($receptionVenues as $reception_venue)
                <div class="card">
                    <a href="#" data-toggle="modal" data-target="#detail-reception-venue-{{ $reception_venue->id }}">
                        <div class="card-image-container">
                            <div class="card-status">
                                @if ($reception_venue->status == "Rejected")
                                    <div class="status-rejected"></div>
                                @elseif ($reception_venue->status == "Invalid")
                                    <div class="status-invalid"></div>
                                @elseif ($reception_venue->status == "Active")
                                    <div class="status-active"></div>
                                @elseif ($reception_venue->status == "Waiting")
                                    <div class="status-waiting"></div>
                                @elseif ($reception_venue->status == "Draft")
                                    <div class="status-draft"></div>
                                @elseif ($reception_venue->status == "Archived")
                                    <div class="status-archived"></div>
                                @endif
                            </div>
                            @if ($reception_venue->status == "Draft")
                                <img class="img-fluid rounded thumbnail-image grayscale" src="{{ url('storage/weddings/reception-venues/' . $reception_venue->cover) }}" alt="{{ $reception_venue->name }}">
                            @else
                                <img class="img-fluid rounded thumbnail-image" src="{{ url('storage/weddings/reception-venues/' . $reception_venue->cover) }}" alt="{{ $reception_venue->name }}">
                            @endif
                            <div class="card-price-container">
                                <div class="card-price-full">
                                    Max: {{ $reception_venue->capacity }} Invitations
                                </div>
                            </div>
                            <div class="card-period-full">
                                {{ date('d M Y',strtotime($reception_venue->periode_start)) }} - {{ date('d M Y',strtotime($reception_venue->periode_end)) }}
                            </div> 
                            <div class="name-card">
                                <p>
                                    <b>{{ $reception_venue->name }}</b><br>
                                </p>
                            </div>
                        </div>
                    </a>
                    @canany(['posDev','posAuthor'])
                        <div class="card-btn-container">
                            @if ($reception_venue->status == "Draft")
                                <a href="/update-reception-venue-{{ $reception_venue->id }}">
                                    <button class="btn-update" data-toggle="tooltip" data-placement="top" title="Update"><i class="icon-copy fa fa-pencil"></i></button><br>
                                </a>
                            @endif
                            <form action="/fdelete-wedding-reception-venue/{{ $reception_venue->id }}" method="post">
                                @csrf
                                @method('delete')
                                <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                            </form>
                        </div>
                    @endcanany
                </div>
                {{-- MODAL RECEPTION VENUE DETAIL --}}
                <div class="modal fade" id="detail-reception-venue-{{ $reception_venue->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title"><i class="icon-copy dw dw-pinwheel"></i> {{ $reception_venue->name }}</div>
                                    <div class="status-card m-t-8">
                                        @if ($reception_venue->status == "Rejected")
                                            <div class="status-rejected"></div>
                                        @elseif ($reception_venue->status == "Invalid")
                                            <div class="status-invalid"></div>
                                        @elseif ($reception_venue->status == "Active")
                                            <div class="status-active"></div>
                                        @elseif ($reception_venue->status == "Waiting")
                                            <div class="status-waiting"></div>
                                        @elseif ($reception_venue->status == "Draft")
                                            <div class="status-draft"></div>
                                        @elseif ($reception_venue->status == "Archived")
                                            <div class="status-archived"></div>
                                        @else
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-12">
                                            <div class="card-banner">
                                                <img src="{{ asset ('storage/weddings/reception-venues/' . $reception_venue->cover) }}" alt="{{ $reception_venue->name }}" loading="lazy">
                                            </div>
                                            <div class="card-text">
                                                <div class="card-ptext-margin">
                                                    <div class="row ">
                                                        <div class="col-6 col-sm-3">
                                                            <div class="card-subtitle">Venue</div>
                                                            <div class="usd-text">{{ $reception_venue->name }}</div>
                                                        </div>
                                                        <div class="col-6 col-sm-3">
                                                            <div class="card-subtitle">Capacity</div>
                                                            <div class="usd-text">{{ $reception_venue->capacity. " Guest" }}</div>
                                                        </div>
                                                        <div class="col-6 col-sm-3">
                                                            <div class="card-subtitle">Period</div>
                                                            <div class="usd-text">{{ date('d M Y',strtotime($reception_venue->periode_start)) }} - {{ date('d M Y',strtotime($reception_venue->periode_end)) }}</div>
                                                        </div>
                                                        <div class="col-6 col-sm-3">
                                                            <div class="card-subtitle">Price</div>
                                                            <div class="usd-rate">{{ '$ ' . number_format($reception_venue->price, 0, ',', '.') }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($reception_venue->description != "")
                                                <div class="card-text">
                                                    <div class="row ">
                                                        <div class="col-12 col-sm-12">
                                                            <div class="tab-inner-title-light">
                                                                Description
                                                            </div>
                                                            <p>{!! $reception_venue->description !!}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($reception_venue->terms_and_conditions != "")
                                                <div class="card-text">
                                                    <div class="row ">
                                                        <div class="col-12 col-sm-12">
                                                            <div class="tab-inner-title-light">
                                                                Terms and Conditions
                                                            </div>
                                                            <p>{!! $reception_venue->terms_and_conditions !!}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <form id="activate-reception-venue-{{ $reception_venue->id }}" action="/factivate-reception-venue-{{ $reception_venue->id }}" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    @method('PUT')
                                </form>
                                <form id="deactivate-reception-venue-{{ $reception_venue->id }}" action="/fdeactivate-reception-venue-{{ $reception_venue->id }}" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    @method('PUT')
                                </form>
                                <div class="card-box-footer">
                                    @if ($reception_venue->status == "Draft")
                                        <button type="submit" form="activate-reception-venue-{{ $reception_venue->id }}" class="btn btn-info"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Activate</button>
                                        <a href="/update-reception-venue-{{ $reception_venue->id }}">
                                            <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                        </a>
                                    @else
                                        <button type="submit" form="deactivate-reception-venue-{{ $reception_venue->id }}" class="btn btn-dark"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Save as Draft</button>
                                    @endif
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="notification">Reception venue not found, please add one!</div>
    @endif
    @canany(['posDev','posAuthor'])
        {{-- MODAL ADD RECEPTION VENUE --}}
        <div class="modal fade" id="add-reception-venue-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="title"><i class="icon-copy fa fa-plus"></i> Add Reception Venue</div>
                        </div>
                        <form id="add-reception-venue" action="/fcreate-new-reception-venue/{{ $hotel->id }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12">
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label for="cover-preview" class="form-label">Cover Image</label>
                                                <div class="dropzone">
                                                    <div id="reception-venue-img-preview">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cover" class="form-label">Cover Image </label>
                                                <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" onchange="updateCoverPreview(event)">
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="capacity" class="form-label">Capacity</label>
                                        <input type="number" min="1" name="capacity" class="form-control @error('capacity') is-invalid @enderror" placeholder="Capacity" value="{{ old('capacity') }}" required>
                                        @error('capacity')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="line-with-text">
                                        <span class="line-text">Period</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="periode_start" class="form-label">Periode Start</label>
                                        <input type="text" name="periode_start" class="form-control date-picker @error('periode_start') is-invalid @enderror" placeholder="Periode Start" value="{{ old('periode_start') }}" required>
                                        @error('periode_start')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="periode_end" class="form-label">Periode End</label>
                                        <input type="text" name="periode_end" class="form-control date-picker @error('periode_end') is-invalid @enderror" placeholder="Periode End" value="{{ old('periode_end') }}" required>
                                        @error('periode_end')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="line-with-text">
                                        <span class="line-text">Price</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price">Price <span>*</span></label>
                                        <div class="btn-icon">
                                            <span>$</span>
                                            <input type="text" id="price" name="price"  class="form-control @error('price') is-invalid @enderror" placeholder="Insert price!" value="{{ old('price') }}" required>
                                            @error('price')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="line-with-text">
                                        <span class="line-text">Additional Informations</span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Insert description" value="{{ old('description') }}">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="terms_and_conditions" class="form-label">Terms and Conditions</label>
                                        <textarea name="terms_and_conditions" class="textarea_editor form-control @error('terms_and_conditions') is-invalid @enderror" placeholder="Insert terms_and_conditions" value="{{ old('terms_and_conditions') }}">{{ old('terms_and_conditions') }}</textarea>
                                        @error('terms_and_conditions')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="card-box-footer">
                            <button type="submit" form="add-reception-venue" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcanany
    <div class="card-box-footer">
        <a href="#" data-toggle="modal" data-target="#add-reception-venue-{{ $hotel->id }}">
            <button class="btn btn-primary"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> Add</button>
        </a>
    </div>
</div>
<script>
    function updateCoverPreview(event) {
        var input = event.target;
        var reader = new FileReader();
        reader.onload = function() {
            var dataURL = reader.result;
            var previewDiv = document.getElementById('reception-venue-img-preview');
            previewDiv.innerHTML = '';
            var imgElement = document.createElement('img');
            imgElement.src = dataURL;
            imgElement.className = 'img-fluid rounded';
            previewDiv.appendChild(imgElement);
        };
        reader.readAsDataURL(input.files[0]);
    }
</script>