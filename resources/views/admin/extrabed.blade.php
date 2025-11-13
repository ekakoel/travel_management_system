{{-- EXTRA BED ----------------------------------------------------------------------------------------------------------- --}} 
<div class="row m-b-18">
    <div class="col-md-8">
        <div class="card-box">
            <div class="card-box-title">
                <div class="subtitle"> <i class="fa fa-user-plus" aria-hidden="true"></i> Extra Bed</div>
            </div>
            @if (count($extra_bed) > 0)
                <table id="tbExtraBed" class="data-table table stripe nowrap" >
                    <thead>
                        <tr>
                            <th style="width: 30%;">Name</th>
                            <th style="width: 50%;">Description</th>
                            <th style="width: 10%;">Published Rate</th>
                            @canany(['posDev','posAuthor'])
                                <th class="datatable-nosort text-center" style="width: 10%;">Action</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($extra_bed as $extrabed)
                            @php
                                $usdrates_extra_bed = ceil($extrabed->contract_rate / $usdrates->rate);
                                $tax_extra_bed = $taxes->tax / 100;
                                $pajak_extra_bed = ceil(($usdrates_extra_bed + $extrabed->markup)*$tax_extra_bed);
                                $extra_bed_room_name = $hotel->rooms->where('id',$extrabed->rooms_id)->first();
                            @endphp
                            <tr>
                                <td>
                                    <b>{{ $extrabed->name." (". $extrabed->type.")"}}</b>
                                </td>
                                <td>
                                    @if ($extrabed->type === 'Adult')
                                        {!! $extrabed->description ? "$extrabed->description":"Adult (12 years and above)" !!}
                                    @elseif ($extrabed->type === 'Child')
                                        {!! $extrabed->description ? "$extrabed->description":"Child (2 - 11 years old)" !!}
                                    @else
                                        {!! $extrabed->description ? "$extrabed->description":"Guest (12 years and above)" !!}
                                    @endif
                                </td>
                                <td>
                                    <div class="rate-usd">{{ currencyFormatUsd($usdrates_extra_bed + $extrabed->markup + $pajak_extra_bed) }}</div>
                                </td>
                                @canany(['posDev','posAuthor'])
                                    <td class="text-right">
                                        <div class="table-action">
                                            <a href="#" data-toggle="modal" data-target="#edit-extrabed-{{ $extrabed->id }}">
                                                <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-edit"></i></button>
                                            </a>
                                            <form action="/fdelete-e-b/{{ $extrabed->id }}" method="post">
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
                            @canany(['posDev','posAuthor'])
                                {{-- MODAL EXTRA BED EDIT ===========================================================================================================================================================--}}
                                <div class="modal fade" id="edit-extrabed-{{ $extrabed->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Extra Bed Edit</div>
                                                </div>
                                                <div class="row">
                                                    @php
                                                        $extra_bed_room = $hotel->rooms->where('id',$extrabed->rooms_id)->first();
                                                    @endphp
                                                    <form id="edit-extra-bed-{{ $extrabed->id }}" action="{{ route('func.extrabed.edit',$extrabed->id) }}" method="post" enctype="multipart/form-data">
                                                        @method('put')
                                                        {{ csrf_field() }}
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="type">Type</label>
                                                                <select name="type" id="type"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Insert type" required>
                                                                    <option {{ $extrabed->type === "Adult" ? "selected":""}} value="Adult">Adult</option>
                                                                    <option {{ $extrabed->type === "Children" ? "selected":""}} value="Children">Child</option>
                                                                    <option {{ $extrabed->type === "Guest" ? "selected":""}} value="Guest">Guest</option>
                                                                </select>
                                                                @error('type')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="min_age" >Min Age</label>
                                                                <input name="min_age" id="min_age"  type="number" wire:model="min_age" class="form-control @error('min_age') is-invalid @enderror" placeholder="Insert min age" value="{{ $extrabed->min_age }}">
                                                                @error('min_age')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="max_age">Max Age</label>
                                                                <input name="max_age" id="max_age" type="number" wire:model="max_age" class="form-control @error('max_age') is-invalid @enderror" placeholder="Insert max age" value="{{ $extrabed->max_age }}">
                                                                @error('max_age')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="contract_rate">Contract Rate</label>
                                                                <div class="btn-icon">
                                                                    <span>Rp</span>
                                                                    <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ $extrabed->contract_rate }}" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="markup">Markup</label>
                                                                <div class="btn-icon">
                                                                    <span>$</span>
                                                                    <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ $extrabed->markup }}" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="description">Description</label>
                                                                <textarea name="description" id="description" wire:model="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Insert description" type="text">{!! $extrabed->description !!}</textarea>
                                                                @error('description')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                        <input name="hotels_id" value="{{ $extrabed->hotels_id }}" type="hidden">
                                                        <input id="service_id" name="service_id" value="{{ $hotel->id }}" type="hidden">
                                                    </form>
                                                </div>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="edit-extra-bed-{{ $extrabed->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
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
                <div class="notif">Extra bed not found!</div>
            @endif
            @canany(['posDev','posAuthor'])
                <div class="card-box-footer">
                    <a href="#" data-toggle="modal" data-target="#add-extrabed-{{ $hotel->id }}" data-toggle="tooltip" data-placement="top" title="Add Extra Bed">
                        <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Extra Bed</button>
                    </a>
                </div>
                {{-- MODAL ADD EXTRA BED --}}
                <div class="modal fade" id="add-extrabed-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Extra Bed</div>
                                </div>
                                <form id="add-extra-bed" action="{{ route('func.extrabed.add') }}" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="type">Type</label>
                                                <select name="type" id="type"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Insert type" required>
                                                    <option selected value="">Select type</option>
                                                    <option value="Adult">Adult</option>
                                                    <option value="Children">Child</option>
                                                    <option value="Guest">Guest</option>
                                                </select>
                                                @error('type')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="min_age">Min Age</label>
                                                    <input name="min_age" id="min_age"  type="number" wire:model="min_age" class="form-control @error('min_age') is-invalid @enderror" placeholder="Insert min age">
                                                @error('min_age')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="max_age" >Max Age</label>
                                                
                                                    <input name="max_age" id="max_age"  type="number" wire:model="max_age" class="form-control @error('max_age') is-invalid @enderror" placeholder="Insert max age">
                                                @error('max_age')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="contract_rate">Contract Rate</label>
                                                <div class="btn-icon">
                                                    <span>Rp</span>
                                                    <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ old('contract_rate') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="markup">Markup</label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ old('markup') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea name="description" id="description" wire:model="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Description" type="text">{!! old('description') !!}</textarea>
                                                @error('description')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <input name="name" value="Extra Bed" type="hidden">
                                        <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                        <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="add-extra-bed" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add</button>
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
