<div id="optional-rate" class="row m-b-18">
    <div class="col-md-8">
        <div class="card-box">
            <div class="card-box-title">
                <i class="fa fa-asterisk" aria-hidden="true"></i> Additional Charge
            </div>
            <div class="card-box-body">
                @if (count($additional_charges) > 0)
                    <table id="tbOptionalrate" class="data-table table stripe nowrap" >
                        <thead>
                            <tr>
                                <th style="width: 30%;">Type</th>
                                <th style="width: 30%;">Name</th>
                                <th style="width: 20%;">Mandatory</th>
                                <th style="width: 10%;">Published Rate</th>
                                @canany(['posDev','posAuthor'])
                                    <th class="datatable-nosort text-center" style="width: 10%;">Action</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($additional_charges as $additional_charge)
                                @php
                                    $usdrates_additional_charge = ceil($additional_charge->contract_rate / $usdrates->rate);
                                    $tax_additional_charge = $taxes->tax / 100;
                                    $pajak_additional_charge = ceil(($usdrates_additional_charge + $additional_charge->markup)*$tax_additional_charge);
                                @endphp
                                @if ($additional_charge->mandatory == 1 && $additional_charge->mandatory_end >= date('Y-m-d'))
                                    <tr>
                                        <td>
                                            <p>{{ $additional_charge->type }}</p>
                                        </td>
                                        <td>
                                            <p>{{ $additional_charge->name }}</p>
                                        </td>
                                        <td>
                                            <p>
                                                {{ $additional_charge->mandatory ? dateFormat($additional_charge->mandatory_start)." - ".dateFormat($additional_charge->mandatory_end) : '-' }}
                                            </p>
                                        </td>
                                        <td>
                                            <div class="rate-usd"> {!! currencyFormatUsd($additional_charge->calculatePrice($usdrates,$tax)) !!}</div>
                                        </td>
                                        @canany(['posDev','posAuthor'])
                                            <td class="text-right">
                                                <div class="table-action">
                                                    <a href="#" data-toggle="modal" data-target="#edit-optionalrate-{{ $additional_charge->id }}">
                                                        <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-edit"></i></button>
                                                    </a>
                                                    <form action="{{ route('func.additional_charge.delete',$additional_charge->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @else
                                    @if ($additional_charge->mandatory == 0)
                                        <tr>
                                            <td>
                                                <p>{{ $additional_charge->type }}</p>
                                            </td>
                                            <td>
                                                <p>{{ $additional_charge->name }}</p>
                                            </td>
                                            <td>
                                                <p>-</p>
                                            </td>
                                            <td>
                                                <div class="rate-usd"> {!! currencyFormatUsd($additional_charge->calculatePrice($usdrates,$tax)) !!}</div>
                                            </td>
                                            @canany(['posDev','posAuthor'])
                                                <td class="text-right">
                                                    <div class="table-action">
                                                        <a href="#" data-toggle="modal" data-target="#edit-optionalrate-{{ $additional_charge->id }}">
                                                            <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-edit"></i></button>
                                                        </a>
                                                        <form action="{{ route('func.additional_charge.delete',$additional_charge->id) }}" method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endcanany
                                        </tr>
                                    @endif
                                @endif
                                @canany(['posDev','posAuthor'])
                                    {{-- MODAL ADDITIONAL CHARGE EDIT --}}
                                    <div class="modal fade" id="edit-optionalrate-{{ $additional_charge->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Additional Charge Edit
                                                    </div>
                                                    <div class="card-box-body">
                                                        <form id="edit-optional-rate-{{ $additional_charge->id }}" action="{{ route('func.additional_charge.update',$additional_charge->id) }}" method="post" enctype="multipart/form-data">
                                                            @method('put')
                                                            {{ csrf_field() }}
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="type">Type <span>*</span></label>
                                                                        <select name="type" id="type"  type="text" class="custom-select @error('type') is-invalid @enderror" required>
                                                                            <option selected value="">Select one</option>
                                                                            <option {{ $additional_charge->type === "Transportation" ? "selected":""}} value="Transportation">Transportation</option>
                                                                            <option {{ $additional_charge->type === "Food & Beverage" ? "selected":""}} value="Food & Beverage">Food & Beverage</option>
                                                                            <option {{ $additional_charge->type === "Wellness & Recreation" ? "selected":""}} value="Wellness & Recreation">Wellness & Recreation</option>
                                                                            <option {{ $additional_charge->type === "Business & Convenience" ? "selected":""}} value="Business & Convenience">Business & Convenience</option>
                                                                            <option {{ $additional_charge->type === "Personalized Experiences" ? "selected":""}} value="Personalized Experiences">Personalized Experiences</option>
                                                                        </select>
                                                                        @error('type')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="name">Name </label>
                                                                        <input name="name" id="name" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Select Date and Time" type="text" value="{{ $additional_charge->name }}" required>
                                                                        @error('name')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="mandatory">Mandatory</label>
                                                                        <select name="mandatory" id="mandatory"  type="text" class="custom-select @error('mandatory') is-invalid @enderror">
                                                                            <option selected value= 0>Select one</option>
                                                                            <option {{ $additional_charge->mandatory === 0 ? "selected":""}} value="0">No</option>
                                                                            <option {{ $additional_charge->mandatory === 1 ? "selected":""}} value="1">Yes</option>
                                                                        </select>
                                                                        @error('mandatory')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="mandatory_start">Mandatory Date Start</label>
                                                                        <div class="btn-icon">
                                                                            <span><i class="icon-copy dw dw-calendar1"></i></span>
                                                                            <input readonly type="text" id="mandatory_start" name="mandatory_start" class="input-icon form-control date-picker @error('mandatory_start') is-invalid @enderror" placeholder="Select date" value="{{ $additional_charge->mandatory_start? date('d F Y',strtotime($additional_charge->mandatory_start)):'' }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="mandatory_end">Mandatory Date End</label>
                                                                        <div class="btn-icon">
                                                                            <span><i class="icon-copy dw dw-calendar1"></i></span>
                                                                            <input readonly type="text" id="mandatory_end" name="mandatory_end" class="input-icon form-control date-picker @error('mandatory_end') is-invalid @enderror" placeholder="Select date" value="{{ $additional_charge->mandatory_end? date('d F Y',strtotime($additional_charge->mandatory_end)):'' }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="contract_rate">Contract Rate <span> *</span></label>
                                                                        <div class="btn-icon">
                                                                            <span>Rp</span>
                                                                            <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert Markup" value="{{ $additional_charge->contract_rate }}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="markup">Markup <span> *</span></label>
                                                                        <div class="btn-icon">
                                                                            <span>$</span>
                                                                            <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert Markup" value="{{ $additional_charge->markup }}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="description">Description </label>
                                                                        <textarea name="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Select Date and Time" type="text" required>{!! $additional_charge->description !!}</textarea>
                                                                        @error('description')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                <input id="service_id" name="service_id" value="{{ $hotel->id }}" type="hidden">
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="edit-optional-rate-{{ $additional_charge->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcanany
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="notif">Optional rates not found!</div>
                @endif
            </div>
            @canany(['posDev','posAuthor'])
                <div class="card-box-footer">
                    <a href="#" data-toggle="modal" data-target="#add-optionalrate-{{ $hotel->id }}" data-toggle="tooltip" data-placement="top" title="Detail">
                        <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Additional Charge</button>
                    </a>
                </div>
                {{-- MODAL ADD ADDITIONAL CHARGE --}}
                <div class="modal fade" id="add-optionalrate-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Additional Charge
                                </div>
                                <div class="card-box-body">
                                    <form id="add-additional-charge" action="{{ route('func.additional_charge.add') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="type">Type <span>*</span></label>
                                                            <select name="type" id="type"  type="text" class="custom-select @error('type') is-invalid @enderror" required>
                                                                <option selected value="">Select one</option>
                                                                <option value="Transportation">Transportation</option>
                                                                <option value="Food & Beverage">Food & Beverage</option>
                                                                <option value="Wellness & Recreation">Wellness & Recreation</option>
                                                                <option value="Business & Convenience">Business & Convenience</option>
                                                                <option value="Personalized Experiences">Personalized Experiences</option>
                                                            </select>
                                                            @error('type')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name">Name </label>
                                                            <input name="name" id="name"  type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert name" value="{{ old('name') }}" required>
                                                            @error('name')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="mandatory">Mandatory</label>
                                                            <select name="mandatory" id="mandatory"  type="text" class="custom-select @error('mandatory') is-invalid @enderror">
                                                                <option selected value="">Select one</option>
                                                                <option value="0">No</option>
                                                                <option value="1">Yes</option>
                                                            </select>
                                                            @error('mandatory')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="mandatory_start">Mandatory Date Start</label>
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy dw dw-calendar1"></i></span>
                                                                <input readonly type="text" id="mandatory_start" name="mandatory_start" class="input-icon form-control date-picker @error('mandatory_start') is-invalid @enderror" placeholder="Select date" value="{{ old('mandatory_start') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="mandatory_end">Mandatory Date End</label>
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy dw dw-calendar1"></i></span>
                                                                <input readonly type="text" id="mandatory_end" name="mandatory_end" class="input-icon form-control date-picker @error('mandatory_end') is-invalid @enderror" placeholder="Select date" value="{{ old('mandatory_end') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="contract_rate">Contract Rate<span> *</span></label>
                                                            <div class="btn-icon">
                                                                <span>Rp</span>
                                                                <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ old('contract_rate') }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="markup">Markup<span> *</span></label>
                                                            <div class="btn-icon">
                                                                <span>$</span>
                                                                <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ old('markup') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="description">Description </label>
                                                                <textarea name="description" id="add_desc_a_c" wire:model="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Select Date and Time" type="text" required>{!! old('description') !!}</textarea>
                                                            @error('description')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input name="hotel_id" value="{{ $hotel->id }}" type="hidden">
                                            <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                            <input id="service_id" name="service_id" value="{{ $hotel->id }}" type="hidden">
                                        </div>
                                    </form>
                                </div>
                                <div class="card-box-footer">
                                    <button type="submit" form="add-additional-charge" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
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