<div class="card-box m-b-18">
    <div class="card-box-title">
        <div class="subtitle"> <i class="icon-copy fa fa-gear" aria-hidden="true"></i>Control Services</div>
    </div>
    <div class="card-box-content">
        @foreach ($services as $service)
            @if ($service->status == 'Active')
                @if ($service->name == "Tours")
                    @php
                        $cactive_service = count($tours_active);
                    @endphp
                @elseif ($service->name == "Hotels")
                    @php
                        $cactive_service = count($hotels_active);
                    @endphp
                @elseif ($service->name == "Activities")
                    @php
                        $cactive_service = count($activities_active);
                    @endphp
                @elseif ($service->name == "Transports")
                    @php
                        $cactive_service = count($transports_active);
                    @endphp
                @elseif ($service->name == "Weddings")
                    @php
                        $cactive_service = count($weddings_active);
                    @endphp
                @endif
                <div class="widget-panel">
                    <div class="chart-icon-active">
                        <i class="{{ $service->icon }}"></i>
                    </div>
                    <div class="widget-data">
                        <div class="widget-data-title">
                            {{ $service->name }}
                        </div>
                        <div class="widget-data-subtitle">
                            <div class="service-no">
                                {{ $cactive_service }}
                                <span>Active</span>
                            </div>
                        </div>
                        <div class="btn-container p-8">
                            <a href="#" data-toggle="modal" data-target="#edit-service-{{ $service->id }}">
                                <button type="submit" class="btn edit-btn" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button>
                            </a>
                            <form id="disable-service" action="/fdisable-service/{{ $service->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <input type="hidden" name="status" value="Draft">
                                <input type="hidden" name="author" value="{{ Auth::User()->id }}">
                                <button type="submit" class="btn delete-btn" data-toggle="tooltip" data-placement="top" title="Disable"><i class="icon-copy ion-close"></i></button>
                            </form>
                        </div>
                    </div>
                    
                </div>
            @else
                <div class="widget-panel">
                    <div class="content-widget">
                        <div class="chart-icon-draft">
                            <i class="{{ $service->icon }}"></i>
                        </div>
                        <div class="widget-data">
                            <div class="widget-data-title">
                                {{ $service->name }}
                            </div>
                            <div class="widget-data-subtitle">
                                {{ $service->status }}
                            </div>
                        </div>
                    </div>
                    @if (Auth::user()->position == "developer")
                        <div class="action-bar">
                            <form id="enable-service" action="/fenable-service/{{ $service->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <input type="hidden" name="status" value="Active">
                                <input type="hidden" name="author" value="{{ Auth::User()->id }}">
                                <button type="submit" class="btn btn-icon-activate" data-toggle="tooltip" data-placement="top" title="Activate"><i class="icon-copy ion-checkmark-circled"></i></button>
                            </form>
                        </div>
                        <form id="remove-service" action="/fremove-service/{{ $service->id }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('delete')
                            <input type="hidden" name="service" value={{ $service->name }}>
                            <input type="hidden" name="author" value="{{ Auth::User()->id }}">
                            <button type="submit" form="remove-service" class="btn btn-icon-remove" data-toggle="tooltip" data-placement="top" title="Remove"><i class="icon-copy fa fa-trash-o" aria-hidden="true"></i></button>
                        </form>
                    @endif
                </div>
            @endif
            {{-- MODAL EDIT SERVICE ----------------------------------------------------------------------------------------------------------- --}}
            <div class="modal fade" id="edit-service-{{ $service->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content ">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Service</div>
                            </div>
                            <form id="update-service-{{ $service->id }}" action="/fedit-service/{{ $service->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group row">
                                                    <label for="name" class="col-sm-12 col-md-12 col-form-label">Service</label>
                                                    <div class="col-sm-12 col-md-12">
                                                        <input name="name" id="name"  type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Insert service name" value="{{ $service->name }}" required>
                                                    @error('name')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group row">
                                                    <label for="nicname" class="col-sm-12 col-md-12 col-form-label">Nicname</label>
                                                    <div class="col-sm-12 col-md-12">
                                                        <input name="nicname" id="nicname"  type="text" class="form-control @error('nicname') is-invalid @enderror" placeholder="Insert service nicname" value="{{ $service->nicname }}" required>
                                                    @error('nicname')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group row">
                                                    <label for="status" class="col-12 col-sm-12 col-md-12 col-form-label">Status</label>
                                                    <div class="col-12 col-sm-12 col-md-12">
                                                        <select id="status" name="status" class="custom-select col-12 @error('status') is-invalid @enderror" required>
                                                            <option selected value="{{ $service->status }}">{{ $service->status }}</option>
                                                            @if ($service->status == "Active")
                                                                <option value="Draft">Draft</option>
                                                            @else
                                                                <option value="Active">Active</option>
                                                            @endif
                                                            
                                                        </select>
                                                        @error('status')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group row">
                                                    <label for="icon" class="col-sm-12 col-md-12 col-form-label">Icon Code</label>
                                                    <div class="col-sm-12 col-md-12">
                                                        <input name="icon" id="icon"  type="text" class="form-control @error('icon') is-invalid @enderror" placeholder="Insert icon code" value="{{ $service->icon }}" required>
                                                    @error('icon')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-right">
                                        <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                    </div>
                                </div>
                            </form>
                            <div class="card-box-footer">
                                <button type="submit" form="update-service-{{ $service->id }}" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                <button class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- END MODAL EDIT SERVICE ----------------------------------------------------------------------------------------------------------- --}}
        @endforeach
        <div class="widget-panel">
            <a href="#" data-toggle="modal" data-target="#add-service">
                <div class="d-flex flex-wrap align-items-center">
                    <div class="chart-icon-active">
                        <i class="icon-copy ion-plus-circled"></i>
                    </div>
                    <div class="widget-data">
                        <div class="widget-data-title">
                            Add Service
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- MODAL ADD SERVICE ----------------------------------------------------------------------------------------------------------- --}}
        <div class="modal fade" id="add-service" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Service</div>
                        </div>
                    
                        <form id="create-service" action="/fadd-service" method="post" enctype="multipart/form-data">
                            @csrf
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label for="name" class="col-sm-12 col-md-12 col-form-label">Service</label>
                                                <div class="col-sm-12 col-md-12">
                                                    <input name="name" id="name"  type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Insert service name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group row">
                                                <label for="nicname" class="col-sm-12 col-md-12 col-form-label">Nicname</label>
                                                <div class="col-sm-12 col-md-12">
                                                    <input name="nicname" id="nicname"  type="text" class="form-control @error('nicname') is-invalid @enderror" placeholder="Insert service nicname" value="{{ old('nicname') }}" required>
                                                @error('nicname')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label for="icon" class="col-sm-12 col-md-12 col-form-label">Icon Code</label>
                                                <div class="col-sm-12 col-md-12">
                                                    <input name="icon" id="icon"  type="text" class="form-control @error('icon') is-invalid @enderror" placeholder="Insert icon code" value="{{ old('icon') }}" required>
                                                @error('icon')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                            </div>
                        </form>
                        <div class="card-box-footer">
                            <button type="submit" form="create-service" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add</button>
                            <button class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- END MODAL ADD SERVICE  ----------------------------------------------------------------------------------------------------------- --}}
    </div>
</div>

