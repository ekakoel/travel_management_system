<div id="optional-rate" class="row">
    <div class="col-md-8">
        <div class="card-box">
            <div class="card-box-title">
                <div class="subtitle"><i class="fa fa-asterisk" aria-hidden="true"></i> Additional Charge</div>
            </div>
            @if (count($optional_rate) > 0)
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
                        @foreach ($optional_rate as $optionalrate)
                            @php
                                $usdrates_optional_rate = ceil($optionalrate->contract_rate / $usdrates->rate);
                                $tax_optional_rate = $taxes->tax / 100;
                                $pajak_optional_rate = ceil(($usdrates_optional_rate + $optionalrate->markup)*$tax_optional_rate);
                            @endphp
                            @if ($optionalrate->mandatory == 1 && $optionalrate->must_buy_end >= date('Y-m-d'))
                                <tr>
                                    <td>
                                        <p>{{ $optionalrate->type }}</p>
                                    </td>
                                    <td>
                                        <p>{{ $optionalrate->name }}</p>
                                    </td>
                                    <td>
                                        <p>
                                            {{ $optionalrate->mandatory ? dateFormat($optionalrate->must_buy_start)." - ".dateFormat($optionalrate->must_buy_end) : '-' }}
                                        </p>
                                    </td>
                                    <td>
                                        <div class="rate-usd"> {{ currencyFormatUsd($optionalrate->calculatePrice($usdrates,$tax)) }}</div>
                                    </td>
                                    @canany(['posDev','posAuthor'])
                                        <td class="text-right">
                                            <div class="table-action">
                                                <a href="#" data-toggle="modal" data-target="#edit-optionalrate-{{ $optionalrate->id }}">
                                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-edit"></i></button>
                                                </a>
                                                <form action="{{ route('func.optional_rate.delete',$optionalrate->id) }}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                    <input type="hidden" name="hotels_id" value="{{ $hotel->id }}">
                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @else
                                @if ($optionalrate->mandatory == 0)
                                    <tr>
                                        <td>
                                            <p>{{ $optionalrate->type }}</p>
                                        </td>
                                        <td>
                                            <p>{{ $optionalrate->name }}</p>
                                        </td>
                                        <td>
                                            <p>-</p>
                                        </td>
                                        <td>
                                            <div class="rate-usd"> {{ currencyFormatUsd($optionalrate->calculatePrice($usdrates,$tax)) }}</div>
                                        </td>
                                        @canany(['posDev','posAuthor'])
                                            <td class="text-right">
                                                <div class="table-action">
                                                    <a href="#" data-toggle="modal" data-target="#edit-optionalrate-{{ $optionalrate->id }}">
                                                        <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-edit"></i></button>
                                                    </a>
                                                    <form action="{{ route('func.optional_rate.delete',$optionalrate->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                        <input type="hidden" name="hotels_id" value="{{ $hotel->id }}">
                                                        <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @endif
                            @endif
                            @canany(['posDev','posAuthor'])
                                {{-- MODAL OPTIONAL RATE EDIT --}}
                                <div class="modal fade" id="edit-optionalrate-{{ $optionalrate->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Additional Charge Edit</div>
                                                </div>
                                                <form id="edit-optional-rate-{{ $optionalrate->id }}" action="{{ route('func.optional_rate.update',$optionalrate->id) }}" method="post" enctype="multipart/form-data">
                                                    @method('put')
                                                    {{ csrf_field() }}
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="type">Type <span>*</span></label>
                                                                <select name="type" id="type"  type="text" class="custom-select @error('type') is-invalid @enderror" required>
                                                                    <option selected value="">Select one</option>
                                                                    <option {{ $optionalrate->type === "Transportation" ? "selected":""}} value="Transportation">Transportation</option>
                                                                    <option {{ $optionalrate->type === "Food & Beverage" ? "selected":""}} value="Food & Beverage">Food & Beverage</option>
                                                                    <option {{ $optionalrate->type === "Wellness & Recreation" ? "selected":""}} value="Wellness & Recreation">Wellness & Recreation</option>
                                                                    <option {{ $optionalrate->type === "Business & Convenience" ? "selected":""}} value="Business & Convenience">Business & Convenience</option>
                                                                    <option {{ $optionalrate->type === "Personalized Experiences" ? "selected":""}} value="Personalized Experiences">Personalized Experiences</option>
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
                                                                <input name="name" id="name" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Select Date and Time" type="text" value="{{ $optionalrate->name }}" required>
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
                                                                    <option {{ $optionalrate->mandatory === 0 ? "selected":""}} value="0">No</option>
                                                                    <option {{ $optionalrate->mandatory === 1 ? "selected":""}} value="1">Yes</option>
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
                                                                <label for="must_buy_start">Mandatory Date Start</label>
                                                                <div class="btn-icon">
                                                                    <span><i class="icon-copy dw dw-calendar1"></i></span>
                                                                    <input readonly type="text" id="must_buy_start" name="must_buy_start" class="input-icon form-control date-picker @error('must_buy_start') is-invalid @enderror" placeholder="Select date" value="{{ date('d F Y',strtotime($optionalrate->must_buy_start)) }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="must_buy_end">Mandatory Date End</label>
                                                                <div class="btn-icon">
                                                                    <span><i class="icon-copy dw dw-calendar1"></i></span>
                                                                    <input readonly type="text" id="must_buy_end" name="must_buy_end" class="input-icon form-control date-picker @error('must_buy_end') is-invalid @enderror" placeholder="Select date" value="{{ date('d F Y',strtotime($optionalrate->must_buy_end)) }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="contract_rate">Contract Rate <span> *</span></label>
                                                                <div class="btn-icon">
                                                                    <span>Rp</span>
                                                                    <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert Markup" value="{{ $optionalrate->contract_rate }}" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="markup">Markup <span> *</span></label>
                                                                <div class="btn-icon">
                                                                    <span>$</span>
                                                                    <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert Markup" value="{{ $optionalrate->markup }}" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="description">Description </label>
                                                                <textarea name="description" id="edit_desc_a_c" wire:model="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Select Date and Time" type="text" required>{!! $optionalrate->description !!}</textarea>
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
                                                <div class="card-box-footer">
                                                    <button type="submit" form="edit-optional-rate-{{ $optionalrate->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
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
            @canany(['posDev','posAuthor'])
                <div class="card-box-footer">
                    <a href="#" data-toggle="modal" data-target="#add-optionalrate-{{ $hotel->id }}" data-toggle="tooltip" data-placement="top" title="Detail">
                        <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Additional Charge</button>
                    </a>
                </div>
                {{-- MODAL ADD OPTIONAL RATE --}}
                <div class="modal fade" id="add-optionalrate-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Additional Charge</div>
                                </div>
                                <form id="add-optional-rate" action="{{ route('func.optional_rate.add') }}" method="post" enctype="multipart/form-data">
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
                                                        <label for="must_buy_start">Mandatory Date Start</label>
                                                        <div class="btn-icon">
                                                            <span><i class="icon-copy dw dw-calendar1"></i></span>
                                                            <input readonly type="text" id="must_buy_start" name="must_buy_start" class="input-icon form-control date-picker @error('must_buy_start') is-invalid @enderror" placeholder="Select date" value="{{ old('must_buy_start') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="must_buy_end">Mandatory Date End</label>
                                                        <div class="btn-icon">
                                                            <span><i class="icon-copy dw dw-calendar1"></i></span>
                                                            <input readonly type="text" id="must_buy_end" name="must_buy_end" class="input-icon form-control date-picker @error('must_buy_end') is-invalid @enderror" placeholder="Select date" value="{{ old('must_buy_end') }}">
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
                                        <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                        <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                        <input id="service_id" name="service_id" value="{{ $hotel->id }}" type="hidden">
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="add-optional-rate" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add Price</button>
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