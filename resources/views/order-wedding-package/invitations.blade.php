{{-- INVITATIONS --}}
<div id="weddingInvitations" class="col-md-12">
    <div class="page-subtitle">
        Invitations
        @if ($orderWedding->status != "Approved")
            <span>
                <a href="#" data-toggle="modal" data-target="#add-invitation-order-wedding-{{ $orderWedding->id }}"> 
                    <i class="icon-copy  fa fa-plus-circle" data-toggle="tooltip" data-placement="top" title="@lang('messages.Add')" aria-hidden="true"></i>
                </a>
            </span>
            {{-- MODAL ADD INVITATIONS  --}}
            <div class="modal fade" id="add-invitation-order-wedding-{{ $orderWedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Invitation')</div>
                            </div>
                            <form id="addInvitationOrderWedding-{{ $orderWedding->id }}" action="/fadd-invitation-to-order-wedding-{{ $orderWedding->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="dropzone">
                                                    <div class="cover-preview-div">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 m-t-8">
                                                <div class="form-group">
                                                    <label for="cover" class="form-label">Identity Card / Passport <span> *</span></label>
                                                    <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                    @error('cover')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="name">Name</label>
                                                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ old('name') }}" required>
                                                    @error('name')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="name_mandarin">Mandarin Name</label>
                                                    <input name="name_mandarin" type="text" class="form-control @error('name_mandarin') is-invalid @enderror" placeholder="Name" value="{{ old('name_mandarin') }}">
                                                    @error('name_mandarin')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="identification_no">ID/Passport Number</label>
                                                    <input name="identification_no" type="text" class="form-control @error('identification_no') is-invalid @enderror" placeholder="ID/Passpoer number" value="{{ old('identification_no') }}" required>
                                                    @error('identification_no')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="phone">Phone</label>
                                                    <input name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" placeholder="Contact" value="{{ old('phone') }}" required>
                                                    @error('phone')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="country">Country <span> *</span></label>
                                                    <select name="country" class="form-control custom-select @error('country') is-invalid @enderror" required>
                                                        <option selected value="">Select one</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}">{{ $country->code2 }} - {{ $country->country_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('country')
                                                        <div class="alert-form">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="card-box-footer">
                                <button type="submit" form="addInvitationOrderWedding-{{ $orderWedding->id }}" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @if (count($orderWedding->invitations)>0)
        <table class="data-table table stripe nowrap no-footer dtr-inline" >
            <thead>
                <tr>
                    <th style="width: 10%">No.</th>
                    <th style="width: 20%" class="datatable-nosort">ID/Passport</th>
                    <th style="width: 30%">Name</th>
                    <th style="width: 25%" class="datatable-nosort">Country</th>
                    <th style="width: 15%" class="datatable-nosort text-center"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($guests as $index=>$guest)
                    <tr>
                        <td class="pd-2-8">{{ ++$index }}</td>
                        <td class="pd-2-8">{{ $guest->passport_no }}</td>
                        <td class="pd-2-8">{{ $guest->name }} {{ $guest->chinese_name?"(".$guest->chinese_name.")":""; }}</td>
                        <td class="pd-2-8">{{ $guest->countries->country_name }}</td>
                        <td class="pd-2-8 text-right">
                        
                            <div class="table-action">
                                
                                @if ($orderWedding->status != "Approved")
                                    
                                    

                                    <div class="table-action">
                                        <a href="#" data-toggle="modal" data-target="#detail-invitation-{{ $guest->id }}"> 
                                            <i class="icon-copy  fa fa-eye" data-toggle="tooltip" data-placement="top" title="@lang('messages.Detail')" aria-hidden="true"></i>
                                        </a>
                                        <a href="#" data-toggle="modal" data-target="#update-invitation-{{ $guest->id }}"> 
                                            <i class="icon-copy  fa fa-pencil" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit')" aria-hidden="true"></i>
                                        </a>
                                        <form id="deleteInvitationOrder{{ $guest->id }}" action="/func-delete-invitation-order-wedding/{{ $guest->id }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            @method('delete')
                                            <button form="deleteInvitationOrder{{ $guest->id }}" class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove"><i class="icon-copy fa fa-trash"></i></button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                            
                        </td>

                    </tr>
                    {{-- MODAL DETAIL INVITATION  --}}
                    <div class="modal fade" id="detail-invitation-{{ $guest->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fa fa-eye" aria-hidden="true"></i>Invitations</div>
                                    </div>
                                
                                    <div class="card-banner">
                                        @if ($guest->passport_img)
                                            <img class="img-fluid rounded" src="{{ url('storage/guests/id_passport/'.$guest->passport_img) }}" alt="{{ $guest->name }}">
                                        @else
                                            <img class="img-fluid rounded" src="{{ url('storage/guests/id_passport/default.jpg') }}" alt="{{ $guest->name }}">
                                        @endif
                                    </div>
                                    <div class="card-content">
                                        <div class="card-text">
                                            <div class="row ">
                                                <div class="col-sm-12 text-center">
                                                    <div class="card-subtitle p-t-8">{{ $guest->name }} {{ $guest->name_mandarin }}</div>
                                                </div>
                                                <div class="col-sm-12 text-center">
                                                    <p>Contact : {{ $guest->phone }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-box-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($orderWedding->status != "Approved")
                        {{-- MODAL EDIT INVITATIONS  --}}
                        <div class="modal fade" id="update-invitation-{{ $guest->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content text-left">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> @lang('messages.Invitation')</div>
                                        </div>
                                        <form id="editInvitationOrderWedding-{{ $guest->id }}" action="/fupdate-invitation-order-wedding/{{ $guest->id }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="dropzone">
                                                                <div class="update-cover-package-preview-div" id="update-cover-package-preview-div-{{ $index }}">
                                                                    @if ($guest->passport_img)
                                                                        <img class="img-fluid rounded" id="preview-img-{{ $index }}" src="{{ url('storage/guests/id_passport/'.$guest->passport_img) }}" alt="{{ $guest->name }}">
                                                                    @else
                                                                        <div class="overlay-text" id="overlay-text-{{ $index }}">ID / Passport</div>
                                                                        <img class="img-fluid rounded thumbnail-image default-img" id="preview-img-{{ $index }}" src="{{ url('storage/guests/id_passport/default.jpg') }}" alt="{{ $guest->name }}">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12 m-t-8">
                                                            <div class="form-group">
                                                                <label for="updateCoverPackage-{{ $index }}" class="form-label">ID / Passport</label>
                                                                <input type="file" name="cover" id="updateCoverPackage-{{ $index }}" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ $guest->passport_img }}" onchange="previewImage(event, {{ $index }})">
                                                                @error('cover')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="name">Name</label>
                                                                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ $guest->name }}" required>
                                                                @error('name')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="chinese_name">Mandarin Name</label>
                                                                <input name="chinese_name" type="text" class="form-control @error('chinese_name') is-invalid @enderror" placeholder="Name" value="{{ $guest->chinese_name }}">
                                                                @error('chinese_name')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="identification_no">ID/Passport Number</label>
                                                                <input name="identification_no" type="text" class="form-control @error('identification_no') is-invalid @enderror" placeholder="ID/Passpoer number" value="{{ $guest->passport_no }}" required>
                                                                @error('identification_no')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="phone">Phone</label>
                                                                <input name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" placeholder="Contact" value="{{ $guest->phone }}" required>
                                                                @error('phone')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label for="country">Country <span> *</span></label>
                                                                <select name="country" class="form-control custom-select @error('country') is-invalid @enderror" required>
                                                                    <option value="">Select one</option>
                                                                    @foreach ($countries as $edit_country)
                                                                        <option {{ $guest->country == $edit_country->id?"selected":""; }} value="{{ $edit_country->id }}">{{ $edit_country->code2 }} - {{ $edit_country->country_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('country')
                                                                    <div class="alert-form">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="card-box-footer">
                                            <button type="submit" form="editInvitationOrderWedding-{{ $guest->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </tbody>
        </table>
    @else
        <div class="card-ptext-margin">
            <p class="page-notification">@lang('messages.You can add the invitations details in this section!')</p>
        </div>
    @endif
</div>